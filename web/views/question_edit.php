<?php
$title = isset($question) ? 'Éditer la question' : 'Nouvelle question';
$questionEditPageData = [
    'questionnaireId' => (int)$questionnaireId,
    'question' => isset($question) ? [
        'id' => $question->id,
        'libelle' => $question->libelle,
        'typeReponse' => $question->typeReponse,
        'reponseVraiFaux' => (bool)$question->reponseVraiFaux,
    ] : null,
    'reponses' => array_map(static function ($reponse) {
        return [
            'valeur' => $reponse->valeur,
            'estCorrecte' => $reponse->estCorrecte,
        ];
    }, $reponses ?? []),
    'error' => $error ?? null,
];
include 'header.php';
?>

<div class="page-container" id="questionEditApp" v-cloak>
    <header class="top-bar">
        <h1>{{ question ? 'Éditer la question' : 'Nouvelle question' }}</h1>
        <div class="header-actions">
            <a href="questionnaire_edit.php?id=<?= $questionnaireId ?>" class="btn">Retour</a>
        </div>
    </header>
    
    <div v-if="error" class="error-message">{{ error }}</div>
    
    <div class="edit-container">
        <form method="POST" class="question-form" id="questionForm">
            <input type="hidden" name="questionnaire_id" :value="questionnaireId">
            
            <div class="form-group">
                <label>Libellé de la question</label>
                <textarea name="libelle" v-model="libelle" rows="3" placeholder="Ex: 192.1024.3.3 est-elle une adresse IP valide ?" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Type de réponse</label>
                <select name="type_reponse" v-model="typeReponse" required>
                    <option value="VraiFaux">Vrai/Faux</option>
                    <option value="ListeValeurs">Liste de valeurs</option>
                </select>
            </div>
            
            <div v-if="showVraiFauxPanel" class="form-group">
                <label>Réponse correcte</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="reponse_vrai_faux" value="1" v-model="reponseVraiFaux">
                        Vrai
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="reponse_vrai_faux" value="0" v-model="reponseVraiFaux">
                        Faux
                    </label>
                </div>
            </div>
            
            <div v-if="showListePanel" class="form-group">
                <label>Valeurs possibles</label>
                
                <div class="add-reponse-row">
                    <input type="text" v-model="nouvelleValeur" placeholder="Nouvelle valeur">
                    <label class="checkbox-label">
                        <input type="checkbox" v-model="estCorrecte">
                        Correcte
                    </label>
                    <button type="button" class="btn" @click="addResponse">Ajouter</button>
                </div>
                
                <div>
                    <div v-for="(response, index) in responses" :key="index" class="reponse-item">
                        <input type="hidden" :name="`reponses[${index}][valeur]`" :value="response.valeur">
                        <input type="hidden" :name="`reponses[${index}][estCorrecte]`" :value="response.estCorrecte">
                        <span class="reponse-valeur">{{ response.valeur }}</span>
                        <span class="reponse-correcte">{{ response.estCorrecte === '1' ? 'Correcte' : '' }}</span>
                        <button type="button" class="btn btn-small btn-danger btn-remove" @click="removeResponse(index)">X</button>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="save" class="btn btn-primary">Enregistrer</button>
                <a href="questionnaire_edit.php?id=<?= $questionnaireId ?>" class="btn">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
window.__QUESTION_EDIT_PAGE__ = <?= json_encode($questionEditPageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php include 'footer.php'; ?>
