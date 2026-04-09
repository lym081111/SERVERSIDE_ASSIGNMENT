<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>
<?php
    $logoUrl = BASE_URL . 'assets/utar_logo.svg';
    $issuedLabel = date('d F Y, h:i A', strtotime((string) $issuedAt));
    $registrationDate = !empty($student['created_at']) && strtotime((string) $student['created_at']) !== false
        ? date('d F Y', strtotime((string) $student['created_at']))
        : '-';
    $periodFromLabel = ($activityPeriodFrom !== null && strtotime((string) $activityPeriodFrom) !== false)
        ? date('d M Y', strtotime((string) $activityPeriodFrom))
        : '-';
    $periodToLabel = ($activityPeriodTo !== null && strtotime((string) $activityPeriodTo) !== false)
        ? date('d M Y', strtotime((string) $activityPeriodTo))
        : '-';
?>

<style>
    .transcript-stage {
        max-width: 1180px;
        margin: 0 auto;
    }

    .transcript-paper {
        background: #fffefa;
        border: 3px solid #102a6a;
        border-radius: 14px;
        box-shadow: 0 24px 45px rgba(15, 23, 42, 0.14);
        padding: 26px 28px 30px;
    }

    .transcript-head {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 20px;
        align-items: center;
        border-bottom: 2px solid rgba(16, 42, 106, 0.25);
        padding-bottom: 16px;
        margin-bottom: 14px;
    }

    .transcript-logo {
        width: 320px;
        max-width: 100%;
        border: 1px solid rgba(15, 23, 42, 0.12);
        border-radius: 8px;
        background: #fff;
    }

    .transcript-title-wrap h1 {
        margin: 0;
        font-family: "Times New Roman", Georgia, serif;
        font-size: 2.05rem;
        color: #0f2a7a;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .transcript-title-wrap p {
        margin: 5px 0 0;
        color: #334155;
        line-height: 1.45;
    }

    .transcript-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .transcript-chip {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 999px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #0f172a;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .transcript-info {
        margin-top: 14px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .transcript-info-card {
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        background: #ffffff;
        padding: 10px 12px;
    }

    .transcript-info-label {
        margin: 0;
        font-size: 0.8rem;
        color: #475569;
    }

    .transcript-info-value {
        margin: 5px 0 0;
        color: #0f172a;
        font-weight: 800;
    }

    .transcript-summary {
        margin-top: 14px;
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 10px;
    }

    .transcript-summary-card {
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        background: #f8fbff;
        padding: 10px 12px;
    }

    .transcript-summary-card h3 {
        margin: 0;
        font-size: 0.84rem;
        color: #334155;
    }

    .transcript-summary-card p {
        margin: 6px 0 0;
        font-size: 1.2rem;
        font-weight: 800;
        color: #102a6a;
    }

    .transcript-section {
        margin-top: 16px;
    }

    .transcript-section h2 {
        margin: 0 0 8px;
        font-size: 1.1rem;
        color: #0f172a;
    }

    .transcript-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        overflow: hidden;
        background: #ffffff;
    }

    .transcript-table th,
    .transcript-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
        text-align: left;
        font-size: 0.93rem;
    }

    .transcript-table th {
        background: #f1f5f9;
        color: #0f172a;
        font-weight: 800;
    }

    .transcript-table tr:last-child td {
        border-bottom: none;
    }

    .transcript-footer {
        margin-top: 18px;
        border-top: 2px solid rgba(16, 42, 106, 0.22);
        padding-top: 14px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 28px;
    }

    .transcript-sign-box {
        text-align: center;
    }

    .transcript-sign-line {
        height: 1px;
        background: #0f172a;
        margin-bottom: 8px;
    }

    .transcript-note {
        margin-top: 12px;
        font-size: 0.86rem;
        color: #475569;
        line-height: 1.5;
    }

    @media (max-width: 980px) {
        .transcript-head {
            grid-template-columns: 1fr;
        }
        .transcript-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .transcript-info {
            grid-template-columns: 1fr;
        }
        .transcript-footer {
            grid-template-columns: 1fr;
        }
    }

    @media print {
        .topbar,
        .sidebar {
            display: none !important;
        }

        .main {
            margin-left: 0 !important;
        }

        .content {
            padding: 0 !important;
        }

        .transcript-paper {
            box-shadow: none !important;
            border-width: 2px;
            border-radius: 0;
        }
    }
</style>

