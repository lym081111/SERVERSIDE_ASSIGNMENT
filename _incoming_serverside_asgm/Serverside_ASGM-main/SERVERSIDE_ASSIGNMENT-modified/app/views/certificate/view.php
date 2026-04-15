<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>
<?php
    $issuedRaw = (string) ($certificate['issued_at'] ?? '');
    $issuedLabel = $issuedRaw;
    if ($issuedRaw !== '' && strtotime($issuedRaw) !== false) {
        $issuedLabel = date('d F Y', strtotime($issuedRaw));
    }

    $verificationUrl = BASE_URL . 'index.php?url=certificate/verify&code=' . urlencode((string) ($certificate['certificate_code'] ?? ''));
    $logoUrl = BASE_URL . 'assets/utar_logo.svg';
?>

<style>
    .certificate-stage {
        max-width: 1100px;
        margin: 0 auto;
    }

    .certificate-paper {
        position: relative;
        margin: 0 auto;
        max-width: 980px;
        background: #fffdf6;
        border: 4px double #1f3a93;
        border-radius: 12px;
        padding: 34px 42px 36px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
        overflow: hidden;
    }

    .certificate-watermark {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        pointer-events: none;
        font-size: 11rem;
        font-weight: 800;
        color: rgba(30, 58, 147, 0.05);
        letter-spacing: 0.3rem;
        user-select: none;
    }

    .certificate-header {
        position: relative;
        z-index: 2;
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: center;
        gap: 20px;
        border-bottom: 2px solid rgba(30, 58, 147, 0.2);
        padding-bottom: 16px;
    }

    .certificate-branding {
        display: grid;
        gap: 8px;
    }

    .certificate-branding h1 {
        margin: 0;
        font-size: 1.52rem;
        letter-spacing: 0.04em;
        color: #102a6a;
    }

    .certificate-branding p {
        margin: 0;
        color: #334155;
    }

    .certificate-logo {
        width: 300px;
        max-width: 44vw;
        border-radius: 8px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: #ffffff;
    }

    .certificate-title-wrap {
        position: relative;
        z-index: 2;
        text-align: center;
        margin-top: 26px;
    }

    .certificate-title-wrap h2 {
        margin: 0;
        font-family: "Times New Roman", Georgia, serif;
        font-size: 2.4rem;
        letter-spacing: 0.08em;
        color: #0f2a7a;
        text-transform: uppercase;
    }

    .certificate-subtitle {
        margin-top: 8px;
        color: #475569;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .recipient-block {
        position: relative;
        z-index: 2;
        margin-top: 26px;
        text-align: center;
    }

    .recipient-label {
        color: #334155;
        margin-bottom: 8px;
    }

    .recipient-name {
        margin: 0;
        font-family: "Times New Roman", Georgia, serif;
        font-size: 2.2rem;
        color: #111827;
        border-bottom: 2px solid rgba(30, 58, 147, 0.24);
        display: inline-block;
        padding: 0 18px 7px;
    }

    .recipient-meta {
        margin-top: 10px;
        color: #334155;
    }

    .certificate-statement {
        position: relative;
        z-index: 2;
        margin: 24px auto 0;
        text-align: center;
        max-width: 780px;
        line-height: 1.8;
        font-size: 1.08rem;
        color: #0f172a;
    }

    .certificate-statement strong {
        color: #0f2a7a;
    }

    .certificate-meta-grid {
        position: relative;
        z-index: 2;
        margin-top: 28px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .certificate-meta-card {
        border: 1px solid rgba(15, 23, 42, 0.12);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.85);
        padding: 12px 14px;
    }

    .certificate-meta-label {
        margin: 0;
        color: #475569;
        font-size: 0.86rem;
    }

    .certificate-meta-value {
        margin: 6px 0 0;
        font-weight: 800;
        color: #0f172a;
        word-break: break-word;
    }

    .certificate-verify {
        position: relative;
        z-index: 2;
        margin-top: 16px;
        text-align: center;
        color: #334155;
        font-size: 0.92rem;
    }

    .certificate-signature-grid {
        position: relative;
        z-index: 2;
        margin-top: 34px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 34px;
    }

    .signature-box {
        text-align: center;
    }

    .signature-line {
        height: 1px;
        width: 100%;
        background: #0f172a;
        margin-bottom: 10px;
    }

    .signature-name {
        margin: 0;
        font-weight: 800;
        color: #0f172a;
    }

    .signature-role {
        margin-top: 4px;
        color: #475569;
        font-size: 0.9rem;
    }

    .certificate-seal {
        position: absolute;
        right: 28px;
        bottom: 22px;
        width: 122px;
        height: 122px;
        border-radius: 50%;
        border: 4px solid rgba(203, 53, 53, 0.82);
        color: rgba(185, 28, 28, 0.9);
        display: grid;
        place-items: center;
        text-align: center;
        font-weight: 800;
        letter-spacing: 0.06em;
        line-height: 1.3;
        background: rgba(254, 242, 242, 0.86);
        transform: rotate(-14deg);
    }

    @media (max-width: 900px) {
        .certificate-paper {
            padding: 24px 22px 28px;
        }

        .certificate-header {
            grid-template-columns: 1fr;
        }

        .certificate-logo {
            width: 240px;
        }

        .certificate-title-wrap h2 {
            font-size: 1.9rem;
        }

        .recipient-name {
            font-size: 1.7rem;
        }

        .certificate-meta-grid {
            grid-template-columns: 1fr;
        }

        .certificate-signature-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .certificate-seal {
            position: static;
            margin: 18px auto 0;
            transform: none;
        }
    }

    @media print {
        .topbar {
            display: none !important;
        }

        .certificate-paper {
            box-shadow: none !important;
            border-width: 3px;
            max-width: none;
        }
    }
