<?php
$title = 'Mon profil';
include 'header.php';
?>

<div class="page-container">
    <header class="top-bar">
        <h1>Mon Profil</h1>
        <div class="header-actions">
            <a href="accueil.php" class="btn">Retour à l'accueil</a>
            <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
        </div>
    </header>
    
    <div class="profil-container">
        <div class="profil-avatar">
            <div class="avatar-circle">
                <?= strtoupper(substr($user->pseudo, 0, 1)) ?>
            </div>
            <h2><?= htmlspecialchars($user->pseudo) ?></h2>
        </div>
        
        <div class="profil-info">
            <h3>Informations du compte</h3>
            
            <div class="info-card">
                <span class="label">ID</span>
                <span class="value"><?= $user->id ?></span>
            </div>
            
            <div class="info-card">
                <span class="label">Pseudo</span>
                <span class="value"><?= htmlspecialchars($user->pseudo) ?></span>
            </div>
            
            <div class="info-card">
                <span class="label">Email</span>
                <span class="value"><?= htmlspecialchars($user->email) ?></span>
            </div>
        </div>
        
        <div class="profil-questionnaires">
            <h3>Mes Questionnaires</h3>
            
            <?php if (empty($questionnaires)): ?>
                <p class="empty-message">Aucun questionnaire créé</p>
            <?php else: ?>
                <div class="questionnaire-list-small">
                    <?php foreach ($questionnaires as $q): ?>
                        <div class="questionnaire-item">
                            <span class="icon">📋</span>
                            <div class="item-info">
                                <strong><?= htmlspecialchars($q->nom) ?></strong>
                                <span><?= htmlspecialchars($q->theme) ?> - <?= $q->nombreQuestions ?> questions</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
