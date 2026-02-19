<?php
session_start();
require_once __DIR__ . '/../models/Utilisateur.php';

class AuthController {
    
    public static function login($identifiant, $mdp) {
        if (empty($identifiant) || empty($mdp)) {
            return ['success' => false, 'message' => 'Veuillez remplir tous les champs'];
        }

        $utilisateur = Utilisateur::getByEmailAndPassword($identifiant, $mdp);
        
        if (!$utilisateur) {
            $utilisateur = Utilisateur::getByPseudoAndPassword($identifiant, $mdp);
        }

        if ($utilisateur) {
            $_SESSION['user_id'] = $utilisateur->id;
            $_SESSION['user_pseudo'] = $utilisateur->pseudo;
            $_SESSION['user_email'] = $utilisateur->email;
            return ['success' => true, 'message' => 'Connexion réussie'];
        }

        return ['success' => false, 'message' => 'Identifiants incorrects'];
    }

    public static function register($pseudo, $email, $mdp, $confirmMdp) {
        if (empty($pseudo) || empty($email) || empty($mdp) || empty($confirmMdp)) {
            return ['success' => false, 'message' => 'Veuillez remplir tous les champs'];
        }

        if ($mdp !== $confirmMdp) {
            return ['success' => false, 'message' => 'Les mots de passe ne correspondent pas'];
        }

        if (strlen($mdp) < 4) {
            return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 4 caractères'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email invalide'];
        }

        if (Utilisateur::emailExists($email)) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
        }

        if (Utilisateur::pseudoExists($pseudo)) {
            return ['success' => false, 'message' => 'Ce pseudo est déjà utilisé'];
        }

        if (Utilisateur::create($pseudo, $email, $mdp)) {
            return ['success' => true, 'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter'];
        }

        return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
    }

    public static function logout() {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: index.php');
            exit;
        }
    }

    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            return Utilisateur::getById($_SESSION['user_id']);
        }
        return null;
    }
}
