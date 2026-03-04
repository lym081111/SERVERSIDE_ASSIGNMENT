<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

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
            <h2 style="margin:0;">Add New Club Record</h2>
            <div class="muted" style="margin-top:6px;">Record your membership and role details.</div>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="index.php?url=club/index">Back</a>
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
                    <label class="label">Club Name</label>
                    <input class="input" type="text" name="clubName" placeholder="e.g. Debate Society" required>
                </div>

                <div>
                    <label class="label">Role</label>
                    <input class="input" type="text" name="role" placeholder="e.g. Member, Secretary">
                </div>

                <div>
                    <label class="label">Start Date</label>
                    <input class="input" type="date" name="startDate">
                </div>

                <div>
                    <label class="label">End Date</label>
                    <input class="input" type="date" name="endDate">
                </div>
            </div>

            <div style="margin-top:14px;">
                <label class="label">Role Description</label>
                <textarea class="input" name="roleDescription" rows="4" placeholder="Describe your role or responsibilities."></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Save Record</button>
                <a href="index.php?url=club/index" class="btn btn-secondary">Cancel</a>
            </div>

        </form>

    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
