<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/QuestionnaireController.php';

AuthController::requireLogin();

$error = null;
$questionnaire = null;
$questions = [];
$currentUserLevel = Role::getLevelById($_SESSION['user_role_id'] ?? 0);
$availableLevels = $currentUserLevel > 0 ? range($currentUserLevel, 4) : [];

if (isset($_GET['id'])) {
    $questionnaire = QuestionnaireController::getById($_GET['id']);
    
    if (!$questionnaire || $questionnaire->utilisateurId != $_SESSION['user_id']) {
        header('Location: questionnaires.php');
        exit;
    }
    
    $questions = QuestionnaireController::getQuestions($questionnaire->id);
}

if ($questionnaire && !in_array((int)$questionnaire->niveau, $availableLevels, true)) {
    $availableLevels[] = (int)$questionnaire->niveau;
    sort($availableLevels);
}

if (isset($_POST['save'])) {
    $nom = $_POST['nom'] ?? '';
    $theme = $_POST['theme'] ?? '';
    $niveau = $_POST['niveau'] ?? 1;
    
    if ($questionnaire) {
        $result = QuestionnaireController::update($questionnaire->id, $nom, $theme, $niveau, $_SESSION['user_id']);
    } else {
        $result = QuestionnaireController::create($nom, $theme, $niveau, $_SESSION['user_id']);
        
        if ($result['success']) {
            header('Location: questionnaire_edit.php?id=' . $result['id']);
            exit;
        }
    }
    
    if (!$result['success']) {
        $error = $result['message'];
    } else {
        $questionnaire = QuestionnaireController::getById($questionnaire->id ?? $result['id']);
        $questions = QuestionnaireController::getQuestions($questionnaire->id);
    }
}

include 'views/questionnaire_edit.php';
