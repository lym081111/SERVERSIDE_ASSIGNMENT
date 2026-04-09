<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">
    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Merit Certificates</div>
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

            <div class="page-header">
                <div>
                    <h2 style="margin:0;">My Merit Certificates</h2>
                    <div class="muted" style="margin-top:6px;">
                        Certificates are auto-issued every time your approved merit hours hit a 100-hour milestone.
                    </div>
                </div>
                <div class="page-actions">
                    <a href="index.php?url=merit/index" class="btn btn-secondary">Back to Merit</a>
                </div>
            </div>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="error">
                    <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">Approved Merit Hours</div>
                    <div class="kpi-value"><?= (int) ($approvedHours ?? 0) ?></div>
                    <div class="kpi-sub">Currently approved by admin</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Certificates Earned</div>
                    <div class="kpi-value"><?= is_array($certificates) ? count($certificates) : 0 ?></div>
                    <div class="kpi-sub">Total issued certificates</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Next Milestone</div>
                    <div class="kpi-value"><?= (int) ($nextMilestone ?? 100) ?>h</div>
                    <div class="kpi-sub"><?= (int) ($hoursToNext ?? 0) ?> hour(s) to go</div>
                </div>
            </div>

            <div class="admin-section">
                <div class="admin-section-header">
                    <h3 class="admin-section-title">Issued Certificates</h3>
                    <span class="admin-section-chip"><?= is_array($certificates) ? count($certificates) : 0 ?> total</span>
                </div>
                <div class="admin-section-body">
                    <table class="admin-table">
                        <tr>
                            <th>Certificate Code</th>
                            <th>Milestone</th>
                            <th>Approved Hours Snapshot</th>
                            <th>Issued At</th>
                            <th>Actions</th>
                        </tr>

                        <?php if (empty($certificates)): ?>
                            <tr>
                                <td colspan="5" class="muted">
                                    You do not have a merit certificate yet. Reach 100 approved merit hours to unlock your first certificate.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($certificates as $certificate): ?>
                            <tr>
                                <td><?= htmlspecialchars($certificate['certificate_code'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= (int) ($certificate['milestone_hours'] ?? 0) ?>h</td>
                                <td><?= (int) ($certificate['approved_hours_snapshot'] ?? 0) ?>h</td>
                                <td><?= htmlspecialchars($certificate['issued_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <a class="link" href="index.php?url=certificate/view&id=<?= htmlspecialchars($certificate['certificateID'] ?? '', ENT_QUOTES, 'UTF-8') ?>">Open</a>
                                    <span class="muted">|</span>
                                    <a class="link" href="index.php?url=certificate/verify&code=<?= urlencode((string) ($certificate['certificate_code'] ?? '')) ?>" target="_blank" rel="noopener">Verify</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "../app/views/layout/footer.php"; ?>
