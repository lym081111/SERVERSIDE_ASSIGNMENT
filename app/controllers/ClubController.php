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

                if ($error === null) {
                    Club::create(
                        $targetUserID,
                        $_POST['clubName'],
                        $_POST['role'],
                        $_POST['roleDescription'],
                        $_POST['startDate'],
                        $_POST['endDate']
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            if ($this->isAdmin()) {
                Club::updateById(
                    $id,
                    $_POST['clubName'],
                    $_POST['role'],
                    $_POST['roleDescription'],
                    $_POST['startDate'],
                    $_POST['endDate']
                );
            } else {
                $userID = $_SESSION['user_id'];
                Club::update(
                    $id,
                    $userID,
                    $_POST['clubName'],
                    $_POST['role'],
                    $_POST['roleDescription'],
                    $_POST['startDate'],
                    $_POST['endDate']
                );
            }

            header("Location: index.php?url=club/index");
            exit();
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
