<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $studentName = (string) ($student['name'] ?? '-');
    $studentEmail = (string) ($student['email'] ?? '-');
    $studentId = (string) ($student['student_id'] ?? '-');
    $selectedEventID = isset($_POST['eventID'])
        ? (int) $_POST['eventID']
        : (int) ($merit['eventID'] ?? 0);
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
            <h2 class="admin-section-title">Merit Details</h2>
            <span class="admin-section-chip">Editable</span>
        </div>
        <div class="admin-section-body">
            <form method="POST" class="form" id="adminMeritEditForm">
                <?php csrf_field(); ?>

                <div class="form-grid">
                    <div>
                        <label class="label">Approved Event</label>
                        <select class="input" name="eventID" id="eventID" required>
                            <option value="">Select event</option>
                            <?php foreach ($approvedEvents as $ev): ?>
                                <?php $eventId = (int) ($ev['eventID'] ?? 0); ?>
                                <option
                                    value="<?= htmlspecialchars((string) $eventId, ENT_QUOTES, 'UTF-8') ?>"
                                    data-title="<?= htmlspecialchars((string) ($ev['eventTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-hours="<?= htmlspecialchars((string) ($ev['eventHours'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-date="<?= htmlspecialchars((string) ($ev['eventDate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-club="<?= htmlspecialchars((string) ($ev['clubName'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $eventId === $selectedEventID ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) ($ev['eventTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($ev['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($ev['eventDate'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="label">Club</label>
                        <input class="input" type="text" id="clubDisplay" value="-" disabled>
                    </div>

                    <div>
                        <label class="label">Activity Name (From Event)</label>
                        <input class="input" type="text" id="activityDisplay" value="-" disabled>
                    </div>

                    <div>
                        <label class="label">Merit Hours (From Event)</label>
                        <input class="input" type="text" id="hoursDisplay" value="-" disabled>
                    </div>

                    <div>
                        <label class="label">Date From</label>
                        <input class="input" type="text" id="dateFromDisplay" value="-" disabled>
                    </div>

                    <div>
                        <label class="label">Date To</label>
                        <input class="input" type="text" id="dateToDisplay" value="-" disabled>
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
                        <input class="input" type="text" name="review_note" value="<?= htmlspecialchars((string) ($merit['review_note'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Optional admin note">
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
                        <td><?= htmlspecialchars((string) ($log['created_at'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(ucfirst((string) ($log['from_status'] ?? 'n/a')), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(ucfirst((string) ($log['to_status'] ?? 'n/a')), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?= htmlspecialchars((string) ($log['changedByName'] ?? 'System'), ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($log['changedByStudentId'])): ?>
                                <br><span class="muted"><?= htmlspecialchars((string) $log['changedByStudentId'], ENT_QUOTES, 'UTF-8') ?></span>
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

<script>
(function () {
    var select = document.getElementById('eventID');
    var activityDisplay = document.getElementById('activityDisplay');
    var hoursDisplay = document.getElementById('hoursDisplay');
    var dateFromDisplay = document.getElementById('dateFromDisplay');
    var dateToDisplay = document.getElementById('dateToDisplay');
    var clubDisplay = document.getElementById('clubDisplay');

    if (!select || !activityDisplay || !hoursDisplay || !dateFromDisplay || !dateToDisplay || !clubDisplay) {
        return;
    }

    function syncEventPreview() {
        var opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) {
            activityDisplay.value = '-';
            hoursDisplay.value = '-';
            dateFromDisplay.value = '-';
            dateToDisplay.value = '-';
            clubDisplay.value = '-';
            return;
        }

        activityDisplay.value = opt.getAttribute('data-title') || '-';
        hoursDisplay.value = opt.getAttribute('data-hours') || '-';
        dateFromDisplay.value = opt.getAttribute('data-date') || '-';
        dateToDisplay.value = opt.getAttribute('data-date') || '-';
        clubDisplay.value = opt.getAttribute('data-club') || '-';
    }

    select.addEventListener('change', syncEventPreview);
    syncEventPreview();
})();
</script>

<?php require "../app/views/layout/footer.php"; ?>
