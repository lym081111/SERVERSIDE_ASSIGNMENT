<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main module-page">

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
        $recentCount = 0;
        $approvedCount = 0;
        $pendingCount = 0;
        $rejectedCount = 0;
        $leadershipScore = 0;
        $leadershipMonths = 0;
        $highestRole = null;
        $highestRoleWeight = 0;
        $journeyEntries = [];
        $recentThreshold = date('Y-m-d', strtotime('-30 days'));

        // New: unique approved clubs for Total Clubs
        $uniqueApprovedClubs = [];

        // New: unique approved active clubs for Active Memberships
        $uniqueActiveClubs = [];

        if (is_array($clubs)) {
            foreach ($clubs as $row) {
                $startDate = trim((string) ($row['startDate'] ?? ''));
                $startDate = ($startDate === '' || $startDate === '0000-00-00') ? '' : $startDate;

                $endDate = trim((string) ($row['endDate'] ?? ''));
                $endDate = ($endDate === '' || $endDate === '0000-00-00') ? '' : $endDate;

                $statusValue = strtolower(trim((string) ($row['status'] ?? 'pending')));
                $clubName = trim((string) ($row['clubName'] ?? ''));

                if ($startDate !== '') {
                    if ($latestStartDate === null || strcmp((string) $startDate, (string) $latestStartDate) > 0) {
                        $latestStartDate = (string) $startDate;
                    }
                    if ((string) $startDate >= $recentThreshold) {
                        $recentCount++;
                    }
                }

                // Count only approved unique club names
                if ($statusValue === 'approved' && $clubName !== '') {
                    $uniqueApprovedClubs[strtolower($clubName)] = true;
                }

                // Count only approved unique club names that have not reached end date
                if (
                    $statusValue === 'approved'
                    && $clubName !== ''
                    && (
                        $endDate === ''
                        || $endDate >= date('Y-m-d')
                    )
                ) {
                    $uniqueActiveClubs[strtolower($clubName)] = true;
                }

                $role = trim((string) ($row['role'] ?? ''));
                if ($role !== '') {
                    $roles[$role] = true;
                }

                $roleLower = strtolower($role);
                $roleWeight = 1;
                if (strpos($roleLower, 'president') !== false || strpos($roleLower, 'chair') !== false) {
                    $roleWeight = 5;
                } elseif (strpos($roleLower, 'vice') !== false || strpos($roleLower, 'captain') !== false) {
                    $roleWeight = 4;
                } elseif (strpos($roleLower, 'secretary') !== false || strpos($roleLower, 'treasurer') !== false || strpos($roleLower, 'committee') !== false) {
                    $roleWeight = 3;
                } elseif (strpos($roleLower, 'leader') !== false || strpos($roleLower, 'head') !== false) {
                    $roleWeight = 4;
                } elseif (strpos($roleLower, 'member') !== false) {
                    $roleWeight = 2;
                }

                if ($roleWeight > $highestRoleWeight && $role !== '') {
                    $highestRoleWeight = $roleWeight;
                    $highestRole = $role;
                }

                if ($startDate !== '') {
                    $startTs = strtotime($startDate);
                    $endTs = $endDate !== '' ? strtotime($endDate) : strtotime(date('Y-m-d'));
                    if ($startTs !== false && $endTs !== false && $endTs >= $startTs) {
                        $days = (int) floor(($endTs - $startTs) / 86400);
                        $months = max(1, (int) floor($days / 30) + 1);
                        $leadershipMonths += $months;
                        $leadershipScore += $months * $roleWeight;
                        $journeyEntries[] = [
                            'clubName' => (string) ($row['clubName'] ?? 'Club'),
                            'role' => $role !== '' ? $role : 'Member',
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                            'months' => $months,
                        ];
                    }
                }

                if ($statusValue === 'approved') {
                    $approvedCount++;
                } elseif ($statusValue === 'rejected') {
                    $rejectedCount++;
                } else {
                    $pendingCount++;
                }
            }
        }

        $totalUniqueApprovedClubs = count($uniqueApprovedClubs);
        $activeUniqueApprovedClubs = count($uniqueActiveClubs);

        $roleCount = count($roles);
        usort($journeyEntries, function ($a, $b) {
            return strcmp((string) ($b['startDate'] ?? ''), (string) ($a['startDate'] ?? ''));
        });
        $recentJourney = array_slice($journeyEntries, 0, 4);

        $milestones = [3, 5, 10, 20];
        $nextMilestone = null;
        foreach ($milestones as $goal) {
            if ($totalUniqueApprovedClubs < $goal) {
                $nextMilestone = $goal;
                break;
            }
        }
        $milestoneLabel = $nextMilestone ? $nextMilestone . " clubs" : "Goal complete";
        $milestoneProgress = $nextMilestone ? min(100, (int) round(($totalUniqueApprovedClubs / $nextMilestone) * 100)) : 100;
    ?>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total Clubs</div>
            <div class="kpi-value"><?= (int) $totalUniqueApprovedClubs ?></div>
            <div class="kpi-sub">Unique approved clubs</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Latest Start</div>
            <div class="kpi-value"><?= htmlspecialchars($latestStartDate ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="kpi-sub">Most recent join date</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Active Memberships</div>
            <div class="kpi-value"><?= (int) $activeUniqueApprovedClubs ?></div>
            <div class="kpi-sub">Approved and not ended</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Roles Held</div>
            <div class="kpi-value"><?= (int) $roleCount ?></div>
            <div class="kpi-sub">Unique roles</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Leadership Score</div>
            <div class="kpi-value"><?= (int) $leadershipScore ?></div>
            <div class="kpi-sub"><?= (int) $leadershipMonths ?> month(s) contribution</div>
        </div>
    </div>

    <div class="page-header">
        <div>
            <h2 style="margin:0;">My Club Records</h2>
            <div class="muted" style="margin-top:6px;">Track your club memberships and roles.</div>
        </div>
        <div class="page-actions">
            <a href="index.php?url=club/create" class="btn">+ Add/Update Club</a>
            <a href="index.php?url=club/timeline" class="btn btn-secondary">View Timeline</a>
            <a href="index.php?url=club/exportSelf" class="btn btn-secondary no-print">Export my CSV</a>
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

    <div class="split-layout">
        <div>
            <div class="card" style="margin-bottom:16px;">
                <form method="GET" class="filter-bar">
                    <input type="hidden" name="url" value="club/index">

                    <input
                        type="text"
                        name="search"
                        class="input"
                        placeholder="Search club name, role, or role description..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                    <?php $currentSort = $_GET['sort'] ?? 'clubID'; ?>
                    <select name="sort" class="input">
                        <option value="clubID" <?= $currentSort === 'clubID' ? 'selected' : '' ?>>Newest</option>
                        <option value="clubName" <?= $currentSort === 'clubName' ? 'selected' : '' ?>>Club Name</option>
                        <option value="role" <?= $currentSort === 'role' ? 'selected' : '' ?>>Role</option>
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

            <table class="co-records-table">
                <tr>
                    <th>Club Name</th>
                    <th>Role</th>
                    <th>Role Description</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

                <?php if (empty($clubs)): ?>
                    <tr>
                        <td colspan="7" class="muted">No club records found.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($clubs as $c): ?>
                <?php
                    $startDateDisplay = trim((string) ($c['startDate'] ?? ''));
                    $startDateDisplay = ($startDateDisplay === '' || $startDateDisplay === '0000-00-00') ? '' : $startDateDisplay;
                    $endDateDisplay = trim((string) ($c['endDate'] ?? ''));
                    $endDateDisplay = ($endDateDisplay === '' || $endDateDisplay === '0000-00-00') ? '-' : $endDateDisplay;
                    $status = (string) ($c['status'] ?? 'approved');
                    $reviewNote = trim((string) ($c['review_note'] ?? ''));
                    $evidencePath = trim((string) ($c['evidence_path'] ?? ''));
                    $isLocked = $status === 'approved';
                ?>
                <tr>
                    <td><?= htmlspecialchars($c['clubName'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['role'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['roleDescription'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($startDateDisplay !== '' ? $startDateDisplay : '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($endDateDisplay, ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="status-badge <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php if ($reviewNote !== ''): ?>
                            <div class="muted" style="margin-top:4px;font-size:0.85rem;">
                                Note: <?= htmlspecialchars($reviewNote, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($evidencePath !== ''): ?>
                            <div class="muted" style="margin-top:4px;font-size:0.85rem;">
                                <a class="link" href="<?= htmlspecialchars(BASE_URL . ltrim($evidencePath, '/'), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">View proof</a>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($isLocked): ?>
                            <span class="muted">Locked after approval (admin only)</span>
                        <?php else: ?>
                            <a class="link" href="index.php?url=club/edit&id=<?= htmlspecialchars($c['clubID'], ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                            <span class="muted">|</span>
                            <form method="POST" action="index.php?url=club/delete" style="display:inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($c['clubID'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="link danger" onclick="return confirm('Are you sure you want to delete this record?')">
                                    Delete
                                </button>
                            </form>
                        <?php endif; ?>
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
                <div class="muted" style="margin-top:8px;"><?= (int) $totalUniqueApprovedClubs ?> clubs logged</div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Leadership Journey</h3>
                    <span class="chip">Role progression</span>
                </div>
                <div class="muted" style="margin-bottom:10px;">
                    Highest role: <strong><?= htmlspecialchars($highestRole ?? 'Member', ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
                <?php if (empty($recentJourney)): ?>
                    <div class="muted">No leadership timeline data available yet.</div>
                <?php else: ?>
                    <ul class="list">
                        <?php foreach ($recentJourney as $journey): ?>
                            <li class="list-item">
                                <div>
                                    <div class="list-item-title">
                                        <?= htmlspecialchars(($journey['clubName'] ?? 'Club') . ' · ' . ($journey['role'] ?? 'Member'), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div class="list-item-sub">
                                        <?= htmlspecialchars(($journey['startDate'] ?? '-') . ' to ' . (($journey['endDate'] ?? '') !== '' ? $journey['endDate'] : 'Present'), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                                <div class="list-item-right"><?= (int) ($journey['months'] ?? 0) ?> mo</div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Review Overview</h3>
                    <span class="chip">Status</span>
                </div>
                <ul class="list">
                    <li class="list-item">
                        <div>
                            <div class="list-item-title">Approved</div>
                            <div class="list-item-sub">Locked and verified</div>
                        </div>
                        <div class="list-item-right">
                            <strong><?= (int) $approvedCount ?></strong>
                        </div>
                    </li>
                    <li class="list-item">
                        <div>
                            <div class="list-item-title">Pending</div>
                            <div class="list-item-sub">Waiting for admin review</div>
                        </div>
                        <div class="list-item-right">
                            <?= (int) $pendingCount ?>
                        </div>
                    </li>
                    <li class="list-item">
                        <div>
                            <div class="list-item-title">Rejected</div>
                            <div class="list-item-sub">Need edits before resubmission</div>
                        </div>
                        <div class="list-item-right">
                            <?= (int) $rejectedCount ?>
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
            </div>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>