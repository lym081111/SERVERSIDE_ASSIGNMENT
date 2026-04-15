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
            $catalogSearch = isset($_GET['catalog_search']) ? trim((string) $_GET['catalog_search']) : null;
            $clubCatalog = ClubCatalog::getAll($catalogSearch);
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

        if ($this->isAdmin()) {

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                verify_csrf();

                $clubName = trim((string) ($_POST['clubName'] ?? ''));
                $description = trim((string) ($_POST['description'] ?? ''));

                if ($clubName === '') {
                    $error = "Club name is required.";
                } else {
                    try {
                        ClubCatalog::create($clubName, $description, (int) $_SESSION['user_id']);
                        $_SESSION['success'] = "Club added to catalog. Students can now request to join.";
                        header("Location: index.php?url=club/create");
                        exit();
                    } catch (PDOException $e) {
                        $error = "Club name already exists in catalog.";
                    }
                }
            }

            $catalogSearch = isset($_GET['catalog_search']) ? trim((string) $_GET['catalog_search']) : null;
            $clubCatalog = ClubCatalog::getAll($catalogSearch);
            require "../app/views/admin/club_create.php";
            return;
        }

        $clubCatalog = ClubCatalog::getAllActive();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $userID = (int) $_SESSION['user_id'];
            $clubCatalogID = isset($_POST['clubCatalogID']) ? (int) $_POST['clubCatalogID'] : 0;
            $requestType = isset($_POST['requestType']) && $_POST['requestType'] === 'role_change'
                ? 'role_change'
                : 'join';

            $clubDefinition = ClubCatalog::findById($clubCatalogID);
            if (!$clubDefinition || (int) ($clubDefinition['is_active'] ?? 0) !== 1) {
                $error = "Please select a valid active club from the catalog.";
            }

            $clubName = trim((string) ($clubDefinition['clubName'] ?? ''));
            $role = 'Member';
            if ($error === null && $requestType === 'role_change') {
                $desiredRole = trim((string) ($_POST['desiredRole'] ?? ''));
                if ($desiredRole === '' || strcasecmp($desiredRole, 'Member') === 0) {
                    $error = "Please enter a higher role (for example: Secretary, Treasurer).";
                } else {
                    $role = $desiredRole;
                }
            }

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

            if ($error === null && $requestType === 'join') {
                if (Club::hasActiveApprovedMembership($userID, $clubName)) {
                    $error = "You already have an active approved membership in this club.";
                } elseif (Club::hasPendingRequest($userID, $clubName, 'join')) {
                    $error = "You already have a pending join request for this club.";
                }
            }

            if ($error === null && $requestType === 'role_change') {
                if (!Club::hasActiveApprovedMembership($userID, $clubName)) {
                    $error = "You must have an approved active membership before requesting a higher role.";
                } elseif (Club::hasPendingRequest($userID, $clubName, 'role_change')) {
                    $error = "You already have a pending role-change request for this club.";
                }
            }

            $roleDescription = trim((string) ($_POST['roleDescription'] ?? ''));

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
                Club::create(
                    $userID,
                    $clubName,
                    $role,
                    $roleDescription,
                    $requestType,
                    $startDate === '' ? null : $startDate,
                    $endDate === '' ? null : $endDate,
                    'pending',
                    null,
                    null,
                    null,
                    $evidencePath
                );

                $_SESSION['success'] = $requestType === 'join'
                    ? "Join request submitted. Please wait for admin approval."
                    : "Role-change request submitted. Please wait for admin approval.";
                header("Location: index.php?url=club/index");
                exit();
            }
        }

        require "../app/views/club/create.php";
    }

    public function createCustom() {
        $this->checkLogin();

        if ($this->isAdmin()) {
            header("Location: index.php?url=admin/index");
            exit();
        }

        $isCustom = true; // Tell create.php to show the custom text input

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $userID = (int) $_SESSION['user_id'];
            $clubName = trim((string) ($_POST['clubName'] ?? ''));
            $role = trim((string) ($_POST['role'] ?? 'Member'));
            $roleDescription = trim((string) ($_POST['roleDescription'] ?? ''));
            $startDate = trim((string) ($_POST['startDate'] ?? ''));
            $endDate = trim((string) ($_POST['endDate'] ?? ''));

            if ($clubName === '' || $startDate === '') {
                $error = "Club Name and Start Date are required.";
                require "../app/views/club/create.php"; // Use existing view
                return;
            }

            $evidencePath = null;
            if (isset($_FILES['evidence_file']) && $_FILES['evidence_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                if (class_exists('EvidenceUpload')) {
                    $uploadResult = EvidenceUpload::uploadFromRequest('evidence_file');
                    if (!$uploadResult['uploaded']) {
                        $error = $uploadResult['error'];
                        require "../app/views/club/create.php"; // Use existing view
                        return;
                    }
                    $evidencePath = $uploadResult['path'];
                }
            }

            try {
                $db = Database::connect();
                $stmt = $db->prepare(
                    "INSERT INTO clubs (userID, clubName, role, roleDescription, startDate, endDate, request_type, evidence_path, status) 
                     VALUES (?, ?, ?, ?, ?, ?, 'join', ?, 'pending')"
                );
                
                $stmt->execute([
                    $userID, $clubName, $role, $roleDescription, $startDate,
                    $endDate !== '' ? $endDate : null, $evidencePath
                ]);

                $_SESSION['success'] = "Custom club added and is pending admin review.";
                header("Location: index.php?url=club/index");
                exit();

            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
                require "../app/views/club/create.php"; // Use existing view
                return;
            }
        }

        require "../app/views/club/create.php"; // Use existing view
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
            if (!$club) {
                $this->redirectWithError("Club record not found.");
            }
            $student = User::findById((int) ($club['userID'] ?? 0));
            $clubCatalog = ClubCatalog::getAll();
        } else {
            $club = Club::find($id, $userID);
            if (!$club) {
                $this->redirectWithError("Club record not found.");
            }
            if (($club['status'] ?? '') === 'approved') {
                $this->redirectWithError("Approved club records can only be edited by admin.");
            }
            $clubCatalog = ClubCatalog::getAllActive();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $requestType = isset($_POST['requestType']) && $_POST['requestType'] === 'role_change'
                ? 'role_change'
                : 'join';
            $clubName = trim((string) ($_POST['clubName'] ?? ''));
            $role = trim((string) ($_POST['role'] ?? ''));
            $roleDescription = trim((string) ($_POST['roleDescription'] ?? ''));

            if ($this->isAdmin()) {
                if ($clubName === '') {
                    $error = "Club name is required.";
                }
                if ($requestType === 'join') {
                    $role = 'Member';
                } elseif ($role === '' || strcasecmp($role, 'Member') === 0) {
                    $error = "Role-change requests must use a higher role (for example: Secretary, Treasurer).";
                }
            } else {
                $clubCatalogID = isset($_POST['clubCatalogID']) ? (int) $_POST['clubCatalogID'] : 0;
                $clubDefinition = ClubCatalog::findById($clubCatalogID);
                if (!$clubDefinition || (int) ($clubDefinition['is_active'] ?? 0) !== 1) {
                    $error = "Please select a valid active club from the catalog.";
                } else {
                    $clubName = trim((string) ($clubDefinition['clubName'] ?? ''));
                }

                if ($error === null && $requestType === 'join') {
                    $role = 'Member';
                } elseif ($error === null) {
                    $desiredRole = trim((string) ($_POST['desiredRole'] ?? ''));
                    if ($desiredRole === '' || strcasecmp($desiredRole, 'Member') === 0) {
                        $error = "Please enter a higher role (for example: Secretary, Treasurer).";
                    } else {
                        $role = $desiredRole;
                    }
                }
            }

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

            if (!$this->isAdmin() && $error === null && $requestType === 'join') {
                if (Club::hasActiveApprovedMembership($userID, $clubName, $id)) {
                    $error = "You already have an active approved membership in this club.";
                } elseif (Club::hasPendingRequest($userID, $clubName, 'join', $id)) {
                    $error = "You already have another pending join request for this club.";
                }
            }

            if (!$this->isAdmin() && $error === null && $requestType === 'role_change') {
                if (!Club::hasActiveApprovedMembership($userID, $clubName, $id)) {
                    $error = "You must have an approved active membership before requesting a higher role.";
                } elseif (Club::hasPendingRequest($userID, $clubName, 'role_change', $id)) {
                    $error = "You already have another pending role-change request for this club.";
                }
            }

            if ($error === null) {
                if ($this->isAdmin()) {
                    Club::updateById(
                        $id,
                        $clubName,
                        $role,
                        $roleDescription,
                        $requestType,
                        $startDate === '' ? null : $startDate,
                        $endDate === '' ? null : $endDate
                    );
                    if (isset($_POST['status'])) {
                        $status = (string) $_POST['status'];
                        $note = trim((string) ($_POST['review_note'] ?? ''));
                        Club::updateStatusById($id, $status, (int) $_SESSION['user_id'], $note);
                        if ($status === 'approved' && $startDate !== '' && strcasecmp($role, 'Member') !== 0) {
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
                            $requestType,
                            $startDate === '' ? null : $startDate,
                            $endDate === '' ? null : $endDate,
                            $upload['path'],
                            $upload['uploaded']
                        );
                        $_SESSION['success'] = $requestType === 'join'
                            ? "Join request updated and resubmitted for review."
                            : "Role-change request updated and resubmitted for review.";
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
            header("Location: index.php?url=admin/index");
            exit();
        }

        $userID = (int) $_SESSION['user_id'];
        $clubs = Club::getByUser($userID, null, null);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="my_club_records.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Club Name', 'Request Type', 'Role', 'Role Description', 'Start Date', 'End Date', 'Status', 'Review Note', 'Evidence File']);

        foreach ($clubs as $row) {
            $startDate = trim((string) ($row['startDate'] ?? ''));
            $endDate = trim((string) ($row['endDate'] ?? ''));
            $startDate = ($startDate === '' || $startDate === '0000-00-00') ? '' : $startDate;
            $endDate = ($endDate === '' || $endDate === '0000-00-00') ? '' : $endDate;

            fputcsv($output, [
                $row['clubName'] ?? '',
                $row['request_type'] ?? 'join',
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

    public function export() {
        $this->checkAdmin();

        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : null;
        $status = isset($_GET['status']) ? (string) $_GET['status'] : null;

        $clubs = Club::getAllWithUser($search, $sort, $status);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="club_records.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student Name', 'Student ID', 'Student Email', 'Club Name', 'Request Type', 'Role', 'Role Description', 'Start Date', 'End Date', 'Status', 'Review Note', 'Evidence File']);

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
                $row['request_type'] ?? 'join',
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

        if ($id) {
            if ($this->isAdmin()) {
                Club::deleteById($id);
                $_SESSION['success'] = "Club record deleted.";
            } else {
                $userID = (int) $_SESSION['user_id'];
                $club = Club::find($id, $userID);
                if (!$club) {
                    $_SESSION['error'] = "Club record not found.";
                } elseif (($club['status'] ?? '') === 'approved') {
                    $_SESSION['error'] = "Approved club records can only be deleted by admin.";
                } else {
                    Club::delete($id, $userID);
                    $_SESSION['success'] = "Club record deleted.";
                }
            }
        }

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
            $role = trim((string) ($club['role'] ?? 'Member'));
            $startDate = trim((string) ($club['startDate'] ?? ''));

            if ($targetUserID > 0 && $clubName !== '' && $startDate !== '' && $startDate !== '0000-00-00' && strcasecmp($role, 'Member') !== 0) {
                Club::closePreviousApprovedRole($targetUserID, $clubName, $startDate, $id);
            }
        }

        $_SESSION['success'] = "Club review status updated.";

        header("Location: index.php?url=club/index");
        exit();
    }

    public function catalogStatus() {
        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=club/create");
            exit();
        }

        verify_csrf();

        $catalogID = isset($_POST['clubCatalogID']) ? (int) $_POST['clubCatalogID'] : 0;
        $isActive = isset($_POST['is_active']) ? (int) $_POST['is_active'] : 0;

        if ($catalogID <= 0) {
            $_SESSION['error'] = "Invalid club catalog record.";
            header("Location: index.php?url=club/create");
            exit();
        }

        ClubCatalog::setActiveStatus($catalogID, $isActive === 1);
        $_SESSION['success'] = $isActive === 1
            ? "Club activated for student requests."
            : "Club marked as inactive.";

        header("Location: index.php?url=club/create");
        exit();
    }
}

?>
