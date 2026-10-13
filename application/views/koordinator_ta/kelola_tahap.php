<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Kelola Status & Downgrade Tahapan TA — Koordinator TA'; ?> - IFIK Telkom University</title>

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
    
    <!-- Google Fonts Plus Jakarta Sans & JetBrains Mono -->
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

        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            min-height: 100vh;
            transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1), width 0.75s cubic-bezier(0.76, 0, 0.24, 1);
            box-sizing: border-box;
        }

        @media (min-width: 1024px) {
            .page-wrapper-for-sidebar {
                margin-left: 280px;
                width: calc(100% - 280px);
            }
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }

        .danger-glow {
            box-shadow: 0 0 25px -5px rgba(239, 68, 68, 0.25);
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
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Unified Multi-Search Bar Styles */
        .search-pill-container {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }
        .unified-search-pill {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 9999px;
            padding: 4px 6px 4px 14px;
            height: 48px;
            transition: all 0.25s ease;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
            flex: 1;
            min-width: 0;
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
            justify-content: space-between;
            gap: 10px;
            background: #fff7ed;
            border: 1.5px solid #ffedd5;
            border-radius: 14px;
            padding: 6px 14px;
            height: 48px;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
        }
        .btn-remove-row {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: #fff1f2;
            border: 1.5px solid #fecdd3;
            border-radius: 14px;
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
        .extra-filter-row.open-dropdown {
            z-index: 120 !important;
        }
        .custom-dropdown-container.open {
            z-index: 130 !important;
        }
        .custom-dropdown-container.open .custom-dropdown-menu {
            display: block !important;
        }
        .custom-dropdown-container.open .dropdown-arrow {
            transform: rotate(180deg);
        }
        @media (max-width: 640px) {
            .search-pill-container {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
            }
            .unified-search-pill {
                padding: 2px 6px 2px 10px;
                height: 42px;
                border-radius: 14px;
            }
            .btn-standalone-add {
                width: 100%;
                height: 42px;
                border-radius: 12px;
            }
            .extra-rows-card {
                padding: 12px;
                border-radius: 14px;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Curved Sidebar Navigation -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Page Content Wrapper -->
    <div class="page-wrapper-for-sidebar flex flex-col min-h-screen">
        
        <!-- Top Sticky Header -->
        <header class="sticky top-0 z-30 glass-header px-4 sm:px-8 py-3.5 sm:py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center font-bold text-lg shadow-xs shrink-0">
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h1 class="text-base sm:text-lg md:text-xl font-bold text-slate-900 tracking-tight truncate">
                                Kelola Status &amp; Downgrade Tahapan TA
                            </h1>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 uppercase tracking-wider shrink-0">
                                <i class="fa-solid fa-lock text-[9px]"></i> Koordinator TA
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-normal truncate">
                            Wewenang khusus untuk menurunkan tahapan bimbingan atau mereset status mahasiswa.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body Container -->
        <main class="max-w-7xl mx-auto px-4 sm:px-8 py-6 w-full space-y-6 flex-grow">

            <!-- Danger Zone Banner -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white p-5 sm:p-6 shadow-sm danger-glow">
                <div class="absolute -right-6 -bottom-6 text-red-800/25 pointer-events-none select-none text-9xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="relative z-10">
                    <div class="space-y-1.5">
                        <div class="inline-flex items-center gap-2 bg-red-900/40 text-red-100 px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider backdrop-blur-xs">
                            <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                            Prosedur Peringatan Keras (Strict Warning Area)
                        </div>
                        <h2 class="text-lg sm:text-xl font-extrabold tracking-tight">
                            Penghapusan Permanen Berkas &amp; Nilai Bimbingan Tanpa Cadangan (No Backup)
                        </h2>
                        <p class="text-xs sm:text-sm text-red-100 font-normal leading-relaxed">
                            Fitur ini digunakan jika terjadi kesalahan alur fatal atau rekomendasi khusus penguji/pembimbing yang mengharuskan mahasiswa turun tahap atau reset total. 
                            <strong>Seluruh nilai, draf bab revisi, dan riwayat bimbingan pada tahap terkait akan dihapus total dari database dan server.</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <?php 
                $candidatesCount = is_array($candidates) ? count($candidates) : 0;
                $countSidang = 0;
                $countP3 = 0;
                $countP2 = 0;
                $countP1 = 0;
                if (!empty($candidates)) {
                    foreach ($candidates as $c) {
                        $sk = $c['stageKey'] ?? '';
                        if (in_array($sk, ['sidang', 'lulus'])) $countSidang++;
                        elseif ($sk === 'preview3') $countP3++;
                        elseif ($sk === 'preview2') $countP2++;
                        elseif ($sk === 'preview1') $countP1++;
                    }
                }
                $totalLogs = is_array($audit_logs) ? count($audit_logs) : 0;
            ?>
            <!-- Stats Overview Cards (Exact Neumorphic Interactive Design) -->
            <div class="relative mb-6">
                <div class="stats-slider-container flex sm:grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 overflow-x-auto sm:overflow-visible snap-x snap-mandatory py-1 scrollbar-none">
                    
                    <!-- 1. Total Mahasiswa TA Card (Brand/Orange) -->
                    <div onclick="setStageFilter('all', document.querySelector('.stage-pill[onclick*=\'all\']'))" class="stats-slide-item min-w-[78vw] xs:min-w-[70vw] sm:min-w-0 snap-center shrink-0 w-full sm:w-auto group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                        <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-orange-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-brand-500/40 hover:shadow-2xl hover:shadow-brand-500/10 p-5 h-full">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-brand-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-brand-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-brand-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-brand-600 transition-colors truncate">Total Mahasiswa TA</p>
                                    <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $candidatesCount; ?></h3>
                                    <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Pengajuan &amp; Bimbingan</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-2xl bg-brand-500/20 blur-md group-hover:blur-lg group-hover:bg-brand-500/30 transition-all"></div>
                                    <div class="relative p-3.5 rounded-2xl border border-orange-200/80 bg-gradient-to-br from-orange-50 to-orange-100/70 shadow-md text-brand-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-users text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-brand-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce"></div>
                                    <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Tahap Sidang TA Card (Rose) -->
                    <div onclick="setStageFilter('sidang', document.querySelector('.stage-pill[onclick*=\'sidang\']'))" class="stats-slide-item min-w-[78vw] xs:min-w-[70vw] sm:min-w-0 snap-center shrink-0 w-full sm:w-auto group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                        <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-rose-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-rose-500/40 hover:shadow-2xl hover:shadow-rose-500/10 p-5 h-full">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-rose-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-rose-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-rose-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-rose-600 transition-colors truncate">Tahap Sidang TA</p>
                                    <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $countSidang; ?> <span class="text-xs font-semibold text-rose-600 font-normal">(<?= $candidatesCount > 0 ? round(($countSidang/$candidatesCount)*100) : 0; ?>%)</span></h3>
                                    <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Sidang Akhir &amp; Kelulusan</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-2xl bg-rose-500/20 blur-md group-hover:blur-lg group-hover:bg-rose-500/30 transition-all"></div>
                                    <div class="relative p-3.5 rounded-2xl border border-rose-200/80 bg-gradient-to-br from-rose-50 to-rose-100/70 shadow-md text-rose-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-graduation-cap text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-rose-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-bounce"></div>
                                    <div class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Tahap Preview 3 Card (Cyan) -->
                    <div onclick="setStageFilter('preview3', document.querySelector('.stage-pill[onclick*=\'preview3\']'))" class="stats-slide-item min-w-[78vw] xs:min-w-[70vw] sm:min-w-0 snap-center shrink-0 w-full sm:w-auto group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                        <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-cyan-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-cyan-500/40 hover:shadow-2xl hover:shadow-cyan-500/10 p-5 h-full">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-cyan-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-cyan-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-cyan-600 transition-colors truncate">Tahap Preview 3</p>
                                    <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $countP3; ?> <span class="text-xs font-semibold text-cyan-600 font-normal">(<?= $candidatesCount > 0 ? round(($countP3/$candidatesCount)*100) : 0; ?>%)</span></h3>
                                    <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Pra-Sidang Tugas Akhir</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-2xl bg-cyan-500/20 blur-md group-hover:blur-lg group-hover:bg-cyan-500/30 transition-all"></div>
                                    <div class="relative p-3.5 rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-cyan-100/70 shadow-md text-cyan-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-file-signature text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-cyan-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce"></div>
                                    <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1.5 h-1.5 bg-cyan-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Tahap Preview 2 Card (Emerald) -->
                    <div onclick="setStageFilter('preview2', document.querySelector('.stage-pill[onclick*=\'preview2\']'))" class="stats-slide-item min-w-[78vw] xs:min-w-[70vw] sm:min-w-0 snap-center shrink-0 w-full sm:w-auto group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                        <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-emerald-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 p-5 h-full">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-emerald-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-emerald-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-emerald-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors truncate">Tahap Preview 2</p>
                                    <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $countP2; ?> <span class="text-xs font-semibold text-emerald-600 font-normal">(<?= $candidatesCount > 0 ? round(($countP2/$candidatesCount)*100) : 0; ?>%)</span></h3>
                                    <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Presentasi &amp; Uji Dosen</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-2xl bg-emerald-500/20 blur-md group-hover:blur-lg group-hover:bg-emerald-500/30 transition-all"></div>
                                    <div class="relative p-3.5 rounded-2xl border border-emerald-200/80 bg-gradient-to-br from-emerald-50 to-emerald-100/70 shadow-md text-emerald-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-chalkboard-user text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-emerald-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce"></div>
                                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Riwayat Downgrade Card (Indigo) -->
                    <div onclick="switchMainTab('tabLogAudit')" class="stats-slide-item min-w-[78vw] xs:min-w-[70vw] sm:min-w-0 snap-center shrink-0 w-full sm:w-auto group cursor-pointer transform transition-all duration-500 hover:scale-[1.03] hover:-translate-y-1">
                        <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-indigo-50/20 to-white shadow-xl relative backdrop-blur-xl overflow-hidden hover:border-indigo-500/40 hover:shadow-2xl hover:shadow-indigo-500/10 p-5 h-full">
                            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500/5 to-transparent opacity-40 group-hover:opacity-70 transition-opacity duration-500"></div>
                                <div class="absolute -bottom-16 -right-16 w-36 h-36 rounded-full bg-gradient-to-tr from-indigo-500/20 to-transparent blur-2xl opacity-30 group-hover:opacity-60 transform group-hover:scale-125 transition-all duration-700"></div>
                                <div class="absolute top-3 left-3 w-8 h-8 rounded-full bg-indigo-500/10 blur-lg"></div>
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-1000"></div>
                            </div>

                            <div class="relative z-10 flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-indigo-600 transition-colors truncate">Riwayat Downgrade</p>
                                    <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight"><?= $totalLogs; ?> <span class="text-xs font-semibold text-indigo-600 font-normal">Aksi</span></h3>
                                    <p class="text-xs font-medium text-slate-500 mt-1 line-clamp-1">Log Audit Tersimpan</p>
                                </div>
                                
                                <div class="relative shrink-0">
                                    <div class="absolute inset-0 rounded-2xl bg-indigo-500/20 blur-md group-hover:blur-lg group-hover:bg-indigo-500/30 transition-all"></div>
                                    <div class="relative p-3.5 rounded-2xl border border-indigo-200/80 bg-gradient-to-br from-indigo-50 to-indigo-100/70 shadow-md text-indigo-600 transform group-hover:rotate-6 group-hover:scale-110 transition-all duration-500">
                                        <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                                <div class="w-1/3 h-0.5 bg-gradient-to-r from-indigo-500 to-transparent rounded-full transform group-hover:w-2/3 transition-all duration-500"></div>
                                <div class="flex space-x-1 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce"></div>
                                    <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Tab Switcher Navigation (Seimbang Kanan Kiri 50/50) -->
            <div class="grid grid-cols-2 border-b border-slate-200 w-full">
                <button type="button" id="btnMainTabList" onclick="switchMainTab('tabListMhs')" class="w-full py-3 px-4 text-xs sm:text-sm font-bold border-b-2 border-red-600 text-red-600 flex items-center justify-center gap-2 transition cursor-pointer">
                    <i class="fa-solid fa-list-check"></i> <span>Daftar Mahasiswa &amp; Eksekusi</span>
                </button>
                <button type="button" id="btnMainTabLog" onclick="switchMainTab('tabLogAudit')" class="w-full py-3 px-4 text-xs sm:text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center justify-center gap-2 transition cursor-pointer">
                    <i class="fa-solid fa-clock-rotate-left"></i> <span>Riwayat Audit Log (<?= $totalLogs; ?>)</span>
                </button>
            </div>

            <!-- SECTION TAB 1: DAFTAR MAHASISWA & EKSEKUSI -->
            <div id="sectionTabListMhs" class="space-y-4">
                
                <!-- Table Toolbar & Multi-Search Filter (Exact Style from Screenshot) -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs space-y-4">
                    
                    <!-- Header Toolbar -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5 tracking-tight">
                                <i class="fa-solid fa-list-check text-orange-600 text-lg"></i> Daftar Mahasiswa Tugas Akhir
                            </h2>
                            <p class="text-xs text-slate-500 font-normal mt-0.5">Pilih mahasiswa untuk meninjau berkas atau melakukan proses penurunan tahap (downgrade).</p>
                        </div>
                    </div>

                    <!-- Row 1: Unified Multi-Search Bar -->
                    <div class="relative search-pill-container" id="multiSearchWrapper">
                        <!-- Main Search Pill -->
                        <div class="unified-search-pill">
                            <!-- Main Category Selector Dropdown -->
                            <div class="relative custom-dropdown-container shrink-0">
                                <input type="hidden" id="mainCategorySelect" value="query">
                                <button type="button" onclick="toggleCustomDropdown('main-cat', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-0.5 hover:text-orange-600 focus:outline-hidden" title="Kategori Pencarian">
                                    <span id="label-filter-main-cat" class="text-sm sm:text-base leading-none block">🔍</span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-main-cat"></i>
                                </button>
                                <div id="menu-filter-main-cat" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                                    <div onclick="selectMainCategory('query', '🔍 Kata Kunci (Semua)', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-orange-600"><span>🔍 Kata Kunci (Semua)</span></div>
                                    <div onclick="selectMainCategory('nama', '🏷️ Nama Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🏷️ Nama Mahasiswa</span></div>
                                    <div onclick="selectMainCategory('nim', '🆔 NIM Mahasiswa', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🆔 NIM Mahasiswa</span></div>
                                    <div onclick="selectMainCategory('judul', '📖 Judul Tugas Akhir', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>📖 Judul Tugas Akhir</span></div>
                                    <div onclick="selectMainCategory('tahap', '🔄 Tahap Bimbingan', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>🔄 Tahap Bimbingan</span></div>
                                    <div onclick="selectMainCategory('dosen', '👤 Dosen Pembimbing/Penguji', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>👤 Dosen Pembimbing/Penguji</span></div>
                                </div>
                            </div>

                            <div class="unified-divider"></div>

                            <!-- Input Text Value Container (NO auto-filter on keyup! only Enter / click Cari!) -->
                            <div id="mainValueContainer" class="flex-1 flex items-center min-w-0">
                                <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                                <input type="text" id="mainSearchInput" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); executeSearch(); }" placeholder="Ketik kata kunci lalu tekan Enter atau klik Cari..." class="w-full text-xs font-medium bg-transparent border-none focus:outline-hidden text-slate-800 placeholder:text-slate-400">
                            </div>

                            <!-- Main Custom Select Dropdown Container (For Tahap) -->
                            <div id="mainCustomSelectWrap" class="hidden flex-1 relative custom-dropdown-container">
                                <input type="hidden" id="mainCustomSelectVal" value="all">
                                <button type="button" onclick="toggleCustomDropdown('main-select', event)" class="w-full py-1 text-xs font-semibold text-slate-800 flex items-center justify-between cursor-pointer focus:outline-hidden">
                                    <span id="label-filter-main-select" class="flex items-center gap-1.5 truncate">Semua Tahap</span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-main-select"></i>
                                </button>
                                <div id="menu-filter-main-select" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                                    <div onclick="selectMainOption('all', 'Semua Tahap', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-orange-600"><span>Semua Tahap</span></div>
                                    <div onclick="selectMainOption('sidang', 'Tahap Sidang TA / Lulus', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>Tahap Sidang TA / Lulus</span></div>
                                    <div onclick="selectMainOption('preview3', 'Tahap Preview 3', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>Tahap Preview 3</span></div>
                                    <div onclick="selectMainOption('preview2', 'Tahap Preview 2', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>Tahap Preview 2</span></div>
                                    <div onclick="selectMainOption('preview1', 'Tahap Preview 1', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>Tahap Preview 1</span></div>
                                </div>
                            </div>

                            <!-- Tombol Cari -->
                            <button type="button" onclick="executeSearch()" class="px-3.5 py-1.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-500 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition cursor-pointer active:scale-95 shrink-0 ml-1.5" title="Klik untuk melakukan pencarian">
                                <i class="fa-solid fa-magnifying-glass text-[11px]"></i> Cari
                            </button>
                        </div>

                        <!-- Standalone Add Filter Button (Filter Tambahan 1/4) -->
                        <button type="button" id="standaloneAddBtn" onclick="toggleOrAddFilterRow(event)" class="btn-standalone-add" title="Buka / Tutup / Tambah Filter Baru (Maks 4)">
                            <span class="btn-standalone-label inline-flex items-center gap-2">
                                <i class="fa-solid fa-filter text-orange-600 text-xs"></i>
                                <span class="font-bold text-xs text-orange-800">Filter Tambahan</span>
                            </span>
                            <span id="filterCountBadge" class="badge-standalone-count">1/4</span>
                        </button>

                        <!-- Extra Filter Rows Card Popover -->
                        <div id="extraRowsCard" class="extra-rows-card space-y-2.5">
                            <div id="additionalFilterRowsContainer" class="space-y-2.5">
                            </div>
                            
                            <div class="flex items-center justify-between border-t border-slate-100 pt-2.5 mt-2 text-xs">
                                <span class="text-slate-400 text-[11px]">Gunakan kombinasi kriteria untuk mempersempit pencarian data pengajuan tugas akhir.</span>
                                <button type="button" onclick="resetMultiSearch()" class="text-rose-600 hover:text-rose-700 font-bold transition-colors cursor-pointer">
                                    Reset All Filters
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Page Size & Records Count + Quick Stage Filter Pills -->
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <!-- Stage Filter Pills -->
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs scrollbar-none">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mr-1 hidden sm:inline">Pill:</span>
                            <button type="button" onclick="setStageFilter('all', this)" class="stage-pill active px-3 py-1.5 rounded-lg font-bold bg-slate-900 text-white transition cursor-pointer whitespace-nowrap">Semua (<?= $candidatesCount; ?>)</button>
                            <button type="button" onclick="setStageFilter('sidang', this)" class="stage-pill px-3 py-1.5 rounded-lg font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer whitespace-nowrap">Sidang (<?= $countSidang; ?>)</button>
                            <button type="button" onclick="setStageFilter('preview3', this)" class="stage-pill px-3 py-1.5 rounded-lg font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer whitespace-nowrap">Preview 3 (<?= $countP3; ?>)</button>
                            <button type="button" onclick="setStageFilter('preview2', this)" class="stage-pill px-3 py-1.5 rounded-lg font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer whitespace-nowrap">Preview 2 (<?= $countP2; ?>)</button>
                            <button type="button" onclick="setStageFilter('preview1', this)" class="stage-pill px-3 py-1.5 rounded-lg font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer whitespace-nowrap">Preview 1 (<?= $countP1; ?>)</button>
                        </div>

                        <!-- Page Size & Counter Right (Exact Style from Screenshot) -->
                        <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-slate-50 border border-slate-200 px-3 h-9 rounded-xl shadow-2xs">
                            <span class="font-medium">Tampilkan</span>
                            <select id="selectPerPageTop" onchange="changePerPage(this.value)" class="h-6 px-1.5 text-xs font-bold bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-hidden focus:ring-1 focus:ring-orange-500 cursor-pointer">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="font-medium">data/hal</span>
                            <span class="text-slate-300">|</span>
                            <span>Total: <strong class="text-slate-900 font-bold" id="toolbarTotalCount"><?= $candidatesCount; ?></strong></span>
                        </div>
                    </div>

                </div>

                <!-- Candidates Table Container -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-600 font-bold uppercase text-[11px] tracking-wider">
                                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                                    <th class="py-3.5 px-4">Mahasiswa</th>
                                    <th class="py-3.5 px-4">Judul Tugas Akhir</th>
                                    <th class="py-3.5 px-4">Tahapan Saat Ini</th>
                                    <th class="py-3.5 px-4">Dosen Pembimbing &amp; Penguji</th>
                                    <th class="py-3.5 px-4 text-center w-36">Tindakan Khusus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="candidateTableBody">
                                <?php if (!empty($candidates)): ?>
                                    <?php $no = 1; foreach ($candidates as $c): ?>
                                        <?php 
                                            $stageColor = $c['stageColor'] ?? 'slate';
                                            $stageLabel = $c['progresStage'] ?? 'Pendaftaran';
                                            $stageKey   = $c['stageKey'] ?? 'pendaftaran';
                                            $targetsJson = htmlspecialchars(json_encode($c['allowed_targets'] ?? []), ENT_QUOTES, 'UTF-8');
                                            $candidateJson = htmlspecialchars(json_encode([
                                                'nim' => $c['nim'],
                                                'nama' => $c['nama'] ?? $c['nim'],
                                                'judul' => $c['judul'] ?? '-',
                                                'stageKey' => $stageKey,
                                                'stageLabel' => $stageLabel,
                                                'stageColor' => $stageColor,
                                                'pembimbing_1' => $c['pembimbing_1_name'] ?? '-',
                                                'pembimbing_2' => $c['pembimbing_2_name'] ?? '-',
                                                'penguji_1' => $c['penguji_1_name'] ?? '-',
                                                'penguji_2' => $c['penguji_2_name'] ?? '-',
                                                'allowed_targets' => $c['allowed_targets'] ?? []
                                            ]), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <tr class="candidate-row hover:bg-slate-50/70 transition" 
                                            data-stage="<?= strtolower($stageKey); ?>" 
                                            data-nama="<?= strtolower(htmlspecialchars($c['nama'] ?? '')); ?>"
                                            data-nim="<?= strtolower(htmlspecialchars($c['nim'] ?? '')); ?>"
                                            data-judul="<?= strtolower(htmlspecialchars($c['judul'] ?? '')); ?>"
                                            data-dosen="<?= strtolower(htmlspecialchars(($c['pembimbing_1_name'] ?? '') . ' ' . ($c['pembimbing_2_name'] ?? '') . ' ' . ($c['penguji_1_name'] ?? '') . ' ' . ($c['penguji_2_name'] ?? ''))); ?>"
                                            data-search="<?= strtolower(htmlspecialchars(($c['nim'] ?? '') . ' ' . ($c['nama'] ?? '') . ' ' . ($c['judul'] ?? '') . ' ' . ($c['pembimbing_1_name'] ?? '') . ' ' . ($c['penguji_1_name'] ?? '') . ' ' . $stageLabel)); ?>">
                                            <td class="py-3.5 px-4 text-center font-mono text-slate-400"><?= $no++; ?></td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-bold text-slate-900 text-xs sm:text-sm"><?= htmlspecialchars($c['nama'] ?? '-'); ?></div>
                                                <div class="font-mono text-slate-500 text-[11px] mt-0.5"><?= htmlspecialchars($c['nim'] ?? '-'); ?></div>
                                            </td>
                                            <td class="py-3.5 px-4 max-w-xs sm:max-w-sm">
                                                <div class="line-clamp-2 text-slate-800 text-xs font-normal" title="<?= htmlspecialchars($c['judul'] ?? '-'); ?>">
                                                    <?= htmlspecialchars($c['judul'] ?? '-'); ?>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <?php 
                                                    $bgBadge = 'bg-slate-100 text-slate-700 border-slate-200';
                                                    if ($stageKey === 'sidang' || $stageKey === 'lulus') $bgBadge = 'bg-rose-50 text-rose-700 border-rose-200';
                                                    elseif ($stageKey === 'preview3') $bgBadge = 'bg-cyan-50 text-cyan-700 border-cyan-200';
                                                    elseif ($stageKey === 'preview2') $bgBadge = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                                    elseif ($stageKey === 'preview1') $bgBadge = 'bg-teal-50 text-teal-700 border-teal-200';
                                                ?>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border <?= $bgBadge; ?>">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                                    <?= htmlspecialchars($stageLabel); ?>
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 text-[11px] space-y-0.5">
                                                <div class="text-slate-700"><span class="text-slate-400 font-medium">P1:</span> <?= htmlspecialchars($c['pembimbing_1_name'] ?? '-'); ?></div>
                                                <?php if (!empty($c['pembimbing_2_name'])): ?>
                                                    <div class="text-slate-700"><span class="text-slate-400 font-medium">P2:</span> <?= htmlspecialchars($c['pembimbing_2_name']); ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($c['penguji_1_name'])): ?>
                                                    <div class="text-indigo-600 font-medium"><span class="text-slate-400 font-normal">U1:</span> <?= htmlspecialchars($c['penguji_1_name']); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                <button type="button" onclick="openDangerDowngradeModal(<?= $candidateJson; ?>)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold text-xs bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 hover:border-red-600 transition shadow-2xs cursor-pointer">
                                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                                    Downgrade
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-slate-400">
                                            <i class="fa-solid fa-user-slash text-3xl mb-2 text-slate-300"></i>
                                            <p class="font-medium text-xs">Belum ada mahasiswa dalam antrean bimbingan aktif.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="emptySearchNotice" class="hidden py-12 text-center text-slate-400 border-t border-slate-100">
                        <i class="fa-solid fa-magnifying-glass text-2xl mb-2 text-slate-300"></i>
                        <p class="font-medium text-xs">Tidak ditemukan mahasiswa dengan kriteria pencarian tersebut.</p>
                    </div>
                </div>

                <!-- Bottom Pagination Controls -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-medium pt-1 px-1">
                    <div>
                        Menampilkan data <strong id="pageStart" class="text-slate-800 font-bold">0</strong> - <strong id="pageEnd" class="text-slate-800 font-bold">0</strong> dari total <strong id="totalRecords" class="text-slate-800 font-bold">0</strong> mahasiswa
                    </div>
                    <div class="flex items-center gap-1 max-w-full overflow-x-auto scrollbar-none py-1" id="paginationControls">
                        <!-- Injected via JS -->
                    </div>
                </div>
            </div>

            <!-- SECTION TAB 2: RIWAYAT AUDIT LOG -->
            <div id="sectionTabLogAudit" class="hidden space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">Catatan Audit Log Downgrade &amp; Reset Tahap</h3>
                            <p class="text-xs text-slate-500">Daftar tindakan penurunan tahap mahasiswa yang pernah dieksekusi oleh Koordinator TA.</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-lg">
                            Total: <?= count($audit_logs); ?> Riwayat
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-200/80 text-slate-600 font-bold uppercase text-[11px] tracking-wider">
                                    <th class="py-3 px-4 w-12 text-center">No</th>
                                    <th class="py-3 px-4">Waktu Eksekusi</th>
                                    <th class="py-3 px-4">Mahasiswa (Target)</th>
                                    <th class="py-3 px-4">Perubahan Tahapan</th>
                                    <th class="py-3 px-4">Alasan Downgrade</th>
                                    <th class="py-3 px-4">Eksekutor (Koordinator TA)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                <?php if (!empty($audit_logs)): ?>
                                    <?php $no = 1; foreach ($audit_logs as $log): ?>
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="py-3 px-4 text-center font-mono text-slate-400"><?= $no++; ?></td>
                                            <td class="py-3 px-4 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                                <?= date('d M Y, H:i', strtotime($log['created_at'])); ?> WIB
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="font-bold text-slate-900"><?= htmlspecialchars($log['nama']); ?></div>
                                                <div class="font-mono text-slate-500 text-[11px]"><?= htmlspecialchars($log['nim']); ?></div>
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1.5 text-xs font-bold">
                                                    <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200"><?= htmlspecialchars($log['dari']); ?></span>
                                                    <i class="fa-solid fa-arrow-right text-[10px] text-slate-400"></i>
                                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200"><?= htmlspecialchars($log['ke']); ?></span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 max-w-xs">
                                                <p class="text-xs text-slate-600 line-clamp-2" title="<?= htmlspecialchars($log['alasan']); ?>">
                                                    <?= htmlspecialchars($log['alasan']); ?>
                                                </p>
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap">
                                                <div class="font-semibold text-slate-800"><?= htmlspecialchars($log['actor_name']); ?></div>
                                                <div class="text-[10px] text-slate-400 font-mono">NIP: <?= htmlspecialchars($log['actor_nip_nim'] ?: '-'); ?></div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-slate-400">
                                            <i class="fa-solid fa-clock-rotate-left text-3xl mb-2 text-slate-300"></i>
                                            <p class="font-medium text-xs">Belum ada riwayat aktivitas penurunan tahapan.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- EXCLUSIVE DANGER MODAL (WARNING MERAH & DISCLAIMER) -->
    <div id="dangerDowngradeModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden border border-red-200 flex flex-col max-h-[92vh] my-auto">
            
            <!-- Modal Header Merah Pekat (Pinned at Top) -->
            <div class="bg-gradient-to-r from-red-600 via-rose-600 to-red-700 p-4 sm:p-5 text-white shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center text-white text-lg shrink-0">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-extrabold tracking-tight">Konfirmasi Downgrade Tahapan TA</h3>
                            <p class="text-[11px] text-red-100 font-normal">Tindakan ini permanen &amp; menghapus seluruh berkas.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDangerDowngradeModal()" class="text-white/80 hover:text-white hover:bg-white/10 rounded-full w-8 h-8 flex items-center justify-center transition cursor-pointer shrink-0">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Content Form with Scrollable Body -->
            <form id="downgradeForm" onsubmit="submitDowngrade(event)" class="flex flex-col overflow-hidden flex-grow min-h-0">
                
                <!-- Scrollable Body Container -->
                <div class="overflow-y-auto p-4 sm:p-6 space-y-4 text-xs flex-grow">
                    <!-- Target Student Preview Card -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Identitas Mahasiswa</span>
                            <span id="modalCurrentStageBadge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                Sidang TA
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <div id="modalMhsNama" class="font-extrabold text-sm text-slate-900">Nama Mahasiswa</div>
                                <div id="modalMhsNim" class="font-mono text-slate-500 text-xs">1301210001</div>
                            </div>
                        </div>
                        <div class="pt-1 border-t border-slate-200/60">
                            <div class="text-[10px] text-slate-400 uppercase font-semibold">Judul Tugas Akhir:</div>
                            <div id="modalMhsJudul" class="text-xs text-slate-700 line-clamp-2 font-normal mt-0.5 leading-snug">
                                -
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Input NIM -->
                    <input type="hidden" id="modalInputNim" name="nim" value="">

                    <!-- Dropdown Target Stage -->
                    <div class="space-y-1.5">
                        <label for="targetStageSelect" class="block font-bold text-slate-800">
                            Pilih Tahapan Tujuan Penurunan: <span class="text-red-500">*</span>
                        </label>
                        <select id="targetStageSelect" name="target_stage" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-900 focus:outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-100 transition cursor-pointer">
                            <!-- Populated dynamically via JS -->
                        </select>
                        <p class="text-[11px] text-slate-400">Pilih tahapan bimbingan yang ingin dituju oleh mahasiswa.</p>
                    </div>

                    <!-- Red Warning Box (Konsekuensi Data) -->
                    <div class="rounded-2xl p-4 bg-red-50/80 border border-red-200 text-red-900 space-y-2">
                        <div class="flex items-center gap-2 font-extrabold text-red-700 text-xs uppercase tracking-wide">
                            <i class="fa-solid fa-radiation"></i> Konsekuensi Penghapusan Data:
                        </div>
                        <ul class="space-y-1.5 text-[11px] text-red-800 list-disc list-inside">
                            <li><strong>Nilai Bimbingan Dosen Terhapus:</strong> Semua nilai pembimbing, penguji, dan sidang akan di-reset total.</li>
                            <li><strong>Berkas &amp; Draf Terhapus:</strong> Seluruh naskah draft bab dan revisi yang telah diunggah akan di-drop permanen dari server.</li>
                            <li><strong>Tidak Ada Backup:</strong> Sistem tidak menyimpan cadangan data atas tindakan ini.</li>
                        </ul>
                    </div>

                    <!-- Alasan Downgrade Input (Wajib) -->
                    <div class="space-y-1.5">
                        <label for="alasanInput" class="block font-bold text-slate-800">
                            Alasan Penurunan / Reset Tahapan: <span class="text-red-500">*</span>
                        </label>
                        <textarea id="alasanInput" name="alasan" rows="3" required placeholder="Tuliskan alasan resmi penurunan tahapan (misal: Perubahan judul besar-besaran, berkas sidang tidak valid, instruksi pembimbing)..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-hidden focus:border-red-500 focus:ring-2 focus:ring-red-100 transition"></textarea>
                        <p class="text-[11px] text-slate-400">Catatan ini akan tersimpan permanen di riwayat audit log.</p>
                    </div>

                    <!-- Responsibility Disclaimer Checkbox -->
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" id="confirmCheckbox" onchange="toggleExecuteButton()" class="mt-0.5 rounded text-red-600 focus:ring-red-500 border-slate-300">
                            <span class="text-[11px] text-slate-700 leading-relaxed select-none">
                                <strong>Pernyataan Tanggung Jawab:</strong> Saya memahami bahwa tindakan ini bersifat <em>permanen</em> dan <em>tidak dapat dibatalkan</em>. Seluruh berkas dan nilai akan hilang, dan pihak pengelola tidak bertanggung jawab atas data yang terhapus.
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Actions Button (Pinned at Bottom) -->
                <div class="p-3.5 sm:p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3 shrink-0">
                    <button type="button" onclick="closeDangerDowngradeModal()" class="px-4 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-200/70 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnExecuteSubmit" disabled class="px-5 py-2.5 rounded-xl font-extrabold text-white bg-red-600 hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed transition shadow-md shadow-red-600/20 flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Ya, Hapus Data &amp; Downgrade</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Logika Client-Side -->
    <script>
        const EXECUTE_DOWNGRADE_URL = "<?= site_url('koordinatorta/ajax_execute_downgrade'); ?>";

        function switchMainTab(tabId) {
            const btnList = document.getElementById('btnMainTabList');
            const btnLog  = document.getElementById('btnMainTabLog');
            const secList = document.getElementById('sectionTabListMhs');
            const secLog  = document.getElementById('sectionTabLogAudit');

            if (tabId === 'tabListMhs') {
                btnList.classList.add('border-red-600', 'text-red-600');
                btnList.classList.remove('border-transparent', 'text-slate-500');
                btnLog.classList.remove('border-red-600', 'text-red-600');
                btnLog.classList.add('border-transparent', 'text-slate-500');

                secList.classList.remove('hidden');
                secLog.classList.add('hidden');
            } else {
                btnLog.classList.add('border-red-600', 'text-red-600');
                btnLog.classList.remove('border-transparent', 'text-slate-500');
                btnList.classList.remove('border-red-600', 'text-red-600');
                btnList.classList.add('border-transparent', 'text-slate-500');

                secLog.classList.remove('hidden');
                secList.classList.add('hidden');
            }
        }

        let currentActiveStageFilter = 'all';
        let currentPage = 1;
        let itemsPerPage = 10;
        let extraRowCounter = 0;

        // Multi-Search Criteria Definitions
        const SEARCH_CRITERIA = [
            { key: 'query', label: '🔍 Kata Kunci (Semua)', emoji: '🔍', isText: true, placeholder: 'Ketik kata kunci lalu tekan Enter atau klik Cari...' },
            { key: 'nama', label: '🏷️ Nama Mahasiswa', emoji: '🏷️', isText: true, placeholder: 'Ketik nama mahasiswa lalu tekan Enter atau klik Cari...' },
            { key: 'nim', label: '🆔 NIM Mahasiswa', emoji: '🆔', isText: true, placeholder: 'Ketik NIM mahasiswa lalu tekan Enter atau klik Cari...' },
            { key: 'judul', label: '📖 Judul Tugas Akhir', emoji: '📖', isText: true, placeholder: 'Ketik judul tugas akhir lalu tekan Enter atau klik Cari...' },
            { key: 'tahap', label: '🔄 Tahap Bimbingan', emoji: '🔄', isText: false },
            { key: 'dosen', label: '👤 Dosen Pembimbing/Penguji', emoji: '👤', isText: true, placeholder: 'Ketik nama dosen pembimbing/penguji...' }
        ];

        function isTextCategory(cat) {
            const item = SEARCH_CRITERIA.find(c => c.key === cat);
            return item ? item.isText : true;
        }

        function getCategoryEmoji(cat) {
            const item = SEARCH_CRITERIA.find(c => c.key === cat);
            return item ? item.emoji : '🔍';
        }

        function getPlaceholderForCategory(cat) {
            const item = SEARCH_CRITERIA.find(c => c.key === cat);
            return item && item.placeholder ? item.placeholder : 'Ketik kata kunci lalu tekan Enter atau klik Cari...';
        }

        function toggleCustomDropdown(type, event) {
            if (event) event.stopPropagation();
            const menu = document.getElementById('menu-filter-' + type);
            const arrow = document.getElementById('arrow-filter-' + type);
            const isHidden = menu ? menu.classList.contains('hidden') : true;

            closeAllCustomDropdowns();

            if (menu && isHidden) {
                menu.classList.remove('hidden');
                if (arrow) arrow.classList.add('rotate-180');

                const parentRow = menu.closest('.extra-filter-row');
                if (parentRow) parentRow.classList.add('open-dropdown');

                const parentContainer = menu.closest('.custom-dropdown-container');
                if (parentContainer) parentContainer.classList.add('open');
            }
        }

        function closeAllCustomDropdowns() {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.dropdown-arrow').forEach(a => a.classList.remove('rotate-180'));
            document.querySelectorAll('.extra-filter-row').forEach(r => r.classList.remove('open-dropdown'));
            document.querySelectorAll('.custom-dropdown-container').forEach(c => c.classList.remove('open'));
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('#extraRowsCard') && !e.target.closest('#standaloneAddBtn')) {
                closeAllCustomDropdowns();
            }
        });

        function selectMainCategory(cat, label, el) {
            closeAllCustomDropdowns();
            document.getElementById('mainCategorySelect').value = cat;
            document.getElementById('label-filter-main-cat').textContent = getCategoryEmoji(cat);

            const inputWrap = document.getElementById('mainValueContainer');
            const selectWrap = document.getElementById('mainCustomSelectWrap');
            const textInput = document.getElementById('mainSearchInput');

            if (isTextCategory(cat)) {
                inputWrap.classList.remove('hidden');
                selectWrap.classList.add('hidden');
                textInput.placeholder = getPlaceholderForCategory(cat);
                textInput.focus();
            } else {
                inputWrap.classList.add('hidden');
                selectWrap.classList.remove('hidden');
            }

            document.querySelectorAll('#menu-filter-main-cat .dropdown-item').forEach(item => {
                item.classList.remove('active', 'bg-orange-50', 'text-orange-600');
                item.classList.add('text-slate-700');
            });
            if (el) {
                el.classList.add('active', 'bg-orange-50', 'text-orange-600');
                el.classList.remove('text-slate-700');
            }
        }

        function selectMainOption(val, label, el) {
            closeAllCustomDropdowns();
            document.getElementById('mainCustomSelectVal').value = val;
            document.getElementById('label-filter-main-select').textContent = label;

            document.querySelectorAll('#menu-filter-main-select .dropdown-item').forEach(item => {
                item.classList.remove('active', 'bg-orange-50', 'text-orange-600');
                item.classList.add('text-slate-700');
            });
            if (el) {
                el.classList.add('active', 'bg-orange-50', 'text-orange-600');
                el.classList.remove('text-slate-700');
            }
        }

        function showExtraCard() {
            const extraCard = document.getElementById('extraRowsCard');
            if (extraCard) extraCard.style.display = 'block';
        }

        function hideExtraCard() {
            const extraCard = document.getElementById('extraRowsCard');
            if (extraCard) extraCard.style.display = 'none';
        }

        function isExtraCardVisible() {
            const extraCard = document.getElementById('extraRowsCard');
            return extraCard && (extraCard.style.display === 'block' || window.getComputedStyle(extraCard).display !== 'none');
        }

        function updateFilterBadge() {
            const totalRows = document.querySelectorAll('.extra-filter-row').length + 1;
            const badge = document.getElementById('filterCountBadge');
            if (badge) badge.innerText = `${totalRows}/4`;
        }

        function toggleOrAddFilterRow(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            closeAllCustomDropdowns();

            const extraRowsCount = document.querySelectorAll('.extra-filter-row').length;

            if (extraRowsCount > 0 && !isExtraCardVisible()) {
                showExtraCard();
                return;
            }

            if (extraRowsCount >= 3) {
                if (isExtraCardVisible()) {
                    hideExtraCard();
                } else {
                    showExtraCard();
                }
                return;
            }

            addAdditionalFilterRow(e);
        }

        function addAdditionalFilterRow(e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }

            const extraRowsCount = document.querySelectorAll('.extra-filter-row').length;
            if (extraRowsCount >= 3) {
                Swal.fire({
                    icon: 'info',
                    title: 'Maksimal 4 Filter',
                    text: 'Maksimal 4 kriteria filter pencarian yang dapat aktif secara bersamaan.',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            showExtraCard();

            extraRowCounter++;
            const rowId = extraRowCounter;

            const allCriteria = ['query', 'nama', 'nim', 'judul', 'tahap', 'dosen'];
            const mainCat = document.getElementById('mainCategorySelect') ? document.getElementById('mainCategorySelect').value : 'query';
            const usedCriteria = [mainCat];
            document.querySelectorAll('.extra-cat-select').forEach(el => usedCriteria.push(el.value));

            const defaultCrit = allCriteria.find(c => !usedCriteria.includes(c)) || 'nama';

            const container = document.getElementById('additionalFilterRowsContainer');
            const rowDiv = document.createElement('div');
            rowDiv.className = 'extra-filter-row unified-search-pill';
            rowDiv.id = `extraRow_${rowId}`;

            let menuItemsHtml = '';
            SEARCH_CRITERIA.forEach(crit => {
                const isActive = (crit.key === defaultCrit);
                menuItemsHtml += `
                    <div onclick="selectExtraCategory(${rowId}, '${crit.key}', '${crit.label}', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium ${isActive ? 'active bg-orange-50 text-orange-600' : 'text-slate-700 hover:bg-orange-50 hover:text-orange-600'}">
                        <span>${crit.label}</span>
                        <i class="fa-solid fa-check text-xs check-icon ${isActive ? '' : 'hidden'}"></i>
                    </div>
                `;
            });

            rowDiv.innerHTML = `
                <!-- Extra Category Dropdown -->
                <div class="relative custom-dropdown-container shrink-0">
                    <input type="hidden" id="extraCatSelect_${rowId}" class="extra-cat-select" value="${defaultCrit}">
                    <button type="button" onclick="toggleCustomDropdown('extra-cat-${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-0.5 hover:text-orange-600 focus:outline-hidden" title="Kategori Pencarian">
                        <span id="label-filter-extra-cat-${rowId}" class="text-sm sm:text-base leading-none block">${getCategoryEmoji(defaultCrit)}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-extra-cat-${rowId}"></i>
                    </button>
                    <div id="menu-filter-extra-cat-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                        ${menuItemsHtml}
                    </div>
                </div>

                <div class="unified-divider"></div>

                <!-- Extra Text Input Container (NO auto-filter on keyup! only Enter / Cari!) -->
                <div id="extraValueContainer_${rowId}" class="${isTextCategory(defaultCrit) ? 'flex-1 flex items-center relative min-w-0' : 'hidden flex-1 items-center relative min-w-0'}">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                    <input type="text" id="extraInput_${rowId}" onkeydown="if(event.key==='Enter') executeSearch()" placeholder="${getPlaceholderForCategory(defaultCrit)}" class="w-full text-xs font-medium bg-transparent border-none focus:outline-hidden text-slate-800 placeholder:text-slate-400">
                </div>

                <!-- Extra Custom Select Container (Tahap) -->
                <div id="extraCustomSelectWrap_${rowId}" class="${!isTextCategory(defaultCrit) ? 'flex-1 relative custom-dropdown-container min-w-0' : 'hidden flex-1 relative custom-dropdown-container min-w-0'}">
                    <input type="hidden" id="extraValueVal_${rowId}" value="all">
                    <button type="button" onclick="toggleCustomDropdown('extra-val-${rowId}', event)" class="w-full py-1 text-xs font-semibold text-slate-800 flex items-center justify-between cursor-pointer focus:outline-hidden">
                        <span id="label-filter-extra-val-${rowId}" class="flex items-center gap-1.5 truncate">Semua Tahap</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-filter-extra-val-${rowId}"></i>
                    </button>
                    <div id="menu-filter-extra-val-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                        <div onclick="selectExtraOption(${rowId}, 'all', 'Semua Tahap', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium active bg-orange-50 text-orange-600"><span>Semua Tahap</span></div>
                        <div onclick="selectExtraOption(${rowId}, 'sidang', 'Tahap Sidang TA / Lulus', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>Tahap Sidang TA / Lulus</span></div>
                        <div onclick="selectExtraOption(${rowId}, 'preview3', 'Tahap Preview 3', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>Tahap Preview 3</span></div>
                        <div onclick="selectExtraOption(${rowId}, 'preview2', 'Tahap Preview 2', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>Tahap Preview 2</span></div>
                        <div onclick="selectExtraOption(${rowId}, 'preview1', 'Tahap Preview 1', this)" class="dropdown-item px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-600"><span>Tahap Preview 1</span></div>
                    </div>
                </div>

                <!-- Search Button inside extra pill -->
                <button type="button" onclick="executeSearch()" class="px-3 py-1.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-500 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1 transition cursor-pointer active:scale-95 shrink-0 ml-1" title="Klik untuk melakukan pencarian">
                    <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                    <span>Cari</span>
                </button>

                <!-- Remove Row Button inside extra pill -->
                <button type="button" onclick="removeExtraFilterRow(${rowId}, event)" class="btn-remove-row ml-1" title="Hapus Filter Ini">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            `;

            container.appendChild(rowDiv);
            updateFilterBadge();
        }

        function removeExtraFilterRow(rowId, e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            const row = document.getElementById(`extraRow_${rowId}`);
            if (row) row.remove();

            const extraRowsCount = document.querySelectorAll('.extra-filter-row').length;
            if (extraRowsCount === 0) {
                hideExtraCard();
            }
            updateFilterBadge();
            executeSearch();
        }

        function selectExtraCategory(rowId, cat, label, el) {
            closeAllCustomDropdowns();
            const catSelect = document.getElementById(`extraCatSelect_${rowId}`);
            if (catSelect) catSelect.value = cat;

            const iconSpan = document.getElementById(`label-filter-extra-cat-${rowId}`);
            if (iconSpan) iconSpan.textContent = getCategoryEmoji(cat);

            const inputWrap = document.getElementById(`extraValueContainer_${rowId}`);
            const selectWrap = document.getElementById(`extraCustomSelectWrap_${rowId}`);
            const textInput = document.getElementById(`extraInput_${rowId}`);

            if (isTextCategory(cat)) {
                if (inputWrap) inputWrap.classList.remove('hidden');
                if (selectWrap) selectWrap.classList.add('hidden');
                if (textInput) {
                    textInput.placeholder = getPlaceholderForCategory(cat);
                    textInput.focus();
                }
            } else {
                if (inputWrap) inputWrap.classList.add('hidden');
                if (selectWrap) selectWrap.classList.remove('hidden');
            }

            const menu = document.getElementById(`menu-filter-extra-cat-${rowId}`);
            if (menu) {
                menu.querySelectorAll('.dropdown-item').forEach(item => {
                    item.classList.remove('active', 'bg-orange-50', 'text-orange-600');
                    item.classList.add('text-slate-700');
                    const check = item.querySelector('.check-icon');
                    if (check) check.classList.add('hidden');
                });
                if (el) {
                    el.classList.add('active', 'bg-orange-50', 'text-orange-600');
                    el.classList.remove('text-slate-700');
                    const check = el.querySelector('.check-icon');
                    if (check) check.classList.remove('hidden');
                }
            }
        }

        function selectExtraOption(rowId, val, label, el) {
            closeAllCustomDropdowns();
            const valInput = document.getElementById(`extraValueVal_${rowId}`);
            if (valInput) valInput.value = val;

            const labelSpan = document.getElementById(`label-filter-extra-val-${rowId}`);
            if (labelSpan) labelSpan.textContent = label;

            const menu = document.getElementById(`menu-filter-extra-val-${rowId}`);
            if (menu) {
                menu.querySelectorAll('.dropdown-item').forEach(item => {
                    item.classList.remove('active', 'bg-orange-50', 'text-orange-600');
                    item.classList.add('text-slate-700');
                });
                if (el) {
                    el.classList.add('active', 'bg-orange-50', 'text-orange-600');
                    el.classList.remove('text-slate-700');
                }
            }
        }

        function resetMultiSearch() {
            closeAllCustomDropdowns();
            // Reset main search
            selectMainCategory('query', '🔍 Kata Kunci (Semua)');
            const mainInput = document.getElementById('mainSearchInput');
            if (mainInput) mainInput.value = '';
            const mainSelect = document.getElementById('mainCustomSelectVal');
            if (mainSelect) mainSelect.value = 'all';
            const mainLabel = document.getElementById('label-filter-main-select');
            if (mainLabel) mainLabel.textContent = 'Semua Tahap';

            // Remove all extra rows
            const container = document.getElementById('additionalFilterRowsContainer');
            if (container) container.innerHTML = '';
            hideExtraCard();
            updateFilterBadge();

            // Reset stage pill
            setStageFilter('all', document.querySelector('.stage-pill[onclick*="all"]'));
        }

        function executeSearch() {
            closeAllCustomDropdowns();
            filterTableRows(true);
        }

        function changePerPage(val) {
            itemsPerPage = parseInt(val) || 10;
            filterTableRows(true);
        }

        function setStageFilter(stage, btnEl) {
            currentActiveStageFilter = stage;
            document.querySelectorAll('.stage-pill').forEach(btn => {
                btn.classList.remove('active', 'bg-slate-900', 'text-white');
                btn.classList.add('bg-slate-100', 'text-slate-700');
            });
            if (btnEl) {
                btnEl.classList.remove('bg-slate-100', 'text-slate-700');
                btnEl.classList.add('active', 'bg-slate-900', 'text-white');
            }

            filterTableRows(true);
        }

        function goToPage(page) {
            currentPage = page;
            filterTableRows(false);
            const tableEl = document.getElementById('sectionTabListMhs');
            if (tableEl) {
                tableEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function renderPagination(totalPages) {
            const container = document.getElementById('paginationControls');
            if (!container) return;

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = `
                <button type="button" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="w-8 h-8 rounded-xl flex items-center justify-center border border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
            `;

            let pages = [];
            if (totalPages <= 7) {
                for (let i = 1; i <= totalPages; i++) pages.push(i);
            } else {
                pages.push(1);
                if (currentPage > 3) pages.push('...');
                let start = Math.max(2, currentPage - 1);
                let end = Math.min(totalPages - 1, currentPage + 1);
                for (let i = start; i <= end; i++) {
                    if (!pages.includes(i)) pages.push(i);
                }
                if (currentPage < totalPages - 2) pages.push('...');
                if (!pages.includes(totalPages)) pages.push(totalPages);
            }

            pages.forEach(p => {
                if (p === '...') {
                    html += `<span class="w-8 h-8 flex items-center justify-center text-slate-400 font-bold text-xs">...</span>`;
                } else {
                    const isActive = (p === currentPage);
                    html += `
                        <button type="button" onclick="goToPage(${p})" class="w-8 h-8 rounded-xl flex items-center justify-center border text-xs font-bold transition cursor-pointer shadow-2xs ${isActive ? 'bg-red-600 border-red-600 text-white shadow-xs' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-100'}">
                            ${p}
                        </button>
                    `;
                }
            });

            html += `
                <button type="button" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="w-8 h-8 rounded-xl flex items-center justify-center border border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            `;

            container.innerHTML = html;
        }

        function filterTableRows(resetPage = true) {
            if (resetPage) {
                currentPage = 1;
            }

            // Sync perPage selectors
            const topSelect = document.getElementById('selectPerPageTop');
            const bottomSelect = document.getElementById('perPageSelect');
            if (topSelect && topSelect.value != itemsPerPage) topSelect.value = itemsPerPage;
            if (bottomSelect && bottomSelect.value != itemsPerPage) bottomSelect.value = itemsPerPage;

            // 1. Gather all active filters
            const activeFilters = [];

            // Main filter
            const mainCat = document.getElementById('mainCategorySelect')?.value || 'query';
            let mainVal = '';
            if (isTextCategory(mainCat)) {
                mainVal = (document.getElementById('mainSearchInput')?.value || '').toLowerCase().trim();
            } else {
                mainVal = (document.getElementById('mainCustomSelectVal')?.value || 'all').toLowerCase().trim();
            }
            if (mainVal !== '' && mainVal !== 'all') {
                activeFilters.push({ cat: mainCat, val: mainVal });
            }

            // Extra filters
            document.querySelectorAll('.extra-filter-row').forEach(row => {
                const catInput = row.querySelector('.extra-cat-select');
                if (!catInput) return;
                const cat = catInput.value;
                let val = '';
                if (isTextCategory(cat)) {
                    val = (row.querySelector('input[type="text"]')?.value || '').toLowerCase().trim();
                } else {
                    val = (row.querySelector('input[type="hidden"][id^="extraValueVal_"]')?.value || 'all').toLowerCase().trim();
                }
                if (val !== '' && val !== 'all') {
                    activeFilters.push({ cat: cat, val: val });
                }
            });

            const rows = Array.from(document.querySelectorAll('.candidate-row'));

            // 2. Filter matching rows
            const matchedRows = rows.filter(row => {
                const stage = (row.getAttribute('data-stage') || '').toLowerCase();
                const nama = (row.getAttribute('data-nama') || '').toLowerCase();
                const nim = (row.getAttribute('data-nim') || '').toLowerCase();
                const judul = (row.getAttribute('data-judul') || '').toLowerCase();
                const dosen = (row.getAttribute('data-dosen') || '').toLowerCase();
                const searchData = (row.getAttribute('data-search') || '').toLowerCase();

                // Check quick stage filter pill
                if (currentActiveStageFilter !== 'all') {
                    if (currentActiveStageFilter === 'sidang' && !(stage === 'sidang' || stage === 'lulus')) {
                        return false;
                    } else if (currentActiveStageFilter !== 'sidang' && stage !== currentActiveStageFilter) {
                        return false;
                    }
                }

                // Check each active filter (AND logic)
                for (let f of activeFilters) {
                    if (f.cat === 'query') {
                        if (!searchData.includes(f.val)) return false;
                    } else if (f.cat === 'nama') {
                        if (!nama.includes(f.val)) return false;
                    } else if (f.cat === 'nim') {
                        if (!nim.includes(f.val)) return false;
                    } else if (f.cat === 'judul') {
                        if (!judul.includes(f.val)) return false;
                    } else if (f.cat === 'dosen') {
                        if (!dosen.includes(f.val)) return false;
                    } else if (f.cat === 'tahap') {
                        if (f.val === 'sidang' && !(stage === 'sidang' || stage === 'lulus')) return false;
                        else if (f.val !== 'sidang' && stage !== f.val) return false;
                    }
                }

                return true;
            });

            const totalRecords = matchedRows.length;
            const totalPages = Math.ceil(totalRecords / itemsPerPage) || 1;

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;

            // 3. Hide all rows
            rows.forEach(r => {
                r.style.display = 'none';
            });

            // 4. Show slice for current page and update numbering
            matchedRows.slice(startIndex, endIndex).forEach((r, idx) => {
                r.style.display = '';
                const noCell = r.querySelector('td:first-child');
                if (noCell) {
                    noCell.textContent = startIndex + idx + 1;
                }
            });

            // 5. Update count stats text
            const startNum = totalRecords === 0 ? 0 : startIndex + 1;
            const endNum = Math.min(endIndex, totalRecords);
            const pageStartEl = document.getElementById('pageStart');
            const pageEndEl = document.getElementById('pageEnd');
            const totalRecordsEl = document.getElementById('totalRecords');
            const toolbarTotalEl = document.getElementById('toolbarTotalCount');

            if (pageStartEl) pageStartEl.textContent = startNum;
            if (pageEndEl) pageEndEl.textContent = endNum;
            if (totalRecordsEl) totalRecordsEl.textContent = totalRecords;
            if (toolbarTotalEl) toolbarTotalEl.textContent = totalRecords;

            // 6. Render Pagination Navigation
            renderPagination(totalPages);

            // 7. Handle empty search notice
            const notice = document.getElementById('emptySearchNotice');
            if (notice) {
                if (totalRecords === 0 && rows.length > 0) {
                    notice.classList.remove('hidden');
                } else {
                    notice.classList.add('hidden');
                }
            }
        }

        // Initialize pagination immediately and on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            filterTableRows(true);
        });
        filterTableRows(true);

        function openDangerDowngradeModal(candidate) {
            if (!candidate) return;

            document.getElementById('modalInputNim').value = candidate.nim;
            document.getElementById('modalMhsNama').textContent = candidate.nama || '-';
            document.getElementById('modalMhsNim').textContent = candidate.nim || '-';
            document.getElementById('modalMhsJudul').textContent = candidate.judul || '-';

            const badgeEl = document.getElementById('modalCurrentStageBadge');
            badgeEl.textContent = candidate.stageLabel || 'Tahap Aktif';

            // Reset inputs
            document.getElementById('alasanInput').value = '';
            document.getElementById('confirmCheckbox').checked = false;
            document.getElementById('btnExecuteSubmit').disabled = true;

            // Populate Target Stages Dropdown
            const selectEl = document.getElementById('targetStageSelect');
            selectEl.innerHTML = '';

            const targets = candidate.allowed_targets || [];
            if (targets.length === 0) {
                selectEl.innerHTML = '<option value="reset_total">Reset Total (Kembali ke Pendaftaran Awal)</option>';
            } else {
                targets.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.key;
                    opt.textContent = t.label;
                    selectEl.appendChild(opt);
                });
            }

            document.getElementById('dangerDowngradeModal').classList.remove('hidden');
        }

        function closeDangerDowngradeModal() {
            document.getElementById('dangerDowngradeModal').classList.add('hidden');
        }

        function toggleExecuteButton() {
            const isChecked = document.getElementById('confirmCheckbox').checked;
            document.getElementById('btnExecuteSubmit').disabled = !isChecked;
        }

        function submitDowngrade(e) {
            e.preventDefault();

            const nim = document.getElementById('modalInputNim').value;
            const targetStage = document.getElementById('targetStageSelect').value;
            const alasan = document.getElementById('alasanInput').value.trim();
            const isConfirmed = document.getElementById('confirmCheckbox').checked;

            if (!nim || !targetStage) {
                Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Target mahasiswa dan tahapan wajib dipilih.' });
                return;
            }

            if (!alasan || alasan.length < 5) {
                Swal.fire({ icon: 'warning', title: 'Alasan Wajib Diisi', text: 'Mohon cantumkan alasan penurunan tahapan minimal 5 karakter.' });
                return;
            }

            if (!isConfirmed) {
                Swal.fire({ icon: 'warning', title: 'Persetujuan Wajib', text: 'Anda wajib menyetujui pernyataan konsekuensi penghapusan permanen data.' });
                return;
            }

            const targetSelectText = document.getElementById('targetStageSelect').selectedOptions[0]?.textContent || targetStage;

            // SweetAlert Final Confirm
            Swal.fire({
                title: 'Konfirmasi Terakhir!',
                html: `<div class="text-left text-xs space-y-2">
                    <p class="font-bold text-red-600">Apakah Anda benar-benar yakin ingin mengeksekusi tindakan ini?</p>
                    <p>Status mahasiswa <strong>NIM ${nim}</strong> akan diturunkan ke <strong>${targetSelectText}</strong>.</p>
                    <p class="text-slate-500">Seluruh nilai dan berkas revisi terkait akan <strong>DIHAPUS TOTAL</strong> dari server tanpa backup.</p>
                </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Eksekusi Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const submitBtn = document.getElementById('btnExecuteSubmit');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';

                    $.ajax({
                        url: EXECUTE_DOWNGRADE_URL,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            nim: nim,
                            target_stage: targetStage,
                            alasan: alasan,
                            confirm: 1
                        },
                        success: function(resp) {
                            if (resp && resp.status) {
                                closeDangerDowngradeModal();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Di-downgrade',
                                    text: resp.message || 'Status mahasiswa berhasil diturunkan.',
                                    confirmButtonColor: '#ea580c'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = '<i class="fa-solid fa-trash-can"></i> Ya, Hapus Data &amp; Downgrade';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Eksekusi',
                                    text: resp.message || 'Terjadi kesalahan saat memproses downgrade.'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fa-solid fa-trash-can"></i> Ya, Hapus Data &amp; Downgrade';
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Sistem',
                                text: 'Gagal menghubungi server. Silakan coba kembali.'
                            });
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