<div class="main">
    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title">Official Co-Curricular Transcript</div>
            <div class="topbar-user-inline"><?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
        <div class="topbar-actions">
            <button class="btn btn-secondary" type="button" onclick="window.print()">Print Transcript</button>
            <a class="btn btn-secondary" href="index.php?url=dashboard/index">Back to Dashboard</a>
        </div>
    </div>

    <div class="content">
        <div class="content-inner transcript-stage">
            <article class="transcript-paper">
                <header class="transcript-head">
                    <img src="<?= htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="UTAR Logo" class="transcript-logo">
                    <div class="transcript-title-wrap">
                        <h1>Official Co-Curricular Transcript</h1>
                        <p>Universiti Tunku Abdul Rahman</p>
                        <p>Division of Student Affairs and Co-Curricular Development</p>
                        <div class="transcript-badges">
                            <span class="transcript-chip">Transcript No: <?= htmlspecialchars($transcriptNumber, ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="transcript-chip">Issued: <?= htmlspecialchars($issuedLabel, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                </header>

                <section class="transcript-info">
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Student Name</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($student['name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Student ID</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($student['student_id'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Email</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($student['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Registration Date</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($registrationDate, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Activity Period (From)</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($periodFromLabel, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="transcript-info-card">
                        <p class="transcript-info-label">Activity Period (To)</p>
                        <p class="transcript-info-value"><?= htmlspecialchars($periodToLabel, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </section>

                <section class="transcript-summary">
                    <div class="transcript-summary-card"><h3>Approved Merits</h3><p><?= (int) ($summary['merits'] ?? 0) ?></p></div>
                    <div class="transcript-summary-card"><h3>Approved Events</h3><p><?= (int) ($summary['events'] ?? 0) ?></p></div>
                    <div class="transcript-summary-card"><h3>Approved Clubs</h3><p><?= (int) ($summary['clubs'] ?? 0) ?></p></div>
                    <div class="transcript-summary-card"><h3>Approved Achievements</h3><p><?= (int) ($summary['achievements'] ?? 0) ?></p></div>
                    <div class="transcript-summary-card"><h3>Approved Merit Hours</h3><p><?= (int) round((float) ($summary['merit_hours'] ?? 0)) ?></p></div>
                </section>

                <section class="transcript-section">
                    <h2>Merit Records (Approved)</h2>
                    <table class="transcript-table">
                        <tr>
                            <th>No.</th>
                            <th>Activity</th>
                            <th>Hours</th>
                            <th>Date From</th>
                            <th>Date To</th>
                        </tr>
                        <?php if (empty($approvedMerits)): ?>
                            <tr><td colspan="5">No approved merit records.</td></tr>
                        <?php else: ?>
                            <?php foreach ($approvedMerits as $index => $row): ?>
                                <tr>
                                    <td><?= (int) ($index + 1) ?></td>
                                    <td><?= htmlspecialchars($row['activityName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars((string) ($row['hours'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(($row['dateFrom'] ?? '') !== '' ? (string) $row['dateFrom'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(($row['dateTo'] ?? '') !== '' ? (string) $row['dateTo'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </table>
                </section>

                <section class="transcript-section">
                    <h2>Event Records (Approved)</h2>
                    <table class="transcript-table">
                        <tr>
                            <th>No.</th>
                            <th>Event</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Location</th>
                        </tr>
                        <?php if (empty($approvedEvents)): ?>
                            <tr><td colspan="5">No approved event records.</td></tr>
                        <?php else: ?>
                            <?php foreach ($approvedEvents as $index => $row): ?>
                                <tr>
                                    <td><?= (int) ($index + 1) ?></td>
                                    <td><?= htmlspecialchars($row['eventTitle'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['eventType'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(($row['eventDate'] ?? '') !== '' ? (string) $row['eventDate'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['location'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </table>
                </section>

                <section class="transcript-section">
                    <h2>Club Records (Approved)</h2>
                    <table class="transcript-table">
                        <tr>
                            <th>No.</th>
                            <th>Club</th>
                            <th>Role</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                        <?php if (empty($approvedClubs)): ?>
                            <tr><td colspan="5">No approved club records.</td></tr>
                        <?php else: ?>
                            <?php foreach ($approvedClubs as $index => $row): ?>
                                <tr>
                                    <td><?= (int) ($index + 1) ?></td>
                                    <td><?= htmlspecialchars($row['clubName'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['role'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(($row['startDate'] ?? '') !== '' ? (string) $row['startDate'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(($row['endDate'] ?? '') !== '' ? (string) $row['endDate'] : 'Present', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </table>
                </section>

                <section class="transcript-section">
                    <h2>Achievement Records (Approved)</h2>
                    <table class="transcript-table">
                        <tr>
                            <th>No.</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Level</th>
                            <th>Date Received</th>
                        </tr>
                        <?php if (empty($approvedAchievements)): ?>
                            <tr><td colspan="5">No approved achievement records.</td></tr>
                        <?php else: ?>
                            <?php foreach ($approvedAchievements as $index => $row): ?>
                                <tr>
                                    <td><?= (int) ($index + 1) ?></td>
                                    <td><?= htmlspecialchars($row['title'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['category'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($row['achievementLevel'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(($row['dateReceived'] ?? '') !== '' ? (string) $row['dateReceived'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </table>
                </section>

                <section class="transcript-footer">
                    <div class="transcript-sign-box">
                        <div class="transcript-sign-line"></div>
                        <strong>Director</strong>
                        <div class="muted">Division of Student Affairs</div>
                    </div>
                    <div class="transcript-sign-box">
                        <div class="transcript-sign-line"></div>
                        <strong>Registrar</strong>
                        <div class="muted">Universiti Tunku Abdul Rahman</div>
                    </div>
                </section>

                <div class="transcript-note">
                    This transcript is system-generated and reflects approved co-curricular records as of the issue timestamp above.
                    Unapproved (pending/rejected) submissions are excluded from this official summary.
                </div>
            </article>
        </div>
    </div>
</div>

<?php require "../app/views/layout/footer.php"; ?>
