<?php
require_once __DIR__ . '/../model/dao/FormularioDAO.php';
require_once __DIR__ . '/../model/dao/ConsultaDAO.php';
require_once __DIR__ . '/../model/dao/MensagemDAO.php';
require_once __DIR__ . '/../model/dao/ProfissionalDAO.php';
require_once __DIR__ . '/../model/dto/FormularioDTO.php';

class PacienteController {
    private FormularioDAO $formularioDAO;
    private ConsultaDAO $consultaDAO;
    private MensagemDAO $mensagemDAO;
    private ProfissionalDAO $profissionalDAO;

    public function __construct() {
        $this->formularioDAO   = new FormularioDAO();
        $this->consultaDAO     = new ConsultaDAO();
        $this->mensagemDAO     = new MensagemDAO();
        $this->profissionalDAO = new ProfissionalDAO();
    }

    public function painel(int $idPaciente): array {
        return [
            'formularios' => $this->formularioDAO->listarPorPaciente($idPaciente),
            'consultas'   => $this->consultaDAO->listarPorPaciente($idPaciente),
            'mensagens'   => $this->mensagemDAO->listarPorUsuario($idPaciente),
        ];
    }

    public function listarProfissionais(?string $estado = null): array {
        $estado = strtoupper(trim($estado ?? ''));
        $estado = array_key_exists($estado, estados_brasileiros()) ? $estado : null;

        return $this->profissionalDAO->listarAtivos($estado);
    }

    public function enviarFormulario(int $idPaciente, int $idProfissional, array $dados): string {
        $descricao = $this->montarDescricaoFormulario($dados);
        if (trim($descricao) === '') return "Relatorio obrigatorio";
        if (!$this->profissionalDAO->buscarPorId($idProfissional)) return "Profissional não encontrado";

        $dto = new FormularioDTO(
            idPaciente: $idPaciente,
            idProfissional: $idProfissional,
            descricao: $descricao
        );

        $this->formularioDAO->inserir($dto);
        return "OK";
    }

    private function montarDescricaoFormulario(array $dados): string {
        $perguntas = [
            'sentindo_muito_tempo' => 'Você está se sentindo assim há muito tempo?',
            'rotina_afetada' => 'Isso tem atrapalhado sua rotina, estudos ou trabalho?',
            'sono_apetite' => 'Seu sono ou apetite mudou recentemente?',
            'apoio_proximo' => 'Você sente que tem alguém próximo para conversar?',
            'precisa_orientacao' => 'Você gostaria de receber orientação de um profissional?',
        ];

        $respostas = $dados['questionario'] ?? [];
        $linhas = ["QUESTIONARIO"];

        foreach ($perguntas as $campo => $pergunta) {
            $resposta = $respostas[$campo] ?? '';
            if (!in_array($resposta, ['Sim', 'Não'], true)) {
                return '';
            }
            $linhas[] = "- {$pergunta} {$resposta}";
        }

        $relatorio = trim($dados['relatorio'] ?? '');
        if ($relatorio === '') {
            return '';
        }

        $linhas[] = "";
        $linhas[] = "RELATORIO";
        $linhas[] = $relatorio;

        return implode("\n", $linhas);
    }

    public function cancelarConsulta(int $idPaciente, int $idConsulta): string {
        if ($idConsulta <= 0) return "Terapia inválida";

        return $this->consultaDAO->alterarStatusPorPaciente($idConsulta, $idPaciente, 'cancelada')
            ? "OK"
            : "Terapia não encontrada";
    }

    public function remarcarConsulta(int $idPaciente, int $idConsulta): string {
        if ($idConsulta <= 0) return "Terapia inválida";

        return $this->consultaDAO->alterarStatusPorPaciente($idConsulta, $idPaciente, 'cancelada')
            ? "OK"
            : "Terapia não encontrada";
    }
}
