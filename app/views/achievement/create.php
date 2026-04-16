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

        <?php
            $selectedEventID = isset($_POST['eventID']) ? (int) $_POST['eventID'] : 0;
            $selectedTitle = trim((string) ($_POST['title'] ?? ''));
            $selectedEventCategory = '';
            foreach ($approvedEvents as $eventRow) {
                if ((int) ($eventRow['eventID'] ?? 0) === $selectedEventID) {
                    $selectedEventCategory = trim((string) ($eventRow['eventType'] ?? ''));
                    break;
                }
            }
        ?>

        <form method="POST" enctype="multipart/form-data" class="form">
            <?php csrf_field(); ?>

            <div class="form-grid">
                <div>
                    <label class="label">Approved Event</label>
                    <select class="input" name="eventID" id="achievementEventID" required>
                        <option value="">Select event</option>
                        <?php foreach ($approvedEvents as $ev): ?>
                            <?php $eventId = (int) ($ev['eventID'] ?? 0); ?>
                            <option
                                value="<?= htmlspecialchars((string) $eventId, ENT_QUOTES, 'UTF-8') ?>"
                                data-category="<?= htmlspecialchars((string) ($ev['eventType'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                <?= $eventId === $selectedEventID ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) ($ev['eventTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($ev['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($ev['eventDate'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="label">Title</label>
                    <select class="input" name="title" required>
                        <option value="">Select title</option>
                        <?php foreach ($achievementTitleOptions as $titleOption): ?>
                            <option value="<?= htmlspecialchars($titleOption, ENT_QUOTES, 'UTF-8') ?>" <?= $selectedTitle === $titleOption ? 'selected' : '' ?>>
                                <?= htmlspecialchars($titleOption, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="label">Category (Auto from Event)</label>
                    <input class="input" type="text" id="achievementCategoryDisplay" value="<?= htmlspecialchars($selectedEventCategory !== '' ? $selectedEventCategory : '-', ENT_QUOTES, 'UTF-8') ?>" disabled>
                    <input type="hidden" name="category" id="achievementCategoryValue" value="<?= htmlspecialchars($selectedEventCategory, ENT_QUOTES, 'UTF-8') ?>">
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
                    <label class="label">Proof Document (Required)</label>
                    <input class="input" type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png" required>
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

<script>
(function () {
    var eventSelect = document.getElementById('achievementEventID');
    var categoryDisplay = document.getElementById('achievementCategoryDisplay');
    var categoryValue = document.getElementById('achievementCategoryValue');

    if (!eventSelect || !categoryDisplay || !categoryValue) {
        return;
    }

    function syncCategoryFromEvent() {
        var selected = eventSelect.options[eventSelect.selectedIndex];
        var category = selected ? (selected.getAttribute('data-category') || '').trim() : '';
        categoryDisplay.value = category !== '' ? category : '-';
        categoryValue.value = category;
    }

    eventSelect.addEventListener('change', syncCategoryFromEvent);
    syncCategoryFromEvent();
})();
</script>

<?php require "../app/views/layout/footer.php"; ?>
