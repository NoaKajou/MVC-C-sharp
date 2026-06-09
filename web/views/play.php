<?php
$title = 'Jouer - ' . $questionnaire->nom;
$playPageData = [
    'questionnaireId' => (int)$questionnaire->id,
    'questionnaire' => [
        'id' => $questionnaire->id,
        'nom' => $questionnaire->nom,
        'theme' => $questionnaire->theme,
    ],
    'questions' => array_map(function($q) {
        return [
            'id' => $q->id,
            'libelle' => $q->libelle,
            'typeReponse' => $q->typeReponse,
            'reponseVraiFaux' => $q->reponseVraiFaux
        ];
    }, $questions),
    'reponses' => $allReponses,
];
include 'header.php';
?>

<div class="page-container" id="playApp" v-cloak>
    <header class="top-bar">
        <h1>{{ questionnaire.nom }}</h1>
        <span class="theme-badge">{{ questionnaire.theme }}</span>
    </header>
    
    <div class="play-container">
        <div v-if="questions.length > 0 && !resultVisible" id="questionContainer">
            <p class="progression">Question <span>{{ currentIndex + 1 }}</span>/<span>{{ totalQuestions }}</span></p>
            
            <div class="question-box" v-if="currentQuestion">
                <h2>{{ currentQuestion.libelle }}</h2>
                
                <div id="reponsesArea">
                    <label v-if="currentQuestion.typeReponse === 'VraiFaux'" class="reponse-option">
                        <input type="radio" :name="'answer-' + currentQuestion.id" value="true" v-model="answers[currentQuestion.id]">
                        Vrai
                    </label>
                    <label v-if="currentQuestion.typeReponse === 'VraiFaux'" class="reponse-option">
                        <input type="radio" :name="'answer-' + currentQuestion.id" value="false" v-model="answers[currentQuestion.id]">
                        Faux
                    </label>

                    <label v-for="response in currentResponses" :key="response.id" class="reponse-option">
                        <input type="radio" :name="'answer-' + currentQuestion.id" :value="String(response.id)" v-model="answers[currentQuestion.id]">
                        {{ response.valeur }}
                    </label>
                </div>
            </div>
            
            <div class="play-actions">
                <button class="btn" @click="quitQuestionnaire">Quitter</button>
                <button class="btn" :disabled="currentIndex === 0" @click="previousQuestion">Précédent</button>
                <button class="btn btn-warn" @click="openSignalModal">&#9873; Signaler</button>
                <button class="btn btn-primary" @click="nextQuestion">{{ isLastQuestion ? 'Terminer' : 'Suivant' }}</button>
            </div>
        </div>

        <div v-else-if="questions.length === 0" id="emptyPlayState" class="result-box fail">
            <h2>Ce questionnaire ne contient aucune question.</h2>
            <a href="questionnaires.php" class="btn btn-primary">Retour</a>
        </div>
        
        <div v-else id="resultContainer">
            <div class="result-box">
                <h2>{{ score / totalQuestions >= 0.5 ? 'Bravo !' : 'Dommage...' }}</h2>
                <p>Vous avez obtenu {{ score }}/{{ totalQuestions }} ({{ totalQuestions ? Math.round((score / totalQuestions) * 100) : 0 }}%)</p>
                <div class="result-chart">
                    <canvas ref="resultChart" aria-label="Diagramme des bonnes et mauvaises réponses" role="img"></canvas>
                </div>
                <a href="questionnaires.php" class="btn btn-primary">Retour aux questionnaires</a>
            </div>
        </div>
    </div>
</div>

<!-- Modale signalement -->
<div v-if="modalVisible" id="modalSignalement" class="modal-overlay">
    <div class="modal-box">
        <h3>Signaler un problème</h3>
        <p class="modal-subtitle">« {{ currentQuestion ? currentQuestion.libelle : '' }} »</p>
        <div class="form-group">
            <label for="signalDescription">Décrivez le problème :</label>
            <textarea id="signalDescription" v-model="signalDescription" rows="4" placeholder="Ex : la bonne réponse semble incorrecte..."></textarea>
        </div>
        <div v-if="signalFeedback" class="signal-feedback" :class="signalFeedbackSuccess ? 'signal-success' : 'signal-error'">{{ signalFeedback }}</div>
        <div class="modal-actions">
            <button class="btn" @click="closeSignalModal">Annuler</button>
            <button class="btn btn-warn" :disabled="signalSending" @click="sendSignal">Envoyer</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
document.getElementById('modalSignalement').addEventListener('click', (e) => {
document.getElementById('btnSignalEnvoyer').addEventListener('click', async () => {
<script>
window.__PLAY_PAGE__ = <?= json_encode($playPageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php include 'footer.php'; ?>
