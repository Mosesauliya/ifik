<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$boleh_kembali = !empty($qr_valid) && in_array(($peminjaman->status ?? ''), ['Sedang Dipinjam', 'Dipinjam'], true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'Validasi Pengembalian Barang - Panel Laboran') ?></title>
    
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
    
    <!-- Google Fonts & Icons -->
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
            }
        }

        @media (max-width: 1023.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
                padding-top: 48px;
            }
        }

        .camera-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased selection:bg-orange-500 selection:text-white">

    <!-- Universal Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar">
        <!-- Main Content Container -->
        <main class="min-h-screen p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto">

            <!-- Top Header Navigation -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <a href="<?= site_url('peminjamanbarang/scanner') ?>"
                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-600 hover:text-orange-600 hover:border-orange-300 text-xs font-semibold shadow-sm transition-colors">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali ke Scanner</span>
                        </a>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <span>Pengembalian Barang</span>
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Validasi Pengembalian Barang
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Cek kelengkapan fisik, kondisi akhir barang saat dikembalikan, serta catat dokumentasi foto.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="<?= site_url('peminjamanbarang/scanner') ?>"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-700 hover:border-orange-300 hover:text-orange-600 text-sm font-semibold shadow-sm transition-all">
                        <i class="bi bi-qr-code-scan text-orange-500"></i>
                        <span>Scan Ulang</span>
                    </a>
                </div>
            </div>

            <!-- Flash Error Message -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="text-sm font-medium leading-relaxed">
                        <?= html_escape($this->session->flashdata('error')) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Detail Transaksi Header Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-xl flex-shrink-0">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kode / Nomor Peminjaman</div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 mt-0.5">
                                <?= html_escape($peminjaman->group_id ?? '-') ?>
                            </h2>
                            <div class="text-sm font-semibold text-slate-700 mt-0.5 flex flex-wrap items-center gap-2">
                                <span><?= html_escape($peminjaman->nama_peminjam ?? '-') ?></span>
                                <span class="text-slate-300">•</span>
                                <span class="text-slate-500 font-mono text-xs"><?= html_escape($peminjaman->nim_nip ?? '-') ?></span>
                                <?php if (!empty($peminjaman->prodi ?? $peminjaman->prodi_peminjam)): ?>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-xs px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium">
                                        <?= html_escape($peminjaman->prodi ?? $peminjaman->prodi_peminjam) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold <?= $boleh_kembali ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' ?>">
                            <i class="bi <?= $boleh_kembali ? 'bi-hourglass-split text-blue-500' : 'bi-exclamation-circle-fill text-amber-500' ?>"></i>
                            <span><?= html_escape($peminjaman->status ?? '-') ?></span>
                        </span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-5">
                    <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Mulai Pinjam</div>
                        <div class="text-sm font-bold text-slate-800 mt-1 flex items-center gap-1.5">
                            <i class="bi bi-calendar-event text-blue-500"></i>
                            <span><?= html_escape(function_exists('tanggal_indonesia') ? tanggal_indonesia($peminjaman->tanggal_pinjam ?? null) : ($peminjaman->tanggal_pinjam ?? '-')) ?></span>
                        </div>
                    </div>

                    <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rencana Pengembalian</div>
                        <div class="text-sm font-bold text-slate-800 mt-1 flex items-center gap-1.5">
                            <i class="bi bi-calendar-check text-blue-500"></i>
                            <span><?= html_escape(function_exists('tanggal_indonesia') ? tanggal_indonesia($peminjaman->tanggal_kembali_rencana ?? null) : ($peminjaman->tanggal_kembali_rencana ?? '-')) ?></span>
                        </div>
                    </div>

                    <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Keperluan Pinjam</div>
                        <div class="text-sm font-medium text-slate-700 mt-1 line-clamp-2" title="<?= html_escape($peminjaman->keperluan ?? '-') ?>">
                            <?= html_escape($peminjaman->keperluan ?? '-') ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($boleh_kembali): ?>
                <form id="qrReturnForm" method="post" enctype="multipart/form-data" action="<?= site_url('peminjamanbarang/kembalikan/' . $peminjaman->id_peminjaman) ?>">
                    <input type="hidden" name="from_qr" value="1">

                    <!-- Table Rincian Barang Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Daftar Barang yang Dikembalikan</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Pastikan jumlah unit fisik yang diterima sesuai dengan data pinjaman.</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 text-slate-600 font-semibold text-xs uppercase border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3.5">Nama Aset / Barang</th>
                                        <th class="px-4 py-3.5">Kode Aset</th>
                                        <th class="px-4 py-3.5">Ruangan / Lab</th>
                                        <th class="px-4 py-3.5 text-center w-28">Dipinjam</th>
                                        <th class="px-4 py-3.5 text-right w-44">Dikembalikan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                    <?php foreach (($peminjaman->detail_barang ?? []) as $item): ?>
                                        <?php $jumlah_pinjam = (int)($item->jumlah_pinjam ?? 0); ?>
                                        <tr class="hover:bg-slate-50/60 transition-colors">
                                            <td class="px-4 py-3.5">
                                                <div class="font-bold text-slate-900"><?= html_escape($item->nama_aset ?? '-') ?></div>
                                            </td>
                                            <td class="px-4 py-3.5 font-mono text-xs text-slate-500">
                                                <?= html_escape($item->kode_aset ?? '-') ?>
                                            </td>
                                            <td class="px-4 py-3.5 text-slate-600">
                                                <?= html_escape($item->nama_ruangan ?? '-') ?>
                                            </td>
                                            <td class="px-4 py-3.5 text-center font-bold text-slate-600">
                                                <?= $jumlah_pinjam ?> unit
                                            </td>
                                            <td class="px-4 py-3.5 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" class="btn-decrement w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition-colors">
                                                        -
                                                    </button>
                                                    <input type="number"
                                                           name="jumlah_barang[<?= html_escape($item->kode_aset ?? '') ?>]"
                                                           value="<?= $jumlah_pinjam ?>"
                                                           min="0"
                                                           max="<?= $jumlah_pinjam ?>"
                                                           class="jumlah-input w-16 px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-center font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm"
                                                           data-max="<?= $jumlah_pinjam ?>"
                                                           required>
                                                    <button type="button" class="btn-increment w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition-colors">
                                                        +
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Kondisi, Catatan, & Bukti Foto Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-6">
                        <div class="space-y-6">
                            
                            <!-- Kondisi Akhir Barang Radio Cards -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                                    Kondisi Fisik Saat Pengembalian <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <!-- Baik Option -->
                                    <label class="condition-card relative flex items-center gap-3.5 p-4 rounded-2xl border-2 border-emerald-500 bg-emerald-50/40 cursor-pointer transition-all">
                                        <input type="radio" name="kondisi_saat_kembali" value="Baik" checked class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <div class="text-sm font-extrabold text-emerald-950 flex items-center gap-1.5">
                                                <i class="bi bi-check-circle-fill text-emerald-600"></i>
                                                <span>Kondisi Baik</span>
                                            </div>
                                            <div class="text-[11px] text-emerald-800/80 mt-0.5">Barang utuh, bersih &amp; berfungsi</div>
                                        </div>
                                    </label>

                                    <!-- Rusak Option -->
                                    <label class="condition-card relative flex items-center gap-3.5 p-4 rounded-2xl border-2 border-slate-200 hover:border-amber-400 bg-white cursor-pointer transition-all">
                                        <input type="radio" name="kondisi_saat_kembali" value="Rusak" class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                                        <div>
                                            <div class="text-sm font-extrabold text-amber-950 flex items-center gap-1.5">
                                                <i class="bi bi-exclamation-triangle-fill text-amber-500"></i>
                                                <span>Kondisi Rusak</span>
                                            </div>
                                            <div class="text-[11px] text-amber-800/80 mt-0.5">Cacat fisik / tidak berfungsi</div>
                                        </div>
                                    </label>

                                    <!-- Hilang Option -->
                                    <label class="condition-card relative flex items-center gap-3.5 p-4 rounded-2xl border-2 border-slate-200 hover:border-rose-400 bg-white cursor-pointer transition-all">
                                        <input type="radio" name="kondisi_saat_kembali" value="Hilang" class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                                        <div>
                                            <div class="text-sm font-extrabold text-rose-950 flex items-center gap-1.5">
                                                <i class="bi bi-x-circle-fill text-rose-500"></i>
                                                <span>Barang Hilang</span>
                                            </div>
                                            <div class="text-[11px] text-rose-800/80 mt-0.5">Unit/kelengkapan hilang</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Catatan Pengembalian -->
                            <div>
                                <label for="catatan_pengembalian" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Catatan Pengembalian <span id="noteRequiredIndicator" class="text-slate-400 font-normal">(Wajib jika Rusak/Hilang)</span>
                                </label>
                                <textarea id="catatan_pengembalian"
                                          name="catatan_pengembalian"
                                          rows="2"
                                          placeholder="Tuliskan keterangan detail kondisi barang, bagian yang rusak, atau kelengkapan yang hilang..."
                                          class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"></textarea>
                            </div>

                            <!-- Foto Bukti Pengembalian -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Dokumentasi Foto Pengembalian
                                    </label>
                                    <span class="text-xs text-slate-400">Maks. 5MB (JPG / PNG)</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                    <button type="button"
                                            id="btnGaleriKembali"
                                            class="flex items-center justify-center gap-2 py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-sm transition-colors border border-slate-200">
                                        <i class="bi bi-images text-blue-500 text-base"></i>
                                        <span>Pilih dari Galeri</span>
                                    </button>
                                    <button type="button"
                                            id="btnKameraKembali"
                                            class="flex items-center justify-center gap-2 py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-sm transition-colors border border-slate-200">
                                        <i class="bi bi-camera-fill text-blue-500 text-base"></i>
                                        <span>Buka Kamera Langsung</span>
                                    </button>
                                </div>

                                <input type="file" id="fotoKembaliInput" class="hidden" name="foto_pengembalian" accept="image/*">

                                <!-- Thumbnail Preview Grid -->
                                <div id="kembaliPreview" class="grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Submit Button -->
                    <div class="flex items-center justify-end gap-3">
                        <a href="<?= site_url('peminjamanbarang/scanner') ?>"
                           class="px-6 py-3 rounded-2xl border border-slate-200 bg-white text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors">
                            Batal
                        </a>
                        <button type="button"
                                id="openQrReturnConfirmation"
                                class="px-7 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition-all flex items-center gap-2">
                            <i class="bi bi-check2-circle text-base"></i>
                            <span>Periksa &amp; Konfirmasi Pengembalian</span>
                        </button>
                    </div>

                    <!-- Camera Overlay Modal -->
                    <div class="camera-overlay hidden" id="cameraOverlayKembali">
                        <div class="bg-slate-900 rounded-3xl overflow-hidden w-full max-w-lg shadow-2xl border border-slate-700">
                            <div class="p-4 bg-slate-800 text-white flex items-center justify-between border-b border-slate-700">
                                <h4 class="font-bold text-sm flex items-center gap-2">
                                    <i class="bi bi-camera text-blue-500"></i> Ambil Foto Bukti Pengembalian
                                </h4>
                                <button type="button" id="btnTutupKameraKembali" class="text-slate-400 hover:text-white text-lg">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="relative bg-black">
                                <video id="cameraVideoKembali" autoplay playsinline class="w-full max-h-[55vh] object-cover"></video>
                            </div>
                            <div class="p-4 bg-slate-800 flex items-center justify-center gap-3">
                                <button type="button"
                                        id="btnJepretKembali"
                                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-sm flex items-center gap-2 shadow-md">
                                    <i class="bi bi-camera-fill"></i>
                                    <span>Jepret Foto</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            <?php else: ?>
                <div class="bg-amber-50 border border-amber-200 rounded-3xl p-6 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-xl mx-auto mb-3 shadow-md shadow-amber-500/20">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h3 class="text-base font-bold text-amber-900 mb-1">Peminjaman Belum Dapat Dikembalikan</h3>
                    <p class="text-sm text-amber-800 max-w-md mx-auto leading-relaxed">
                        <?= html_escape($qr_message ?? 'QR peminjaman ini belum berstatus sedang dipinjam atau sudah pernah diselesaikan.') ?>
                    </p>
                    <div class="mt-5">
                        <a href="<?= site_url('peminjamanbarang/scanner') ?>"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-bold hover:bg-blue-600 transition-colors">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali ke Scanner</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <?php if ($boleh_kembali): ?>
    <!-- Confirmation Modal Component -->
    <div id="qrReturnConfirmationModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-base">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">Konfirmasi Pengembalian</h3>
                </div>
                <button type="button" id="btnCloseReturnModal" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="py-4 space-y-4">
                <?php $loan_action_item = $peminjaman; include APPPATH . 'views/shared/loan_action_summary.php'; unset($loan_action_item); ?>

                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-100">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Unit Diterima</div>
                        <div id="modalReturnUnits" class="text-lg font-extrabold text-blue-600 mt-0.5">0 unit</div>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-100">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kondisi Akhir</div>
                        <div id="modalReturnCondition" class="text-lg font-extrabold text-slate-900 mt-0.5">Baik</div>
                    </div>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed">
                    <i class="bi bi-info-circle text-blue-500 me-1"></i>
                    Persetujuan ini akan menyelesaikan peminjaman dan mengembalikan alokasi unit barang ke inventaris laboratorium.
                </p>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" id="btnCancelReturnModal" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-sm hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" id="btnSubmitReturn" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm flex items-center gap-1.5 shadow-md shadow-emerald-600/30">
                    <i class="bi bi-check2-circle"></i>
                    <span>Selesaikan Pengembalian</span>
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Quantity inputs & steppers
        document.querySelectorAll('.jumlah-input').forEach((input) => {
            const container = input.closest('tr');
            const btnDec = container ? container.querySelector('.btn-decrement') : null;
            const btnInc = container ? container.querySelector('.btn-increment') : null;

            function validateVal() {
                const max = parseInt(input.dataset.max || '0', 10);
                let val = parseInt(input.value || '0', 10);
                if (isNaN(val) || val < 0) val = 0;
                if (val > max) val = max;
                input.value = val;
            }

            input.addEventListener('input', validateVal);

            if (btnDec) {
                btnDec.addEventListener('click', () => {
                    let val = parseInt(input.value || '0', 10);
                    if (val > 0) {
                        input.value = val - 1;
                        validateVal();
                    }
                });
            }

            if (btnInc) {
                btnInc.addEventListener('click', () => {
                    const max = parseInt(input.dataset.max || '0', 10);
                    let val = parseInt(input.value || '0', 10);
                    if (val < max) {
                        input.value = val + 1;
                        validateVal();
                    }
                });
            }
        });

        // Condition radio selection styling
        const conditionRadios = document.querySelectorAll('input[name="kondisi_saat_kembali"]');
        const conditionCards = document.querySelectorAll('.condition-card');
        const noteIndicator = document.getElementById('noteRequiredIndicator');
        const noteTextarea = document.getElementById('catatan_pengembalian');

        conditionRadios.forEach((radio) => {
            radio.addEventListener('change', () => {
                conditionCards.forEach((card) => {
                    card.classList.remove('border-emerald-500', 'bg-emerald-50/40', 'border-amber-500', 'bg-amber-50/40', 'border-rose-500', 'bg-rose-50/40');
                    card.classList.add('border-slate-200', 'bg-white');
                });

                const parentCard = radio.closest('.condition-card');
                if (radio.value === 'Baik') {
                    parentCard.classList.remove('border-slate-200', 'bg-white');
                    parentCard.classList.add('border-emerald-500', 'bg-emerald-50/40');
                    if (noteIndicator) {
                        noteIndicator.className = 'text-slate-400 font-normal';
                        noteIndicator.textContent = '(Opsional)';
                    }
                } else if (radio.value === 'Rusak') {
                    parentCard.classList.remove('border-slate-200', 'bg-white');
                    parentCard.classList.add('border-amber-500', 'bg-amber-50/40');
                    if (noteIndicator) {
                        noteIndicator.className = 'text-rose-500 font-bold';
                        noteIndicator.textContent = '(Wajib diisi)';
                    }
                } else if (radio.value === 'Hilang') {
                    parentCard.classList.remove('border-slate-200', 'bg-white');
                    parentCard.classList.add('border-rose-500', 'bg-rose-50/40');
                    if (noteIndicator) {
                        noteIndicator.className = 'text-rose-500 font-bold';
                        noteIndicator.textContent = '(Wajib diisi)';
                    }
                }
            });
        });

        const fileInput = document.getElementById('fotoKembaliInput');
        const btnGaleri = document.getElementById('btnGaleriKembali');
        const btnKamera = document.getElementById('btnKameraKembali');
        const preview = document.getElementById('kembaliPreview');
        const form = document.getElementById('qrReturnForm');

        if (!fileInput || !form) return;

        // Modal Confirmation
        const openConfirmBtn = document.getElementById('openQrReturnConfirmation');
        const confirmModal = document.getElementById('qrReturnConfirmationModal');
        const closeConfirmBtn = document.getElementById('btnCloseReturnModal');
        const cancelConfirmBtn = document.getElementById('btnCancelReturnModal');
        const submitReturnBtn = document.getElementById('btnSubmitReturn');
        const modalUnitsEl = document.getElementById('modalReturnUnits');
        const modalConditionEl = document.getElementById('modalReturnCondition');

        if (openConfirmBtn && confirmModal) {
            openConfirmBtn.addEventListener('click', () => {
                if (!form.reportValidity()) return;

                const selectedCondition = document.querySelector('input[name="kondisi_saat_kembali"]:checked')?.value || 'Baik';
                if ((selectedCondition === 'Rusak' || selectedCondition === 'Hilang') && (!noteTextarea.value || !noteTextarea.value.trim())) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Catatan Wajib Diisi',
                        text: 'Silakan isi catatan pengembalian untuk menjelaskan kondisi barang yang rusak atau hilang.',
                        confirmButtonColor: '#ea580c'
                    });
                    noteTextarea.focus();
                    return;
                }

                const units = Array.from(form.querySelectorAll('.jumlah-input')).reduce((total, input) => total + (parseInt(input.value || '0', 10) || 0), 0);
                if (modalUnitsEl) modalUnitsEl.textContent = units + ' unit';
                if (modalConditionEl) modalConditionEl.textContent = selectedCondition;

                confirmModal.classList.remove('hidden');
            });

            const hideModal = () => confirmModal.classList.add('hidden');
            if (closeConfirmBtn) closeConfirmBtn.addEventListener('click', hideModal);
            if (cancelConfirmBtn) cancelConfirmBtn.addEventListener('click', hideModal);

            if (submitReturnBtn) {
                submitReturnBtn.addEventListener('click', () => {
                    hideModal();
                    form.submit();
                });
            }
        }

        if (btnGaleri) {
            btnGaleri.addEventListener('click', () => {
                fileInput.click();
            });
        }

        fileInput.addEventListener('change', () => {
            preview.innerHTML = '';
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran File Terlalu Besar',
                        text: 'File ' + file.name + ' melebihi batas 5MB.',
                        confirmButtonColor: '#ea580c'
                    });
                    fileInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML = `
                        <div class="relative group rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shadow-sm aspect-video">
                            <img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">
                        </div>`;
                };
                reader.readAsDataURL(file);
            }
        });

        // Camera capture logic
        const overlay = document.getElementById('cameraOverlayKembali');
        const video = document.getElementById('cameraVideoKembali');
        const btnJepret = document.getElementById('btnJepretKembali');
        const btnTutupKamera = document.getElementById('btnTutupKameraKembali');
        let cameraStream = null;

        async function openCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kamera Tidak Didukung',
                    text: 'Browser Anda tidak mendukung akses kamera langsung.',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                video.srcObject = cameraStream;
                overlay.classList.remove('hidden');
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membuka Kamera',
                    text: err.message,
                    confirmButtonColor: '#ea580c'
                });
            }
        }

        function closeCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach((track) => track.stop());
                cameraStream = null;
            }
            overlay.classList.add('hidden');
        }

        function ambilFoto() {
            if (!cameraStream) return;
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.toBlob((blob) => {
                if (!blob) return;
                const file = new File([blob], `bukti-kembali-${Date.now()}.jpg`, { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                
                preview.innerHTML = `
                    <div class="relative group rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shadow-sm aspect-video">
                        <img src="${canvas.toDataURL('image/jpeg')}" alt="Preview" class="w-full h-full object-cover">
                    </div>`;
                closeCamera();
            }, 'image/jpeg', 0.9);
        }

        if (btnKamera) btnKamera.addEventListener('click', openCamera);
        if (btnJepret) btnJepret.addEventListener('click', ambilFoto);
        if (btnTutupKamera) btnTutupKamera.addEventListener('click', closeCamera);
    });
    </script>
</body>
</html>
