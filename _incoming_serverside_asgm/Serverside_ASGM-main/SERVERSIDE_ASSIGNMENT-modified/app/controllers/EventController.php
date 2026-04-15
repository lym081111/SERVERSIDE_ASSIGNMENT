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

    private function redirectWithError($message) {
        $_SESSION['error'] = $message;
        header("Location: index.php?url=event/index");
        exit();
    }

    private function normalizeEventType($value) {
        $type = trim((string) $value);
        $allowed = ['Leadership', 'Volunteerism', 'Academic', 'Technical', 'Sports', 'Community'];
        return in_array($type, $allowed, true) ? $type : 'Leadership';
    }

    private function resolvePostedClubCatalog($clubCatalogID) {
        $clubCatalogID = (int) $clubCatalogID;
        if ($clubCatalogID <= 0) {
            return null;
        }

        $clubDefinition = ClubCatalog::findById($clubCatalogID);
        if (!$clubDefinition || (int) ($clubDefinition['is_active'] ?? 0) !== 1) {
            return null;
        }

        return $clubDefinition;
    }

    public function index() {

        $this->checkLogin();

        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : null;
        $status = isset($_GET['status']) ? (string) $_GET['status'] : null;

        if ($this->isAdmin()) {
            $events = Event::getAllWithUser($search, $sort, $status);
            require "../app/views/admin/event_index.php";
            return;
        }

        $userID = (int) $_SESSION['user_id'];
        $events = Event::getByUser($userID, $search, $sort, $status);

        require "../app/views/event/index.php";
    }

    public function create() {

        $this->checkLogin();

        $error = null;

        if ($this->isAdmin()) {
            $students = User::getAll();
            $clubCatalog = ClubCatalog::getAllActive();
        } else {
            $userID = (int) $_SESSION['user_id'];
            $clubCatalog = Club::getApprovedActiveClubCatalogByUser($userID);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $eventTitle = trim((string) ($_POST['eventTitle'] ?? ''));
            $eventDate = trim((string) ($_POST['eventDate'] ?? ''));
            $eventDate = ($eventDate === '' || $eventDate === '0000-00-00') ? '' : $eventDate;
            $eventType = $this->normalizeEventType($_POST['eventType'] ?? 'Leadership');
            $location = trim((string) ($_POST['location'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $reflection = trim((string) ($_POST['reflection'] ?? ''));
            $eventHours = isset($_POST['eventHours']) ? (float) $_POST['eventHours'] : 0.0;

            $clubDefinition = $this->resolvePostedClubCatalog($_POST['clubCatalogID'] ?? 0);
            if (!$clubDefinition) {
                $error = "Please select a valid active club.";
            }

            if ($error === null && ($eventTitle === '' || $eventDate === '')) {
                $error = "Event title and date are required.";
            }

            if ($error === null && $eventHours <= 0) {
                $error = "Event hours must be greater than 0.";
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

            if ($error === null && !$this->isAdmin()) {
                $isAllowedClub = false;
                foreach ($clubCatalog as $clubRow) {
                    if ((int) ($clubRow['clubCatalogID'] ?? 0) === (int) ($clubDefinition['clubCatalogID'] ?? 0)) {
                        $isAllowedClub = true;
                        break;
                    }
                }
                if (!$isAllowedClub) {
                    $error = "You can only create events under clubs that you have joined and have been approved.";
                }
            }

            if ($error === null && $this->isAdmin()) {
                $clubName = trim((string) ($clubDefinition['clubName'] ?? ''));
                if ($clubName === '' || !Club::hasActiveApprovedMembership($targetUserID, $clubName)) {
                    $error = "Selected student does not have an active approved membership in this club.";
                }
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

                Event::create(
                    $targetUserID,
                    (int) ($clubDefinition['clubCatalogID'] ?? 0),
                    $eventTitle,
                    $eventType,
                    $eventDate,
                    $eventHours,
                    $location,
                    $description,
                    $reflection === '' ? null : $reflection,
                    $status,
                    $reviewedBy,
                    null,
                    $reviewedAt,
                    $evidencePath
                );

                $_SESSION['success'] = $this->isAdmin()
                    ? "Event record added and approved."
                    : "Event record submitted for review.";
                header("Location: index.php?url=event/index");
                exit();
            }
        }

        if ($this->isAdmin()) {
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
        $userID = (int) ($_SESSION['user_id'] ?? 0);

        if ($this->isAdmin()) {
            $event = Event::findById($id);
            if (!$event) {
                $this->redirectWithError("Event record not found.");
            }
            $student = User::findById((int) ($event['userID'] ?? 0));
            $clubCatalog = ClubCatalog::getAllActive();
        } else {
            $event = Event::find($id, $userID);
            if (!$event) {
                $this->redirectWithError("Event record not found.");
            }
            if (($event['status'] ?? '') === 'approved') {
                $this->redirectWithError("Approved event records can only be edited by admin.");
            }
            $clubCatalog = Club::getApprovedActiveClubCatalogByUser($userID);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $eventTitle = trim((string) ($_POST['eventTitle'] ?? ''));
            $eventDate = trim((string) ($_POST['eventDate'] ?? ''));
            $eventDate = ($eventDate === '' || $eventDate === '0000-00-00') ? '' : $eventDate;
            $eventType = $this->normalizeEventType($_POST['eventType'] ?? 'Leadership');
            $location = trim((string) ($_POST['location'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $reflection = trim((string) ($_POST['reflection'] ?? ''));
            $eventHours = isset($_POST['eventHours']) ? (float) $_POST['eventHours'] : 0.0;

            $clubDefinition = $this->resolvePostedClubCatalog($_POST['clubCatalogID'] ?? 0);
            if (!$clubDefinition) {
                $error = "Please select a valid active club.";
            }

            if ($error === null && ($eventTitle === '' || $eventDate === '')) {
                $error = "Event title and date are required.";
            }

            if ($error === null && $eventHours <= 0) {
                $error = "Event hours must be greater than 0.";
            }

            $targetUserID = $this->isAdmin() ? (int) ($event['userID'] ?? 0) : $userID;

            if ($error === null && !$this->isAdmin()) {
                $isAllowedClub = false;
                foreach ($clubCatalog as $clubRow) {
                    if ((int) ($clubRow['clubCatalogID'] ?? 0) === (int) ($clubDefinition['clubCatalogID'] ?? 0)) {
                        $isAllowedClub = true;
                        break;
                    }
                }
                if (!$isAllowedClub) {
                    $error = "You can only create events under clubs that you have joined and have been approved.";
                }
            }

            if ($error === null && $this->isAdmin()) {
                $clubName = trim((string) ($clubDefinition['clubName'] ?? ''));
                if ($clubName === '' || !Club::hasActiveApprovedMembership($targetUserID, $clubName)) {
                    $error = "Selected student does not have an active approved membership in this club.";
                }
            }

            if ($error === null) {
                if ($this->isAdmin()) {
                    Event::updateById(
                        $id,
                        (int) ($clubDefinition['clubCatalogID'] ?? 0),
                        $eventTitle,
                        $eventType,
                        $eventDate,
                        $eventHours,
                        $location,
                        $description,
                        $reflection === '' ? null : $reflection
                    );
                    if (isset($_POST['status'])) {
                        $status = (string) $_POST['status'];
                        $note = trim((string) ($_POST['review_note'] ?? ''));
                        Event::updateStatusById($id, $status, (int) $_SESSION['user_id'], $note);
                    }
                    $_SESSION['success'] = "Event record updated.";
                } else {
                    $upload = EvidenceUpload::uploadFromRequest('evidence_file');
                    if ($upload['error'] !== null) {
                        $error = $upload['error'];
                    } else {
                        Event::update(
                            $id,
                            $userID,
                            (int) ($clubDefinition['clubCatalogID'] ?? 0),
                            $eventTitle,
                            $eventType,
                            $eventDate,
                            $eventHours,
                            $location,
                            $description,
                            $reflection === '' ? null : $reflection,
                            $upload['path'],
                            $upload['uploaded']
                        );
                        $_SESSION['success'] = "Event record updated and resubmitted for review.";
                    }
                }

                if ($error === null) {
                    header("Location: index.php?url=event/index");
                    exit();
                }
            }
        }

        if ($this->isAdmin()) {
            require "../app/views/admin/event_edit.php";
            return;
        }

        require "../app/views/event/edit.php";
    }

    public function exportSelf() {
        $this->checkLogin();
        if ($this->isAdmin()) {
            header("Location: index.php?url=admin/index");
            exit();
        }

        $userID = (int) $_SESSION['user_id'];
        $events = Event::getByUser($userID, null, null);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="my_event_records.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Club', 'Event Title', 'Event Type', 'Event Date', 'Event Hours', 'Location', 'Description', 'Reflection', 'Status', 'Review Note', 'Evidence File']);

        foreach ($events as $row) {
            $eventDate = trim((string) ($row['eventDate'] ?? ''));
            $eventDate = ($eventDate === '' || $eventDate === '0000-00-00') ? '' : $eventDate;

            fputcsv($output, [
                $row['clubName'] ?? '',
                $row['eventTitle'] ?? '',
                $row['eventType'] ?? '',
                $eventDate,
                $row['eventHours'] ?? '',
                $row['location'] ?? '',
                $row['description'] ?? '',
                $row['reflection'] ?? '',
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

        $events = Event::getAllWithUser($search, $sort, $status);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="event_records.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student Name', 'Student ID', 'Student Email', 'Club', 'Event Title', 'Event Type', 'Event Date', 'Event Hours', 'Location', 'Description', 'Reflection', 'Status', 'Review Note', 'Evidence File']);

        foreach ($events as $row) {
            $eventDate = trim((string) ($row['eventDate'] ?? ''));
            $eventDate = ($eventDate === '' || $eventDate === '0000-00-00') ? '' : $eventDate;

            fputcsv($output, [
                $row['userName'] ?? '',
                $row['studentId'] ?? '',
                $row['userEmail'] ?? '',
                $row['clubName'] ?? '',
                $row['eventTitle'] ?? '',
                $row['eventType'] ?? '',
                $eventDate,
                $row['eventHours'] ?? '',
                $row['location'] ?? '',
                $row['description'] ?? '',
                $row['reflection'] ?? '',
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
            header("Location: index.php?url=event/index");
            exit();
        }

        verify_csrf();

        $id = $_POST['id'] ?? null;

        if ($id) {
            if ($this->isAdmin()) {
                Event::deleteById($id);
                $_SESSION['success'] = "Event record deleted.";
            } else {
                $userID = (int) $_SESSION['user_id'];
                $event = Event::find($id, $userID);
                if (!$event) {
                    $_SESSION['error'] = "Event record not found.";
                } elseif (($event['status'] ?? '') === 'approved') {
                    $_SESSION['error'] = "Approved event records can only be deleted by admin.";
                } else {
                    Event::delete($id, $userID);
                    $_SESSION['success'] = "Event record deleted.";
                }
            }
        }

        header("Location: index.php?url=event/index");
        exit();
    }

    public function review() {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=event/index");
            exit();
        }

        verify_csrf();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $status = isset($_POST['status']) ? (string) $_POST['status'] : '';
        $note = trim((string) ($_POST['review_note'] ?? ''));

        if ($id > 0) {
            Event::updateStatusById($id, $status, (int) $_SESSION['user_id'], $note);
            $_SESSION['success'] = "Event review status updated.";
        }

        header("Location: index.php?url=event/index");
        exit();
    }
}

?>
