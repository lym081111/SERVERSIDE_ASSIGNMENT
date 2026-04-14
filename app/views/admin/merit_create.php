<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main module-page">

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
            <p class="admin-subtitle">Select a student and link merit to one of their approved events.</p>
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
            <h2 class="admin-section-title">Student and Event Selection</h2>
            <span class="admin-section-chip">Required</span>
        </div>
        <div class="admin-section-body">
            <form method="POST" class="form" id="adminMeritEventForm">
                <?php csrf_field(); ?>

                <div class="form-grid">
                    <div>
                        <label class="label">Student ID (searchable)</label>
                        <input class="input" type="text" name="studentId" list="student-ids" placeholder="Start typing student ID...">
                        <datalist id="student-ids">
                            <?php foreach ($students as $s): ?>
                                <option value="<?= htmlspecialchars((string) ($s['student_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="label">Student Email (searchable)</label>
                        <input class="input" type="text" name="studentEmail" list="student-emails" placeholder="Start typing email...">
                        <datalist id="student-emails">
                            <?php foreach ($students as $s): ?>
                                <option value="<?= htmlspecialchars((string) ($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="label">Or Select Student</label>
                        <select class="input" name="studentID">
                            <option value="">Select student</option>
                            <?php foreach ($students as $s): ?>
                                <option value="<?= htmlspecialchars((string) ($s['userID'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars((string) ($s['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($s['student_id'] ?? '-'), ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars((string) ($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="label">Approved Event</label>
                        <select class="input" name="eventID" id="eventID" required>
                            <option value="">Select event</option>
                            <?php foreach ($approvedEvents as $ev): ?>
                                <option
                                    value="<?= htmlspecialchars((string) ($ev['eventID'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-title="<?= htmlspecialchars((string) ($ev['eventTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-hours="<?= htmlspecialchars((string) ($ev['eventHours'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-date="<?= htmlspecialchars((string) ($ev['eventDate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-club="<?= htmlspecialchars((string) ($ev['clubName'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars((string) ($ev['eventTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($ev['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($ev['userName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($ev['studentId'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>)
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
