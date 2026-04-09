<?php

class AdminController {

    private function checkAdmin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit();
        }

        if (empty($_SESSION['isAdmin'])) {
            header("Location: index.php?url=dashboard/index");
            exit();
        }
    }

    public function index() {

        $this->checkAdmin();

        $db = Database::connect();

        $userCount = (int) ($db->query("SELECT COUNT(*) FROM users WHERE isAdmin = 0")->fetchColumn() ?? 0);

        $meritCount = (int) ($db->query("SELECT COUNT(*) FROM merits")->fetchColumn() ?? 0);
        $eventCount = (int) ($db->query("SELECT COUNT(*) FROM events")->fetchColumn() ?? 0);
        $clubCount = (int) ($db->query("SELECT COUNT(*) FROM clubs")->fetchColumn() ?? 0);
        $achievementCount = (int) ($db->query("SELECT COUNT(*) FROM achievements")->fetchColumn() ?? 0);

        $pendingMeritCount = (int) ($db->query("SELECT COUNT(*) FROM merits WHERE status = 'pending'")->fetchColumn() ?? 0);
        $pendingEventCount = (int) ($db->query("SELECT COUNT(*) FROM events WHERE status = 'pending'")->fetchColumn() ?? 0);
        $pendingClubCount = (int) ($db->query("SELECT COUNT(*) FROM clubs WHERE status = 'pending'")->fetchColumn() ?? 0);
        $pendingAchievementCount = (int) ($db->query("SELECT COUNT(*) FROM achievements WHERE status = 'pending'")->fetchColumn() ?? 0);

        $approvedMeritCount = (int) ($db->query("SELECT COUNT(*) FROM merits WHERE status = 'approved'")->fetchColumn() ?? 0);
        $approvedEventCount = (int) ($db->query("SELECT COUNT(*) FROM events WHERE status = 'approved'")->fetchColumn() ?? 0);
        $approvedClubCount = (int) ($db->query("SELECT COUNT(*) FROM clubs WHERE status = 'approved'")->fetchColumn() ?? 0);
        $approvedAchievementCount = (int) ($db->query("SELECT COUNT(*) FROM achievements WHERE status = 'approved'")->fetchColumn() ?? 0);

        $rejectedMeritCount = (int) ($db->query("SELECT COUNT(*) FROM merits WHERE status = 'rejected'")->fetchColumn() ?? 0);
        $rejectedEventCount = (int) ($db->query("SELECT COUNT(*) FROM events WHERE status = 'rejected'")->fetchColumn() ?? 0);
        $rejectedClubCount = (int) ($db->query("SELECT COUNT(*) FROM clubs WHERE status = 'rejected'")->fetchColumn() ?? 0);
        $rejectedAchievementCount = (int) ($db->query("SELECT COUNT(*) FROM achievements WHERE status = 'rejected'")->fetchColumn() ?? 0);

        $totalPendingReviews = $pendingMeritCount + $pendingEventCount + $pendingClubCount + $pendingAchievementCount;
        $totalApproved = $approvedMeritCount + $approvedEventCount + $approvedClubCount + $approvedAchievementCount;
        $totalRejected = $rejectedMeritCount + $rejectedEventCount + $rejectedClubCount + $rejectedAchievementCount;
        $totalReviewed = $totalApproved + $totalRejected;

        $totalRecords = $meritCount + $eventCount + $clubCount + $achievementCount;
        $approvalRate = $totalReviewed > 0 ? round(($totalApproved / $totalReviewed) * 100, 1) : 0;

        $totalMeritHours = (float) ($db->query("SELECT COALESCE(SUM(hours), 0) FROM merits")->fetchColumn() ?? 0);
        $newUsers30d = (int) ($db->query("SELECT COUNT(*) FROM users WHERE isAdmin = 0 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn() ?? 0);

        $activeStudentCount = (int) ($db->query(
            "SELECT COUNT(DISTINCT userID) FROM (
                SELECT userID FROM merits
                UNION
                SELECT userID FROM events
                UNION
                SELECT userID FROM clubs
                UNION
                SELECT userID FROM achievements
            ) AS activityUsers"
        )->fetchColumn() ?? 0);

        $moduleSummaries = [
            [
                'name' => 'Merits',
                'pending' => $pendingMeritCount,
                'approved' => $approvedMeritCount,
                'rejected' => $rejectedMeritCount,
                'total' => $meritCount,
                'url' => 'index.php?url=merit/index',
            ],
            [
                'name' => 'Events',
                'pending' => $pendingEventCount,
                'approved' => $approvedEventCount,
                'rejected' => $rejectedEventCount,
                'total' => $eventCount,
                'url' => 'index.php?url=event/index',
            ],
            [
                'name' => 'Clubs',
                'pending' => $pendingClubCount,
                'approved' => $approvedClubCount,
                'rejected' => $rejectedClubCount,
                'total' => $clubCount,
                'url' => 'index.php?url=club/index',
            ],
            [
                'name' => 'Achievements',
                'pending' => $pendingAchievementCount,
                'approved' => $approvedAchievementCount,
                'rejected' => $rejectedAchievementCount,
                'total' => $achievementCount,
                'url' => 'index.php?url=achievement/index',
            ],
        ];

        $pendingQueue = $db->query(
            "SELECT * FROM (
                SELECT 'Merit' AS module, m.meritID AS recordID, m.activityName AS recordTitle,
                       m.submitted_at AS submittedAt, u.name AS studentName, u.student_id AS studentId,
                       'index.php?url=merit/index' AS listUrl
                FROM merits m
                JOIN users u ON u.userID = m.userID
                WHERE m.status = 'pending'

                UNION ALL

                SELECT 'Event' AS module, e.eventID AS recordID, e.eventTitle AS recordTitle,
                       e.submitted_at AS submittedAt, u.name AS studentName, u.student_id AS studentId,
                       'index.php?url=event/index' AS listUrl
                FROM events e
                JOIN users u ON u.userID = e.userID
                WHERE e.status = 'pending'

                UNION ALL

                SELECT 'Club' AS module, c.clubID AS recordID, c.clubName AS recordTitle,
                       c.submitted_at AS submittedAt, u.name AS studentName, u.student_id AS studentId,
                       'index.php?url=club/index' AS listUrl
                FROM clubs c
                JOIN users u ON u.userID = c.userID
                WHERE c.status = 'pending'

                UNION ALL

                SELECT 'Achievement' AS module, a.achievementID AS recordID, a.title AS recordTitle,
                       a.submitted_at AS submittedAt, u.name AS studentName, u.student_id AS studentId,
                       'index.php?url=achievement/index' AS listUrl
                FROM achievements a
                JOIN users u ON u.userID = a.userID
                WHERE a.status = 'pending'
            ) AS pendingRecords
            ORDER BY submittedAt ASC
            LIMIT 12"
        )->fetchAll(PDO::FETCH_ASSOC);

        $studentSummaries = $db->query(
            "SELECT u.userID, u.student_id, u.name, u.email,
                    (SELECT COUNT(*) FROM merits m WHERE m.userID = u.userID) AS meritCount,
                    (SELECT COUNT(*) FROM events e WHERE e.userID = u.userID) AS eventCount,
                    (SELECT COUNT(*) FROM clubs c WHERE c.userID = u.userID) AS clubCount,
                    (SELECT COUNT(*) FROM achievements a WHERE a.userID = u.userID) AS achievementCount
             FROM users u
             WHERE COALESCE(u.isAdmin, 0) = 0
             ORDER BY (
                    (SELECT COUNT(*) FROM merits m WHERE m.userID = u.userID) +
                    (SELECT COUNT(*) FROM events e WHERE e.userID = u.userID) +
                    (SELECT COUNT(*) FROM clubs c WHERE c.userID = u.userID) +
                    (SELECT COUNT(*) FROM achievements a WHERE a.userID = u.userID)
             ) DESC, u.name ASC
             LIMIT 8"
        )->fetchAll(PDO::FETCH_ASSOC);

        require "../app/views/admin/index.php";
    }
}

?>