</style>

<div class="main">
    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Merit Certificate</div>
            <div class="topbar-user-inline">
                <?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-secondary" type="button" onclick="window.print()">Print</button>
            <a href="<?= !empty($_SESSION['isAdmin']) ? 'index.php?url=merit/index' : 'index.php?url=certificate/myMerit' ?>" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="content">
        <div class="content-inner certificate-stage">
            <article class="certificate-paper">
                <div class="certificate-watermark">UTAR</div>

                <header class="certificate-header">
                    <div class="certificate-branding">
                        <h1>Universiti Tunku Abdul Rahman</h1>
                        <p>Division of Student Affairs and Co-Curricular Development</p>
                        <p>Official University Merit Recognition Certificate</p>
                    </div>
                    <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="UTAR Logo" class="certificate-logo">
                </header>

                <section class="certificate-title-wrap">
                    <h2>Certificate of Merit Achievement</h2>
                    <div class="certificate-subtitle">Issued for Verified Co-Curricular Excellence</div>
                    <?php
                        $milestoneHours = (int) ($certificate['milestone_hours'] ?? 0);
                        $certStars = min(5, max(1, (int) ($milestoneHours / 100)));
                    ?>
                    <div style="margin-top:10px; font-size:1.9rem; letter-spacing:5px;">
                        <?php for ($cs = 1; $cs <= 5; $cs++): ?>
                            <span style="color:<?= $cs <= $certStars ? '#f59e0b' : 'rgba(15,23,42,0.15)' ?>; text-shadow:<?= $cs <= $certStars ? '0 2px 6px rgba(245,158,11,0.5)' : 'none' ?>;">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <div style="margin-top:4px; color:#475569; font-size:0.82rem; letter-spacing:0.08em; text-transform:uppercase; font-weight:700;">
                        <?= (int) $certStars ?>-Star Merit Milestone
                    </div>
                </section>

                <section class="recipient-block">
                    <div class="recipient-label">This is to certify that</div>
                    <h3 class="recipient-name"><?= htmlspecialchars($certificate['studentName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></h3>
                    <div class="recipient-meta">Student ID: <?= htmlspecialchars($certificate['studentId'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                </section>

                <section class="certificate-statement">
                    has successfully attained the university milestone of
                    <strong><?= (int) ($certificate['milestone_hours'] ?? 0) ?> approved merit hours</strong>.
                    This certificate is granted in recognition of sustained participation and verified contribution
                    to co-curricular development at Universiti Tunku Abdul Rahman.
                </section>

                <section class="certificate-meta-grid">
                    <div class="certificate-meta-card">
                        <p class="certificate-meta-label">Certificate Number</p>
                        <p class="certificate-meta-value"><?= htmlspecialchars($certificate['certificate_code'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="certificate-meta-card">
                        <p class="certificate-meta-label">Issued Date</p>
                        <p class="certificate-meta-value"><?= htmlspecialchars($issuedLabel, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="certificate-meta-card">
                        <p class="certificate-meta-label">Approved Hours Snapshot</p>
                        <p class="certificate-meta-value"><?= (int) ($certificate['approved_hours_snapshot'] ?? 0) ?> hours</p>
                    </div>
                </section>

                <div class="certificate-verify">
                    Verification: <a class="link" href="<?= htmlspecialchars($verificationUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= htmlspecialchars($verificationUrl, ENT_QUOTES, 'UTF-8') ?></a>
                </div>

                <section class="certificate-signature-grid">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <p class="signature-name">Director</p>
                        <p class="signature-role">Division of Student Affairs</p>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <p class="signature-name">Registrar</p>
                        <p class="signature-role">Universiti Tunku Abdul Rahman</p>
                    </div>
                </section>

                <div class="certificate-seal">UTAR<br>VERIFIED</div>
            </article>
        </div>
    </div>
</div>

<?php require "../app/views/layout/footer.php"; ?>
