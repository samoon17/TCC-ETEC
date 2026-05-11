<?php
class FormularioDTO {
    public function __construct(
        public readonly ?int    $id = null,
        public readonly int     $idPaciente = 0,
        public readonly int     $idProfissional = 0,
        public readonly string  $descricao = '',
        public readonly string  $status = 'enviado',
        public readonly ?string $dataEnvio = null,
        public readonly string  $nomePaciente = '',
        public readonly string  $nomeProfissional = ''
    ) {}
}
