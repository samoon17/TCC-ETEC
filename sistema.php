<?php
ob_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/conexao.php';

$pdo = conectarBanco();

function garantirTabelaFormularioConsulta(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS formulario_consulta (
            id INT AUTO_INCREMENT PRIMARY KEY,
            id_paciente INT NOT NULL,
            descricao TEXT NOT NULL,
            data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_paciente)
                REFERENCES usuario(id_usuario)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
}

function garantirTabelaMensagem(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS mensagem (
            id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            titulo VARCHAR(150),
            conteudo TEXT NOT NULL,
            tipo ENUM('consulta','aviso','sistema') DEFAULT 'aviso',
            lida BOOLEAN DEFAULT FALSE,
            data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario)
                REFERENCES usuario(id_usuario)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
}

function garantirTabelaConsulta(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS consulta (
            id_consulta INT AUTO_INCREMENT PRIMARY KEY,
            id_paciente INT NOT NULL,
            id_profissional INT NOT NULL,
            data_hora DATETIME NOT NULL,
            link_chamada VARCHAR(255),
            status ENUM('agendada','confirmada','cancelada','finalizada') DEFAULT 'agendada',
            tipo ENUM('online','presencial') DEFAULT 'online',
            FOREIGN KEY (id_paciente)
                REFERENCES usuario(id_usuario)
                ON DELETE CASCADE,
            FOREIGN KEY (id_profissional)
                REFERENCES profissional(id_profissional)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
}

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

function garantirEstruturaProfissional(PDO $pdo): void {
    if (!colunaExiste($pdo, 'profissional', 'descricao')) {
        $pdo->exec("ALTER TABLE profissional ADD descricao TEXT NULL AFTER registro_profissional");
    }

    if (!colunaExiste($pdo, 'profissional', 'especialidade')) {
        $pdo->exec("ALTER TABLE profissional ADD especialidade VARCHAR(120) NULL AFTER descricao");
    }

    if (!colunaExiste($pdo, 'profissional', 'valor_consulta')) {
        $pdo->exec("ALTER TABLE profissional ADD valor_consulta DECIMAL(10,2) NULL AFTER especialidade");
    }

    if (!colunaExiste($pdo, 'formulario_consulta', 'id_profissional')) {
        $pdo->exec("ALTER TABLE formulario_consulta ADD id_profissional INT NULL AFTER id_paciente");
        $pdo->exec("
            ALTER TABLE formulario_consulta
            ADD CONSTRAINT fk_formulario_profissional
            FOREIGN KEY (id_profissional)
            REFERENCES profissional(id_profissional)
            ON DELETE SET NULL
        ");
    }

    if (!colunaExiste($pdo, 'formulario_consulta', 'status')) {
        $pdo->exec("ALTER TABLE formulario_consulta ADD status ENUM('enviado','respondido') DEFAULT 'enviado' AFTER descricao");
    }
}

function garantirEstruturaConsulta(PDO $pdo): void {
    if (!colunaExiste($pdo, 'consulta', 'link_chamada')) {
        $pdo->exec("ALTER TABLE consulta ADD link_chamada VARCHAR(255) NULL AFTER data_hora");
    }

    if (!colunaExiste($pdo, 'consulta', 'status')) {
        $pdo->exec("ALTER TABLE consulta ADD status ENUM('agendada','confirmada','cancelada','finalizada') DEFAULT 'agendada' AFTER link_chamada");
    }

    if (!colunaExiste($pdo, 'consulta', 'tipo')) {
        $pdo->exec("ALTER TABLE consulta ADD tipo ENUM('online','presencial') DEFAULT 'online' AFTER status");
    }
}

garantirTabelaFormularioConsulta($pdo);
garantirTabelaMensagem($pdo);
garantirTabelaConsulta($pdo);
garantirEstruturaProfissional($pdo);
garantirEstruturaConsulta($pdo);

function iniciarSessao() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function getIP() {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function cadastrarPaciente($nome, $email, $senha, $dataNascimento = null) {
    global $pdo;

    $check = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
    $check->execute([$email]);

    if ($check->fetch()) {
        return "Email ja existe";
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO usuario (nome, email, senha, tipo_usuario, data_nascimento)
        VALUES (?, ?, ?, 'paciente', ?)
    ");

    $stmt->execute([$nome, $email, $senhaHash, $dataNascimento]);

    return "Paciente cadastrado!";
}

function cadastrarProfissional($nome, $email, $senha, $registro, $cidade, $estado, $descricao, $especialidade, $valorConsulta) {
    global $pdo;

    try {
        $pdo->beginTransaction();

        $nome = trim($nome);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $senha = trim($senha);
        $registro = trim($registro);
        $cidade = trim($cidade);
        $estado = trim($estado);
        $descricao = trim($descricao);
        $especialidade = trim($especialidade);
        $valorConsulta = $valorConsulta !== '' ? (float) str_replace(',', '.', $valorConsulta) : null;

        if (!$nome || !$email || !$senha) {
            $pdo->rollBack();
            return "Dados invalidos";
        }

        $check = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $pdo->rollBack();
            return "Email ja existe";
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO usuario (nome, email, senha, tipo_usuario)
            VALUES (?, ?, ?, 'profissional')
        ");
        $stmt->execute([$nome, $email, $senhaHash]);

        $idUsuario = $pdo->lastInsertId();

        $stmt = $pdo->prepare("
            INSERT INTO profissional (id_usuario, registro_profissional, descricao, especialidade, valor_consulta, cidade, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$idUsuario, $registro, $descricao, $especialidade, $valorConsulta, $cidade, $estado]);

        $pdo->commit();

        return "Profissional cadastrado!";
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return "Erro: " . $e->getMessage();
    }
}

function estaBloqueado($email, $ip) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT bloqueado_ate FROM login_tentativas WHERE email = ? AND ip = ?");
    $stmt->execute([$email, $ip]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    return $dados && $dados['bloqueado_ate'] && strtotime($dados['bloqueado_ate']) > time();
}

function registrarErroLogin($email, $ip) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM login_tentativas WHERE email = ? AND ip = ?");
    $stmt->execute([$email, $ip]);
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    $maxTentativas = 5;
    $bloqueioMinutos = 15;

    if ($dados) {
        $tentativas = $dados['tentativas'] + 1;
        $bloqueadoAte = null;

        if ($tentativas >= $maxTentativas) {
            $bloqueadoAte = date("Y-m-d H:i:s", strtotime("+$bloqueioMinutos minutes"));
            $tentativas = 0;
        }

        $update = $pdo->prepare("
            UPDATE login_tentativas
            SET tentativas = ?, bloqueado_ate = ?, ultimo_login = NOW()
            WHERE id = ?
        ");

        $update->execute([$tentativas, $bloqueadoAte, $dados['id']]);
        return;
    }

    $insert = $pdo->prepare("
        INSERT INTO login_tentativas (email, ip, tentativas, ultimo_login)
        VALUES (?, ?, 1, NOW())
    ");
    $insert->execute([$email, $ip]);
}

function resetarTentativas($email, $ip) {
    global $pdo;

    $stmt = $pdo->prepare("DELETE FROM login_tentativas WHERE email = ? AND ip = ?");
    $stmt->execute([$email, $ip]);
}

function login($email, $senha) {
    global $pdo;

    $ip = getIP();

    if (estaBloqueado($email, $ip)) {
        return "Muitas tentativas. Tente mais tarde.";
    }

    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($senha, $user['senha'])) {
        registrarErroLogin($email, $ip);
        return "Email ou senha invalidos";
    }

    if ($user['status'] !== 'ativo') {
        return "Conta inativa";
    }

    resetarTentativas($email, $ip);

    iniciarSessao();
    session_regenerate_id(true);

    $_SESSION['id'] = $user['id_usuario'];
    $_SESSION['nome'] = $user['nome'];
    $_SESSION['tipo'] = $user['tipo_usuario'];

    return "OK";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];

    switch ($acao) {
        case 'cadastrar_paciente':
            $nome = trim($_POST['nome'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $senha = $_POST['senha'] ?? '';

            if (!$nome || !$email || !$senha) {
                echo "Dados invalidos";
                exit;
            }

            $resultado = cadastrarPaciente($nome, $email, $senha, $_POST['data_nascimento'] ?? null);

            if ($resultado === "Paciente cadastrado!") {
                header("Location: login.html?sucesso=1");
                exit;
            }

            echo $resultado;
            break;

        case 'cadastrar_profissional':
            $resultado = cadastrarProfissional(
                $_POST['nome'] ?? '',
                $_POST['email'] ?? '',
                $_POST['senha'] ?? '',
                $_POST['registro'] ?? '',
                $_POST['cidade'] ?? '',
                $_POST['estado'] ?? '',
                $_POST['descricao'] ?? '',
                $_POST['especialidade'] ?? '',
                $_POST['valor_consulta'] ?? ''
            );

            if ($resultado === "Profissional cadastrado!") {
                header("Location: login.html?sucesso=1");
                exit;
            }

            echo $resultado;
            break;

        case 'login':
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $senha = $_POST['senha'] ?? '';

            if (!$email || !$senha) {
                echo "Dados invalidos";
                exit;
            }

            $resultado = login($email, $senha);

            if ($resultado === "OK") {
                if ($_SESSION['tipo'] === 'paciente') {
                    header("Location: painelFormPaciente.php");
                } else {
                    header("Location: painelFormProfissional.php");
                }
                exit;
            }

            echo $resultado;
            break;

        case 'enviar_formulario':
            iniciarSessao();

            if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'paciente') {
                echo "Acesso negado";
                exit;
            }

            $descricao = trim($_POST['descricao'] ?? '');
            $idProfissional = filter_input(INPUT_POST, 'id_profissional', FILTER_VALIDATE_INT);

            if ($descricao === '') {
                echo "Descricao obrigatoria";
                exit;
            }

            if (!$idProfissional) {
                echo "Escolha um profissional para enviar o formulario";
                exit;
            }

            $stmtProf = $pdo->prepare("SELECT id_profissional FROM profissional WHERE id_profissional = ?");
            $stmtProf->execute([$idProfissional]);

            if (!$stmtProf->fetch()) {
                echo "Profissional nao encontrado";
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO formulario_consulta (id_paciente, id_profissional, descricao)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$_SESSION['id'], $idProfissional, $descricao]);

            header("Location: painelFormPaciente.php?ok=1");
            exit;

        case 'criar_consulta':
            iniciarSessao();

            if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'profissional') {
                echo "Acesso negado";
                exit;
            }

            $stmtProf = $pdo->prepare("
                SELECT p.id_profissional, u.nome
                FROM profissional p
                JOIN usuario u ON u.id_usuario = p.id_usuario
                WHERE p.id_usuario = ?
            ");
            $stmtProf->execute([$_SESSION['id']]);
            $prof = $stmtProf->fetch(PDO::FETCH_ASSOC);

            if (!$prof) {
                echo "Profissional nao encontrado";
                exit;
            }

            $idPaciente = filter_input(INPUT_POST, 'id_paciente', FILTER_VALIDATE_INT);
            $dataHora = $_POST['data_hora'] ?? null;
            $linkChamada = trim($_POST['link_chamada'] ?? '');

            if (!$idPaciente || !$dataHora || $linkChamada === '') {
                echo "Dados da consulta invalidos";
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO consulta (id_paciente, id_profissional, data_hora, link_chamada)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $idPaciente,
                $prof['id_profissional'],
                $dataHora,
                $linkChamada
            ]);

            $dataConsulta = date('d/m/Y', strtotime($dataHora));
            $horaConsulta = date('H:i', strtotime($dataHora));
            $tituloMensagem = "Consulta agendada";
            $conteudoMensagem = "Sua consulta com {$prof['nome']} foi agendada para o dia {$dataConsulta} as {$horaConsulta}. Link da chamada: {$linkChamada}";

            $stmtMensagem = $pdo->prepare("
                INSERT INTO mensagem (id_usuario, titulo, conteudo, tipo)
                VALUES (?, ?, ?, 'consulta')
            ");
            $stmtMensagem->execute([$idPaciente, $tituloMensagem, $conteudoMensagem]);

            if (!empty($_POST['id_formulario'])) {
                $stmtStatus = $pdo->prepare("
                    UPDATE formulario_consulta
                    SET status = 'respondido'
                    WHERE id = ? AND id_profissional = ?
                ");
                $stmtStatus->execute([$_POST['id_formulario'], $prof['id_profissional']]);
            }

            header("Location: painelFormProfissional.php?ok=1");
            exit;
    }
}
?>
