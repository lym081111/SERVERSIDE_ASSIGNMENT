<?php require "../app/views/layout/header.php"; ?>
<?php require "../app/views/layout/sidebar.php"; ?>

<div class="main module-page">

    <div class="topbar admin-topbar">
        <div class="topbar-left">
            <div class="topbar-title">Club Catalog Management</div>
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
            <div class="admin-eyebrow">Admin Setup</div>
            <h1 class="admin-title">Create Clubs for Students</h1>
            <p class="admin-subtitle">Only admin-created and active clubs can be requested by students.</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-secondary" href="index.php?url=club/index">Back</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success">
            <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error">
            <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Add Club to Catalog</h2>
            <span class="admin-section-chip">Required</span>
        </div>
        <div class="admin-section-body">
            <form method="POST" class="form">
                <?php csrf_field(); ?>

                <div class="form-grid">
                    <div>
                        <label class="label">Club Name</label>
                        <input class="input" type="text" name="clubName" placeholder="e.g. Debate Society" required>
                    </div>
                </div>

                <div style="margin-top:14px;">
                    <label class="label">Description</label>
                    <textarea class="input" name="description" rows="4" placeholder="Short description of the club."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Add Club</button>
                    <a href="index.php?url=club/index" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-section-header">
            <h2 class="admin-section-title">Current Club Catalog</h2>
            <span class="admin-section-chip"><?= is_array($clubCatalog) ? count($clubCatalog) : 0 ?> clubs</span>
        </div>
        <div class="admin-section-body">
            <form method="GET" class="filter-bar" style="margin-bottom:14px;">
                <input type="hidden" name="url" value="club/create">
                <input
                    type="text"
                    name="catalog_search"
                    class="input"
                    placeholder="Search club name or description..."
                    value="<?= isset($_GET['catalog_search']) ? htmlspecialchars((string) $_GET['catalog_search'], ENT_QUOTES, 'UTF-8') : '' ?>">
                <button type="submit" class="btn">Search</button>
                <a href="index.php?url=club/create" class="btn btn-secondary">Reset</a>
            </form>

            <table class="admin-table co-records-table">
                <tr>
                    <th>Club</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>

                <?php if (empty($clubCatalog)): ?>
                    <tr>
                        <td colspan="6" class="muted">No clubs in catalog yet.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($clubCatalog as $clubDef): ?>
                    <?php $isActive = (int) ($clubDef['is_active'] ?? 0) === 1; ?>
                    <tr>
                        <td><?= htmlspecialchars((string) ($clubDef['clubName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($clubDef['description'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if ($isActive): ?>
                                <span class="status-badge approved">Active</span>
                            <?php else: ?>
                                <span class="status-badge rejected">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars((string) ($clubDef['createdByName'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($clubDef['created_at'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <form method="POST" action="index.php?url=club/catalogStatus" style="display:inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="clubCatalogID" value="<?= htmlspecialchars((string) ($clubDef['clubCatalogID'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="is_active" value="<?= $isActive ? '0' : '1' ?>">
                                <button type="submit" class="btn btn-secondary">
                                    <?= $isActive ? 'Set Inactive' : 'Set Active' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

        </div>
    </div>

</div>

<?php require "../app/views/layout/footer.php"; ?>
