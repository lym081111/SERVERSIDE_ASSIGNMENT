<?php
    $oldEmail = htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Co-curricular System - Forgot Password</title>

    <style>
        :root {
            --primary-color: #0b1d4d;
            --secondary-color: #3b82f6;
            --border-color: #e5e7eb;
            --error-color: #dc2626;
            --success-color: #059669;
        }

        body.login-layout {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            background: #dfe5ea;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-wrapper {
            display: flex;
            flex-direction: row;
            background: #ffffff;
            width: 100%;
            max-width: 1020px;
            border-radius: 18px;
            box-shadow: 0 18px 40px rgba(0, 51, 102, 0.12);
            overflow: hidden;
        }

        .login-brand {
            flex: 1;
            background: linear-gradient(180deg, #132a6b 0%, #3a7be0 100%);
            color: #ffffff;
            padding: 56px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .login-brand h1 {
            font-size: 2.1em;
            margin-bottom: 22px;
            font-weight: 700;
            color: #ffffff;
            border: none;
        }

        .login-brand p {
            font-size: 1.05em;
            line-height: 1.7;
            color: #ffffff;
            font-weight: 600;
        }

        .login-form-container {
            flex: 1;
            padding: 56px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .login-form-container h2 {
            margin-top: 0;
            margin-bottom: 8px;
            color: var(--primary-color);
            border: none;
            font-size: 1.8em;
        }

        .login-subtitle {
            color: #666;
            margin-bottom: 28px;
            font-size: 0.95em;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
            font-size: 0.95em;
        }

        .modern-input {
            width: 100%;
            padding: 14px 15px;
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
            box-sizing: border-box;
            background-color: #f5f7fa;
        }

        .modern-input:focus {
            border-color: var(--secondary-color);
            background-color: #ffffff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        }

        .input-error {
            border-color: var(--error-color);
            background-color: #fff5f5;
        }

        .btn-full {
            width: 100%;
            padding: 14px;
            font-size: 1.1em;
            font-weight: 600;
            border-radius: 8px;
            background-color: #0b1d5b;
            border: none;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
            margin-top: 6px;
        }

        .btn-full:hover {
            background-color: #12307f;
        }

        .btn-full:active {
            transform: scale(0.98);
        }

        .alert-box {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.92em;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #b91c1c;
            border-left: 4px solid #dc2626;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .back-link {
            text-align: center;
            margin-top: 24px;
            font-size: 0.92em;
            color: #666;
        }

        .back-link a {
            font-weight: 600;
            color: #2563eb;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .reset-link-box {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 8px;
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            color: #1e3a8a;
            font-size: 0.92em;
        }

        .reset-link-box p {
            margin: 0 0 12px 0;
        }

        .reset-btn {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .reset-btn:hover {
            background: #1e40af;
        }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
            }

            .login-brand {
                padding: 32px 24px;
            }

            .login-form-container {
                padding: 36px 24px;
            }

            .login-brand h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body class="login-layout">

<div class="login-wrapper">
    <div class="login-brand">
        <h1>Password Reset</h1>
        <p>
            We will help you regain access to your student
            account in a few simple steps.
        </p>
    </div>

    <div class="login-form-container">
        <h2>Forgot Password</h2>
        <p class="login-subtitle">Enter your registered email to start the reset process.</p>

        <?php if (isset($error)): ?>
            <div class="alert-box alert-error">
                <strong>Error:</strong> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert-box alert-success">
                <strong>Success:</strong> <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <?php csrf_field(); ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="modern-input <?= isset($error) ? 'input-error' : '' ?>"
                    placeholder="student@domain.edu"
                    value="<?= $oldEmail ?>"
                    required
                >
            </div>

            <button type="submit" class="btn-full">Confirm Email</button>
        </form>

        <?php if (isset($_SESSION['reset_token']) && isset($_SESSION['reset_email']) && isset($success)): ?>
            <div class="reset-link-box">
                <p>Click the button below to continue resetting your password.</p>
                <a
                    href="index.php?url=auth/reset&token=<?= urlencode($_SESSION['reset_token']) ?>"
                    class="reset-btn"
                >
                    Reset Password Now
                </a>
            </div>
        <?php endif; ?>

        <div class="back-link">
            Remembered your password? <a href="index.php?url=auth/login">Back to login</a>
        </div>
    </div>
</div>

</body>
</html>