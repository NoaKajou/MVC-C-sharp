<?php
require_once __DIR__ . '/../config/database.php';

class Utilisateur {
    public $id;
    public $email;
    public $pseudo;
    public $mdp;

    public function __construct($id = null, $email = '', $pseudo = '', $mdp = '') {
        $this->id = $id;
        $this->email = $email;
        $this->pseudo = $pseudo;
        $this->mdp = $mdp;
    }

    public static function getByEmailAndPassword($email, $mdp) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id, email, pseudo, mdp FROM Utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        
        if ($row && password_verify($mdp, $row['mdp'])) {
            return new Utilisateur($row['id'], $row['email'], $row['pseudo'], $row['mdp']);
        }
        return null;
    }

    public static function getByPseudoAndPassword($pseudo, $mdp) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id, email, pseudo, mdp FROM Utilisateur WHERE pseudo = ?");
        $stmt->execute([$pseudo]);
        $row = $stmt->fetch();
        
        if ($row && password_verify($mdp, $row['mdp'])) {
            return new Utilisateur($row['id'], $row['email'], $row['pseudo'], $row['mdp']);
        }
        return null;
    }

    public static function getById($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id, email, pseudo, mdp FROM Utilisateur WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row) {
            return new Utilisateur($row['id'], $row['email'], $row['pseudo'], $row['mdp']);
        }
        return null;
    }

    public static function emailExists($email) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    public static function pseudoExists($pseudo) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Utilisateur WHERE pseudo = ?");
        $stmt->execute([$pseudo]);
        return $stmt->fetchColumn() > 0;
    }

    public static function create($pseudo, $email, $mdp) {
        $pdo = Database::getConnection();
        $hashedPassword = password_hash($mdp, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO Utilisateur (pseudo, email, mdp) VALUES (?, ?, ?)");
        return $stmt->execute([$pseudo, $email, $hashedPassword]);
    }
}
