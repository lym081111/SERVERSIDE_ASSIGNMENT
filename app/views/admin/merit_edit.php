<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $studentName = '-';
    $studentEmail = '-';
    $studentId = '-';
    foreach ($students as $s) {
        if ((int) ($s['userID'] ?? 0) === (int) ($merit['userID'] ?? 0)) {
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
            <div class="topbar-title">Edit Merit Record</div>
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
            <h1 class="admin-title">Update Merit Record</h1>
            <p class="admin-subtitle">Adjust record details for the selected student.</p>
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
            <h2 class="admin-section-title">Merit Details</h2>
            <span class="admin-section-chip">Editable</span>
        </div>
        <div class="admin-section-body">
            <form method="POST" class="form">
                <?php csrf_field(); ?>

                <div class="form-grid">
                    <div>
                        <label class="label">Activity Name</label>
                        <input class="input" type="text" name="activityName" value="<?= htmlspecialchars($merit['activityName'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div>
                        <label class="label">Contribution Hours</label>
                        <input class="input" type="number" name="hours" step="0.01" min="0.01" value="<?= htmlspecialchars($merit['hours'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div>
                        <label class="label">Date From</label>
                        <input class="input" type="date" name="dateFrom" value="<?= htmlspecialchars($merit['dateFrom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div>
                        <label class="label">Date To</label>
                        <input class="input" type="date" name="dateTo" value="<?= htmlspecialchars($merit['dateTo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <div class="muted" style="margin-top:6px;">Optional for one-day activity.</div>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="label">Status</label>
                        <?php $statusValue = (string) ($merit['status'] ?? 'pending'); ?>
                        <select name="status" class="input">
                            <option value="pending" <?= $statusValue === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $statusValue === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $statusValue === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Review Note</label>
                        <input class="input" type="text" name="review_note" value="<?= htmlspecialchars($merit['review_note'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Optional admin note">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="index.php?url=merit/index" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Appeal Context</h2>
            <span class="admin-section-chip">Student resubmission</span>
        </div>
        <div class="admin-section-body">
            <?php
                $appealNote = trim((string) ($merit['appeal_note'] ?? ''));
                $appealedAt = trim((string) ($merit['appealed_at'] ?? ''));
                $resubmissionCount = (int) ($merit['resubmission_count'] ?? 0);
                $lastResubmittedAt = trim((string) ($merit['last_resubmitted_at'] ?? ''));
            ?>
            <div class="form-grid">
                <div>
                    <label class="label">Resubmission Count</label>
                    <input class="input" type="text" value="<?= (int) $resubmissionCount ?>" disabled>
                </div>
                <div>
                    <label class="label">Appealed At</label>
                    <input class="input" type="text" value="<?= htmlspecialchars($appealedAt !== '' ? $appealedAt : '-', ENT_QUOTES, 'UTF-8') ?>" disabled>
                </div>
                <div>
                    <label class="label">Last Resubmitted At</label>
                    <input class="input" type="text" value="<?= htmlspecialchars($lastResubmittedAt !== '' ? $lastResubmittedAt : '-', ENT_QUOTES, 'UTF-8') ?>" disabled>
                </div>
            </div>
            <div style="margin-top:14px;">
                <label class="label">Latest Appeal Note</label>
                <textarea class="input" rows="3" disabled><?= htmlspecialchars($appealNote !== '' ? $appealNote : '-', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Status Audit Trail</h2>
            <span class="admin-section-chip"><?= is_array($statusLogs) ? count($statusLogs) : 0 ?> entries</span>
        </div>
        <div class="admin-section-body">
            <table class="admin-table co-records-table">
                <tr>
                    <th>When</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Changed By</th>
                    <th>Source</th>
                    <th>Note</th>
                </tr>
                <?php if (empty($statusLogs)): ?>
                    <tr>
                        <td colspan="6" class="muted">No status changes logged for this record.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($statusLogs as $log): ?>
                    <?php
                        $source = (string) ($log['change_source'] ?? 'system');
                        $sourceLabel = ucwords(str_replace('_', ' ', $source));
                        $note = trim((string) ($log['change_note'] ?? ''));
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($log['created_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(ucfirst((string) ($log['from_status'] ?? 'n/a')), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(ucfirst((string) ($log['to_status'] ?? 'n/a')), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?= htmlspecialchars($log['changedByName'] ?? 'System', ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($log['changedByStudentId'])): ?>
                                <br><span class="muted"><?= htmlspecialchars($log['changedByStudentId'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($sourceLabel, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $note !== '' ? htmlspecialchars($note, ENT_QUOTES, 'UTF-8') : '<span class="muted">-</span>' ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>

