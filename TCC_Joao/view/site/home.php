<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VivaMente</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
</head>

<body class="home-page">

<header class="topo">
    <?= logo_site() ?>

    <nav class="menu">
        <a href="<?= base_url('index.php?rota=home') ?>">Início</a>
        <a href="<?= base_url('index.php?rota=como-funciona') ?>">Como funciona</a>
        <a href="<?= base_url('index.php?rota=sobre') ?>">Sobre</a>
        <a href="<?= base_url('index.php?rota=cadastro') ?>">Cadastrar</a>
        <a href="<?= base_url('index.php?rota=login') ?>">Login</a>
    </nav>
</header>

<section class="home-hero">
    <div class="home-hero-conteudo">
        <h1>Orientação online para saúde mental</h1>
        <p>Um espaço simples para receber apoio, conversar com profissionais e encontrar orientação.</p>
        <div class="home-acoes">
            <a href="<?= base_url('index.php?rota=cadastro-paciente') ?>" class="home-cta">
                Quero começar agora
            </a>
            <a href="<?= base_url('index.php?rota=cadastro-profissional') ?>" class="home-cta home-cta-profissional">
                Sou profissional
            </a>
        </div>
    </div>
</section>

<section class="home-proposta">
    <div class="home-proposta-conteudo">
        <h2>O VivaMente é <span>apoio acessível</span> para saúde mental</h2>
        <p>
            O VivaMente existe para facilitar o acesso de pessoas que enfrentam dificuldades emocionais
            a profissionais preparados para orientar, acolher e ajudar no primeiro passo.
        </p>
        <p>
            Aqui, cuidar do seu bem-estar fica mais simples: você envia um formulário, recebe uma resposta
            do profissional e pode combinar contato pelo WhatsApp caso queira conversar melhor.
        </p>
        <p class="home-aviso">
            A plataforma oferece orientação e apoio, mas não emite diagnóstico, laudo ou atestado.
        </p>
    </div>
</section>

<footer class="rodape">
    <p>Esta plataforma oferece orientação e apoio. Não emite diagnóstico, laudo ou atestado.</p>
</footer>

</body>
</html>
