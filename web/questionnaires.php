<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/QuestionnaireController.php';

AuthController::requireLogin();

$allQuestionnaires = QuestionnaireController::getAll();
$myQuestionnaires = QuestionnaireController::getMine($_SESSION['user_id']);

include 'views/questionnaires.php';
