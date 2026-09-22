<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pendaftaran Sidang — Admin LAA'; ?> - IFIK</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed', 100: '#ffedd5', 500: '#f97316',
                            600: '#ea580c', 700: '#c2410c', 900: '#7c2d12'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Icons & Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .unified-search-pill {
            display: flex; align-items: center;
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px);
            border: 1.5px solid #e2e8f0; border-radius: 16px;
            padding: 3px 14px; height: 46px;
            transition: border-color 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important; background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.14), 0 10px 25px -5px rgba(234, 88, 12, 0.12) !important;
        }
        .unified-divider { width: 1px; height: 22px; background: #e2e8f0; margin: 0 8px; }
        .autocomplete-box {
            max-height: 320px; overflow-y: auto; border-radius: 18px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.2), 0 8px 24px -4px rgba(234, 88, 12, 0.15);
        }
        .autocomplete-item-row { padding: 10px 16px; transition: all 0.18s ease; cursor: pointer; }
        .autocomplete-item-row:hover, .autocomplete-item-row.active-nav { background-color: #fff7ed; color: #ea580c; }
        .autocomplete-item-row mark { background: #fed7aa; color: #c2410c; font-weight: 800; border-radius: 4px; padding: 0 3px; }

        .btn-standalone-add {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 16px;
            padding: 6px 14px; height: 46px; font-size: 0.8rem; font-weight: 700; color: #ea580c;
            cursor: pointer; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); white-space: nowrap;
        }
        .btn-standalone-add:hover { background: #ffedd5; border-color: #fdba74; transform: scale(1.02); }
        .badge-standalone-count {
            background: #ea580c; color: #ffffff; font-size: 0.72rem; font-weight: 800; padding: 1.5px 8px; border-radius: 99px;
        }
        .btn-remove-row {
            display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px;
            background: #fff1f2; border: 1.5px solid #fecdd3; border-radius: 14px; color: #e11d48;
            cursor: pointer; transition: all 0.2s ease; flex-shrink: 0;
        }
        .btn-remove-row:hover { background: #ffe4e6; border-color: #fda4af; color: #be123c; transform: scale(1.05); }
        .extra-rows-card {
            display: none; position: relative; margin-top: 12px; background: #fafafa;
            border: 1.5px solid #e2e8f0; border-radius: 16px; padding: 14px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .extra-rows-card.open { display: block !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Sidebar & Sticky Navbar -->
    <?php $this->load->view('admin_layanan/sidebar'); ?>
    <?php $this->load->view('partials/app_navbar', [
        'user_role_id'      => $this->session->userdata('role_id') ?? 5,
        'user_role_label'   => 'Admin Layanan (LAA)',
        'user_display_name' => 'Unit Layanan FIK',
        'user_display_sub'  => 'Pendaftaran Sidang Mahasiswa'
    ]); ?>

    <main class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5 sm:pt-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Pendaftaran Sidang</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5 sm:gap-3">
                        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center shadow-lg shadow-orange-500/25 shrink-0">
                            <i class="bi bi-mortarboard text-lg sm:text-xl"></i>
                        </span>
                        <span>List Mahasiswa Pendaftar Sidang</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        Kelola data verifikasi berkas dan status pengajuan pendaftaran sidang Tugas Akhir mahasiswa.
                    </p>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="px-3.5 py-2 rounded-xl bg-orange-50 text-orange-800 text-xs font-extrabold uppercase tracking-wider border border-orange-200 shadow-xs flex items-center gap-1.5">
                        <i class="bi bi-people-fill text-orange-600"></i> Total: <?= count($list ?? []); ?> Mahasiswa
                    </span>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if($this->session->flashdata('success')): ?>
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-green-500"></i> <?= $this->session->flashdata('success') ?>
        </div>
        <?php endif; ?>
        <?php if($this->session->flashdata('error')): ?>
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-semibold flex items-center gap-2">
            <i class="bi bi-x-circle-fill text-red-500"></i> <?= $this->session->flashdata('error') ?>
        </div>
        <?php endif; ?>

        <!-- Stat Overview Cards (Metrics) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
            <!-- Total Pendaftar -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs sm:shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block truncate">Total Pendaftar</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 block"><?= $stats['total'] ?? count($list ?? []); ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium block">Semua Jalur</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                    <i class="bi bi-person-lines-fill"></i>
                </div>
            </div>

            <!-- Sidang Reguler -->
            <a href="<?= site_url('adminlayanan/pendaftaran_sidang?jenis=sidang') ?>" class="bg-white p-4 sm:p-5 rounded-2xl border border-orange-200/80 shadow-xs sm:shadow-sm flex items-center justify-between hover:border-orange-400 transition group">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-orange-600 uppercase tracking-wider block truncate">Jalur Sidang</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 block group-hover:text-orange-600 transition"><?= $stats['sidang'] ?? 0; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium block">Sidang Reguler</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                    <i class="bi bi-easel2-fill"></i>
                </div>
            </a>

            <!-- Non-Sidang (Publikasi / HKI / Proyek) -->
            <a href="<?= site_url('adminlayanan/pendaftaran_sidang?jenis=non-sidang') ?>" class="bg-white p-4 sm:p-5 rounded-2xl border border-blue-200/80 shadow-xs sm:shadow-sm flex items-center justify-between hover:border-blue-400 transition group">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-blue-600 uppercase tracking-wider block truncate">Non-Sidang</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 block group-hover:text-blue-600 transition"><?= $stats['non_sidang'] ?? 0; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium block">Jurnal / HKI / Proyek</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
            </a>

            <!-- Disetujui Admin LAA -->
            <a href="<?= site_url('adminlayanan/pendaftaran_sidang?status=disetujui') ?>" class="bg-white p-4 sm:p-5 rounded-2xl border border-emerald-200/80 shadow-xs sm:shadow-sm flex items-center justify-between hover:border-emerald-400 transition group">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-emerald-600 uppercase tracking-wider block truncate">Disetujui LAA</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 block group-hover:text-emerald-600 transition"><?= $stats['disetujui'] ?? 0; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium block">Berkas Lengkap</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                    <i class="bi bi-check2-all"></i>
                </div>
            </a>
        </div>

        <!-- Quick Filter Tab Pills -->
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <a href="<?= site_url('adminlayanan/pendaftaran_sidang') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= ($filter_jenis === 'all' && $filter_status === 'all') ? 'bg-orange-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                Semua Pendaftar (<?= count($list ?? []); ?>)
            </a>
            <a href="<?= site_url('adminlayanan/pendaftaran_sidang?jenis=sidang') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= ($filter_jenis === 'sidang') ? 'bg-orange-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <i class="bi bi-mortarboard mr-1"></i> Sidang Reguler (<?= $stats['sidang'] ?? 0; ?>)
            </a>
            <a href="<?= site_url('adminlayanan/pendaftaran_sidang?jenis=non-sidang') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= ($filter_jenis === 'non-sidang') ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <i class="bi bi-journal-text mr-1"></i> Non-Sidang (<?= $stats['non_sidang'] ?? 0; ?>)
            </a>
            <a href="<?= site_url('adminlayanan/pendaftaran_sidang?status=disetujui') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= ($filter_status === 'disetujui') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <i class="bi bi-check-circle mr-1"></i> Disetujui (<?= $stats['disetujui'] ?? 0; ?>)
            </a>
            <a href="<?= site_url('adminlayanan/pendaftaran_sidang?status=pending') ?>" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= ($filter_status === 'pending') ? 'bg-amber-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <i class="bi bi-clock-history mr-1"></i> Pending Verifikasi (<?= $stats['pending'] ?? 0; ?>)
            </a>
        </div>

        <!-- Unified Multi-Search Bar (4 Filters + Autocomplete) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 mb-6 relative">
            <form action="<?= site_url('adminlayanan/pendaftaran_sidang'); ?>" method="GET" id="formSearchSidang" onsubmit="executeSidangSearch(event)" class="relative">
                <input type="hidden" name="cat" id="mainCategorySelectSidang" value="<?= htmlspecialchars($cat ?? 'query'); ?>">
                <input type="hidden" name="jenis" value="<?= htmlspecialchars($filter_jenis ?? 'all'); ?>">
                <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status ?? 'all'); ?>">

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    <!-- Main Search Pill -->
                    <div class="unified-search-pill flex-1 flex items-center justify-between gap-1 min-w-0">
                        <div class="relative custom-dropdown-container shrink-0" id="dropdownCatWrapperSidang">
                            <?php
                                $catLabels = [
                                    'query' => '🔍 Kata Kunci (Semua)',
                                    'nama'  => '🏷️ Nama Mahasiswa',
                                    'nim'   => '🆔 NIM Mahasiswa',
                                    'judul' => '📖 Judul Tugas Akhir',
                                    'prodi' => '🎯 Program Studi & KK',
                                    'dosen' => '👨‍🏫 Dosen Wali / Pembimbing'
                                ];
                                $curLabel = $catLabels[$cat ?? 'query'] ?? '🔍 Kata Kunci (Semua)';
                            ?>
                            <button type="button" onclick="toggleSidangCatDropdown(event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none">
                                <span id="labelCatSidang" class="truncate max-w-[120px] sm:max-w-[170px]"><?= $curLabel; ?></span>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200 dropdown-arrow" id="arrowCatSidang"></i>
                            </button>
                            <div id="menuCatSidang" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-1.5 space-y-0.5 text-xs">
                                <div onclick="selectSidangMainCategory('query', '🔍 Kata Kunci (Semua)')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? 'query') === 'query' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🔍 Kata Kunci (Semua)</span></div>
                                <div onclick="selectSidangMainCategory('nama', '🏷️ Nama Mahasiswa')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'nama' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🏷️ Nama Mahasiswa</span></div>
                                <div onclick="selectSidangMainCategory('nim', '🆔 NIM Mahasiswa')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'nim' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🆔 NIM Mahasiswa</span></div>
                                <div onclick="selectSidangMainCategory('judul', '📖 Judul Tugas Akhir')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'judul' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>📖 Judul Tugas Akhir</span></div>
                                <div onclick="selectSidangMainCategory('prodi', '🎯 Program Studi & KK')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'prodi' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>🎯 Program Studi & KK</span></div>
                                <div onclick="selectSidangMainCategory('dosen', '👨‍🏫 Dosen Wali / Pembimbing')" class="dropdown-item px-3 py-2 rounded-xl cursor-pointer font-semibold <?= ($cat ?? '') === 'dosen' ? 'bg-orange-50 text-orange-700 font-bold' : 'text-slate-700 hover:bg-slate-50'; ?>"><span>👨‍🏫 Dosen Wali / Pembimbing</span></div>
                            </div>
                        </div>

                        <div class="unified-divider shrink-0"></div>

                        <div class="flex-1 flex items-center min-w-0 px-1 relative">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                            <input type="text" name="q" id="inputSearchSidang" autocomplete="off" value="<?= htmlspecialchars($search ?? ''); ?>"
                                   placeholder="Ketik kata kunci lalu tekan Enter..." 
                                   oninput="handleSidangAutocomplete(this.value)"
                                   onkeydown="handleSidangInputKey(event)"
                                   class="w-full text-xs font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400 min-w-0">
                            
                            <button type="button" id="btnClearSearchSidang" onclick="clearSidangSearch()" class="<?= empty($search) ? 'opacity-0 scale-75 pointer-events-none' : 'opacity-100 scale-100'; ?> text-slate-400 hover:text-rose-600 text-xs font-bold px-1.5 py-1 cursor-pointer shrink-0 transition-all transform" title="Hapus pencarian">
                                <i class="fa-solid fa-circle-xmark text-sm"></i>
                            </button>
                        </div>

                        <button type="button" onclick="executeSidangSearch(event)" class="px-3 sm:px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-500 hover:to-amber-400 text-white text-xs font-bold rounded-xl shadow-xs flex items-center gap-1.5 transition-all cursor-pointer active:scale-95 shrink-0 ml-1">
                            <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>

                    <button type="button" id="standaloneAddBtnSidang" onclick="toggleSidangMultiFilter(event)" class="btn-standalone-add shrink-0 justify-center w-full sm:w-auto" title="Tambah Kriteria Filter Baru (Maks 4)">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span id="filterCountBadgeSidang" class="badge-standalone-count">1/4</span>
                    </button>
                </div>

                <!-- Autocomplete Dropdown Box -->
                <div id="autocompleteSidang" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 autocomplete-box overflow-hidden">
                    <div id="autocompleteResultsSidang" class="divide-y divide-slate-100 text-xs"></div>
                </div>

                <!-- Extra Filter Rows Container -->
                <div id="extraRowsCardSidang" class="extra-rows-card space-y-2.5">
                    <div id="additionalFilterRowsContainerSidang" class="space-y-2.5"></div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-100 pt-2.5 mt-2 text-xs">
                        <span class="text-slate-400 text-[11px]">Gunakan kombinasi kriteria untuk mempersempit pencarian. Tekan Enter / Cari.</span>
                        <button type="button" onclick="resetSidangMultiSearch()" class="text-rose-600 hover:text-rose-700 font-bold cursor-pointer self-start sm:self-auto">
                            Reset All Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Card: List Mahasiswa Pendaftar Sidang -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">
            
            <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-table text-orange-600"></i> Rekapitulasi Pendaftar Sidang
                    </h3>
                    <span class="text-xs text-slate-400 font-medium">(<?= count($list ?? []); ?> data)</span>
                </div>
            </div>

            <!-- Responsive Table View -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="pendaftaranSidangTable">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-4 text-center w-12">No</th>
                            <th class="py-3.5 px-4 text-center">Action</th>
                            <th class="py-3.5 px-5">Nama Mahasiswa</th>
                            <th class="py-3.5 px-4">NIM</th>
                            <th class="py-3.5 px-4">Prodi</th>
                            <th class="py-3.5 px-4">Konsentrasi</th>
                            <th class="py-3.5 px-4">Dosen Wali</th>
                            <th class="py-3.5 px-4 text-center">Jenis TA</th>
                            <th class="py-3.5 px-4 text-center">Tahapan / Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium" id="sidangTableBody">
                        <?php if (empty($list)): ?>
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <div class="w-16 h-16 rounded-3xl bg-orange-50 text-orange-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <p class="font-bold text-slate-700">Tidak Ada Data Pendaftar Sidang</p>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                        Mahasiswa yang mengajukan pendaftaran sidang akan muncul pada tabel rekapitulasi ini.
                                    </p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($list as $idx => $row): 
                                $isApproved = $row['is_approved'];
                                $isNonSidang = $row['is_non_sidang'];
                            ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <!-- No -->
                                    <td class="py-4 px-4 text-center font-bold text-slate-400"><?= $idx + 1; ?></td>

                                    <!-- Details Button (Matching Photo 1) -->
                                    <td class="py-4 px-4 text-center">
                                        <a href="<?= site_url('adminlayanan/detail_pendaftaran_sidang/' . $row['nim']); ?>" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-orange-600 text-white text-[11px] font-bold shadow-xs hover:shadow-md transition-all">
                                            <span>Details</span>
                                            <i class="bi bi-arrow-right-short text-sm"></i>
                                        </a>
                                    </td>

                                    <!-- Nama Mahasiswa -->
                                    <td class="py-4 px-5 font-bold text-slate-800">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-orange-100 text-orange-700 flex items-center justify-center text-xs font-extrabold shrink-0">
                                                <?= strtoupper(substr($row['nama'], 0, 1)); ?>
                                            </div>
                                            <span class="truncate max-w-[180px]"><?= htmlspecialchars($row['nama']); ?></span>
                                        </div>
                                    </td>

                                    <!-- NIM -->
                                    <td class="py-4 px-4 font-mono font-bold text-orange-600">
                                        <?= htmlspecialchars($row['nim']); ?>
                                    </td>

                                    <!-- Prodi -->
                                    <td class="py-4 px-4 text-slate-600 font-semibold truncate max-w-[150px]">
                                        <?= htmlspecialchars($row['prodi']); ?>
                                    </td>

                                    <!-- Konsentrasi -->
                                    <td class="py-4 px-4 text-slate-500 truncate max-w-[130px]">
                                        <?= htmlspecialchars($row['konsentrasi']); ?>
                                    </td>

                                    <!-- Dosen Wali -->
                                    <td class="py-4 px-4 text-slate-600 truncate max-w-[150px]">
                                        <?= htmlspecialchars($row['dosen_wali']); ?>
                                    </td>

                                    <!-- Jenis TA (Sidang vs Non-Sidang) -->
                                    <td class="py-4 px-4 text-center">
                                        <?php if ($isNonSidang): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <i class="bi bi-journal-check"></i> Non-Sidang
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                                                <i class="bi bi-mortarboard"></i> Sidang TA
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status / Tahapan -->
                                    <td class="py-4 px-4 text-center">
                                        <?php if ($isApproved): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="bi bi-check-circle-fill"></i> Disetujui
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <i class="bi bi-clock-fill"></i> Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </main>

    <!-- Raw Data for Autocomplete -->
    <script>
        window.sidangData = <?= json_encode(array_map(function($r) {
            return [
                'nim'   => $r['nim'],
                'nama'  => $r['nama'],
                'judul' => $r['judul'] ?? '',
                'prodi' => $r['prodi'] ?? '',
                'dosen' => $r['dosen_wali'] ?? ''
            ];
        }, $list ?? [])); ?>;

        let extraRowCounterSidang = 0;

        function updateSidangFilterBadge() {
            const totalRows = document.querySelectorAll('.extra-filter-row-sidang').length + 1;
            const badge = document.getElementById('filterCountBadgeSidang');
            if (badge) badge.innerText = `${totalRows}/4`;
        }

        function toggleSidangCatDropdown(e) {
            e.stopPropagation();
            const menu = document.getElementById('menuCatSidang');
            const arrow = document.getElementById('arrowCatSidang');
            menu.classList.toggle('hidden');
            arrow.style.transform = menu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        function selectSidangMainCategory(val, label) {
            document.getElementById('mainCategorySelectSidang').value = val;
            document.getElementById('labelCatSidang').textContent = label;
            document.getElementById('menuCatSidang').classList.add('hidden');
            document.getElementById('arrowCatSidang').style.transform = 'rotate(0deg)';
            document.getElementById('inputSearchSidang').focus();
        }

        function toggleSidangMultiFilter(e) {
            if (e) { e.stopPropagation(); e.preventDefault(); }
            const extraCard = document.getElementById('extraRowsCardSidang');
            const currentRows = document.querySelectorAll('.extra-filter-row-sidang').length;
            if (currentRows >= 3) {
                if (extraCard) extraCard.classList.toggle('open');
                return;
            }
            addSidangFilterRow(e);
        }

        function addSidangFilterRow(e) {
            if (e) e.stopPropagation();
            const container = document.getElementById('additionalFilterRowsContainerSidang');
            const extraCard = document.getElementById('extraRowsCardSidang');
            const currentRows = document.querySelectorAll('.extra-filter-row-sidang').length;
            if (currentRows >= 3) return;

            extraRowCounterSidang++;
            const rowId = 'extra-sidang-' + extraRowCounterSidang;

            const catOptions = [
                { key: 'nama', label: '🏷️ Nama Mahasiswa', placeholder: 'Ketik nama mahasiswa...' },
                { key: 'nim', label: '🆔 NIM Mahasiswa', placeholder: 'Ketik NIM mahasiswa...' },
                { key: 'judul', label: '📖 Judul Tugas Akhir', placeholder: 'Ketik judul TA...' },
                { key: 'prodi', label: '🎯 Program Studi & KK', placeholder: 'Ketik prodi/konsentrasi...' },
                { key: 'dosen', label: '👨‍🏫 Dosen Wali / Pembimbing', placeholder: 'Ketik nama dosen...' }
            ];

            const selectedCat = catOptions[currentRows % catOptions.length];
            const rowDiv = document.createElement('div');
            rowDiv.className = 'extra-filter-row-sidang flex items-center gap-2 relative';
            rowDiv.id = rowId;

            rowDiv.innerHTML = `
                <div class="unified-search-pill flex-1 flex items-center justify-between gap-1">
                    <div class="relative custom-dropdown-container shrink-0">
                        <button type="button" onclick="toggleExtraDropdownSidang('${rowId}', event)" class="flex items-center gap-1.5 bg-transparent border-none text-xs font-bold text-slate-800 cursor-pointer py-1 px-1 hover:text-orange-600 focus:outline-none">
                            <span id="label-${rowId}" class="truncate max-w-[120px] sm:max-w-[130px]">${selectedCat.label}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dropdown-arrow transition-transform duration-200" id="arrow-${rowId}"></i>
                        </button>
                        <div id="menu-${rowId}" class="custom-dropdown-menu hidden absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs">
                            <div onclick="selectExtraCatSidang('${rowId}', 'nama', '🏷️ Nama Mahasiswa', 'Ketik nama mahasiswa...', this)" class="px-3 py-2 rounded-lg cursor-pointer font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>🏷️ Nama Mahasiswa</span></div>
                            <div onclick="selectExtraCatSidang('${rowId}', 'nim', '🆔 NIM Mahasiswa', 'Ketik NIM mahasiswa...', this)" class="px-3 py-2 rounded-lg cursor-pointer font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>🆔 NIM Mahasiswa</span></div>
                            <div onclick="selectExtraCatSidang('${rowId}', 'judul', '📖 Judul Tugas Akhir', 'Ketik judul TA...', this)" class="px-3 py-2 rounded-lg cursor-pointer font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>📖 Judul Tugas Akhir</span></div>
                            <div onclick="selectExtraCatSidang('${rowId}', 'prodi', '🎯 Program Studi & KK', 'Ketik prodi/konsentrasi...', this)" class="px-3 py-2 rounded-lg cursor-pointer font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700"><span>🎯 Program Studi & KK</span></div>
                        </div>
                    </div>
                    <div class="unified-divider"></div>
                    <div class="flex-1 flex items-center min-w-0 px-1">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                        <input type="text" data-cat="${selectedCat.key}" placeholder="${selectedCat.placeholder}" onkeydown="handleSidangInputKey(event)" class="extra-row-input-sidang w-full text-xs font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                    </div>
                </div>
                <button type="button" onclick="removeSidangFilterRow(this)" class="btn-remove-row shrink-0" title="Hapus Filter Ini">
                    <i class="fa-solid fa-trash-can text-sm"></i>
                </button>
            `;

            container.appendChild(rowDiv);
            if (extraCard) extraCard.classList.add('open');
            updateSidangFilterBadge();

            const newInput = rowDiv.querySelector('input');
            if (newInput) newInput.focus();
        }

        function removeSidangFilterRow(btn) {
            const row = btn.closest('.extra-filter-row-sidang');
            if (row) row.remove();

            const extraCard = document.getElementById('extraRowsCardSidang');
            const remainingRows = document.querySelectorAll('.extra-filter-row-sidang').length;
            if (remainingRows === 0 && extraCard) {
                extraCard.classList.remove('open');
            }

            updateSidangFilterBadge();
        }

        function toggleExtraDropdownSidang(rowId, e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('menu-' + rowId);
            const arrow = document.getElementById('arrow-' + rowId);
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });
            document.querySelectorAll('.dropdown-arrow').forEach(a => {
                if (a !== arrow) a.style.transform = 'rotate(0deg)';
            });
            if (menu) {
                menu.classList.toggle('hidden');
                if (arrow) arrow.style.transform = menu.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        }

        function selectExtraCatSidang(rowId, key, label, placeholder, el) {
            const labelEl = document.getElementById('label-' + rowId);
            const inputEl = document.querySelector(`#${rowId} input.extra-row-input-sidang`);
            if (labelEl) labelEl.textContent = label;
            if (inputEl) {
                inputEl.setAttribute('data-cat', key);
                inputEl.placeholder = placeholder;
                inputEl.focus();
            }
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
        }

        function resetSidangMultiSearch() {
            const container = document.getElementById('additionalFilterRowsContainerSidang');
            const extraCard = document.getElementById('extraRowsCardSidang');
            if (container) container.innerHTML = '';
            if (extraCard) extraCard.classList.remove('open');

            const mainInput = document.getElementById('inputSearchSidang');
            if (mainInput) mainInput.value = '';

            const btnClear = document.getElementById('btnClearSearchSidang');
            if (btnClear) {
                btnClear.classList.remove('opacity-100', 'scale-100');
                btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
            }

            updateSidangFilterBadge();
        }

        function clearSidangSearch() {
            document.getElementById('inputSearchSidang').value = '';
            document.getElementById('autocompleteSidang').classList.add('hidden');
            const btnClear = document.getElementById('btnClearSearchSidang');
            if (btnClear) {
                btnClear.classList.remove('opacity-100', 'scale-100');
                btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
            }
        }

        function executeSidangSearch(e) {
            if (e && e.preventDefault) e.preventDefault();
            const form = document.getElementById('formSearchSidang');
            let q = document.getElementById('inputSearchSidang').value.trim();
            let cat = document.getElementById('mainCategorySelectSidang').value;

            if (!q) {
                document.querySelectorAll('.extra-row-input-sidang').forEach(inp => {
                    if (!q && inp.value.trim()) {
                        q = inp.value.trim();
                        cat = inp.getAttribute('data-cat') || cat;
                    }
                });
            }

            if (q) {
                document.getElementById('inputSearchSidang').value = q;
                document.getElementById('mainCategorySelectSidang').value = cat;
            }
            form.submit();
        }

        function handleSidangInputKey(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                executeSidangSearch(e);
            }
            if (e.key === 'Escape') {
                document.getElementById('autocompleteSidang').classList.add('hidden');
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            }
        }

        // Autocomplete Suggestion Logic
        function handleSidangAutocomplete(val) {
            const q = val.trim().toLowerCase();
            const btnClear = document.getElementById('btnClearSearchSidang');
            const autoBox = document.getElementById('autocompleteSidang');
            const autoResults = document.getElementById('autocompleteResultsSidang');

            if (btnClear) {
                if (q.length > 0) {
                    btnClear.classList.remove('opacity-0', 'scale-75', 'pointer-events-none');
                    btnClear.classList.add('opacity-100', 'scale-100');
                } else {
                    btnClear.classList.remove('opacity-100', 'scale-100');
                    btnClear.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
                }
            }

            if (q.length < 1 || !window.sidangData || window.sidangData.length === 0) {
                if (autoBox) autoBox.classList.add('hidden');
                return;
            }

            const cat = document.getElementById('mainCategorySelectSidang').value;
            const matches = window.sidangData.filter(item => {
                if (cat === 'nim')   return item.nim.toLowerCase().includes(q);
                if (cat === 'nama')  return item.nama.toLowerCase().includes(q);
                if (cat === 'judul') return (item.judul || '').toLowerCase().includes(q);
                if (cat === 'prodi') return (item.prodi || '').toLowerCase().includes(q);
                return item.nim.toLowerCase().includes(q) || item.nama.toLowerCase().includes(q) || (item.judul || '').toLowerCase().includes(q);
            }).slice(0, 8);

            if (matches.length === 0) {
                autoBox.classList.add('hidden');
                return;
            }

            const highlight = (str) => {
                if (!str) return '';
                return str.replace(new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi'), '<mark>$1</mark>');
            };

            autoResults.innerHTML = matches.map(item => `
                <div class="autocomplete-item-row flex items-center justify-between" onclick="selectSidangAutocomplete('${item.nim}')">
                    <div class="min-w-0 pr-2">
                        <span class="font-bold text-slate-800 block truncate">${highlight(item.nama)}</span>
                        <span class="text-[11px] text-slate-400 block truncate">${highlight(item.prodi)} ${item.judul ? '· ' + highlight(item.judul.substring(0, 50)) + '...' : ''}</span>
                    </div>
                    <span class="font-mono text-orange-600 font-bold text-xs shrink-0">${highlight(item.nim)}</span>
                </div>
            `).join('');

            autoBox.classList.remove('hidden');
        }

        function selectSidangAutocomplete(nim) {
            window.location.href = '<?= site_url('adminlayanan/detail_pendaftaran_sidang/'); ?>' + nim;
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.custom-dropdown-container') && !e.target.closest('#dropdownCatWrapperSidang')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
                document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
            }
            const autoBox = document.getElementById('autocompleteSidang');
            if (autoBox && !e.target.closest('#inputSearchSidang') && !e.target.closest('#autocompleteSidang')) {
                autoBox.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
