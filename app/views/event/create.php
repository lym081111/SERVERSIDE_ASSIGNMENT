<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Event Tracker Module</div>
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
            <h2 style="margin:0;">Add New Event Record</h2>
            <div class="muted" style="margin-top:6px;">Fill in the details below to record an event.</div>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="index.php?url=event/index">Back</a>
        </div>
    </div>

    <?php if(isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="card">

        <form method="POST" class="form">
            <?php csrf_field(); ?>

            <div class="form-grid">
                <div>
                    <label class="label">Event Title</label>
                    <input class="input" type="text" name="eventTitle" placeholder="e.g. Campus Leadership Talk" required>
                </div>

                <div>
                    <label class="label">Event Date</label>
                    <input class="input" type="date" name="eventDate" required>
                </div>

                <div>
                    <label class="label">Location</label>
                    <input class="input" type="text" name="location" placeholder="e.g. Main Hall">
                </div>
            </div>

            <div style="margin-top:14px;">
                <label class="label">Description</label>
                <textarea class="input" name="description" rows="4" placeholder="Add notes or a short summary."></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Save Record</button>
                <a href="index.php?url=event/index" class="btn btn-secondary">Cancel</a>
            </div>

        </form>

    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
