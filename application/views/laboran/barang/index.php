<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$active_tab = $active_tab ?? 'pending';
$pending_count = (int)($pending_count ?? 0);
$peminjaman_list = $peminjaman_list ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'Approval Peminjaman Barang - Laboran') ?></title>

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
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
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
                margin-left: 270px;
                width: calc(100% - 270px);
            }
            body.curved-sidebar-desktop-collapsed .page-wrapper-for-sidebar {
                margin-left: 0;
                width: 100%;
                padding-left: 76px;
            }
        }

        @media (max-width: 1023.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
                padding-bottom: 90px;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar">
        <!-- Top Navbar -->
        <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 px-4 sm:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-orange-600/10 text-orange-600 flex items-center justify-center font-bold text-lg">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">Approval &amp; Verifikasi Laboran</h1>
                    <p class="text-xs text-slate-500">Pemeriksaan Fisik &amp; Ketersediaan Alat Laboratorium</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?= site_url('peminjamanbarang/scanner') ?>" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-orange-600 hover:bg-orange-700 transition-colors flex items-center gap-2 shadow-sm shadow-orange-500/20">
                    <i class="bi bi-qr-code-scan"></i>
                    <span>Scanner QR Serah Terima</span>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-8 py-6">

            <!-- Flashdata Notifications -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-sm">
                    <i class="bi bi-check-circle-fill text-emerald-500 text-lg flex-shrink-0"></i>
                    <span><?= html_escape($this->session->flashdata('success')) ?></span>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold flex items-center gap-3 shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill text-rose-500 text-lg flex-shrink-0"></i>
                    <span><?= html_escape($this->session->flashdata('error')) ?></span>
                </div>
            <?php endif; ?>

            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-orange-700 via-orange-600 to-amber-700 rounded-3xl p-6 sm:p-8 text-white shadow-lg mb-6 relative overflow-hidden">
                <div class="relative z-10 max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md text-orange-100 border border-white/20 mb-3">
                        <i class="bi bi-tools"></i>
                        <span>Panel Petugas Laboran</span>
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black tracking-tight mb-2">Verifikasi Kelaikan Fisik &amp; Stok Alat</h2>
                    <p class="text-xs sm:text-sm text-orange-100/90 leading-relaxed">
                        Pengajuan peminjaman barang yang telah disetujui Kaprodi memerlukan pengecekan fisik dan ketersediaan stok oleh Laboran. Klik <strong>Setujui &amp; Teruskan</strong> agar pengajuan dapat diproses untuk persetujuan resmi Kepala Urusan (Kaur).
                    </p>
                </div>
                <div class="absolute -right-6 -bottom-6 w-44 h-44 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex items-center gap-2 border-b border-slate-200 pb-3 mb-6 overflow-x-auto">
                <a href="<?= site_url('peminjamanbarang?tab=pending') ?>"
                   class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all <?= $active_tab === 'pending' ? 'bg-orange-600 text-white shadow-md shadow-orange-600/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                    <i class="bi bi-hourglass-split"></i>
                    <span>Perlu Verifikasi (Pending)</span>
                    <?php if ($pending_count > 0): ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= $active_tab === 'pending' ? 'bg-white text-orange-700' : 'bg-orange-100 text-orange-700' ?>">
                            <?= $pending_count ?>
                        </span>
                    <?php endif; ?>
                </a>

                <a href="<?= site_url('peminjamanbarang?tab=approved') ?>"
                   class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all <?= $active_tab === 'approved' ? 'bg-orange-600 text-white shadow-md shadow-orange-600/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                    <i class="bi bi-check2-circle"></i>
                    <span>Disetujui / Berjalan</span>
                </a>

                <a href="<?= site_url('peminjamanbarang?tab=history') ?>"
                   class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all <?= $active_tab === 'history' ? 'bg-orange-600 text-white shadow-md shadow-orange-600/20' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Selesai &amp; Ditolak</span>
                </a>
            </div>

            <!-- Data List -->
            <?php if (empty($peminjaman_list)): ?>
                <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center shadow-sm">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-2xl mx-auto mb-3">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Tidak ada pengajuan peminjaman barang</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                        Saat ini tidak ada data peminjaman barang pada kategori tab ini.
                    </p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($peminjaman_list as $item): ?>
                        <?php
                            $progress = function_exists('scm_loan_progress') ? scm_loan_progress($item) : null;
                            $group_id = $item->group_id ?? ('single-' . ($item->id_peminjaman ?? '0'));
                            $can_laboran_act = function_exists('scm_loan_can_act') ? scm_loan_can_act($item, 'laboran') : false;
                            $status_str = (string)($item->status ?? '');
                            $is_ready_pickup = in_array($status_str, ['Disetujui (Menunggu Pengambilan)', 'Disetujui (Menunggu Finalisasi QR)'], true);
                            $is_active_loan = in_array($status_str, ['Sedang Dipinjam', 'Dipinjam'], true);
                        ?>
                        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow p-5 sm:p-6">
                            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                                <div class="space-y-2 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 border border-slate-200">
                                            <?= html_escape($group_id) ?>
                                        </span>
                                        <span class="px-2.5 py-1 rounded-xl text-xs font-bold <?= $can_laboran_act ? 'bg-amber-50 text-amber-700 border border-amber-200' : ($is_ready_pickup ? 'bg-blue-50 text-blue-700 border border-blue-200' : ($is_active_loan ? 'bg-orange-50 text-orange-700 border border-orange-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200')) ?>">
                                            <i class="bi <?= $can_laboran_act ? 'bi-hourglass-split' : ($is_ready_pickup ? 'bi-qr-code' : ($is_active_loan ? 'bi-box-seam' : 'bi-check-circle-fill')) ?> me-1"></i>
                                            <?= html_escape($progress['status_label'] ?? ($item->status ?? '-')) ?>
                                        </span>
                                        <?php if (!empty($item->prodi)): ?>
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                <?= html_escape($item->prodi) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <h3 class="text-base sm:text-lg font-extrabold text-slate-900">
                                            <?= html_escape($item->nama_peminjam ?? 'Peminjam') ?>
                                            <span class="text-xs font-medium text-slate-400 ms-1">(<?= html_escape($item->nim_nip ?? '-') ?>)</span>
                                        </h3>
                                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                                            Keperluan: <strong class="text-slate-700"><?= html_escape($item->keperluan ?? '-') ?></strong>
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600 pt-2">
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-calendar-event text-orange-600 text-sm"></i>
                                            <span>Masa Pinjam: <strong><?= date('d/m/Y', strtotime($item->tanggal_pinjam)) ?> &ndash; <?= date('d/m/Y', strtotime($item->tanggal_kembali_rencana)) ?></strong></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-person-check text-orange-600 text-sm"></i>
                                            <span>Approval Kaprodi: <strong class="text-emerald-700"><?= html_escape($item->status_kaprodi ?? 'Disetujui') ?></strong></span>
                                        </div>
                                    </div>

                                    <!-- Daftar Barang & Stok Lab -->
                                    <?php if (!empty($item->detail_barang)): ?>
                                        <div class="pt-2">
                                            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Rincian Barang &amp; Sisa Stok:</div>
                                            <div class="flex flex-wrap gap-2">
                                                <?php foreach ($item->detail_barang as $brg): ?>
                                                    <?php 
                                                        $qty = (int)($brg->jumlah_pinjam ?? 1);
                                                        $tersedia = (int)($brg->jumlah_tersedia ?? 0);
                                                    ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700">
                                                        <i class="bi bi-box-seam text-slate-400"></i>
                                                        <span><?= html_escape($brg->nama_aset ?? '-') ?></span>
                                                        <span class="font-bold text-orange-600">(Minta: <?= $qty ?> unit)</span>
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold <?= $tersedia > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' ?>">
                                                            Stok: <?= $tersedia ?> unit
                                                        </span>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap sm:flex-nowrap lg:flex-col items-stretch justify-end gap-2 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100 flex-shrink-0 w-full sm:w-auto lg:w-48">
                                    <?php if ($can_laboran_act): ?>
                                        <button type="button"
                                                onclick="openApproveModal('<?= html_escape($group_id) ?>', '<?= html_escape(addslashes($item->nama_peminjam ?? '')) ?>')"
                                                class="w-full justify-center px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs sm:text-sm flex items-center gap-2 shadow-md shadow-orange-600/20 transition-all">
                                            <i class="bi bi-send-check-fill"></i>
                                            <span>Setujui &amp; Teruskan</span>
                                        </button>
                                        <button type="button"
                                                onclick="openRejectModal('<?= html_escape($group_id) ?>', '<?= html_escape(addslashes($item->nama_peminjam ?? '')) ?>')"
                                                class="w-full justify-center px-4 py-2.5 rounded-xl bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold text-xs sm:text-sm flex items-center gap-1.5 transition-colors">
                                            <i class="bi bi-x-circle"></i>
                                            <span>Tolak</span>
                                        </button>
                                    <?php elseif ($is_ready_pickup): ?>
                                        <a href="<?= site_url('peminjamanbarang/serah_terima/' . rawurlencode($group_id)) ?>"
                                           class="w-full justify-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm flex items-center gap-2 shadow-md shadow-blue-600/20 transition-all">
                                            <i class="bi bi-box-seam-fill"></i>
                                            <span>Serah Terima Fisik</span>
                                        </a>
                                    <?php elseif ($is_active_loan): ?>
                                        <a href="<?= site_url('peminjamanbarang/serah_terima/' . rawurlencode($group_id)) ?>"
                                           class="w-full justify-center px-4 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm flex items-center gap-2 shadow-md shadow-slate-700/20 transition-all">
                                            <i class="bi bi-arrow-return-left"></i>
                                            <span>Validasi Pengembalian</span>
                                        </a>
                                    <?php endif; ?>

                                    <button type="button"
                                            onclick='openDetailModal(<?= html_escape(json_encode([
                                                'group_id' => $group_id,
                                                'nama_peminjam' => $item->nama_peminjam ?? 'Peminjam',
                                                'nim_nip' => $item->nim_nip ?? '-',
                                                'prodi' => $item->prodi ?? '-',
                                                'keperluan' => $item->keperluan ?? '-',
                                                'tgl_pinjam' => date('d/m/Y', strtotime($item->tanggal_pinjam)),
                                                'tgl_kembali' => date('d/m/Y', strtotime($item->tanggal_kembali_rencana)),
                                                'status' => $progress['status_label'] ?? ($item->status ?? '-'),
                                                'status_laboran' => $item->status_laboran ?? 'Pending',
                                                'status_kaur' => $item->status_kaur ?? 'Pending',
                                                'items' => array_map(function($b) {
                                                    return [
                                                        'nama' => $b->nama_aset ?? '-',
                                                        'kode' => $b->kode_aset ?? '-',
                                                        'qty' => (int)($b->jumlah_pinjam ?? 1),
                                                        'tersedia' => (int)($b->jumlah_tersedia ?? 0)
                                                    ];
                                                }, $item->detail_barang ?? [])
                                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)'
                                            class="w-full justify-center px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm flex items-center gap-1.5 transition-colors">
                                        <i class="bi bi-eye"></i>
                                        <span>Rincian</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- Modal ACC / Verifikasi Laboran -->
    <div id="modalApproveLaboran" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">Verifikasi Alat &amp; Teruskan</h3>
                </div>
                <button type="button" onclick="closeApproveModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <form id="formApproveLaboran" method="post" action="">
                <div class="py-4 space-y-3">
                    <p class="text-xs sm:text-sm text-slate-600">
                        Pastikan Anda telah mengecek fisik peralatan di lab dan stoknya mencukupi untuk peminjam <strong id="approveModalName">-</strong>. Pengajuan akan diteruskan ke Ka. Ur untuk persetujuan resmi.
                    </p>
                    <div>
                        <label for="catatan_laboran" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Catatan Laboran <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea id="catatan_laboran" name="catatan_laboran" rows="2" placeholder="Contoh: Alat fisik telah dicek dalam kondisi baik dan siap di lab." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeApproveModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-orange-600/20">
                        <i class="bi bi-send-check"></i>
                        <span>Setujui &amp; Teruskan ke Kaur</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tolak Laboran -->
    <div id="modalRejectLaboran" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-base">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">Tolak Pengajuan Barang</h3>
                </div>
                <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <form id="formRejectLaboran" method="post" action="">
                <div class="py-4 space-y-3">
                    <p class="text-xs sm:text-sm text-slate-600">
                        Tolak pengajuan barang untuk <strong id="rejectModalName">-</strong>. Alokasi stok reservasi alat di sistem akan otomatis dilepas kembali.
                    </p>
                    <div>
                        <label for="catatan_reject_laboran" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Alasan Penolakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="catatan_reject_laboran" name="catatan_laboran" rows="2" required placeholder="Contoh: Alat sedang dalam perbaikan / stok fisik tidak mencukupi..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-rose-600/20">
                        <i class="bi bi-x-circle"></i>
                        <span>Tolak Pengajuan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Peminjaman -->
    <div id="modalDetail" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-base">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Rincian Pengajuan</h3>
                        <p id="detailGroupId" class="text-xs font-mono font-bold text-slate-400">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            
            <div class="py-4 space-y-4 text-xs sm:text-sm">
                <div class="bg-slate-50 rounded-2xl p-4 space-y-2 border border-slate-100">
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-semibold">Nama Peminjam:</span>
                        <strong id="detailNama" class="text-slate-800">-</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-semibold">NIM / NIP:</span>
                        <span id="detailNim" class="font-mono text-slate-700 font-bold">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-semibold">Program Studi:</span>
                        <span id="detailProdi" class="text-slate-700 font-bold">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-semibold">Periode Pinjam:</span>
                        <span id="detailPeriode" class="text-slate-800 font-bold">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-semibold">Status Saat Ini:</span>
                        <span id="detailStatus" class="font-bold text-orange-600">-</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keperluan Peminjaman</label>
                    <div id="detailKeperluan" class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-xs sm:text-sm">-</div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Daftar Peralatan &amp; Stok Lab</label>
                    <div id="detailItemList" class="space-y-2">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                <button type="button" onclick="closeDetailModal()" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function openApproveModal(groupId, name) {
            document.getElementById('approveModalName').textContent = name;
            document.getElementById('formApproveLaboran').action = '<?= site_url("peminjamanbarang/setujui/") ?>' + encodeURIComponent(groupId);
            document.getElementById('modalApproveLaboran').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('modalApproveLaboran').classList.add('hidden');
        }

        function openRejectModal(groupId, name) {
            document.getElementById('rejectModalName').textContent = name;
            document.getElementById('formRejectLaboran').action = '<?= site_url("peminjamanbarang/tolak/") ?>' + encodeURIComponent(groupId);
            document.getElementById('modalRejectLaboran').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('modalRejectLaboran').classList.add('hidden');
        }

        function openDetailModal(data) {
            document.getElementById('detailGroupId').textContent = data.group_id;
            document.getElementById('detailNama').textContent = data.nama_peminjam;
            document.getElementById('detailNim').textContent = data.nim_nip;
            document.getElementById('detailProdi').textContent = data.prodi;
            document.getElementById('detailPeriode').textContent = data.tgl_pinjam + ' s.d. ' + data.tgl_kembali;
            document.getElementById('detailStatus').textContent = data.status;
            document.getElementById('detailKeperluan').textContent = data.keperluan;

            const listContainer = document.getElementById('detailItemList');
            listContainer.innerHTML = '';
            if (data.items && data.items.length > 0) {
                data.items.forEach(function(item) {
                    const row = document.createElement('div');
                    row.className = 'flex items-center justify-between p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs';
                    row.innerHTML = `
                        <div class="flex items-center gap-2">
                            <i class="bi bi-box-seam text-orange-500"></i>
                            <div>
                                <span class="font-bold text-slate-800">${item.nama}</span>
                                <span class="text-slate-400 font-mono text-[10px] block">${item.kode || '-'}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-orange-600">${item.qty} unit diajukan</span>
                            <span class="block text-[10px] text-slate-500">Sisa stok: <strong>${item.tersedia}</strong></span>
                        </div>
                    `;
                    listContainer.appendChild(row);
                });
            } else {
                listContainer.innerHTML = '<p class="text-slate-400 italic">Tidak ada rincian item.</p>';
            }

            document.getElementById('modalDetail').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('modalDetail').classList.add('hidden');
        }
    </script>
</body>
</html>
