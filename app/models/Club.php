<?php

class Club {

    public static function getByUser($userID, $search = null, $sort = null, $status = null) {
        $db = Database::connect();

        $allowedSort = [
            'clubID' => 'clubID',
            'clubName' => 'clubName',
            'role' => 'role',
            'request_type' => 'request_type',
            'startDate' => 'startDate',
            'endDate' => 'endDate',
            'status' => 'status',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'clubID';

        $sql = "SELECT * FROM clubs WHERE userID = ?";
        $params = [$userID];

        if ($search !== null && $search !== '') {
            $term = '%' . $search . '%';
            $sql .= " AND (clubName LIKE ? OR role LIKE ? OR roleDescription LIKE ? OR request_type LIKE ?)";
            array_push($params, $term, $term, $term, $term);
        }

        $allowedStatus = ['pending', 'approved', 'rejected'];
        if ($status !== null && in_array($status, $allowedStatus, true)) {
            $sql .= " AND status = ?";
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
            'clubID' => 'c.clubID',
            'clubName' => 'c.clubName',
            'role' => 'c.role',
            'request_type' => 'c.request_type',
            'startDate' => 'c.startDate',
            'endDate' => 'c.endDate',
            'student' => 'u.name',
            'student_id' => 'u.student_id',
            'status' => 'c.status',
        ];

        $sortColumn = $allowedSort[$sort] ?? 'c.clubID';

        $sql = "SELECT c.*, u.name AS userName, u.email AS userEmail, u.student_id AS studentId
                FROM clubs c
                JOIN users u ON u.userID = c.userID";
        $params = [];
        $conditions = [];

        if ($search !== null && $search !== '') {
            $t = '%' . $search . '%';
            $conditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.student_id LIKE ? OR c.clubName LIKE ? OR c.role LIKE ? OR c.roleDescription LIKE ? OR c.request_type LIKE ?)";
            $params = array_merge($params, [$t, $t, $t, $t, $t, $t, $t]);
        }

        $allowedStatus = ['pending', 'approved', 'rejected'];
        if ($status !== null && in_array($status, $allowedStatus, true)) {
            $conditions[] = "c.status = ?";
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

    public static function create($userID, $clubName, $role, $roleDescription, $requestType, $startDate, $endDate, $status = 'pending', $reviewedBy = null, $reviewNote = null, $reviewedAt = null, $evidencePath = null) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO clubs (userID, clubName, role, roleDescription, request_type, startDate, endDate, status, reviewed_by, review_note, reviewed_at, evidence_path)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$userID, $clubName, $role, $roleDescription, $requestType, $startDate, $endDate, $status, $reviewedBy, $reviewNote, $reviewedAt, $evidencePath]);
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

