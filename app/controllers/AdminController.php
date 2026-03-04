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

        $stmt = $db->query("SELECT COUNT(*) FROM users");
        $userCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->query("SELECT COUNT(*) FROM merits");
        $meritCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->query("SELECT COUNT(*) FROM events");
        $eventCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->query("SELECT COUNT(*) FROM clubs");
        $clubCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->query("SELECT COUNT(*) FROM achievements");
        $achievementCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->query(
            "SELECT u.userID, u.name, u.email,
                    (SELECT COUNT(*) FROM merits m WHERE m.userID = u.userID) AS meritCount,
                    (SELECT COUNT(*) FROM events e WHERE e.userID = u.userID) AS eventCount,
                    (SELECT COUNT(*) FROM clubs c WHERE c.userID = u.userID) AS clubCount,
                    (SELECT COUNT(*) FROM achievements a WHERE a.userID = u.userID) AS achievementCount
             FROM users u
             ORDER BY u.name ASC"
        );
        $userSummaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->query(
            "SELECT COUNT(DISTINCT userID) FROM (
                SELECT userID FROM merits
                UNION
                SELECT userID FROM events
                UNION
                SELECT userID FROM clubs
                UNION
                SELECT userID FROM achievements
            ) AS activityUsers"
        );
        $activeStudentCount = $stmt->fetchColumn() ?? 0;

        $merits = $db->query(
            "SELECT m.*, u.name AS userName
             FROM merits m
             JOIN users u ON u.userID = m.userID
             ORDER BY m.meritID DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $events = $db->query(
            "SELECT e.*, u.name AS userName
             FROM events e
             JOIN users u ON u.userID = e.userID
             ORDER BY e.eventID DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $clubs = $db->query(
            "SELECT c.*, u.name AS userName
             FROM clubs c
             JOIN users u ON u.userID = c.userID
             ORDER BY c.clubID DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $achievements = $db->query(
            "SELECT a.*, u.name AS userName
             FROM achievements a
             JOIN users u ON u.userID = a.userID
             ORDER BY a.achievementID DESC"
        )->fetchAll(PDO::FETCH_ASSOC);

        require "../app/views/admin/index.php";
    }
}

?>
