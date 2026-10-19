<?php
/**
 * Riwayat Peminjaman Barang & Alat - IFIK
 * Redesigned to match the unified Peminjaman Ruangan curved sidebar and card dashboard design
 * Features exact multi-search system with category dropdown, date range Flatpickr, status picker, and dynamic filter popover
 */
$session_role = strtolower((string)$this->session->userdata('role'));
$display_nama = ($session_role === 'admin') ? 'Laboran' : ($this->session->userdata('nama') ?: $this->session->userdata('username') ?: 'Pengguna FIK');
$user_role_id = (int)($this->session->userdata('role_id') ?? 0);
$all_loans = isset($riwayat) && is_array($riwayat) ? $riwayat : [];

if (!function_exists('riwayat_loan_status_tone')) {
    function riwayat_loan_status_tone($status)
    {
        $normalized = strtolower(trim((string)$status));
        if (strpos($normalized, 'tolak') !== false || strpos($normalized, 'batal') !== false || strpos($normalized, 'reject') !== false || strpos($normalized, 'kedaluwarsa') !== false) {
            return 'is-rejected';
        }
        if (strpos($normalized, 'dikembalikan') !== false || strpos($normalized, 'selesai') !== false || strpos($normalized, 'disetujui') !== false) {
            return 'is-completed';
        }
        if (strpos($normalized, 'menunggu') !== false || strpos($normalized, 'dipinjam') !== false || strpos($normalized, 'sedang') !== false || strpos($normalized, 'proses') !== false) {
            return 'is-current';
        }
        return 'is-pending';
    }
}

if (!function_exists('get_loan_status_category')) {
    function get_loan_status_category($status)
    {
        $normalized = strtolower(trim((string)$status));
        if (strpos($normalized, 'tolak') !== false || strpos($normalized, 'batal') !== false || strpos($normalized, 'reject') !== false || strpos($normalized, 'kedaluwarsa') !== false) {
            return 'ditolak';
        }
        if (strpos($normalized, 'disetujui') !== false || strpos($normalized, 'selesai') !== false || strpos($normalized, 'dikembalikan') !== false || strpos($normalized, 'dipinjam') !== false) {
            return 'disetujui';
        }
        return 'menunggu';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Riwayat Peminjaman Barang & Alat - IFIK' ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 & Flatpickr -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/loan-progress.css'); ?>?v=<?= @filemtime(FCPATH . 'assets/css/loan-progress.css'); ?>">

    <style>
        :root {
            --bg-color: #fbf7f1;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --primary: #ea580c;
            --primary-hover: #c2410c;
            --primary-light: #fff7ed;
            --primary-border: rgba(234, 88, 12, 0.2);
            --border-color: #e2e8f0;
            --card-bg: rgba(255, 255, 255, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            background-image:
                radial-gradient(at 0% 0%, rgba(234, 88, 12, 0.07) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(234, 88, 12, 0.05) 0px, transparent 50%);
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            min-height: 100vh;
            padding: 24px 20px 48px;
            transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1), width 0.75s cubic-bezier(0.76, 0, 0.24, 1);
            box-sizing: border-box;
        }

        @media (min-width: 1024px) {
            .page-wrapper-for-sidebar {
                margin-left: 270px;
                width: calc(100% - 270px);
            }

            body.curved-sidebar-desktop-collapsed .page-wrapper-for-sidebar {
                margin-left: 0;
                width: 100%;
            }
        }

        @media (max-width: 1023.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 56px 12px 32px 12px !important;
            }
        }

        .main-wrapper {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Top Header Card */
        .page-header-card {
            background: var(--card-bg);
            border: 1px solid var(--primary-border);
            border-radius: 24px;
            padding: 28px 32px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 18px;
            backdrop-filter: blur(10px);
        }

        .header-title-group h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
        }

        .header-title-group p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin: 6px 0 0 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-action-main {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.88rem;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary-orange {
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            color: #fff;
            box-shadow: 0 6px 18px rgba(234, 88, 12, 0.3);
        }
        .btn-primary-orange:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(234, 88, 12, 0.4);
            color: #fff;
        }

        .btn-outline-secondary {
            background: #fff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-outline-secondary:hover {
            background: #f8fafc;
            color: var(--primary);
            border-color: var(--primary);
        }

        /* Stat Cards Grid (3D Claymorphic Highlight Style) */
        .stat-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .stat-card-highlight {
            position: relative;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 20px;
            padding: 20px 22px 16px 22px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            backdrop-filter: blur(12px);
            overflow: hidden;
            cursor: default;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card-highlight:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 30px -8px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(0,0,0,0.02);
        }

        .stat-card-glow {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }
        .stat-card-glow .glow-bubble {
            position: absolute;
            bottom: -40px;
            right: -40px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--card-accent-soft, rgba(234, 88, 12, 0.12));
            filter: blur(28px);
            transition: transform 0.5s ease;
        }
        .stat-card-highlight:hover .glow-bubble {
            transform: scale(1.4);
        }

        .stat-card-top {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-card-meta {
            flex: 1;
            min-width: 0;
        }

        .stat-card-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            margin-bottom: 4px;
            transition: color 0.2s ease;
        }
        .stat-card-highlight:hover .stat-card-label {
            color: var(--card-accent, #ea580c);
        }

        .stat-card-val {
            font-size: 1.85rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
            letter-spacing: -0.02em;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .stat-card-desc {
            font-size: 0.76rem;
            font-weight: 500;
            color: #64748b;
            margin-top: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-card-3d-icon {
            position: relative;
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            background: var(--card-icon-bg, linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%));
            border: 1px solid var(--card-icon-border, #fed7aa);
            color: var(--card-accent, #ea580c);
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.9), 0 6px 16px var(--card-icon-shadow, rgba(234, 88, 12, 0.16));
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease;
        }
        .stat-card-highlight:hover .stat-card-3d-icon {
            transform: rotate(6deg) scale(1.1);
        }

        .stat-card-bottom {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid rgba(241, 245, 249, 0.9);
        }

        .stat-card-bar {
            width: 38%;
            height: 2.5px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--card-accent, #ea580c), transparent);
            transition: width 0.4s ease;
        }
        .stat-card-highlight:hover .stat-card-bar {
            width: 65%;
        }

        .stat-card-dots {
            display: flex;
            align-items: center;
            gap: 4px;
            opacity: 0.55;
            transition: opacity 0.3s ease;
        }
        .stat-card-highlight:hover .stat-card-dots {
            opacity: 1;
        }

        .stat-card-dots span {
            width: 4.5px;
            height: 4.5px;
            border-radius: 50%;
            background: var(--card-accent, #ea580c);
            display: inline-block;
        }

        .theme-orange {
            --card-accent: #ea580c;
            --card-accent-soft: rgba(234, 88, 12, 0.14);
            --card-icon-bg: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            --card-icon-border: #fed7aa;
            --card-icon-shadow: rgba(234, 88, 12, 0.18);
        }
        .theme-amber {
            --card-accent: #d97706;
            --card-accent-soft: rgba(245, 158, 11, 0.14);
            --card-icon-bg: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            --card-icon-border: #fde68a;
            --card-icon-shadow: rgba(245, 158, 11, 0.18);
        }
        .theme-emerald {
            --card-accent: #059669;
            --card-accent-soft: rgba(16, 185, 129, 0.14);
            --card-icon-bg: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            --card-icon-border: #a7f3d0;
            --card-icon-shadow: rgba(16, 185, 129, 0.18);
        }
        .theme-rose {
            --card-accent: #e11d48;
            --card-accent-soft: rgba(225, 29, 72, 0.14);
            --card-icon-bg: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
            --card-icon-border: #fecdd3;
            --card-icon-shadow: rgba(225, 29, 72, 0.18);
        }

        /* Content Card */
        .content-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 24px 28px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Filter Toolbar */
        .filter-toolbar {
            display: flex;
            flex-direction: column;
            gap: 14px;
            width: 100%;
        }

        .filter-pills {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            width: 100%;
        }

        .pill-btn {
            padding: 8px 16px;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 700;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            outline: none;
        }
        .pill-btn:hover {
            border-color: #cbd5e1;
            color: var(--text-main);
        }
        .pill-btn.active {
            background: #1e293b;
            color: #fff;
            border-color: #1e293b;
        }
        .pill-btn.active .pill-count {
            background: rgba(255,255,255,0.2);
            color: #fff;
        }
        .pill-count {
            background: #e2e8f0;
            color: #475569;
            font-size: 0.72rem;
            padding: 1px 7px;
            border-radius: 9999px;
        }

        /* Search Pill Container & Multi-Search */
        .search-pill-container {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            max-width: 100%;
        }

        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 4px 6px 4px 14px;
            flex: 1;
            width: 100%;
            height: 48px;
            transition: all 0.2s ease;
            position: relative;
        }
        .unified-search-pill:focus-within {
            border-color: #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12) !important;
        }

        .unified-divider {
            width: 1.5px;
            height: 22px;
            background-color: #cbd5e1;
            margin: 0 12px;
            flex-shrink: 0;
        }

        .btn-search-cari {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 18px;
            border-radius: 12px;
            font-size: 0.82rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            color: #fff;
            border: none;
            cursor: pointer;
            box-shadow: 0 3px 8px rgba(234, 88, 12, 0.25);
            transition: all 0.2s ease;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .btn-search-cari:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 12px rgba(234, 88, 12, 0.35);
        }

        .btn-standalone-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff7ed;
            border: 1.5px solid #ffedd5;
            border-radius: 16px;
            padding: 6px 16px;
            height: 48px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #ea580c;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.06);
            flex-shrink: 0;
        }
        .btn-standalone-add:hover, .btn-standalone-add.active {
            background: #ffedd5;
            border-color: #fdba74;
            transform: scale(1.02);
        }

        .badge-standalone-count {
            background: #ea580c;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 2px 9px;
            border-radius: 99px;
        }

        .extra-rows-card {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            width: 100%;
            min-width: 100%;
            background: #ffffff;
            border: 1.5px solid #fed7aa;
            border-radius: 18px;
            padding: 16px;
            box-shadow: 0 16px 36px -6px rgba(15, 23, 42, 0.16);
            z-index: 1000;
        }

        .extra-filter-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .btn-remove-row {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: #fff1f2;
            border: 1.5px solid #fecdd3;
            border-radius: 12px;
            color: #e11d48;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }
        .btn-remove-row:hover {
            background: #ffe4e6;
            transform: scale(1.05);
        }

        .custom-dropdown-container {
            position: relative;
        }

        .custom-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 6px;
            box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.15);
            z-index: 1050;
            min-width: 220px;
        }

        .custom-dropdown-menu .dropdown-item {
            padding: 8px 12px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .custom-dropdown-menu .dropdown-item:hover, .custom-dropdown-menu .dropdown-item.active {
            background: #fff7ed;
            color: #ea580c;
        }

        /* Table Sub-bar */
        .table-sub-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 0.82rem;
            color: var(--text-muted);
            padding-bottom: 4px;
        }

        /* Modern Table Style */
        .table-custom-wrapper {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
        }

        .riwayat-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .riwayat-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.73rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 14px 18px;
            border-bottom: 1.5px solid #e2e8f0;
            white-space: nowrap;
        }

        .riwayat-table tbody td {
            padding: 16px 18px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
            background: #fff;
            transition: background-color 0.15s ease;
        }

        .riwayat-table tbody tr:hover td {
            background-color: #fafbfc;
        }

        .riwayat-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badge Status */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px;
            border-radius: 9999px;
            font-size: 0.74rem;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
        }
        .badge-status.menunggu {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-status.disetujui {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .badge-status.ditolak {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .item-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            color: #0f172a;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 4px 9px;
            border-radius: 8px;
            font-size: 0.82rem;
        }

        .agenda-text {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.84rem;
            max-width: 240px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .agenda-sub {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-top: 2px;
            white-space: nowrap;
        }

        .time-date {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.82rem;
            white-space: nowrap;
        }
        .time-hours {
            font-size: 0.74rem;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
        }

        /* Action Buttons */
        .btn-table-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.76rem;
            font-weight: 700;
            color: #334155;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-table-action:hover {
            background: #fff7ed;
            color: #ea580c;
            border-color: #fdba74;
            box-shadow: 0 3px 8px rgba(234, 88, 12, 0.12);
        }

        .btn-qr-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.76rem;
            font-weight: 700;
            color: #0f172a;
            background: #f8fafc;
            border: 1.5px solid #0f172a;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-qr-action:hover {
            background: #0f172a;
            color: #fff;
        }

        /* Pagination buttons */
        .page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 6px;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
            outline: none;
        }
        .page-btn:hover:not(:disabled) {
            border-color: #ea580c;
            color: #ea580c;
            background: #fff7ed;
        }
        .page-btn.active {
            background: #ea580c;
            border-color: #ea580c;
            color: #ffffff;
        }
        .page-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        /* Flatpickr input styling */
        .flatpickr-custom-alt-input {
            width: 100% !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            color: #1e293b !important;
            cursor: pointer !important;
        }
    </style>
