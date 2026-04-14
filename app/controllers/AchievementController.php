<?php

class AchievementController {

    private function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit();
        }
    }

    private function isAdmin() {
        return !empty($_SESSION['isAdmin']);
    }

    private function checkAdmin() {
        $this->checkLogin();
        if (!$this->isAdmin()) {
            header("Location: index.php?url=dashboard/index");
            exit();
        }
    }

    private function redirectWithError($message) {
        $_SESSION['error'] = $message;
        header("Location: index.php?url=achievement/index");
        exit();
    }

    private function normalizeAchievementLevel($value) {
        $level = trim((string) $value);
        $allowed = ['Faculty', 'University', 'National', 'International'];
        return in_array($level, $allowed, true) ? $level : 'Faculty';
    }

    private function getEncouragementMessage($totalAchievements) {
        $totalAchievements = (int) $totalAchievements;

        if ($totalAchievements >= 10) {
            return "Excellent work! You already have {$totalAchievements} achievements and an outstanding co-curricular profile.";
        }

        if ($totalAchievements >= 5) {
            return "Well done! You already have {$totalAchievements} achievements. Keep up the momentum and aim for 10 achievements.";
        }

        if ($totalAchievements >= 3) {
            return "Great job! You already have {$totalAchievements} achievements. Stay active and work towards your next milestone of 5 achievements.";
        }

        if ($totalAchievements > 0) {
            return "Good start! You already have {$totalAchievements} achievement" . ($totalAchievements > 1 ? "s" : "") . ". Keep participating in competitions and activities to earn more achievements.";
        }

        return "Start joining activities and competitions to earn your first achievement.";
    }

    public function index() {

        $this->checkLogin();

        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : null;
        $status = isset($_GET['status']) ? (string) $_GET['status'] : null;

        if ($this->isAdmin()) {
            $achievements = Achievement::getAllWithUser($search, $sort, $status);
            require "../app/views/admin/achievement_index.php";
            return;
        }

        $userID = (int) $_SESSION['user_id'];
        $achievements = Achievement::getByUser($userID, $search, $sort, $status);

        require "../app/views/achievement/index.php";
    }

    public function summary() {
        $this->checkLogin();

        if ($this->isAdmin()) {
            header("Location: index.php?url=admin/index");
            exit();
        }

        $userID = (int) $_SESSION['user_id'];
        $achievements = Achievement::getByUser($userID, null, 'dateReceived', 'approved');
        $totalAchievements = is_array($achievements) ? count($achievements) : 0;

        $levelCounts = [
            'Faculty' => 0,
            'University' => 0,
            'National' => 0,
            'International' => 0,
        ];

        if (is_array($achievements)) {
            foreach ($achievements as $achievement) {
                $level = trim((string) ($achievement['achievementLevel'] ?? 'Faculty'));
                if (!isset($levelCounts[$level])) {
                    $levelCounts[$level] = 0;
                }
                $levelCounts[$level]++;
            }
        }

        $encouragementMessage = $this->getEncouragementMessage($totalAchievements);

        require "../app/views/achievement/summary.php";
    }

    public function create() {

        $this->checkLogin();

        $error = null;

        if ($this->isAdmin()) {
            $students = User::getAll();
            $approvedEvents = Event::getApprovedAllWithUser();
        } else {
            $userID = (int) $_SESSION['user_id'];
            $approvedEvents = Event::getApprovedByUser($userID);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $title = trim((string) ($_POST['title'] ?? ''));
            if ($title === '') {
                $error = "Achievement title is required.";
            }

            $eventID = isset($_POST['eventID']) ? (int) $_POST['eventID'] : 0;
            if ($error === null && $eventID <= 0) {
                $error = "Please select an approved event.";
            }

            $targetUserID = (int) $_SESSION['user_id'];
            if ($this->isAdmin() && $error === null) {
                $selection = User::resolveStudentSelectionForAdmin(
                    $_POST['studentID'] ?? 0,
                    $_POST['studentEmail'] ?? '',
                    $_POST['studentId'] ?? ''
                );
                $targetUserID = (int) ($selection['userID'] ?? 0);
                if ($targetUserID <= 0) {
                    $error = (string) ($selection['error'] ?? "Please select a valid student.");
                }
            }

            $event = null;
            if ($error === null) {
                $event = Event::findApprovedByIdForUser($eventID, $targetUserID);
                if (!$event) {
                    $error = "Selected event is not approved for this student.";
                }
            }

            $achievementLevel = $this->normalizeAchievementLevel($_POST['achievementLevel'] ?? 'Faculty');
            $category = trim((string) ($_POST['category'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));

            $dateReceived = trim((string) ($_POST['dateReceived'] ?? ''));
            $dateReceived = ($dateReceived === '' || $dateReceived === '0000-00-00') ? '' : $dateReceived;
            if ($error === null && $dateReceived === '') {
                $error = "Date received is required.";
            }

            $evidencePath = null;
            if ($error === null) {
                $upload = EvidenceUpload::uploadFromRequest('evidence_file');
                if ($upload['error'] !== null) {
                    $error = $upload['error'];
                } else {
                    $evidencePath = $upload['path'];
                }
            }

            if ($error === null) {
                $status = $this->isAdmin() ? 'approved' : 'pending';
                $reviewedBy = $this->isAdmin() ? (int) $_SESSION['user_id'] : null;
                $reviewedAt = $this->isAdmin() ? date('Y-m-d H:i:s') : null;
                Achievement::create(
                    $targetUserID,
                    (int) $eventID,
                    $title,
                    $category,
                    $achievementLevel,
                    $dateReceived === '' ? null : $dateReceived,
                    $description,
                    $status,
                    $reviewedBy,
                    null,
                    $reviewedAt,
                    $evidencePath
                );

                $_SESSION['success'] = $this->isAdmin()
                    ? "Achievement record added and approved."
                    : "Achievement record submitted for review.";
                header("Location: index.php?url=achievement/index");
                exit();
            }
        }

        if ($this->isAdmin()) {
            require "../app/views/admin/achievement_create.php";
            return;
        }

        require "../app/views/achievement/create.php";
    }

    public function edit() {

        $this->checkLogin();

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php?url=achievement/index");
            exit();
        }

        $error = null;
        $userID = (int) ($_SESSION['user_id'] ?? 0);

        if ($this->isAdmin()) {
            $achievement = Achievement::findById($id);
            if (!$achievement) {
                $this->redirectWithError("Achievement record not found.");
            }
            $student = User::findById((int) ($achievement['userID'] ?? 0));
            $approvedEvents = Event::getApprovedByUser((int) ($achievement['userID'] ?? 0));
        } else {
            $achievement = Achievement::find($id, $userID);
            if (!$achievement) {
                $this->redirectWithError("Achievement record not found.");
            }
            if (($achievement['status'] ?? '') === 'approved') {
                $this->redirectWithError("Approved achievement records can only be edited by admin.");
            }
            $approvedEvents = Event::getApprovedByUser($userID);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $title = trim((string) ($_POST['title'] ?? ''));
            if ($title === '') {
                $error = "Achievement title is required.";
            }

            $eventID = isset($_POST['eventID']) ? (int) $_POST['eventID'] : 0;
            if ($error === null && $eventID <= 0) {
                $error = "Please select an approved event.";
            }

            $targetUserID = $this->isAdmin() ? (int) ($achievement['userID'] ?? 0) : $userID;
            if ($error === null) {
                $event = Event::findApprovedByIdForUser($eventID, $targetUserID);
                if (!$event) {
                    $error = "Selected event is not approved for this student.";
                }
            }

            $achievementLevel = $this->normalizeAchievementLevel($_POST['achievementLevel'] ?? 'Faculty');
            $category = trim((string) ($_POST['category'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));

            $dateReceived = trim((string) ($_POST['dateReceived'] ?? ''));
            $dateReceived = ($dateReceived === '' || $dateReceived === '0000-00-00') ? '' : $dateReceived;
            if ($error === null && $dateReceived === '') {
                $error = "Date received is required.";
            }

            if ($error === null) {
                if ($this->isAdmin()) {
                    Achievement::updateById(
                        $id,
                        (int) $eventID,
                        $title,
                        $category,
                        $achievementLevel,
                        $dateReceived === '' ? null : $dateReceived,
                        $description
                    );
                    if (isset($_POST['status'])) {
                        $status = (string) $_POST['status'];
                        $note = trim((string) ($_POST['review_note'] ?? ''));
                        Achievement::updateStatusById($id, $status, (int) $_SESSION['user_id'], $note);
                    }
                    $_SESSION['success'] = "Achievement record updated.";
                } else {
                    $upload = EvidenceUpload::uploadFromRequest('evidence_file');
                    if ($upload['error'] !== null) {
                        $error = $upload['error'];
                    } else {
                        Achievement::update(
                            $id,
                            $userID,
                            (int) $eventID,
                            $title,
                            $category,
                            $achievementLevel,
                            $dateReceived === '' ? null : $dateReceived,
                            $description,
                            $upload['path'],
                            $upload['uploaded']
                        );
                        $_SESSION['success'] = "Achievement record updated and resubmitted for review.";
                    }
                }

                if ($error === null) {
                    header("Location: index.php?url=achievement/index");
                    exit();
                }
            }
        }

        if ($this->isAdmin()) {
            require "../app/views/admin/achievement_edit.php";
            return;
        }

        require "../app/views/achievement/edit.php";
    }

    public function exportSelf() {
        $this->checkLogin();
        if ($this->isAdmin()) {
            header("Location: index.php?url=admin/index");
            exit();
        }

        $userID = (int) $_SESSION['user_id'];
        $achievements = Achievement::getByUser($userID, null, null);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="my_achievement_records.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Club', 'Event', 'Title', 'Category', 'Achievement Level', 'Date Received', 'Description', 'Status', 'Review Note', 'Evidence File']);

        foreach ($achievements as $row) {
            $dateReceived = trim((string) ($row['dateReceived'] ?? ''));
            $dateReceived = ($dateReceived === '' || $dateReceived === '0000-00-00') ? '' : $dateReceived;

            fputcsv($output, [
                $row['clubName'] ?? '',
                $row['eventTitle'] ?? '',
                $row['title'] ?? '',
                $row['category'] ?? '',
                $row['achievementLevel'] ?? '',
                $dateReceived,
                $row['description'] ?? '',
                $row['status'] ?? '',
                $row['review_note'] ?? '',
                $row['evidence_path'] ?? '',
            ]);
        }

        fclose($output);
        exit();
    }

    public function export() {
        $this->checkAdmin();

        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : null;
        $status = isset($_GET['status']) ? (string) $_GET['status'] : null;

        $achievements = Achievement::getAllWithUser($search, $sort, $status);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="achievement_records.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student Name', 'Student ID', 'Student Email', 'Club', 'Event', 'Title', 'Category', 'Achievement Level', 'Date Received', 'Description', 'Status', 'Review Note', 'Evidence File']);

        foreach ($achievements as $row) {
            $dateReceived = trim((string) ($row['dateReceived'] ?? ''));
            $dateReceived = ($dateReceived === '' || $dateReceived === '0000-00-00') ? '' : $dateReceived;

            fputcsv($output, [
                $row['userName'] ?? '',
                $row['studentId'] ?? '',
                $row['userEmail'] ?? '',
                $row['clubName'] ?? '',
                $row['eventTitle'] ?? '',
                $row['title'] ?? '',
                $row['category'] ?? '',
                $row['achievementLevel'] ?? '',
                $dateReceived,
                $row['description'] ?? '',
                $row['status'] ?? '',
                $row['review_note'] ?? '',
                $row['evidence_path'] ?? '',
            ]);
        }

        fclose($output);
        exit();
    }

    public function delete() {

        $this->checkLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=achievement/index");
            exit();
        }

        verify_csrf();

        $id = $_POST['id'] ?? null;

        if ($id) {
            if ($this->isAdmin()) {
                Achievement::deleteById($id);
                $_SESSION['success'] = "Achievement record deleted.";
            } else {
                $userID = (int) $_SESSION['user_id'];
                $achievement = Achievement::find($id, $userID);
                if (!$achievement) {
                    $_SESSION['error'] = "Achievement record not found.";
                } elseif (($achievement['status'] ?? '') === 'approved') {
                    $_SESSION['error'] = "Approved achievement records can only be deleted by admin.";
                } else {
                    Achievement::delete($id, $userID);
                    $_SESSION['success'] = "Achievement record deleted.";
                }
            }
        }

        header("Location: index.php?url=achievement/index");
        exit();
    }

    public function review() {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=achievement/index");
            exit();
        }

        verify_csrf();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $status = isset($_POST['status']) ? (string) $_POST['status'] : '';
        $note = trim((string) ($_POST['review_note'] ?? ''));

        if ($id > 0) {
            Achievement::updateStatusById($id, $status, (int) $_SESSION['user_id'], $note);
            $_SESSION['success'] = "Achievement review status updated.";
        }

        header("Location: index.php?url=achievement/index");
        exit();
    }
}

?>
