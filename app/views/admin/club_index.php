<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main module-page">

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

    <?php
        $exportParams = ['url' => 'club/export'];
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

        $currentPage = max(1, (int) ($pagination['currentPage'] ?? 1));
        $totalPages = max(1, (int) ($pagination['totalPages'] ?? 1));
        $totalRecords = max(0, (int) ($pagination['totalRecords'] ?? (is_array($clubs) ? count($clubs) : 0)));
        $perPage = max(1, (int) ($pagination['perPage'] ?? 10));
        $startRecord = $totalRecords > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
        $endRecord = $totalRecords > 0 ? min($totalRecords, $currentPage * $perPage) : 0;
        $paginationParams = ['url' => 'club/index'];
        if (!empty($_GET['search'])) {
            $paginationParams['search'] = (string) $_GET['search'];
        }
        if (!empty($_GET['sort'])) {
            $paginationParams['sort'] = (string) $_GET['sort'];
        }
        if (!empty($_GET['status'])) {
            $paginationParams['status'] = (string) $_GET['status'];
        }
        $pageWindowStart = max(1, $currentPage - 2);
        $pageWindowEnd = min($totalPages, $currentPage + 2);
    ?>

    <div class="print-title">Club Records (Admin)</div>

    <div class="admin-hero">
        <div>
            <div class="admin-eyebrow">Club Oversight</div>
            <h1 class="admin-title">Club Records</h1>
            <p class="admin-subtitle">Search students and manage club records across the system.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn" href="index.php?url=club/create">Manage Club Catalog</a>
            <a class="btn" href="index.php?url=club/addStudent">Add Club for Student</a>
            <a class="btn btn-secondary no-print" href="<?= htmlspecialchars($exportUrl, ENT_QUOTES, 'UTF-8') ?>">Export CSV</a>
            <button class="btn btn-secondary" type="button" onclick="window.print()">Print</button>
            <a class="btn btn-secondary" href="index.php?url=club/index">Refresh</a>
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
                <input type="hidden" name="url" value="club/index">

                    <input
                        type="text"
                        name="search"
                        class="input"
                        placeholder="Search student, ID, email, club, role, request type, or description..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                <?php $currentSort = $_GET['sort'] ?? 'clubID'; ?>
                <select name="sort" class="input">
                    <option value="clubID" <?= $currentSort === 'clubID' ? 'selected' : '' ?>>Newest</option>
                    <option value="student" <?= $currentSort === 'student' ? 'selected' : '' ?>>Student Name</option>
                    <option value="student_id" <?= $currentSort === 'student_id' ? 'selected' : '' ?>>Student ID</option>
                    <option value="clubName" <?= $currentSort === 'clubName' ? 'selected' : '' ?>>Club Name</option>
                    <option value="role" <?= $currentSort === 'role' ? 'selected' : '' ?>>Role</option>
                    <option value="request_type" <?= $currentSort === 'request_type' ? 'selected' : '' ?>>Request Type</option>
                    <option value="startDate" <?= $currentSort === 'startDate' ? 'selected' : '' ?>>Start Date</option>
                    <option value="endDate" <?= $currentSort === 'endDate' ? 'selected' : '' ?>>End Date</option>
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
                <a class="btn btn-secondary" href="index.php?url=club/index">Reset</a>
            </form>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">All Club Records</h2>
            <span class="admin-section-chip"><?= (int) $totalRecords ?> total</span>
        </div>
        <div class="admin-section-body">
            <div class="records-table-wrap">
            <table class="admin-table co-records-table">
                <tr>
                    <th>Student</th>
                    <th>Student ID</th>
                    <th>Email</th>
                    <th>Club</th>
                    <th>Request Type</th>
                    <th>Role</th>
                    <th>Period</th>
                    <th>Status</th>
                    <th>Review</th>
                    <th>Actions</th>
                </tr>
                <?php if (empty($clubs)): ?>
                    <tr>
                        <td colspan="10" class="muted">No club records found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($clubs as $c): ?>
                    <?php
                        $status = (string) ($c['status'] ?? 'approved');
                        $reviewNote = trim((string) ($c['review_note'] ?? ''));
                        $startDate = trim((string) ($c['startDate'] ?? ''));
                        $endDate = trim((string) ($c['endDate'] ?? ''));
                        $startDate = ($startDate === '' || $startDate === '0000-00-00') ? '-' : $startDate;
                        $endDate = ($endDate === '' || $endDate === '0000-00-00') ? '-' : $endDate;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($c['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['studentId'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['userEmail'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['clubName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(((string) ($c['request_type'] ?? 'join')) === 'role_change' ? 'Role Change' : 'Join Club', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['role'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($startDate . ' to ' . $endDate, ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="status-badge <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td>
                            <form method="POST" action="index.php?url=club/review" class="review-form">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($c['clubID'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_search" value="<?= htmlspecialchars((string) ($_GET['search'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_sort" value="<?= htmlspecialchars((string) ($_GET['sort'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_status" value="<?= htmlspecialchars((string) ($_GET['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_page" value="<?= (int) $currentPage ?>">
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
                            <a class="btn btn-secondary" href="index.php?url=club/edit&id=<?= htmlspecialchars($c['clubID'], ENT_QUOTES, 'UTF-8') ?>">View Details</a>
                            <span class="muted">|</span>
                            <form method="POST" action="index.php?url=club/delete" style="display:inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($c['clubID'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_search" value="<?= htmlspecialchars((string) ($_GET['search'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_sort" value="<?= htmlspecialchars((string) ($_GET['sort'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_status" value="<?= htmlspecialchars((string) ($_GET['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_page" value="<?= (int) $currentPage ?>">
                                <button type="submit" class="link danger" onclick="return confirm('Delete this club record?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            </div>
            <div class="pagination-bar">
                <div class="pagination-meta">
                    Showing <?= (int) $startRecord ?>-<?= (int) $endRecord ?> of <?= (int) $totalRecords ?>
                </div>
                <?php if ($totalPages > 1): ?>
                    <div class="pagination-links">
                        <?php
                            $prevParams = $paginationParams;
                            $prevParams['page'] = max(1, $currentPage - 1);
                            $nextParams = $paginationParams;
                            $nextParams['page'] = min($totalPages, $currentPage + 1);
                        ?>
                        <a
                            class="btn btn-secondary <?= $currentPage <= 1 ? 'disabled' : '' ?>"
                            <?= $currentPage <= 1 ? 'aria-disabled="true"' : '' ?>
                            href="index.php?<?= htmlspecialchars(http_build_query($prevParams), ENT_QUOTES, 'UTF-8') ?>">
                            Previous
                        </a>
                        <?php for ($pageNo = $pageWindowStart; $pageNo <= $pageWindowEnd; $pageNo++): ?>
                            <?php $pageParams = $paginationParams; $pageParams['page'] = $pageNo; ?>
                            <a
                                class="btn btn-secondary <?= $pageNo === $currentPage ? 'active' : '' ?>"
                                href="index.php?<?= htmlspecialchars(http_build_query($pageParams), ENT_QUOTES, 'UTF-8') ?>">
                                <?= (int) $pageNo ?>
                            </a>
                        <?php endfor; ?>
                        <a
                            class="btn btn-secondary <?= $currentPage >= $totalPages ? 'disabled' : '' ?>"
                            <?= $currentPage >= $totalPages ? 'aria-disabled="true"' : '' ?>
                            href="index.php?<?= htmlspecialchars(http_build_query($nextParams), ENT_QUOTES, 'UTF-8') ?>">
                            Next
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>

