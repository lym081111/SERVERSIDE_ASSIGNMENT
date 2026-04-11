<?php

class DashboardController {

    private function requireStudentSession() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit();
        }

        if (!empty($_SESSION['isAdmin'])) {
            header("Location: index.php?url=admin/index");
            exit();
        }
    }

    public function index() {
        $this->requireStudentSession();

        $userID = (int) $_SESSION['user_id'];

        $db = Database::connect();

        $stmt = $db->prepare("SELECT COUNT(*) FROM events WHERE userID = ?");
        $stmt->execute([$userID]);
        $eventCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT COUNT(*) FROM events WHERE userID = ? AND status = 'pending'");
        $stmt->execute([$userID]);
        $pendingEventCount = (int) ($stmt->fetchColumn() ?? 0);

        $stmt = $db->prepare("SELECT MAX(eventDate) FROM events WHERE userID = ?");
        $stmt->execute([$userID]);
        $latestEventDate = $stmt->fetchColumn() ?: null;

        $stmt = $db->prepare("SELECT COUNT(*) FROM clubs WHERE userID = ?");
        $stmt->execute([$userID]);
        $clubCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT COUNT(*) FROM clubs WHERE userID = ? AND status = 'pending'");
        $stmt->execute([$userID]);
        $pendingClubCount = (int) ($stmt->fetchColumn() ?? 0);

        $stmt = $db->prepare("SELECT COUNT(*) FROM clubs WHERE userID = ? AND (endDate IS NULL OR endDate = '')");
        $stmt->execute([$userID]);
        $activeClubCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT MAX(startDate) FROM clubs WHERE userID = ?");
        $stmt->execute([$userID]);
        $latestClubStart = $stmt->fetchColumn() ?: null;

        $stmt = $db->prepare("SELECT SUM(hours) FROM merits WHERE userID = ?");
        $stmt->execute([$userID]);
        $meritHours = $stmt->fetchColumn() ?? 0;

        $approvedMeritHours = MeritCertificate::getApprovedMeritHours($userID);
        $meritCertificateCount = MeritCertificate::countByUser($userID);
        $latestMeritCertificate = MeritCertificate::getLatestByUser($userID);
        $nextMeritCertificateMilestone = ((int) floor(((int) $approvedMeritHours) / 100) + 1) * 100;
        $hoursToNextCertificate = max(0, $nextMeritCertificateMilestone - (int) $approvedMeritHours);

        $stmt = $db->prepare("SELECT COUNT(*) FROM merits WHERE userID = ?");
        $stmt->execute([$userID]);
        $meritCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT COUNT(*) FROM merits WHERE userID = ? AND status = 'pending'");
        $stmt->execute([$userID]);
        $pendingMeritCount = (int) ($stmt->fetchColumn() ?? 0);

        $stmt = $db->prepare("SELECT MAX(COALESCE(dateTo, dateFrom)) FROM merits WHERE userID = ?");
        $stmt->execute([$userID]);
        $latestMeritDate = $stmt->fetchColumn() ?: null;

        $stmt = $db->prepare("SELECT COUNT(*) FROM achievements WHERE userID = ?");
        $stmt->execute([$userID]);
        $achievementCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT COUNT(*) FROM achievements WHERE userID = ? AND status = 'pending'");
        $stmt->execute([$userID]);
        $pendingAchievementCount = (int) ($stmt->fetchColumn() ?? 0);

        $stmt = $db->prepare("SELECT MAX(dateReceived) FROM achievements WHERE userID = ?");
        $stmt->execute([$userID]);
        $latestAchievementDate = $stmt->fetchColumn() ?: null;

        require "../app/views/dashboard/index.php";
    }

    public function transcript() {
        $this->requireStudentSession();

        $userID = (int) $_SESSION['user_id'];
        $db = Database::connect();

        // Keep sidebar snapshot consistent with dashboard/index values.
        $stmt = $db->prepare("SELECT SUM(hours) FROM merits WHERE userID = ?");
        $stmt->execute([$userID]);
        $meritHours = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT COUNT(*) FROM merits WHERE userID = ?");
        $stmt->execute([$userID]);
        $meritCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT COUNT(*) FROM events WHERE userID = ?");
        $stmt->execute([$userID]);
        $eventCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT COUNT(*) FROM clubs WHERE userID = ?");
        $stmt->execute([$userID]);
        $clubCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT COUNT(*) FROM clubs WHERE userID = ? AND (endDate IS NULL OR endDate = '')");
        $stmt->execute([$userID]);
        $activeClubCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT COUNT(*) FROM achievements WHERE userID = ?");
        $stmt->execute([$userID]);
        $achievementCount = $stmt->fetchColumn() ?? 0;

        $studentStmt = $db->prepare("SELECT userID, student_id, name, email, created_at FROM users WHERE userID = ?");
        $studentStmt->execute([$userID]);
        $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            $_SESSION['error'] = "Student profile not found.";
            header("Location: index.php?url=dashboard/index");
            exit();
        }

        $approvedMerits = Merit::getByUser($userID, null, 'dateFrom', 'approved');
        $approvedEvents = Event::getByUser($userID, null, 'eventDate', 'approved');
        $approvedClubs = Club::getByUser($userID, null, 'startDate', 'approved');
        $approvedAchievements = Achievement::getByUser($userID, null, 'dateReceived', 'approved');

        $approvedMeritHours = 0.0;
        foreach ($approvedMerits as $row) {
            $approvedMeritHours += (float) ($row['hours'] ?? 0);
        }

        $summary = [
            'merits' => count($approvedMerits),
            'events' => count($approvedEvents),
            'clubs' => count($approvedClubs),
            'achievements' => count($approvedAchievements),
            'merit_hours' => $approvedMeritHours,
        ];
        $summary['total_records'] = (int) ($summary['merits'] + $summary['events'] + $summary['clubs'] + $summary['achievements']);

        $timelineDates = [];
        foreach ($approvedMerits as $row) {
            $from = trim((string) ($row['dateFrom'] ?? ''));
            $to = trim((string) ($row['dateTo'] ?? ''));
            if ($from !== '' && $from !== '0000-00-00') {
                $timelineDates[] = $from;
            }
            if ($to !== '' && $to !== '0000-00-00') {
                $timelineDates[] = $to;
            }
        }
        foreach ($approvedEvents as $row) {
            $date = trim((string) ($row['eventDate'] ?? ''));
            if ($date !== '' && $date !== '0000-00-00') {
                $timelineDates[] = $date;
            }
        }
        foreach ($approvedClubs as $row) {
            $start = trim((string) ($row['startDate'] ?? ''));
            $end = trim((string) ($row['endDate'] ?? ''));
            if ($start !== '' && $start !== '0000-00-00') {
                $timelineDates[] = $start;
            }
            if ($end !== '' && $end !== '0000-00-00') {
                $timelineDates[] = $end;
            }
        }
        foreach ($approvedAchievements as $row) {
            $date = trim((string) ($row['dateReceived'] ?? ''));
            if ($date !== '' && $date !== '0000-00-00') {
                $timelineDates[] = $date;
            }
        }

        $activityPeriodFrom = null;
        $activityPeriodTo = null;
        if (!empty($timelineDates)) {
            sort($timelineDates);
            $activityPeriodFrom = $timelineDates[0];
            $activityPeriodTo = $timelineDates[count($timelineDates) - 1];
        }

        $issuedAt = date('Y-m-d H:i:s');
        $transcriptNumber = 'UTAR-COCU-' . date('Y') . '-' . str_pad((string) $userID, 6, '0', STR_PAD_LEFT);

        require "../app/views/dashboard/transcript.php";
    }
}
