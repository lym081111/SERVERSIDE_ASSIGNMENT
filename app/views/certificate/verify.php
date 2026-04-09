<?php require "../app/views/layout/header.php"; ?>

<div class="main" style="margin-left:0;">
    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Certificate Verification</div>
        </div>
        <div class="topbar-actions">
            <a href="index.php?url=auth/login" class="btn btn-secondary">Back to Login</a>
        </div>
    </div>

    <div class="content">
        <div class="content-inner">
            <div class="card" style="max-width:900px;margin:0 auto;">
                <?php if (empty($certificate)): ?>
                    <h2 style="margin-top:0;">Invalid Certificate</h2>
                    <p class="muted">The provided certificate code was not found.</p>
                <?php else: ?>
                    <h2 style="margin-top:0;">Certificate Verified</h2>
                    <p class="muted">This merit certificate is valid and issued by SCMS.</p>

                    <table class="admin-table">
                        <tr>
                            <th>Certificate Code</th>
                            <td><?= htmlspecialchars($certificate['certificate_code'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                        <tr>
                            <th>Student Name</th>
                            <td><?= htmlspecialchars($certificate['studentName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                        <tr>
                            <th>Student ID</th>
                            <td><?= htmlspecialchars($certificate['studentId'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                        <tr>
                            <th>Milestone</th>
                            <td><?= (int) ($certificate['milestone_hours'] ?? 0) ?> approved merit hours</td>
                        </tr>
                        <tr>
                            <th>Issued At</th>
                            <td><?= htmlspecialchars($certificate['issued_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require "../app/views/layout/footer.php"; ?>
