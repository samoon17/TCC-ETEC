<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../dto/MensagemDTO.php';

class MensagemDAO {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function inserir(MensagemDTO $dto): void {
        $this->pdo->prepare("
            INSERT INTO mensagem (id_usuario, titulo, conteudo, tipo)
            VALUES (?, ?, ?, ?)
        ")->execute([$dto->idUsuario, $dto->titulo, $dto->conteudo, $dto->tipo]);
    }

    public function listarPorUsuario(int $idUsuario): array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM mensagem WHERE id_usuario = ? ORDER BY id_mensagem DESC
        ");
        $stmt->execute([$idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
