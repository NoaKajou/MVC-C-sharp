<?php
require_once __DIR__ . '/../config/database.php';

class StatsModel {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getStatsCategories() {
        $sql = "SELECT nom, COUNT(idreponse) AS total_questionnaires FROM Role
                    LEFT JOIN Utilisateur USING(idrole)
                    LEFT JOIN reponseUtilisateur USING(idUtilisateur)
                    GROUP BY nom";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>