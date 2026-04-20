<?php
$title = 'Jouer - ' . $questionnaire->nom;
include 'header.php';
?>

<div class="page-container">
    <header class="top-bar">
        <h1><?= htmlspecialchars($questionnaire->nom) ?></h1>
        <span class="theme-badge"><?= htmlspecialchars($questionnaire->theme) ?></span>
    </header>
    
    <div class="play-container">
        <div id="questionContainer">
            <p class="progression">Question <span id="currentNum">1</span>/<span id="totalNum"><?= count($questions) ?></span></p>
            
            <div class="question-box">
                <h2 id="questionText"></h2>
                
                <div id="reponsesArea"></div>
            </div>
            
            <div class="play-actions">
                <button id="btnQuitter" class="btn">Quitter</button>
                <button id="btnPrecedent" class="btn" disabled>Précédent</button>
                <button id="btnSignaler" class="btn btn-warn">&#9873; Signaler</button>
                <button id="btnSuivant" class="btn btn-primary">Suivant</button>
            </div>
        </div>
        
        <div id="resultContainer" style="display: none;">
            <div class="result-box">
                <h2 id="resultTitle"></h2>
                <p id="resultScore"></p>
                <div class="result-chart">
                    <canvas id="resultChart" aria-label="Diagramme des bonnes et mauvaises réponses" role="img"></canvas>
                </div>
                <a href="questionnaires.php" class="btn btn-primary">Retour aux questionnaires</a>
            </div>
        </div>
    </div>
</div>

<!-- Modale signalement -->
<div id="modalSignalement" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h3>Signaler un problème</h3>
        <p class="modal-subtitle" id="signalQuestionLabel"></p>
        <div class="form-group">
            <label for="signalDescription">Décrivez le problème :</label>
            <textarea id="signalDescription" rows="4" placeholder="Ex : la bonne réponse semble incorrecte..."></textarea>
        </div>
        <div id="signalFeedback" class="signal-feedback" style="display:none;"></div>
        <div class="modal-actions">
            <button id="btnSignalAnnuler" class="btn">Annuler</button>
            <button id="btnSignalEnvoyer" class="btn btn-warn">Envoyer</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const questions = <?= json_encode(array_map(function($q) {
    return [
        'id' => $q->id,
        'libelle' => $q->libelle,
        'typeReponse' => $q->typeReponse,
        'reponseVraiFaux' => $q->reponseVraiFaux
    ];
}, $questions)) ?>;

const reponses = <?= json_encode($allReponses) ?>;
const questionnaireId = <?= (int)$questionnaire->id ?>;

let currentIndex = 0;
let answers = {};
let score = 0;
let resultChart = null;

function displayQuestion() {
    const q = questions[currentIndex];
    document.getElementById('currentNum').textContent = currentIndex + 1;
    document.getElementById('questionText').textContent = q.libelle;
    
    const area = document.getElementById('reponsesArea');
    area.innerHTML = '';
    
    if (q.typeReponse === 'VraiFaux') {
        area.innerHTML = `
            <label class="reponse-option">
                <input type="radio" name="answer" value="true" ${answers[q.id] === 'true' ? 'checked' : ''}>
                Vrai
            </label>
            <label class="reponse-option">
                <input type="radio" name="answer" value="false" ${answers[q.id] === 'false' ? 'checked' : ''}>
                Faux
            </label>
        `;
    } else {
        const qReponses = reponses[q.id] || [];
        qReponses.forEach(r => {
            area.innerHTML += `
                <label class="reponse-option">
                    <input type="radio" name="answer" value="${r.id}" ${answers[q.id] == r.id ? 'checked' : ''}>
                    ${r.valeur}
                </label>
            `;
        });
    }
    
    document.getElementById('btnPrecedent').disabled = currentIndex === 0;
    document.getElementById('btnSuivant').textContent = currentIndex === questions.length - 1 ? 'Terminer' : 'Suivant';
}

function saveAnswer() {
    const selected = document.querySelector('input[name="answer"]:checked');
    if (selected) {
        answers[questions[currentIndex].id] = selected.value;
    }
}

function isAnswerCorrect(question, answer) {
    if (!question || answer === undefined || answer === null) {
        return false;
    }

    if (question.typeReponse === 'VraiFaux') {
        const expected = question.reponseVraiFaux ? 'true' : 'false';
        return answer === expected;
    }

    const qReponses = reponses[question.id] || [];
    const matchedResponse = qReponses.find(r => String(r.id) === String(answer));
    return Boolean(matchedResponse && Number(matchedResponse.estCorrecte) === 1);
}

