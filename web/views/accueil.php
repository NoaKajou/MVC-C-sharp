<?php
$title = 'Accueil';
$accueilPageData = [
    'userPseudo' => $_SESSION['user_pseudo'] ?? '',
];
include 'header.php';
?>

<div class="main-layout" id="accueilApp" v-cloak>
    <header class="top-bar">
        <h1>QUESTIONNAIRE - Accueil</h1>
        <div class="user-info">
            <span>{{ userPseudo }}</span>
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
            <h2>Bienvenue, {{ userPseudo }} !</h2>
            <p>Sélectionnez une option dans le menu de gauche pour commencer.</p>
        </main>
    </div>
</div>

<script>
window.__ACCUEIL_PAGE__ = <?= json_encode($accueilPageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php include 'footer.php'; ?>
