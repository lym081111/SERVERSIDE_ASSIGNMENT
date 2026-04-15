<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main module-page">

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

    <?php
        $exportParams = ['url' => 'achievement/export'];
        if (!empty($_GET['search'])) {
            $exportParams['search'] = (string) $_GET['search'];
        }
        if (!empty($_GET['sort'])) {
            $exportParams['sort'] = (string) $_GET['sort'];
        }
        if (!empty($_GET['status'])) {
            $exportParams['status'] = (string) $_GET['status'];
        }
        $exportUrl = 'index.php?' . http_build_query($exportParams);
    ?>

    <div class="print-title">Achievement Records (Admin)</div>

    <div class="admin-hero">
        <div>
            <div class="admin-eyebrow">Achievement Oversight</div>
            <h1 class="admin-title">Achievement Records</h1>
            <p class="admin-subtitle">Search students and manage achievements across the system.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn" href="index.php?url=achievement/create">Add Achievement for Student</a>
            <a class="btn btn-secondary no-print" href="<?= htmlspecialchars($exportUrl, ENT_QUOTES, 'UTF-8') ?>">Export CSV</a>
            <button class="btn btn-secondary" type="button" onclick="window.print()">Print</button>
            <a class="btn btn-secondary" href="index.php?url=achievement/index">Refresh</a>
        </div>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="success">
            <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="error">
            <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

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
                    placeholder="Search student, title, event, club, category, level, or description..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                <?php $currentSort = $_GET['sort'] ?? 'achievementID'; ?>
                <select name="sort" class="input">
                    <option value="achievementID" <?= $currentSort === 'achievementID' ? 'selected' : '' ?>>Newest</option>
                    <option value="student" <?= $currentSort === 'student' ? 'selected' : '' ?>>Student Name</option>
                    <option value="student_id" <?= $currentSort === 'student_id' ? 'selected' : '' ?>>Student ID</option>
                    <option value="clubName" <?= $currentSort === 'clubName' ? 'selected' : '' ?>>Club</option>
                    <option value="eventTitle" <?= $currentSort === 'eventTitle' ? 'selected' : '' ?>>Event</option>
                    <option value="title" <?= $currentSort === 'title' ? 'selected' : '' ?>>Title</option>
                    <option value="category" <?= $currentSort === 'category' ? 'selected' : '' ?>>Category</option>
                    <option value="achievementLevel" <?= $currentSort === 'achievementLevel' ? 'selected' : '' ?>>Level</option>
                    <option value="dateReceived" <?= $currentSort === 'dateReceived' ? 'selected' : '' ?>>Date Received</option>
                    <option value="status" <?= $currentSort === 'status' ? 'selected' : '' ?>>Status</option>
                </select>

                <?php $currentStatus = $_GET['status'] ?? ''; ?>
                <select name="status" class="input">
                    <option value="" <?= $currentStatus === '' ? 'selected' : '' ?>>All Status</option>
                    <option value="pending" <?= $currentStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $currentStatus === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $currentStatus === 'rejected' ? 'selected' : '' ?>>Rejected</option>
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
            <table class="admin-table co-records-table">
                <tr>
                    <th>Student</th>
                    <th>Student ID</th>
                    <th>Email</th>
                    <th>Club</th>
                    <th>Event</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Level</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Review</th>
                    <th>Actions</th>
                </tr>
                <?php if (empty($achievements)): ?>
                    <tr>
                        <td colspan="14" class="muted">No achievement records found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($achievements as $a): ?>
                    <?php
                        $status = (string) ($a['status'] ?? 'approved');
                        $reviewNote = trim((string) ($a['review_note'] ?? ''));
                        $evidencePath = trim((string) ($a['evidence_path'] ?? ''));
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($a['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['studentId'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['userEmail'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($a['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($a['eventTitle'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['title'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['category'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(($a['achievementLevel'] ?? 'Faculty') ?: 'Faculty', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['dateReceived'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['description'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if ($evidencePath !== ''): ?>
                                <a class="link" href="<?= htmlspecialchars(BASE_URL . ltrim($evidencePath, '/'), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Open file</a>
                            <?php else: ?>
                                <span class="muted">No file</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td>
                            <form method="POST" action="index.php?url=achievement/review" class="review-form">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($a['achievementID'], ENT_QUOTES, 'UTF-8') ?>">
                                <select name="status" class="input">
                                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                                <input
                                    type="text"
                                    name="review_note"
                                    class="input"
                                    placeholder="Review note (optional)"
                                    value="<?= htmlspecialchars($reviewNote, ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-secondary">Save</button>
                            </form>
                        </td>
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

