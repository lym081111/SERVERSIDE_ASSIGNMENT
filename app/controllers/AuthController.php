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
