<?php
require_once __DIR__ . '/../config/database.php';

class Questionnaire {
    public $id;
    public $nom;
    public $theme;
    public $utilisateurId;
    public $nombreQuestions;
    public $estPublie;
    public $datePublication;

    public function __construct($id = null, $nom = '', $theme = '', $utilisateurId = null, $nombreQuestions = 0, $estPublie = false, $datePublication = null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->theme = $theme;
        $this->utilisateurId = $utilisateurId;
        $this->nombreQuestions = $nombreQuestions;
        $this->estPublie = (bool)$estPublie;
        $this->datePublication = $datePublication;
    }

    public static function getAll() {
        $pdo = Database::getConnection();
        self::ensurePublicationColumns();

        $stmt = $pdo->query("SELECT q.id, q.nom, q.theme, q.utilisateur_id,
                            q.est_publie, q.date_publication,
                            (SELECT COUNT(*) FROM Question WHERE questionnaire_id = q.id) as nb_questions
                            FROM Questionnaire q
                            WHERE q.est_publie = 1");
        
        $questionnaires = [];
        while ($row = $stmt->fetch()) {
            $questionnaires[] = new Questionnaire(
                $row['id'],
                $row['nom'],
                $row['theme'],
                $row['utilisateur_id'],
                $row['nb_questions'],
                $row['est_publie'],
                $row['date_publication']
            );
        }
        return $questionnaires;
    }

    public static function getAllByUtilisateur($utilisateurId) {
        $pdo = Database::getConnection();
        self::ensurePublicationColumns();

        $stmt = $pdo->prepare("SELECT q.id, q.nom, q.theme, q.utilisateur_id,
                              q.est_publie, q.date_publication,
                              (SELECT COUNT(*) FROM Question WHERE questionnaire_id = q.id) as nb_questions
                              FROM Questionnaire q
                              WHERE q.utilisateur_id = ?
                              ORDER BY q.id DESC");
        $stmt->execute([$utilisateurId]);
        
        $questionnaires = [];
        while ($row = $stmt->fetch()) {
            $questionnaires[] = new Questionnaire(
                $row['id'],
                $row['nom'],
                $row['theme'],
                $row['utilisateur_id'],
                $row['nb_questions'],
                $row['est_publie'],
                $row['date_publication']
            );
        }
        return $questionnaires;
    }

    public static function getById($id) {
        $pdo = Database::getConnection();
        self::ensurePublicationColumns();

        $stmt = $pdo->prepare("SELECT q.id, q.nom, q.theme, q.utilisateur_id,
                              q.est_publie, q.date_publication,
                              (SELECT COUNT(*) FROM Question WHERE questionnaire_id = q.id) as nb_questions
                              FROM Questionnaire q WHERE q.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row) {
            return new Questionnaire(
                $row['id'],
                $row['nom'],
                $row['theme'],
                $row['utilisateur_id'],
                $row['nb_questions'],
                $row['est_publie'],
                $row['date_publication']
            );
        }
        return null;
    }

    public static function create($nom, $theme, $utilisateurId) {
        $pdo = Database::getConnection();
        self::ensurePublicationColumns();

        $stmt = $pdo->prepare("INSERT INTO Questionnaire (nom, theme, utilisateur_id, est_publie, date_publication) VALUES (?, ?, ?, 0, NULL)");
        $stmt->execute([$nom, $theme, $utilisateurId]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $nom, $theme) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE Questionnaire SET nom = ?, theme = ? WHERE id = ?");
        return $stmt->execute([$nom, $theme, $id]);
    }

    public static function delete($id) {
        $pdo = Database::getConnection();
        
        $stmt = $pdo->prepare("DELETE r FROM Reponse r 
                              INNER JOIN Question q ON r.question_id = q.id 
                              WHERE q.questionnaire_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $pdo->prepare("DELETE FROM Question WHERE questionnaire_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $pdo->prepare("DELETE FROM Questionnaire WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getQuestionnaireCountByTheme() {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT theme, COUNT(*) AS total
                             FROM Questionnaire
                             GROUP BY theme
                             ORDER BY total DESC, theme ASC");

        $data = [];
        while ($row = $stmt->fetch()) {
            $data[] = [
                'theme' => $row['theme'],
                'total' => (int)$row['total']
            ];
        }
        return $data;
    }

    public static function getWeeklyUserConnections() {
        $pdo = Database::getConnection();
        self::ensureAccessLogTable();
        $stmt = $pdo->query("SELECT WEEKDAY(date_connexion) AS weekday_index,
                         COUNT(DISTINCT CONCAT(utilisateur_id, '-', DATE(date_connexion))) AS users_count
                     FROM QuestionnaireConnexion
                     GROUP BY WEEKDAY(date_connexion)");

        $weekdayLabels = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $countsByDay = array_fill(0, 7, 0);

        while ($row = $stmt->fetch()) {
            $index = (int)$row['weekday_index'];
            if ($index >= 0 && $index <= 6) {
                $countsByDay[$index] = (int)$row['users_count'];
            }
        }

        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $data[] = [
                'jour' => $weekdayLabels[$i],
                'total' => $countsByDay[$i]
            ];
        }
        return $data;
    }

    public static function getSuccessRateByTheme() {
        $pdo = Database::getConnection();
         $stmt = $pdo->query("SELECT q.theme,
                         COALESCE(ROUND(AVG(CASE WHEN ru.est_correcte = 1 THEN 100 ELSE 0 END), 1), 0) AS success_rate
                     FROM Questionnaire q
                     LEFT JOIN ReponseUtilisateur ru ON ru.questionnaire_id = q.id
                     GROUP BY q.theme
                             ORDER BY success_rate DESC, q.theme ASC");

        $data = [];
        while ($row = $stmt->fetch()) {
            $data[] = [
                'theme' => $row['theme'],
                'taux' => (float)$row['success_rate']
            ];
        }
        return $data;
    }

    public static function saveUserAnswer($utilisateurId, $questionnaireId, $questionId, $reponseTexte, $reponseBool, $estCorrecte) {
        $pdo = Database::getConnection();

        $findStmt = $pdo->prepare("SELECT id
                                  FROM ReponseUtilisateur
                                  WHERE utilisateur_id = ? AND questionnaire_id = ? AND question_id = ?
                                  LIMIT 1");
        $findStmt->execute([$utilisateurId, $questionnaireId, $questionId]);
        $existingId = $findStmt->fetchColumn();

        if ($existingId) {
            $updateStmt = $pdo->prepare("UPDATE ReponseUtilisateur
                                         SET reponse_texte = ?,
                                             reponse_bool = ?,
                                             est_correcte = ?,
                                             date_reponse = NOW()
                                         WHERE id = ?");
            return $updateStmt->execute([$reponseTexte, $reponseBool, $estCorrecte ? 1 : 0, $existingId]);
        }

        $insertStmt = $pdo->prepare("INSERT INTO ReponseUtilisateur
                                    (utilisateur_id, question_id, questionnaire_id, reponse_texte, reponse_bool, est_correcte)
                                    VALUES (?, ?, ?, ?, ?, ?)");
        return $insertStmt->execute([
            $utilisateurId,
            $questionId,
            $questionnaireId,
            $reponseTexte,
            $reponseBool,
            $estCorrecte ? 1 : 0
        ]);
    }

    public static function publish($id, $utilisateurId) {
        $pdo = Database::getConnection();
        self::ensurePublicationColumns();

        $stmt = $pdo->prepare("UPDATE Questionnaire
                               SET est_publie = 1,
                                   date_publication = COALESCE(date_publication, NOW())
                               WHERE id = ?
                                 AND utilisateur_id = ?");
        return $stmt->execute([$id, $utilisateurId]);
    }

    public static function getPlayHistoryByUtilisateur($utilisateurId, $limit = 20) {
        $pdo = Database::getConnection();
        self::ensureAccessLogTable();

        $limit = max(1, (int)$limit);

        $stmt = $pdo->prepare("SELECT q.id AS questionnaire_id,
                                      q.nom AS questionnaire_nom,
                                      q.theme AS questionnaire_theme,
                                      qc.date_connexion,
                                      u.pseudo AS auteur_pseudo
                               FROM QuestionnaireConnexion qc
                               INNER JOIN Questionnaire q ON q.id = qc.questionnaire_id
                               INNER JOIN Utilisateur u ON u.id = q.utilisateur_id
                               WHERE qc.utilisateur_id = ?
                               ORDER BY qc.date_connexion DESC
                               LIMIT " . $limit);
        $stmt->execute([$utilisateurId]);
        return $stmt->fetchAll();
    }

    public static function trackQuestionnaireAccess($utilisateurId, $questionnaireId) {
        $pdo = Database::getConnection();
        self::ensureAccessLogTable();

        $stmt = $pdo->prepare("INSERT INTO QuestionnaireConnexion (utilisateur_id, questionnaire_id) VALUES (?, ?)");
        return $stmt->execute([$utilisateurId, $questionnaireId]);
    }

    private static function ensureAccessLogTable() {
        $pdo = Database::getConnection();
        $pdo->exec("CREATE TABLE IF NOT EXISTS QuestionnaireConnexion (
            id INT NOT NULL AUTO_INCREMENT,
            utilisateur_id INT NOT NULL,
            questionnaire_id INT NOT NULL,
            date_connexion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            INDEX idx_qconn_user (utilisateur_id),
            INDEX idx_qconn_questionnaire (questionnaire_id),
            INDEX idx_qconn_date (date_connexion),
            CONSTRAINT fk_qconn_user FOREIGN KEY (utilisateur_id) REFERENCES Utilisateur(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_qconn_questionnaire FOREIGN KEY (questionnaire_id) REFERENCES Questionnaire(id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    private static function ensurePublicationColumns() {
        $pdo = Database::getConnection();

        if (!self::hasColumn($pdo, 'Questionnaire', 'est_publie')) {
            $pdo->exec("ALTER TABLE Questionnaire ADD COLUMN est_publie TINYINT(1) NOT NULL DEFAULT 0");
        }

        if (!self::hasColumn($pdo, 'Questionnaire', 'date_publication')) {
            $pdo->exec("ALTER TABLE Questionnaire ADD COLUMN date_publication DATETIME NULL DEFAULT NULL");
        }
    }

    private static function hasColumn($pdo, $tableName, $columnName) {
        $stmt = $pdo->prepare("SELECT COUNT(*)
                               FROM information_schema.columns
                               WHERE table_schema = DATABASE()
                                 AND table_name = ?
                                 AND column_name = ?");
        $stmt->execute([$tableName, $columnName]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
