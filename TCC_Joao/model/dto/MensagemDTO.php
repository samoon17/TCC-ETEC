<?php
class MensagemDTO {
    public function __construct(
        public readonly ?int    $id = null,
        public readonly int     $idUsuario = 0,
        public readonly string  $titulo = '',
        public readonly string  $conteudo = '',
        public readonly string  $tipo = 'aviso',
        public readonly bool    $lida = false,
        public readonly ?string $dataEnvio = null
    ) {}
}
