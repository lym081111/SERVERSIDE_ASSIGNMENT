<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Admin Clubs</div>
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
            <div class="admin-eyebrow">Club Oversight</div>
            <h1 class="admin-title">Club Records</h1>
            <p class="admin-subtitle">Search students and manage club records across the system.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn" href="index.php?url=club/create">Add Club for Student</a>
            <a class="btn btn-secondary" href="index.php?url=club/index">Refresh</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Search Records</h2>
            <span class="admin-section-chip">Admin view</span>
        </div>
        <div class="admin-section-body">
            <form method="GET" class="filter-bar">
                <input type="hidden" name="url" value="club/index">

                <input
                    type="text"
                    name="search"
                    class="input"
                    placeholder="Search student name, email, or club name..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                <?php $currentSort = $_GET['sort'] ?? 'clubID'; ?>
                <select name="sort" class="input">
                    <option value="clubID" <?= $currentSort === 'clubID' ? 'selected' : '' ?>>Newest</option>
                    <option value="student" <?= $currentSort === 'student' ? 'selected' : '' ?>>Student Name</option>
                    <option value="clubName" <?= $currentSort === 'clubName' ? 'selected' : '' ?>>Club Name</option>
                    <option value="role" <?= $currentSort === 'role' ? 'selected' : '' ?>>Role</option>
                    <option value="startDate" <?= $currentSort === 'startDate' ? 'selected' : '' ?>>Start Date</option>
                    <option value="endDate" <?= $currentSort === 'endDate' ? 'selected' : '' ?>>End Date</option>
                </select>

                <button class="btn" type="submit">Search / Filter</button>
                <a class="btn btn-secondary" href="index.php?url=club/index">Reset</a>
            </form>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">All Club Records</h2>
            <span class="admin-section-chip"><?= is_array($clubs) ? count($clubs) : 0 ?> total</span>
        </div>
        <div class="admin-section-body">
            <table class="admin-table">
                <tr>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Club</th>
                    <th>Role</th>
                    <th>Role Description</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Actions</th>
                </tr>
                <?php if (empty($clubs)): ?>
                    <tr>
                        <td colspan="8" class="muted">No club records found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($clubs as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['userEmail'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['clubName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['role'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['roleDescription'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['startDate'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['endDate'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a class="link" href="index.php?url=club/edit&id=<?= htmlspecialchars($c['clubID'], ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                            <span class="muted">|</span>
                            <form method="POST" action="index.php?url=club/delete" style="display:inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($c['clubID'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="link danger" onclick="return confirm('Delete this club record?')">
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
