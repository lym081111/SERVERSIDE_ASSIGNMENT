<?php

class Achievement {

    public static function getByUser($userID, $search = null, $sort = null, $status = null) {
        $db = Database::connect();

        $allowedSort = [
            'achievementID' => 'a.achievementID',
            'title' => 'a.title',
            'eventTitle' => 'e.eventTitle',
            'clubName' => 'cc.clubName',
            'category' => 'a.category',
            'achievementLevel' => 'a.achievementLevel',
            'dateReceived' => 'a.dateReceived',
            'status' => 'a.status',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'a.achievementID';

        $sql = "SELECT a.*, e.eventTitle, e.eventDate, cc.clubName
                FROM achievements a
                LEFT JOIN events e ON e.eventID = a.eventID
                LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
                WHERE a.userID = ?";
        $params = [(int) $userID];

        if ($search !== null && $search !== '') {
            $term = '%' . $search . '%';
            $sql .= " AND (a.title LIKE ? OR e.eventTitle LIKE ? OR cc.clubName LIKE ? OR a.category LIKE ? OR a.achievementLevel LIKE ? OR a.description LIKE ?)";
            array_push($params, $term, $term, $term, $term, $term, $term);
        }

        $allowedStatus = ['pending', 'approved', 'rejected'];
        if ($status !== null && in_array($status, $allowedStatus, true)) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY {$sortColumn} DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllWithUser($search = null, $sort = null, $status = null) {
        $db = Database::connect();

        $allowedSort = [
            'achievementID' => 'a.achievementID',
            'title' => 'a.title',
            'eventTitle' => 'e.eventTitle',
            'clubName' => 'cc.clubName',
            'category' => 'a.category',
            'achievementLevel' => 'a.achievementLevel',
            'dateReceived' => 'a.dateReceived',
            'student' => 'u.name',
            'student_id' => 'u.student_id',
            'status' => 'a.status',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'a.achievementID';

        $sql = "SELECT a.*, u.name AS userName, u.email AS userEmail, u.student_id AS studentId,
                       e.eventTitle, e.eventDate, cc.clubName
                FROM achievements a
                JOIN users u ON u.userID = a.userID
                LEFT JOIN events e ON e.eventID = a.eventID
                LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID";
        $params = [];
        $conditions = [];

        if ($search !== null && $search !== '') {
            $t = '%' . $search . '%';
            $conditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.student_id LIKE ? OR a.title LIKE ? OR e.eventTitle LIKE ? OR cc.clubName LIKE ? OR a.category LIKE ? OR a.achievementLevel LIKE ? OR a.description LIKE ?)";
            $params = array_merge($params, [$t, $t, $t, $t, $t, $t, $t, $t, $t]);
        }

        $allowedStatus = ['pending', 'approved', 'rejected'];
        if ($status !== null && in_array($status, $allowedStatus, true)) {
            $conditions[] = "a.status = ?";
            $params[] = $status;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY {$sortColumn} DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($userID, $eventID, $title, $category, $achievementLevel, $dateReceived, $description, $status = 'pending', $reviewedBy = null, $reviewNote = null, $reviewedAt = null, $evidencePath = null) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO achievements (userID, eventID, title, category, achievementLevel, dateReceived, description, status, reviewed_by, review_note, reviewed_at, evidence_path)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            (int) $userID,
            $eventID !== null ? (int) $eventID : null,
            $title,
            $category,
            $achievementLevel,
            $dateReceived,
            $description,
            $status,
            $reviewedBy,
            $reviewNote,
            $reviewedAt,
            $evidencePath,
        ]);
    }

    public static function find($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT a.*, e.eventTitle, e.eventDate, cc.clubName
             FROM achievements a
             LEFT JOIN events e ON e.eventID = a.eventID
             LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
             WHERE a.achievementID = ? AND a.userID = ?"
        );
        $stmt->execute([(int) $id, (int) $userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT a.*, e.eventTitle, e.eventDate, cc.clubName
             FROM achievements a
             LEFT JOIN events e ON e.eventID = a.eventID
             LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
             WHERE a.achievementID = ?"
        );
        $stmt->execute([(int) $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($id, $userID, $eventID, $title, $category, $achievementLevel, $dateReceived, $description, $evidencePath = null, $replaceEvidence = false) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE achievements
             SET eventID = ?, title = ?, category = ?, achievementLevel = ?, dateReceived = ?, description = ?,
                 evidence_path = CASE WHEN ? = 1 THEN ? ELSE evidence_path END,
                 status = 'pending', reviewed_at = NULL, reviewed_by = NULL, review_note = NULL
             WHERE achievementID = ? AND userID = ? AND status IN ('pending', 'rejected')"
        );
        return $stmt->execute([
            $eventID !== null ? (int) $eventID : null,
            $title,
            $category,
            $achievementLevel,
            $dateReceived,
            $description,
            $replaceEvidence ? 1 : 0,
            $evidencePath,
            (int) $id,
            (int) $userID,
        ]);
    }

    public static function updateById($id, $eventID, $title, $category, $achievementLevel, $dateReceived, $description) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE achievements
             SET eventID = ?, title = ?, category = ?, achievementLevel = ?, dateReceived = ?, description = ?
             WHERE achievementID = ?"
        );
        return $stmt->execute([
            $eventID !== null ? (int) $eventID : null,
            $title,
            $category,
            $achievementLevel,
            $dateReceived,
            $description,
            (int) $id,
        ]);
    }

    public static function updateStatusById($id, $status, $reviewedBy, $reviewNote) {
        $db = Database::connect();

        $allowedStatus = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $allowedStatus, true)) {
            return false;
        }

        if ($status === 'pending') {
            $stmt = $db->prepare(
                "UPDATE achievements
                 SET status = ?, reviewed_at = NULL, reviewed_by = NULL, review_note = NULL
                 WHERE achievementID = ?"
            );
            return $stmt->execute([$status, (int) $id]);
        }

        $reviewedAt = date('Y-m-d H:i:s');
        $stmt = $db->prepare(
            "UPDATE achievements
             SET status = ?, reviewed_at = ?, reviewed_by = ?, review_note = ?
             WHERE achievementID = ?"
        );
        return $stmt->execute([$status, $reviewedAt, $reviewedBy, $reviewNote, (int) $id]);
    }

    public static function delete($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM achievements WHERE achievementID = ? AND userID = ? AND status IN ('pending', 'rejected')");
        return $stmt->execute([(int) $id, (int) $userID]);
    }

    public static function deleteById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM achievements WHERE achievementID = ?");
        return $stmt->execute([(int) $id]);
    }
}

?>
