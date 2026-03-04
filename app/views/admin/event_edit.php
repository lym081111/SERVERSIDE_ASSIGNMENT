<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $studentName = '-';
    $studentEmail = '-';
    foreach ($students as $s) {
        if ((int) ($s['userID'] ?? 0) === (int) ($event['userID'] ?? 0)) {
            $studentName = $s['name'] ?? '-';
            $studentEmail = $s['email'] ?? '-';
            break;
        }
    }
?>

<div class="main">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Edit Event Record</div>
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
            <div class="admin-eyebrow">Admin Update</div>
            <h1 class="admin-title">Update Event Record</h1>
            <p class="admin-subtitle">Adjust event details for the selected student.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-secondary" href="index.php?url=event/index">Back</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Student</h2>
            <span class="admin-section-chip">Read-only</span>
        </div>
        <div class="admin-section-body">
            <div class="form-grid">
                <div>
                    <label class="label">Student Name</label>
                    <input class="input" type="text" value="<?= htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') ?>" disabled>
                </div>
                <div>
                    <label class="label">Student Email</label>
                    <input class="input" type="text" value="<?= htmlspecialchars($studentEmail, ENT_QUOTES, 'UTF-8') ?>" disabled>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Event Details</h2>
            <span class="admin-section-chip">Editable</span>
        </div>
        <div class="admin-section-body">
            <form method="POST" class="form">
                <?php csrf_field(); ?>

                <div class="form-grid">
                    <div>
                        <label class="label">Event Title</label>
                        <input class="input" type="text" name="eventTitle" value="<?= htmlspecialchars($event['eventTitle'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div>
                        <label class="label">Event Date</label>
                        <input class="input" type="date" name="eventDate" value="<?= htmlspecialchars($event['eventDate'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div>
                        <label class="label">Location</label>
                        <input class="input" type="text" name="location" value="<?= htmlspecialchars($event['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <label class="label">Description</label>
                    <textarea class="input" name="description" rows="4"><?= htmlspecialchars($event['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="index.php?url=event/index" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
