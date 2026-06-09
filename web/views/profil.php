<?php
$title = 'Mon profil';
$profilPageData = [
    'user' => [
        'id' => $user->id,
        'pseudo' => $user->pseudo,
        'email' => $user->email,
    ],
    'questionnaires' => array_map(static function ($questionnaire) {
        return [
            'nom' => $questionnaire->nom,
            'theme' => $questionnaire->theme,
            'nombreQuestions' => $questionnaire->nombreQuestions,
        ];
    }, $questionnaires ?? []),
    'history' => array_map(static function ($entry) {
        return $entry;
    }, $history ?? []),
];
include 'header.php';
?>

<div class="page-container" id="profilApp" v-cloak>
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
                {{ user.pseudo ? user.pseudo.charAt(0).toUpperCase() : '' }}
            </div>
            <h2>{{ user.pseudo }}</h2>
        </div>
        
        <div class="profil-info">
            <h3>Informations du compte</h3>
            
            <div class="info-card">
                <span class="label">ID</span>
                <span class="value">{{ user.id }}</span>
            </div>
            
            <div class="info-card">
                <span class="label">Pseudo</span>
                <span class="value">{{ user.pseudo }}</span>
            </div>
            
            <div class="info-card">
                <span class="label">Email</span>
                <span class="value">{{ user.email }}</span>
            </div>
        </div>
        
        <div class="profil-questionnaires">
            <h3>Mes Questionnaires</h3>
            <div class="form-group">
                <input type="search" v-model="searchQuestionnaires" placeholder="Rechercher un questionnaire ou un thème">
            </div>
            
            <p v-if="filteredQuestionnaires.length === 0" class="empty-message">Aucun questionnaire créé</p>
            <div v-else class="questionnaire-list-small">
                <div v-for="questionnaire in filteredQuestionnaires" :key="questionnaire.nom" class="questionnaire-item">
                    <div class="item-info">
                        <strong>{{ questionnaire.nom }}</strong>
                        <span>{{ questionnaire.theme }} - {{ questionnaire.nombreQuestions }} questions</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="profil-questionnaires">
            <h3>Historique des questionnaires joués</h3>
            <div class="form-group">
                <input type="search" v-model="searchHistory" placeholder="Rechercher dans l'historique">
            </div>

            <p v-if="filteredHistory.length === 0" class="empty-message">Aucun historique pour le moment</p>
            <div v-else class="questionnaire-list-small">
                <div v-for="(entry, index) in filteredHistory" :key="index" class="questionnaire-item">
                    <div class="item-info">
                        <strong>{{ entry.questionnaire_nom }}</strong>
                        <span>
                            {{ entry.questionnaire_theme }}
                            - par {{ entry.auteur_pseudo }}
                            - joué le {{ new Date(entry.date_connexion).toLocaleString('fr-FR') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.__PROFIL_PAGE__ = <?= json_encode($profilPageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php include 'footer.php'; ?>
