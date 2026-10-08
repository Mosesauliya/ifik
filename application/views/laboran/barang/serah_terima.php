<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$workflow_stage = $workflow_stage ?? 'serah_terima';
$boleh_serah = !empty($qr_valid);
$is_verifikasi_laboran = ($workflow_stage === 'verifikasi_laboran');
$is_menunggu_kaur = ($workflow_stage === 'menunggu_kaur');
$is_serah_terima = ($workflow_stage === 'serah_terima');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'Serah Terima Barang - Panel Laboran') ?></title>
    
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
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold uppercase tracking-wider">
                            <i class="bi bi-box-seam-fill"></i>
                            <span>Serah Terima Barang</span>
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Verifikasi &amp; Serah Terima Barang
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Periksa ketersediaan unit fisik di lab, sesuaikan jumlah jika diperlukan, lalu serahkan barang.
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
                        <div class="w-12 h-12 rounded-2xl bg-orange-500/10 text-orange-600 flex items-center justify-center font-bold text-xl flex-shrink-0">
                            <i class="bi bi-receipt"></i>
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
                        <?php if ($is_verifikasi_laboran): ?>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="bi bi-clock-history text-blue-500"></i>
                                <span>Tahap 3 &bull; Menunggu Verifikasi Laboran</span>
                            </span>
                        <?php elseif ($is_menunggu_kaur): ?>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <i class="bi bi-hourglass-split text-amber-500"></i>
                                <span>Tahap 4 &bull; Menunggu ACC Kaur</span>
                            </span>
                        <?php elseif ($is_serah_terima): ?>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="bi bi-check-circle-fill text-emerald-500"></i>
                                <span>Tahap 5/6 &bull; Siap Serah Terima Fisik</span>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-slate-50 text-slate-700 border border-slate-200">
                                <i class="bi bi-info-circle text-slate-500"></i>
                                <span><?= html_escape($peminjaman->status ?? '-') ?></span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-5">
                    <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Mulai Pinjam</div>
                        <div class="text-sm font-bold text-slate-800 mt-1 flex items-center gap-1.5">
                            <i class="bi bi-calendar-event text-orange-500"></i>
                            <span><?= html_escape(function_exists('tanggal_indonesia') ? tanggal_indonesia($peminjaman->tanggal_pinjam ?? null) : ($peminjaman->tanggal_pinjam ?? '-')) ?></span>
                        </div>
                    </div>

                    <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Rencana Pengembalian</div>
                        <div class="text-sm font-bold text-slate-800 mt-1 flex items-center gap-1.5">
                            <i class="bi bi-calendar-check text-orange-500"></i>
                            <span><?= html_escape(function_exists('tanggal_indonesia') ? tanggal_indonesia($peminjaman->tanggal_kembali_rencana ?? null) : ($peminjaman->tanggal_kembali_rencana ?? '-')) ?></span>
                        </div>
                    </div>

                    <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Keperluan / Alasan</div>
                        <div class="text-sm font-medium text-slate-700 mt-1 line-clamp-2" title="<?= html_escape($peminjaman->keperluan ?? '-') ?>">
                            <?= html_escape($peminjaman->keperluan ?? '-') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Callout Stage Banners -->
            <?php if ($is_verifikasi_laboran): ?>
                <div class="bg-blue-50/90 border border-blue-200 rounded-3xl p-5 sm:p-6 mb-6">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-md shadow-blue-500/20">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-blue-700 uppercase tracking-wider">Tahap 3 &bull; Verifikasi Kelayakan Laboran</div>
                            <h3 class="text-base font-extrabold text-blue-950 mt-0.5">Pemeriksaan Barang &amp; Penerusan ke Kaur</h3>
                            <p class="text-xs sm:text-sm text-blue-800/90 mt-1 leading-relaxed">
                                Pengajuan ini telah disetujui Kaprodi. Silakan periksa kelayakan fisik dan stok barang di laboratorium. Klik tombol <strong>"Verifikasi &amp; Teruskan ke Kaur"</strong> untuk menyimpan hasil verifikasi dan meneruskan transaksi ke Kepala Urusan (Kaur). <em>Barang fisik belum diserahterimakan pada tahap ini.</em>
                            </p>
                        </div>
                    </div>
                </div>
            <?php elseif ($is_menunggu_kaur): ?>
                <div class="bg-amber-50 border-2 border-amber-300 rounded-3xl p-5 sm:p-6 mb-6 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-md shadow-amber-500/20">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-amber-800 uppercase tracking-wider">Tahap 4 &bull; Menunggu Persetujuan Ka. Ur</div>
                                <h3 class="text-base font-extrabold text-amber-950 mt-0.5">Menunggu ACC Kepala Urusan (Kaur)</h3>
                                <p class="text-xs sm:text-sm text-amber-800/90 mt-1 leading-relaxed">
                                    Pengajuan ini sudah diverifikasi Laboran dan saat ini <strong>menunggu persetujuan resmi Kepala Urusan (Kaur)</strong>. Fisik barang <u>belum boleh diserahterimakan</u> ke peminjam sebelum Kaur menyetujuinya.
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2.5 pt-2 md:pt-0">
                            <form method="post" action="<?= site_url('peminjamanbarang/approve_kaur/' . rawurlencode($peminjaman->group_id)) ?>" class="inline">
                                <button type="submit" onclick="return confirm('Apakah Anda yakin menyetujui peminjaman barang ini sebagai Kepala Urusan (Kaur)?')" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold flex items-center gap-2 shadow-md shadow-emerald-600/20 transition-all">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Setujui (ACC Kaur)</span>
                                </button>
                            </form>
                            <form method="post" action="<?= site_url('peminjamanbarang/tolak_kaur/' . rawurlencode($peminjaman->group_id)) ?>" class="inline">
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?')" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold flex items-center gap-2 shadow-md shadow-rose-600/20 transition-all">
                                    <i class="bi bi-x-circle-fill"></i>
                                    <span>Tolak Pengajuan</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php elseif ($is_serah_terima): ?>
                <div class="bg-emerald-50/80 border border-emerald-200 rounded-3xl p-5 sm:p-6 mb-6">
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-md shadow-emerald-500/20">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Tahap 5 &amp; 6 &bull; Siap Serah Terima Fisik</div>
                            <h3 class="text-base font-extrabold text-emerald-950 mt-0.5">Persetujuan Lengkap (Kaprodi &bull; Laboran &bull; Kaur)</h3>
                            <p class="text-xs sm:text-sm text-emerald-800/90 mt-1 leading-relaxed">
                                Pengajuan telah resmi disetujui oleh Kaur. Silakan serahkan unit fisik barang ke peminjam, jepret foto bukti serah terima, dan klik tombol konfirmasi serah terima di bawah. Status akan diperbarui menjadi <strong>Sedang Dipinjam</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($is_verifikasi_laboran || $is_serah_terima): ?>
                <?php
                    $form_target = $is_verifikasi_laboran
                        ? site_url('peminjamanbarang/verifikasi_laboran/' . rawurlencode($peminjaman->group_id))
                        : site_url('peminjamanbarang/proses_serah/' . rawurlencode($peminjaman->group_id));
                ?>
                <form id="handoverForm" method="post" enctype="multipart/form-data" action="<?= $form_target ?>">
                    
                    <!-- Table Rincian Barang Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Daftar Barang yang Diajukan</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Sesuaikan jumlah unit yang diserahkan jika stok fisik di lab kurang dari pengajuan.</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 text-slate-600 font-semibold text-xs uppercase border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3.5">Nama Aset / Barang</th>
                                        <th class="px-4 py-3.5">Kode Aset</th>
                                        <th class="px-4 py-3.5">Ruangan / Lab</th>
                                        <th class="px-4 py-3.5 text-center w-28">Diajukan</th>
                                        <th class="px-4 py-3.5 text-right w-44">Diserahkan</th>
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
                                                           class="jumlah-input w-16 px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-center font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white text-sm"
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

                    <!-- Catatan & Foto Dokumentasi Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6 mb-6">
                        <div class="space-y-5">
                            <!-- Catatan Serah Terima -->
                            <div>
                                <label for="catatan_serah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Catatan Serah Terima <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <textarea id="catatan_serah"
                                          name="catatan_serah"
                                          rows="2"
                                          placeholder="Contoh: Barang diserahkan lengkap dalam kondisi baik bersama kelengkapannya."
                                          class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all"></textarea>
                            </div>

                            <!-- Foto Dokumentasi -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Dokumentasi Foto Serah Terima
                                    </label>
                                    <span class="text-xs text-slate-400">Maks. 5MB per file (JPG / PNG)</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                    <button type="button"
                                            id="btnGaleriSerah"
                                            class="flex items-center justify-center gap-2 py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-sm transition-colors border border-slate-200">
                                        <i class="bi bi-images text-orange-500 text-base"></i>
                                        <span>Pilih dari Galeri</span>
                                    </button>
                                    <button type="button"
                                            id="btnKameraSerah"
                                            class="flex items-center justify-center gap-2 py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-sm transition-colors border border-slate-200">
                                        <i class="bi bi-camera-fill text-orange-500 text-base"></i>
                                        <span>Buka Kamera Langsung</span>
                                    </button>
                                </div>

                                <input type="file" id="fotoSerahInput" class="hidden" accept="image/*" multiple>

                                <!-- Thumbnail Preview Grid -->
                                <div id="serahPreview" class="grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
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
                                id="openHandoverConfirmation"
                                class="px-7 py-3 rounded-2xl <?= $is_verifikasi_laboran ? 'bg-blue-600 hover:bg-blue-700 shadow-blue-600/30' : 'bg-orange-600 hover:bg-orange-700 shadow-orange-600/30' ?> text-white font-bold text-sm shadow-lg transition-all flex items-center gap-2">
                            <i class="bi <?= $is_verifikasi_laboran ? 'bi-shield-check' : 'bi-check2-circle' ?> text-base"></i>
                            <span><?= $is_verifikasi_laboran ? 'Verifikasi &amp; Teruskan ke Kaur' : 'Periksa &amp; Konfirmasi Serah Terima' ?></span>
                        </button>
                    </div>

                    <!-- Camera Overlay Modal -->
                    <div class="camera-overlay hidden" id="cameraOverlaySerah">
                        <div class="bg-slate-900 rounded-3xl overflow-hidden w-full max-w-lg shadow-2xl border border-slate-700">
                            <div class="p-4 bg-slate-800 text-white flex items-center justify-between border-b border-slate-700">
                                <h4 class="font-bold text-sm flex items-center gap-2">
                                    <i class="bi bi-camera text-orange-500"></i> Ambil Foto Bukti Serah Terima
                                </h4>
                                <button type="button" id="btnTutupKameraSerah" class="text-slate-400 hover:text-white text-lg">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="relative bg-black">
                                <video id="cameraVideoSerah" autoplay playsinline class="w-full max-h-[55vh] object-cover"></video>
                            </div>
                            <div class="p-4 bg-slate-800 flex items-center justify-center gap-3">
                                <button type="button"
                                        id="btnJepretSerah"
                                        class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-2xl font-bold text-sm flex items-center gap-2 shadow-md">
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
                    <h3 class="text-base font-bold text-amber-900 mb-1">Peminjaman Belum Siap Serah</h3>
                    <p class="text-sm text-amber-800 max-w-md mx-auto leading-relaxed">
                        <?= html_escape($qr_message ?? 'QR peminjaman ini belum berada pada status siap serah terima atau sudah pernah diproses sebelumnya.') ?>
                    </p>
                    <div class="mt-5">
                        <a href="<?= site_url('peminjamanbarang/scanner') ?>"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-slate-900 text-white text-sm font-bold hover:bg-orange-600 transition-colors">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali ke Scanner</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <?php if ($is_verifikasi_laboran || $is_serah_terima): ?>
    <!-- Confirmation Modal Component -->
    <div id="handoverConfirmationModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl <?= $is_verifikasi_laboran ? 'bg-blue-500/10 text-blue-600' : 'bg-orange-500/10 text-orange-600' ?> flex items-center justify-center font-bold text-base">
                        <i class="bi <?= $is_verifikasi_laboran ? 'bi-shield-check' : 'bi-box-seam-fill' ?>"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        <?= $is_verifikasi_laboran ? 'Konfirmasi Verifikasi Laboran' : 'Konfirmasi Serah Terima Fisik' ?>
                    </h3>
                </div>
                <button type="button" id="btnCloseConfirmModal" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="py-4 space-y-4">
                <?php $loan_action_item = $peminjaman; include APPPATH . 'views/shared/loan_action_summary.php'; unset($loan_action_item); ?>

                <div class="<?= $is_verifikasi_laboran ? 'bg-blue-50/80 border-blue-200/80' : 'bg-orange-50/80 border-orange-200/80' ?> border rounded-2xl p-4 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold <?= $is_verifikasi_laboran ? 'text-blue-950' : 'text-orange-950' ?> uppercase tracking-wide">
                            <?= $is_verifikasi_laboran ? 'Total Unit Diverifikasi' : 'Total Unit Diserahkan' ?>
                        </div>
                        <div class="text-xs <?= $is_verifikasi_laboran ? 'text-blue-800/80' : 'text-orange-800/80' ?> mt-0.5">
                            <?= $is_verifikasi_laboran ? 'Ketersediaan stok yang akan diteruskan ke Kaur' : 'Sesuai penyerahan fisik langsung ke peminjam' ?>
                        </div>
                    </div>
                    <div id="modalConfirmUnits" class="text-xl font-extrabold <?= $is_verifikasi_laboran ? 'text-blue-600' : 'text-orange-600' ?>">0 unit</div>
                </div>

                <?php if ($is_verifikasi_laboran): ?>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        <i class="bi bi-info-circle text-blue-500 me-1"></i>
                        Dengan menekan tombol simpan, pengajuan akan berstatus <strong>Menunggu ACC Kaur</strong>. Barang <u>belum diserahkan</u> dan akan menunggu persetujuan resmi Kepala Urusan (Kaur).
                    </p>
                <?php else: ?>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        <i class="bi bi-info-circle text-orange-500 me-1"></i>
                        Dengan menekan tombol setujui, status peminjaman akan diperbarui menjadi <strong>Sedang Dipinjam</strong>. Pastikan barang fisik telah diserahkan langsung kepada peminjam.
                    </p>
                <?php endif; ?>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" id="btnCancelConfirmModal" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-sm hover:bg-slate-50">
                    Batal Periksa
                </button>
                <button type="button" id="btnSubmitHandover" class="px-6 py-2.5 rounded-xl <?= $is_verifikasi_laboran ? 'bg-blue-600 hover:bg-blue-700 shadow-blue-600/30' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/30' ?> text-white font-bold text-sm flex items-center gap-1.5 shadow-md">
                    <i class="bi <?= $is_verifikasi_laboran ? 'bi-send-check' : 'bi-check2-circle' ?>"></i>
                    <span><?= $is_verifikasi_laboran ? 'Simpan &amp; Teruskan ke Kaur' : 'Setujui Serah Terima' ?></span>
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

        const fileInput = document.getElementById('fotoSerahInput');
        const btnGaleri = document.getElementById('btnGaleriSerah');
        const btnKamera = document.getElementById('btnKameraSerah');
        const preview = document.getElementById('serahPreview');
        const form = document.getElementById('handoverForm');
        let selectedFiles = [];

        if (!fileInput || !form) return;

        // Modal Confirmation
        const openConfirmBtn = document.getElementById('openHandoverConfirmation');
        const confirmModal = document.getElementById('handoverConfirmationModal');
        const closeConfirmBtn = document.getElementById('btnCloseConfirmModal');
        const cancelConfirmBtn = document.getElementById('btnCancelConfirmModal');
        const submitHandoverBtn = document.getElementById('btnSubmitHandover');
        const modalUnitsEl = document.getElementById('modalConfirmUnits');

        if (openConfirmBtn && confirmModal) {
            openConfirmBtn.addEventListener('click', () => {
                if (!form.reportValidity()) return;
                
                const units = Array.from(form.querySelectorAll('.jumlah-input')).reduce((total, input) => total + (parseInt(input.value || '0', 10) || 0), 0);
                if (modalUnitsEl) modalUnitsEl.textContent = units + ' unit';
                confirmModal.classList.remove('hidden');
            });

            const hideModal = () => confirmModal.classList.add('hidden');
            if (closeConfirmBtn) closeConfirmBtn.addEventListener('click', hideModal);
            if (cancelConfirmBtn) cancelConfirmBtn.addEventListener('click', hideModal);

            if (submitHandoverBtn) {
                submitHandoverBtn.addEventListener('click', () => {
                    hideModal();
                    fileInput.name = 'foto_serah[]';
                    form.submit();
                });
            }
        }

        function syncFileInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach((f) => dt.items.add(f));
            fileInput.files = dt.files;
        }

        function renderPreview() {
            preview.innerHTML = '';
            selectedFiles.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = (event) => {
                    const card = document.createElement('div');
                    card.className = 'relative group rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shadow-sm aspect-video';
                    card.innerHTML = `
                        <img src="${event.target.result}" alt="Preview" class="w-full h-full object-cover">
                        <button type="button" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-slate-900/80 hover:bg-rose-600 text-white flex items-center justify-center text-xs shadow-md transition-colors" data-idx="${idx}">
                            <i class="bi bi-x"></i>
                        </button>`;
                    preview.appendChild(card);
                    card.querySelector('button').addEventListener('click', () => {
                        selectedFiles.splice(idx, 1);
                        syncFileInput();
                        renderPreview();
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        if (btnGaleri) {
            btnGaleri.addEventListener('click', () => {
                fileInput.removeAttribute('capture');
                fileInput.setAttribute('multiple', 'multiple');
                fileInput.click();
            });
        }

        fileInput.addEventListener('change', () => {
            Array.from(fileInput.files || []).forEach((file) => {
                if (file.type.startsWith('image/') && file.size <= 5 * 1024 * 1024) {
                    selectedFiles.push(file);
                } else if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran File Terlalu Besar',
                        text: 'File ' + file.name + ' melebihi batas 5MB.',
                        confirmButtonColor: '#ea580c'
                    });
                }
            });
            syncFileInput();
            renderPreview();
        });

        // Camera capture logic
        const overlay = document.getElementById('cameraOverlaySerah');
        const video = document.getElementById('cameraVideoSerah');
        const btnJepret = document.getElementById('btnJepretSerah');
        const btnTutupKamera = document.getElementById('btnTutupKameraSerah');
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
                const file = new File([blob], `bukti-serah-${Date.now()}.jpg`, { type: 'image/jpeg' });
                selectedFiles.push(file);
                syncFileInput();
                renderPreview();
                closeCamera();
            }, 'image/jpeg', 0.9);
        }

        if (btnKamera) btnKamera.addEventListener('click', openCamera);
        if (btnJepret) btnJepret.addEventListener('click', ambilFoto);
        if (btnTutupKamera) btnTutupKamera.addEventListener('click', closeCamera);

        form.addEventListener('submit', () => {
            fileInput.name = 'foto_serah[]';
        });
    });
    </script>
</body>
</html>
