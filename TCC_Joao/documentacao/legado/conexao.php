<?php
function conectarBanco(): PDO {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=plataforma_saude_mental;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        exit("
            <div style='font-family: Arial, sans-serif; max-width: 720px; margin: 60px auto; line-height: 1.5; color: #263238;'>
                <h1 style='color: #1e88e5;'>Banco de dados indisponivel</h1>
                <p>Nao foi possivel conectar ao MySQL. Abra o painel do XAMPP e clique em <strong>Start</strong> no MySQL.</p>
                <p>Depois recarregue esta pagina. Se o MySQL ja estiver ligado, confira se o banco <strong>plataforma_saude_mental</strong> existe no phpMyAdmin.</p>
            </div>
        ");
    }
}
?>
