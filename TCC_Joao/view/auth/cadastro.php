<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Escolha seu cadastro | VivaMente</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
<style>
    .cadastro-escolha { max-width: 920px; margin: 0 auto; padding: 54px 20px 70px; }
    .cadastro-escolha h1 { color: #0b6b73; font-size: clamp(2rem, 4vw, 2.8rem); text-align: center; margin-bottom: 10px; }
    .cadastro-escolha > p { max-width: 640px; margin: 0 auto 30px; text-align: center; color: #5d7274; font-size: 1.05rem; line-height: 1.6; }
    .cadastro-opcoes { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .cadastro-card { background: #fff; border: 1px solid #d6ebe7; border-radius: 8px; padding: 26px; box-shadow: 0 14px 32px rgba(31,92,91,0.08); text-decoration: none; color: #21494c; transition: transform 0.2s, box-shadow 0.2s; }
    .cadastro-card:hover { transform: translateY(-2px); box-shadow: 0 18px 36px rgba(31,92,91,0.12); }
    .cadastro-icone { width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #fff; font-size: 1.55rem; font-weight: 800; }
    .cadastro-paciente .cadastro-icone { background: #0b6b73; }
    .cadastro-profissional .cadastro-icone { background: #f4b56f; color: #3f250d; }
    .cadastro-card h2 { color: #0b6b73; font-size: 1.45rem; margin-bottom: 10px; }
    .cadastro-card p { color: #5d7274; line-height: 1.6; margin-bottom: 18px; }
    .cadastro-card span { display: inline-flex; align-items: center; justify-content: center; min-height: 42px; padding: 0 16px; border-radius: 8px; font-weight: 800; }
    .cadastro-paciente span { background: #0b6b73; color: #fff; }
    .cadastro-profissional span { background: #f4b56f; color: #3f250d; }
    @media (max-width: 760px) { .cadastro-opcoes { grid-template-columns: 1fr; } }
</style>
</head>
<body class="cadastro-escolha-page pagina-simples">

<header class="topo">
    <?= logo_site() ?>
    <nav class="menu">
        <a href="<?= base_url('index.php?rota=home') ?>">Início</a>
        <a href="<?= base_url('index.php?rota=como-funciona') ?>">Como funciona</a>
        <a href="<?= base_url('index.php?rota=sobre') ?>">Sobre</a>
        <a href="<?= base_url('index.php?rota=login') ?>">Login</a>
    </nav>
</header>

<main class="cadastro-escolha">
    <h1>Como você quer se cadastrar?</h1>
    <p>Escolha o tipo de conta para receber apoio ou para oferecer orientação profissional.</p>

    <section class="cadastro-opcoes">
        <a class="cadastro-card cadastro-paciente" href="<?= base_url('index.php?rota=cadastro-paciente') ?>">
            <div class="cadastro-icone">P</div>
            <h2>Sou paciente</h2>
            <p>Crie uma conta para enviar seu formulário, encontrar profissionais e acompanhar suas terapias.</p>
            <span>Cadastrar paciente</span>
        </a>

        <a class="cadastro-card cadastro-profissional" href="<?= base_url('index.php?rota=cadastro-profissional') ?>">
            <div class="cadastro-icone">+</div>
            <h2>Sou profissional</h2>
            <p>Cadastre seu perfil para receber solicitacoes e orientar pessoas que precisam de apoio.</p>
            <span>Cadastrar profissional</span>
        </a>
    </section>
</main>

</body>
</html>
