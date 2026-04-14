<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

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
                    <p class="admin-subtitle">Assign an achievement record to a registered student.</p>
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
                                <input
                                    class="input"
                                    type="text"
                                    id="student-id-input"
                                    name="studentId"
                                    list="student-ids"
                                    placeholder="Start typing student ID..."
                                    value="<?= htmlspecialchars($_POST['studentId'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    autocomplete="off"
                                >
                                <datalist id="student-ids">
                                    <?php foreach ($students as $s): ?>
                                        <option value="<?= htmlspecialchars($s['student_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>

                            <div>
                                <label class="label">Student Email (searchable)</label>
                                <input
                                    class="input"
                                    type="text"
                                    id="student-email-input"
                                    name="studentEmail"
                                    list="student-emails"
                                    placeholder="Start typing email..."
                                    value="<?= htmlspecialchars($_POST['studentEmail'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    autocomplete="off"
                                >
                                <datalist id="student-emails">
                                    <?php foreach ($students as $s): ?>
                                        <option value="<?= htmlspecialchars($s['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>

                            <div>
                                <label class="label">Or Select Student</label>
                                <select class="input" id="student-select" name="studentID">
                                    <option value="">Select student</option>
                                    <?php foreach ($students as $s): ?>
                                        <option
                                            value="<?= htmlspecialchars($s['userID'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            data-student-id="<?= htmlspecialchars($s['student_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            data-email="<?= htmlspecialchars($s['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            data-name="<?= htmlspecialchars($s['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            <?= (string) ($s['userID'] ?? '') === (string) ($_POST['studentID'] ?? '') ? 'selected' : '' ?>
                                        >
                                            <?= htmlspecialchars($s['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            (<?= htmlspecialchars($s['student_id'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            · <?= htmlspecialchars($s['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid" style="margin-top:14px;">
                            <div>
                                <label class="label">Achievement Title</label>
                                <input
                                    class="input"
                                    type="text"
                                    name="title"
                                    placeholder="e.g. Best Volunteer Award"
                                    value="<?= htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div>
                                <label class="label">Achievement Type</label>
                                <?php $achievementType = $_POST['type'] ?? 'Award'; ?>
                                <select class="input" name="type" required>
                                    <option value="Award" <?= $achievementType === 'Award' ? 'selected' : '' ?>>Award</option>
                                    <option value="Certificate" <?= $achievementType === 'Certificate' ? 'selected' : '' ?>>Certificate</option>
                                    <option value="Recognition" <?= $achievementType === 'Recognition' ? 'selected' : '' ?>>Recognition</option>
                                    <option value="Competition" <?= $achievementType === 'Competition' ? 'selected' : '' ?>>Competition</option>
                                    <option value="Leadership" <?= $achievementType === 'Leadership' ? 'selected' : '' ?>>Leadership</option>
                                    <option value="Other" <?= $achievementType === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="label">Date Received</label>
                                <input
                                    class="input"
                                    type="date"
                                    name="dateReceived"
                                    value="<?= htmlspecialchars($_POST['dateReceived'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div>
                                <label class="label">Organizer / Issuer</label>
                                <input
                                    class="input"
                                    type="text"
                                    name="organizer"
                                    placeholder="e.g. UTAR Student Affairs"
                                    value="<?= htmlspecialchars($_POST['organizer'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                >
                            </div>
                        </div>

                        <div style="margin-top:14px;">
                            <label class="label">Description</label>
                            <textarea
                                class="input"
                                name="description"
                                rows="4"
                                placeholder="Describe the achievement."
                            ><?= htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div style="margin-top:14px;">
                            <label class="label">Certificate / Proof File</label>
                            <input
                                class="input"
                                type="file"
                                name="certificate"
                                accept=".pdf,.jpg,.jpeg,.png,.webp"
                            >
                            <div class="muted" style="margin-top:6px;">Optional. Upload supporting proof if available.</div>
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
document.addEventListener('DOMContentLoaded', function () {
    const idInput = document.getElementById('student-id-input');
    const emailInput = document.getElementById('student-email-input');
    const studentSelect = document.getElementById('student-select');

    if (!idInput || !emailInput || !studentSelect) {
        return;
    }

    const students = [];

    Array.from(studentSelect.options).forEach(function (option) {
        if (!option.value) return;

        students.push({
            userId: String(option.value),
            studentId: String(option.dataset.studentId || ''),
            email: String(option.dataset.email || ''),
            name: String(option.dataset.name || '')
        });
    });

    function applyStudent(student) {
        if (!student) return;
        studentSelect.value = student.userId;
        idInput.value = student.studentId;
        emailInput.value = student.email;
    }

    function findByStudentId(studentId) {
        const value = String(studentId || '').trim().toLowerCase();
        if (!value) return null;

        return students.find(function (student) {
            return student.studentId.trim().toLowerCase() === value;
        }) || null;
    }

    function findByEmail(email) {
        const value = String(email || '').trim().toLowerCase();
        if (!value) return null;

        return students.find(function (student) {
            return student.email.trim().toLowerCase() === value;
        }) || null;
    }

    function findByUserId(userId) {
        const value = String(userId || '').trim();
        if (!value) return null;

        return students.find(function (student) {
            return student.userId === value;
        }) || null;
    }

    idInput.addEventListener('input', function () {
        const student = findByStudentId(idInput.value);
        if (student) {
            applyStudent(student);
        }
    });

    idInput.addEventListener('change', function () {
        const student = findByStudentId(idInput.value);
        if (student) {
            applyStudent(student);
        }
    });

    emailInput.addEventListener('input', function () {
        const student = findByEmail(emailInput.value);
        if (student) {
            applyStudent(student);
        }
    });

    emailInput.addEventListener('change', function () {
        const student = findByEmail(emailInput.value);
        if (student) {
            applyStudent(student);
        }
    });

    studentSelect.addEventListener('change', function () {
        const student = findByUserId(studentSelect.value);
        if (student) {
            applyStudent(student);
        }
    });

    if (studentSelect.value) {
        const student = findByUserId(studentSelect.value);
        if (student) {
            applyStudent(student);
        }
    } else if (idInput.value.trim() !== '') {
        const student = findByStudentId(idInput.value);
        if (student) {
            applyStudent(student);
        }
    } else if (emailInput.value.trim() !== '') {
        const student = findByEmail(emailInput.value);
        if (student) {
            applyStudent(student);
        }
    }
});
</script>

<?php require "../app/views/layout/footer.php"; ?>