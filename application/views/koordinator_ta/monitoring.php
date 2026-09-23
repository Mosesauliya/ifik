<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Monitoring Status Peserta Tugas Akhir — Koordinator TA'; ?> - IFIK</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Bootstrap Icons & Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- jQuery & SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        /* Smooth Content Shifting for Curved Sidebar */
        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            min-height: 100vh;
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

            .header-nav-container {
                padding-left: 0.5rem !important;
            }

            body.curved-sidebar-desktop-collapsed .header-nav-container {
                padding-left: 3.5rem !important;
            }
        }

        @media (max-width: 1023.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }

        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 2px 14px;
            height: 44px;
            transition: all 0.2s ease;
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.12) !important;
        }

        .stage-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            white-space: nowrap;
            user-select: none;
        }
        .stage-chip:hover {
            border-color: #fdba74;
            background: #fff7ed;
            color: #c2410c;
            transform: translateY(-1px);
        }
        .stage-chip.active {
            border-color: #ea580c !important;
            background: linear-gradient(135deg, #ea580c, #c2410c) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25);
            transform: translateY(-1px);
        }

        /* Micro Progress Stepper Bar in Table */
        .micro-stepper {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            width: 68px;
            flex-shrink: 0;
        }
        .micro-step-dot {
            flex: 1;
            min-width: 4px;
            height: 4px;
            border-radius: 99px;
            background: #e2e8f0;
            transition: all 0.3s ease;
        }
        .micro-step-dot.completed {
            background: #10b981;
        }
        .micro-step-dot.active {
            background: #ea580c;
            box-shadow: 0 0 6px rgba(234, 88, 12, 0.5);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Hide scrollbars */
        .scrollbar-none::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .scrollbar-none {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }

        /* Rotating Conic Border Table Wrapper */
        .table-rotating-border-wrap {
            position: relative;
            border-radius: 20px;
            padding: 1.5px;
            background: linear-gradient(135deg, rgba(234, 88, 12, 0.3), rgba(245, 158, 11, 0.1), rgba(226, 232, 240, 0.8));
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08);
        }
        .table-rotating-border-inner {
            background: #ffffff;
            border-radius: 18.5px;
        }

        /* =========================================================================
           3D Claymorphic Stat Cards & Mobile Carousel
           ========================================================================= */
        .stat-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        @media (max-width: 1023px) {
            .stat-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 639px) {
            .stat-cards-grid {
                display: flex !important;
                flex-direction: row !important;
                overflow-x: auto !important;
                scroll-snap-type: x mandatory;
                -webkit-overflow-scrolling: touch;
                gap: 14px !important;
                padding: 4px 2px 8px 2px;
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
            }
            .stat-slider-dots {
                display: flex !important;
            }
        }

        .stat-card-highlight {
            position: relative;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 20px;
            padding: 18px 18px 16px 18px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            backdrop-filter: blur(12px);
            overflow: hidden;
            cursor: default;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
        }

        .stat-card-highlight:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 30px -8px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(0,0,0,0.02);
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
            width: 130px;
            height: 130px;
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
            gap: 10px;
        }

        .stat-card-meta {
            flex: 1;
            min-width: 0;
        }

        .stat-card-label {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color 0.2s ease;
        }
        .stat-card-highlight:hover .stat-card-label {
            color: var(--card-accent, #ea580c);
        }

        .stat-card-val {
            font-size: 1.75rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
            letter-spacing: -0.02em;
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .stat-card-percent {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--card-accent, #ea580c);
        }

        .stat-card-desc {
            font-size: 0.75rem;
            font-weight: 500;
            color: #64748b;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* 3D Claymorphic Icon Box */
        .stat-card-3d-icon {
            position: relative;
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            background: var(--card-icon-bg, linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%));
            border: 1px solid var(--card-icon-border, #fed7aa);
            color: var(--card-accent, #ea580c);
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.9), 0 6px 16px var(--card-icon-shadow, rgba(234, 88, 12, 0.16));
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease;
        }
        .stat-card-highlight:hover .stat-card-3d-icon {
            transform: rotate(6deg) scale(1.08);
        }

        /* Bottom Row Divider & Pulse Dots */
        .stat-card-bottom {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
            padding-top: 10px;
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
            --card-icon-bg: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            --card-icon-border: #bbf7d0;
            --card-icon-shadow: rgba(16, 185, 129, 0.18);
        }

        /* Stat Slider Dots Indicator */
        .stat-slider-dots {
            display: none;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
            margin-bottom: 18px;
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

        /* Stage Filter Pills Scroll Dots Indicator on Mobile */
        .filter-pills-dots {
            display: none;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 4px;
            margin-bottom: 2px;
        }

        @media (max-width: 768px) {
            .filter-pills-dots {
                display: flex !important;
            }
        }

        .filter-pills-dots .pill-dot {
            width: 5px;
            height: 5px;
            border-radius: 999px;
            background: #cbd5e1;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .filter-pills-dots .pill-dot.active {
            width: 18px;
            background: #ea580c;
            border-radius: 999px;
        }

        /* Unified Multi-Search Pill Component (Approval / Booking Style) */
        .search-pill-container {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 4px 6px 4px 14px;
            flex: 1;
            height: 48px;
            transition: all 0.2s ease;
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12) !important;
        }

        .unified-divider {
            width: 1.5px;
            height: 22px;
            background-color: #cbd5e1;
            margin: 0 10px;
            flex-shrink: 0;
        }

        .standalone-btn-text {
            display: none;
        }

        @media (min-width: 640px) {
            .standalone-btn-text {
                display: inline;
            }
        }

        .btn-standalone-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff7ed;
            border: 1.5px solid #ffedd5;
            border-radius: 16px;
            padding: 6px 14px;
            height: 48px;
            font-size: 0.82rem;
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
            font-size: 0.72rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 99px;
        }

        @media (max-width: 639px) {
            .search-pill-container {
                gap: 6px;
            }
            .unified-search-pill {
                height: 42px;
                padding: 2px 4px 2px 10px;
                border-radius: 14px;
            }
            .unified-divider {
                height: 18px;
                margin: 0 6px;
            }
            .btn-standalone-add {
                height: 42px;
                padding: 4px 10px;
                gap: 4px;
                border-radius: 14px;
            }
            .badge-standalone-count {
                padding: 1.5px 6px;
                font-size: 0.68rem;
            }
            .extra-rows-card {
                padding: 12px;
                border-radius: 14px;
            }
        }

        .extra-rows-card {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            width: 100%;
            background: #ffffff;
            border: 1.5px solid #fed7aa;
            border-radius: 16px;
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
            width: 40px;
            height: 40px;
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
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 6px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 12px 30px -5px rgba(15, 23, 42, 0.16);
            z-index: 2000;
            padding: 6px;
            min-width: 220px;
            display: none;
        }
        .custom-dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.15s ease;
        }
        .dropdown-item:hover, .dropdown-item.active {
            background: #fff7ed;
            color: #ea580c;
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-24">

    <!-- Include Curved Animated Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Page Content Wrapper -->
    <div id="mainPageContent" class="page-wrapper-for-sidebar">

        <!-- Top Navigation Header -->
        <header class="sticky top-0 z-40 glass-header px-4 sm:px-8 py-3 sm:py-4 mb-6 sm:mb-8">
            <div class="header-nav-container max-w-7xl mx-auto flex items-center justify-between gap-3 sm:gap-4 pl-12 sm:pl-14 md:pl-16">
                <div class="flex items-center gap-2.5 sm:gap-4 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center font-bold text-base sm:text-lg shadow-md shadow-orange-600/20 shrink-0">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h1 class="text-sm sm:text-base md:text-xl font-bold text-slate-900 tracking-tight leading-tight truncate sm:whitespace-normal">Monitoring Status Peserta Tugas Akhir</h1>
                            <span class="hidden md:inline-flex px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-orange-100 text-orange-800">Semua Tahapan</span>
                        </div>
                        <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5 line-clamp-1 sm:line-clamp-none">Pemantauan progres seluruh mahasiswa dari pendaftaran, Dosen Wali, LAA, Koor, KK, Preview 1-3 hingga Sidang &amp; Lulus.</p>
                    </div>
                </div>

                <!-- Profile Badge Right -->
                <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 flex items-center justify-center font-bold text-sm sm:text-base shadow-xs" title="Koordinator TA">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                </div>
            </div>
        </header>

        <main class="w-full max-w-[1440px] mx-auto px-3 sm:px-5 lg:px-6 space-y-6">

            <?php
                $pesertaList = $list_peserta ?? [];
                $totalPeserta = count($pesertaList);

                // Stage Counters
                $countWali    = 0;
                $countAdmin   = 0;
                $countKoor    = 0;
                $countKk      = 0;
                $countP1      = 0;
                $countP2      = 0;
                $countP3      = 0;
                $countSidang  = 0;
                $countLulus   = 0;

                // Berkas Counters
                $countBerkasLengkap = 0;
                $countBerkasRevisi  = 0;
                $countBerkasProses  = 0;
                $countBerkasKosong  = 0;

                foreach ($pesertaList as $p) {
                    $stKey = $p['stage_key'] ?? '';
                    if ($stKey === 'dosen_wali')        $countWali++;
                    elseif ($stKey === 'admin_layanan') $countAdmin++;
                    elseif ($stKey === 'koordinator_ta')$countKoor++;
                    elseif ($stKey === 'ketua_kk')      $countKk++;
                    elseif ($stKey === 'preview1')      $countP1++;
                    elseif ($stKey === 'preview2')      $countP2++;
                    elseif ($stKey === 'preview3')      $countP3++;
                    elseif ($stKey === 'sidang')        $countSidang++;
                    elseif ($stKey === 'lulus')         $countLulus++;

                    $bCode = $p['berkas_status_code'] ?? 'kosong';
                    if ($bCode === 'lengkap')       $countBerkasLengkap++;
                    elseif ($bCode === 'revisi')    $countBerkasRevisi++;
                    elseif ($bCode === 'proses')    $countBerkasProses++;
                    else                            $countBerkasKosong++;
                }

                $totalPendaftaran = $countWali + $countAdmin + $countKoor + $countKk;
                $totalBimbingan   = $countP1 + $countP2 + $countP3;
                $totalSidangLulus = $countSidang + $countLulus;
            ?>

            <!-- Top Stat Summary Cards (3D Claymorphic Highlight Style with Mobile Slider) -->
            <div class="stat-cards-grid">
                <!-- Card 1: Total Peserta -->
                <div class="stat-card-highlight theme-orange">
                    <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                    <div class="stat-card-top">
                        <div class="stat-card-meta">
                            <div class="stat-card-label">Total Peserta TA</div>
                            <div class="stat-card-val"><?= $totalPeserta; ?></div>
                            <div class="stat-card-desc">Seluruh mahasiswa aktif TA</div>
                        </div>
                        <div class="stat-card-3d-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-card-bottom">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-dots"><span></span><span></span><span></span></div>
                    </div>
                </div>

                <!-- Card 2: Tahap Pendaftaran -->
                <div class="stat-card-highlight theme-amber">
                    <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                    <div class="stat-card-top">
                        <div class="stat-card-meta">
                            <div class="stat-card-label">Proses Pendaftaran</div>
                            <div class="stat-card-val">
                                <?= $totalPendaftaran; ?>
                                <?php if ($totalPeserta > 0): ?>
                                    <span class="stat-card-percent">(<?= round(($totalPendaftaran / $totalPeserta) * 100); ?>%)</span>
                                <?php endif; ?>
                            </div>
                            <div class="stat-card-desc">Wali (<?= $countWali ?>), LAA (<?= $countAdmin ?>), Koor (<?= $countKoor ?>), KK (<?= $countKk ?>)</div>
                        </div>
                        <div class="stat-card-3d-icon">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                    </div>
                    <div class="stat-card-bottom">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-dots"><span></span><span></span><span></span></div>
                    </div>
                </div>

                <!-- Card 3: Bimbingan & Preview -->
                <div class="stat-card-highlight theme-sky">
                    <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                    <div class="stat-card-top">
                        <div class="stat-card-meta">
                            <div class="stat-card-label">Bimbingan &amp; Evaluasi</div>
                            <div class="stat-card-val">
                                <?= $totalBimbingan; ?>
                                <?php if ($totalPeserta > 0): ?>
                                    <span class="stat-card-percent">(<?= round(($totalBimbingan / $totalPeserta) * 100); ?>%)</span>
                                <?php endif; ?>
                            </div>
                            <div class="stat-card-desc">P1 (<?= $countP1 ?>), P2 (<?= $countP2 ?>), P3 (<?= $countP3 ?>)</div>
                        </div>
                        <div class="stat-card-3d-icon">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                    </div>
                    <div class="stat-card-bottom">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-dots"><span></span><span></span><span></span></div>
                    </div>
                </div>

                <!-- Card 4: Sidang & Lulus -->
                <div class="stat-card-highlight theme-emerald">
                    <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                    <div class="stat-card-top">
                        <div class="stat-card-meta">
                            <div class="stat-card-label">Sidang &amp; Lulus</div>
                            <div class="stat-card-val">
                                <?= $totalSidangLulus; ?>
                                <?php if ($totalPeserta > 0): ?>
                                    <span class="stat-card-percent">(<?= round(($totalSidangLulus / $totalPeserta) * 100); ?>%)</span>
                                <?php endif; ?>
                            </div>
                            <div class="stat-card-desc">Sidang (<?= $countSidang ?>), Lulus (<?= $countLulus ?>)</div>
                        </div>
                        <div class="stat-card-3d-icon">
                            <i class="fa-solid fa-graduation-cap"></i>
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

            <!-- Milestone Funnel Filter Chips Container (Scrollable Horizontal) -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-sm space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5 sm:gap-2 whitespace-nowrap shrink-0">
                        <i class="fa-solid fa-filter text-orange-500"></i>
                        <span class="hidden xs:inline">Filter Tahap Progres:</span>
                        <span class="xs:hidden">Filter Tahap:</span>
                    </span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 text-right truncate">
                        <span class="hidden xs:inline">Klik salah satu tahap untuk memfilter data</span>
                        <span class="xs:hidden">Geser &amp; pilih tahap</span>
                    </span>
                </div>

                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none" id="stageChipsWrap">
                    <button type="button" class="stage-chip active" data-stage="all" onclick="filterByStage('all', this)">
                        <span>Semua Tahap</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-700 font-black"><?= $totalPeserta ?></span>
                    </button>

                    <button type="button" class="stage-chip" data-stage="dosen_wali" onclick="filterByStage('dosen_wali', this)">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>1. Dosen Wali</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-blue-100 text-blue-800 font-bold"><?= $countWali ?></span>
                    </button>

                    <button type="button" class="stage-chip" data-stage="admin_layanan" onclick="filterByStage('admin_layanan', this)">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>2. Admin Layanan</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-100 text-amber-800 font-bold"><?= $countAdmin ?></span>
                    </button>

                    <button type="button" class="stage-chip" data-stage="koordinator_ta" onclick="filterByStage('koordinator_ta', this)">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        <span>3. Koordinator TA</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-orange-100 text-orange-800 font-bold"><?= $countKoor ?></span>
                    </button>

                    <button type="button" class="stage-chip" data-stage="ketua_kk" onclick="filterByStage('ketua_kk', this)">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <span>4. Ketua KK</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-purple-100 text-purple-800 font-bold"><?= $countKk ?></span>
                    </button>

                    <button type="button" class="stage-chip" data-stage="preview1" onclick="filterByStage('preview1', this)">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <span>5. Preview 1</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-teal-100 text-teal-800 font-bold"><?= $countP1 ?></span>
                    </button>

                    <button type="button" class="stage-chip" data-stage="preview2" onclick="filterByStage('preview2', this)">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        <span>6. Preview 2</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-indigo-100 text-indigo-800 font-bold"><?= $countP2 ?></span>
                    </button>

                    <button type="button" class="stage-chip" data-stage="preview3" onclick="filterByStage('preview3', this)">
                        <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                        <span>7. Preview 3</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-cyan-100 text-cyan-800 font-bold"><?= $countP3 ?></span>
                    </button>

                    <button type="button" class="stage-chip" data-stage="sidang" onclick="filterByStage('sidang', this)">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>8. Sidang TA</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-100 text-rose-800 font-bold"><?= $countSidang ?></span>
                    </button>

                    <button type="button" class="stage-chip" data-stage="lulus" onclick="filterByStage('lulus', this)">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>9. Lulus</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-bold"><?= $countLulus ?></span>
                    </button>
                </div>

                <!-- Mobile Stage Chips Scroll Dots Indicator -->
                <div class="filter-pills-dots" id="stageChipsDots"></div>
            </div>

            <!-- Search & Toolbar Card (Spacious Multi-Search Row + Meta Row) -->
            <div class="toolbar-card bg-white p-3 sm:p-4 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col gap-2.5 sm:gap-3">
                <!-- Row 1: Unified Multi-Search Pill Component & Standalone Add Button (+ 1/4) -->
                <div class="search-pill-container">
                    <div class="unified-search-pill" id="mainSearchPill">
                        <!-- Category Dropdown Container -->
                        <div class="custom-dropdown-container">
                            <input type="hidden" id="mainCategoryVal" value="query">
                            <button type="button" onclick="toggleCustomDropdown('main-cat', event)" class="flex items-center gap-1 sm:gap-1.5 bg-transparent border-none font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none" style="background:none;border:none;cursor:pointer;" title="Pilih Kategori Pencarian">
                                <span id="label-filter-main-cat" class="text-sm sm:text-base leading-none block">🔍</span>
                                <i class="fa-solid fa-chevron-down text-[10px] sm:text-[11px] text-slate-400 dropdown-arrow shrink-0" id="arrow-filter-main-cat"></i>
                            </button>
                            <div id="menu-filter-main-cat" class="custom-dropdown-menu">
                                <div onclick="selectMainCategory('query', this)" class="dropdown-item active"><span>🔍 Kata Kunci (Semua)</span></div>
                                <div onclick="selectMainCategory('nama', this)" class="dropdown-item"><span>🏷️ Mahasiswa (Nama / NIM)</span></div>
                                <div onclick="selectMainCategory('judul', this)" class="dropdown-item"><span>📖 Usulan Judul TA</span></div>
                                <div onclick="selectMainCategory('pembimbing', this)" class="dropdown-item"><span>👨‍🏫 Dosen Pembimbing</span></div>
                                <div onclick="selectMainCategory('penguji', this)" class="dropdown-item"><span>🎓 Dosen Penguji</span></div>
                                <div onclick="selectMainCategory('wali', this)" class="dropdown-item"><span>🧑‍💼 Dosen Wali</span></div>
                                <div onclick="selectMainCategory('berkas', this)" class="dropdown-item"><span>📁 Status Berkas</span></div>
                            </div>
                        </div>

                        <div class="unified-divider"></div>

                        <!-- Input Value Container (2 modes: text, berkas) -->
                        <div id="mainValueContainer" style="flex: 1; display: flex; align-items: center; min-width: 0; position: relative;">
                            <!-- MODE 1: Text Search (default) -->
                            <div id="modeText" style="flex:1;display:flex;align-items:center;min-width:0;">
                                <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-1.5 sm:mr-2.5 shrink-0"></i>
                                <input type="text" id="mainSearchInput" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); renderTable(); }" placeholder="Cari kata kunci..." class="w-full text-xs sm:text-[0.85rem] font-medium bg-transparent border-none outline-none text-slate-800 placeholder:text-slate-400 min-w-0">
                            </div>
                            <!-- MODE 2: Status Berkas Dropdown -->
                            <div id="modeBerkas" style="flex:1;display:none;align-items:center;min-width:0;position:relative;">
                                <i class="fa-solid fa-folder-closed text-slate-400 text-xs mr-1.5 sm:mr-2.5 shrink-0"></i>
                                <button type="button" id="berkasDropdownTrigger" onclick="toggleMainBerkasDropdown(event)" class="flex-1 flex items-center justify-between bg-transparent border-none outline-none cursor-pointer text-xs sm:text-[0.85rem] font-bold text-slate-800 p-0 min-w-0">
                                    <span id="berkasDropdownLabel" class="flex items-center gap-1.5 sm:gap-2 truncate">
                                        <span id="berkasDropdownDot" class="w-2 h-2 rounded-full bg-slate-400 inline-block shrink-0"></span>
                                        <span id="berkasDropdownText" class="truncate">Semua Status Berkas</span>
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 mr-1 shrink-0"></i>
                                </button>
                                <input type="hidden" id="berkasDropdownVal" value="">
                                <!-- Status Berkas Dropdown Menu -->
                                <div id="berkasDropdownMenu" style="display:none;position:absolute;top:calc(100% + 8px);left:0;min-width:240px;background:#fff;border:1.5px solid #e2e8f0;border-radius:16px;box-shadow:0 16px 40px rgba(0,0,0,0.18);z-index:100030;padding:6px;">
                                    <div onclick="selectBerkasFilter('','Semua Status Berkas','#94a3b8',this)" class="berkas-filter-opt active" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                        <span style="width:8px;height:8px;border-radius:50%;background:#94a3b8;display:inline-block;flex-shrink:0;"></span> Semua Status Berkas
                                    </div>
                                    <div onclick="selectBerkasFilter('lengkap','🟢 Lengkap (<?= $countBerkasLengkap ?>)','#10b981',this)" class="berkas-filter-opt" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                        <span style="width:8px;height:8px;border-radius:50%;background:#10b981;display:inline-block;flex-shrink:0;"></span> 🟢 Lengkap (<?= $countBerkasLengkap ?>)
                                    </div>
                                    <div onclick="selectBerkasFilter('revisi','🔴 Ada Revisi (<?= $countBerkasRevisi ?>)','#ef4444',this)" class="berkas-filter-opt" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                        <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;flex-shrink:0;"></span> 🔴 Ada Revisi (<?= $countBerkasRevisi ?>)
                                    </div>
                                    <div onclick="selectBerkasFilter('proses','🟡 Sedang Verifikasi (<?= $countBerkasProses ?>)','#f59e0b',this)" class="berkas-filter-opt" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                        <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;flex-shrink:0;"></span> 🟡 Sedang Verifikasi (<?= $countBerkasProses ?>)
                                    </div>
                                    <div onclick="selectBerkasFilter('kosong','⚪ Belum Unggah (<?= $countBerkasKosong ?>)','#94a3b8',this)" class="berkas-filter-opt" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:10px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                        <span style="width:8px;height:8px;border-radius:50%;background:#94a3b8;display:inline-block;flex-shrink:0;"></span> ⚪ Belum Unggah (<?= $countBerkasKosong ?>)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Cari -->
                        <button type="button" onclick="renderTable()" class="btn-search-cari" style="margin-left: 4px; padding: 6px 12px; background: linear-gradient(135deg, #ea580c, #f97316); color: #fff; font-weight: 700; border: none; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 5px; flex-shrink: 0; box-shadow: 0 2px 8px rgba(234,88,12,0.25);">
                            <i class="fa-solid fa-magnifying-glass text-[11px] sm:text-xs"></i>
                            <span class="text-xs font-bold hidden xs:inline">Cari</span>
                        </button>
                    </div>

                    <!-- Standalone Add Filter Button (+ 1/4) -->
                    <button type="button" id="standaloneAddBtn" onclick="toggleOrAddFilterRow(event)" class="btn-standalone-add" title="Buka / Tutup / Tambah Filter Baru (Maks 4)">
                        <span class="standalone-btn-content" style="display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-filter text-[11px]" style="color: #ea580c; font-size: 0.75rem;"></i>
                            <span class="standalone-btn-text">Filter Tambahan</span>
                        </span>
                        <span id="filterCountBadge" class="badge-standalone-count">1/4</span>
                    </button>

                    <!-- Extra Filter Rows Card Popover -->
                    <div id="extraRowsCard" class="extra-rows-card">
                        <div id="additionalFilterRowsContainer" style="display: flex; flex-direction: column; gap: 8px;"></div>
                        
                        <!-- Tombol Tambah Baris Filter di dalam Popover -->
                        <div class="mt-2" id="btnAddFilterRowWrap">
                            <button type="button" onclick="addFilterRow()" class="w-full py-2 px-3 border border-dashed border-orange-300 hover:border-orange-500 bg-orange-50/50 hover:bg-orange-50 text-orange-600 font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 cursor-pointer shadow-2xs">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Tambah Kriteria Filter Baru</span>
                            </button>
                        </div>
                        
                        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 10px; margin-top: 10px; font-size: 0.72rem; flex-wrap: wrap; gap: 8px;">
                            <span style="color: #94a3b8;">Gunakan kombinasi kriteria untuk mempersempit pencarian peserta (Maks. 4).</span>
                            <div style="display: flex; align-items: center; gap: 8px; margin-left: auto;">
                                <button type="button" onclick="resetMultiSearch()" style="background: none; border: none; color: #dc2626; font-weight: 700; cursor: pointer;">
                                    Reset All
                                </button>
                                <button type="button" onclick="renderTable()" style="padding: 5px 12px; background: #ea580c; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-check text-xs"></i> Terapkan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Subtitle Left & Page Size / Total / Reset Right -->
                <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500 font-medium">
                    <div class="text-[10.5px] sm:text-xs text-slate-500 leading-tight">
                        <span>Kelola &amp; telusuri data peserta tugas akhir secara langsung.</span>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                        <!-- Tampilkan Data/Hal & Total Badge -->
                        <div class="flex items-center gap-1 sm:gap-1.5 text-[10.5px] sm:text-xs text-slate-600 bg-slate-50 border border-slate-200 px-2 sm:px-3 py-1 sm:py-1.5 rounded-xl font-medium whitespace-nowrap shrink-0">
                            <span class="hidden xs:inline">Tampilkan</span>
                            <select id="pageSizeSelect" onchange="changePageSize(this.value)" class="h-6 sm:h-6.5 px-1 text-[11px] sm:text-xs font-bold bg-white border border-slate-300 rounded-md text-slate-800 focus:outline-none cursor-pointer">
                                <option value="10">10</option>
                                <option value="20" selected>20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="hidden xs:inline">data</span><span>/hal</span>
                            <span class="text-slate-300">|</span>
                            <span class="whitespace-nowrap">Total: <strong class="text-slate-900 font-bold" id="toolbarTotalCount"><?= count($pesertaList) ?></strong></span>
                        </div>

                        <button type="button" onclick="resetFilters()" class="h-7.5 sm:h-8 px-2.5 sm:px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-[11px] sm:text-xs transition flex items-center gap-1.5 cursor-pointer shrink-0" title="Reset Semua Filter & Pencarian">
                            <i class="fa-solid fa-rotate-right text-[10px]"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table View Desktop with Rotating Conic-Gradient Border -->
            <div class="table-rotating-border-wrap hidden sm:block">
                <div class="table-rotating-border-inner overflow-hidden">
                    <table class="table-fixed w-full text-left text-xs border-collapse">
                        <colgroup>
                            <col style="width: 38px;">
                            <col style="width: 170px;">
                            <col style="width: auto;">
                            <col style="width: 120px;">
                            <col style="width: 175px;">
                            <col style="width: 135px;">
                            <col style="width: 75px;">
                        </colgroup>
                        <thead class="bg-slate-50/80 text-slate-700 font-bold border-b border-slate-200/90 uppercase text-[10.5px] tracking-wider">
                            <tr>
                                <th class="py-3 px-1 text-center">No</th>
                                <th class="py-3 px-2">Mahasiswa</th>
                                <th class="py-3 px-2">Usulan Judul Tugas Akhir</th>
                                <th class="py-3 px-1 text-center">Status Berkas</th>
                                <th class="py-3 px-2">Tim Bimbingan &amp; Uji</th>
                                <th class="py-3 px-1 text-center">Tahap &amp; Pipeline</th>
                                <th class="py-3 px-1 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium bg-white" id="monitoringTableBody">
                            <!-- Injected via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Cards View -->
            <div id="monitoringMobileCards" class="sm:hidden space-y-3">
                <!-- Injected via JS -->
            </div>

            <!-- Bottom Pagination Controls -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-medium pt-2">
                <div>
                    Menampilkan <strong id="pageStart" class="text-slate-800 font-bold">0</strong> - <strong id="pageEnd" class="text-slate-800 font-bold">0</strong> dari total <strong id="totalRecords" class="text-slate-800 font-bold">0</strong> mahasiswa
                </div>
                <div class="flex items-center gap-1" id="paginationControls">
                    <!-- Injected via JS -->
                </div>
            </div>

        </main>
    </div>

    <!-- Floating Non-Blocking Container: Lihat & Pratinjau Berkas (Identik Dosen Wali Multi-Sub-Pratinjau) -->
    <div id="lihatBerkasContainer" class="fixed inset-0 z-[60] flex items-center justify-center p-2 sm:p-4 gap-2.5 sm:gap-3.5 overflow-x-auto scrollbar-none bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200 select-none" style="display: none;" onclick="if(event.target === this) closeLihatBerkasPanel()">
        
        <!-- Left: Kartu Mahasiswa (Daftar Berkas) -->
        <div id="wrapperDaftarMhs" class="flex flex-row items-center gap-3 shrink-0 max-h-[94vh] overflow-y-auto scrollbar-none" onclick="event.stopPropagation()">
            <!-- Rendered dynamically -->
        </div>

        <!-- Right: Panel Sub-Pratinjau Berkas (Dapat Menampilkan Hingga 4-5 Panel Berdampingan) -->
        <div id="wrapperPreviewBerkas" class="flex items-center gap-2.5 sm:gap-3 shrink-0 hidden overflow-x-auto scrollbar-none" onclick="event.stopPropagation()">
            <!-- Rendered dynamically -->
        </div>

    </div>

    <!-- Pass backend data to JavaScript -->
    <script>
        const RAW_PESERTA_DATA = <?= json_encode($pesertaList, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        let currentStageFilter = 'all';
        let currentPage = 1;
        let pageSize = 20;
        let extraRowCounter = 0;

        const SEARCH_CATEGORIES = [
            { key: 'query', label: '🔍 Kata Kunci (Semua)', emoji: '🔍', placeholder: 'Cari kata kunci...' },
            { key: 'nama', label: '🏷️ Mahasiswa (Nama / NIM)', emoji: '🏷️', placeholder: 'Ketik nama / NIM...' },
            { key: 'judul', label: '📖 Usulan Judul TA', emoji: '📖', placeholder: 'Ketik judul TA...' },
            { key: 'pembimbing', label: '👨‍🏫 Dosen Pembimbing', emoji: '👨‍🏫', placeholder: 'Ketik pembimbing...' },
            { key: 'penguji', label: '🎓 Dosen Penguji', emoji: '🎓', placeholder: 'Ketik penguji...' },
            { key: 'wali', label: '🧑‍💼 Dosen Wali', emoji: '🧑‍💼', placeholder: 'Ketik dosen wali...' },
            { key: 'berkas', label: '📁 Status Berkas', emoji: '📁', placeholder: 'Pilih status...' }
        ];

        // ==========================================
        // MULTI-SEARCH & DROPDOWNS
        // ==========================================
        function toggleCustomDropdown(id, e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('menu-filter-' + id);
            const arrow = document.getElementById('arrow-filter-' + id);
            const isShown = menu && menu.classList.contains('show');
            closeAllCustomDropdowns();
            if (menu && !isShown) {
                menu.classList.add('show');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
            }
        }

        function closeAllCustomDropdowns() {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.remove('show'));
            document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('.extra-rows-card') && !e.target.closest('#standaloneAddBtn')) {
                closeAllCustomDropdowns();
                const card = document.getElementById('extraRowsCard');
                if (card && !e.target.closest('.extra-rows-card')) {
                    card.style.display = 'none';
                    const btn = document.getElementById('standaloneAddBtn');
                    if (btn) btn.classList.remove('active');
                }
            }
        });

        function toggleMainBerkasDropdown(e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('berkasDropdownMenu');
            if (!menu) return;
            const isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
        }

        function selectBerkasFilter(val, label, color, el) {
            const valInput = document.getElementById('berkasDropdownVal');
            const dot = document.getElementById('berkasDropdownDot');
            const text = document.getElementById('berkasDropdownText');
            if (valInput) valInput.value = val;
            if (dot) dot.style.background = color;
            if (text) text.innerText = label.replace(/^[^\s]+\s*/, '');
            document.querySelectorAll('.berkas-filter-opt').forEach(o => {
                o.style.background = '';
                o.style.color = '#334155';
            });
            if (el) {
                el.style.background = '#fff7ed';
                el.style.color = '#ea580c';
            }
            const menu = document.getElementById('berkasDropdownMenu');
            if (menu) menu.style.display = 'none';
        }

        function switchMainMode(mode) {
            const modeText = document.getElementById('modeText');
            const modeBerkas = document.getElementById('modeBerkas');
            if (modeText) modeText.style.display = (mode === 'text') ? 'flex' : 'none';
            if (modeBerkas) modeBerkas.style.display = (mode === 'berkas') ? 'flex' : 'none';
        }

        function selectMainCategory(catKey, el) {
            const catObj = SEARCH_CATEGORIES.find(c => c.key === catKey) || SEARCH_CATEGORIES[0];
            const valInput = document.getElementById('mainCategoryVal');
            const labelEl = document.getElementById('label-filter-main-cat');
            if (valInput) valInput.value = catKey;
            if (labelEl) labelEl.innerText = catObj.emoji;
            if (el && el.parentElement) {
                el.parentElement.querySelectorAll('.dropdown-item').forEach(d => d.classList.remove('active'));
                el.classList.add('active');
            }
            closeAllCustomDropdowns();

            if (catKey === 'berkas') {
                switchMainMode('berkas');
            } else {
                switchMainMode('text');
                const inp = document.getElementById('mainSearchInput');
                if (inp) {
                    inp.placeholder = catObj.placeholder;
                    inp.focus();
                }
            }
        }

        function toggleOrAddFilterRow(e) {
            if (e) e.stopPropagation();
            const card = document.getElementById('extraRowsCard');
            const btn = document.getElementById('standaloneAddBtn');
            if (!card) return;
            const isOpen = card.style.display === 'block';

            if (isOpen) {
                card.style.display = 'none';
                if (btn) btn.classList.remove('active');
            } else {
                card.style.display = 'block';
                if (btn) btn.classList.add('active');
                const container = document.getElementById('additionalFilterRowsContainer');
                if (container && container.children.length === 0) {
                    addFilterRow();
                }
            }
        }

        function getExtraRowInputHtml(rowId, catKey, defaultVal = '') {
            const catObj = SEARCH_CATEGORIES.find(c => c.key === catKey) || SEARCH_CATEGORIES[0];

            if (catKey === 'berkas') {
                return `
                    <div style="flex: 1; display: flex; align-items: center; min-width: 0; position: relative;" id="extraBerkasWrapper_${rowId}">
                        <input type="hidden" class="extra-berkas-val" id="extraBerkasVal_${rowId}" value="${defaultVal || ''}">
                        <button type="button" onclick="toggleExtraBerkasDropdown('${rowId}', event)" style="background: none; border: none; display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.82rem; font-weight: 700; color: #1e293b; width: 100%; text-align: left; padding: 0;">
                            <span id="extraBerkasDot_${rowId}" style="width: 8px; height: 8px; border-radius: 50%; background: #94a3b8; display: inline-block; flex-shrink: 0;"></span>
                            <span id="extraBerkasText_${rowId}" style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Semua Status Berkas</span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.65rem; color: #94a3b8; margin-right: 4px;"></i>
                        </button>
                        <div id="extraBerkasMenu_${rowId}" class="extra-berkas-menu" style="display: none; position: absolute; top: calc(100% + 8px); left: 0; min-width: 220px; background: #fff; border: 1.5px solid #e2e8f0; border-radius: 14px; box-shadow: 0 16px 40px rgba(0,0,0,0.18); z-index: 100030; padding: 6px;">
                            <div onclick="selectExtraBerkas('${rowId}', '', 'Semua Status Berkas', '#94a3b8', this)" class="berkas-filter-opt active" style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:8px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                <span style="width:8px;height:8px;border-radius:50%;background:#94a3b8;display:inline-block;flex-shrink:0;"></span> Semua Status Berkas
                            </div>
                            <div onclick="selectExtraBerkas('${rowId}', 'lengkap', '🟢 Lengkap', '#10b981', this)" class="berkas-filter-opt" style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:8px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                <span style="width:8px;height:8px;border-radius:50%;background:#10b981;display:inline-block;flex-shrink:0;"></span> 🟢 Lengkap
                            </div>
                            <div onclick="selectExtraBerkas('${rowId}', 'revisi', '🔴 Ada Revisi', '#ef4444', this)" class="berkas-filter-opt" style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:8px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;flex-shrink:0;"></span> 🔴 Ada Revisi
                            </div>
                            <div onclick="selectExtraBerkas('${rowId}', 'proses', '🟡 Sedang Verifikasi', '#f59e0b', this)" class="berkas-filter-opt" style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:8px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;flex-shrink:0;"></span> 🟡 Sedang Verifikasi
                            </div>
                            <div onclick="selectExtraBerkas('${rowId}', 'kosong', '⚪ Belum Unggah', '#94a3b8', this)" class="berkas-filter-opt" style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:8px;font-size:0.82rem;font-weight:700;color:#334155;cursor:pointer;transition:all 0.15s;">
                                <span style="width:8px;height:8px;border-radius:50%;background:#94a3b8;display:inline-block;flex-shrink:0;"></span> ⚪ Belum Unggah
                            </div>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div style="flex: 1; display: flex; align-items: center; min-width: 0;">
                        <input type="text" class="extra-search-input" value="${defaultVal}" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); renderTable(); }" placeholder="${catObj.placeholder}" style="width: 100%; font-size: 0.82rem; font-weight: 500; background: transparent; border: none; outline: none; color: #1e293b; min-width: 0;">
                    </div>
                `;
            }
        }

        function toggleExtraBerkasDropdown(rowId, e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('extraBerkasMenu_' + rowId);
            if (!menu) return;
            const isShown = menu.style.display === 'block';
            document.querySelectorAll('.extra-berkas-menu').forEach(m => m.style.display = 'none');
            const mainBerkasMenu = document.getElementById('berkasDropdownMenu');
            if (mainBerkasMenu) mainBerkasMenu.style.display = 'none';
            closeAllCustomDropdowns();
            menu.style.display = isShown ? 'none' : 'block';
        }

        function selectExtraBerkas(rowId, val, label, color, el) {
            const valInput = document.getElementById('extraBerkasVal_' + rowId);
            const dot = document.getElementById('extraBerkasDot_' + rowId);
            const text = document.getElementById('extraBerkasText_' + rowId);
            const menu = document.getElementById('extraBerkasMenu_' + rowId);
            if (valInput) valInput.value = val;
            if (dot) dot.style.background = color;
            if (text) text.innerText = label.replace(/^[^\s]+\s*/, '');
            if (menu) {
                menu.querySelectorAll('.berkas-filter-opt').forEach(o => {
                    o.style.background = '';
                    o.style.color = '#334155';
                });
                if (el) {
                    el.style.background = '#fff7ed';
                    el.style.color = '#ea580c';
                }
                menu.style.display = 'none';
            }
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#modeBerkas')) {
                const m = document.getElementById('berkasDropdownMenu');
                if (m) m.style.display = 'none';
            }
            document.querySelectorAll('.extra-berkas-menu').forEach(menu => {
                if (!e.target.closest('#' + menu.parentElement?.id)) {
                    menu.style.display = 'none';
                }
            });
        }, true);

        function addFilterRow(defaultKey = 'judul', defaultVal = '') {
            const container = document.getElementById('additionalFilterRowsContainer');
            if (!container) return;
            if (container.children.length >= 3) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Batas Maksimal Filter',
                        text: 'Maksimal 4 kriteria pencarian kombinasi (1 utama + 3 filter tambahan).',
                        icon: 'info',
                        confirmButtonColor: '#ea580c'
                    });
                } else {
                    alert('Maksimal 4 kriteria pencarian kombinasi.');
                }
                return;
            }

            extraRowCounter++;
            const rowId = 'extra-row-' + extraRowCounter;

            let dropdownItems = '';
            SEARCH_CATEGORIES.forEach(c => {
                const isActive = (c.key === defaultKey) ? 'active' : '';
                dropdownItems += `<div onclick="selectExtraCategory('${rowId}', '${c.key}', this)" class="dropdown-item ${isActive}"><span>${c.label}</span></div>`;
            });

            const catObj = SEARCH_CATEGORIES.find(c => c.key === defaultKey) || SEARCH_CATEGORIES[0];

            const rowHtml = document.createElement('div');
            rowHtml.className = 'extra-filter-row';
            rowHtml.id = rowId;
            rowHtml.innerHTML = `
                <div class="unified-search-pill" style="height: 42px;">
                    <div class="custom-dropdown-container">
                        <input type="hidden" class="extra-category-val" value="${defaultKey}">
                        <button type="button" onclick="toggleCustomDropdown('${rowId}', event)" class="flex items-center gap-1 sm:gap-1.5 bg-transparent border-none font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none" style="display:flex;align-items:center;gap:4px;background:none;border:none;cursor:pointer;" title="${catObj.label}">
                            <span class="extra-category-label text-sm sm:text-base leading-none block">${catObj.emoji}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow shrink-0" id="arrow-filter-${rowId}"></i>
                        </button>
                        <div id="menu-filter-${rowId}" class="custom-dropdown-menu">
                            ${dropdownItems}
                        </div>
                    </div>
                    <div class="unified-divider" style="height: 18px;"></div>
                    <div id="extraInputContainer_${rowId}" style="flex: 1; display: flex; align-items: center; min-width: 0;">
                        ${getExtraRowInputHtml(rowId, defaultKey, defaultVal)}
                    </div>
                </div>
                <button type="button" onclick="removeFilterRow('${rowId}')" class="btn-remove-row" title="Hapus kriteria ini" style="width: 36px; height: 36px; border-radius: 10px;">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            `;

            container.appendChild(rowHtml);
            updateFilterCountBadge();
        }

        function selectExtraCategory(rowId, catKey, el) {
            const row = document.getElementById(rowId);
            if (!row) return;
            const catObj = SEARCH_CATEGORIES.find(c => c.key === catKey) || SEARCH_CATEGORIES[0];
            const valInput = row.querySelector('.extra-category-val');
            const labelEl = row.querySelector('.extra-category-label');
            if (valInput) valInput.value = catKey;
            if (labelEl) labelEl.innerText = catObj.emoji;
            if (el && el.parentElement) {
                el.parentElement.querySelectorAll('.dropdown-item').forEach(d => d.classList.remove('active'));
                el.classList.add('active');
            }
            closeAllCustomDropdowns();

            const inputContainer = document.getElementById('extraInputContainer_' + rowId);
            if (inputContainer) {
                inputContainer.innerHTML = getExtraRowInputHtml(rowId, catKey, '');
                if (catKey !== 'berkas') {
                    const inp = inputContainer.querySelector('.extra-search-input');
                    if (inp) inp.focus();
                }
            }
        }

        function removeFilterRow(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                row.remove();
                updateFilterCountBadge();
                renderTable();
            }
        }

        function updateFilterCountBadge() {
            const container = document.getElementById('additionalFilterRowsContainer');
            const rowCount = container ? container.children.length : 0;
            const count = 1 + rowCount;
            const badge = document.getElementById('filterCountBadge');
            if (badge) badge.innerText = count + '/4';

            const addBtnWrap = document.getElementById('btnAddFilterRowWrap');
            if (addBtnWrap) {
                addBtnWrap.style.display = (rowCount >= 3) ? 'none' : 'block';
            }
        }

        function resetMultiSearch() {
            const searchInp = document.getElementById('mainSearchInput');
            if (searchInp) searchInp.value = '';
            selectBerkasFilter('', 'Semua Status Berkas', '#94a3b8', null);
            selectMainCategory('query', null);
            
            const container = document.getElementById('additionalFilterRowsContainer');
            if (container) container.innerHTML = '';
            updateFilterCountBadge();
            const card = document.getElementById('extraRowsCard');
            if (card) card.style.display = 'none';
            const btn2 = document.getElementById('standaloneAddBtn');
            if (btn2) btn2.classList.remove('active');
            renderTable();
        }

        function getActiveFilters() {
            const filters = [];
            const mainKey = document.getElementById('mainCategoryVal')?.value || 'query';

            if (mainKey === 'berkas') {
                const berkasVal = (document.getElementById('berkasDropdownVal')?.value || '').trim();
                if (berkasVal) {
                    filters.push({ key: 'berkas', val: berkasVal });
                }
            } else {
                const mainVal = (document.getElementById('mainSearchInput')?.value || '').toLowerCase().trim();
                if (mainVal) {
                    filters.push({ key: mainKey, val: mainVal });
                }
            }

            document.querySelectorAll('#additionalFilterRowsContainer .extra-filter-row').forEach(row => {
                const key = row.querySelector('.extra-category-val')?.value;
                if (key === 'berkas') {
                    const berkasVal = (row.querySelector('.extra-berkas-val')?.value || '').trim();
                    if (berkasVal) {
                        filters.push({ key: 'berkas', val: berkasVal });
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

        function getStageBadgeHTML(stageKey, stageName, stageDesc) {
            const badges = {
                'dosen_wali': { bg: 'bg-blue-50 text-blue-700 border-blue-200', icon: 'fa-user-tie', text: '1. Dosen Wali' },
                'admin_layanan': { bg: 'bg-amber-50 text-amber-700 border-amber-200', icon: 'fa-building-columns', text: '2. Admin Layanan' },
                'koordinator_ta': { bg: 'bg-orange-50 text-orange-700 border-orange-200', icon: 'fa-graduation-cap', text: '3. Koordinator TA' },
                'ketua_kk': { bg: 'bg-purple-50 text-purple-700 border-purple-200', icon: 'fa-people-group', text: '4. Ketua KK' },
                'preview1': { bg: 'bg-teal-50 text-teal-700 border-teal-200', icon: 'fa-file-lines', text: '5. Preview 1' },
                'preview2': { bg: 'bg-indigo-50 text-indigo-700 border-indigo-200', icon: 'fa-chalkboard-user', text: '6. Preview 2' },
                'preview3': { bg: 'bg-cyan-50 text-cyan-700 border-cyan-200', icon: 'fa-clipboard-check', text: '7. Preview 3' },
                'sidang': { bg: 'bg-rose-50 text-rose-700 border-rose-200', icon: 'fa-scale-balanced', text: '8. Sidang TA' },
                'lulus': { bg: 'bg-emerald-50 text-emerald-700 border-emerald-200', icon: 'fa-award', text: '9. Lulus TA' }
            };

            const b = badges[stageKey] || { bg: 'bg-slate-50 text-slate-700 border-slate-200', icon: 'fa-circle-info', text: stageName };

            return `
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-[10.5px] font-bold border ${b.bg} shadow-2xs whitespace-nowrap">
                    <i class="fa-solid ${b.icon} text-[9.5px]"></i>
                    <span>${b.text}</span>
                </span>
            `;
        }

        function getBerkasBadgeHTML(item) {
            const code  = item.berkas_status_code || 'kosong';
            const label = item.berkas_status_label || 'Belum Unggah';
            const nim   = item.nim || '';

            let badgeConfig = {
                'lengkap': { bg: 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100', icon: 'fa-circle-check text-emerald-600' },
                'revisi':  { bg: 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100', icon: 'fa-triangle-exclamation text-rose-600' },
                'proses':  { bg: 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100', icon: 'fa-clock text-amber-600' },
                'kosong':  { bg: 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100', icon: 'fa-circle-xmark text-slate-400' }
            };

            const conf = badgeConfig[code] || badgeConfig['kosong'];

            return `
                <button type="button" onclick="openStudentBerkasPreview('${nim}')" class="group inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10.5px] font-bold border ${conf.bg} shadow-2xs transition-all duration-150 cursor-pointer whitespace-nowrap" title="Klik untuk melihat rincian & berkas PDF">
                    <i class="fa-solid ${conf.icon} text-[9.5px]"></i>
                    <span>${label}</span>
                    <i class="fa-solid fa-chevron-right text-[7.5px] opacity-40 group-hover:opacity-100 group-hover:translate-x-0.5 transition"></i>
                </button>
            `;
        }

        function getMicroStepperHTML(stageIndex) {
            const idx = parseInt(stageIndex, 10) || 1;
            let html = '<div class="micro-stepper" title="Step ' + idx + ' dari 9">';
            for (let i = 1; i <= 9; i++) {
                if (i < idx) {
                    html += '<div class="micro-step-dot completed" title="Tahap ' + i + ' Selesai"></div>';
                } else if (i === idx) {
                    html += '<div class="micro-step-dot active" title="Sedang di Tahap ' + i + '"></div>';
                } else {
                    html += '<div class="micro-step-dot" title="Tahap ' + i + ' Belum"></div>';
                }
            }
            html += '</div>';
            return html;
        }

        function filterByStage(stage, el) {
            currentStageFilter = stage;
            currentPage = 1;
            document.querySelectorAll('.stage-chip').forEach(c => c.classList.remove('active'));
            if (el) {
                el.classList.add('active');
                el.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
            }
            renderTable();
        }

        function changePageSize(size) {
            pageSize = parseInt(size, 10);
            currentPage = 1;
            renderTable();
        }

        function resetFilters() {
            resetMultiSearch();
            pageSize = 20;
            const pSizeSelect = document.getElementById('pageSizeSelect');
            if (pSizeSelect) pSizeSelect.value = '20';
            filterByStage('all', document.querySelector('.stage-chip[data-stage="all"]'));
        }

        function renderTable() {
            const filters = getActiveFilters();

            // Filter data
            const filtered = RAW_PESERTA_DATA.filter(item => {
                // Filter by stage
                if (currentStageFilter !== 'all' && item.stage_key !== currentStageFilter) {
                    return false;
                }

                // Filter by all active multi-search criteria (AND logic)
                for (let i = 0; i < filters.length; i++) {
                    const f = filters[i];
                    const q = f.val;

                    if (f.key === 'query') {
                        const matchNim = (item.nim || '').toLowerCase().includes(q);
                        const matchNama = (item.nama || item.name || '').toLowerCase().includes(q);
                        const matchJudul = (item.judul_1 || '').toLowerCase().includes(q);
                        const matchP1 = (item.nama_pembimbing_1 || item.pembimbing_1 || '').toLowerCase().includes(q);
                        const matchP2 = (item.nama_pembimbing_2 || item.pembimbing_2 || '').toLowerCase().includes(q);
                        const matchU1 = (item.nama_penguji_1 || item.penguji_1 || '').toLowerCase().includes(q);
                        const matchU2 = (item.nama_penguji_2 || item.penguji_2 || '').toLowerCase().includes(q);
                        const matchWali = (item.nama_dosen_wali || item.dosen_wali || '').toLowerCase().includes(q);
                        const matchStage = (item.progres_stage || '').toLowerCase().includes(q);
                        const matchBerkas = (item.berkas_status_label || '').toLowerCase().includes(q);
                        if (!matchNim && !matchNama && !matchJudul && !matchP1 && !matchP2 && !matchU1 && !matchU2 && !matchWali && !matchStage && !matchBerkas) {
                            return false;
                        }
                    } else if (f.key === 'nama') {
                        const matchNama = (item.nama || item.name || '').toLowerCase().includes(q);
                        const matchNim = (item.nim || '').toLowerCase().includes(q);
                        if (!matchNama && !matchNim) return false;
                    } else if (f.key === 'judul') {
                        const matchJudul = (item.judul_1 || '').toLowerCase().includes(q);
                        if (!matchJudul) return false;
                    } else if (f.key === 'pembimbing') {
                        const matchP1 = (item.nama_pembimbing_1 || item.pembimbing_1 || '').toLowerCase().includes(q);
                        const matchP2 = (item.nama_pembimbing_2 || item.pembimbing_2 || '').toLowerCase().includes(q);
                        if (!matchP1 && !matchP2) return false;
                    } else if (f.key === 'penguji') {
                        const matchU1 = (item.nama_penguji_1 || item.penguji_1 || '').toLowerCase().includes(q);
                        const matchU2 = (item.nama_penguji_2 || item.penguji_2 || '').toLowerCase().includes(q);
                        if (!matchU1 && !matchU2) return false;
                    } else if (f.key === 'wali') {
                        const matchWali = (item.nama_dosen_wali || item.dosen_wali || '').toLowerCase().includes(q);
                        if (!matchWali) return false;
                    } else if (f.key === 'berkas') {
                        if (item.berkas_status_code !== q) return false;
                    }
                }

                return true;
            });

            const total = filtered.length;
            const totalPages = Math.ceil(total / pageSize) || 1;
            if (currentPage > totalPages) currentPage = totalPages;

            const startIdx = (currentPage - 1) * pageSize;
            const endIdx = Math.min(startIdx + pageSize, total);
            const pageData = filtered.slice(startIdx, endIdx);

            // Update Counts
            document.getElementById('totalRecords').innerText = total;
            const tbCount = document.getElementById('toolbarTotalCount');
            if (tbCount) tbCount.innerText = total;
            document.getElementById('pageStart').innerText = total > 0 ? startIdx + 1 : 0;
            document.getElementById('pageEnd').innerText = endIdx;

            // Render Desktop Table Rows
            const tbody = document.getElementById('monitoringTableBody');
            if (pageData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-folder-open text-3xl mb-2 block text-slate-300"></i>
                            <p class="font-bold text-sm">Tidak ada data mahasiswa pada kriteria filter ini.</p>
                            <p class="text-xs text-slate-400 mt-1">Coba ganti filter tahap, status berkas, atau kata kunci pencarian.</p>
                        </td>
                    </tr>
                `;
            } else {
                let rowsHtml = '';
                pageData.forEach((item, idx) => {
                    const rowNo = startIdx + idx + 1;
                    const initial = (item.nama || item.name || 'U').charAt(0).toUpperCase();

                    const p1 = item.nama_pembimbing_1 || item.pembimbing_1;
                    const p2 = item.nama_pembimbing_2 || item.pembimbing_2;
                    const u1 = item.nama_penguji_1 || item.penguji_1;
                    const u2 = item.nama_penguji_2 || item.penguji_2;

                    rowsHtml += `
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-1 text-center text-slate-400 font-bold text-xs">${rowNo}</td>
                            <td class="py-3 px-2 min-w-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-xl bg-orange-100 text-orange-700 font-black text-xs flex items-center justify-center shrink-0">
                                        ${initial}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-900 text-xs truncate" title="${item.nama || item.name || ''}">${item.nama || item.name || 'Mahasiswa'}</div>
                                        <div class="text-[10px] font-mono font-semibold text-slate-500 truncate">${item.nim || '-'} • <span class="text-orange-600 font-sans font-bold">${item.prodi || 'Informatika'}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-2 min-w-0">
                                <div class="font-semibold text-slate-800 text-xs line-clamp-2 leading-snug" title="${item.judul_1 || '-'}">
                                    ${item.judul_1 || '<span class="text-slate-400 italic">Belum mengisi judul</span>'}
                                </div>
                            </td>
                            <td class="py-3 px-1 text-center">
                                ${getBerkasBadgeHTML(item)}
                            </td>
                            <td class="py-3 px-2 min-w-0">
                                <div class="space-y-0.5 text-[10.5px]">
                                    <div class="truncate text-slate-700" title="Pembimbing 1: ${p1 || 'Belum diplot'}">
                                        <span class="text-[9.5px] font-bold text-orange-600">P1:</span> ${p1 ? p1 : '<span class="text-slate-400 italic">Belum diplot</span>'}
                                    </div>
                                    <div class="truncate text-slate-700" title="Pembimbing 2: ${p2 || 'Belum diplot'}">
                                        <span class="text-[9.5px] font-bold text-amber-600">P2:</span> ${p2 ? p2 : '<span class="text-slate-400 italic">Belum diplot</span>'}
                                    </div>
                                    ${u1 ? `
                                        <div class="truncate text-slate-700 pt-0.5 border-t border-slate-100" title="Penguji 1: ${u1}">
                                            <span class="text-[9.5px] font-bold text-indigo-600">U1:</span> ${u1}
                                        </div>
                                    ` : ''}
                                    ${u2 ? `
                                        <div class="truncate text-slate-700" title="Penguji 2: ${u2}">
                                            <span class="text-[9.5px] font-bold text-indigo-600">U2:</span> ${u2}
                                        </div>
                                    ` : ''}
                                </div>
                            </td>
                            <td class="py-3 px-1 text-center">
                                <div class="flex flex-col items-center justify-center gap-1">
                                    ${getStageBadgeHTML(item.stage_key, item.progres_stage)}
                                    <div class="flex items-center justify-center gap-1.5 mt-0.5" title="Tahap ${item.stage_index} dari 9">
                                        ${getMicroStepperHTML(item.stage_index)}
                                        <span class="text-[9px] font-bold text-slate-500 shrink-0 font-mono">${item.stage_index}/9</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-1 text-center">
                                <a href="<?= site_url('koordinatorta/detail_mahasiswa/'); ?>${item.nim}" class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-500 text-white rounded-xl font-bold text-xs shadow-xs transition active:scale-95">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = rowsHtml;
            }

            // Render Mobile Cards View
            const mobileContainer = document.getElementById('monitoringMobileCards');
            if (pageData.length === 0) {
                mobileContainer.innerHTML = `
                    <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center text-slate-400">
                        <i class="fa-solid fa-folder-open text-3xl mb-2 block text-slate-300"></i>
                        <p class="font-bold text-sm">Tidak ada data mahasiswa.</p>
                    </div>
                `;
            } else {
                let cardsHtml = '';
                pageData.forEach((item, idx) => {
                    const rowNo = startIdx + idx + 1;
                    const initial = (item.nama || item.name || 'U').charAt(0).toUpperCase();
                    const p1 = item.nama_pembimbing_1 || item.pembimbing_1;
                    const p2 = item.nama_pembimbing_2 || item.pembimbing_2;
                    const u1 = item.nama_penguji_1 || item.penguji_1;
                    const u2 = item.nama_penguji_2 || item.penguji_2;

                    cardsHtml += `
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-700 font-black text-xs flex items-center justify-center shrink-0">
                                        ${initial}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 text-xs truncate">${item.nama || item.name || 'Mahasiswa'}</div>
                                        <div class="text-[11px] font-mono text-slate-500">${item.nim || '-'}</div>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">#${rowNo}</span>
                            </div>

                            <div class="text-xs font-semibold text-slate-800 line-clamp-2">
                                ${item.judul_1 || '<span class="text-slate-400 italic">Belum mengisi judul</span>'}
                            </div>

                            <!-- Berkas Status Mobile Pill -->
                            <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl">
                                <span class="text-[11px] font-bold text-slate-500">Status Berkas:</span>
                                ${getBerkasBadgeHTML(item)}
                            </div>

                            <div class="p-3 bg-slate-50 rounded-xl space-y-1.5 text-[11px]">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Pembimbing 1:</span>
                                    <strong class="text-slate-800 truncate max-w-[180px]">${p1 ? p1 : 'Belum diplot'}</strong>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Pembimbing 2:</span>
                                    <strong class="text-slate-800 truncate max-w-[180px]">${p2 ? p2 : 'Belum diplot'}</strong>
                                </div>
                                ${u1 ? `
                                    <div class="flex items-center justify-between text-slate-600 pt-1 border-t border-slate-200/60">
                                        <span>Penguji 1:</span>
                                        <strong class="text-slate-800 truncate max-w-[180px]">${u1}</strong>
                                    </div>
                                ` : ''}
                                ${u2 ? `
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span>Penguji 2:</span>
                                        <strong class="text-slate-800 truncate max-w-[180px]">${u2}</strong>
                                    </div>
                                ` : ''}
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <div>
                                    ${getStageBadgeHTML(item.stage_key, item.progres_stage)}
                                </div>
                                <a href="<?= site_url('koordinatorta/detail_mahasiswa/'); ?>${item.nim}" class="px-3.5 py-1.5 bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs inline-flex items-center gap-1.5">
                                    <span>Detail</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    `;
                });
                mobileContainer.innerHTML = cardsHtml;
            }

            // Render Pagination Buttons
            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            const container = document.getElementById('paginationControls');
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = `
                <button type="button" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
            `;

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }

            for (let p = startPage; p <= endPage; p++) {
                html += `
                    <button type="button" onclick="goToPage(${p})" class="w-8 h-8 rounded-lg flex items-center justify-center border ${p === currentPage ? 'border-orange-500 bg-orange-600 text-white font-bold' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'} font-bold text-xs">
                        ${p}
                    </button>
                `;
            }

            html += `
                <button type="button" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            `;

            container.innerHTML = html;
        }

        function goToPage(page) {
            currentPage = page;
            renderTable();
            window.scrollTo({ top: 300, behavior: 'smooth' });
        }

        // ==========================================
        // FLOATING MULTI-SUB-PRATINJAU BERKAS HANDLERS
        // (IDENTIK DENGAN DOSEN WALI MULTI-VIEW)
        // ==========================================
        window.activeLihatBerkasNim = null;
        window.activePreviews = []; // [{ nim, docKey }]
        window.previewIframeInteractions = {}; // { 'nim_docKey': bool }

        function resolveDocPdfUrl(filename, defaultUrl = '') {
            if (!filename) return defaultUrl || '<?= base_url("uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf"); ?>';
            if (filename.startsWith('http://') || filename.startsWith('https://')) return filename;
            if (filename.startsWith('uploads/')) return '<?= base_url(); ?>' + filename;
            return '<?= base_url("uploads/persyaratan_ta/"); ?>' + filename;
        }

        function openStudentBerkasPreview(nim, docKey = null) {
            if (!nim) return;
            const item = RAW_PESERTA_DATA.find(p => String(p.nim) === String(nim));
            if (!item) return;

            window.activeLihatBerkasNim = String(nim);
            if (!window.activePreviews) window.activePreviews = [];

            if (docKey) {
                const isPreviewed = window.activePreviews.some(p => String(p.nim) === String(nim) && String(p.docKey) === String(docKey));
                if (!isPreviewed) {
                    if (window.activePreviews.length >= 5) window.activePreviews.shift();
                    window.activePreviews.push({ nim: String(nim), docKey: String(docKey) });
                }
            }

            const container = document.getElementById('lihatBerkasContainer');
            if (container) {
                container.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            renderLihatBerkasView();
        }

        function closeLihatBerkasPanel() {
            const container = document.getElementById('lihatBerkasContainer');
            if (container) {
                container.style.display = 'none';
                document.body.style.overflow = '';
            }
            window.activeLihatBerkasNim = null;
            window.activePreviews = [];
        }

        window.activeFocusedPreviewKey = null;

        function focusPreviewCard(nim, docKey) {
            window.activeFocusedPreviewKey = `${nim}_${docKey}`;
            
            // Update all open preview cards: border, ring, pointer-events on iframe, overlay
            (window.activePreviews || []).forEach(p => {
                const key = `${p.nim}_${p.docKey}`;
                const cardEl = document.getElementById(`previewCard_${key}`);
                const iframe = document.getElementById(`iframePreviewBerkas_${key}`);
                const overlay = document.getElementById(`previewOverlay_${key}`);
                const isFocused = (key === window.activeFocusedPreviewKey);

                if (cardEl) {
                    if (isFocused) {
                        cardEl.classList.add('ring-2', 'ring-orange-500', 'border-orange-500', 'shadow-xl', 'shadow-orange-500/15');
                        cardEl.classList.remove('border-slate-200/90');
                    } else {
                        cardEl.classList.remove('ring-2', 'ring-orange-500', 'border-orange-500', 'shadow-xl', 'shadow-orange-500/15');
                        cardEl.classList.add('border-slate-200/90');
                    }
                }
                if (iframe) {
                    if (isFocused) {
                        iframe.classList.remove('pointer-events-none');
                        iframe.classList.add('pointer-events-auto');
                    } else {
                        iframe.classList.remove('pointer-events-auto');
                        iframe.classList.add('pointer-events-none');
                    }
                }
                if (overlay) {
                    if (isFocused) {
                        overlay.classList.add('hidden');
                    } else {
                        overlay.classList.remove('hidden');
                    }
                }
            });
        }

        function previewBerkasItem(nim, docKey) {
            if (!window.activePreviews) window.activePreviews = [];
            const idx = window.activePreviews.findIndex(p => String(p.nim) === String(nim) && String(p.docKey) === String(docKey));
            if (idx > -1) {
                closeSinglePreview(idx);
                return;
            }

            if (window.activePreviews.length >= 5) {
                window.activePreviews.shift();
            }
            window.activePreviews.push({ nim: String(nim), docKey: String(docKey) });
            window.activeFocusedPreviewKey = `${nim}_${docKey}`;
            renderLihatBerkasView();

            setTimeout(() => {
                focusPreviewCard(nim, docKey);
                const previewWrapper = document.getElementById('wrapperPreviewBerkas');
                if (previewWrapper) {
                    previewWrapper.scrollLeft = previewWrapper.scrollWidth;
                }
            }, 100);
        }

        function closeSinglePreview(index) {
            if (!window.activePreviews) return;
            const removed = window.activePreviews.splice(index, 1)[0];
            if (removed && `${removed.nim}_${removed.docKey}` === window.activeFocusedPreviewKey) {
                if (window.activePreviews.length > 0) {
                    const last = window.activePreviews[window.activePreviews.length - 1];
                    window.activeFocusedPreviewKey = `${last.nim}_${last.docKey}`;
                } else {
                    window.activeFocusedPreviewKey = null;
                }
            }
            renderLihatBerkasView();
        }

        function updateLihatBerkasLayout() {
            const container = document.getElementById('lihatBerkasContainer');
            const wrapper = document.getElementById('wrapperDaftarMhs');
            const previewWrapper = document.getElementById('wrapperPreviewBerkas');
            if (!container || !wrapper) return;

            const isMobile = window.innerWidth < 1024;
            const isPreviewActive = window.activePreviews && window.activePreviews.length > 0;

            if (isMobile) {
                if (isPreviewActive) {
                    container.className = 'fixed inset-0 pointer-events-none z-[60] flex flex-col items-center justify-start p-2.5 sm:p-4 gap-3 overflow-y-auto bg-slate-900/60 backdrop-blur-xs pointer-events-auto';
                    wrapper.className = 'flex flex-col items-center gap-2.5 w-full max-w-[94vw] sm:max-w-md shrink-0';
                    if (previewWrapper) {
                        previewWrapper.className = 'flex flex-row items-start gap-3 w-full max-w-[94vw] sm:max-w-md overflow-x-auto p-1 shrink-0 scroll-smooth scrollbar-none pb-8';
                    }
                } else {
                    container.className = 'fixed inset-0 pointer-events-none z-[60] flex items-center justify-center p-3 sm:p-5 gap-4 overflow-y-auto bg-slate-900/60 backdrop-blur-xs pointer-events-auto';
                    wrapper.className = 'flex flex-col items-center justify-center w-full max-w-[94vw] sm:max-w-md max-h-[90vh] overflow-y-auto shrink-0 my-auto scrollbar-none';
                }
            } else if (isPreviewActive) {
                container.className = 'fixed inset-0 pointer-events-none z-[60] flex flex-row items-center justify-start p-4 sm:p-6 gap-4 sm:gap-5 overflow-x-auto scrollbar-none bg-slate-900/60 backdrop-blur-xs pointer-events-auto';
                wrapper.className = 'flex flex-col gap-3 max-h-[92vh] overflow-y-auto pr-1 shrink-0 w-[380px] sm:w-[410px] scrollbar-none';
                if (previewWrapper) {
                    previewWrapper.className = 'flex items-center gap-3 shrink-0 max-w-[calc(100vw-460px)] overflow-x-auto p-1.5 scroll-smooth scrollbar-none';
                }
            } else {
                container.className = 'fixed inset-0 pointer-events-none z-[60] flex flex-row items-center justify-center p-4 sm:p-6 gap-4 sm:gap-5 overflow-x-auto scrollbar-none bg-slate-900/60 backdrop-blur-xs pointer-events-auto';
                wrapper.className = 'flex flex-row items-center gap-4 max-h-[92vh] overflow-x-auto p-1 shrink-0 scrollbar-none w-[380px] sm:w-[410px]';
            }
        }

        function renderLihatBerkasView() {
            updateLihatBerkasLayout();
            renderStudentCard();
            renderAllPreviewCards();
        }

        function renderStudentCard() {
            const wrapper = document.getElementById('wrapperDaftarMhs');
            if (!wrapper) return;

            const nim = window.activeLihatBerkasNim;
            if (!nim) {
                wrapper.innerHTML = '';
                return;
            }

            const item = RAW_PESERTA_DATA.find(p => String(p.nim) === String(nim));
            if (!item) {
                wrapper.innerHTML = '';
                return;
            }

            const name = item.nama || item.name || 'Mahasiswa';
            const summary = item.berkas_summary || {};
            const items = summary.items || [];
            const isMobile = window.innerWidth < 1024;
            const cardWidthClass = isMobile ? 'w-full max-w-[94vw] sm:max-w-md' : 'w-[380px] sm:w-[410px] shrink-0';

            let validCount = 0;
            let itemsHtml = '';

            if (items.length === 0) {
                itemsHtml = `
                    <div class="p-6 text-center text-slate-400">
                        <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                        <p class="font-bold text-xs">Belum ada berkas</p>
                    </div>
                `;
            } else {
                items.forEach((b, idx) => {
                    const docKey = b.kode || `doc_${idx}`;
                    const st = b.status || 'Pending';
                    if (st === 'Valid') validCount++;

                    const isCurrentlyPreviewed = window.activePreviews && window.activePreviews.some(p => String(p.nim) === String(nim) && String(p.docKey) === String(docKey));
                    const rawFile = b.file_name || '';
                    const fileUrl = b.file_url ? b.file_url : resolveDocPdfUrl(rawFile);

                    let stBadge = '';
                    if (st === 'Valid') {
                        stBadge = '<span class="px-1.5 py-0.2 text-[8.5px] font-bold bg-emerald-100 text-emerald-700 rounded-full border border-emerald-200 shrink-0">Valid</span>';
                    } else if (st === 'Invalid') {
                        stBadge = '<span class="px-1.5 py-0.2 text-[8.5px] font-bold bg-rose-100 text-rose-700 rounded-full border border-rose-200 shrink-0">Revisi</span>';
                    } else {
                        stBadge = '<span class="px-1.5 py-0.2 text-[8.5px] font-bold bg-amber-100 text-amber-700 rounded-full border border-amber-200 shrink-0">Pending</span>';
                    }

                    // Clean name like "KSM (Kartu Studi Mahasiswa)" -> "KSM"
                    let displayDocName = (b.kode === 'ksm' ? 'KSM' : (b.nama || b.kode)).replace(/\s*\([^)]*\)/, '');

                    itemsHtml += `
                        <div class="p-2.5 rounded-xl border transition-all ${isCurrentlyPreviewed ? 'bg-orange-50/70 border-orange-400 ring-2 ring-orange-400/40 shadow-xs' : 'bg-white border-slate-200 hover:border-slate-300'}">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <i class="fa-solid fa-file-pdf text-xs ${isCurrentlyPreviewed ? 'text-orange-600' : 'text-rose-500'} shrink-0"></i>
                                        <span class="font-bold text-xs text-slate-900 truncate" title="${escapeHtml(b.nama || b.kode)}">${idx + 1}. ${escapeHtml(displayDocName)}</span>
                                        ${stBadge}
                                    </div>
                                    <div class="text-[9.5px] text-slate-400 font-mono truncate mt-0.5" title="${escapeHtml(rawFile || 'Belum diunggah')}">
                                        <i class="fa-solid fa-file-lines text-[9px] mr-1 text-rose-400"></i>${escapeHtml(rawFile || 'Belum diunggah')}
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="button" 
                                            onclick="previewBerkasItem('${nim}', '${docKey}')" 
                                            class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all active:scale-95 flex items-center gap-1 cursor-pointer ${isCurrentlyPreviewed ? 'bg-orange-600 text-white shadow-xs' : 'bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200'}">
                                        <i class="fa-solid ${isCurrentlyPreviewed ? 'fa-eye-slash' : 'fa-eye'} text-[9px]"></i>
                                        <span>${isCurrentlyPreviewed ? 'Tutup' : 'Lihat'}</span>
                                    </button>
                                    <a href="${fileUrl}" download="${escapeHtml(rawFile || 'berkas.pdf')}" target="_blank" class="px-2.5 py-1 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg transition flex items-center gap-1 cursor-pointer" title="Unduh Berkas">
                                        <i class="fa-solid fa-download text-[9px]"></i>
                                        <span>Unduh</span>
                                    </a>
                                </div>
                            </div>
                            ${b.catatan ? `
                                <div class="mt-1.5 p-1.5 bg-rose-50 border border-rose-200/80 rounded-lg text-[9.5px] text-rose-700 flex items-start gap-1">
                                    <i class="fa-solid fa-comment-dots text-rose-500 mt-0.5 shrink-0"></i>
                                    <div><strong class="font-bold">Catatan:</strong> ${escapeHtml(b.catatan)}</div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                });
            }

            const totalDocs = items.length || 4;

            wrapper.innerHTML = `
                <div class="student-card-item pointer-events-auto ${cardWidthClass} bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col shrink-0">
                    <!-- Dark Header -->
                    <div class="p-3 px-4 bg-slate-900 text-white flex items-center justify-between gap-2 shrink-0 border-b border-slate-800">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-6 h-6 rounded-lg bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-[11px] shrink-0 shadow-2xs">
                                1
                            </div>
                            <div class="min-w-0 flex items-center gap-2">
                                <h4 class="text-xs font-bold text-white truncate max-w-[170px] sm:max-w-[210px]">${escapeHtml(name)}</h4>
                                <span class="px-2 py-0.5 rounded bg-white/10 text-orange-300 font-mono text-[10px] font-bold">${escapeHtml(nim)}</span>
                            </div>
                        </div>
                        <button type="button" onclick="closeLihatBerkasPanel()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer" title="Tutup">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- List of Berkas -->
                    <div class="p-3 space-y-2.5 bg-slate-50/50 overflow-y-auto max-h-[60vh] scrollbar-none">
                        ${itemsHtml}
                    </div>

                    <!-- Footer Info & Actions -->
                    <div class="p-3 px-4 bg-white border-t border-slate-200 flex items-center justify-between gap-2 shrink-0">
                        <span class="text-[11px] text-slate-500 font-medium">
                            <strong class="text-slate-800 font-bold">${validCount}/${totalDocs}</strong> Berkas Disetujui
                        </span>
                        <button type="button" onclick="closeLihatBerkasPanel()" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            `;
        }

        function renderAllPreviewCards() {
            const wrapper = document.getElementById('wrapperPreviewBerkas');
            if (!wrapper) return;

            if (!window.activePreviews || window.activePreviews.length === 0) {
                wrapper.innerHTML = '';
                wrapper.classList.add('hidden');
                return;
            }

            wrapper.classList.remove('hidden');
            const totalPreviews = window.activePreviews.length;
            const isMobile = window.innerWidth < 1024;
            const panelWidthClass = isMobile 
                ? 'w-[90vw] sm:w-[400px] shrink-0' 
                : (totalPreviews >= 3 ? 'w-[360px] sm:w-[390px] lg:w-[420px] shrink-0' : (totalPreviews === 2 ? 'w-[420px] sm:w-[460px] lg:w-[500px] shrink-0' : 'w-[480px] sm:w-[540px] lg:w-[580px] shrink-0'));

            // Remove cards no longer in activePreviews
            const activeCardIds = window.activePreviews.map(p => `previewCard_${p.nim}_${p.docKey}`);
            Array.from(wrapper.children).forEach(child => {
                if (!activeCardIds.includes(child.id)) {
                    child.remove();
                }
            });

            // Append or update cards
            window.activePreviews.forEach((p, index) => {
                const cardId = `previewCard_${p.nim}_${p.docKey}`;
                let cardEl = document.getElementById(cardId);
                const slotNum = index + 1;

                const item = RAW_PESERTA_DATA.find(m => String(m.nim) === String(p.nim)) || {};
                const summary = item.berkas_summary || {};
                const items = summary.items || [];
                const doc = items.find(d => d.kode === p.docKey) || items.find((_, i) => `doc_${i}` === p.docKey) || { nama: p.docKey, kode: p.docKey, status: 'Pending' };

                const rawFilename = doc.file_name || `${p.docKey}_${p.nim}.pdf`;
                const pdfUrl = doc.file_url ? doc.file_url : resolveDocPdfUrl(rawFilename);
                const fullName = item.nama || item.name || 'Mahasiswa ' + p.nim;
                const currentStatus = doc.status || 'Pending';

                let statusBadgeHtml = '';
                if (currentStatus === 'Valid') {
                    statusBadgeHtml = '<span class="px-2 py-0.5 rounded-md text-[9.5px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"><i class="fa-solid fa-circle-check mr-1 text-emerald-600"></i>Valid</span>';
                } else if (currentStatus === 'Invalid') {
                    statusBadgeHtml = '<span class="px-2 py-0.5 rounded-md text-[9.5px] font-bold bg-rose-100 text-rose-800 border border-rose-200"><i class="fa-solid fa-triangle-exclamation mr-1 text-rose-600"></i>Revisi</span>';
                } else {
                    statusBadgeHtml = '<span class="px-2 py-0.5 rounded-md text-[9.5px] font-bold bg-amber-100 text-amber-800 border border-amber-200"><i class="fa-solid fa-clock mr-1 text-amber-600"></i>Pending</span>';
                }

                const isFocused = (window.activeFocusedPreviewKey === `${p.nim}_${p.docKey}`);
                const focusBorderClass = isFocused ? 'ring-2 ring-orange-500 border-orange-500 shadow-xl shadow-orange-500/15' : 'border-slate-200/90 hover:border-slate-300';

                if (cardEl) {
                    cardEl.className = `preview-card-item pointer-events-auto bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col shrink-0 transition-all duration-200 ${panelWidthClass} ${focusBorderClass}`;
                    const badgeEl = cardEl.querySelector('.preview-slot-badge');
                    if (badgeEl) badgeEl.innerHTML = totalPreviews > 1 ? slotNum : '<i class="fa-solid fa-file-pdf"></i>';
                    const closeBtn = cardEl.querySelector('.preview-close-btn');
                    if (closeBtn) closeBtn.setAttribute('onclick', `closeSinglePreview(${index})`);
                    const footerCloseBtn = cardEl.querySelector('.preview-footer-close-btn');
                    if (footerCloseBtn) footerCloseBtn.setAttribute('onclick', `closeSinglePreview(${index})`);
                    const iframe = document.getElementById(`iframePreviewBerkas_${p.nim}_${p.docKey}`);
                    if (iframe) {
                        if (isFocused) {
                            iframe.classList.remove('pointer-events-none');
                            iframe.classList.add('pointer-events-auto');
                        } else {
                            iframe.classList.remove('pointer-events-auto');
                            iframe.classList.add('pointer-events-none');
                        }
                    }
                    const overlay = document.getElementById(`previewOverlay_${p.nim}_${p.docKey}`);
                    if (overlay) {
                        if (isFocused) overlay.classList.add('hidden');
                        else overlay.classList.remove('hidden');
                    }
                } else {
                    const div = document.createElement('div');
                    div.id = cardId;
                    div.className = `preview-card-item pointer-events-auto bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col shrink-0 transition-all duration-200 ${panelWidthClass} ${focusBorderClass}`;
                    div.setAttribute('onclick', `focusPreviewCard('${p.nim}', '${p.docKey}')`);
                    div.innerHTML = `
                        <!-- Header Pratinjau Kompak dengan Label Sub-Pratinjau Jelas -->
                        <div class="p-2 px-3 bg-slate-900 text-white flex items-center justify-between gap-2 shrink-0 border-b border-slate-800">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="preview-slot-badge w-6 h-6 rounded-lg bg-rose-600/30 border border-rose-500/50 text-rose-400 flex items-center justify-center font-bold text-[11px] shrink-0 shadow-2xs">
                                    ${totalPreviews > 1 ? slotNum : '<i class="fa-solid fa-file-pdf"></i>'}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.2 text-[7.5px] font-bold bg-orange-500/20 text-orange-400 border border-orange-500/30 rounded uppercase tracking-wider">Sub-Pratinjau</span>
                                        <h4 class="text-xs font-bold text-white truncate max-w-[150px] sm:max-w-[200px]">${escapeHtml(doc.nama || doc.kode)}</h4>
                                    </div>
                                    <p class="text-[9.5px] text-slate-300 font-medium truncate mt-0.5">${escapeHtml(fullName)} · <span class="font-mono text-slate-400">${escapeHtml(rawFilename)}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <a href="${pdfUrl}" target="_blank" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Buka Layar Penuh di Tab Baru">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                </a>
                                <button type="button" onclick="event.stopPropagation(); closeSinglePreview(${index})" class="preview-close-btn w-6 h-6 rounded-lg bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer ml-0.5" title="Tutup Pratinjau Ini">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Body Frame Pratinjau PDF -->
                        <div class="${isMobile ? 'h-[300px] sm:h-[340px]' : 'h-[380px] sm:h-[410px] md:h-[430px]'} bg-slate-200 relative border-b border-slate-200 overflow-hidden cursor-default select-none">
                            <iframe id="iframePreviewBerkas_${p.nim}_${p.docKey}" src="${pdfUrl}#toolbar=0&navpanes=0" class="w-full h-full border-0 ${isFocused ? 'pointer-events-auto' : 'pointer-events-none'}" title="Pratinjau Dokumen PDF"></iframe>
                            <div id="previewOverlay_${p.nim}_${p.docKey}" class="absolute inset-0 cursor-pointer ${isFocused ? 'hidden' : ''}" onclick="event.stopPropagation(); focusPreviewCard('${p.nim}', '${p.docKey}')" title="Klik untuk fokus dan scroll berkas ini"></div>
                        </div>

                        <!-- Footer Pratinjau dengan Status & Unduh -->
                        <div class="p-2 px-3 bg-white flex flex-col gap-1.5 shrink-0 border-t border-slate-200/90">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <div class="flex items-center gap-1.5">
                                    <span id="previewStatusBadge_${p.nim}_${p.docKey}">
                                        ${statusBadgeHtml}
                                    </span>
                                    <a href="${pdfUrl}" download="${escapeHtml(rawFilename)}" target="_blank" class="px-2 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold transition flex items-center gap-1 cursor-pointer shadow-2xs text-[10.5px]" title="Unduh Berkas Ini">
                                        <i class="fa-solid fa-download text-[9px]"></i>
                                        <span>Unduh</span>
                                    </a>
                                </div>

                                <div class="flex items-center gap-1">
                                    <button type="button" 
                                            onclick="event.stopPropagation(); closeSinglePreview(${index})" 
                                            class="preview-footer-close-btn px-2.5 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition cursor-pointer text-xs ml-0.5" 
                                            title="Tutup Pratinjau">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                            ${doc.catatan ? `
                                <div class="p-1.5 bg-rose-50 border border-rose-200 rounded-lg text-[9.5px] text-rose-700 flex items-start gap-1">
                                    <i class="fa-solid fa-comment-dots text-rose-500 mt-0.5 shrink-0"></i>
                                    <div><strong class="font-bold">Catatan:</strong> ${escapeHtml(doc.catatan)}</div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    wrapper.appendChild(div);
                }
            });
        }

        function escapeHtml(text) {
            return String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

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

        function initStageChipsDots() {
            const wrap = document.getElementById('stageChipsWrap');
            const dotsContainer = document.getElementById('stageChipsDots');
            if (!wrap || !dotsContainer) return;

            const chips = Array.from(wrap.querySelectorAll('.stage-chip'));
            if (chips.length <= 1) return;

            dotsContainer.innerHTML = '';
            chips.forEach((chip, idx) => {
                const dot = document.createElement('span');
                dot.className = 'pill-dot' + (idx === 0 ? ' active' : '');
                dot.setAttribute('title', chip.textContent.trim());
                dot.addEventListener('click', () => {
                    chip.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
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
            updateDots();
        }

        // Init render on page load
        function initMonitoringPage() {
            renderTable();
            initStatSliderDots();
            initStageChipsDots();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMonitoringPage);
        } else {
            initMonitoringPage();
        }
    </script>
</body>
</html>
