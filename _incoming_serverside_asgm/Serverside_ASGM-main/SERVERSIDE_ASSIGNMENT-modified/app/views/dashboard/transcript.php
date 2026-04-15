<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>
<?php
    $logoUrl = BASE_URL . 'assets/utar_logo.svg';
    $issuedLabel = date('d F Y, h:i A', strtotime((string) $issuedAt));
    $registrationDate = !empty($student['created_at']) && strtotime((string) $student['created_at']) !== false
        ? date('d F Y', strtotime((string) $student['created_at']))
        : '-';
    $periodFromLabel = ($activityPeriodFrom !== null && strtotime((string) $activityPeriodFrom) !== false)
        ? date('d M Y', strtotime((string) $activityPeriodFrom))
        : '-';
    $periodToLabel = ($activityPeriodTo !== null && strtotime((string) $activityPeriodTo) !== false)
        ? date('d M Y', strtotime((string) $activityPeriodTo))
        : '-';

    $displayDate = function ($value, $fallback = '-') {
        $dateValue = trim((string) $value);
        if ($dateValue === '' || $dateValue === '0000-00-00' || strtotime($dateValue) === false) {
            return $fallback;
        }
        return date('d M Y', strtotime($dateValue));
    };

    $clubFlow = [];

    $ensureClubNode = function ($clubName) use (&$clubFlow) {
        $resolvedName = trim((string) $clubName);
        if ($resolvedName === '') {
            $resolvedName = 'Independent Activities';
        }

        $clubKey = strtolower($resolvedName);
        if (!isset($clubFlow[$clubKey])) {
            $clubFlow[$clubKey] = [
                'clubName' => $resolvedName,
                'memberships' => [],
                'events' => [],
                'unlinkedMerits' => [],
                'unlinkedAchievements' => [],
            ];
        }

        return $clubKey;
    };

    foreach ($approvedClubs as $row) {
        $clubKey = $ensureClubNode($row['clubName'] ?? '');
        $membership = [
            'role' => trim((string) ($row['role'] ?? 'Member')) ?: 'Member',
            'startDate' => trim((string) ($row['startDate'] ?? '')),
            'endDate' => trim((string) ($row['endDate'] ?? '')),
        ];

        $membershipKey = strtolower($membership['role'] . '|' . $membership['startDate'] . '|' . $membership['endDate']);
        $clubFlow[$clubKey]['memberships'][$membershipKey] = $membership;
    }

    $eventLookup = [];
    foreach ($approvedEvents as $row) {
        $clubKey = $ensureClubNode($row['clubName'] ?? '');
        $eventID = (int) ($row['eventID'] ?? 0);
        $eventKey = $eventID > 0
            ? 'event-' . $eventID
            : 'event-free-' . md5((string) (($row['eventTitle'] ?? '') . '|' . ($row['eventDate'] ?? '') . '|' . ($row['clubName'] ?? '')));

        if (!isset($clubFlow[$clubKey]['events'][$eventKey])) {
            $clubFlow[$clubKey]['events'][$eventKey] = [
                'eventID' => $eventID,
                'eventTitle' => trim((string) ($row['eventTitle'] ?? '')) ?: 'Untitled Event',
                'eventType' => trim((string) ($row['eventType'] ?? '')),
                'eventDate' => trim((string) ($row['eventDate'] ?? '')),
                'eventHours' => (float) ($row['eventHours'] ?? 0),
                'location' => trim((string) ($row['location'] ?? '')),
                'merits' => [],
                'achievements' => [],
            ];
        }

        if ($eventID > 0) {
            $eventLookup[$eventID] = ['clubKey' => $clubKey, 'eventKey' => $eventKey];
        }
    }

    foreach ($approvedMerits as $row) {
        $eventID = (int) ($row['eventID'] ?? 0);
        $meritEntry = [
            'activityName' => trim((string) ($row['activityName'] ?? '')) ?: 'Merit Activity',
            'hours' => (float) ($row['hours'] ?? 0),
            'dateFrom' => trim((string) ($row['dateFrom'] ?? '')),
            'dateTo' => trim((string) ($row['dateTo'] ?? '')),
        ];

        if ($eventID > 0 && isset($eventLookup[$eventID])) {
            $clubKey = $eventLookup[$eventID]['clubKey'];
            $eventKey = $eventLookup[$eventID]['eventKey'];
            $clubFlow[$clubKey]['events'][$eventKey]['merits'][] = $meritEntry;
            continue;
        }

        $clubKey = $ensureClubNode($row['clubName'] ?? '');
        $clubFlow[$clubKey]['unlinkedMerits'][] = $meritEntry;
    }

    foreach ($approvedAchievements as $row) {
        $eventID = (int) ($row['eventID'] ?? 0);
        $achievementEntry = [
            'title' => trim((string) ($row['title'] ?? '')) ?: 'Achievement',
            'category' => trim((string) ($row['category'] ?? '')),
            'level' => trim((string) ($row['achievementLevel'] ?? '')),
            'dateReceived' => trim((string) ($row['dateReceived'] ?? '')),
            'description' => trim((string) ($row['description'] ?? '')),
        ];

        if ($eventID > 0 && isset($eventLookup[$eventID])) {
            $clubKey = $eventLookup[$eventID]['clubKey'];
            $eventKey = $eventLookup[$eventID]['eventKey'];
            $clubFlow[$clubKey]['events'][$eventKey]['achievements'][] = $achievementEntry;
            continue;
        }

        $clubKey = $ensureClubNode($row['clubName'] ?? '');
        $clubFlow[$clubKey]['unlinkedAchievements'][] = $achievementEntry;
    }

    if (!empty($clubFlow)) {
        uasort($clubFlow, function ($a, $b) {
            if ($a['clubName'] === 'Independent Activities') {
                return 1;
            }
            if ($b['clubName'] === 'Independent Activities') {
                return -1;
            }
            return strcasecmp((string) $a['clubName'], (string) $b['clubName']);
        });
    }
