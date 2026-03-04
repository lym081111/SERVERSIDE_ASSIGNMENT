<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Admin Events</div>
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
            <div class="admin-eyebrow">Event Oversight</div>
            <h1 class="admin-title">Event Records</h1>
            <p class="admin-subtitle">Search students and manage event records across the system.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn" href="index.php?url=event/create">Add Event for Student</a>
            <a class="btn btn-secondary" href="index.php?url=event/index">Refresh</a>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Search Records</h2>
            <span class="admin-section-chip">Admin view</span>
        </div>
        <div class="admin-section-body">
            <form method="GET" class="filter-bar">
                <input type="hidden" name="url" value="event/index">

                <input
                    type="text"
                    name="search"
                    class="input"
                    placeholder="Search student name, email, or event title..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                <?php $currentSort = $_GET['sort'] ?? 'eventID'; ?>
                <select name="sort" class="input">
                    <option value="eventID" <?= $currentSort === 'eventID' ? 'selected' : '' ?>>Newest</option>
                    <option value="student" <?= $currentSort === 'student' ? 'selected' : '' ?>>Student Name</option>
                    <option value="eventTitle" <?= $currentSort === 'eventTitle' ? 'selected' : '' ?>>Event Title</option>
                    <option value="eventDate" <?= $currentSort === 'eventDate' ? 'selected' : '' ?>>Event Date</option>
                    <option value="location" <?= $currentSort === 'location' ? 'selected' : '' ?>>Location</option>
                </select>

                <button class="btn" type="submit">Search / Filter</button>
                <a class="btn btn-secondary" href="index.php?url=event/index">Reset</a>
            </form>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">All Event Records</h2>
            <span class="admin-section-chip"><?= is_array($events) ? count($events) : 0 ?> total</span>
        </div>
        <div class="admin-section-body">
            <table class="admin-table">
                <tr>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="7" class="muted">No event records found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($events as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['userEmail'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['eventTitle'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['eventDate'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['location'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['description'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a class="link" href="index.php?url=event/edit&id=<?= htmlspecialchars($e['eventID'], ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                            <span class="muted">|</span>
                            <form method="POST" action="index.php?url=event/delete" style="display:inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($e['eventID'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="link danger" onclick="return confirm('Delete this event record?')">
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
