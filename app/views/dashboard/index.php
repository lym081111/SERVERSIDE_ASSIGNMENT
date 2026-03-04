<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $meritHoursValue = isset($meritHours) ? (float) $meritHours : 0.0;
    $meritCountValue = isset($meritCount) ? (int) $meritCount : 0;
    $eventCountValue = isset($eventCount) ? (int) $eventCount : 0;
    $clubCountValue = isset($clubCount) ? (int) $clubCount : 0;
    $achievementCountValue = isset($achievementCount) ? (int) $achievementCount : 0;

    $latestMeritDateValue = $latestMeritDate ?? null;
    $latestEventDateValue = $latestEventDate ?? null;
    $latestClubStartValue = $latestClubStart ?? null;
    $latestAchievementDateValue = $latestAchievementDate ?? null;
    $activeClubCountValue = isset($activeClubCount) ? (int) $activeClubCount : 0;
?>

<div class="main">

    <div class="topbar">
        <div class="topbar-left"></div>
        <div class="topbar-actions">
            <form method="POST" action="index.php?url=auth/logout">
                <?php csrf_field(); ?>
                <button type="submit" class="topbar-logout">Logout</button>
            </form>
        </div>
    </div>

    <div class="content">
        <div class="content-inner">

        <h1 class="page-title">Co-curricular Dashboard</h1>
        <p class="page-subtitle">Overview your live modules and jump straight into each tracker.</p>

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Merit Hours</div>
                <div class="kpi-value"><?= (int) round($meritHoursValue) ?></div>
                <div class="kpi-sub">Across <?= (int) $meritCountValue ?> records</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Events</div>
                <div class="kpi-value"><?= (int) $eventCountValue ?></div>
                <div class="kpi-sub">Total event records</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Clubs</div>
                <div class="kpi-value"><?= (int) $clubCountValue ?></div>
                <div class="kpi-sub"><?= (int) $activeClubCountValue ?> active memberships</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-label">Achievements</div>
                <div class="kpi-value"><?= (int) $achievementCountValue ?></div>
                <div class="kpi-sub">Awards and recognition</div>
            </div>
        </div>

        <div class="module-grid">

            <div class="module-card merit">
                <div class="module-card-header">
                    <div style="display:flex;gap:10px;align-items:center;">
                        <div class="module-icon active merit">MT</div>
                        <div>
                            <h3 class="module-title">Merit Tracker</h3>
                            <div class="muted" style="font-size:0.8rem;">Active</div>
                        </div>
                    </div>
                    <span class="module-status active">Active</span>
                </div>
                <div class="module-body">
                    Log and monitor your co-curricular merit hours across activities.
                    <div class="module-meta">
                        <div class="module-meta-row">Total Hours: <strong><?= (int) round($meritHoursValue) ?></strong></div>
                        <div class="module-meta-row">Latest Activity: <strong><?= htmlspecialchars($latestMeritDateValue ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></div>
                    </div>
                </div>
                <a href="index.php?url=merit/index" class="btn btn-pill">Access Module</a>
            </div>

            <div class="module-card event">
                <div class="module-card-header">
                    <div style="display:flex;gap:10px;align-items:center;">
                        <div class="module-icon active event">EV</div>
                        <div>
                            <h3 class="module-title">Event Tracker</h3>
                            <div class="muted" style="font-size:0.8rem;">Active</div>
                        </div>
                    </div>
                    <span class="module-status active">Active</span>
                </div>
                <div class="module-body">
                    Register, record, and review your university event history.
                    <div class="module-meta">
                        <div class="module-meta-row">Total Events: <strong><?= (int) $eventCountValue ?></strong></div>
                        <div class="module-meta-row">Latest Event: <strong><?= htmlspecialchars($latestEventDateValue ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></div>
                    </div>
                </div>
                <a href="index.php?url=event/index" class="btn btn-pill">Access Module</a>
            </div>

            <div class="module-card club">
                <div class="module-card-header">
                    <div style="display:flex;gap:10px;align-items:center;">
                        <div class="module-icon active club">CL</div>
                        <div>
                            <h3 class="module-title">Club Tracker</h3>
                            <div class="muted" style="font-size:0.8rem;">Active</div>
                        </div>
                    </div>
                    <span class="module-status active">Active</span>
                </div>
                <div class="module-body">
                    Track your club memberships, roles, and involvement timeline.
                    <div class="module-meta">
                        <div class="module-meta-row">Total Clubs: <strong><?= (int) $clubCountValue ?></strong></div>
                        <div class="module-meta-row">Active Memberships: <strong><?= (int) $activeClubCountValue ?></strong></div>
                    </div>
                </div>
                <a href="index.php?url=club/index" class="btn btn-pill">Access Module</a>
            </div>

            <div class="module-card achievement">
                <div class="module-card-header">
                    <div style="display:flex;gap:10px;align-items:center;">
                        <div class="module-icon active achievement">AW</div>
                        <div>
                            <h3 class="module-title">Achievements</h3>
                            <div class="muted" style="font-size:0.8rem;">Active</div>
                        </div>
                    </div>
                    <span class="module-status active">Active</span>
                </div>
                <div class="module-body">
                    Record awards, competition results, and special recognitions.
                    <div class="module-meta">
                        <div class="module-meta-row">Total Awards: <strong><?= (int) $achievementCountValue ?></strong></div>
                        <div class="module-meta-row">Latest Award: <strong><?= htmlspecialchars($latestAchievementDateValue ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></div>
                    </div>
                </div>
                <a href="index.php?url=achievement/index" class="btn btn-pill">Access Module</a>
            </div>

        </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
