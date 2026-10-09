<?php
/** @var array $pengajuan */
$pengajuan = is_array($pengajuan ?? null) ? $pengajuan : [];
$session_role = strtolower((string) $this->session->userdata('role'));
$display_nama = $this->session->userdata('nama') ?: $this->session->userdata('username') ?: 'Wakil Dekan (Wadek)';
$user_role_id = (int)($this->session->userdata('role_id') ?? 0);
$role_names = [
    1  => 'Admin System',
    2  => 'Kepala Urusan',
    3  => 'Dosen / Wadek',
    4  => 'Mahasiswa',
    5  => 'Admin LAA',
    6  => 'Koordinator TA',
    7  => 'PIC KK',
    9  => 'Ketua KK',
    21 => 'Laboran',
    22 => 'Super Admin'
];
$user_role_label = $role_names[$user_role_id] ?? ($this->session->userdata('role') ?: 'Wakil Dekan (Wadek)');
$notif_items = isset($notifikasi) && is_array($notifikasi) ? $notifikasi : [];
$notif_count = (int) ($unread_notifikasi ?? 0);
$approval_total = (int) ($approval_total ?? count($pengajuan));
$approval_actionable = (int) ($approval_actionable ?? count(array_filter($pengajuan, static function ($p) { return scm_loan_can_act($p, 'wadek'); })));
$page = max(1, (int) ($page ?? 1));
$per_page = (int) ($per_page ?? 10);
$total_pages = max(1, (int) ($total_pages ?? 1));
$wadek_query = $_GET;
$wadek_query['per_page'] = $per_page;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'Approval Peminjaman Luar Kampus - IFIK'); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/loan-progress.css'); ?>?v=<?= @filemtime(FCPATH . 'assets/css/loan-progress.css'); ?>">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }

        /* Palette FIK */
        .text-fik-orange { color: #ea5b1a !important; }
        .bg-fik-orange { background-color: #ea5b1a !important; }
        .text-fik-brown { color: #5d3315 !important; }

        /* Custom Table Styling */
        .modal { display: none; }
        .table-custom { border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); width: 100%; table-layout: fixed; }
        .table-custom thead th { background-color: #5d3315; color: white; font-weight: 600; border: none; padding: 14px 15px; font-size: 0.82rem; letter-spacing: 0.3px; vertical-align: middle; }
        .table-custom tbody td { padding: 14px 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; background: white; font-size: 0.83rem; }
        .table-custom tbody tr:hover td { background-color: #faf5ff; }
        
        .history-date { display:grid; width:100%; grid-template-columns:24px minmax(0, 1fr); align-items:center; gap:.55rem; padding:.5rem .6rem; border:1px solid transparent; border-radius:10px; cursor:help; transition:background-color .18s ease, border-color .18s ease; }
        .history-date:hover { background:#fff3eb; }
        .history-date > i { width:24px; font-size:1rem; text-align:center; }
        .history-date__range { display:flex; min-width:0; flex-direction:column; gap:.12rem; }
        .history-date__line { display:block; overflow-wrap:normal !important; word-break:normal !important; white-space:nowrap !important; }
        .history-date__line--start { color:#252a31; font-size:.8rem; font-weight:600; }
        .history-date__line--end { color:#6c757d; font-size:.75rem; }
        .history-date__connector { display:inline-block; min-width:2.15rem; color:#9aa1aa; }

        .stat-card-fik {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #ffffff;
            padding: 18px 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card-fik:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(234, 91, 26, 0.1);
            border-color: #fdba74;
        }
        .stat-card-fik::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #ea580c, #f97316);
            border-radius: 4px 0 0 4px;
        }

        /* Unified Search Pill & Custom Multi-Filter (Exact Admin LAA & Peminjaman Barang Style) */
        .unified-search-pill {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 3px 14px;
            height: 48px;
            transition: border-color 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.14), 0 10px 25px -5px rgba(234, 88, 12, 0.12) !important;
        }
        .unified-divider {
            width: 1px;
            height: 24px;
            background: #e2e8f0;
            margin: 0 10px;
        }
        .custom-dropdown-menu {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            transform-origin: top left;
        }
        .custom-dropdown-menu.hidden {
            display: none !important;
        }
        .dropdown-item-opt {
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 500;
            color: #334155;
            transition: background-color 0.15s ease, color 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dropdown-item-opt:hover, .dropdown-item-opt.active {
            background-color: #fff7ed;
            color: #ea580c;
            font-weight: 600;
        }
        .autocomplete-box {
            max-height: 340px;
            overflow-y: auto;
            border-radius: 18px;
            background: #ffffff !important;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.25), 0 8px 24px -4px rgba(234, 88, 12, 0.2) !important;
            z-index: 2050 !important;
            position: absolute !important;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .autocomplete-item-row {
            padding: 10px 16px;
            transition: all 0.18s ease;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
        }
        .autocomplete-item-row:last-child {
            border-bottom: none;
        }
        .autocomplete-item-row:hover, .autocomplete-item-row.active-nav {
            background-color: #fff7ed;
            color: #ea580c;
        }
        .btn-standalone-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
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
            padding: 1.5px 8px;
            border-radius: 99px;
        }
        .btn-remove-row {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
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
            position: relative;
            margin-top: 12px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 14px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.25s ease;
        }
        .extra-rows-card.open {
            display: block !important;
        }
        .extra-filter-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .history-pagination { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-top:1.25rem; }
        .history-pagination__info { margin:0; color:#6c757d; font-size:.85rem; }
        .history-pagination .pagination { flex-wrap:wrap; }
        .history-pagination .page-link {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:38px;
            height:38px;
            padding:.4rem .7rem;
            border-color:#e2e5e9;
            color:#5d3315;
            font-weight:600;
            box-shadow:none;
        }
        .history-pagination .page-item:first-child .page-link,
        .history-pagination .page-item:last-child .page-link { border-radius:10px; }
        .history-pagination .page-item.active .page-link { background:#ea5b1a; border-color:#ea5b1a; color:#fff; }
        .history-pagination .page-item:not(.active):not(.disabled) .page-link:hover { background:#fff3eb; border-color:#ea5b1a; color:#c44810; }
        .history-pagination .page-item.disabled .page-link { color:#adb5bd; background:#f3f4f6; }
    </style>
    <?php include APPPATH . 'views/shared/theme_assets.php'; ?>
</head>
<body>

    <!-- Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

<div id="laaMainContentWrapper">
    <!-- Sub Navigation Page Title Bar -->
    <header class="glass-header-ifik mb-4">
        <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3 header-inner-pad">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 44px; height: 44px; background: rgba(234, 91, 26, 0.12); color: #ea5b1a; font-size: 1.35rem;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h1 class="h5 fw-bold text-dark mb-0 tracking-tight">Approval Peminjaman Luar Kampus</h1>
                        <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(234, 91, 26, 0.12); color: #ea5b1a; font-weight: 700; font-size: 11px;">Wakil Dekan (Wadek)</span>
                    </div>
                    <p class="text-muted small mb-0 d-none d-sm-block" style="font-size: 12px;">Validasi &amp; persetujuan resmi izin peminjaman alat untuk kegiatan di luar kampus.</p>
                </div>
            </div>

            <!-- Profile & Quick Action -->
            <div class="d-flex align-items-center gap-2 ms-auto">
                <a href="<?= site_url('peminjaman_barang'); ?>" class="btn-ifik-action" title="Buka Katalog Barang">
                    <i class="bi bi-box-seam"></i>
                    <span>Katalog Alat</span>
                </a>
                
                <div class="dropdown">
                    <button class="btn-ifik-profile" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="profile-avatar-box">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="profile-text-group d-none d-sm-flex">
                            <span class="profile-name"><?= html_escape($display_nama); ?></span>
                            <span class="profile-role"><?= html_escape($user_role_label); ?></span>
                        </div>
                        <i class="bi bi-chevron-down profile-chevron"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-ifik mt-2">
                        <li class="px-3 py-2 border-bottom mb-1">
                            <span class="d-block text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Login Sebagai</span>
                            <span class="fw-bold text-dark d-block text-truncate" style="font-size: 13px;"><?= html_escape($this->session->userdata('username') ?: $display_nama); ?></span>
                            <span class="badge rounded-pill mt-1" style="background: #fff7ed; color: #ea580c; font-size: 10px; font-weight: 700;"><?= html_escape($user_role_label); ?></span>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('peminjaman_barang/riwayat') ?>">
                                <i class="bi bi-clock-history text-primary"></i>
                                <span>Riwayat Pinjam</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('peminjaman_barang') ?>">
                                <i class="bi bi-grid text-warning"></i>
                                <span>Katalog Alat</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item text-danger fw-bold" href="<?= site_url('login/logout') ?>">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Keluar</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="container py-3">

        <!-- Flash Messages -->
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center rounded-3 mb-4" data-aos="zoom-in">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div><?= $this->session->flashdata('success'); ?></div>
            </div>
        <?php endif; ?>
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center rounded-3 mb-4" data-aos="zoom-in">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                <div><?= $this->session->flashdata('error'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4" data-aos="fade-up">
            <div class="col-md-6 col-lg-4">
                <div class="stat-card-fik">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Menunggu ACC Wadek</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1"><?= number_format($approval_actionable, 0, ',', '.') ?></h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-2xs" style="width: 48px; height: 48px; background: #fff7ed; color: #ea580c; font-size: 1.4rem;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted" style="font-size: 12px;">
                        <span class="text-warning fw-bold"><i class="bi bi-arrow-right-circle me-1"></i>Perlu Persetujuan</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="stat-card-fik">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Total Pengajuan Luar Kampus</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1"><?= number_format($approval_total, 0, ',', '.') ?></h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-2xs" style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-size: 1.4rem;">
                            <i class="bi bi-send-check"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted" style="font-size: 12px;">
                        <span>Keseluruhan data riwayat peminjaman eksternal</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="stat-card-fik">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Status Verifikasi Sistem</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">Aktif &amp; Terintegrasi</h4>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-2xs" style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 1.4rem;">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-muted" style="font-size: 12px;">
                        <span class="text-success fw-semibold"><i class="bi bi-check2-all me-1"></i>Otorisasi Wadek 1 Aktif</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unified Multi-Search Bar -->
        <?php
            $cat_labels = [
                'all'      => '🔍 Semua Pengajuan',
                'barang'   => '📦 Nama Barang',
                'peminjam' => '👤 Nama Peminjam',
                'number'   => '🏷️ No. Pengajuan / ID',
                'lab'      => '🏛️ Ruangan / Lab',
                'status'   => '⏳ Status Approval',
                'masa'     => '📅 Tanggal Pinjam'
            ];
            $cat_placeholders = [
                'all'      => 'Cari nama barang, peminjam, status, lab...',
                'barang'   => 'Cari nama barang...',
                'peminjam' => 'Cari nama peminjam / NIM...',
                'number'   => 'Cari nomor pengajuan...',
                'lab'      => 'Cari ruangan / laboratorium...',
                'status'   => 'Cari status (misal: Menunggu ACC Wadek)...',
                'masa'     => 'Cari tanggal (YYYY-MM-DD)...'
            ];
            $current_main_cat = $filter_rows[0]['field'] ?? ($filters['sort_by'] ?? 'all');
            $current_main_val = $filter_rows[0]['value'] ?? ($filters['pencarian'] ?? '');
            $extra_rows = array_slice($filter_rows ?? [], 1);
            $total_active_rows = 1 + count($extra_rows);
        ?>
        <div class="card border-0 shadow-sm p-3 mb-4 rounded-4 position-relative bg-white" data-aos="fade-up" style="position: relative; z-index: 1050;">
            <form action="<?= site_url('wadek/peminjaman'); ?>" method="GET" id="formSearchWadek" class="position-relative" style="position: relative; z-index: 1051;">
                <input type="hidden" name="per_page" value="<?= (int)$per_page; ?>">
                <input type="hidden" name="sort_by" value="<?= htmlspecialchars($filters['sort_by'] ?? ''); ?>">
                <input type="hidden" name="sort_dir" value="<?= htmlspecialchars($filters['sort_dir'] ?? 'desc'); ?>">
                <input type="hidden" name="filter_field[]" id="mainCategorySelectWadek" value="<?= htmlspecialchars($current_main_cat); ?>">
                
                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                    <!-- Main Search Pill -->
                    <div class="unified-search-pill flex-grow-1 d-flex align-items-center justify-content-between min-w-0" id="mainSearchPillWadek">
                        <!-- Main Category Selector Dropdown -->
                        <div class="position-relative flex-shrink-0">
                            <button type="button" onclick="toggleWadekCustomDropdown('main-cat', event)" class="d-flex align-items-center gap-1 bg-transparent border-0 text-dark fw-bold cursor-pointer py-1 px-1" style="font-size: 0.8rem;">
                                <span id="label-filter-main-cat" class="text-truncate" style="max-width: 145px;">
                                    <?= $cat_labels[$current_main_cat] ?? '🔍 Semua Pengajuan'; ?>
                                </span>
                                <i class="fa-solid fa-chevron-down text-secondary ms-1 dropdown-arrow transition-all" id="arrow-filter-main-cat" style="font-size: 9px;"></i>
                            </button>
                            <div id="menu-filter-main-cat" class="custom-dropdown-menu hidden position-absolute top-100 start-0 mt-2 bg-white border border-light-subtle rounded-3 shadow-lg p-1" style="width: 225px; z-index: 1050;">
                                <?php foreach($cat_labels as $ck => $clabel): ?>
                                    <div onclick="selectWadekMainCategory('<?= $ck ?>', '<?= htmlspecialchars($clabel, ENT_QUOTES) ?>', '<?= htmlspecialchars($cat_placeholders[$ck], ENT_QUOTES) ?>', this)" 
                                         class="dropdown-item-opt <?= $current_main_cat === $ck ? 'active' : '' ?>">
                                        <span><?= $clabel ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="unified-divider flex-shrink-0"></div>

                        <!-- Input Text Container -->
                        <div id="mainValueContainerWadek" class="flex-grow-1 d-flex align-items-center min-w-0 px-1">
                            <i id="searchIconWadek" class="fa-solid fa-magnifying-glass text-secondary me-2 flex-shrink-0" style="font-size: 13px;"></i>
                            <input type="text" name="filter_value[]" id="inputSearchWadek" autocomplete="off" value="<?= htmlspecialchars($current_main_val); ?>" 
                                   placeholder="<?= htmlspecialchars($cat_placeholders[$current_main_cat] ?? $cat_placeholders['all']); ?>" 
                                   class="form-control border-0 shadow-none bg-transparent p-0 text-dark fw-semibold" style="font-size: 0.82rem;">
                            
                            <button type="button" id="btnClearSearchWadek" onclick="clearWadekSearch()" class="<?= empty($current_main_val) ? 'd-none' : ''; ?> btn btn-link p-0 text-secondary text-decoration-none me-2" title="Hapus pencarian">
                                <i class="fa-solid fa-circle-xmark fs-6"></i>
                            </button>
                        </div>

                        <!-- Tombol Cari -->
                        <button type="submit" id="btnSubmitSearchWadek" class="btn text-white fw-bold d-flex align-items-center gap-1 rounded-3 px-3 py-1 flex-shrink-0 shadow-sm" style="background: linear-gradient(135deg, #ea580c, #f97316); font-size: 0.78rem;">
                            <i class="fa-solid fa-magnifying-glass" style="font-size: 11px;"></i>
                            <span class="d-none d-sm-inline">Cari</span>
                        </button>
                    </div>

                    <!-- Standalone Add Filter Button (+ 1/4) -->
                    <button type="button" id="standaloneAddBtnWadek" onclick="toggleWadekMultiFilter(event)" class="btn-standalone-add flex-shrink-0 justify-content-center" title="Buka / Tambah Filter Baru (Maks 4)">
                        <i class="fa-solid fa-plus fs-6"></i>
                        <span id="filterCountBadgeWadek" class="badge-standalone-count"><?= min(4, max(1, $total_active_rows)) ?>/4</span>
                    </button>
                </div>

                <!-- Autocomplete Dropdown -->
                <div id="autocompleteDropdownWadek" class="d-none position-absolute start-0 end-0 top-100 mt-2 bg-white border border-light-subtle rounded-4 shadow-lg overflow-hidden autocomplete-box" style="z-index: 1060;">
                    <div id="autocompleteResultsWadek" class="p-0"></div>
                </div>

                <!-- Extra Filter Rows Card Popover -->
                <div id="extraRowsCardWadek" class="extra-rows-card <?= !empty($extra_rows) ? 'open' : '' ?>">
                    <div id="additionalFilterRowsContainerWadek" class="d-flex flex-column gap-2 mb-2">
                        <?php if(!empty($extra_rows)): ?>
                            <?php foreach($extra_rows as $idx => $erow): 
                                $erow_id = 'extra-row-' . ($idx + 1);
                                $erow_cat = $erow['field'] ?? 'barang';
                                $erow_val = $erow['value'] ?? '';
                            ?>
                                <div class="extra-filter-row" id="<?= $erow_id ?>">
                                    <input type="hidden" name="filter_field[]" id="field-<?= $erow_id ?>" value="<?= htmlspecialchars($erow_cat) ?>">
                                    <div class="unified-search-pill flex-grow-1 d-flex align-items-center justify-content-between min-w-0" style="height: 44px;">
                                        <div class="position-relative flex-shrink-0">
                                            <button type="button" onclick="toggleWadekCustomDropdown('<?= $erow_id ?>', event)" class="d-flex align-items-center gap-1 bg-transparent border-0 text-dark fw-bold cursor-pointer py-1 px-1" style="font-size: 0.8rem;">
                                                <span id="label-filter-<?= $erow_id ?>" class="text-truncate" style="max-width: 145px;"><?= $cat_labels[$erow_cat] ?? '📦 Nama Barang' ?></span>
                                                <i class="fa-solid fa-chevron-down text-secondary ms-1 dropdown-arrow transition-all" id="arrow-filter-<?= $erow_id ?>" style="font-size: 9px;"></i>
                                            </button>
                                            <div id="menu-filter-<?= $erow_id ?>" class="custom-dropdown-menu hidden position-absolute top-100 start-0 mt-2 bg-white border border-light-subtle rounded-3 shadow-lg p-1" style="width: 225px; z-index: 1050;">
                                                <?php foreach($cat_labels as $ck => $clabel): ?>
                                                    <div onclick="selectWadekExtraCategory('<?= $erow_id ?>', '<?= $ck ?>', '<?= htmlspecialchars($clabel, ENT_QUOTES) ?>', '<?= htmlspecialchars($cat_placeholders[$ck], ENT_QUOTES) ?>', this)" 
                                                         class="dropdown-item-opt <?= $erow_cat === $ck ? 'active' : '' ?>">
                                                        <span><?= $clabel ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="unified-divider flex-shrink-0"></div>
                                        <div class="flex-grow-1 d-flex align-items-center min-w-0 px-1">
                                            <i class="fa-solid fa-magnifying-glass text-secondary me-2 flex-shrink-0" style="font-size: 12px;"></i>
                                            <input type="text" name="filter_value[]" value="<?= htmlspecialchars($erow_val) ?>" placeholder="<?= htmlspecialchars($cat_placeholders[$erow_cat] ?? '') ?>" class="form-control border-0 shadow-none bg-transparent p-0 text-dark fw-semibold extra-row-input" style="font-size: 0.82rem;">
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeWadekFilterRow(this)" class="btn-remove-row" title="Hapus Kriteria Ini">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between border-top border-light-subtle pt-2 mt-2" style="font-size: 0.78rem;">
                        <span class="text-muted"><i class="fa-solid fa-circle-info me-1"></i>Gunakan kombinasi kriteria untuk mempersempit pencarian pengajuan peminjaman.</span>
                        <button type="button" onclick="resetWadekMultiSearch()" class="btn btn-link btn-sm text-danger fw-bold text-decoration-none p-0">
                            Reset All Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Pagination Size Summary -->
        <?php if(!empty($pengajuan)): ?>
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2 text-muted small">
                <span>Tampilkan:</span>
                <select onchange="changeWadekPerPage(this.value)" class="form-select form-select-sm" style="width: auto;">
                    <?php foreach ([10, 25, 50, 100] as $sz): ?>
                        <option value="<?= $sz ?>" <?= $per_page === $sz ? 'selected' : '' ?>><?= $sz ?></option>
                    <?php endforeach; ?>
                </select>
                <span>Total: <strong class="text-dark"><?= number_format($approval_total, 0, ',', '.') ?></strong> pengajuan</span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Table Card -->
        <div class="table-responsive history-table-shell" data-aos="fade-up">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th style="width: 22%;">Tgl &amp; Peminjam</th>
                        <th style="width: 25%;">Barang &amp; Lokasi</th>
                        <th style="width: 20%;">Masa Pinjam</th>
                        <th style="width: 20%;">Status &amp; Pipeline</th>
                        <th style="width: 13%;" class="text-center">Aksi Wadek</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($pengajuan)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-folder2-open text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0 fw-semibold">Tidak ada pengajuan peminjaman luar kampus yang ditemukan.</p>
                                <a href="<?= site_url('wadek/peminjaman') ?>" class="btn btn-sm btn-outline-secondary mt-2">Reset Filter</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($pengajuan as $p): ?>
                        <?php 
                            $can_act = scm_loan_can_act($p, 'wadek');
                            $items = !empty($p->detail_barang) ? $p->detail_barang : [];
                            $group_ref = $p->group_id ?: 'single-' . $p->id_peminjaman;
                            $first_item = !empty($items) ? $items[0] : null;
                            $item_count = count($items);
                        ?>
                        <tr>
                            <!-- 1. Tgl & Peminjam -->
                            <td>
                                <div class="fw-bold text-dark"><?= html_escape($p->nama_peminjam ?? 'Peminjam') ?></div>
                                <div class="text-muted small" style="font-size: 11px;">
                                    <?= html_escape($p->nim_nip ?? '-') ?> &bull; <span class="text-truncate"><?= html_escape($p->prodi ?? 'FIK') ?></span>
                                </div>
                                <div class="text-muted small mt-1" style="font-size: 10.5px;">
                                    <i class="bi bi-clock me-1"></i><?= tanggal_indonesia($p->created_at) ?>
                                </div>
                            </td>

                            <!-- 2. Barang & Lokasi -->
                            <td>
                                <?php if($item_count > 1): ?>
                                    <div class="fw-bold text-dark"><?= html_escape($first_item->nama_aset ?? 'Multi Item') ?></div>
                                    <div class="text-muted small" style="font-size: 11px;">
                                        + <?= ($item_count - 1) ?> item lainnya &bull; Total: <span class="text-fik-orange fw-bold"><?= (int)$p->total_jumlah ?> unit</span>
                                    </div>
                                <?php else: ?>
                                    <div class="fw-bold text-dark"><?= html_escape($first_item->nama_aset ?? $p->nama_aset ?? 'Barang') ?></div>
                                    <div class="text-muted small" style="font-size: 11px;">
                                        Kode: <?= html_escape($first_item->kode_aset ?? $p->kode_aset ?? '-') ?> &bull; Jml: <span class="text-fik-orange fw-bold"><?= (int)($first_item->jumlah_pinjam ?? $p->jumlah_pinjam ?? 1) ?> unit</span>
                                    </div>
                                <?php endif; ?>
                                <span class="badge rounded-pill bg-purple-subtle text-primary border border-primary-subtle mt-1" style="font-size: 10px;">
                                    🚀 Luar Kampus
                                </span>
                            </td>

                            <!-- 3. Masa Pinjam -->
                            <td>
                                <span class="history-date" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="Keperluan: <?= html_escape($p->keperluan ?? '-') ?>">
                                    <i class="bi bi-calendar-range text-fik-orange"></i>
                                    <span class="history-date__range">
                                        <span class="history-date__line history-date__line--start"><time datetime="<?= html_escape(substr((string) $p->tanggal_pinjam, 0, 10)) ?>"><?= html_escape(tanggal_indonesia($p->tanggal_pinjam)) ?></time></span>
                                        <span class="history-date__line history-date__line--end"><span class="history-date__connector" aria-hidden="true">s.d.</span><time datetime="<?= html_escape(substr((string) $p->tanggal_kembali_rencana, 0, 10)) ?>"><?= html_escape(tanggal_indonesia($p->tanggal_kembali_rencana)) ?></time></span>
                                    </span>
                                </span>
                            </td>

                            <!-- 4. Pipeline Progress -->
                            <td>
                                <?php 
                                    $loan_progress_item = $p; 
                                    $loan_progress_compact = true; 
                                    $loan_progress_detail_target = '#modalDetail' . $p->id_peminjaman;
                                    include APPPATH . 'views/shared/loan_progress.php'; 
                                    unset($loan_progress_detail_target);
                                ?>
                            </td>

                            <!-- 5. Aksi Wadek -->
                            <td class="text-center">
                                <div class="d-flex flex-column gap-1.5 align-items-center">
                                    <?php if($can_act): ?>
                                        <button type="button" class="btn btn-sm btn-success fw-bold w-100 rounded-3 shadow-2xs py-1" data-bs-toggle="modal" data-bs-target="#modalApprove<?= $p->id_peminjaman ?>" style="font-size: 11px;">
                                            <i class="bi bi-check-circle me-1"></i> Setujui
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger fw-semibold w-100 rounded-3 py-1" data-bs-toggle="modal" data-bs-target="#modalReject<?= $p->id_peminjaman ?>" style="font-size: 11px;">
                                            <i class="bi bi-x-circle me-1"></i> Tolak
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary w-100 rounded-3 py-1" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $p->id_peminjaman ?>" style="font-size: 11px;">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Modals Container (Placed outside table) -->
        <?php if(!empty($pengajuan)): ?>
            <?php foreach($pengajuan as $p): 
                $can_act = scm_loan_can_act($p, 'wadek');
                $items = !empty($p->detail_barang) ? $p->detail_barang : [];
            ?>
                <!-- Modal Detail -->
                <div class="modal fade" id="modalDetail<?= $p->id_peminjaman ?>" tabindex="-1" aria-labelledby="modalDetailLabel<?= $p->id_peminjaman ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-bottom py-3 px-4">
                                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalDetailLabel<?= $p->id_peminjaman ?>">
                                    <i class="bi bi-info-circle text-primary"></i> Detail Pengajuan Peminjaman Eksternal
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-3 mb-4">
                                    <div class="col-sm-6">
                                        <span class="text-muted small d-block">Nama Peminjam</span>
                                        <strong class="text-dark"><?= html_escape($p->nama_peminjam ?? '-') ?></strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-muted small d-block">NIM / NIP</span>
                                        <strong class="text-dark"><?= html_escape($p->nim_nip ?? '-') ?></strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-muted small d-block">Program Studi</span>
                                        <strong class="text-dark"><?= html_escape($p->prodi ?? '-') ?></strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-muted small d-block">Masa Pinjam</span>
                                        <strong class="text-dark"><?= tanggal_indonesia($p->tanggal_pinjam) ?> s.d. <?= tanggal_indonesia($p->tanggal_kembali_rencana) ?></strong>
                                    </div>
                                    <div class="col-12">
                                        <span class="text-muted small d-block">Keperluan / Kegiatan Luar Kampus</span>
                                        <div class="p-2.5 bg-light rounded-3 text-dark mt-1" style="font-size: 12.5px;">
                                            <?= nl2br(html_escape($p->keperluan ?? '-')) ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-diagram-3 me-1 text-primary"></i> Progress &amp; Alur Peminjaman</h6>
                                    <div class="p-3 bg-light rounded-3">
                                        <?php $loan_progress_item = $p; $loan_progress_compact = false; include APPPATH . 'views/shared/loan_progress.php'; ?>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-box-seam me-1 text-fik-orange"></i> Daftar Barang yang Diajukan</h6>
                                <div class="table-responsive rounded-3 border">
                                    <table class="table table-sm mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-3 py-2 text-muted small">No</th>
                                                <th class="py-2 text-muted small">Nama Barang</th>
                                                <th class="py-2 text-muted small">Kode Aset</th>
                                                <th class="py-2 text-muted small text-center">Jumlah</th>
                                                <th class="pe-3 py-2 text-muted small">Ruangan Asal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($items)): ?>
                                                <?php foreach($items as $i_idx => $it): ?>
                                                    <tr>
                                                        <td class="ps-3 py-2"><?= $i_idx + 1 ?></td>
                                                        <td class="py-2 fw-semibold"><?= html_escape($it->nama_aset) ?></td>
                                                        <td class="py-2 text-muted"><?= html_escape($it->kode_aset) ?></td>
                                                        <td class="py-2 text-center fw-bold text-fik-orange"><?= (int)$it->jumlah_pinjam ?> unit</td>
                                                        <td class="pe-3 py-2 text-muted"><?= html_escape($it->nama_ruangan) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td class="ps-3 py-2">1</td>
                                                    <td class="py-2 fw-semibold"><?= html_escape($p->nama_aset ?? 'Barang') ?></td>
                                                    <td class="py-2 text-muted"><?= html_escape($p->kode_aset ?? '-') ?></td>
                                                    <td class="py-2 text-center fw-bold text-fik-orange"><?= (int)($p->jumlah_pinjam ?? 1) ?> unit</td>
                                                    <td class="pe-3 py-2 text-muted">-</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer border-top py-2.5 px-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-sm btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
                                <?php if($can_act): ?>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger fw-semibold rounded-3 px-3" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalReject<?= $p->id_peminjaman ?>">
                                            <i class="bi bi-x-circle me-1"></i> Tolak
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success fw-bold rounded-3 px-3" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalApprove<?= $p->id_peminjaman ?>">
                                            <i class="bi bi-check-circle me-1"></i> Setujui
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Setujui -->
                <?php if($can_act): ?>
                <div class="modal fade" id="modalApprove<?= $p->id_peminjaman ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <form action="<?= site_url('wadek/peminjaman/setujui/' . $p->id_peminjaman); ?>" method="POST">
                                <div class="modal-header border-bottom py-3 px-4">
                                    <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill"></i> Persetujuan Wadek
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <p class="text-dark mb-3">Apakah Anda yakin menyetujui peminjaman luar kampus untuk <strong><?= html_escape($p->nama_peminjam ?? 'Peminjam') ?></strong>?</p>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-bold">Catatan / Arahan Wakil Dekan (Opsional)</label>
                                        <textarea name="catatan_wadek" rows="3" class="form-control rounded-3" placeholder="Contoh: Disetujui dengan pengawasan dosen pendamping..."></textarea>
                                    </div>

                                    <div class="p-3 bg-light rounded-3 text-muted" style="font-size: 11.5px;">
                                        <i class="bi bi-info-circle text-primary me-1"></i> Setelah disetujui, QR Code serah terima barang akan langsung aktif dan peminjam dapat mengambil barang di laboratorium.
                                    </div>
                                </div>
                                <div class="modal-footer border-top py-2.5 px-4">
                                    <button type="button" class="btn btn-sm btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-sm btn-success fw-bold rounded-3 px-3">
                                        <i class="bi bi-check-lg me-1"></i> Ya, Setujui Pengajuan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Tolak -->
                <div class="modal fade" id="modalReject<?= $p->id_peminjaman ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <form action="<?= site_url('wadek/peminjaman/tolak/' . $p->id_peminjaman); ?>" method="POST">
                                <div class="modal-header border-bottom py-3 px-4">
                                    <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                                        <i class="bi bi-x-circle-fill"></i> Tolak Pengajuan Wadek
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <p class="text-dark mb-3">Tolak pengajuan peminjaman luar kampus untuk <strong><?= html_escape($p->nama_peminjam ?? 'Peminjam') ?></strong>?</p>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-danger small fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                                        <textarea name="catatan_wadek" rows="3" class="form-control rounded-3" required placeholder="Tuliskan alasan penolakan..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-top py-2.5 px-4">
                                    <button type="button" class="btn btn-sm btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-sm btn-danger fw-bold rounded-3 px-3">
                                        <i class="bi bi-x-lg me-1"></i> Tolak Pengajuan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Pagination Footer -->
        <?php if($total_pages > 1): ?>
            <div class="history-pagination">
                <p class="history-pagination__info">
                    Halaman <strong><?= $page ?></strong> dari <strong><?= $total_pages ?></strong>
                </p>
                <nav aria-label="Navigasi Halaman Wadek">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= site_url('wadek/peminjaman?' . http_build_query(array_merge($wadek_query, ['page' => $page - 1]))); ?>">&laquo;</a>
                        </li>
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= site_url('wadek/peminjaman?' . http_build_query(array_merge($wadek_query, ['page' => $i]))); ?>"><?= $i ?></a>
                                </li>
                            <?php elseif($i == $page - 3 || $i == $page + 3): ?>
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= site_url('wadek/peminjaman?' . http_build_query(array_merge($wadek_query, ['page' => $page + 1]))); ?>">&raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 500, once: true });

    const wadekCatLabels = <?= json_encode($cat_labels) ?>;
    const wadekCatPlaceholders = <?= json_encode($cat_placeholders) ?>;
    let wadekExtraRowCount = <?= count($extra_rows) ?>;

    function changeWadekPerPage(val) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', val);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }

    function toggleWadekCustomDropdown(id, e) {
        if(e) e.stopPropagation();
        const menu = document.getElementById('menu-filter-' + id);
        const arrow = document.getElementById('arrow-filter-' + id);
        const isHidden = menu.classList.contains('hidden');
        
        document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
        
        if (isHidden) {
            menu.classList.remove('hidden');
            if(arrow) arrow.style.transform = 'rotate(180deg)';
        }
    }

    function selectWadekMainCategory(catKey, catLabel, placeholder, elem) {
        document.getElementById('mainCategorySelectWadek').value = catKey;
        document.getElementById('label-filter-main-cat').textContent = catLabel;
        document.getElementById('inputSearchWadek').placeholder = placeholder;
        
        const menu = document.getElementById('menu-filter-main-cat');
        menu.querySelectorAll('.dropdown-item-opt').forEach(opt => opt.classList.remove('active'));
        elem.classList.add('active');
        menu.classList.add('hidden');
        const arrow = document.getElementById('arrow-filter-main-cat');
        if(arrow) arrow.style.transform = 'rotate(0deg)';

        triggerWadekAutocomplete();
    }

    function selectWadekExtraCategory(rowId, catKey, catLabel, placeholder, elem) {
        document.getElementById('field-' + rowId).value = catKey;
        document.getElementById('label-filter-' + rowId).textContent = catLabel;
        const input = document.querySelector('#' + rowId + ' .extra-row-input');
        if(input) input.placeholder = placeholder;
        
        const menu = document.getElementById('menu-filter-' + rowId);
        menu.querySelectorAll('.dropdown-item-opt').forEach(opt => opt.classList.remove('active'));
        elem.classList.add('active');
        menu.classList.add('hidden');
        const arrow = document.getElementById('arrow-filter-' + rowId);
        if(arrow) arrow.style.transform = 'rotate(0deg)';
    }

    function toggleWadekMultiFilter(e) {
        if(e) e.stopPropagation();
        const card = document.getElementById('extraRowsCardWadek');
        if (card.classList.contains('open')) {
            if (wadekExtraRowCount < 3) {
                addWadekFilterRow();
            } else {
                card.classList.remove('open');
            }
        } else {
            card.classList.add('open');
            if (wadekExtraRowCount === 0) {
                addWadekFilterRow();
            }
        }
    }

    function addWadekFilterRow() {
        if (wadekExtraRowCount >= 3) return;
        wadekExtraRowCount++;
        const rowId = 'extra-row-' + Date.now();
        const defaultCat = 'barang';
        
        let dropdownOpts = '';
        for (const [key, label] of Object.entries(wadekCatLabels)) {
            if (key === 'all') continue;
            dropdownOpts += `
                <div onclick="selectWadekExtraCategory('${rowId}', '${key}', '${label}', '${wadekCatPlaceholders[key] || ''}', this)" 
                     class="dropdown-item-opt ${key === defaultCat ? 'active' : ''}">
                    <span>${label}</span>
                </div>
            `;
        }

        const html = `
            <div class="extra-filter-row" id="${rowId}">
                <input type="hidden" name="filter_field[]" id="field-${rowId}" value="${defaultCat}">
                <div class="unified-search-pill flex-grow-1 d-flex align-items-center justify-content-between min-w-0" style="height: 44px;">
                    <div class="position-relative flex-shrink-0">
                        <button type="button" onclick="toggleWadekCustomDropdown('${rowId}', event)" class="d-flex align-items-center gap-1 bg-transparent border-0 text-dark fw-bold cursor-pointer py-1 px-1" style="font-size: 0.8rem;">
                            <span id="label-filter-${rowId}" class="text-truncate" style="max-width: 145px;">${wadekCatLabels[defaultCat]}</span>
                            <i class="fa-solid fa-chevron-down text-secondary ms-1 dropdown-arrow transition-all" id="arrow-filter-${rowId}" style="font-size: 9px;"></i>
                        </button>
                        <div id="menu-filter-${rowId}" class="custom-dropdown-menu hidden position-absolute top-100 start-0 mt-2 bg-white border border-light-subtle rounded-3 shadow-lg p-1" style="width: 225px; z-index: 1050;">
                            ${dropdownOpts}
                        </div>
                    </div>
                    <div class="unified-divider flex-shrink-0"></div>
                    <div class="flex-grow-1 d-flex align-items-center min-w-0 px-1">
                        <i class="fa-solid fa-magnifying-glass text-secondary me-2 flex-shrink-0" style="font-size: 12px;"></i>
                        <input type="text" name="filter_value[]" placeholder="${wadekCatPlaceholders[defaultCat]}" class="form-control border-0 shadow-none bg-transparent p-0 text-dark fw-semibold extra-row-input" style="font-size: 0.82rem;">
                    </div>
                </div>
                <button type="button" onclick="removeWadekFilterRow(this)" class="btn-remove-row" title="Hapus Kriteria Ini">
                    <i class="fa-solid fa-trash text-xs"></i>
                </button>
            </div>
        `;
        document.getElementById('additionalFilterRowsContainerWadek').insertAdjacentHTML('beforeend', html);
        updateWadekBadge();
    }

    function removeWadekFilterRow(btn) {
        const row = btn.closest('.extra-filter-row');
        if (row) row.remove();
        wadekExtraRowCount--;
        updateWadekBadge();
        if (wadekExtraRowCount <= 0) {
            document.getElementById('extraRowsCardWadek').classList.remove('open');
        }
    }

    function updateWadekBadge() {
        const total = 1 + wadekExtraRowCount;
        document.getElementById('filterCountBadgeWadek').textContent = total + '/4';
    }

    function clearWadekSearch() {
        const input = document.getElementById('inputSearchWadek');
        input.value = '';
        document.getElementById('btnClearSearchWadek').classList.add('d-none');
        document.getElementById('autocompleteDropdownWadek').classList.add('d-none');
        input.focus();
    }

    function resetWadekMultiSearch() {
        window.location.href = '<?= site_url("wadek/peminjaman"); ?>';
    }

    // Autocomplete Logic
    const searchInput = document.getElementById('inputSearchWadek');
    const autocompleteDropdown = document.getElementById('autocompleteDropdownWadek');
    const autocompleteResults = document.getElementById('autocompleteResultsWadek');
    let searchDebounceTimer = null;
    let activeNavIdx = -1;

    function triggerWadekAutocomplete() {
        const term = searchInput.value.trim();
        const cat = document.getElementById('mainCategorySelectWadek').value;
        const btnClear = document.getElementById('btnClearSearchWadek');
        
        if (term.length > 0) {
            btnClear.classList.remove('d-none');
        } else {
            btnClear.classList.add('d-none');
            autocompleteDropdown.classList.add('d-none');
            return;
        }

        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            fetch(`<?= site_url('wadek/peminjaman/autocomplete') ?>?q=${encodeURIComponent(term)}&cat=${encodeURIComponent(cat)}`)
                .then(res => res.json())
                .then(data => {
                    activeNavIdx = -1;
                    if (!data || data.length === 0) {
                        autocompleteResults.innerHTML = `
                            <div class="p-3 text-center text-muted" style="font-size: 0.8rem;">
                                <i class="fa-solid fa-circle-question me-1"></i> Tidak ditemukan saran autocomplete untuk <strong>"${term}"</strong>. Tekan Enter untuk cari.
                            </div>
                        `;
                    } else {
                        let html = '';
                        data.forEach((item, idx) => {
                            html += `
                                <div class="autocomplete-item-row" data-idx="${idx}" onclick="selectWadekAutocompleteItem('${encodeURIComponent(item.nama_aset || item.nama_peminjam || '')}')">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-bold" style="font-size: 0.84rem;">${item.nama_aset || item.nama_peminjam}</span>
                                        <span class="badge rounded-pill bg-light text-muted border" style="font-size: 10px;">${item.status || 'Pending'}</span>
                                    </div>
                                    <div class="text-muted small mt-0.5" style="font-size: 11px;">
                                        ${item.nama_peminjam ? 'Peminjam: ' + item.nama_peminjam + ' &bull; ' : ''}
                                        ${item.ruangan ? 'Lab: ' + item.ruangan : ''}
                                    </div>
                                </div>
                            `;
                        });
                        autocompleteResults.innerHTML = html;
                    }
                    autocompleteDropdown.classList.remove('d-none');
                })
                .catch(() => {
                    autocompleteDropdown.classList.add('d-none');
                });
        }, 220);
    }

    function selectWadekAutocompleteItem(val) {
        searchInput.value = decodeURIComponent(val);
        autocompleteDropdown.classList.add('d-none');
        document.getElementById('formSearchWadek').submit();
    }

    if (searchInput) {
        searchInput.addEventListener('input', triggerWadekAutocomplete);
        searchInput.addEventListener('keydown', function(e) {
            const rows = autocompleteResults.querySelectorAll('.autocomplete-item-row');
            if (!autocompleteDropdown.classList.contains('d-none') && rows.length > 0) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeNavIdx = (activeNavIdx + 1) % rows.length;
                    rows.forEach(r => r.classList.remove('active-nav'));
                    rows[activeNavIdx].classList.add('active-nav');
                    rows[activeNavIdx].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeNavIdx = (activeNavIdx - 1 + rows.length) % rows.length;
                    rows.forEach(r => r.classList.remove('active-nav'));
                    rows[activeNavIdx].classList.add('active-nav');
                    rows[activeNavIdx].scrollIntoView({ block: 'nearest' });
                } else if (e.key === 'Enter' && activeNavIdx >= 0) {
                    e.preventDefault();
                    rows[activeNavIdx].click();
                }
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#mainSearchPillWadek') && !e.target.closest('#autocompleteDropdownWadek')) {
            autocompleteDropdown.classList.add('d-none');
        }
        if (!e.target.closest('.position-relative')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.dropdown-arrow').forEach(a => a.style.transform = 'rotate(0deg)');
        }
    });
</script>
</body>
</html>
