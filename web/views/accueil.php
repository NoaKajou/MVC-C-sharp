<?php
$title = 'Accueil';
include 'header.php';
?>

<div class="main-layout">
    <header class="top-bar">
        <h1>QUESTIONNAIRE - Accueil</h1>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['user_pseudo']) ?></span>
            <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
        </div>
    </header>
    
    <div class="content-wrapper">
        <aside class="sidebar">
            <a href="questionnaires.php" class="btn btn-sidebar">📋 Questionnaires</a>
            <a href="profil.php" class="btn btn-sidebar">👤 Mon profil</a>
        </aside>
        
        <main class="main-content">
            <h2>Bienvenue, <?= htmlspecialchars($_SESSION['user_pseudo']) ?> !</h2>
            <p>Sélectionnez une option dans le menu de gauche pour commencer.</p>
        </main>
    </div>
</div>

<?php include 'footer.php'; ?>