?>

<style>
    .transcript-stage {
        max-width: 1180px;
        margin: 0 auto;
    }

    .transcript-paper {
        background: #fffefa;
        border: 3px solid #102a6a;
        border-radius: 14px;
        box-shadow: 0 24px 45px rgba(15, 23, 42, 0.14);
        padding: 26px 28px 30px;
    }

    .transcript-head {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 20px;
        align-items: center;
        border-bottom: 2px solid rgba(16, 42, 106, 0.25);
        padding-bottom: 16px;
        margin-bottom: 14px;
    }

    .transcript-logo {
        width: 320px;
        max-width: 100%;
        border: 1px solid rgba(15, 23, 42, 0.12);
        border-radius: 8px;
        background: #fff;
    }

    .transcript-title-wrap h1 {
        margin: 0;
        font-family: "Times New Roman", Georgia, serif;
        font-size: 2.05rem;
        color: #0f2a7a;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .transcript-title-wrap p {
        margin: 5px 0 0;
        color: #334155;
        line-height: 1.45;
    }

    .transcript-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .transcript-chip {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 999px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #0f172a;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .transcript-info {
        margin-top: 14px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .transcript-info-card {
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        background: #ffffff;
        padding: 10px 12px;
    }

    .transcript-info-label {
        margin: 0;
        font-size: 0.8rem;
        color: #475569;
    }

    .transcript-info-value {
        margin: 5px 0 0;
        color: #0f172a;
        font-weight: 800;
    }

    .transcript-summary {
        margin-top: 14px;
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 10px;
    }

    .transcript-summary-card {
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        background: #f8fbff;
        padding: 10px 12px;
    }

    .transcript-summary-card h3 {
        margin: 0;
        font-size: 0.84rem;
        color: #334155;
    }

    .transcript-summary-card p {
        margin: 6px 0 0;
        font-size: 1.2rem;
        font-weight: 800;
        color: #102a6a;
    }

    .transcript-section {
        margin-top: 16px;
    }

    .transcript-section h2 {
        margin: 0 0 8px;
        font-size: 1.1rem;
        color: #0f172a;
    }

    .transcript-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        overflow: hidden;
        background: #ffffff;
    }

    .transcript-table th,
    .transcript-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
        text-align: left;
        font-size: 0.93rem;
    }

    .transcript-table th {
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 800;
    }

    .transcript-table tr:last-child td {
        border-bottom: none;
    }

    .transcript-flow {
        margin-top: 14px;
        display: grid;
        gap: 12px;
    }

    .club-flow-card {
        border: 1px solid #dbe3ee;
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
    }

    .club-flow-head {
        padding: 10px 12px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fbff;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .club-flow-name {
        margin: 0;
        font-size: 1rem;
        color: #0f172a;
    }

    .club-flow-meta {
        margin: 4px 0 0;
        color: #334155;
        font-size: 0.86rem;
    }

    .club-flow-body {
        padding: 12px;
        display: grid;
        gap: 10px;
    }

    .event-flow-card {
        border: 1px solid #e2e8f0;
        border-left: 4px solid #1d4ed8;
        border-radius: 10px;
        padding: 10px 11px;
        background: #fcfdff;
    }

    .event-flow-title {
        margin: 0;
        font-size: 0.95rem;
        color: #0f172a;
    }

    .event-flow-meta {
        margin-top: 4px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .event-flow-chip {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        border: 1px solid #cbd5e1;
        padding: 2px 8px;
        font-size: 0.78rem;
        color: #334155;
        background: #ffffff;
    }

    .flow-subsection {
        margin-top: 8px;
    }

    .flow-subtitle {
        margin: 0 0 4px;
        font-size: 0.82rem;
        font-weight: 800;
        color: #0f172a;
    }

    .flow-list {
        margin: 0;
        padding-left: 18px;
        display: grid;
        gap: 3px;
        color: #1e293b;
        font-size: 0.86rem;
    }

    .flow-empty {
        margin: 0;
        color: #64748b;
        font-size: 0.84rem;
    }

    .transcript-footer {
        margin-top: 18px;
        border-top: 2px solid rgba(16, 42, 106, 0.22);
        padding-top: 14px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 28px;
    }

    .transcript-sign-box {
        text-align: center;
    }

    .transcript-sign-line {
        height: 1px;
        background: #0f172a;
        margin-bottom: 8px;
    }

    .transcript-note {
        margin-top: 12px;
        font-size: 0.86rem;
        color: #475569;
        line-height: 1.5;
    }

    @media (max-width: 980px) {
        .transcript-head {
            grid-template-columns: 1fr;
        }
        .transcript-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .transcript-info {
            grid-template-columns: 1fr;
        }
        .transcript-footer {
            grid-template-columns: 1fr;
        }
    }

    @media print {
        .topbar,
        .sidebar {
            display: none !important;
        }

        .main {
            margin-left: 0 !important;
        }

        .content {
            padding: 0 !important;
        }

        .transcript-paper {
            box-shadow: none !important;
            border-width: 2px;
            border-radius: 0;
        }
    }
</style>

<div class="main">
    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Official Co-Curricular Transcript</div>
            <div class="topbar-user-inline"><?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-secondary" type="button" onclick="window.print()">Print Transcript</button>
            <a class="btn btn-secondary" href="index.php?url=dashboard/index">Back to Dashboard</a>
        </div>
    </div>

    <div class="content">
        <div class="content-inner transcript-stage">
            <article class="transcript-paper">
                <header class="transcript-head">
                    <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="UTAR Logo" class="transcript-logo">
                    <div class="transcript-title-wrap">
                        <h1>Official Co-Curricular Transcript</h1>
                        <p>Universiti Tunku Abdul Rahman</p>
                        <p>Division of Student Affairs and Co-Curricular Development</p>
                        <div class="transcript-badges">
                            <span class="transcript-chip">Transcript No: <?= htmlspecialchars($transcriptNumber, ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="transcript-chip">Issued: <?= htmlspecialchars($issuedLabel, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if (isset($approvedAchievements) && count($approvedAchievements) > 0): ?>
                                <span title="Star of Achievements" style="color: #f59e0b; font-size: 1.8rem; margin-left: 6px; display: inline-block; vertical-align: middle;">
                                    &#9733;
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </header>

                <section class="transcript-info">
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Student Name</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($student['name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Student ID</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($student['student_id'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Email</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($student['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Registration Date</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($registrationDate, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Activity Period (From)</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($periodFromLabel, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Activity Period (To)</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($periodToLabel, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </section>

                <section class="transcript-summary">
                    <div class="transcript-summary-card"><h3>Approved Merits</h3><p><?= (int) ($summary['merits'] ?? 0) ?></p></div>
                    <div class="transcript-summary-card"><h3>Approved Events</h3><p><?= (int) ($summary['events'] ?? 0) ?></p></div>
                    <div class="transcript-summary-card"><h3>Approved Clubs</h3><p><?= (int) ($summary['clubs'] ?? 0) ?></p></div>
                    <div class="transcript-summary-card"><h3>Approved Achievements</h3><p><?= (int) ($summary['achievements'] ?? 0) ?></p></div>
                    <div class="transcript-summary-card"><h3>Approved Merit Hours</h3><p><?= (int) round((float) ($summary['merit_hours'] ?? 0)) ?></p></div>
                </section>

                <section class="transcript-section">
                    <h2>Co-Curricular Activity Flow (Approved)</h2>
                    <div class="transcript-flow">
                        <?php if (empty($clubFlow)): ?>
                            <p class="flow-empty">No approved co-curricular records available.</p>
                        <?php else: ?>
                            <?php foreach ($clubFlow as $clubData): ?>
                                <?php
                                    $membershipRows = array_values($clubData['memberships']);
                                    usort($membershipRows, function ($a, $b) {
                                        return strcmp((string) ($a['startDate'] ?? ''), (string) ($b['startDate'] ?? ''));
                                    });

                                    $eventRows = array_values($clubData['events']);
                                    usort($eventRows, function ($a, $b) {
                                        $dateA = trim((string) ($a['eventDate'] ?? ''));
                                        $dateB = trim((string) ($b['eventDate'] ?? ''));
                                        $dateA = ($dateA === '' || $dateA === '0000-00-00') ? '0000-00-00' : $dateA;
                                        $dateB = ($dateB === '' || $dateB === '0000-00-00') ? '0000-00-00' : $dateB;

                                        if ($dateA === $dateB) {
                                            return strcasecmp((string) ($a['eventTitle'] ?? ''), (string) ($b['eventTitle'] ?? ''));
                                        }
                                        return strcmp($dateB, $dateA);
                                    });

                                    $totalMeritsInClub = 0;
                                    $totalAchievementsInClub = 0;
                                    foreach ($eventRows as $eventRow) {
                                        $totalMeritsInClub += count($eventRow['merits']);
                                        $totalAchievementsInClub += count($eventRow['achievements']);
                                    }
                                    $totalMeritsInClub += count($clubData['unlinkedMerits']);
                                    $totalAchievementsInClub += count($clubData['unlinkedAchievements']);
                                ?>
                                <article class="club-flow-card">
                                    <header class="club-flow-head">
                                        <div>
                                            <h3 class="club-flow-name"><?= htmlspecialchars($clubData['clubName'], ENT_QUOTES, 'UTF-8') ?></h3>
                                            <?php if (!empty($membershipRows)): ?>
                                                <p class="club-flow-meta">
                                                    Membership timeline:
                                                    <?php
                                                        $membershipTexts = [];
                                                        foreach ($membershipRows as $membership) {
                                                            $membershipTexts[] = ($membership['role'] ?? 'Member')
                                                                . ' (' . $displayDate($membership['startDate'] ?? null)
                                                                . ' to ' . $displayDate($membership['endDate'] ?? null, 'Present') . ')';
                                                        }
                                                        echo htmlspecialchars(implode('; ', $membershipTexts), ENT_QUOTES, 'UTF-8');
                                                    ?>
                                                </p>
                                            <?php else: ?>
                                                <p class="club-flow-meta">No direct membership timeline recorded for this club.</p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="transcript-badges">
                                            <span class="transcript-chip"><?= count($eventRows) ?> event(s)</span>
                                            <span class="transcript-chip"><?= (int) $totalMeritsInClub ?> merit record(s)</span>
                                            <span class="transcript-chip"><?= (int) $totalAchievementsInClub ?> achievement(s)</span>
                                        </div>
                                    </header>
                                    <div class="club-flow-body">
                                        <?php if (empty($eventRows)): ?>
                                            <p class="flow-empty">No approved events linked under this club yet.</p>
                                        <?php else: ?>
                                            <?php foreach ($eventRows as $eventRow): ?>
                                                <?php
                                                    $eventHours = (float) ($eventRow['eventHours'] ?? 0);
                                                    $eventHoursText = fmod($eventHours, 1.0) === 0.0
                                                        ? (string) ((int) $eventHours)
                                                        : number_format($eventHours, 1);
                                                ?>
                                                <div class="event-flow-card">
                                                    <h4 class="event-flow-title">
                                                        <?= htmlspecialchars($displayDate($eventRow['eventDate'] ?? null), ENT_QUOTES, 'UTF-8') ?>
                                                        - <?= htmlspecialchars($eventRow['eventTitle'] ?? 'Untitled Event', ENT_QUOTES, 'UTF-8') ?>
                                                    </h4>
                                                    <div class="event-flow-meta">
                                                        <span class="event-flow-chip">Type: <?= htmlspecialchars(($eventRow['eventType'] ?? '') !== '' ? (string) $eventRow['eventType'] : '-', ENT_QUOTES, 'UTF-8') ?></span>
                                                        <span class="event-flow-chip">Event Merit Hours: <?= htmlspecialchars($eventHoursText, ENT_QUOTES, 'UTF-8') ?>h</span>
                                                        <span class="event-flow-chip">Location: <?= htmlspecialchars(($eventRow['location'] ?? '') !== '' ? (string) $eventRow['location'] : '-', ENT_QUOTES, 'UTF-8') ?></span>
                                                    </div>

                                                    <div class="flow-subsection">
                                                        <p class="flow-subtitle">Merit Submission(s)</p>
                                                        <?php if (empty($eventRow['merits'])): ?>
                                                            <p class="flow-empty">No approved merit submission linked to this event.</p>
                                                        <?php else: ?>
                                                            <ul class="flow-list">
                                                                <?php foreach ($eventRow['merits'] as $meritRow): ?>
                                                                    <?php
                                                                        $meritHours = (float) ($meritRow['hours'] ?? 0);
                                                                        $meritHoursText = fmod($meritHours, 1.0) === 0.0
                                                                            ? (string) ((int) $meritHours)
                                                                            : number_format($meritHours, 1);
                                                                    ?>
                                                                    <li>
                                                                        <?= htmlspecialchars($meritRow['activityName'] ?? 'Merit Activity', ENT_QUOTES, 'UTF-8') ?>
                                                                        (<?= htmlspecialchars($meritHoursText, ENT_QUOTES, 'UTF-8') ?>h)
                                                                        - <?= htmlspecialchars($displayDate($meritRow['dateFrom'] ?? null), ENT_QUOTES, 'UTF-8') ?>
                                                                        to <?= htmlspecialchars($displayDate($meritRow['dateTo'] ?? null), ENT_QUOTES, 'UTF-8') ?>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="flow-subsection">
                                                        <p class="flow-subtitle">Achievement(s)</p>
                                                        <?php if (empty($eventRow['achievements'])): ?>
                                                            <p class="flow-empty">No approved achievement linked to this event.</p>
                                                        <?php else: ?>
                                                            <ul class="flow-list">
                                                                <?php foreach ($eventRow['achievements'] as $achievementRow): ?>
                                                                    <li>
                                                                        <?= htmlspecialchars($achievementRow['title'] ?? 'Achievement', ENT_QUOTES, 'UTF-8') ?>
                                                                        [<?= htmlspecialchars(($achievementRow['level'] ?? '') !== '' ? (string) $achievementRow['level'] : '-', ENT_QUOTES, 'UTF-8') ?>]
                                                                        - <?= htmlspecialchars(($achievementRow['category'] ?? '') !== '' ? (string) $achievementRow['category'] : '-', ENT_QUOTES, 'UTF-8') ?>
                                                                        - <?= htmlspecialchars($displayDate($achievementRow['dateReceived'] ?? null), ENT_QUOTES, 'UTF-8') ?>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <?php if (!empty($clubData['unlinkedMerits'])): ?>
                                            <div class="flow-subsection">
                                                <p class="flow-subtitle">Unlinked Merit Submission(s)</p>
                                                <ul class="flow-list">
                                                    <?php foreach ($clubData['unlinkedMerits'] as $meritRow): ?>
                                                        <?php
                                                            $meritHours = (float) ($meritRow['hours'] ?? 0);
                                                            $meritHoursText = fmod($meritHours, 1.0) === 0.0
                                                                ? (string) ((int) $meritHours)
                                                                : number_format($meritHours, 1);
                                                        ?>
                                                        <li>
                                                            <?= htmlspecialchars($meritRow['activityName'] ?? 'Merit Activity', ENT_QUOTES, 'UTF-8') ?>
                                                            (<?= htmlspecialchars($meritHoursText, ENT_QUOTES, 'UTF-8') ?>h)
                                                            - <?= htmlspecialchars($displayDate($meritRow['dateFrom'] ?? null), ENT_QUOTES, 'UTF-8') ?>
                                                            to <?= htmlspecialchars($displayDate($meritRow['dateTo'] ?? null), ENT_QUOTES, 'UTF-8') ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($clubData['unlinkedAchievements'])): ?>
                                            <div class="flow-subsection">
                                                <p class="flow-subtitle">Unlinked Achievement(s)</p>
                                                <ul class="flow-list">
                                                    <?php foreach ($clubData['unlinkedAchievements'] as $achievementRow): ?>
                                                        <li>
                                                            <?= htmlspecialchars($achievementRow['title'] ?? 'Achievement', ENT_QUOTES, 'UTF-8') ?>
                                                            [<?= htmlspecialchars(($achievementRow['level'] ?? '') !== '' ? (string) $achievementRow['level'] : '-', ENT_QUOTES, 'UTF-8') ?>]
                                                            - <?= htmlspecialchars(($achievementRow['category'] ?? '') !== '' ? (string) $achievementRow['category'] : '-', ENT_QUOTES, 'UTF-8') ?>
                                                            - <?= htmlspecialchars($displayDate($achievementRow['dateReceived'] ?? null), ENT_QUOTES, 'UTF-8') ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="transcript-footer">
                    <div class="transcript-sign-box">
                        <div class="transcript-sign-line"></div>
                        <strong>Director</strong>
                        <div class="muted">Division of Student Affairs</div>
                    </div>
                    <div class="transcript-sign-box">
                        <div class="transcript-sign-line"></div>
                        <strong>Registrar</strong>
                        <div class="muted">Universiti Tunku Abdul Rahman</div>
                    </div>
                </section>

                <div class="transcript-note">
                    This transcript is system-generated and reflects approved co-curricular records as of the issue timestamp above.
                    Unapproved (pending/rejected) submissions are excluded from this official summary.
                </div>
            </article>
        </div>
    </div>
</div>

<?php require "../app/views/layout/footer.php"; ?>
