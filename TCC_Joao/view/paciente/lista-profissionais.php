<?php
// Variável esperada: $profissionais
function iniciais(string $nome): string {
    $partes = preg_split('/\s+/', trim($nome));
    $primeira = $partes[0][0] ?? '';
    $ultima = count($partes) > 1 ? ($partes[count($partes) - 1][0] ?? '') : '';
    return strtoupper($primeira . $ultima);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Escolher profissional | VivaMente</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
<style>
    body.lista-profissionais-page { display: block; background: #f6fbfa; color: #21494c; }
    .lista-profissionais-page .topo { position: sticky; top: 0; z-index: 10; box-shadow: 0 8px 26px rgba(12,96,103,0.16); }
    .hero-profissionais, .lista-profissionais { max-width: 1180px; margin: 0 auto; padding: 36px 20px 18px; }
    .hero-profissionais h1 { font-size: clamp(2rem, 4vw, 3rem); color: #0b6b73; margin-bottom: 14px; }
    .hero-profissionais p { max-width: 760px; font-size: 1.05rem; line-height: 1.6; color: #5d7274; }
    .filtros-profissionais { display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; max-width: 1180px; margin: 0 auto; padding: 0 20px 18px; }
    .campo-filtro { display: flex; flex-direction: column; gap: 6px; min-width: 240px; }
    .campo-filtro label { font-weight: 700; color: #0b6b73; }
    .campo-filtro select { height: 46px; border: 1px solid #c7ded9; border-radius: 8px; padding: 0 12px; background: #fff; color: #21494c; font-size: 1rem; }
    .botao-filtro, .limpar-filtro { height: 46px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; padding: 0 18px; font-weight: 700; text-decoration: none; border: 0; cursor: pointer; }
    .botao-filtro { background: #0b6b73; color: #fff; }
    .limpar-filtro { background: #fff; color: #0b6b73; border: 1px solid #c7ded9; }
    .lista-profissionais { padding-top: 12px; padding-bottom: 50px; }
    .card-profissional { display: grid; grid-template-columns: 150px 1fr 250px; gap: 28px; background: #fff; border: 1px solid #d6ebe7; border-radius: 8px; padding: 28px; margin-bottom: 24px; box-shadow: 0 14px 32px rgba(31,92,91,0.08); }
    .perfil-lateral { text-align: center; }
    .avatar-profissional { width: 118px; height: 118px; border-radius: 50%; margin: 0 auto 18px; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; color: #fff; background: linear-gradient(135deg, #0b6b73, #5aaa8d); box-shadow: 0 14px 28px rgba(31,92,91,0.18); }
    .topo-card { display: flex; justify-content: space-between; gap: 16px; align-items: flex-start; margin-bottom: 14px; }
    .topo-card h2 { font-size: clamp(1.45rem, 3vw, 2rem); margin-bottom: 4px; color: #161d29; }
    .especialidade, .registro, .descricao-profissional { color: #607089; }
    .registro { white-space: nowrap; font-weight: 700; font-size: 0.95rem; }
    .meta-profissional { display: flex; flex-wrap: wrap; gap: 18px; margin-bottom: 16px; color: #2d3748; font-size: 0.98rem; }
    .descricao-profissional { line-height: 1.7; }
    .acao-card { display: flex; flex-direction: column; justify-content: center; gap: 14px; }
    .cta-formulario { display: block; text-align: center; text-decoration: none; background: #0b6b73; color: #fff; font-weight: 700; padding: 16px 18px; border-radius: 8px; box-shadow: 0 12px 22px rgba(31,92,91,0.18); }
    .info-contato { background: #eef8f5; border: 1px solid #d6ebe7; border-radius: 8px; padding: 16px; color: #5d7274; line-height: 1.7; }
    .vazio { background: #fff; border: 1px solid #dfe7f1; border-radius: 16px; padding: 24px; color: #516075; }
    .aviso-remarcar { max-width: 1180px; margin: 0 auto 18px; padding: 14px 20px; background: #fff3cd; color: #664d03; border: 1px solid #ffe69c; border-radius: 12px; font-weight: 700; }
    @media (max-width: 980px) {
        .card-profissional { grid-template-columns: 1fr; }
        .perfil-lateral { display: flex; align-items: center; justify-content: flex-start; gap: 18px; text-align: left; }
        .avatar-profissional { margin: 0; width: 92px; height: 92px; font-size: 1.6rem; }
    }
    @media (max-width: 768px) {
        .card-profissional { padding: 22px; }
        .topo-card, .perfil-lateral { flex-direction: column; }
        .perfil-lateral { align-items: center; text-align: center; }
    }
</style>
</head>

<body class="lista-profissionais-page">

<header class="topo">
    <?= logo_site() ?>
    <nav class="menu">
        <a href="<?= inicio_url() ?>">Início</a>
        <a href="<?= base_url('index.php?rota=como-funciona') ?>">Como funciona</a>
        <a href="<?= base_url('index.php?rota=sobre') ?>">Sobre</a>
        <a href="<?= base_url('index.php?rota=cadastro') ?>">Cadastrar</a>
    </nav>
</header>

<section class="hero-profissionais">
    <h1>Escolha um profissional para receber seu formulário</h1>
    <p>Veja os profissionais cadastrados, conheça a área de atuação de cada um e envie seu formulário para receber orientação e apoio.</p>
</section>

<form class="filtros-profissionais" action="<?= base_url('index.php') ?>" method="GET">
    <input type="hidden" name="rota" value="lista-profissionais">
    <div class="campo-filtro">
        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            <option value="">Todos os estados</option>
            <?php foreach (estados_brasileiros() as $uf => $nomeEstado): ?>
                <option value="<?= htmlspecialchars($uf) ?>" <?= ($estadoSelecionado ?? '') === $uf ? 'selected' : '' ?>>
                    <?= htmlspecialchars($uf . ' - ' . $nomeEstado) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="botao-filtro" type="submit">Filtrar</button>
    <?php if (!empty($estadoSelecionado)): ?>
        <a class="limpar-filtro" href="<?= base_url('index.php?rota=lista-profissionais') ?>">Limpar</a>
    <?php endif; ?>
</form>

<main class="lista-profissionais">
    <?php if (isset($_GET['remarcar'])): ?>
        <div class="aviso-remarcar">Sua terapia anterior foi cancelada. Escolha um profissional para enviar um novo formulário e remarcar.</div>
    <?php endif; ?>

    <?php if (empty($profissionais)): ?>
        <div class="vazio">Nenhum profissional encontrado para este filtro.</div>
    <?php endif; ?>

    <?php foreach ($profissionais as $prof): ?>
        <article class="card-profissional">
            <div class="perfil-lateral">
                <?php $fotoProfissional = foto_profissional_url((int) $prof['id_usuario']); ?>
                <div class="avatar-profissional">
                    <?php if ($fotoProfissional): ?>
                        <img src="<?= htmlspecialchars($fotoProfissional) ?>" alt="Foto de <?= htmlspecialchars($prof['nome']) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <?php else: ?>
                        <?= htmlspecialchars(iniciais($prof['nome'])) ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="conteudo-profissional">
                <div class="topo-card">
                    <div>
                        <h2><?= htmlspecialchars($prof['nome']) ?></h2>
                        <p class="especialidade"><?= htmlspecialchars($prof['especialidade'] ?: 'Profissional de saúde mental') ?></p>
                    </div>
                    <?php if (!empty($prof['registro_profissional'])): ?>
                        <span class="registro">Registro: <?= htmlspecialchars($prof['registro_profissional']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="meta-profissional">
                    <?php if (!empty($prof['cidade']) || !empty($prof['estado'])): ?>
                        <span><strong>Local:</strong> <?= htmlspecialchars(trim(($prof['cidade'] ?? '') . ' - ' . ($prof['estado'] ?? ''), ' -')) ?></span>
                    <?php endif; ?>
                    <span><strong>Atendimento:</strong> Online</span>
                </div>

                <p class="descricao-profissional">
                    <?= nl2br(htmlspecialchars($prof['descricao'] ?: 'Este profissional ainda não adicionou uma descrição completa.')) ?>
                </p>
            </div>

            <div class="acao-card">
                <a href="<?= base_url('index.php?rota=formulario&profissional=' . urlencode($prof['id_profissional'])) ?>" class="cta-formulario">
                    Enviar formulário para <?= htmlspecialchars(explode(' ', trim($prof['nome']))[0]) ?>
                </a>
                <div class="info-contato">
                    <strong>Próximo passo:</strong><br>
                    O profissional recebe seu formulário e pode orientar você em uma terapia online.
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</main>

</body>
</html>
