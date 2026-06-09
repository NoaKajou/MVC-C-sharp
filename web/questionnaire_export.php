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

$exportPageData = [
    'questionnaire' => [
        'nom' => $questionnaire->nom,
        'theme' => $questionnaire->theme,
        'estPublie' => $questionnaire->estPublie,
    ],
    'questions' => array_map(static function ($question) {
        return [
            'id' => $question->id,
            'numero' => $question->numero,
            'libelle' => $question->libelle,
            'typeReponse' => $question->typeReponse,
            'reponseVraiFaux' => (bool)$question->reponseVraiFaux,
        ];
    }, $questions),
    'reponsesByQuestion' => array_map(static function ($reponses) {
        return array_map(static function ($reponse) {
            return [
                'valeur' => $reponse->valeur,
                'estCorrecte' => (bool)$reponse->estCorrecte,
            ];
        }, $reponses);
    }, $reponsesByQuestion),
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export PDF - <?= htmlspecialchars($questionnaire->nom) ?></title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <style>
        [v-cloak] {
            display: none;
        }

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
    <div id="exportApp" v-cloak>
        <div class="screen-actions">
            <a href="questionnaires.php" class="btn">Retour</a>
            <button type="button" class="btn btn-print" @click="printPage">Générer le PDF</button>
        </div>

        <h1>{{ questionnaire.nom }}</h1>
        <p class="meta">
            Thème : {{ questionnaire.theme }}
            | Questions : {{ questions.length }}
            | Statut : {{ questionnaire.estPublie ? 'Publié' : 'Brouillon' }}
        </p>

        <p v-if="questions.length === 0">Aucune question à exporter.</p>
        <div v-else>
            <div v-for="question in questions" :key="question.id" class="question-block">
                <div class="question-title">Question {{ question.numero }} : {{ question.libelle }}</div>

                <p v-if="question.typeReponse === 'VraiFaux'" class="bool-answer">
                    Réponse attendue : {{ question.reponseVraiFaux ? 'Vrai' : 'Faux' }}
                </p>
                <ul v-else-if="reponsesByQuestion[question.id] && reponsesByQuestion[question.id].length > 0">
                    <li v-for="(reponse, index) in reponsesByQuestion[question.id]" :key="index">
                        {{ reponse.valeur }}
                        <span v-if="reponse.estCorrecte" class="correct">(Correcte)</span>
                    </li>
                </ul>
                <p v-else>Aucune réponse configurée.</p>
            </div>
        </div>
    </div>

    <script>
    window.__EXPORT_PAGE__ = <?= json_encode($exportPageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const exportApp = Vue.createApp({
        data() {
            return window.__EXPORT_PAGE__;
        },
        methods: {
            printPage() {
                window.print();
            }
        }
    });
    exportApp.mount('#exportApp');
    </script>
</body>
</html>
