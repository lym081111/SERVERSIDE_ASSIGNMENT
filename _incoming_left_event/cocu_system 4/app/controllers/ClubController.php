<?php

class ClubController {

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
        header("Location: index.php?url=club/index");
        exit();
    }

    public function index() {

        $this->checkLogin();

        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : null;
        $status = isset($_GET['status']) ? (string) $_GET['status'] : null;

        if ($this->isAdmin()) {
            $clubs = Club::getAllWithUser($search, $sort, $status);
            require "../app/views/admin/club_index.php";
            return;
        }

        $userID = $_SESSION['user_id'];
        $clubs = Club::getByUser($userID, $search, $sort, $status);

        require "../app/views/club/index.php";
    }

    public function timeline() {

        $this->checkLogin();

        if ($this->isAdmin()) {
            header("Location: index.php?url=club/index");
            exit();
        }

        $userID = (int) $_SESSION['user_id'];
        $clubs = Club::getApprovedTimelineByUser($userID);

        $grouped = [];

        foreach ($clubs as $club) {
            $clubName = trim((string) ($club['clubName'] ?? ''));
            if ($clubName === '') {
                $clubName = 'Unknown Club';
            }
            $grouped[$clubName][] = $club;
        }

        require "../app/views/club/timeline.php";
    }

    public function create() {

        $this->checkLogin();

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            if (empty($_POST['clubName'])) {
                $error = "Club name is required.";
            } else {
                $targetUserID = $_SESSION['user_id'];

                if ($this->isAdmin()) {
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

                $clubName = trim((string) ($_POST['clubName'] ?? ''));
                $role = trim((string) ($_POST['role'] ?? ''));
                $roleDescription = trim((string) ($_POST['roleDescription'] ?? ''));

                $startDate = trim((string) ($_POST['startDate'] ?? ''));
                $endDate = trim((string) ($_POST['endDate'] ?? ''));
                $startDate = ($startDate === '' || $startDate === '0000-00-00') ? '' : $startDate;
                $endDate = ($endDate === '' || $endDate === '0000-00-00') ? '' : $endDate;

                if ($error === null && $startDate === '') {
                    $error = "Start date is required.";
                }

                if ($error === null && $endDate !== '' && $endDate < $startDate) {
                    $error = "End date must be on or after start date.";
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

                    if ($this->isAdmin() && $startDate !== '') {
                        Club::closePreviousApprovedRole($targetUserID, $clubName, $startDate);
                    }

                    Club::create(
                        $targetUserID,
                        $clubName,
                        $role,
                        $roleDescription,
                        $startDate === '' ? null : $startDate,
                        $endDate === '' ? null : $endDate,
                        $status,
                        $reviewedBy,
                        null,
                        $reviewedAt,
                        $evidencePath
                    );

                    $_SESSION['success'] = $this->isAdmin()
                        ? "Club record added and approved."
                        : "Club record submitted for review.";

                    header("Location: index.php?url=club/index");
                    exit();
                }
            }
        }

        if ($this->isAdmin()) {
            $students = User::getAll();
            require "../app/views/admin/club_create.php";
            return;
        }

        require "../app/views/club/create.php";
    }

    public function edit() {

        $this->checkLogin();

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php?url=club/index");
            exit();
        }

        $error = null;
        $userID = (int) ($_SESSION['user_id'] ?? 0);

        if ($this->isAdmin()) {
            $club = Club::findById($id);
            $students = User::getAll();
            if (!$club) {
                $this->redirectWithError("Club record not found.");
            }
        } else {
            $club = Club::find($id, $userID);
            if (!$club) {
                $this->redirectWithError("Club record not found.");
            }
            if (($club['status'] ?? '') === 'approved') {
                $this->redirectWithError("Approved club records can only be edited by admin.");
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            if (empty($_POST['clubName'])) {
                $error = "Club name is required.";
            }

            $clubName = trim((string) ($_POST['clubName'] ?? ''));
            $role = trim((string) ($_POST['role'] ?? ''));
            $roleDescription = trim((string) ($_POST['roleDescription'] ?? ''));

            $startDate = trim((string) ($_POST['startDate'] ?? ''));
            $endDate = trim((string) ($_POST['endDate'] ?? ''));
            $startDate = ($startDate === '' || $startDate === '0000-00-00') ? '' : $startDate;
            $endDate = ($endDate === '' || $endDate === '0000-00-00') ? '' : $endDate;

            if ($error === null && $startDate === '') {
                $error = "Start date is required.";
            }

            if ($error === null && $endDate !== '' && $endDate < $startDate) {
                $error = "End date must be on or after start date.";
            }

            if ($error === null) {
                if ($this->isAdmin()) {
                    Club::updateById(
                        $id,
                        $clubName,
                        $role,
                        $roleDescription,
                        $startDate === '' ? null : $startDate,
                        $endDate === '' ? null : $endDate
                    );

                    if (isset($_POST['status'])) {
                        $status = (string) $_POST['status'];
                        $note = trim((string) ($_POST['review_note'] ?? ''));

                        Club::updateStatusById($id, $status, (int) $_SESSION['user_id'], $note);

                        if ($status === 'approved' && $startDate !== '') {
                            $targetUserID = (int) ($club['userID'] ?? 0);
                            Club::closePreviousApprovedRole($targetUserID, $clubName, $startDate, $id);
                        }
                    }

                    $_SESSION['success'] = "Club record updated.";
                } else {
                    $upload = EvidenceUpload::uploadFromRequest('evidence_file');
                    if ($upload['error'] !== null) {
                        $error = $upload['error'];
                    } else {
                        Club::update(
                            $id,
                            $userID,
                            $clubName,
                            $role,
                            $roleDescription,
                            $startDate === '' ? null : $startDate,
                            $endDate === '' ? null : $endDate,
                            $upload['path'],
                            $upload['uploaded']
                        );
                        $_SESSION['success'] = "Club record updated and resubmitted for review.";
                    }
                }

                if ($error === null) {
                    header("Location: index.php?url=club/index");
                    exit();
                }
            }
        }

        if ($this->isAdmin()) {
            require "../app/views/admin/club_edit.php";
            return;
        }

        require "../app/views/club/edit.php";
    }

    public function exportSelf() {
        $this->checkLogin();

        if ($this->isAdmin()) {
            header("Location: index.php?url=club/index");
            exit();
        }

        $userID = (int) $_SESSION['user_id'];
        $rows = Club::getByUser($userID);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="my_clubs.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Club Name', 'Role', 'Role Description', 'Start Date', 'End Date', 'Status']);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['clubName'] ?? '',
                $row['role'] ?? '',
                $row['roleDescription'] ?? '',
                $row['startDate'] ?? '',
                $row['endDate'] ?? '',
                $row['status'] ?? '',
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

        $clubs = Club::getAllWithUser($search, $sort, $status);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="club_records.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student Name', 'Student ID', 'Student Email', 'Club Name', 'Role', 'Role Description', 'Start Date', 'End Date', 'Status', 'Review Note', 'Evidence File']);

        foreach ($clubs as $row) {
            $startDate = trim((string) ($row['startDate'] ?? ''));
            $endDate = trim((string) ($row['endDate'] ?? ''));
            $startDate = ($startDate === '' || $startDate === '0000-00-00') ? '' : $startDate;
            $endDate = ($endDate === '' || $endDate === '0000-00-00') ? '' : $endDate;

            fputcsv($output, [
                $row['userName'] ?? '',
                $row['studentId'] ?? '',
                $row['userEmail'] ?? '',
                $row['clubName'] ?? '',
                $row['role'] ?? '',
                $row['roleDescription'] ?? '',
                $startDate,
                $endDate,
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
            header("Location: index.php?url=club/index");
            exit();
        }

        verify_csrf();

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $this->redirectWithError("Invalid club record.");
        }

        if ($this->isAdmin()) {
            Club::deleteById($id);
            $_SESSION['success'] = "Club record deleted.";
            header("Location: index.php?url=club/index");
            exit();
        }

        $deleted = Club::delete($id, (int) $_SESSION['user_id']);
        if (!$deleted) {
            $this->redirectWithError("Approved club records cannot be deleted.");
        }

        $_SESSION['success'] = "Club record deleted.";
        header("Location: index.php?url=club/index");
        exit();
    }

    public function review() {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=club/index");
            exit();
        }

        verify_csrf();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $status = isset($_POST['status']) ? trim((string) $_POST['status']) : '';
        $note = trim((string) ($_POST['review_note'] ?? ''));

        if ($id <= 0) {
            $_SESSION['error'] = "Invalid club record.";
            header("Location: index.php?url=club/index");
            exit();
        }

        $club = Club::findById($id);
        if (!$club) {
            $_SESSION['error'] = "Club record not found.";
            header("Location: index.php?url=club/index");
            exit();
        }

        Club::updateStatusById($id, $status, (int) $_SESSION['user_id'], $note);

        if ($status === 'approved') {
            $targetUserID = (int) ($club['userID'] ?? 0);
            $clubName = trim((string) ($club['clubName'] ?? ''));
            $startDate = trim((string) ($club['startDate'] ?? ''));

            if ($targetUserID > 0 && $clubName !== '' && $startDate !== '' && $startDate !== '0000-00-00') {
                Club::closePreviousApprovedRole($targetUserID, $clubName, $startDate, $id);
            }
        }

        $_SESSION['success'] = "Club review status updated.";
        header("Location: index.php?url=club/index");
        exit();
    }
}