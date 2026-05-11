<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../dto/UsuarioDTO.php';

class UsuarioDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function buscarPorEmail(string $email): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function emailExiste(string $email): bool {
        $stmt = $this->pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }

    public function emailExisteParaOutroUsuario(string $email, int $idUsuario): bool {
        $stmt = $this->pdo->prepare("SELECT id_usuario FROM usuario WHERE email = ? AND id_usuario <> ?");
        $stmt->execute([$email, $idUsuario]);
        return (bool) $stmt->fetch();
    }

    public function existeAdmin(): bool {
        $stmt = $this->pdo->query("SELECT id_usuario FROM usuario WHERE tipo_usuario = 'admin' LIMIT 1");
        return (bool) $stmt->fetch();
    }

    public function inserir(UsuarioDTO $dto): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO usuario (nome, email, senha, tipo_usuario, data_nascimento)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$dto->nome, $dto->email, $dto->senha, $dto->tipo, $dto->dataNascimento]);
        return (int) $this->pdo->lastInsertId();
    }

    public function estaBloqueado(string $email, string $ip): bool {
        $stmt = $this->pdo->prepare("SELECT bloqueado_ate FROM login_tentativas WHERE email = ? AND ip = ?");
        $stmt->execute([$email, $ip]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dados && $dados['bloqueado_ate'] && strtotime($dados['bloqueado_ate']) > time();
    }

    public function registrarErroLogin(string $email, string $ip): void {
        $stmt = $this->pdo->prepare("SELECT * FROM login_tentativas WHERE email = ? AND ip = ?");
        $stmt->execute([$email, $ip]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dados) {
            $tentativas = $dados['tentativas'] + 1;
            $bloqueadoAte = $tentativas >= 5 ? date("Y-m-d H:i:s", strtotime("+15 minutes")) : null;
            if ($tentativas >= 5) $tentativas = 0;

            $this->pdo->prepare("
                UPDATE login_tentativas SET tentativas = ?, bloqueado_ate = ?, ultimo_login = NOW() WHERE id = ?
            ")->execute([$tentativas, $bloqueadoAte, $dados['id']]);
        } else {
            $this->pdo->prepare("
                INSERT INTO login_tentativas (email, ip, tentativas, ultimo_login) VALUES (?, ?, 1, NOW())
            ")->execute([$email, $ip]);
        }
    }

    public function resetarTentativas(string $email, string $ip): void {
        $this->pdo->prepare("DELETE FROM login_tentativas WHERE email = ? AND ip = ?")->execute([$email, $ip]);
    }

    public function listarTodos(): array {
        $stmt = $this->pdo->query("
            SELECT id_usuario, nome, email, tipo_usuario, status, data_cadastro, data_nascimento
            FROM usuario
            ORDER BY data_cadastro DESC, nome
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function alterarStatus(int $idUsuario, string $status): void {
        $permitidos = ['ativo', 'inativo', 'bloqueado'];
        if (!in_array($status, $permitidos, true)) {
            throw new InvalidArgumentException('Status invalido');
        }

        $stmt = $this->pdo->prepare("UPDATE usuario SET status = ? WHERE id_usuario = ?");
        $stmt->execute([$status, $idUsuario]);
    }

    public function atualizarPerfil(int $idUsuario, string $nome, string $email, ?string $senhaHash = null): void {
        if ($senhaHash) {
            $stmt = $this->pdo->prepare("UPDATE usuario SET nome = ?, email = ?, senha = ? WHERE id_usuario = ?");
            $stmt->execute([$nome, $email, $senhaHash, $idUsuario]);
            return;
        }

        $stmt = $this->pdo->prepare("UPDATE usuario SET nome = ?, email = ? WHERE id_usuario = ?");
        $stmt->execute([$nome, $email, $idUsuario]);
    }

    public function deletar(int $idUsuario): void {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("SELECT id_profissional FROM profissional WHERE id_usuario = ?");
            $stmt->execute([$idUsuario]);
            $idProfissional = $stmt->fetchColumn();

            $this->pdo->prepare("DELETE FROM consulta WHERE id_paciente = ?")->execute([$idUsuario]);

            if ($idProfissional) {
                $this->pdo->prepare("DELETE FROM consulta WHERE id_profissional = ?")->execute([$idProfissional]);
            }

            $this->pdo->prepare("DELETE FROM login_tentativas WHERE email IN (SELECT email FROM usuario WHERE id_usuario = ?)")->execute([$idUsuario]);
            $this->pdo->prepare("DELETE FROM usuario WHERE id_usuario = ?")->execute([$idUsuario]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
