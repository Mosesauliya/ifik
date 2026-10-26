<?php
/**
 * Halaman Plotting Pembimbing 1 Kerja Praktik (KP)
 * Path: application/views/koordinator_kp/plotting.php
 *
 * Akses: Koordinator KP (role_id 23) & Admin System (role_id 1)
 *
 * Syarat Tampil: Mahasiswa HANYA muncul jika berkas persyaratannya sudah di-ACC oleh Admin Layanan (LAA) di Tab 1.
 * Fitur: Multi-search bar, pagination, rotating border table, modal plotting pembimbing, & update realtime.
 */

$sub_tab = $sub_tab ?? 'belum_diplot';
$total_belum_diplot = count($belum_diplot ?? []);
$total_sudah_diplot = count($sudah_diplot ?? []);
$total_semua        = $total_belum_diplot + $total_sudah_diplot;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Plotting Pembimbing 1 Kerja Praktik (KP) - Koordinator KP'; ?></title>
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

        @keyframes popInCard { 0% { opacity: 0; transform: scale(0.92) translateY(20px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes fadeInDownSmooth { 0% { opacity: 0; transform: translateY(-12px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-pop-in { animation: popInCard 0.35s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }

        /* Modal Overlay Plotting */
        #modalPlottingOverlay {
            position: fixed; inset: 0; z-index: 9999; display: none;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(5px);
            align-items: center; justify-content: center; padding: 1rem;
        }
        #modalPlottingOverlay.active { display: flex; }
        .plotting-modal-box {
            background: #ffffff; border-radius: 1.5rem; width: 620px; max-width: 96vw;
            overflow: hidden; display: flex; flex-direction: column;
            box-shadow: 0 25px 60px -15px rgba(15,23,42,0.5);
            border: 1px solid #fed7aa;
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

        <!-- HERO HEADER (Gaya Dosen Bimbingan / Koordinator TA) -->
        <div class="bg-gradient-to-r from-[#9a3412] via-[#ea580c] to-[#c2410c] rounded-3xl p-7 sm:p-9 relative overflow-hidden shadow-2xl text-white">
            <div class="relative z-10 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-8">
                <div class="space-y-3 max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold">
                        <i class="bi bi-person-fill-gear text-amber-200"></i> Modul Koordinator Kerja Praktik (KP)
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        Plotting Dosen Pembimbing 1 KP
                    </h1>
                    <p class="text-xs sm:text-sm text-orange-100 font-normal leading-relaxed">
                        Tentukan Dosen Pembimbing 1 untuk mahasiswa yang seluruh berkas persyaratannya telah diverifikasi dan disetujui (ACC) oleh Admin Layanan (LAA).
                    </p>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-black/25 border border-white/20 text-xs font-semibold text-orange-100">
                        <i class="bi bi-info-circle-fill text-amber-300"></i>
                        <span>Mahasiswa hanya muncul setelah seluruh berkas persyaratan berstatus <strong>Disetujui LAA</strong>.</span>
                    </div>
                </div>

                <!-- Sub-tab Navigasi Plotting -->
                <div class="w-full xl:w-auto bg-black/25 backdrop-blur-xl rounded-3xl p-3 border border-white/20 shadow-2xl shrink-0">
                    <div class="flex items-center gap-2">
                        <!-- Sub-tab Belum Diplot -->
                        <button type="button" onclick="switchSubTabPlotting('belum_diplot')" id="btnTabBelumDiplot"
                                class="px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer <?= ($sub_tab === 'belum_diplot') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40' : 'bg-white/10 text-white hover:bg-white/20'; ?>">
                            <i class="bi bi-person-plus-fill text-base"></i>
                            <div class="text-left">
                                <span class="block">Belum Di-Plot</span>
                                <span class="text-[10px] font-normal text-orange-100/90 block">Menunggu Penetapan P1</span>
                            </div>
                            <span class="ml-1 px-2.5 py-0.5 rounded-full bg-black/30 text-amber-200 text-xs font-black" id="badgeCountBelumDiplot">
                                <?= $total_belum_diplot ?>
                            </span>
                        </button>

                        <!-- Sub-tab Sudah Diplot -->
                        <button type="button" onclick="switchSubTabPlotting('sudah_diplot')" id="btnTabSudahDiplot"
                                class="px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer <?= ($sub_tab === 'sudah_diplot') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40' : 'bg-white/10 text-white hover:bg-white/20'; ?>">
                            <i class="bi bi-person-check-fill text-base"></i>
                            <div class="text-left">
                                <span class="block">Sudah Di-Plot</span>
                                <span class="text-[10px] font-normal text-orange-100/90 block">Telah Memiliki P1</span>
                            </div>
                            <span class="ml-1 px-2.5 py-0.5 rounded-full bg-black/30 text-emerald-300 text-xs font-black" id="badgeCountSudahDiplot">
                                <?= $total_sudah_diplot ?>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTAINER CARD TABEL PLOTTING -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-lg shadow-orange-500/5 space-y-6 border border-slate-200">

            <div class="flex flex-wrap items-center justify-between border-b border-orange-100 pb-4 gap-3">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <i class="bi bi-people-fill text-orange-500 text-xl"></i>
                        Daftar Mahasiswa Siap Bimbingan KP
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5" id="descSubTabInfo">
                        Mahasiswa yang belum ditetapkan Dosen Pembimbing 1.
                    </p>
                </div>
                <div class="text-xs font-bold text-slate-600 px-3.5 py-1.5 rounded-full bg-orange-50 border border-orange-200">
                    Total Mahasiswa Siap: <span id="badgeTotalSiap" class="text-orange-600 font-extrabold"><?= $total_semua ?></span> Mahasiswa
                </div>
            </div>

            <!-- Multi-kriteria Search Bar (Identik Dosen Bimbingan) -->
            <div id="multiFilterPanelPlotting" class="bg-slate-50/70 rounded-2xl border border-slate-200 p-4 shadow-2xs">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-700 flex items-center gap-1.5">
                        <i class="bi bi-funnel-fill"></i> Filter Multi-Kriteria (Maksimal 4 Kriteria)
                    </span>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnAddFilterPlotting" onclick="addFilterRowPlotting()"
                                class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-2xs flex items-center gap-1.5 transition cursor-pointer">
                            <i class="bi bi-plus-lg"></i> Tambah Filter
                            <span id="filterCountBadgePlotting" class="bg-white/30 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">1/4</span>
                        </button>
                        <button type="button" onclick="resetFiltersPlotting()"
                                class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-orange-50 transition cursor-pointer flex items-center gap-1.5">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </div>

                <div id="filterRowsPlotting" class="space-y-2 mb-3"></div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
                    <p class="text-[10px] text-slate-500 font-medium italic">
                        <i class="bi bi-info-circle"></i> Tekan <strong>Enter</strong> atau klik <strong>Cari</strong> untuk memfilter daftar mahasiswa.
                    </p>
                    <button type="button" onclick="applyFiltersPlotting()"
                            class="px-5 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-md flex items-center gap-1.5 transition cursor-pointer">
                        <i class="bi bi-search"></i> Cari Data
                    </button>
                </div>
            </div>

            <!-- Kontrol Pagination -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1 px-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <label class="text-[11px] font-bold text-slate-600 whitespace-nowrap">Tampilkan:</label>
                    <select id="perPagePlotting" onchange="changePerPagePlotting()" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-bold bg-white focus:ring-orange-500 focus:border-orange-500 cursor-pointer outline-none">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">Semua</option>
                    </select>
                    <span class="text-[11px] text-slate-500 font-medium" id="paginationInfoPlotting"></span>
                </div>
                <div class="flex items-center gap-1 flex-wrap" id="paginationButtonsPlotting"></div>
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
                                <th class="py-4 px-4">Instansi Tempat KP &amp; Posisi</th>
                                <th class="py-4 px-4 text-center">Verifikasi Syarat LAA</th>
                                <th class="py-4 px-4">Dosen Pembimbing 1</th>
                                <th class="py-4 px-4 text-center">Aksi Plotting</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium bg-white" id="tbodyPlotting">
                            <tr>
                                <td colspan="6" class="text-center py-10 text-slate-400">
                                    <i class="bi bi-arrow-repeat animate-spin text-lg mr-2"></i> Memuat data mahasiswa siap bimbingan...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>
    </div>

    <!-- =========================================================================
         MODAL DIALOG PLOTTING DOSEN PEMBIMBING 1
         ========================================================================= -->
    <div id="modalPlottingOverlay" onclick="if(event.target===this) closeModalPlotting()">
        <div class="plotting-modal-box animate-pop-in">
            <!-- Modal Header -->
            <div class="p-5 bg-gradient-to-r from-orange-600 to-amber-500 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-white/20 text-white flex items-center justify-center text-lg">
                        <i class="bi bi-person-fill-gear"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold leading-tight">Plotting Dosen Pembimbing 1 KP</h3>
                        <p class="text-xs text-orange-100">Pilih dosen yang akan membimbing mahasiswa dalam Kerja Praktik.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalPlotting()" class="text-white hover:text-orange-200 text-2xl font-bold leading-none cursor-pointer">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 overflow-y-auto max-h-[75vh]">
                <!-- Ringkasan Mahasiswa -->
                <div class="p-4 rounded-2xl bg-orange-50/70 border border-orange-200/80 space-y-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-orange-700 block">Identitas Mahasiswa</span>
                            <h4 class="text-sm font-extrabold text-slate-900" id="plotModalNamaMhs">-</h4>
                            <p class="text-xs text-orange-600 font-bold" id="plotModalNimMhs">-</p>
                        </div>
                        <span class="badge badge-success text-[10px]">Syarat Disetujui LAA</span>
                    </div>
                    <div class="pt-2 border-t border-orange-200/60 text-xs text-slate-700 grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-bold">Program Studi:</span>
                            <span class="font-semibold text-slate-800" id="plotModalProdiMhs">-</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-bold">Tempat KP:</span>
                            <span class="font-semibold text-slate-800 truncate block" id="plotModalPerusahaanMhs">-</span>
                        </div>
                    </div>
                </div>

                <!-- Form Pilihan Dosen Pembimbing 1 -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Pilih Dosen Pembimbing 1 <span class="text-rose-500">*</span>
                    </label>

                    <!-- Filter / Cari Dosen Cepat -->
                    <div class="relative">
                        <input type="text" id="plotDosenSearchInput" oninput="filterDosenOptions(this.value)"
                               placeholder="Ketik nama atau NIP dosen untuk memfilter..."
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-orange-400 outline-none">
                        <i class="bi bi-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>

                    <!-- Select List Dosen -->
                    <select id="plotDosenSelect" size="8" class="w-full p-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-orange-400 outline-none cursor-pointer bg-white">
                        <!-- Diisi via JS -->
                    </select>
                    <p class="text-[11px] text-slate-500 italic">
                        <i class="bi bi-info-circle"></i> Klik salah satu dosen dari daftar di atas, kemudian klik <strong>Simpan Plotting</strong>.
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 px-6 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModalPlotting()"
                        class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="submitPlotting()" id="btnSubmitPlotting"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Simpan Plotting</span>
                </button>
            </div>
        </div>
    </div>

    <!-- DATASET DARI SERVER UNTUK JAVASCRIPT TABLE & FILTER -->
    <script>
        const RAW_DATA = {
            belum_diplot: <?= json_encode($belum_diplot ?? []); ?>,
            sudah_diplot: <?= json_encode($sudah_diplot ?? []); ?>,
            dosen_list:   <?= json_encode($dosen_list ?? []); ?>
        };

        const BASE_URL = '<?= rtrim(base_url(), '/') . '/'; ?>';
        const SITE_URL = '<?= rtrim(site_url(), '/') . '/'; ?>';

        let currentSubTabPlotting = '<?= $sub_tab ?>';

        const COLUMNS_PLOTTING = [
            { value: 'all', label: 'Semua Kolom' },
            { value: 'nim', label: 'NIM Mahasiswa' },
            { value: 'nama', label: 'Nama Mahasiswa' },
            { value: 'prodi', label: 'Program Studi' },
            { value: 'perusahaan', label: 'Instansi / Tempat KP' },
            { value: 'pembimbing', label: 'Dosen Pembimbing 1' }
        ];

        let filtersPlotting = [{ col: 'all', val: '' }];
        let appliedFiltersPlotting = [{ col: 'all', val: '' }];
        let pagePlotting = 1;
        let perPagePlotting = 25;

        let currentActiveNimToPlot = null;

        // =========================================================================
        // SUB-TAB SWITCHING (BELUM DI-PLOT VS SUDAH DI-PLOT)
        // =========================================================================
        function switchSubTabPlotting(sub) {
            currentSubTabPlotting = sub;
            const btnBelum = document.getElementById('btnTabBelumDiplot');
            const btnSudah = document.getElementById('btnTabSudahDiplot');
            const desc = document.getElementById('descSubTabInfo');

            if (sub === 'belum_diplot') {
                btnBelum.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40';
                btnSudah.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-white/10 text-white hover:bg-white/20';
                desc.textContent = 'Mahasiswa yang seluruh berkas persyaratannya telah di-ACC dan belum ditetapkan Dosen Pembimbing 1.';
            } else {
                btnSudah.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg shadow-orange-950/40';
                btnBelum.className = 'px-5 py-3 rounded-2xl font-extrabold text-xs sm:text-sm transition-all duration-200 flex items-center gap-2.5 cursor-pointer bg-white/10 text-white hover:bg-white/20';
                desc.textContent = 'Mahasiswa yang sudah memiliki Dosen Pembimbing 1 (Anda dapat mengubah / re-plotting jika diperlukan).';
            }

            pagePlotting = 1;
            renderTablePlotting();

            const url = new URL(window.location);
            url.searchParams.set('subtab', sub);
            window.history.replaceState({}, '', url);
        }

        // =========================================================================
        // MULTI-FILTER ROWS RENDERING
        // =========================================================================
        function renderFilterRowsPlotting() {
            const container = document.getElementById('filterRowsPlotting');
            if (!container) return;

            container.innerHTML = filtersPlotting.map((f, i) => `
                <div class="filter-row-dw">
                    <span class="text-[10px] font-bold text-slate-500 w-16 shrink-0">Kriteria #${i + 1}:</span>
                    <select class="filter-col-dw" data-idx="${i}">
                        ${COLUMNS_PLOTTING.map(c => `<option value="${c.value}" ${c.value === f.col ? 'selected' : ''}>${c.label}</option>`).join('')}
                    </select>
                    <input type="text" class="filter-val-dw" data-idx="${i}"
                           value="${(f.val || '').replace(/"/g, '&quot;')}"
                           placeholder="Ketik kata kunci pencarian..."
                           onkeydown="if(event.key==='Enter'){event.preventDefault();applyFiltersPlotting();}">
                    ${i > 0 ? `<button type="button" class="btn-remove-filter-dw" onclick="removeFilterRowPlotting(${i})" title="Hapus filter"><i class="bi bi-x-lg"></i></button>` : ''}
                </div>
            `).join('');

            updateFilterBadgePlotting();
        }

        function updateFilterBadgePlotting() {
            const badge = document.getElementById('filterCountBadgePlotting');
            const btnAdd = document.getElementById('btnAddFilterPlotting');
            if (badge) badge.textContent = `${filtersPlotting.length}/4`;
            if (btnAdd) {
                btnAdd.style.opacity = filtersPlotting.length >= 4 ? '0.5' : '1';
                btnAdd.style.pointerEvents = filtersPlotting.length >= 4 ? 'none' : 'auto';
            }
        }

        function addFilterRowPlotting() {
            if (filtersPlotting.length >= 4) return;
            filtersPlotting.push({ col: 'all', val: '' });
            renderFilterRowsPlotting();
        }

        function removeFilterRowPlotting(idx) {
            if (filtersPlotting.length <= 1) return;
            filtersPlotting.splice(idx, 1);
            renderFilterRowsPlotting();
        }

        function applyFiltersPlotting() {
            document.querySelectorAll('#filterRowsPlotting .filter-col-dw').forEach(sel => {
                const idx = parseInt(sel.dataset.idx);
                if (filtersPlotting[idx]) filtersPlotting[idx].col = sel.value;
            });
            document.querySelectorAll('#filterRowsPlotting .filter-val-dw').forEach(inp => {
                const idx = parseInt(inp.dataset.idx);
                if (filtersPlotting[idx]) filtersPlotting[idx].val = inp.value;
            });

            appliedFiltersPlotting = filtersPlotting.map(f => ({ ...f }));
            pagePlotting = 1;
            renderTablePlotting();
        }

        function resetFiltersPlotting() {
            filtersPlotting = [{ col: 'all', val: '' }];
            appliedFiltersPlotting = [{ col: 'all', val: '' }];
            pagePlotting = 1;
            renderFilterRowsPlotting();
            renderTablePlotting();
        }

        function matchMultiFiltersPlotting(item, filters) {
            return filters.every(f => {
                const query = (f.val || '').trim().toLowerCase();
                if (!query) return true;
                const col = f.col;

                if (col === 'nim') return (item.nim || '').toLowerCase().includes(query);
                if (col === 'nama') return (item.nama || '').toLowerCase().includes(query);
                if (col === 'prodi') return (item.prodi || '').toLowerCase().includes(query);
                if (col === 'perusahaan') return (item.nama_perusahaan || '').toLowerCase().includes(query);
                if (col === 'pembimbing') return (item.pembimbing1_nama || '').toLowerCase().includes(query);
                // 'all'
                return (
                    (item.nim || '').toLowerCase().includes(query) ||
                    (item.nama || '').toLowerCase().includes(query) ||
                    (item.prodi || '').toLowerCase().includes(query) ||
                    (item.nama_perusahaan || '').toLowerCase().includes(query) ||
                    (item.pembimbing1_nama || '').toLowerCase().includes(query)
                );
            });
        }

        // =========================================================================
        // RENDERING TABEL PLOTTING
        // =========================================================================
        function renderTablePlotting() {
            const rawList = (currentSubTabPlotting === 'belum_diplot') ? RAW_DATA.belum_diplot : RAW_DATA.sudah_diplot;
            const filtered = rawList.filter(item => matchMultiFiltersPlotting(item, appliedFiltersPlotting));
            const total = filtered.length;

            const tbody = document.getElementById('tbodyPlotting');
            if (!tbody) return;

            if (total === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-12 text-slate-400">
                            <i class="bi bi-inbox text-4xl block mb-2 text-slate-300"></i>
                            <p class="font-semibold text-sm">Tidak ada mahasiswa ${currentSubTabPlotting === 'belum_diplot' ? 'yang belum di-plot' : 'yang sudah di-plot'}.</p>
                            <p class="text-xs text-slate-400 mt-1">Hanya mahasiswa yang persyaratannya disetujui Admin LAA yang akan muncul di halaman ini.</p>
                        </td>
                    </tr>
                `;
                renderPaginationControlsPlotting(0, 0, 0);
                return;
            }

            const effectivePerPage = perPagePlotting >= 999999 ? total : perPagePlotting;
            const totalPages = Math.ceil(total / effectivePerPage);
            if (pagePlotting > totalPages) pagePlotting = totalPages;

            const startIdx = (pagePlotting - 1) * effectivePerPage;
            const endIdx = Math.min(startIdx + effectivePerPage, total);
            const pagedItems = filtered.slice(startIdx, endIdx);

            let rowsHtml = '';
            pagedItems.forEach((mhs, idx) => {
                const rowNo = startIdx + idx + 1;
                const hasP1 = mhs.has_pembimbing1 && mhs.pembimbing1_nama;

                let p1Display = '';
                if (hasP1) {
                    p1Display = `
                        <div>
                            <span class="text-xs font-bold text-slate-900 block truncate">${mhs.pembimbing1_nama}</span>
                            <span class="text-[10px] text-slate-500 block">NIP / Kode: ${mhs.dosen_pembimbing1 || '-'}</span>
                        </div>
                    `;
                } else {
                    p1Display = `<span class="badge badge-warning text-xs"><i class="bi bi-hourglass-split"></i> Belum Di-Plot</span>`;
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
                        <td class="py-4 px-4 text-center">
                            <span class="badge badge-success text-xs">
                                <i class="bi bi-patch-check-fill"></i> Disetujui LAA
                            </span>
                        </td>
                        <td class="py-4 px-4">${p1Display}</td>
                        <td class="py-4 px-4 text-center">
                            <button type="button" onclick="openModalPlotting('${mhs.nim}')"
                                    class="px-4 py-2 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5 mx-auto cursor-pointer">
                                <i class="bi bi-person-fill-gear"></i>
                                <span>${hasP1 ? 'Ubah Pembimbing' : 'Plot Pembimbing 1'}</span>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = rowsHtml;
            renderPaginationControlsPlotting(total, startIdx, endIdx);
        }

        // =========================================================================
        // PAGINATION CONTROLS
        // =========================================================================
        function changePerPagePlotting() {
            const select = document.getElementById('perPagePlotting');
            perPagePlotting = select.value === 'all' ? 999999 : parseInt(select.value);
            pagePlotting = 1;
            renderTablePlotting();
        }

        function goToPagePlotting(page) {
            if (page < 1) return;
            pagePlotting = page;
            renderTablePlotting();
        }

        function renderPaginationControlsPlotting(totalItems, startIdx, endIdx) {
            const info = document.getElementById('paginationInfoPlotting');
            const btns = document.getElementById('paginationButtonsPlotting');
            if (!info || !btns) return;

            if (totalItems === 0) {
                info.textContent = 'Tidak ada data';
                btns.innerHTML = '';
                return;
            }

            info.textContent = `Menampilkan ${startIdx + 1}-${endIdx} dari ${totalItems} data`;
            if (perPagePlotting >= 999999) { btns.innerHTML = ''; return; }

            const totalPages = Math.ceil(totalItems / perPagePlotting);
            let html = '';
            html += `<button class="pagination-btn" onclick="goToPagePlotting(${pagePlotting - 1})" ${pagePlotting === 1 ? 'disabled' : ''}><i class="bi bi-chevron-left"></i></button>`;

            const maxButtons = 5;
            let startPage = Math.max(1, pagePlotting - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage + 1 < maxButtons) startPage = Math.max(1, endPage - maxButtons + 1);

            if (startPage > 1) {
                html += `<button class="pagination-btn" onclick="goToPagePlotting(1)">1</button>`;
                if (startPage > 2) html += `<span class="pagination-ellipsis">…</span>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                html += `<button class="pagination-btn ${i === pagePlotting ? 'active' : ''}" onclick="goToPagePlotting(${i})">${i}</button>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += `<span class="pagination-ellipsis">…</span>`;
                html += `<button class="pagination-btn" onclick="goToPagePlotting(${totalPages})">${totalPages}</button>`;
            }

            html += `<button class="pagination-btn" onclick="goToPagePlotting(${pagePlotting + 1})" ${pagePlotting === totalPages ? 'disabled' : ''}><i class="bi bi-chevron-right"></i></button>`;
            btns.innerHTML = html;
        }

        // =========================================================================
        // MODAL PLOTTING LOGIC
        // =========================================================================
        function openModalPlotting(nim) {
            const allList = RAW_DATA.belum_diplot.concat(RAW_DATA.sudah_diplot);
            const mhs = allList.find(m => m.nim === nim);
            if (!mhs) return;

            currentActiveNimToPlot = nim;

            document.getElementById('plotModalNamaMhs').textContent = mhs.nama || 'Mahasiswa';
            document.getElementById('plotModalNimMhs').textContent = mhs.nim;
            document.getElementById('plotModalProdiMhs').textContent = mhs.prodi || 'Informatika';
            document.getElementById('plotModalPerusahaanMhs').textContent = mhs.nama_perusahaan || '-';
            document.getElementById('plotDosenSearchInput').value = '';

            renderDosenOptions(RAW_DATA.dosen_list, mhs.dosen_pembimbing1);

            document.getElementById('modalPlottingOverlay').classList.add('active');
        }

        function closeModalPlotting() {
            document.getElementById('modalPlottingOverlay').classList.remove('active');
            currentActiveNimToPlot = null;
        }

        function renderDosenOptions(list, selectedIdentifier) {
            const select = document.getElementById('plotDosenSelect');
            if (!select) return;

            if (!list || list.length === 0) {
                select.innerHTML = '<option value="" disabled class="p-2 text-slate-400">Tidak ada data dosen yang tersedia.</option>';
                return;
            }

            select.innerHTML = list.map(d => {
                const isSelected = (d.nip === selectedIdentifier || d.id === selectedIdentifier);
                const nipText = d.nip ? `[${d.nip}] ` : (d.kode_dosen ? `[${d.kode_dosen}] ` : '');
                return `
                    <option value="${d.nip || d.id}" ${isSelected ? 'selected' : ''} class="p-2.5 rounded-lg border-b border-slate-100 hover:bg-orange-50 font-medium">
                        ${nipText}${d.nama_dosen} (${d.prodi || 'Informatika'})
                    </option>
                `;
            }).join('');
        }

        function filterDosenOptions(keyword) {
            const q = (keyword || '').trim().toLowerCase();
            const filtered = RAW_DATA.dosen_list.filter(d => {
                return (d.nama_dosen || '').toLowerCase().includes(q) ||
                       (d.nip || '').toLowerCase().includes(q) ||
                       (d.kode_dosen || '').toLowerCase().includes(q);
            });
            renderDosenOptions(filtered, null);
        }

        function submitPlotting() {
            if (!currentActiveNimToPlot) return;

            const select = document.getElementById('plotDosenSelect');
            const dosenIdentifier = select.value;

            if (!dosenIdentifier) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Dosen',
                    text: 'Silakan pilih Dosen Pembimbing 1 dari daftar terlebih dahulu.'
                });
                return;
            }

            const selectedOptionText = select.options[select.selectedIndex]?.text || 'Dosen Terpilih';

            Swal.fire({
                title: 'Konfirmasi Plotting',
                html: `Tetapkan <strong>${selectedOptionText}</strong> sebagai Dosen Pembimbing 1 untuk mahasiswa NIM <strong>${currentActiveNimToPlot}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tetapkan Pembimbing',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#94a3b8'
            }).then(result => {
                if (result.isConfirmed) {
                    doAjaxSubmitPlotting(currentActiveNimToPlot, dosenIdentifier);
                }
            });
        }

        function doAjaxSubmitPlotting(nim, dosenIdentifier) {
            Swal.fire({ title: 'Menyimpan Plotting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            const formData = new FormData();
            formData.append('nim', nim);
            formData.append('dosen_identifier', dosenIdentifier);

            fetch(SITE_URL + 'koordinatorkp/ajax_plot_pembimbing', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Plotting Berhasil!',
                        text: res.message || 'Dosen Pembimbing 1 berhasil ditetapkan.',
                        confirmButtonColor: '#ea580c'
                    });
                    closeModalPlotting();
                    reloadPlottingData();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Plotting',
                        text: res.message || 'Terjadi kesalahan sistem.'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error Koneksi',
                    text: 'Gagal menghubungi server: ' + err.message
                });
            });
        }

        function reloadPlottingData() {
            fetch(SITE_URL + 'koordinatorkp/ajax_get_plotting_data')
                .then(r => r.json())
                .then(res => {
                    if (res.status) {
                        RAW_DATA.belum_diplot = res.belum_diplot || [];
                        RAW_DATA.sudah_diplot = res.sudah_diplot || [];

                        document.getElementById('badgeCountBelumDiplot').textContent = RAW_DATA.belum_diplot.length;
                        document.getElementById('badgeCountSudahDiplot').textContent = RAW_DATA.sudah_diplot.length;
                        document.getElementById('badgeTotalSiap').textContent = RAW_DATA.belum_diplot.length + RAW_DATA.sudah_diplot.length;

                        renderTablePlotting();
                    }
                })
                .catch(err => console.error('Gagal memuat ulang data plotting:', err));
        }

        // =========================================================================
        // INISIALISASI SAAT HALAMAN DIMUAT
        // =========================================================================
        document.addEventListener('DOMContentLoaded', function() {
            renderFilterRowsPlotting();
            renderTablePlotting();
        });
    </script>
</body>
</html>
