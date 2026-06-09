<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Questionnaire' ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="db-status-bar">
        <span class="db-status-label">Base en ligne :</span>
        <span id="dbStatusBadge" class="db-status-badge db-status-loading">Vérification...</span>
    </div>
    <div class="container">
