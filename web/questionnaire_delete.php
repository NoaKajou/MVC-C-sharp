<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/QuestionnaireController.php';

AuthController::requireLogin();

if (isset($_GET['id'])) {
    QuestionnaireController::delete($_GET['id'], $_SESSION['user_id']);
}

header('Location: questionnaires.php');
exit;
