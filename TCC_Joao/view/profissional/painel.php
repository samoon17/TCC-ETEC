<?php
// Variáveis esperadas: $profissional, $formularios
function iniciais_profissional(string $nome): string {
    $partes = preg_split('/\s+/', trim($nome));
    $primeira = $partes[0][0] ?? '';
    $ultima = count($partes) > 1 ? ($partes[count($partes) - 1][0] ?? '') : '';
    return strtoupper($primeira . $ultima);
}

$fotoUrl = foto_profissional_url((int) $profissional['id_usuario']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Painel Profissional | VivaMente</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
<style>
    body.profissional-page { display: block; background: #f6fbfa; }
    .profissional-container { max-width: 1100px; margin: 0 auto; padding: 34px 20px 60px; }
    .perfil-topo { display: grid; grid-template-columns: 150px 1fr; gap: 24px; align-items: center; background: #fff; border: 1px solid #d6ebe7; border-radius: 8px; padding: 24px; box-shadow: 0 14px 32px rgba(31,92,91,0.08); margin-bottom: 24px; }
    .perfil-foto { width: 128px; height: 128px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0b6b73, #5aaa8d); color: #fff; font-size: 2.1rem; font-weight: 900; }
    .perfil-foto img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .foto-upload { display: grid; justify-items: center; gap: 8px; }
    .foto-upload-label { position: relative; cursor: pointer; }
    .foto-upload-label::after { content: "Trocar foto"; position: absolute; inset: auto 0 0; padding: 8px 4px; background: rgba(8, 54, 59, 0.78); color: #fff; font-size: 0.78rem; font-weight: 800; text-align: center; }
    .foto-upload input { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; }
    .foto-ajuda { color: #5d7274; font-size: 0.86rem; text-align: center; }
    .perfil-topo h1 { color: #0b6b73; font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 6px; }
    .perfil-topo p { color: #5d7274; line-height: 1.55; }
    .perfil-status { display: inline-flex; margin-top: 10px; padding: 6px 10px; border-radius: 999px; background: #eaf8f5; color: #0b6b73; font-weight: 800; font-size: 0.9rem; }
    .painel-grid { display: grid; grid-template-columns: minmax(0, 420px) minmax(0, 1fr); gap: 22px; align-items: flex-start; }
    .painel-card { background: #fff; border: 1px solid #d6ebe7; border-radius: 8px; padding: 22px; box-shadow: 0 14px 32px rgba(31,92,91,0.08); }
    .painel-card h2 { color: #0b6b73; margin-bottom: 14px; }
    .perfil-form { display: grid; gap: 12px; }
    .perfil-form input, .perfil-form select, .perfil-form textarea { width: 100%; border: 1px solid #c7ded9; border-radius: 8px; padding: 12px 14px; font: inherit; color: #333; background: #fff; }
    .perfil-form textarea { min-height: 120px; resize: vertical; }
    .perfil-form input:focus, .perfil-form select:focus, .perfil-form textarea:focus { border-color: #178b8f; box-shadow: 0 0 0 3px rgba(23,139,143,0.10); outline: none; }
    .sucesso { background: #eaf8f5; color: #0b6b73; border: 1px solid #b8ddd6; padding: 12px; border-radius: 8px; margin-bottom: 18px; }
    .formulario-card { border: 1px solid #d6ebe7; border-radius: 8px; padding: 16px; margin-top: 14px; background: #fff; }
    .formulario-card p { margin-bottom: 9px; color: #526b6e; line-height: 1.45; }
    .formulario-card strong { color: #21494c; }
    .resposta-form { display: grid; gap: 10px; margin-top: 12px; }
    .resposta-form input, .resposta-form textarea { width: 100%; border: 1px solid #c7ded9; border-radius: 8px; padding: 12px 14px; font: inherit; }
    .resposta-form textarea { min-height: 120px; resize: vertical; }
    .resposta-form input:focus, .resposta-form textarea:focus { border-color: #178b8f; box-shadow: 0 0 0 3px rgba(23,139,143,0.10); outline: none; }
    .resposta-ajuda { color: #5d7274; font-size: 0.92rem; line-height: 1.45; }
    .vazio { color: #5d7274; line-height: 1.55; }
    @media (max-width: 900px) {
        .painel-grid, .perfil-topo { grid-template-columns: 1fr; }
        .perfil-foto { margin: 0 auto; }
        .perfil-topo { text-align: center; }
    }
</style>
</head>
<body class="profissional-page">

<header class="topo">
    <?= logo_site() ?>
    <nav class="menu">
        <a href="<?= base_url('index.php?rota=painel-profissional') ?>">Painel</a>
        <a href="<?= base_url('index.php?rota=home') ?>">Site</a>
        <a href="<?= base_url('index.php?rota=logout') ?>">Sair</a>
    </nav>
</header>

<main class="profissional-container">
    <?php if (isset($_GET['perfil_ok'])): ?>
        <div class="sucesso">Perfil atualizado com sucesso.</div>
    <?php endif; ?>
    <?php if (isset($_GET['resposta_ok'])): ?>
        <div class="sucesso">Resposta enviada ao paciente com sucesso.</div>
    <?php endif; ?>

    <section class="perfil-topo">
        <div class="foto-upload">
            <label class="foto-upload-label" for="fotoPerfil">
                <span class="perfil-foto">
                    <?php if ($fotoUrl): ?>
                        <img src="<?= htmlspecialchars($fotoUrl) ?>" alt="Foto de <?= htmlspecialchars($profissional['nome']) ?>">
                    <?php else: ?>
                        <?= htmlspecialchars(iniciais_profissional($profissional['nome'])) ?>
                    <?php endif; ?>
                </span>
            </label>
            <span class="foto-ajuda">Clique na foto para trocar</span>
        </div>
        <div>
            <h1>Ola, <?= htmlspecialchars($profissional['nome']) ?></h1>
            <p><?= htmlspecialchars($profissional['especialidade'] ?: 'Profissional de saúde mental') ?></p>
            <p><?= htmlspecialchars(trim(($profissional['cidade'] ?? '') . ' - ' . ($profissional['estado'] ?? ''), ' -')) ?></p>
            <span class="perfil-status">Registro validado</span>
        </div>
    </section>

    <section class="painel-grid">
        <div class="painel-card">
            <h2>Meu perfil</h2>
            <form action="<?= base_url('index.php') ?>" method="POST" enctype="multipart/form-data" class="perfil-form">
                <input type="hidden" name="acao" value="atualizar_perfil_profissional">

                <input id="fotoPerfil" type="file" name="foto" accept="image/jpeg,image/png,image/webp" hidden>

                <input type="text" name="nome" value="<?= htmlspecialchars($profissional['nome']) ?>" placeholder="Nome completo" required>
                <input type="email" name="email" value="<?= htmlspecialchars($profissional['email']) ?>" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Nova senha (opcional)">
                <input type="text" name="registro" value="<?= htmlspecialchars($profissional['registro_profissional']) ?>" placeholder="Registro profissional" required>
                <input type="text" name="especialidade" value="<?= htmlspecialchars($profissional['especialidade'] ?? '') ?>" placeholder="Especialidade">
                <input type="text" name="cidade" value="<?= htmlspecialchars($profissional['cidade'] ?? '') ?>" placeholder="Cidade">
                <select name="estado" required>
                    <option value="">Estado</option>
                    <?php foreach (estados_brasileiros() as $uf => $nomeEstado): ?>
                        <option value="<?= htmlspecialchars($uf) ?>" <?= ($profissional['estado'] ?? '') === $uf ? 'selected' : '' ?>>
                            <?= htmlspecialchars($uf . ' - ' . $nomeEstado) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <textarea name="descricao" placeholder="Fale sobre sua abordagem" required><?= htmlspecialchars($profissional['descricao'] ?? '') ?></textarea>

                <button class="button btn-paciente" type="submit">Salvar perfil</button>
            </form>
        </div>

        <div class="painel-card">
            <h2>Formulários recebidos</h2>

            <?php if (empty($formularios)): ?>
                <p class="vazio">Nenhum formulário foi enviado até agora.</p>
            <?php endif; ?>

            <?php foreach ($formularios as $f): ?>
                <div class="formulario-card">
                    <p><strong>Paciente:</strong> <?= htmlspecialchars($f['paciente']) ?></p>
                    <p><strong>Descrição:</strong></p>
                    <p><?= nl2br(htmlspecialchars($f['descricao'])) ?></p>
                    <p><strong>Enviado em:</strong> <?= htmlspecialchars($f['data_envio']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($f['status'] ?? 'enviado') ?></p>

                    <?php if (($f['status'] ?? '') === 'respondido'): ?>
                        <p class="resposta-ajuda"><strong>Formulário já respondido.</strong></p>
                    <?php else: ?>
                        <form action="<?= base_url('index.php') ?>" method="POST" class="resposta-form">
                            <input type="hidden" name="acao" value="responder_formulario">
                            <input type="hidden" name="id_paciente" value="<?= htmlspecialchars($f['id_paciente']) ?>">
                            <input type="hidden" name="id_formulario" value="<?= htmlspecialchars($f['id']) ?>">
                            <textarea name="relatorio" placeholder="Escreva sua resposta, orientações e observações para o paciente..." required></textarea>
                            <input type="tel" name="whatsapp" placeholder="WhatsApp para contato (ex: 11999999999)" required>
                            <p class="resposta-ajuda">Caso o paciente queira tentar uma videochamada, ele podera entrar em contato pelo WhatsApp informado.</p>
                            <button class="button btn-psicologo" type="submit">Enviar resposta</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

</body>
</html>
