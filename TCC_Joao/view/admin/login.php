<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin | VivaMente</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
<style>
    body.admin-login-page { display: block; background: #f4f7fb; }
    .admin-login-wrap { min-height: calc(100vh - 70px); display: flex; align-items: center; justify-content: center; padding: 36px 20px; background: #eef4fb; }
    .admin-login-card { width: 100%; max-width: 430px; background: #fff; border: 1px solid #dfe7f1; border-radius: 8px; padding: 34px 30px; box-shadow: 0 10px 32px rgba(34,58,94,0.12); }
    .admin-login-card h1 { color: #114a8d; font-size: 1.8rem; margin-bottom: 8px; text-align: center; }
    .admin-login-card p { color: #5c6b7d; text-align: center; margin-bottom: 24px; }
    .admin-alerta { display: none; background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; border-radius: 8px; padding: 10px 14px; font-size: 14px; text-align: center; margin-bottom: 16px; }
    .admin-login-form { display: grid; gap: 14px; }
    .admin-login-form label { display: grid; gap: 6px; color: #455a64; font-weight: 700; font-size: 0.95rem; }
    .admin-login-form input { height: 46px; border: 1.5px solid #cfd8e3; border-radius: 8px; padding: 0 12px; font-size: 1rem; outline: none; }
    .admin-login-form input:focus { border-color: #114a8d; box-shadow: 0 0 0 3px rgba(17,74,141,0.1); }
    .admin-login-form .button { width: 100%; margin: 4px 0 0; border: 0; cursor: pointer; }
    .admin-links { display: flex; justify-content: space-between; gap: 12px; margin-top: 18px; font-size: 0.95rem; }
    .admin-links a { color: #1565c0; font-weight: 700; text-decoration: none; }
    @media (max-width: 460px) {
        .admin-links { flex-direction: column; text-align: center; }
    }
</style>
</head>
<body class="admin-login-page">

<header class="topo">
    <?= logo_site() ?>
    <nav class="menu">
        <a href="<?= inicio_url() ?>">Site</a>
    </nav>
</header>

<main class="admin-login-wrap">
    <section class="admin-login-card">
        <h1>Login administrativo</h1>
        <p>Acesse o painel de administracao da plataforma.</p>

        <div class="admin-alerta" id="adminAlerta">
            Administrador cadastrado com sucesso. Faca login para continuar.
        </div>

        <form action="<?= base_url('index.php') ?>" method="POST" class="admin-login-form">
            <input type="hidden" name="acao" value="admin-login">

            <label>
                Email
                <input type="email" name="email" placeholder="admin@email.com" required autofocus>
            </label>

            <label>
                Senha
                <input type="password" name="senha" placeholder="Senha do admin" required>
            </label>

            <button class="button btn-paciente" type="submit">Entrar no painel</button>
        </form>

        <div class="admin-links">
            <a href="<?= base_url('index.php?rota=cadastro-admin') ?>">Cadastrar admin</a>
            <a href="<?= base_url('index.php?rota=login') ?>">Login do site</a>
        </div>
    </section>
</main>

<script>
if (window.location.search.includes("admin_criado=1")) {
    const alerta = document.getElementById("adminAlerta");
    alerta.style.display = "block";
}
</script>

</body>
</html>
