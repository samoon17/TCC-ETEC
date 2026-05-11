<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../dto/ProfissionalDTO.php';

class ProfissionalDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function listarAtivos(?string $estado = null): array {
        $sql = "
            SELECT p.id_profissional, p.id_usuario, p.registro_profissional, p.descricao,
                   p.especialidade, p.cidade, p.estado, u.nome
            FROM profissional p
            JOIN usuario u ON u.id_usuario = p.id_usuario
            WHERE u.status = 'ativo' AND p.validado = 1
        ";
        $params = [];

        if ($estado !== null) {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
        }

        $sql .= " ORDER BY u.nome";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorUsuario(int $idUsuario): ?array {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.nome, u.email
            FROM profissional p
            JOIN usuario u ON u.id_usuario = p.id_usuario
            WHERE p.id_usuario = ?
        ");
        $stmt->execute([$idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function buscarPorId(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM profissional WHERE id_profissional = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function inserir(ProfissionalDTO $dto): void {
        $this->pdo->prepare("
            INSERT INTO profissional (id_usuario, registro_profissional, descricao, especialidade, cidade, estado, validado)
            VALUES (?, ?, ?, ?, ?, ?, 0)
        ")->execute([
            $dto->idUsuario, $dto->registro, $dto->descricao,
            $dto->especialidade, $dto->cidade, $dto->estado
        ]);
    }

    public function listarTodos(): array {
        $stmt = $this->pdo->query("
            SELECT p.id_profissional, p.id_usuario, p.registro_profissional, p.descricao,
                   p.especialidade, p.cidade, p.estado, p.validado,
                   u.nome, u.email, u.status, u.data_cadastro
            FROM profissional p
            JOIN usuario u ON u.id_usuario = p.id_usuario
            ORDER BY p.validado ASC, u.data_cadastro DESC, u.nome
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function alterarValidacao(int $idProfissional, bool $validado): void {
        $stmt = $this->pdo->prepare("UPDATE profissional SET validado = ? WHERE id_profissional = ?");
        $stmt->execute([$validado ? 1 : 0, $idProfissional]);
    }

    public function atualizarPerfil(int $idUsuario, string $registro, string $especialidade, string $cidade, string $estado, string $descricao): void {
        $stmt = $this->pdo->prepare("
            UPDATE profissional
            SET registro_profissional = ?, especialidade = ?, cidade = ?, estado = ?, descricao = ?
            WHERE id_usuario = ?
        ");
        $stmt->execute([$registro, $especialidade, $cidade, $estado, $descricao, $idUsuario]);
    }
}
