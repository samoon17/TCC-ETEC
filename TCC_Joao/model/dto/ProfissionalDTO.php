<?php
class ProfissionalDTO {
    public function __construct(
        public readonly ?int    $id = null,
        public readonly int     $idUsuario = 0,
        public readonly string  $nome = '',
        public readonly string  $registro = '',
        public readonly string  $especialidade = '',
        public readonly string  $descricao = '',
        public readonly string  $cidade = '',
        public readonly string  $estado = ''
    ) {}
}
