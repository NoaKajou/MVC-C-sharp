<?php
require_once __DIR__ . '/../config/database.php';

class Reponse {
    public $id;
    public $questionId;
    public $valeur;
    public $estCorrecte;

    public function __construct($id = null, $questionId = null, $valeur = '', $estCorrecte = false) {
        $this->id = $id;
        $this->questionId = $questionId;
        $this->valeur = $valeur;
        $this->estCorrecte = $estCorrecte;
    }

    public static function getAllByQuestion($questionId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id, question_id, valeur, est_correcte FROM Reponse WHERE question_id = ?");
        $stmt->execute([$questionId]);
        
        $reponses = [];
        while ($row = $stmt->fetch()) {
            $reponses[] = new Reponse(
                $row['id'],
                $row['question_id'],
                $row['valeur'],
                $row['est_correcte']
            );
        }
        return $reponses;
    }

    public static function create($questionId, $valeur, $estCorrecte) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO Reponse (question_id, valeur, est_correcte) VALUES (?, ?, ?)");
        $stmt->execute([$questionId, $valeur, $estCorrecte ? 1 : 0]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $valeur, $estCorrecte) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE Reponse SET valeur = ?, est_correcte = ? WHERE id = ?");
        return $stmt->execute([$valeur, $estCorrecte ? 1 : 0, $id]);
    }

    public static function delete($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM Reponse WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function deleteAllByQuestion($questionId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM Reponse WHERE question_id = ?");
        return $stmt->execute([$questionId]);
    }
}
