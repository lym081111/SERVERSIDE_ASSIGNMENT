<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Add Merit Record</div>
            <div class="topbar-user-inline">
                <?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
        <div class="topbar-actions">
            <div class="admin-badge">Administrator</div>
            <form method="POST" action="index.php?url=auth/logout">
                <?php csrf_field(); ?>
                <button type="submit" class="topbar-logout">Logout</button>
            </form>
        </div>
    </div>

    <div class="content admin-content">
        <div class="content-inner">

    <div class="admin-hero">
        <div>
            <div class="admin-eyebrow">Admin Entry</div>
            <h1 class="admin-title">Create Merit for Student</h1>
            <p class="admin-subtitle">Search a student and log merit activity on their behalf.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-secondary" href="index.php?url=merit/index">Back</a>
        </div>
    </div>

    <?php if(isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Student Selection</h2>
            <span class="admin-section-chip">Required</span>
        </div>
        <div class="admin-section-body">
            <form method="POST" class="form">
                <?php csrf_field(); ?>

                <div class="form-grid">
                    <div>
                        <label class="label">Student Email (searchable)</label>
                        <input class="input" type="text" name="studentEmail" list="student-emails" placeholder="Start typing email...">
                        <datalist id="student-emails">
                            <?php foreach ($students as $s): ?>
                                <option value="<?= htmlspecialchars($s['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="label">Or Select Student</label>
                        <select class="input" name="studentID">
                            <option value="">Select student</option>
                            <?php foreach ($students as $s): ?>
                                <option value="<?= htmlspecialchars($s['userID'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($s['name'] ?? '', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($s['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="label">Activity Name</label>
                        <input class="input" type="text" name="activityName" placeholder="e.g. Volunteer Program" required>
                    </div>

                    <div>
                        <label class="label">Contribution Hours</label>
                        <input class="input" type="number" name="hours" step="0.01" min="0.01" placeholder="e.g. 2" required>
                    </div>

                    <div>
                        <label class="label">Date From</label>
                        <input class="input" type="date" name="dateFrom">
                    </div>

                    <div>
                        <label class="label">Date To</label>
                        <input class="input" type="date" name="dateTo">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Save Record</button>
                    <a href="index.php?url=merit/index" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
