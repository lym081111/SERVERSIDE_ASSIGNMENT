<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $studentName = (string) ($student['name'] ?? '-');
    $studentEmail = (string) ($student['email'] ?? '-');
    $studentId = (string) ($student['student_id'] ?? '-');

    $currentRequestType = ((string) ($club['request_type'] ?? 'join')) === 'role_change' ? 'role_change' : 'join';
    $currentClubName = (string) ($club['clubName'] ?? '');
    $currentCatalogId = '';

    foreach ($clubCatalog as $catalogRow) {
        if (strcasecmp((string) ($catalogRow['clubName'] ?? ''), $currentClubName) === 0) {
            $currentCatalogId = (string) ($catalogRow['clubCatalogID'] ?? '');
            break;
        }
    }
?>

<div class="main module-page">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Edit Club Record</div>
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
            <h1 class="admin-title">Update Club Request</h1>
            <p class="admin-subtitle">Review and maintain student join and role-change requests.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-secondary" href="index.php?url=club/index">Back</a>
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
            <h2 class="admin-section-title">Request Details</h2>
            <span class="admin-section-chip">Editable</span>
        </div>
        <div class="admin-section-body">
            <form method="POST" class="form">
                <?php csrf_field(); ?>

                <div class="form-grid">
                    <div>
                        <label class="label">Club</label>
                        <select class="input" name="clubName" required>
                            <?php if ($currentCatalogId === '' && $currentClubName !== ''): ?>
                                <option value="<?= htmlspecialchars($currentClubName, ENT_QUOTES, 'UTF-8') ?>" selected>
                                    <?= htmlspecialchars($currentClubName, ENT_QUOTES, 'UTF-8') ?> (legacy)
                                </option>
                            <?php endif; ?>
                            <?php foreach ($clubCatalog as $catalogRow): ?>
                                <?php
                                    $catalogName = (string) ($catalogRow['clubName'] ?? '');
                                    $catalogId = (string) ($catalogRow['clubCatalogID'] ?? '');
                                ?>
                                <option value="<?= htmlspecialchars($catalogName, ENT_QUOTES, 'UTF-8') ?>" <?= $catalogId === $currentCatalogId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($catalogName, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="label">Request Type</label>
                        <select name="requestType" id="requestType" class="input">
                            <option value="join" <?= $currentRequestType === 'join' ? 'selected' : '' ?>>Join Club</option>
                            <option value="role_change" <?= $currentRequestType === 'role_change' ? 'selected' : '' ?>>Role Change</option>
                        </select>
                    </div>

                    <div>
                        <label class="label">Role</label>
                        <input class="input" type="text" name="role" id="roleInput" value="<?= htmlspecialchars((string) ($club['role'] ?? 'Member'), ENT_QUOTES, 'UTF-8') ?>" placeholder="Member / Secretary / Treasurer">
                    </div>

                    <div>
                        <label class="label">Start Date</label>
                        <input class="input" type="date" name="startDate" value="<?= htmlspecialchars((string) ($club['startDate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div>
                        <label class="label">End Date</label>
                        <input class="input" type="date" name="endDate" value="<?= htmlspecialchars((string) ($club['endDate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="muted" style="margin-top:6px;">Leave blank if this membership is still active.</div>
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <label class="label">Reason / Description</label>
                    <textarea class="input" name="roleDescription" rows="4"><?= htmlspecialchars((string) ($club['roleDescription'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="label">Status</label>
                        <?php $statusValue = (string) ($club['status'] ?? 'pending'); ?>
                        <select name="status" class="input">
                            <option value="pending" <?= $statusValue === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $statusValue === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $statusValue === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Review Note</label>
                        <input class="input" type="text" name="review_note" value="<?= htmlspecialchars((string) ($club['review_note'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Optional admin note">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="index.php?url=club/index" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

        </div>
    </div>

</div>

<script>
(function () {
    var requestType = document.getElementById('requestType');
    var roleInput = document.getElementById('roleInput');

    if (!requestType || !roleInput) {
        return;
    }

    function syncRole() {
        if (requestType.value === 'join') {
            roleInput.value = 'Member';
        }
    }

    requestType.addEventListener('change', syncRole);
    syncRole();
})();
</script>

<?php require "../app/views/layout/footer.php"; ?>
