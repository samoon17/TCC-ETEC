<?php
class UsuarioDTO {
    public function __construct(
        public readonly ?int    $id = null,
        public readonly string  $nome = '',
        public readonly string  $email = '',
        public readonly string  $senha = '',
        public readonly string  $tipo = 'paciente',
        public readonly ?string $dataNascimento = null,
        public readonly string  $status = 'ativo'
    ) {}
}
