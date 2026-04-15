<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main">

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

    <div class="page-header">
        <div>
            <h2 style="margin:0;">Add New Event Record</h2>
            <div class="muted" style="margin-top:6px;">Create an event under your approved club membership.</div>
        </div>
        <div class="page-actions">
            <a class="btn btn-secondary" href="index.php?url=event/index">Back</a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($clubCatalog)): ?>
        <div class="card">
            <div class="muted">You need at least one approved active club membership before creating events.</div>
        </div>
    <?php else: ?>

    <div class="card">

        <form method="POST" enctype="multipart/form-data" class="form">
            <?php csrf_field(); ?>

            <div class="form-grid">
                <div>
                    <label class="label">Club</label>
                    <select class="input" name="clubCatalogID" required>
                        <option value="">Select club</option>
                        <?php foreach ($clubCatalog as $clubDef): ?>
                            <option value="<?= htmlspecialchars((string) ($clubDef['clubCatalogID'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars((string) ($clubDef['clubName'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="label">Event Title</label>
                    <input class="input" type="text" name="eventTitle" placeholder="e.g. UTAR Hackathon" required>
                </div>

                <div>
                    <label class="label">Event Type</label>
                    <select class="input" name="eventType">
                        <option value="Leadership">Leadership</option>
                        <option value="Volunteerism">Volunteerism</option>
                        <option value="Academic">Academic</option>
                        <option value="Technical">Technical</option>
                        <option value="Sports">Sports</option>
                        <option value="Community">Community</option>
                    </select>
                </div>

                <div>
                    <label class="label">Event Date</label>
                    <input class="input" type="date" name="eventDate" required>
                </div>

                <div>
                    <label class="label">Event Hours</label>
                    <input class="input" type="number" name="eventHours" step="0.01" min="0.01" placeholder="e.g. 2" required>
                    <div class="muted" style="margin-top:6px;">This value will be used by merit records linked to this event.</div>
                </div>

                <div>
                    <label class="label">Location</label>
                    <input class="input" type="text" name="location" placeholder="e.g. Main Hall">
                </div>

                <div>
                    <label class="label">Participant Capacity (optional)</label>
                    <input class="input" type="number" name="participantCapacity" min="1" step="1" placeholder="Leave empty for unlimited">
                    <div class="muted" style="margin-top:6px;">Registered count is auto-calculated from approved participant records.</div>
                </div>

                <div style="display:flex;align-items:center;gap:8px;padding-top:28px;">
                    <input type="checkbox" id="waitlistEnabled" name="waitlistEnabled" value="1" checked>
                    <label class="label" for="waitlistEnabled" style="margin:0;">Enable waitlist when full</label>
                </div>
            </div>

            <div style="margin-top:14px;">
                <label class="label">Description</label>
                <textarea class="input" name="description" rows="4" placeholder="Add notes or a short summary."></textarea>
            </div>

            <div style="margin-top:14px;">
                <label class="label">Reflection (What did you learn?)</label>
                <textarea class="input" name="reflection" rows="4" placeholder="Example: I improved teamwork and communication by coordinating with 30 participants."></textarea>
            </div>

            <div class="form-actions">
                <div style="width:100%;">
                    <label class="label">Proof Document (Optional)</label>
                    <input class="input" type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="muted" style="margin-top:6px;">Accepted: PDF, JPG, PNG (max 5MB).</div>
                </div>
                <button type="submit" class="btn">Save Record</button>
                <a href="index.php?url=event/index" class="btn btn-secondary">Cancel</a>
            </div>

        </form>

    </div>

    <?php endif; ?>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
