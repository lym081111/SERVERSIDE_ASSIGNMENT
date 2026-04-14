<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Merit Tracker Module</div>
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
            <h2 style="margin:0;">Edit Merit Record</h2>
            <div class="muted" style="margin-top:6px;">Update the record details and submit for admin review.</div>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="index.php?url=merit/index">Back</a>
        </div>
    </div>

    <?php if(isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php
        $currentStatus = (string) ($merit['status'] ?? 'pending');
        $isRejected = $currentStatus === 'rejected';
        $reviewNote = trim((string) ($merit['review_note'] ?? ''));
        $appealValue = isset($_POST['appeal_note'])
            ? (string) $_POST['appeal_note']
            : (string) ($merit['appeal_note'] ?? '');
    ?>

    <?php if ($isRejected): ?>
        <div class="error" style="margin-bottom:14px;">
            This record was rejected. Please add clarification in the appeal note and optionally upload new proof before resubmitting.
            <?php if ($reviewNote !== ''): ?>
                <div style="margin-top:8px;">
                    Admin feedback: <?= htmlspecialchars($reviewNote, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" enctype="multipart/form-data" class="form">
            <?php csrf_field(); ?>

            <div class="form-grid">
                <div>
                    <label class="label">Activity Name</label>
                    <input class="input" type="text" name="activityName" value="<?= htmlspecialchars($merit['activityName'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div>
                    <label class="label">Contribution Hours</label>
                    <input class="input" type="number" name="hours" step="0.01" min="0.01" value="<?= htmlspecialchars($merit['hours'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div>
                    <label class="label">Date From</label>
                    <input class="input" type="date" name="dateFrom" value="<?= htmlspecialchars($merit['dateFrom'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div>
                    <label class="label">Date To</label>
                    <input class="input" type="date" name="dateTo" value="<?= htmlspecialchars($merit['dateTo'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="muted" style="margin-top:6px;">Optional for one-day activity. If empty, it follows Date From.</div>
                </div>
            </div>

            <?php if ($isRejected): ?>
                <div style="margin-top:14px;">
                    <label class="label">Appeal Note</label>
                    <textarea class="input" name="appeal_note" rows="4" placeholder="Explain what was corrected and why this merit should be approved."><?= htmlspecialchars($appealValue, ENT_QUOTES, 'UTF-8') ?></textarea>
                    <div class="muted" style="margin-top:6px;">Tip: include short evidence context (what, when, who verified).</div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <div style="width:100%;">
                    <label class="label"><?= $isRejected ? 'Supporting Proof for Appeal (Optional)' : 'Proof Document (Optional)' ?></label>
                    <input class="input" type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png">
                    <?php if (!empty($merit['evidence_path'])): ?>
                        <div class="muted" style="margin-top:6px;">
                            Current file:
                            <a class="link" href="<?= htmlspecialchars(BASE_URL . ltrim((string) $merit['evidence_path'], '/'), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">View evidence</a>
                        </div>
                    <?php else: ?>
                        <div class="muted" style="margin-top:6px;">No file uploaded yet. Accepted: PDF, JPG, PNG (max 5MB).</div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn">Save Changes</button>
                <a href="index.php?url=merit/index" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
