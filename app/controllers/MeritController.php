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

    private function redirectWithError($message) {
        $_SESSION['error'] = $message;
        header("Location: index.php?url=merit/index");
        exit();
    }

    private function buildAdminListRedirectUrl() {
        $params = ['url' => 'merit/index'];
        $search = trim((string) ($_POST['_filter_search'] ?? ''));
        $sort = trim((string) ($_POST['_filter_sort'] ?? ''));
        $status = trim((string) ($_POST['_filter_status'] ?? ''));
        $page = isset($_POST['_filter_page']) ? (int) $_POST['_filter_page'] : 1;

        if ($search !== '') {
            $params['search'] = $search;
        }
        if ($sort !== '') {
            $params['sort'] = $sort;
        }
        if ($status !== '') {
            $params['status'] = $status;
        }
        if ($page > 1) {
            $params['page'] = $page;
        }

        return 'index.php?' . http_build_query($params);
    }

    private function appendCertificateMessage($baseMessage, $newCertificates) {
        if (!is_array($newCertificates) || empty($newCertificates)) {
            return $baseMessage;
        }

        $milestones = array_map(function ($certificate) {
            return (int) ($certificate['milestone_hours'] ?? 0);
        }, $newCertificates);
        $milestones = array_values(array_filter($milestones, function ($value) {
            return $value > 0;
        }));

        if (empty($milestones)) {
            return $baseMessage;
        }

        sort($milestones);
        $label = implode(', ', array_map(function ($value) {
            return $value . 'h';
        }, $milestones));

        return $baseMessage . " Certificate unlocked at {$label}.";
    }

    public function index() {

        $this->checkLogin();

        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : null;
        $status = isset($_GET['status']) ? (string) $_GET['status'] : null;

        if ($this->isAdmin()) {
            $merits = Merit::getAllWithUser($search, $sort, $status);
            $meritStats = [
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
                'appealed' => 0,
            ];
            if (is_array($merits)) {
                foreach ($merits as $row) {
                    $statusCountValue = (string) ($row['status'] ?? 'pending');
                    if ($statusCountValue === 'approved') {
                        $meritStats['approved']++;
                    } elseif ($statusCountValue === 'rejected') {
                        $meritStats['rejected']++;
                    } else {
                        $meritStats['pending']++;
                    }
                    if ((int) ($row['resubmission_count'] ?? 0) > 0) {
                        $meritStats['appealed']++;
                    }
                }
            }
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            if ($page < 1) {
                $page = 1;
            }
            $perPage = 10;
            $totalRecords = is_array($merits) ? count($merits) : 0;
            $totalPages = max(1, (int) ceil($totalRecords / $perPage));
            if ($page > $totalPages) {
                $page = $totalPages;
            }
            $offset = ($page - 1) * $perPage;
            $merits = is_array($merits) ? array_slice($merits, $offset, $perPage) : [];
            $pagination = [
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalRecords' => $totalRecords,
                'totalPages' => $totalPages,
            ];
            $recentStatusLogs = Merit::getRecentStatusLogs(20);
            require "../app/views/admin/merit_index.php";
            return;
        }

        $userID = (int) $_SESSION['user_id'];
        $merits = Merit::getByUser($userID, $search, $sort, $status);
        $certificateCount = MeritCertificate::countByUser($userID);
        $latestCertificate = MeritCertificate::getLatestByUser($userID);
        $approvedMeritHours = MeritCertificate::getApprovedMeritHours($userID);

        require "../app/views/merit/index.php";
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

            $eventID = isset($_POST['eventID']) ? (int) $_POST['eventID'] : 0;
            if ($eventID <= 0) {
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

            $evidencePath = null;
            if ($error === null) {
                $upload = EvidenceUpload::uploadFromRequest('evidence_file');
                if ($upload['error'] !== null) {
                    $error = $upload['error'];
                } else {
                    $evidencePath = $upload['path'];
                }
            }

            if ($error === null && $event !== null) {
                $activityName = trim((string) ($event['eventTitle'] ?? ''));
                $hours = isset($event['eventHours']) ? (float) $event['eventHours'] : 0.0;
                $eventDate = trim((string) ($event['eventDate'] ?? ''));
                if ($activityName === '' || $hours <= 0 || $eventDate === '' || $eventDate === '0000-00-00') {
                    $error = "Selected event is missing required details (title/date/hours).";
                }

                if ($error === null) {
                    $status = $this->isAdmin() ? 'approved' : 'pending';
                    $reviewedBy = $this->isAdmin() ? (int) $_SESSION['user_id'] : null;
                    $reviewedAt = $this->isAdmin() ? date('Y-m-d H:i:s') : null;
                    $created = Merit::create(
                        $targetUserID,
                        (int) $eventID,
                        $activityName,
                        $hours,
                        $eventDate,
                        $eventDate,
                        $status,
                        $reviewedBy,
                        null,
                        $reviewedAt,
                        $evidencePath
                    );
                    if (!$created) {
                        $error = "Unable to save merit record. Please try again.";
                    }
                }

                if ($error === null) {
                    $successMessage = $this->isAdmin()
                        ? "Merit record added and approved."
                        : "Merit record submitted for review.";
                    if ($this->isAdmin() && $status === 'approved') {
                        $newCertificates = MeritCertificate::issueEligibleForUser(
                            (int) $targetUserID,
                            (int) $_SESSION['user_id']
                        );
                        $successMessage = $this->appendCertificateMessage($successMessage, $newCertificates);
                    }
                    $_SESSION['success'] = $successMessage;
                    header("Location: index.php?url=merit/index");
                    exit();
                }
            }
        }

        if ($this->isAdmin()) {
            require "../app/views/admin/merit_create.php";
            return;
        }

        require "../app/views/merit/create.php";
    }

    public function edit() {

        $this->checkLogin();

        $id = $_GET['id'] ?? null;
        $error = null;

        if (!$id) {
            header("Location: index.php?url=merit/index");
            exit();
        }

        $userID = (int) ($_SESSION['user_id'] ?? 0);

        if ($this->isAdmin()) {
            $merit = Merit::findById($id);
            if (!$merit) {
                $this->redirectWithError("Merit record not found.");
            }
            $student = User::findById((int) ($merit['userID'] ?? 0));
            $approvedEvents = Event::getApprovedByUser((int) ($merit['userID'] ?? 0));
            $statusLogs = Merit::getStatusLogsByMerit($id);
        } else {
            $merit = Merit::find($id, $userID);
            if (!$merit) {
                $this->redirectWithError("Merit record not found.");
            }
            if (($merit['status'] ?? '') === 'approved') {
                $this->redirectWithError("Approved merit records can only be edited by admin.");
            }
            $approvedEvents = Event::getApprovedByUser($userID);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $eventID = isset($_POST['eventID']) ? (int) $_POST['eventID'] : 0;
            if ($eventID <= 0) {
                $error = "Please select an approved event.";
            }

            $targetUserID = $this->isAdmin() ? (int) ($merit['userID'] ?? 0) : $userID;
            $event = null;
            if ($error === null) {
                $event = Event::findApprovedByIdForUser($eventID, $targetUserID);
                if (!$event) {
                    $error = "Selected event is not approved for this student.";
                }
            }

            if ($error === null && $event !== null) {
                $activityName = trim((string) ($event['eventTitle'] ?? ''));
                $hours = isset($event['eventHours']) ? (float) $event['eventHours'] : 0.0;
                $eventDate = trim((string) ($event['eventDate'] ?? ''));
                if ($activityName === '' || $hours <= 0 || $eventDate === '' || $eventDate === '0000-00-00') {
                    $error = "Selected event is missing required details (title/date/hours).";
                }

                if ($error === null) {
                    if ($this->isAdmin()) {
                        Merit::updateById(
                            $id,
                            (int) $eventID,
                            $activityName,
                            $hours,
                            $eventDate,
                            $eventDate
                        );
                        if (isset($_POST['status'])) {
                            $status = (string) $_POST['status'];
                            $note = trim((string) ($_POST['review_note'] ?? ''));
                            Merit::updateStatusById($id, $status, (int) $_SESSION['user_id'], $note, 'admin_edit');
                        }
                        $updatedMerit = Merit::findById($id);
                        $successMessage = "Merit record updated.";
                        if ($updatedMerit && ($updatedMerit['status'] ?? '') === 'approved') {
                            $newCertificates = MeritCertificate::issueEligibleForUser(
                                (int) $updatedMerit['userID'],
                                (int) $_SESSION['user_id'],
                                (int) $id
                            );
                            $successMessage = $this->appendCertificateMessage($successMessage, $newCertificates);
                        }
                        $_SESSION['success'] = $successMessage;
                    } else {
                        $appealNote = trim((string) ($_POST['appeal_note'] ?? ''));
                        $upload = EvidenceUpload::uploadFromRequest('evidence_file');
                        if ($upload['error'] !== null) {
                            $error = $upload['error'];
                        } else {
                            $updated = Merit::update(
                                $id,
                                $userID,
                                (int) $eventID,
                                $activityName,
                                $hours,
                                $eventDate,
                                $eventDate,
                                $upload['path'],
                                $upload['uploaded'],
                                $appealNote
                            );
                            if (!$updated) {
                                $error = "Unable to update record. It may no longer be editable.";
                            } elseif (($merit['status'] ?? '') === 'rejected') {
                                $_SESSION['success'] = "Appeal submitted and merit record resubmitted for admin review.";
                            } else {
                                $_SESSION['success'] = "Merit record updated and resubmitted for review.";
                            }
                        }
                    }

                    if ($error === null) {
                        header("Location: index.php?url=merit/index");
                        exit();
                    }
                }
            }
        }

        if ($this->isAdmin()) {
            require "../app/views/admin/merit_edit.php";
            return;
        }

        require "../app/views/merit/edit.php";
    }

    public function exportSelf() {
        $this->checkLogin();
        if ($this->isAdmin()) {
            header("Location: index.php?url=admin/index");
            exit();
        }

        $userID = (int) $_SESSION['user_id'];
        $merits = Merit::getByUser($userID, null, null);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="my_merit_records.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Club', 'Event', 'Activity', 'Hours', 'Date From', 'Date To', 'Status', 'Review Note', 'Evidence File']);

        foreach ($merits as $row) {
            $dateFrom = trim((string) ($row['dateFrom'] ?? ''));
            $dateTo = trim((string) ($row['dateTo'] ?? ''));
            $dateFrom = ($dateFrom === '' || $dateFrom === '0000-00-00') ? '' : $dateFrom;
            $dateTo = ($dateTo === '' || $dateTo === '0000-00-00') ? '' : $dateTo;

            fputcsv($output, [
                $row['clubName'] ?? '',
                $row['eventTitle'] ?? '',
                $row['activityName'] ?? '',
                $row['hours'] ?? '',
                $dateFrom,
                $dateTo,
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

        $merits = Merit::getAllWithUser($search, $sort, $status);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="merit_records.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student Name', 'Student ID', 'Student Email', 'Club', 'Event', 'Activity', 'Hours', 'Date From', 'Date To', 'Status', 'Review Note', 'Evidence File']);

        foreach ($merits as $row) {
            $dateFrom = trim((string) ($row['dateFrom'] ?? ''));
            $dateTo = trim((string) ($row['dateTo'] ?? ''));
            $dateFrom = ($dateFrom === '' || $dateFrom === '0000-00-00') ? '' : $dateFrom;
            $dateTo = ($dateTo === '' || $dateTo === '0000-00-00') ? '' : $dateTo;

            fputcsv($output, [
                $row['userName'] ?? '',
                $row['studentId'] ?? '',
                $row['userEmail'] ?? '',
                $row['clubName'] ?? '',
                $row['eventTitle'] ?? '',
                $row['activityName'] ?? '',
                $row['hours'] ?? '',
                $dateFrom,
                $dateTo,
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
            header("Location: index.php?url=merit/index");
            exit();
        }

        verify_csrf();

        $id = $_POST['id'] ?? null;

        if ($id) {
            if ($this->isAdmin()) {
                Merit::deleteById($id);
                $_SESSION['success'] = "Merit record deleted.";
            } else {
                $userID = (int) $_SESSION['user_id'];
                $merit = Merit::find($id, $userID);
                if (!$merit) {
                    $_SESSION['error'] = "Merit record not found.";
                } elseif (($merit['status'] ?? '') === 'approved') {
                    $_SESSION['error'] = "Approved merit records can only be deleted by admin.";
                } else {
                    Merit::delete($id, $userID);
                    $_SESSION['success'] = "Merit record deleted.";
                }
            }
        }

        $redirectUrl = $this->isAdmin()
            ? $this->buildAdminListRedirectUrl()
            : "index.php?url=merit/index";
        header("Location: " . $redirectUrl);
        exit();
    }

    public function review() {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=merit/index");
            exit();
        }

        verify_csrf();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $status = isset($_POST['status']) ? (string) $_POST['status'] : '';
        $note = trim((string) ($_POST['review_note'] ?? ''));

        if ($id > 0) {
            Merit::updateStatusById($id, $status, (int) $_SESSION['user_id'], $note, 'admin_review');
            $successMessage = "Merit review status updated.";
            if ($status === 'approved') {
                $merit = Merit::findById($id);
                if ($merit) {
                    $newCertificates = MeritCertificate::issueEligibleForUser(
                        (int) $merit['userID'],
                        (int) $_SESSION['user_id'],
                        $id
                    );
                    $successMessage = $this->appendCertificateMessage($successMessage, $newCertificates);
                }
            }
            $_SESSION['success'] = $successMessage;
        }

        header("Location: " . $this->buildAdminListRedirectUrl());
        exit();
    }
}

?>
