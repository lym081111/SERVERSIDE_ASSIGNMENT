<?php

class Achievement {

    public static function getByUser($userID, $search = null, $sort = null) {
        $db = Database::connect();

        $allowedSort = [
            'achievementID' => 'achievementID',
            'title' => 'title',
            'category' => 'category',
            'dateReceived' => 'dateReceived',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'achievementID';

        $sql = "SELECT * FROM achievements WHERE userID = ?";
        $params = [$userID];

        if ($search !== null && $search !== '') {
            $sql .= " AND title LIKE ?";
            $params[] = '%' . $search . '%';
        }

        $sql .= " ORDER BY {$sortColumn} DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllWithUser($search = null, $sort = null) {
        $db = Database::connect();

        $allowedSort = [
            'achievementID' => 'a.achievementID',
            'title' => 'a.title',
            'category' => 'a.category',
            'dateReceived' => 'a.dateReceived',
            'student' => 'u.name',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'a.achievementID';

        $sql = "SELECT a.*, u.name AS userName, u.email AS userEmail
                FROM achievements a
                JOIN users u ON u.userID = a.userID";
        $params = [];

        if ($search !== null && $search !== '') {
            $sql .= " WHERE u.name LIKE ? OR u.email LIKE ? OR a.title LIKE ?";
            $searchTerm = '%' . $search . '%';
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        $sql .= " ORDER BY {$sortColumn} DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($userID, $title, $category, $dateReceived, $description) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO achievements (userID, title, category, dateReceived, description)
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$userID, $title, $category, $dateReceived, $description]);
    }

    public static function find($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM achievements WHERE achievementID = ? AND userID = ?");
        $stmt->execute([$id, $userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM achievements WHERE achievementID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($id, $userID, $title, $category, $dateReceived, $description) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE achievements
             SET title = ?, category = ?, dateReceived = ?, description = ?
             WHERE achievementID = ? AND userID = ?"
        );
        return $stmt->execute([$title, $category, $dateReceived, $description, $id, $userID]);
    }

    public static function updateById($id, $title, $category, $dateReceived, $description) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE achievements
             SET title = ?, category = ?, dateReceived = ?, description = ?
             WHERE achievementID = ?"
        );
        return $stmt->execute([$title, $category, $dateReceived, $description, $id]);
    }

    public static function delete($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM achievements WHERE achievementID = ? AND userID = ?");
        return $stmt->execute([$id, $userID]);
    }

    public static function deleteById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM achievements WHERE achievementID = ?");
        return $stmt->execute([$id]);
    }
}

?>
