<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<?php
    $selectedStudentID = isset($_POST['studentID']) ? (int) $_POST['studentID'] : 0;
    $selectedStudentIdInput = isset($_POST['studentId']) ? (string) $_POST['studentId'] : '';
    $selectedStudentEmailInput = isset($_POST['studentEmail']) ? (string) $_POST['studentEmail'] : '';
    $selectedEventID = isset($_POST['eventID']) ? (int) $_POST['eventID'] : 0;
    $selectedAchievementID = isset($_POST['achievementID']) ? (int) $_POST['achievementID'] : 0;
    $resolveAchievementMeta = function ($title) {
        $normalized = strtolower(trim((string) $title));
        $rankLabel = 'Participant / Certificate';
        $bonus = 5;

        if (in_array($normalized, ['1st prize', 'champion', 'gold medal', 'best performer', 'special award'], true)) {
            $rankLabel = '1st Prize';
            $bonus = 15;
        } elseif (in_array($normalized, ['2nd prize', 'runner-up', 'silver medal'], true)) {
            $rankLabel = '2nd Prize';
            $bonus = 12;
        } elseif (in_array($normalized, ['3rd prize', 'bronze medal', 'finalist'], true)) {
            $rankLabel = '3rd Prize';
            $bonus = 10;
        } elseif (in_array($normalized, ['consolation prize', 'honorable mention', 'semi-finalist'], true)) {
            $rankLabel = 'Consolation';
            $bonus = 7;
        }

        return ['rankLabel' => $rankLabel, 'bonus' => $bonus];
    };
?>

