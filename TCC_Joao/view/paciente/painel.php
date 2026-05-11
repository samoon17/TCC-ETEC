<?php
// Variáveis esperadas: $nome, $formularios, $consultas, $mensagens
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel do Paciente</title>
<?= favicon_tags() ?>
<link rel="stylesheet" href="<?= asset_url('view/assets/style.css') ?>">
<style>
.paciente-painel .principal { align-items: flex-start; }
.paciente-painel .conteudo { max-width: 980px; height: auto; min-height: 0; padding-bottom: 40px; }
.painel-cabecalho { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 22px; }
.painel-cabecalho p { margin-bottom: 0; }
.atalhos-paciente { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 24px; }
.atalho-card { background: #fff; border: 1px solid #d6ebe7; border-radius: 8px; padding: 16px; text-decoration: none; color: #21494c; box-shadow: 0 8px 20px rgba(31,92,91,0.08); }
.atalho-card strong { display: block; color: #0b6b73; font-size: 22px; margin-bottom: 4px; }
.secao-painel { margin-top: 26px; scroll-margin-top: 92px; }
.secao-painel h2 { margin-bottom: 12px; color: #0b6b73; }
.card { background: #fff; padding: 15px; margin-top: 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.paciente-painel .card p { display: block; overflow: visible; margin-bottom: 10px; }
.paciente-painel .card a { color: #0b6b73; font-weight: 700; }
.sucesso { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
.aviso { background: #fff3cd; color: #664d03; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
.menu .menu-consulta { background: #f4b56f; color: #3f250d; padding: 10px 16px; border-radius: 999px; margin-left: 20px; display: inline-flex; align-items: center; }
.menu .menu-ativo { background: rgba(255,255,255,0.18); padding: 10px 14px; border-radius: 999px; }
.cta-consulta { background: #eef8f5; border: 1px solid #d6ebe7; border-radius: 8px; padding: 18px 20px; margin-bottom: 22px; }
.mensagem-consulta { border-left: 5px solid #0b6b73; }
.mensagem-meta { font-size: 14px; color: #607089; }
.acoes-consulta { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
.acoes-consulta form { margin: 0; }
.button-secundario { background: #fff; color: #0b6b73; border: 1px solid #0b6b73; }
.button-cancelar { background: #c62828; color: #fff; }
.link-indisponivel { color: #607089; font-weight: 700; }
</style>
</head>
<body class="painel paciente-painel">

<header class="topo">
    <?= logo_site() ?>
    <nav class="menu">
        <a href="#mensagens" class="menu-ativo">Mensagens</a>
        <a href="#consultas">Terapias</a>
        <a href="#historico">Histórico</a>
        <a href="<?= base_url('index.php?rota=lista-profissionais') ?>" class="menu-consulta">Marcar terapia</a>
        <a href="<?= base_url('index.php?rota=logout') ?>">Sair</a>
    </nav>
</header>

<section class="principal">
    <div class="conteudo">

        <div class="painel-cabecalho">
            <div>
                <h1>Ola, <?= htmlspecialchars($nome) ?></h1>
                <p>Acompanhe suas mensagens, formulários enviados e terapias agendadas.</p>
            </div>
            <a href="<?= base_url('index.php?rota=lista-profissionais') ?>" class="button btn-paciente">Agendar terapia</a>
        </div>

        <?php if (isset($_GET['ok'])): ?>
            <div class="sucesso">Formulário enviado com sucesso!</div>
        <?php endif; ?>
        <?php if (isset($_GET['consulta_cancelada'])): ?>
            <div class="aviso">Terapia cancelada com sucesso.</div>
        <?php endif; ?>

        <div class="atalhos-paciente">
            <a href="#mensagens" class="atalho-card">
                <strong><?= count($mensagens) ?></strong>Mensagens recebidas
            </a>
            <a href="#consultas" class="atalho-card">
                <strong><?= count($consultas) ?></strong>Terapias agendadas
            </a>
            <a href="#historico" class="atalho-card">
                <strong><?= count($formularios) ?></strong>Formulários enviados
            </a>
        </div>

        <div class="cta-consulta">
            <p><strong>Quer marcar uma terapia?</strong> Veja os profissionais disponíveis.</p>
            <a href="<?= base_url('index.php?rota=lista-profissionais') ?>" class="button btn-paciente">Ver lista de profissionais</a>
        </div>

        <section class="secao-painel" id="mensagens">
            <h2>Mensagens</h2>
            <?php if (empty($mensagens)): ?>
                <p>Você ainda não recebeu mensagens.</p>
            <?php endif; ?>
            <?php foreach ($mensagens as $m): ?>
                <div class="card <?= $m['tipo'] === 'consulta' ? 'mensagem-consulta' : '' ?>">
                    <p><strong><?= htmlspecialchars($m['titulo'] ?: 'Mensagem') ?></strong></p>
                    <?php
                        $conteudoMensagem = $m['conteudo'];
                        if ($m['tipo'] === 'consulta') {
                            $conteudoMensagem = preg_replace('/https?:\/\/\S+/', 'Link disponivel apenas no dia da terapia.', $conteudoMensagem);
                        }
                    ?>
                    <p><?= nl2br(htmlspecialchars($conteudoMensagem)) ?></p>
                    <p class="mensagem-meta">Recebida em <?= htmlspecialchars($m['data_envio']) ?></p>
                    <?php if ($m['tipo'] !== 'consulta' && preg_match('/https?:\/\/\S+/', $m['conteudo'], $link)): ?>
                        <p><a href="<?= htmlspecialchars($link[0]) ?>" target="_blank" rel="noopener noreferrer">Abrir link da terapia</a></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="secao-painel" id="consultas">
            <h2>Minhas terapias</h2>
            <?php if (empty($consultas)): ?>
                <p>Você ainda não tem terapias agendadas.</p>
            <?php endif; ?>
            <?php foreach ($consultas as $c): ?>
                <?php
                    $dataConsulta = date('Y-m-d', strtotime($c['data_hora']));
                    $consultaHoje = $dataConsulta === date('Y-m-d');
                    $consultaCancelada = ($c['status'] ?? '') === 'cancelada';
                    $podeAcessarLink = !$consultaCancelada && $consultaHoje && !empty($c['link_chamada']);
                ?>
                <div class="card">
                    <p><strong>Profissional:</strong> <?= htmlspecialchars($c['profissional']) ?></p>
                    <p><strong>Data:</strong> <?= htmlspecialchars($c['data_hora']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($c['status']) ?></p>
                    <?php if ($podeAcessarLink): ?>
                        <p><a href="<?= htmlspecialchars($c['link_chamada']) ?>" target="_blank" rel="noopener noreferrer">Entrar na terapia</a></p>
                    <?php elseif (!$consultaCancelada && !empty($c['link_chamada'])): ?>
                        <p class="link-indisponivel">Link da chamada disponivel apenas no dia da terapia.</p>
                    <?php endif; ?>

                    <?php if (!$consultaCancelada): ?>
                        <div class="acoes-consulta">
                            <form action="<?= base_url('index.php') ?>" method="POST">
                                <input type="hidden" name="acao" value="cancelar_consulta">
                                <input type="hidden" name="id_consulta" value="<?= htmlspecialchars($c['id_consulta']) ?>">
                                <button class="button button-cancelar" type="submit">Cancelar</button>
                            </form>
                            <form action="<?= base_url('index.php') ?>" method="POST">
                                <input type="hidden" name="acao" value="remarcar_consulta">
                                <input type="hidden" name="id_consulta" value="<?= htmlspecialchars($c['id_consulta']) ?>">
                                <button class="button button-secundario" type="submit">Remarcar</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="secao-painel" id="historico">
            <h2>Histórico de formulários</h2>
            <?php if (empty($formularios)): ?>
                <p>Você ainda não enviou nenhum formulário.</p>
            <?php endif; ?>
            <?php foreach ($formularios as $f): ?>
                <div class="card">
                    <p><strong>Descrição:</strong></p>
                    <p><?= htmlspecialchars($f['descricao']) ?></p>
                    <?php if (!empty($f['profissional'])): ?>
                        <p><strong>Enviado para:</strong> <?= htmlspecialchars($f['profissional']) ?></p>
                    <?php endif; ?>
                    <p><strong>Status:</strong> <?= htmlspecialchars($f['status'] ?? 'enviado') ?></p>
                    <?php if (isset($f['data_envio'])): ?>
                        <p><strong>Enviado em:</strong> <?= htmlspecialchars($f['data_envio']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>

    </div>
</section>

</body>
</html>
