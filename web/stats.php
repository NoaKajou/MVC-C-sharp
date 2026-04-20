<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/QuestionnaireController.php';

if (!AuthController::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$stats = QuestionnaireController::getPedagogicalStats();

include 'views/stats.php';