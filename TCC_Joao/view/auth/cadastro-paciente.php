<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro de paciente | VivaMente</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
</head>

<body class="pagina-simples">

<header class="topo">
    <?= logo_site() ?>

    <nav class="menu">
        <a href="<?= base_url('index.php?rota=home') ?>">Início</a>
        <a href="<?= base_url('index.php?rota=como-funciona') ?>">Como funciona</a>
        <a href="<?= base_url('index.php?rota=sobre') ?>">Sobre</a>
        <a href="<?= base_url('index.php?rota=login') ?>">Login</a>
    </nav>
</header>

<section class="principal">
    <div class="conteudo">
        <h1>Cadastro de paciente</h1>
        <p>Crie sua conta para receber orientação e acompanhar suas terapias.</p>

        <form action="<?= base_url('index.php') ?>" method="POST" class="formulario">
            <input type="hidden" name="acao" value="cadastrar_paciente">

            <input type="text" name="nome" placeholder="Nome completo" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="date" name="data_nascimento">

            <button class="button btn-paciente">Cadastrar paciente</button>
        </form>
    </div>
</section>

<footer class="rodape">
    <p>&copy; VivaMente</p>
</footer>

</body>
</html>
