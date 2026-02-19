<?php
$title = 'Inscription';
include 'header.php';
?>

<div class="auth-container">
    <h1>QUESTIONNAIRE</h1>
    <p class="subtitle">Créez votre compte</p>
    
    <?php if (isset($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="success-message"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form method="POST" action="../register.php">
        <div class="form-group">
            <label>Pseudo</label>
            <input type="text" name="pseudo" placeholder="Entrez votre pseudo" required>
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Entrez votre email" required>
        </div>
        
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="mdp" placeholder="Entrez votre mot de passe" required>
        </div>
        
        <div class="form-group">
            <label>Confirmer le mot de passe</label>
            <input type="password" name="confirm_mdp" placeholder="Confirmez votre mot de passe" required>
        </div>
        
        <button type="submit" name="register" class="btn btn-primary btn-full">S'inscrire</button>
    </form>
    
    <a href="../index.php" class="btn btn-link">Déjà un compte ? Se connecter</a>
</div>

<?php include 'footer.php'; ?>
