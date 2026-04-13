<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'paciente') {
    header("Location: login.html");
    exit;
}

// conexão
$pdo = new PDO("mysql:host=localhost;dbname=plataforma_saude_mental;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// busca formulários do paciente
$stmt = $pdo->prepare("
    SELECT * 
    FROM formulario_consulta 
    WHERE id_paciente = ?
    ORDER BY id DESC
");

$stmt = $pdo->prepare("
    SELECT c.*, u.nome as profissional
    FROM consulta c
    JOIN profissional p ON p.id_profissional = c.id_profissional
    JOIN usuario u ON u.id_usuario = p.id_usuario
    WHERE c.id_paciente = ?
");
$stmt->execute([$_SESSION['id']]);
$consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt->execute([$_SESSION['id']]);

$formularios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel do Paciente</title>
<link rel="stylesheet" href="style.css">

<style>
.card {
    background: #fff;
    padding: 15px;
    margin-top: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.sucesso {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
}
</style>

</head>

<body class="painel">

<header class="topo">
    <h2 class="logo">VivaMente</h2>

    <nav class="menu">
        <a href="index.html">Início</a>
        <a href="formulario.html">Novo formulário</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<section class="principal">
    <div class="conteudo">

        <h1>Olá, <?= $_SESSION['nome'] ?></h1>

        <!-- ALERTA -->
        <?php if (isset($_GET['ok'])): ?>
            <div class="sucesso">Formulário enviado com sucesso!</div>
        <?php endif; ?>

        <h2>Histórico de formulários</h2>

        <?php if (count($formularios) === 0): ?>
            <p>Você ainda não enviou nenhum formulário.</p>
        <?php endif; ?>

        <?php foreach ($formularios as $f): ?>

            <div class="card">

                <p><strong>Descrição:</strong></p>
                <p><?= htmlspecialchars($f['descricao']) ?></p>

                <?php if (isset($f['data_envio'])): ?>
                    <p><strong>Enviado em:</strong> <?= $f['data_envio'] ?></p>
                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>
</section>

<footer hidden class="rodape" >
    <p>© VivaMente</p>
</footer>

</body>
</html>