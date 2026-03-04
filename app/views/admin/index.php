<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

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
            <h1 class="admin-title">Admin Module</h1>
            <p class="admin-subtitle">You have full visibility across every student record and tracker.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-secondary" href="index.php?url=admin/index">Refresh</a>
        </div>
    </div>

    <div class="admin-kpi-grid">
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Students</div>
            <div class="admin-kpi-value"><?= (int) $userCount ?></div>
            <div class="admin-kpi-sub">Registered users</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Active Students</div>
            <div class="admin-kpi-value"><?= (int) $activeStudentCount ?></div>
            <div class="admin-kpi-sub">With any record</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Merits</div>
            <div class="admin-kpi-value"><?= (int) $meritCount ?></div>
            <div class="admin-kpi-sub">Total records</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Events</div>
            <div class="admin-kpi-value"><?= (int) $eventCount ?></div>
            <div class="admin-kpi-sub">Total records</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Clubs</div>
            <div class="admin-kpi-value"><?= (int) $clubCount ?></div>
            <div class="admin-kpi-sub">Total records</div>
        </div>
        <div class="admin-kpi-card">
            <div class="admin-kpi-label">Achievements</div>
            <div class="admin-kpi-value"><?= (int) $achievementCount ?></div>
            <div class="admin-kpi-sub">Total records</div>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Student Directory</h2>
            <span class="admin-section-chip">All users</span>
        </div>
        <div class="admin-section-body">
            <table class="admin-table">
                <tr>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Merits</th>
                    <th>Events</th>
                    <th>Clubs</th>
                    <th>Achievements</th>
                </tr>
                <?php if (empty($userSummaries)): ?>
                    <tr>
                        <td colspan="6" class="muted">No students found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($userSummaries as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($u['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) ($u['meritCount'] ?? 0) ?></td>
                        <td><?= (int) ($u['eventCount'] ?? 0) ?></td>
                        <td><?= (int) ($u['clubCount'] ?? 0) ?></td>
                        <td><?= (int) ($u['achievementCount'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <details class="admin-section admin-detail" open>
        <summary class="admin-detail-summary">
            <span>Merit Records</span>
            <span class="admin-section-chip"><?= (int) $meritCount ?> total</span>
        </summary>
        <div class="admin-section-body">
            <table class="admin-table">
                <tr>
                    <th>Student</th>
                    <th>Activity</th>
                    <th>Hours</th>
                    <th>Date From</th>
                    <th>Date To</th>
                </tr>
                <?php if (empty($merits)): ?>
                    <tr>
                        <td colspan="5" class="muted">No merit records yet.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($merits as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['activityName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['hours'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['dateFrom'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($m['dateTo'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </details>

    <details class="admin-section admin-detail" open>
        <summary class="admin-detail-summary">
            <span>Event Records</span>
            <span class="admin-section-chip"><?= (int) $eventCount ?> total</span>
        </summary>
        <div class="admin-section-body">
            <table class="admin-table">
                <tr>
                    <th>Student</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Description</th>
                </tr>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="5" class="muted">No event records yet.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($events as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['eventTitle'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['eventDate'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['location'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($e['description'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </details>

    <details class="admin-section admin-detail" open>
        <summary class="admin-detail-summary">
            <span>Club Records</span>
            <span class="admin-section-chip"><?= (int) $clubCount ?> total</span>
        </summary>
        <div class="admin-section-body">
            <table class="admin-table">
                <tr>
                    <th>Student</th>
                    <th>Club</th>
                    <th>Role</th>
                    <th>Role Description</th>
                    <th>Start</th>
                    <th>End</th>
                </tr>
                <?php if (empty($clubs)): ?>
                    <tr>
                        <td colspan="6" class="muted">No club records yet.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($clubs as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['clubName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['role'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['roleDescription'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['startDate'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($c['endDate'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </details>

    <details class="admin-section admin-detail">
        <summary class="admin-detail-summary">
            <span>Achievement Records</span>
            <span class="admin-section-chip"><?= (int) $achievementCount ?> total</span>
        </summary>
        <div class="admin-section-body">
            <table class="admin-table">
                <tr>
                    <th>Student</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Description</th>
                </tr>
                <?php if (empty($achievements)): ?>
                    <tr>
                        <td colspan="5" class="muted">No achievement records yet.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($achievements as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['userName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['title'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['category'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['dateReceived'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($a['description'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </details>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
