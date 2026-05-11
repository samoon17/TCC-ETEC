<?php
// ================== CONEXÃO ==================
$pdo = new PDO("mysql:host=localhost;dbname=plataforma_saude_mental;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// ================== MIGRAÇÃO ==================
echo "Iniciando migração de senhas...\n";

$sql = "SELECT id_usuario, senha FROM usuario";
$stmt = $pdo->query($sql);

$atualizados = 0;

while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $senhaAtual = $user['senha'];

    //Detecta se já é hash
    if (password_get_info($senhaAtual)['algo'] !== 0) {
        continue; // já está seguro
    }

    //Converte para hash
    $novaSenha = password_hash($senhaAtual, PASSWORD_DEFAULT);

    $update = $pdo->prepare("UPDATE usuario SET senha = ? WHERE id_usuario = ?");
    $update->execute([$novaSenha, $user['id_usuario']]);

    $atualizados++;
}

echo "Migração concluída!\n";
echo "Usuários atualizados: $atualizados\n";