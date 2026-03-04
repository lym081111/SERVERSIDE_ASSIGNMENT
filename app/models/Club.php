<?php

class Club {

    public static function getByUser($userID, $search = null, $sort = null) {
        $db = Database::connect();

        $allowedSort = [
            'clubID' => 'clubID',
            'clubName' => 'clubName',
            'role' => 'role',
            'startDate' => 'startDate',
            'endDate' => 'endDate',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'clubID';

        $sql = "SELECT * FROM clubs WHERE userID = ?";
        $params = [$userID];

        if ($search !== null && $search !== '') {
            $sql .= " AND clubName LIKE ?";
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
            'clubID' => 'c.clubID',
            'clubName' => 'c.clubName',
            'role' => 'c.role',
            'startDate' => 'c.startDate',
            'endDate' => 'c.endDate',
            'student' => 'u.name',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'c.clubID';

        $sql = "SELECT c.*, u.name AS userName, u.email AS userEmail
                FROM clubs c
                JOIN users u ON u.userID = c.userID";
        $params = [];

        if ($search !== null && $search !== '') {
            $sql .= " WHERE u.name LIKE ? OR u.email LIKE ? OR c.clubName LIKE ?";
            $searchTerm = '%' . $search . '%';
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        $sql .= " ORDER BY {$sortColumn} DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($userID, $clubName, $role, $roleDescription, $startDate, $endDate) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO clubs (userID, clubName, role, roleDescription, startDate, endDate)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$userID, $clubName, $role, $roleDescription, $startDate, $endDate]);
    }

    public static function find($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM clubs WHERE clubID = ? AND userID = ?");
        $stmt->execute([$id, $userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM clubs WHERE clubID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($id, $userID, $clubName, $role, $roleDescription, $startDate, $endDate) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE clubs
             SET clubName = ?, role = ?, roleDescription = ?, startDate = ?, endDate = ?
             WHERE clubID = ? AND userID = ?"
        );
        return $stmt->execute([$clubName, $role, $roleDescription, $startDate, $endDate, $id, $userID]);
    }

    public static function updateById($id, $clubName, $role, $roleDescription, $startDate, $endDate) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE clubs
             SET clubName = ?, role = ?, roleDescription = ?, startDate = ?, endDate = ?
             WHERE clubID = ?"
        );
        return $stmt->execute([$clubName, $role, $roleDescription, $startDate, $endDate, $id]);
    }

    public static function delete($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM clubs WHERE clubID = ? AND userID = ?");
        return $stmt->execute([$id, $userID]);
    }

    public static function deleteById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM clubs WHERE clubID = ?");
        return $stmt->execute([$id]);
    }
}

?>
