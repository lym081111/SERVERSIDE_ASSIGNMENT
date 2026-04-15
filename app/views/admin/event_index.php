<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main module-page">

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

    <?php
        $exportParams = ['url' => 'event/export'];
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
        $totalRecords = max(0, (int) ($pagination['totalRecords'] ?? (is_array($events) ? count($events) : 0)));
        $perPage = max(1, (int) ($pagination['perPage'] ?? 10));
        $startRecord = $totalRecords > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
        $endRecord = $totalRecords > 0 ? min($totalRecords, $currentPage * $perPage) : 0;
        $paginationParams = ['url' => 'event/index'];
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

    <div class="print-title">Event Records (Admin)</div>

    <div class="admin-hero">
        <div>
            <div class="admin-eyebrow">Event Oversight</div>
            <h1 class="admin-title">Event Records</h1>
            <p class="admin-subtitle">Search students and manage event records across the system.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn" href="index.php?url=event/create">Add Event for Student</a>
            <a class="btn btn-secondary no-print" href="<?= htmlspecialchars($exportUrl, ENT_QUOTES, 'UTF-8') ?>">Export CSV</a>
            <button class="btn btn-secondary" type="button" onclick="window.print()">Print</button>
            <a class="btn btn-secondary" href="index.php?url=event/index">Refresh</a>
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
                <input type="hidden" name="url" value="event/index">

                <input
                    type="text"
                    name="search"
                    class="input"
                    placeholder="Search student, ID, email, club, title, type, hours, location, reflection, or date..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                <?php $currentSort = $_GET['sort'] ?? 'eventID'; ?>
                <select name="sort" class="input">
                    <option value="eventID" <?= $currentSort === 'eventID' ? 'selected' : '' ?>>Newest</option>
                    <option value="student" <?= $currentSort === 'student' ? 'selected' : '' ?>>Student Name</option>
                    <option value="student_id" <?= $currentSort === 'student_id' ? 'selected' : '' ?>>Student ID</option>
                    <option value="clubName" <?= $currentSort === 'clubName' ? 'selected' : '' ?>>Club</option>
                    <option value="eventTitle" <?= $currentSort === 'eventTitle' ? 'selected' : '' ?>>Event Title</option>
                    <option value="eventType" <?= $currentSort === 'eventType' ? 'selected' : '' ?>>Event Type</option>
                    <option value="eventDate" <?= $currentSort === 'eventDate' ? 'selected' : '' ?>>Event Date</option>
                    <option value="eventHours" <?= $currentSort === 'eventHours' ? 'selected' : '' ?>>Event Hours</option>
                    <option value="location" <?= $currentSort === 'location' ? 'selected' : '' ?>>Location</option>
                    <option value="participantCapacity" <?= $currentSort === 'participantCapacity' ? 'selected' : '' ?>>Capacity</option>
                    <option value="registeredCount" <?= $currentSort === 'registeredCount' ? 'selected' : '' ?>>Registered Count</option>
                    <option value="waitlistCount" <?= $currentSort === 'waitlistCount' ? 'selected' : '' ?>>Waitlist Count</option>
                    <option value="registrationStatus" <?= $currentSort === 'registrationStatus' ? 'selected' : '' ?>>Registration Status</option>
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
                <a class="btn btn-secondary" href="index.php?url=event/index">Reset</a>
            </form>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">All Event Records</h2>
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
                    <th>Event</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Hours</th>
                    <th>Seats</th>
                    <th>Registration</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Reflection</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Review</th>
                    <th>Actions</th>
                </tr>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="17" class="muted">No event records found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($events as $e): ?>
                    <?php
                        $status = (string) ($e['status'] ?? 'approved');
                        $reviewNote = trim((string) ($e['review_note'] ?? ''));
                        $evidencePath = trim((string) ($e['evidence_path'] ?? ''));
                        $participantCapacity = isset($e['participantCapacity']) ? (int) $e['participantCapacity'] : 0;
                        $registeredCount = isset($e['registeredCount']) ? (int) $e['registeredCount'] : 0;
                        $waitlistCount = isset($e['waitlistCount']) ? (int) $e['waitlistCount'] : 0;
                        $registrationStatus = trim((string) ($e['registrationStatus'] ?? ''));
                        if ($registrationStatus === '') {
                            if ($participantCapacity <= 0 || $registeredCount < $participantCapacity) {
                                $registrationStatus = 'open';
                            } elseif (!empty($e['waitlistEnabled'])) {
                                $registrationStatus = 'waitlist';
                            } else {
                                $registrationStatus = 'full';
                            }
                        }
                        $seatSummary = $participantCapacity > 0
                            ? ($registeredCount . '/' . $participantCapacity)
                            : 'Unlimited';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($e['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['studentId'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['userEmail'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($e['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['eventTitle'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(($e['eventType'] ?? 'General') ?: 'General', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['eventDate'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($e['eventHours'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?= htmlspecialchars($seatSummary, ENT_QUOTES, 'UTF-8') ?>
                            <?php if ($participantCapacity > 0): ?>
                                <div class="muted" style="margin-top:4px;font-size:0.85rem;">Waitlist: <?= (int) $waitlistCount ?></div>
                            <?php endif; ?>
                        </td>
                        <td><span class="chip"><?= htmlspecialchars(ucfirst($registrationStatus), ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><?= htmlspecialchars($e['location'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['description'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['reflection'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
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
                            <form method="POST" action="index.php?url=event/review" class="review-form">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($e['eventID'], ENT_QUOTES, 'UTF-8') ?>">
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
                            <a class="link" href="index.php?url=event/edit&id=<?= htmlspecialchars($e['eventID'], ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                            <span class="muted">|</span>
                            <form method="POST" action="index.php?url=event/delete" style="display:inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($e['eventID'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_search" value="<?= htmlspecialchars((string) ($_GET['search'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_sort" value="<?= htmlspecialchars((string) ($_GET['sort'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_status" value="<?= htmlspecialchars((string) ($_GET['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="_filter_page" value="<?= (int) $currentPage ?>">
                                <button type="submit" class="link danger" onclick="return confirm('Delete this event record?')">
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

