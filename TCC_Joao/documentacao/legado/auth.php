<?php

function iniciarSessao() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function verificarLogin() {
    iniciarSessao();

    if (!isset($_SESSION['id'])) {
        header("Location: login.html");
        exit;
    }
}

function verificarPaciente() {
    verificarLogin();

    if ($_SESSION['tipo'] !== 'paciente') {
        header("Location: login.html");
        exit;
    }
}

function verificarProfissional() {
    verificarLogin();

    if ($_SESSION['tipo'] !== 'profissional') {
        header("Location: login.html");
        exit;
    }
}