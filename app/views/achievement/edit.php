<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $selectedEventID = isset($_POST['eventID'])
        ? (int) $_POST['eventID']
        : (int) ($achievement['eventID'] ?? 0);
    $filterSearch = htmlspecialchars((string) ($_GET['search'] ?? ''), ENT_QUOTES, 'UTF-8');
    $filterSort = htmlspecialchars((string) ($_GET['sort'] ?? ''), ENT_QUOTES, 'UTF-8');
    $filterStatus = htmlspecialchars((string) ($_GET['status'] ?? ''), ENT_QUOTES, 'UTF-8');
?>

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
            <h2 style="margin:0;">Edit Achievement</h2>
            <div class="muted" style="margin-top:6px;">Update the achievement details.</div>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="index.php?url=achievement/index&search=<?= $filterSearch ?>&sort=<?= $filterSort ?>&status=<?= $filterStatus ?>">Back</a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($approvedEvents)): ?>
        <div class="card">
            <div class="muted">No approved events are available for this record.</div>
        </div>
    <?php else: ?>

    <div class="card">
        <form method="POST" enctype="multipart/form-data" class="form">
            <?php csrf_field(); ?>
            <input type="hidden" name="_filter_search" value="<?= $filterSearch ?>">
            <input type="hidden" name="_filter_sort" value="<?= $filterSort ?>">
            <input type="hidden" name="_filter_status" value="<?= $filterStatus ?>">

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
                    <input class="input" type="text" name="title" value="<?= htmlspecialchars((string) ($achievement['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div>
                    <label class="label">Category (Auto from Event)</label>
                    <?php $categoryValue = (string) ($achievement['category'] ?? ''); ?>
                    <input class="input" type="text" id="achievementCategoryDisplay" value="<?= htmlspecialchars($categoryValue !== '' ? $categoryValue : '-', ENT_QUOTES, 'UTF-8') ?>" disabled>
                    <input type="hidden" name="category" id="achievementCategoryValue" value="<?= htmlspecialchars($categoryValue, ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div>
                    <label class="label">Achievement Level</label>
                    <?php $levelValue = (string) ($achievement['achievementLevel'] ?? 'Faculty'); ?>
                    <select class="input" name="achievementLevel">
                        <option value="Faculty" <?= $levelValue === 'Faculty' ? 'selected' : '' ?>>Faculty</option>
                        <option value="University" <?= $levelValue === 'University' ? 'selected' : '' ?>>University</option>
                        <option value="National" <?= $levelValue === 'National' ? 'selected' : '' ?>>National</option>
                        <option value="International" <?= $levelValue === 'International' ? 'selected' : '' ?>>International</option>
                    </select>
                </div>

                <div>
                    <label class="label">Date Received</label>
                    <input class="input" type="date" name="dateReceived" value="<?= htmlspecialchars((string) ($achievement['dateReceived'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
            </div>

            <div style="margin-top:14px;">
                <label class="label">Description</label>
                <textarea class="input" name="description" rows="4"><?= htmlspecialchars((string) ($achievement['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-actions">
                <div style="width:100%;">
                    <label class="label">Proof Document (Optional)</label>
                    <input class="input" type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png">
                    <?php if (!empty($achievement['evidence_path'])): ?>
                        <div class="muted" style="margin-top:6px;">
                            Current file:
                            <a class="link" href="<?= htmlspecialchars(BASE_URL . ltrim((string) $achievement['evidence_path'], '/'), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">View evidence</a>
                        </div>
                    <?php else: ?>
                        <div class="muted" style="margin-top:6px;">No file uploaded yet. Accepted: PDF, JPG, PNG (max 5MB).</div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn">Save Changes</button>
                <a href="index.php?url=achievement/index&search=<?= $filterSearch ?>&sort=<?= $filterSort ?>&status=<?= $filterStatus ?>" class="btn btn-secondary">Cancel</a>
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
