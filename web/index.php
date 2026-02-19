<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';

if (AuthController::isLoggedIn()) {
    header('Location: accueil.php');
    exit;
}

$error = null;
$success = null;

if (isset($_POST['login'])) {
    $result = AuthController::login($_POST['identifiant'] ?? '', $_POST['mdp'] ?? '');
    
    if ($result['success']) {
        header('Location: accueil.php');
        exit;
    } else {
        $error = $result['message'];
    }
}

if (isset($_GET['registered'])) {
    $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter';
}

include 'views/login.php';
