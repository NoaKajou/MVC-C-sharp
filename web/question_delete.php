<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/QuestionnaireController.php';

AuthController::requireLogin();

$questionId = $_GET['id'] ?? null;
$questionnaireId = $_GET['questionnaire_id'] ?? null;

if ($questionId) {
    QuestionnaireController::deleteQuestion($questionId);
}

header('Location: questionnaire_edit.php?id=' . $questionnaireId);
exit;
