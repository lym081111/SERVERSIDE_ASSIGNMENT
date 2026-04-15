<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - Student Co-curricular System</title>

    <style>
        :root {
            --primary-color: #0b1d4d;
            --secondary-color: #3b82f6;
            --border-color: #e5e7eb;
        }

        body.login-layout {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #e0eaf5 0%, #f4f7f6 100%);
            font-family: 'Segoe UI', sans-serif;
        }

        .login-wrapper {
            display: flex;
            flex-direction: row;
            background: #ffffff;
            width: 100%;
            max-width: 1020px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 51, 102, 0.1);
            overflow: hidden;
        }

        .login-brand {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #ffffff;
            padding: 56px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .login-brand h1 {
            font-size: 2.2em;
            margin-bottom: 15px;
            font-weight: 700;
            color: #ffffff;
            border: none;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .login-brand p {
            font-size: 1.1em;
            line-height: 1.6;
            color: #ffffff;
            font-weight: 500;
            text-shadow: 0 2px 5px rgba(0,0,0,0.4);
        }

        .login-form-container {
            flex: 1;
            padding: 56px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
            margin-bottom: 30px;
            font-size: 0.95em;
        }

        .modern-input {
            width: 100%;
            padding: 12px 15px;
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
            box-sizing: border-box;
            background-color: #f8fafc;
        }

        .modern-input:focus {
            border-color: var(--secondary-color);
            background-color: #ffffff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 91, 159, 0.1);
        }

        .form-group label {
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
            display: block;
            font-size: 0.9em;
        }

        .btn-full {
            width: 100%;
            padding: 14px;
            font-size: 1.1em;
            font-weight: 600;
            border-radius: 8px;
            background-color: var(--primary-color);
            border: none;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
            margin-top: 20px;
        }

        .btn-full:hover {
            background-color: var(--secondary-color);
        }

        .btn-full:active {
            transform: scale(0.98);
        }

        .alert-box {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #059669;
            border-left: 4px solid #10b981;
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9em;
            color: #666;
        }

        .register-link a {
            font-weight: 600;
            color: var(--secondary-color);
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .reset-link {
            display: inline-block;
            margin-top: 10px;
            font-weight: 600;
            color: var(--secondary-color);
            text-decoration: none;
        }

        .reset-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; }
            .login-brand { padding: 30px 20px; }
            .login-brand h1 { font-size: 1.8em; }
            .login-form-container { padding: 40px 25px; }
        }
    </style>
</head>
<body class="login-layout">

    <div class="login-wrapper">
        <div class="login-brand">
            <h1>Password Reset</h1>
            <p>We will help you regain access to your student account in a few simple steps.</p>
        </div>

        <div class="login-form-container">
            <h2>Forgot Password</h2>
            <p class="login-subtitle">Enter your registered email to start the reset process.</p>

            <?php if(isset($success)): ?>
                <div class="alert-box alert-success">
                    <strong>Success:</strong> <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                    <?php if (!empty($_SESSION['reset_token'])): ?>
                        <br>
                        <a class="reset-link" href="index.php?url=auth/reset&token=<?= urlencode($_SESSION['reset_token']) ?>">
                            Continue to reset password
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="alert-box alert-error">
                    <strong>Error:</strong> <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <?php csrf_field(); ?>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="modern-input" placeholder="student@domain.edu" required>
                </div>

                <button type="submit" class="btn-full">Generate Reset Link</button>
            </form>

            <div class="register-link">
                Remembered your password? <a href="index.php?url=auth/login">Back to login</a>
            </div>
        </div>
    </div>

</body>
</html>
