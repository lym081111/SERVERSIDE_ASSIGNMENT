<?php

class Merit {

    public static function getByUser($userID, $search = null, $sort = null, $status = null) {
        $db = Database::connect();

        $allowedSort = [
            'meritID' => 'm.meritID',
            'activityName' => 'm.activityName',
            'eventTitle' => 'e.eventTitle',
            'clubName' => 'cc.clubName',
            'hours' => 'm.hours',
            'dateFrom' => 'm.dateFrom',
            'dateTo' => 'm.dateTo',
            'status' => 'm.status',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'm.meritID';

        $sql = "SELECT m.*, e.eventTitle, e.eventDate, e.eventHours, cc.clubName
                FROM merits m
                LEFT JOIN events e ON e.eventID = m.eventID
                LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
                WHERE m.userID = ?";
        $params = [(int) $userID];

        if ($search !== null && $search !== '') {
            $sql .= " AND (m.activityName LIKE ? OR e.eventTitle LIKE ? OR cc.clubName LIKE ?)";
            $term = '%' . $search . '%';
            array_push($params, $term, $term, $term);
        }

        $allowedStatus = ['pending', 'approved', 'rejected'];
        if ($status !== null && in_array($status, $allowedStatus, true)) {
            $sql .= " AND m.status = ?";
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
            'meritID' => 'm.meritID',
            'activityName' => 'm.activityName',
            'eventTitle' => 'e.eventTitle',
            'clubName' => 'cc.clubName',
            'hours' => 'm.hours',
            'dateFrom' => 'm.dateFrom',
            'dateTo' => 'm.dateTo',
            'student' => 'u.name',
            'student_id' => 'u.student_id',
            'status' => 'm.status',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'm.meritID';

        $sql = "SELECT m.*, u.name AS userName, u.email AS userEmail, u.student_id AS studentId,
                       e.eventTitle, e.eventDate, e.eventHours, cc.clubName
                FROM merits m
                JOIN users u ON u.userID = m.userID
                LEFT JOIN events e ON e.eventID = m.eventID
                LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID";
        $params = [];
        $conditions = [];

        if ($search !== null && $search !== '') {
            $conditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.student_id LIKE ? OR m.activityName LIKE ? OR e.eventTitle LIKE ? OR cc.clubName LIKE ?)";
            $searchTerm = '%' . $search . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        $allowedStatus = ['pending', 'approved', 'rejected'];
        if ($status !== null && in_array($status, $allowedStatus, true)) {
            $conditions[] = "m.status = ?";
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

    public static function create($userID, $eventID, $activityName, $hours, $dateFrom, $dateTo, $status = 'pending', $reviewedBy = null, $reviewNote = null, $reviewedAt = null, $evidencePath = null, $achievementID = null, $achievementRank = null, $achievementBonus = 0, $baseHours = null) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO merits (userID, eventID, achievementID, activityName, achievement_rank, achievement_bonus, hours, base_hours, dateFrom, dateTo, status, reviewed_by, review_note, reviewed_at, evidence_path)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $baseHoursValue = $baseHours !== null ? (int) $baseHours : (int) $hours;
        $created = $stmt->execute([
            (int) $userID,
            $eventID !== null ? (int) $eventID : null,
            $achievementID !== null ? (int) $achievementID : null,
            $activityName,
            $achievementRank !== null ? (string) $achievementRank : null,
            (int) $achievementBonus,
            $hours,
            $baseHoursValue,
            $dateFrom,
            $dateTo,
            $status,
            $reviewedBy,
            $reviewNote,
            $reviewedAt,
            $evidencePath,
        ]);
        if (!$created) {
            return false;
        }

        $meritId = (int) $db->lastInsertId();
        $changeSource = $status === 'approved' ? 'admin_creation' : 'student_submission';
        $changedBy = $reviewedBy !== null ? (int) $reviewedBy : (int) $userID;
        self::addStatusLog($meritId, null, $status, $changedBy, $reviewNote, $changeSource);

        return true;
    }

    public static function find($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT m.*, e.eventTitle, e.eventDate, e.eventHours, cc.clubName
             FROM merits m
             LEFT JOIN events e ON e.eventID = m.eventID
             LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
             WHERE m.meritID = ? AND m.userID = ?"
        );
        $stmt->execute([(int) $id, (int) $userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT m.*, e.eventTitle, e.eventDate, e.eventHours, cc.clubName
             FROM merits m
             LEFT JOIN events e ON e.eventID = m.eventID
             LEFT JOIN club_catalog cc ON cc.clubCatalogID = e.clubCatalogID
             WHERE m.meritID = ?"
        );
        $stmt->execute([(int) $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($id, $userID, $eventID, $activityName, $hours, $dateFrom, $dateTo, $evidencePath = null, $replaceEvidence = false, $appealNote = null, $achievementID = null, $achievementRank = null, $achievementBonus = 0, $baseHours = null) {
        $db = Database::connect();
        $currentStmt = $db->prepare("SELECT status FROM merits WHERE meritID = ? AND userID = ? AND status IN ('pending', 'rejected')");
        $currentStmt->execute([(int) $id, (int) $userID]);
        $current = $currentStmt->fetch(PDO::FETCH_ASSOC);
        if (!$current) {
            return false;
        }

        $previousStatus = (string) ($current['status'] ?? 'pending');
        $isAppealResubmission = $previousStatus === 'rejected';
        $appealNote = trim((string) $appealNote);
        $appealNote = $appealNote !== '' ? $appealNote : null;
        $now = date('Y-m-d H:i:s');

        $stmt = $db->prepare(
            "UPDATE merits
             SET eventID = ?, achievementID = ?, activityName = ?, achievement_rank = ?, achievement_bonus = ?, hours = ?, base_hours = ?, dateFrom = ?, dateTo = ?,
                 evidence_path = CASE WHEN ? = 1 THEN ? ELSE evidence_path END,
                 status='pending', reviewed_at=NULL, reviewed_by=NULL, review_note=NULL,
                 appeal_note = CASE WHEN ? = 1 THEN ? ELSE appeal_note END,
                 appealed_at = CASE WHEN ? = 1 THEN ? ELSE appealed_at END,
                 resubmission_count = CASE WHEN ? = 1 THEN COALESCE(resubmission_count, 0) + 1 ELSE COALESCE(resubmission_count, 0) END,
                 last_resubmitted_at = CASE WHEN ? = 1 THEN ? ELSE last_resubmitted_at END
             WHERE meritID=? AND userID=? AND status IN ('pending', 'rejected')"
        );
        $updated = $stmt->execute([
            $eventID !== null ? (int) $eventID : null,
            $achievementID !== null ? (int) $achievementID : null,
            $activityName,
            $achievementRank !== null ? (string) $achievementRank : null,
            (int) $achievementBonus,
            $hours,
            $baseHours !== null ? (int) $baseHours : (int) $hours,
            $dateFrom,
            $dateTo,
            $replaceEvidence ? 1 : 0,
            $evidencePath,
            $isAppealResubmission ? 1 : 0,
            $appealNote,
            $isAppealResubmission ? 1 : 0,
            $now,
            $isAppealResubmission ? 1 : 0,
            $isAppealResubmission ? 1 : 0,
            $now,
            (int) $id,
            (int) $userID,
        ]);

        if ($updated && $isAppealResubmission) {
            self::addStatusLog((int) $id, 'rejected', 'pending', (int) $userID, $appealNote, 'student_appeal');
        }

        return $updated;
    }

    public static function updateById($id, $eventID, $activityName, $hours, $dateFrom, $dateTo, $achievementID = null, $achievementRank = null, $achievementBonus = 0, $baseHours = null) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE merits
             SET eventID = ?, achievementID = ?, activityName = ?, achievement_rank = ?, achievement_bonus = ?, hours = ?, base_hours = ?, dateFrom = ?, dateTo = ?
             WHERE meritID = ?"
        );
        return $stmt->execute([
            $eventID !== null ? (int) $eventID : null,
            $achievementID !== null ? (int) $achievementID : null,
            $activityName,
            $achievementRank !== null ? (string) $achievementRank : null,
            (int) $achievementBonus,
            $hours,
            $baseHours !== null ? (int) $baseHours : (int) $hours,
            $dateFrom,
            $dateTo,
            (int) $id,
        ]);
    }

    public static function updateStatusById($id, $status, $reviewedBy, $reviewNote, $source = 'admin_review') {
        $db = Database::connect();

        $allowedStatus = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $allowedStatus, true)) {
            return false;
        }

        $existing = self::findById($id);
        if (!$existing) {
            return false;
        }
        $fromStatus = (string) ($existing['status'] ?? 'pending');
        $reviewNote = trim((string) $reviewNote);
        $reviewNote = $reviewNote !== '' ? $reviewNote : null;

        if ($status === 'pending') {
            $stmt = $db->prepare(
                "UPDATE merits
                 SET status = ?, reviewed_at = NULL, reviewed_by = NULL, review_note = NULL
                 WHERE meritID = ?"
            );
            $updated = $stmt->execute([$status, (int) $id]);
            if ($updated && $fromStatus !== $status) {
                self::addStatusLog((int) $id, $fromStatus, $status, $reviewedBy, $reviewNote, $source);
            }
            return $updated;
        }

        $reviewedAt = date('Y-m-d H:i:s');
        $stmt = $db->prepare(
            "UPDATE merits
             SET status = ?, reviewed_at = ?, reviewed_by = ?, review_note = ?
             WHERE meritID = ?"
        );
        $updated = $stmt->execute([$status, $reviewedAt, $reviewedBy, $reviewNote, (int) $id]);
        if ($updated && $fromStatus !== $status) {
            self::addStatusLog((int) $id, $fromStatus, $status, $reviewedBy, $reviewNote, $source);
        }
        return $updated;
    }

    public static function delete($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM merits WHERE meritID=? AND userID=? AND status IN ('pending', 'rejected')");
        return $stmt->execute([(int) $id, (int) $userID]);
    }

    public static function deleteById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM merits WHERE meritID=?");
        return $stmt->execute([(int) $id]);
    }

    public static function addStatusLog($meritID, $fromStatus, $toStatus, $changedBy, $changeNote, $changeSource) {
        try {
            $db = Database::connect();
            $allowedStatus = ['pending', 'approved', 'rejected'];
            if ($toStatus === null || !in_array($toStatus, $allowedStatus, true)) {
                return false;
            }
            if ($fromStatus !== null && !in_array($fromStatus, $allowedStatus, true)) {
                return false;
            }

            $changeSource = trim((string) $changeSource);
            if ($changeSource === '') {
                $changeSource = 'system';
            }

            $note = trim((string) $changeNote);
            $note = $note !== '' ? $note : null;

            $stmt = $db->prepare(
                "INSERT INTO merit_status_logs (meritID, from_status, to_status, changed_by, change_note, change_source)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                (int) $meritID,
                $fromStatus,
                $toStatus,
                $changedBy !== null ? (int) $changedBy : null,
                $note,
                $changeSource,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getStatusLogsByMerit($meritID) {
        try {
            $db = Database::connect();
            $stmt = $db->prepare(
                "SELECT l.*,
                        actor.name AS changedByName,
                        actor.student_id AS changedByStudentId
                 FROM merit_status_logs l
                 LEFT JOIN users actor ON actor.userID = l.changed_by
                 WHERE l.meritID = ?
                 ORDER BY l.logID DESC"
            );
            $stmt->execute([(int) $meritID]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public static function getRecentStatusLogs($limit = 20) {
        try {
            $db = Database::connect();
            $limit = (int) $limit;
            if ($limit <= 0) {
                $limit = 20;
            }
            if ($limit > 100) {
                $limit = 100;
            }

            $sql = "SELECT l.*,
                           m.activityName,
                           owner.name AS ownerName,
                           owner.student_id AS ownerStudentId,
                           actor.name AS changedByName,
                           actor.student_id AS changedByStudentId
                    FROM merit_status_logs l
                    JOIN merits m ON m.meritID = l.meritID
                    JOIN users owner ON owner.userID = m.userID
                    LEFT JOIN users actor ON actor.userID = l.changed_by
                    ORDER BY l.created_at DESC, l.logID DESC
                    LIMIT {$limit}";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

?>
