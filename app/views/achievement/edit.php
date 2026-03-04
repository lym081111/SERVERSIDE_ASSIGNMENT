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
            <h2 style="margin:0;">Edit Achievement</h2>
            <div class="muted" style="margin-top:6px;">Update the achievement details.</div>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="index.php?url=achievement/index">Back</a>
        </div>
    </div>

    <div class="card">
        <form method="POST" class="form">
            <?php csrf_field(); ?>

            <div class="form-grid">
                <div>
                    <label class="label">Title</label>
                    <input class="input" type="text" name="title" value="<?= htmlspecialchars($achievement['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div>
                    <label class="label">Category</label>
                    <input class="input" type="text" name="category" value="<?= htmlspecialchars($achievement['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div>
                    <label class="label">Date Received</label>
                    <input class="input" type="date" name="dateReceived" value="<?= htmlspecialchars($achievement['dateReceived'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <div style="margin-top:14px;">
                <label class="label">Description</label>
                <textarea class="input" name="description" rows="4"><?= htmlspecialchars($achievement['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
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

<?php require "../app/views/layout/footer.php"; ?>
