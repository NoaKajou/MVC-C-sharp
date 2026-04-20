<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/QuestionnaireController.php';

AuthController::requireLogin();

$allQuestionnaires = QuestionnaireController::getAll();
$myQuestionnaires = QuestionnaireController::getMine($_SESSION['user_id']);
$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

include 'views/questionnaires.php';
