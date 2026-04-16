<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $selectedStudentID = isset($_POST['studentID']) ? (int) $_POST['studentID'] : 0;
    $selectedStudentIdInput = isset($_POST['studentId']) ? (string) $_POST['studentId'] : '';
    $selectedStudentEmailInput = isset($_POST['studentEmail']) ? (string) $_POST['studentEmail'] : '';
    $selectedEventID = isset($_POST['eventID']) ? (int) $_POST['eventID'] : 0;
    $selectedTitle = trim((string) ($_POST['title'] ?? ''));
    $selectedAchievementLevel = trim((string) ($_POST['achievementLevel'] ?? 'Faculty'));
    $selectedDateReceived = trim((string) ($_POST['dateReceived'] ?? ''));
    $selectedDescription = (string) ($_POST['description'] ?? '');
?>

<div class="main module-page">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Add Achievement Record</div>
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
            <h1 class="admin-title">Create Achievement for Student</h1>
            <p class="admin-subtitle">Assign an achievement under an approved student event.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-secondary" href="index.php?url=achievement/index">Back</a>
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
            <form method="POST" enctype="multipart/form-data" class="form">
                <?php csrf_field(); ?>

                <div class="form-grid">
                    <div>
                        <label class="label">Student ID (searchable)</label>
                        <input class="input" type="text" name="studentId" list="student-ids" placeholder="Start typing student ID..." value="<?= htmlspecialchars($selectedStudentIdInput, ENT_QUOTES, 'UTF-8') ?>">
                        <datalist id="student-ids">
                            <?php foreach ($students as $s): ?>
                                <?php if (!empty($s['isAdmin'])) { continue; } ?>
                                <option value="<?= htmlspecialchars((string) ($s['student_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="label">Student Email (searchable)</label>
                        <input class="input" type="text" name="studentEmail" list="student-emails" placeholder="Start typing email..." value="<?= htmlspecialchars($selectedStudentEmailInput, ENT_QUOTES, 'UTF-8') ?>">
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
                                    data-student-email="<?= htmlspecialchars((string) ($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    <?= (int) ($s['userID'] ?? 0) === $selectedStudentID ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) ($s['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($s['student_id'] ?? '-'), ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars((string) ($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="label">Approved Event</label>
                        <?php
                            $selectedEventCategory = '';
                            foreach ($approvedEvents as $eventRow) {
                                if ((int) ($eventRow['eventID'] ?? 0) === $selectedEventID) {
                                    $selectedEventCategory = trim((string) ($eventRow['eventType'] ?? ''));
                                    break;
                                }
                            }
                        ?>
                        <select class="input" name="eventID" id="achievementEventID" required>
                            <option value="">Select event</option>
                            <?php foreach ($approvedEvents as $ev): ?>
                                <?php $eventId = (int) ($ev['eventID'] ?? 0); ?>
                                <option
                                    value="<?= htmlspecialchars((string) $eventId, ENT_QUOTES, 'UTF-8') ?>"
                                    data-category="<?= htmlspecialchars((string) ($ev['eventType'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $eventId === $selectedEventID ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) ($ev['eventTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($ev['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($ev['userName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($ev['studentId'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="label">Title</label>
                        <select class="input" name="title" required>
                            <option value="">Select title</option>
                            <?php foreach ($achievementTitleOptions as $titleOption): ?>
                                <option value="<?= htmlspecialchars($titleOption, ENT_QUOTES, 'UTF-8') ?>" <?= $selectedTitle === $titleOption ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($titleOption, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="label">Category (Auto from Event)</label>
                        <input class="input" type="text" id="achievementCategoryDisplay" value="<?= htmlspecialchars($selectedEventCategory !== '' ? $selectedEventCategory : '-', ENT_QUOTES, 'UTF-8') ?>" disabled>
                        <input type="hidden" name="category" id="achievementCategoryValue" value="<?= htmlspecialchars($selectedEventCategory, ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div>
                        <label class="label">Achievement Level</label>
                        <select class="input" name="achievementLevel">
                            <option value="Faculty" <?= $selectedAchievementLevel === 'Faculty' ? 'selected' : '' ?>>Faculty</option>
                            <option value="University" <?= $selectedAchievementLevel === 'University' ? 'selected' : '' ?>>University</option>
                            <option value="National" <?= $selectedAchievementLevel === 'National' ? 'selected' : '' ?>>National</option>
                            <option value="International" <?= $selectedAchievementLevel === 'International' ? 'selected' : '' ?>>International</option>
                        </select>
                    </div>

                    <div>
                        <label class="label">Date Received</label>
                        <input class="input" type="date" name="dateReceived" value="<?= htmlspecialchars($selectedDateReceived, ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <label class="label">Description</label>
                    <textarea class="input" name="description" rows="4" placeholder="Add notes or results."><?= htmlspecialchars($selectedDescription, ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div style="margin-top:14px;">
                    <label class="label">Proof Document (Required)</label>
                    <input class="input" type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png" required>
                    <div class="muted" style="margin-top:6px;">Accepted: PDF, JPG, PNG (max 5MB).</div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Save Record</button>
                    <a href="index.php?url=achievement/index" class="btn btn-secondary">Cancel</a>
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

<script>
(function () {
    var eventSelect = document.getElementById('achievementEventID');
    var categoryDisplay = document.getElementById('achievementCategoryDisplay');
    var categoryValue = document.getElementById('achievementCategoryValue');

    if (!eventSelect || !categoryDisplay || !categoryValue) {
        return;
    }

    function syncCategoryFromEvent() {
        var selected = eventSelect.options[eventSelect.selectedIndex];
        var category = selected ? (selected.getAttribute('data-category') || '').trim() : '';
        categoryDisplay.value = category !== '' ? category : '-';
        categoryValue.value = category;
    }

    eventSelect.addEventListener('change', syncCategoryFromEvent);
    syncCategoryFromEvent();
})();
</script>

<?php require "../app/views/layout/footer.php"; ?>