</head>
<body>

    <!-- Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Page Content Wrapper -->
    <div id="mainPageContent" class="page-wrapper-for-sidebar">
        <div class="main-wrapper">

            <!-- Top Header Card -->
            <div class="page-header-card">
                <div class="header-title-group">
                    <h1>
                        <span>📦</span>
                        <span>Riwayat Peminjaman Barang &amp; Alat</span>
                    </h1>
                    <p>Pantau status persetujuan, cek ketersediaan kalender alat, dan ajukan peminjaman baru.</p>
                </div>
                <div class="header-actions">
                    <a href="<?= site_url('peminjaman_barang') ?>" class="btn-action-main btn-primary-orange" title="Ajukan permohonan peminjaman alat/barang baru">
                        <i class="fa-solid fa-plus"></i>
                        <span>Ajukan Peminjaman Barang</span>
                    </a>
                    <a href="<?= site_url('peminjaman-barang/kalender') ?>" class="btn-action-main btn-outline-secondary" title="Lihat kalender jadwal ketersediaan barang">
                        <i class="fa-solid fa-calendar-days" style="color: #ea580c;"></i>
                        <span>Kalender &amp; Jadwal</span>
                    </a>
                    <a href="<?= $back_url ?? site_url('dashboard') ?>" class="btn-action-main btn-outline-secondary" title="Kembali ke Dashboard">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span><?= $back_label ?? 'Dashboard' ?></span>
                    </a>
                </div>
            </div>

            <!-- Stats Overview Cards (3D Claymorphic Highlight Style) -->
            <div class="stat-cards-grid">
                <!-- 1. Total Pengajuan -->
                <div class="stat-card-highlight theme-orange">
                    <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                    <div class="stat-card-top">
                        <div class="stat-card-meta">
                            <div class="stat-card-label">Total Pengajuan</div>
                            <div class="stat-card-val"><span id="stat-total"><?= $total_pengajuan ?? 0 ?></span></div>
                            <div class="stat-card-desc">Semua permohonan masuk</div>
                        </div>
                        <div class="stat-card-3d-icon">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                    </div>
                    <div class="stat-card-bottom">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-dots"><span></span><span></span><span></span></div>
                    </div>
                </div>

                <!-- 2. Menunggu Persetujuan -->
                <div class="stat-card-highlight theme-amber">
                    <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                    <div class="stat-card-top">
                        <div class="stat-card-meta">
                            <div class="stat-card-label">Menunggu Persetujuan</div>
                            <div class="stat-card-val"><span id="stat-menunggu"><?= $total_menunggu ?? 0 ?></span></div>
                            <div class="stat-card-desc">Menunggu review & persetujuan</div>
                        </div>
                        <div class="stat-card-3d-icon">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-card-bottom">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-dots"><span></span><span></span><span></span></div>
                    </div>
                </div>

                <!-- 3. Disetujui / Selesai -->
                <div class="stat-card-highlight theme-emerald">
                    <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                    <div class="stat-card-top">
                        <div class="stat-card-meta">
                            <div class="stat-card-label">Disetujui / Selesai</div>
                            <div class="stat-card-val"><span id="stat-disetujui"><?= $total_disetujui ?? 0 ?></span></div>
                            <div class="stat-card-desc">Peminjaman disetujui / aktif</div>
                        </div>
                        <div class="stat-card-3d-icon">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <div class="stat-card-bottom">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-dots"><span></span><span></span><span></span></div>
                    </div>
                </div>

                <!-- 4. Ditolak / Batal -->
                <div class="stat-card-highlight theme-rose">
                    <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                    <div class="stat-card-top">
                        <div class="stat-card-meta">
                            <div class="stat-card-label">Ditolak / Batal</div>
                            <div class="stat-card-val"><span id="stat-ditolak"><?= $total_ditolak ?? 0 ?></span></div>
                            <div class="stat-card-desc">Permohonan ditolak/dibatalkan</div>
                        </div>
                        <div class="stat-card-3d-icon">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                    </div>
                    <div class="stat-card-bottom">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-dots"><span></span><span></span><span></span></div>
                    </div>
                </div>
            </div>

            <!-- Content Card -->
            <div class="content-card">
                <!-- Filter Toolbar -->
                <div class="filter-toolbar">
                    <div class="filter-pills" id="filterPillsWrap">
                        <button type="button" class="pill-btn active" data-filter="all" onclick="filterByStatus('all', this)">
                            <span>Semua</span>
                            <span class="pill-count" id="pill-all"><?= $total_pengajuan ?? 0 ?></span>
                        </button>
                        <button type="button" class="pill-btn" data-filter="menunggu" onclick="filterByStatus('menunggu', this)">
                            <span>⏳ Menunggu Persetujuan</span>
                            <span class="pill-count" id="pill-menunggu"><?= $total_menunggu ?? 0 ?></span>
                        </button>
                        <button type="button" class="pill-btn" data-filter="disetujui" onclick="filterByStatus('disetujui', this)">
                            <span>✅ Disetujui</span>
                            <span class="pill-count" id="pill-disetujui"><?= $total_disetujui ?? 0 ?></span>
                        </button>
                        <button type="button" class="pill-btn" data-filter="ditolak" onclick="filterByStatus('ditolak', this)">
                            <span>❌ Ditolak / Batal</span>
                            <span class="pill-count" id="pill-ditolak"><?= $total_ditolak ?? 0 ?></span>
                        </button>
                    </div>

                    <!-- Unified MultiSearch Bar Component with Extra Filter Rows Popover (Exact Peminjaman Ruangan System) -->
                    <div class="search-pill-container" id="multiSearchWrapper">
                        <div class="unified-search-pill" id="mainSearchPill">
                            <!-- Category Selector Dropdown -->
                            <div class="custom-dropdown-container">
                                <input type="hidden" id="mainCategoryVal" value="query">
                                <button type="button" onclick="toggleCustomDropdown('main-cat', event)" style="display: flex; align-items: center; gap: 6px; background: none; border: none; cursor: pointer; font-weight: 700; font-size: 0.84rem; color: #1e293b; outline: none; padding: 4px 2px;" title="Kategori Pencarian">
                                    <span id="label-filter-main-cat" style="font-size: 0.95rem; line-height: 1;">🔍</span>
                                    <i class="fa-solid fa-chevron-down dropdown-arrow" id="arrow-filter-main-cat" style="font-size: 0.65rem; color: #94a3b8; transition: transform 0.2s;"></i>
                                </button>
                                <div id="menu-filter-main-cat" class="custom-dropdown-menu">
                                    <div onclick="selectMainCategory('query', '🔍 Kata Kunci (Semua)', 'Ketik kata kunci lalu tekan Enter atau klik Cari...', this)" class="dropdown-item active"><span>🔍 Kata Kunci (Semua)</span></div>
                                    <div onclick="selectMainCategory('barang', '📦 Nama Barang', 'Cari nama barang / alat...', this)" class="dropdown-item"><span>📦 Nama Barang</span></div>
                                    <div onclick="selectMainCategory('kode', '🏷️ Kode Aset', 'Cari kode aset (cth: AST-DKV-01)...', this)" class="dropdown-item"><span>🏷️ Kode Aset</span></div>
                                    <div onclick="selectMainCategory('ruangan', '🚪 Laboratorium / Ruangan', 'Cari nama laboratorium / ruangan...', this)" class="dropdown-item"><span>🚪 Laboratorium / Ruangan</span></div>
                                    <div onclick="selectMainCategory('keperluan', '📝 Keperluan / Agenda', 'Cari keperluan peminjaman...', this)" class="dropdown-item"><span>📝 Keperluan / Agenda</span></div>
                                    <div onclick="selectMainCategory('tanggal', '📅 Tanggal Pinjam', 'Pilih rentang tanggal dari kalender...', this)" class="dropdown-item"><span>📅 Tanggal Pinjam</span></div>
                                    <div onclick="selectMainCategory('status', '⚡ Status Pengajuan', 'Cari status (Menunggu / Disetujui / Ditolak)...', this)" class="dropdown-item"><span>⚡ Status Pengajuan</span></div>
                                </div>
                            </div>

                            <div class="unified-divider"></div>

                            <!-- Input Value Container (Modes: Text, Tanggal Flatpickr, Status Dropdown) -->
                            <div id="mainValueContainer" style="flex: 1; display: flex; align-items: center; min-width: 0; position: relative;">
                                <!-- MODE 1: Text Search (default) -->
                                <div id="modeText" style="flex: 1; display: flex; align-items: center; min-width: 0;">
                                    <i class="fa-solid fa-magnifying-glass" style="color: #94a3b8; font-size: 0.82rem; margin-right: 8px; flex-shrink: 0;"></i>
                                    <input type="text" id="mainSearchInput" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); applyFilterAndSearch(); }" placeholder="Cari kata kunci..." style="width: 100%; font-size: 0.85rem; font-weight: 500; background: transparent; border: none; outline: none; color: #1e293b;">
                                </div>
                                <!-- MODE 2: Date Range Picker (Flatpickr) -->
                                <div id="modeTanggal" style="flex: 1; display: none; align-items: center; min-width: 0; gap: 8px;">
                                    <i class="fa-solid fa-calendar-days" style="color: #ea580c; font-size: 0.85rem; flex-shrink: 0; margin-right: 4px;"></i>
                                    <input type="text" id="dateRangePicker" placeholder="Pilih rentang tanggal dari kalender..." readonly style="flex: 1; font-size: 0.85rem; font-weight: 600; background: transparent; border: none; outline: none; color: #1e293b; cursor: pointer; min-width: 0;">
                                    <button type="button" id="btnClearDateRange" onclick="clearDateRange()" style="display: none; background: none; border: none; color: #94a3b8; cursor: pointer; padding: 2px 4px; font-size: 0.8rem; flex-shrink: 0;" title="Hapus filter tanggal">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                                <!-- MODE 3: Status Dropdown Picker -->
                                <div id="modeStatus" style="flex: 1; display: none; align-items: center; min-width: 0; position: relative;">
                                    <i class="fa-solid fa-circle-half-stroke" style="color: #ea580c; font-size: 0.85rem; margin-right: 8px; flex-shrink: 0;"></i>
                                    <button type="button" id="statusDropdownTrigger" onclick="toggleMainStatusDropdown(event)" style="flex: 1; display: flex; align-items: center; justify-content: space-between; background: transparent; border: none; outline: none; cursor: pointer; font-size: 0.85rem; font-weight: 700; color: #1e293b; padding: 0;">
                                        <span id="statusDropdownLabel" style="display: flex; align-items: center; gap: 8px;">
                                            <span id="statusDropdownDot" style="width: 8px; height: 8px; border-radius: 50%; background: #94a3b8; display: inline-block; flex-shrink: 0;"></span>
                                            <span id="statusDropdownText">Semua Status</span>
                                        </span>
                                        <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; color: #94a3b8; margin-right: 4px;"></i>
                                    </button>
                                    <input type="hidden" id="statusDropdownVal" value="">
                                    <div id="statusDropdownMenu" class="custom-dropdown-menu" style="min-width: 240px; top: calc(100% + 8px);">
                                        <div onclick="selectStatusFilter('', 'Semua Status', '#94a3b8', this)" class="dropdown-item active">
                                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #94a3b8; display: inline-block; margin-right: 8px;"></span>
                                            <span>Semua Status</span>
                                        </div>
                                        <div onclick="selectStatusFilter('menunggu', '⏳ Menunggu Persetujuan', '#f59e0b', this)" class="dropdown-item">
                                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; display: inline-block; margin-right: 8px;"></span>
                                            <span>⏳ Menunggu Persetujuan</span>
                                        </div>
                                        <div onclick="selectStatusFilter('disetujui', '✅ Disetujui / Selesai', '#16a34a', this)" class="dropdown-item">
                                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #16a34a; display: inline-block; margin-right: 8px;"></span>
                                            <span>✅ Disetujui / Selesai</span>
                                        </div>
                                        <div onclick="selectStatusFilter('ditolak', '❌ Ditolak / Batal', '#ef4444', this)" class="dropdown-item">
                                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; display: inline-block; margin-right: 8px;"></span>
                                            <span>❌ Ditolak / Batal</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Cari -->
                            <button type="button" onclick="applyFilterAndSearch()" class="btn-search-cari" title="Klik untuk melakukan pencarian">
                                <i class="fa-solid fa-magnifying-glass" style="font-size: 0.75rem;"></i>
                                <span>Cari</span>
                            </button>
                        </div>

                        <!-- Standalone Add Filter Button (+ 1/4) -->
                        <button type="button" id="standaloneAddBtn" onclick="toggleOrAddFilterRow(event)" class="btn-standalone-add" title="Buka / Tutup / Tambah Filter Baru (Maks 4)">
                            <span style="display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-filter" style="color: #ea580c; font-size: 0.75rem;"></i>
                                <span>Filter Tambahan</span>
                            </span>
                            <span id="filterCountBadge" class="badge-standalone-count">1/4</span>
                        </button>

                        <!-- Extra Filter Rows Card Popover -->
                        <div id="extraRowsCard" class="extra-rows-card">
                            <div id="additionalFilterRowsContainer" style="display: flex; flex-direction: column; gap: 8px;"></div>
                            
                            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 12px; margin-top: 10px; font-size: 0.76rem; flex-wrap: wrap; gap: 10px;">
                                <span style="color: #94a3b8; flex: 1; min-width: 180px;">Gunakan kombinasi kriteria untuk mempersempit pencarian data barang.</span>
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <button type="button" onclick="resetMultiSearch()" style="background: none; border: none; color: #dc2626; font-weight: 700; cursor: pointer; font-size: 0.78rem;">
                                        Reset All
                                    </button>
                                    <button type="button" onclick="addFilterRow()" style="padding: 5px 12px; background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; font-size: 0.78rem;">
                                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Kriteria
                                    </button>
                                    <button type="button" onclick="applyFilterAndSearch()" style="padding: 6px 16px; background: linear-gradient(135deg, #ea580c, #f97316); color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 0.8rem; box-shadow: 0 2px 8px rgba(234,88,12,0.25);">
                                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i> Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Sub-bar Info -->
                <div class="table-sub-bar">
                    <div>Kelola &amp; telusuri data peminjaman barang secara langsung.</div>
                    <div class="d-flex align-items-center gap-2">
                        <span>Tampilkan</span>
                        <select id="selectPerPage" onchange="changePageSize(this.value)" class="form-select form-select-sm py-0 px-2" style="width: 70px; height: 30px; font-weight: 700;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>data/hal</span>
                        <span class="text-muted ms-2">| Total: <strong id="toolbarTotalCount"><?= count($all_loans) ?></strong></span>
                    </div>
                </div>

                <!-- Modern Table -->
                <div class="table-custom-wrapper">
                    <div class="table-responsive">
                        <table class="riwayat-table" id="bookingTable">
                            <thead>
                                <tr>
                                    <th>Kode &amp; Nama Barang</th>
                                    <th>Keperluan / Agenda</th>
                                    <th>Tanggal &amp; Waktu</th>
                                    <th>Status Pengajuan</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="bookingTableBody">
                                <?php if(empty($all_loans)): ?>
                                    <tr id="emptyInitialRow">
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-box-seam text-muted" style="font-size: 2.8rem; opacity: 0.5;"></i>
                                            <p class="text-muted mt-2 mb-1 fw-bold">Belum ada riwayat peminjaman barang.</p>
                                            <p class="text-muted small mb-3">Ajukan peminjaman pertama Anda melalui katalog alat laboratorium.</p>
                                            <a href="<?= site_url('peminjaman_barang') ?>" class="btn-action-main btn-primary-orange" style="font-size: 0.8rem; padding: 8px 16px;">
                                                <i class="fa-solid fa-plus"></i>
                                                <span>Ajukan Peminjaman Sekarang</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($all_loans as $r): 
                                        $cat_tone = get_loan_status_category($r->status ?? '');
                                        $is_laboran_scan = in_array($r->status, ['Menunggu Verifikasi Laboran', 'Menunggu Pengecekan Laboran', 'Menunggu Scan Laboran'], true);
                                        $is_ready_for_pickup = in_array($r->status, ['Disetujui (Menunggu Pengambilan)', 'Disetujui (Menunggu Finalisasi QR)'], true);
                                        $is_borrowed = in_array($r->status, ['Sedang Dipinjam', 'Dipinjam'], true);
                                        $show_qr = $is_laboran_scan || $is_ready_for_pickup || $is_borrowed;
                                        $tanggal_raw = substr((string)($r->tanggal_pinjam ?? $r->created_at ?? ''), 0, 10);
                                        $search_corpus = strtolower(implode(' ', [
                                            $r->nama_aset ?? '',
                                            $r->kode_aset ?? '',
                                            $r->nama_ruangan ?? '',
                                            $r->keperluan ?? '',
                                            $r->status ?? '',
                                            $r->group_id ?? '',
                                            $tanggal_raw
                                        ]));
                                    ?>
                                        <tr class="booking-row"
                                            data-status="<?= $cat_tone ?>"
                                            data-tanggal-raw="<?= $tanggal_raw ?>"
                                            data-barang="<?= htmlspecialchars(strtolower($r->nama_aset ?? '')) ?>"
                                            data-kode="<?= htmlspecialchars(strtolower($r->kode_aset ?? '')) ?>"
                                            data-ruangan="<?= htmlspecialchars(strtolower($r->nama_ruangan ?? '')) ?>"
                                            data-keperluan="<?= htmlspecialchars(strtolower($r->keperluan ?? '')) ?>"
                                            data-search="<?= htmlspecialchars($search_corpus) ?>"
                                        >
                                            <td>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="item-pill">
                                                        <i class="fa-solid fa-cube text-primary" style="font-size: 11px;"></i>
                                                        <?= htmlspecialchars($r->nama_aset ?? 'Barang') ?>
                                                    </span>
                                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10px;">
                                                        <?= htmlspecialchars($r->kode_aset ?? '-') ?>
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.74rem;">
                                                    <span>Jumlah: <strong class="text-dark"><?= (int)($r->jumlah_pinjam ?? 1) ?> unit</strong></span>
                                                    <span>&bull;</span>
                                                    <span><?= htmlspecialchars($r->nama_ruangan ?? 'Lab FIK') ?></span>
                                                    <?php if (($r->jenis_peminjaman ?? '') === 'luar_kampus'): ?>
                                                        <span class="badge rounded-pill bg-purple-subtle text-primary border border-primary-subtle" style="font-size: 9px;">🚀 Luar Kampus</span>
                                                    <?php else: ?>
                                                        <span class="badge rounded-pill bg-warning-subtle text-dark border border-warning-subtle" style="font-size: 9px;">🏛️ Internal</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="agenda-text" title="<?= htmlspecialchars($r->keperluan ?? '-') ?>">
                                                    <?= htmlspecialchars($r->keperluan ?: '-') ?>
                                                </div>
                                                <div class="agenda-sub">
                                                    No Transaksi: <span class="font-monospace text-dark fw-bold"><?= htmlspecialchars($r->group_id ?: ('PJM_'.$r->id_peminjaman)) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="time-date">
                                                    <?= date('d M Y', strtotime($r->tanggal_pinjam)) ?>
                                                    <?php if(!empty($r->tanggal_kembali_rencana) && $r->tanggal_kembali_rencana !== $r->tanggal_pinjam): ?>
                                                        <span class="text-muted fw-normal">s.d.</span> <?= date('d M Y', strtotime($r->tanggal_kembali_rencana)) ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="time-hours">
                                                    Diajukan: <?= date('d M Y, H:i', strtotime($r->created_at ?? $r->tanggal_pinjam)) ?> WIB
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-status <?= $cat_tone ?>">
                                                    <?php if($cat_tone === 'disetujui'): ?>
                                                        <i class="fa-solid fa-circle-check"></i>
                                                    <?php elseif($cat_tone === 'ditolak'): ?>
                                                        <i class="fa-solid fa-circle-xmark"></i>
                                                    <?php else: ?>
                                                        <i class="fa-solid fa-clock"></i>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($r->status ?? 'Menunggu') ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <?php if($show_qr): ?>
                                                        <button type="button" class="btn-qr-action" data-bs-toggle="modal" data-bs-target="#qrModal<?= $r->id_peminjaman ?>" title="Buka QR Transaksi">
                                                            <i class="bi bi-qr-code-scan"></i>
                                                            <span class="d-none d-md-inline">QR Transaksi</span>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn-table-action" data-bs-toggle="modal" data-bs-target="#riwayatProgressModal<?= (int) $r->id_peminjaman ?>" title="Lihat detail & tracking progress">
                                                        <i class="fa-solid fa-eye"></i>
                                                        <span class="d-none d-md-inline">Detail</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- No Match Empty State -->
                    <div id="noSearchMatch" style="display: none; text-align: center; padding: 48px 16px;">
                        <i class="fa-solid fa-magnifying-glass text-muted" style="font-size: 2.4rem; opacity: 0.4; margin-bottom: 12px; display: inline-block;"></i>
                        <h4 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 4px;">Tidak Ditemukan Data Peminjaman</h4>
                        <p style="font-size: 0.82rem; color: #64748b; margin-bottom: 16px;">Kombinasi kata kunci dan filter yang Anda gunakan tidak cocok dengan riwayat manapun.</p>
                        <button type="button" onclick="resetMultiSearch()" class="btn-action-main btn-outline-secondary" style="font-size: 0.8rem; padding: 6px 16px;">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Reset Pencarian</span>
                        </button>
                    </div>
                </div>

                <!-- Instant Pagination Controls Bar -->
                <div id="paginationBar" class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2">
                    <div class="text-muted small">
                        Menampilkan <strong id="showingStart">1</strong> - <strong id="showingEnd">10</strong> dari <strong id="showingTotal"><?= count($all_loans) ?></strong> data
                    </div>
                    <div id="paginationControls" class="d-flex align-items-center gap-1"></div>
                </div>

            </div>

        </div>
    </div>

    <!-- MODAL TIKET QR CODE -->
    <?php if(!empty($all_loans)): ?>
        <?php foreach($all_loans as $r): ?>
        <?php
            $is_laboran_scan = in_array($r->status, ['Menunggu Verifikasi Laboran', 'Menunggu Pengecekan Laboran', 'Menunggu Scan Laboran'], true);
            $is_ready_for_pickup = in_array($r->status, ['Disetujui (Menunggu Pengambilan)', 'Disetujui (Menunggu Finalisasi QR)'], true);
            $is_borrowed = in_array($r->status, ['Sedang Dipinjam', 'Dipinjam'], true);
            $show_qr = $is_laboran_scan || $is_ready_for_pickup || $is_borrowed;
            if(!$show_qr) { continue; }
            $show_return_qr = in_array($r->status, ['Sedang Dipinjam', 'Dipinjam'], true);
            $qr_url = site_url('peminjamanbarang/serah_terima/'.rawurlencode($r->group_id));
        ?>
        <div class="modal fade" id="qrModal<?= $r->id_peminjaman ?>" tabindex="-1" aria-labelledby="qrModalLabel<?= $r->id_peminjaman ?>" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content text-center p-4 border-0 shadow-lg" style="border-radius: 20px;">
                    <h5 class="fw-bold text-dark mb-1" id="qrModalLabel<?= $r->id_peminjaman ?>">QR Transaksi Laboratorium</h5>
                    <p class="small text-muted mb-4"><?= $show_return_qr ? 'Tunjukkan QR ini kepada Laboran saat mengembalikan barang.' : ($is_laboran_scan ? 'Tunjukkan QR ini kepada Laboran untuk scan & verifikasi fisik barang.' : 'Tunjukkan QR ini saat serah terima barang di laboratorium.') ?></p>
                    
                    <div class="bg-white p-3 rounded-4 mb-3 mx-auto shadow-sm border" style="display: inline-block;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?= rawurlencode($qr_url) ?>" alt="QR Code" class="img-fluid">
                    </div>
                    
                    <div class="font-monospace fs-6 fw-bold bg-light border px-3 py-2 rounded-3 text-secondary mb-3">
                        <?= htmlspecialchars($r->group_id ?: '') ?>
                    </div>

                    <div class="alert alert-info py-2 small mb-4 text-start">
                        <strong>Barang:</strong> <?= htmlspecialchars($r->nama_aset ?? '-') ?><br>
                        <strong>Status:</strong> <?= htmlspecialchars($r->status ?? '-') ?>
                    </div>
                    
                    <button type="button" class="btn btn-secondary w-100 rounded-pill" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- MODAL DETAIL PROGRESS PEMINJAMAN -->
    <?php if(!empty($all_loans)): ?>
        <?php foreach($all_loans as $r): ?>
            <?php
                $pjm_number = $r->group_id ?: ('PJM_' . $r->id_peminjaman);
            ?>
            <div class="modal fade kp-loan-modal" id="riwayatProgressModal<?= (int) $r->id_peminjaman ?>" tabindex="-1" aria-labelledby="riwayatProgressModalTitle<?= (int) $r->id_peminjaman ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="w-100 pe-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <div>
                                        <div class="kp-modal-kicker">Peminjaman</div>
                                        <h2 id="riwayatProgressModalTitle<?= (int) $r->id_peminjaman ?>" class="modal-title">
                                            <?= html_escape($pjm_number) ?>
                                        </h2>
                                    </div>
                                    <span class="kp-status-badge <?= riwayat_loan_status_tone($r->status) ?>">
                                        <span class="kp-status-dot"></span><?= html_escape($r->status) ?>
                                    </span>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <section class="kp-modal-section">
                                <div class="kp-modal-section-title">Progress Peminjaman</div>
                                <?php $loan_progress_item = $r; $loan_progress_compact = false; include APPPATH . 'views/shared/loan_progress.php'; ?>
                            </section>
                            <section class="kp-modal-section">
                                <div class="kp-modal-section-title">Keperluan</div>
                                <p class="kp-purpose"><?= nl2br(html_escape($r->keperluan ?? '-')) ?></p>
                            </section>
                            <section class="kp-modal-section">
                                <div class="kp-modal-section-title">Detail Barang</div>
                                <div class="kp-detail-table table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Barang</th>
                                            <th>Kode</th>
                                            <th>Laboratorium</th>
                                            <th class="text-center">Jumlah</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $detail_list = !empty($r->detail_barang) ? $r->detail_barang : [$r]; ?>
                                        <?php foreach ($detail_list as $detail): ?>
                                            <tr>
                                                <td class="fw-semibold"><?= html_escape($detail->nama_aset ?? '-') ?></td>
                                                <td><?= html_escape($detail->kode_aset ?? '-') ?></td>
                                                <td><?= html_escape($detail->nama_ruangan ?? '-') ?></td>
                                                <td class="text-center"><?= (int) ($detail->jumlah_pinjam ?? 1) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Unified Multi-Search Client Engine (Identical to Peminjaman Ruangan) -->
    <script>
        let currentFilter = 'all';
        let extraRowCounter = 0;
        let fpRange = null;
        let extraFpInstances = {};
        let currentPage = 1;
        let pageSize = 10;

        const SEARCH_CATEGORIES = [
            { key: 'query', emoji: '🔍', label: '🔍 Kata Kunci (Semua)', placeholder: 'Ketik kata kunci lalu tekan Enter atau klik Cari...' },
            { key: 'barang', emoji: '📦', label: '📦 Nama Barang', placeholder: 'Cari nama barang / alat...' },
            { key: 'kode', emoji: '🏷️', label: '🏷️ Kode Aset', placeholder: 'Cari kode aset (cth: AST-DKV-01)...' },
            { key: 'ruangan', emoji: '🚪', label: '🚪 Laboratorium / Ruangan', placeholder: 'Cari nama laboratorium / ruangan...' },
            { key: 'keperluan', emoji: '📝', label: '📝 Keperluan / Agenda', placeholder: 'Cari keperluan peminjaman...' },
            { key: 'tanggal', emoji: '📅', label: '📅 Tanggal Pinjam', placeholder: 'Pilih rentang tanggal dari kalender...' },
            { key: 'status', emoji: '⚡', label: '⚡ Status Pengajuan', placeholder: 'Cari status (Menunggu / Disetujui / Ditolak)...' }
        ];

        function getCategoryEmoji(key) {
            const found = SEARCH_CATEGORIES.find(c => c.key === key);
            return found ? (found.emoji || '🔍') : '🔍';
        }

        // Dropdown toggle
        function toggleCustomDropdown(id, e) {
            if (e) { e.stopPropagation(); e.preventDefault(); }
            let menu = $('#menu-filter-' + id);
            let arrow = $('#arrow-filter-' + id);
            let isVisible = menu.is(':visible');
            
            $('.custom-dropdown-menu').hide();
            $('.dropdown-arrow').css('transform', 'rotate(0deg)');

            if (!isVisible) {
                menu.show();
                if (arrow.length) arrow.css('transform', 'rotate(180deg)');
            }
        }

        // Initialize Main Flatpickr Date Range
        function initMainDateRange() {
            if (fpRange) return;
            const el = document.getElementById('dateRangePicker');
            if (el) {
                fpRange = flatpickr(el, {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altInputClass: 'flatpickr-custom-alt-input',
                    altFormat: 'd M Y',
                    allowInput: false,
                    disableMobile: true,
                    onClose: function(selectedDates) {
                        const btn = document.getElementById('btnClearDateRange');
                        if (btn) btn.style.display = selectedDates.length > 0 ? 'inline-flex' : 'none';
                        applyFilterAndSearch();
                    }
                });
            }
        }

        function clearDateRange() {
            if (fpRange) fpRange.clear();
            const btn = document.getElementById('btnClearDateRange');
            if (btn) btn.style.display = 'none';
            applyFilterAndSearch();
        }

        function switchMainMode(mode) {
            const modeText = document.getElementById('modeText');
            const modeTanggal = document.getElementById('modeTanggal');
            const modeStatus = document.getElementById('modeStatus');
            if (modeText) modeText.style.display = (mode === 'text') ? 'flex' : 'none';
            if (modeTanggal) modeTanggal.style.display = (mode === 'tanggal') ? 'flex' : 'none';
            if (modeStatus) modeStatus.style.display = (mode === 'status') ? 'flex' : 'none';
            
            if (mode === 'tanggal') {
                initMainDateRange();
            }
        }

        function toggleMainStatusDropdown(e) {
            if (e) { e.stopPropagation(); e.preventDefault(); }
            const menu = $('#statusDropdownMenu');
            const isVisible = menu.is(':visible');
            $('.custom-dropdown-menu').hide();
            $('.dropdown-arrow').css('transform', 'rotate(0deg)');
            if (!isVisible) menu.show();
        }

        function selectStatusFilter(val, label, dotColor, el) {
            $('#statusDropdownVal').val(val);
            $('#statusDropdownDot').css('background', dotColor);
            $('#statusDropdownText').text(label.replace(/^[^\s]+\s*/, ''));
            $('#statusDropdownMenu .dropdown-item').removeClass('active');
            if (el) $(el).addClass('active');
            $('#statusDropdownMenu').hide();
            applyFilterAndSearch();
        }

        function selectMainCategory(cat, label, placeholder, el) {
            $('#mainCategoryVal').val(cat);
            $('#label-filter-main-cat').text(getCategoryEmoji(cat));
            
            $('#menu-filter-main-cat .dropdown-item').removeClass('active');
            if (el) $(el).addClass('active');
            
            $('#menu-filter-main-cat').hide();
            $('#arrow-filter-main-cat').css('transform', 'rotate(0deg)');

            if (cat === 'tanggal') {
                switchMainMode('tanggal');
            } else if (cat === 'status') {
                switchMainMode('status');
            } else {
                switchMainMode('text');
                $('#mainSearchInput').attr('placeholder', placeholder).focus();
            }
        }

        // Toggle / Add Extra Filter Popover
        function toggleOrAddFilterRow(e) {
            if (e) { e.stopPropagation(); e.preventDefault(); }
            const card = document.getElementById('extraRowsCard');
            const btn = document.getElementById('standaloneAddBtn');
            const isOpen = card && card.style.display === 'block';

            if (isOpen) {
                card.style.display = 'none';
                if (btn) btn.classList.remove('active');
            } else {
                if (card) card.style.display = 'block';
                if (btn) btn.classList.add('active');
                const container = document.getElementById('additionalFilterRowsContainer');
                if (container && container.children.length === 0) {
                    addFilterRow('barang');
                }
            }
        }

        function getExtraRowInputHtml(rowId, catKey, defaultVal = '') {
            if (catKey === 'tanggal') {
                return `
                    <div style="flex: 1; display: flex; align-items: center; min-width: 0; gap: 6px;">
                        <i class="fa-solid fa-calendar-days" style="color: #ea580c; font-size: 0.85rem; flex-shrink: 0; margin-right: 4px;"></i>
                        <input type="text" id="extraDateInput_${rowId}" class="extra-date-input" placeholder="Pilih rentang tanggal..." readonly style="flex: 1; font-size: 0.85rem; font-weight: 600; background: transparent; border: none; outline: none; color: #1e293b; cursor: pointer; min-width: 0;">
                        <button type="button" id="extraDateClear_${rowId}" onclick="clearExtraDateRange('${rowId}')" style="display: none; background: none; border: none; color: #94a3b8; cursor: pointer; padding: 2px 4px; font-size: 0.8rem; flex-shrink: 0;" title="Hapus tanggal">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                `;
            } else if (catKey === 'status') {
                return `
                    <div style="flex: 1; display: flex; align-items: center; min-width: 0; position: relative;" id="extraStatusWrapper_${rowId}">
                        <input type="hidden" class="extra-status-val" id="extraStatusVal_${rowId}" value="${defaultVal || ''}">
                        <button type="button" onclick="toggleExtraStatusDropdown('${rowId}', event)" style="background: none; border: none; display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.84rem; font-weight: 700; color: #1e293b; width: 100%; text-align: left; padding: 0;">
                            <span id="extraStatusDot_${rowId}" style="width: 8px; height: 8px; border-radius: 50%; background: #94a3b8; display: inline-block; flex-shrink: 0;"></span>
                            <span id="extraStatusText_${rowId}" style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Semua Status</span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; color: #94a3b8; margin-right: 4px;"></i>
                        </button>
                        <div id="extraStatusMenu_${rowId}" class="custom-dropdown-menu" style="min-width: 220px; top: calc(100% + 8px);">
                            <div onclick="selectExtraStatus('${rowId}', '', 'Semua Status', '#94a3b8', this)" class="dropdown-item active">
                                <span style="width:8px;height:8px;border-radius:50%;background:#94a3b8;display:inline-block;margin-right:8px;"></span> Semua Status
                            </div>
                            <div onclick="selectExtraStatus('${rowId}', 'menunggu', '⏳ Menunggu Persetujuan', '#f59e0b', this)" class="dropdown-item">
                                <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;margin-right:8px;"></span> ⏳ Menunggu Persetujuan
                            </div>
                            <div onclick="selectExtraStatus('${rowId}', 'disetujui', '✅ Disetujui / Selesai', '#16a34a', this)" class="dropdown-item">
                                <span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;margin-right:8px;"></span> ✅ Disetujui / Selesai
                            </div>
                            <div onclick="selectExtraStatus('${rowId}', 'ditolak', '❌ Ditolak / Batal', '#ef4444', this)" class="dropdown-item">
                                <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;margin-right:8px;"></span> ❌ Ditolak / Batal
                            </div>
                        </div>
                    </div>
                `;
            } else {
                const catObj = SEARCH_CATEGORIES.find(c => c.key === catKey) || SEARCH_CATEGORIES[0];
                return `
                    <div style="flex: 1; display: flex; align-items: center; min-width: 0;">
                        <i class="fa-solid fa-magnifying-glass" style="color: #94a3b8; font-size: 0.82rem; margin-right: 8px; flex-shrink: 0;"></i>
                        <input type="text" class="extra-search-input" value="${defaultVal}" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); applyFilterAndSearch(); }" placeholder="${catObj.placeholder}" style="width: 100%; font-size: 0.85rem; font-weight: 500; background: transparent; border: none; outline: none; color: #1e293b;">
                    </div>
                `;
            }
        }

        function initExtraRowInput(rowId, catKey) {
            if (extraFpInstances[rowId]) {
                extraFpInstances[rowId].destroy();
                delete extraFpInstances[rowId];
            }
            if (catKey === 'tanggal') {
                setTimeout(() => {
                    const el = document.getElementById('extraDateInput_' + rowId);
                    if (el) {
                        extraFpInstances[rowId] = flatpickr(el, {
                            mode: 'range',
                            dateFormat: 'Y-m-d',
                            altInput: true,
                            altInputClass: 'flatpickr-custom-alt-input',
                            altFormat: 'd M Y',
                            allowInput: false,
                            disableMobile: true,
                            onClose: function(selectedDates) {
                                const btn = document.getElementById('extraDateClear_' + rowId);
                                if (btn) btn.style.display = selectedDates.length > 0 ? 'inline-flex' : 'none';
                                applyFilterAndSearch();
                            }
                        });
                    }
                }, 50);
            }
        }

        function clearExtraDateRange(rowId) {
            if (extraFpInstances[rowId]) {
                extraFpInstances[rowId].clear();
            }
            const btn = document.getElementById('extraDateClear_' + rowId);
            if (btn) btn.style.display = 'none';
            applyFilterAndSearch();
        }

        function toggleExtraStatusDropdown(rowId, e) {
            if (e) { e.stopPropagation(); e.preventDefault(); }
            const menu = $('#extraStatusMenu_' + rowId);
            const isVisible = menu.is(':visible');
            $('.custom-dropdown-menu').hide();
            $('.dropdown-arrow').css('transform', 'rotate(0deg)');
            if (!isVisible) menu.show();
        }

        function selectExtraStatus(rowId, val, label, dotColor, el) {
            $('#extraStatusVal_' + rowId).val(val);
            $('#extraStatusDot_' + rowId).css('background', dotColor);
            $('#extraStatusText_' + rowId).text(label.replace(/^[^\s]+\s*/, ''));
            $('#extraStatusMenu_' + rowId + ' .dropdown-item').removeClass('active');
            if (el) $(el).addClass('active');
            $('#extraStatusMenu_' + rowId).hide();
            applyFilterAndSearch();
        }

        function addFilterRow(defaultKey = 'barang', defaultVal = '') {
            const container = document.getElementById('additionalFilterRowsContainer');
            if (!container) return;

            if (container.children.length >= 3) {
                Swal.fire({
                    title: 'Batas Maksimal Filter',
                    text: 'Maksimal 4 kriteria pencarian kombinasi (1 utama + 3 filter tambahan).',
                    icon: 'info',
                    confirmButtonColor: '#ea580c',
                    borderRadius: '16px'
                });
                return;
            }

            extraRowCounter++;
            const rowId = 'extra-row-' + extraRowCounter;

            let dropdownItems = '';
            SEARCH_CATEGORIES.forEach(c => {
                const isActive = (c.key === defaultKey) ? 'active' : '';
                dropdownItems += `<div onclick="selectExtraCategory('${rowId}', '${c.key}', '${c.label}', this)" class="dropdown-item ${isActive}"><span>${c.label}</span></div>`;
            });

            const emoji = getCategoryEmoji(defaultKey);

            const rowHtml = document.createElement('div');
            rowHtml.className = 'extra-filter-row';
            rowHtml.id = rowId;
            rowHtml.innerHTML = `
                <div class="unified-search-pill" style="height: 44px; flex: 1;">
                    <div class="custom-dropdown-container">
                        <input type="hidden" class="extra-category-val" value="${defaultKey}">
                        <button type="button" onclick="toggleCustomDropdown('${rowId}', event)" style="display:flex;align-items:center;gap:6px;background:none;border:none;cursor:pointer;font-weight:700;font-size:0.84rem;color:#1e293b;outline:none;padding:4px 2px;" title="Kategori Pencarian">
                            <span class="extra-category-label" style="font-size: 0.95rem; line-height: 1; display: block;">${emoji}</span>
                            <i class="fa-solid fa-chevron-down dropdown-arrow" id="arrow-filter-${rowId}" style="font-size:0.65rem;color:#94a3b8;transition:transform 0.2s;"></i>
                        </button>
                        <div id="menu-filter-${rowId}" class="custom-dropdown-menu">
                            ${dropdownItems}
                        </div>
                    </div>
                    <div class="unified-divider" style="height: 20px;"></div>
                    <div id="extraInputContainer_${rowId}" style="flex: 1; display: flex; align-items: center; min-width: 0;">
                        ${getExtraRowInputHtml(rowId, defaultKey, defaultVal)}
                    </div>
                </div>
                <button type="button" onclick="removeFilterRow('${rowId}')" class="btn-remove-row" title="Hapus kriteria ini">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;

            container.appendChild(rowHtml);
            initExtraRowInput(rowId, defaultKey);
            updateFilterCountBadge();
        }

        function selectExtraCategory(rowId, catKey, catLabel, el) {
            const row = document.getElementById(rowId);
            if (!row) return;

            row.querySelector('.extra-category-val').value = catKey;
            row.querySelector('.extra-category-label').innerText = getCategoryEmoji(catKey);
            
            if (el) {
                el.parentElement.querySelectorAll('.dropdown-item').forEach(d => d.classList.remove('active'));
                el.classList.add('active');
            }

            $(`#menu-filter-${rowId}`).hide();
            $(`#arrow-filter-${rowId}`).css('transform', 'rotate(0deg)');

            const inputContainer = document.getElementById('extraInputContainer_' + rowId);
            if (inputContainer) {
                inputContainer.innerHTML = getExtraRowInputHtml(rowId, catKey, '');
                initExtraRowInput(rowId, catKey);
            }
        }

        function removeFilterRow(rowId) {
            if (extraFpInstances[rowId]) {
                extraFpInstances[rowId].destroy();
                delete extraFpInstances[rowId];
            }
            const row = document.getElementById(rowId);
            if (row) {
                row.remove();
                updateFilterCountBadge();
                applyFilterAndSearch();
            }
        }

        function updateFilterCountBadge() {
            const container = document.getElementById('additionalFilterRowsContainer');
            const count = 1 + (container ? container.children.length : 0);
            const badge = document.getElementById('filterCountBadge');
            if (badge) badge.innerText = count + '/4';
        }

        function resetMultiSearch() {
            $('#mainCategoryVal').val('query');
            $('#label-filter-main-cat').text('🔍');
            $('#mainSearchInput').val('').attr('placeholder', 'Ketik kata kunci lalu tekan Enter atau klik Cari...');
            switchMainMode('text');
            clearDateRange();
            selectStatusFilter('', 'Semua Status', '#94a3b8');

            Object.keys(extraFpInstances).forEach(k => {
                if (extraFpInstances[k]) extraFpInstances[k].destroy();
            });
            extraFpInstances = {};
            const container = document.getElementById('additionalFilterRowsContainer');
            if (container) container.innerHTML = '';
            
            const card = document.getElementById('extraRowsCard');
            if (card) card.style.display = 'none';
            const btn = document.getElementById('standaloneAddBtn');
            if (btn) btn.classList.remove('active');

            updateFilterCountBadge();
            currentPage = 1;
            applyFilterAndSearch();
        }

        function getActiveFilters() {
            const filters = [];
            const mainKey = $('#mainCategoryVal').val() || 'query';

            if (mainKey === 'tanggal') {
                const dates = fpRange ? fpRange.selectedDates : [];
                if (dates.length > 0) {
                    const fromDate = new Date(dates[0]);
                    fromDate.setHours(0, 0, 0, 0);
                    const toDate = dates[1] ? new Date(dates[1]) : new Date(dates[0]);
                    toDate.setHours(23, 59, 59, 999);
                    filters.push({ key: 'tanggal_range', from: fromDate, to: toDate });
                }
            } else if (mainKey === 'status') {
                const statusVal = ($('#statusDropdownVal').val() || '').toLowerCase().trim();
                if (statusVal) {
                    filters.push({ key: 'status', val: statusVal });
                }
            } else {
                const mainVal = ($('#mainSearchInput').val() || '').toLowerCase().trim();
                if (mainVal) {
                    filters.push({ key: mainKey, val: mainVal });
                }
            }

            document.querySelectorAll('#additionalFilterRowsContainer .extra-filter-row').forEach(row => {
                const rowId = row.id;
                const key = row.querySelector('.extra-category-val').value;
                if (key === 'tanggal') {
                    const fp = extraFpInstances[rowId];
                    const dates = fp ? fp.selectedDates : [];
                    if (dates.length > 0) {
                        const fromDate = new Date(dates[0]);
                        fromDate.setHours(0, 0, 0, 0);
                        const toDate = dates[1] ? new Date(dates[1]) : new Date(dates[0]);
                        toDate.setHours(23, 59, 59, 999);
                        filters.push({ key: 'tanggal_range', from: fromDate, to: toDate });
                    }
                } else if (key === 'status') {
                    const statusVal = ($('#extraStatusVal_' + rowId).val() || '').toLowerCase().trim();
                    if (statusVal) {
                        filters.push({ key: 'status', val: statusVal });
                    }
                } else {
                    const val = (row.querySelector('.extra-search-input')?.value || '').toLowerCase().trim();
                    if (val) {
                        filters.push({ key: key, val: val });
                    }
                }
            });

            return filters;
        }

        function filterByStatus(status, btn) {
            currentFilter = status;
            $('.pill-btn').removeClass('active');
            if (btn) $(btn).addClass('active');
            currentPage = 1;
            applyFilterAndSearch();
        }

        function changePageSize(sz) {
            pageSize = parseInt(sz, 10) || 10;
            currentPage = 1;
            applyFilterAndSearch();
        }

        function goToPage(p) {
            currentPage = p;
            applyFilterAndSearch();
        }

        function renderPaginationControls(totalPages) {
            const container = document.getElementById('paginationControls');
            if (!container) return;

            if (totalPages <= 0) {
                container.innerHTML = '';
                return;
            }

            let html = '';
            const prevDisabled = currentPage <= 1 ? 'disabled' : '';
            html += `<button type="button" class="page-btn" ${prevDisabled} onclick="goToPage(${currentPage - 1})" title="Halaman Sebelumnya">
                <i class="fa-solid fa-chevron-left" style="font-size: 0.65rem;"></i>
            </button>`;

            let pages = [];
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) pages.push(i);
            } else {
                if (currentPage <= 4) {
                    pages = [1, 2, 3, 4, 5, '...', totalPages];
                } else if (currentPage >= totalPages - 3) {
                    pages = [1, '...', totalPages - 4, totalPages - 3, totalPages - 2, totalPages - 1, totalPages];
                } else {
                    pages = [1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages];
                }
            }

            pages.forEach(p => {
                if (p === '...') {
                    html += `<span style="padding: 0 4px; color: #94a3b8; font-weight: 700; font-size: 0.75rem;">...</span>`;
                } else {
                    const activeClass = (p === currentPage) ? 'active' : '';
                    html += `<button type="button" class="page-btn ${activeClass}" onclick="goToPage(${p})">${p}</button>`;
                }
            });

            const nextDisabled = currentPage >= totalPages ? 'disabled' : '';
            html += `<button type="button" class="page-btn" ${nextDisabled} onclick="goToPage(${currentPage + 1})" title="Halaman Berikutnya">
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
            </button>`;

            container.innerHTML = html;
        }

        // Apply Filter & Search (Runs instantly across all table rows)
        function applyFilterAndSearch() {
            let rows = $('.booking-row');
            const activeFilters = getActiveFilters();

            if (rows.length === 0) {
                $('#paginationBar').hide();
                return;
            }

            let matchingRows = [];

            rows.each(function() {
                const row = $(this);
                const rowStatus = row.data('status') || '';
                
                // 1. Status Pill Match
                const matchStatusPill = (currentFilter === 'all') || (rowStatus === currentFilter);
                if (!matchStatusPill) {
                    return;
                }

                // 2. All Active MultiSearch Criteria (AND logic)
                if (activeFilters.length === 0) {
                    matchingRows.push(row);
                    return;
                }

                const matchesAll = activeFilters.every(f => {
                    if (f.key === 'tanggal_range') {
                        const rowDateStr = row.data('tanggal-raw') || '';
                        if (!rowDateStr) return false;
                        const rowDate = new Date(rowDateStr + 'T00:00:00');
                        return rowDate >= f.from && rowDate <= f.to;
                    }

                    let fieldVal = '';
                    if (f.key === 'query') {
                        fieldVal = (row.data('search') || '').toString().toLowerCase();
                    } else if (f.key === 'barang') {
                        fieldVal = (row.data('barang') || '').toString().toLowerCase();
                    } else if (f.key === 'kode') {
                        fieldVal = (row.data('kode') || '').toString().toLowerCase();
                    } else if (f.key === 'ruangan') {
                        fieldVal = (row.data('ruangan') || '').toString().toLowerCase();
                    } else if (f.key === 'keperluan') {
                        fieldVal = (row.data('keperluan') || '').toString().toLowerCase();
                    } else if (f.key === 'status') {
                        fieldVal = (row.data('status') || '').toString().toLowerCase();
                    } else {
                        fieldVal = (row.data('search') || '').toString().toLowerCase();
                    }
                    return fieldVal.indexOf(f.val) !== -1;
                });

                if (matchesAll) {
                    matchingRows.push(row);
                }
            });

            const totalMatching = matchingRows.length;
            const totalPages = Math.ceil(totalMatching / pageSize) || 1;

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, totalMatching);

            // Hide all rows first
            rows.hide();

            // Show current page items
            for (let i = startIndex; i < endIndex; i++) {
                if (matchingRows[i]) {
                    matchingRows[i].show();
                }
            }

            // Update UI elements
            if (totalMatching === 0) {
                $('#noSearchMatch').show();
                $('#bookingTable').hide();
                $('#paginationBar').hide();
                $('#toolbarTotalCount').text('0');
            } else {
                $('#noSearchMatch').hide();
                $('#bookingTable').show();
                $('#paginationBar').show();
                $('#showingStart').text(startIndex + 1);
                $('#showingEnd').text(endIndex);
                $('#showingTotal').text(totalMatching);
                $('#toolbarTotalCount').text(totalMatching);
                renderPaginationControls(totalPages);
            }
        }

        // Close dropdowns on outside click
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.custom-dropdown-container').length && !$(e.target).closest('.custom-dropdown-menu').length) {
                $('.custom-dropdown-menu').hide();
                $('.dropdown-arrow').css('transform', 'rotate(0deg)');
            }
        });

        // Initialize on DOM ready
        $(document).ready(function() {
            applyFilterAndSearch();
        });
    </script>
</body>
</html>
