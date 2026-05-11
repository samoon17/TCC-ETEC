<?php
// Variaveis esperadas: $usuarios, $profissionais
$totalUsuarios = count($usuarios);
$totalProfissionais = count($profissionais);
$profissionaisPendentes = count(array_filter($profissionais, fn($p) => (int) $p['validado'] !== 1));
$usuariosBloqueados = count(array_filter($usuarios, fn($u) => $u['status'] === 'bloqueado'));
$profissionaisPorUsuario = [];
foreach ($profissionais as $profissional) {
    $profissionaisPorUsuario[(int) $profissional['id_usuario']] = $profissional;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Admin | VivaMente</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
<style>
    body.admin-page { display: block; background: #f4f7fb; }
    .admin-container { max-width: 1180px; margin: 0 auto; padding: 34px 20px 60px; }
    .admin-cabecalho { display: flex; justify-content: space-between; gap: 20px; align-items: flex-start; margin-bottom: 24px; }
    .admin-cabecalho h1 { color: #114a8d; font-size: clamp(2rem, 4vw, 3rem); }
    .admin-cabecalho p { color: #5c6b7d; margin-top: 8px; }
    .admin-cabecalho .button { margin: 0; white-space: nowrap; }
    .admin-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 26px; }
    .admin-metrica { background: #fff; border: 1px solid #dfe7f1; border-radius: 8px; padding: 18px; box-shadow: 0 8px 20px rgba(34,58,94,0.08); }
    .admin-metrica strong { display: block; color: #0f5fa8; font-size: 2rem; }
    .admin-secao { background: #fff; border: 1px solid #dfe7f1; border-radius: 8px; padding: 20px; margin-top: 22px; box-shadow: 0 8px 24px rgba(34,58,94,0.08); overflow-x: auto; }
    .admin-secao h2 { color: #16324f; margin-bottom: 14px; }
    .admin-tabela { width: 100%; border-collapse: collapse; min-width: 860px; }
    .admin-tabela th, .admin-tabela td { padding: 12px 10px; border-bottom: 1px solid #e5edf6; text-align: left; vertical-align: top; }
    .admin-tabela th { color: #516075; font-size: 0.9rem; text-transform: uppercase; }
    .admin-badge { display: inline-flex; padding: 5px 9px; border-radius: 999px; font-size: 0.85rem; font-weight: 700; }
    .badge-ativo, .badge-validado { background: #e6f4ea; color: #1b7f35; }
    .badge-inativo, .badge-pendente { background: #fff4d6; color: #926100; }
    .badge-bloqueado { background: #fde2e1; color: #a32219; }
    .admin-acoes { display: flex; flex-wrap: wrap; gap: 8px; }
    .admin-acoes form { margin: 0; }
    .admin-btn { border: 0; border-radius: 7px; padding: 8px 10px; cursor: pointer; font-weight: 700; color: #fff; }
    .btn-validar, .btn-ativar { background: #2e7d32; }
    .btn-bloquear { background: #c62828; }
    .btn-inativar { background: #ef8f00; }
    .btn-deletar { background: #6b2430; }
    .admin-sucesso { background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 18px; }
    @media (max-width: 900px) {
        .admin-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .admin-cabecalho { flex-direction: column; }
    }
</style>
</head>
<body class="admin-page">

<header class="topo">
    <?= logo_site() ?>
    <nav class="menu">
        <a href="<?= base_url('index.php?rota=painel-admin') ?>">Admin</a>
        <a href="<?= base_url('index.php?rota=home') ?>">Site</a>
        <a href="<?= base_url('index.php?rota=logout') ?>">Sair</a>
    </nav>
</header>

<main class="admin-container">
    <div class="admin-cabecalho">
        <div>
            <h1>Painel administrativo</h1>
            <p>Gerencie cadastros, bloqueios de usuários e validação dos registros profissionais.</p>
        </div>
        <a href="<?= base_url('index.php?rota=cadastro-admin') ?>" class="button btn-paciente">Cadastrar admin</a>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="admin-sucesso">Ação realizada com sucesso.</div>
    <?php endif; ?>
    <?php if (isset($_GET['admin_criado'])): ?>
        <div class="admin-sucesso">Administrador cadastrado com sucesso.</div>
    <?php endif; ?>

    <section class="admin-grid">
        <div class="admin-metrica"><strong><?= $totalUsuarios ?></strong>Usuários</div>
        <div class="admin-metrica"><strong><?= $totalProfissionais ?></strong>Profissionais</div>
        <div class="admin-metrica"><strong><?= $profissionaisPendentes ?></strong>Registros pendentes/bloqueados</div>
        <div class="admin-metrica"><strong><?= $usuariosBloqueados ?></strong>Usuários bloqueados</div>
    </section>

    <section class="admin-secao">
        <h2>Usuarios, bloqueios e registros</h2>
        <table class="admin-tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Registro</th>
                    <th>Cadastro</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <?php $prof = $profissionaisPorUsuario[(int) $usuario['id_usuario']] ?? null; ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['nome']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= htmlspecialchars($usuario['tipo_usuario']) ?></td>
                        <td><span class="admin-badge badge-<?= htmlspecialchars($usuario['status']) ?>"><?= htmlspecialchars($usuario['status']) ?></span></td>
                        <td>
                            <?php if ($prof): ?>
                                <?= htmlspecialchars($prof['registro_profissional'] ?: 'Não informado') ?><br>
                                <?php if ((int) $prof['validado'] === 1): ?>
                                    <span class="admin-badge badge-validado">Validado</span>
                                <?php else: ?>
                                    <span class="admin-badge badge-pendente">Pendente/bloqueado</span>
                                <?php endif; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($usuario['data_cadastro']) ?></td>
                        <td>
                            <div class="admin-acoes">
                                <?php if ($prof): ?>
                                    <?php if ((int) $prof['validado'] !== 1): ?>
                                        <form action="<?= base_url('index.php') ?>" method="POST">
                                            <input type="hidden" name="acao" value="admin_gerenciar">
                                            <input type="hidden" name="tipo_acao" value="validar_crm">
                                            <input type="hidden" name="id_profissional" value="<?= htmlspecialchars($prof['id_profissional']) ?>">
                                            <button class="admin-btn btn-validar" type="submit">Validar registro</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?= base_url('index.php') ?>" method="POST">
                                            <input type="hidden" name="acao" value="admin_gerenciar">
                                            <input type="hidden" name="tipo_acao" value="bloquear_crm">
                                            <input type="hidden" name="id_profissional" value="<?= htmlspecialchars($prof['id_profissional']) ?>">
                                            <button class="admin-btn btn-bloquear" type="submit">Bloquear registro</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <form action="<?= base_url('index.php') ?>" method="POST">
                                    <input type="hidden" name="acao" value="admin_gerenciar">
                                    <input type="hidden" name="tipo_acao" value="ativar_usuario">
                                    <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">
                                    <button class="admin-btn btn-ativar" type="submit">Ativar</button>
                                </form>
                                <form action="<?= base_url('index.php') ?>" method="POST">
                                    <input type="hidden" name="acao" value="admin_gerenciar">
                                    <input type="hidden" name="tipo_acao" value="bloquear_usuario">
                                    <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">
                                    <button class="admin-btn btn-bloquear" type="submit">Bloquear</button>
                                </form>
                                <form action="<?= base_url('index.php') ?>" method="POST">
                                    <input type="hidden" name="acao" value="admin_gerenciar">
                                    <input type="hidden" name="tipo_acao" value="inativar_usuario">
                                    <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">
                                    <button class="admin-btn btn-inativar" type="submit">Inativar</button>
                                </form>
                                <form action="<?= base_url('index.php') ?>" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar este cadastro?');">
                                    <input type="hidden" name="acao" value="admin_gerenciar">
                                    <input type="hidden" name="tipo_acao" value="deletar_usuario">
                                    <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario['id_usuario']) ?>">
                                    <button class="admin-btn btn-deletar" type="submit">Deletar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

</body>
</html>
