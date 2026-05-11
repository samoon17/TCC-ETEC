<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Como funciona</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
</head>

<body class="pagina-simples">

<header class="topo">
    <?= logo_site() ?>

    <nav class="menu">
        <a href="<?= base_url('index.php?rota=home') ?>">Início</a>
        <a href="<?= base_url('index.php?rota=sobre') ?>">Sobre</a>
        <a href="<?= base_url('index.php?rota=cadastro') ?>">Cadastrar</a>
        <a href="<?= base_url('index.php?rota=login') ?>">Login</a>
    </nav>
</header>

<section class="principal">
    <div class="conteudo texto-centro">

        <h1>Como funciona</h1>

        <p>
            Você pode se cadastrar para buscar orientação em saúde mental,
            ou como profissional para apoiar pessoas que precisam de ajuda.
        </p>

        <p>
            Após o cadastro, você poderá enviar um formulário, marcar terapias online,
            trocar mensagens e acompanhar seu histórico.
        </p>

        <p>
            A plataforma não emite diagnóstico, laudo ou atestado. O objetivo é orientar,
            acolher e aproximar a pessoa de um profissional.
        </p>

    </div>
</section>

<footer class="rodape">
    <p>&copy; VivaMente</p>
</footer>

</body>
</html>
