<?php

class MeritCertificate {

    public static function getApprovedMeritHours($userID) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT COALESCE(SUM(hours), 0) FROM merits WHERE userID = ? AND status = 'approved'");
        $stmt->execute([$userID]);
        return (int) round((float) ($stmt->fetchColumn() ?? 0));
    }

    public static function countByUser($userID) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT COUNT(*) FROM merit_certificates WHERE userID = ?");
        $stmt->execute([$userID]);
        return (int) ($stmt->fetchColumn() ?? 0);
    }

    public static function getByUser($userID) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT mc.*, u.name AS studentName, u.student_id AS studentId
             FROM merit_certificates mc
             JOIN users u ON u.userID = mc.userID
             WHERE mc.userID = ?
             ORDER BY mc.milestone_hours DESC, mc.issued_at DESC"
        );
        $stmt->execute([$userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLatestByUser($userID) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM merit_certificates
             WHERE userID = ?
             ORDER BY milestone_hours DESC, issued_at DESC
             LIMIT 1"
        );
        $stmt->execute([$userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findById($id) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT mc.*, u.name AS studentName, u.student_id AS studentId, u.email AS studentEmail
             FROM merit_certificates mc
             JOIN users u ON u.userID = mc.userID
             WHERE mc.certificateID = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByIdForUser($id, $userID) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT mc.*, u.name AS studentName, u.student_id AS studentId, u.email AS studentEmail
             FROM merit_certificates mc
             JOIN users u ON u.userID = mc.userID
             WHERE mc.certificateID = ? AND mc.userID = ?"
        );
        $stmt->execute([$id, $userID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByCode($code) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT mc.*, u.name AS studentName, u.student_id AS studentId, u.email AS studentEmail
             FROM merit_certificates mc
             JOIN users u ON u.userID = mc.userID
             WHERE mc.certificate_code = ?"
        );
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function issueEligibleForUser($userID, $issuedBy = null, $sourceMeritID = null) {
        $db = Database::connect();
        $approvedHours = self::getApprovedMeritHours($userID);
        $maxMilestone = (int) floor($approvedHours / 100) * 100;

        if ($maxMilestone < 100) {
            return [];
        }

        $stmt = $db->prepare("SELECT milestone_hours FROM merit_certificates WHERE userID = ?");
        $stmt->execute([$userID]);
        $issuedMilestones = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        $issuedLookup = array_fill_keys($issuedMilestones, true);

        $newCertificates = [];

        for ($milestone = 100; $milestone <= $maxMilestone; $milestone += 100) {
            if (isset($issuedLookup[$milestone])) {
                continue;
            }

            $certificateCode = self::generateUniqueCode($db);
            $insert = $db->prepare(
                "INSERT INTO merit_certificates
                 (userID, milestone_hours, approved_hours_snapshot, certificate_code, source_meritID, issued_by)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            try {
                $insert->execute([
                    $userID,
                    $milestone,
                    $approvedHours,
                    $certificateCode,
                    $sourceMeritID,
                    $issuedBy,
                ]);
            } catch (PDOException $e) {
                // Unique milestone constraint can race in concurrent requests.
                if ($e->getCode() === '23000') {
                    continue;
                }
                throw $e;
            }

            $newCertificates[] = [
                'certificateID' => (int) $db->lastInsertId(),
                'milestone_hours' => $milestone,
                'approved_hours_snapshot' => $approvedHours,
                'certificate_code' => $certificateCode,
            ];
        }

        return $newCertificates;
    }

    private static function generateUniqueCode($db) {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                $token = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            } catch (Exception $e) {
                $token = strtoupper(substr(md5(uniqid('', true)), 0, 8));
            }

            $code = 'MC-' . date('Y') . '-' . $token;

            $check = $db->prepare("SELECT COUNT(*) FROM merit_certificates WHERE certificate_code = ?");
            $check->execute([$code]);
            if ((int) $check->fetchColumn() === 0) {
                return $code;
            }
        }

        return 'MC-' . date('YmdHis') . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 8));
    }
}

?>