function persistAnswer(question) {
    const answer = answers[question.id];
    if (!answer) {
        return Promise.resolve();
    }

    return fetch('api/check_answer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ questionId: question.id, questionnaireId, answer })
    }).catch(() => null);
}

async function calculateScore() {
    score = 0;
    const saveTasks = [];

    for (const q of questions) {
        const answer = answers[q.id];
        if (answer) {
            if (isAnswerCorrect(q, answer)) {
                score++;
            }
            saveTasks.push(persistAnswer(q));
        }
    }

    await Promise.allSettled(saveTasks);
    
    showResult();
}

function showResult() {
    document.getElementById('questionContainer').style.display = 'none';
    document.getElementById('resultContainer').style.display = 'block';
    
    const percentage = Math.round((score / questions.length) * 100);
    const wrongAnswers = questions.length - score;
    document.getElementById('resultTitle').textContent = percentage >= 50 ? 'Bravo !' : 'Dommage...';
    document.getElementById('resultScore').textContent = `Vous avez obtenu ${score}/${questions.length} (${percentage}%)`;
    
    document.querySelector('.result-box').className = 'result-box ' + (percentage >= 50 ? 'success' : 'fail');

    const chartCanvas = document.getElementById('resultChart');
    if (resultChart) {
        resultChart.destroy();
    }

    resultChart = new Chart(chartCanvas, {
        type: 'pie',
        data: {
            labels: ['Bonnes réponses', 'Mauvaises réponses'],
            datasets: [{
                data: [score, wrongAnswers],
                backgroundColor: ['#1f9d55', '#d64545'],
                borderColor: ['#ffffff', '#ffffff'],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

document.getElementById('btnSuivant').addEventListener('click', () => {
    saveAnswer();
    
    if (currentIndex < questions.length - 1) {
        currentIndex++;
        displayQuestion();
    } else {
        calculateScore();
    }
});

document.getElementById('btnPrecedent').addEventListener('click', () => {
    saveAnswer();
    if (currentIndex > 0) {
        currentIndex--;
        displayQuestion();
    }
});

document.getElementById('btnQuitter').addEventListener('click', () => {
    if (confirm('Êtes-vous sûr de vouloir quitter ?')) {
        window.location.href = 'questionnaires.php';
    }
});

// --- Signalement ---
document.getElementById('btnSignaler').addEventListener('click', () => {
    const q = questions[currentIndex];
    document.getElementById('signalQuestionLabel').textContent = '« ' + q.libelle + ' »';
    document.getElementById('signalDescription').value = '';
    document.getElementById('signalFeedback').style.display = 'none';
    document.getElementById('btnSignalEnvoyer').disabled = false;
    document.getElementById('modalSignalement').style.display = 'flex';
});

document.getElementById('btnSignalAnnuler').addEventListener('click', () => {
    document.getElementById('modalSignalement').style.display = 'none';
});

document.getElementById('modalSignalement').addEventListener('click', (e) => {
    if (e.target === document.getElementById('modalSignalement')) {
        document.getElementById('modalSignalement').style.display = 'none';
    }
});

document.getElementById('btnSignalEnvoyer').addEventListener('click', async () => {
    const description = document.getElementById('signalDescription').value.trim();
    if (!description) {
        showSignalFeedback('Veuillez décrire le problème.', false);
        return;
    }
    document.getElementById('btnSignalEnvoyer').disabled = true;

    try {
        const res = await fetch('api/report_question.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ questionId: questions[currentIndex].id, description })
        });
        const data = await res.json();
        showSignalFeedback(data.message, data.success === true);
        if (data.success) {
            setTimeout(() => {
                document.getElementById('modalSignalement').style.display = 'none';
            }, 1800);
        } else {
            document.getElementById('btnSignalEnvoyer').disabled = false;
        }
    } catch {
        showSignalFeedback('Erreur réseau, veuillez réessayer.', false);
        document.getElementById('btnSignalEnvoyer').disabled = false;
    }
});

function showSignalFeedback(msg, success) {
    const el = document.getElementById('signalFeedback');
    el.textContent = msg;
    el.className = 'signal-feedback ' + (success ? 'signal-success' : 'signal-error');
    el.style.display = 'block';
}

if (questions.length > 0) {
    displayQuestion();
} else {
    document.getElementById('questionContainer').innerHTML = '<p>Ce questionnaire ne contient aucune question.</p><a href="questionnaires.php" class="btn">Retour</a>';
}
</script>

<?php include 'footer.php'; ?>
