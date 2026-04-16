<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main module-page">

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
        $recentCount = 0;
        $approvedCount = 0;
        $pendingCount = 0;
        $rejectedCount = 0;
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
                $candidate = $dateTo !== '' ? $dateTo : ($dateFrom !== '' ? $dateFrom : '');

                if ($candidate !== '') {
                    if ($latestDate === null || strcmp((string) $candidate, (string) $latestDate) > 0) {
                        $latestDate = (string) $candidate;
                    }
                    if ((string) $candidate >= $recentThreshold) {
                        $recentCount++;
                    }
                }

                $statusValue = (string) ($row['status'] ?? 'pending');
                if ($statusValue === 'approved') {
                    $approvedCount++;
                } elseif ($statusValue === 'rejected') {
                    $rejectedCount++;
                } else {
                    $pendingCount++;
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

        arsort($byActivity);

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

        $approvedHoursForCert = isset($approvedMeritHours) ? (int) $approvedMeritHours : 0;
        $nextCertificateMilestone = ((int) floor($approvedHoursForCert / 100) + 1) * 100;
        $hoursToNextCertificate = max(0, $nextCertificateMilestone - $approvedHoursForCert);
        $certificateProgressPercent = $nextCertificateMilestone > 0
            ? min(100, (int) round(($approvedHoursForCert / $nextCertificateMilestone) * 100))
            : 0;
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

        <div class="kpi-card">
            <div class="kpi-label">Approved Merit Hours</div>
            <div class="kpi-value"><?= (int) ($approvedMeritHours ?? 0) ?></div>
            <div class="kpi-sub"><?= (int) ($certificateCount ?? 0) ?> certificate(s) earned</div>
        </div>
    </div>

    <div class="page-header">
        <div>
            <h2 style="margin:0;">My Recorded Merits</h2>
            <div class="muted" style="margin-top:6px;">Track your co-curricular contribution hours below.</div>
            <?php if (!empty($latestCertificate)): ?>
                <div class="muted" style="margin-top:6px;">
                    Latest certificate: <?= htmlspecialchars($latestCertificate['milestone_hours'] ?? '-', ENT_QUOTES, 'UTF-8') ?>h
                    (<?= htmlspecialchars($latestCertificate['certificate_code'] ?? '-', ENT_QUOTES, 'UTF-8') ?>)
                </div>
            <?php endif; ?>
        </div>
        <div class="page-actions">
            <a href="index.php?url=merit/create" class="btn">+ Add Merit</a>
            <a href="index.php?url=certificate/myMerit" class="btn btn-secondary">My Certificates</a>
            <a href="index.php?url=merit/exportSelf" class="btn btn-secondary no-print">Export my CSV</a>
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
                    <input type="hidden" name="url" value="merit/index">

                    <input
                        type="text"
                        name="search"
                        class="input"
                        placeholder="Search activity, event, or club..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                    <?php $currentSort = $_GET['sort'] ?? 'meritID'; ?>
                    <select name="sort" class="input">
                        <option value="meritID" <?= $currentSort === 'meritID' ? 'selected' : '' ?>>Newest</option>
                        <option value="activityName" <?= $currentSort === 'activityName' ? 'selected' : '' ?>>Activity Name</option>
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

            <table class="co-records-table">
                <tr>
                    <th>Club</th>
                    <th>Event</th>
                    <th>Activity</th>
                    <th>Points</th>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Status</th>
                    <th>Appeal</th>
                    <th>Actions</th>
                </tr>

                <?php if (empty($merits)): ?>
                    <tr>
                        <td colspan="9" class="muted">No merit records found.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($merits as $m): ?>
                <?php
                    $dateFromDisplay = trim((string) ($m['dateFrom'] ?? ''));
                    $dateToDisplay = trim((string) ($m['dateTo'] ?? ''));
                    $dateFromDisplay = ($dateFromDisplay === '' || $dateFromDisplay === '0000-00-00') ? '' : $dateFromDisplay;
                    $dateToDisplay = ($dateToDisplay === '' || $dateToDisplay === '0000-00-00') ? '' : $dateToDisplay;
                    $status = (string) ($m['status'] ?? 'approved');
                    $reviewNote = trim((string) ($m['review_note'] ?? ''));
                    $evidencePath = trim((string) ($m['evidence_path'] ?? ''));
                    $isLocked = $status === 'approved';
                    $appealNote = trim((string) ($m['appeal_note'] ?? ''));
                    $appealedAt = trim((string) ($m['appealed_at'] ?? ''));
                    $resubmissionCount = (int) ($m['resubmission_count'] ?? 0);
                    $basePoints = (int) ($m['base_hours'] ?? $m['hours'] ?? 0);
                    $bonusPoints = (int) ($m['achievement_bonus'] ?? 0);
                    $rankKey = trim((string) ($m['achievement_rank'] ?? ''));
                    $rankLabelMap = [
                        'first_prize' => '1st Prize',
                        'second_prize' => '2nd Prize',
                        'third_prize' => '3rd Prize',
                        'consolation' => 'Consolation',
                        'participant' => 'Participant / Certificate',
                    ];
                    $rankLabel = $rankLabelMap[$rankKey] ?? '';
                ?>
                <tr>
                    <td><?= htmlspecialchars((string) ($m['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($m['eventTitle'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($m['activityName']) ?></td>
                    <td>
                        <?= htmlspecialchars((string) ($m['hours'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                        <?php if ($bonusPoints > 0): ?>
                            <div class="muted" style="margin-top:4px;font-size:0.85rem;">
                                Base <?= (int) $basePoints ?> + Bonus <?= (int) $bonusPoints ?><?= $rankLabel !== '' ? ' (' . htmlspecialchars($rankLabel, ENT_QUOTES, 'UTF-8') . ')' : '' ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($dateFromDisplay !== '' ? $dateFromDisplay : '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($dateToDisplay !== '' ? $dateToDisplay : '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="status-badge <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?></span>
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
                        <?php if ($status === 'rejected'): ?>
                            <span class="status-badge rejected">Needs appeal</span>
                            <div style="margin-top:4px;">
                                <a class="link" href="index.php?url=merit/edit&id=<?= htmlspecialchars($m['meritID'], ENT_QUOTES, 'UTF-8') ?>">Appeal &amp; resubmit</a>
                            </div>
                        <?php elseif ($status === 'pending'): ?>
                            <span class="muted">Under review</span>
                            <?php if ($resubmissionCount > 0): ?>
                                <div class="muted" style="margin-top:4px;font-size:0.85rem;">Resubmitted <?= (int) $resubmissionCount ?> time(s)</div>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if ($resubmissionCount > 0): ?>
                                <div class="muted" style="font-size:0.85rem;">Resolved after <?= (int) $resubmissionCount ?> resubmission(s)</div>
                                <?php if ($appealedAt !== ''): ?>
                                    <div class="muted" style="font-size:0.85rem;">Last appeal: <?= htmlspecialchars($appealedAt, ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="muted">-</span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($appealNote !== ''): ?>
                            <div class="muted" style="margin-top:4px;font-size:0.85rem;">
                                Appeal note: <?= htmlspecialchars($appealNote, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($isLocked): ?>
                            <span class="muted">Locked after approval (admin only)</span>
                        <?php else: ?>
                            <a class="link" href="index.php?url=merit/edit&id=<?= htmlspecialchars($m['meritID'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= $status === 'rejected' ? 'Appeal &amp; Edit' : 'Edit' ?>
                            </a>
                            <span class="muted">|</span>
                            <form method="POST" action="index.php?url=merit/delete" style="display:inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($m['meritID'], ENT_QUOTES, 'UTF-8') ?>">
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
                <div class="muted" style="margin-top:8px;"><?= (int) round($totalHours) ?> hrs logged</div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Certificate Progress</h3>
                    <span class="chip">100h tiers</span>
                </div>
                <div class="milestone-value">Next: <?= (int) $nextCertificateMilestone ?> approved hours</div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: <?= (int) $certificateProgressPercent ?>%;"></div>
                </div>
                <div class="muted" style="margin-top:8px;">
                    <?= (int) $approvedHoursForCert ?> approved hour(s), <?= (int) $hoursToNextCertificate ?> hour(s) to unlock
                </div>
                <div style="margin-top:10px;">
                    <a class="link" href="index.php?url=certificate/myMerit">Open my certificates</a>
                </div>
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
                            <div class="list-item-sub">Needs correction and appeal</div>
                        </div>
                        <div class="list-item-right">
                            <?= (int) $rejectedCount ?>
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
            </div>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
