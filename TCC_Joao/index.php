<?php
ini_set('session.gc_maxlifetime', (string) (60 * 60 * 24 * 7));
session_set_cookie_params([
    'lifetime' => 60 * 60 * 24 * 7,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/controller/AuthController.php';
require_once __DIR__ . '/controller/PacienteController.php';
require_once __DIR__ . '/controller/ProfissionalController.php';
require_once __DIR__ . '/controller/AdminController.php';

$rota = $_GET['rota'] ?? ($_POST['acao'] ?? 'home');

// Helpers
function autenticado(): bool {
    return isset($_SESSION['id']);
}

function exigirPaciente(): void {
    if (!autenticado() || $_SESSION['tipo'] !== 'paciente') {
        redirect_to('index.php?rota=login');
    }
}

function exigirProfissional(): void {
    if (!autenticado() || $_SESSION['tipo'] !== 'profissional') {
        redirect_to('index.php?rota=login');
    }
}

function exigirAdmin(): void {
    if (!autenticado() || $_SESSION['tipo'] !== 'admin') {
        redirect_to('index.php?rota=login');
    }
}

function podeCadastrarAdmin(): bool {
    if (autenticado() && $_SESSION['tipo'] === 'admin') {
        return true;
    }

    $ctrl = new AdminController();
    return !$ctrl->existeAdmin();
}

function rotaPainelLogado(): string {
    if (!autenticado()) {
        return 'index.php?rota=home';
    }

    return match ($_SESSION['tipo']) {
        'paciente' => 'index.php?rota=painel-paciente',
        'profissional' => 'index.php?rota=painel-profissional',
        'admin' => 'index.php?rota=painel-admin',
        default => 'index.php?rota=home',
    };
}

// Roteamento
switch ($rota) {

    // --- Paginas publicas ---
    case 'home':
        if (autenticado() && $_SESSION['tipo'] !== 'admin') {
            redirect_to(rotaPainelLogado());
        }
        require __DIR__ . '/view/site/home.php';
        break;

    case 'como-funciona':
        require __DIR__ . '/view/site/como-funciona.php';
        break;

    case 'sobre':
        require __DIR__ . '/view/site/sobre.php';
        break;

    case 'cadastro':
        require __DIR__ . '/view/auth/cadastro.php';
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (autenticado() && $_SESSION['tipo'] !== 'admin') {
                redirect_to(rotaPainelLogado());
            }
            require __DIR__ . '/view/auth/login.php';
            break;
        }

        $ctrl = new AuthController();
        $resultado = $ctrl->login($_POST['email'] ?? '', $_POST['senha'] ?? '');
        if ($resultado === 'OK') {
            if ($_SESSION['tipo'] === 'admin') {
                session_destroy();
                redirect_to('index.php?rota=admin-login');
            } elseif ($_SESSION['tipo'] === 'paciente') {
                $destino = '?rota=painel-paciente';
            } else {
                $destino = '?rota=painel-profissional';
            }
            redirect_to("index.php$destino");
        }
        if (str_contains($resultado, 'aguardando validação')) {
            redirect_to('index.php?rota=login&validacao_pendente=1');
        }
        if ($resultado === 'Conta bloqueada') {
            redirect_to('index.php?rota=login&conta_bloqueada=1');
        }
        if ($resultado === 'Conta inativa') {
            redirect_to('index.php?rota=login&conta_inativa=1');
        }
        if ($resultado === 'Conta não encontrada ou deletada') {
            redirect_to('index.php?rota=login&conta_deletada=1');
        }
        echo $resultado;
        break;

    case 'admin-login':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (autenticado() && $_SESSION['tipo'] === 'admin') {
                redirect_to('index.php?rota=painel-admin');
            }
            require __DIR__ . '/view/admin/login.php';
            break;
        }

        $ctrl = new AuthController();
        $resultado = $ctrl->loginAdmin($_POST['email'] ?? '', $_POST['senha'] ?? '');
        if ($resultado === 'OK') {
            redirect_to('index.php?rota=painel-admin');
        }
        echo $resultado;
        break;

    case 'cadastro-paciente':
        if (autenticado() && $_SESSION['tipo'] !== 'admin') {
            redirect_to(rotaPainelLogado());
        }
        require __DIR__ . '/view/auth/cadastro-paciente.php';
        break;

    case 'cadastro-profissional':
        if (autenticado() && $_SESSION['tipo'] !== 'admin') {
            redirect_to(rotaPainelLogado());
        }
        require __DIR__ . '/view/auth/cadastro-profissional.php';
        break;

    case 'cadastrar_paciente':
        $ctrl = new AuthController();
        $resultado = $ctrl->cadastrarPaciente($_POST);
        if ($resultado === 'Paciente cadastrado!') {
            redirect_to('index.php?rota=login&sucesso=1');
        }
        echo $resultado;
        break;

    case 'cadastrar_profissional':
        $ctrl = new AuthController();
        $resultado = $ctrl->cadastrarProfissional($_POST);
        if ($resultado === 'Profissional cadastrado!') {
            redirect_to('index.php?rota=login&profissional_pendente=1');
        }
        echo $resultado;
        break;

    case 'logout':
        session_destroy();
        redirect_to('index.php?rota=login');

    // --- Paciente ---
    case 'painel-paciente':
        exigirPaciente();
        $ctrl = new PacienteController();
        extract($ctrl->painel($_SESSION['id']));
        $nome = $_SESSION['nome'];
        require __DIR__ . '/view/paciente/painel.php';
        break;

    case 'formulario':
        exigirPaciente();
        $ctrl = new PacienteController();
        $profissionais = $ctrl->listarProfissionais();
        $profissionalSelecionado = filter_input(INPUT_GET, 'profissional', FILTER_VALIDATE_INT) ?: 0;
        require __DIR__ . '/view/paciente/formulario.php';
        break;

    case 'lista-profissionais':
        $ctrl = new PacienteController();
        $estadoSelecionado = strtoupper(trim($_GET['estado'] ?? ''));
        $profissionais = $ctrl->listarProfissionais($estadoSelecionado);
        require __DIR__ . '/view/paciente/lista-profissionais.php';
        break;

    case 'enviar_formulario':
        exigirPaciente();
        $ctrl = new PacienteController();
        $resultado = $ctrl->enviarFormulario(
            $_SESSION['id'],
            (int) ($_POST['id_profissional'] ?? 0),
            $_POST
        );
        if ($resultado === 'OK') {
            redirect_to('index.php?rota=painel-paciente&ok=1');
        }
        echo $resultado;
        break;

    case 'cancelar_consulta':
        exigirPaciente();
        $ctrl = new PacienteController();
        $resultado = $ctrl->cancelarConsulta(
            (int) $_SESSION['id'],
            (int) ($_POST['id_consulta'] ?? 0)
        );
        if ($resultado === 'OK') {
            redirect_to('index.php?rota=painel-paciente&consulta_cancelada=1#consultas');
        }
        echo $resultado;
        break;

    case 'remarcar_consulta':
        exigirPaciente();
        $ctrl = new PacienteController();
        $resultado = $ctrl->remarcarConsulta(
            (int) $_SESSION['id'],
            (int) ($_POST['id_consulta'] ?? 0)
        );
        if ($resultado === 'OK') {
            redirect_to('index.php?rota=lista-profissionais&remarcar=1');
        }
        echo $resultado;
        break;

    // --- Admin ---
    case 'painel-admin':
        exigirAdmin();
        $ctrl = new AdminController();
        $dados = $ctrl->painel();
        $usuarios = $dados['usuarios'];
        $profissionais = $dados['profissionais'];
        require __DIR__ . '/view/admin/painel.php';
        break;

    case 'cadastro-admin':
        require __DIR__ . '/view/admin/cadastro.php';
        break;

    case 'cadastrar_admin':
        $ctrl = new AdminController();
        $resultado = $ctrl->cadastrarAdmin($_POST);
        if ($resultado === 'OK') {
            if (autenticado() && $_SESSION['tipo'] === 'admin') {
                redirect_to('index.php?rota=painel-admin&admin_criado=1');
            }
            redirect_to('index.php?rota=admin-login&admin_criado=1');
        }
        echo $resultado;
        break;

    case 'admin_gerenciar':
        exigirAdmin();
        $ctrl = new AdminController();
        $resultado = $ctrl->gerenciarAcao($_POST, (int) $_SESSION['id']);
        if ($resultado === 'OK') {
            redirect_to('index.php?rota=painel-admin&ok=1');
        }
        echo $resultado;
        break;

    case 'admin_alterar_status':
        exigirAdmin();
        $ctrl = new AdminController();
        $resultado = $ctrl->alterarStatusUsuario(
            (int) ($_POST['id_usuario'] ?? 0),
            $_POST['status'] ?? '',
            (int) $_SESSION['id']
        );
        if ($resultado === 'OK') {
            redirect_to('index.php?rota=painel-admin&ok=1');
        }
        echo $resultado;
        break;

    case 'admin_deletar_usuario':
        exigirAdmin();
        $ctrl = new AdminController();
        $resultado = $ctrl->deletarUsuario(
            (int) ($_POST['id_usuario'] ?? 0),
            (int) $_SESSION['id']
        );
        if ($resultado === 'OK') {
            redirect_to('index.php?rota=painel-admin&ok=1');
        }
        echo $resultado;
        break;

    case 'admin_validar_crm':
        exigirAdmin();
        $ctrl = new AdminController();
        $resultado = $ctrl->alterarValidacaoCrm((int) ($_POST['id_profissional'] ?? 0), true);
        if ($resultado === 'OK') {
            redirect_to('index.php?rota=painel-admin&ok=1');
        }
        echo $resultado;
        break;

    case 'admin_bloquear_crm':
        exigirAdmin();
        $ctrl = new AdminController();
        $resultado = $ctrl->alterarValidacaoCrm((int) ($_POST['id_profissional'] ?? 0), false);
        if ($resultado === 'OK') {
            redirect_to('index.php?rota=painel-admin&ok=1');
        }
        echo $resultado;
        break;

    // --- Profissional ---
    case 'painel-profissional':
        exigirProfissional();
        $ctrl = new ProfissionalController();
        $dados = $ctrl->painel($_SESSION['id']);
        if (isset($dados['erro'])) { echo $dados['erro']; break; }
        $profissional = $dados['profissional'];
        $formularios = $dados['formularios'];
        require __DIR__ . '/view/profissional/painel.php';
        break;

    case 'atualizar_perfil_profissional':
        exigirProfissional();
        $ctrl = new ProfissionalController();
        $resultado = $ctrl->atualizarPerfil((int) $_SESSION['id'], $_POST, $_FILES['foto'] ?? []);
        if ($resultado === 'OK') {
            redirect_to('index.php?rota=painel-profissional&perfil_ok=1');
        }
        echo $resultado;
        break;

    case 'responder_formulario':
        exigirProfissional();
        $ctrl = new ProfissionalController();
        $resultado = $ctrl->responderFormulario((int) $_SESSION['id'], $_POST);
        if ($resultado === 'OK') {
            redirect_to('index.php?rota=painel-profissional&resposta_ok=1');
        }
        echo $resultado;
        break;

    default:
        redirect_to('index.php?rota=home');
}
