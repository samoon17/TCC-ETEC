<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../dto/FormularioDTO.php';

class FormularioDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function inserir(FormularioDTO $dto): void {
        $this->pdo->prepare("
            INSERT INTO formulario_consulta (id_paciente, id_profissional, descricao)
            VALUES (?, ?, ?)
        ")->execute([$dto->idPaciente, $dto->idProfissional, $dto->descricao]);
    }

    public function listarPorPaciente(int $idPaciente): array {
        $stmt = $this->pdo->prepare("
            SELECT f.*, u.nome AS profissional
            FROM formulario_consulta f
            LEFT JOIN profissional p ON p.id_profissional = f.id_profissional
            LEFT JOIN usuario u ON u.id_usuario = p.id_usuario
            WHERE f.id_paciente = ?
            ORDER BY f.id DESC
        ");
        $stmt->execute([$idPaciente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorProfissional(int $idProfissional): array {
        $stmt = $this->pdo->prepare("
            SELECT f.*, u.nome AS paciente
            FROM formulario_consulta f
            JOIN usuario u ON u.id_usuario = f.id_paciente
            WHERE f.id_profissional = ?
            ORDER BY f.id DESC
        ");
        $stmt->execute([$idProfissional]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarRespondido(int $idFormulario, int $idProfissional): void {
        $this->pdo->prepare("
            UPDATE formulario_consulta SET status = 'respondido'
            WHERE id = ? AND id_profissional = ?
        ")->execute([$idFormulario, $idProfissional]);
    }
}
