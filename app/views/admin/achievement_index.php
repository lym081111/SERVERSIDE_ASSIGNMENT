<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Admin Achievements</div>
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
            <div class="admin-eyebrow">Achievement Oversight</div>
            <h1 class="admin-title">Achievement Records</h1>
            <p class="admin-subtitle">Search students and manage achievements across the system.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn" href="index.php?url=achievement/create">Add Achievement for Student</a>
            <a class="btn btn-secondary" href="index.php?url=achievement/index">Refresh</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Search Records</h2>
            <span class="admin-section-chip">Admin view</span>
        </div>
        <div class="admin-section-body">
            <form method="GET" class="filter-bar">
                <input type="hidden" name="url" value="achievement/index">

                <input
                    type="text"
                    name="search"
                    class="input"
                    placeholder="Search student name, email, or title..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                <?php $currentSort = $_GET['sort'] ?? 'achievementID'; ?>
                <select name="sort" class="input">
                    <option value="achievementID" <?= $currentSort === 'achievementID' ? 'selected' : '' ?>>Newest</option>
                    <option value="student" <?= $currentSort === 'student' ? 'selected' : '' ?>>Student Name</option>
                    <option value="title" <?= $currentSort === 'title' ? 'selected' : '' ?>>Title</option>
                    <option value="category" <?= $currentSort === 'category' ? 'selected' : '' ?>>Category</option>
                    <option value="dateReceived" <?= $currentSort === 'dateReceived' ? 'selected' : '' ?>>Date Received</option>
                </select>

                <button class="btn" type="submit">Search / Filter</button>
                <a class="btn btn-secondary" href="index.php?url=achievement/index">Reset</a>
            </form>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">All Achievement Records</h2>
            <span class="admin-section-chip"><?= is_array($achievements) ? count($achievements) : 0 ?> total</span>
        </div>
        <div class="admin-section-body">
            <table class="admin-table">
                <tr>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                <?php if (empty($achievements)): ?>
                    <tr>
                        <td colspan="7" class="muted">No achievement records found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($achievements as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['userEmail'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['title'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['category'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['dateReceived'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['description'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a class="link" href="index.php?url=achievement/edit&id=<?= htmlspecialchars($a['achievementID'], ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                            <span class="muted">|</span>
                            <form method="POST" action="index.php?url=achievement/delete" style="display:inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($a['achievementID'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="link danger" onclick="return confirm('Delete this achievement record?')">
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
