<?php
require_once __DIR__ . '/../model/dao/UsuarioDAO.php';
require_once __DIR__ . '/../model/dao/ProfissionalDAO.php';
require_once __DIR__ . '/../model/dto/UsuarioDTO.php';

class AdminController {
    private UsuarioDAO $usuarioDAO;
    private ProfissionalDAO $profissionalDAO;

    public function __construct() {
        $this->usuarioDAO = new UsuarioDAO();
        $this->profissionalDAO = new ProfissionalDAO();
    }

    public function painel(): array {
        return [
            'usuarios' => $this->usuarioDAO->listarTodos(),
            'profissionais' => $this->profissionalDAO->listarTodos(),
        ];
    }

    public function existeAdmin(): bool {
        return $this->usuarioDAO->existeAdmin();
    }

    public function alterarStatusUsuario(int $idUsuario, string $status, int $idAdmin): string {
        if ($idUsuario === $idAdmin && $status !== 'ativo') {
            return 'Você não pode bloquear ou inativar seu próprio usuário admin';
        }

        $this->usuarioDAO->alterarStatus($idUsuario, $status);
        return 'OK';
    }

    public function deletarUsuario(int $idUsuario, int $idAdmin): string {
        if ($idUsuario === $idAdmin) {
            return 'Você não pode deletar seu próprio usuário admin';
        }

        $this->usuarioDAO->deletar($idUsuario);
        return 'OK';
    }

    public function alterarValidacaoCrm(int $idProfissional, bool $validado): string {
        $this->profissionalDAO->alterarValidacao($idProfissional, $validado);
        return 'OK';
    }

    public function cadastrarAdmin(array $dados): string {
        $nome  = trim($dados['nome'] ?? '');
        $email = filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $senha = $dados['senha'] ?? '';

        if (!$nome || !$email || !$senha) return 'Dados inválidos';
        if ($this->usuarioDAO->emailExiste($email)) return 'Email já existe';

        $dto = new UsuarioDTO(
            nome: $nome,
            email: $email,
            senha: password_hash($senha, PASSWORD_DEFAULT),
            tipo: 'admin'
        );

        $this->usuarioDAO->inserir($dto);
        return 'OK';
    }

    public function gerenciarAcao(array $dados, int $idAdmin): string {
        $acao = $dados['tipo_acao'] ?? '';

        return match ($acao) {
            'ativar_usuario' => $this->alterarStatusUsuario((int) ($dados['id_usuario'] ?? 0), 'ativo', $idAdmin),
            'bloquear_usuario' => $this->alterarStatusUsuario((int) ($dados['id_usuario'] ?? 0), 'bloqueado', $idAdmin),
            'inativar_usuario' => $this->alterarStatusUsuario((int) ($dados['id_usuario'] ?? 0), 'inativo', $idAdmin),
            'deletar_usuario' => $this->deletarUsuario((int) ($dados['id_usuario'] ?? 0), $idAdmin),
            'validar_crm' => $this->alterarValidacaoCrm((int) ($dados['id_profissional'] ?? 0), true),
            'bloquear_crm' => $this->alterarValidacaoCrm((int) ($dados['id_profissional'] ?? 0), false),
            default => 'Ação inválida',
        };
    }
}
