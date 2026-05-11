<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'paciente') {
    header("Location: login.html");
    exit;
}

require_once __DIR__ . '/conexao.php';

$pdo = conectarBanco();

function colunaExiste(PDO $pdo, string $tabela, string $coluna): bool {
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
          AND COLUMN_NAME = ?
    ");
    $stmt->execute([$tabela, $coluna]);
    return (int) $stmt->fetchColumn() > 0;
}

if (!colunaExiste($pdo, 'profissional', 'descricao')) {
    $pdo->exec("ALTER TABLE profissional ADD descricao TEXT NULL AFTER registro_profissional");
}

if (!colunaExiste($pdo, 'profissional', 'especialidade')) {
    $pdo->exec("ALTER TABLE profissional ADD especialidade VARCHAR(120) NULL AFTER descricao");
}

$profissionalSelecionado = filter_input(INPUT_GET, 'profissional', FILTER_VALIDATE_INT);

$stmt = $pdo->query("
    SELECT p.id_profissional, p.registro_profissional, p.especialidade, u.nome
    FROM profissional p
    JOIN usuario u ON u.id_usuario = p.id_usuario
    WHERE u.status = 'ativo'
    ORDER BY u.nome
");
$profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="style.css">
<title>Formulario</title>
</head>

<body>

<section class="principal">
    <div class="conteudo">

        <h1>Descreva como voce esta se sentindo</h1>

        <form action="/TCC/sistema.php" method="POST" class="formulario">

            <input type="hidden" name="acao" value="enviar_formulario">

            <select name="id_profissional" required>
                <option value="">Escolha o profissional</option>
                <?php foreach ($profissionais as $prof): ?>
                    <option value="<?= htmlspecialchars($prof['id_profissional']) ?>" <?= $profissionalSelecionado === (int) $prof['id_profissional'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prof['nome']) ?>
                        <?php if (!empty($prof['especialidade'])): ?>
                            - <?= htmlspecialchars($prof['especialidade']) ?>
                        <?php endif; ?>
                        <?php if (!empty($prof['registro_profissional'])): ?>
                            (<?= htmlspecialchars($prof['registro_profissional']) ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <textarea name="descricao" placeholder="Descreva seus sintomas..." required></textarea>

            <button type="submit" class="button btn-paciente">
                Enviar
            </button>

        </form>

    </div>
</section>

</body>
</html>
