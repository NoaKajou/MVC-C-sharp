<?php
require_once __DIR__ . '/../config/database.php';

class Question {
    public $id;
    public $questionnaireId;
    public $numero;
    public $libelle;
    public $typeReponse;
    public $reponseVraiFaux;

    public function __construct($id = null, $questionnaireId = null, $numero = 0, $libelle = '', $typeReponse = 'VraiFaux', $reponseVraiFaux = null) {
        $this->id = $id;
        $this->questionnaireId = $questionnaireId;
        $this->numero = $numero;
        $this->libelle = $libelle;
        $this->typeReponse = $typeReponse;
        $this->reponseVraiFaux = $reponseVraiFaux;
    }

    public static function getAllByQuestionnaire($questionnaireId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id, questionnaire_id, numero, libelle, type_reponse, reponse_vrai_faux 
                              FROM Question WHERE questionnaire_id = ? ORDER BY numero");
        $stmt->execute([$questionnaireId]);
        
        $questions = [];
        while ($row = $stmt->fetch()) {
            $questions[] = new Question(
                $row['id'],
                $row['questionnaire_id'],
                $row['numero'],
                $row['libelle'],
                $row['type_reponse'],
                $row['reponse_vrai_faux']
            );
        }
        return $questions;
    }

    public static function getById($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id, questionnaire_id, numero, libelle, type_reponse, reponse_vrai_faux 
                              FROM Question WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row) {
            return new Question(
                $row['id'],
                $row['questionnaire_id'],
                $row['numero'],
                $row['libelle'],
                $row['type_reponse'],
                $row['reponse_vrai_faux']
            );
        }
        return null;
    }

    public static function getNextNumero($questionnaireId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(numero), 0) + 1 FROM Question WHERE questionnaire_id = ?");
        $stmt->execute([$questionnaireId]);
        return $stmt->fetchColumn();
    }

    public static function create($questionnaireId, $numero, $libelle, $typeReponse, $reponseVraiFaux = null) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO Question (questionnaire_id, numero, libelle, type_reponse, reponse_vrai_faux) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$questionnaireId, $numero, $libelle, $typeReponse, $reponseVraiFaux]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $libelle, $typeReponse, $reponseVraiFaux = null) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE Question SET libelle = ?, type_reponse = ?, reponse_vrai_faux = ? WHERE id = ?");
        return $stmt->execute([$libelle, $typeReponse, $reponseVraiFaux, $id]);
    }

    public static function delete($id) {
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare("DELETE FROM Reponse WHERE question_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $pdo->prepare("DELETE FROM Question WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
