<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $studentName = (string) ($student['name'] ?? '-');
    $studentEmail = (string) ($student['email'] ?? '-');
    $studentId = (string) ($student['student_id'] ?? '-');
    $selectedEventID = isset($_POST['eventID'])
        ? (int) $_POST['eventID']
        : (int) ($achievement['eventID'] ?? 0);
    $selectedTitle = isset($_POST['title'])
        ? trim((string) $_POST['title'])
        : trim((string) ($achievement['title'] ?? ''));
    $titleNormalizationMap = [
        'consolation prize' => 'Consolation',
        'participant' => 'Participant / Certificate',
        'participate / certificate' => 'Participant / Certificate',
    ];
    $selectedTitleNormalizedKey = strtolower($selectedTitle);
    if (isset($titleNormalizationMap[$selectedTitleNormalizedKey])) {
        $selectedTitle = $titleNormalizationMap[$selectedTitleNormalizedKey];
    }
?>

<div class="main module-page">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Edit Achievement Record</div>
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
            <h1 class="admin-title">Update Achievement Record</h1>
            <p class="admin-subtitle">Adjust achievement details for the selected student.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-secondary" href="index.php?url=achievement/index">Back</a>
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
            <h2 class="admin-section-title">Achievement Details</h2>
            <span class="admin-section-chip">Editable</span>
        </div>
        <div class="admin-section-body">
            <form method="POST" class="form">
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
                        <input class="input" type="text" id="achievementCategoryDisplay" value="<?= htmlspecialchars((string) (($achievement['category'] ?? '') !== '' ? $achievement['category'] : '-'), ENT_QUOTES, 'UTF-8') ?>" disabled>
                        <input type="hidden" name="category" id="achievementCategoryValue" value="<?= htmlspecialchars((string) ($achievement['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div>
                        <label class="label">Achievement Level</label>
                        <?php $achievementLevelValue = (string) ($achievement['achievementLevel'] ?? 'Faculty'); ?>
                        <select class="input" name="achievementLevel">
                            <option value="Faculty" <?= $achievementLevelValue === 'Faculty' ? 'selected' : '' ?>>Faculty</option>
                            <option value="University" <?= $achievementLevelValue === 'University' ? 'selected' : '' ?>>University</option>
                            <option value="National" <?= $achievementLevelValue === 'National' ? 'selected' : '' ?>>National</option>
                            <option value="International" <?= $achievementLevelValue === 'International' ? 'selected' : '' ?>>International</option>
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

                <div style="margin-top:14px;">
                    <label class="label">Proof Document</label>
                    <?php $evidencePath = trim((string) ($achievement['evidence_path'] ?? '')); ?>
                    <?php if ($evidencePath !== ''): ?>
                        <div class="muted">
                            <a class="link" href="<?= htmlspecialchars(BASE_URL . ltrim($evidencePath, '/'), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">View uploaded evidence</a>
                        </div>
                    <?php else: ?>
                        <div class="error" style="margin:0;">No evidence uploaded. This record cannot be approved until evidence is provided.</div>
                    <?php endif; ?>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="label">Status</label>
                        <?php $statusValue = (string) ($achievement['status'] ?? 'pending'); ?>
                        <select name="status" class="input">
                            <option value="pending" <?= $statusValue === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $statusValue === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $statusValue === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Review Note</label>
                        <input class="input" type="text" name="review_note" value="<?= htmlspecialchars((string) ($achievement['review_note'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Optional admin note">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="index.php?url=achievement/index" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

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
