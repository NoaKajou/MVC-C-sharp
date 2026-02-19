<?php
$title = 'Questionnaires';
include 'header.php';
?>

<div class="page-container">
    <header class="top-bar">
        <h1>Questionnaires</h1>
        <div class="header-actions">
            <a href="accueil.php" class="btn">Retour à l'accueil</a>
            <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
        </div>
    </header>
    
    <div class="questionnaires-grid">
        <section class="questionnaire-section">
            <h2>Tous les questionnaires</h2>
            <p class="section-subtitle">Sélectionnez un questionnaire pour y jouer</p>
            
            <div class="questionnaire-list" id="allQuestionnaires">
                <?php if (empty($allQuestionnaires)): ?>
                    <p class="empty-message">Aucun questionnaire disponible</p>
                <?php else: ?>
                    <?php foreach ($allQuestionnaires as $q): ?>
                        <div class="questionnaire-card" data-id="<?= $q->id ?>">
                            <h3><?= htmlspecialchars($q->nom) ?></h3>
                            <div class="questionnaire-meta">
                                <span class="theme"><?= htmlspecialchars($q->theme) ?></span>
                                <span class="count"><?= $q->nombreQuestions ?> questions</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <button id="btnJouer" class="btn btn-success btn-full" disabled>Jouer au questionnaire sélectionné</button>
        </section>
        
        <section class="questionnaire-section">
            <h2>Mes questionnaires</h2>
            <p class="section-subtitle">Gérez vos propres questionnaires</p>
            
            <div class="questionnaire-list" id="myQuestionnaires">
                <?php if (empty($myQuestionnaires)): ?>
                    <p class="empty-message">Vous n'avez pas encore créé de questionnaire</p>
                <?php else: ?>
                    <?php foreach ($myQuestionnaires as $q): ?>
                        <div class="questionnaire-card my-card" data-id="<?= $q->id ?>">
                            <h3><?= htmlspecialchars($q->nom) ?></h3>
                            <div class="questionnaire-meta">
                                <span class="theme"><?= htmlspecialchars($q->theme) ?></span>
                                <span class="count"><?= $q->nombreQuestions ?> questions</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="action-buttons">
                <a href="questionnaire_edit.php" class="btn btn-primary btn-full">Nouveau questionnaire</a>
                <div class="btn-row">
                    <button id="btnEditer" class="btn" disabled>Editer</button>
                    <button id="btnSupprimer" class="btn btn-danger" disabled>Supprimer</button>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
let selectedAllId = null;
let selectedMyId = null;

document.querySelectorAll('#allQuestionnaires .questionnaire-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('#allQuestionnaires .questionnaire-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        selectedAllId = card.dataset.id;
        document.getElementById('btnJouer').disabled = false;
    });
});

document.querySelectorAll('#myQuestionnaires .questionnaire-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('#myQuestionnaires .questionnaire-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        selectedMyId = card.dataset.id;
        document.getElementById('btnEditer').disabled = false;
        document.getElementById('btnSupprimer').disabled = false;
    });
});

document.getElementById('btnJouer').addEventListener('click', () => {
    if (selectedAllId) {
        window.location.href = 'play.php?id=' + selectedAllId;
    }
});

document.getElementById('btnEditer').addEventListener('click', () => {
    if (selectedMyId) {
        window.location.href = 'questionnaire_edit.php?id=' + selectedMyId;
    }
});

document.getElementById('btnSupprimer').addEventListener('click', () => {
    if (selectedMyId && confirm('Êtes-vous sûr de vouloir supprimer ce questionnaire ?')) {
        window.location.href = 'questionnaire_delete.php?id=' + selectedMyId;
    }
});
</script>

<?php include 'footer.php'; ?>
