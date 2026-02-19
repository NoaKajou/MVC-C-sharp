<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';

if (AuthController::isLoggedIn()) {
    header('Location: accueil.php');
    exit;
}

$error = null;
$success = null;

if (isset($_POST['register'])) {
    $result = AuthController::register(
        $_POST['pseudo'] ?? '',
        $_POST['email'] ?? '',
        $_POST['mdp'] ?? '',
        $_POST['confirm_mdp'] ?? ''
    );
    
    if ($result['success']) {
        header('Location: index.php?registered=1');
        exit;
    } else {
        $error = $result['message'];
    }
}

include 'views/register.php';
