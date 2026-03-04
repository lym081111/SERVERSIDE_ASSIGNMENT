<?php

class MeritController {

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
            $merits = Merit::getAllWithUser($search, $sort);
            require "../app/views/admin/merit_index.php";
            return;
        }

        $userID = $_SESSION['user_id'];
        $merits = Merit::getByUser($userID, $search, $sort);

        require "../app/views/merit/index.php";
    }

    public function create() {

    $this->checkLogin();

    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        verify_csrf();

        if (empty($_POST['activityName']) || empty($_POST['hours'])) {
            $error = "Activity name and hours are required.";
        } elseif ($_POST['hours'] <= 0) {
            $error = "Hours must be greater than 0.";
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
                Merit::create(
                    $targetUserID,
                    $_POST['activityName'],
                    $_POST['hours'],
                    $_POST['dateFrom'],
                    $_POST['dateTo']
                );

                $_SESSION['success'] = "Merit record added successfully.";
                header("Location: index.php?url=merit/index");
                exit();
            }
        }
    }

    if ($this->isAdmin()) {
        $students = User::getAll();
        require "../app/views/admin/merit_create.php";
        return;
    }

    require "../app/views/merit/create.php";
    }

    public function edit() {

        $this->checkLogin();

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php?url=merit/index");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            if ($this->isAdmin()) {
                Merit::updateById(
                    $id,
                    $_POST['activityName'],
                    $_POST['hours'],
                    $_POST['dateFrom'],
                    $_POST['dateTo']
                );
            } else {
                $userID = $_SESSION['user_id'];
                Merit::update(
                    $id,
                    $userID,
                    $_POST['activityName'],
                    $_POST['hours'],
                    $_POST['dateFrom'],
                    $_POST['dateTo']
                );
            }

            header("Location: index.php?url=merit/index");
            exit();
        }

        if ($this->isAdmin()) {
            $merit = Merit::findById($id);
            $students = User::getAll();
            require "../app/views/admin/merit_edit.php";
            return;
        }

        $userID = $_SESSION['user_id'];
        $merit = Merit::find($id, $userID);

        require "../app/views/merit/edit.php";
    }

    public function delete() {

        $this->checkLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=merit/index");
            exit();
        }

        verify_csrf();

        $id = $_POST['id'] ?? null;

        if ($id) {
            if ($this->isAdmin()) {
                Merit::deleteById($id);
            } else {
                $userID = $_SESSION['user_id'];
                Merit::delete($id, $userID);
            }
        }

        header("Location: index.php?url=merit/index");
        exit();
    }
}

?>
