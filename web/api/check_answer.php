<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/QuestionnaireController.php';

if (!AuthController::isLoggedIn()) {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$questionId = $data['questionId'] ?? null;
$answer = $data['answer'] ?? null;

if (!$questionId || $answer === null) {
    echo json_encode(['error' => 'Paramètres manquants']);
    exit;
}

$result = QuestionnaireController::checkAnswer($questionId, $answer);
echo json_encode($result);
