<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'profissional') {
    header("Location: login.html");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=plataforma_saude_mental;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// pega formulários com nome do paciente
$stmt = $pdo->query("
    SELECT f.*, u.nome as paciente
    FROM formulario_consulta f
    JOIN usuario u ON u.id_usuario = f.id_paciente
    ORDER BY f.id DESC
");

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
    <h2 class="logo">VivaMente</h2>

    <nav class="menu">
        <a href="index.html">Início</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<section class="principal">
    <div class="conteudo">

        <h1>Painel do Profissional</h1>

        <h2>Formulários recebidos</h2>

        <div class="lista-formularios">

        <?php foreach ($formularios as $f): ?>

            <div class="card">

                <p><strong>Paciente:</strong> <?= $f['paciente'] ?></p>

                <p><strong>Descrição:</strong></p>
                <p><?= htmlspecialchars($f['descricao']) ?></p>

                <p><strong>Enviado em:</strong> <?= $f['data_envio'] ?></p>

                <!-- FORM DE RESPOSTA -->
                <form action="sistema.php" method="POST" style="margin-top:10px;">

                    <input type="hidden" name="acao" value="criar_consulta">
                    <input type="hidden" name="id_paciente" value="<?= $f['id_paciente'] ?>">

                    <input type="datetime-local" name="data_hora" required>
                    <input type="text" name="link_chamada" placeholder="Link da reunião" required>

                    <button class="button btn-psicologo">
                        Criar consulta
                    </button>

                </form>

            </div>

        <?php endforeach; ?>

        </div>

    </div>
</section>

<footer class="rodape">
    <p>© VivaMente</p>
</footer>

</body>
</html>