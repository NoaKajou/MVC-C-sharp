<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/QuestionnaireController.php';

AuthController::requireLogin();

$questionnaireId = $_GET['id'] ?? null;

if (!$questionnaireId) {
    header('Location: questionnaires.php');
    exit;
}

$questionnaire = QuestionnaireController::getById($questionnaireId);
if (!$questionnaire) {
    header('Location: questionnaires.php');
    exit;
}

if (!QuestionnaireController::canCurrentUserAccess($questionnaire)) {
    header('Location: questionnaires.php?error=Vous+ne+pouvez+pas+acceder+a+ce+questionnaire');
    exit;
}

QuestionnaireController::trackQuestionnaireAccess($_SESSION['user_id'], $questionnaireId);

$questions = QuestionnaireController::getQuestions($questionnaireId);

$allReponses = [];
foreach ($questions as $q) {
    $reponses = QuestionnaireController::getReponses($q->id);
    $allReponses[$q->id] = array_map(function($r) {
        return ['id' => $r->id, 'valeur' => $r->valeur, 'estCorrecte' => $r->estCorrecte];
    }, $reponses);
}

include 'views/play.php';
