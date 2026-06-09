<?php
$title = 'Stats';
$statsPageData = [
    'questionnairesByTheme' => $questionnairesByTheme,
    'weeklyConnections' => $weeklyConnections,
    'successByTheme' => $successByTheme,
];
include 'header.php';
?>

<?php
$questionnairesByTheme = $stats['questionnairesByTheme'] ?? [];
$weeklyConnections = $stats['weeklyConnections'] ?? [];
$successByTheme = $stats['successByTheme'] ?? [];
?>

<div class="main-layout" id="statsApp" v-cloak>
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
window.__STATS_PAGE__ = <?= json_encode($statsPageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php include 'footer.php'; ?>
