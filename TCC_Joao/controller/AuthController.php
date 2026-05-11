<?php
require_once __DIR__ . '/../model/dao/UsuarioDAO.php';
require_once __DIR__ . '/../model/dao/ProfissionalDAO.php';
require_once __DIR__ . '/../model/dto/UsuarioDTO.php';
require_once __DIR__ . '/../model/dto/ProfissionalDTO.php';

class AuthController {
    private UsuarioDAO $usuarioDAO;
    private ProfissionalDAO $profissionalDAO;

    public function __construct() {
        $this->usuarioDAO = new UsuarioDAO();
        $this->profissionalDAO = new ProfissionalDAO();
    }

    public function login(string $email, string $senha): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if ($this->usuarioDAO->estaBloqueado($email, $ip)) {
            return "Muitas tentativas. Tente mais tarde.";
        }

        $user = $this->usuarioDAO->buscarPorEmail($email);

        if (!$user) {
            $this->usuarioDAO->registrarErroLogin($email, $ip);
            return "Conta não encontrada ou deletada";
        }

        if (!password_verify($senha, $user['senha'])) {
            $this->usuarioDAO->registrarErroLogin($email, $ip);
            return "Email ou senha inválidos";
        }

        if ($user['status'] === 'bloqueado') {
            return "Conta bloqueada";
        }

        if ($user['status'] === 'inativo') {
            return "Conta inativa";
        }

        if ($user['tipo_usuario'] === 'profissional') {
            $profissional = $this->profissionalDAO->buscarPorUsuario((int) $user['id_usuario']);
            if (!$profissional || (int) $profissional['validado'] !== 1) {
                return "Seu cadastro profissional está aguardando validação do registro pelo admin.";
            }
        }

        $this->usuarioDAO->resetarTentativas($email, $ip);

        if (session_status() === PHP_SESSION_NONE) session_start();
        session_regenerate_id(true);

        $_SESSION['id']   = $user['id_usuario'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['tipo'] = $user['tipo_usuario'];

        return "OK";
    }

    public function loginAdmin(string $email, string $senha): string {
        $resultado = $this->login($email, $senha);

        if ($resultado !== 'OK') {
            return $resultado;
        }

        if (($_SESSION['tipo'] ?? '') !== 'admin') {
            session_destroy();
            return 'Acesso permitido apenas para administradores';
        }

        return 'OK';
    }

    public function cadastrarPaciente(array $dados): string {
        $nome  = trim($dados['nome'] ?? '');
        $email = filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $senha = $dados['senha'] ?? '';

        if (!$nome || !$email || !$senha) return "Dados inválidos";
        if ($this->usuarioDAO->emailExiste($email)) return "Email já existe";

        $dto = new UsuarioDTO(
            nome: $nome,
            email: $email,
            senha: password_hash($senha, PASSWORD_DEFAULT),
            tipo: 'paciente',
            dataNascimento: $dados['data_nascimento'] ?? null
        );

        $this->usuarioDAO->inserir($dto);
        return "Paciente cadastrado!";
    }

    public function cadastrarProfissional(array $dados): string {
        $nome  = trim($dados['nome'] ?? '');
        $email = filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $senha = $dados['senha'] ?? '';

        if (!$nome || !$email || !$senha) return "Dados inválidos";
        if ($this->usuarioDAO->emailExiste($email)) return "Email já existe";

        $registro = strtoupper(trim($dados['registro'] ?? ''));
        if (strlen($registro) < 4) {
            return "Informe um registro profissional válido";
        }

        $estado = strtoupper(trim($dados['estado'] ?? ''));
        if (!array_key_exists($estado, estados_brasileiros())) {
            return "Estado inválido";
        }

        try {
            $usuarioDTO = new UsuarioDTO(
                nome: $nome,
                email: $email,
                senha: password_hash($senha, PASSWORD_DEFAULT),
                tipo: 'profissional'
            );

            $idUsuario = $this->usuarioDAO->inserir($usuarioDTO);

            $profDTO = new ProfissionalDTO(
                idUsuario: $idUsuario,
                registro: $registro,
                especialidade: trim($dados['especialidade'] ?? ''),
                descricao: trim($dados['descricao'] ?? ''),
                cidade: trim($dados['cidade'] ?? ''),
                estado: $estado
            );

            $this->profissionalDAO->inserir($profDTO);
            return "Profissional cadastrado!";
        } catch (Exception $e) {
            return "Erro: " . $e->getMessage();
        }
    }
}
