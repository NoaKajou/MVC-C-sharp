<?php
$title = 'Stats';
include 'header.php';
?>

<?php
$questionnairesByTheme = $stats['questionnairesByTheme'] ?? [];
$weeklyConnections = $stats['weeklyConnections'] ?? [];
$successByTheme = $stats['successByTheme'] ?? [];
?>

<div class="main-layout">
    <header class="top-bar">
        <h1>QUESTIONNAIRE - Stats</h1>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['user_pseudo']) ?></span>
            <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
        </div>
    </header>

    <div class="content-wrapper">
        <aside class="sidebar">
            <a href="questionnaires.php" class="btn btn-sidebar">Questionnaires</a>
            <a href="profil.php" class="btn btn-sidebar">Mon profil</a>
            <a href="stats.php" class="btn btn-sidebar">Stats</a>
        </aside>

        <main class="main-content">
            <h2>Statistiques</h2>
            <p>Voici les indicateurs demandés pour le responsable pédagogique.</p>

            <section class="stats-grid">
                <article class="stats-card">
                    <h3>Répartition des questionnaires par thème</h3>
                    <p class="stats-subtitle">Nombre de questionnaires pour chaque thème.</p>
                    <div class="chart-wrap">
                        <canvas id="chartQuestionnairesByTheme"></canvas>
                    </div>
                </article>

                <article class="stats-card">
                    <h3>Connexions utilisateurs sur la semaine</h3>
                    <p class="stats-subtitle">Nombre d'utilisateurs distincts actifs par jour (semaine en cours).</p>
                    <div class="chart-wrap">
                        <canvas id="chartWeeklyConnections"></canvas>
                    </div>
                </article>

                <article class="stats-card stats-card-full">
                    <h3>Taux de succès par thème</h3>
                    <p class="stats-subtitle">Pourcentage de réponses correctes par thème.</p>
                    <div class="chart-wrap">
                        <canvas id="chartSuccessByTheme"></canvas>
                    </div>
                </article>
            </section>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const questionnairesByTheme = <?= json_encode($questionnairesByTheme, JSON_UNESCAPED_UNICODE) ?>;
const weeklyConnections = <?= json_encode($weeklyConnections, JSON_UNESCAPED_UNICODE) ?>;
const successByTheme = <?= json_encode($successByTheme, JSON_UNESCAPED_UNICODE) ?>;
const successByThemeFallback = successByTheme.length > 0
    ? successByTheme
    : questionnairesByTheme.map(item => ({ theme: item.theme, taux: 0 }));

function getPalette(size) {
    const palette = [
        '#0e8b7d', '#1f9d55', '#2f80ed', '#f59e0b', '#ef4444',
        '#14b8a6', '#f97316', '#22c55e', '#8b5cf6', '#0ea5e9'
    ];
    const colors = [];
    for (let i = 0; i < size; i++) {
        colors.push(palette[i % palette.length]);
    }
    return colors;
}

function renderQuestionnairesByTheme() {
    const ctx = document.getElementById('chartQuestionnairesByTheme');
    const labels = questionnairesByTheme.map(item => item.theme);
    const values = questionnairesByTheme.map(item => item.total);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: getPalette(values.length),
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function renderWeeklyConnections() {
    const ctx = document.getElementById('chartWeeklyConnections');
    const labels = weeklyConnections.map(item => item.jour);
    const values = weeklyConnections.map(item => item.total);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Utilisateurs actifs',
                data: values,
                backgroundColor: '#2f80ed'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

function renderSuccessByTheme() {
    const ctx = document.getElementById('chartSuccessByTheme');
    const labels = successByThemeFallback.map(item => item.theme);
    const values = successByThemeFallback.map(item => item.taux);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Taux de succès (%)',
                data: values,
                backgroundColor: '#1f9d55'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        callback: (value) => value + '%'
                    }
                }
            }
        }
    });
}

if (questionnairesByTheme.length > 0) {
    renderQuestionnairesByTheme();
}

if (weeklyConnections.length > 0) {
    renderWeeklyConnections();
}

if (successByThemeFallback.length > 0) {
    renderSuccessByTheme();
}
</script>

<?php include 'footer.php'; ?>
