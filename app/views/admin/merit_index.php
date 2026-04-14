<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main module-page">

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

    <?php
        $exportParams = ['url' => 'merit/export'];
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

        $pendingCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;
        $appealedCount = 0;
        if (is_array($merits)) {
            foreach ($merits as $row) {
                $statusCountValue = (string) ($row['status'] ?? 'pending');
                if ($statusCountValue === 'approved') {
                    $approvedCount++;
                } elseif ($statusCountValue === 'rejected') {
                    $rejectedCount++;
                } else {
                    $pendingCount++;
                }
                if ((int) ($row['resubmission_count'] ?? 0) > 0) {
                    $appealedCount++;
                }
            }
        }

        $sourceLabelMap = [
            'student_submission' => 'Student Submission',
            'student_appeal' => 'Student Appeal',
            'admin_creation' => 'Admin Creation',
            'admin_review' => 'Admin Review',
            'admin_edit' => 'Admin Edit',
            'migration_backfill' => 'Backfill',
            'system' => 'System',
        ];
    ?>

    <div class="print-title">Merit Records (Admin)</div>

    <div class="admin-hero">
        <div>
            <div class="admin-eyebrow">Merit Oversight</div>
            <h1 class="admin-title">Merit Records</h1>
            <p class="admin-subtitle">Search students and manage merit records across the system.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn" href="index.php?url=merit/create">Add Merit for Student</a>
            <a class="btn btn-secondary no-print" href="<?= htmlspecialchars($exportUrl, ENT_QUOTES, 'UTF-8') ?>">Export CSV</a>
            <button class="btn btn-secondary" type="button" onclick="window.print()">Print</button>
            <a class="btn btn-secondary" href="index.php?url=merit/index">Refresh</a>
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

    <div class="kpi-grid" style="margin-bottom:16px;">
        <div class="kpi-card">
            <div class="kpi-label">Pending Reviews</div>
            <div class="kpi-value"><?= (int) $pendingCount ?></div>
            <div class="kpi-sub">Awaiting admin decision</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Approved</div>
            <div class="kpi-value"><?= (int) $approvedCount ?></div>
            <div class="kpi-sub">Completed records</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Rejected</div>
            <div class="kpi-value"><?= (int) $rejectedCount ?></div>
            <div class="kpi-sub">Need student correction</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-label">Appealed</div>
            <div class="kpi-value"><?= (int) $appealedCount ?></div>
            <div class="kpi-sub">Had at least 1 resubmission</div>
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
                    placeholder="Search student, activity, event, or club..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                <?php $currentSort = $_GET['sort'] ?? 'meritID'; ?>
                <select name="sort" class="input">
                    <option value="meritID" <?= $currentSort === 'meritID' ? 'selected' : '' ?>>Newest</option>
                    <option value="student" <?= $currentSort === 'student' ? 'selected' : '' ?>>Student Name</option>
                    <option value="student_id" <?= $currentSort === 'student_id' ? 'selected' : '' ?>>Student ID</option>
                    <option value="activityName" <?= $currentSort === 'activityName' ? 'selected' : '' ?>>Activity</option>
                    <option value="eventTitle" <?= $currentSort === 'eventTitle' ? 'selected' : '' ?>>Event</option>
                    <option value="clubName" <?= $currentSort === 'clubName' ? 'selected' : '' ?>>Club</option>
                    <option value="hours" <?= $currentSort === 'hours' ? 'selected' : '' ?>>Hours</option>
                    <option value="dateFrom" <?= $currentSort === 'dateFrom' ? 'selected' : '' ?>>Date From</option>
                    <option value="dateTo" <?= $currentSort === 'dateTo' ? 'selected' : '' ?>>Date To</option>
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
            <table class="admin-table co-records-table">
                <tr>
                    <th>Student</th>
                    <th>Student ID</th>
                    <th>Email</th>
                    <th>Club</th>
                    <th>Event</th>
                    <th>Activity</th>
                    <th>Hours</th>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Appeal</th>
                    <th>Review</th>
                    <th>Actions</th>
                </tr>
                <?php if (empty($merits)): ?>
                    <tr>
                        <td colspan="14" class="muted">No merit records found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($merits as $m): ?>
                    <?php
                        $status = (string) ($m['status'] ?? 'approved');
                        $reviewNote = trim((string) ($m['review_note'] ?? ''));
                        $evidencePath = trim((string) ($m['evidence_path'] ?? ''));
                        $appealNote = trim((string) ($m['appeal_note'] ?? ''));
                        $appealedAt = trim((string) ($m['appealed_at'] ?? ''));
                        $resubmissionCount = (int) ($m['resubmission_count'] ?? 0);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($m['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['studentId'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['userEmail'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($m['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($m['eventTitle'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['activityName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['hours'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['dateFrom'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['dateTo'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
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
                            <?php if ($resubmissionCount > 0): ?>
                                <div class="muted" style="font-size:0.85rem;">Resubmissions: <?= (int) $resubmissionCount ?></div>
                            <?php else: ?>
                                <span class="muted">No appeals yet</span>
                            <?php endif; ?>
                            <?php if ($appealedAt !== ''): ?>
                                <div class="muted" style="font-size:0.85rem;">Last: <?= htmlspecialchars($appealedAt, ENT_QUOTES, 'UTF-8') ?></div>
                            <?php endif; ?>
                            <?php if ($appealNote !== ''): ?>
                                <div class="muted" style="margin-top:4px;font-size:0.85rem;">
                                    <?= htmlspecialchars($appealNote, ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="index.php?url=merit/review" class="review-form">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($m['meritID'], ENT_QUOTES, 'UTF-8') ?>">
                                <select name="status" class="input">
                                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                                <input
                                    type="text"
                                    name="review_note"
                                    class="input"
                                    placeholder="Decision note (optional)"
                                    value="<?= htmlspecialchars($reviewNote, ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-secondary">Save</button>
                            </form>
                        </td>
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

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Recent Status Changes</h2>
            <span class="admin-section-chip">Audit trail</span>
        </div>
        <div class="admin-section-body">
            <table class="admin-table co-records-table">
                <tr>
                    <th>When</th>
                    <th>Student</th>
                    <th>Activity</th>
                    <th>Change</th>
                    <th>By</th>
                    <th>Source</th>
                    <th>Note</th>
                </tr>
                <?php if (empty($recentStatusLogs)): ?>
                    <tr>
                        <td colspan="7" class="muted">No status changes logged yet.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($recentStatusLogs as $log): ?>
                    <?php
                        $fromStatus = (string) ($log['from_status'] ?? '');
                        $toStatus = (string) ($log['to_status'] ?? '');
                        $changeNote = trim((string) ($log['change_note'] ?? ''));
                        $sourceKey = (string) ($log['change_source'] ?? 'system');
                        $sourceLabel = $sourceLabelMap[$sourceKey] ?? ucwords(str_replace('_', ' ', $sourceKey));
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($log['created_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?= htmlspecialchars($log['ownerName'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
                            <span class="muted"><?= htmlspecialchars($log['ownerStudentId'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td><?= htmlspecialchars($log['activityName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?= htmlspecialchars($fromStatus !== '' ? ucfirst($fromStatus) : 'N/A', ENT_QUOTES, 'UTF-8') ?>
                            <span class="muted">&rarr;</span>
                            <?= htmlspecialchars(ucfirst($toStatus), ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($log['changedByName'] ?? 'System', ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($log['changedByStudentId'])): ?>
                                <br><span class="muted"><?= htmlspecialchars($log['changedByStudentId'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($sourceLabel, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $changeNote !== '' ? htmlspecialchars($changeNote, ENT_QUOTES, 'UTF-8') : '<span class="muted">-</span>' ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>

