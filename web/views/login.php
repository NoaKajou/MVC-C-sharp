<?php
$title = 'Connexion';
$loginPageData = [
    'error' => $error ?? null,
    'success' => $success ?? null,
];
include 'header.php';
?>

<div class="auth-container" id="loginApp" v-cloak>
    <h1>QUESTIONNAIRE</h1>
    <p class="subtitle">Connectez-vous pour continuer</p>
    
    <div v-if="error" class="error-message">{{ error }}</div>
    <div v-if="success" class="success-message">{{ success }}</div>
    
    <form method="POST" action="../index.php">
        <div class="form-group">
            <label>Email ou Pseudo</label>
            <input type="text" name="identifiant" v-model="identifiant" placeholder="Entrez votre email ou pseudo" required>
        </div>
        
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="mdp" v-model="mdp" placeholder="Entrez votre mot de passe" required>
        </div>
        
        <button type="submit" name="login" class="btn btn-primary btn-full" :disabled="!canSubmit">Se connecter</button>
    </form>
    
    <a href="../register.php" class="btn btn-link">Pas encore de compte ? S'inscrire</a>
</div>

<script>
window.__LOGIN_PAGE__ = <?= json_encode($loginPageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php include 'footer.php'; ?>
