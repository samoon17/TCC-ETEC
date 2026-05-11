<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'paciente') {
    header("Location: login.html");
    exit;
}

require_once __DIR__ . '/conexao.php';

$pdo = conectarBanco();

function garantirTabelaFormularioConsulta(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS formulario_consulta (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_paciente INT NOT NULL,
            descricao TEXT NOT NULL,
            data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_paciente)
                REFERENCES usuario(id_usuario)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
}

function garantirTabelaMensagem(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS mensagem (
            id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            titulo VARCHAR(150),
            conteudo TEXT NOT NULL,
            tipo ENUM('consulta','aviso','sistema') DEFAULT 'aviso',
            lida BOOLEAN DEFAULT FALSE,
            data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario)
                REFERENCES usuario(id_usuario)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
}

function garantirTabelaConsulta(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS consulta (
            id_consulta INT AUTO_INCREMENT PRIMARY KEY,
            id_paciente INT NOT NULL,
            id_profissional INT NOT NULL,
            data_hora DATETIME NOT NULL,
            link_chamada VARCHAR(255),
            status ENUM('agendada','confirmada','cancelada','finalizada') DEFAULT 'agendada',
            tipo ENUM('online','presencial') DEFAULT 'online',
            FOREIGN KEY (id_paciente)
                REFERENCES usuario(id_usuario)
                ON DELETE CASCADE,
            FOREIGN KEY (id_profissional)
                REFERENCES profissional(id_profissional)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
}

function colunaExiste(PDO $pdo, string $tabela, string $coluna): bool {
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
          AND COLUMN_NAME = ?
    ");
    $stmt->execute([$tabela, $coluna]);
    return (int) $stmt->fetchColumn() > 0;
}

function garantirColunasFormulario(PDO $pdo): void {
    if (!colunaExiste($pdo, 'formulario_consulta', 'id_profissional')) {
        $pdo->exec("ALTER TABLE formulario_consulta ADD id_profissional INT NULL AFTER id_paciente");
    }

    if (!colunaExiste($pdo, 'formulario_consulta', 'status')) {
        $pdo->exec("ALTER TABLE formulario_consulta ADD status ENUM('enviado','respondido') DEFAULT 'enviado' AFTER descricao");
    }
}

function garantirColunasConsulta(PDO $pdo): void {
    if (!colunaExiste($pdo, 'consulta', 'link_chamada')) {
        $pdo->exec("ALTER TABLE consulta ADD link_chamada VARCHAR(255) NULL AFTER data_hora");
    }

    if (!colunaExiste($pdo, 'consulta', 'status')) {
        $pdo->exec("ALTER TABLE consulta ADD status ENUM('agendada','confirmada','cancelada','finalizada') DEFAULT 'agendada' AFTER link_chamada");
    }

    if (!colunaExiste($pdo, 'consulta', 'tipo')) {
        $pdo->exec("ALTER TABLE consulta ADD tipo ENUM('online','presencial') DEFAULT 'online' AFTER status");
    }
}

garantirTabelaFormularioConsulta($pdo);
garantirTabelaMensagem($pdo);
garantirTabelaConsulta($pdo);
garantirColunasFormulario($pdo);
garantirColunasConsulta($pdo);

$stmtFormularios = $pdo->prepare("
    SELECT f.*, u.nome AS profissional
    FROM formulario_consulta f
    LEFT JOIN profissional p ON p.id_profissional = f.id_profissional
    LEFT JOIN usuario u ON u.id_usuario = p.id_usuario
    WHERE f.id_paciente = ?
    ORDER BY f.id DESC
");
$stmtFormularios->execute([$_SESSION['id']]);
$formularios = $stmtFormularios->fetchAll(PDO::FETCH_ASSOC);

$stmtConsultas = $pdo->prepare("
    SELECT c.*, u.nome AS profissional
    FROM consulta c
    JOIN profissional p ON p.id_profissional = c.id_profissional
    JOIN usuario u ON u.id_usuario = p.id_usuario
    WHERE c.id_paciente = ?
    ORDER BY c.data_hora DESC
");
$stmtConsultas->execute([$_SESSION['id']]);
$consultas = $stmtConsultas->fetchAll(PDO::FETCH_ASSOC);

$stmtMensagens = $pdo->prepare("
    SELECT *
    FROM mensagem
    WHERE id_usuario = ?
    ORDER BY id_mensagem DESC
");
$stmtMensagens->execute([$_SESSION['id']]);
$mensagens = $stmtMensagens->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel do Paciente</title>
<link rel="stylesheet" href="style.css">

<style>
.paciente-painel .principal {
    align-items: flex-start;
}
.paciente-painel .conteudo {
    max-width: 980px;
    height: auto;
    min-height: 0;
    padding-bottom: 40px;
}
.painel-cabecalho {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 22px;
}
.painel-cabecalho p {
    margin-bottom: 0;
}
.atalhos-paciente {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}
.atalho-card {
    background: #fff;
    border: 1px solid #dfe7f1;
    border-radius: 10px;
    padding: 16px;
    text-decoration: none;
    color: #263238;
    box-shadow: 0 8px 20px rgba(34, 58, 94, 0.08);
}
.atalho-card strong {
    display: block;
    color: #1e88e5;
    font-size: 22px;
    margin-bottom: 4px;
}
.secao-painel {
    margin-top: 26px;
    scroll-margin-top: 92px;
}
.secao-painel h2 {
    margin-bottom: 12px;
    color: #16324f;
}
.card {
    background: #fff;
    padding: 15px;
    margin-top: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.paciente-painel .card p {
    display: block;
    overflow: visible;
    margin-bottom: 10px;
}
.paciente-painel .card a {
    color: #1565c0;
    font-weight: 700;
}
.sucesso {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
}
.menu .menu-consulta {
    background: #43a047;
    color: #fff;
    padding: 10px 16px;
    border-radius: 999px;
    margin-left: 20px;
    display: inline-flex;
    align-items: center;
}
.menu .menu-ativo {
    background: rgba(255,255,255,0.18);
    padding: 10px 14px;
    border-radius: 999px;
}
.menu .menu-consulta:hover {
    background: #2e7d32;
    opacity: 1;
}
.cta-consulta {
    background: linear-gradient(135deg, #e3f2fd, #f1f8e9);
    border: 1px solid #c9def5;
    border-radius: 16px;
    padding: 18px 20px;
    margin-bottom: 22px;
    box-shadow: 0 10px 24px rgba(30, 136, 229, 0.08);
}
.cta-consulta p {
    margin-bottom: 12px;
}
.cta-consulta .button {
    margin-top: 6px;
}
.mensagem-consulta {
    border-left: 5px solid #1e88e5;
}
.mensagem-meta {
    font-size: 14px;
    color: #607089;
}
@media (max-width: 768px) {
    .paciente-painel .conteudo {
        max-width: 100%;
    }
    .painel-cabecalho {
        flex-direction: column;
    }
    .atalhos-paciente {
        grid-template-columns: 1fr;
    }
    .menu .menu-consulta,
    .menu .menu-ativo {
        margin-left: 0;
    }
}
</style>

</head>

<body class="painel paciente-painel">

<header class="topo">
    <h2 class="logo"><a href="index.html">VivaMente</a></h2>

    <nav class="menu">
        <a href="#mensagens" class="menu-ativo">Mensagens</a>
        <a href="#consultas">Consultas</a>
        <a href="#historico">Historico</a>
        <a href="lista-profissionais.php" class="menu-consulta">Marcar consulta</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<section class="principal">
    <div class="conteudo">

        <div class="painel-cabecalho">
            <div>
                <h1>Ola, <?= htmlspecialchars($_SESSION['nome']) ?></h1>
                <p>Acompanhe suas mensagens, formularios enviados e consultas agendadas.</p>
            </div>
            <a href="lista-profissionais.php" class="button btn-paciente">Agendar consulta</a>
        </div>

        <?php if (isset($_GET['ok'])): ?>
            <div class="sucesso">Formulario enviado com sucesso!</div>
        <?php endif; ?>

        <div class="atalhos-paciente">
            <a href="#mensagens" class="atalho-card">
                <strong><?= count($mensagens) ?></strong>
                Mensagens recebidas
            </a>
            <a href="#consultas" class="atalho-card">
                <strong><?= count($consultas) ?></strong>
                Consultas agendadas
            </a>
            <a href="#historico" class="atalho-card">
                <strong><?= count($formularios) ?></strong>
                Formularios enviados
            </a>
        </div>

        <div class="cta-consulta">
            <p><strong>Quer marcar uma consulta?</strong> Veja a lista de profissionais disponiveis e escolha com quem deseja iniciar o atendimento.</p>
            <a href="lista-profissionais.php" class="button btn-paciente">Ver lista de profissionais</a>
        </div>

        <section class="secao-painel" id="mensagens">
        <h2>Mensagens</h2>

        <?php if (count($mensagens) === 0): ?>
            <p>Voce ainda nao recebeu mensagens.</p>
        <?php endif; ?>

        <?php foreach ($mensagens as $m): ?>
            <div class="card <?= $m['tipo'] === 'consulta' ? 'mensagem-consulta' : '' ?>">
                <p><strong><?= htmlspecialchars($m['titulo'] ?: 'Mensagem') ?></strong></p>
                <p><?= nl2br(htmlspecialchars($m['conteudo'])) ?></p>
                <p class="mensagem-meta">
                    Recebida em <?= htmlspecialchars($m['data_envio']) ?>
                </p>

                <?php if (preg_match('/https?:\/\/\S+/', $m['conteudo'], $linkMensagem)): ?>
                    <p>
                        <a href="<?= htmlspecialchars($linkMensagem[0]) ?>" target="_blank" rel="noopener noreferrer">
                            Abrir link da consulta
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </section>

        <section class="secao-painel" id="consultas">
        <h2>Minhas consultas</h2>

        <?php if (count($consultas) === 0): ?>
            <p>Voce ainda nao tem consultas agendadas.</p>
        <?php endif; ?>

        <?php foreach ($consultas as $c): ?>
            <div class="card">
                <p><strong>Profissional:</strong> <?= htmlspecialchars($c['profissional']) ?></p>
                <p><strong>Data:</strong> <?= htmlspecialchars($c['data_hora']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($c['status']) ?></p>

                <?php if (!empty($c['link_chamada'])): ?>
                    <p>
                        <a href="<?= htmlspecialchars($c['link_chamada']) ?>" target="_blank" rel="noopener noreferrer">
                            Entrar na consulta
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </section>

        <section class="secao-painel" id="historico">
        <h2>Historico de formularios</h2>

        <?php if (count($formularios) === 0): ?>
            <p>Voce ainda nao enviou nenhum formulario.</p>
        <?php endif; ?>

        <?php foreach ($formularios as $f): ?>
            <div class="card">
                <p><strong>Descricao:</strong></p>
                <p><?= htmlspecialchars($f['descricao']) ?></p>

                <?php if (!empty($f['profissional'])): ?>
                    <p><strong>Enviado para:</strong> <?= htmlspecialchars($f['profissional']) ?></p>
                <?php endif; ?>

                <p><strong>Status:</strong> <?= htmlspecialchars($f['status'] ?? 'enviado') ?></p>

                <?php if (isset($f['data_envio'])): ?>
                    <p><strong>Enviado em:</strong> <?= htmlspecialchars($f['data_envio']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </section>

    </div>
</section>

<footer hidden class="rodape">
    <p>&copy; VivaMente</p>
</footer>

</body>
</html>
