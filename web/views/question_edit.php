<?php
$title = isset($question) ? 'Éditer la question' : 'Nouvelle question';
include 'header.php';
?>

<div class="page-container">
    <header class="top-bar">
        <h1><?= isset($question) ? 'Éditer la question' : 'Nouvelle question' ?></h1>
        <div class="header-actions">
            <a href="questionnaire_edit.php?id=<?= $questionnaireId ?>" class="btn">Retour</a>
        </div>
    </header>
    
    <?php if (isset($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="edit-container">
        <form method="POST" class="question-form" id="questionForm">
            <input type="hidden" name="questionnaire_id" value="<?= $questionnaireId ?>">
            
            <div class="form-group">
                <label>Libellé de la question</label>
                <textarea name="libelle" rows="3" placeholder="Ex: 192.1024.3.3 est-elle une adresse IP valide ?" required><?= htmlspecialchars($question->libelle ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Type de réponse</label>
                <select name="type_reponse" id="typeReponse" required>
                    <option value="VraiFaux" <?= (!isset($question) || $question->typeReponse === 'VraiFaux') ? 'selected' : '' ?>>Vrai/Faux</option>
                    <option value="ListeValeurs" <?= (isset($question) && $question->typeReponse === 'ListeValeurs') ? 'selected' : '' ?>>Liste de valeurs</option>
                </select>
            </div>
            
            <div id="panelVraiFaux" class="form-group">
                <label>Réponse correcte</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="reponse_vrai_faux" value="1" <?= (isset($question) && $question->reponseVraiFaux) ? 'checked' : '' ?>>
                        Vrai
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="reponse_vrai_faux" value="0" <?= (!isset($question) || !$question->reponseVraiFaux) ? 'checked' : '' ?>>
                        Faux
                    </label>
                </div>
            </div>
            
            <div id="panelListeValeurs" class="form-group" style="display: none;">
                <label>Valeurs possibles</label>
                
                <div class="add-reponse-row">
                    <input type="text" id="nouvelleValeur" placeholder="Nouvelle valeur">
                    <label class="checkbox-label">
                        <input type="checkbox" id="estCorrecte">
                        Correcte
                    </label>
                    <button type="button" id="btnAjouterValeur" class="btn">Ajouter</button>
                </div>
                
                <div id="reponsesContainer">
                    <?php if (isset($reponses) && !empty($reponses)): ?>
                        <?php foreach ($reponses as $index => $r): ?>
                            <div class="reponse-item">
                                <input type="hidden" name="reponses[<?= $index ?>][valeur]" value="<?= htmlspecialchars($r->valeur) ?>">
                                <input type="hidden" name="reponses[<?= $index ?>][estCorrecte]" value="<?= $r->estCorrecte ? '1' : '0' ?>">
                                <span class="reponse-valeur"><?= htmlspecialchars($r->valeur) ?></span>
                                <span class="reponse-correcte"><?= $r->estCorrecte ? '✓ Correcte' : '' ?></span>
                                <button type="button" class="btn btn-small btn-danger btn-remove">✕</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
let reponseIndex = <?= isset($reponses) ? count($reponses) : 0 ?>;

const typeReponse = document.getElementById('typeReponse');
const panelVraiFaux = document.getElementById('panelVraiFaux');
const panelListeValeurs = document.getElementById('panelListeValeurs');

function togglePanels() {
    if (typeReponse.value === 'VraiFaux') {
        panelVraiFaux.style.display = 'block';
        panelListeValeurs.style.display = 'none';
    } else {
        panelVraiFaux.style.display = 'none';
        panelListeValeurs.style.display = 'block';
    }
}

typeReponse.addEventListener('change', togglePanels);
togglePanels();

document.getElementById('btnAjouterValeur').addEventListener('click', () => {
    const valeur = document.getElementById('nouvelleValeur').value.trim();
    const estCorrecte = document.getElementById('estCorrecte').checked;
    
    if (valeur) {
        const container = document.getElementById('reponsesContainer');
        const div = document.createElement('div');
        div.className = 'reponse-item';
        div.innerHTML = `
            <input type="hidden" name="reponses[${reponseIndex}][valeur]" value="${valeur}">
            <input type="hidden" name="reponses[${reponseIndex}][estCorrecte]" value="${estCorrecte ? '1' : '0'}">
            <span class="reponse-valeur">${valeur}</span>
            <span class="reponse-correcte">${estCorrecte ? '✓ Correcte' : ''}</span>
            <button type="button" class="btn btn-small btn-danger btn-remove">✕</button>
        `;
        container.appendChild(div);
        
        reponseIndex++;
        document.getElementById('nouvelleValeur').value = '';
        document.getElementById('estCorrecte').checked = false;
    }
});

document.getElementById('reponsesContainer').addEventListener('click', (e) => {
    if (e.target.classList.contains('btn-remove')) {
        e.target.closest('.reponse-item').remove();
    }
});
</script>

<?php include 'footer.php'; ?>
