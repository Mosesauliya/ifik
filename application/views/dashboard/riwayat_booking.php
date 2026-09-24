<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Riwayat Peminjaman Ruangan - IFIK' ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome & Remixicon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery & SweetAlert2 & Flatpickr & html2pdf & JSZip -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

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
            max-width: 1200px;
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
        }

        .header-title-group p {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 5px;
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
            margin-bottom: 24px;
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

        /* Ambient Glow Backdrop */
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

        /* Top Row Content */
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

        /* 3D Claymorphic Icon Box */
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

        /* Bottom Row Divider & Pulse Dots */
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

        /* Theme Variants */
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
        .theme-sky {
            --card-accent: #0284c7;
            --card-accent-soft: rgba(2, 132, 199, 0.14);
            --card-icon-bg: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            --card-icon-border: #bae6fd;
            --card-icon-shadow: rgba(2, 132, 199, 0.18);
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

        /* Stat Slider Dots Indicator */
        .stat-slider-dots {
            display: none;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: -12px;
            margin-bottom: 20px;
        }

        .stat-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: #cbd5e1;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .stat-dot.active {
            width: 22px;
            background: #ea580c;
            border-radius: 999px;
        }

        /* Filter Pills Scroll Dots Indicator */
        .filter-pills-dots {
            display: none;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: -6px;
            margin-bottom: 2px;
        }

        .pill-dot {
            width: 5px;
            height: 5px;
            border-radius: 999px;
            background: #cbd5e1;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .pill-dot.active {
            width: 18px;
            background: #ea580c;
            border-radius: 999px;
        }

        .standalone-btn-text {
            display: none;
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

        /* Filter & Search Bar */
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

        /* Unified Multi-Search Pill Component & Extra Rows (Full Width 100%) */
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
            min-width: 220px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.12);
            z-index: 99999;
            padding: 6px;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .dropdown-item:hover {
            background: #fff7ed;
            color: #ea580c;
        }
        .dropdown-item.active {
            background: #fff7ed;
            color: #ea580c;
            font-weight: 700;
        }

        .btn-search-cari {
            padding: 7px 16px;
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: #fff;
            font-size: 0.82rem;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.25);
            transition: all 0.2s ease;
        }
        .btn-search-cari:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.35);
        }

        /* Flatpickr Custom Styling & Reset */
        #modeTanggal input,
        #modeTanggal input.flatpickr-input,
        #modeTanggal input.form-control,
        #modeTanggal input.flatpickr-custom-alt-input,
        .extra-filter-row input.flatpickr-input,
        .extra-filter-row input.form-control,
        .extra-filter-row input.flatpickr-custom-alt-input {
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            cursor: pointer !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            height: auto !important;
            font-family: inherit !important;
        }

        .flatpickr-calendar {
            border-radius: 16px !important;
            box-shadow: 0 16px 36px -6px rgba(15, 23, 42, 0.18), 0 4px 12px rgba(0,0,0,0.08) !important;
            border: 1.5px solid #fed7aa !important;
            font-family: inherit !important;
            z-index: 999999 !important;
        }
        .flatpickr-calendar .flatpickr-day.selected,
        .flatpickr-calendar .flatpickr-day.startRange,
        .flatpickr-calendar .flatpickr-day.endRange {
            background: #ea580c !important;
            border-color: #ea580c !important;
            color: #fff !important;
        }
        .flatpickr-calendar .flatpickr-day.inRange {
            background: #ffedd5 !important;
            border-color: #ffedd5 !important;
            color: #ea580c !important;
        }
        .flatpickr-calendar .flatpickr-day:hover {
            background: #fed7aa !important;
            border-color: #fed7aa !important;
            color: #9a3412 !important;
        }
        .flatpickr-months .flatpickr-month {
            background: #fff7ed !important;
            color: #1e293b !important;
            border-top-left-radius: 14px !important;
            border-top-right-radius: 14px !important;
        }

        /* Desktop riwayat-table structure & column widths */
        .table-responsive {
            width: 100%;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            background: #ffffff;
            min-height: 280px;
            padding-bottom: 20px;
            box-sizing: border-box;
        }

        .riwayat-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.86rem;
            table-layout: auto;
        }

        .riwayat-table th {
            background: #f8fafc;
            padding: 12px 14px;
            font-weight: 700;
            color: #475569;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }

        .riwayat-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
            position: relative;
        }

        .riwayat-table tr:hover td {
            background: #fffbf7;
        }

        .riwayat-table th.booking-check-col, .riwayat-table td.booking-check-col {
            width: 38px;
            text-align: center !important;
            padding-left: 12px !important;
            padding-right: 6px !important;
        }

        .riwayat-table th.action-th, .riwayat-table td:last-child {
            width: 90px;
            text-align: right !important;
            padding-right: 14px !important;
        }

        /* Badge Status */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            text-transform: none;
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
        .badge-status.dibatalkan {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .room-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 700;
            color: #0f172a;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 0.8rem;
            max-width: 170px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .agenda-text {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.84rem;
            max-width: 200px;
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

        .time-badge {
            display: flex;
            flex-direction: column;
            gap: 2px;
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
            white-space: nowrap;
        }

        /* Custom Checkbox & Selection */
        .booking-checkbox, .custom-checkbox-all {
            width: 17px;
            height: 17px;
            border-radius: 5px;
            cursor: pointer;
            accent-color: #ea580c;
            vertical-align: middle;
            transition: transform 0.15s ease;
        }
        .booking-checkbox:hover, .custom-checkbox-all:hover {
            transform: scale(1.15);
        }
        .booking-row.row-selected {
            background: #fff7ed !important;
        }

        /* Action Buttons & Dropdown (3-Dots + Teks Aksi) */
        .action-dropdown-wrap {
            position: relative;
            display: inline-block;
            text-align: right;
        }
        .btn-action-dropdown {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.76rem;
            font-weight: 700;
            color: #334155;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            white-space: nowrap;
            outline: none;
        }
        .btn-action-dropdown:hover {
            background: #fff7ed;
            color: #ea580c;
            border-color: #fdba74;
            box-shadow: 0 3px 8px rgba(234, 88, 12, 0.12);
        }
        .btn-action-dropdown i {
            font-size: 0.8rem;
            color: #64748b;
            transition: transform 0.2s ease;
        }
        .btn-action-dropdown:hover i {
            color: #ea580c;
        }
        .action-menu-popup {
            position: absolute;
            right: 0;
            top: calc(100% + 6px);
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.16);
            padding: 6px;
            min-width: 195px;
            z-index: 1000;
            display: none;
            flex-direction: column;
            gap: 3px;
            animation: dropdownFadeIn 0.15s ease;
        }
        .action-menu-popup.show {
            display: flex !important;
        }

        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .action-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #334155;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .action-menu-item:hover {
            background: #f8fafc;
            color: #0f172a;
        }
        .action-menu-item i {
            font-size: 0.85rem;
            width: 16px;
            text-align: center;
            flex-shrink: 0;
        }
        .action-menu-item.item-danger {
            color: #e11d48;
        }
        .action-menu-item.item-danger:hover {
            background: #fff1f2;
            color: #be123c;
        }

        /* Floating Multi-Select Action Bar */
        .floating-action-bar {
            position: fixed;
            bottom: 28px;
            left: 50%;
            transform: translateX(-50%) translateY(50px);
            background: rgba(15, 23, 42, 0.94);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            color: #fff;
            padding: 8px 12px 8px 16px;
            border-radius: 50px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.12);
            z-index: 99999;
            display: none;
            align-items: center;
            gap: 12px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            opacity: 0;
            pointer-events: none;
            max-width: 92vw;
            box-sizing: border-box;
        }
        .floating-action-bar.show {
            display: flex;
            transform: translateX(-50%) translateY(0);
            opacity: 1;
            pointer-events: auto;
        }
        .floating-bar-header {
            display: contents;
        }
        .floating-badge {
            order: 1;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #f8fafc;
            white-space: nowrap;
        }
        .floating-badge i {
            color: #fb923c;
        }
        .floating-actions {
            order: 2;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-floating-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            outline: none;
        }
        .btn-floating-surat {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #fff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }
        .btn-floating-surat:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.45);
        }
        .btn-floating-cancel {
            background: linear-gradient(135deg, #e11d48, #f43f5e);
            color: #fff;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.35);
        }
        .btn-floating-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(225, 29, 72, 0.45);
        }
        .btn-floating-close {
            order: 3;
            background: rgba(255, 255, 255, 0.12);
            border: none;
            color: #cbd5e1;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .btn-floating-close:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        .btn-table-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.78rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .btn-surat {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }
        .btn-surat:hover {
            background: #10b981;
            color: #fff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }
        .btn-detail {
            background: #f8fafc;
            color: #475569;
            border-color: #cbd5e1;
        }
        .btn-detail:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .btn-cancel {
            background: #fff1f2;
            color: #e11d48;
            border-color: #fecdd3;
        }
        .btn-cancel:hover {
            background: #e11d48;
            color: #fff;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25);
        }

        /* Pagination Bar & Controls */
        .pagination-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 20px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-radius: 0 0 20px 20px;
            font-size: 0.78rem;
            color: #64748b;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .page-btn {
            height: 32px;
            min-width: 32px;
            padding: 0 8px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .page-btn:hover:not(:disabled) {
            border-color: #ea580c;
            color: #ea580c;
            background: #fff7ed;
        }
        .page-btn.active {
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            border-color: #ea580c;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
        }
        .page-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .page-size-selector {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #475569;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 4px 10px;
            border-radius: 10px;
        }
        .page-size-selector select {
            height: 24px;
            padding: 0 4px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            color: #1e293b;
            cursor: pointer;
            outline: none;
        }

        /* Empty State */
        #noDataRow {
            width: 100%;
        }
        #noDataRow td {
            text-align: center;
            border: none;
            width: 100%;
            padding: 0;
        }
        .empty-state {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            padding: 56px 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .empty-state .empty-state-icon {
            font-size: 3.2rem;
            color: #cbd5e1;
            margin-bottom: 4px;
        }
        .empty-state h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #334155;
        }
        .empty-state p {
            font-size: 0.88rem;
            color: #94a3b8;
            max-width: 380px;
            line-height: 1.5;
        }
        .btn-empty-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 12px;
            font-size: 0.84rem;
            font-weight: 700;
            color: #fff !important;
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.25);
            transition: all 0.2s ease;
            margin-top: 6px;
        }
        .btn-empty-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(234, 88, 12, 0.35);
            color: #fff !important;
        }
        .btn-empty-action i {
            font-size: 0.78rem !important;
        }

        /* Surat Modal & Detail Modal */
        .modal-backdrop-custom {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-card-custom {
            background: #fff;
            border-radius: 24px;
            width: 100%;
            max-width: 800px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
            animation: modalFadeIn 0.25s ease;
            overflow: hidden;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.96) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-header-custom {
            padding: 18px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-header-custom h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #94a3b8;
            cursor: pointer;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .modal-close-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .modal-body-custom {
            padding: 24px;
            overflow-y: auto;
            flex-grow: 1;
        }
        .modal-iframe-wrap {
            width: 100%;
            height: 600px;
            border: none;
            border-radius: 12px;
            background: #f8fafc;
        }

        /* Detail Modal Grid */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .detail-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
        }
        .detail-item.full-span {
            grid-column: 1 / -1;
        }
        .detail-item-label {
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.05em;
        }
        .detail-item-value {
            font-size: 0.92rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 4px;
            word-break: break-word;
        }

        /* Media Query for Mobile Layout (<640px) */
        @media (max-width: 640px) {
            body {
                padding: 0 !important;
            }
            .page-wrapper-for-sidebar {
                padding: 56px 8px 32px 8px !important;
            }
            .main-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                gap: 12px !important;
            }
            .page-header-card {
                padding: 16px 14px !important;
                border-radius: 18px !important;
            }
            .header-title-group h1 {
                font-size: 1.22rem !important;
                gap: 8px !important;
            }
            .header-title-group p {
                font-size: 0.8rem !important;
                margin-top: 3px !important;
            }
            .header-actions {
                width: 100%;
                justify-content: stretch;
            }
            .header-actions .btn-action-main {
                flex: 1;
                justify-content: center;
                padding: 9px 12px !important;
                font-size: 0.82rem !important;
                border-radius: 12px !important;
            }
            .stat-cards-grid {
                display: flex !important;
                flex-direction: row !important;
                overflow-x: auto !important;
                overflow-y: hidden !important;
                scroll-snap-type: x mandatory;
                -webkit-overflow-scrolling: touch;
                gap: 12px !important;
                padding: 2px 2px 8px 2px;
                margin-bottom: 4px !important;
                scrollbar-width: none;
                -ms-overflow-style: none;
                width: 100%;
            }
            .stat-cards-grid::-webkit-scrollbar {
                display: none;
            }
            .stat-card-highlight {
                flex: 0 0 100% !important;
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
                scroll-snap-align: start;
                scroll-snap-stop: always;
                padding: 16px 16px 14px 16px !important;
                border-radius: 18px !important;
            }
            .stat-slider-dots {
                display: flex;
            }
            .content-card {
                padding: 14px 10px !important;
                border-radius: 18px !important;
                gap: 12px !important;
            }

            /* Filter Pills Horizontal Scroll & Dots */
            .filter-pills {
                display: flex !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                -ms-overflow-style: none;
                padding: 2px 2px 6px 2px;
                width: 100%;
                gap: 8px;
            }
            .filter-pills::-webkit-scrollbar {
                display: none;
            }
            .pill-btn {
                flex-shrink: 0 !important;
                white-space: nowrap !important;
            }
            .filter-pills-dots {
                display: flex;
            }

            /* Multi-Search Mobile Optimization */
            .search-pill-container {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 10px !important;
                width: 100% !important;
            }

            .unified-search-pill {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
                height: 44px !important;
                padding: 2px 8px !important;
            }

            .category-text-label {
                max-width: 100px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-size: 0.82rem !important;
            }

            .btn-search-cari {
                margin-left: 6px !important;
                padding: 6px 14px !important;
                font-size: 0.78rem !important;
                gap: 4px !important;
                border-radius: 10px !important;
                white-space: nowrap !important;
            }

            .standalone-btn-text {
                display: inline !important;
            }

            .btn-standalone-add {
                width: 100% !important;
                height: 44px !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                padding: 0 16px !important;
                font-size: 0.84rem !important;
                border-radius: 14px !important;
                box-sizing: border-box !important;
            }

            .extra-rows-card {
                position: relative !important;
                top: auto !important;
                left: auto !important;
                right: auto !important;
                width: 100% !important;
                border-radius: 16px !important;
                margin-top: 6px !important;
                padding: 14px 12px !important;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06) !important;
                z-index: 50 !important;
                box-sizing: border-box !important;
            }

            .extra-filter-row {
                width: 100% !important;
                display: flex !important;
                align-items: center !important;
                gap: 8px !important;
                margin-bottom: 8px !important;
            }

            .extra-filter-row .unified-search-pill {
                flex: 1 !important;
                width: auto !important;
                min-width: 0 !important;
                height: 46px !important;
            }

            .extra-filter-row .btn-remove-row {
                width: 44px !important;
                height: 44px !important;
                border-radius: 12px !important;
                flex-shrink: 0 !important;
            }

            /* =========================================================
               RESPONSIVE TABLE TO MOBILE CARDS TRANSFORMATION (<640px)
               ========================================================= */
            .table-responsive {
                overflow: visible !important;
            }

            table.riwayat-table {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                border: none !important;
            }

            table.riwayat-table thead {
                display: none !important;
            }

            table.riwayat-table tbody {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
                width: 100% !important;
                min-width: 0 !important;
            }

            #noDataRow {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 100% !important;
                box-sizing: border-box !important;
                border: none !important;
                background: transparent !important;
                padding: 0 !important;
            }

            #noDataRow td {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 100% !important;
                box-sizing: border-box !important;
                padding: 0 !important;
                border: none !important;
            }

            .empty-state {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                padding: 44px 16px !important;
            }

            .empty-state p {
                max-width: 100% !important;
            }

            .booking-row {
                display: flex !important;
                flex-direction: column !important;
                background: #ffffff !important;
                border: 1.5px solid #e2e8f0 !important;
                border-radius: 16px !important;
                padding: 12px 12px 12px 12px !important;
                box-shadow: 0 3px 12px rgba(15, 23, 42, 0.04) !important;
                gap: 9px !important;
                position: relative !important;
                box-sizing: border-box !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s !important;
            }

            .booking-row[style*="display: none"] {
                display: none !important;
            }

            .booking-row:hover {
                border-color: #cbd5e1 !important;
                box-shadow: 0 6px 20px rgba(15, 23, 42, 0.08) !important;
            }

            .booking-row.row-selected,
            .booking-row:has(.booking-checkbox:checked) {
                border-color: #fdba74 !important;
                background: #fffbf7 !important;
                box-shadow: 0 4px 16px rgba(234, 88, 12, 0.09) !important;
            }

            .booking-row td {
                display: block !important;
                padding: 0 !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                box-sizing: border-box !important;
            }

            /* TD 1: Checkbox (Top Left Absolute) */
            .booking-row td:nth-child(1) {
                position: absolute !important;
                top: 13px !important;
                left: 14px !important;
                width: auto !important;
                z-index: 2 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .booking-row td:nth-child(1) .booking-checkbox {
                margin: 0 !important;
                padding: 0 !important;
                width: 16px !important;
                height: 16px !important;
                cursor: pointer;
            }

            /* TD 5: Status Badge (Top Bar, Next to Checkbox) */
            .booking-row td:nth-child(5) {
                order: 1 !important;
                display: flex !important;
                align-items: center !important;
                padding-left: 36px !important;
                padding-right: 70px !important;
                min-height: 24px !important;
            }

            .booking-row .badge-status {
                display: inline-flex !important;
                align-items: center !important;
                gap: 5px !important;
                width: auto !important;
                max-width: fit-content !important;
                height: 22px !important;
                padding: 2px 8px !important;
                font-size: 0.68rem !important;
                font-weight: 700 !important;
                border-radius: 9999px !important;
                text-transform: none !important;
                letter-spacing: normal !important;
                justify-content: flex-start !important;
                white-space: nowrap !important;
                line-height: 1 !important;
            }

            .booking-row .badge-status i {
                font-size: 0.65rem !important;
            }

            /* TD 6: Aksi Dropdown (Top Right Absolute) */
            .booking-row td:nth-child(6) {
                position: absolute !important;
                top: 12px !important;
                right: 14px !important;
                width: auto !important;
                z-index: 20 !important;
                padding: 0 !important;
                text-align: right !important;
            }

            .booking-row .btn-action-dropdown {
                height: 24px !important;
                padding: 2px 8px !important;
                font-size: 0.72rem !important;
                border-radius: 6px !important;
                gap: 3px !important;
            }

            .booking-row .action-menu-popup {
                right: 0 !important;
                left: auto !important;
                top: calc(100% + 4px) !important;
            }

            /* TD 2: Ruangan & ID (Order 2) */
            .booking-row td:nth-child(2) {
                order: 2 !important;
                margin-top: 2px !important;
            }

            .booking-row .room-pill {
                display: inline-flex !important;
                align-items: center !important;
                gap: 6px !important;
                font-size: 0.78rem !important;
                font-weight: 700 !important;
                padding: 3px 8px !important;
                background: #fff7ed !important;
                color: #ea580c !important;
                border: 1px solid #ffedd5 !important;
                border-radius: 8px !important;
                width: fit-content !important;
                max-width: 100% !important;
            }

            /* TD 3: Agenda / Keterangan (Order 3) */
            .booking-row td:nth-child(3) {
                order: 3 !important;
            }

            .booking-row .agenda-text {
                font-size: 0.88rem !important;
                font-weight: 700 !important;
                color: #1e293b !important;
                line-height: 1.4 !important;
                white-space: normal !important;
                max-width: 100% !important;
            }

            .booking-row .agenda-sub {
                font-size: 0.74rem !important;
                color: #94a3b8 !important;
                margin-top: 2px !important;
                font-weight: 500 !important;
            }

            /* TD 4: Tanggal & Waktu (Order 4) */
            .booking-row td:nth-child(4) {
                order: 4 !important;
                margin-top: 2px !important;
            }

            .booking-row .time-badge {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: wrap !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 6px !important;
                background: #f8fafc !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 10px !important;
                padding: 6px 12px !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .booking-row .time-date {
                font-size: 0.76rem !important;
                font-weight: 700 !important;
                color: #334155 !important;
            }

            .booking-row .time-hours {
                font-size: 0.74rem !important;
                font-weight: 700 !important;
                color: #ea580c !important;
            }

            .search-box {
                max-width: 100%;
            }
            .detail-grid {
                grid-template-columns: 1fr;
            }

            /* Floating Action Bar Mobile Optimization */
            .floating-action-bar {
                left: 12px !important;
                right: 12px !important;
                bottom: 16px !important;
                transform: translateY(50px) !important;
                width: auto !important;
                max-width: none !important;
                border-radius: 20px !important;
                padding: 12px 14px !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 10px !important;
                box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.15) !important;
            }
            .floating-action-bar.show {
                transform: translateY(0) !important;
            }
            .floating-bar-header {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                width: 100% !important;
                order: 1 !important;
            }
            .floating-badge {
                order: 1 !important;
                padding: 5px 12px !important;
                font-size: 0.78rem !important;
                border-radius: 20px !important;
            }
            .btn-floating-close {
                order: 2 !important;
                width: 28px !important;
                height: 28px !important;
                font-size: 0.75rem !important;
                border-radius: 50% !important;
            }
            .floating-actions {
                order: 2 !important;
                display: flex !important;
                flex-direction: row !important;
                align-items: stretch !important;
                gap: 8px !important;
                width: 100% !important;
            }
            .btn-floating-action {
                flex: 1 !important;
                min-width: 0 !important;
                padding: 10px 8px !important;
                font-size: 0.76rem !important;
                border-radius: 12px !important;
                justify-content: center !important;
                text-align: center !important;
                gap: 5px !important;
            }
            .btn-floating-action span {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>

    <!-- Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Page Content Wrapper (Smoothly shifts when sidebar is open) -->
    <div id="mainPageContent" class="page-wrapper-for-sidebar">
        <div class="main-wrapper">
            
            <!-- Header Section -->
            <div class="page-header-card">
            <div class="header-title-group">
                <h1>
                    <span>📋</span>
                    <span>Riwayat Peminjaman Ruangan</span>
                </h1>
                <p>Pantau status persetujuan, cetak Surat QR Resmi, dan kelola pengajuan laboratorium Anda.</p>
            </div>
            <div class="header-actions">
                <a href="<?= site_url('dashboard') ?>" class="btn-action-main btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Dashboard</span>
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

            <!-- 3. Disetujui / Surat QR -->
            <div class="stat-card-highlight theme-emerald">
                <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                <div class="stat-card-top">
                    <div class="stat-card-meta">
                        <div class="stat-card-label">Disetujui / Surat QR</div>
                        <div class="stat-card-val"><span id="stat-disetujui"><?= $total_disetujui ?? 0 ?></span></div>
                        <div class="stat-card-desc">Surat resmi ber-QR Code aktif</div>
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

        <!-- Mobile Stat Slider Pagination Dots -->
        <div class="stat-slider-dots" id="statSliderDots"></div>

        <!-- Table & Filter Section -->
        <div class="content-card">
            <div class="filter-toolbar">
                <div class="filter-pills" id="filterPillsWrap">
                    <button class="pill-btn active" data-filter="all">
                        <span>Semua</span>
                        <span class="pill-count" id="pill-all"><?= $total_pengajuan ?? 0 ?></span>
                    </button>
                    <button class="pill-btn" data-filter="menunggu">
                        <span>⏳ Menunggu Persetujuan</span>
                        <span class="pill-count" id="pill-menunggu"><?= $total_menunggu ?? 0 ?></span>
                    </button>
                    <button class="pill-btn" data-filter="disetujui">
                        <span>✅ Disetujui</span>
                        <span class="pill-count" id="pill-disetujui"><?= $total_disetujui ?? 0 ?></span>
                    </button>
                    <button class="pill-btn" data-filter="ditolak">
                        <span>❌ Ditolak / Batal</span>
                        <span class="pill-count" id="pill-ditolak"><?= $total_ditolak ?? 0 ?></span>
                    </button>
                </div>

                <!-- Mobile Filter Pills Dots Indicator -->
                <div class="filter-pills-dots" id="filterPillsDots"></div>

                <!-- Unified MultiSearch Bar Component with Extra Filter Rows Popover -->
                <div class="search-pill-container" id="multiSearchWrapper">
                    <div class="unified-search-pill" id="mainSearchPill">
                        <!-- Category Selector Dropdown -->
                        <div class="custom-dropdown-container">
                            <input type="hidden" id="mainCategoryVal" value="query">
                            <button type="button" onclick="toggleCustomDropdown('main-cat', event)" style="display: flex; align-items: center; gap: 6px; background: none; border: none; cursor: pointer; font-weight: 700; font-size: 0.84rem; color: #1e293b; outline: none; padding: 4px 2px;" title="Kategori Pencarian">
                                <span id="label-filter-main-cat" class="text-sm sm:text-base leading-none block" style="font-size: 0.95rem; line-height: 1;">🔍</span>
                                <i class="fa-solid fa-chevron-down dropdown-arrow" id="arrow-filter-main-cat" style="font-size: 0.65rem; color: #94a3b8; transition: transform 0.2s;"></i>
                            </button>
                            <div id="menu-filter-main-cat" class="custom-dropdown-menu">
                                <div onclick="selectMainCategory('query', '🔍 Kata Kunci (Semua)', 'Ketik kata kunci lalu tekan Enter atau klik Cari...', this)" class="dropdown-item active"><span>🔍 Kata Kunci (Semua)</span></div>
                                <div onclick="selectMainCategory('ruangan', '🚪 Ruangan Lab', 'Cari nama/kode ruangan...', this)" class="dropdown-item"><span>🚪 Ruangan Lab</span></div>
                                <div onclick="selectMainCategory('agenda', '📝 Agenda / Keterangan', 'Cari agenda / keperluan...', this)" class="dropdown-item"><span>📝 Agenda / Keterangan</span></div>
                                <div onclick="selectMainCategory('tanggal', '📅 Tanggal Booking', 'Cari tanggal (cth: 15 Sep 2026)...', this)" class="dropdown-item"><span>📅 Tanggal Booking</span></div>
                                <div onclick="selectMainCategory('kategori', '🏷️ Kategori Pemakaian', 'Cari kategori pemakaian...', this)" class="dropdown-item"><span>🏷️ Kategori Pemakaian</span></div>
                                <div onclick="selectMainCategory('status', '⚡ Status Pengajuan', 'Cari status (Menunggu / Disetujui / Ditolak)...', this)" class="dropdown-item"><span>⚡ Status Pengajuan</span></div>
                            </div>
                        </div>

                        <div class="unified-divider"></div>

                        <!-- Input Value Container (Modes: Text & Tanggal Flatpickr) -->
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
                                <!-- Status Dropdown Menu -->
                                <div id="statusDropdownMenu" class="custom-dropdown-menu" style="min-width: 240px; top: calc(100% + 8px);">
                                    <div onclick="selectStatusFilter('', 'Semua Status', '#94a3b8', this)" class="dropdown-item active">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #94a3b8; display: inline-block; margin-right: 8px;"></span>
                                        <span>Semua Status</span>
                                    </div>
                                    <div onclick="selectStatusFilter('menunggu', '⏳ Menunggu Persetujuan', '#f59e0b', this)" class="dropdown-item">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; display: inline-block; margin-right: 8px;"></span>
                                        <span>⏳ Menunggu Persetujuan</span>
                                    </div>
                                    <div onclick="selectStatusFilter('disetujui', '✅ Disetujui', '#16a34a', this)" class="dropdown-item">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #16a34a; display: inline-block; margin-right: 8px;"></span>
                                        <span>✅ Disetujui</span>
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
                        <span class="standalone-btn-content" style="display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-filter" style="color: #ea580c; font-size: 0.75rem;"></i>
                            <span class="standalone-btn-text">Filter Tambahan</span>
                        </span>
                        <span id="filterCountBadge" class="badge-standalone-count">1/4</span>
                    </button>

                    <!-- Extra Filter Rows Card Popover -->
                    <div id="extraRowsCard" class="extra-rows-card">
                        <div id="additionalFilterRowsContainer" style="display: flex; flex-direction: column; gap: 8px;"></div>
                        
                        <div class="extra-rows-footer" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 12px; margin-top: 10px; font-size: 0.76rem; flex-wrap: wrap; gap: 10px;">
                            <span class="extra-rows-hint" style="color: #94a3b8; flex: 1; min-width: 180px;">Gunakan kombinasi kriteria untuk mempersempit pencarian data.</span>
                            <div class="extra-rows-actions" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
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

                <!-- Row 2: Page Size & Records Count -->
                <div style="padding-top: 10px; border-top: 1px solid #f1f5f9; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px;">
                    <div style="font-size: 0.76rem; color: #64748b; font-weight: 500;">
                        <span>Kelola & telusuri data peminjaman ruangan secara langsung.</span>
                    </div>

                    <!-- Page Size & Counter Right -->
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 12px; border-radius: 10px;">
                            <span style="font-weight: 500;">Tampilkan</span>
                            <select id="selectPerPage" onchange="changePageSize(this.value)" style="height: 24px; padding: 0 4px; font-size: 0.75rem; font-weight: 700; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; color: #1e293b; cursor: pointer; outline: none;">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span style="font-weight: 500;">data/hal</span>
                            <span style="color: #cbd5e1;">|</span>
                            <span>Total: <strong class="total-rows-count" id="toolbarTotalCount" style="color: #0f172a; font-weight: 800;"><?= $total_pengajuan ?? count($peminjaman ?? []) ?></strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Table -->
            <div class="table-responsive">
                <table class="riwayat-table" id="bookingTable">
                    <thead>
                        <tr>
                            <th class="booking-check-col">
                                <input type="checkbox" id="checkAllBookings" class="custom-checkbox-all" onchange="toggleSelectAll(this)" title="Pilih Semua di Halaman Ini">
                            </th>
                            <th>ID & Ruangan</th>
                            <th>Agenda / Keterangan</th>
                            <th>Tanggal & Waktu</th>
                            <th>Status Pengajuan</th>
                            <th style="text-align: right; width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bookingTableBody">
                        <?php if (empty($peminjaman)): ?>
                            <tr id="noDataRow">
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-calendar-xmark empty-state-icon"></i>
                                        <h3>Belum Ada Riwayat Peminjaman</h3>
                                        <p>Anda belum pernah mengajukan peminjaman ruangan laboratorium.</p>
                                        <a href="<?= site_url('ajukan-booking') ?>" class="btn-empty-action">
                                            <i class="fa-solid fa-plus"></i>
                                            <span>Ajukan Peminjaman</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($peminjaman as $row): 
                                $statusRaw = strtolower(trim($row['status'] ?? ''));
                                $statusClass = 'menunggu';
                                $statusLabel = 'Menunggu Persetujuan';
                                $statusIcon = 'fa-hourglass-half';
                                $filterType = 'menunggu';

                                if ($statusRaw === 'disetujui' || strpos($statusRaw, 'setuju') !== false) {
                                    $statusClass = 'disetujui';
                                    $statusLabel = 'Disetujui';
                                    $statusIcon = 'fa-circle-check';
                                    $filterType = 'disetujui';
                                } elseif ($statusRaw === 'ditolak' || strpos($statusRaw, 'tolak') !== false) {
                                    $statusClass = 'ditolak';
                                    $statusLabel = 'Ditolak';
                                    $statusIcon = 'fa-circle-xmark';
                                    $filterType = 'ditolak';
                                } elseif ($statusRaw === 'dibatalkan' || strpos($statusRaw, 'batal') !== false) {
                                    $statusClass = 'dibatalkan';
                                    $statusLabel = 'Dibatalkan';
                                    $statusIcon = 'fa-ban';
                                    $filterType = 'ditolak';
                                }

                                $tglFormat = !empty($row['tanggal_booking']) ? date('d M Y', strtotime($row['tanggal_booking'])) : (!empty($row['tanggal']) ? date('d M Y', strtotime($row['tanggal'])) : '-');
                                $jamFormat = (!empty($row['waktu_mulai']) ? substr($row['waktu_mulai'], 0, 5) : '-') . ' - ' . (!empty($row['waktu_selesai']) ? substr($row['waktu_selesai'], 0, 5) : '-');
                                $encodedData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                            ?>
                            <tr class="booking-row" 
                                id="row-<?= $row['id'] ?>"
                                data-id="<?= $row['id'] ?>"
                                data-status="<?= $filterType ?>" 
                                data-search="<?= strtolower(htmlspecialchars(($row['ruangan'] ?? '') . ' ' . ($row['agenda'] ?? '') . ' ' . ($row['keterangan'] ?? '') . ' ' . ($row['kategori'] ?? '') . ' ' . $tglFormat . ' ' . $statusLabel)) ?>"
                                data-ruangan="<?= strtolower(htmlspecialchars($row['ruangan'] ?? '')) ?>"
                                data-agenda="<?= strtolower(htmlspecialchars(($row['agenda'] ?? '') . ' ' . ($row['keterangan'] ?? ''))) ?>"
                                data-tanggal="<?= strtolower(htmlspecialchars($tglFormat . ' ' . ($row['tanggal_booking'] ?? '') . ' ' . ($row['tanggal'] ?? ''))) ?>"
                                data-tanggal-raw="<?= !empty($row['tanggal_booking']) ? $row['tanggal_booking'] : (!empty($row['tanggal']) ? $row['tanggal'] : '') ?>"
                                data-kategori="<?= strtolower(htmlspecialchars($row['kategori'] ?? '')) ?>">
                                <td class="booking-check-col">
                                    <input type="checkbox" class="booking-checkbox" value="<?= $row['id'] ?>" data-id="<?= $row['id'] ?>" data-status="<?= $statusClass ?>" data-ruangan="<?= htmlspecialchars($row['ruangan'] ?? 'Ruangan Lab') ?>" data-agenda="<?= htmlspecialchars($row['agenda'] ?? $row['keterangan'] ?? '-') ?>" data-tanggal="<?= $tglFormat ?>" onchange="updateFloatingBar()">
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <span class="room-pill">
                                            <i class="fa-solid fa-door-open" style="color: var(--primary);"></i>
                                            <?= htmlspecialchars($row['ruangan'] ?? 'Ruangan Lab') ?>
                                        </span>
                                        <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">
                                            #BK-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?>
                                            <?= !empty($row['kategori']) ? ' • ' . htmlspecialchars($row['kategori']) : '' ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="agenda-text" title="<?= htmlspecialchars($row['agenda'] ?? $row['keterangan'] ?? '-') ?>">
                                        <?= htmlspecialchars($row['agenda'] ?? $row['keterangan'] ?? '-') ?>
                                    </div>
                                    <div class="agenda-sub">
                                        <?= !empty($row['created_at']) ? 'Diajukan: ' . date('d/m/Y H:i', strtotime($row['created_at'])) : '' ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="time-badge">
                                        <span class="time-date"><i class="fa-regular fa-calendar" style="margin-right: 4px; color: #64748b;"></i><?= $tglFormat ?></span>
                                        <span class="time-hours"><i class="fa-regular fa-clock" style="margin-right: 4px; color: #94a3b8;"></i><?= $jamFormat ?> WIB</span>
                                    </div>
                                </td>
                                <td id="status-col-<?= $row['id'] ?>">
                                    <span class="badge-status <?= $statusClass ?>">
                                        <i class="fa-solid <?= $statusIcon ?>"></i>
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-dropdown-wrap" id="action-col-<?= $row['id'] ?>">
                                        <button type="button" class="btn-action-dropdown" onclick="toggleRowActionMenu('<?= $row['id'] ?>', event)">
                                            <span>Aksi</span>
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <div class="action-menu-popup" id="rowActionMenu_<?= $row['id'] ?>">
                                            <button type="button" class="action-menu-item" onclick='openDetailModal(<?= $encodedData ?>)'>
                                                <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                                                <span>Detail Peminjaman</span>
                                            </button>
                                            <?php if ($statusClass === 'disetujui'): ?>
                                                <button type="button" class="action-menu-item" onclick="openSuratModal('<?= $row['id'] ?>')">
                                                    <i class="fa-solid fa-qrcode" style="color: #059669;"></i>
                                                    <span>Surat QR Resmi</span>
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($statusClass === 'menunggu'): ?>
                                                <button type="button" class="action-menu-item item-danger" onclick="cancelMyBooking('<?= $row['id'] ?>')">
                                                    <i class="fa-solid fa-ban" style="color: #e11d48;"></i>
                                                    <span>Batalkan Pengajuan</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="pagination-bar" id="paginationBar">
                <div id="paginationInfo" style="font-size: 0.78rem; font-weight: 500; color: #64748b;">
                    Menampilkan <span id="pageInfoStart" style="font-weight: 700; color: #0f172a;">1</span> - <span id="pageInfoEnd" style="font-weight: 700; color: #0f172a;">10</span> dari <strong id="pageInfoTotal" style="color: #0f172a; font-weight: 800;"><?= $total_pengajuan ?? count($peminjaman ?? []) ?></strong> data
                </div>
                <div class="pagination-controls" id="paginationControls"></div>
            </div>

            <!-- Empty Search Results Placeholder -->
            <div id="noSearchMatch" style="display: none; padding: 40px 20px; text-align: center;">
                <i class="fa-solid fa-magnifying-glass" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                <h4 style="font-weight: 700; color: #475569;">Tidak Ada Pengajuan yang Cocok</h4>
                <p style="font-size: 0.85rem; color: #94a3b8;">Coba kata kunci lain atau ubah filter status.</p>
            </div>
        </div>

        </div> <!-- /main-wrapper -->
    </div> <!-- /mainPageContent -->

    <!-- Modal Preview Surat Resmi QR -->
    <div class="modal-backdrop-custom" id="suratModal">
        <div class="modal-card-custom" style="max-width: 900px; height: 90vh;">
            <div class="modal-header-custom">
                <h3>
                    <i class="fa-solid fa-file-shield" style="color: #059669;"></i>
                    <span>Surat Izin Peminjaman Ruangan Resmi (QR Verified)</span>
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeSuratModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body-custom" style="padding: 0; display: flex; flex-direction: column;">
                <iframe id="suratIframe" class="modal-iframe-wrap" style="flex-grow: 1; height: 100%; border-radius: 0;" src="about:blank"></iframe>
            </div>
        </div>
    </div>

    <!-- Modal Detail Peminjaman -->
    <div class="modal-backdrop-custom" id="detailModal">
        <div class="modal-card-custom" style="max-width: 600px;">
            <div class="modal-header-custom">
                <h3>
                    <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                    <span>Detail Peminjaman Ruangan</span>
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeDetailModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body-custom">
                <div class="detail-grid" id="detailGridContent">
                    <!-- Dynamic Content by JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Bar for Multiple Select -->
    <div class="floating-action-bar" id="floatingActionBar">
        <div class="floating-bar-header">
            <div class="floating-badge">
                <i class="fa-solid fa-check-double"></i>
                <span id="selectedCountText">0 Pengajuan Terpilih</span>
            </div>
            <button type="button" class="btn-floating-close" onclick="clearAllSelections()" title="Batal Pilih">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="floating-actions">
            <button type="button" class="btn-floating-action btn-floating-surat" id="btnFloatingSurat" onclick="bulkDownloadSurat()">
                <i class="fa-solid fa-file-arrow-down"></i>
                <span>Download Surat (<span id="countSurat">0</span>)</span>
            </button>
            <button type="button" class="btn-floating-action btn-floating-cancel" id="btnFloatingCancel" onclick="bulkCancelBookings()">
                <i class="fa-solid fa-ban"></i>
                <span>Batalkan Pengajuan (<span id="countCancel">0</span>)</span>
            </button>
        </div>
    </div>

    <script>
        let currentFilter = 'all';
        let extraRowCounter = 0;
        let fpRange = null;
        let extraFpInstances = {};
        let currentPage = 1;
        let pageSize = 10;

        const SEARCH_CATEGORIES = [
            { key: 'query', emoji: '🔍', label: '🔍 Kata Kunci (Semua)', placeholder: 'Ketik kata kunci lalu tekan Enter atau klik Cari...' },
            { key: 'ruangan', emoji: '🚪', label: '🚪 Ruangan Lab', placeholder: 'Cari nama / kode ruangan...' },
            { key: 'agenda', emoji: '📝', label: '📝 Agenda / Keterangan', placeholder: 'Cari agenda / keperluan...' },
            { key: 'tanggal', emoji: '📅', label: '📅 Tanggal Booking', placeholder: 'Pilih rentang tanggal...' },
            { key: 'kategori', emoji: '🏷️', label: '🏷️ Kategori Pemakaian', placeholder: 'Cari kategori pemakaian...' },
            { key: 'status', emoji: '⚡', label: '⚡ Status Pengajuan', placeholder: 'Cari status (Menunggu / Disetujui / Ditolak)...' }
        ];

        function getCategoryEmoji(key) {
            const found = SEARCH_CATEGORIES.find(c => c.key === key);
            return found ? (found.emoji || '🔍') : '🔍';
        }

        // Toggle Custom Dropdown Menu
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

        // Initialize Main Flatpickr
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

        // Main Status Dropdown Controls
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

        // Extra Row Status Dropdown Controls
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

        // Select MultiSearch Category on Main Pill
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

        // Toggle or Add Extra Filter Row Popover
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
                    addFilterRow('ruangan');
                }
            }
        }

        // Generate Extra Row Input HTML based on category
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
                            <div onclick="selectExtraStatus('${rowId}', 'disetujui', '✅ Disetujui', '#16a34a', this)" class="dropdown-item">
                                <span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;margin-right:8px;"></span> ✅ Disetujui
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

        // Add Extra Filter Row (Max 3 extra rows = 4 criteria total)
        function addFilterRow(defaultKey = 'ruangan', defaultVal = '') {
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

        // Select Category on Extra Row
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

        // Remove Extra Filter Row
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

        // Update Filter Badge Count (e.g. 1/4, 2/4, 3/4, 4/4)
        function updateFilterCountBadge() {
            const container = document.getElementById('additionalFilterRowsContainer');
            const count = 1 + (container ? container.children.length : 0);
            const badge = document.getElementById('filterCountBadge');
            if (badge) badge.innerText = count + '/4';
        }

        // Collect All Active Filter Criteria
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

        // Change Page Size (Rows Per Page)
        function changePageSize(sz) {
            pageSize = parseInt(sz, 10) || 10;
            currentPage = 1;
            applyFilterAndSearch();
        }

        // Navigate to Specific Page
        function goToPage(p) {
            currentPage = p;
            applyFilterAndSearch();
        }

        // Render Pagination Control Buttons
        function renderPaginationControls(totalPages) {
            const container = document.getElementById('paginationControls');
            if (!container) return;

            if (totalPages <= 0) {
                container.innerHTML = '';
                return;
            }

            let html = '';

            // Prev Button
            const prevDisabled = currentPage <= 1 ? 'disabled' : '';
            html += `<button type="button" class="page-btn" ${prevDisabled} onclick="goToPage(${currentPage - 1})" title="Halaman Sebelumnya">
                <i class="fa-solid fa-chevron-left" style="font-size: 0.65rem;"></i>
            </button>`;

            // Page numbers with ellipsis
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

            // Next Button
            const nextDisabled = currentPage >= totalPages ? 'disabled' : '';
            html += `<button type="button" class="page-btn" ${nextDisabled} onclick="goToPage(${currentPage + 1})" title="Halaman Berikutnya">
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
            </button>`;

            container.innerHTML = html;
        }

        // Apply Filter & Search (Triggered ONLY on Click Cari, Enter Key, or Filter Pill)
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
                    } else if (f.key === 'ruangan') {
                        fieldVal = (row.data('ruangan') || '').toString().toLowerCase();
                    } else if (f.key === 'agenda') {
                        fieldVal = (row.data('agenda') || '').toString().toLowerCase();
                    } else if (f.key === 'kategori') {
                        fieldVal = (row.data('kategori') || '').toString().toLowerCase();
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

            if (currentPage > totalPages) {
                currentPage = totalPages;
            }
            if (currentPage < 1) {
                currentPage = 1;
            }

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, totalMatching);

            // Hide all rows first
            rows.hide();

            // Show only the sliced items for current page
            for (let i = startIndex; i < endIndex; i++) {
                if (matchingRows[i]) {
                    matchingRows[i].show();
                }
            }

            // Update Pagination UI elements
            if (totalMatching === 0) {
                $('#noSearchMatch').show();
                $('#bookingTable').hide();
                $('#paginationBar').hide();
            } else {
                $('#noSearchMatch').hide();
                $('#bookingTable').show();
                $('#paginationBar').show();

                $('#toolbarTotalCount').text(totalMatching);
                $('#pageInfoStart').text(startIndex + 1);
                $('#pageInfoEnd').text(endIndex);
                $('#pageInfoTotal').text(totalMatching);

                renderPaginationControls(totalPages);
            }

            // Sync floating bar & header checkbox with visible rows
            updateFloatingBar();
        }

        // Selection State & Functions
        let selectedBookingIds = [];

        function toggleRowActionMenu(id, e) {
            if (e) { e.stopPropagation(); e.preventDefault(); }
            const menu = $('#rowActionMenu_' + id);
            const isVisible = menu.hasClass('show');
            $('.action-menu-popup').removeClass('show');
            if (!isVisible) {
                menu.addClass('show');
            }
        }

        function toggleSelectAll(el) {
            const isChecked = $(el).is(':checked');
            $('.booking-row:visible').each(function() {
                const cb = $(this).find('.booking-checkbox');
                cb.prop('checked', isChecked);
                if (isChecked) {
                    $(this).addClass('row-selected');
                } else {
                    $(this).removeClass('row-selected');
                }
            });
            updateFloatingBar();
        }

        function updateFloatingBar() {
            selectedBookingIds = [];
            let selectedApproved = [];
            let selectedPending = [];

            $('.booking-checkbox:checked').each(function() {
                const id = String($(this).val());
                const status = $(this).data('status');
                const ruangan = $(this).data('ruangan');
                const agenda = $(this).data('agenda');
                const tanggal = $(this).data('tanggal');

                selectedBookingIds.push(id);
                $(this).closest('tr').addClass('row-selected');

                if (status === 'disetujui') {
                    selectedApproved.push({ id, ruangan, agenda, tanggal });
                } else if (status === 'menunggu') {
                    selectedPending.push({ id, ruangan, agenda, tanggal });
                }
            });

            $('.booking-checkbox:not(:checked)').each(function() {
                $(this).closest('tr').removeClass('row-selected');
            });

            const totalSelected = selectedBookingIds.length;
            const visibleRows = $('.booking-row:visible');
            const totalVisible = visibleRows.length;
            const checkedVisible = visibleRows.find('.booking-checkbox:checked').length;

            if (totalVisible > 0 && checkedVisible === totalVisible) {
                $('#checkAllBookings').prop('checked', true).prop('indeterminate', false);
            } else if (checkedVisible > 0) {
                $('#checkAllBookings').prop('checked', false).prop('indeterminate', true);
            } else {
                $('#checkAllBookings').prop('checked', false).prop('indeterminate', false);
            }

            if (totalSelected > 0) {
                $('#selectedCountText').text(totalSelected + ' Pengajuan Terpilih');
                $('#countSurat').text(selectedApproved.length);
                $('#countCancel').text(selectedPending.length);

                if (selectedApproved.length === 0) {
                    $('#btnFloatingSurat').hide();
                } else {
                    $('#btnFloatingSurat').show();
                }

                if (selectedPending.length === 0) {
                    $('#btnFloatingCancel').hide();
                } else {
                    $('#btnFloatingCancel').show();
                }

                $('#floatingActionBar').addClass('show').fadeIn(150);
            } else {
                $('#floatingActionBar').removeClass('show').fadeOut(150);
            }
        }

        function clearAllSelections() {
            $('.booking-checkbox').prop('checked', false);
            $('.booking-row').removeClass('row-selected');
            $('#checkAllBookings').prop('checked', false).prop('indeterminate', false);
            selectedBookingIds = [];
            $('#floatingActionBar').removeClass('show').fadeOut(150);
        }

        function bulkCancelBookings() {
            let pendingIds = [];
            $('.booking-checkbox:checked').each(function() {
                if ($(this).data('status') === 'menunggu') {
                    pendingIds.push(String($(this).val()));
                }
            });

            if (pendingIds.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Ada yang Pending',
                    text: 'Dari pengajuan yang dipilih, tidak ada yang berstatus Menunggu Persetujuan.',
                    confirmButtonColor: '#ea580c',
                    borderRadius: '16px'
                });
                return;
            }

            Swal.fire({
                title: 'Batalkan ' + pendingIds.length + ' Pengajuan?',
                text: 'Pengajuan ruangan yang berstatus Menunggu Persetujuan akan dibatalkan bersamaan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Batalkan Semua!',
                cancelButtonText: 'Kembali',
                reverseButtons: true,
                borderRadius: '16px'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang membatalkan pengajuan',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: "<?= site_url('riwayat-booking/bulk-cancel') ?>",
                        type: 'POST',
                        data: { ids: pendingIds },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Dibatalkan',
                                    text: response.message,
                                    timer: 2500,
                                    showConfirmButton: false,
                                    borderRadius: '16px'
                                });
                                clearAllSelections();
                                syncLiveBookingData();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Membatalkan',
                                    text: response.message,
                                    borderRadius: '16px'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Sistem',
                                text: 'Tidak dapat menghubungi server. Silakan coba beberapa saat lagi.',
                                borderRadius: '16px'
                            });
                        }
                    });
                }
            });
        }

        async function getSuratPdfBlob(id) {
            const url = "<?= site_url('verifikasi/surat/cetak/') ?>" + id;
            const res = await fetch(url);
            const htmlText = await res.text();

            // Create invisible iframe sandbox positioned at (0,0) with exact A4 dimensions
            const frame = document.createElement('iframe');
            frame.style.position = 'fixed';
            frame.style.left = '0';
            frame.style.top = '0';
            frame.style.width = '794px';
            frame.style.height = '1123px';
            frame.style.border = 'none';
            frame.style.background = '#ffffff';
            frame.style.opacity = '0';
            frame.style.pointerEvents = 'none';
            frame.style.zIndex = '-99999';
            document.body.appendChild(frame);

            const doc = frame.contentWindow.document;
            doc.open();
            doc.write(htmlText);
            doc.close();

            // Wait for QR code image and resources to load
            let attempts = 0;
            while (attempts < 25) {
                const qrImg = doc.querySelector('#qrcode img');
                if (qrImg && qrImg.src && qrImg.naturalWidth > 0) {
                    break;
                }
                await new Promise(r => setTimeout(r, 100));
                attempts++;
            }
            await new Promise(r => setTimeout(r, 300));

            // Clean & prepare styles for single-page 1:1 A4 capture
            const docBody = doc.body;
            if (docBody) {
                docBody.style.background = '#ffffff';
                docBody.style.margin = '0';
                docBody.style.padding = '0';
                docBody.style.width = '794px';
            }

            const qrBox = doc.querySelector('.qr-box');
            if (qrBox) {
                qrBox.style.border = 'none';
                qrBox.style.boxShadow = 'none';
                qrBox.style.background = 'transparent';
                qrBox.style.padding = '0';
            }

            const noPrint = doc.querySelector('.no-print');
            if (noPrint) noPrint.remove();

            const paperEl = doc.querySelector('.paper') || doc.body;
            if (paperEl) {
                paperEl.style.width = '794px';
                paperEl.style.minHeight = '1120px';
                paperEl.style.maxHeight = '1123px';
                paperEl.style.boxShadow = 'none';
                paperEl.style.margin = '0';
                paperEl.style.padding = '25mm 25mm';
                paperEl.style.boxSizing = 'border-box';
                paperEl.style.overflow = 'hidden';
            }

            const opt = {
                margin: 0,
                filename: 'Surat_Izin_Ruangan_BK-' + String(id).padStart(4, '0') + '.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff'
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                },
                pagebreak: { mode: 'avoid-all' }
            };

            const blob = await html2pdf().set(opt).from(paperEl).outputPdf('blob');

            setTimeout(() => {
                frame.remove();
            }, 600);

            return blob;
        }

        async function downloadSingleSuratPdf(id) {
            const blob = await getSuratPdfBlob(id);
            const filename = 'Surat_Izin_Ruangan_BK-' + String(id).padStart(4, '0') + '.pdf';
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            setTimeout(() => {
                a.remove();
                URL.revokeObjectURL(a.href);
            }, 1000);
        }

        async function bulkDownloadSurat() {
            let approvedIds = [];
            $('.booking-checkbox:checked').each(function() {
                if ($(this).data('status') === 'disetujui') {
                    approvedIds.push(parseInt($(this).val(), 10));
                }
            });

            if (approvedIds.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Belum Ada Surat Disetujui',
                    text: 'Dari pengajuan yang dipilih, belum ada yang berstatus Disetujui.',
                    confirmButtonColor: '#ea580c',
                    borderRadius: '16px'
                });
                return;
            }

            // Jika hanya 1 item, download langsung file PDF
            if (approvedIds.length === 1) {
                Swal.fire({
                    title: 'Mengunduh Dokumen Surat...',
                    html: '<div style="font-size:0.88rem; color:#64748b;">Sedang memproses <strong>PDF Surat Resmi</strong> ke perangkat Anda...</div>',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                try {
                    await downloadSingleSuratPdf(approvedIds[0]);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Diunduh',
                        text: 'File PDF Surat Resmi berhasil diunduh ke folder Downloads Anda.',
                        timer: 2000,
                        showConfirmButton: false,
                        borderRadius: '16px'
                    });
                } catch (err) {
                    console.error('Download single error:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengunduh',
                        text: 'Terjadi kendala saat memproses file PDF surat. Silakan coba kembali.',
                        borderRadius: '16px'
                    });
                }
                return;
            }

            // Jika lebih dari 1 item, kemas semua PDF ke file ZIP
            Swal.fire({
                title: 'Mengemas File ZIP...',
                html: '<div style="font-size:0.88rem; color:#64748b;">Sedang memproses <strong id="swalZipProgress" style="color:#ea580c;">0</strong> dari <strong>' + approvedIds.length + ' surat</strong> ke dalam arsip ZIP...</div>',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const zip = new JSZip();

                for (let i = 0; i < approvedIds.length; i++) {
                    const id = approvedIds[i];
                    const blob = await getSuratPdfBlob(id);
                    const filename = 'Surat_Izin_Ruangan_BK-' + String(id).padStart(4, '0') + '.pdf';
                    zip.file(filename, blob);

                    const progressEl = document.getElementById('swalZipProgress');
                    if (progressEl) progressEl.innerText = (i + 1);
                }

                const zipBlob = await zip.generateAsync({ type: 'blob' });
                const todayStr = new Date().toISOString().slice(0, 10);
                const zipFilename = 'Surat_Izin_Peminjaman_IFIK_' + todayStr + '.zip';

                const a = document.createElement('a');
                a.href = URL.createObjectURL(zipBlob);
                a.download = zipFilename;
                document.body.appendChild(a);
                a.click();
                setTimeout(() => {
                    a.remove();
                    URL.revokeObjectURL(a.href);
                }, 1000);

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Diunduh (.ZIP)',
                    text: approvedIds.length + ' file Surat Resmi berhasil dikemas & diunduh sebagai file ZIP (' + zipFilename + ').',
                    timer: 3000,
                    showConfirmButton: false,
                    borderRadius: '16px'
                });

            } catch (err) {
                console.error('Download zip error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengunduh ZIP',
                    text: 'Terjadi kendala saat mengemas file ZIP surat. Silakan coba kembali.',
                    borderRadius: '16px'
                });
            }
        }

        // Reset MultiSearch All Filters
        function resetMultiSearch() {
            $('#mainSearchInput').val('');
            if (fpRange) fpRange.clear();
            const btnClearDate = document.getElementById('btnClearDateRange');
            if (btnClearDate) btnClearDate.style.display = 'none';

            selectStatusFilter('', 'Semua Status', '#94a3b8', $('#statusDropdownMenu .dropdown-item:first'));
            selectMainCategory('query', '🔍 Kata Kunci (Semua)', 'Ketik kata kunci lalu tekan Enter atau klik Cari...', $('#menu-filter-main-cat .dropdown-item:first'));
            
            Object.keys(extraFpInstances).forEach(rowId => {
                if (extraFpInstances[rowId]) {
                    extraFpInstances[rowId].destroy();
                }
            });
            extraFpInstances = {};

            const container = document.getElementById('additionalFilterRowsContainer');
            if (container) container.innerHTML = '';
            
            updateFilterCountBadge();
            
            const card = document.getElementById('extraRowsCard');
            if (card) card.style.display = 'none';
            const btn = document.getElementById('standaloneAddBtn');
            if (btn) btn.classList.remove('active');

            currentPage = 1;
            applyFilterAndSearch();
        }

        let previousBookingDataHash = '';
        let isFirstSync = true;

        function buildRowHtml(row) {
            let statusRaw = (row.status || '').toLowerCase().trim();
            let statusClass = 'menunggu';
            let statusLabel = 'Menunggu Persetujuan';
            let statusIcon = 'fa-hourglass-half';
            let filterType = 'menunggu';

            if (statusRaw === 'disetujui' || statusRaw.indexOf('setuju') !== -1 || statusRaw.indexOf('approved') !== -1 || statusRaw.indexOf('ka. ur') !== -1 || statusRaw.indexOf('laboran') !== -1 || statusRaw.indexOf('admin') !== -1) {
                statusClass = 'disetujui';
                statusLabel = 'Disetujui';
                statusIcon = 'fa-circle-check';
                filterType = 'disetujui';
            } else if (statusRaw === 'ditolak' || statusRaw.indexOf('tolak') !== -1) {
                statusClass = 'ditolak';
                statusLabel = 'Ditolak';
                statusIcon = 'fa-circle-xmark';
                filterType = 'ditolak';
            } else if (statusRaw === 'dibatalkan' || statusRaw.indexOf('batal') !== -1) {
                statusClass = 'dibatalkan';
                statusLabel = 'Dibatalkan';
                statusIcon = 'fa-ban';
                filterType = 'ditolak';
            }

            let tglFormat = row.tanggal_formatted || (row.tanggal_booking ? row.tanggal_booking : (row.tanggal ? row.tanggal : '-'));
            let jamFormat = row.time_formatted || ((row.waktu_mulai ? row.waktu_mulai.substring(0,5) : '-') + ' - ' + (row.waktu_selesai ? row.waktu_selesai.substring(0,5) : '-') + ' WIB');
            let encodedData = JSON.stringify(row).replace(/"/g, '&quot;');
            let roomName = row.ruangan || row.nama_ruangan || (row.kode_ruangan ? 'Ruang ' + row.kode_ruangan : 'Ruangan Lab');
            let agendaText = row.agenda || row.keterangan || '-';
            let categoryName = row.kategori || row.nama_kategori || 'Ruangan';

            let cancelBtn = '';
            if (statusClass === 'menunggu') {
                cancelBtn = `
                    <button type="button" class="action-menu-item item-danger" onclick="cancelMyBooking('${row.id}')">
                        <i class="fa-solid fa-ban" style="color: #e11d48;"></i>
                        <span>Batalkan Pengajuan</span>
                    </button>
                `;
            }

            let suratBtn = '';
            if (statusClass === 'disetujui') {
                suratBtn = `
                    <button type="button" class="action-menu-item" onclick="openSuratModal('${row.id}')">
                        <i class="fa-solid fa-qrcode" style="color: #059669;"></i>
                        <span>Surat QR Resmi</span>
                    </button>
                `;
            }

            let createdDate = row.created_at ? 'Diajukan: ' + new Date(row.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'}).replace(/\./g, ':') : '';

            let isChecked = selectedBookingIds.includes(String(row.id)) ? 'checked' : '';

            return `
                <tr class="booking-row ${isChecked ? 'row-selected' : ''}" 
                    id="row-${row.id}"
                    data-id="${row.id}"
                    data-status="${filterType}" 
                    data-search="${(roomName + ' ' + agendaText + ' ' + categoryName + ' ' + tglFormat + ' ' + statusLabel + ' ' + (row.status || '')).toLowerCase()}"
                    data-ruangan="${roomName.toLowerCase()}"
                    data-agenda="${agendaText.toLowerCase()}"
                    data-tanggal="${(tglFormat + ' ' + (row.tanggal_booking || '') + ' ' + (row.tanggal || '')).toLowerCase()}"
                    data-tanggal-raw="${row.tanggal_raw || row.tanggal_booking || row.tanggal || ''}"
                    data-kategori="${categoryName.toLowerCase()}">
                    <td class="booking-check-col">
                        <input type="checkbox" class="booking-checkbox" value="${row.id}" data-id="${row.id}" data-status="${statusClass}" data-ruangan="${roomName}" data-agenda="${agendaText}" data-tanggal="${tglFormat}" ${isChecked} onchange="updateFloatingBar()">
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <span class="room-pill">
                                <i class="fa-solid fa-door-open" style="color: var(--primary);"></i>
                                ${roomName}
                            </span>
                            <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">
                                #BK-${String(row.id).padStart(4, '0')} • ${categoryName}
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="agenda-text" title="${agendaText}">
                            ${agendaText}
                        </div>
                        <div class="agenda-sub">
                            ${createdDate}
                        </div>
                    </td>
                    <td>
                        <div class="time-badge">
                            <span class="time-date"><i class="fa-regular fa-calendar" style="margin-right: 4px; color: #64748b;"></i>${tglFormat}</span>
                            <span class="time-hours"><i class="fa-regular fa-clock" style="margin-right: 4px; color: #94a3b8;"></i>${jamFormat}</span>
                        </div>
                    </td>
                    <td id="status-col-${row.id}">
                        <span class="badge-status ${statusClass}">
                            <i class="fa-solid ${statusIcon}"></i>
                            ${statusLabel}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="action-dropdown-wrap" id="action-col-${row.id}">
                            <button type="button" class="btn-action-dropdown" onclick="toggleRowActionMenu('${row.id}', event)">
                                <span>Aksi</span>
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <div class="action-menu-popup" id="rowActionMenu_${row.id}">
                                <button type="button" class="action-menu-item" onclick='openDetailModal(${encodedData})'>
                                    <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                                    <span>Detail Peminjaman</span>
                                </button>
                                ${suratBtn}
                                ${cancelBtn}
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }

        // Realtime Polling Sync Engine
        function syncLiveBookingData() {
            $.ajax({
                url: "<?= site_url('riwayat-booking/live-data') ?>",
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res && res.status === 'success') {
                        const newHash = JSON.stringify(res.data);
                        if (newHash !== previousBookingDataHash) {
                            if (!isFirstSync && previousBookingDataHash !== '') {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3500,
                                    timerProgressBar: true
                                });
                                Toast.fire({
                                    icon: 'info',
                                    title: 'Data riwayat peminjaman telah diperbarui otomatis.'
                                });
                            }
                            previousBookingDataHash = newHash;

                            // Update stats counters
                            $('#stat-total').text(res.totalCount || 0);
                            $('#pill-all').text(res.totalCount || 0);
                            $('#stat-menunggu').text(res.pendingCount || 0);
                            $('#pill-menunggu').text(res.pendingCount || 0);
                            $('#stat-disetujui').text(res.approvedCount || 0);
                            $('#pill-disetujui').text(res.approvedCount || 0);
                            $('#stat-ditolak').text(res.rejectedCount || 0);
                            $('#pill-ditolak').text(res.rejectedCount || 0);

                            // Re-render Table Rows
                            const tbody = document.getElementById('bookingTableBody');
                            if (tbody) {
                                if (!res.data || res.data.length === 0) {
                                    tbody.innerHTML = `
                                        <tr id="noDataRow">
                                            <td colspan="6">
                                                <div class="empty-state">
                                                    <i class="fa-solid fa-calendar-xmark empty-state-icon"></i>
                                                    <h3>Belum Ada Riwayat Peminjaman</h3>
                                                    <p>Anda belum pernah mengajukan peminjaman ruangan laboratorium.</p>
                                                    <a href="<?= site_url('ajukan-booking') ?>" class="btn-empty-action">
                                                        <i class="fa-solid fa-plus"></i>
                                                        <span>Ajukan Peminjaman</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                } else {
                                    let html = '';
                                    res.data.forEach(item => {
                                        html += buildRowHtml(item);
                                    });
                                    tbody.innerHTML = html;
                                }
                                applyFilterAndSearch();
                            }
                        }
                    }
                    isFirstSync = false;
                },
                complete: function() {
                    setTimeout(syncLiveBookingData, 4000);
                }
            });
        }

        // ==========================================
        // MOBILE STAT SLIDER DOTS LOGIC
        // ==========================================
        function initStatSliderDots() {
            const grid = document.querySelector('.stat-cards-grid');
            const dotsContainer = document.getElementById('statSliderDots');
            if (!grid || !dotsContainer) return;

            const cards = grid.querySelectorAll('.stat-card-highlight');
            if (cards.length <= 1) return;

            dotsContainer.innerHTML = '';
            cards.forEach((_, idx) => {
                const dot = document.createElement('span');
                dot.className = 'stat-dot' + (idx === 0 ? ' active' : '');
                dot.setAttribute('title', `Slide ${idx + 1}`);
                dot.addEventListener('click', () => {
                    cards[idx].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
                });
                dotsContainer.appendChild(dot);
            });

            const dots = dotsContainer.querySelectorAll('.stat-dot');
            grid.addEventListener('scroll', () => {
                const scrollLeft = grid.scrollLeft;
                const cardWidth = grid.offsetWidth || 1;
                const activeIndex = Math.min(Math.max(0, Math.round(scrollLeft / cardWidth)), cards.length - 1);
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === activeIndex);
                });
            }, { passive: true });
        }

        // ==========================================
        // MOBILE FILTER PILLS DOTS LOGIC
        // ==========================================
        function initFilterPillsDots() {
            const wrap = document.getElementById('filterPillsWrap') || document.querySelector('.filter-pills');
            const dotsContainer = document.getElementById('filterPillsDots');
            if (!wrap || !dotsContainer) return;

            const pills = Array.from(wrap.querySelectorAll('.pill-btn'));
            if (pills.length <= 1) return;

            dotsContainer.innerHTML = '';
            pills.forEach((pill, idx) => {
                const dot = document.createElement('span');
                dot.className = 'pill-dot' + (idx === 0 ? ' active' : '');
                dot.setAttribute('title', pill.textContent.trim());
                dot.addEventListener('click', () => {
                    pill.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                });
                dotsContainer.appendChild(dot);
            });

            const dots = dotsContainer.querySelectorAll('.pill-dot');
            const updateDots = () => {
                const maxScroll = wrap.scrollWidth - wrap.clientWidth;
                if (maxScroll <= 0) {
                    dots.forEach((dot, i) => dot.classList.toggle('active', i === 0));
                    return;
                }
                const scrollLeft = wrap.scrollLeft;
                let activeIdx = 0;
                if (scrollLeft <= 5) {
                    activeIdx = 0;
                } else if (scrollLeft >= maxScroll - 5) {
                    activeIdx = dots.length - 1;
                } else {
                    activeIdx = Math.min(Math.max(0, Math.round((scrollLeft / maxScroll) * (dots.length - 1))), dots.length - 1);
                }

                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === activeIdx);
                });
            };

            wrap.addEventListener('scroll', updateDots, { passive: true });
            window.addEventListener('resize', updateDots, { passive: true });
            setTimeout(updateDots, 300);
        }

        document.addEventListener('DOMContentLoaded', () => {
            initStatSliderDots();
            initFilterPillsDots();
        });
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            initStatSliderDots();
            initFilterPillsDots();
        }

        // Event Listeners on Ready
        $(document).ready(function() {
            initStatSliderDots();
            initFilterPillsDots();

            $('.pill-btn').on('click', function() {
                $('.pill-btn').removeClass('active');
                $(this).addClass('active');
                currentFilter = $(this).data('filter');
                currentPage = 1;
                applyFilterAndSearch();
            });

            // Initial pagination and filter application
            applyFilterAndSearch();

            // Close dropdowns & popover when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.action-dropdown-wrap').length) {
                    $('.action-menu-popup').removeClass('show');
                }
                if (!$(e.target).closest('.custom-dropdown-container').length) {
                    $('.custom-dropdown-menu').hide();
                    $('.dropdown-arrow').css('transform', 'rotate(0deg)');
                }
                if (!$(e.target).closest('#multiSearchWrapper').length) {
                    const card = document.getElementById('extraRowsCard');
                    const btn = document.getElementById('standaloneAddBtn');
                    if (card) card.style.display = 'none';
                    if (btn) btn.classList.remove('active');
                }
            });

            // Start Realtime Polling
            syncLiveBookingData();
        });

        // Open Surat Modal
        function openSuratModal(id) {
            let url = "<?= site_url('verifikasi/surat/') ?>" + id;
            $('#suratIframe').attr('src', url);
            $('#suratModal').css('display', 'flex');
        }

        function closeSuratModal() {
            $('#suratModal').hide();
            $('#suratIframe').attr('src', 'about:blank');
        }

        // Open Detail Modal
        function openDetailModal(data) {
            if (!data) return;

            let tgl = data.tanggal_booking || data.tanggal || data.tanggal_formatted || '-';
            let jam = data.time_formatted || ((data.waktu_mulai ? data.waktu_mulai.substring(0,5) : '-') + ' - ' + (data.waktu_selesai ? data.waktu_selesai.substring(0,5) : '-') + ' WIB');
            
            let statusBadge = '<span class="badge-status menunggu">Menunggu Persetujuan</span>';
            let st = (data.status || '').toLowerCase();
            if (st === 'disetujui' || st.indexOf('setuju') !== -1 || st.indexOf('approved') !== -1 || st.indexOf('laboran') !== -1 || st.indexOf('ka. ur') !== -1 || st.indexOf('admin') !== -1) {
                statusBadge = '<span class="badge-status disetujui">Disetujui</span>';
            } else if (st === 'ditolak' || st.indexOf('tolak') !== -1) {
                statusBadge = '<span class="badge-status ditolak">Ditolak</span>';
            } else if (st === 'dibatalkan' || st.indexOf('batal') !== -1) {
                statusBadge = '<span class="badge-status dibatalkan">Dibatalkan</span>';
            }

            let docHtml = '<span style="color:#94a3b8;">Tidak ada dokumen</span>';
            if (data.dokumen_pendukung) {
                let docUrl = "<?= base_url('uploads/dokumen_booking/') ?>" + data.dokumen_pendukung;
                docHtml = `<a href="${docUrl}" target="_blank" style="color:var(--primary); font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    <i class="fa-solid fa-file-arrow-down"></i> Unduh / Lihat Dokumen
                </a>`;
            }

            let html = `
                <div class="detail-item">
                    <div class="detail-item-label">ID Peminjaman</div>
                    <div class="detail-item-value">#BK-${String(data.id).padStart(4, '0')}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-item-label">Status Saat Ini</div>
                    <div class="detail-item-value" style="margin-top:6px;">${statusBadge}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-item-label">Ruangan Laboratorium</div>
                    <div class="detail-item-value">${data.ruangan || data.nama_ruangan || '-'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-item-label">Kategori Pemakaian</div>
                    <div class="detail-item-value">${data.kategori || data.nama_kategori || '-'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-item-label">Tanggal Pelaksanaan</div>
                    <div class="detail-item-value">${tgl}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-item-label">Waktu / Sesi</div>
                    <div class="detail-item-value">${jam}</div>
                </div>
                <div class="detail-item full-span">
                    <div class="detail-item-label">Agenda / Keterangan Acara</div>
                    <div class="detail-item-value" style="font-weight: 500; line-height: 1.5;">${data.agenda || data.keterangan || '-'}</div>
                </div>
                <div class="detail-item full-span">
                    <div class="detail-item-label">Dokumen Surat Pengantar / Proposal</div>
                    <div class="detail-item-value" style="margin-top:6px;">${docHtml}</div>
                </div>
            `;

            $('#detailGridContent').html(html);
            $('#detailModal').css('display', 'flex');
        }

        function closeDetailModal() {
            $('#detailModal').hide();
        }

        // Realtime AJAX Cancellation
        function cancelMyBooking(id) {
            Swal.fire({
                title: 'Batalkan Pengajuan?',
                text: "Apakah Anda yakin ingin membatalkan pengajuan peminjaman ruangan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Kembali',
                reverseButtons: true,
                borderRadius: '16px'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang membatalkan pengajuan',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: "<?= site_url('riwayat-booking/cancel/') ?>" + id,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Dibatalkan',
                                    text: response.message || 'Pengajuan peminjaman Anda telah dibatalkan.',
                                    timer: 2000,
                                    showConfirmButton: false,
                                    borderRadius: '16px'
                                });

                                // Trigger immediate live sync
                                syncLiveBookingData();

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Membatalkan',
                                    text: response.message || 'Terjadi kesalahan saat membatalkan pengajuan.',
                                    borderRadius: '16px'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Sistem',
                                text: 'Tidak dapat menghubungi server. Silakan coba beberapa saat lagi.',
                                borderRadius: '16px'
                            });
                        }
                    });
                }
            });
        }

        // Close modal when clicking outside
        $(window).on('click', function(e) {
            if ($(e.target).is('#suratModal')) {
                closeSuratModal();
            }
            if ($(e.target).is('#detailModal')) {
                closeDetailModal();
            }
        });
    </script>
</body>
</html>
