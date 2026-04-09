<?php

class User {

    public static function getAll() {
        $db = Database::connect();
        $stmt = $db->query("SELECT userID, student_id, name, email, isAdmin FROM users ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByEmail($email) {

        $db = Database::connect();

        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByStudentId($studentId) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE student_id = ?");
        $stmt->execute([$studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($name, $email, $passwordHash, $studentId) {

        $db = Database::connect();

        $stmt = $db->prepare("INSERT INTO users (student_id, name, email, passwordHash) VALUES (?, ?, ?, ?)");

        return $stmt->execute([$studentId, $name, $email, $passwordHash]);
    }

    public static function generateStudentId() {
        $db = Database::connect();
        do {
            $candidate = (string) random_int(2000000, 9999999);
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE student_id = ?");
            $stmt->execute([$candidate]);
            $exists = (int) ($stmt->fetchColumn() ?? 0);
        } while ($exists > 0);

        return $candidate;
    }

    public static function updatePasswordByEmail($email, $passwordHash) {

        $db = Database::connect();

        $stmt = $db->prepare("UPDATE users SET passwordHash = ? WHERE email = ?");

        return $stmt->execute([$passwordHash, $email]);
    }
}

?>
