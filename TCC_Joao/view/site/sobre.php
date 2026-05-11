<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sobre | VivaMente</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
<style>
    .sobre-container { max-width: 900px; margin: 0 auto; padding: 54px 20px 70px; }
    .sobre-card { background: #fff; border: 1px solid #d6ebe7; border-radius: 8px; padding: 30px; box-shadow: 0 14px 32px rgba(31,92,91,0.08); }
    .sobre-card h1 { color: #0b6b73; font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 14px; }
    .sobre-card p { color: #5d7274; line-height: 1.65; font-size: 1.05rem; margin-bottom: 16px; }
    .sobre-contatos { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-top: 24px; }
    .sobre-contato { background: #eef8f5; border: 1px solid #d6ebe7; border-radius: 8px; padding: 18px; }
    .sobre-contato strong { display: block; color: #0b6b73; margin-bottom: 6px; }
    .sobre-contato a { color: #21494c; font-weight: 700; text-decoration: none; }
    @media (max-width: 700px) {
        .sobre-contatos { grid-template-columns: 1fr; }
    }
</style>
</head>
<body class="pagina-simples">

<header class="topo">
    <?= logo_site() ?>
    <nav class="menu">
        <a href="<?= base_url('index.php?rota=home') ?>">Início</a>
        <a href="<?= base_url('index.php?rota=como-funciona') ?>">Como funciona</a>
        <a href="<?= base_url('index.php?rota=cadastro') ?>">Cadastrar</a>
        <a href="<?= base_url('index.php?rota=login') ?>">Login</a>
    </nav>
</header>

<main class="sobre-container">
    <section class="sobre-card">
        <h1>Sobre o VivaMente</h1>
        <p>
            O VivaMente é uma plataforma criada para aproximar pessoas que enfrentam dificuldades emocionais
            de profissionais preparados para oferecer orientação, acolhimento e apoio.
        </p>
        <p>
            Nosso objetivo é facilitar o primeiro contato com um profissional, sem burocracia e sem substituir
            atendimentos especializados. A plataforma não emite diagnóstico, laudo ou atestado.
        </p>
        <p>
            Caso precise falar com a equipe do site, use os contatos abaixo.
        </p>

        <div class="sobre-contatos">
            <div class="sobre-contato">
                <strong>Telefone</strong>
                <a href="tel:+5541999999999">(41) 99999-9999</a>
            </div>
            <div class="sobre-contato">
                <strong>Email</strong>
                <a href="mailto:contato@vivamente.com">contato@vivamente.com</a>
            </div>
        </div>
    </section>
</main>

<footer class="rodape">
    <p>&copy; VivaMente</p>
</footer>

</body>
</html>
