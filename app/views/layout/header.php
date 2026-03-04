<!DOCTYPE html>
<html>
<head>
    <title>SCMS Dashboard</title>

    <style>
        :root {
            --bg: #f3f4f6;
            --card: #ffffff;
            --border: #e5e7eb;
            --text: #0f172a;
            --muted: #334155;
            --primary: #2563eb;
            --danger: #ef4444;
            --sidebar-w: 240px;
            --grad: linear-gradient(135deg, #0b1d4d, #1e40af 35%, #3b82f6);
            --sidebar-grad:
                radial-gradient(circle at top left, rgba(191, 219, 254, 0.18), transparent 34%),
                linear-gradient(180deg, #0b1d4d 0%, #12327d 46%, #0f1a35 100%);
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-grad);
            position: fixed;
            color: white;
            box-shadow: 0 18px 50px rgba(2, 6, 23, 0.20);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }

        .sidebar-title {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .sidebar-subtitle {
            margin: 4px 0 0;
            font-size: 0.75rem;
            opacity: 0.92;
        }

        .sidebar-eyebrow {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            opacity: 0.78;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .sidebar-nav {
            padding: 10px 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            font-size: 0.95rem;
            opacity: 0.92;
        }

        .nav-link:hover {
            background: rgba(15,23,42,0.35);
        }

        .nav-link.active {
            background: rgba(15,23,42,0.55);
            position: relative;
        }

        .nav-link.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 8px;
            bottom: 8px;
            width: 3px;
            border-radius: 999px;
            background: #bfdbfe;
        }

        .logout {
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 10px;
        }

        .logout button {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 12px 20px;
            font: inherit;
        }

        .logout button:hover {
            background: rgba(255,255,255,0.12);
        }

        .main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
        }

        .dashboard-sidebar {
            width: 300px;
            background: var(--sidebar-grad);
        }

        .dashboard-sidebar-header {
            padding-bottom: 18px;
        }

        .dashboard-sidebar-body {
            flex: 1;
            overflow-y: auto;
            padding: 18px;
            display: grid;
            gap: 14px;
        }

        .sidebar-panel {
            border-radius: 18px;
            padding: 16px;
            background: rgba(15, 23, 42, 0.26);
            border: 1px solid rgba(255,255,255,0.12);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
            backdrop-filter: blur(8px);
        }

        .sidebar-profile-card {
            display: grid;
            gap: 12px;
        }

        .sidebar-avatar {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.18);
            font-weight: 800;
            letter-spacing: 0.08em;
        }

        .sidebar-profile-name {
            font-size: 1rem;
            font-weight: 800;
        }

        .sidebar-profile-meta {
            margin-top: 4px;
            font-size: 0.86rem;
            color: rgba(255,255,255,0.74);
        }

        .sidebar-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .sidebar-panel-title {
            font-size: 0.88rem;
            font-weight: 800;
            letter-spacing: 0.01em;
        }

        .sidebar-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 9px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 800;
            background: rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.92);
            border: 1px solid rgba(255,255,255,0.14);
        }

        .sidebar-pill-strong {
            width: fit-content;
            background: #dbeafe;
            color: #1e3a8a;
            border-color: transparent;
        }

        .sidebar-metric {
            font-size: 1.1rem;
            font-weight: 800;
            line-height: 1.35;
            margin-bottom: 10px;
        }

        .sidebar-progress-track {
            width: 100%;
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
            background: rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }

        .sidebar-progress-bar {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #60a5fa, #bfdbfe);
        }

        .sidebar-note {
            font-size: 0.86rem;
            line-height: 1.5;
            color: rgba(255,255,255,0.76);
        }

        .sidebar-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }

        .sidebar-stat-grid.quad {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sidebar-stat-card {
            border-radius: 14px;
            padding: 12px 10px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .sidebar-stat-card strong {
            display: block;
            margin-top: 6px;
            font-size: 1rem;
        }

        .sidebar-stat-label {
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.72);
        }

        .sidebar-focus-title {
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .sidebar-link-list {
            display: grid;
            gap: 10px;
        }

        .sidebar-link {
            display: block;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.92);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.16);
        }

        .sidebar-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin-top: 14px;
            padding: 11px 14px;
            border-radius: 14px;
            background: #eff6ff;
            color: #0f172a;
            text-decoration: none;
            font-weight: 800;
            border: 1px solid transparent;
        }

        .sidebar-action:hover {
            background: #dbeafe;
        }

        .sidebar-status-list {
            display: grid;
            gap: 10px;
        }

        .sidebar-status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-status-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .sidebar-status-label {
            font-size: 0.92rem;
            font-weight: 700;
        }

        .sidebar-state {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 800;
        }

        .sidebar-state.active {
            background: #dcfce7;
            color: #166534;
        }

        .sidebar-state.pending {
            background: rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.88);
        }

        .dashboard-sidebar .logout {
            padding: 14px 18px 18px;
            border-top: 1px solid rgba(255,255,255,0.12);
        }

        .dashboard-sidebar .logout button {
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.08);
            padding: 12px 14px;
        }

        .dashboard-sidebar .logout button:hover {
            background: rgba(255,255,255,0.14);
        }

        .dashboard-sidebar ~ .main {
            margin-left: 300px;
        }

        .topbar {
            background: var(--grad);
            color: white;
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-user-inline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.25);
            border: 1px solid rgba(255,255,255,0.18);
            font-size: 0.85rem;
            font-weight: 700;
            width: fit-content;
        }

        .topbar-logout {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.22);
            background: rgba(15, 23, 42, 0.25);
            color: white;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
        }

        .topbar-logout:hover {
            background: rgba(15, 23, 42, 0.4);
        }

        .content {
            padding: 24px;
            overflow-x: auto;
        }

        .content-inner {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .kpi-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px 16px;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.05);
        }

        .kpi-label {
            font-size: 0.85rem;
            color: var(--muted);
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .kpi-value {
            margin-top: 8px;
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1.1;
        }

        .kpi-sub {
            margin-top: 6px;
            font-size: 0.9rem;
            color: var(--muted);
        }

        .split-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 16px;
            align-items: start;
        }

        .side-stack {
            display: grid;
            gap: 16px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .card-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 800;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: #f8fafc;
            color: var(--muted);
            font-weight: 700;
            font-size: 0.85rem;
        }

        .list {
            display: grid;
            gap: 10px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .list-item {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #ffffff;
        }

        .list-item-title {
            font-weight: 800;
            color: var(--text);
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .list-item-sub {
            color: var(--muted);
            font-size: 0.9rem;
            margin-top: 2px;
        }

        .list-item-right {
            text-align: right;
            color: var(--muted);
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: var(--card);
            padding: 18px;
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.06);
        }

        .card h4 {
            margin: 0;
            color: var(--muted);
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .card p {
            font-size: 26px;
            margin: 10px 0 0;
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            background: #0f172a;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            border: 1px solid #0f172a;
            cursor: pointer;
            font: inherit;
        }

        .btn:hover {
            background: #111827;
            border-color: #111827;
        }

        .btn.btn-secondary {
            background: #ffffff;
            color: #0f172a;
            border-color: var(--border);
        }

        .btn.btn-secondary:hover {
            background: #f8fafc;
        }

        .btn-pill {
            border-radius: 999px;
            padding-inline: 18px;
        }

        .page-title {
            font-size: 1.7rem;
            margin: 6px 0 4px;
            font-weight: 800;
        }

        .page-subtitle {
            font-size: 0.95rem;
            color: var(--muted);
            margin-bottom: 22px;
        }

        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 18px;
        }

        .module-card {
            background: var(--card);
            border-radius: 18px;
            padding: 18px 18px 16px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 35px rgba(15,23,42,0.06);
        }

        .module-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .module-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.08em;
        }

        .module-icon.active {
            background: var(--grad);
            color: white;
        }

        .module-icon.pending {
            background: #e2e8f0;
            color: #334155;
        }

        .module-icon.merit {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.16), rgba(30, 64, 175, 0.3));
            color: #1e40af;
            border: 1px solid rgba(37, 99, 235, 0.35);
        }

        .module-icon.event {
            background: linear-gradient(135deg, rgba(14, 116, 144, 0.18), rgba(14, 165, 233, 0.28));
            color: #0e7490;
            border: 1px solid rgba(14, 116, 144, 0.35);
        }

        .module-icon.club {
            background: linear-gradient(135deg, rgba(22, 163, 74, 0.18), rgba(34, 197, 94, 0.25));
            color: #166534;
            border: 1px solid rgba(22, 163, 74, 0.35);
        }

        .module-icon.achievement {
            background: linear-gradient(135deg, rgba(234, 179, 8, 0.2), rgba(251, 191, 36, 0.28));
            color: #92400e;
            border: 1px solid rgba(234, 179, 8, 0.35);
        }

        .module-card.merit {
            border-color: rgba(37, 99, 235, 0.18);
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(255, 255, 255, 0.92) 55%);
        }

        .module-card.event {
            border-color: rgba(14, 116, 144, 0.18);
            background: linear-gradient(135deg, rgba(14, 116, 144, 0.08), rgba(255, 255, 255, 0.92) 55%);
        }

        .module-card.club {
            border-color: rgba(22, 163, 74, 0.18);
            background: linear-gradient(135deg, rgba(22, 163, 74, 0.08), rgba(255, 255, 255, 0.92) 55%);
        }

        .module-card.achievement {
            border-color: rgba(234, 179, 8, 0.2);
            background: linear-gradient(135deg, rgba(234, 179, 8, 0.1), rgba(255, 255, 255, 0.92) 55%);
        }

        .module-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 800;
        }

        .module-status {
            font-size: 0.8rem;
            padding: 4px 10px;
            border-radius: 999px;
            font-weight: 700;
        }

        .module-status.active {
            background: #dcfce7;
            color: #166534;
        }

        .module-status.pending {
            background: #e5e7eb;
            color: #4b5563;
        }

        .module-body {
            font-size: 0.9rem;
            color: var(--muted);
            margin-bottom: 14px;
        }

        .module-meta {
            margin-top: 10px;
            display: grid;
            gap: 6px;
            font-size: 0.85rem;
            color: var(--text);
        }

        .module-meta-row strong {
            font-weight: 800;
        }

        .bar-track {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
            margin-top: 8px;
        }

        .bar-fill {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #2563eb, #60a5fa);
        }

        .mix-row {
            display: grid;
            gap: 6px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #ffffff;
        }

        .mix-row-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-weight: 700;
            color: var(--text);
        }

        .mix-row-sub {
            font-size: 0.85rem;
            color: var(--muted);
        }

        .milestone-value {
            font-size: 1.1rem;
            font-weight: 800;
        }

        .success, .error {
            padding: 12px 14px;
            border-radius: 8px;
            margin: 12px 0 18px;
            border: 1px solid transparent;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            border-color: #86efac;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f8fafc;
            font-weight: 600;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .muted {
            color: var(--muted);
        }

        .topbar-title {
            font-size: 1.05rem;
            line-height: 1.15;
        }

        .topbar-subtitle {
            margin-top: 2px;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.92);
            font-weight: 500;
        }

        .topbar-user {
            font-size: 0.95rem;
            font-weight: 700;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.25);
            border: 1px solid rgba(255,255,255,0.18);
        }

        .admin-topbar {
            background: linear-gradient(135deg, #0f172a, #111827 50%, #1f2937);
        }

        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .admin-content {
            background: radial-gradient(circle at top, rgba(15, 23, 42, 0.08), transparent 55%);
        }

        .admin-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 20px;
            border-radius: 18px;
            border: 1px solid rgba(15, 23, 42, 0.12);
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.06), #ffffff 60%);
            margin-bottom: 18px;
        }

        .admin-eyebrow {
            font-size: 0.75rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            font-weight: 800;
            color: #1f2937;
        }

        .admin-title {
            margin: 8px 0 6px;
            font-size: 1.6rem;
            font-weight: 900;
        }

        .admin-subtitle {
            margin: 0;
            color: var(--muted);
        }

        .admin-hero-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .admin-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .admin-kpi-card {
            padding: 14px 16px;
            border-radius: 14px;
            background: #0f172a;
            color: white;
            border: 1px solid rgba(15, 23, 42, 0.2);
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.15);
        }

        .admin-kpi-label {
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            opacity: 0.7;
            font-weight: 700;
        }

        .admin-kpi-value {
            margin-top: 6px;
            font-size: 1.4rem;
            font-weight: 900;
        }

        .admin-kpi-sub {
            margin-top: 4px;
            font-size: 0.85rem;
            opacity: 0.78;
        }

        .admin-section {
            margin-bottom: 18px;
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid var(--border);
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.06);
        }

        .admin-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px 8px;
            gap: 10px;
        }

        .admin-section-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .admin-section-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #0f172a;
            font-size: 0.75rem;
            font-weight: 800;
        }

        .admin-section-body {
            padding: 0 18px 18px;
        }

        .admin-detail {
            overflow: hidden;
        }

        .admin-detail-summary {
            list-style: none;
            cursor: pointer;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 800;
            border-bottom: 1px solid var(--border);
            background: #f8fafc;
        }

        .admin-detail-summary::-webkit-details-marker {
            display: none;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th,
        .admin-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }

        .admin-table th {
            background: #f8fafc;
            font-weight: 700;
        }

        .admin-table tr:last-child td {
            border-bottom: none;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .page-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #ffffff;
            font: inherit;
            box-sizing: border-box;
        }

        .input:focus {
            outline: none;
            border-color: rgba(37, 99, 235, 0.55);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        .filter-bar {
            display: grid;
            grid-template-columns: 1.2fr 220px auto auto;
            gap: 10px;
            align-items: center;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #0f172a;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .link:hover {
            text-decoration: underline;
        }

        .link.danger {
            color: var(--danger);
        }

        .dashboard-highlight {
            margin-bottom: 22px;
        }

        @media (max-width: 900px) {
            .filter-bar {
                grid-template-columns: 1fr 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .split-layout {
                grid-template-columns: 1fr;
            }

            .admin-hero {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 900px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
            }

            .dashboard-sidebar ~ .main {
                margin-left: 0;
            }

            .dashboard-sidebar-body {
                padding-top: 14px;
            }
        }

    </style>
</head>
<body>
