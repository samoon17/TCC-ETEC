<?php
class ConsultaDTO {
    public function __construct(
        public readonly ?int    $id = null,
        public readonly int     $idPaciente = 0,
        public readonly int     $idProfissional = 0,
        public readonly string  $dataHora = '',
        public readonly string  $linkChamada = '',
        public readonly string  $status = 'agendada',
        public readonly string  $tipo = 'online',
        public readonly string  $nomeProfissional = ''
    ) {}
}
