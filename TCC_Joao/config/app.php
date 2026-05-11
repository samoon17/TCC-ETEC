<?php

function base_url(string $path = ''): string {
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    $base = $base === '' ? '' : $base;

    return $base . ($path !== '' ? '/' . ltrim($path, '/') : '');
}

function redirect_to(string $path): void {
    header('Location: ' . base_url($path));
    exit;
}

function inicio_url(): string {
    if (!isset($_SESSION['tipo'])) {
        return base_url('index.php?rota=home');
    }

    return match ($_SESSION['tipo']) {
        'paciente' => base_url('index.php?rota=painel-paciente'),
        'profissional' => base_url('index.php?rota=painel-profissional'),
        'admin' => base_url('index.php?rota=painel-admin'),
        default => base_url('index.php?rota=home'),
    };
}

function logo_site(): string {
    $urlHome = htmlspecialchars(base_url('index.php?rota=home'), ENT_QUOTES, 'UTF-8');
    $urlLogo = htmlspecialchars(asset_url('view/assets/logo-vivamente.svg'), ENT_QUOTES, 'UTF-8');

    return '<h2 class="logo"><a href="' . $urlHome . '" aria-label="VivaMente"><img class="logo-icon" src="' . $urlLogo . '" alt="" style="display:block;width:32px;height:32px;max-width:32px;max-height:32px;object-fit:contain;"></a></h2>';
}

function favicon_tags(): string {
    $urlIcone = htmlspecialchars(asset_url('view/assets/logo-vivamente.svg'), ENT_QUOTES, 'UTF-8');

    return '<link rel="icon" type="image/svg+xml" href="' . $urlIcone . '">';
}

function asset_url(string $path): string {
    $file = __DIR__ . '/../' . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
    $version = is_file($file) ? filemtime($file) : time();

    return base_url($path) . '?v=' . $version;
}

function foto_profissional_url(int $idUsuario): ?string {
    $diretorio = __DIR__ . '/../view/uploads/profissionais';
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $extensao) {
        $arquivo = $diretorio . '/profissional-' . $idUsuario . '.' . $extensao;
        if (is_file($arquivo)) {
            return asset_url('view/uploads/profissionais/profissional-' . $idUsuario . '.' . $extensao);
        }
    }

    return null;
}

function estados_brasileiros(): array {
    return [
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceara',
        'DF' => 'Distrito Federal',
        'ES' => 'Espírito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MT' => 'Mato Grosso',
        'MS' => 'Mato Grosso do Sul',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins',
    ];
}
