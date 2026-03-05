<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Merit Tracker Module</div>
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
        $totalRecords = is_array($merits) ? count($merits) : 0;
        $totalHours = 0.0;
        $latestDate = null;
        $earliestDate = null;
        $missingDateCount = 0;
        $recentCount = 0;
        $withDatesCount = 0;
        $missingDateRecords = [];
        $byActivity = [];
        $recentThreshold = date('Y-m-d', strtotime('-30 days'));

        if (is_array($merits)) {
            foreach ($merits as $row) {
                $h = isset($row['hours']) ? (float) $row['hours'] : 0.0;
                $totalHours += $h;

                $activity = (string) ($row['activityName'] ?? '');
                if ($activity !== '') {
                    $byActivity[$activity] = ($byActivity[$activity] ?? 0.0) + $h;
                }

                $dateTo = trim((string) ($row['dateTo'] ?? ''));
                $dateFrom = trim((string) ($row['dateFrom'] ?? ''));
                $dateTo = ($dateTo === '' || $dateTo === '0000-00-00') ? '' : $dateTo;
                $dateFrom = ($dateFrom === '' || $dateFrom === '0000-00-00') ? '' : $dateFrom;
                $missingAnyDate = ($dateFrom === '' || $dateTo === '');
                $candidate = $dateTo !== '' ? $dateTo : ($dateFrom !== '' ? $dateFrom : '');

                if ($candidate !== '') {
                    if (!$missingAnyDate) {
                        $withDatesCount++;
                    } else {
                        $missingDateCount++;
                        $missingDateRecords[] = $row;
                    }
                    if ($latestDate === null || strcmp((string) $candidate, (string) $latestDate) > 0) {
                        $latestDate = (string) $candidate;
                    }
                    if ($earliestDate === null || strcmp((string) $candidate, (string) $earliestDate) < 0) {
                        $earliestDate = (string) $candidate;
                    }
                    if ((string) $candidate >= $recentThreshold) {
                        $recentCount++;
                    }
                } else {
                    $missingDateCount++;
                    $missingDateRecords[] = $row;
                }
            }
        }

        $topActivity = null;
        $topHours = 0.0;
        foreach ($byActivity as $name => $hours) {
            if ($hours > $topHours) {
                $topHours = $hours;
                $topActivity = $name;
            }
        }

        $avgHours = $totalRecords > 0 ? $totalHours / $totalRecords : 0.0;

        arsort($byActivity);
        $mixActivities = array_slice($byActivity, 0, 3, true);

        $milestones = [10, 20, 50, 100];
        $nextMilestone = null;
        foreach ($milestones as $goal) {
            if ($totalHours < $goal) {
                $nextMilestone = $goal;
                break;
            }
        }
        $milestoneLabel = $nextMilestone ? $nextMilestone . " hrs" : "Goal complete";
        $milestoneProgress = $nextMilestone ? min(100, (int) round(($totalHours / $nextMilestone) * 100)) : 100;
    ?>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total Records</div>
            <div class="kpi-value"><?= (int) $totalRecords ?></div>
            <div class="kpi-sub">All-time entries</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Latest Activity</div>
            <div class="kpi-value"><?= htmlspecialchars($latestDate ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="kpi-sub">Most recent date</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Total Merit Hours</div>
            <div class="kpi-value"><?= (int) round($totalHours) ?></div>
            <div class="kpi-sub">Across all activities</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Top Activity</div>
            <div class="kpi-value"><?= htmlspecialchars($topActivity ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="kpi-sub"><?= $topActivity ? ((int) round($topHours)) . " hrs total" : "-" ?></div>
        </div>
    </div>

    <div class="page-header">
        <div>
            <h2 style="margin:0;">My Recorded Merits</h2>
            <div class="muted" style="margin-top:6px;">Track your co-curricular contribution hours below.</div>
        </div>
        <div class="page-actions">
            <a href="index.php?url=merit/create" class="btn">+ Add Merit</a>
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
                    <input type="hidden" name="url" value="merit/index">

                    <input
                        type="text"
                        name="search"
                        class="input"
                        placeholder="Search activity name..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                    <?php $currentSort = $_GET['sort'] ?? 'meritID'; ?>
                    <select name="sort" class="input">
                        <option value="meritID" <?= $currentSort === 'meritID' ? 'selected' : '' ?>>Newest</option>
                        <option value="activityName" <?= $currentSort === 'activityName' ? 'selected' : '' ?>>Activity Name</option>
                        <option value="hours" <?= $currentSort === 'hours' ? 'selected' : '' ?>>Hours</option>
                        <option value="dateFrom" <?= $currentSort === 'dateFrom' ? 'selected' : '' ?>>Date From</option>
                        <option value="dateTo" <?= $currentSort === 'dateTo' ? 'selected' : '' ?>>Date To</option>
                    </select>

                    <button class="btn" type="submit">Search / Filter</button>
                    <a class="btn btn-secondary" href="index.php?url=merit/index">Reset</a>
                </form>
            </div>

            <table>
                <tr>
                    <th>Activity</th>
                    <th>Hours</th>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Actions</th>
                </tr>

                <?php if (empty($merits)): ?>
                    <tr>
                        <td colspan="5" class="muted">No merit records found.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($merits as $m): ?>
                <?php
                    $dateFromDisplay = trim((string) ($m['dateFrom'] ?? ''));
                    $dateToDisplay = trim((string) ($m['dateTo'] ?? ''));
                    $dateFromDisplay = ($dateFromDisplay === '' || $dateFromDisplay === '0000-00-00') ? '' : $dateFromDisplay;
                    $dateToDisplay = ($dateToDisplay === '' || $dateToDisplay === '0000-00-00') ? '' : $dateToDisplay;
                    $dateFromMissing = $dateFromDisplay === '';
                    $dateToMissing = $dateToDisplay === '';
                ?>
                <tr>
                    <td><?= htmlspecialchars($m['activityName']) ?></td>
                    <td><?= htmlspecialchars($m['hours'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($dateFromMissing): ?>
                            <span class="status-badge warn">Date missing</span>
                            <a class="link" href="index.php?url=merit/edit&id=<?= htmlspecialchars($m['meritID'], ENT_QUOTES, 'UTF-8') ?>">Fix</a>
                        <?php else: ?>
                            <?= htmlspecialchars($dateFromDisplay, ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($dateToMissing): ?>
                            <span class="status-badge warn">Date missing</span>
                            <a class="link" href="index.php?url=merit/edit&id=<?= htmlspecialchars($m['meritID'], ENT_QUOTES, 'UTF-8') ?>">Fix</a>
                        <?php else: ?>
                            <?= htmlspecialchars($dateToDisplay, ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="link" href="index.php?url=merit/edit&id=<?= htmlspecialchars($m['meritID'], ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                        <span class="muted">|</span>
                        <form method="POST" action="index.php?url=merit/delete" style="display:inline;">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($m['meritID'], ENT_QUOTES, 'UTF-8') ?>">
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
                <div class="muted" style="margin-top:8px;"><?= (int) round($totalHours) ?> hrs logged</div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Quality</h3>
                    <span class="chip">Checks</span>
                </div>
                <ul class="list">
                    <li class="list-item">
                        <div>
                            <div class="list-item-title">Records with dates</div>
                            <div class="list-item-sub">Date coverage</div>
                        </div>
                        <div class="list-item-right">
                            <strong><?= (int) $withDatesCount ?></strong> / <?= (int) $totalRecords ?>
                        </div>
                    </li>
                    <li class="list-item">
                        <div>
                            <div class="list-item-title">Missing dates</div>
                            <div class="list-item-sub">Fix missing dates</div>
                        </div>
                        <div class="list-item-right">
                            <?= (int) $missingDateCount ?>
                        </div>
                    </li>
                    <li class="list-item">
                        <div>
                            <div class="list-item-title">Recent activity</div>
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
                                    <div class="list-item-title"><?= htmlspecialchars($missing['activityName'] ?? 'Untitled', ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="list-item-sub">Add date range</div>
                                </div>
                                <div class="list-item-right">
                                    <a class="link" href="index.php?url=merit/edit&id=<?= htmlspecialchars($missing['meritID'] ?? '', ENT_QUOTES, 'UTF-8') ?>">Fix</a>
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