    public static function update($id, $userID, $clubName, $role, $roleDescription, $requestType, $startDate, $endDate, $evidencePath = null, $replaceEvidence = false) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE clubs
             SET clubName = ?, role = ?, roleDescription = ?, request_type = ?, startDate = ?, endDate = ?,
                 evidence_path = CASE WHEN ? = 1 THEN ? ELSE evidence_path END,
                 status = 'pending', reviewed_at = NULL, reviewed_by = NULL, review_note = NULL
             WHERE clubID = ? AND userID = ? AND status IN ('pending', 'rejected')"
        );
        return $stmt->execute([
            $clubName,
            $role,
            $roleDescription,
            $requestType,
            $startDate,
            $endDate,
            $replaceEvidence ? 1 : 0,
            $evidencePath,
            $id,
            $userID,
        ]);
    }

    public static function updateById($id, $clubName, $role, $roleDescription, $requestType, $startDate, $endDate) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE clubs
             SET clubName = ?, role = ?, roleDescription = ?, request_type = ?, startDate = ?, endDate = ?
             WHERE clubID = ?"
        );
        return $stmt->execute([$clubName, $role, $roleDescription, $requestType, $startDate, $endDate, $id]);
    }

    public static function updateStatusById($id, $status, $reviewedBy, $reviewNote) {
        $db = Database::connect();

        $allowedStatus = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $allowedStatus, true)) {
            return false;
        }

        if ($status === 'pending') {
            $stmt = $db->prepare(
                "UPDATE clubs
                 SET status = ?, reviewed_at = NULL, reviewed_by = NULL, review_note = NULL
                 WHERE clubID = ?"
            );
            return $stmt->execute([$status, $id]);
        }

        $reviewedAt = date('Y-m-d H:i:s');
        $stmt = $db->prepare(
            "UPDATE clubs
             SET status = ?, reviewed_at = ?, reviewed_by = ?, review_note = ?
             WHERE clubID = ?"
        );
        return $stmt->execute([$status, $reviewedAt, $reviewedBy, $reviewNote, $id]);
    }

    public static function delete($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM clubs WHERE clubID = ? AND userID = ? AND status IN ('pending', 'rejected')");
        return $stmt->execute([$id, $userID]);
    }

    public static function deleteById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM clubs WHERE clubID = ?");
        return $stmt->execute([$id]);
    }

    public static function getApprovedTimelineByUser($userID) {
        $db = Database::connect();

        $stmt = $db->prepare(
            "SELECT clubName, role, roleDescription, startDate, endDate, status
             FROM clubs
             WHERE userID = ? AND status = 'approved'
             ORDER BY clubName ASC, startDate ASC, clubID ASC"
        );

        $stmt->execute([$userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function closePreviousApprovedRole($userID, $clubName, $newStartDate, $excludeClubID = null) {
        $newStartDate = trim((string) $newStartDate);
        if ($newStartDate === '' || $newStartDate === '0000-00-00') {
            return false;
        }

        $timestamp = strtotime($newStartDate);
        if ($timestamp === false) {
            return false;
        }

        $db = Database::connect();
        $previousEndDate = date('Y-m-d', strtotime('-1 day', $timestamp));

        $sql = "UPDATE clubs
                SET endDate = ?
                WHERE userID = ?
                  AND clubName = ?
                  AND status = 'approved'
                  AND startDate < ?
                  AND (
                        endDate IS NULL
                        OR endDate = ''
                        OR endDate = '0000-00-00'
                        OR endDate >= ?
                  )";

        $params = [
            $previousEndDate,
            $userID,
            $clubName,
            $newStartDate,
            $newStartDate,
        ];

        if ($excludeClubID !== null) {
            $sql .= " AND clubID <> ?";
            $params[] = $excludeClubID;
        }

        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function hasPendingRequest($userID, $clubName, $requestType = null, $excludeClubID = null) {
        $db = Database::connect();
        $sql = "SELECT COUNT(*) FROM clubs WHERE userID = ? AND clubName = ? AND status = 'pending'";
        $params = [$userID, $clubName];

        if ($requestType !== null) {
            $sql .= " AND request_type = ?";
            $params[] = $requestType;
        }

        if ($excludeClubID !== null) {
            $sql .= " AND clubID <> ?";
            $params[] = $excludeClubID;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return ((int) ($stmt->fetchColumn() ?? 0)) > 0;
    }

    public static function hasActiveApprovedMembership($userID, $clubName, $excludeClubID = null) {
        $db = Database::connect();
        $today = date('Y-m-d');

        $sql = "SELECT COUNT(*) FROM clubs
                WHERE userID = ?
                  AND clubName = ?
                  AND status = 'approved'
                  AND (
                    startDate IS NULL
                    OR startDate = ''
                    OR startDate = '0000-00-00'
                    OR startDate <= ?
                  )
                  AND (
                    endDate IS NULL
                    OR endDate = ''
                    OR endDate = '0000-00-00'
                    OR endDate >= ?
                  )";
        $params = [$userID, $clubName, $today, $today];

        if ($excludeClubID !== null) {
            $sql .= " AND clubID <> ?";
            $params[] = $excludeClubID;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return ((int) ($stmt->fetchColumn() ?? 0)) > 0;
    }

    public static function getApprovedActiveClubCatalogByUser($userID) {
        $db = Database::connect();
        $today = date('Y-m-d');

        $stmt = $db->prepare(
            "SELECT DISTINCT cc.clubCatalogID, cc.clubName
             FROM clubs c
             JOIN club_catalog cc ON cc.clubName = c.clubName
             WHERE c.userID = ?
               AND c.status = 'approved'
               AND cc.is_active = 1
               AND (
                    c.startDate IS NULL
                    OR c.startDate = ''
                    OR c.startDate = '0000-00-00'
                    OR c.startDate <= ?
               )
               AND (
                    c.endDate IS NULL
                    OR c.endDate = ''
                    OR c.endDate = '0000-00-00'
                    OR c.endDate >= ?
               )
             ORDER BY cc.clubName ASC"
        );

        $stmt->execute([(int) $userID, $today, $today]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
