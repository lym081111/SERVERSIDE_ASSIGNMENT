<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<style>
    .timeline-summary-shell {
        background:
            radial-gradient(circle at 10% 12%, rgba(245, 158, 11, 0.08), transparent 40%),
            radial-gradient(circle at 90% 2%, rgba(30, 64, 175, 0.08), transparent 34%),
            #f3f5fb;
    }

    .timeline-summary-shell .page-header {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(255, 255, 255, 0.96) 65%);
        border: 1px solid rgba(245, 158, 11, 0.18);
        border-radius: 18px;
        padding: 20px 22px;
        margin-bottom: 16px;
    }

    .timeline-summary-shell .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .timeline-summary-shell .summary-card {
        background: linear-gradient(140deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.07);
        border: 1px solid rgba(191, 219, 254, 0.6);
    }

    .timeline-summary-shell .summary-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
    }

    .timeline-summary-shell .summary-value {
        font-size: 2rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .timeline-summary-shell .summary-sub {
        margin-top: 8px;
        font-size: 0.92rem;
        color: #64748b;
    }

    .timeline-summary-shell .detail-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        border: 1px solid rgba(191, 219, 254, 0.45);
        margin-top: 20px;
    }

    .timeline-summary-shell .level-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin-top: 14px;
    }

    .timeline-summary-shell .level-item {
        padding: 14px 16px;
        border-radius: 14px;
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        border: 1px solid #dbeafe;
    }

    .timeline-summary-shell .timeline-club-card {
        padding: 16px;
        border: 1px solid #dbeafe;
        border-radius: 16px;
        background: linear-gradient(150deg, #ffffff, #f8fbff);
        margin-bottom: 14px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
    }

    .timeline-summary-shell .timeline-club-card:first-child {
        padding-top: 16px;
    }

    .timeline-summary-shell .timeline-club-card:last-child {
        margin-bottom: 0;
    }

    .timeline-summary-shell .club-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    .timeline-summary-shell .club-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
    }

    .timeline-summary-shell .club-chip {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 700;
        background: #dbeafe;
        color: #1d4ed8;
    }

    .timeline-summary-shell .club-chip.completed {
        background: #e2e8f0;
        color: #334155;
    }

    .timeline-summary-shell .club-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 10px;
        color: #334155;
        font-size: 0.9rem;
        margin-bottom: 14px;
    }

    .timeline-summary-shell .club-meta > div {
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .timeline-summary-shell .club-meta strong {
        color: #0f172a;
    }

    .timeline-summary-shell .role-timeline-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 12px;
        position: relative;
    }

    .timeline-summary-shell .role-timeline-list::before {
        content: "";
        position: absolute;
        left: 13px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: linear-gradient(to bottom, #60a5fa, #cbd5e1);
        border-radius: 999px;
    }

    .timeline-summary-shell .role-timeline-item {
        position: relative;
        display: grid;
        grid-template-columns: 1fr auto;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 14px 12px 30px;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
    }

    .timeline-summary-shell .role-timeline-item::before {
        content: "";
        position: absolute;
        left: 8px;
        top: 20px;
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #94a3b8;
        box-shadow: 0 0 0 3px #f8fafc;
    }

    .timeline-summary-shell .role-timeline-item.status-current {
        border-color: #86efac;
        background: #f0fdf4;
    }

    .timeline-summary-shell .role-timeline-item.status-current::before {
        background: #22c55e;
    }

    .timeline-summary-shell .role-timeline-item.status-upcoming {
        border-color: #93c5fd;
        background: #eff6ff;
    }

    .timeline-summary-shell .role-timeline-item.status-upcoming::before {
        background: #3b82f6;
    }

    .timeline-summary-shell .role-timeline-item.status-past::before {
        background: #64748b;
    }

    .timeline-summary-shell .role-name {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .timeline-summary-shell .role-date {
        color: #475569;
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .timeline-summary-shell .role-status {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        border-radius: 999px;
        padding: 4px 10px;
        background: #e2e8f0;
    }

    .timeline-summary-shell .role-status.current {
        background: #bbf7d0;
        color: #166534;
    }

    .timeline-summary-shell .role-status.upcoming {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .timeline-summary-shell .role-status.past {
        background: #e2e8f0;
        color: #334155;
    }

    .timeline-summary-shell .timeline-empty {
        padding: 16px;
        border-radius: 12px;
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        color: #64748b;
    }

    @media (max-width: 820px) {
        .timeline-summary-shell .role-timeline-item {
            grid-template-columns: 1fr;
        }
        .timeline-summary-shell .role-status {
            width: fit-content;
        }
    }
</style>

<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Involvement Timeline</div>
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

    <div class="content timeline-summary-shell">
        <div class="content-inner">

            <?php
                $today = date('Y-m-d');
                $totalClubJoined = count($grouped);
                $currentLeadershipCount = 0;
                $longestClubName = '-';
                $longestClubDurationMonths = 0;
                $totalRoleEntries = 0;

                $roleCounts = [
                    'President' => 0,
                    'Vice President' => 0,
                    'Secretary' => 0,
                    'Treasurer' => 0,
                    'Member' => 0,
                ];

                $activeClubCount = 0;
                $preparedClubs = [];
                $formatDate = function ($rawDate, $fallback = '-') {
                    $value = trim((string) $rawDate);
                    if ($value === '' || $value === '0000-00-00' || strtotime($value) === false) {
                        return $fallback;
                    }
                    return date('d M Y', strtotime($value));
                };

                foreach ($grouped as $clubName => $records) {
                    $firstStart = null;
                    $latestRecordStart = null;
                    $latestRecordEnd = null;
                    $latestRelevantDate = null;
                    $isActive = false;

                    foreach ($records as $r) {
                        $startDate = trim((string) ($r['startDate'] ?? ''));
                        $endDate = trim((string) ($r['endDate'] ?? ''));

                        $startDate = ($startDate === '' || $startDate === '0000-00-00') ? null : $startDate;
                        $endDate = ($endDate === '' || $endDate === '0000-00-00') ? null : $endDate;

                        if ($startDate !== null && ($firstStart === null || $startDate < $firstStart)) {
                            $firstStart = $startDate;
                        }

                        $candidateDate = $endDate ?? $startDate;

                        if ($candidateDate !== null && ($latestRelevantDate === null || $candidateDate > $latestRelevantDate)) {
                            $latestRelevantDate = $candidateDate;
                            $latestRecordStart = $startDate;
                            $latestRecordEnd = $endDate;
                        }
                    }

                    if ($latestRecordStart !== null) {
                        if ($latestRecordEnd === null || $latestRecordEnd >= $today) {
                            $isActive = true;
                        }
                    }

                    if ($isActive) {
                        $activeClubCount++;
                    }

                    $durationText = '-';
                    $durationMonths = 0;
                    if ($firstStart !== null) {
                        try {
                            $startObj = new DateTime($firstStart);

                            if ($isActive) {
                                $endObj = new DateTime($today);
                            } else {
                                $endObj = new DateTime($latestRelevantDate ?? $firstStart);
                            }

                            $diff = $startObj->diff($endObj);
                            $durationMonths = max(1, ($diff->y * 12) + $diff->m + ($diff->d > 0 ? 1 : 0));

                            $parts = [];
                            if ($diff->y > 0) {
                                $parts[] = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
                            }
                            if ($diff->m > 0) {
                                $parts[] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
                            }
                            if (empty($parts)) {
                                $parts[] = 'Less than 1 month';
                            }

                            $durationText = implode(' ', $parts);
                        } catch (Exception $e) {
                            $durationText = '-';
                            $durationMonths = 0;
                        }
                    }

                    usort($records, function ($a, $b) {
                        $aStart = trim((string) ($a['startDate'] ?? ''));
                        $bStart = trim((string) ($b['startDate'] ?? ''));

                        if ($aStart === $bStart) {
                            $aEnd = trim((string) ($a['endDate'] ?? ''));
                            $bEnd = trim((string) ($b['endDate'] ?? ''));
                            return strcmp($aEnd, $bEnd);
                        }

                        return strcmp($aStart, $bStart);
                    });

                    foreach ($records as &$record) {
                        $role = trim((string) ($record['role'] ?? ''));
                        $role = $role === '' ? 'Member' : $role;
                        $record['displayRole'] = $role;
                    }
                    unset($record);

                    $latestRoleRecord = end($records);
                    $latestRole = 'Member';

                    if ($latestRoleRecord !== false) {
                        $latestRole = trim((string) ($latestRoleRecord['role'] ?? ''));
                        $latestRole = $latestRole === '' ? 'Member' : $latestRole;

                        $normalizedRole = strtolower(trim($latestRole));

                        if ($normalizedRole === 'president') {
                            $roleCounts['President']++;
                        } elseif (
                            $normalizedRole === 'vice president' ||
                            $normalizedRole === 'vice-president' ||
                            $normalizedRole === 'vicepresident'
                        ) {
                            $roleCounts['Vice President']++;
                        } elseif ($normalizedRole === 'secretary') {
                            $roleCounts['Secretary']++;
                        } elseif ($normalizedRole === 'treasurer') {
                            $roleCounts['Treasurer']++;
                        } else {
                            $roleCounts['Member']++;
                        }

                        if (
                            $isActive
                            && $normalizedRole !== 'member'
                        ) {
                            $currentLeadershipCount++;
                        }
                    }

                    reset($records);
                    $totalRoleEntries += count($records);

                    if ($durationMonths > $longestClubDurationMonths) {
                        $longestClubDurationMonths = $durationMonths;
                        $longestClubName = $clubName;
                    }

                    $preparedClubs[] = [
                        'clubName' => $clubName,
                        'records' => $records,
                        'firstStart' => $firstStart,
                        'firstStartLabel' => $formatDate($firstStart),
                        'durationText' => $durationText,
                        'durationMonths' => $durationMonths,
                        'isActive' => $isActive,
                        'latestRole' => $latestRole,
                        'roleEntryCount' => count($records),
                    ];
                }

                usort($preparedClubs, function ($a, $b) {
                    if ((bool) $a['isActive'] !== (bool) $b['isActive']) {
                        return $a['isActive'] ? -1 : 1;
                    }
                    $aDate = trim((string) ($a['firstStart'] ?? ''));
                    $bDate = trim((string) ($b['firstStart'] ?? ''));
                    return strcmp($bDate, $aDate);
                });

                if ($activeClubCount <= 0) {
                    $clubActiveMessage = 'No active club membership at the moment.';
                } elseif ($activeClubCount === 1) {
                    $clubActiveMessage = 'You currently have 1 active club membership.';
                } else {
                    $clubActiveMessage = 'You currently have ' . $activeClubCount . ' active club memberships.';
                }

                $averageRolesPerClub = $totalClubJoined > 0
                    ? round($totalRoleEntries / $totalClubJoined, 1)
                    : 0;
            ?>

            <div class="page-header">
                <div>
                    <h1 class="page-title">Involvement Timeline</h1>
                    <p class="page-subtitle">Track your club journey from first join date to latest role progression.</p>
                </div>
                <div class="page-actions">
                    <a class="btn btn-secondary" href="index.php?url=dashboard/index">Back to Dashboard</a>
                    <a class="btn" href="index.php?url=club/index">Go to Club Module</a>
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-label">Total Clubs Joined</div>
                    <div class="summary-value"><?= (int) $totalClubJoined ?></div>
                    <div class="summary-sub">Approved clubs only</div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Active Clubs</div>
                    <div class="summary-value"><?= (int)$activeClubCount ?></div>
                    <div class="summary-sub"><?= htmlspecialchars($clubActiveMessage, ENT_QUOTES, 'UTF-8') ?></div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Current Leadership Roles</div>
                    <div class="summary-value"><?= (int) $currentLeadershipCount ?></div>
                    <div class="summary-sub">Active clubs where your latest role is above Member</div>
                </div>

                <div class="summary-card">
                    <div class="summary-label">Longest Involvement</div>
                    <div class="summary-value" style="font-size:1.6rem;"><?= (int) $longestClubDurationMonths ?> month(s)</div>
                    <div class="summary-sub"><?= htmlspecialchars($longestClubName, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </div>

            <div class="detail-card">
                <h3 style="margin-top:0; margin-bottom: 10px;">Role Distribution Snapshot</h3>
                <p class="muted" style="margin:0 0 6px;">Average role transitions per club: <strong><?= htmlspecialchars((string) $averageRolesPerClub, ENT_QUOTES, 'UTF-8') ?></strong></p>

                <div class="level-list">
                    <div class="level-item">
                        <div class="summary-label">President</div>
                        <div class="summary-value" style="font-size:1.6rem;"><?= (int) $roleCounts['President'] ?></div>
                    </div>

                    <div class="level-item">
                        <div class="summary-label">Vice President</div>
                        <div class="summary-value" style="font-size:1.6rem;"><?= (int) $roleCounts['Vice President'] ?></div>
                    </div>

                    <div class="level-item">
                        <div class="summary-label">Secretary</div>
                        <div class="summary-value" style="font-size:1.6rem;"><?= (int) $roleCounts['Secretary'] ?></div>
                    </div>

                    <div class="level-item">
                        <div class="summary-label">Treasurer</div>
                        <div class="summary-value" style="font-size:1.6rem;"><?= (int) $roleCounts['Treasurer'] ?></div>
                    </div>

                    <div class="level-item">
                        <div class="summary-label">Member</div>
                        <div class="summary-value" style="font-size:1.6rem;"><?= (int) $roleCounts['Member'] ?></div>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <h3 style="margin-top:0; margin-bottom: 10px;">Detailed Club Journey</h3>
                <p class="muted" style="margin:0 0 10px;">Each card shows your timeline progression, tenure, and latest role for that club.</p>

                <?php if (empty($preparedClubs)): ?>
                    <div class="timeline-empty">No approved club timeline records found.</div>
                <?php else: ?>
                    <?php foreach ($preparedClubs as $index => $clubData): ?>
                        <div class="timeline-club-card">
                            <div class="club-title-row">
                                <div class="club-title"><?= (int) ($index + 1) ?>. <?= htmlspecialchars($clubData['clubName'], ENT_QUOTES, 'UTF-8') ?></div>
                                <span class="club-chip <?= $clubData['isActive'] ? '' : 'completed' ?>">
                                    <?= $clubData['isActive'] ? 'Active' : 'Completed' ?>
                                </span>
                            </div>

                            <div class="club-meta">
                                <div><strong>First Joined:</strong> <?= htmlspecialchars($clubData['firstStartLabel'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                                <div><strong>Latest Role:</strong> <?= htmlspecialchars($clubData['latestRole'] ?? 'Member', ENT_QUOTES, 'UTF-8') ?></div>
                                <div><strong>Total Duration:</strong> <?= htmlspecialchars($clubData['durationText'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div><strong>Role Entries:</strong> <?= (int) ($clubData['roleEntryCount'] ?? 0) ?> stage(s)</div>
                            </div>

                            <ul class="role-timeline-list">
                                <?php foreach ($clubData['records'] as $r): ?>
                                    <?php
                                        $startDateRaw = trim((string) ($r['startDate'] ?? ''));
                                        $endDateRaw = trim((string) ($r['endDate'] ?? ''));
                                        $startDate = ($startDateRaw === '' || $startDateRaw === '0000-00-00') ? '-' : $startDateRaw;
                                        $startDateDisplay = $formatDate($startDateRaw);

                                        $role = trim((string) ($r['displayRole'] ?? 'Member'));
                                        $roleDescription = trim((string) ($r['roleDescription'] ?? ''));

                                        if ($endDateRaw === '' || $endDateRaw === '0000-00-00') {
                                            if ($startDate !== '-' && $startDate > $today) {
                                                $timelineStatus = 'Upcoming';
                                                $endDateDisplay = 'Present';
                                            } else {
                                                $timelineStatus = 'Current';
                                                $endDateDisplay = 'Present';
                                            }
                                        } else {
                                            $endDateDisplay = $formatDate($endDateRaw);

                                            if ($startDate !== '-' && $startDate > $today) {
                                                $timelineStatus = 'Upcoming';
                                            } elseif ($endDateRaw < $today) {
                                                $timelineStatus = 'Past';
                                            } elseif ($startDate <= $today && $endDateRaw >= $today) {
                                                $timelineStatus = 'Current';
                                            } else {
                                                $timelineStatus = 'Past';
                                            }
                                        }

                                        $statusClass = strtolower($timelineStatus);
                                    ?>
                                    <li class="role-timeline-item status-<?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>">
                                        <div>
                                            <div class="role-name"><?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="role-date">
                                                <?= htmlspecialchars($startDateDisplay . ' to ' . $endDateDisplay, ENT_QUOTES, 'UTF-8') ?>
                                                <?php if ($roleDescription !== ''): ?>
                                                    <br><?= htmlspecialchars($roleDescription, ENT_QUOTES, 'UTF-8') ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="role-status <?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($timelineStatus, ENT_QUOTES, 'UTF-8') ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
