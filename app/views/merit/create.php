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
            <h2 style="margin:0;">Add New Merit Record</h2>
            <div class="muted" style="margin-top:6px;">Select an approved event to auto-link merit hours.</div>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="index.php?url=merit/index">Back</a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($approvedEvents)): ?>
        <div class="card">
            <div class="muted">No approved events available yet. Get an event approved first, then add merit.</div>
        </div>
    <?php else: ?>

    <div class="card">

        <form method="POST" enctype="multipart/form-data" class="form" id="meritEventForm">
            <?php csrf_field(); ?>

            <div class="form-grid">
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
                                <?= htmlspecialchars((string) ($ev['eventTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($ev['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($ev['eventDate'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="label">Club</label>
                    <input class="input" type="text" id="clubDisplay" value="-" readonly>
                </div>

                <div>
                    <label class="label">Activity Name (From Event)</label>
                    <input class="input" type="text" id="activityDisplay" value="-" readonly>
                </div>

                <div>
                    <label class="label">Merit Hours (From Event)</label>
                    <input class="input" type="text" id="hoursDisplay" value="-" readonly>
                </div>

                <div>
                    <label class="label">Date From</label>
                    <input class="input" type="text" id="dateFromDisplay" value="-" readonly>
                </div>

                <div>
                    <label class="label">Date To</label>
                    <input class="input" type="text" id="dateToDisplay" value="-" readonly>
                </div>
            </div>

            <div class="form-actions">
                <div style="width:100%;">
                    <label class="label">Proof Document (Optional)</label>
                    <input class="input" type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="muted" style="margin-top:6px;">Accepted: PDF, JPG, PNG (max 5MB).</div>
                </div>
                <button type="submit" class="btn">Save Record</button>
                <a href="index.php?url=merit/index" class="btn btn-secondary">Cancel</a>
            </div>

        </form>

    </div>

    <?php endif; ?>

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
