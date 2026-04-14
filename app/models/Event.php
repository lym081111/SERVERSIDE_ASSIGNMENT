<?php

class Event {

    public static function getByUser($userID, $search = null, $sort = null, $status = null) {
        $db = Database::connect();

        $allowedSort = [
            'eventID' => 'e.eventID',
            'eventTitle' => 'e.eventTitle',
            'eventType' => 'e.eventType',
            'eventDate' => 'e.eventDate',
            'clubName' => 'cc.clubName',
            'eventHours' => 'e.eventHours',
            'location' => 'e.location',
            'status' => 'e.status',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'e.eventID';

        $sql = "SELECT e.*, cc.clubName AS clubName
                FROM events e
                LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
                WHERE e.userID = ?";
        $params = [(int) $userID];

        if ($search !== null && $search !== '') {
            $term = '%' . $search . '%';
            $sql .= " AND (e.eventTitle LIKE ? OR e.eventType LIKE ? OR cc.clubName LIKE ? OR CAST(e.eventHours AS CHAR) LIKE ? OR e.location LIKE ? OR e.description LIKE ? OR e.reflection LIKE ? OR CAST(e.eventDate AS CHAR) LIKE ?)";
            array_push($params, $term, $term, $term, $term, $term, $term, $term, $term);
        }

        $allowedStatus = ['pending', 'approved', 'rejected'];
        if ($status !== null && in_array($status, $allowedStatus, true)) {
            $sql .= " AND e.status = ?";
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
            'eventID' => 'e.eventID',
            'eventTitle' => 'e.eventTitle',
            'eventType' => 'e.eventType',
            'eventDate' => 'e.eventDate',
            'clubName' => 'cc.clubName',
            'eventHours' => 'e.eventHours',
            'location' => 'e.location',
            'student' => 'u.name',
            'student_id' => 'u.student_id',
            'status' => 'e.status',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'e.eventID';

        $sql = "SELECT e.*, u.name AS userName, u.email AS userEmail, u.student_id AS studentId, cc.clubName AS clubName
                FROM events e
                JOIN users u ON u.userID = e.userID
                LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID";
        $params = [];
        $conditions = [];

        if ($search !== null && $search !== '') {
            $t = '%' . $search . '%';
            $conditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.student_id LIKE ? OR e.eventTitle LIKE ? OR e.eventType LIKE ? OR cc.clubName LIKE ? OR CAST(e.eventHours AS CHAR) LIKE ? OR e.location LIKE ? OR e.description LIKE ? OR e.reflection LIKE ? OR CAST(e.eventDate AS CHAR) LIKE ?)";
            $params = array_merge($params, [$t, $t, $t, $t, $t, $t, $t, $t, $t, $t, $t]);
        }

        $allowedStatus = ['pending', 'approved', 'rejected'];
        if ($status !== null && in_array($status, $allowedStatus, true)) {
            $conditions[] = "e.status = ?";
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

    public static function create($userID, $clubCatalogID, $eventTitle, $eventType, $eventDate, $eventHours, $location, $description, $reflection = null, $status = 'pending', $reviewedBy = null, $reviewNote = null, $reviewedAt = null, $evidencePath = null) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO events (userID, clubCatalogID, eventTitle, eventType, eventDate, eventHours, location, description, reflection, status, reviewed_by, review_note, reviewed_at, evidence_path)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            (int) $userID,
            $clubCatalogID !== null ? (int) $clubCatalogID : null,
            $eventTitle,
            $eventType,
            $eventDate,
            $eventHours,
            $location,
            $description,
            $reflection,
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
            "SELECT e.*, cc.clubName AS clubName
             FROM events e
             LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
             WHERE e.eventID = ? AND e.userID = ?"
        );
        $stmt->execute([(int) $id, (int) $userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT e.*, cc.clubName AS clubName
             FROM events e
             LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
             WHERE e.eventID = ?"
        );
        $stmt->execute([(int) $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findApprovedByIdForUser($eventID, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT e.*, cc.clubName AS clubName
             FROM events e
             LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
             WHERE e.eventID = ? AND e.userID = ? AND e.status = 'approved'"
        );
        $stmt->execute([(int) $eventID, (int) $userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getApprovedByUser($userID) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT e.eventID, e.userID, e.clubCatalogID, e.eventTitle, e.eventDate, e.eventHours, e.status,
                    cc.clubName
             FROM events e
             LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
             WHERE e.userID = ? AND e.status = 'approved'
             ORDER BY e.eventDate DESC, e.eventID DESC"
        );
        $stmt->execute([(int) $userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getApprovedAllWithUser() {
        $db = Database::connect();
        $stmt = $db->query(
            "SELECT e.eventID, e.userID, e.clubCatalogID, e.eventTitle, e.eventDate, e.eventHours,
                    u.name AS userName, u.student_id AS studentId,
                    cc.clubName
             FROM events e
             JOIN users u ON u.userID = e.userID
             LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
             WHERE e.status = 'approved'
             ORDER BY e.eventDate DESC, e.eventID DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function update($id, $userID, $clubCatalogID, $eventTitle, $eventType, $eventDate, $eventHours, $location, $description, $reflection = null, $evidencePath = null, $replaceEvidence = false) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE events
             SET clubCatalogID = ?, eventTitle = ?, eventType = ?, eventDate = ?, eventHours = ?, location = ?, description = ?, reflection = ?,
                 evidence_path = CASE WHEN ? = 1 THEN ? ELSE evidence_path END,
                 status = 'pending', reviewed_at = NULL, reviewed_by = NULL, review_note = NULL
             WHERE eventID = ? AND userID = ? AND status IN ('pending', 'rejected')"
        );
        return $stmt->execute([
            $clubCatalogID !== null ? (int) $clubCatalogID : null,
            $eventTitle,
            $eventType,
            $eventDate,
            $eventHours,
            $location,
            $description,
            $reflection,
            $replaceEvidence ? 1 : 0,
            $evidencePath,
            (int) $id,
            (int) $userID,
        ]);
    }

    public static function updateById($id, $clubCatalogID, $eventTitle, $eventType, $eventDate, $eventHours, $location, $description, $reflection = null) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE events
             SET clubCatalogID = ?, eventTitle = ?, eventType = ?, eventDate = ?, eventHours = ?, location = ?, description = ?, reflection = ?
             WHERE eventID = ?"
        );
        return $stmt->execute([
            $clubCatalogID !== null ? (int) $clubCatalogID : null,
            $eventTitle,
            $eventType,
            $eventDate,
            $eventHours,
            $location,
            $description,
            $reflection,
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
                "UPDATE events
                 SET status = ?, reviewed_at = NULL, reviewed_by = NULL, review_note = NULL
                 WHERE eventID = ?"
            );
            return $stmt->execute([$status, (int) $id]);
        }

        $reviewedAt = date('Y-m-d H:i:s');
        $stmt = $db->prepare(
            "UPDATE events
             SET status = ?, reviewed_at = ?, reviewed_by = ?, review_note = ?
             WHERE eventID = ?"
        );
        return $stmt->execute([$status, $reviewedAt, $reviewedBy, $reviewNote, (int) $id]);
    }

    public static function delete($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM events WHERE eventID = ? AND userID = ? AND status IN ('pending', 'rejected')");
        return $stmt->execute([(int) $id, (int) $userID]);
    }

    public static function deleteById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM events WHERE eventID = ?");
        return $stmt->execute([(int) $id]);
    }
}

?>
