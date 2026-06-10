<?php
$title = 'Inscription';
$registerPageData = [
    'error' => $error ?? null,
    'success' => $success ?? null,
    'roles' => $roles ?? [],
];
include 'header.php';
?>

<div class="auth-container" id="registerApp" v-cloak>
    <h1>QUESTIONNAIRE</h1>
    <p class="subtitle">Créez votre compte</p>
    
    <div v-if="error" class="error-message">{{ error }}</div>
    <div v-if="success" class="success-message">{{ success }}</div>
    
    <form method="POST" action="../register.php">
        <div class="form-group">
            <label>Pseudo</label>
            <input type="text" name="pseudo" v-model="pseudo" placeholder="Entrez votre pseudo" required>
        </div>
        
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" v-model="email" placeholder="Entrez votre email" required>
        </div>

        <div class="form-group">
            <label>Rôle</label>
            <select name="idrole" v-model="roleId" required>
                <option value="" disabled>Sélectionnez votre rôle</option>
                <?php foreach (($registerPageData['roles'] ?? []) as $role): ?>
                    <option value="<?= htmlspecialchars($role['id']) ?>">
                        <?= htmlspecialchars($role['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="mdp" v-model="mdp" placeholder="Entrez votre mot de passe" required>
        </div>
        
        <div class="form-group">
            <label>Confirmer le mot de passe</label>
            <input type="password" name="confirm_mdp" v-model="confirmMdp" placeholder="Confirmez votre mot de passe" required>
        </div>
        
        <button type="submit" name="register" class="btn btn-primary btn-full" :disabled="!canSubmit">S'inscrire</button>
    </form>
    
    <a href="../index.php" class="btn btn-link">Déjà un compte ? Se connecter</a>
</div>

<script>
window.__REGISTER_PAGE__ = <?= json_encode($registerPageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php include 'footer.php'; ?>
