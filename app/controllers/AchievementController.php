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

    public function index() {

        $this->checkLogin();

        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : null;

        if ($this->isAdmin()) {
            $achievements = Achievement::getAllWithUser($search, $sort);
            require "../app/views/admin/achievement_index.php";
            return;
        }

        $userID = $_SESSION['user_id'];
        $achievements = Achievement::getByUser($userID, $search, $sort);

        require "../app/views/achievement/index.php";
    }

    public function create() {

        $this->checkLogin();

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            if (empty($_POST['title'])) {
                $error = "Achievement title is required.";
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
                    Achievement::create(
                        $targetUserID,
                        $_POST['title'],
                        $_POST['category'],
                        $_POST['dateReceived'],
                        $_POST['description']
                    );

                    $_SESSION['success'] = "Achievement record added successfully.";
                    header("Location: index.php?url=achievement/index");
                    exit();
                }
            }
        }

        if ($this->isAdmin()) {
            $students = User::getAll();
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            if ($this->isAdmin()) {
                Achievement::updateById(
                    $id,
                    $_POST['title'],
                    $_POST['category'],
                    $_POST['dateReceived'],
                    $_POST['description']
                );
            } else {
                $userID = $_SESSION['user_id'];
                Achievement::update(
                    $id,
                    $userID,
                    $_POST['title'],
                    $_POST['category'],
                    $_POST['dateReceived'],
                    $_POST['description']
                );
            }

            header("Location: index.php?url=achievement/index");
            exit();
        }

        if ($this->isAdmin()) {
            $achievement = Achievement::findById($id);
            $students = User::getAll();
            require "../app/views/admin/achievement_edit.php";
            return;
        }

        $userID = $_SESSION['user_id'];
        $achievement = Achievement::find($id, $userID);

        require "../app/views/achievement/edit.php";
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
            } else {
                $userID = $_SESSION['user_id'];
                Achievement::delete($id, $userID);
            }
        }

        header("Location: index.php?url=achievement/index");
        exit();
    }
}

?>
