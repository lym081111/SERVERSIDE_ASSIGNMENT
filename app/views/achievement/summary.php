<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<style>
    .achievement-summary-shell {
        background:
            radial-gradient(circle at 10% 12%, rgba(245, 158, 11, 0.08), transparent 40%),
            radial-gradient(circle at 90% 2%, rgba(30, 64, 175, 0.08), transparent 34%),
            #f3f5fb;
    }

    .achievement-summary-shell .page-header {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(255, 255, 255, 0.96) 65%);
        border: 1px solid rgba(245, 158, 11, 0.18);
        border-radius: 18px;
        padding: 20px 22px;
        margin-bottom: 16px;
    }

    .achievement-summary-shell .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .achievement-summary-shell .summary-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(226, 232, 240, 0.9);
    }

    .achievement-summary-shell .summary-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
    }

    .achievement-summary-shell .summary-value {
        font-size: 2rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .achievement-summary-shell .summary-sub {
        margin-top: 8px;
        font-size: 0.92rem;
        color: #64748b;
    }

    .achievement-summary-shell .detail-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(226, 232, 240, 0.9);
        margin-top: 20px;
    }

    .achievement-summary-shell .level-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-top: 14px;
    }

    .achievement-summary-shell .level-item {
        padding: 14px 16px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .achievement-summary-shell .achievement-item {
        padding: 16px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .achievement-summary-shell .achievement-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .achievement-summary-shell .achievement-item:first-child {
        padding-top: 6px;
    }

    .achievement-summary-shell .achievement-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .achievement-summary-shell .achievement-meta {
        display: grid;
        gap: 6px;
        color: #334155;
        font-size: 0.95rem;
    }
</style>

<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Achievement Summary</div>
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

    <div class="content achievement-summary-shell">
        <div class="content-inner">

            <div class="page-header">
                <div>
                    <h1 class="page-title">Achievement Summary</h1>
                    <p class="page-subtitle">A quick overview of your approved achievements and recognition records.</p>
                </div>
                <div class="page-actions">
                    <a class="btn btn-secondary" href="index.php?url=dashboard/index">Back to Dashboard</a>
                    <a class="btn" href="index.php?url=achievement/index">Go to Achievements Module</a>
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-label">Total Achievement Get</div>
                    <div class="summary-value"><?= (int) $totalAchievements ?></div>
                    <div class="summary-sub">Approved achievements only</div>
                </div>

                <div class="summary-card" style="text-align:center;">
                    <div class="summary-label">Milestone Stars</div>
                    <?php
                        $starMilestones = [3, 5, 10, 20];
                        $earnedStars = 0;
                        foreach ($starMilestones as $goal) {
                            if ((int) $totalAchievements >= $goal) {
                                $earnedStars++;
                            }
                        }
                        if ((int) $totalAchievements > 20) {
                            $earnedStars = 5;
                        }
                        $totalStars = 5;
                        $starLabels = ['', '3 achievements', '5 achievements', '10 achievements', '20 achievements', '20+ achievements'];
                    ?>
                    <div style="font-size:2.4rem; letter-spacing:4px; margin: 10px 0 6px;">
                        <?php for ($s = 1; $s <= $totalStars; $s++): ?>
                            <span style="color:<?= $s <= $earnedStars ? '#f59e0b' : '#e2e8f0' ?>; text-shadow:<?= $s <= $earnedStars ? '0 1px 3px rgba(245,158,11,0.4)' : 'none' ?>;">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <div class="summary-sub" style="margin-top:4px; font-weight:700; color:#1e293b;">
                        <?= (int) $earnedStars ?> / <?= (int) $totalStars ?> stars earned
                    </div>
                    <div class="summary-sub" style="margin-top:6px; color:#64748b; font-size:0.85rem;">
                        <?php if ($earnedStars < $totalStars): ?>
                            Next star at <?= htmlspecialchars((string) ($starLabels[$earnedStars + 1] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        <?php else: ?>
                            All milestones achieved!
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <h3 style="margin-top:0; margin-bottom: 10px;">Achievements by Level</h3>

                <div class="level-list">
                    <div class="level-item">
                        <div class="summary-label">Faculty</div>
                        <div class="summary-value" style="font-size:1.6rem;"><?= (int) ($levelCounts['Faculty'] ?? 0) ?></div>
                    </div>

                    <div class="level-item">
                        <div class="summary-label">University</div>
                        <div class="summary-value" style="font-size:1.6rem;"><?= (int) ($levelCounts['University'] ?? 0) ?></div>
                    </div>

                    <div class="level-item">
                        <div class="summary-label">National</div>
                        <div class="summary-value" style="font-size:1.6rem;"><?= (int) ($levelCounts['National'] ?? 0) ?></div>
                    </div>

                    <div class="level-item">
                        <div class="summary-label">International</div>
                        <div class="summary-value" style="font-size:1.6rem;"><?= (int) ($levelCounts['International'] ?? 0) ?></div>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <h3 style="margin-top:0; margin-bottom: 10px;">Detail of the Achievements</h3>

                <?php if (empty($achievements)): ?>
                    <div class="muted">No approved achievement records found.</div>
                <?php else: ?>
                    <?php foreach ($achievements as $index => $achievement): ?>
                        <?php
                            $title = trim((string) ($achievement['title'] ?? '-'));
                            $category = trim((string) ($achievement['category'] ?? '-'));
                            $achievementLevel = trim((string) ($achievement['achievementLevel'] ?? '-'));
                            $dateReceived = trim((string) ($achievement['dateReceived'] ?? '-'));
                            $dateReceived = ($dateReceived === '' || $dateReceived === '0000-00-00') ? '-' : $dateReceived;
                        ?>
                        <div class="achievement-item">
                            <div class="achievement-title"><?= (int) ($index + 1) ?>. <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="achievement-meta">
                                <div><strong>Title:</strong> <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></div>
                                <div><strong>Category:</strong> <?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></div>
                                <div><strong>Achievement Level:</strong> <?= htmlspecialchars($achievementLevel, ENT_QUOTES, 'UTF-8') ?></div>
                                <div><strong>Date Received:</strong> <?= htmlspecialchars($dateReceived, ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
