<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro profissional | VivaMente</title>
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
        <h1>Cadastro profissional</h1>
        <p>Cadastre seu perfil para oferecer orientação e apoio por terapia online.</p>

        <form action="<?= base_url('index.php') ?>" method="POST" class="formulario">
            <input type="hidden" name="acao" value="cadastrar_profissional">
            <input type="text" name="nome" placeholder="Nome completo" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input
                type="text"
                name="registro"
                placeholder="Registro profissional (ex: CRP/SP 123456)"
                maxlength="14"
                title="Informe seu registro profissional"
                oninput="
                    let valor = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                    if (valor.startsWith('CRP')) valor = valor.slice(3);
                    const uf = valor.replace(/[^A-Z]/g, '').slice(0, 2);
                    const numero = valor.replace(/\D/g, '').slice(0, 6);
                    this.value = 'CRP' + (uf ? '/' + uf : '') + (numero ? ' ' + numero : '');
                "
                required
            >
            <input type="text" name="especialidade" placeholder="Especialidade (ex: Psicologia clínica)">
            <input type="text" name="cidade" placeholder="Cidade">
            <select name="estado" required>
                <option value="">Estado</option>
                <?php foreach (estados_brasileiros() as $uf => $nomeEstado): ?>
                    <option value="<?= htmlspecialchars($uf) ?>">
                        <?= htmlspecialchars($uf . ' - ' . $nomeEstado) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <textarea name="descricao" placeholder="Fale sobre sua abordagem e como orienta as pessoas" required></textarea>

            <button class="button btn-psicologo">Cadastrar profissional</button>
        </form>
    </div>
</section>

<footer class="rodape">
    <p>&copy; VivaMente</p>
</footer>

</body>
</html>
