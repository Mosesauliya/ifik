<?php
/**
 * Halaman Verifikasi Berkas KP - Admin Layanan (LAA)
 * Path: application/views/admin_layanan/verifikasi_kp.php
 *
 * Tab 1: Melihat & Meng-ACC Berkas Persyaratan KP (/ifik/mahasiswa/pengajuan_kp)
 *         - Dipisahkan antara: Belum Disetujui Semua & Sudah Disetujui Semua
 * Tab 2: Melihat & Meng-ACC File Laporan KP (/ifik/mahasiswa/laporan_kp)
 *
 * Visual & Fitur: Diselaraskan dengan dosen_bimbingan (Multi-search bar, pagination, rotating border table, & split-view preview modal).
 */

$active_tab = $active_tab ?? 'persyaratan';
$sub_tab    = $sub_tab ?? 'belum_acc';

$total_belum_acc = count($belum_acc ?? []);
$total_sudah_acc = count($sudah_acc ?? []);
$total_laporan   = count($laporan ?? []);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Verifikasi Berkas Kerja Praktik (KP) - Layanan Akademik'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            min-height: 100vh;
            transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1),
                        width 0.75s cubic-bezier(0.76, 0, 0.24, 1);
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
            }
        }

        /* Filter Multi Kriteria */
        .filter-row-dw {
            display: flex;
            align-items: center;
            gap: 8px;
            animation: fadeInDownSmooth 0.3s cubic-bezier(0.2, 0.9, 0.2, 1) forwards;
        }
        .filter-col-dw {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 700;
            background: #fff;
            color: #1e293b;
            outline: none;
            cursor: pointer;
            transition: all 0.18s ease;
            flex-shrink: 0;
            min-width: 170px;
        }
        .filter-col-dw:focus {
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }
        .filter-val-dw {
            flex: 1;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 600;
            background: #f8fafc;
            color: #1e293b;
            outline: none;
            transition: all 0.18s ease;
        }
        .filter-val-dw:focus {
            border-color: #ea580c;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }
        .btn-remove-filter-dw {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: #ffe4e6;
            color: #e11d48;
            border: 1.5px solid #fecdd3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }
        .btn-remove-filter-dw:hover { background: #fecdd3; transform: scale(1.05); }

        /* Rotating Border Table Wrap */
        @keyframes spinRotatingBorder { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .table-rotating-border-wrap {
            position: relative; border-radius: 18px; padding: 2px; overflow: hidden;
            box-shadow: 0 12px 36px -8px rgba(234, 88, 12, 0.15), 0 4px 16px rgba(71, 85, 105, 0.06); background: #ffffff;
        }
        .table-rotating-border-spin {
            position: absolute; inset: -350%; pointer-events: none; opacity: 0.95;
            background: conic-gradient(from 90deg at 50% 50%, #ea580c 0%, #f97316 12%, #ffffff 22%, #cbd5e1 35%, #475569 48%, #1e293b 58%, #ea580c 68%, #ffffff 80%, #94a3b8 90%, #ea580c 100%);
            animation: spinRotatingBorder 8s linear infinite;
        }
        .table-rotating-border-inner { position: relative; z-index: 10; width: 100%; background: #ffffff; border-radius: 16px; overflow: hidden; }
        .table-custom-rounded { border-collapse: separate !important; border-spacing: 0 !important; width: 100%; }
        .table-custom-rounded thead tr th:first-child { border-top-left-radius: 16px; }
        .table-custom-rounded thead tr th:last-child { border-top-right-radius: 16px; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; font-size: 0.75rem; gap: 0.375rem; border: 1px solid transparent; }
        .badge-success { background-color: #d1fae5; color: #065f46; border-color: #34d399; }
        .badge-warning { background-color: #fef3c7; color: #92400e; border-color: #fbbf24; }
        .badge-danger { background-color: #ffe4e6; color: #9f1239; border-color: #fb7185; }
        .badge-secondary { background-color: #f1f5f9; color: #475569; border-color: #cbd5e1; }
        .badge-primary { background-color: #e0e7ff; color: #3730a3; border-color: #818cf8; }

        /* Pagination Buttons */
        .pagination-btn {
            width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 11px; border: 1.5px solid #e2e8f0; background: #fff; color: #475569;
            font-size: 0.75rem; font-weight: 800; cursor: pointer; transition: all 0.15s ease;
        }
        .pagination-btn:hover:not(:disabled):not(.active) { background: #fff7ed; border-color: #fed7aa; color: #ea580c; }
        .pagination-btn.active {
            background: linear-gradient(135deg, #ea580c, #f97316); border-color: #ea580c;
            color: #fff; box-shadow: 0 4px 12px rgba(234,88,12,0.3); cursor: default;
        }
        .pagination-btn:disabled { opacity: 0.35; cursor: not-allowed; }
        .pagination-ellipsis { padding: 0 4px; color: #94a3b8; font-weight: 800; font-size: 0.75rem; }

        /* Animation */
        @keyframes popInCard { 0% { opacity: 0; transform: scale(0.92) translateY(20px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes fadeInSlideRight { 0% { opacity: 0; transform: scale(0.96) translateX(40px); } 100% { opacity: 1; transform: scale(1) translateX(0); } }
        @keyframes fadeInDownSmooth { 0% { opacity: 0; transform: translateY(-12px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-pop-in { animation: popInCard 0.35s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }
        .animate-preview-in { animation: fadeInSlideRight 0.35s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }

        /* Modal Preview Berkas Split View */
        #previewModalOverlay {
            position: fixed; inset: 0; z-index: 9999; display: none;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px);
            align-items: center; justify-content: center; padding: 1rem;
        }
        #previewModalOverlay.active { display: flex; }
        .preview-modal-box {
            background: #ffffff; border-radius: 1.5rem; width: 1250px; max-width: 96vw;
            height: 90vh; max-height: 94vh; overflow: hidden; display: flex;
            box-shadow: 0 25px 60px -15px rgba(15,23,42,0.5);
            border: 1px solid #fed7aa;
        }
        .preview-left-pane {
            width: 440px; min-width: 380px; max-width: 460px;
            background: #fafaf9; border-right: 1.5px solid #e7e5e4;
            display: flex; flex-direction: column; overflow-y: auto;
        }
        .preview-right-pane {
            flex: 1; display: flex; flex-direction: column; background: #e2e8f0; min-width: 0;
        }
        @media (max-width: 900px) {
            .preview-modal-box { flex-direction: column; height: 94vh; }
            .preview-left-pane { width: 100%; min-width: 0; max-width: none; height: 42%; border-right: none; border-bottom: 2px solid #e2e8f0; }
            .preview-right-pane { height: 58%; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50/40 via-orange-50/25 to-slate-100 min-h-screen text-slate-800 antialiased flex flex-col justify-between selection:bg-orange-500 selection:text-white">

    <!-- Unified Curved Animated Sidebar -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar flex flex-col min-h-screen flex-grow">
    <main class="w-full px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex-grow space-y-7">

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="p-5 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-emerald-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-2xl leading-none">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="p-5 rounded-3xl bg-rose-50 border-2 border-rose-300 text-rose-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-rose-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-500 text-white flex items-center justify-center text-lg shrink-0">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('error'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold text-2xl leading-none">&times;</button>
            </div>
        <?php endif; ?>

        <!-- HERO HEADER (Mirip Dosen Bimbingan & Pendaftaran TA) -->
        <div class="bg-gradient-to-r from-[#9a3412] via-[#ea580c] to-[#c2410c] rounded-3xl p-7 sm:p-9 relative overflow-hidden shadow-2xl text-white">
            <div class="relative z-10 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-8">
                <div class="space-y-3 max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold">
                        <i class="bi bi-shield-check text-amber-200"></i> Portal Verifikasi Layanan Akademik (LAA)
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        Verifikasi Berkas Kerja Praktik (KP)
                    </h1>
                    <p class="text-xs sm:text-sm text-orange-100 font-normal leading-relaxed">
                        Kelola evaluasi, verifikasi keabsahan, dan setujui (ACC) berkas persyaratan KP serta laporan akhir kerja praktik mahasiswa secara akurat dan transparan.
                    </p>
                </div>

                <!-- Navigasi 2 Tab Utama -->
                <div class="w-full xl:w-auto bg-black/25 backdrop-blur-xl rounded-3xl p-3 border border-white/20 shadow-2xl shrink-0">
                    <div class="flex items-center gap-2">
                        <!-- Tab 1 Button -->
                        <button type="button" onclick="switchMainTab('persyaratan')" id="btnTabPersyaratan"
                                class="px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer <?= ($active_tab === 'persyaratan') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40' : 'bg-white/10 text-white hover:bg-white/20'; ?>">
                            <i class="bi bi-file-earmark-check-fill text-base"></i>
                            <div class="text-left">
                                <span class="block">Tab 1: Persyaratan KP</span>
                                <span class="text-[10px] font-normal text-orange-100/90 block">Pengajuan Berkas (Step 2)</span>
                            </div>
                            <span class="ml-1 px-2 py-0.5 rounded-full bg-black/30 text-amber-200 text-xs font-black">
                                <?= $total_belum_acc + $total_sudah_acc ?>
                            </span>
                        </button>

                        <!-- Tab 2 Button -->
                        <button type="button" onclick="switchMainTab('laporan')" id="btnTabLaporan"
                                class="px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer <?= ($active_tab === 'laporan') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40' : 'bg-white/10 text-white hover:bg-white/20'; ?>">
                            <i class="bi bi-journal-richtext text-base"></i>
                            <div class="text-left">
                                <span class="block">Tab 2: Laporan KP</span>
                                <span class="text-[10px] font-normal text-orange-100/90 block">Laporan &amp; Nilai Lapangan</span>
                            </div>
                            <span class="ml-1 px-2 py-0.5 rounded-full bg-black/30 text-amber-200 text-xs font-black">
                                <?= $total_laporan ?>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================================
             KONTEN TAB 1: PERSYARATAN KP (Pemisahan Belum ACC vs Sudah ACC)
             ========================================================================= -->
        <div id="contentTabPersyaratan" class="<?= ($active_tab === 'persyaratan') ? '' : 'hidden' ?> space-y-6">

            <!-- Sub-tab Switcher: Pemisahan Tabel Mahasiswa Belum di-ACC vs Sudah di-ACC Semua -->
            <div class="flex flex-wrap items-center justify-between gap-4 bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-folder-check text-orange-500 text-xl"></i>
                        Verifikasi Persyaratan Pengajuan KP
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Data berkas yang diunggah mahasiswa pada menu <code>/ifik/mahasiswa/pengajuan_kp</code>.
                    </p>
                </div>

                <!-- Sub-tab Pill -->
                <div class="inline-flex p-1.5 rounded-2xl bg-slate-100 border border-slate-200 gap-1.5">
                    <button type="button" onclick="switchSubTabPersyaratan('belum_acc')" id="btnSubBelumAcc"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer <?= ($sub_tab === 'belum_acc') ? 'bg-orange-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'; ?>">
                        <i class="bi bi-hourglass-split"></i>
                        <span>Belum Disetujui Semua</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($sub_tab === 'belum_acc') ? 'bg-white text-orange-600' : 'bg-slate-200 text-slate-700'; ?>" id="badgeCountBelumAcc">
                            <?= $total_belum_acc ?>
                        </span>
                    </button>
                    <button type="button" onclick="switchSubTabPersyaratan('sudah_acc')" id="btnSubSudahAcc"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer <?= ($sub_tab === 'sudah_acc') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'; ?>">
                        <i class="bi bi-check-all"></i>
                        <span>Sudah Disetujui Semua (Lengkap)</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= ($sub_tab === 'sudah_acc') ? 'bg-white text-emerald-600' : 'bg-slate-200 text-slate-700'; ?>" id="badgeCountSudahAcc">
                            <?= $total_sudah_acc ?>
                        </span>
                    </button>
                </div>
            </div>

            <!-- CONTAINER CARD TABEL PERSYARATAN -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-lg shadow-orange-500/5 space-y-6 border border-slate-200">

                <!-- Multi-kriteria Search Bar (Identik Dosen Bimbingan) -->
                <div id="multiFilterPanelPersyaratan" class="bg-slate-50/70 rounded-2xl border border-slate-200 p-4 shadow-2xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-700 flex items-center gap-1.5">
                            <i class="bi bi-funnel-fill"></i> Filter Multi-Kriteria (Maksimal 4 Kriteria)
                        </span>
                        <div class="flex items-center gap-2">
                            <button type="button" id="btnAddFilterPersyaratan" onclick="addFilterRow('persyaratan')"
                                    class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-2xs flex items-center gap-1.5 transition cursor-pointer">
                                <i class="bi bi-plus-lg"></i> Tambah Filter
                                <span id="filterCountBadgePersyaratan" class="bg-white/30 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">1/4</span>
                            </button>
                            <button type="button" onclick="resetFilters('persyaratan')"
                                    class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-orange-50 transition cursor-pointer flex items-center gap-1.5">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div id="filterRowsPersyaratan" class="space-y-2 mb-3"></div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
                        <p class="text-[10px] text-slate-500 font-medium italic">
                            <i class="bi bi-info-circle"></i> Tekan <strong>Enter</strong> atau klik <strong>Cari</strong> untuk memfilter mahasiswa.
                        </p>
                        <button type="button" onclick="applyFilters('persyaratan')"
                                class="px-5 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-md flex items-center gap-1.5 transition cursor-pointer">
                            <i class="bi bi-search"></i> Cari Data
                        </button>
                    </div>
                </div>

                <!-- Kontrol Pagination -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-1 px-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="text-[11px] font-bold text-slate-600 whitespace-nowrap">Tampilkan:</label>
                        <select id="perPagePersyaratan" onchange="changePerPage('persyaratan')" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-bold bg-white focus:ring-orange-500 focus:border-orange-500 cursor-pointer outline-none">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="text-[11px] text-slate-500 font-medium" id="paginationInfoPersyaratan"></span>
                    </div>
                    <div class="flex items-center gap-1 flex-wrap" id="paginationButtonsPersyaratan"></div>
                </div>

                <!-- Tabel Rotating Border -->
                <div class="table-rotating-border-wrap">
                    <span class="table-rotating-border-spin"></span>
                    <div class="table-rotating-border-inner overflow-x-auto">
                        <table class="table-custom-rounded text-left text-sm w-full">
                            <thead class="bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-4 px-4 text-center w-12">No</th>
                                    <th class="py-4 px-4">Mahasiswa &amp; Program Studi</th>
                                    <th class="py-4 px-4">Perusahaan / Instansi KP</th>
                                    <th class="py-4 px-4 text-center">Kelengkapan Berkas</th>
                                    <th class="py-4 px-4 text-center">Status LAA</th>
                                    <th class="py-4 px-4 text-center">Aksi Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium bg-white" id="tbodyPersyaratan">
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-slate-400">
                                        <i class="bi bi-arrow-repeat animate-spin text-lg mr-2"></i> Memuat data pengajuan KP...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- =========================================================================
             KONTEN TAB 2: LAPORAN & PENILAIAN KP
             ========================================================================= -->
        <div id="contentTabLaporan" class="<?= ($active_tab === 'laporan') ? '' : 'hidden' ?> space-y-6">

            <div class="flex flex-wrap items-center justify-between gap-4 bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-journal-text text-orange-500 text-xl"></i>
                        Verifikasi File Laporan KP &amp; Form Penilaian
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Data laporan akhir dan lembar penilaian pembimbing lapangan yang diunggah mahasiswa pada menu <code>/ifik/mahasiswa/laporan_kp</code>.
                    </p>
                </div>
                <div class="text-xs font-bold text-slate-600 px-3.5 py-1.5 rounded-full bg-orange-50 border border-orange-200">
                    Total: <span id="badgeTotalLaporan"><?= $total_laporan ?></span> Laporan Mahasiswa
                </div>
            </div>

            <!-- CONTAINER CARD TABEL LAPORAN -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-lg shadow-orange-500/5 space-y-6 border border-slate-200">

                <!-- Multi-kriteria Search Bar Laporan -->
                <div id="multiFilterPanelLaporan" class="bg-slate-50/70 rounded-2xl border border-slate-200 p-4 shadow-2xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-700 flex items-center gap-1.5">
                            <i class="bi bi-funnel-fill"></i> Filter Multi-Kriteria (Maksimal 4 Kriteria)
                        </span>
                        <div class="flex items-center gap-2">
                            <button type="button" id="btnAddFilterLaporan" onclick="addFilterRow('laporan')"
                                    class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-2xs flex items-center gap-1.5 transition cursor-pointer">
                                <i class="bi bi-plus-lg"></i> Tambah Filter
                                <span id="filterCountBadgeLaporan" class="bg-white/30 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">1/4</span>
                            </button>
                            <button type="button" onclick="resetFilters('laporan')"
                                    class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-orange-50 transition cursor-pointer flex items-center gap-1.5">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div id="filterRowsLaporan" class="space-y-2 mb-3"></div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
                        <p class="text-[10px] text-slate-500 font-medium italic">
                            <i class="bi bi-info-circle"></i> Tekan <strong>Enter</strong> atau klik <strong>Cari</strong> untuk memfilter laporan.
                        </p>
                        <button type="button" onclick="applyFilters('laporan')"
                                class="px-5 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-md flex items-center gap-1.5 transition cursor-pointer">
                            <i class="bi bi-search"></i> Cari Data
                        </button>
                    </div>
                </div>

                <!-- Kontrol Pagination Laporan -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-1 px-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="text-[11px] font-bold text-slate-600 whitespace-nowrap">Tampilkan:</label>
                        <select id="perPageLaporan" onchange="changePerPage('laporan')" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-bold bg-white focus:ring-orange-500 focus:border-orange-500 cursor-pointer outline-none">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="text-[11px] text-slate-500 font-medium" id="paginationInfoLaporan"></span>
                    </div>
                    <div class="flex items-center gap-1 flex-wrap" id="paginationButtonsLaporan"></div>
                </div>

                <!-- Tabel Rotating Border Laporan -->
                <div class="table-rotating-border-wrap">
                    <span class="table-rotating-border-spin"></span>
                    <div class="table-rotating-border-inner overflow-x-auto">
                        <table class="table-custom-rounded text-left text-sm w-full">
                            <thead class="bg-slate-50 text-slate-700 font-bold text-xs uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-4 px-4 text-center w-12">No</th>
                                    <th class="py-4 px-4">Mahasiswa &amp; Program Studi</th>
                                    <th class="py-4 px-4">Instansi KP &amp; Pembimbing Lapangan</th>
                                    <th class="py-4 px-4">Dosen Pembimbing 1</th>
                                    <th class="py-4 px-4 text-center">File Laporan</th>
                                    <th class="py-4 px-4 text-center">Nilai Lapangan</th>
                                    <th class="py-4 px-4 text-center">Status LAA</th>
                                    <th class="py-4 px-4 text-center">Aksi Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium bg-white" id="tbodyLaporan">
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-slate-400">
                                        <i class="bi bi-arrow-repeat animate-spin text-lg mr-2"></i> Memuat data laporan KP...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </main>
    </div>

    <!-- =========================================================================
         MODAL PREVIEW & VERIFIKASI BERKAS SPLIT-VIEW (IDENTIK DOSEN BIMBINGAN)
         ========================================================================= -->
    <div id="previewModalOverlay" onclick="if(event.target===this) closePreviewModal()">
        <div class="preview-modal-box animate-pop-in">

            <!-- LEFT PANE: INFORMASI MAHASISWA & DAFTAR BERKAS -->
            <div class="preview-left-pane p-5 flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold text-sm">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Detail Mahasiswa</h4>
                                <span class="text-[10px] text-slate-500 font-semibold" id="modalMhsProdi">Informatika</span>
                            </div>
                        </div>
                        <span class="badge badge-warning text-[10px]" id="modalMhsStatusKp">Pending</span>
                    </div>

                    <!-- Profil Mahasiswa -->
                    <div class="p-3.5 rounded-2xl bg-white border border-stone-200 space-y-1.5 shadow-2xs">
                        <h3 class="text-sm font-extrabold text-slate-900" id="modalMhsNama">Nama Mahasiswa</h3>
                        <p class="text-xs text-orange-600 font-bold" id="modalMhsNim">1301210000</p>
                        <div class="text-[11px] text-slate-600 space-y-0.5 pt-1 border-t border-slate-100 mt-2">
                            <p class="truncate"><i class="bi bi-building mr-1.5 text-slate-400"></i><strong id="modalMhsPerusahaan">-</strong></p>
                            <p class="truncate"><i class="bi bi-geo-alt mr-1.5 text-slate-400"></i><span id="modalMhsKota">-</span></p>
                            <p class="truncate"><i class="bi bi-person-badge mr-1.5 text-slate-400"></i><span id="modalMhsP1">Menunggu Plotting Koordinator KP</span></p>
                        </div>
                    </div>

                    <!-- Daftar Berkas Mahasiswa -->
                    <div class="space-y-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 block">Daftar Berkas Mahasiswa</span>
                        <div class="space-y-1.5 max-h-[220px] overflow-y-auto pr-1" id="modalFilesListContainer">
                            <!-- Diisi secara dinamis -->
                        </div>
                    </div>
                </div>

                <!-- Tombol Cepat: ACC Seluruh Berkas Persyaratan -->
                <div class="pt-4 border-t border-stone-200 space-y-2" id="modalPersyaratanBulkActionArea">
                    <button type="button" onclick="confirmBulkAccPersyaratanCurrent()"
                            class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="bi bi-check-all text-base"></i>
                        <span>ACC Semua Berkas Persyaratan</span>
                    </button>
                    <p class="text-[10px] text-center text-slate-400">Menyetujui seluruh persyaratan mahasiswa ini sekaligus.</p>
                </div>
            </div>

            <!-- RIGHT PANE: IFRAME PREVIEW PDF & AKSI VERIFIKASI -->
            <div class="preview-right-pane">
                <!-- Header Preview -->
                <div class="p-3.5 px-5 bg-slate-900 text-white flex items-center justify-between gap-3 shrink-0 border-b border-slate-800">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-lg bg-orange-500/20 text-orange-400 border border-orange-500/40 flex items-center justify-center shrink-0">
                            <i class="bi bi-file-earmark-pdf-fill text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-white truncate" id="modalPreviewDocTitle">Pratinjau Dokumen</h4>
                            <p class="text-[10px] text-slate-400 truncate" id="modalPreviewDocSub">-</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="#" id="modalDownloadBtn" target="_blank"
                           class="px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-slate-200 text-xs font-semibold flex items-center gap-1.5 transition">
                            <i class="bi bi-download"></i> Unduh
                        </a>
                        <button type="button" onclick="toggleModalFullscreen()"
                                class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-slate-200 flex items-center justify-center transition cursor-pointer" title="Perbesar Tampilan">
                            <i class="bi bi-arrows-fullscreen text-xs"></i>
                        </button>
                        <button type="button" onclick="closePreviewModal()"
                                class="w-8 h-8 rounded-lg bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white flex items-center justify-center transition cursor-pointer" title="Tutup">
                            <i class="bi bi-x-lg text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Iframe Viewer -->
                <div class="flex-1 relative bg-slate-800 overflow-hidden" id="modalIframeContainer">
                    <iframe id="modalPdfFrame" src="about:blank" class="w-full h-full border-none"></iframe>
                    <div id="modalPdfEmptyPlaceholder" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-slate-100 text-slate-400 p-6 text-center">
                        <i class="bi bi-file-earmark-x text-5xl mb-2 text-slate-300"></i>
                        <h4 class="text-sm font-bold text-slate-700">Berkas Belum Diunggah</h4>
                        <p class="text-xs text-slate-500">Mahasiswa belum mengunggah dokumen ini.</p>
                    </div>
                </div>

                <!-- Action Footer: Verifikasi (ACC / Tolak Revisi) -->
                <div class="p-3.5 px-5 bg-white border-t border-slate-200 flex flex-wrap items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-2" id="modalCurrentStatusBadge">
                        <span class="text-xs font-bold text-slate-500">Status Saat Ini:</span>
                        <span class="badge badge-warning text-xs" id="modalActiveFileStatus">Pending</span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="promptRejectCurrentFile()"
                                class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                            <i class="bi bi-x-circle-fill text-rose-600"></i>
                            <span>Tolak / Minta Revisi</span>
                        </button>
                        <button type="button" onclick="confirmAccCurrentFile()"
                                class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>ACC / Setujui Berkas Ini</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- DATASET DARI SERVER UNTUK JAVASCRIPT TABLE & FILTER -->
    <script>
        const RAW_DATA = {
            belum_acc: <?= json_encode($belum_acc ?? []); ?>,
            sudah_acc: <?= json_encode($sudah_acc ?? []); ?>,
            laporan:   <?= json_encode($laporan ?? []); ?>,
            syarat_kp: <?= json_encode($syarat_list ?? []); ?>
        };

        const BASE_URL = '<?= rtrim(base_url(), '/') . '/'; ?>';
        const SITE_URL = '<?= rtrim(site_url(), '/') . '/'; ?>';

        let currentMainTab = '<?= $active_tab ?>';
        let currentSubTabPersyaratan = '<?= $sub_tab ?>';

        // Filter State
        const COLUMNS_PERSYARATAN = [
            { value: 'all', label: 'Semua Kolom' },
            { value: 'nim', label: 'NIM Mahasiswa' },
            { value: 'nama', label: 'Nama Mahasiswa' },
            { value: 'prodi', label: 'Program Studi' },
            { value: 'perusahaan', label: 'Nama Perusahaan / Tempat KP' },
            { value: 'status', label: 'Status Pengajuan' }
        ];

        const COLUMNS_LAPORAN = [
            { value: 'all', label: 'Semua Kolom' },
            { value: 'nim', label: 'NIM Mahasiswa' },
            { value: 'nama', label: 'Nama Mahasiswa' },
            { value: 'prodi', label: 'Program Studi' },
            { value: 'perusahaan', label: 'Instansi / Perusahaan' },
            { value: 'pembimbing', label: 'Dosen Pembimbing 1' },
            { value: 'status', label: 'Status Verifikasi' }
        ];

        let filtersPersyaratan = [{ col: 'all', val: '' }];
        let appliedFiltersPersyaratan = [{ col: 'all', val: '' }];
        let pagePersyaratan = 1;
        let perPagePersyaratan = 25;

        let filtersLaporan = [{ col: 'all', val: '' }];
        let appliedFiltersLaporan = [{ col: 'all', val: '' }];
        let pageLaporan = 1;
        let perPageLaporan = 25;

        // Modal Active Item State
        let activeModalData = {
            mode: 'persyaratan', // 'persyaratan' or 'laporan'
            mhs: null,
            activeFileIndex: 0
        };

        // =========================================================================
        // TAB SWITCHING
        // =========================================================================
        function switchMainTab(tab) {
            currentMainTab = tab;
            const btnP = document.getElementById('btnTabPersyaratan');
            const btnL = document.getElementById('btnTabLaporan');
            const cntP = document.getElementById('contentTabPersyaratan');
            const cntL = document.getElementById('contentTabLaporan');

            if (tab === 'persyaratan') {
                btnP.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40';
                btnL.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-white/10 text-white hover:bg-white/20';
                cntP.classList.remove('hidden');
                cntL.classList.add('hidden');
                renderTablePersyaratan();
            } else {
                btnL.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40';
                btnP.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-white/10 text-white hover:bg-white/20';
                cntL.classList.remove('hidden');
                cntP.classList.add('hidden');
                renderTableLaporan();
            }

            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        }

        function switchSubTabPersyaratan(sub) {
            currentSubTabPersyaratan = sub;
            const btnBelum = document.getElementById('btnSubBelumAcc');
            const btnSudah = document.getElementById('btnSubSudahAcc');
            const badgeBelum = document.getElementById('badgeCountBelumAcc');
            const badgeSudah = document.getElementById('badgeCountSudahAcc');

            if (sub === 'belum_acc') {
                btnBelum.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer bg-orange-600 text-white shadow-md';
                btnSudah.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900 hover:bg-white/60';
                badgeBelum.className = 'px-2 py-0.5 rounded-full text-[10px] font-black bg-white text-orange-600';
                badgeSudah.className = 'px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-200 text-slate-700';
            } else {
                btnSudah.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer bg-emerald-600 text-white shadow-md';
                btnBelum.className = 'px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer text-slate-600 hover:text-slate-900 hover:bg-white/60';
                badgeSudah.className = 'px-2 py-0.5 rounded-full text-[10px] font-black bg-white text-emerald-600';
                badgeBelum.className = 'px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-200 text-slate-700';
            }

            pagePersyaratan = 1;
            renderTablePersyaratan();

            const url = new URL(window.location);
            url.searchParams.set('subtab', sub);
            window.history.replaceState({}, '', url);
        }

        // =========================================================================
        // MULTI-FILTER ROWS RENDERING (PERSYARATAN & LAPORAN)
        // =========================================================================
        function renderFilterRows(target) {
            const isPersyaratan = (target === 'persyaratan');
            const container = document.getElementById(isPersyaratan ? 'filterRowsPersyaratan' : 'filterRowsLaporan');
            const filters = isPersyaratan ? filtersPersyaratan : filtersLaporan;
            const columns = isPersyaratan ? COLUMNS_PERSYARATAN : COLUMNS_LAPORAN;

            if (!container) return;

            container.innerHTML = filters.map((f, i) => `
                <div class="filter-row-dw">
                    <span class="text-[10px] font-bold text-slate-500 w-16 shrink-0">Kriteria #${i + 1}:</span>
                    <select class="filter-col-dw" data-target="${target}" data-idx="${i}">
                        ${columns.map(c => `<option value="${c.value}" ${c.value === f.col ? 'selected' : ''}>${c.label}</option>`).join('')}
                    </select>
                    <input type="text" class="filter-val-dw" data-target="${target}" data-idx="${i}"
                           value="${(f.val || '').replace(/"/g, '&quot;')}"
                           placeholder="Ketik kata kunci pencarian..."
                           onkeydown="if(event.key==='Enter'){event.preventDefault();applyFilters('${target}');}">
                    ${i > 0 ? `<button type="button" class="btn-remove-filter-dw" onclick="removeFilterRow('${target}', ${i})" title="Hapus filter"><i class="bi bi-x-lg"></i></button>` : ''}
                </div>
            `).join('');

            updateFilterBadge(target);
        }

        function updateFilterBadge(target) {
            const isPersyaratan = (target === 'persyaratan');
            const filters = isPersyaratan ? filtersPersyaratan : filtersLaporan;
            const badge = document.getElementById(isPersyaratan ? 'filterCountBadgePersyaratan' : 'filterCountBadgeLaporan');
            const btnAdd = document.getElementById(isPersyaratan ? 'btnAddFilterPersyaratan' : 'btnAddFilterLaporan');

            if (badge) badge.textContent = `${filters.length}/4`;
            if (btnAdd) {
                btnAdd.style.opacity = filters.length >= 4 ? '0.5' : '1';
                btnAdd.style.pointerEvents = filters.length >= 4 ? 'none' : 'auto';
            }
        }

        function addFilterRow(target) {
            const isPersyaratan = (target === 'persyaratan');
            const filters = isPersyaratan ? filtersPersyaratan : filtersLaporan;
            if (filters.length >= 4) return;
            filters.push({ col: 'all', val: '' });
            renderFilterRows(target);
        }

        function removeFilterRow(target, idx) {
            const isPersyaratan = (target === 'persyaratan');
            const filters = isPersyaratan ? filtersPersyaratan : filtersLaporan;
            if (filters.length <= 1) return;
            filters.splice(idx, 1);
            renderFilterRows(target);
        }

        function applyFilters(target) {
            const isPersyaratan = (target === 'persyaratan');
            const filters = isPersyaratan ? filtersPersyaratan : filtersLaporan;

            document.querySelectorAll(`.filter-col-dw[data-target="${target}"]`).forEach(sel => {
                const idx = parseInt(sel.dataset.idx);
                if (filters[idx]) filters[idx].col = sel.value;
            });
            document.querySelectorAll(`.filter-val-dw[data-target="${target}"]`).forEach(inp => {
                const idx = parseInt(inp.dataset.idx);
                if (filters[idx]) filters[idx].val = inp.value;
            });

            if (isPersyaratan) {
                appliedFiltersPersyaratan = filters.map(f => ({ ...f }));
                pagePersyaratan = 1;
                renderTablePersyaratan();
            } else {
                appliedFiltersLaporan = filters.map(f => ({ ...f }));
                pageLaporan = 1;
                renderTableLaporan();
            }
        }

        function resetFilters(target) {
            if (target === 'persyaratan') {
                filtersPersyaratan = [{ col: 'all', val: '' }];
                appliedFiltersPersyaratan = [{ col: 'all', val: '' }];
                pagePersyaratan = 1;
                renderFilterRows('persyaratan');
                renderTablePersyaratan();
            } else {
                filtersLaporan = [{ col: 'all', val: '' }];
                appliedFiltersLaporan = [{ col: 'all', val: '' }];
                pageLaporan = 1;
                renderFilterRows('laporan');
                renderTableLaporan();
            }
        }

        // =========================================================================
        // FILTERING LOGIC
        // =========================================================================
        function matchMultiFilters(item, filters, target) {
            return filters.every(f => {
                const query = (f.val || '').trim().toLowerCase();
                if (!query) return true;
                const col = f.col;

                if (target === 'persyaratan') {
                    if (col === 'nim') return (item.nim || '').toLowerCase().includes(query);
                    if (col === 'nama') return (item.nama || '').toLowerCase().includes(query);
                    if (col === 'prodi') return (item.prodi || '').toLowerCase().includes(query);
                    if (col === 'perusahaan') return (item.nama_perusahaan || '').toLowerCase().includes(query);
                    if (col === 'status') return (item.status_file || '').toLowerCase().includes(query);
                    // 'all'
                    return (
                        (item.nim || '').toLowerCase().includes(query) ||
                        (item.nama || '').toLowerCase().includes(query) ||
                        (item.prodi || '').toLowerCase().includes(query) ||
                        (item.nama_perusahaan || '').toLowerCase().includes(query) ||
                        (item.status_file || '').toLowerCase().includes(query)
                    );
                } else {
                    if (col === 'nim') return (item.nim || '').toLowerCase().includes(query);
                    if (col === 'nama') return (item.nama || '').toLowerCase().includes(query);
                    if (col === 'prodi') return (item.prodi || '').toLowerCase().includes(query);
                    if (col === 'perusahaan') return (item.nama_perusahaan || '').toLowerCase().includes(query);
                    if (col === 'pembimbing') return (item.pembimbing1_nama || '').toLowerCase().includes(query);
                    if (col === 'status') return (item.status_adminlaa || '').toLowerCase().includes(query);
                    // 'all'
                    return (
                        (item.nim || '').toLowerCase().includes(query) ||
                        (item.nama || '').toLowerCase().includes(query) ||
                        (item.prodi || '').toLowerCase().includes(query) ||
                        (item.nama_perusahaan || '').toLowerCase().includes(query) ||
                        (item.pembimbing1_nama || '').toLowerCase().includes(query) ||
                        (item.status_adminlaa || '').toLowerCase().includes(query)
                    );
                }
            });
        }

        // =========================================================================
        // RENDERING TABEL TAB 1: PERSYARATAN KP
        // =========================================================================
        function renderTablePersyaratan() {
            const rawList = (currentSubTabPersyaratan === 'belum_acc') ? RAW_DATA.belum_acc : RAW_DATA.sudah_acc;
            const filtered = rawList.filter(item => matchMultiFilters(item, appliedFiltersPersyaratan, 'persyaratan'));
            const total = filtered.length;

            const tbody = document.getElementById('tbodyPersyaratan');
            if (!tbody) return;

            if (total === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-12 text-slate-400">
                            <i class="bi bi-inbox text-4xl block mb-2 text-slate-300"></i>
                            <p class="font-semibold text-sm">Tidak ada data mahasiswa ${currentSubTabPersyaratan === 'belum_acc' ? 'yang belum di-ACC' : 'yang sudah di-ACC'}.</p>
                            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian pada filter multi-kriteria.</p>
                        </td>
                    </tr>
                `;
                renderPaginationControls('persyaratan', 0, 0, 0);
                return;
            }

            const effectivePerPage = perPagePersyaratan >= 999999 ? total : perPagePersyaratan;
            const totalPages = Math.ceil(total / effectivePerPage);
            if (pagePersyaratan > totalPages) pagePersyaratan = totalPages;

            const startIdx = (pagePersyaratan - 1) * effectivePerPage;
            const endIdx = Math.min(startIdx + effectivePerPage, total);
            const pagedItems = filtered.slice(startIdx, endIdx);

            let rowsHtml = '';
            pagedItems.forEach((mhs, idx) => {
                const rowNo = startIdx + idx + 1;
                const totalReq = mhs.total_wajib || 0;
                const appCount = mhs.approved_wajib || 0;
                const upCount  = mhs.uploaded_wajib || 0;
                const rejCount = mhs.rejected_count || 0;

                let progressBadge = '';
                if (mhs.is_all_approved) {
                    progressBadge = `<span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Lengkap (${appCount}/${totalReq})</span>`;
                } else if (rejCount > 0) {
                    progressBadge = `<span class="badge badge-danger"><i class="bi bi-exclamation-octagon-fill"></i> ${rejCount} Revisi (${appCount}/${totalReq})</span>`;
                } else if (upCount === 0) {
                    progressBadge = `<span class="badge badge-secondary"><i class="bi bi-dash-circle"></i> Belum Upload</span>`;
                } else {
                    progressBadge = `<span class="badge badge-warning"><i class="bi bi-hourglass-split"></i> Pending (${appCount}/${totalReq} ACC)</span>`;
                }

                let statusFileBadge = '';
                const st = mhs.status_file || 'Sudah Upload Persyaratan';
                if (st === 'Disetujui LAA' || st === 'Selesai' || st === 'Sudah Plot Pembimbing') {
                    statusFileBadge = `<span class="badge badge-success">${st}</span>`;
                } else if (st === 'Revisi Persyaratan' || st === 'Revisi Laporan') {
                    statusFileBadge = `<span class="badge badge-danger">${st}</span>`;
                } else {
                    statusFileBadge = `<span class="badge badge-primary">${st}</span>`;
                }

                rowsHtml += `
                    <tr class="hover:bg-orange-50/40 transition">
                        <td class="py-4 px-4 text-center font-bold text-slate-500">${rowNo}</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 font-extrabold flex items-center justify-center shrink-0 text-xs">
                                    ${(mhs.nama || 'M').charAt(0).toUpperCase()}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-extrabold text-slate-900 text-xs sm:text-sm truncate">${mhs.nama || 'Mahasiswa'}</h4>
                                    <span class="text-orange-600 font-bold text-xs">${mhs.nim}</span>
                                    <span class="text-slate-400 text-xs ml-1">• ${mhs.prodi || 'Informatika'}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="min-w-0">
                                <span class="font-bold text-slate-800 text-xs block truncate">${mhs.nama_perusahaan || '-'}</span>
                                <span class="text-[11px] text-slate-500 block truncate">${mhs.posisi_pekerjaan || mhs.kota || '-'}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center">${progressBadge}</td>
                        <td class="py-4 px-4 text-center">${statusFileBadge}</td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <button type="button" onclick="openPreviewPersyaratan('${mhs.nim}')"
                                        class="px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                                    <i class="bi bi-eye-fill"></i> Periksa Berkas
                                </button>
                                ${!mhs.is_all_approved ? `
                                    <button type="button" onclick="confirmBulkAccPersyaratan('${mhs.nim}', '${(mhs.nama || '').replace(/'/g, "\\'")}')"
                                            class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1 cursor-pointer shadow-2xs"
                                            title="ACC Semua Berkas Mahasiswa Ini">
                                        <i class="bi bi-check-all"></i> ACC Semua
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = rowsHtml;
            renderPaginationControls('persyaratan', total, startIdx, endIdx);
        }

        // =========================================================================
        // RENDERING TABEL TAB 2: LAPORAN KP
        // =========================================================================
        function renderTableLaporan() {
            const rawList = RAW_DATA.laporan;
            const filtered = rawList.filter(item => matchMultiFilters(item, appliedFiltersLaporan, 'laporan'));
            const total = filtered.length;

            const tbody = document.getElementById('tbodyLaporan');
            if (!tbody) return;

            if (total === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-12 text-slate-400">
                            <i class="bi bi-inbox text-4xl block mb-2 text-slate-300"></i>
                            <p class="font-semibold text-sm">Tidak ada data laporan KP mahasiswa yang cocok.</p>
                            <p class="text-xs text-slate-400 mt-1">Laporan akan muncul setelah mahasiswa mengunggah berkas di menu Laporan KP.</p>
                        </td>
                    </tr>
                `;
                renderPaginationControls('laporan', 0, 0, 0);
                return;
            }

            const effectivePerPage = perPageLaporan >= 999999 ? total : perPageLaporan;
            const totalPages = Math.ceil(total / effectivePerPage);
            if (pageLaporan > totalPages) pageLaporan = totalPages;

            const startIdx = (pageLaporan - 1) * effectivePerPage;
            const endIdx = Math.min(startIdx + effectivePerPage, total);
            const pagedItems = filtered.slice(startIdx, endIdx);

            let rowsHtml = '';
            pagedItems.forEach((mhs, idx) => {
                const rowNo = startIdx + idx + 1;
                const hasFile = mhs.has_uploaded && mhs.file_laporan;
                const statusLaa = mhs.status_adminlaa || 'Pending';

                let statusBadge = '';
                if (statusLaa === 'Approved') {
                    statusBadge = `<span class="badge badge-success"><i class="bi bi-check-circle-fill"></i> Disetujui (Selesai)</span>`;
                } else if (statusLaa === 'Rejected') {
                    statusBadge = `<span class="badge badge-danger"><i class="bi bi-exclamation-triangle-fill"></i> Revisi Laporan</span>`;
                } else {
                    statusBadge = `<span class="badge badge-warning"><i class="bi bi-hourglass-split"></i> Pending Review</span>`;
                }

                rowsHtml += `
                    <tr class="hover:bg-orange-50/40 transition">
                        <td class="py-4 px-4 text-center font-bold text-slate-500">${rowNo}</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 font-extrabold flex items-center justify-center shrink-0 text-xs">
                                    ${(mhs.nama || 'M').charAt(0).toUpperCase()}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-extrabold text-slate-900 text-xs sm:text-sm truncate">${mhs.nama || 'Mahasiswa'}</h4>
                                    <span class="text-orange-600 font-bold text-xs">${mhs.nim}</span>
                                    <span class="text-slate-400 text-xs ml-1">• ${mhs.prodi || 'Informatika'}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="min-w-0">
                                <span class="font-bold text-slate-800 text-xs block truncate">${mhs.nama_perusahaan || '-'}</span>
                                <span class="text-[11px] text-slate-500 block truncate">Pembimbing: ${mhs.nama_tujuan || '-'}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="text-xs font-bold text-slate-800 block truncate">${mhs.pembimbing1_nama || '-'}</span>
                            <span class="text-[10px] text-slate-500 block">${mhs.dosen_pembimbing1 ? 'NIP: ' + mhs.dosen_pembimbing1 : 'Belum di-plot'}</span>
                        </td>
                        <td class="py-4 px-4 text-center">
                            ${hasFile ? `
                                <button type="button" onclick="openPreviewLaporan('${mhs.nim}')"
                                        class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-300 text-xs font-bold hover:bg-emerald-100 transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <i class="bi bi-file-earmark-pdf-fill text-rose-500"></i> Lihat PDF
                                </button>
                            ` : `<span class="badge badge-secondary text-xs">Belum Upload</span>`}
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-extrabold text-xs border border-slate-200">
                                ${mhs.poin || 0}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center">${statusBadge}</td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" onclick="openPreviewLaporan('${mhs.nim}')"
                                        class="px-3 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs">
                                    <i class="bi bi-shield-check"></i> Verifikasi
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = rowsHtml;
            renderPaginationControls('laporan', total, startIdx, endIdx);
        }

        // =========================================================================
        // PAGINATION CONTROLS
        // =========================================================================
        function changePerPage(target) {
            const isPersyaratan = (target === 'persyaratan');
            const select = document.getElementById(isPersyaratan ? 'perPagePersyaratan' : 'perPageLaporan');
            const val = select.value === 'all' ? 999999 : parseInt(select.value);

            if (isPersyaratan) {
                perPagePersyaratan = val;
                pagePersyaratan = 1;
                renderTablePersyaratan();
            } else {
                perPageLaporan = val;
                pageLaporan = 1;
                renderTableLaporan();
            }
        }

        function goToPage(target, page) {
            if (page < 1) return;
            if (target === 'persyaratan') {
                pagePersyaratan = page;
                renderTablePersyaratan();
            } else {
                pageLaporan = page;
                renderTableLaporan();
            }
        }

        function renderPaginationControls(target, totalItems, startIdx, endIdx) {
            const isPersyaratan = (target === 'persyaratan');
            const info = document.getElementById(isPersyaratan ? 'paginationInfoPersyaratan' : 'paginationInfoLaporan');
            const btns = document.getElementById(isPersyaratan ? 'paginationButtonsPersyaratan' : 'paginationButtonsLaporan');
            const curPage = isPersyaratan ? pagePersyaratan : pageLaporan;
            const perP = isPersyaratan ? perPagePersyaratan : perPageLaporan;

            if (!info || !btns) return;

            if (totalItems === 0) {
                info.textContent = 'Tidak ada data';
                btns.innerHTML = '';
                return;
            }

            info.textContent = `Menampilkan ${startIdx + 1}-${endIdx} dari ${totalItems} data`;
            if (perP >= 999999) { btns.innerHTML = ''; return; }

            const totalPages = Math.ceil(totalItems / perP);
            let html = '';
            html += `<button class="pagination-btn" onclick="goToPage('${target}', ${curPage - 1})" ${curPage === 1 ? 'disabled' : ''}><i class="bi bi-chevron-left"></i></button>`;

            const maxButtons = 5;
            let startPage = Math.max(1, curPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage + 1 < maxButtons) startPage = Math.max(1, endPage - maxButtons + 1);

            if (startPage > 1) {
                html += `<button class="pagination-btn" onclick="goToPage('${target}', 1)">1</button>`;
                if (startPage > 2) html += `<span class="pagination-ellipsis">…</span>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `<button class="pagination-btn ${i === curPage ? 'active' : ''}" onclick="goToPage('${target}', ${i})">${i}</button>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += `<span class="pagination-ellipsis">…</span>`;
                html += `<button class="pagination-btn" onclick="goToPage('${target}', ${totalPages})">${totalPages}</button>`;
            }

            html += `<button class="pagination-btn" onclick="goToPage('${target}', ${curPage + 1})" ${curPage === totalPages ? 'disabled' : ''}><i class="bi bi-chevron-right"></i></button>`;
            btns.innerHTML = html;
        }

        // =========================================================================
        // PREVIEW MODAL LOGIC (PERSYARATAN & LAPORAN)
        // =========================================================================
        function openPreviewPersyaratan(nim) {
            const allList = RAW_DATA.belum_acc.concat(RAW_DATA.sudah_acc);
            const mhs = allList.find(m => m.nim === nim);
            if (!mhs) return;

            activeModalData = {
                mode: 'persyaratan',
                mhs: mhs,
                activeFileIndex: 0
            };

            // Cari file pertama yang terunggah jika ada
            const uploadedIdx = (mhs.files || []).findIndex(f => f.file && f.file.length > 0);
            if (uploadedIdx !== -1) {
                activeModalData.activeFileIndex = uploadedIdx;
            }

            document.getElementById('modalPersyaratanBulkActionArea').classList.remove('hidden');
            renderPreviewModalContent();
            document.getElementById('previewModalOverlay').classList.add('active');
        }

        function openPreviewLaporan(nim) {
            const mhs = RAW_DATA.laporan.find(m => m.nim === nim);
            if (!mhs) return;

            activeModalData = {
                mode: 'laporan',
                mhs: mhs,
                activeFileIndex: 0
            };

            document.getElementById('modalPersyaratanBulkActionArea').classList.add('hidden');
            renderPreviewModalContent();
            document.getElementById('previewModalOverlay').classList.add('active');
        }

        function closePreviewModal() {
            document.getElementById('previewModalOverlay').classList.remove('active');
            document.getElementById('modalPdfFrame').src = 'about:blank';
        }

        function toggleModalFullscreen() {
            const box = document.querySelector('.preview-modal-box');
            if (!box) return;
            box.classList.toggle('fixed');
            box.classList.toggle('inset-0');
            box.classList.toggle('w-screen');
            box.classList.toggle('h-screen');
            box.classList.toggle('max-w-none');
            box.classList.toggle('max-h-none');
            box.classList.toggle('rounded-none');
        }

        function renderPreviewModalContent() {
            const mhs = activeModalData.mhs;
            if (!mhs) return;

            document.getElementById('modalMhsNama').textContent = mhs.nama || 'Mahasiswa';
            document.getElementById('modalMhsNim').textContent = mhs.nim;
            document.getElementById('modalMhsProdi').textContent = mhs.prodi || 'Informatika';
            document.getElementById('modalMhsPerusahaan').textContent = mhs.nama_perusahaan || '-';
            document.getElementById('modalMhsKota').textContent = mhs.kota || mhs.alamat_perusahaan || '-';
            document.getElementById('modalMhsP1').textContent = mhs.pembimbing1_nama ? 'Pembimbing: ' + mhs.pembimbing1_nama : 'Pembimbing: Belum di-plot';
            document.getElementById('modalMhsStatusKp').textContent = mhs.status_file || mhs.status_file_kp || 'Pending';

            const listContainer = document.getElementById('modalFilesListContainer');

            if (activeModalData.mode === 'persyaratan') {
                const files = mhs.files || [];
                listContainer.innerHTML = files.map((f, idx) => {
                    const isActive = (idx === activeModalData.activeFileIndex);
                    const isUp = f.file && f.file.length > 0;
                    let stBadge = '';
                    if (!isUp) stBadge = `<span class="badge badge-secondary text-[9px]">Belum Upload</span>`;
                    else if (f.status === 'Approved') stBadge = `<span class="badge badge-success text-[9px]">Disetujui</span>`;
                    else if (f.status === 'Rejected') stBadge = `<span class="badge badge-danger text-[9px]">Revisi</span>`;
                    else stBadge = `<span class="badge badge-warning text-[9px]">Pending</span>`;

                    return `
                        <div onclick="selectModalFileIndex(${idx})"
                             class="p-2.5 rounded-xl border transition cursor-pointer flex items-center justify-between gap-2 ${isActive ? 'bg-orange-100/90 border-orange-400 shadow-2xs' : 'bg-white border-stone-200 hover:bg-orange-50/50'}">
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-800 block truncate">${f.nama_berkas}</span>
                                <span class="text-[10px] text-slate-500 block truncate">${f.file_basename || 'Tidak ada file'}</span>
                            </div>
                            <div class="shrink-0">${stBadge}</div>
                        </div>
                    `;
                }).join('');

                const activeFile = files[activeModalData.activeFileIndex] || files[0];
                updatePreviewIframeAndActions(activeFile);
            } else {
                // Mode Laporan KP
                listContainer.innerHTML = `
                    <div class="p-3 rounded-xl border bg-orange-100/90 border-orange-400 shadow-2xs flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-800 block truncate">Laporan KP &amp; Form Penilaian</span>
                            <span class="text-[10px] text-slate-500 block truncate">${mhs.file_laporan_name || 'laporan_kp.pdf'}</span>
                        </div>
                        <div class="shrink-0">
                            <span class="badge ${mhs.status_adminlaa === 'Approved' ? 'badge-success' : (mhs.status_adminlaa === 'Rejected' ? 'badge-danger' : 'badge-warning')} text-[9px]">
                                ${mhs.status_adminlaa || 'Pending'}
                            </span>
                        </div>
                    </div>
                `;

                const fileObj = {
                    nama_berkas: 'Laporan KP & Lembar Penilaian Lapangan',
                    file_basename: mhs.file_laporan_name || '',
                    file_url: mhs.file_laporan_url || (mhs.file_laporan ? BASE_URL + mhs.file_laporan : ''),
                    status: mhs.status_adminlaa || 'Pending',
                    komentar: mhs.komentar || ''
                };
                updatePreviewIframeAndActions(fileObj);
            }
        }

        function selectModalFileIndex(idx) {
            activeModalData.activeFileIndex = idx;
            renderPreviewModalContent();
        }

        function updatePreviewIframeAndActions(fileObj) {
            const frame = document.getElementById('modalPdfFrame');
            const placeholder = document.getElementById('modalPdfEmptyPlaceholder');
            const docTitle = document.getElementById('modalPreviewDocTitle');
            const docSub = document.getElementById('modalPreviewDocSub');
            const dwnBtn = document.getElementById('modalDownloadBtn');
            const badge = document.getElementById('modalActiveFileStatus');

            if (!fileObj) return;

            docTitle.textContent = fileObj.nama_berkas || 'Dokumen Persyaratan';
            docSub.textContent = fileObj.file_basename || (fileObj.file_url ? 'File PDF' : 'Belum diunggah');

            if (fileObj.file_url && fileObj.file_url.length > 5) {
                frame.classList.remove('hidden');
                placeholder.classList.add('hidden');
                frame.src = fileObj.file_url;
                dwnBtn.href = fileObj.file_url;
                dwnBtn.classList.remove('hidden');
            } else {
                frame.classList.add('hidden');
                placeholder.classList.remove('hidden');
                frame.src = 'about:blank';
                dwnBtn.href = '#';
                dwnBtn.classList.add('hidden');
            }

            // Update badge status aktif
            const st = fileObj.status || 'Pending';
            badge.textContent = st;
            if (st === 'Approved') badge.className = 'badge badge-success text-xs';
            else if (st === 'Rejected') badge.className = 'badge badge-danger text-xs';
            else badge.className = 'badge badge-warning text-xs';
        }

        // =========================================================================
        // AJAX AKSI ACC & TOLAK VERIFIKASI
        // =========================================================================
        function confirmAccCurrentFile() {
            const mhs = activeModalData.mhs;
            if (!mhs) return;

            if (activeModalData.mode === 'persyaratan') {
                const files = mhs.files || [];
                const curFile = files[activeModalData.activeFileIndex];
                if (!curFile) return;

                Swal.fire({
                    title: 'Setujui Berkas?',
                    html: `Apakah Anda yakin ingin menyetujui (ACC) berkas <strong>${curFile.nama_berkas}</strong> untuk mahasiswa <strong>${mhs.nama}</strong> (${mhs.nim})?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Setujui (ACC)',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#94a3b8'
                }).then(result => {
                    if (result.isConfirmed) {
                        doAjaxAccBerkas(curFile.id_file, mhs.nim);
                    }
                });
            } else {
                // Mode Laporan KP
                Swal.fire({
                    title: 'Setujui Laporan KP?',
                    html: `Apakah Anda yakin ingin menyetujui (ACC) Laporan KP untuk mahasiswa <strong>${mhs.nama}</strong> (${mhs.nim})? Status KP mahasiswa akan menjadi <strong>Selesai</strong>.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, ACC Laporan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#94a3b8'
                }).then(result => {
                    if (result.isConfirmed) {
                        doAjaxAccLaporan(mhs.nim);
                    }
                });
            }
        }

        function promptRejectCurrentFile() {
            const mhs = activeModalData.mhs;
            if (!mhs) return;

            const isPersyaratan = (activeModalData.mode === 'persyaratan');
            const curFile = isPersyaratan ? (mhs.files || [])[activeModalData.activeFileIndex] : null;
            const docName = isPersyaratan ? (curFile ? curFile.nama_berkas : 'Berkas') : 'Laporan KP';

            Swal.fire({
                title: 'Tolak / Minta Revisi',
                html: `<p class="text-xs text-slate-600 mb-3">Masukkan catatan perbaikan untuk mahasiswa <strong>${mhs.nama}</strong> (${mhs.nim}) terkait dokumen <strong>${docName}</strong>:</p>`,
                input: 'textarea',
                inputPlaceholder: 'Tuliskan alasan penolakan atau petunjuk revisi berkas...',
                inputAttributes: { 'aria-label': 'Catatan Revisi' },
                showCancelButton: true,
                confirmButtonText: 'Kirim Revisi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                inputValidator: (value) => {
                    if (!value || !value.trim()) {
                        return 'Alasan / catatan revisi wajib diisi agar mahasiswa mengetahui bagian yang perlu diperbaiki!';
                    }
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const komentar = result.value.trim();
                    if (isPersyaratan) {
                        doAjaxRejectBerkas(curFile.id_file, mhs.nim, komentar);
                    } else {
                        doAjaxRejectLaporan(mhs.nim, komentar);
                    }
                }
            });
        }

        function confirmBulkAccPersyaratanCurrent() {
            const mhs = activeModalData.mhs;
            if (!mhs) return;
            confirmBulkAccPersyaratan(mhs.nim, mhs.nama);
        }

        function confirmBulkAccPersyaratan(nim, nama) {
            Swal.fire({
                title: 'ACC Seluruh Berkas?',
                html: `Apakah Anda yakin ingin menyetujui (ACC) <strong>seluruh berkas persyaratan</strong> untuk mahasiswa <strong>${nama || ''}</strong> (${nim})? Status mahasiswa akan otomatis berubah menjadi <strong>Disetujui LAA</strong>.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, ACC Semua Berkas',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#94a3b8'
            }).then(result => {
                if (result.isConfirmed) {
                    doAjaxBulkAccPersyaratan(nim);
                }
            });
        }

        // =========================================================================
        // AJAX CALLS
        // =========================================================================
        function doAjaxAccBerkas(idFile, nim) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const formData = new FormData();
            formData.append('id_file', idFile);
            formData.append('nim', nim);

            fetch(SITE_URL + 'adminlayanan/ajax_acc_berkas_kp', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1800, showConfirmButton: false });
                        reloadVerifikasiData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal: ' + err.message });
                });
        }

        function doAjaxRejectBerkas(idFile, nim, komentar) {
            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const formData = new FormData();
            formData.append('id_file', idFile);
            formData.append('nim', nim);
            formData.append('komentar', komentar);

            fetch(SITE_URL + 'adminlayanan/ajax_reject_berkas_kp', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Tersimpan', text: res.message, timer: 1800, showConfirmButton: false });
                        reloadVerifikasiData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal: ' + err.message });
                });
        }

        function doAjaxBulkAccPersyaratan(nim) {
            Swal.fire({ title: 'Memproses ACC Semua...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const formData = new FormData();
            formData.append('nim', nim);

            fetch(SITE_URL + 'adminlayanan/ajax_acc_semua_kp', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Disetujui!', text: res.message, confirmButtonColor: '#059669' });
                        reloadVerifikasiData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal: ' + err.message });
                });
        }

        function doAjaxAccLaporan(nim) {
            Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const formData = new FormData();
            formData.append('nim', nim);

            fetch(SITE_URL + 'adminlayanan/ajax_acc_laporan_kp', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Laporan Disetujui!', text: res.message, confirmButtonColor: '#059669' });
                        reloadVerifikasiData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal: ' + err.message });
                });
        }

        function doAjaxRejectLaporan(nim, komentar) {
            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const formData = new FormData();
            formData.append('nim', nim);
            formData.append('komentar', komentar);

            fetch(SITE_URL + 'adminlayanan/ajax_reject_laporan_kp', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire({ icon: 'success', title: 'Diminta Revisi', text: res.message, timer: 1800, showConfirmButton: false });
                        reloadVerifikasiData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal: ' + err.message });
                });
        }

        function reloadVerifikasiData() {
            fetch(SITE_URL + 'adminlayanan/ajax_get_verifikasi_kp_data')
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        RAW_DATA.belum_acc = res.pengajuan.belum_acc || [];
                        RAW_DATA.sudah_acc = res.pengajuan.sudah_acc || [];
                        RAW_DATA.laporan   = res.laporan || [];

                        document.getElementById('badgeCountBelumAcc').textContent = RAW_DATA.belum_acc.length;
                        document.getElementById('badgeCountSudahAcc').textContent = RAW_DATA.sudah_acc.length;
                        document.getElementById('badgeTotalLaporan').textContent = RAW_DATA.laporan.length;

                        renderTablePersyaratan();
                        renderTableLaporan();

                        // Update isi modal jika sedang terbuka
                        if (document.getElementById('previewModalOverlay').classList.contains('active') && activeModalData.mhs) {
                            const curNim = activeModalData.mhs.nim;
                            if (activeModalData.mode === 'persyaratan') {
                                const refreshed = RAW_DATA.belum_acc.concat(RAW_DATA.sudah_acc).find(m => m.nim === curNim);
                                if (refreshed) {
                                    activeModalData.mhs = refreshed;
                                    renderPreviewModalContent();
                                }
                            } else {
                                const refreshed = RAW_DATA.laporan.find(m => m.nim === curNim);
                                if (refreshed) {
                                    activeModalData.mhs = refreshed;
                                    renderPreviewModalContent();
                                }
                            }
                        }
                    }
                })
                .catch(err => console.error('Gagal memuat ulang data realtime:', err));
        }

        // =========================================================================
        // INISIALISASI SAAT HALAMAN DIMUAT
        // =========================================================================
        document.addEventListener('DOMContentLoaded', function() {
            renderFilterRows('persyaratan');
            renderFilterRows('laporan');
            renderTablePersyaratan();
            renderTableLaporan();
        });
    </script>
</body>
</html>
