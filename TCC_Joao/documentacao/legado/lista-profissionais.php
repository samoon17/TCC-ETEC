<?php
session_start();

require_once __DIR__ . '/conexao.php';

$pdo = conectarBanco();

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

if (!colunaExiste($pdo, 'profissional', 'descricao')) {
    $pdo->exec("ALTER TABLE profissional ADD descricao TEXT NULL AFTER registro_profissional");
}

if (!colunaExiste($pdo, 'profissional', 'especialidade')) {
    $pdo->exec("ALTER TABLE profissional ADD especialidade VARCHAR(120) NULL AFTER descricao");
}

if (!colunaExiste($pdo, 'profissional', 'valor_consulta')) {
    $pdo->exec("ALTER TABLE profissional ADD valor_consulta DECIMAL(10,2) NULL AFTER especialidade");
}

$stmt = $pdo->query("
    SELECT p.id_profissional, p.registro_profissional, p.descricao, p.especialidade,
           p.valor_consulta, p.cidade, p.estado, u.nome
    FROM profissional p
    JOIN usuario u ON u.id_usuario = p.id_usuario
    WHERE u.status = 'ativo'
    ORDER BY u.nome
");
$profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);

function iniciais(string $nome): string {
    $partes = preg_split('/\s+/', trim($nome));
    $primeira = $partes[0][0] ?? '';
    $ultima = count($partes) > 1 ? ($partes[count($partes) - 1][0] ?? '') : '';
    return strtoupper($primeira . $ultima);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Escolher profissional | VivaMente</title>
<link rel="stylesheet" href="style.css">
<style>
    body.lista-profissionais-page {
        display: block;
        background: linear-gradient(180deg, #f7f9fc 0%, #eef3f9 100%);
        color: #222;
    }
    .lista-profissionais-page .topo {
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 2px 18px rgba(26, 66, 123, 0.08);
    }
    .hero-profissionais,
    .lista-profissionais {
        max-width: 1180px;
        margin: 0 auto;
        padding: 36px 20px 18px;
    }
    .hero-profissionais h1 {
        font-size: clamp(2rem, 4vw, 3rem);
        color: #114a8d;
        margin-bottom: 14px;
    }
    .hero-profissionais p {
        max-width: 760px;
        font-size: 1.05rem;
        line-height: 1.6;
        color: #516075;
    }
    .lista-profissionais {
        padding-top: 12px;
        padding-bottom: 50px;
    }
    .card-profissional {
        display: grid;
        grid-template-columns: 170px 1fr 250px;
        gap: 28px;
        background: #fff;
        border: 1px solid #dfe7f1;
        border-radius: 24px;
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 16px 40px rgba(34, 58, 94, 0.10);
    }
    .perfil-lateral {
        text-align: center;
    }
    .avatar-profissional {
        width: 118px;
        height: 118px;
        border-radius: 50%;
        margin: 0 auto 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #3bb2b8, #4a7cff);
        box-shadow: 0 14px 28px rgba(61, 98, 153, 0.22);
    }
    .valor-consulta {
        font-size: 1.65rem;
        font-weight: 800;
        color: #1e3f6e;
    }
    .valor-consulta span {
        display: block;
        margin-top: 4px;
        font-size: 0.9rem;
        font-weight: 500;
        color: #6e7d91;
    }
    .topo-card {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 14px;
    }
    .topo-card h2 {
        font-size: clamp(1.45rem, 3vw, 2rem);
        margin-bottom: 4px;
        color: #161d29;
    }
    .especialidade,
    .registro,
    .descricao-profissional {
        color: #607089;
    }
    .registro {
        white-space: nowrap;
        font-weight: 700;
        font-size: 0.95rem;
    }
    .meta-profissional {
        display: flex;
        flex-wrap: wrap;
        gap: 18px;
        margin-bottom: 16px;
        color: #2d3748;
        font-size: 0.98rem;
    }
    .descricao-profissional {
        line-height: 1.7;
    }
    .acao-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 14px;
    }
    .cta-formulario {
        display: block;
        text-align: center;
        text-decoration: none;
        background: #36a852;
        color: #fff;
        font-weight: 700;
        padding: 16px 18px;
        border-radius: 14px;
        box-shadow: 0 14px 24px rgba(54, 168, 82, 0.20);
    }
    .info-contato {
        background: #f6f9fd;
        border: 1px solid #dfe8f3;
        border-radius: 16px;
        padding: 16px;
        color: #56657b;
        line-height: 1.7;
    }
    .vazio {
        background: #fff;
        border: 1px solid #dfe7f1;
        border-radius: 16px;
        padding: 24px;
        color: #516075;
    }
    @media (max-width: 980px) {
        .card-profissional {
            grid-template-columns: 1fr;
        }
        .perfil-lateral {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 18px;
            text-align: left;
        }
        .avatar-profissional {
            margin: 0;
            width: 92px;
            height: 92px;
            font-size: 1.6rem;
        }
    }
    @media (max-width: 768px) {
        .card-profissional {
            padding: 22px;
        }
        .topo-card,
        .perfil-lateral {
            flex-direction: column;
        }
        .perfil-lateral {
            align-items: center;
            text-align: center;
        }
    }
</style>
</head>

<body class="lista-profissionais-page">

<header class="topo">
    <h2 class="logo"><a href="index.html">VivaMente</a></h2>
    <nav class="menu">
        <a href="index.html">Inicio</a>
        <a href="comoFunciona.html">Como funciona</a>
        <a href="cadastroPaciente.html">Pacientes</a>
        <a href="cadastroProfissional.html">Profissionais</a>
    </nav>
</header>

<section class="hero-profissionais">
    <h1>Escolha um profissional para receber seu formulario</h1>
    <p>Veja os profissionais cadastrados, conheca a area de atuacao de cada um e envie seu formulario para iniciar o atendimento.</p>
</section>

<main class="lista-profissionais">
    <?php if (count($profissionais) === 0): ?>
        <div class="vazio">Nenhum profissional cadastrado ate agora.</div>
    <?php endif; ?>

    <?php foreach ($profissionais as $prof): ?>
        <article class="card-profissional">
            <div class="perfil-lateral">
                <div class="avatar-profissional"><?= htmlspecialchars(iniciais($prof['nome'])) ?></div>
                <div class="valor-consulta">
                    <?php if ($prof['valor_consulta'] !== null): ?>
                        R$ <?= number_format((float) $prof['valor_consulta'], 2, ',', '.') ?>
                    <?php else: ?>
                        A combinar
                    <?php endif; ?>
                    <span>consulta</span>
                </div>
            </div>

            <div class="conteudo-profissional">
                <div class="topo-card">
                    <div>
                        <h2><?= htmlspecialchars($prof['nome']) ?></h2>
                        <p class="especialidade"><?= htmlspecialchars($prof['especialidade'] ?: 'Profissional de saude mental') ?></p>
                    </div>
                    <?php if (!empty($prof['registro_profissional'])): ?>
                        <span class="registro">Registro: <?= htmlspecialchars($prof['registro_profissional']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="meta-profissional">
                    <?php if (!empty($prof['cidade']) || !empty($prof['estado'])): ?>
                        <span><strong>Local:</strong> <?= htmlspecialchars(trim(($prof['cidade'] ?? '') . ' - ' . ($prof['estado'] ?? ''), ' -')) ?></span>
                    <?php endif; ?>
                    <span><strong>Atendimento:</strong> Online</span>
                </div>

                <p class="descricao-profissional">
                    <?= nl2br(htmlspecialchars($prof['descricao'] ?: 'Este profissional ainda nao adicionou uma descricao completa.')) ?>
                </p>
            </div>

            <div class="acao-card">
                <a href="formulario.php?profissional=<?= htmlspecialchars($prof['id_profissional']) ?>" class="cta-formulario">
                    Enviar formulario para <?= htmlspecialchars(explode(' ', trim($prof['nome']))[0]) ?>
                </a>

                <div class="info-contato">
                    <strong>Proximo passo:</strong><br>
                    O profissional recebe seu formulario e pode criar uma consulta para voce.
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</main>

</body>
</html>
