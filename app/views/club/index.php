<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Club Tracker Module</div>
            <div class="topbar-user-inline">
                <?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
        <div class="topbar-actions">
            <form method="POST" action="index.php?url=auth/logout">
                <?php csrf_field(); ?>
                <button type="submit" class="topbar-logout">Logout</button>
            </form>
        </div>
    </div>

    <div class="content">
        <div class="content-inner">

    <?php
        $totalRecords = is_array($clubs) ? count($clubs) : 0;
        $latestStartDate = null;
        $activeCount = 0;
        $roles = [];
        $withDatesCount = 0;
        $missingDateCount = 0;
        $recentCount = 0;
        $missingDateRecords = [];
        $recentThreshold = date('Y-m-d', strtotime('-30 days'));

        if (is_array($clubs)) {
            foreach ($clubs as $row) {
                $startDate = trim((string) ($row['startDate'] ?? ''));
                $startDate = ($startDate === '' || $startDate === '0000-00-00') ? '' : $startDate;
                if ($startDate !== '') {
                    $withDatesCount++;
                    if ($latestStartDate === null || strcmp((string) $startDate, (string) $latestStartDate) > 0) {
                        $latestStartDate = (string) $startDate;
                    }
                    if ((string) $startDate >= $recentThreshold) {
                        $recentCount++;
                    }
                } else {
                    $missingDateCount++;
                    $missingDateRecords[] = $row;
                }

                $endDate = trim((string) ($row['endDate'] ?? ''));
                $endDate = ($endDate === '' || $endDate === '0000-00-00') ? '' : $endDate;
                if ($endDate === '') {
                    $activeCount++;
                }

                $role = trim((string) ($row['role'] ?? ''));
                if ($role !== '') {
                    $roles[$role] = true;
                }
            }
        }

        $roleCount = count($roles);

        $milestones = [3, 5, 10, 20];
        $nextMilestone = null;
        foreach ($milestones as $goal) {
            if ($totalRecords < $goal) {
                $nextMilestone = $goal;
                break;
            }
        }
        $milestoneLabel = $nextMilestone ? $nextMilestone . " clubs" : "Goal complete";
        $milestoneProgress = $nextMilestone ? min(100, (int) round(($totalRecords / $nextMilestone) * 100)) : 100;
    ?>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total Clubs</div>
            <div class="kpi-value"><?= (int) $totalRecords ?></div>
            <div class="kpi-sub">All records</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Latest Start</div>
            <div class="kpi-value"><?= htmlspecialchars($latestStartDate ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="kpi-sub">Most recent join date</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Active Memberships</div>
            <div class="kpi-value"><?= (int) $activeCount ?></div>
            <div class="kpi-sub">No end date set</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Roles Held</div>
            <div class="kpi-value"><?= (int) $roleCount ?></div>
            <div class="kpi-sub">Unique roles</div>
        </div>
    </div>

    <div class="page-header">
        <div>
            <h2 style="margin:0;">My Club Records</h2>
            <div class="muted" style="margin-top:6px;">Track your club memberships and roles.</div>
        </div>
        <div class="page-actions">
            <a href="index.php?url=club/create" class="btn">+ Add Club</a>
        </div>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="success">
            <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="split-layout">
        <div>
            <div class="card" style="margin-bottom:16px;">
                <form method="GET" class="filter-bar">
                    <input type="hidden" name="url" value="club/index">

                    <input
                        type="text"
                        name="search"
                        class="input"
                        placeholder="Search club name..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                    <?php $currentSort = $_GET['sort'] ?? 'clubID'; ?>
                    <select name="sort" class="input">
                        <option value="clubID" <?= $currentSort === 'clubID' ? 'selected' : '' ?>>Newest</option>
                        <option value="clubName" <?= $currentSort === 'clubName' ? 'selected' : '' ?>>Club Name</option>
                        <option value="role" <?= $currentSort === 'role' ? 'selected' : '' ?>>Role</option>
                        <option value="startDate" <?= $currentSort === 'startDate' ? 'selected' : '' ?>>Start Date</option>
                        <option value="endDate" <?= $currentSort === 'endDate' ? 'selected' : '' ?>>End Date</option>
                    </select>

                    <button class="btn" type="submit">Search / Filter</button>
                    <a class="btn btn-secondary" href="index.php?url=club/index">Reset</a>
                </form>
            </div>

            <table>
                <tr>
                    <th>Club Name</th>
                    <th>Role</th>
                    <th>Role Description</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>

                <?php if (empty($clubs)): ?>
                    <tr>
                        <td colspan="6" class="muted">No club records found.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($clubs as $c): ?>
                <?php
                    $startDateDisplay = trim((string) ($c['startDate'] ?? ''));
                    $startDateDisplay = ($startDateDisplay === '' || $startDateDisplay === '0000-00-00') ? '' : $startDateDisplay;
                    $startDateMissing = $startDateDisplay === '';
                    $endDateDisplay = trim((string) ($c['endDate'] ?? ''));
                    $endDateDisplay = ($endDateDisplay === '' || $endDateDisplay === '0000-00-00') ? '-' : $endDateDisplay;
                ?>
                <tr>
                    <td><?= htmlspecialchars($c['clubName'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['role'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['roleDescription'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($startDateMissing): ?>
                            <span class="status-badge warn">Date missing</span>
                            <a class="link" href="index.php?url=club/edit&id=<?= htmlspecialchars($c['clubID'], ENT_QUOTES, 'UTF-8') ?>">Fix</a>
                        <?php else: ?>
                            <?= htmlspecialchars($startDateDisplay, ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($endDateDisplay, ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a class="link" href="index.php?url=club/edit&id=<?= htmlspecialchars($c['clubID'], ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                        <span class="muted">|</span>
                        <form method="POST" action="index.php?url=club/delete" style="display:inline;">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($c['clubID'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="link danger" onclick="return confirm('Are you sure you want to delete this record?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>

            </table>
        </div>

        <div class="side-stack">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Milestones</h3>
                    <span class="chip">Progress</span>
                </div>
                <div class="muted" style="margin-bottom:10px;">Keep building toward your next goal.</div>
                <div class="milestone-value">Next goal: <?= htmlspecialchars($milestoneLabel, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: <?= (int) $milestoneProgress ?>%;"></div>
                </div>
                <div class="muted" style="margin-top:8px;"><?= (int) $totalRecords ?> clubs logged</div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Quality</h3>
                    <span class="chip">Checks</span>
                </div>
                <ul class="list">
                    <li class="list-item">
                        <div>
                            <div class="list-item-title">Records with start dates</div>
                            <div class="list-item-sub">Date coverage</div>
                        </div>
                        <div class="list-item-right">
                            <strong><?= (int) $withDatesCount ?></strong> / <?= (int) $totalRecords ?>
                        </div>
                    </li>
                    <li class="list-item">
                        <div>
                            <div class="list-item-title">Missing start dates</div>
                            <div class="list-item-sub">Fix missing dates</div>
                        </div>
                        <div class="list-item-right">
                            <?= (int) $missingDateCount ?>
                        </div>
                    </li>
                    <li class="list-item">
                        <div>
                            <div class="list-item-title">Recent joins</div>
                            <div class="list-item-sub">Last 30 days</div>
                        </div>
                        <div class="list-item-right">
                            <?= (int) $recentCount ?> record<?= $recentCount === 1 ? '' : 's' ?>
                        </div>
                    </li>
                </ul>
                <?php if ($missingDateCount > 0): ?>
                    <div class="muted" style="margin-top:10px;">Records needing update:</div>
                    <ul class="list" style="margin-top:8px;">
                        <?php foreach (array_slice($missingDateRecords, 0, 3) as $missing): ?>
                            <li class="list-item">
                                <div>
                                    <div class="list-item-title"><?= htmlspecialchars($missing['clubName'] ?? 'Untitled', ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="list-item-sub">Add start date</div>
                                </div>
                                <div class="list-item-right">
                                    <a class="link" href="index.php?url=club/edit&id=<?= htmlspecialchars($missing['clubID'] ?? '', ENT_QUOTES, 'UTF-8') ?>">Fix</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <?php if ($missingDateCount > 3): ?>
                            <li class="muted">And <?= (int) ($missingDateCount - 3) ?> more record(s).</li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
