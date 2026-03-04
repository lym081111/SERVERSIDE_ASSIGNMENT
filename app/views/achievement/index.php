<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Achievements Module</div>
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
        $totalRecords = is_array($achievements) ? count($achievements) : 0;
        $latestDate = null;
        $categories = [];
        $recentCount = 0;
        $withDatesCount = 0;
        $missingDateCount = 0;
        $missingDateRecords = [];
        $threshold = date('Y-m-d', strtotime('-90 days'));

        if (is_array($achievements)) {
            foreach ($achievements as $row) {
                $dateReceived = trim((string) ($row['dateReceived'] ?? ''));
                $dateReceived = ($dateReceived === '' || $dateReceived === '0000-00-00') ? '' : $dateReceived;
                if ($dateReceived !== '') {
                    $withDatesCount++;
                    if ($latestDate === null || strcmp((string) $dateReceived, (string) $latestDate) > 0) {
                        $latestDate = (string) $dateReceived;
                    }
                    if ((string) $dateReceived >= $threshold) {
                        $recentCount++;
                    }
                } else {
                    $missingDateCount++;
                    $missingDateRecords[] = $row;
                }

                $category = trim((string) ($row['category'] ?? ''));
                if ($category !== '') {
                    $categories[$category] = true;
                }
            }
        }

        $categoryCount = count($categories);

        $milestones = [3, 5, 10, 20];
        $nextMilestone = null;
        foreach ($milestones as $goal) {
            if ($totalRecords < $goal) {
                $nextMilestone = $goal;
                break;
            }
        }
        $milestoneLabel = $nextMilestone ? $nextMilestone . " awards" : "Goal complete";
        $milestoneProgress = $nextMilestone ? min(100, (int) round(($totalRecords / $nextMilestone) * 100)) : 100;
    ?>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total Achievements</div>
            <div class="kpi-value"><?= (int) $totalRecords ?></div>
            <div class="kpi-sub">All records</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Latest Award</div>
            <div class="kpi-value"><?= htmlspecialchars($latestDate ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="kpi-sub">Most recent date</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Recent Awards</div>
            <div class="kpi-value"><?= (int) $recentCount ?></div>
            <div class="kpi-sub">Last 90 days</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Categories</div>
            <div class="kpi-value"><?= (int) $categoryCount ?></div>
            <div class="kpi-sub">Distinct categories</div>
        </div>
    </div>

    <div class="page-header">
        <div>
            <h2 style="margin:0;">My Achievement Records</h2>
            <div class="muted" style="margin-top:6px;">Record awards, competition results, and recognition.</div>
        </div>
        <div class="page-actions">
            <a href="index.php?url=achievement/create" class="btn">+ Add Achievement</a>
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
                    <input type="hidden" name="url" value="achievement/index">

                    <input
                        type="text"
                        name="search"
                        class="input"
                        placeholder="Search achievement title..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                    <?php $currentSort = $_GET['sort'] ?? 'achievementID'; ?>
                    <select name="sort" class="input">
                        <option value="achievementID" <?= $currentSort === 'achievementID' ? 'selected' : '' ?>>Newest</option>
                        <option value="title" <?= $currentSort === 'title' ? 'selected' : '' ?>>Title</option>
                        <option value="category" <?= $currentSort === 'category' ? 'selected' : '' ?>>Category</option>
                        <option value="dateReceived" <?= $currentSort === 'dateReceived' ? 'selected' : '' ?>>Date Received</option>
                    </select>

                    <button class="btn" type="submit">Search / Filter</button>
                    <a class="btn btn-secondary" href="index.php?url=achievement/index">Reset</a>
                </form>
            </div>

            <table>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date Received</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>

                <?php if (empty($achievements)): ?>
                    <tr>
                        <td colspan="5" class="muted">No achievement records found.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($achievements as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($a['category'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($a['dateReceived'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($a['description'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a class="link" href="index.php?url=achievement/edit&id=<?= htmlspecialchars($a['achievementID'], ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                        <span class="muted">|</span>
                        <form method="POST" action="index.php?url=achievement/delete" style="display:inline;">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($a['achievementID'], ENT_QUOTES, 'UTF-8') ?>">
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
                <div class="muted" style="margin-top:8px;"><?= (int) $totalRecords ?> awards logged</div>
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
                            <div class="list-item-title">Recent awards</div>
                            <div class="list-item-sub">Last 90 days</div>
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
                                    <div class="list-item-title"><?= htmlspecialchars($missing['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="list-item-sub">Add award date</div>
                                </div>
                                <div class="list-item-right">
                                    <a class="link" href="index.php?url=achievement/edit&id=<?= htmlspecialchars($missing['achievementID'] ?? '', ENT_QUOTES, 'UTF-8') ?>">Fix</a>
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
