<?php

class Event {

    public static function getByUser($userID, $search = null, $sort = null) {
        $db = Database::connect();

        $allowedSort = [
            'eventID' => 'eventID',
            'eventTitle' => 'eventTitle',
            'eventDate' => 'eventDate',
            'location' => 'location',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'eventID';

        $sql = "SELECT * FROM events WHERE userID = ?";
        $params = [$userID];

        if ($search !== null && $search !== '') {
            $sql .= " AND eventTitle LIKE ?";
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
            'eventID' => 'e.eventID',
            'eventTitle' => 'e.eventTitle',
            'eventDate' => 'e.eventDate',
            'location' => 'e.location',
            'student' => 'u.name',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'e.eventID';

        $sql = "SELECT e.*, u.name AS userName, u.email AS userEmail
                FROM events e
                JOIN users u ON u.userID = e.userID";
        $params = [];

        if ($search !== null && $search !== '') {
            $sql .= " WHERE u.name LIKE ? OR u.email LIKE ? OR e.eventTitle LIKE ?";
            $searchTerm = '%' . $search . '%';
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        $sql .= " ORDER BY {$sortColumn} DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($userID, $eventTitle, $eventDate, $location, $description) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO events (userID, eventTitle, eventDate, location, description)
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$userID, $eventTitle, $eventDate, $location, $description]);
    }

    public static function find($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM events WHERE eventID = ? AND userID = ?");
        $stmt->execute([$id, $userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM events WHERE eventID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($id, $userID, $eventTitle, $eventDate, $location, $description) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE events
             SET eventTitle = ?, eventDate = ?, location = ?, description = ?
             WHERE eventID = ? AND userID = ?"
        );
        return $stmt->execute([$eventTitle, $eventDate, $location, $description, $id, $userID]);
    }

    public static function updateById($id, $eventTitle, $eventDate, $location, $description) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE events
             SET eventTitle = ?, eventDate = ?, location = ?, description = ?
             WHERE eventID = ?"
        );
        return $stmt->execute([$eventTitle, $eventDate, $location, $description, $id]);
    }

    public static function delete($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM events WHERE eventID = ? AND userID = ?");
        return $stmt->execute([$id, $userID]);
    }

    public static function deleteById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM events WHERE eventID = ?");
        return $stmt->execute([$id]);
    }
}

?>
