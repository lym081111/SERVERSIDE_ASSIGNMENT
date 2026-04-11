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

    public static function findById($userId) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE userID = ?");
        $stmt->execute([(int) $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByStudentId($studentId) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE student_id = ?");
        $stmt->execute([$studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function resolveStudentSelectionForAdmin($selectedUserId, $studentEmail, $studentId) {
        $selectedUserId = (int) $selectedUserId;
        $studentEmail = trim((string) $studentEmail);
        $studentId = trim((string) $studentId);

        $matches = [];

        if ($selectedUserId > 0) {
            $selectedUser = self::findById($selectedUserId);
            if (!$selectedUser || !empty($selectedUser['isAdmin'])) {
                return ['userID' => 0, 'error' => 'Selected student is invalid. Please choose a registered student account.'];
            }
            $matches['selected'] = (int) $selectedUser['userID'];
        }

        if ($studentEmail !== '') {
            $emailUser = self::findByEmail($studentEmail);
            if (!$emailUser || !empty($emailUser['isAdmin'])) {
                return ['userID' => 0, 'error' => 'Student email not found. Please enter a valid registered student email.'];
            }
            $matches['email'] = (int) $emailUser['userID'];
        }

        if ($studentId !== '') {
            $idUser = self::findByStudentId($studentId);
            if (!$idUser || !empty($idUser['isAdmin'])) {
                return ['userID' => 0, 'error' => 'Student ID not found. Please enter a valid registered student ID.'];
            }
            $matches['student_id'] = (int) $idUser['userID'];
        }

        if (empty($matches)) {
            return ['userID' => 0, 'error' => 'Please select a valid student.'];
        }

        $uniqueIds = array_values(array_unique(array_values($matches)));
        if (count($uniqueIds) > 1) {
            return ['userID' => 0, 'error' => 'Student information does not match. Selected student, Student ID, and Student Email must refer to the same account.'];
        }

        return ['userID' => $uniqueIds[0], 'error' => null];
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
