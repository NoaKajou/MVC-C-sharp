<?php
$title = 'Connexion';
include 'header.php';
?>

<div class="auth-container">
    <h1>QUESTIONNAIRE</h1>
    <p class="subtitle">Connectez-vous pour continuer</p>
    
    <?php if (isset($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="success-message"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form method="POST" action="../index.php">
        <div class="form-group">
            <label>Email ou Pseudo</label>
            <input type="text" name="identifiant" placeholder="Entrez votre email ou pseudo" required>
        </div>
        
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="mdp" placeholder="Entrez votre mot de passe" required>
        </div>
        
        <button type="submit" name="login" class="btn btn-primary btn-full">Se connecter</button>
    </form>
    
    <a href="../register.php" class="btn btn-link">Pas encore de compte ? S'inscrire</a>
</div>

<?php include 'footer.php'; ?>
