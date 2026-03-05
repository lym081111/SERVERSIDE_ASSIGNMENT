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

            $eventDate = trim((string) ($_POST['eventDate'] ?? ''));
            $eventDate = ($eventDate === '' || $eventDate === '0000-00-00') ? '' : $eventDate;

            if (empty($_POST['eventTitle']) || $eventDate === '') {
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
                        $eventDate,
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

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $eventDate = trim((string) ($_POST['eventDate'] ?? ''));
            $eventDate = ($eventDate === '' || $eventDate === '0000-00-00') ? '' : $eventDate;

            if (empty($_POST['eventTitle']) || $eventDate === '') {
                $error = "Event title and date are required.";
            }

            if ($error === null) {
                if ($this->isAdmin()) {
                    Event::updateById(
                        $id,
                        $_POST['eventTitle'],
                        $eventDate,
                        $_POST['location'],
                        $_POST['description']
                    );
                } else {
                    $userID = $_SESSION['user_id'];
                    Event::update(
                        $id,
                        $userID,
                        $_POST['eventTitle'],
                        $eventDate,
                        $_POST['location'],
                        $_POST['description']
                    );
                }

                header("Location: index.php?url=event/index");
                exit();
            }
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

    public function export() {
        $this->checkAdmin();

        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : null;

        $events = Event::getAllWithUser($search, $sort);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="event_records.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student Name', 'Student Email', 'Event Title', 'Event Date', 'Location', 'Description']);

        foreach ($events as $row) {
            $eventDate = trim((string) ($row['eventDate'] ?? ''));
            $eventDate = ($eventDate === '' || $eventDate === '0000-00-00') ? '' : $eventDate;

            fputcsv($output, [
                $row['userName'] ?? '',
                $row['userEmail'] ?? '',
                $row['eventTitle'] ?? '',
                $eventDate,
                $row['location'] ?? '',
                $row['description'] ?? '',
            ]);
        }

        fclose($output);
        exit();
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
