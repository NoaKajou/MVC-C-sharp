<?php
require_once __DIR__ . '/../config/database.php';

class Questionnaire {
    public $id;
    public $nom;
    public $theme;
    public $utilisateurId;
    public $nombreQuestions;

    public function __construct($id = null, $nom = '', $theme = '', $utilisateurId = null, $nombreQuestions = 0) {
        $this->id = $id;
        $this->nom = $nom;
        $this->theme = $theme;
        $this->utilisateurId = $utilisateurId;
        $this->nombreQuestions = $nombreQuestions;
    }

    public static function getAll() {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT q.id, q.nom, q.theme, q.utilisateur_id,
                            (SELECT COUNT(*) FROM Question WHERE questionnaire_id = q.id) as nb_questions
                            FROM Questionnaire q");
        
        $questionnaires = [];
        while ($row = $stmt->fetch()) {
            $questionnaires[] = new Questionnaire(
                $row['id'],
                $row['nom'],
                $row['theme'],
                $row['utilisateur_id'],
                $row['nb_questions']
            );
        }
        return $questionnaires;
    }

    public static function getAllByUtilisateur($utilisateurId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT q.id, q.nom, q.theme, q.utilisateur_id,
                              (SELECT COUNT(*) FROM Question WHERE questionnaire_id = q.id) as nb_questions
                              FROM Questionnaire q WHERE q.utilisateur_id = ?");
        $stmt->execute([$utilisateurId]);
        
        $questionnaires = [];
        while ($row = $stmt->fetch()) {
            $questionnaires[] = new Questionnaire(
                $row['id'],
                $row['nom'],
                $row['theme'],
                $row['utilisateur_id'],
                $row['nb_questions']
            );
        }
        return $questionnaires;
    }

    public static function getById($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT q.id, q.nom, q.theme, q.utilisateur_id,
                              (SELECT COUNT(*) FROM Question WHERE questionnaire_id = q.id) as nb_questions
                              FROM Questionnaire q WHERE q.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row) {
            return new Questionnaire(
                $row['id'],
                $row['nom'],
                $row['theme'],
                $row['utilisateur_id'],
                $row['nb_questions']
            );
        }
        return null;
    }

    public static function create($nom, $theme, $utilisateurId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO Questionnaire (nom, theme, utilisateur_id) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $theme, $utilisateurId]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $nom, $theme) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE Questionnaire SET nom = ?, theme = ? WHERE id = ?");
        return $stmt->execute([$nom, $theme, $id]);
    }

    public static function delete($id) {
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare("DELETE r FROM Reponse r 
                              INNER JOIN Question q ON r.question_id = q.id 
                              WHERE q.questionnaire_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $pdo->prepare("DELETE FROM Question WHERE questionnaire_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $pdo->prepare("DELETE FROM Questionnaire WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
