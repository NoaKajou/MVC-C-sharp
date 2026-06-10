<?php
class Database {
    private static $connections = [
        [
            'label' => 'locale',
            'host' => 'localhost',
            'port' => '3306',
            'dbname' => 'Stadium_Questionnaire',
            'username' => 'root',
            'password' => 'password',
        ],
    ];
    private static $pdo = null;
    private static $activeConnection = null;

    private static function buildDsn($connection) {
        return "mysql:host=" . $connection['host'] . ";port=" . $connection['port'] . ";dbname=" . $connection['dbname'] . ";charset=utf8mb4";
    }

    private static function tryConnection($connection) {
        $pdo = new PDO(
            self::buildDsn($connection),
            $connection['username'],
            $connection['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

        return $pdo;
    }

    public static function getStatus() {
        foreach (self::$connections as $connection) {
            try {
                $pdo = self::tryConnection($connection);
                $pdo = null;

                return [
                    'connected' => true,
                    'source' => $connection['label'],
                    'host' => $connection['host'],
                    'port' => $connection['port'],
                    'database' => $connection['dbname'],
                ];
            } catch (PDOException $e) {
                continue;
            }
        }

        return [
            'connected' => false,
            'source' => null,
            'host' => self::$connections[0]['host'],
            'port' => self::$connections[0]['port'],
            'database' => self::$connections[0]['dbname'],
            'error' => 'Aucune base disponible'
        ];
    }

    public static function getConnection() {
        if (self::$pdo === null) {
            $lastError = null;

            foreach (self::$connections as $connection) {
                try {
                    self::$pdo = self::tryConnection($connection);
                    self::$activeConnection = $connection;
                    break;
                } catch (PDOException $e) {
                    $lastError = $e;
                }
            }

            if (self::$pdo === null) {
                die("Erreur de connexion : " . ($lastError ? $lastError->getMessage() : 'aucune base disponible'));
            }
        }

        return self::$pdo;
    }

    public static function getActiveConnection() {
        if (self::$activeConnection !== null) {
            return self::$activeConnection;
        }

        self::getConnection();
        return self::$activeConnection;
    }
}
