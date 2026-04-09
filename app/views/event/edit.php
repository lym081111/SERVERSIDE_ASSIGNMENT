<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Event Tracker Module</div>
            <div class="topbar-user-inline">
                <?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
        <div class="topbar-actions">
            <form method="POST" action="index.php?url=auth/logout">
                <?php csrf_field(); ?>
                <button type="submit" class="topbar-logout">Logout</button>
            </form>
        </div>
    </div>

    <div class="content">
        <div class="content-inner">

    <div class="page-header">
        <div>
            <h2 style="margin:0;">Edit Event Record</h2>
            <div class="muted" style="margin-top:6px;">Update the record details and save your changes.</div>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="index.php?url=event/index">Back</a>
        </div>
    </div>

    <?php if(isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" enctype="multipart/form-data" class="form">
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
                <label class="label">Reflection (What did you learn?)</label>
                <textarea class="input" name="reflection" rows="4"><?= htmlspecialchars($event['reflection'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-actions">
                <div style="width:100%;">
                    <label class="label">Proof Document (Optional)</label>
                    <input class="input" type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png">
                    <?php if (!empty($event['evidence_path'])): ?>
                        <div class="muted" style="margin-top:6px;">
                            Current file:
                            <a class="link" href="<?= htmlspecialchars(BASE_URL . ltrim((string) $event['evidence_path'], '/'), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">View evidence</a>
                        </div>
                    <?php else: ?>
                        <div class="muted" style="margin-top:6px;">No file uploaded yet. Accepted: PDF, JPG, PNG (max 5MB).</div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn">Save Changes</button>
                <a href="index.php?url=event/index" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
