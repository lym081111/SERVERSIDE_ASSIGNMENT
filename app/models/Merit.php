<?php

class Merit {

    public static function getByUser($userID, $search = null, $sort = null) {
        $db = Database::connect();

        $allowedSort = [
            'meritID' => 'meritID',
            'activityName' => 'activityName',
            'hours' => 'hours',
            'dateFrom' => 'dateFrom',
            'dateTo' => 'dateTo',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'meritID';

        $sql = "SELECT * FROM merits WHERE userID = ?";
        $params = [$userID];

        if ($search !== null && $search !== '') {
            $sql .= " AND activityName LIKE ?";
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
            'meritID' => 'm.meritID',
            'activityName' => 'm.activityName',
            'hours' => 'm.hours',
            'dateFrom' => 'm.dateFrom',
            'dateTo' => 'm.dateTo',
            'student' => 'u.name',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'm.meritID';

        $sql = "SELECT m.*, u.name AS userName, u.email AS userEmail
                FROM merits m
                JOIN users u ON u.userID = m.userID";
        $params = [];

        if ($search !== null && $search !== '') {
            $sql .= " WHERE u.name LIKE ? OR u.email LIKE ? OR m.activityName LIKE ?";
            $searchTerm = '%' . $search . '%';
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        $sql .= " ORDER BY {$sortColumn} DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($userID, $activityName, $hours, $dateFrom, $dateTo) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO merits (userID, activityName, hours, dateFrom, dateTo)
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$userID, $activityName, $hours, $dateFrom, $dateTo]);
    }

    public static function find($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM merits WHERE meritID = ? AND userID = ?");
        $stmt->execute([$id, $userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM merits WHERE meritID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($id, $userID, $activityName, $hours, $dateFrom, $dateTo) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE merits
             SET activityName=?, hours=?, dateFrom=?, dateTo=?
             WHERE meritID=? AND userID=?"
        );
        return $stmt->execute([$activityName, $hours, $dateFrom, $dateTo, $id, $userID]);
    }

    public static function updateById($id, $activityName, $hours, $dateFrom, $dateTo) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE merits
             SET activityName=?, hours=?, dateFrom=?, dateTo=?
             WHERE meritID=?"
        );
        return $stmt->execute([$activityName, $hours, $dateFrom, $dateTo, $id]);
    }

    public static function delete($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM merits WHERE meritID=? AND userID=?");
        return $stmt->execute([$id, $userID]);
    }

    public static function deleteById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM merits WHERE meritID=?");
        return $stmt->execute([$id]);
    }
}

?>
