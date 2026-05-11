<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastrar Admin | VivaMente</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
<style>
    body.admin-page { display: block; background: #f4f7fb; }
    .admin-form-container { max-width: 720px; margin: 0 auto; padding: 36px 20px 60px; }
    .admin-form-card { background: #fff; border: 1px solid #dfe7f1; border-radius: 8px; padding: 24px; box-shadow: 0 8px 24px rgba(34,58,94,0.08); }
    .admin-form-card h1 { color: #114a8d; margin-bottom: 8px; }
    .admin-form-card p { color: #5c6b7d; margin-bottom: 20px; }
    .admin-form-card form { display: grid; gap: 14px; }
    .admin-form-card input { width: 100%; height: 46px; border: 1px solid #cfdbea; border-radius: 8px; padding: 0 12px; font-size: 1rem; }
    .admin-form-acoes { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-top: 6px; }
    .admin-form-acoes .button { margin: 0; }
    .voltar-admin { color: #1565c0; font-weight: 700; text-decoration: none; }
</style>
</head>
<body class="admin-page">

<header class="topo">
    <?= logo_site() ?>
    <nav class="menu">
        <?php if (autenticado() && $_SESSION['tipo'] === 'admin'): ?>
            <a href="<?= base_url('index.php?rota=painel-admin') ?>">Painel</a>
            <a href="<?= base_url('index.php?rota=logout') ?>">Sair</a>
        <?php else: ?>
            <a href="<?= inicio_url() ?>">Início</a>
            <a href="<?= base_url('index.php?rota=admin-login') ?>">Login admin</a>
        <?php endif; ?>
    </nav>
</header>

<main class="admin-form-container">
    <section class="admin-form-card">
        <h1>Cadastrar administrador</h1>
        <p>Crie uma conta com acesso ao painel administrativo.</p>

        <form action="<?= base_url('index.php') ?>" method="POST">
            <input type="hidden" name="acao" value="cadastrar_admin">
            <input type="text" name="nome" placeholder="Nome do administrador" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>

            <div class="admin-form-acoes">
                <button class="button btn-paciente" type="submit">Cadastrar admin</button>
                <?php if (autenticado() && $_SESSION['tipo'] === 'admin'): ?>
                    <a class="voltar-admin" href="<?= base_url('index.php?rota=painel-admin') ?>">Voltar ao painel</a>
                <?php else: ?>
                    <a class="voltar-admin" href="<?= base_url('index.php?rota=admin-login') ?>">Voltar ao login admin</a>
                <?php endif; ?>
            </div>
        </form>
    </section>
</main>

</body>
</html>
