<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $roleChangeOptions = isset($roleChangeOptions) && is_array($roleChangeOptions) && !empty($roleChangeOptions)
        ? $roleChangeOptions
        : ['President', 'Vice President', 'Secretary', 'Treasurer', 'Committee Member', 'Club Leader'];
    $currentRequestType = ((string) ($_POST['requestType'] ?? ($club['request_type'] ?? 'join'))) === 'role_change' ? 'role_change' : 'join';
    $currentDesiredRole = $currentRequestType === 'role_change'
        ? trim((string) ($_POST['desiredRole'] ?? ($club['role'] ?? '')))
        : '';
    $selectedCatalogId = '';
    $currentClubName = (string) ($club['clubName'] ?? '');
    foreach ($clubCatalog as $clubDef) {
        if (strcasecmp((string) ($clubDef['clubName'] ?? ''), $currentClubName) === 0) {
            $selectedCatalogId = (string) ($clubDef['clubCatalogID'] ?? '');
            break;
        }
    }
?>

<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Club Tracker Module</div>
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
            <h2 style="margin:0;">Edit Club Request</h2>
            <div class="muted" style="margin-top:6px;">Update your pending or rejected club request.</div>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="index.php?url=club/index">Back</a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($clubCatalog)): ?>
        <div class="card">
            <div class="muted">No active clubs are available for updates. Please contact admin.</div>
        </div>
    <?php else: ?>

    <div class="card">
        <form method="POST" enctype="multipart/form-data" class="form" id="clubRequestForm">
            <?php csrf_field(); ?>

            <div class="form-grid">
                <div>
                    <label class="label">Club</label>
                    <select class="input" name="clubCatalogID" required>
                        <option value="">Select club</option>
                        <?php foreach ($clubCatalog as $clubDef): ?>
                            <?php $value = (string) ($clubDef['clubCatalogID'] ?? ''); ?>
                            <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" <?= $selectedCatalogId === $value ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) ($clubDef['clubName'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="label">Request Type</label>
                    <select class="input" name="requestType" id="requestType" required>
                        <option value="join" <?= $currentRequestType === 'join' ? 'selected' : '' ?>>Join club (Member)</option>
                        <option value="role_change" <?= $currentRequestType === 'role_change' ? 'selected' : '' ?>>Request higher role</option>
                    </select>
                </div>

                <div id="desiredRoleGroup" style="<?= $currentRequestType === 'role_change' ? 'display:block;' : 'display:none;' ?>">
                    <label class="label">Desired Role</label>
                    <select class="input" name="desiredRole" id="desiredRole">
                        <option value="">Select higher role</option>
                        <?php foreach ($roleChangeOptions as $roleOption): ?>
                            <option value="<?= htmlspecialchars((string) $roleOption, ENT_QUOTES, 'UTF-8') ?>" <?= $currentDesiredRole === (string) $roleOption ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $roleOption, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="label">Start Date</label>
                    <input class="input" type="date" name="startDate" value="<?= htmlspecialchars((string) ($club['startDate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div>
                    <label class="label">End Date</label>
                    <input class="input" type="date" name="endDate" value="<?= htmlspecialchars((string) ($club['endDate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="muted" style="margin-top:6px;">Optional. Leave blank if ongoing.</div>
                </div>
            </div>

            <div style="margin-top:14px;">
                <label class="label">Reason / Note</label>
                <textarea class="input" name="roleDescription" rows="4"><?= htmlspecialchars((string) ($club['roleDescription'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-actions">
                <div style="width:100%;">
                    <label class="label">Proof Document (Optional)</label>
                    <input class="input" type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png">
                    <?php if (!empty($club['evidence_path'])): ?>
                        <div class="muted" style="margin-top:6px;">
                            Current file:
                            <a class="link" href="<?= htmlspecialchars(BASE_URL . ltrim((string) $club['evidence_path'], '/'), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">View evidence</a>
                        </div>
                    <?php else: ?>
                        <div class="muted" style="margin-top:6px;">No file uploaded yet. Accepted: PDF, JPG, PNG (max 5MB).</div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn">Save Changes</button>
                <a href="index.php?url=club/index" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <?php endif; ?>

        </div>
    </div>

</div>

<script>
(function () {
    var requestType = document.getElementById('requestType');
    var desiredRoleGroup = document.getElementById('desiredRoleGroup');
    var desiredRole = document.getElementById('desiredRole');

    if (!requestType || !desiredRoleGroup || !desiredRole) {
        return;
    }

    function syncRequestType() {
        var isRoleChange = requestType.value === 'role_change';
        desiredRoleGroup.style.display = isRoleChange ? 'block' : 'none';
        desiredRole.required = isRoleChange;
        if (!isRoleChange) {
            desiredRole.value = '';
        }
    }

    requestType.addEventListener('change', syncRequestType);
    syncRequestType();
})();
</script>

<?php require "../app/views/layout/footer.php"; ?>
