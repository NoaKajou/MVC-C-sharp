<?php
$title = isset($questionnaire) ? 'Éditer - ' . $questionnaire->nom : 'Nouveau questionnaire';
include 'header.php';
?>

<div class="page-container">
    <header class="top-bar">
        <h1><?= isset($questionnaire) ? 'Éditer le questionnaire' : 'Nouveau questionnaire' ?></h1>
        <div class="header-actions">
            <a href="questionnaires.php" class="btn">Retour</a>
        </div>
    </header>
    
    <?php if (isset($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="edit-container">
        <form method="POST" class="questionnaire-form">
            <div class="form-group">
                <label>Nom du questionnaire</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($questionnaire->nom ?? '') ?>" placeholder="Nom du questionnaire" required>
            </div>
            
            <div class="form-group">
                <label>Thème</label>
                <select name="theme" required>
                    <option value="">Sélectionnez un thème</option>
                    <option value="Développement" <?= (isset($questionnaire) && $questionnaire->theme === 'Développement') ? 'selected' : '' ?>>Développement</option>
                    <option value="Réseau" <?= (isset($questionnaire) && $questionnaire->theme === 'Réseau') ? 'selected' : '' ?>>Réseau</option>
                    <option value="Culture générale" <?= (isset($questionnaire) && $questionnaire->theme === 'Culture générale') ? 'selected' : '' ?>>Culture générale</option>
                </select>
            </div>
            
            <button type="submit" name="save" class="btn btn-primary">Enregistrer</button>
        </form>
        
        <?php if (isset($questionnaire)): ?>
        <div class="questions-section">
            <h2>Questions</h2>
            
            <div class="questions-actions">
                <a href="question_edit.php?questionnaire_id=<?= $questionnaire->id ?>" class="btn btn-primary">Ajouter une question</a>
            </div>
            
            <div class="questions-list">
                <?php if (empty($questions)): ?>
                    <p class="empty-message">Aucune question</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $q): ?>
                                <tr>
                                    <td><?= $q->numero ?></td>
                                    <td><?= htmlspecialchars($q->libelle) ?></td>
                                    <td><?= $q->typeReponse ?></td>
                                    <td>
                                        <a href="question_edit.php?id=<?= $q->id ?>&questionnaire_id=<?= $questionnaire->id ?>" class="btn btn-small">Éditer</a>
                                        <a href="question_delete.php?id=<?= $q->id ?>&questionnaire_id=<?= $questionnaire->id ?>" class="btn btn-small btn-danger" onclick="return confirm('Supprimer cette question ?')">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
