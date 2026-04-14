<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $studentName = '-';
    $studentEmail = '-';
    $studentId = '-';
    foreach ($students as $s) {
        if ((int) ($s['userID'] ?? 0) === (int) ($event['userID'] ?? 0)) {
            $studentName = $s['name'] ?? '-';
            $studentEmail = $s['email'] ?? '-';
            $studentId = $s['student_id'] ?? '-';
            break;
        }
    }
?>

<div class="main module-page">

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

    <?php if(isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

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
                    <label class="label">Student ID</label>
                    <input class="input" type="text" value="<?= htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8') ?>" disabled>
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
                        <label class="label">Event Type</label>
                        <?php $eventTypeValue = (string) ($event['eventType'] ?? 'Leadership'); ?>
                        <select class="input" name="eventType">
                            <option value="Leadership" <?= $eventTypeValue === 'Leadership' ? 'selected' : '' ?>>Leadership</option>
                            <option value="Volunteerism" <?= $eventTypeValue === 'Volunteerism' ? 'selected' : '' ?>>Volunteerism</option>
                            <option value="Academic" <?= $eventTypeValue === 'Academic' ? 'selected' : '' ?>>Academic</option>
                            <option value="Technical" <?= $eventTypeValue === 'Technical' ? 'selected' : '' ?>>Technical</option>
                            <option value="Sports" <?= $eventTypeValue === 'Sports' ? 'selected' : '' ?>>Sports</option>
                            <option value="Community" <?= $eventTypeValue === 'Community' ? 'selected' : '' ?>>Community</option>
                        </select>
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

                <div style="margin-top:14px;">
                    <label class="label">Reflection (Learning Outcome)</label>
                    <textarea class="input" name="reflection" rows="4"><?= htmlspecialchars($event['reflection'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="label">Status</label>
                        <?php $statusValue = (string) ($event['status'] ?? 'pending'); ?>
                        <select name="status" class="input">
                            <option value="pending" <?= $statusValue === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $statusValue === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $statusValue === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Review Note</label>
                        <input class="input" type="text" name="review_note" value="<?= htmlspecialchars($event['review_note'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Optional admin note">
                    </div>
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

