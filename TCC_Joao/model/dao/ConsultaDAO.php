<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../dto/ConsultaDTO.php';

class ConsultaDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function inserir(ConsultaDTO $dto): void {
        $this->pdo->prepare("
            INSERT INTO consulta (id_paciente, id_profissional, data_hora, link_chamada)
            VALUES (?, ?, ?, ?)
        ")->execute([$dto->idPaciente, $dto->idProfissional, $dto->dataHora, $dto->linkChamada]);
    }

    public function listarPorPaciente(int $idPaciente): array {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.nome AS profissional
            FROM consulta c
            JOIN profissional p ON p.id_profissional = c.id_profissional
            JOIN usuario u ON u.id_usuario = p.id_usuario
            WHERE c.id_paciente = ?
            ORDER BY c.data_hora DESC
        ");
        $stmt->execute([$idPaciente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function alterarStatusPorPaciente(int $idConsulta, int $idPaciente, string $status): bool {
        $stmt = $this->pdo->prepare("
            UPDATE consulta
            SET status = ?
            WHERE id_consulta = ?
              AND id_paciente = ?
              AND status <> 'cancelada'
        ");
        $stmt->execute([$status, $idConsulta, $idPaciente]);

        return $stmt->rowCount() > 0;
    }
}
