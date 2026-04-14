<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

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
<p class="admin-subtitle">Search a student and log merit activity on their behalf.</p>
</div>

<div class="admin-hero-actions">
<a class="btn btn-secondary" href="index.php?url=merit/index">Back</a>
</div>
</div>

<?php if(isset($error)): ?>
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

<!-- hidden userID for backend -->
<input type="hidden" id="hidden-studentID" name="studentID">

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

<select class="input" id="student-select">

<option value="">Select student</option>

<?php foreach ($students as $s): ?>

<option
value="<?= htmlspecialchars($s['userID'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
data-student-id="<?= htmlspecialchars($s['student_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
data-email="<?= htmlspecialchars($s['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
<label class="label">Activity Name</label>

<input
class="input"
type="text"
name="activityName"
placeholder="e.g. Volunteer Program"
required
>

</div>


<div>
<label class="label">Contribution Hours</label>

<input
class="input"
type="number"
name="hours"
step="0.01"
min="0.01"
placeholder="e.g. 2"
required
>

</div>


<div>
<label class="label">Date From</label>

<input
class="input"
type="date"
name="dateFrom"
required
>

</div>


<div>
<label class="label">Date To</label>

<input
class="input"
type="date"
name="dateTo"
>

<div class="muted" style="margin-top:6px;">
Optional for one-day activity.
</div>

</div>

</div>


<div style="margin-top:14px;">

<label class="label">Description</label>

<textarea
class="input"
name="description"
rows="4"
placeholder="Describe the activity or contribution."
></textarea>

</div>


<div class="form-actions">

<button type="submit" class="btn">
Save Record
</button>

<a href="index.php?url=merit/index" class="btn btn-secondary">
Cancel
</a>

</div>

</form>

</div>
</div>

</div>
</div>

</div>


<script>

document.addEventListener("DOMContentLoaded", function(){

const idInput = document.getElementById("student-id-input");
const emailInput = document.getElementById("student-email-input");
const select = document.getElementById("student-select");
const hiddenID = document.getElementById("hidden-studentID");

const students = [];

Array.from(select.options).forEach(function(option){

if(option.value === "") return;

students.push({

userId: option.value,
studentId: option.dataset.studentId,
email: option.dataset.email

});

});


function fillStudent(student){

if(!student) return;

hiddenID.value = student.userId;
select.value = student.userId;

idInput.value = student.studentId;
emailInput.value = student.email;

}


function findById(id){

return students.find(s => s.studentId === id);

}


function findByEmail(email){

return students.find(s => s.email.toLowerCase() === email.toLowerCase());

}


function findByUserId(id){

return students.find(s => s.userId === id);

}


idInput.addEventListener("change", function(){

fillStudent(findById(idInput.value));

});


emailInput.addEventListener("change", function(){

fillStudent(findByEmail(emailInput.value));

});


select.addEventListener("change", function(){

fillStudent(findByUserId(select.value));

});


document.querySelector("form").addEventListener("submit", function(){

if(select.value){
hiddenID.value = select.value;
}

});

});

</script>


<?php require "../app/views/layout/footer.php"; ?>