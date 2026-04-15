<?php

class Database {

    public static function connect() {

        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 5,
                ]
            );

            return $pdo;

        } catch (PDOException $e) {
            throw new PDOException("Database connection failed: " . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}

?>
