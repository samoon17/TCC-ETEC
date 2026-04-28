<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'profissional') {
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

garantirTabelaFormularioConsulta($pdo);
garantirColunasFormulario($pdo);

$stmtProf = $pdo->prepare("
    SELECT id_profissional
    FROM profissional
    WHERE id_usuario = ?
");
$stmtProf->execute([$_SESSION['id']]);
$profissional = $stmtProf->fetch(PDO::FETCH_ASSOC);

if (!$profissional) {
    echo "Profissional nao encontrado";
    exit;
}

$stmt = $pdo->prepare("
    SELECT f.*, u.nome AS paciente
    FROM formulario_consulta f
    JOIN usuario u ON u.id_usuario = f.id_paciente
    WHERE f.id_profissional = ?
    ORDER BY f.id DESC
");
$stmt->execute([$profissional['id_profissional']]);

$formularios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel Profissional</title>
<link rel="stylesheet" href="style.css">
</head>

<body class="painel">

<header class="topo">
    <h2 class="logo"><a href="index.html">VivaMente</a></h2>

    <nav class="menu">
        <a href="index.html">Inicio</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<section class="principal">
    <div class="conteudo">

        <h1>Painel do Profissional</h1>

        <?php if (isset($_GET['ok'])): ?>
            <div class="sucesso">Consulta criada com sucesso!</div>
        <?php endif; ?>

        <h2>Formularios recebidos</h2>

        <div class="lista-formularios">

        <?php if (count($formularios) === 0): ?>
            <p>Nenhum formulario foi enviado ate agora.</p>
        <?php endif; ?>

        <?php foreach ($formularios as $f): ?>

            <div class="card">

                <p><strong>Paciente:</strong> <?= htmlspecialchars($f['paciente']) ?></p>

                <p><strong>Descricao:</strong></p>
                <p><?= htmlspecialchars($f['descricao']) ?></p>

                <p><strong>Enviado em:</strong> <?= htmlspecialchars($f['data_envio']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($f['status'] ?? 'enviado') ?></p>

                <form action="sistema.php" method="POST" style="margin-top:10px;">
                    <input type="hidden" name="acao" value="criar_consulta">
                    <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($f['id_paciente']) ?>">
                    <input type="hidden" name="id_formulario" value="<?= htmlspecialchars($f['id']) ?>">

                    <input type="datetime-local" name="data_hora" required>
                    <input type="text" name="link_chamada" placeholder="Link da reuniao" required>

                    <button class="button btn-psicologo" type="submit">
                        Criar consulta
                    </button>
                </form>

            </div>

        <?php endforeach; ?>

        </div>

    </div>
</section>

<footer class="rodape">
    <p>&copy; VivaMente</p>
</footer>

</body>
</html>
