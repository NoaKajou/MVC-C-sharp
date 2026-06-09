<?php
$title = 'Questionnaires';
$questionnairePageData = [
    'allQuestionnaires' => array_map(static function ($questionnaire) {
        return [
            'id' => $questionnaire->id,
            'nom' => $questionnaire->nom,
            'theme' => $questionnaire->theme,
            'nombreQuestions' => $questionnaire->nombreQuestions,
            'estPublie' => $questionnaire->estPublie ?? false,
        ];
    }, $allQuestionnaires ?? []),
    'myQuestionnaires' => array_map(static function ($questionnaire) {
        return [
            'id' => $questionnaire->id,
            'nom' => $questionnaire->nom,
            'theme' => $questionnaire->theme,
            'nombreQuestions' => $questionnaire->nombreQuestions,
            'estPublie' => $questionnaire->estPublie ?? false,
        ];
    }, $myQuestionnaires ?? []),
];
include 'header.php';
?>

<div class="page-container" id="questionnairesApp" v-cloak>
    <header class="top-bar">
        <h1>Questionnaires</h1>
        <div class="header-actions">
            <a href="accueil.php" class="btn">Retour à l'accueil</a>
            <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
        </div>
    </header>

    <?php if (!empty($success)): ?>
        <div class="success-message"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="questionnaires-grid">
        <section class="questionnaire-section">
            <h2>Tous les questionnaires</h2>
            <p class="section-subtitle">Sélectionnez un questionnaire pour y jouer</p>
            <div class="form-group">
                <input type="search" v-model="searchAll" placeholder="Rechercher un questionnaire ou un thème">
            </div>
            
            <div class="questionnaire-list">
                <p v-if="allQuestionnaires.length === 0" class="empty-message">Aucun questionnaire disponible</p>
                <p v-else-if="filteredAllQuestionnaires.length === 0" class="empty-message">Aucun résultat pour cette recherche</p>
                <div
                    v-for="questionnaire in filteredAllQuestionnaires"
                    :key="questionnaire.id"
                    class="questionnaire-card"
                    :class="{ selected: selectedAllId === questionnaire.id }"
                    @click="selectAllQuestionnaire(questionnaire.id)"
                >
                    <h3>{{ questionnaire.nom }}</h3>
                    <div class="questionnaire-meta">
                        <span class="theme">{{ questionnaire.theme }}</span>
                        <span class="count">{{ questionnaire.nombreQuestions }} questions</span>
                    </div>
                </div>
            </div>
            
            <button class="btn btn-success btn-full" :disabled="!selectedAllId" @click="playSelectedQuestionnaire">Jouer au questionnaire sélectionné</button>
        </section>
        
        <section class="questionnaire-section">
            <h2>Mes questionnaires</h2>
            <p class="section-subtitle">Gérez vos propres questionnaires</p>
            <div class="form-group">
                <input type="search" v-model="searchMine" placeholder="Rechercher dans mes questionnaires">
            </div>
            
            <div class="questionnaire-list">
                <p v-if="myQuestionnaires.length === 0" class="empty-message">Vous n'avez pas encore créé de questionnaire</p>
                <p v-else-if="filteredMyQuestionnaires.length === 0" class="empty-message">Aucun résultat pour cette recherche</p>
                <div
                    v-for="questionnaire in filteredMyQuestionnaires"
                    :key="questionnaire.id"
                    class="questionnaire-card my-card"
                    :class="{ selected: selectedMyId === questionnaire.id }"
                    @click="selectMyQuestionnaire(questionnaire.id)"
                >
                    <h3>{{ questionnaire.nom }}</h3>
                    <div class="questionnaire-meta">
                        <span class="theme">{{ questionnaire.theme }}</span>
                        <span class="count">{{ questionnaire.nombreQuestions }} questions</span>
                        <span class="count">{{ questionnaire.estPublie ? 'Publié' : 'Brouillon' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="questionnaire_edit.php" class="btn btn-primary btn-full">Nouveau questionnaire</a>
                <div class="btn-row">
                    <button class="btn" :disabled="!selectedMyId" @click="editSelectedQuestionnaire">Editer</button>
                    <button class="btn btn-success" :disabled="!selectedMyId" @click="publishSelectedQuestionnaire">Publier</button>
                </div>
                <div class="btn-row">
                    <button class="btn btn-warn" :disabled="!selectedMyId" @click="exportSelectedQuestionnaire">Exporter PDF</button>
                    <button class="btn btn-danger" :disabled="!selectedMyId" @click="deleteSelectedQuestionnaire">Supprimer</button>
                </div>
            </div>
        </section>
    </div>
</div>
<script>
window.__QUESTIONNAIRES_PAGE__ = <?= json_encode($questionnairePageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php include 'footer.php'; ?>
