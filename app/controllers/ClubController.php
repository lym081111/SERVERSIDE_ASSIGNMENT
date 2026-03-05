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

    public function index() {

        $this->checkLogin();

        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : null;

        if ($this->isAdmin()) {
            $clubs = Club::getAllWithUser($search, $sort);
            require "../app/views/admin/club_index.php";
            return;
        }

        $userID = $_SESSION['user_id'];
        $clubs = Club::getByUser($userID, $search, $sort);

        require "../app/views/club/index.php";
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
                    $targetUserID = (int) ($_POST['studentID'] ?? 0);
                    $studentEmail = trim((string) ($_POST['studentEmail'] ?? ''));
                    if ($targetUserID <= 0 && $studentEmail !== '') {
                        $student = User::findByEmail($studentEmail);
                        $targetUserID = $student['userID'] ?? 0;
                    }
                    if ($targetUserID <= 0) {
                        $error = "Please select a valid student.";
                    }
                }

                $startDate = trim((string) ($_POST['startDate'] ?? ''));
                $endDate = trim((string) ($_POST['endDate'] ?? ''));
                $startDate = ($startDate === '' || $startDate === '0000-00-00') ? '' : $startDate;
                $endDate = ($endDate === '' || $endDate === '0000-00-00') ? '' : $endDate;
                if ($error === null && $startDate !== '' && $endDate !== '' && $endDate < $startDate) {
                    $error = "End date must be on or after start date.";
                }

                if ($error === null) {
                    Club::create(
                        $targetUserID,
                        $_POST['clubName'],
                        $_POST['role'],
                        $_POST['roleDescription'],
                        $startDate === '' ? null : $startDate,
                        $endDate === '' ? null : $endDate
                    );

                    $_SESSION['success'] = "Club record added successfully.";
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            if (empty($_POST['clubName'])) {
                $error = "Club name is required.";
            }

            $startDate = trim((string) ($_POST['startDate'] ?? ''));
            $endDate = trim((string) ($_POST['endDate'] ?? ''));
            $startDate = ($startDate === '' || $startDate === '0000-00-00') ? '' : $startDate;
            $endDate = ($endDate === '' || $endDate === '0000-00-00') ? '' : $endDate;
            if ($error === null && $startDate !== '' && $endDate !== '' && $endDate < $startDate) {
                $error = "End date must be on or after start date.";
            }

            if ($error === null) {
                if ($this->isAdmin()) {
                    Club::updateById(
                        $id,
                        $_POST['clubName'],
                        $_POST['role'],
                        $_POST['roleDescription'],
                        $startDate === '' ? null : $startDate,
                        $endDate === '' ? null : $endDate
                    );
                } else {
                    $userID = $_SESSION['user_id'];
                    Club::update(
                        $id,
                        $userID,
                        $_POST['clubName'],
                        $_POST['role'],
                        $_POST['roleDescription'],
                        $startDate === '' ? null : $startDate,
                        $endDate === '' ? null : $endDate
                    );
                }

                header("Location: index.php?url=club/index");
                exit();
            }
        }

        if ($this->isAdmin()) {
            $club = Club::findById($id);
            $students = User::getAll();
            require "../app/views/admin/club_edit.php";
            return;
        }

        $userID = $_SESSION['user_id'];
        $club = Club::find($id, $userID);

        require "../app/views/club/edit.php";
    }

    public function export() {
        $this->checkAdmin();

        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : null;

        $clubs = Club::getAllWithUser($search, $sort);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="club_records.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student Name', 'Student Email', 'Club Name', 'Role', 'Role Description', 'Start Date', 'End Date']);

        foreach ($clubs as $row) {
            $startDate = trim((string) ($row['startDate'] ?? ''));
            $endDate = trim((string) ($row['endDate'] ?? ''));
            $startDate = ($startDate === '' || $startDate === '0000-00-00') ? '' : $startDate;
            $endDate = ($endDate === '' || $endDate === '0000-00-00') ? '' : $endDate;

            fputcsv($output, [
                $row['userName'] ?? '',
                $row['userEmail'] ?? '',
                $row['clubName'] ?? '',
                $row['role'] ?? '',
                $row['roleDescription'] ?? '',
                $startDate,
                $endDate,
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

        if ($id) {
            if ($this->isAdmin()) {
                Club::deleteById($id);
            } else {
                $userID = $_SESSION['user_id'];
                Club::delete($id, $userID);
            }
        }

        header("Location: index.php?url=club/index");
        exit();
    }
}

?>
