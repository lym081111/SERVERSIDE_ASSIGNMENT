<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main module-page">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Admin Control Center</div>
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
            <div class="admin-eyebrow">System Overview</div>
            <h1 class="admin-title">Admin Dashboard</h1>
            <p class="admin-subtitle">Focused view for approvals, student progress, and quick actions.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-secondary" href="index.php?url=admin/index">Refresh</a>
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

    <div class="admin-kpi-grid">
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Students</div>
            <div class="admin-kpi-value"><?= (int) $userCount ?></div>
            <div class="admin-kpi-sub">Registered students</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Active Students</div>
            <div class="admin-kpi-value"><?= (int) $activeStudentCount ?></div>
            <div class="admin-kpi-sub">With at least one record</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">All Records</div>
            <div class="admin-kpi-value"><?= (int) $totalRecords ?></div>
            <div class="admin-kpi-sub">Across all modules</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Pending Reviews</div>
            <div class="admin-kpi-value"><?= (int) $totalPendingReviews ?></div>
            <div class="admin-kpi-sub">Needs admin action</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Approval Rate</div>
            <div class="admin-kpi-value"><?= htmlspecialchars((string) $approvalRate, ENT_QUOTES, 'UTF-8') ?>%</div>
            <div class="admin-kpi-sub">Approved over reviewed</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Rejected</div>
            <div class="admin-kpi-value"><?= (int) $totalRejected ?></div>
            <div class="admin-kpi-sub">Need student resubmission</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Merit Hours</div>
            <div class="admin-kpi-value"><?= (int) round((float) $totalMeritHours) ?></div>
            <div class="admin-kpi-sub">System-wide total</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">New Students</div>
            <div class="admin-kpi-value"><?= (int) $newUsers30d ?></div>
            <div class="admin-kpi-sub">Last 30 days</div>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Review Modules</h2>
            <span class="admin-section-chip">Quick actions</span>
        </div>
        <div class="admin-section-body">
            <div class="module-grid">
                <?php foreach ($moduleSummaries as $module): ?>
                    <div class="module-card">
                        <div class="module-card-header">
                            <h3 class="module-title"><?= htmlspecialchars($module['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <span class="module-status <?= ($module['pending'] ?? 0) > 0 ? 'pending' : 'active' ?>">
                                <?= (int) ($module['pending'] ?? 0) ?> pending
                            </span>
                        </div>
                        <div class="module-meta">
                            <div class="module-meta-row"><strong>Total:</strong> <?= (int) ($module['total'] ?? 0) ?></div>
                            <div class="module-meta-row"><strong>Approved:</strong> <?= (int) ($module['approved'] ?? 0) ?></div>
                            <div class="module-meta-row"><strong>Rejected:</strong> <?= (int) ($module['rejected'] ?? 0) ?></div>
                        </div>
                        <div style="margin-top:12px;">
                            <a class="btn btn-secondary" href="<?= htmlspecialchars((string) ($module['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>">Open <?= htmlspecialchars($module['name'], ENT_QUOTES, 'UTF-8') ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Pending Review Queue</h2>
            <span class="admin-section-chip"><?= is_array($pendingQueue) ? count($pendingQueue) : 0 ?> shown</span>
        </div>
        <div class="admin-section-body">
            <table class="admin-table">
                <tr>
                    <th>Module</th>
                    <th>Student</th>
                    <th>Student ID</th>
                    <th>Record</th>
                    <th>Submitted At</th>
                    <th>Action</th>
                </tr>
                <?php if (empty($pendingQueue)): ?>
                    <tr>
                        <td colspan="6" class="muted">No pending records. Great job keeping reviews up to date.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($pendingQueue as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['module'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['studentName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['studentId'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['recordTitle'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['submittedAt'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a class="link" href="<?= htmlspecialchars((string) ($row['listUrl'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>">Review</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Student Snapshot</h2>
            <span class="admin-section-chip">Top activity contributors</span>
        </div>
        <div class="admin-section-body">
            <table class="admin-table">
                <tr>
                    <th>Student</th>
                    <th>Student ID</th>
                    <th>Email</th>
                    <th>Merits</th>
                    <th>Events</th>
                    <th>Clubs</th>
                    <th>Achievements</th>
                </tr>
                <?php if (empty($studentSummaries)): ?>
                    <tr>
                        <td colspan="7" class="muted">No student records found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($studentSummaries as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($u['student_id'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($u['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) ($u['meritCount'] ?? 0) ?></td>
                        <td><?= (int) ($u['eventCount'] ?? 0) ?></td>
                        <td><?= (int) ($u['clubCount'] ?? 0) ?></td>
                        <td><?= (int) ($u['achievementCount'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>

