<?php
require_once __DIR__ . '/../models/Questionnaire.php';
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/Reponse.php';

class QuestionnaireController {
    
    public static function getAll() {
        return Questionnaire::getAll();
    }

    public static function getMine($utilisateurId) {
        return Questionnaire::getAllByUtilisateur($utilisateurId);
    }

    public static function getPlayHistory($utilisateurId, $limit = 20) {
        return Questionnaire::getPlayHistoryByUtilisateur($utilisateurId, $limit);
    }

    public static function getById($id) {
        return Questionnaire::getById($id);
    }

    public static function create($nom, $theme, $utilisateurId) {
        if (empty($nom)) {
            return ['success' => false, 'message' => 'Le nom est obligatoire'];
        }
        if (empty($theme)) {
            return ['success' => false, 'message' => 'Le thème est obligatoire'];
        }

        $id = Questionnaire::create($nom, $theme, $utilisateurId);
        if ($id) {
            return ['success' => true, 'message' => 'Questionnaire créé', 'id' => $id];
        }
        return ['success' => false, 'message' => 'Erreur lors de la création'];
    }

    public static function update($id, $nom, $theme, $utilisateurId) {
        $questionnaire = Questionnaire::getById($id);
        if (!$questionnaire || $questionnaire->utilisateurId != $utilisateurId) {
            return ['success' => false, 'message' => 'Questionnaire non trouvé ou non autorisé'];
        }

        if (empty($nom)) {
            return ['success' => false, 'message' => 'Le nom est obligatoire'];
        }

        if (Questionnaire::update($id, $nom, $theme)) {
            return ['success' => true, 'message' => 'Questionnaire mis à jour'];
        }
        return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
    }

    public static function delete($id, $utilisateurId) {
        $questionnaire = Questionnaire::getById($id);
        if (!$questionnaire || $questionnaire->utilisateurId != $utilisateurId) {
            return ['success' => false, 'message' => 'Questionnaire non trouvé ou non autorisé'];
        }

        if (Questionnaire::delete($id)) {
            return ['success' => true, 'message' => 'Questionnaire supprimé'];
        }
        return ['success' => false, 'message' => 'Erreur lors de la suppression'];
    }

    public static function getQuestions($questionnaireId) {
        return Question::getAllByQuestionnaire($questionnaireId);
    }

    public static function getQuestion($id) {
        return Question::getById($id);
    }

    public static function addQuestion($questionnaireId, $libelle, $typeReponse, $reponseVraiFaux = null, $reponses = []) {
        if (empty($libelle)) {
            return ['success' => false, 'message' => 'Le libellé est obligatoire'];
        }

        $numero = Question::getNextNumero($questionnaireId);
        $questionId = Question::create($questionnaireId, $numero, $libelle, $typeReponse, $reponseVraiFaux);

        if ($questionId && $typeReponse === 'ListeValeurs') {
            foreach ($reponses as $reponse) {
                Reponse::create($questionId, $reponse['valeur'], $reponse['estCorrecte']);
            }
        }

        if ($questionId) {
            return ['success' => true, 'message' => 'Question ajoutée', 'id' => $questionId];
        }
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout'];
    }

    public static function updateQuestion($id, $libelle, $typeReponse, $reponseVraiFaux = null, $reponses = []) {
        if (empty($libelle)) {
            return ['success' => false, 'message' => 'Le libellé est obligatoire'];
        }

        if (Question::update($id, $libelle, $typeReponse, $reponseVraiFaux)) {
            Reponse::deleteAllByQuestion($id);
            
            if ($typeReponse === 'ListeValeurs') {
                foreach ($reponses as $reponse) {
                    Reponse::create($id, $reponse['valeur'], $reponse['estCorrecte']);
                }
            }
            
            return ['success' => true, 'message' => 'Question mise à jour'];
        }
        return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
    }

    public static function deleteQuestion($id) {
        if (Question::delete($id)) {
            return ['success' => true, 'message' => 'Question supprimée'];
        }
        return ['success' => false, 'message' => 'Erreur lors de la suppression'];
    }

    public static function getReponses($questionId) {
        return Reponse::getAllByQuestion($questionId);
    }

    public static function checkAnswer($questionId, $answer) {
        $question = Question::getById($questionId);
        if (!$question) {
            return ['correct' => false];
        }

        if ($question->typeReponse === 'VraiFaux') {
            $correct = ($answer === 'true' && $question->reponseVraiFaux) || 
                       ($answer === 'false' && !$question->reponseVraiFaux);
            return ['correct' => $correct];
        } else {
            $reponses = Reponse::getAllByQuestion($questionId);
            foreach ($reponses as $reponse) {
                if ($reponse->id == $answer && $reponse->estCorrecte) {
                    return ['correct' => true];
                }
            }
            return ['correct' => false];
        }
    }

    public static function checkAndStoreAnswer($utilisateurId, $questionnaireId, $questionId, $answer) {
        $question = Question::getById($questionId);
        if (!$question) {
            return ['correct' => false];
        }

        $result = self::checkAnswer($questionId, $answer);
        $isCorrect = $result['correct'] === true;

        $reponseBool = null;
        if ($question->typeReponse === 'VraiFaux') {
            $reponseBool = $answer === 'true' ? 1 : 0;
        }

        Questionnaire::saveUserAnswer(
            $utilisateurId,
            $questionnaireId,
            $questionId,
            (string)$answer,
            $reponseBool,
            $isCorrect
        );

        return ['correct' => $isCorrect];
    }

    public static function getPedagogicalStats() {
        return [
            'questionnairesByTheme' => Questionnaire::getQuestionnaireCountByTheme(),
            'weeklyConnections' => Questionnaire::getWeeklyUserConnections(),
            'successByTheme' => Questionnaire::getSuccessRateByTheme()
        ];
    }

    public static function trackQuestionnaireAccess($utilisateurId, $questionnaireId) {
        return Questionnaire::trackQuestionnaireAccess($utilisateurId, $questionnaireId);
    }

    public static function publish($id, $utilisateurId) {
        $questionnaire = Questionnaire::getById($id);
        if (!$questionnaire || $questionnaire->utilisateurId != $utilisateurId) {
            return ['success' => false, 'message' => 'Questionnaire non trouvé ou non autorisé'];
        }

        if ($questionnaire->estPublie) {
            return ['success' => false, 'message' => 'Ce questionnaire est déjà publié'];
        }

        if ($questionnaire->nombreQuestions < 1) {
            return ['success' => false, 'message' => 'Ajoutez au moins une question avant de publier'];
        }

        if (Questionnaire::publish($id, $utilisateurId)) {
            return ['success' => true, 'message' => 'Questionnaire publié avec succès'];
        }

        return ['success' => false, 'message' => 'Erreur lors de la publication'];
    }
}
