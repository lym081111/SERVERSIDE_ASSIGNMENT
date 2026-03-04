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
            <h2 style="margin:0;">Edit Merit Record</h2>
            <div class="muted" style="margin-top:6px;">Update the record details and save your changes.</div>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="index.php?url=merit/index">Back</a>
        </div>
    </div>

    <div class="card">
        <form method="POST" class="form">
            <?php csrf_field(); ?>

            <div class="form-grid">
                <div>
                    <label class="label">Activity Name</label>
                    <input class="input" type="text" name="activityName" value="<?= htmlspecialchars($merit['activityName'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div>
                    <label class="label">Contribution Hours</label>
                    <input class="input" type="number" name="hours" step="0.01" min="0.01" value="<?= htmlspecialchars($merit['hours'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div>
                    <label class="label">Date From</label>
                    <input class="input" type="date" name="dateFrom" value="<?= htmlspecialchars($merit['dateFrom'], ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div>
                    <label class="label">Date To</label>
                    <input class="input" type="date" name="dateTo" value="<?= htmlspecialchars($merit['dateTo'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Save Changes</button>
                <a href="index.php?url=merit/index" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
