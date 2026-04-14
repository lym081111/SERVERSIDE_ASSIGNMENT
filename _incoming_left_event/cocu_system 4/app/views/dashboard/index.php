<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $meritHoursValue = isset($meritHours) ? (float) $meritHours : 0.0;
    $meritCountValue = isset($meritCount) ? (int) $meritCount : 0;
    $eventCountValue = isset($eventCount) ? (int) $eventCount : 0;
    $clubCountValue = isset($clubCount) ? (int) $clubCount : 0;
    $achievementCountValue = isset($achievementCount) ? (int) $achievementCount : 0;
    $pendingMeritCountValue = isset($pendingMeritCount) ? (int) $pendingMeritCount : 0;
    $pendingEventCountValue = isset($pendingEventCount) ? (int) $pendingEventCount : 0;
    $pendingClubCountValue = isset($pendingClubCount) ? (int) $pendingClubCount : 0;
    $pendingAchievementCountValue = isset($pendingAchievementCount) ? (int) $pendingAchievementCount : 0;
    $approvedMeritHoursValue = isset($approvedMeritHours) ? (int) $approvedMeritHours : 0;
    $meritCertificateCountValue = isset($meritCertificateCount) ? (int) $meritCertificateCount : 0;
    $nextMeritCertificateMilestoneValue = isset($nextMeritCertificateMilestone) ? (int) $nextMeritCertificateMilestone : 100;
    $hoursToNextCertificateValue = isset($hoursToNextCertificate) ? (int) $hoursToNextCertificate : max(0, $nextMeritCertificateMilestoneValue - $approvedMeritHoursValue);

    $latestMeritDateValue = $latestMeritDate ?? null;
    $latestEventDateValue = $latestEventDate ?? null;
    $latestClubStartValue = $latestClubStart ?? null;
    $latestAchievementDateValue = $latestAchievementDate ?? null;
    $activeClubCountValue = isset($activeClubCount) ? (int) $activeClubCount : 0;
?>

<style>
    .content.dashboard-shell {
        background:
            radial-gradient(circle at 10% 12%, rgba(30, 64, 175, 0.08), transparent 40%),
            radial-gradient(circle at 90% 2%, rgba(14, 165, 233, 0.08), transparent 34%),
            #f3f5fb;
    }

    .dashboard-shell .page-header {
        background: linear-gradient(135deg, rgba(30, 64, 175, 0.08), rgba(255, 255, 255, 0.96) 65%);
        border: 1px solid rgba(37, 99, 235, 0.16);
        border-radius: 18px;
        padding: 20px 22px;
        margin-bottom: 16px;
    }

    .dashboard-shell .page-subtitle {
        margin-bottom: 0;
    }

    .dashboard-shell .kpi-card {
        border-width: 0;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .dashboard-shell .kpi-card:nth-child(1) {
        background: linear-gradient(135deg, #eff6ff, #ffffff 58%);
    }

    .dashboard-shell .kpi-card:nth-child(2) {
        background: linear-gradient(135deg, #ecfeff, #ffffff 58%);
    }

    .dashboard-shell .kpi-card:nth-child(3) {
        background: linear-gradient(135deg, #f0fdf4, #ffffff 58%);
    }

    .dashboard-shell .kpi-card:nth-child(4) {
        background: linear-gradient(135deg, #fffbeb, #ffffff 58%);
    }
</style>

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

    <div class="content dashboard-shell">
        <div class="content-inner">

        <div class="page-header">
            <div>
                <h1 class="page-title">Co-curricular Dashboard</h1>
                <p class="page-subtitle">Overview your live modules and jump straight into each tracker.</p>
            </div>
            <div class="page-actions">
                <a class="btn btn-secondary" href="index.php?url=dashboard/transcript">Print Official Transcript</a>
            </div>
        </div>

        <div class="kpi-grid">
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

            <div class="kpi-card">
                <div class="kpi-label">Merit Certificates</div>
                <div class="kpi-value"><?= (int) $meritCertificateCountValue ?></div>
                <div class="kpi-sub"><?= (int) $hoursToNextCertificateValue ?>h to <?= (int) $nextMeritCertificateMilestoneValue ?>h</div>
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
                        <div class="module-meta-row">Approved Hours: <strong><?= (int) $approvedMeritHoursValue ?></strong></div>
                        <div class="module-meta-row">Latest Activity: <strong><?= htmlspecialchars($latestMeritDateValue ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></div>
                        <div class="module-meta-row">Pending Review: <strong><?= (int) $pendingMeritCountValue ?></strong></div>
                        <div class="module-meta-row">Certificates: <strong><?= (int) $meritCertificateCountValue ?></strong></div>
                    </div>
                </div>
                <a href="index.php?url=merit/index" class="btn btn-pill">Access Module</a>
                <div style="margin-top:10px;">
                    <a class="link" href="index.php?url=certificate/myMerit">View Merit Certificates</a>
                </div>
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
                        <div class="module-meta-row">Pending Review: <strong><?= (int) $pendingEventCountValue ?></strong></div>
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
                        <div class="module-meta-row">Pending Review: <strong><?= (int) $pendingClubCountValue ?></strong></div>
                    </div>
                </div>
                <a href="index.php?url=club/index" class="btn btn-pill">Access Module</a>
                <div style="margin-top:10px;">
                    <a href="index.php?url=club/timeline" class="link">
                        View Club Involvement Timeline
                    </a>
                </div>
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
                        <div class="module-meta-row">Pending Review: <strong><?= (int) $pendingAchievementCountValue ?></strong></div>
                    </div>
                </div>
                <a href="index.php?url=achievement/index" class="btn btn-pill">Access Module</a>
                <div style="margin-top:10px;">
                    <a class="link" href="index.php?url=achievement/summary">Achievement Summary</a>
                </div>
            </div>

        </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>