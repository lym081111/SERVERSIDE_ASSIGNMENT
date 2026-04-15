<?php

class CertificateController {

    private function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit();
        }
    }

    private function isAdmin() {
        return !empty($_SESSION['isAdmin']);
    }

    public function myMerit() {
        $this->checkLogin();

        if ($this->isAdmin()) {
            header("Location: index.php?url=admin/index");
            exit();
        }

        $userID = (int) ($_SESSION['user_id'] ?? 0);
        $certificates = MeritCertificate::getByUser($userID);
        $approvedHours = MeritCertificate::getApprovedMeritHours($userID);
        $nextMilestone = ((int) floor($approvedHours / 100) + 1) * 100;
        $hoursToNext = max(0, $nextMilestone - $approvedHours);

        require "../app/views/certificate/merit_list.php";
    }

    public function view() {
        $this->checkLogin();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            header("Location: index.php?url=certificate/myMerit");
            exit();
        }

        if ($this->isAdmin()) {
            $certificate = MeritCertificate::findById($id);
        } else {
            $certificate = MeritCertificate::findByIdForUser($id, (int) $_SESSION['user_id']);
        }

        if (!$certificate) {
            $_SESSION['error'] = "Certificate not found.";
            header("Location: index.php?url=certificate/myMerit");
            exit();
        }

        require "../app/views/certificate/view.php";
    }

    public function verify() {
        $code = trim((string) ($_GET['code'] ?? ''));
        $certificate = null;

        if ($code !== '') {
            $certificate = MeritCertificate::findByCode($code);
        }

        require "../app/views/certificate/verify.php";
    }
}

?>
