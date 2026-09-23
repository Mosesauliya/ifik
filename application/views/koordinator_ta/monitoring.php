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
            display: flex;
            align-items: center;
            gap: 2px;
            width: 100%;
            max-width: 140px;
        }
        .micro-step-dot {
            flex: 1;
            height: 5px;
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
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-24">

    <!-- Include Curved Animated Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Page Content Wrapper -->
    <div id="mainPageContent" class="page-wrapper-for-sidebar">

        <!-- Top Navigation Header -->
        <header class="sticky top-0 z-40 glass-header px-4 sm:px-8 py-3 sm:py-4 mb-6 sm:mb-8">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-3 sm:gap-4 pl-12 sm:pl-14 md:pl-16">
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
                    <a href="<?= site_url('koordinatorta'); ?>" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Dashboard Koor</span>
                    </a>
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

                foreach ($pesertaList as $p) {
                    $stKey = $p['stage_key'] ?? '';
                    if ($stKey === 'dosen_wali')     $countWali++;
                    elseif ($stKey === 'admin_layanan') $countAdmin++;
                    elseif ($stKey === 'koordinator_ta')$countKoor++;
                    elseif ($stKey === 'ketua_kk')      $countKk++;
                    elseif ($stKey === 'preview1')      $countP1++;
                    elseif ($stKey === 'preview2')      $countP2++;
                    elseif ($stKey === 'preview3')      $countP3++;
                    elseif ($stKey === 'sidang')        $countSidang++;
                    elseif ($stKey === 'lulus')         $countLulus++;
                }

                $totalPendaftaran = $countWali + $countAdmin + $countKoor + $countKk;
                $totalBimbingan   = $countP1 + $countP2 + $countP3;
                $totalSidangLulus = $countSidang + $countLulus;
            ?>

            <!-- Top Stat Summary Cards (4 Columns Grid) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- Card 1: Total Peserta -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-sm relative overflow-hidden group hover:border-orange-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Peserta TA</span>
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mt-2"><?= $totalPeserta; ?></h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Seluruh mahasiswa aktif TA</p>
                </div>

                <!-- Card 2: Tahap Pendaftaran -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-sm relative overflow-hidden group hover:border-amber-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Proses Pendaftaran</span>
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-amber-600 mt-2"><?= $totalPendaftaran; ?></h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Wali (<?= $countWali ?>), LAA (<?= $countAdmin ?>), Koor (<?= $countKoor ?>), KK (<?= $countKk ?>)</p>
                </div>

                <!-- Card 3: Bimbingan & Preview -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-sm relative overflow-hidden group hover:border-indigo-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Bimbingan &amp; Evaluasi</span>
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-indigo-600 mt-2"><?= $totalBimbingan; ?></h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">P1 (<?= $countP1 ?>), P2 (<?= $countP2 ?>), P3 (<?= $countP3 ?>)</p>
                </div>

                <!-- Card 4: Sidang & Lulus -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-sm relative overflow-hidden group hover:border-emerald-300 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sidang &amp; Lulus</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-emerald-600 mt-2"><?= $totalSidangLulus; ?></h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Sidang (<?= $countSidang ?>), Lulus (<?= $countLulus ?>)</p>
                </div>
            </div>

            <!-- Milestone Funnel Filter Chips Container (Scrollable Horizontal) -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                        <i class="fa-solid fa-filter text-orange-500"></i> Filter Tahap Progres:
                    </span>
                    <span class="text-[11px] text-slate-400">Klik salah satu tahap untuk memfilter data</span>
                </div>

                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
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
            </div>

            <!-- Search Bar & Controls -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="unified-search-pill flex-1">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                    <input type="text" id="searchInput" oninput="renderTable()" placeholder="Cari nama, NIM, usulan judul, dosen pembimbing, penguji, atau dosen wali..." class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                </div>

                <div class="flex items-center gap-2.5 shrink-0 justify-between sm:justify-end">
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-slate-50 border border-slate-200 px-3 h-10 rounded-xl">
                        <span class="font-medium">Tampilkan:</span>
                        <select id="pageSizeSelect" onchange="changePageSize(this.value)" class="h-6 px-1.5 text-xs font-bold bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none cursor-pointer">
                            <option value="10">10</option>
                            <option value="20" selected>20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="font-medium">baris</span>
                    </div>

                    <button type="button" onclick="resetFilters()" class="h-10 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition flex items-center gap-1.5 cursor-pointer" title="Reset Pencarian & Filter">
                        <i class="fa-solid fa-rotate-right text-xs"></i>
                        <span class="hidden sm:inline">Reset</span>
                    </button>
                </div>
            </div>

            <!-- Table View Desktop with Rotating Conic-Gradient Border -->
            <div class="table-rotating-border-wrap hidden sm:block">
                <div class="table-rotating-border-inner overflow-hidden">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-slate-700 font-bold border-b border-slate-200/90 uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-3.5 px-3 w-12 text-center">No</th>
                                <th class="py-3.5 px-3 w-48">Mahasiswa</th>
                                <th class="py-3.5 px-3 min-w-[220px]">Usulan Judul Tugas Akhir</th>
                                <th class="py-3.5 px-3 w-48">Tim Pembimbing &amp; Penguji</th>
                                <th class="py-3.5 px-3 w-36 text-center">Milestone Pipeline</th>
                                <th class="py-3.5 px-3 w-40 text-center">Tahap Saat Ini</th>
                                <th class="py-3.5 px-3 w-28 text-center">Aksi</th>
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

    <!-- Pass backend data to JavaScript -->
    <script>
        const RAW_PESERTA_DATA = <?= json_encode($pesertaList, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        let currentStageFilter = 'all';
        let currentPage = 1;
        let pageSize = 20;

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
                <div class="inline-flex flex-col items-center">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-bold border ${b.bg} shadow-2xs">
                        <i class="fa-solid ${b.icon} text-[10px]"></i>
                        <span>${b.text}</span>
                    </span>
                    ${stageDesc ? `<span class="text-[9.5px] text-slate-400 mt-1 line-clamp-1 text-center max-w-[150px]" title="${stageDesc}">${stageDesc}</span>` : ''}
                </div>
            `;
        }

        function getMicroStepperHTML(stageIndex) {
            // 9 Total Steps: 1:Wali, 2:LAA, 3:Koor, 4:KK, 5:P1, 6:P2, 7:P3, 8:Sidang, 9:Lulus
            let html = '<div class="micro-stepper" title="Step ' + stageIndex + ' dari 9">';
            for (let i = 1; i <= 9; i++) {
                if (i < stageIndex) {
                    html += '<div class="micro-step-dot completed" title="Tahap ' + i + ' Selesai"></div>';
                } else if (i === stageIndex) {
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
            if (el) el.classList.add('active');
            renderTable();
        }

        function changePageSize(size) {
            pageSize = parseInt(size, 10);
            currentPage = 1;
            renderTable();
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            filterByStage('all', document.querySelector('.stage-chip[data-stage="all"]'));
        }

        function renderTable() {
            const query = (document.getElementById('searchInput').value || '').trim().toLowerCase();

            // Filter data
            const filtered = RAW_PESERTA_DATA.filter(item => {
                // Filter by stage
                if (currentStageFilter !== 'all' && item.stage_key !== currentStageFilter) {
                    return false;
                }

                // Filter by search query
                if (query) {
                    const matchNim = (item.nim || '').toLowerCase().includes(query);
                    const matchNama = (item.nama || item.name || '').toLowerCase().includes(query);
                    const matchJudul = (item.judul_1 || '').toLowerCase().includes(query);
                    const matchP1 = (item.nama_pembimbing_1 || item.pembimbing_1 || '').toLowerCase().includes(query);
                    const matchP2 = (item.nama_pembimbing_2 || item.pembimbing_2 || '').toLowerCase().includes(query);
                    const matchU1 = (item.nama_penguji_1 || item.penguji_1 || '').toLowerCase().includes(query);
                    const matchU2 = (item.nama_penguji_2 || item.penguji_2 || '').toLowerCase().includes(query);
                    const matchWali = (item.nama_dosen_wali || item.dosen_wali || '').toLowerCase().includes(query);
                    const matchStage = (item.progres_stage || '').toLowerCase().includes(query);

                    return matchNim || matchNama || matchJudul || matchP1 || matchP2 || matchU1 || matchU2 || matchWali || matchStage;
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
                            <p class="text-xs text-slate-400 mt-1">Coba ganti filter tahap atau kata kunci pencarian.</p>
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
                    const wali = item.nama_dosen_wali || item.dosen_wali;

                    rowsHtml += `
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-3 text-center text-slate-400 font-bold">${rowNo}</td>
                            <td class="py-3.5 px-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-700 font-black text-xs flex items-center justify-center shrink-0">
                                        ${initial}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 truncate" title="${item.nama || item.name || ''}">${item.nama || item.name || 'Mahasiswa'}</div>
                                        <div class="text-[11px] font-mono font-semibold text-slate-500">${item.nim || '-'} • <span class="text-orange-600 font-sans font-bold">${item.prodi || 'Informatika'}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="font-semibold text-slate-800 line-clamp-2 leading-snug" title="${item.judul_1 || '-'}">
                                    ${item.judul_1 || '<span class="text-slate-400 italic">Belum mengisi judul</span>'}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1">
                                    Peminatan: <strong class="text-slate-600">${item.peminatan || item.konsentrasi_dkv || 'Informatika'}</strong>
                                </div>
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="space-y-0.5 text-[11px]">
                                    <div class="truncate text-slate-700" title="Pembimbing 1: ${p1 || 'Belum diplot'}">
                                        <span class="text-[10px] font-bold text-orange-600">P1:</span> ${p1 ? p1 : '<span class="text-slate-400 italic">Belum diplot</span>'}
                                    </div>
                                    <div class="truncate text-slate-700" title="Pembimbing 2: ${p2 || 'Belum diplot'}">
                                        <span class="text-[10px] font-bold text-amber-600">P2:</span> ${p2 ? p2 : '<span class="text-slate-400 italic">Belum diplot</span>'}
                                    </div>
                                    ${(u1 || u2) ? `
                                        <div class="truncate text-slate-700 pt-0.5 border-t border-slate-100" title="Penguji: ${u1 || ''} / ${u2 || ''}">
                                            <span class="text-[10px] font-bold text-indigo-600">U1/U2:</span> ${u1 ? u1 : '-'} / ${u2 ? u2 : '-'}
                                        </div>
                                    ` : ''}
                                </div>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    ${getMicroStepperHTML(item.stage_index)}
                                    <span class="text-[10px] font-bold text-slate-500">Tahap ${item.stage_index} / 9</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                ${getStageBadgeHTML(item.stage_key, item.progres_stage, item.stage_desc)}
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <a href="<?= site_url('koordinatorta/detail_mahasiswa/'); ?>${item.nim}" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-500 text-white rounded-xl font-bold text-xs shadow-xs transition active:scale-95">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
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

                            <div class="p-3 bg-slate-50 rounded-xl space-y-1.5 text-[11px]">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Pembimbing 1:</span>
                                    <strong class="text-slate-800 truncate max-w-[180px]">${p1 ? p1 : 'Belum diplot'}</strong>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Pembimbing 2:</span>
                                    <strong class="text-slate-800 truncate max-w-[180px]">${p2 ? p2 : 'Belum diplot'}</strong>
                                </div>
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

        // Init render on page load
        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
        });
    </script>
</body>
</html>
