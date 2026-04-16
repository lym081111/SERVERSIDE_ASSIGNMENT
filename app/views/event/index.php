<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main module-page">

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Event Tracker Module</div>
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
        $totalRecords = is_array($events) ? count($events) : 0;
        $latestDate = null;
        $locations = [];
        $recentCount = 0;
        $approvedCount = 0;
        $pendingCount = 0;
        $rejectedCount = 0;
        $eventTypes = [];
        $threshold = date('Y-m-d', strtotime('-30 days'));

        if (is_array($events)) {
            foreach ($events as $row) {
                $eventDate = trim((string) ($row['eventDate'] ?? ''));
                $eventDate = ($eventDate === '' || $eventDate === '0000-00-00') ? '' : $eventDate;
                if ($eventDate !== '') {
                    if ($latestDate === null || strcmp((string) $eventDate, (string) $latestDate) > 0) {
                        $latestDate = (string) $eventDate;
                    }
                    if ((string) $eventDate >= $threshold) {
                        $recentCount++;
                    }
                }

                $eventType = trim((string) ($row['eventType'] ?? ''));
                if ($eventType !== '') {
                    $eventTypes[$eventType] = ($eventTypes[$eventType] ?? 0) + 1;
                }

                $location = trim((string) ($row['location'] ?? ''));
                if ($location !== '') {
                    $locations[$location] = true;
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

        $locationCount = count($locations);
        $eventTypeCount = count($eventTypes);
        arsort($eventTypes);
        $topEventTypes = array_slice($eventTypes, 0, 4, true);

        $milestones = [3, 5, 10, 20];
        $nextMilestone = null;
        foreach ($milestones as $goal) {
            if ($totalRecords < $goal) {
                $nextMilestone = $goal;
                break;
            }
        }
        $milestoneLabel = $nextMilestone ? $nextMilestone . " events" : "Goal complete";
        $milestoneProgress = $nextMilestone ? min(100, (int) round(($totalRecords / $nextMilestone) * 100)) : 100;
    ?>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">Total Events</div>
            <div class="kpi-value"><?= (int) $totalRecords ?></div>
            <div class="kpi-sub">All records</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Latest Event</div>
            <div class="kpi-value"><?= htmlspecialchars($latestDate ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
            <div class="kpi-sub">Most recent date</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Recent Entries</div>
            <div class="kpi-value"><?= (int) $recentCount ?></div>
            <div class="kpi-sub">Last 30 days</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Locations Used</div>
            <div class="kpi-value"><?= (int) $locationCount ?></div>
            <div class="kpi-sub">Unique venues</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Event Types</div>
            <div class="kpi-value"><?= (int) $eventTypeCount ?></div>
            <div class="kpi-sub">Different activity categories</div>
        </div>
    </div>

    <div class="page-header">
        <div>
            <h2 style="margin:0;">My Event Records</h2>
            <div class="muted" style="margin-top:6px;">Track your campus event participation below.</div>
        </div>
        <div class="page-actions">
            <a href="index.php?url=event/create" class="btn">Advanced Add Event</a>
            <a href="index.php?url=event/exportSelf" class="btn btn-secondary no-print">Export my CSV</a>
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

    <div class="card" style="margin-bottom:16px;">
        <div class="card-header">
            <h3 class="card-title">Available Events Created By Members</h3>
            <span class="chip"><?= is_array($availableEvents ?? null) ? (int) count($availableEvents) : 0 ?> available</span>
        </div>

        <?php if (empty($availableEvents)): ?>
            <div class="muted">No joinable events yet. Once someone creates an event in your approved clubs, it appears here.</div>
        <?php else: ?>
            <ul class="list">
                <?php foreach ($availableEvents as $availableEvent): ?>
                    <?php
                        $joinState = (string) ($availableEvent['joinState'] ?? 'can_join');
                        $joinMessage = (string) ($availableEvent['joinMessage'] ?? 'Seats available');
                        $badgeClass = 'warn';
                        $badgeLabel = 'Open';
                        if ($joinState === 'pending') {
                            $badgeClass = 'pending';
                            $badgeLabel = 'Pending';
                        } elseif ($joinState === 'joined') {
                            $badgeClass = 'approved';
                            $badgeLabel = 'Joined';
                        } elseif ($joinState === 'setup_required') {
                            $badgeClass = 'rejected';
                            $badgeLabel = 'Setup Needed';
                        } elseif ($joinState === 'full') {
                            $badgeClass = 'rejected';
                            $badgeLabel = 'Full';
                        } elseif ($joinState === 'waitlist') {
                            $badgeClass = 'pending';
                            $badgeLabel = 'Waitlist';
                        }

                        $capacity = (int) ($availableEvent['participantCapacity'] ?? 0);
                        $registered = (int) ($availableEvent['registeredCount'] ?? 0);
                        $seatSummary = $capacity > 0 ? ($registered . '/' . $capacity) : 'Not set';
                        $eventType = trim((string) ($availableEvent['eventType'] ?? 'General'));
                        $location = trim((string) ($availableEvent['location'] ?? ''));
                        $description = trim((string) ($availableEvent['description'] ?? ''));
                    ?>
                    <li class="list-item" style="align-items:center;">
                        <div style="min-width:0;">
                            <div class="list-item-title" style="max-width:none;white-space:normal;">
                                <?= htmlspecialchars((string) ($availableEvent['eventTitle'] ?? 'Event'), ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="list-item-sub">
                                <?= htmlspecialchars((string) ($availableEvent['clubName'] ?? 'Unknown club'), ENT_QUOTES, 'UTF-8') ?>
                                <?= htmlspecialchars(' | ' . ((string) ($availableEvent['eventDate'] ?? '-') !== '' ? (string) ($availableEvent['eventDate'] ?? '-') : '-'), ENT_QUOTES, 'UTF-8') ?>
                                <?= htmlspecialchars(' | ' . ($eventType !== '' ? $eventType : 'General'), ENT_QUOTES, 'UTF-8') ?>
                                <?= htmlspecialchars(' | Seats: ' . $seatSummary, ENT_QUOTES, 'UTF-8') ?>
                                <?php if ($location !== ''): ?>
                                    <?= htmlspecialchars(' | ' . $location, ENT_QUOTES, 'UTF-8') ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($description !== ''): ?>
                                <div class="muted" style="margin-top:4px;font-size:0.85rem;">
                                    <?= htmlspecialchars(strlen($description) > 130 ? substr($description, 0, 127) . '...' : $description, ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                            <div class="muted" style="margin-top:4px;font-size:0.85rem;">
                                <?= htmlspecialchars($joinMessage, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                        <div class="list-item-right" style="display:flex;align-items:center;gap:10px;">
                            <span class="status-badge <?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($badgeLabel, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if (!empty($availableEvent['isJoinable'])): ?>
                                <form method="POST" action="index.php?url=event/quickJoin" style="margin:0;">
                                    <?php csrf_field(); ?>
                                    <input type="hidden" name="templateEventID" value="<?= htmlspecialchars((string) ($availableEvent['templateEventID'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit" class="btn" style="padding:7px 12px;font-size:0.86rem;">
                                        <?= htmlspecialchars((string) ($availableEvent['joinButtonLabel'] ?? 'Join'), ENT_QUOTES, 'UTF-8') ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="split-layout">
        <div>
            <div class="card" style="margin-bottom:16px;">
                <form method="GET" class="filter-bar">
                    <input type="hidden" name="url" value="event/index">

                    <input
                        type="text"
                        name="search"
                        class="input"
                        placeholder="Search club, title, type, hours, location, or date..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : '' ?>">

                    <?php $currentSort = $_GET['sort'] ?? 'eventID'; ?>
                    <select name="sort" class="input">
                        <option value="eventID" <?= $currentSort === 'eventID' ? 'selected' : '' ?>>Newest</option>
                        <option value="eventTitle" <?= $currentSort === 'eventTitle' ? 'selected' : '' ?>>Event Title</option>
                        <option value="clubName" <?= $currentSort === 'clubName' ? 'selected' : '' ?>>Club</option>
                        <option value="eventType" <?= $currentSort === 'eventType' ? 'selected' : '' ?>>Event Type</option>
                        <option value="eventDate" <?= $currentSort === 'eventDate' ? 'selected' : '' ?>>Event Date</option>
                        <option value="eventHours" <?= $currentSort === 'eventHours' ? 'selected' : '' ?>>Event Hours</option>
                        <option value="location" <?= $currentSort === 'location' ? 'selected' : '' ?>>Location</option>
                        <option value="participantCapacity" <?= $currentSort === 'participantCapacity' ? 'selected' : '' ?>>Capacity</option>
                        <option value="registeredCount" <?= $currentSort === 'registeredCount' ? 'selected' : '' ?>>Registered Count</option>
                        <option value="waitlistCount" <?= $currentSort === 'waitlistCount' ? 'selected' : '' ?>>Waitlist Count</option>
                        <option value="registrationStatus" <?= $currentSort === 'registrationStatus' ? 'selected' : '' ?>>Registration Status</option>
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
                    <a class="btn btn-secondary" href="index.php?url=event/index">Reset</a>
                </form>
            </div>

            <div class="records-table-wrap">
            <table class="co-records-table">
                <tr>
                    <th>Club</th>
                    <th>Event Title</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Hours</th>
                    <th>Seats</th>
                    <th>Registration</th>
                    <th>Location</th>
                    <th>Summary</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="11" class="muted">No event records found.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($events as $e): ?>
                <?php
                    $eventDateDisplay = trim((string) ($e['eventDate'] ?? ''));
                    $eventDateDisplay = ($eventDateDisplay === '' || $eventDateDisplay === '0000-00-00') ? '' : $eventDateDisplay;
                    $status = (string) ($e['status'] ?? 'approved');
                    $reviewNote = trim((string) ($e['review_note'] ?? ''));
                    $evidencePath = trim((string) ($e['evidence_path'] ?? ''));
                    $isLocked = $status === 'approved';
                    $participantCapacity = isset($e['participantCapacity']) ? (int) $e['participantCapacity'] : 0;
                    $registeredCount = isset($e['registeredCount']) ? (int) $e['registeredCount'] : 0;
                    $waitlistCount = isset($e['waitlistCount']) ? (int) $e['waitlistCount'] : 0;
                    $registrationStatus = trim((string) ($e['registrationStatus'] ?? ''));
                    if ($registrationStatus === '') {
                        if ($participantCapacity <= 0 || $registeredCount < $participantCapacity) {
                            $registrationStatus = 'open';
                        } elseif (!empty($e['waitlistEnabled'])) {
                            $registrationStatus = 'waitlist';
                        } else {
                            $registrationStatus = 'full';
                        }
                    }
                    $seatSummary = $participantCapacity > 0
                        ? ($registeredCount . '/' . $participantCapacity)
                        : 'Not set';
                ?>
                <tr>
                    <td><?= htmlspecialchars((string) ($e['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($e['eventTitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="chip"><?= htmlspecialchars(($e['eventType'] ?? 'General') ?: 'General', ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td><?= htmlspecialchars($eventDateDisplay !== '' ? $eventDateDisplay : '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($e['eventHours'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?= htmlspecialchars($seatSummary, ENT_QUOTES, 'UTF-8') ?>
                        <?php if ($participantCapacity > 0): ?>
                            <div class="muted" style="margin-top:4px;font-size:0.85rem;">Waitlist: <?= (int) $waitlistCount ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="chip"><?= htmlspecialchars(ucfirst($registrationStatus), ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td><?= htmlspecialchars($e['location'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($e['description'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
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
                        <?php if ($isLocked): ?>
                            <span class="muted">Locked after approval (admin only)</span>
                        <?php else: ?>
                            <a class="link" href="index.php?url=event/edit&id=<?= htmlspecialchars($e['eventID'], ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                            <span class="muted">|</span>
                            <form method="POST" action="index.php?url=event/delete" style="display:inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($e['eventID'], ENT_QUOTES, 'UTF-8') ?>">
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
                <div class="muted" style="margin-top:8px;"><?= (int) $totalRecords ?> events logged</div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Type Distribution</h3>
                    <span class="chip">Categories</span>
                </div>
                <?php if (empty($topEventTypes)): ?>
                    <div class="muted">No event type data yet.</div>
                <?php else: ?>
                    <ul class="list">
                        <?php foreach ($topEventTypes as $type => $count): ?>
                            <li class="list-item">
                                <div>
                                    <div class="list-item-title"><?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="list-item-sub">Recorded events</div>
                                </div>
                                <div class="list-item-right"><?= (int) $count ?></div>
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
