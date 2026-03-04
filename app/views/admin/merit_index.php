<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Admin Merits</div>
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
            <div class="admin-eyebrow">Merit Oversight</div>
            <h1 class="admin-title">Merit Records</h1>
            <p class="admin-subtitle">Search students and manage merit records across the system.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn" href="index.php?url=merit/create">Add Merit for Student</a>
            <a class="btn btn-secondary" href="index.php?url=merit/index">Refresh</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Search Records</h2>
            <span class="admin-section-chip">Admin view</span>
        </div>
        <div class="admin-section-body">
            <form method="GET" class="filter-bar">
                <input type="hidden" name="url" value="merit/index">

                <input
                    type="text"
                    name="search"
                    class="input"
                    placeholder="Search student name, email, or activity..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                <?php $currentSort = $_GET['sort'] ?? 'meritID'; ?>
                <select name="sort" class="input">
                    <option value="meritID" <?= $currentSort === 'meritID' ? 'selected' : '' ?>>Newest</option>
                    <option value="student" <?= $currentSort === 'student' ? 'selected' : '' ?>>Student Name</option>
                    <option value="activityName" <?= $currentSort === 'activityName' ? 'selected' : '' ?>>Activity</option>
                    <option value="hours" <?= $currentSort === 'hours' ? 'selected' : '' ?>>Hours</option>
                    <option value="dateFrom" <?= $currentSort === 'dateFrom' ? 'selected' : '' ?>>Date From</option>
                    <option value="dateTo" <?= $currentSort === 'dateTo' ? 'selected' : '' ?>>Date To</option>
                </select>

                <button class="btn" type="submit">Search / Filter</button>
                <a class="btn btn-secondary" href="index.php?url=merit/index">Reset</a>
            </form>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">All Merit Records</h2>
            <span class="admin-section-chip"><?= is_array($merits) ? count($merits) : 0 ?> total</span>
        </div>
        <div class="admin-section-body">
            <table class="admin-table">
                <tr>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Activity</th>
                    <th>Hours</th>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Actions</th>
                </tr>
                <?php if (empty($merits)): ?>
                    <tr>
                        <td colspan="7" class="muted">No merit records found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($merits as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['userEmail'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['activityName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['hours'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['dateFrom'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['dateTo'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a class="link" href="index.php?url=merit/edit&id=<?= htmlspecialchars($m['meritID'], ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                            <span class="muted">|</span>
                            <form method="POST" action="index.php?url=merit/delete" style="display:inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($m['meritID'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="link danger" onclick="return confirm('Delete this merit record?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
