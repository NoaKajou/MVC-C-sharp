<?php
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/QuestionnaireController.php';

AuthController::requireLogin();

$questionnaireId = $_GET['id'] ?? null;
if (!$questionnaireId) {
    header('Location: questionnaires.php?error=' . urlencode('Questionnaire introuvable'));
    exit;
}

$questionnaire = QuestionnaireController::getById($questionnaireId);
if (!$questionnaire || $questionnaire->utilisateurId != $_SESSION['user_id']) {
    header('Location: questionnaires.php?error=' . urlencode('Accès non autorisé'));
    exit;
}

$questions = QuestionnaireController::getQuestions($questionnaire->id);
$reponsesByQuestion = [];
foreach ($questions as $question) {
    $reponsesByQuestion[$question->id] = QuestionnaireController::getReponses($question->id);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export PDF - <?= htmlspecialchars($questionnaire->nom) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1f2d3d;
            margin: 24px;
            line-height: 1.5;
        }

        .screen-actions {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            border: 1px solid #d0d8e2;
            border-radius: 8px;
            padding: 8px 12px;
            text-decoration: none;
            color: #1f2d3d;
            cursor: pointer;
            background: #f5f7fa;
            font-weight: 600;
        }

        .btn-print {
            background: #0e8b7d;
            color: #ffffff;
            border-color: #0e8b7d;
        }

        h1 {
            margin-bottom: 6px;
        }

        .meta {
            margin-bottom: 20px;
            color: #556274;
        }

        .question-block {
            border: 1px solid #d8e2ec;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 14px;
        }

        .question-title {
            font-weight: 700;
            margin-bottom: 8px;
        }

        ul {
            margin-left: 20px;
        }

        .correct {
            color: #1f7d48;
            font-weight: 700;
        }

        .bool-answer {
            font-style: italic;
            color: #0b5f80;
        }

        @media print {
            .screen-actions {
                display: none;
            }

            body {
                margin: 0;
            }

            .question-block {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="screen-actions">
        <a href="questionnaires.php" class="btn">Retour</a>
        <button type="button" class="btn btn-print" onclick="window.print()">Générer le PDF</button>
    </div>

    <h1><?= htmlspecialchars($questionnaire->nom) ?></h1>
    <p class="meta">
        Thème : <?= htmlspecialchars($questionnaire->theme) ?>
        | Questions : <?= count($questions) ?>
        | Statut : <?= $questionnaire->estPublie ? 'Publié' : 'Brouillon' ?>
    </p>

    <?php if (empty($questions)): ?>
        <p>Aucune question à exporter.</p>
    <?php else: ?>
        <?php foreach ($questions as $question): ?>
            <div class="question-block">
                <div class="question-title">Question <?= (int)$question->numero ?> : <?= htmlspecialchars($question->libelle) ?></div>

                <?php if ($question->typeReponse === 'VraiFaux'): ?>
                    <p class="bool-answer">
                        Réponse attendue : <?= $question->reponseVraiFaux ? 'Vrai' : 'Faux' ?>
                    </p>
                <?php else: ?>
                    <?php $reponses = $reponsesByQuestion[$question->id] ?? []; ?>
                    <?php if (!empty($reponses)): ?>
                        <ul>
                            <?php foreach ($reponses as $reponse): ?>
                                <li>
                                    <?= htmlspecialchars($reponse->valeur) ?>
                                    <?= $reponse->estCorrecte ? '<span class="correct">(Correcte)</span>' : '' ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Aucune réponse configurée.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