<div class="main module-page">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Add Merit Record</div>
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
            <h1 class="admin-title">Create Merit for Student</h1>
            <p class="admin-subtitle">Select a student, link an approved event, then optionally apply achievement bonus points.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-secondary" href="index.php?url=merit/index">Back</a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Student and Merit Selection</h2>
            <span class="admin-section-chip">Required</span>
        </div>
        <div class="admin-section-body">
            <form method="POST" enctype="multipart/form-data" class="form" id="adminMeritEventForm">
                <?php csrf_field(); ?>

                <div class="form-grid">
                    <div>
                        <label class="label">Student ID (searchable)</label>
                        <input class="input" type="text" name="studentId" id="studentIdInput" list="student-ids" placeholder="Start typing student ID..." value="<?= htmlspecialchars($selectedStudentIdInput, ENT_QUOTES, 'UTF-8') ?>">
                        <datalist id="student-ids">
                            <?php foreach ($students as $s): ?>
                                <?php if (!empty($s['isAdmin'])) { continue; } ?>
                                <option value="<?= htmlspecialchars((string) ($s['student_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="label">Student Email (searchable)</label>
                        <input class="input" type="text" name="studentEmail" id="studentEmailInput" list="student-emails" placeholder="Start typing email..." value="<?= htmlspecialchars($selectedStudentEmailInput, ENT_QUOTES, 'UTF-8') ?>">
                        <datalist id="student-emails">
                            <?php foreach ($students as $s): ?>
                                <?php if (!empty($s['isAdmin'])) { continue; } ?>
                                <option value="<?= htmlspecialchars((string) ($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="label">Or Select Student</label>
                        <select class="input" name="studentID" id="studentIDSelect">
                            <option value="">Select student</option>
                            <?php foreach ($students as $s): ?>
                                <?php if (!empty($s['isAdmin'])) { continue; } ?>
                                <?php $studentUserID = (int) ($s['userID'] ?? 0); ?>
                                <option
                                    value="<?= htmlspecialchars((string) $studentUserID, ENT_QUOTES, 'UTF-8') ?>"
                                    data-student-id="<?= htmlspecialchars((string) ($s['student_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-student-email="<?= htmlspecialchars((string) ($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $studentUserID === $selectedStudentID ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) ($s['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($s['student_id'] ?? '-'), ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars((string) ($s['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-grid" style="margin-top:14px;">
                    <div>
                        <label class="label">Approved Event</label>
                        <select class="input" name="eventID" id="eventID" required>
                            <option value="">Select event</option>
                            <?php foreach ($approvedEvents as $ev): ?>
                                <?php $eventId = (int) ($ev['eventID'] ?? 0); ?>
                                <option
                                    value="<?= htmlspecialchars((string) $eventId, ENT_QUOTES, 'UTF-8') ?>"
                                    data-owner-id="<?= htmlspecialchars((string) ((int) ($ev['userID'] ?? 0)), ENT_QUOTES, 'UTF-8') ?>"
                                    data-title="<?= htmlspecialchars((string) ($ev['eventTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-hours="<?= htmlspecialchars((string) ($ev['eventHours'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-date="<?= htmlspecialchars((string) ($ev['eventDate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    data-club="<?= htmlspecialchars((string) ($ev['clubName'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $eventId === $selectedEventID ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) ($ev['eventTitle'] ?? ''), ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars((string) ($ev['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($ev['userName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($ev['studentId'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="label">Achievement (Auto from Event)</label>
                        <input class="input" type="text" id="achievementDisplay" value="-" readonly>
                        <input type="hidden" name="achievementID" id="achievementID" value="<?= htmlspecialchars((string) $selectedAchievementID, ENT_QUOTES, 'UTF-8') ?>">
                        <select class="input" id="achievementPool" style="display:none;">
                            <option value="">No achievement bonus</option>
                            <?php foreach ($approvedAchievements as $ac): ?>
                                <?php
                                    $achievementId = (int) ($ac['achievementID'] ?? 0);
                                    $achievementOwnerID = (int) ($ac['userID'] ?? 0);
                                    $achievementEventID = (int) ($ac['eventID'] ?? 0);
                                    $achievementTitle = trim((string) ($ac['title'] ?? 'Achievement'));
                                    $achievementOwnerName = trim((string) ($ac['userName'] ?? ''));
                                    $achievementDate = trim((string) ($ac['dateReceived'] ?? ''));
                                    $achievementMeta = $resolveAchievementMeta($achievementTitle);
                                ?>
                                <option
                                    value="<?= htmlspecialchars((string) $achievementId, ENT_QUOTES, 'UTF-8') ?>"
                                    data-owner-id="<?= htmlspecialchars((string) $achievementOwnerID, ENT_QUOTES, 'UTF-8') ?>"
                                    data-event-id="<?= htmlspecialchars((string) $achievementEventID, ENT_QUOTES, 'UTF-8') ?>"
                                    data-rank-label="<?= htmlspecialchars((string) ($achievementMeta['rankLabel'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>"
                                    data-bonus="<?= htmlspecialchars((string) ((int) ($achievementMeta['bonus'] ?? 0)), ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $achievementId === $selectedAchievementID ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($achievementTitle, ENT_QUOTES, 'UTF-8') ?><?= $achievementOwnerName !== '' ? ' - ' . htmlspecialchars($achievementOwnerName, ENT_QUOTES, 'UTF-8') : '' ?><?= $achievementDate !== '' ? ' (' . htmlspecialchars($achievementDate, ENT_QUOTES, 'UTF-8') . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="label">Achievement Result (Auto)</label>
                        <input class="input" type="text" id="achievementResultDisplay" value="-" readonly>
                    </div>

                    <div>
                        <label class="label">Club</label>
                        <input class="input" type="text" id="clubDisplay" value="-" readonly>
                    </div>

                    <div>
                        <label class="label">Activity Name (From Event)</label>
                        <input class="input" type="text" id="activityDisplay" value="-" readonly>
                    </div>

                    <div>
                        <label class="label">Base Merit Points (From Event)</label>
                        <input class="input" type="text" id="hoursDisplay" value="-" readonly>
                    </div>

                    <div>
                        <label class="label">Achievement Bonus Points</label>
                        <input class="input" type="text" id="bonusDisplay" value="0" readonly>
                    </div>

                    <div>
                        <label class="label">Total Merit Points</label>
                        <input class="input" type="text" id="totalPointsDisplay" value="-" readonly>
                    </div>

                    <div>
                        <label class="label">Date From</label>
                        <input class="input" type="text" id="dateFromDisplay" value="-" readonly>
                    </div>

                    <div>
                        <label class="label">Date To</label>
                        <input class="input" type="text" id="dateToDisplay" value="-" readonly>
                    </div>
                </div>

                <div class="form-actions">
                    <div style="width:100%;">
                        <label class="label">Proof Document (Optional)</label>
                        <input class="input" type="file" name="evidence_file" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="muted" style="margin-top:6px;">Accepted: PDF, JPG, PNG (max 5MB).</div>
                    </div>
                    <button type="submit" class="btn">Save Record</button>
                    <a href="index.php?url=merit/index" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

        </div>
    </div>

</div>

<script>
(function () {
    var studentIdInput = document.getElementById('studentIdInput');
    var studentEmailInput = document.getElementById('studentEmailInput');
    var studentSelect = document.getElementById('studentIDSelect');
    var eventSelect = document.getElementById('eventID');
    var achievementIdInput = document.getElementById('achievementID');
    var achievementPool = document.getElementById('achievementPool');
    var achievementDisplay = document.getElementById('achievementDisplay');
    var achievementResultDisplay = document.getElementById('achievementResultDisplay');

    var clubDisplay = document.getElementById('clubDisplay');
    var activityDisplay = document.getElementById('activityDisplay');
    var hoursDisplay = document.getElementById('hoursDisplay');
    var bonusDisplay = document.getElementById('bonusDisplay');
    var totalPointsDisplay = document.getElementById('totalPointsDisplay');
    var dateFromDisplay = document.getElementById('dateFromDisplay');
    var dateToDisplay = document.getElementById('dateToDisplay');

    if (!studentIdInput || !studentEmailInput || !studentSelect || !eventSelect || !achievementIdInput || !achievementPool || !achievementDisplay || !achievementResultDisplay || !clubDisplay || !activityDisplay || !hoursDisplay || !bonusDisplay || !totalPointsDisplay || !dateFromDisplay || !dateToDisplay) {
        return;
    }

    var eventOptions = Array.prototype.slice.call(eventSelect.options || []);
    var achievementOptions = Array.prototype.slice.call(achievementPool.options || []);

    var records = Array.prototype.map.call(studentSelect.options, function (option) {
        if (!option.value) {
            return null;
        }
        return {
            userID: option.value.trim(),
            studentId: (option.getAttribute('data-student-id') || '').trim(),
            email: (option.getAttribute('data-student-email') || '').trim()
        };
    }).filter(function (item) {
        return item && item.userID !== '';
    });

    function parseIntSafe(value) {
        var n = parseInt(value, 10);
        return isNaN(n) ? 0 : n;
    }

    function findRecordByUserID(userID) {
        var key = (userID || '').trim();
        if (!key) {
            return null;
        }
        return records.find(function (record) {
            return record.userID === key;
        }) || null;
    }

    function findRecordByStudentId(studentId) {
        var key = (studentId || '').trim().toLowerCase();
        if (!key) {
            return null;
        }
        return records.find(function (record) {
            return record.studentId.trim().toLowerCase() === key;
        }) || null;
    }

    function findRecordByEmail(email) {
        var key = (email || '').trim().toLowerCase();
        if (!key) {
            return null;
        }
        return records.find(function (record) {
            return record.email.trim().toLowerCase() === key;
        }) || null;
    }

    function selectedStudentUserID() {
        return (studentSelect.value || '').trim();
    }

    function syncEventPreview() {
        var opt = eventSelect.options[eventSelect.selectedIndex];
        if (!opt || !opt.value) {
            clubDisplay.value = '-';
            activityDisplay.value = '-';
            hoursDisplay.value = '-';
            dateFromDisplay.value = '-';
            dateToDisplay.value = '-';
            bonusDisplay.value = '0';
            totalPointsDisplay.value = '-';
            achievementIdInput.value = '';
            achievementDisplay.value = '-';
            achievementResultDisplay.value = '-';
            return;
        }

        clubDisplay.value = opt.getAttribute('data-club') || '-';
        activityDisplay.value = opt.getAttribute('data-title') || '-';
        hoursDisplay.value = opt.getAttribute('data-hours') || '-';
        dateFromDisplay.value = opt.getAttribute('data-date') || '-';
        dateToDisplay.value = opt.getAttribute('data-date') || '-';
        syncTotalPoints();
    }

    function resolveAchievementForContext() {
        var ownerId = selectedStudentUserID();
        var eventId = (eventSelect.value || '').trim();
        if (!eventId) {
            achievementIdInput.value = '';
            achievementDisplay.value = '-';
            achievementResultDisplay.value = '-';
            return 0;
        }

        var currentId = (achievementIdInput.value || '').trim();
        var chosen = null;

        if (currentId) {
            chosen = achievementOptions.find(function (option) {
                if (!option || !option.value || option.value !== currentId) {
                    return false;
                }
                var optionOwner = (option.getAttribute('data-owner-id') || '').trim();
                var optionEvent = (option.getAttribute('data-event-id') || '').trim();
                return optionEvent === eventId && (ownerId === '' || optionOwner === ownerId);
            }) || null;
        }

        if (!chosen) {
            chosen = achievementOptions.find(function (option) {
                if (!option || !option.value) {
                    return false;
                }
                var optionOwner = (option.getAttribute('data-owner-id') || '').trim();
                var optionEvent = (option.getAttribute('data-event-id') || '').trim();
                return optionEvent === eventId && (ownerId === '' || optionOwner === ownerId);
            }) || null;
        }

        if (!chosen) {
            achievementIdInput.value = '';
            achievementDisplay.value = 'No approved achievement for this event';
            achievementResultDisplay.value = '-';
            return 0;
        }

        achievementIdInput.value = chosen.value;
        achievementDisplay.value = chosen.text || '-';
        achievementResultDisplay.value = chosen.getAttribute('data-rank-label') || '-';
        return parseIntSafe(chosen.getAttribute('data-bonus') || '0');
    }

    function syncTotalPoints() {
        var base = parseIntSafe(hoursDisplay.value);
        var bonus = resolveAchievementForContext();
        bonusDisplay.value = String(bonus);
        if (base > 0) {
            totalPointsDisplay.value = String(base + bonus);
        } else {
            totalPointsDisplay.value = '-';
        }
    }

    function filterEventsForStudent() {
        var selectedEventValue = eventSelect.value;
        var ownerId = selectedStudentUserID();

        eventOptions.forEach(function (option) {
            if (!option.value) {
                option.disabled = false;
                option.hidden = false;
                return;
            }
            var optionOwner = (option.getAttribute('data-owner-id') || '').trim();
            var visible = ownerId === '' || optionOwner === ownerId;
            option.disabled = !visible;
            option.hidden = !visible;
        });

        if (selectedEventValue) {
            var selectedOption = Array.prototype.find.call(eventSelect.options, function (option) {
                return option.value === selectedEventValue && !option.disabled;
            });
            if (!selectedOption) {
                eventSelect.value = '';
            }
        }

        syncEventPreview();
    }

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

        filterEventsForStudent();
        syncTotalPoints();
    }

    function onStudentFieldChanged(source) {
        if (source === 'select') {
            applyStudent(findRecordByUserID(studentSelect.value), source);
            return;
        }
        if (source === 'id') {
            applyStudent(findRecordByStudentId(studentIdInput.value), source);
            return;
        }
        applyStudent(findRecordByEmail(studentEmailInput.value), source);
    }

    studentSelect.addEventListener('change', function () {
        onStudentFieldChanged('select');
    });

    ['input', 'change', 'blur'].forEach(function (eventName) {
        studentIdInput.addEventListener(eventName, function () {
            onStudentFieldChanged('id');
        });
        studentEmailInput.addEventListener(eventName, function () {
            onStudentFieldChanged('email');
        });
    });

    eventSelect.addEventListener('change', function () {
        syncEventPreview();
        syncTotalPoints();
    });

    if (studentSelect.value) {
        applyStudent(findRecordByUserID(studentSelect.value), 'select');
    } else if (studentIdInput.value.trim() !== '') {
        applyStudent(findRecordByStudentId(studentIdInput.value), 'id');
    } else if (studentEmailInput.value.trim() !== '') {
        applyStudent(findRecordByEmail(studentEmailInput.value), 'email');
    } else {
        filterEventsForStudent();
        syncTotalPoints();
    }

    syncEventPreview();
    syncTotalPoints();
})();
</script>

<?php require "../app/views/layout/footer.php"; ?>
