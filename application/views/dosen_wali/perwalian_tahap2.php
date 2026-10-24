<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Perwalian Tahap 2 (Penilaian & Evaluasi) - Dosen Wali'); ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74',
                            400: '#fb923c', 500: '#f97316', 600: '#ea580c', 700: '#c2410c',
                            800: '#9a3412', 900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SheetJS (XLSX parsing & export) -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .swal2-container { z-index: 99999999 !important; }
        
        /* Unified Multi-Search Bar Component Styling */
        .search-pill-container {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }
        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 2px 10px;
            flex: 1;
            height: 44px;
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
            height: 20px;
            background-color: #cbd5e1;
            margin: 0 10px;
            flex-shrink: 0;
        }
        .btn-standalone-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff7ed;
            border: 1.5px solid #ffedd5;
            border-radius: 14px;
            padding: 6px 14px;
            height: 44px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #ea580c;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.06);
            flex-shrink: 0;
        }
        .btn-standalone-add:hover {
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
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .btn-remove-row:hover {
            background: #ffe4e6;
            border-color: #fda4af;
            color: #be123c;
            transform: scale(1.05);
        }
        .extra-rows-card {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            box-shadow: 0 20px 40px -8px rgba(15, 23, 42, 0.16);
            z-index: 50;
            padding: 16px;
        }
        .extra-filter-row {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 10;
            width: 100%;
        }
        .extra-filter-row .unified-search-pill {
            flex: 1;
            min-width: 0;
            width: 100%;
        }
        .custom-dropdown-container.open .custom-dropdown-menu {
            display: block !important;
        }

        @media (max-width: 639.98px) {
            .search-pill-container {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
            }
            .btn-standalone-add {
                width: 100% !important;
                justify-content: center !important;
                height: 38px !important;
            }
            .extra-rows-card {
                position: static !important;
                margin-top: 8px !important;
                box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1) !important;
                padding: 12px !important;
            }
        }

        /* Stats Mobile Horizontal Snap Slider */
        .stats-slider-container {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
        .stats-slider-container::-webkit-scrollbar {
            display: none !important;
        }

        .clay-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(226, 232, 240, 0.8);
            transition: all 0.2s ease;
        }

        .stat-card-3d {
            border-radius: 18px;
            padding: 16px 20px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 20px -4px rgba(0,0,0,0.05);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .stat-card-3d:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -4px rgba(0,0,0,0.08);
        }

        .dropzone-box {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            background: #f8fafc;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .dropzone-box:hover {
            border-color: #059669;
            background: #f0fdf4;
        }
        .dropzone-box.drag-active {
            border-color: #059669 !important;
            background: #dcfce7 !important;
            transform: scale(1.02);
            box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.25);
        }

        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            transition: all 0.3s ease;
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
            }
            .header-title-shift {
                padding-left: 3rem !important;
            }
        }
        body.curved-sidebar-desktop-collapsed .header-title-shift {
            padding-left: 3.25rem !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>

<body class="bg-gradient-to-br from-purple-100/60 via-orange-50/50 to-amber-100/70 text-slate-800 font-sans antialiased min-h-screen flex flex-col relative">

    <!-- Sidebar Partial -->
    <?php $this->load->view('partials/dosen_sidebar'); ?>

    <!-- Page Wrapper -->
    <div class="page-wrapper-for-sidebar flex flex-col min-h-screen flex-grow">

        <!-- Top Header Bar -->
        <header class="w-full bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-3.5 sm:px-8 py-2.5 sm:py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-4">
            <!-- Title & Mobile Nav Row -->
            <div class="flex items-center justify-between gap-2 min-w-0 w-full sm:w-auto">
                <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 header-title-shift">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-purple-500/20 shrink-0">
                        <i class="fa-solid fa-award text-sm sm:text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center space-x-1.5 sm:space-x-2">
                            <span class="px-1.5 sm:px-2 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-800 rounded-md shrink-0">Tahap 2</span>
                            <h1 class="text-xs sm:text-lg font-bold text-slate-900 leading-tight truncate">Evaluasi Nilai (Tahap 2)</h1>
                        </div>
                    </div>
                </div>

                <!-- Link to Tahap 1 on mobile top row -->
                <a href="<?= site_url('dosen/wali/perwalian_tahap1'); ?>" class="inline-flex sm:hidden items-center px-2.5 py-1 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition shrink-0 whitespace-nowrap shadow-2xs" title="Buka Tahap 1 (KRS)">
                    <i class="fa-solid fa-arrow-left text-[10px] mr-1 text-slate-400"></i> <span>Tahap 1</span>
                </a>
            </div>

            <!-- Action Buttons Row -->
            <div class="hidden sm:flex items-center gap-2 shrink-0">
                <a href="<?= site_url('dosen/wali/perwalian_tahap1'); ?>" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition shadow-2xs shrink-0 whitespace-nowrap" title="Buka Tahap 1 (KRS)">
                    <i class="fa-solid fa-arrow-left text-[11px] mr-1.5 text-slate-400"></i> <span>Buka Tahap 1</span>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="w-full px-3 sm:px-8 py-4 sm:py-8 flex-grow">

            <?php
                $totMhs2 = (int)($stats['tahap1_disetujui'] ?? 0);
                $belumDinilai = (int)($stats['tahap2_belum_dinilai'] ?? 0);
                $selesai = (int)($stats['tahap2_selesai'] ?? 0);
                $proses = (int)($stats['tahap2_proses'] ?? 0);

                $pctBelum = $totMhs2 > 0 ? round(($belumDinilai / $totMhs2) * 100) : 0;
                $pctSelesai = $totMhs2 > 0 ? round(($selesai / $totMhs2) * 100) : 0;
                $pctProses = $totMhs2 > 0 ? round(($proses / $totMhs2) * 100) : 0;
            ?>

            <!-- Stats Overview Cards (Highlight Card Design & Mobile Snap Slider) -->
            <div class="relative mb-4 sm:mb-6">
                <div id="statsCardsSliderT2" class="stats-slider-container flex sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 overflow-x-auto sm:overflow-visible snap-x snap-mandatory py-1 scrollbar-none items-stretch">
                    <!-- 1. Total Mahasiswa Card -->
                    <div class="stats-slide-item w-full min-w-full sm:w-auto sm:min-w-0 snap-center shrink-0 group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full flex flex-col">
                        <div class="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-orange-50/20 to-white shadow-md sm:shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-orange-500/40 hover:shadow-2xl hover:shadow-orange-500/10 p-3.5 sm:p-5 h-full flex flex-col justify-between">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-orange-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-orange-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-orange-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-2.5 sm:gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-orange-600 transition-colors">TOTAL MAHASISWA</p>
                                    <h3 id="statT2TotalMhs" class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5 sm:mt-1 tracking-tight"><?= $totMhs2; ?></h3>
                                    <p class="text-[11px] sm:text-xs font-medium text-slate-500 mt-0.5 sm:mt-1 line-clamp-1">KRS Tahap 1 telah disetujui</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-xl sm:rounded-2xl bg-orange-500/20 blur-md group-hover:blur-lg group-hover:bg-orange-500/30 transition-all"></div>
                                    <div class="relative p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-orange-200/80 bg-gradient-to-br from-orange-50 to-orange-100/70 shadow-sm sm:shadow-md text-orange-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-users text-sm sm:text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-2.5 pt-2 sm:mt-3 sm:pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-orange-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-orange-500 rounded-full animate-bounce"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-orange-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>

                            <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-orange-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        </div>
                    </div>

                    <!-- 2. Belum Dinilai Card (Amber) -->
                    <div class="stats-slide-item w-full min-w-full sm:w-auto sm:min-w-0 snap-center shrink-0 group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full flex flex-col">
                        <div class="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-amber-50/20 to-white shadow-md sm:shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-amber-500/40 hover:shadow-2xl hover:shadow-amber-500/10 p-3.5 sm:p-5 h-full flex flex-col justify-between">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-amber-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-amber-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-2.5 sm:gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-amber-600 transition-colors">BELUM DINILAI</p>
                                    <h3 id="statT2Belum" class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5 sm:mt-1 tracking-tight">
                                        <?= $belumDinilai; ?> <span class="text-[11px] sm:text-xs font-semibold text-amber-600 font-normal">(<?= $pctBelum; ?>%)</span>
                                    </h3>
                                    <p class="text-[11px] sm:text-xs font-medium text-slate-500 mt-0.5 sm:mt-1 line-clamp-1">Belum ada evaluasi nilai</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-xl sm:rounded-2xl bg-amber-500/20 blur-md group-hover:blur-lg group-hover:bg-amber-500/30 transition-all"></div>
                                    <div class="relative p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50 to-amber-100/70 shadow-sm sm:shadow-md text-amber-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-hourglass-start text-sm sm:text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-2.5 pt-2 sm:mt-3 sm:pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-amber-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-amber-500 rounded-full animate-bounce"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>

                            <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-amber-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        </div>
                    </div>

                    <!-- 3. Proses Penilaian Card (Cyan) -->
                    <div class="stats-slide-item w-full min-w-full sm:w-auto sm:min-w-0 snap-center shrink-0 group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full flex flex-col">
                        <div class="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-cyan-50/20 to-white shadow-md sm:shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-cyan-500/40 hover:shadow-2xl hover:shadow-cyan-500/10 p-3.5 sm:p-5 h-full flex flex-col justify-between">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-cyan-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-cyan-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-2.5 sm:gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors">PROSES PENILAIAN</p>
                                    <h3 id="statT2Proses" class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5 sm:mt-1 tracking-tight">
                                        <?= $proses; ?> <span class="text-[11px] sm:text-xs font-semibold text-cyan-600 font-normal">(<?= $pctProses; ?>%)</span>
                                    </h3>
                                    <p class="text-[11px] sm:text-xs font-medium text-slate-500 mt-0.5 sm:mt-1 line-clamp-1">Nilai sedang diinput bertahap</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-xl sm:rounded-2xl bg-cyan-500/20 blur-md group-hover:blur-lg group-hover:bg-cyan-500/30 transition-all"></div>
                                    <div class="relative p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-sm sm:shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-pen-ruler text-sm sm:text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-2.5 pt-2 sm:mt-3 sm:pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-cyan-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-cyan-500 rounded-full animate-bounce"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>

                            <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-cyan-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        </div>
                    </div>

                    <!-- 4. Evaluasi Selesai Card (Emerald) -->
                    <div class="stats-slide-item w-full min-w-full sm:w-auto sm:min-w-0 snap-center shrink-0 group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1 h-full flex flex-col">
                        <div class="rounded-xl sm:rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-emerald-50/20 to-white shadow-md sm:shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 p-3.5 sm:p-5 h-full flex flex-col justify-between">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-emerald-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-emerald-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-2.5 sm:gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">EVALUASI SELESAI</p>
                                    <h3 id="statT2Selesai" class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5 sm:mt-1 tracking-tight">
                                        <?= $selesai; ?> <span class="text-[11px] sm:text-xs font-semibold text-emerald-600 font-normal">(<?= $pctSelesai; ?>%)</span>
                                    </h3>
                                    <p class="text-[11px] sm:text-xs font-medium text-slate-500 mt-0.5 sm:mt-1 line-clamp-1">Nilai lengkap & perwalian terkunci</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-xl sm:rounded-2xl bg-emerald-500/20 blur-md group-hover:blur-lg group-hover:bg-emerald-500/30 transition-all"></div>
                                    <div class="relative p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-emerald-100/70 shadow-sm sm:shadow-md text-emerald-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-lock text-sm sm:text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-2.5 pt-2 sm:mt-3 sm:pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-emerald-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-emerald-500 rounded-full animate-bounce"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>

                            <div class="absolute top-0 left-0 w-10 h-10 bg-gradient-to-br from-white/80 to-transparent rounded-br-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            <div class="absolute bottom-0 right-0 w-10 h-10 bg-gradient-to-tl from-emerald-500/10 to-transparent rounded-tl-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Slider Indicator Bar & Dots -->
                <div class="flex sm:hidden items-center justify-center gap-1.5 mt-2" id="statsSliderIndicatorsT2">
                    <button type="button" onclick="scrollToStatSlideT2(0)" class="stats-indicator-dot h-1.5 w-5 rounded-full bg-orange-500 transition-all duration-300 border-none cursor-pointer p-0" aria-label="Slide 1"></button>
                    <button type="button" onclick="scrollToStatSlideT2(1)" class="stats-indicator-dot h-1.5 w-1.5 rounded-full bg-slate-300 transition-all duration-300 border-none cursor-pointer p-0" aria-label="Slide 2"></button>
                    <button type="button" onclick="scrollToStatSlideT2(2)" class="stats-indicator-dot h-1.5 w-1.5 rounded-full bg-slate-300 transition-all duration-300 border-none cursor-pointer p-0" aria-label="Slide 3"></button>
                    <button type="button" onclick="scrollToStatSlideT2(3)" class="stats-indicator-dot h-1.5 w-1.5 rounded-full bg-slate-300 transition-all duration-300 border-none cursor-pointer p-0" aria-label="Slide 4"></button>
                </div>
            </div>

            <!-- Unified Multi-Search Bar Component (Exact Reference Style) -->
            <div class="clay-card p-3 sm:p-5 mb-5 sm:mb-6 space-y-2.5 sm:space-y-3">
                <div class="relative search-pill-container" id="multiSearchWrapperT2">
                    <!-- Main Search Pill -->
                    <div class="unified-search-pill">
                        <!-- Main Category Selector Dropdown -->
                        <div class="relative custom-dropdown-container" id="dropdownMainCatT2">
                            <input type="hidden" id="mainCatT2" value="query">
                            <button type="button" onclick="toggleDropdownT2('dropdownMainCatT2', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none" title="Kategori Pencarian">
                                <span id="labelMainCatT2" class="text-sm sm:text-base leading-none block">🔍</span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200"></i>
                            </button>
                            <div class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                                <div onclick="selectCategoryT2('main', 'query', '🔍 Semua Kriteria', '🔍')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-orange-600"><span>🔍 Semua Kriteria</span></div>
                                <div onclick="selectCategoryT2('main', 'nama', '🏷️ Nama Mahasiswa', '🏷️')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🏷️ Nama Mahasiswa</span></div>
                                <div onclick="selectCategoryT2('main', 'nim', '🆔 NIM Mahasiswa', '🆔')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🆔 NIM Mahasiswa</span></div>
                                <div onclick="selectCategoryT2('main', 'prodi', '🎓 Program Studi', '🎓')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🎓 Program Studi</span></div>
                                <div onclick="selectCategoryT2('main', 'status', '⚡ Status Penilaian', '⚡')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>⚡ Status Penilaian</span></div>
                            </div>
                        </div>

                        <div class="unified-divider"></div>

                        <!-- Input Text Value -->
                        <div class="flex-1 flex items-center min-w-0">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" id="mainInputT2" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); executeMultiSearchT2(); }" placeholder="Cari nama, NIM, prodi... (Tekan Enter)" class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                        </div>

                        <!-- Tombol Cari -->
                        <button type="button" onclick="executeMultiSearchT2()" class="px-3 sm:px-3.5 py-1.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-500 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition cursor-pointer active:scale-95 shrink-0 ml-1.5" title="Klik untuk melakukan pencarian">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i> Cari
                        </button>
                    </div>

                    <!-- Filter Counter Button -->
                    <button type="button" onclick="toggleExtraPopoverT2(event)" class="btn-standalone-add" title="Buka / Tutup Kriteria Filter Tambahan">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-filter text-orange-500 text-xs"></i>
                            <span class="font-bold text-xs text-orange-700">Filter Tambahan</span>
                        </span>
                        <span id="filterBadgeT2" class="badge-standalone-count">1/4</span>
                    </button>

                    <!-- Extra Filter Rows Card Popover -->
                    <div id="extraCardT2" class="extra-rows-card space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-filter text-orange-500"></i> Kriteria Filter Tambahan
                            </span>
                            <button type="button" onclick="closeExtraPopoverT2()" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center text-xs transition cursor-pointer" title="Tutup Card Filter">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <!-- Container of Extra Filter Rows -->
                        <div id="extraRowsContainerT2" class="space-y-2.5"></div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1">
                            <button type="button" onclick="addExtraRowT2()" id="btnAddExtraRowT2" class="w-full sm:w-auto px-3.5 py-2 sm:py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold text-xs border border-orange-200 flex items-center justify-center gap-1.5 transition cursor-pointer shadow-2xs active:scale-95 shrink-0 whitespace-nowrap">
                                <i class="fa-solid fa-plus text-[11px]"></i> <span>Tambah Kriteria Filter</span>
                            </button>
                            <span id="extraCountTextT2" class="text-[11px] text-slate-500 font-medium text-center sm:text-right shrink-0">1/4 kriteria aktif (Maks. 4)</span>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-100 pt-2.5 mt-2 text-xs">
                            <span class="text-slate-400 text-[11px] leading-relaxed text-center sm:text-left">Gunakan kombinasi kriteria untuk mempersempit pencarian data evaluasi nilai.</span>
                            <button type="button" onclick="resetMultiSearchT2()" class="w-full sm:w-auto text-rose-600 hover:text-rose-700 font-bold text-xs transition-colors cursor-pointer text-center sm:text-right py-1 sm:py-0 shrink-0 inline-flex items-center justify-center gap-1 whitespace-nowrap">
                                <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset Semua Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Search Counter & Active Filter Tags -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 text-xs text-slate-500 pt-1 border-t border-slate-100">
                    <div id="searchResultSummaryT2" class="text-[11px] sm:text-xs">
                        Menampilkan <strong class="text-slate-900 font-bold" id="visibleRowCountT2"><?= count($list_perwalian); ?></strong> dari <strong class="text-slate-900 font-bold" id="totalRowCountT2"><?= count($list_perwalian); ?></strong> mahasiswa bimbingan.
                    </div>
                    <div id="activeTagsContainerT2" class="flex flex-wrap items-center gap-1.5"></div>
                </div>
            </div>

            <!-- Table Mahasiswa Bimbingan Tahap 2 -->
            <div class="clay-card overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 sm:py-4 border-b border-slate-100 space-y-1">
                    <div class="flex items-center justify-between gap-2.5 sm:gap-4">
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900 leading-tight">Daftar Persetujuan Nilai Hasil Studi Mahasiswa (Tahap 2)</h2>
                        <span data-sync-count class="text-[11px] sm:text-xs font-bold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg whitespace-nowrap shrink-0">
                            <?= count($list_perwalian); ?> Mahasiswa
                        </span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-500 leading-normal">
                        Periksa nilai mutu (A-E) yang diinput mahasiswa dan lakukan persetujuan (approve) satu per satu maupun sekaligus (multiple approve)
                    </p>
                </div>

                <!-- 1. Desktop Table View (>= 768px) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-600 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="py-3 px-3 w-10 text-center whitespace-nowrap">No</th>
                                <th class="py-3 px-3 whitespace-nowrap">Mahasiswa</th>
                                <th class="py-3 px-3 whitespace-nowrap">Program Studi</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Periode</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Mata Kuliah</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Progress Approval</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Status Evaluasi</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <?php if (empty($list_perwalian)): ?>
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400 text-lg">
                                            <i class="fa-solid fa-award"></i>
                                        </div>
                                        <p class="font-medium text-slate-600">Belum ada mahasiswa yang KRS Tahap 1-nya disetujui untuk dievaluasi.</p>
                                        <p class="text-xs text-slate-400 mt-1">Mahasiswa akan otomatis muncul di Tahap 2 setelah KRS pada Tahap 1 Anda setujui.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr id="noSearchResultRowT2" style="display: none;">
                                    <td colspan="8" class="py-12 text-center text-slate-400">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400 text-lg">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </div>
                                        <p class="font-medium text-slate-600">Tidak ada mahasiswa yang cocok dengan kriteria pencarian.</p>
                                        <button type="button" onclick="resetMultiSearchT2()" class="mt-2 text-xs font-bold text-orange-600 hover:text-orange-700 underline cursor-pointer">Reset Semua Filter</button>
                                    </td>
                                </tr>
                                <?php $no = 1; foreach ($list_perwalian as $item): 
                                    $rowNim = strtolower(htmlspecialchars($item['nim'] ?? ''));
                                    $rowNama = strtolower(htmlspecialchars($item['nama_lengkap_user'] ?: ($item['nama_mahasiswa'] ?: '')));
                                    $rowProdi = strtolower(htmlspecialchars($item['prodi'] ?? ''));
                                    $rowStatus = strtolower(htmlspecialchars($item['status_tahap2'] ?? 'Belum Dinilai'));
                                    $rowPeriode = strtolower(htmlspecialchars(($item['tahun_ajaran'] ?? '') . ' ' . ($item['semester'] ?? '')));
                                ?>
                                    <tr class="t2-table-row hover:bg-purple-50/40 transition"
                                        id="t2Row_<?= (int)$item['id_perwalian']; ?>"
                                        data-id="<?= (int)$item['id_perwalian']; ?>"
                                        data-total-mk="<?= (int)$item['total_mk']; ?>"
                                        data-nim="<?= $rowNim; ?>"
                                        data-nama="<?= $rowNama; ?>"
                                        data-prodi="<?= $rowProdi; ?>"
                                        data-status="<?= $rowStatus; ?>"
                                        data-periode="<?= $rowPeriode; ?>"
                                    >
                                        <td class="py-2.5 px-3 text-center font-mono text-slate-400 whitespace-nowrap"><?= $no++; ?></td>
                                        
                                        <!-- Mahasiswa Identity -->
                                        <td class="py-2.5 px-3 whitespace-nowrap">
                                            <div class="font-bold text-slate-900 text-xs sm:text-sm leading-snug">
                                                <?= htmlspecialchars($item['nama_lengkap_user'] ?: ($item['nama_mahasiswa'] ?: 'Mahasiswa')); ?>
                                            </div>
                                            <div class="text-[11px] font-mono text-slate-500 leading-tight">
                                                NIM: <?= htmlspecialchars($item['nim']); ?>
                                            </div>
                                        </td>

                                        <!-- Prodi -->
                                        <td class="py-2.5 px-3 whitespace-nowrap">
                                            <span class="inline-block px-2 py-0.5 text-[11px] font-medium bg-slate-100 text-slate-700 rounded-md whitespace-nowrap">
                                                <?= htmlspecialchars($item['prodi'] ?: '-'); ?>
                                            </span>
                                        </td>

                                        <!-- Periode -->
                                        <td class="py-2.5 px-3 text-center font-mono text-[11px] whitespace-nowrap leading-tight">
                                            <div><?= htmlspecialchars($item['tahun_ajaran']); ?></div>
                                            <div class="text-slate-400"><?= htmlspecialchars($item['semester']); ?></div>
                                        </td>

                                        <!-- Total MK & SKS -->
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                            <span class="font-mono font-bold text-slate-900">
                                                <?= (int)$item['total_mk']; ?> MK
                                            </span>
                                            <span class="text-slate-400 text-[11px]">(<?= (int)$item['total_sks']; ?> SKS)</span>
                                        </td>

                                        <!-- Progress Approval -->
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" id="t2ProgressCol_<?= (int)$item['id_perwalian']; ?>">
                                            <div class="font-mono font-bold text-slate-800 whitespace-nowrap" id="t2DinilaiText_<?= (int)$item['id_perwalian']; ?>">
                                                <?= (int)($item['total_approved_mk'] ?? 0); ?> / <?= (int)$item['total_mk']; ?> Disetujui
                                            </div>
                                            <div class="text-[10px] text-slate-500 mt-0.5 space-x-1 whitespace-nowrap" id="t2GradingCountRow_<?= (int)$item['id_perwalian']; ?>" <?= ((int)$item['total_dinilai'] > 0) ? '' : 'style="display: none;"'; ?>>
                                                <span class="text-emerald-600 font-semibold"><i class="fa-solid fa-circle-check text-[9px]"></i> <span id="t2LulusVal_<?= (int)$item['id_perwalian']; ?>"><?= (int)$item['total_lulus']; ?></span> Lulus</span>
                                                <span class="text-rose-600 font-semibold"><i class="fa-solid fa-circle-xmark text-[9px]"></i> <span id="t2TidakLulusVal_<?= (int)$item['id_perwalian']; ?>"><?= (int)$item['total_tidak_lulus']; ?></span> Tidak Lulus</span>
                                            </div>
                                        </td>

                                        <!-- Badge Status Tahap 2 -->
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" id="t2StatusCol_<?= (int)$item['id_perwalian']; ?>">
                                            <?php
                                                $st2 = isset($item['status_tahap2']) && $item['status_tahap2'] !== '' ? $item['status_tahap2'] : 'Belum Dinilai';
                                                $badge2ClassMap = [
                                                    'Selesai'          => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                                    'Proses Penilaian' => 'bg-purple-100 text-purple-800 border-purple-300',
                                                ];
                                                $badge2IconMap = [
                                                    'Selesai'          => 'fa-circle-check',
                                                    'Proses Penilaian' => 'fa-clock',
                                                ];
                                                $badge2Class = isset($badge2ClassMap[$st2]) ? $badge2ClassMap[$st2] : 'bg-slate-100 text-slate-600 border-slate-300';
                                                $badge2Icon  = isset($badge2IconMap[$st2]) ? $badge2IconMap[$st2] : 'fa-hourglass-start';
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-semibold rounded-full border whitespace-nowrap <?= $badge2Class; ?>">
                                                <i class="fa-solid <?= $badge2Icon; ?> mr-1 text-[10px]"></i>
                                                <?= htmlspecialchars($st2); ?>
                                            </span>
                                        </td>

                                        <!-- Action Button -->
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" id="t2BtnCol_<?= (int)$item['id_perwalian']; ?>">
                                            <?php if ($st2 === 'Selesai'): ?>
                                                <button type="button" onclick="openGradingModal(<?= (int)$item['id_perwalian']; ?>)" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-xl shadow-2xs transition cursor-pointer whitespace-nowrap">
                                                    <i class="fa-solid fa-eye mr-1.5 text-emerald-600"></i> Lihat / Edit Approval
                                                </button>
                                            <?php else: ?>
                                                <button type="button" onclick="openGradingModal(<?= (int)$item['id_perwalian']; ?>)" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 rounded-xl shadow-sm transition cursor-pointer whitespace-nowrap">
                                                    <i class="fa-solid fa-list-check mr-1.5"></i> Review & Approve
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 2. Mobile Card View (< 768px) - Zero Horizontal Scrolling -->
                <div class="block md:hidden p-3 sm:p-4 space-y-3 bg-slate-50/50" id="t2MobileCardsContainer">
                    <?php if (empty($list_perwalian)): ?>
                        <div class="py-10 text-center text-slate-400 bg-white rounded-2xl border border-slate-200/80 p-5">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400 text-lg">
                                <i class="fa-solid fa-award"></i>
                            </div>
                            <p class="font-medium text-slate-600 text-xs">Belum ada mahasiswa yang KRS Tahap 1-nya disetujui untuk dievaluasi.</p>
                            <p class="text-[11px] text-slate-400 mt-1">Mahasiswa otomatis muncul di Tahap 2 setelah KRS Tahap 1 disetujui.</p>
                        </div>
                    <?php else: ?>
                        <div id="noSearchResultCardT2" class="py-10 text-center text-slate-400 bg-white rounded-2xl border border-slate-200/80 p-5" style="display: none;">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400 text-lg">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                            <p class="font-medium text-slate-600 text-xs">Tidak ada mahasiswa yang cocok dengan kriteria pencarian.</p>
                            <button type="button" onclick="resetMultiSearchT2()" class="mt-2 text-xs font-bold text-orange-600 hover:text-orange-700 underline cursor-pointer">Reset Semua Filter</button>
                        </div>

                        <?php foreach ($list_perwalian as $item): 
                            $rowNim = strtolower(htmlspecialchars($item['nim'] ?? ''));
                            $rowNama = strtolower(htmlspecialchars($item['nama_lengkap_user'] ?: ($item['nama_mahasiswa'] ?: '')));
                            $rowProdi = strtolower(htmlspecialchars($item['prodi'] ?? ''));
                            $rowStatus = strtolower(htmlspecialchars($item['status_tahap2'] ?? 'Belum Dinilai'));
                            $rowPeriode = strtolower(htmlspecialchars(($item['tahun_ajaran'] ?? '') . ' ' . ($item['semester'] ?? '')));
                            $full_name = htmlspecialchars($item['nama_lengkap_user'] ?: ($item['nama_mahasiswa'] ?: 'Mahasiswa'));
                            $st2 = isset($item['status_tahap2']) && $item['status_tahap2'] !== '' ? $item['status_tahap2'] : 'Belum Dinilai';
                            $badge2Class = isset($badge2ClassMap[$st2]) ? $badge2ClassMap[$st2] : 'bg-slate-100 text-slate-600 border-slate-300';
                            $badge2Icon  = isset($badge2IconMap[$st2]) ? $badge2IconMap[$st2] : 'fa-hourglass-start';
                        ?>
                            <div class="t2-card-row bg-white border border-slate-200/90 rounded-2xl shadow-xs p-3.5 space-y-3"
                                 id="t2Card_<?= (int)$item['id_perwalian']; ?>"
                                 data-id="<?= (int)$item['id_perwalian']; ?>"
                                 data-total-mk="<?= (int)$item['total_mk']; ?>"
                                 data-nim="<?= $rowNim; ?>"
                                 data-nama="<?= $rowNama; ?>"
                                 data-prodi="<?= $rowProdi; ?>"
                                 data-status="<?= $rowStatus; ?>"
                                 data-periode="<?= $rowPeriode; ?>"
                            >
                                <!-- Card Header: Avatar, Name, NIM & Status -->
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-purple-100 border border-purple-200 text-purple-700 font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            <?= strtoupper(substr($full_name, 0, 1)); ?>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 text-xs sm:text-sm truncate"><?= $full_name; ?></div>
                                            <div class="text-[11px] text-slate-500 font-mono">NIM: <?= htmlspecialchars($item['nim']); ?></div>
                                        </div>
                                    </div>
                                    <span id="t2CardStatusBadge_<?= (int)$item['id_perwalian']; ?>" class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full border shrink-0 <?= $badge2Class; ?>">
                                        <i class="fa-solid <?= $badge2Icon; ?> mr-1 text-[9px]"></i>
                                        <?= htmlspecialchars($st2); ?>
                                    </span>
                                </div>

                                <!-- Card Info Details -->
                                <div class="bg-slate-50/80 p-2.5 rounded-xl border border-slate-200/70 space-y-1.5 text-xs">
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="inline-block px-2 py-0.5 font-medium bg-white text-slate-700 rounded-md border border-slate-200">
                                            <?= htmlspecialchars($item['prodi'] ?: '-'); ?>
                                        </span>
                                        <span class="font-mono text-slate-500">
                                            <?= htmlspecialchars($item['tahun_ajaran']); ?> (<?= htmlspecialchars($item['semester']); ?>)
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                                        <span class="text-slate-500">Mata Kuliah / SKS:</span>
                                        <span class="font-mono font-bold text-slate-900">
                                            <?= (int)$item['total_mk']; ?> MK <span class="font-normal text-slate-500 text-[11px]">(<?= (int)$item['total_sks']; ?> SKS)</span>
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                                        <span class="text-slate-500">Progress Approval:</span>
                                        <div class="text-right">
                                            <span class="font-mono font-bold text-slate-800 text-xs" id="t2CardDinilaiText_<?= (int)$item['id_perwalian']; ?>">
                                                <?= (int)($item['total_approved_mk'] ?? 0); ?> / <?= (int)$item['total_mk']; ?> Disetujui
                                            </span>
                                            <div class="text-[10px] space-x-1 mt-0.5" id="t2CardGradingCount_<?= (int)$item['id_perwalian']; ?>" <?= ((int)$item['total_dinilai'] > 0) ? '' : 'style="display: none;"'; ?>>
                                                <span class="text-emerald-600 font-semibold"><i class="fa-solid fa-circle-check text-[9px]"></i> <span id="t2CardLulusVal_<?= (int)$item['id_perwalian']; ?>"><?= (int)$item['total_lulus']; ?></span> Lulus</span>
                                                <span class="text-rose-600 font-semibold"><i class="fa-solid fa-circle-xmark text-[9px]"></i> <span id="t2CardTidakLulusVal_<?= (int)$item['id_perwalian']; ?>"><?= (int)$item['total_tidak_lulus']; ?></span> Tidak Lulus</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Action Button -->
                                <?php if ($st2 === 'Selesai'): ?>
                                    <button type="button" id="t2CardBtn_<?= (int)$item['id_perwalian']; ?>" onclick="openGradingModal(<?= (int)$item['id_perwalian']; ?>)" class="w-full py-2 px-3 text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-xl shadow-2xs flex items-center justify-center gap-1.5 transition active:scale-98 cursor-pointer">
                                        <i class="fa-solid fa-eye text-emerald-600 text-[11px]"></i> Lihat / Edit Approval
                                    </button>
                                <?php else: ?>
                                    <button type="button" id="t2CardBtn_<?= (int)$item['id_perwalian']; ?>" onclick="openGradingModal(<?= (int)$item['id_perwalian']; ?>)" class="w-full py-2 px-3 text-xs font-bold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 rounded-xl shadow-xs flex items-center justify-center gap-1.5 transition active:scale-98 cursor-pointer">
                                        <i class="fa-solid fa-list-check text-[11px]"></i> Review & Approve
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: PERSETUJUAN NILAI TAHAP 2 (SATU-SATU PER MK & MULTIPLE APPROVE)    -->
    <!-- ========================================================================= -->
    <div id="modalGradingTahap2" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100000] hidden flex items-center justify-center p-2.5 pb-6 sm:p-4 overflow-y-auto">
        <div class="clay-card w-full max-w-4xl max-h-[82vh] max-h-[82dvh] sm:max-h-[92vh] flex flex-col overflow-hidden bg-white shadow-2xl">
            <!-- Modal Header -->
            <div class="px-4 py-3 sm:px-6 sm:py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white flex items-center justify-between gap-2 shrink-0">
                <div class="flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/20 flex items-center justify-center text-white shrink-0">
                        <i class="fa-solid fa-award text-base sm:text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm sm:text-base leading-tight truncate" id="m2MhsNama">Nama Mahasiswa</h3>
                        <p class="text-[11px] sm:text-xs text-purple-200 truncate">
                            NIM: <span class="font-mono font-bold" id="m2MhsNim">-</span> | 
                            Prodi: <span id="m2MhsProdi">-</span> | 
                            Periode: <span id="m2MhsPeriode">-</span>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeGradingModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition shrink-0 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-3.5 sm:p-6 overflow-y-auto flex-grow space-y-3.5 sm:space-y-4">
                <!-- Info Banner & Multiple Approve Toolbar -->
                <div class="p-3 sm:p-4 rounded-xl bg-purple-50/80 border border-purple-200 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    <div>
                        <h4 class="text-xs font-bold text-purple-900 flex items-center">
                            <i class="fa-solid fa-circle-info text-purple-600 mr-1.5"></i> Persetujuan Nilai Mata Kuliah (Tahap 2)
                        </h4>
                        <p class="text-[11px] text-purple-800 mt-0.5 leading-relaxed">
                            Nilai diinput oleh mahasiswa (<strong class="text-emerald-700">A–C = Lulus 🟢</strong>, <strong class="text-rose-700">D–E = Tidak Lulus 🔴</strong>). Anda dapat melakukan <strong>Approve satu per satu</strong> per mata kuliah atau mencentang beberapa MK untuk <strong>Multiple Approve</strong>.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <button type="button" id="btnBulkApproveMk" onclick="submitBulkApproveMkTahap2()" disabled class="flex-1 sm:flex-none px-3.5 py-2 text-xs font-bold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 disabled:opacity-45 disabled:pointer-events-none rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap">
                            <i class="fa-solid fa-check-double"></i>
                            <span id="btnBulkApproveText">Approve Terpilih (0)</span>
                        </button>
                        <button type="button" id="btnApproveAllMk" onclick="submitApproveAllMkTahap2()" class="flex-1 sm:flex-none px-3.5 py-2 text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Approve Semua MK</span>
                        </button>
                    </div>
                </div>

                <!-- Approval Table -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="px-3.5 py-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-2 flex-wrap">
                        <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer select-none">
                            <input type="checkbox" id="checkAllMkTahap2" onchange="toggleSelectAllMkTahap2(this.checked)" class="w-4 h-4 rounded border-slate-300 text-purple-600 focus:ring-purple-500 cursor-pointer">
                            <span class="uppercase tracking-wider">Pilih Semua Mata Kuliah</span>
                            <span id="m2SelectedCountBadge" class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 border border-purple-200 hidden">0 dipilih</span>
                        </label>
                        <span class="text-xs font-mono font-bold text-slate-600" id="m2GradingProgressText">0 / 0 MK Disetujui</span>
                    </div>
                    <div class="block sm:hidden text-[10px] text-slate-400 text-right px-3 py-1 bg-slate-50/50 border-b border-slate-100">
                        Geser tabel ke kanan untuk melihat nilai & tombol approve 👉
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[700px] text-left text-xs">
                            <thead class="bg-slate-100/60 text-slate-600 font-bold uppercase text-[10px]">
                                <tr>
                                    <th class="py-2.5 px-3 w-10 text-center whitespace-nowrap">Pilih</th>
                                    <th class="py-2.5 px-2.5 w-10 text-center whitespace-nowrap">No</th>
                                    <th class="py-2.5 px-3 whitespace-nowrap">Kode MK</th>
                                    <th class="py-2.5 px-3">Nama Mata Kuliah</th>
                                    <th class="py-2.5 px-3 text-center w-14 whitespace-nowrap">SKS</th>
                                    <th class="py-2.5 px-3 text-center whitespace-nowrap">Nilai Huruf</th>
                                    <th class="py-2.5 px-3 text-center whitespace-nowrap">Status Kelulusan</th>
                                    <th class="py-2.5 px-3 text-center whitespace-nowrap">Aksi Approve</th>
                                </tr>
                            </thead>
                            <tbody id="m2GradingTableBody" class="divide-y divide-slate-100 text-slate-700"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="hidden sm:flex px-4 py-2.5 sm:px-6 sm:py-3 bg-slate-50 border-t border-slate-200 items-center justify-between shrink-0">
                <span class="text-[11px] text-slate-500 italic">Persetujuan tersimpan otomatis ke sistem saat tombol Approve diklik.</span>
                <button type="button" onclick="closeGradingModal()" class="px-4 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-white border border-slate-200 hover:bg-slate-100 rounded-xl transition text-center cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: IMPORT NILAI EXCEL (KOLEKTIF & SINGLE)                             -->
    <!-- ========================================================================= -->
    <div id="modalImportNilai" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[100010] hidden flex items-center justify-center p-2.5 pb-6 sm:p-4 overflow-y-auto">
        <div class="clay-card w-full max-w-3xl xl:max-w-4xl max-h-[82vh] max-h-[82dvh] sm:max-h-[92vh] flex flex-col overflow-hidden bg-white shadow-2xl rounded-2xl">
            <!-- Modal Fixed Header -->
            <div class="px-5 py-3.5 sm:px-6 sm:py-4 border-b border-slate-100 flex items-center justify-between gap-3 shrink-0 bg-white">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-file-excel text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-slate-900 truncate" id="importModalTitle">Import Rekap Nilai Mahasiswa dari Excel</h3>
                        <p class="text-xs text-slate-500 truncate" id="importModalSubtitle">Format: .xlsx, .xls, .csv</p>
                    </div>
                </div>
                <button type="button" onclick="closeImportModal()" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition shrink-0 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-4 sm:p-6 overflow-y-auto flex-grow space-y-3.5">
                <!-- Dropzone Input File -->
                <input type="file" id="fileExcelInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleExcelSelect(event)">
                <div id="excelDropzone" onclick="document.getElementById('fileExcelInput').click()" class="dropzone-box p-6 text-center select-none transition-all duration-200">
                    <span class="text-3xl text-emerald-600 block pointer-events-none"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                    <h4 class="text-xs font-bold text-slate-800 mt-2 pointer-events-none" id="dropzoneTitleText">Pilih atau Drag & Drop File Excel (.xlsx / .csv)</h4>
                    <p class="text-[11px] text-slate-500 mt-1 pointer-events-none" id="importColumnHints">
                        Header: <code>NIM</code>, <code>Kode MK</code>, <code>Nilai Huruf</code> (A, AB, B, BC, C, D, E)
                    </p>
                    <div class="mt-2 text-[10px] text-emerald-700 font-semibold pointer-events-none flex items-center justify-center gap-1">
                        <i class="fa-solid fa-arrow-pointer text-[10px]"></i> Klik untuk memilih file atau tarik & letakkan (drag & drop) file langsung di sini
                    </div>
                </div>

                <!-- Compact File Loaded Bar (Shows when file is active) -->
                <div id="excelSelectedFileBar" class="hidden p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between gap-2 text-xs">
                    <div class="flex items-center space-x-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-excel"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-slate-900 block truncate" id="excelSelectedFileName">file.xlsx</span>
                            <span class="text-[11px] text-emerald-700 block" id="excelSelectedFileInfo">File berhasil dimuat & divalidasi</span>
                        </div>
                    </div>
                    <button type="button" onclick="resetImportFile()" class="px-2.5 py-1 text-xs font-semibold text-rose-700 hover:text-rose-800 bg-white hover:bg-rose-50 border border-rose-200 rounded-lg transition shrink-0 flex items-center gap-1 cursor-pointer shadow-2xs">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i> Ganti File
                    </button>
                </div>

                <!-- Download Template Shortcut inside modal -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-2.5 bg-emerald-50/60 border border-emerald-100 rounded-xl text-xs gap-2">
                    <div class="flex items-center space-x-2 text-emerald-900">
                        <i class="fa-solid fa-circle-info text-emerald-600 shrink-0"></i>
                        <span id="importTemplateHintText">Belum punya format Excel yang sesuai?</span>
                    </div>
                    <button type="button" onclick="downloadTemplateForCurrentMode()" class="inline-flex items-center justify-center px-3 py-1.5 font-bold text-emerald-700 bg-white hover:bg-emerald-100/80 border border-emerald-300 rounded-lg shadow-2xs transition shrink-0 cursor-pointer">
                        <i class="fa-solid fa-download mr-1.5 text-emerald-600"></i>
                        <span id="btnTemplateDownloadText">Download Template</span>
                    </button>
                </div>

                <!-- Preview Container with Robust Validation Info -->
                <div id="importPreviewContainer" class="hidden space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-slate-800">Pratinjau Validasi Nilai:</span>
                            <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-md bg-slate-100 text-slate-700 border border-slate-200" id="importBadgeTotal">Total: <span id="importRowCount">0</span></span>
                            <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-md bg-emerald-100 text-emerald-800 border border-emerald-300" id="importBadgeValid">Cocok: <span id="importValidCount">0</span></span>
                            <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-md bg-rose-100 text-rose-800 border border-rose-300" id="importBadgeInvalid">Tidak Cocok: <span id="importInvalidCount">0</span></span>
                        </div>
                    </div>

                    <!-- Alert banner for validation status -->
                    <div id="importWarningBanner" class="hidden p-3 rounded-xl text-xs space-y-1">
                        <div class="font-bold flex items-center gap-1.5" id="importWarningTitle">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span id="importWarningTitleText">Peringatan Validasi File</span>
                        </div>
                        <p class="text-[11px] leading-relaxed" id="importWarningText">
                            Beberapa kode mata kuliah atau format nilai tidak sesuai dengan rencana studi mahasiswa. Baris berwarna merah tidak akan disimpan ke sistem.
                        </p>
                    </div>

                    <!-- Table Preview with clean overflow -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs bg-white">
                        <div class="max-h-60 overflow-y-auto overflow-x-auto">
                            <table class="w-full text-left text-xs min-w-[550px]">
                                <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-600 sticky top-0 border-b border-slate-200 z-10">
                                    <tr>
                                        <th class="py-2.5 px-3 w-28 whitespace-nowrap">NIM</th>
                                        <th class="py-2.5 px-3 w-28 whitespace-nowrap">Kode MK di File</th>
                                        <th class="py-2.5 px-3 min-w-[140px]">Mata Kuliah di Sistem</th>
                                        <th class="py-2.5 px-3 w-16 text-center whitespace-nowrap">Nilai</th>
                                        <th class="py-2.5 px-3 w-40 text-center whitespace-nowrap">Hasil Validasi</th>
                                    </tr>
                                </thead>
                                <tbody id="importPreviewTableBody" class="divide-y divide-slate-100"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Fixed Footer -->
            <div class="px-5 py-3 sm:px-6 sm:py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-2 shrink-0">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-200/60 rounded-xl transition cursor-pointer">
                    Batal
                </button>
                <button type="button" id="btnProcessImport" onclick="processImportNilai()" disabled class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl shadow-md transition flex items-center cursor-pointer">
                    <i class="fa-solid fa-check mr-1.5"></i> Simpan Nilai ke Sistem
                </button>
            </div>
        </div>
    </div>


    <!-- Script Tahap 2 -->
    <script>
        let currentPerwalian2 = null;
        let parsedImportRows = [];
        let importTargetIdPerwalian = null;

        async function openGradingModal(idPerwalian) {
            Swal.fire({
                title: 'Memuat Form Penilaian...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const res = await fetch(`<?= site_url('dosen/wali/perwalian_detail_ajax/'); ?>${idPerwalian}`);
                const json = await res.json();
                Swal.close();

                if (!json.status || !json.data) {
                    Swal.fire('Gagal', json.message || 'Tidak dapat memuat detail evaluasi.', 'error');
                    return;
                }

                currentPerwalian2 = json.data;
                const p = currentPerwalian2.perwalian;
                const mkList = currentPerwalian2.mata_kuliah || [];

                document.getElementById('m2MhsNama').innerText = p.nama_lengkap || p.nama_mahasiswa || 'Mahasiswa';
                document.getElementById('m2MhsNim').innerText = p.nim || '-';
                document.getElementById('m2MhsProdi').innerText = p.prodi || p.user_prodi || '-';
                document.getElementById('m2MhsPeriode').innerText = `${p.tahun_ajaran} (${p.semester})`;

                renderGradingModalTable(mkList);
                document.getElementById('modalGradingTahap2').classList.remove('hidden');
            } catch (err) {
                Swal.close();
                Swal.fire('Error', 'Terjadi kesalahan saat memuat data evaluasi.', 'error');
            }
        }

        function closeGradingModal() {
            document.getElementById('modalGradingTahap2').classList.add('hidden');
            currentPerwalian2 = null;
        }

        function renderGradingModalTable(mkList) {
            const tbody = document.getElementById('m2GradingTableBody');
            tbody.innerHTML = '';

            const checkAllEl = document.getElementById('checkAllMkTahap2');
            if (checkAllEl) checkAllEl.checked = false;

            let approvedCount = 0;
            if (!mkList || mkList.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">
                            <i class="fa-solid fa-book-open text-2xl text-slate-300 mb-2 block"></i>
                            <p class="font-medium text-slate-600">Belum ada mata kuliah yang dikontrak untuk semester ini.</p>
                            <p class="text-xs text-slate-400 mt-1">Mata kuliah ditentukan dari hasil pengajuan KRS di Tahap 1 atau setup oleh Admin Prodi.</p>
                        </td>
                    </tr>
                `;
            } else {
                // Kelompokkan mata kuliah sesuai dengan semester
                const groupedBySemester = {};
                mkList.forEach(mk => {
                    const sem = parseInt(mk.semester_mk || mk.semester || 1);
                    if (!groupedBySemester[sem]) {
                        groupedBySemester[sem] = [];
                    }
                    groupedBySemester[sem].push(mk);
                });

                const sortedSemesters = Object.keys(groupedBySemester).sort((a, b) => parseInt(a) - parseInt(b));

                sortedSemesters.forEach(sem => {
                    const mks = groupedBySemester[sem];
                    const totalSksSem = mks.reduce((acc, curr) => acc + parseInt(curr.sks || 0), 0);

                    // Header Pemisah Semester
                    tbody.innerHTML += `
                        <tr class="bg-gradient-to-r from-purple-50/90 via-indigo-50/60 to-purple-50/90 border-y border-purple-200/80">
                            <td colspan="8" class="py-2 px-3.5">
                                <div class="flex items-center justify-between text-xs font-bold text-purple-950">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-graduation-cap text-purple-600 text-sm"></i>
                                        <span class="tracking-wide uppercase text-[11px]">Semester ${sem}</span>
                                    </span>
                                    <span class="text-[11px] font-mono font-bold px-2.5 py-0.5 bg-white text-purple-800 rounded-md border border-purple-200 shadow-2xs">
                                        Subtotal: ${totalSksSem} SKS (${mks.length} MK)
                                    </span>
                                </div>
                            </td>
                        </tr>
                    `;

                    mks.forEach((mk, idx) => {
                        const grade = (mk.nilai_huruf || '').toUpperCase().trim();
                        const isApproved = parseInt(mk.is_approved_wali || 0) === 1;
                        if (isApproved) approvedCount++;

                        const isLulusGrade = ['A', 'AB', 'B', 'BC', 'C'].includes(grade);
                        const isTidakLulusGrade = ['D', 'E'].includes(grade);
                        const status = isLulusGrade ? 'Lulus' : (isTidakLulusGrade ? 'Tidak Lulus' : (mk.status_kelulusan || 'Belum Ditentukan'));
                        const isLulus = (status === 'Lulus');
                        const isTidakLulus = (status === 'Tidak Lulus');

                        const gradeBadgeHtml = grade
                            ? `<span class="inline-flex items-center justify-center min-w-[2.25rem] px-2.5 py-0.5 text-xs font-mono font-extrabold rounded-lg border whitespace-nowrap ${isLulus ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'}">${escapeHtml(grade)}</span>`
                            : `<span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-medium text-slate-400 bg-slate-100 rounded-md border border-slate-200 whitespace-nowrap">Belum Diinput</span>`;

                        const statusBadgeHtml = isLulus 
                            ? `<span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 whitespace-nowrap"><i class="fa-solid fa-circle-check mr-1 text-emerald-600"></i> Lulus</span>`
                            : (isTidakLulus 
                                ? `<span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-300 whitespace-nowrap"><i class="fa-solid fa-circle-xmark mr-1 text-rose-600"></i> Tidak Lulus</span>`
                                : `<span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-medium rounded-full bg-slate-100 text-slate-500 border border-slate-200 whitespace-nowrap"><i class="fa-solid fa-minus mr-1 text-slate-400"></i> Belum Ada Nilai</span>`);

                        const approveActionHtml = renderMkApproveActionHtml(mk.id, isApproved);

                        tbody.innerHTML += `
                            <tr class="hover:bg-slate-50/60 transition ${isApproved ? 'bg-emerald-50/20' : ''}" id="m2Row_${mk.id}">
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <input type="checkbox" class="mk-approve-cb w-4 h-4 rounded border-slate-300 text-purple-600 focus:ring-purple-500 cursor-pointer" value="${mk.id}" data-approved="${isApproved ? '1' : '0'}" onchange="onMkCheckboxChange()">
                                </td>
                                <td class="py-2.5 px-2.5 text-center font-mono text-slate-400 whitespace-nowrap">${idx + 1}</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-slate-900 whitespace-nowrap">${escapeHtml(mk.kode_mk)}</td>
                                <td class="py-2.5 px-3 font-medium text-slate-800">${escapeHtml(mk.nama_mk)}</td>
                                <td class="py-2.5 px-3 text-center font-mono font-semibold whitespace-nowrap">${mk.sks}</td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">${gradeBadgeHtml}</td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap" id="m2StatusCol_${mk.id}">
                                    ${statusBadgeHtml}
                                </td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap" id="m2ApproveCol_${mk.id}">
                                    ${approveActionHtml}
                                </td>
                            </tr>
                        `;
                    });
                });
            }

            const progEl = document.getElementById('m2GradingProgressText');
            if (progEl) {
                progEl.innerText = `${approvedCount} / ${mkList.length} MK Disetujui`;
            }
            onMkCheckboxChange();
        }

        function renderMkApproveActionHtml(idDetail, isApproved) {
            if (isApproved) {
                return `
                    <div class="inline-flex items-center justify-center gap-1.5">
                        <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <i class="fa-solid fa-circle-check mr-1 text-emerald-600"></i> Approved
                        </span>
                        <button type="button" onclick="approveSingleMkTahap2(${idDetail}, 0)" class="px-2 py-1 text-[10px] font-semibold text-slate-500 hover:text-rose-700 bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 rounded-lg transition cursor-pointer" title="Batalkan Approval MK Ini">
                            Batal
                        </button>
                    </div>
                `;
            }
            return `
                <button type="button" onclick="approveSingleMkTahap2(${idDetail}, 1)" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 rounded-lg shadow-2xs transition cursor-pointer whitespace-nowrap">
                    <i class="fa-solid fa-check mr-1.5"></i> Approve
                </button>
            `;
        }

        function toggleSelectAllMkTahap2(checked) {
            const cbs = document.querySelectorAll('#m2GradingTableBody .mk-approve-cb');
            cbs.forEach(cb => {
                cb.checked = checked;
            });
            onMkCheckboxChange();
        }

        function onMkCheckboxChange() {
            const allCbs = Array.from(document.querySelectorAll('#m2GradingTableBody .mk-approve-cb'));
            const checkedCbs = allCbs.filter(cb => cb.checked);
            const count = checkedCbs.length;

            const checkAllEl = document.getElementById('checkAllMkTahap2');
            if (checkAllEl) {
                checkAllEl.checked = (allCbs.length > 0 && count === allCbs.length);
                checkAllEl.indeterminate = (count > 0 && count < allCbs.length);
            }

            const badge = document.getElementById('m2SelectedCountBadge');
            if (badge) {
                if (count > 0) {
                    badge.innerText = `${count} dipilih`;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            const btnBulk = document.getElementById('btnBulkApproveMk');
            const btnBulkText = document.getElementById('btnBulkApproveText');
            if (btnBulkText) {
                btnBulkText.innerText = `Approve Terpilih (${count})`;
            }
            if (btnBulk) {
                if (count > 0) {
                    btnBulk.removeAttribute('disabled');
                } else {
                    btnBulk.setAttribute('disabled', 'true');
                }
            }
        }

        async function approveSingleMkTahap2(idDetail, approveVal) {
            if (!currentPerwalian2 || !currentPerwalian2.perwalian) return;
            const idPerwalian = currentPerwalian2.perwalian.id_perwalian;
            await executeApproveMkTahap2(idPerwalian, [idDetail], approveVal);
        }

        async function submitBulkApproveMkTahap2() {
            if (!currentPerwalian2 || !currentPerwalian2.perwalian) return;
            const idPerwalian = currentPerwalian2.perwalian.id_perwalian;
            const checkedIds = Array.from(document.querySelectorAll('#m2GradingTableBody .mk-approve-cb:checked')).map(cb => parseInt(cb.value));

            if (checkedIds.length === 0) {
                Swal.fire('Pilih Mata Kuliah', 'Silakan centang minimal satu mata kuliah yang ingin di-approve.', 'info');
                return;
            }

            await executeApproveMkTahap2(idPerwalian, checkedIds, 1);
        }

        async function submitApproveAllMkTahap2() {
            if (!currentPerwalian2 || !currentPerwalian2.perwalian) return;
            const idPerwalian = currentPerwalian2.perwalian.id_perwalian;
            const allCbs = document.querySelectorAll('#m2GradingTableBody .mk-approve-cb');
            if (allCbs.length === 0) return;

            const conf = await Swal.fire({
                title: 'Approve Semua Mata Kuliah?',
                text: `Setujui seluruh ${allCbs.length} mata kuliah mahasiswa ini sekaligus?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                confirmButtonText: 'Ya, Approve Semua!',
                cancelButtonText: 'Batal'
            });

            if (!conf.isConfirmed) return;
            await executeApproveMkTahap2(idPerwalian, 'all', 1);
        }

        async function executeApproveMkTahap2(idPerwalian, detailIds, approveVal) {
            const fd = new FormData();
            fd.append('id_perwalian', idPerwalian);
            fd.append('approve', approveVal);
            fd.append('detail_ids', detailIds === 'all' ? 'all' : JSON.stringify(detailIds));

            try {
                const res = await fetch(`<?= site_url('dosen/wali/perwalian_approve_mk_tahap2'); ?>`, {
                    method: 'POST',
                    body: fd
                });
                const json = await res.json();

                if (json.status && json.data) {
                    const approvedSet = new Set((json.data.approved_ids || []).map(x => parseInt(x)));

                    // Update state di currentPerwalian2 & tampilan baris modal
                    if (currentPerwalian2 && Array.isArray(currentPerwalian2.mata_kuliah)) {
                        currentPerwalian2.mata_kuliah.forEach(mk => {
                            const id = parseInt(mk.id);
                            const isApp = approvedSet.has(id);
                            mk.is_approved_wali = isApp ? 1 : 0;

                            const col = document.getElementById(`m2ApproveCol_${id}`);
                            if (col) col.innerHTML = renderMkApproveActionHtml(id, isApp);

                            const row = document.getElementById(`m2Row_${id}`);
                            if (row) {
                                if (isApp) row.classList.add('bg-emerald-50/20');
                                else row.classList.remove('bg-emerald-50/20');
                            }

                            const cb = row ? row.querySelector('.mk-approve-cb') : null;
                            if (cb) {
                                cb.setAttribute('data-approved', isApp ? '1' : '0');
                                if (approveVal === 1 && (detailIds === 'all' || (Array.isArray(detailIds) && detailIds.includes(id)))) {
                                    cb.checked = false;
                                }
                            }
                        });
                    }

                    const progEl = document.getElementById('m2GradingProgressText');
                    if (progEl) {
                        progEl.innerText = `${json.data.total_approved_mk} / ${json.data.total_mk} MK Disetujui`;
                    }
                    onMkCheckboxChange();

                    // Update tabel utama & statistik di belakang modal secara real-time
                    updatePerwalianTahap2Counters(
                        idPerwalian,
                        json.data.total_lulus,
                        json.data.total_tidak_lulus,
                        json.data.status_tahap2,
                        json.stats,
                        json.data.total_approved_mk
                    );

                    showToast(json.message || 'Status approval diperbarui', 'success');
                } else {
                    Swal.fire('Gagal', json.message || 'Gagal memperbarui approval mata kuliah.', 'error');
                }
            } catch (err) {
                showToast('Gagal koneksi server', 'error');
            }
        }

        // --- IMPORT EXCEL HANDLERS ---
        function openImportModalKolektif() {
            importTargetIdPerwalian = null;
            document.getElementById('importModalTitle').innerText = 'Import Nilai Global (Semua Mahasiswa Bimbingan)';
            document.getElementById('importModalSubtitle').innerText = 'Upload 1 file Excel untuk memperbarui nilai seluruh mahasiswa bimbingan sekaligus';
            document.getElementById('importColumnHints').innerHTML = 'Header kolom wajib: <code>NIM</code>, <code>Kode MK</code>, <code>Nilai Huruf</code> (A, AB, B, BC, C, D, E)';
            document.getElementById('importTemplateHintText').innerText = 'Format Excel Nilai Global (Semua Mahasiswa):';
            document.getElementById('btnTemplateDownloadText').innerText = 'Unduh Template Excel Global';
            resetImportFile();
            document.getElementById('modalImportNilai').classList.remove('hidden');
        }

        function openImportModalSingle() {
            if (!currentPerwalian2) return;
            importTargetIdPerwalian = currentPerwalian2.perwalian.id_perwalian;
            const mhsNama = currentPerwalian2.perwalian.nama_lengkap || currentPerwalian2.perwalian.nama_mahasiswa;
            document.getElementById('importModalTitle').innerText = `Import Nilai Khusus: ${mhsNama}`;
            document.getElementById('importModalSubtitle').innerText = `NIM: ${currentPerwalian2.perwalian.nim} (Hanya untuk mata kuliah mahasiswa ini)`;
            document.getElementById('importColumnHints').innerHTML = 'Header kolom wajib: <code>Kode MK</code>, <code>Nilai Huruf</code> (A, AB, B, BC, C, D, E)';
            document.getElementById('importTemplateHintText').innerText = 'Format Excel khusus mahasiswa ini:';
            document.getElementById('btnTemplateDownloadText').innerText = 'Unduh Template Mahasiswa Ini';
            resetImportFile();
            document.getElementById('modalImportNilai').classList.remove('hidden');
        }

        function closeImportModal() {
            document.getElementById('modalImportNilai').classList.add('hidden');
            resetImportFile();
        }

        function downloadTemplateForCurrentMode() {
            if (importTargetIdPerwalian) {
                downloadTemplateExcelSingle();
            } else {
                downloadTemplateExcelKolektif();
            }
        }

        function downloadTemplateExcelSingle() {
            if (!currentPerwalian2 || !currentPerwalian2.details) {
                downloadTemplateExcelKolektif();
                return;
            }
            const sampleData = currentPerwalian2.details.map(d => ({
                'Kode MK': d.kode_mk,
                'Mata Kuliah': d.nama_mk,
                'Nilai Huruf': d.nilai_huruf || ''
            }));
            const ws = XLSX.utils.json_to_sheet(sampleData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Nilai Mahasiswa');
            const safeNim = currentPerwalian2.perwalian.nim || 'mahasiswa';
            XLSX.writeFile(wb, `template_nilai_${safeNim}.xlsx`);
        }

        function resetImportFile() {
            const input = document.getElementById('fileExcelInput');
            if (input) input.value = '';

            const preview = document.getElementById('importPreviewContainer');
            if (preview) preview.classList.add('hidden');

            const dropzone = document.getElementById('excelDropzone');
            if (dropzone) dropzone.classList.remove('hidden');

            const fileBar = document.getElementById('excelSelectedFileBar');
            if (fileBar) fileBar.classList.add('hidden');

            const btn = document.getElementById('btnProcessImport');
            if (btn) btn.setAttribute('disabled', 'true');

            parsedImportRows = [];
        }

        function handleExcelSelect(e) {
            const file = (e.target.files && e.target.files.length > 0) ? e.target.files[0] : null;
            if (file) {
                processExcelFile(file);
            }
        }

        function processExcelFile(file) {
            if (!file) return;

            // Validasi ekstensi file
            const fileName = file.name.toLowerCase();
            const validExts = ['.xlsx', '.xls', '.csv'];
            const isValid = validExts.some(ext => fileName.endsWith(ext));
            if (!isValid) {
                Swal.fire('Format File Tidak Sesuai', 'Silakan unggah file dengan format .xlsx, .xls, atau .csv.', 'warning');
                resetImportFile();
                return;
            }

            const selNameEl = document.getElementById('excelSelectedFileName');
            if (selNameEl) selNameEl.innerText = file.name;

            const reader = new FileReader();
            reader.onload = function(evt) {
                try {
                    const data = new Uint8Array(evt.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    const json = XLSX.utils.sheet_to_json(firstSheet);

                    parsedImportRows = json.map(r => ({
                        nim: String(r['NIM'] || r['nim'] || r['Nim'] || '').trim(),
                        kode_mk: String(r['Kode MK'] || r['kode_mk'] || r['Kode'] || r['KODE MK'] || '').trim().toUpperCase(),
                        nilai_huruf: String(r['Nilai Huruf'] || r['nilai_huruf'] || r['Nilai'] || r['NILAI'] || '').trim().toUpperCase(),
                    })).filter(r => r.kode_mk !== '' && r.nilai_huruf !== '');

                    if (parsedImportRows.length === 0) {
                        Swal.fire('File Kosong', 'Tidak ditemukan baris nilai yang valid pada file.', 'warning');
                        resetImportFile();
                        return;
                    }

                    renderImportPreview(parsedImportRows);
                } catch (err) {
                    Swal.fire('Gagal Baca File', 'Pastikan format file adalah Excel atau CSV valid.', 'error');
                    resetImportFile();
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function initExcelDropzone() {
            const dropzone = document.getElementById('excelDropzone');
            if (!dropzone) return;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.add('drag-active');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.remove('drag-active');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt ? dt.files : null;
                if (files && files.length > 0) {
                    processExcelFile(files[0]);
                }
            }, false);
        }

        function renderImportPreview(rows) {
            const rowCountEl = document.getElementById('importRowCount');
            if (rowCountEl) rowCountEl.innerText = rows.length;

            const dropzone = document.getElementById('excelDropzone');
            if (dropzone) dropzone.classList.add('hidden');

            const fileBar = document.getElementById('excelSelectedFileBar');
            if (fileBar) fileBar.classList.remove('hidden');
            const tbody = document.getElementById('importPreviewTableBody');
            tbody.innerHTML = '';

            const VALID_GRADES = ['A', 'AB', 'B', 'BC', 'C', 'D', 'E'];
            let validCount = 0;
            let invalidCount = 0;

            // 1. Buat peta mata kuliah terdaftar jika dalam mode single mahasiswa
            let mkMap = null;
            let targetNim = '';
            if (importTargetIdPerwalian && currentPerwalian2) {
                mkMap = {};
                const list = currentPerwalian2.mata_kuliah || currentPerwalian2.details || [];
                list.forEach(m => {
                    const code = String(m.kode_mk || '').trim().toUpperCase();
                    if (code) {
                        mkMap[code] = m.nama_mk || 'Mata Kuliah Terdaftar';
                    }
                });
                targetNim = String((currentPerwalian2.perwalian ? currentPerwalian2.perwalian.nim : '') || '').trim();
            }

            rows.slice(0, 100).forEach(r => {
                const mkCode = String(r.kode_mk || '').trim().toUpperCase();
                const grade = String(r.nilai_huruf || '').trim().toUpperCase();
                const rowNim = String(r.nim || '').trim();

                let isRowValid = true;
                let errorReason = '';
                let namaMkDisplay = '-';

                // Validasi Nilai Huruf
                if (!VALID_GRADES.includes(grade)) {
                    isRowValid = false;
                    errorReason = 'Nilai tidak sah (harus A, AB, B, BC, C, D, E)';
                }

                // Validasi Kode MK & NIM pada mode Single Mahasiswa
                if (mkMap !== null) {
                    if (targetNim && rowNim && rowNim !== targetNim) {
                        isRowValid = false;
                        errorReason = `NIM tidak sesuai (Harus: ${targetNim})`;
                    } else if (!mkMap[mkCode]) {
                        isRowValid = false;
                        errorReason = 'Kode MK TIDAK COCOK dengan rencana studi mahasiswa';
                        namaMkDisplay = '<span class="text-rose-600 font-bold text-[11px] flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> Tidak terdaftar di KRS</span>';
                    } else {
                        namaMkDisplay = `<span class="text-slate-900 font-medium">${escapeHtml(mkMap[mkCode])}</span>`;
                    }
                } else {
                    namaMkDisplay = '<span class="text-slate-500 italic">Mata Kuliah Bimbingan</span>';
                }

                // Tandai pada objek baris
                r._isValid = isRowValid;

                let rowClass = 'hover:bg-slate-50/60';
                let statusBadge = '';

                if (isRowValid) {
                    validCount++;
                    const isLulus = ['A', 'AB', 'B', 'BC', 'C'].includes(grade);
                    statusBadge = isLulus 
                        ? `<span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 whitespace-nowrap"><i class="fa-solid fa-check mr-1 text-[9px]"></i> Valid (Lulus)</span>`
                        : `<span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-300 whitespace-nowrap"><i class="fa-solid fa-xmark mr-1 text-[9px]"></i> Valid (Tidak Lulus)</span>`;
                } else {
                    invalidCount++;
                    rowClass = 'bg-rose-50/80 hover:bg-rose-100/60 border-l-4 border-l-rose-500';
                    let shortMsg = 'Tidak Cocok';
                    if (errorReason.includes('NIM')) shortMsg = 'NIM Tidak Cocok';
                    else if (errorReason.includes('Kode MK')) shortMsg = 'Kode MK Tidak Cocok';
                    else if (errorReason.includes('Nilai')) shortMsg = 'Format Nilai Salah';

                    statusBadge = `<span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-200 text-rose-900 border border-rose-300 gap-1 whitespace-nowrap" title="${escapeHtml(errorReason)}"><i class="fa-solid fa-circle-xmark text-rose-600"></i> ${escapeHtml(shortMsg)}</span>`;
                }

                tbody.innerHTML += `
                    <tr class="${rowClass}">
                        <td class="py-2 px-3 font-mono font-bold ${rowNim && targetNim && rowNim !== targetNim ? 'text-rose-700 bg-rose-100/50' : 'text-slate-600'}">${escapeHtml(rowNim || '-')}</td>
                        <td class="py-2 px-3 font-mono font-black ${!isRowValid && errorReason.includes('Kode MK') ? 'text-rose-700 bg-rose-100/70 font-mono underline decoration-rose-400' : 'text-slate-900'}">${escapeHtml(mkCode || '-')}</td>
                        <td class="py-2 px-3 text-xs">${namaMkDisplay}</td>
                        <td class="py-2 px-3 text-center font-black font-mono ${!VALID_GRADES.includes(grade) ? 'text-rose-600 bg-rose-100' : 'text-slate-900'}">${escapeHtml(grade || '-')}</td>
                        <td class="py-2 px-3 text-center whitespace-nowrap">${statusBadge}</td>
                    </tr>
                `;
            });

            // Update badge counters
            document.getElementById('importValidCount').innerText = validCount;
            document.getElementById('importInvalidCount').innerText = invalidCount;

            // Update warning banner
            const banner = document.getElementById('importWarningBanner');
            const bannerTitle = document.getElementById('importWarningTitle');
            const bannerTitleText = document.getElementById('importWarningTitleText');
            const bannerText = document.getElementById('importWarningText');
            const btnProcess = document.getElementById('btnProcessImport');

            if (invalidCount > 0 && validCount === 0) {
                // Semua baris tidak cocok / gagal
                banner.className = 'p-3 rounded-xl text-xs space-y-1 bg-rose-100 border border-rose-300 text-rose-900';
                bannerTitle.className = 'font-bold flex items-center gap-1.5 text-rose-800';
                bannerTitleText.innerText = 'Semua Data Tidak Cocok!';
                bannerText.innerHTML = 'Seluruh kode mata kuliah pada file Excel Anda <strong>tidak terdaftar pada rencana studi mahasiswa ini</strong>. Silakan periksa kembali atau gunakan tombol <strong>"Unduh Template Mahasiswa Ini"</strong> di atas.';
                banner.classList.remove('hidden');

                btnProcess.setAttribute('disabled', 'true');
                btnProcess.innerHTML = `<i class="fa-solid fa-ban mr-1.5"></i> Tidak Ada Data Cocok`;
            } else if (invalidCount > 0 && validCount > 0) {
                // Sebagian cocok, sebagian tidak
                banner.className = 'p-3 rounded-xl text-xs space-y-1 bg-amber-50 border border-amber-300 text-amber-900';
                bannerTitle.className = 'font-bold flex items-center gap-1.5 text-amber-800';
                bannerTitleText.innerText = 'Sebagian Kode MK Tidak Cocok';
                bannerText.innerHTML = `Ditemukan <strong>${invalidCount} baris</strong> dengan Kode MK / Nilai tidak cocok. Hanya <strong>${validCount} mata kuliah yang valid</strong> yang akan disimpan ke sistem. Baris yang tidak cocok akan otomatis dilewati.`;
                banner.classList.remove('hidden');

                btnProcess.removeAttribute('disabled');
                btnProcess.innerHTML = `<i class="fa-solid fa-check mr-1.5"></i> Simpan ${validCount} Nilai Valid`;
            } else {
                // Semua valid
                banner.classList.add('hidden');
                btnProcess.removeAttribute('disabled');
                btnProcess.innerHTML = `<i class="fa-solid fa-check mr-1.5"></i> Simpan Nilai ke Sistem (${validCount} MK)`;
            }

            document.getElementById('importPreviewContainer').classList.remove('hidden');
        }

        async function processImportNilai() {
            if (parsedImportRows.length === 0) return;

            // Filter hanya baris yang valid
            const validRowsToSubmit = parsedImportRows.filter(r => r._isValid !== false);
            const invalidRowsCount = parsedImportRows.length - validRowsToSubmit.length;

            if (validRowsToSubmit.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak Ada Data Valid',
                    text: 'Tidak ada kode mata kuliah yang cocok dengan rencana studi mahasiswa. Silakan gunakan template resmi.'
                });
                return;
            }

            // Jika ada baris yang tidak cocok, beri konfirmasi dulu
            if (invalidRowsCount > 0) {
                const conf = await Swal.fire({
                    icon: 'warning',
                    title: 'Konfirmasi Penyimpanan Nilai',
                    html: `Terdapat <b>${invalidRowsCount} baris mata kuliah tidak cocok</b> yang akan dilewati.<br><br>Hanya <b>${validRowsToSubmit.length} mata kuliah valid</b> yang akan disimpan ke sistem. Lanjutkan?`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan yang Valid',
                    confirmButtonColor: '#059669',
                    cancelButtonText: 'Batal & Perbaiki File'
                });
                if (!conf.isConfirmed) return;
            }

            Swal.fire({ title: 'Menyimpan Nilai...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

            try {
                const res = await fetch(`<?= site_url('dosen/wali/perwalian_import_nilai'); ?>`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        rows: validRowsToSubmit,
                        id_perwalian: importTargetIdPerwalian
                    })
                });
                const json = await res.json();
                Swal.close();

                if (json.status) {
                    closeImportModal();
                    if (importTargetIdPerwalian) {
                        openGradingModal(importTargetIdPerwalian);
                    }
                    if (json.list) {
                        updateAllPerwalianTahap2UI(json.list, json.stats);
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses Import!',
                        text: json.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire('Gagal Import', json.message, 'error');
                }
            } catch (err) {
                Swal.close();
                Swal.fire('Error', 'Terjadi kesalahan server saat memproses import.', 'error');
            }
        }

        function downloadTemplateExcelKolektif() {
            const sampleData = [
                { 'NIM': '1301210001', 'Kode MK': 'CSG2A3', 'Nilai Huruf': 'A' },
                { 'NIM': '1301210001', 'Kode MK': 'CSG2B3', 'Nilai Huruf': 'AB' },
                { 'NIM': '1301210002', 'Kode MK': 'CSG2A3', 'Nilai Huruf': 'B' },
                { 'NIM': '1301210002', 'Kode MK': 'CSG2C3', 'Nilai Huruf': 'C' },
            ];
            const ws = XLSX.utils.json_to_sheet(sampleData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Template Nilai');
            XLSX.writeFile(wb, 'template_nilai_perwalian_dosen.xlsx');
        }

        // ===== Auto-sync data tanpa refresh (polling ringan) =====
        let __syncSig = null;
        let __syncBusy = false;

        async function syncListFromServer() {
            if (__syncBusy || document.hidden) return;
            __syncBusy = true;
            try {
                const res = await fetch(window.location.href, {
                    cache: 'no-store',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) return;
                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');

                const newBody = doc.querySelector('table tbody.divide-y');
                const newCards = doc.getElementById('t2MobileCardsContainer');
                if (!newBody || !newCards) return; // mis. sesi habis -> halaman login

                const sig = newBody.innerHTML + '||' + newCards.innerHTML;
                if (sig === __syncSig) return;
                __syncSig = sig;

                const curBody = document.querySelector('table tbody.divide-y');
                const curCards = document.getElementById('t2MobileCardsContainer');
                if (curBody) curBody.innerHTML = newBody.innerHTML;
                if (curCards) curCards.innerHTML = newCards.innerHTML;

                ['statT2TotalMhs', 'statT2Belum', 'statT2Proses', 'statT2Selesai'].forEach(id => {
                    const n = doc.getElementById(id), c = document.getElementById(id);
                    if (n && c) c.innerHTML = n.innerHTML;
                });

                const nt = doc.getElementById('totalRowCountT2'), ct = document.getElementById('totalRowCountT2');
                if (nt && ct) ct.textContent = nt.textContent.trim();
                const nb = doc.querySelector('[data-sync-count]'), cb = document.querySelector('[data-sync-count]');
                if (nb && cb) cb.textContent = nb.textContent.trim();

                // terapkan ulang filter pencarian yang sedang aktif
                if (typeof executeMultiSearchT2 === 'function') executeMultiSearchT2();
            } catch (e) {
                /* abaikan error jaringan sesaat, coba lagi di siklus berikutnya */
            } finally {
                __syncBusy = false;
            }
        }

        setInterval(syncListFromServer, 4000);
        document.addEventListener('visibilitychange', () => { if (!document.hidden) syncListFromServer(); });

        function escapeHtml(text) {
            if (!text) return '';
            return String(text).replace(/[&<>"']/g, function(m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
            });
        }

        function showToast(message, icon = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            Toast.fire({ icon: icon, title: message });
        }

        // =========================================================================
        // MULTI-SEARCH CRITERIA ENGINE (Tahap 2)
        // Hanya menyaring data saat tombol Cari diklik atau tombol Enter ditekan!
        // =========================================================================
        let extraRowCounterT2 = 0;

        function toggleDropdownT2(dropdownId, e) {
            if (e) e.stopPropagation();
            const el = document.getElementById(dropdownId);
            if (!el) return;
            const menu = el.querySelector('.custom-dropdown-menu');
            if (menu) menu.classList.toggle('hidden');
        }

        // Close dropdown when clicked outside
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => {
                if (!m.contains(e.target) && !m.parentElement.contains(e.target)) {
                    m.classList.add('hidden');
                }
            });
            const popover = document.getElementById('extraCardT2');
            const toggleBtn = document.querySelector('.btn-standalone-add');
            if (popover && !popover.contains(e.target) && (!toggleBtn || !toggleBtn.contains(e.target))) {
                popover.style.display = 'none';
            }
        });

        function selectCategoryT2(scope, cat, label, icon) {
            if (scope === 'main') {
                document.getElementById('mainCatT2').value = cat;
                document.getElementById('labelMainCatT2').innerText = icon;
                const inp = document.getElementById('mainInputT2');
                inp.placeholder = getPlaceholderT2(cat);
                inp.focus();
                const menu = document.querySelector('#dropdownMainCatT2 .custom-dropdown-menu');
                if (menu) menu.classList.add('hidden');
            }
        }

        function getPlaceholderT2(cat) {
            switch(cat) {
                case 'nama': return 'Cari nama mahasiswa...';
                case 'nim': return 'Cari NIM mahasiswa...';
                case 'prodi': return 'Cari program studi...';
                case 'status': return 'Cari status (Belum Dinilai, Proses Penilaian, Selesai)...';
                default: return 'Ketik kata kunci lalu tekan Enter atau klik Cari...';
            }
        }

        function getCategoryEmojiT2(cat) {
            switch(cat) {
                case 'nama': return '🏷️';
                case 'nim': return '🆔';
                case 'prodi': return '🎓';
                case 'status': return '⚡';
                default: return '🔍';
            }
        }

        function toggleExtraPopoverT2(e) {
            if (e) e.stopPropagation();
            const card = document.getElementById('extraCardT2');
            if (card) {
                card.style.display = (card.style.display === 'block') ? 'none' : 'block';
            }
        }

        function closeExtraPopoverT2() {
            const card = document.getElementById('extraCardT2');
            if (card) card.style.display = 'none';
        }

        function updateFilterBadgeT2() {
            const container = document.getElementById('extraRowsContainerT2');
            const extraCount = container ? container.querySelectorAll('.extra-filter-row').length : 0;
            const total = 1 + extraCount;
            const badge = document.getElementById('filterBadgeT2');
            if (badge) badge.innerText = `${total}/4`;
            const countText = document.getElementById('extraCountTextT2');
            if (countText) countText.innerText = `${total}/4 kriteria aktif (Maks. 4)`;
        }

        function addExtraRowT2(defaultCat = 'nama') {
            const container = document.getElementById('extraRowsContainerT2');
            if (!container) return;

            const currentRows = container.querySelectorAll('.extra-filter-row').length;
            if (currentRows >= 3) {
                Swal.fire({
                    icon: 'info',
                    title: 'Maksimal 4 Kriteria',
                    text: 'Anda telah mencapai batas maksimal 4 kriteria pencarian sekaligus.',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            extraRowCounterT2++;
            const rowId = extraRowCounterT2;

            const row = document.createElement('div');
            row.className = 'extra-filter-row';
            row.id = `extraRowT2_${rowId}`;
            row.innerHTML = `
                <div class="unified-search-pill">
                    <div class="relative custom-dropdown-container" id="dropdownExtraCatT2_${rowId}">
                        <input type="hidden" class="extra-cat-val" id="extraCatValT2_${rowId}" value="${defaultCat}">
                        <button type="button" onclick="toggleDropdownT2('dropdownExtraCatT2_${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none">
                            <span id="labelExtraCatT2_${rowId}" class="text-sm sm:text-base leading-none block">${getCategoryEmojiT2(defaultCat)}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200"></i>
                        </button>
                        <div class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            <div onclick="selectExtraCategoryT2(${rowId}, 'query', '🔍 Semua Kriteria', '🔍')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🔍 Semua Kriteria</span></div>
                            <div onclick="selectExtraCategoryT2(${rowId}, 'nama', '🏷️ Nama Mahasiswa', '🏷️')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🏷️ Nama Mahasiswa</span></div>
                            <div onclick="selectExtraCategoryT2(${rowId}, 'nim', '🆔 NIM Mahasiswa', '🆔')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🆔 NIM Mahasiswa</span></div>
                            <div onclick="selectExtraCategoryT2(${rowId}, 'prodi', '🎓 Program Studi', '🎓')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🎓 Program Studi</span></div>
                            <div onclick="selectExtraCategoryT2(${rowId}, 'status', '⚡ Status Penilaian', '⚡')" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>⚡ Status Penilaian</span></div>
                        </div>
                    </div>

                    <div class="unified-divider"></div>

                    <div class="flex-1 flex items-center min-w-0">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                        <input type="text" id="extraInputT2_${rowId}" class="extra-input-val w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); executeMultiSearchT2(); }" placeholder="${getPlaceholderT2(defaultCat)}">
                    </div>
                </div>

                <button type="button" onclick="removeExtraRowT2(${rowId})" class="btn-remove-row" title="Hapus Kriteria Ini">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            `;

            container.appendChild(row);
            updateFilterBadgeT2();
            const input = document.getElementById(`extraInputT2_${rowId}`);
            if (input) input.focus();
        }

        function removeExtraRowT2(rowId) {
            const row = document.getElementById(`extraRowT2_${rowId}`);
            if (row) {
                row.remove();
                updateFilterBadgeT2();
                executeMultiSearchT2();
            }
        }

        function selectExtraCategoryT2(rowId, cat, label, icon) {
            const valInput = document.getElementById(`extraCatValT2_${rowId}`);
            const labelSpan = document.getElementById(`labelExtraCatT2_${rowId}`);
            const inp = document.getElementById(`extraInputT2_${rowId}`);
            if (valInput) valInput.value = cat;
            if (labelSpan) labelSpan.innerText = icon;
            if (inp) {
                inp.placeholder = getPlaceholderT2(cat);
                inp.focus();
            }
            const menu = document.querySelector(`#dropdownExtraCatT2_${rowId} .custom-dropdown-menu`);
            if (menu) menu.classList.add('hidden');
        }

        // EKSEKUSI PENCARIAN (HANYA KETIKA ENTER / CARI DIKLIK)
        function executeMultiSearchT2() {
            const filters = [];

            // 1. Kriteria Utama
            const mainCat = document.getElementById('mainCatT2').value;
            const mainVal = document.getElementById('mainInputT2').value.trim().toLowerCase();
            if (mainVal) {
                filters.push({ cat: mainCat, val: mainVal });
            }

            // 2. Kriteria Tambahan
            const extraRows = document.querySelectorAll('#extraRowsContainerT2 .extra-filter-row');
            extraRows.forEach(row => {
                const catEl = row.querySelector('.extra-cat-val');
                const inpEl = row.querySelector('.extra-input-val');
                if (catEl && inpEl) {
                    const cat = catEl.value;
                    const val = inpEl.value.trim().toLowerCase();
                    if (val) {
                        filters.push({ cat: cat, val: val });
                    }
                }
            });

            function checkMatchT2(el) {
                for (const f of filters) {
                    if (f.cat === 'query') {
                        const haystack = [
                            el.dataset.nim || '',
                            el.dataset.nama || '',
                            el.dataset.prodi || '',
                            el.dataset.status || '',
                            el.dataset.periode || ''
                        ].join(' ').toLowerCase();
                        if (!haystack.includes(f.val)) return false;
                    } else if (f.cat === 'nama') {
                        if (!(el.dataset.nama || '').includes(f.val)) return false;
                    } else if (f.cat === 'nim') {
                        if (!(el.dataset.nim || '').includes(f.val)) return false;
                    } else if (f.cat === 'prodi') {
                        if (!(el.dataset.prodi || '').includes(f.val)) return false;
                    } else if (f.cat === 'status') {
                        if (!(el.dataset.status || '').includes(f.val)) return false;
                    }
                }
                return true;
            }

            // Saring Baris Tabel Desktop
            const rows = document.querySelectorAll('.t2-table-row');
            let visibleCount = 0;
            rows.forEach(row => {
                const match = checkMatchT2(row);
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            // Saring Kartu Mobile
            const cards = document.querySelectorAll('.t2-card-row');
            cards.forEach(card => {
                const match = checkMatchT2(card);
                card.style.display = match ? '' : 'none';
            });

            // Update Counter
            const counterEl = document.getElementById('visibleRowCountT2');
            if (counterEl) counterEl.innerText = visibleCount;

            const emptyRow = document.getElementById('noSearchResultRowT2');
            if (emptyRow) {
                emptyRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
            }
            const emptyCard = document.getElementById('noSearchResultCardT2');
            if (emptyCard) {
                emptyCard.style.display = (visibleCount === 0 && cards.length > 0) ? '' : 'none';
            }

            // Update Active Filter Tags
            renderActiveFilterTagsT2(filters);
        }

        function renderActiveFilterTagsT2(filters) {
            const container = document.getElementById('activeTagsContainerT2');
            if (!container) return;
            container.innerHTML = '';

            if (filters.length === 0) return;

            filters.forEach(f => {
                container.innerHTML += `
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-100 text-orange-800 border border-orange-200">
                        <span>${getCategoryEmojiT2(f.cat)} ${f.cat.toUpperCase()}: <em>"${escapeHtml(f.val)}"</em></span>
                    </span>
                `;
            });
        }

        function resetMultiSearchT2() {
            document.getElementById('mainInputT2').value = '';
            document.getElementById('mainCatT2').value = 'query';
            document.getElementById('labelMainCatT2').innerText = '🔍';
            document.getElementById('mainInputT2').placeholder = getPlaceholderT2('query');

            const container = document.getElementById('extraRowsContainerT2');
            if (container) container.innerHTML = '';
            updateFilterBadgeT2();

            closeExtraPopoverT2();
            executeMultiSearchT2();
        }

        // --- Mobile Stats Slider Controls ---
        function scrollToStatSlideT2(index) {
            const slider = document.getElementById('statsCardsSliderT2');
            if (!slider) return;
            const items = slider.querySelectorAll('.stats-slide-item');
            if (items[index]) {
                const targetLeft = items[index].offsetLeft - (slider.clientWidth - items[index].clientWidth) / 2;
                slider.scrollTo({ left: Math.max(0, targetLeft), behavior: 'smooth' });
                updateStatsIndicatorsT2(index);
            }
        }

        function updateStatsIndicatorsT2(activeIndex) {
            const dots = document.querySelectorAll('#statsSliderIndicatorsT2 .stats-indicator-dot');
            dots.forEach((dot, idx) => {
                if (idx === activeIndex) {
                    dot.classList.remove('w-1.5', 'bg-slate-300');
                    dot.classList.add('w-5', 'bg-orange-500');
                } else {
                    dot.classList.remove('w-5', 'bg-orange-500');
                    dot.classList.add('w-1.5', 'bg-slate-300');
                }
            });
        }

        function initStatsSliderListenerT2() {
            const slider = document.getElementById('statsCardsSliderT2');
            if (!slider) return;
            slider.addEventListener('scroll', () => {
                const items = slider.querySelectorAll('.stats-slide-item');
                const scrollLeft = slider.scrollLeft;
                const containerCenter = scrollLeft + slider.clientWidth / 2;
                let closestIdx = 0;
                let minDiff = Infinity;
                items.forEach((item, idx) => {
                    const itemCenter = item.offsetLeft + item.clientWidth / 2;
                    const diff = Math.abs(containerCenter - itemCenter);
                    if (diff < minDiff) {
                        minDiff = diff;
                        closestIdx = idx;
                    }
                });
                updateStatsIndicatorsT2(closestIdx);
            }, { passive: true });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initStatsSliderListenerT2();
            initExcelDropzone();
        });

        // Cegah browser membuka file secara default saat di-drop di luar dropzone
        window.addEventListener('dragover', (e) => e.preventDefault(), false);
        window.addEventListener('drop', (e) => e.preventDefault(), false);

// =========================================================================
        // AJAX DYNAMIC UI UPDATE HELPERS (Tahap 2)
        // =========================================================================
        function updatePerwalianTahap2Counters(idPerwalian, totalLulus, totalTidakLulus, statusTahap2, stats, totalApproved) {
            totalLulus = parseInt(totalLulus || 0);
            totalTidakLulus = parseInt(totalTidakLulus || 0);
            const totalDinilai = totalLulus + totalTidakLulus;
            const approvedCount = (typeof totalApproved !== 'undefined' && totalApproved !== null)
                ? parseInt(totalApproved || 0)
                : totalDinilai;

            // Dapatkan total MK dari data-attribute baris atau card
            const row = document.getElementById(`t2Row_${idPerwalian}`);
            const card = document.getElementById(`t2Card_${idPerwalian}`);
            const totalMk = parseInt((row ? row.getAttribute('data-total-mk') : 0) || (card ? card.getAttribute('data-total-mk') : 0) || 0);

            // 1. Update di tabel desktop
            const dinilaiText = document.getElementById(`t2DinilaiText_${idPerwalian}`);
            if (dinilaiText) {
                dinilaiText.innerText = `${approvedCount} / ${totalMk} Disetujui`;
            }
            const countRow = document.getElementById(`t2GradingCountRow_${idPerwalian}`);
            if (countRow) {
                countRow.style.display = totalDinilai > 0 ? '' : 'none';
            }
            const lulusVal = document.getElementById(`t2LulusVal_${idPerwalian}`);
            if (lulusVal) lulusVal.innerText = totalLulus;
            const tidakLulusVal = document.getElementById(`t2TidakLulusVal_${idPerwalian}`);
            if (tidakLulusVal) tidakLulusVal.innerText = totalTidakLulus;

            // 2. Update di mobile card
            const cardDinilaiText = document.getElementById(`t2CardDinilaiText_${idPerwalian}`);
            if (cardDinilaiText) {
                cardDinilaiText.innerText = `${approvedCount} / ${totalMk} Disetujui`;
            }
            const cardCount = document.getElementById(`t2CardGradingCount_${idPerwalian}`);
            if (cardCount) {
                cardCount.style.display = totalDinilai > 0 ? '' : 'none';
            }
            const cardLulusVal = document.getElementById(`t2CardLulusVal_${idPerwalian}`);
            if (cardLulusVal) cardLulusVal.innerText = totalLulus;
            const cardTidakLulusVal = document.getElementById(`t2CardTidakLulusVal_${idPerwalian}`);
            if (cardTidakLulusVal) cardTidakLulusVal.innerText = totalTidakLulus;

            // 3. Update status jika ada
            if (statusTahap2) {
                updatePerwalianTahap2Status(idPerwalian, statusTahap2, stats);
            }
        }

        function updatePerwalianTahap2Status(idPerwalian, newStatus, stats) {
            let badgeClass = 'bg-slate-100 text-slate-600 border-slate-300';
            let badgeIcon = 'fa-hourglass-start';

            if (newStatus === 'Selesai') {
                badgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                badgeIcon = 'fa-circle-check';
            } else if (newStatus === 'Proses Penilaian') {
                badgeClass = 'bg-purple-100 text-purple-800 border-purple-300';
                badgeIcon = 'fa-clock';
            }

            // 1. Update row desktop
            const row = document.getElementById(`t2Row_${idPerwalian}`);
            if (row) {
                row.setAttribute('data-status', newStatus.toLowerCase());
            }
            const stCol = document.getElementById(`t2StatusCol_${idPerwalian}`);
            if (stCol) {
                stCol.innerHTML = `
                    <span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-semibold rounded-full border whitespace-nowrap ${badgeClass}">
                        <i class="fa-solid ${badgeIcon} mr-1 text-[10px]"></i>
                        ${escapeHtml(newStatus)}
                    </span>
                `;
            }

            // 2. Update mobile card
            const card = document.getElementById(`t2Card_${idPerwalian}`);
            if (card) {
                card.setAttribute('data-status', newStatus.toLowerCase());
            }
            const cardBadge = document.getElementById(`t2CardStatusBadge_${idPerwalian}`);
            if (cardBadge) {
                cardBadge.className = `inline-flex items-center px-2 py-0.5 text-[10px] font-bold rounded-full border shrink-0 whitespace-nowrap ${badgeClass}`;
                cardBadge.innerHTML = `<i class="fa-solid ${badgeIcon} mr-1 text-[9px]"></i> ${escapeHtml(newStatus)}`;
            }

            // 3. Update Tombol Aksi Desktop & Mobile
            const btnCol = document.getElementById(`t2BtnCol_${idPerwalian}`);
            if (btnCol) {
                if (newStatus === 'Selesai') {
                    btnCol.innerHTML = `
                        <button type="button" onclick="openGradingModal(${idPerwalian})" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-xl shadow-2xs transition cursor-pointer whitespace-nowrap">
                            <i class="fa-solid fa-eye mr-1.5 text-emerald-600"></i> Lihat / Edit Approval
                        </button>
                    `;
                } else {
                    btnCol.innerHTML = `
                        <button type="button" onclick="openGradingModal(${idPerwalian})" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 rounded-xl shadow-sm transition cursor-pointer whitespace-nowrap">
                            <i class="fa-solid fa-list-check mr-1.5"></i> Review & Approve
                        </button>
                    `;
                }
            }

            const cardBtn = document.getElementById(`t2CardBtn_${idPerwalian}`);
            if (cardBtn) {
                if (newStatus === 'Selesai') {
                    cardBtn.className = "w-full py-2 px-3 text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-xl shadow-2xs flex items-center justify-center gap-1.5 transition active:scale-98 cursor-pointer";
                    cardBtn.innerHTML = `<i class="fa-solid fa-eye text-emerald-600 text-[11px]"></i> Lihat / Edit Approval`;
                } else {
                    cardBtn.className = "w-full py-2 px-3 text-xs font-bold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 rounded-xl shadow-xs flex items-center justify-center gap-1.5 transition active:scale-98 cursor-pointer";
                    cardBtn.innerHTML = `<i class="fa-solid fa-check-double text-[11px]"></i> Review & Approve`;
                }
            }

            // 4. Update top summary statistics
            if (stats) {
                const tot = parseInt(stats.tahap1_disetujui || 0);
                const belum = parseInt(stats.tahap2_belum_dinilai || 0);
                const proses = parseInt(stats.tahap2_proses || 0);
                const selesai = parseInt(stats.tahap2_selesai || 0);

                const pctBelum = tot > 0 ? Math.round((belum / tot) * 100) : 0;
                const pctProses = tot > 0 ? Math.round((proses / tot) * 100) : 0;
                const pctSelesai = tot > 0 ? Math.round((selesai / tot) * 100) : 0;

                const elTot = document.getElementById('statT2TotalMhs');
                if (elTot) elTot.innerText = tot;
                const elBelum = document.getElementById('statT2Belum');
                if (elBelum) elBelum.innerHTML = `${belum} <span class="text-[11px] sm:text-xs font-semibold text-amber-600 font-normal">(${pctBelum}%)</span>`;
                const elProses = document.getElementById('statT2Proses');
                if (elProses) elProses.innerHTML = `${proses} <span class="text-[11px] sm:text-xs font-semibold text-cyan-600 font-normal">(${pctProses}%)</span>`;
                const elSelesai = document.getElementById('statT2Selesai');
                if (elSelesai) elSelesai.innerHTML = `${selesai} <span class="text-[11px] sm:text-xs font-semibold text-emerald-600 font-normal">(${pctSelesai}%)</span>`;
            }
        }

        function updateAllPerwalianTahap2UI(list, stats) {
            if (Array.isArray(list)) {
                list.forEach(item => {
                    if (item.id_perwalian) {
                        updatePerwalianTahap2Counters(
                            item.id_perwalian,
                            item.total_lulus || 0,
                            item.total_tidak_lulus || 0,
                            item.status_tahap2 || 'Belum Dinilai',
                            null,
                            item.total_approved_mk || 0
                        );
                    }
                });
            }
            if (stats) {
                updatePerwalianTahap2Status(0, '', stats);
            }
        }
    </script>
</body>
</html>
