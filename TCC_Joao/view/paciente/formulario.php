<?php
// Variaveis esperadas: $profissionais, $profissionalSelecionado
$perguntas = [
    'sentindo_muito_tempo' => 'Você está se sentindo assim há muito tempo?',
    'rotina_afetada' => 'Isso tem atrapalhado sua rotina, estudos ou trabalho?',
    'sono_apetite' => 'Seu sono ou apetite mudou recentemente?',
    'apoio_proximo' => 'Você sente que tem alguém próximo para conversar?',
    'precisa_orientacao' => 'Você gostaria de receber orientação de um profissional?',
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
<title>Formulário de orientação | VivaMente</title>
<?= favicon_tags() ?>
<style>
    .questionario { display: grid; gap: 14px; margin: 6px 0; }
    .pergunta-card { border: 1px solid #d6ebe7; border-radius: 8px; padding: 14px; background: #fff; }
    .pergunta-card p { margin: 0 0 10px; font-size: 1rem; color: #21494c; font-weight: 700; }
    .opcoes-resposta { display: flex; gap: 10px; flex-wrap: wrap; }
    .opcoes-resposta label { display: inline-flex; align-items: center; gap: 6px; min-height: 38px; padding: 0 12px; border: 1px solid #c7ded9; border-radius: 999px; color: #5d7274; cursor: pointer; }
    .opcoes-resposta input { width: auto; }
</style>
</head>
<body class="pagina-simples">

<header class="topo">
    <?= logo_site() ?>
    <nav class="menu">
        <a href="<?= base_url('index.php?rota=painel-paciente') ?>">Painel</a>
        <a href="<?= base_url('index.php?rota=lista-profissionais') ?>">Profissionais</a>
        <a href="<?= base_url('index.php?rota=logout') ?>">Sair</a>
    </nav>
</header>

<section class="principal">
    <div class="conteudo">
        <h1>Formulário de orientação</h1>
        <p>Responda algumas perguntas simples e, no final, escreva mais detalhes para o profissional.</p>

        <form action="<?= base_url('index.php') ?>" method="POST" class="formulario">
            <input type="hidden" name="acao" value="enviar_formulario">

            <select name="id_profissional" required>
                <option value="">Escolha o profissional</option>
                <?php foreach ($profissionais as $prof): ?>
                    <option value="<?= htmlspecialchars($prof['id_profissional']) ?>"
                        <?= $profissionalSelecionado === (int) $prof['id_profissional'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prof['nome']) ?>
                        <?php if (!empty($prof['especialidade'])): ?> - <?= htmlspecialchars($prof['especialidade']) ?><?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="questionario">
                <?php foreach ($perguntas as $campo => $pergunta): ?>
                    <div class="pergunta-card">
                        <p><?= htmlspecialchars($pergunta) ?></p>
                        <div class="opcoes-resposta">
                            <label>
                                <input type="radio" name="questionario[<?= htmlspecialchars($campo) ?>]" value="Sim" required>
                                Sim
                            </label>
                            <label>
                                <input type="radio" name="questionario[<?= htmlspecialchars($campo) ?>]" value="Não" required>
                                Não
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <textarea name="relatorio" placeholder="Relatório final: conte com mais detalhes o que você está sentindo, quando começou e o que gostaria de trabalhar..." required></textarea>

            <button type="submit" class="button btn-paciente">Enviar formulário</button>
        </form>
    </div>
</section>

</body>
</html>
