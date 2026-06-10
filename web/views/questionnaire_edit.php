<?php
$title = isset($questionnaire) ? 'Éditer - ' . $questionnaire->nom : 'Nouveau questionnaire';
$questionnaireEditPageData = [
    'questionnaire' => isset($questionnaire) ? [
        'id' => $questionnaire->id,
        'nom' => $questionnaire->nom,
        'theme' => $questionnaire->theme,
        'niveau' => $questionnaire->niveau ?? 1,
        'estPublie' => $questionnaire->estPublie,
    ] : null,
    'questions' => array_map(static function ($question) {
        return [
            'id' => $question->id,
            'numero' => $question->numero,
            'libelle' => $question->libelle,
            'typeReponse' => $question->typeReponse,
        ];
    }, $questions ?? []),
    'error' => $error ?? null,
];
include 'header.php';
?>

<div class="page-container" id="questionnaireEditApp" v-cloak>
    <header class="top-bar">
        <h1>{{ isEditing ? 'Éditer le questionnaire' : 'Nouveau questionnaire' }}</h1>
        <div class="header-actions">
            <a href="questionnaires.php" class="btn">Retour</a>
        </div>
    </header>
    
    <div v-if="error" class="error-message">{{ error }}</div>
    
    <div class="edit-container">
        <form method="POST" class="questionnaire-form">
            <div class="form-group">
                <label>Nom du questionnaire</label>
                <input type="text" name="nom" v-model="questionnaire.nom" placeholder="Nom du questionnaire" required>
            </div>
            
            <div class="form-group">
                <label>Thème</label>
                <select name="theme" v-model="questionnaire.theme" required>
                    <option value="">Sélectionnez un thème</option>
                    <option value="Développement">Développement</option>
                    <option value="Réseau">Réseau</option>
                    <option value="Culture générale">Culture générale</option>
                </select>
            </div>

            <div class="form-group">
                <label>Niveau du questionnaire</label>
                <select name="niveau" v-model="questionnaire.niveau" required>
                    <?php foreach (($availableLevels ?? []) as $level): ?>
                        <?php
                            $label = match ($level) {
                                1 => '1 - Administratif',
                                2 => '2 - Technicien',
                                3 => '3 - Support / Gestion',
                                4 => '4 - Direction',
                                default => (string)$level,
                            };
                        ?>
                        <option :value="<?= (int)$level ?>"><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" name="save" class="btn btn-primary">Enregistrer</button>
        </form>

        <div v-if="isEditing" class="form-actions">
            <a v-if="!questionnaire.estPublie" :href="'questionnaire_publish.php?id=' + questionnaire.id" class="btn btn-success">Publier ce questionnaire</a>
            <a :href="'questionnaire_export.php?id=' + questionnaire.id" class="btn btn-warn">Exporter en PDF</a>
        </div>
        
        <div v-if="isEditing" class="questions-section">
            <h2>Questions</h2>
            
            <div class="questions-actions">
                <a :href="'question_edit.php?questionnaire_id=' + questionnaire.id" class="btn btn-primary">Ajouter une question</a>
            </div>
            
            <div class="questions-list">
                <p v-if="questions.length === 0" class="empty-message">Aucune question</p>
                <table v-else class="data-table">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Question</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="question in questions" :key="question.id">
                            <td>{{ question.numero }}</td>
                            <td>{{ question.libelle }}</td>
                            <td>{{ question.typeReponse }}</td>
                            <td>
                                <a :href="'question_edit.php?id=' + question.id + '&questionnaire_id=' + questionnaire.id" class="btn btn-small">Éditer</a>
                                <a :href="'question_delete.php?id=' + question.id + '&questionnaire_id=' + questionnaire.id" class="btn btn-small btn-danger" onclick="return confirm('Supprimer cette question ?')">Supprimer</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
window.__QUESTIONNAIRE_EDIT_PAGE__ = <?= json_encode($questionnaireEditPageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php include 'footer.php'; ?>
