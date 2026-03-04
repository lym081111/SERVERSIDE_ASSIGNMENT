<?php

class EventController {

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
            $events = Event::getAllWithUser($search, $sort);
            require "../app/views/admin/event_index.php";
            return;
        }

        $userID = $_SESSION['user_id'];
        $events = Event::getByUser($userID, $search, $sort);

        require "../app/views/event/index.php";
    }

    public function create() {

        $this->checkLogin();

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            if (empty($_POST['eventTitle']) || empty($_POST['eventDate'])) {
                $error = "Event title and date are required.";
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
                    Event::create(
                        $targetUserID,
                        $_POST['eventTitle'],
                        $_POST['eventDate'],
                        $_POST['location'],
                        $_POST['description']
                    );

                    $_SESSION['success'] = "Event record added successfully.";
                    header("Location: index.php?url=event/index");
                    exit();
                }
            }
        }

        if ($this->isAdmin()) {
            $students = User::getAll();
            require "../app/views/admin/event_create.php";
            return;
        }

        require "../app/views/event/create.php";
    }

    public function edit() {

        $this->checkLogin();

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php?url=event/index");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            if ($this->isAdmin()) {
                Event::updateById(
                    $id,
                    $_POST['eventTitle'],
                    $_POST['eventDate'],
                    $_POST['location'],
                    $_POST['description']
                );
            } else {
                $userID = $_SESSION['user_id'];
                Event::update(
                    $id,
                    $userID,
                    $_POST['eventTitle'],
                    $_POST['eventDate'],
                    $_POST['location'],
                    $_POST['description']
                );
            }

            header("Location: index.php?url=event/index");
            exit();
        }

        if ($this->isAdmin()) {
            $event = Event::findById($id);
            $students = User::getAll();
            require "../app/views/admin/event_edit.php";
            return;
        }

        $userID = $_SESSION['user_id'];
        $event = Event::find($id, $userID);

        require "../app/views/event/edit.php";
    }

    public function delete() {

        $this->checkLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=event/index");
            exit();
        }

        verify_csrf();

        $id = $_POST['id'] ?? null;

        if ($id) {
            if ($this->isAdmin()) {
                Event::deleteById($id);
            } else {
                $userID = $_SESSION['user_id'];
                Event::delete($id, $userID);
            }
        }

        header("Location: index.php?url=event/index");
        exit();
    }
}

?>
