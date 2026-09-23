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

            <!-- Search Bar & Combined Filters -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="unified-search-pill flex-1">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                    <input type="text" id="searchInput" oninput="renderTable()" placeholder="Cari nama, NIM, usulan judul, dosen pembimbing, penguji, atau dosen wali..." class="w-full text-xs font-medium bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0 justify-between sm:justify-end">
                    <!-- Filter Status Berkas -->
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-slate-50 border border-slate-200 px-3 h-10 rounded-xl">
                        <i class="fa-solid fa-folder-closed text-slate-400 text-xs mr-1"></i>
                        <span class="font-medium hidden md:inline">Berkas:</span>
                        <select id="berkasFilterSelect" onchange="filterByBerkas(this.value)" class="h-7 px-1.5 text-xs font-bold bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none cursor-pointer">
                            <option value="all">Semua Status Berkas</option>
                            <option value="lengkap">🟢 Lengkap (<?= $countBerkasLengkap ?>)</option>
                            <option value="revisi">🔴 Ada Revisi (<?= $countBerkasRevisi ?>)</option>
                            <option value="proses">🟡 Sedang Verifikasi (<?= $countBerkasProses ?>)</option>
                            <option value="kosong">⚪ Belum Unggah (<?= $countBerkasKosong ?>)</option>
                        </select>
                    </div>

                    <!-- Filter Page Size -->
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-slate-50 border border-slate-200 px-3 h-10 rounded-xl">
                        <span class="font-medium">Tampilkan:</span>
                        <select id="pageSizeSelect" onchange="changePageSize(this.value)" class="h-7 px-1.5 text-xs font-bold bg-white border border-slate-300 rounded-lg text-slate-800 focus:outline-none cursor-pointer">
                            <option value="10">10</option>
                            <option value="20" selected>20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="font-medium hidden md:inline">baris</span>
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
        let currentBerkasFilter = 'all';
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
            if (el) el.classList.add('active');
            renderTable();
        }

        function filterByBerkas(code) {
            currentBerkasFilter = code;
            currentPage = 1;
            renderTable();
        }

        function changePageSize(size) {
            pageSize = parseInt(size, 10);
            currentPage = 1;
            renderTable();
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('berkasFilterSelect').value = 'all';
            currentBerkasFilter = 'all';
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

                // Filter by berkas status
                if (currentBerkasFilter !== 'all' && item.berkas_status_code !== currentBerkasFilter) {
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
                    const matchBerkas = (item.berkas_status_label || '').toLowerCase().includes(query);

                    return matchNim || matchNama || matchJudul || matchP1 || matchP2 || matchU1 || matchU2 || matchWali || matchStage || matchBerkas;
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

        // Init render on page load
        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
        });
    </script>
</body>
</html>
