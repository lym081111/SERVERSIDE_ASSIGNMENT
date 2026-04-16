<?php

class Event {

    public static function findCapacityTemplate($clubCatalogID, $eventTitle, $eventType, $eventDate, $excludeEventID = null) {
        $db = Database::connect();

        $sql = "SELECT participantCapacity, waitlistEnabled
                FROM events
                WHERE clubCatalogID = ?
                  AND eventDate = ?
                  AND LOWER(TRIM(eventTitle)) = LOWER(TRIM(?))
                  AND LOWER(TRIM(COALESCE(eventType, ''))) = LOWER(TRIM(?))
                  AND participantCapacity IS NOT NULL
                  AND participantCapacity > 0
                  AND status IN ('pending', 'approved')";
        $params = [
            (int) $clubCatalogID,
            (string) $eventDate,
            (string) $eventTitle,
            (string) $eventType,
        ];

        if ($excludeEventID !== null) {
            $sql .= " AND eventID <> ?";
            $params[] = (int) $excludeEventID;
        }

        $sql .= " ORDER BY eventID ASC LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function getByUser($userID, $search = null, $sort = null, $status = null) {
        $db = Database::connect();
        $registeredCountSql = "(SELECT COUNT(*)
                                FROM events e_reg
                                WHERE e_reg.status = 'approved'
                                  AND e_reg.eventDate = e.eventDate
                                  AND e_reg.clubCatalogID <=> e.clubCatalogID
                                  AND LOWER(TRIM(e_reg.eventTitle)) = LOWER(TRIM(e.eventTitle)))";
        $pendingCountSql = "(SELECT COUNT(*)
                             FROM events e_pending
                             WHERE e_pending.status = 'pending'
                               AND e_pending.eventDate = e.eventDate
                               AND e_pending.clubCatalogID <=> e.clubCatalogID
                               AND LOWER(TRIM(e_pending.eventTitle)) = LOWER(TRIM(e.eventTitle)))";

        $allowedSort = [
            'eventID' => 'e.eventID',
            'eventTitle' => 'e.eventTitle',
            'eventType' => 'e.eventType',
            'eventDate' => 'e.eventDate',
            'clubName' => 'cc.clubName',
            'eventHours' => 'e.eventHours',
            'location' => 'e.location',
            'status' => 'e.status',
            'participantCapacity' => 'e.participantCapacity',
            'registeredCount' => 'registeredCount',
            'waitlistCount' => 'waitlistCount',
            'registrationStatus' => 'registrationStatus',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'e.eventID';

        $sql = "SELECT e.*, cc.clubName AS clubName,
                       {$registeredCountSql} AS registeredCount,
                       CASE
                           WHEN COALESCE(e.waitlistEnabled, 1) = 1 THEN {$pendingCountSql}
                           ELSE 0
                       END AS waitlistCount,
                       CASE
                           WHEN COALESCE(e.participantCapacity, 0) <= 0 THEN 'open'
                           WHEN {$registeredCountSql} < e.participantCapacity THEN 'open'
                           WHEN COALESCE(e.waitlistEnabled, 1) = 1 THEN 'waitlist'
                           ELSE 'full'
                       END AS registrationStatus
                FROM events e
                LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
                WHERE e.userID = ?";
        $params = [(int) $userID];

        if ($search !== null && $search !== '') {
            $term = '%' . $search . '%';
            $sql .= " AND (e.eventTitle LIKE ? OR e.eventType LIKE ? OR cc.clubName LIKE ? OR CAST(e.eventHours AS CHAR) LIKE ? OR e.location LIKE ? OR e.description LIKE ? OR CAST(e.eventDate AS CHAR) LIKE ? OR CAST(e.participantCapacity AS CHAR) LIKE ?)";
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
        $registeredCountSql = "(SELECT COUNT(*)
                                FROM events e_reg
                                WHERE e_reg.status = 'approved'
                                  AND e_reg.eventDate = e.eventDate
                                  AND e_reg.clubCatalogID <=> e.clubCatalogID
                                  AND LOWER(TRIM(e_reg.eventTitle)) = LOWER(TRIM(e.eventTitle)))";
        $pendingCountSql = "(SELECT COUNT(*)
                             FROM events e_pending
                             WHERE e_pending.status = 'pending'
                               AND e_pending.eventDate = e.eventDate
                               AND e_pending.clubCatalogID <=> e.clubCatalogID
                               AND LOWER(TRIM(e_pending.eventTitle)) = LOWER(TRIM(e.eventTitle)))";

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
            'participantCapacity' => 'e.participantCapacity',
            'registeredCount' => 'registeredCount',
            'waitlistCount' => 'waitlistCount',
            'registrationStatus' => 'registrationStatus',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'e.eventID';

        $sql = "SELECT e.*, u.name AS userName, u.email AS userEmail, u.student_id AS studentId, cc.clubName AS clubName,
                       {$registeredCountSql} AS registeredCount,
                       CASE
                           WHEN COALESCE(e.waitlistEnabled, 1) = 1 THEN {$pendingCountSql}
                           ELSE 0
                       END AS waitlistCount,
                       CASE
                           WHEN COALESCE(e.participantCapacity, 0) <= 0 THEN 'open'
                           WHEN {$registeredCountSql} < e.participantCapacity THEN 'open'
                           WHEN COALESCE(e.waitlistEnabled, 1) = 1 THEN 'waitlist'
                           ELSE 'full'
                       END AS registrationStatus
                FROM events e
                JOIN users u ON u.userID = e.userID
                LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID";
        $params = [];
        $conditions = [];

        if ($search !== null && $search !== '') {
            $t = '%' . $search . '%';
            $conditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.student_id LIKE ? OR e.eventTitle LIKE ? OR e.eventType LIKE ? OR cc.clubName LIKE ? OR CAST(e.eventHours AS CHAR) LIKE ? OR e.location LIKE ? OR e.description LIKE ? OR CAST(e.eventDate AS CHAR) LIKE ? OR CAST(e.participantCapacity AS CHAR) LIKE ?)";
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

    public static function getJoinTemplatesForUser($userID) {
        $db = Database::connect();
        $registeredCountSql = "(SELECT COUNT(*)
                                FROM events e_reg
                                WHERE e_reg.status = 'approved'
                                  AND e_reg.eventDate = e.eventDate
                                  AND e_reg.clubCatalogID <=> e.clubCatalogID
                                  AND LOWER(TRIM(e_reg.eventTitle)) = LOWER(TRIM(e.eventTitle)))";
        $pendingCountSql = "(SELECT COUNT(*)
                             FROM events e_pending
                             WHERE e_pending.status = 'pending'
                               AND e_pending.eventDate = e.eventDate
                               AND e_pending.clubCatalogID <=> e.clubCatalogID
                               AND LOWER(TRIM(e_pending.eventTitle)) = LOWER(TRIM(e.eventTitle)))";

        $sql = "SELECT e.eventID AS templateEventID, e.clubCatalogID, cc.clubName, e.eventTitle, e.eventType, e.eventDate,
                       e.eventHours, e.location, e.description, e.participantCapacity, e.waitlistEnabled,
                       {$registeredCountSql} AS registeredCount,
                       {$pendingCountSql} AS pendingCount,
                       CASE
                           WHEN COALESCE(e.participantCapacity, 0) <= 0 THEN 'open'
                           WHEN {$registeredCountSql} < e.participantCapacity THEN 'open'
                           WHEN COALESCE(e.waitlistEnabled, 1) = 1 THEN 'waitlist'
                           ELSE 'full'
                       END AS registrationStatus,
                       CASE
                           WHEN EXISTS (
                               SELECT 1
                               FROM events e_user_approved
                               WHERE e_user_approved.userID = ?
                                 AND e_user_approved.status = 'approved'
                                 AND e_user_approved.eventDate = e.eventDate
                                 AND e_user_approved.clubCatalogID <=> e.clubCatalogID
                                 AND LOWER(TRIM(e_user_approved.eventTitle)) = LOWER(TRIM(e.eventTitle))
                           ) THEN 'approved'
                           WHEN EXISTS (
                               SELECT 1
                               FROM events e_user_pending
                               WHERE e_user_pending.userID = ?
                                 AND e_user_pending.status = 'pending'
                                 AND e_user_pending.eventDate = e.eventDate
                                 AND e_user_pending.clubCatalogID <=> e.clubCatalogID
                                 AND LOWER(TRIM(e_user_pending.eventTitle)) = LOWER(TRIM(e.eventTitle))
                           ) THEN 'pending'
                           ELSE ''
                       END AS userJoinState
                FROM events e
                JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
                WHERE cc.is_active = 1
                  AND e.clubCatalogID IS NOT NULL
                  AND e.status IN ('pending', 'approved')
                  AND e.eventID IN (
                      SELECT MIN(e2.eventID)
                      FROM events e2
                      WHERE e2.status IN ('pending', 'approved')
                        AND e2.clubCatalogID IS NOT NULL
                      GROUP BY e2.clubCatalogID, LOWER(TRIM(e2.eventTitle)), e2.eventDate
                  )
                ORDER BY e.eventDate DESC, e.eventTitle ASC, e.eventID DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $userID, (int) $userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findJoinTemplateById($eventID) {
        $db = Database::connect();
        $registeredCountSql = "(SELECT COUNT(*)
                                FROM events e_reg
                                WHERE e_reg.status = 'approved'
                                  AND e_reg.eventDate = e.eventDate
                                  AND e_reg.clubCatalogID <=> e.clubCatalogID
                                  AND LOWER(TRIM(e_reg.eventTitle)) = LOWER(TRIM(e.eventTitle)))";
        $pendingCountSql = "(SELECT COUNT(*)
                             FROM events e_pending
                             WHERE e_pending.status = 'pending'
                               AND e_pending.eventDate = e.eventDate
                               AND e_pending.clubCatalogID <=> e.clubCatalogID
                               AND LOWER(TRIM(e_pending.eventTitle)) = LOWER(TRIM(e.eventTitle)))";

        $sql = "SELECT e.eventID AS templateEventID, e.clubCatalogID, cc.clubName, e.eventTitle, e.eventType, e.eventDate,
                       e.eventHours, e.location, e.description, e.participantCapacity, e.waitlistEnabled,
                       {$registeredCountSql} AS registeredCount,
                       {$pendingCountSql} AS pendingCount,
                       CASE
                           WHEN COALESCE(e.participantCapacity, 0) <= 0 THEN 'open'
                           WHEN {$registeredCountSql} < e.participantCapacity THEN 'open'
                           WHEN COALESCE(e.waitlistEnabled, 1) = 1 THEN 'waitlist'
                           ELSE 'full'
                       END AS registrationStatus
                FROM events e
                JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
                WHERE e.eventID = ?
                  AND cc.is_active = 1
                  AND e.status IN ('pending', 'approved')
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([(int) $eventID]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function hasPendingOrApprovedRegistration($userID, $clubCatalogID, $eventTitle, $eventDate) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT COUNT(*)
             FROM events
             WHERE userID = ?
               AND status IN ('pending', 'approved')
               AND clubCatalogID <=> ?
               AND eventDate = ?
               AND LOWER(TRIM(eventTitle)) = LOWER(TRIM(?))"
        );
        $stmt->execute([
            (int) $userID,
            $clubCatalogID !== null ? (int) $clubCatalogID : null,
            (string) $eventDate,
            (string) $eventTitle,
        ]);
        return ((int) ($stmt->fetchColumn() ?? 0)) > 0;
    }

    public static function create($userID, $clubCatalogID, $eventTitle, $eventType, $eventDate, $eventHours, $location, $description, $reflection = null, $status = 'pending', $reviewedBy = null, $reviewNote = null, $reviewedAt = null, $evidencePath = null, $participantCapacity = null, $waitlistEnabled = 1) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO events (userID, clubCatalogID, eventTitle, eventType, eventDate, eventHours, location, description, reflection, status, reviewed_by, review_note, reviewed_at, evidence_path, participantCapacity, waitlistEnabled)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
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
            $participantCapacity !== null ? (int) $participantCapacity : null,
            (int) ($waitlistEnabled ? 1 : 0),
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
            "SELECT e.eventID, e.userID, e.clubCatalogID, e.eventTitle, e.eventType, e.eventDate, e.eventHours, e.status,
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
            "SELECT e.eventID, e.userID, e.clubCatalogID, e.eventTitle, e.eventType, e.eventDate, e.eventHours,
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

    public static function update($id, $userID, $clubCatalogID, $eventTitle, $eventType, $eventDate, $eventHours, $location, $description, $reflection = null, $evidencePath = null, $replaceEvidence = false, $participantCapacity = null, $waitlistEnabled = 1) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE events
             SET clubCatalogID = ?, eventTitle = ?, eventType = ?, eventDate = ?, eventHours = ?, location = ?, description = ?, reflection = ?,
                 participantCapacity = ?, waitlistEnabled = ?,
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
            $participantCapacity !== null ? (int) $participantCapacity : null,
            (int) ($waitlistEnabled ? 1 : 0),
            $replaceEvidence ? 1 : 0,
            $evidencePath,
            (int) $id,
            (int) $userID,
        ]);
    }

    public static function updateById($id, $clubCatalogID, $eventTitle, $eventType, $eventDate, $eventHours, $location, $description, $reflection = null, $participantCapacity = null, $waitlistEnabled = 1) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE events
             SET clubCatalogID = ?, eventTitle = ?, eventType = ?, eventDate = ?, eventHours = ?, location = ?, description = ?, reflection = ?,
                 participantCapacity = ?, waitlistEnabled = ?
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
            $participantCapacity !== null ? (int) $participantCapacity : null,
            (int) ($waitlistEnabled ? 1 : 0),
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
