<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/QuestionnaireController.php';

AuthController::requireLogin();

$error = null;
$question = null;
$reponses = [];
$questionnaireId = $_GET['questionnaire_id'] ?? $_POST['questionnaire_id'] ?? null;

if (!$questionnaireId) {
    header('Location: questionnaires.php');
    exit;
}

$questionnaire = QuestionnaireController::getById($questionnaireId);
if (!$questionnaire || $questionnaire->utilisateurId != $_SESSION['user_id']) {
    header('Location: questionnaires.php');
    exit;
}

if (isset($_GET['id'])) {
    $question = QuestionnaireController::getQuestion($_GET['id']);
    if ($question) {
        $reponses = QuestionnaireController::getReponses($question->id);
    }
}

if (isset($_POST['save'])) {
    $libelle = $_POST['libelle'] ?? '';
    $typeReponse = $_POST['type_reponse'] ?? 'VraiFaux';
    $reponseVraiFaux = isset($_POST['reponse_vrai_faux']) ? (bool)$_POST['reponse_vrai_faux'] : null;
    
    $reponsesArray = [];
    if (isset($_POST['reponses'])) {
        foreach ($_POST['reponses'] as $r) {
            $reponsesArray[] = [
                'valeur' => $r['valeur'],
                'estCorrecte' => (bool)($r['estCorrecte'] ?? false)
            ];
        }
    }
    
    if ($question) {
        $result = QuestionnaireController::updateQuestion($question->id, $libelle, $typeReponse, $reponseVraiFaux, $reponsesArray);
    } else {
        $result = QuestionnaireController::addQuestion($questionnaireId, $libelle, $typeReponse, $reponseVraiFaux, $reponsesArray);
    }
    
    if ($result['success']) {
        header('Location: questionnaire_edit.php?id=' . $questionnaireId);
        exit;
    } else {
        $error = $result['message'];
    }
}

include 'views/question_edit.php';
