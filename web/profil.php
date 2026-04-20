<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/QuestionnaireController.php';

AuthController::requireLogin();

$user = AuthController::getCurrentUser();
$questionnaires = QuestionnaireController::getMine($_SESSION['user_id']);
$history = QuestionnaireController::getPlayHistory($_SESSION['user_id'], 25);

include 'views/profil.php';
