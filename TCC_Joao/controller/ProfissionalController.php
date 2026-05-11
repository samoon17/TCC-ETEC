<?php
require_once __DIR__ . '/../model/dao/FormularioDAO.php';
require_once __DIR__ . '/../model/dao/ConsultaDAO.php';
require_once __DIR__ . '/../model/dao/MensagemDAO.php';
require_once __DIR__ . '/../model/dao/ProfissionalDAO.php';
require_once __DIR__ . '/../model/dao/UsuarioDAO.php';
require_once __DIR__ . '/../model/dto/ConsultaDTO.php';
require_once __DIR__ . '/../model/dto/MensagemDTO.php';

class ProfissionalController {
    private FormularioDAO $formularioDAO;
    private ConsultaDAO $consultaDAO;
    private MensagemDAO $mensagemDAO;
    private ProfissionalDAO $profissionalDAO;
    private UsuarioDAO $usuarioDAO;

    public function __construct() {
        $this->formularioDAO   = new FormularioDAO();
        $this->consultaDAO     = new ConsultaDAO();
        $this->mensagemDAO     = new MensagemDAO();
        $this->profissionalDAO = new ProfissionalDAO();
        $this->usuarioDAO      = new UsuarioDAO();
    }

    public function painel(int $idUsuario): array {
        $prof = $this->profissionalDAO->buscarPorUsuario($idUsuario);
        if (!$prof) return ['erro' => 'Profissional não encontrado'];

        return [
            'profissional' => $prof,
            'formularios'  => $this->formularioDAO->listarPorProfissional($prof['id_profissional']),
        ];
    }

    public function criarConsulta(int $idUsuarioProfissional, array $dados): string {
        $prof = $this->profissionalDAO->buscarPorUsuario($idUsuarioProfissional);
        if (!$prof) return "Profissional não encontrado";

        $idPaciente  = filter_var($dados['id_paciente'] ?? 0, FILTER_VALIDATE_INT);
        $dataHora    = $dados['data_hora'] ?? '';
        $linkChamada = trim($dados['link_chamada'] ?? '');

        if (!$idPaciente || !$dataHora || $linkChamada === '') return "Dados inválidos";

        $consultaDTO = new ConsultaDTO(
            idPaciente: $idPaciente,
            idProfissional: $prof['id_profissional'],
            dataHora: $dataHora,
            linkChamada: $linkChamada
        );

        $this->consultaDAO->inserir($consultaDTO);

        $dataFormatada = date('d/m/Y', strtotime($dataHora));
        $hora          = date('H:i', strtotime($dataHora));

        $mensagemDTO = new MensagemDTO(
            idUsuario: $idPaciente,
            titulo: "Terapia agendada",
            conteudo: "Sua terapia foi agendada para {$dataFormatada} as {$hora}. Link: {$linkChamada}",
            tipo: 'consulta'
        );

        $this->mensagemDAO->inserir($mensagemDTO);

        if (!empty($dados['id_formulario'])) {
            $this->formularioDAO->marcarRespondido((int) $dados['id_formulario'], $prof['id_profissional']);
        }

        return "OK";
    }

    public function responderFormulario(int $idUsuarioProfissional, array $dados): string {
        $prof = $this->profissionalDAO->buscarPorUsuario($idUsuarioProfissional);
        if (!$prof) return "Profissional não encontrado";

        $idPaciente = filter_var($dados['id_paciente'] ?? 0, FILTER_VALIDATE_INT);
        $idFormulario = filter_var($dados['id_formulario'] ?? 0, FILTER_VALIDATE_INT);
        $relatorio = trim($dados['relatorio'] ?? '');
        $whatsapp = trim($dados['whatsapp'] ?? '');

        if (!$idPaciente || !$idFormulario || $relatorio === '' || $whatsapp === '') {
            return "Preencha o relatório e o WhatsApp";
        }

        $whatsappLimpo = preg_replace('/\D+/', '', $whatsapp);
        if (strlen($whatsappLimpo) < 10 || strlen($whatsappLimpo) > 13) {
            return "Informe um WhatsApp válido com DDD";
        }

        $conteudo = "Resposta do profissional {$prof['nome']}\n\n";
        $conteudo .= "RELATÓRIO\n{$relatorio}\n\n";
        $conteudo .= "CONTATO\nWhatsApp: {$whatsapp}\n";
        $conteudo .= "Link: https://wa.me/55{$whatsappLimpo}\n";
        $conteudo .= "Caso queira tentar uma videochamada ou conversar melhor, entre em contato pelo WhatsApp acima.";

        $this->mensagemDAO->inserir(new MensagemDTO(
            idUsuario: $idPaciente,
            titulo: "Resposta ao seu formulário",
            conteudo: $conteudo,
            tipo: 'aviso'
        ));

        $this->formularioDAO->marcarRespondido((int) $idFormulario, $prof['id_profissional']);

        return "OK";
    }

    public function atualizarPerfil(int $idUsuario, array $dados, array $arquivoFoto): string {
        $nome = trim($dados['nome'] ?? '');
        $email = filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $registro = strtoupper(trim($dados['registro'] ?? ''));
        $especialidade = trim($dados['especialidade'] ?? '');
        $cidade = trim($dados['cidade'] ?? '');
        $estado = strtoupper(trim($dados['estado'] ?? ''));
        $descricao = trim($dados['descricao'] ?? '');
        $senha = trim($dados['senha'] ?? '');

        if (!$nome || !$email || strlen($registro) < 4 || !$descricao) {
            return "Preencha os dados obrigatorios";
        }

        if (!array_key_exists($estado, estados_brasileiros())) {
            return "Estado inválido";
        }

        if ($this->usuarioDAO->emailExisteParaOutroUsuario($email, $idUsuario)) {
            return "Email já existe";
        }

        $senhaHash = $senha !== '' ? password_hash($senha, PASSWORD_DEFAULT) : null;
        $this->usuarioDAO->atualizarPerfil($idUsuario, $nome, $email, $senhaHash);
        $this->profissionalDAO->atualizarPerfil($idUsuario, $registro, $especialidade, $cidade, $estado, $descricao);

        if (($arquivoFoto['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $resultadoFoto = $this->salvarFoto($idUsuario, $arquivoFoto);
            if ($resultadoFoto !== 'OK') {
                return $resultadoFoto;
            }
        }

        $_SESSION['nome'] = $nome;
        return "OK";
    }

    private function salvarFoto(int $idUsuario, array $arquivoFoto): string {
        if (($arquivoFoto['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return "Não foi possível enviar a foto";
        }

        if (($arquivoFoto['size'] ?? 0) > 2 * 1024 * 1024) {
            return "A foto deve ter no maximo 2MB";
        }

        $tiposPermitidos = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        $mime = mime_content_type($arquivoFoto['tmp_name']);
        if (!isset($tiposPermitidos[$mime])) {
            return "Envie uma foto JPG, PNG ou WEBP";
        }

        $diretorio = __DIR__ . '/../view/uploads/profissionais';
        if (!is_dir($diretorio) && !mkdir($diretorio, 0775, true)) {
            return "Não foi possível criar a pasta de fotos";
        }

        foreach (['jpg', 'jpeg', 'png', 'webp'] as $extensao) {
            $antiga = $diretorio . '/profissional-' . $idUsuario . '.' . $extensao;
            if (is_file($antiga)) {
                unlink($antiga);
            }
        }

        $destino = $diretorio . '/profissional-' . $idUsuario . '.' . $tiposPermitidos[$mime];
        return move_uploaded_file($arquivoFoto['tmp_name'], $destino) ? "OK" : "Não foi possível salvar a foto";
    }
}
