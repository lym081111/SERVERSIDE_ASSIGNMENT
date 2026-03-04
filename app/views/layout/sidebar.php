<?php
    $currentUrl = $_GET['url'] ?? 'dashboard/index';
    $isDashboardHome = strpos($currentUrl, 'dashboard') === 0;
    $userName = trim((string) ($_SESSION['user_name'] ?? 'Student'));
    $isAdmin = !empty($_SESSION['isAdmin']);
    $nameParts = preg_split('/\s+/', $userName) ?: [];
    $userInitials = '';

    foreach ($nameParts as $part) {
        if ($part === '') {
            continue;
        }

        $userInitials .= strtoupper(substr($part, 0, 1));

        if (strlen($userInitials) >= 2) {
            break;
        }
    }

    if ($userInitials === '') {
        $userInitials = 'ST';
    }
?>

<?php if ($isDashboardHome): ?>
    <?php
        $meritHours = isset($meritHours) ? (float) $meritHours : 0.0;
        $meritCount = isset($meritCount) ? (int) $meritCount : 0;
        $eventCount = isset($eventCount) ? (int) $eventCount : 0;
        $clubCount = isset($clubCount) ? (int) $clubCount : 0;
        $activeClubCount = isset($activeClubCount) ? (int) $activeClubCount : 0;
        $achievementCount = isset($achievementCount) ? (int) $achievementCount : 0;
    ?>

    <div class="sidebar dashboard-sidebar">
        <div class="sidebar-header dashboard-sidebar-header">
            <div class="sidebar-eyebrow">SCMS Student Hub</div>
            <h2 class="sidebar-title">Student Portal</h2>
            <div class="sidebar-subtitle">A quick read on your co-curricular progress.</div>
        </div>

        <div class="dashboard-sidebar-body">
            <div class="sidebar-panel sidebar-profile-card">
                <div class="sidebar-avatar"><?= htmlspecialchars($userInitials, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="sidebar-profile-copy">
                    <div class="sidebar-profile-name"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="sidebar-profile-meta">Student Portal</div>
                </div>
                <span class="sidebar-pill sidebar-pill-strong">All modules live</span>
            </div>

            <div class="sidebar-panel">
                <div class="sidebar-panel-header">
                    <span class="sidebar-panel-title">Snapshot</span>
                    <span class="sidebar-pill">Live</span>
                </div>
                <div class="sidebar-metric"><?= (int) round($meritHours) ?> merit hours recorded</div>
                <div class="sidebar-note">Quick totals across your active trackers.</div>

                <div class="sidebar-stat-grid quad">
                    <div class="sidebar-stat-card">
                        <span class="sidebar-stat-label">Merits</span>
                        <strong><?= $meritCount ?></strong>
                    </div>
                    <div class="sidebar-stat-card">
                        <span class="sidebar-stat-label">Events</span>
                        <strong><?= $eventCount ?></strong>
                    </div>
                    <div class="sidebar-stat-card">
                        <span class="sidebar-stat-label">Clubs</span>
                        <strong><?= $clubCount ?></strong>
                    </div>
                    <div class="sidebar-stat-card">
                        <span class="sidebar-stat-label">Awards</span>
                        <strong><?= $achievementCount ?></strong>
                    </div>
                </div>
                <div class="sidebar-note" style="margin-top:10px;"><?= $activeClubCount ?> active club memberships</div>
            </div>

            <div class="sidebar-panel">
                <div class="sidebar-panel-header">
                    <span class="sidebar-panel-title">Quick Actions</span>
                    <span class="sidebar-pill">Actions</span>
                </div>
                <div class="sidebar-link-list">
                    <a class="sidebar-link" href="index.php?url=merit/create">Add Merit Record</a>
                    <a class="sidebar-link" href="index.php?url=event/create">Add Event Record</a>
                    <a class="sidebar-link" href="index.php?url=club/create">Add Club Record</a>
                    <a class="sidebar-link" href="index.php?url=achievement/create">Add Achievement</a>
                    <?php if ($isAdmin): ?>
                        <a class="sidebar-link" href="index.php?url=admin/index">Admin Overview</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
<?php else: ?>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2 class="sidebar-title">SCMS</h2>
            <div class="sidebar-subtitle">Student Co-curricular</div>
        </div>

        <div class="sidebar-nav">
            <?php if ($isAdmin): ?>
                <a href="index.php?url=admin/index"
                   class="nav-link <?= strpos($currentUrl, 'admin') === 0 ? 'active' : '' ?>">
                    <span>Admin Overview</span>
                </a>
                <a href="index.php?url=merit/index"
                   class="nav-link <?= strpos($currentUrl, 'merit') === 0 ? 'active' : '' ?>">
                    <span>Admin Merits</span>
                </a>
                <a href="index.php?url=event/index"
                   class="nav-link <?= strpos($currentUrl, 'event') === 0 ? 'active' : '' ?>">
                    <span>Admin Events</span>
                </a>
                <a href="index.php?url=club/index"
                   class="nav-link <?= strpos($currentUrl, 'club') === 0 ? 'active' : '' ?>">
                    <span>Admin Clubs</span>
                </a>
                <a href="index.php?url=achievement/index"
                   class="nav-link <?= strpos($currentUrl, 'achievement') === 0 ? 'active' : '' ?>">
                    <span>Admin Achievements</span>
                </a>
            <?php else: ?>
                <a href="index.php?url=dashboard/index"
                   class="nav-link <?= strpos($currentUrl, 'dashboard') === 0 ? 'active' : '' ?>">
                    <span>Dashboard</span>
                </a>
                <a href="index.php?url=event/index"
                   class="nav-link <?= strpos($currentUrl, 'event') === 0 ? 'active' : '' ?>">
                    <span>Events</span>
                </a>
                <a href="index.php?url=club/index"
                   class="nav-link <?= strpos($currentUrl, 'club') === 0 ? 'active' : '' ?>">
                    <span>Clubs</span>
                </a>
                <a href="index.php?url=merit/index"
                   class="nav-link <?= strpos($currentUrl, 'merit') === 0 ? 'active' : '' ?>">
                    <span>Merit</span>
                </a>
                <a href="index.php?url=achievement/index"
                   class="nav-link <?= strpos($currentUrl, 'achievement') === 0 ? 'active' : '' ?>">
                    <span>Achievements</span>
                </a>
            <?php endif; ?>
        </div>

    </div>
<?php endif; ?>
