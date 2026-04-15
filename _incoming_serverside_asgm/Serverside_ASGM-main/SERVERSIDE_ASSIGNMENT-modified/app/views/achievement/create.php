<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Achievements Module</div>
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
            <h2 style="margin:0;">Add New Achievement</h2>
            <div class="muted" style="margin-top:6px;">Record achievement results under an approved event.</div>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="index.php?url=achievement/index">Back</a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($approvedEvents)): ?>
        <div class="card">
            <div class="muted">No approved events available yet. Get an event approved first, then add achievement.</div>
        </div>
    <?php else: ?>

    <div class="card">

        <form method="POST" enctype="multipart/form-data" class="form">
            <?php csrf_field(); ?>

            <div class="form-grid">
                <div>
                    <label class="label">Approved Event</label>
                    <select class="input" name="eventID" required>
                        <option value="">Select event</option>
                        <?php foreach ($approvedEvents as $ev): ?>
                            <option value="<?= htmlspecialchars((string) ($ev['eventID'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars((string) ($ev['eventTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($ev['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($ev['eventDate'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="label">Title</label>
                    <input class="input" type="text" name="title" placeholder="e.g. 1st Prize" required>
                </div>

                <div>
                    <label class="label">Category</label>
                    <select class="input" name="category">
                        <option value="">Select category</option>
                        <option value="Leadership">Leadership</option>
                        <option value="Volunteerism">Volunteerism</option>
                        <option value="Academic">Academic</option>
                        <option value="Technical">Technical</option>
                        <option value="Sports">Sports</option>
                        <option value="Community">Community</option>
                    </select>
                </div>

                <div>
                    <label class="label">Achievement Level</label>
                    <select class="input" name="achievementLevel">
                        <option value="Faculty">Faculty</option>
                        <option value="University">University</option>
                        <option value="National">National</option>
                        <option value="International">International</option>
                    </select>
                </div>

                <div>
                    <label class="label">Date Received</label>
                    <input class="input" type="date" name="dateReceived" required>
                </div>
            </div>

            <div style="margin-top:14px;">
                <label class="label">Description</label>
                <textarea class="input" name="description" rows="4" placeholder="Add notes or result details."></textarea>
            </div>

            <div class="form-actions">
                <div style="width:100%;">
                    <label class="label">Proof Document (Optional)</label>
                    <input class="input" type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="muted" style="margin-top:6px;">Accepted: PDF, JPG, PNG (max 5MB).</div>
                </div>
                <button type="submit" class="btn">Save Record</button>
                <a href="index.php?url=achievement/index" class="btn btn-secondary">Cancel</a>
            </div>

        </form>

    </div>

    <?php endif; ?>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
