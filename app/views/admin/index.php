<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<style>
    .admin-simple-page .content-inner {
        max-width: 1440px;
    }

    .admin-simple-page .admin-content {
        background:
            radial-gradient(circle at 12% 5%, rgba(37, 99, 235, 0.08), transparent 34%),
            radial-gradient(circle at 88% 10%, rgba(22, 163, 74, 0.08), transparent 34%),
            #f3f6fb;
    }

    .admin-simple-page .admin-hero {
        margin-bottom: 14px;
        padding: 16px 18px;
        border: 1px solid #9ab6ea;
        background: linear-gradient(120deg, #eaf2ff 0%, #f5f9ff 52%, #eefcf4 100%);
    }

    .admin-focus-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 14px;
    }

    .admin-focus-card {
        border: 1px solid #cbd7e6;
        border-radius: 14px;
        background: linear-gradient(180deg, #ffffff, #f8fbff);
        padding: 16px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    }

    .admin-focus-card.pending {
        border-color: #e8b04d;
        background: linear-gradient(160deg, #fff1d6, #ffffff 72%);
    }

    .admin-focus-card.approval {
        border-color: #6ea5ff;
        background: linear-gradient(160deg, #e0eeff, #ffffff 72%);
    }

    .admin-focus-card.students {
        border-color: #5bc590;
        background: linear-gradient(160deg, #e1f8ea, #ffffff 72%);
    }

    .admin-focus-label {
        margin: 0;
        font-size: 0.86rem;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 800;
    }

    .admin-focus-value {
        margin: 8px 0 0;
        font-size: 2rem;
        line-height: 1.1;
        font-weight: 900;
        color: #0f172a;
    }

    .admin-focus-sub {
        margin-top: 6px;
        font-size: 0.92rem;
        color: #475569;
    }

    .admin-secondary-strip {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }

    .admin-secondary-item {
        border: 1px solid #d5dfec;
        border-radius: 10px;
        background: #ffffff;
        padding: 10px 12px;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.04);
    }

    .admin-secondary-item:nth-child(1) { background: #eef5ff; border-color: #bdd5ff; }
    .admin-secondary-item:nth-child(2) { background: #f0f6ff; border-color: #c2d7ff; }
    .admin-secondary-item:nth-child(3) { background: #ecfbf2; border-color: #b6e5ca; }
    .admin-secondary-item:nth-child(4) { background: #fff2ea; border-color: #f0c4ab; }

    .admin-secondary-item .label {
        margin: 0;
        font-size: 0.8rem;
        color: #64748b;
    }

    .admin-secondary-item .value {
        margin: 4px 0 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }

    .admin-module-workbench {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .admin-module-card {
        border: 1px solid #dbe3ee;
        border-radius: 12px;
        background: #ffffff;
        padding: 14px;
        display: grid;
        gap: 10px;
    }

    .admin-module-card:nth-child(1) {
        border-color: rgba(54, 93, 157, 0.5);
        background: linear-gradient(160deg, rgba(54, 93, 157, 0.22), #ffffff 72%);
    }

    .admin-module-card:nth-child(2) {
        border-color: rgba(47, 111, 76, 0.5);
        background: linear-gradient(160deg, rgba(47, 111, 76, 0.22), #ffffff 72%);
    }

    .admin-module-card:nth-child(3) {
        border-color: rgba(43, 127, 114, 0.5);
        background: linear-gradient(160deg, rgba(43, 127, 114, 0.2), #ffffff 72%);
    }

    .admin-module-card:nth-child(4) {
        border-color: rgba(161, 120, 58, 0.5);
        background: linear-gradient(160deg, rgba(161, 120, 58, 0.2), #ffffff 72%);
    }

    .admin-module-card h3 {
        margin: 0;
        font-size: 1rem;
        color: #0f172a;
    }

    .admin-module-pending {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 800;
        width: fit-content;
        background: #e2e8f0;
        color: #0f172a;
    }

    .admin-module-pending.has-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .admin-module-meta {
        font-size: 0.9rem;
        color: #334155;
        line-height: 1.5;
    }

    .admin-module-meta strong {
        color: #0f172a;
    }

    .admin-module-open {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        font-weight: 800;
        border-radius: 10px;
        padding: 11px 12px;
    }

    .admin-module-card:nth-child(1) .admin-module-open {
        background: #1e40af;
        border-color: #1e40af;
    }

    .admin-module-card:nth-child(1) .admin-module-open:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
    }

    .admin-module-card:nth-child(2) .admin-module-open {
        background: #166534;
        border-color: #166534;
    }

    .admin-module-card:nth-child(2) .admin-module-open:hover {
        background: #15803d;
        border-color: #15803d;
    }

    .admin-module-card:nth-child(3) .admin-module-open {
        background: #0f766e;
        border-color: #0f766e;
    }

    .admin-module-card:nth-child(3) .admin-module-open:hover {
        background: #0d9488;
        border-color: #0d9488;
    }

    .admin-module-card:nth-child(4) .admin-module-open {
        background: #a16207;
        border-color: #a16207;
    }

    .admin-module-card:nth-child(4) .admin-module-open:hover {
        background: #b45309;
        border-color: #b45309;
    }

    .admin-queue-table th,
    .admin-queue-table td {
        padding: 12px 14px;
        font-size: 0.95rem;
    }

    .admin-queue-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .admin-queue-toolbar label {
        font-size: 0.84rem;
        font-weight: 700;
        color: #334155;
    }

    .admin-queue-toolbar select {
        min-width: 180px;
        padding: 8px 10px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #0f172a;
        font: inherit;
    }

    .admin-queue-count-note {
        margin-left: auto;
        font-size: 0.84rem;
        color: #64748b;
    }

    .student-mini-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .student-mini-card {
        border: 1px solid #d5dfec;
        border-radius: 12px;
        padding: 12px;
        background: #ffffff;
    }

    .student-mini-name {
        font-weight: 800;
        color: #0f172a;
    }

    .student-mini-id {
        margin-top: 3px;
        color: #64748b;
        font-size: 0.85rem;
    }

    .student-mini-stats {
        margin-top: 10px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 6px;
    }

    .student-mini-stat {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        padding: 7px 6px;
        text-align: center;
    }

    .student-mini-stat .k {
        font-size: 0.72rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .student-mini-stat .v {
        margin-top: 3px;
        font-weight: 800;
        color: #0f172a;
    }

    @media (max-width: 1280px) {
        .admin-focus-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .admin-secondary-strip {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .admin-module-workbench {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .student-mini-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 820px) {
        .admin-focus-grid {
            grid-template-columns: 1fr;
        }

        .admin-secondary-strip {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .admin-module-workbench {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="main module-page admin-simple-page">

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
            <p class="admin-subtitle">Simplified overview for quick decisions and clearer review workflow.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-secondary" href="#pending-queue">Go to Queue</a>
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

    <div class="admin-focus-grid">
        <div class="admin-focus-card pending">
            <p class="admin-focus-label">Pending Reviews</p>
            <div class="admin-focus-value"><?= (int) $totalPendingReviews ?></div>
            <div class="admin-focus-sub">Records waiting for admin action</div>
        </div>
        <div class="admin-focus-card approval">
            <p class="admin-focus-label">Approval Rate</p>
            <div class="admin-focus-value"><?= htmlspecialchars((string) $approvalRate, ENT_QUOTES, 'UTF-8') ?>%</div>
            <div class="admin-focus-sub">Approved over all reviewed records</div>
        </div>
        <div class="admin-focus-card students">
            <p class="admin-focus-label">Active Students</p>
            <div class="admin-focus-value"><?= (int) $activeStudentCount ?></div>
            <div class="admin-focus-sub">Students with at least one record</div>
        </div>
    </div>

    <div class="admin-secondary-strip">
        <div class="admin-secondary-item">
            <p class="label">All Records</p>
            <div class="value"><?= (int) $totalRecords ?></div>
        </div>
        <div class="admin-secondary-item">
            <p class="label">Reviewed</p>
            <div class="value"><?= (int) $totalReviewed ?></div>
        </div>
        <div class="admin-secondary-item">
            <p class="label">Approved</p>
            <div class="value"><?= (int) $totalApproved ?></div>
        </div>
        <div class="admin-secondary-item">
            <p class="label">Rejected</p>
            <div class="value"><?= (int) $totalRejected ?></div>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Module Workbench</h2>
            <span class="admin-section-chip">Open by priority</span>
        </div>
        <div class="admin-section-body">
            <div class="admin-module-workbench">
                <?php foreach ($moduleSummaries as $module): ?>
                    <article class="admin-module-card">
                        <h3><?= htmlspecialchars($module['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <span class="admin-module-pending <?= ((int) ($module['pending'] ?? 0)) > 0 ? 'has-pending' : '' ?>">
                            <?= (int) ($module['pending'] ?? 0) ?> pending
                        </span>
                        <div class="admin-module-meta">
                            Total: <strong><?= (int) ($module['total'] ?? 0) ?></strong><br>
                            Approved: <strong><?= (int) ($module['approved'] ?? 0) ?></strong><br>
                            Rejected: <strong><?= (int) ($module['rejected'] ?? 0) ?></strong>
                        </div>
                        <a class="btn admin-module-open" href="<?= htmlspecialchars((string) ($module['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>">
                            Open <?= htmlspecialchars($module['name'], ENT_QUOTES, 'UTF-8') ?> Records
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="admin-section" id="pending-queue">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Pending Review Queue</h2>
            <span class="admin-section-chip"><?= is_array($pendingQueue) ? count($pendingQueue) : 0 ?> shown</span>
        </div>
        <div class="admin-section-body">
            <form method="GET" action="index.php#pending-queue" class="admin-queue-toolbar">
                <input type="hidden" name="url" value="admin/index">
                <div>
                    <label for="queueModule">Category</label><br>
                    <select id="queueModule" name="queue_module">
                        <?php foreach (($queueModuleOptions ?? []) as $value => $label): ?>
                            <option value="<?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?>" <?= (($queueModule ?? 'all') === $value) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="queueSort">Sort</label><br>
                    <select id="queueSort" name="queue_sort">
                        <?php foreach (($queueSortOptions ?? []) as $value => $label): ?>
                            <option value="<?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?>" <?= (($queueSort ?? 'oldest') === $value) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Apply</button>
                <a class="btn btn-secondary" href="index.php?url=admin/index#pending-queue">Reset</a>
                <div class="admin-queue-count-note">
                    Showing <?= is_array($pendingQueue) ? count($pendingQueue) : 0 ?> pending record(s)
                </div>
            </form>

            <div class="records-table-wrap">
                <table class="admin-table admin-queue-table">
                    <tr>
                        <th>Submitted At</th>
                        <th>Student</th>
                        <th>Student ID</th>
                        <th>Student Email</th>
                        <th>Module Info</th>
                        <th>Action</th>
                    </tr>
                    <?php if (empty($pendingQueue)): ?>
                        <tr>
                            <td colspan="6" class="muted">No pending records. Great job keeping reviews up to date.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($pendingQueue as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['submittedAt'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['studentName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['studentId'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['studentEmail'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?= htmlspecialchars($row['module'] ?? '-', ENT_QUOTES, 'UTF-8') ?>:
                                <?= htmlspecialchars($row['recordTitle'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <a class="btn btn-secondary" href="<?= htmlspecialchars((string) ($row['reviewUrl'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
