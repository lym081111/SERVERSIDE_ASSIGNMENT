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
        $reflectionCount = 0;
        $latestReflection = null;
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

                $reflection = trim((string) ($row['reflection'] ?? ''));
                if ($reflection !== '') {
                    $reflectionCount++;
                    if ($latestReflection === null) {
                        $latestReflection = [
                            'title' => (string) ($row['eventTitle'] ?? 'Untitled'),
                            'text' => $reflection,
                        ];
                    }
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
            <a href="index.php?url=event/create" class="btn">+ Add Event</a>
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

    <div class="split-layout">
        <div>
            <div class="card" style="margin-bottom:16px;">
                <form method="GET" class="filter-bar">
                    <input type="hidden" name="url" value="event/index">

                    <input
                        type="text"
                        name="search"
                        class="input"
                        placeholder="Search club, title, type, hours, location, reflection, or date..."
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

            <table class="co-records-table">
                <tr>
                    <th>Club</th>
                    <th>Event Title</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Hours</th>
                    <th>Location</th>
                    <th>Summary</th>
                    <th>Reflection</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="10" class="muted">No event records found.</td>
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
                ?>
                <tr>
                    <td><?= htmlspecialchars((string) ($e['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($e['eventTitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="chip"><?= htmlspecialchars(($e['eventType'] ?? 'General') ?: 'General', ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td><?= htmlspecialchars($eventDateDisplay !== '' ? $eventDateDisplay : '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($e['eventHours'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($e['location'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($e['description'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php
                            $reflectionText = trim((string) ($e['reflection'] ?? ''));
                        ?>
                        <?= $reflectionText === '' ? '<span class="muted">No reflection</span>' : htmlspecialchars(strlen($reflectionText) > 90 ? substr($reflectionText, 0, 87) . '...' : $reflectionText, ENT_QUOTES, 'UTF-8') ?>
                    </td>
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
                    <h3 class="card-title">Reflection Log</h3>
                    <span class="chip"><?= (int) $reflectionCount ?> entries</span>
                </div>
                <div class="muted" style="margin-bottom:10px;">Capture what you learned for each event to build a stronger portfolio.</div>
                <?php if ($latestReflection !== null): ?>
                    <div class="list-item">
                        <div>
                            <div class="list-item-title"><?= htmlspecialchars($latestReflection['title'] ?? 'Latest reflection', ENT_QUOTES, 'UTF-8') ?></div>
                            <?php $latestReflectionText = (string) ($latestReflection['text'] ?? ''); ?>
                            <div class="list-item-sub"><?= htmlspecialchars(strlen($latestReflectionText) > 140 ? substr($latestReflectionText, 0, 137) . '...' : $latestReflectionText, ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="muted">No reflection yet. Add one when you edit an event.</div>
                <?php endif; ?>
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
