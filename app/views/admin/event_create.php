<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main module-page">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Add Event Record</div>
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
            <div class="admin-eyebrow">Admin Entry</div>
            <h1 class="admin-title">Create Event for Student</h1>
            <p class="admin-subtitle">Assign an event to a student under a club they joined.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-secondary" href="index.php?url=event/index">Back</a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Student Selection</h2>
            <span class="admin-section-chip">Required</span>
        </div>
        <div class="admin-section-body">
            <form method="POST" class="form">
                <?php csrf_field(); ?>

                <div class="form-grid">
                    <div>
                        <label class="label">Student ID (searchable)</label>
                        <input class="input" type="text" name="studentId" list="student-ids" placeholder="Start typing student ID...">
                        <datalist id="student-ids">
                            <?php foreach ($students as $s): ?>
                                <?php if (!empty($s['isAdmin'])) { continue; } ?>
                                <option value="<?= htmlspecialchars((string) ($s['student_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="label">Student Email (searchable)</label>
                        <input class="input" type="text" name="studentEmail" list="student-emails" placeholder="Start typing email...">
                        <datalist id="student-emails">
                            <?php foreach ($students as $s): ?>
                                <?php if (!empty($s['isAdmin'])) { continue; } ?>
                                <option value="<?= htmlspecialchars((string) ($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="label">Or Select Student</label>
                        <select class="input" name="studentID">
                            <option value="">Select student</option>
                            <?php foreach ($students as $s): ?>
                                <?php if (!empty($s['isAdmin'])) { continue; } ?>
                                <option
                                    value="<?= htmlspecialchars((string) ($s['userID'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-student-id="<?= htmlspecialchars((string) ($s['student_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-student-email="<?= htmlspecialchars((string) ($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars((string) ($s['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($s['student_id'] ?? '-'), ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars((string) ($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
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
                    <label class="label">Reflection (Learning Outcome)</label>
                    <textarea class="input" name="reflection" rows="4" placeholder="What skill or insight did the student gain?"></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Save Record</button>
                    <a href="index.php?url=event/index" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

        </div>
    </div>

</div>

<script>
(function () {
    var studentIdInput = document.querySelector('input[name="studentId"]');
    var studentEmailInput = document.querySelector('input[name="studentEmail"]');
    var studentSelect = document.querySelector('select[name="studentID"]');

    if (!studentIdInput || !studentEmailInput || !studentSelect) {
        return;
    }

    var studentRecords = Array.prototype.map.call(studentSelect.options, function (option) {
        if (!option.value) {
            return null;
        }
        return {
            userID: option.value.trim(),
            studentId: (option.getAttribute('data-student-id') || '').trim(),
            email: (option.getAttribute('data-student-email') || '').trim()
        };
    }).filter(function (record) {
        return record && record.userID !== '';
    });

    function applyStudent(record, source) {
        if (!record) {
            return;
        }

        if (source !== 'id') {
            studentIdInput.value = record.studentId;
        }
        if (source !== 'email') {
            studentEmailInput.value = record.email;
        }
        if (source !== 'select') {
            studentSelect.value = record.userID;
        }
    }

    function findByUserID(userID) {
        var key = (userID || '').trim();
        return studentRecords.find(function (record) {
            return record.userID === key;
        }) || null;
    }

    function findByStudentId(studentId) {
        var key = (studentId || '').trim().toLowerCase();
        if (key === '') {
            return null;
        }
        return studentRecords.find(function (record) {
            return record.studentId.trim().toLowerCase() === key;
        }) || null;
    }

    function findByEmail(email) {
        var key = (email || '').trim().toLowerCase();
        if (key === '') {
            return null;
        }
        return studentRecords.find(function (record) {
            return record.email.trim().toLowerCase() === key;
        }) || null;
    }

    studentSelect.addEventListener('change', function () {
        applyStudent(findByUserID(studentSelect.value), 'select');
    });

    ['input', 'change', 'blur'].forEach(function (eventName) {
        studentIdInput.addEventListener(eventName, function () {
            applyStudent(findByStudentId(studentIdInput.value), 'id');
        });
        studentEmailInput.addEventListener(eventName, function () {
            applyStudent(findByEmail(studentEmailInput.value), 'email');
        });
    });

    if (studentSelect.value) {
        applyStudent(findByUserID(studentSelect.value), 'select');
    } else if (studentIdInput.value.trim() !== '') {
        applyStudent(findByStudentId(studentIdInput.value), 'id');
    } else if (studentEmailInput.value.trim() !== '') {
        applyStudent(findByEmail(studentEmailInput.value), 'email');
    }
})();
</script>

<?php require "../app/views/layout/footer.php"; ?>
