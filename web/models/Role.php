<?php
require_once __DIR__ . '/../config/database.php';

class Role {
    public static function ensureSchema() {
        $pdo = Database::getConnection();

        $pdo->exec("CREATE TABLE IF NOT EXISTS Role (
            id INT NOT NULL AUTO_INCREMENT,
            nom VARCHAR(100) NOT NULL,
            `desc` VARCHAR(255) NOT NULL,
            niveau INT NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_role_nom (nom),
            INDEX idx_role_niveau (niveau)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        self::ensureColumn($pdo, 'Utilisateur', 'idrole', 'INT NULL');

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = 'Utilisateur' AND constraint_name = 'fk_utilisateur_role'");
        $stmt->execute();
        $constraintExists = (int)$stmt->fetchColumn() > 0;

        if (!$constraintExists) {
            try {
                $pdo->exec("ALTER TABLE Utilisateur
                    ADD CONSTRAINT fk_utilisateur_role
                    FOREIGN KEY (idrole)
                    REFERENCES Role(id)
                    ON DELETE SET NULL
                    ON UPDATE CASCADE");
            } catch (PDOException $e) {
                // If legacy data or schema state blocks the FK, keep the app working.
            }
        }

        $stmt = $pdo->query("SELECT COUNT(*) FROM Role");
        if ((int)$stmt->fetchColumn() === 0) {
            $roles = [
                [1, 'Administratif', 'Personnel des services administratifs', 1],
                [2, 'Technicien', 'Personnel technique', 2],
                [3, 'Support', 'Personnel des services support ICT', 3],
                [4, 'Gestion', 'Personnel comptable et financier, management', 3],
                [5, 'Direction', 'Directeur de departements', 4],
            ];

            $insert = $pdo->prepare("INSERT INTO Role (id, nom, `desc`, niveau) VALUES (?, ?, ?, ?)");
            foreach ($roles as $role) {
                $insert->execute($role);
            }
        }
    }

    private static function ensureColumn(PDO $pdo, $tableName, $columnName, $definition) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
        $stmt->execute([$tableName, $columnName]);

        if ((int)$stmt->fetchColumn() === 0) {
            $pdo->exec("ALTER TABLE {$tableName} ADD COLUMN {$columnName} {$definition}");
        }
    }

    public static function getAll() {
        self::ensureSchema();
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT id, nom, `desc`, niveau FROM Role ORDER BY niveau ASC, nom ASC");

        $roles = [];
        while ($row = $stmt->fetch()) {
            $roles[] = [
                'id' => $row['id'],
                'nom' => $row['nom'],
                'desc' => $row['desc'],
                'niveau' => $row['niveau']
            ];
        }

        return $roles;
    }

    public static function exists($id) {
        self::ensureSchema();
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Role WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    public static function getLevelById($id) {
        self::ensureSchema();
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT niveau FROM Role WHERE id = ?");
        $stmt->execute([$id]);
        $level = $stmt->fetchColumn();

        return $level !== false ? (int)$level : 0;
    }
}