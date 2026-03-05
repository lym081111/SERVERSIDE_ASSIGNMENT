<?php

class AuthController {

    public function login() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = User::findByEmail($email);

            if ($user && password_verify($password, $user['passwordHash'])) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['userID'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['isAdmin'] = $user['isAdmin'];

                setcookie("last_login", date("Y-m-d H:i:s"), [
                    'expires'  => time() + 86400 * 30,
                    'path'     => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                ]);

                if (!empty($user['isAdmin'])) {
                    header("Location: index.php?url=admin/index");
                } else {
                    header("Location: index.php?url=dashboard/index");
                }
                exit();
            }

            $error = "Invalid email or password.";
            require "../app/views/auth/login.php";
            return;
        }

        require "../app/views/auth/login.php";
    }

    public function register() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $name = trim((string) ($_POST['name'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            if ($name === '' || $email === '' || $password === '') {
                $error = "Please fill in all fields.";
                require "../app/views/auth/register.php";
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address.";
                require "../app/views/auth/register.php";
                return;
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            try {
                User::create($name, $email, $passwordHash);
            } catch (PDOException $e) {
                $error = "Error: Name or Email might already exist.";
                require "../app/views/auth/register.php";
                return;
            }

            $_SESSION['success'] = "Registration successful! You can now log in.";
            header("Location: index.php?url=auth/login");
            exit();
        }

        require "../app/views/auth/register.php";
    }

    public function forgot() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $email = trim((string) ($_POST['email'] ?? ''));

            if ($email === '') {
                $error = "Please enter your registered email.";
                require "../app/views/auth/forgot.php";
                return;
            }

            $user = User::findByEmail($email);

            if (!$user) {
                $error = "Email not found. Please use a registered email address.";
                require "../app/views/auth/forgot.php";
                return;
            }

            $_SESSION['reset_email'] = $user['email'];
            $_SESSION['reset_token'] = bin2hex(random_bytes(16));
            $_SESSION['reset_token_time'] = time();

            $success = "Reset request created. Use the simulated link below to continue.";
            require "../app/views/auth/forgot.php";
            return;
        }

        require "../app/views/auth/forgot.php";
    }

    public function reset() {

        if (empty($_SESSION['reset_email']) || empty($_SESSION['reset_token'])) {
            $error = "Please request a password reset first.";
            require "../app/views/auth/forgot.php";
            return;
        }

        $tokenParam = (string) ($_GET['token'] ?? '');

        if ($tokenParam !== '' && !hash_equals($_SESSION['reset_token'], $tokenParam)) {
            $error = "Invalid or expired reset link.";
            require "../app/views/auth/forgot.php";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            verify_csrf();

            $token = (string) ($_POST['token'] ?? '');
            $password = (string) ($_POST['password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');

            if (!hash_equals($_SESSION['reset_token'], $token)) {
                $error = "Invalid or expired reset link.";
                require "../app/views/auth/reset.php";
                return;
            }

            if ($password === '' || $confirm === '') {
                $error = "Please fill in all fields.";
                require "../app/views/auth/reset.php";
                return;
            }

            if ($password !== $confirm) {
                $error = "Passwords do not match.";
                require "../app/views/auth/reset.php";
                return;
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            User::updatePasswordByEmail($_SESSION['reset_email'], $passwordHash);

            unset($_SESSION['reset_email'], $_SESSION['reset_token'], $_SESSION['reset_token_time']);

            $_SESSION['success'] = "Password reset successful! You can now log in.";
            header("Location: index.php?url=auth/login");
            exit();
        }

        require "../app/views/auth/reset.php";
    }

    public function logout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=dashboard/index");
            exit();
        }

        verify_csrf();
        session_destroy();
        header("Location: index.php?url=auth/login");
        exit();
    }
}

?>
