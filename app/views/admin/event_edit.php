<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $studentName = (string) ($student['name'] ?? '-');
    $studentEmail = (string) ($student['email'] ?? '-');
    $studentId = (string) ($student['student_id'] ?? '-');
    $currentClubCatalogId = (string) ($event['clubCatalogID'] ?? '');
    $currentClubName = (string) ($event['clubName'] ?? '');
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

    <?php if (isset($error)): ?>
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
                        <label class="label">Club</label>
                        <select class="input" name="clubCatalogID" required>
                            <?php if ($currentClubCatalogId === '' && $currentClubName !== ''): ?>
                                <option value="" selected><?= htmlspecialchars($currentClubName, ENT_QUOTES, 'UTF-8') ?> (legacy)</option>
                            <?php endif; ?>
                            <?php foreach ($clubCatalog as $clubDef): ?>
                                <?php $clubId = (string) ($clubDef['clubCatalogID'] ?? ''); ?>
                                <option value="<?= htmlspecialchars($clubId, ENT_QUOTES, 'UTF-8') ?>" <?= $clubId === $currentClubCatalogId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) ($clubDef['clubName'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="label">Event Title</label>
                        <input class="input" type="text" name="eventTitle" value="<?= htmlspecialchars((string) ($event['eventTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
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
                        <input class="input" type="date" name="eventDate" value="<?= htmlspecialchars((string) ($event['eventDate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div>
                        <label class="label">Event Hours</label>
                        <input class="input" type="number" name="eventHours" step="0.01" min="0.01" value="<?= htmlspecialchars((string) ($event['eventHours'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div>
                        <label class="label">Location</label>
                        <input class="input" type="text" name="location" value="<?= htmlspecialchars((string) ($event['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div>
                        <label class="label">Participant Capacity (optional)</label>
                        <input class="input" type="number" name="participantCapacity" min="1" step="1" value="<?= htmlspecialchars((string) ($event['participantCapacity'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Leave empty for unlimited">
                        <div class="muted" style="margin-top:6px;">Registered and waitlist counts are auto-calculated by approval status.</div>
                    </div>

                    <div style="display:flex;align-items:center;gap:8px;padding-top:28px;">
                        <input type="checkbox" id="waitlistEnabled" name="waitlistEnabled" value="1" <?= !isset($event['waitlistEnabled']) || (int) $event['waitlistEnabled'] === 1 ? 'checked' : '' ?>>
                        <label class="label" for="waitlistEnabled" style="margin:0;">Enable waitlist when full</label>
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <label class="label">Description</label>
                    <textarea class="input" name="description" rows="4"><?= htmlspecialchars((string) ($event['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div style="margin-top:14px;">
                    <label class="label">Reflection (Learning Outcome)</label>
                    <textarea class="input" name="reflection" rows="4"><?= htmlspecialchars((string) ($event['reflection'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
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
                        <input class="input" type="text" name="review_note" value="<?= htmlspecialchars((string) ($event['review_note'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Optional admin note">
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
