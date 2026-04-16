<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $roleChangeOptions = isset($roleChangeOptions) && is_array($roleChangeOptions) && !empty($roleChangeOptions)
        ? $roleChangeOptions
        : ['President', 'Vice President', 'Secretary', 'Treasurer', 'Committee Member', 'Club Leader'];
    $selectedRequestType = ((string) ($_POST['requestType'] ?? 'join')) === 'role_change' ? 'role_change' : 'join';
    $selectedDesiredRole = trim((string) ($_POST['desiredRole'] ?? ''));
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
            <h2 style="margin:0;">Club Join / Role Request</h2>
            <div class="muted" style="margin-top:6px;">Pick an admin-created club and submit a request for approval.</div>
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
            <div class="muted">No active clubs are available yet. Please wait for admin to create clubs.</div>
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
                            <option value="<?= htmlspecialchars((string) ($clubDef['clubCatalogID'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars((string) ($clubDef['clubName'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="label">Request Type</label>
                    <select class="input" name="requestType" id="requestType" required>
                        <option value="join" <?= $selectedRequestType === 'join' ? 'selected' : '' ?>>Join club (Member)</option>
                        <option value="role_change" <?= $selectedRequestType === 'role_change' ? 'selected' : '' ?>>Request higher role</option>
                    </select>
                </div>

                <div id="desiredRoleGroup" style="<?= $selectedRequestType === 'role_change' ? 'display:block;' : 'display:none;' ?>">
                    <label class="label">Desired Role</label>
                    <select class="input" name="desiredRole" id="desiredRole">
                        <option value="">Select higher role</option>
                        <?php foreach ($roleChangeOptions as $roleOption): ?>
                            <option value="<?= htmlspecialchars((string) $roleOption, ENT_QUOTES, 'UTF-8') ?>" <?= $selectedDesiredRole === (string) $roleOption ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $roleOption, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="label">Start Date</label>
                    <input class="input" type="date" name="startDate" required>
                </div>

                <div>
                    <label class="label">End Date</label>
                    <input class="input" type="date" name="endDate">
                    <div class="muted" style="margin-top:6px;">Optional. Leave blank if ongoing.</div>
                </div>
            </div>

            <div style="margin-top:14px;">
                <label class="label">Reason / Note</label>
                <textarea class="input" name="roleDescription" rows="4" placeholder="Tell admin why you are joining or requesting this role."></textarea>
            </div>

            <div class="form-actions">
                <div style="width:100%;">
                    <label class="label">Proof Document (Optional)</label>
                    <input class="input" type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="muted" style="margin-top:6px;">Accepted: PDF, JPG, PNG (max 5MB).</div>
                </div>
                <button type="submit" class="btn">Submit Request</button>
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
