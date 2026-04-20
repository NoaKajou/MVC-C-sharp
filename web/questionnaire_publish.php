<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/QuestionnaireController.php';

AuthController::requireLogin();

if (!isset($_GET['id'])) {
    header('Location: questionnaires.php?error=' . urlencode('Questionnaire introuvable'));
    exit;
}

$result = QuestionnaireController::publish($_GET['id'], $_SESSION['user_id']);

if ($result['success']) {
    header('Location: questionnaires.php?success=' . urlencode($result['message']));
    exit;
}

header('Location: questionnaires.php?error=' . urlencode($result['message']));
exit;
