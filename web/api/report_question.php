<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../config/database.php';

if (!AuthController::isLoggedIn()) {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$questionId = isset($data['questionId']) ? (int)$data['questionId'] : null;
$description = isset($data['description']) ? trim($data['description']) : '';

if (!$questionId || $description === '') {
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    exit;
}

if (strlen($description) > 1000) {
    echo json_encode(['success' => false, 'message' => 'Description trop longue (max 1000 caractères)']);
    exit;
}

try {
    $pdo = Database::getConnection();

    // Crée la table si elle n'existe pas encore
    $pdo->exec("CREATE TABLE IF NOT EXISTS Signalement (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_id INT NOT NULL,
        utilisateur_id INT NOT NULL,
        description TEXT NOT NULL,
        date_signalement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (question_id) REFERENCES Question(id) ON DELETE CASCADE,
        FOREIGN KEY (utilisateur_id) REFERENCES Utilisateur(id) ON DELETE CASCADE
    )");

    $stmt = $pdo->prepare("INSERT INTO Signalement (question_id, utilisateur_id, description) VALUES (?, ?, ?)");
    $stmt->execute([$questionId, $_SESSION['user_id'], $description]);

    echo json_encode(['success' => true, 'message' => 'Signalement envoyé, merci !']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi du signalement']);
}
