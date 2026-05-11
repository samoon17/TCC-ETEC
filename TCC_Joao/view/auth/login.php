<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | VivaMente</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
<style>
    .login-page .principal { min-height: calc(100vh - 116px); }
    .login-card {
        width: 100%;
        max-width: 420px;
        padding: 36px 34px;
        background: #fff;
        border: 1px solid #d6ebe7;
        border-radius: 8px;
        box-shadow: 0 14px 32px rgba(31, 92, 91, 0.08);
    }
    .login-card h1 { color: #0b6b73; text-align: center; font-size: 1.8rem; margin-bottom: 8px; }
    .login-card .subtitulo { color: #5d7274; text-align: center; margin-bottom: 24px; }
    .login-card .formulario { gap: 14px; }
    .campo-label { display: grid; gap: 6px; color: #21494c; font-weight: 700; font-size: 0.95rem; }
    .campo-label input { height: 46px; border: 1.5px solid #c7ded9; border-radius: 8px; padding: 0 12px; font-size: 1rem; outline: none; }
    .campo-label input:focus { border-color: #178b8f; box-shadow: 0 0 0 3px rgba(23, 139, 143, 0.10); }
    .btn-entrar { width: 100%; border: 0; cursor: pointer; margin-top: 4px; }
    .divisor { display: flex; align-items: center; gap: 10px; margin: 20px 0 14px; color: #8aa09d; font-size: 0.9rem; }
    .divisor::before, .divisor::after { content: ""; flex: 1; height: 1px; background: #d6ebe7; }
    .links-cadastro { display: grid; gap: 10px; }
    .links-cadastro a { text-align: center; padding: 12px; border-radius: 8px; font-weight: 700; text-decoration: none; border: 1.5px solid; transition: 0.2s; }
    .link-paciente { border-color: #0b6b73; color: #0b6b73; }
    .link-paciente:hover { background: #0b6b73; color: #fff; }
    .link-profissional { border-color: #d89552; color: #8a551d; }
    .link-profissional:hover { background: #f4b56f; color: #3f250d; }
    .alerta-sucesso { display: none; background: #eaf8f5; color: #0b6b73; border: 1px solid #b8ddd6; border-radius: 8px; padding: 10px 14px; text-align: center; margin-bottom: 16px; }
    .alerta-pendente {
        display: none;
        grid-template-columns: 42px 1fr;
        gap: 12px;
        align-items: flex-start;
        background: #fff8e8;
        color: #5d4210;
        border: 1px solid #f2d37b;
        border-radius: 8px;
        padding: 14px;
        margin-bottom: 18px;
        text-align: left;
    }
    .alerta-pendente strong { display: block; color: #7a5600; margin-bottom: 4px; }
    .alerta-pendente p { margin: 0; line-height: 1.45; font-size: 0.95rem; }
    .alerta-status {
        display: none;
        grid-template-columns: 42px 1fr;
        gap: 12px;
        align-items: flex-start;
        background: #fff3f1;
        color: #6b2d26;
        border: 1px solid #f0b4ac;
        border-radius: 8px;
        padding: 14px;
        margin-bottom: 18px;
        text-align: left;
    }
    .alerta-status strong { display: block; color: #9b3328; margin-bottom: 4px; }
    .alerta-status p { margin: 0; line-height: 1.45; font-size: 0.95rem; }
    .alerta-icone {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f4b56f;
        color: #3f250d;
        font-weight: 900;
        font-size: 1.2rem;
    }
</style>
</head>

<body class="login-page pagina-simples">

<header class="topo">
    <?= logo_site() ?>
    <nav class="menu">
        <a href="<?= base_url('index.php?rota=home') ?>">Início</a>
        <a href="<?= base_url('index.php?rota=como-funciona') ?>">Como funciona</a>
        <a href="<?= base_url('index.php?rota=sobre') ?>">Sobre</a>
    </nav>
</header>

<section class="principal">
    <div class="login-card">
        <h1>Bem-vindo de volta</h1>
        <p class="subtitulo">Entre para acompanhar suas mensagens e terapias.</p>

        <div class="alerta-sucesso" id="alertaSucesso">
            Cadastro realizado com sucesso. Faca login para continuar.
        </div>
        <div class="alerta-pendente" id="alertaPendente">
            <div class="alerta-icone">!</div>
            <div>
                <strong>Cadastro em análise</strong>
                <p>Seu registro profissional ainda está aguardando validação do admin. Assim que for aprovado, você poderá acessar o painel.</p>
            </div>
        </div>
        <div class="alerta-status" id="alertaStatus">
            <div class="alerta-icone">!</div>
            <div>
                <strong id="alertaStatusTitulo">Acesso indisponivel</strong>
                <p id="alertaStatusTexto">Não foi possível acessar sua conta no momento.</p>
            </div>
        </div>

        <form action="<?= base_url('index.php') ?>" method="POST" class="formulario">
            <input type="hidden" name="acao" value="login">

            <label class="campo-label">
                Email
                <input type="email" name="email" placeholder="seu@email.com" required autofocus>
            </label>

            <label class="campo-label">
                Senha
                <input type="password" name="senha" placeholder="Digite sua senha" required>
            </label>

            <button class="button btn-paciente btn-entrar" type="submit">Entrar</button>
        </form>

        <div class="divisor">Não tem conta?</div>

        <div class="links-cadastro">
            <a href="<?= base_url('index.php?rota=cadastro-paciente') ?>" class="link-paciente">Cadastrar como paciente</a>
            <a href="<?= base_url('index.php?rota=cadastro-profissional') ?>" class="link-profissional">Sou profissional</a>
        </div>
    </div>
</section>

<footer class="rodape">
    <p>&copy; VivaMente</p>
</footer>

<script>
if (window.location.search.includes("sucesso=1")) {
    document.getElementById("alertaSucesso").style.display = "block";
}
if (window.location.search.includes("profissional_pendente=1") || window.location.search.includes("validacao_pendente=1")) {
    document.getElementById("alertaPendente").style.display = "grid";
}

const alertaStatus = document.getElementById("alertaStatus");
const alertaStatusTitulo = document.getElementById("alertaStatusTitulo");
const alertaStatusTexto = document.getElementById("alertaStatusTexto");

if (window.location.search.includes("conta_bloqueada=1")) {
    alertaStatusTitulo.textContent = "Conta bloqueada";
    alertaStatusTexto.textContent = "Seu acesso foi bloqueado pelo admin. Entre em contato com a equipe para verificar sua conta.";
    alertaStatus.style.display = "grid";
}

if (window.location.search.includes("conta_inativa=1")) {
    alertaStatusTitulo.textContent = "Conta inativa";
    alertaStatusTexto.textContent = "Sua conta está inativa no momento. Solicite a reativação para voltar a acessar o painel.";
    alertaStatus.style.display = "grid";
}

if (window.location.search.includes("conta_deletada=1")) {
    alertaStatusTitulo.textContent = "Conta não encontrada";
    alertaStatusTexto.textContent = "Esta conta pode ter sido deletada ou não existe mais na plataforma.";
    alertaStatus.style.display = "grid";
}
</script>

</body>
</html>
