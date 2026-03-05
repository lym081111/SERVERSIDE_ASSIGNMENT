<?php

class User {

    public static function getAll() {
        $db = Database::connect();
        $stmt = $db->query("SELECT userID, name, email, isAdmin FROM users ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByEmail($email) {

        $db = Database::connect();

        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($name, $email, $passwordHash) {

        $db = Database::connect();

        $stmt = $db->prepare("INSERT INTO users (name, email, passwordHash) VALUES (?, ?, ?)");

        return $stmt->execute([$name, $email, $passwordHash]);
    }

    public static function updatePasswordByEmail($email, $passwordHash) {

        $db = Database::connect();

        $stmt = $db->prepare("UPDATE users SET passwordHash = ? WHERE email = ?");

        return $stmt->execute([$passwordHash, $email]);
    }
}

?>
