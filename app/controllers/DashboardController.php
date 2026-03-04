<?php

class DashboardController {

    public function index() {

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit();
        }

        if (!empty($_SESSION['isAdmin'])) {
            header("Location: index.php?url=admin/index");
            exit();
        }

        $userID = (int) $_SESSION['user_id'];

        $db = Database::connect();

        $stmt = $db->prepare("SELECT COUNT(*) FROM events WHERE userID = ?");
        $stmt->execute([$userID]);
        $eventCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT MAX(eventDate) FROM events WHERE userID = ?");
        $stmt->execute([$userID]);
        $latestEventDate = $stmt->fetchColumn() ?: null;

        $stmt = $db->prepare("SELECT COUNT(*) FROM clubs WHERE userID = ?");
        $stmt->execute([$userID]);
        $clubCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT COUNT(*) FROM clubs WHERE userID = ? AND (endDate IS NULL OR endDate = '')");
        $stmt->execute([$userID]);
        $activeClubCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT MAX(startDate) FROM clubs WHERE userID = ?");
        $stmt->execute([$userID]);
        $latestClubStart = $stmt->fetchColumn() ?: null;

        $stmt = $db->prepare("SELECT SUM(hours) FROM merits WHERE userID = ?");
        $stmt->execute([$userID]);
        $meritHours = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT COUNT(*) FROM merits WHERE userID = ?");
        $stmt->execute([$userID]);
        $meritCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT MAX(COALESCE(dateTo, dateFrom)) FROM merits WHERE userID = ?");
        $stmt->execute([$userID]);
        $latestMeritDate = $stmt->fetchColumn() ?: null;

        $stmt = $db->prepare("SELECT COUNT(*) FROM achievements WHERE userID = ?");
        $stmt->execute([$userID]);
        $achievementCount = $stmt->fetchColumn() ?? 0;

        $stmt = $db->prepare("SELECT MAX(dateReceived) FROM achievements WHERE userID = ?");
        $stmt->execute([$userID]);
        $latestAchievementDate = $stmt->fetchColumn() ?: null;

        require "../app/views/dashboard/index.php";
    }
}
