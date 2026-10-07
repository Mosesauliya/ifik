<?php
/**
 * View Form Input Ticketing - Unit Ticketing Panel
 * Reusable for: Kemahasiswaan, Sekretariat, SDM & Keuangan, Program Studi
 */
$baseResponUrl = $baseResponUrl ?? ($baseRoute . '/respon-ticketing');
$baseInputUrl   = $baseInputUrl   ?? ($baseRoute . '/ticketing/input');
$baseRiwayatUrl = $baseRiwayatUrl ?? ($baseRoute . '/ticketing/riwayat');
$panelBadge     = $panelBadge     ?? 'Unit Ticketing';
$panelTitle     = $panelTitle     ?? 'Formulir Pelaporan Kendala';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? ($panelTitle . ' — IFIK Portal'); ?></title>

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
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- TinyMCE Rich Text Editor -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>

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
                padding-top: 56px;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Include Curved Sidebar -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar">
    <!-- Main Content -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-5xl mx-auto">
        
        <!-- Header & Breadcrumb -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
                <a href="<?= site_url($baseResponUrl) ?>" class="hover:text-orange-600 transition-colors"><?= htmlspecialchars($panelBadge) ?></a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Ticketing</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Buat Tiket Baru</span>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span class="w-10 h-10 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl shadow-xs">
                            <i class="bi bi-ticket-perforated-fill"></i>
                        </span>
                        <span><?= htmlspecialchars($panelTitle); ?></span>
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">Sampaikan kendala fasilitas, operasional, atau administrasi fakultas untuk ditindaklanjuti.</p>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="<?= site_url($baseResponUrl); ?>" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-50 border border-orange-200/80 text-orange-700 hover:bg-orange-100 text-xs font-bold transition-all shadow-xs">
                        <i class="bi bi-inbox-fill text-sm"></i>
                        <span>Respon Ticketing</span>
                    </a>
                    <a href="<?= site_url($baseRiwayatUrl); ?>" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200/80 text-slate-700 hover:text-orange-600 hover:border-orange-200 text-xs font-bold transition-all shadow-xs">
                        <i class="bi bi-clock-history text-sm"></i>
                        <span>Riwayat Tiket</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Notification -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs">
                <i class="bi bi-exclamation-triangle-fill text-rose-500 text-lg mt-0.5"></i>
                <div class="text-sm">
                    <span class="font-bold">Gagal Menyimpan Tiket:</span>
                    <p class="mt-0.5"><?= $this->session->flashdata('error'); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form Card Container -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">
            
            <!-- Card Header Notice -->
            <div class="px-6 sm:px-8 py-5 border-b border-slate-100 bg-gradient-to-r from-orange-50/40 via-amber-50/20 to-transparent flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-400">Akun Pelapor:</div>
                        <div class="text-sm font-bold text-slate-800">
                            <?= htmlspecialchars($user['nama'] ?: 'Pengguna'); ?> 
                            <span class="text-xs font-medium text-orange-700 bg-orange-100/70 px-2 py-0.5 rounded-md border border-orange-200 ml-1.5"><?= htmlspecialchars($panelBadge); ?></span>
                        </div>
                    </div>
                </div>
                <div class="text-xs font-semibold text-slate-400">
                    Email: <span class="text-slate-700 font-bold"><?= htmlspecialchars($user['email'] ?: '-'); ?></span>
                </div>
            </div>

            <!-- Form -->
            <form id="ticketingForm" action="<?= site_url($baseRoute . '/ticketing/simpan') ?>" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                
                <!-- 1. Nama Lengkap -->
                <div>
                    <label for="nama_lengkap" class="block text-sm font-bold text-slate-700 mb-2">
                        1. Nama Pelapor <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-person-fill text-base"></i>
                        </span>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required
                               value="<?= htmlspecialchars($user['nama'] ?: ''); ?>"
                               placeholder="Masukkan nama lengkap Anda..."
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden">
                    </div>
                </div>

                <!-- 2. Unit yang Dituju -->
                <div>
                    <label for="unit_tujuan" class="block text-sm font-bold text-slate-700 mb-2">
                        2. Unit yang Dituju <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-building-check text-base"></i>
                        </span>
                        <select id="unit_tujuan" name="unit_tujuan" required
                                class="w-full pl-11 pr-10 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden appearance-none cursor-pointer">
                            <option value="" disabled selected>-- Pilih Unit Tujuan Penanganan --</option>
                            <?php foreach ($unit_kategori_map as $unitKey => $catList): ?>
                                <option value="<?= htmlspecialchars($unitKey); ?>" <?= (isset($default_unit) && $default_unit === $unitKey) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($unitKey); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-chevron-down text-xs"></i>
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5">Pilih unit kerja fakultas yang berwenang menindaklanjuti kendala Anda.</p>
                </div>

                <!-- 2.1 Sub Program Studi (Hanya muncul jika unit Program Studi dipilih) -->
                <div id="container_sub_prodi" class="hidden transition-all duration-300">
                    <label for="sub_prodi" class="block text-sm font-bold text-slate-700 mb-2">
                        2.1 Program Studi Terkait <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-mortarboard text-base"></i>
                        </span>
                        <select id="sub_prodi" name="sub_prodi"
                                class="w-full pl-11 pr-10 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden appearance-none cursor-pointer">
                            <option value="" disabled selected>-- Pilih Jurusan / Program Studi --</option>
                            <option value="Desain Komunikasi Visual (DKV)">Desain Komunikasi Visual (DKV)</option>
                            <option value="Desain Interior (DI)">Desain Interior (DI)</option>
                            <option value="Desain Produk (DP)">Desain Produk (DP)</option>
                            <option value="Kriya Tekstil dan Fashion (KTF)">Kriya Tekstil dan Fashion (KTF)</option>
                            <option value="Seni Rupa (SR)">Seni Rupa (SR)</option>
                            <option value="Film dan Animasi">Film dan Animasi</option>
                        </select>
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-chevron-down text-xs"></i>
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5">Pilih jurusan/prodi spesifik tujuan pengajuan tiket kendala Anda.</p>
                </div>

                <!-- 3. Kategori Kendala -->
                <div>
                    <label for="kategori" class="block text-sm font-bold text-slate-700 mb-2">
                        3. Kategori Kendala <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-tags-fill text-base"></i>
                        </span>
                        <select id="kategori" name="kategori" required disabled
                                class="w-full pl-11 pr-10 py-3 rounded-xl bg-slate-100 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden appearance-none cursor-pointer disabled:cursor-not-allowed disabled:text-slate-400">
                            <option value="" disabled selected>-- Pilih Unit Tujuan Terlebih Dahulu --</option>
                        </select>
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-chevron-down text-xs"></i>
                        </span>
                    </div>
                </div>

                <!-- 3.1 Detail Kategori Lainnya (Conditional) -->
                <div id="container_kategori_lainnya" class="hidden transition-all duration-300">
                    <label for="kategori_lainnya" class="block text-sm font-bold text-slate-700 mb-2">
                        3.1 Keterangan Kategori Lainnya <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-pencil-square text-base"></i>
                        </span>
                        <input type="text" id="kategori_lainnya" name="kategori_lainnya"
                               placeholder="Sebutkan detail kategori kendala spesifik Anda..."
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden">
                    </div>
                </div>

                <!-- 4. Prioritas Kendala -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        4. Prioritas Kendala <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="prioritas" value="Rendah" class="peer sr-only">
                            <div class="p-3.5 rounded-2xl border-2 border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-slate-500 peer-checked:bg-slate-50/80 transition-all text-center">
                                <span class="block text-xs font-bold text-slate-700">Rendah</span>
                                <span class="text-[10px] text-slate-400">Dapat ditunda</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="prioritas" value="Sedang" class="peer sr-only" checked>
                            <div class="p-3.5 rounded-2xl border-2 border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-amber-500 peer-checked:bg-amber-50/50 peer-checked:text-amber-700 transition-all text-center">
                                <span class="block text-xs font-bold text-slate-700 peer-checked:text-amber-700">Sedang</span>
                                <span class="text-[10px] text-slate-400">Standar operasional</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="prioritas" value="Tinggi" class="peer sr-only">
                            <div class="p-3.5 rounded-2xl border-2 border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-orange-500 peer-checked:bg-orange-50/50 peer-checked:text-orange-700 transition-all text-center">
                                <span class="block text-xs font-bold text-slate-700 peer-checked:text-orange-700">Tinggi</span>
                                <span class="text-[10px] text-slate-400">Mendesak</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="prioritas" value="Darurat" class="peer sr-only">
                            <div class="p-3.5 rounded-2xl border-2 border-slate-200 bg-white hover:bg-slate-50 peer-checked:border-rose-500 peer-checked:bg-rose-50/50 peer-checked:text-rose-700 transition-all text-center">
                                <span class="block text-xs font-bold text-slate-700 peer-checked:text-rose-700">Darurat</span>
                                <span class="text-[10px] text-slate-400">Kritis / Mogok</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 5. Subjek Kendala -->
                <div>
                    <label for="subjek" class="block text-sm font-bold text-slate-700 mb-2">
                        5. Subjek / Judul Kendala <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-chat-left-dots-fill text-base"></i>
                        </span>
                        <input type="text" id="subjek" name="subjek" required
                               placeholder="Contoh: Permohonan SK Dekan untuk kegiatan mahasiswa..."
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden">
                    </div>
                </div>

                <!-- 6. Deskripsi Kendala -->
                <div>
                    <label for="deskripsi" class="block text-sm font-bold text-slate-700 mb-2">
                        6. Deskripsi Kendala & Penjelasan Rinci <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="6"
                              placeholder="Tuliskan rincian kendala Anda secara lengkap di sini..."></textarea>
                </div>

                <!-- 7. Lampiran Pendukung -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        7. Lampiran / Bukti Pendukung <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    
                    <div id="dropzone" class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:border-orange-500 bg-slate-50/50 hover:bg-orange-50/30 transition-all cursor-pointer relative group">
                        <input type="file" id="lampiran" name="lampiran" 
                               accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx,.zip"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="flex flex-col items-center justify-center space-y-2 pointer-events-none">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                            </div>
                            <div class="text-xs font-bold text-slate-700">
                                Klik untuk unggah atau seret berkas ke sini
                            </div>
                            <div class="text-[11px] text-slate-400">
                                Format: JPG, PNG, PDF, DOCX, XLSX, ZIP (Maks. 5MB)
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Preview Tag -->
                    <div id="filePreview" class="hidden mt-3 p-3 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-between">
                        <div class="flex items-center gap-2.5 overflow-hidden">
                            <i class="bi bi-file-earmark-check-fill text-orange-600 text-lg"></i>
                            <span id="fileName" class="text-xs font-bold text-slate-700 truncate"></span>
                            <span id="fileSize" class="text-[10px] text-slate-400"></span>
                        </div>
                        <button type="button" id="btnRemoveFile" class="text-slate-400 hover:text-rose-600 p-1">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <a href="<?= site_url($baseRiwayatUrl); ?>" 
                       class="w-full sm:w-auto px-6 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold text-center transition-all">
                        Batal
                    </a>
                    <button type="submit" id="btnSubmitTicket"
                            class="w-full sm:w-auto px-8 py-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-lg shadow-orange-600/30 transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-send-fill text-sm"></i>
                        <span>Kirim Tiket Kendala</span>
                    </button>
                </div>
            </form>
        </div>
    </main>
    </div>

    <!-- Progress Modal -->
    <div id="modalTicketingProgress" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div id="modalTicketingCard" class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl scale-95 opacity-0 transition-all duration-200">
            <div id="progressIconContainer" class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl shadow-inner relative">
                <i id="progressIcon" class="bi bi-send-fill animate-pulse"></i>
                <div id="progressPing" class="absolute inset-0 rounded-2xl bg-orange-400/20 animate-ping"></div>
            </div>
            
            <h3 id="progressTitle" class="text-lg font-black text-slate-800 mb-1">Mengirimkan Tiket Kendala...</h3>
            <p id="progressStatusText" class="text-xs text-slate-500 mb-6">Menyiapkan data formulir...</p>

            <!-- Step Indicators -->
            <div class="grid grid-cols-3 gap-2 mb-6">
                <div id="step-1" class="text-center">
                    <div class="step-icon w-6 h-6 mx-auto rounded-full bg-orange-500 text-white flex items-center justify-center text-[10px] font-bold mb-1 ring-2 ring-orange-400/30">1</div>
                    <span class="text-[10px] font-bold text-orange-600">Validasi</span>
                </div>
                <div id="step-2" class="text-center">
                    <div class="step-icon w-6 h-6 mx-auto rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-bold mb-1">2</div>
                    <span class="text-[10px] font-semibold text-slate-400">Unggah</span>
                </div>
                <div id="step-3" class="text-center">
                    <div class="step-icon w-6 h-6 mx-auto rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-bold mb-1">3</div>
                    <span class="text-[10px] font-semibold text-slate-400">Pendaftaran</span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden mb-3">
                <div id="ticketProgressBar" class="bg-gradient-to-r from-orange-500 to-amber-500 h-2.5 rounded-full transition-all duration-300" style="width: 15%"></div>
            </div>
            <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono">
                <span id="progressSubDetail">Memproses...</span>
                <span id="ticketProgressPercent" class="font-bold text-orange-600">15%</span>
            </div>

            <div id="progressErrorContainer" class="hidden mt-4 pt-4 border-t border-slate-100">
                <p id="progressErrorMessage" class="text-xs text-rose-600 font-medium mb-3"></p>
                <button type="button" onclick="closeProgressModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                    Tutup & Coba Lagi
                </button>
            </div>
        </div>
    </div>

    <!-- Dynamic Category & Form JS -->
    <script>
        const unitKategoriMap = <?= json_encode($unit_kategori_map); ?>;
        const unitSelect = document.getElementById('unit_tujuan');
        const kategoriSelect = document.getElementById('kategori');
        const containerLainnya = document.getElementById('container_kategori_lainnya');
        const inputLainnya = document.getElementById('kategori_lainnya');
        const containerSubProdi = document.getElementById('container_sub_prodi');
        const subProdiSelect = document.getElementById('sub_prodi');

        function updateKategoriOptions() {
            const selectedUnit = unitSelect.value;
            kategoriSelect.innerHTML = '';

            if (selectedUnit && unitKategoriMap[selectedUnit]) {
                kategoriSelect.disabled = false;
                kategoriSelect.classList.remove('bg-slate-100', 'disabled:cursor-not-allowed', 'disabled:text-slate-400');
                kategoriSelect.classList.add('bg-slate-50');

                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = '-- Pilih Kategori Kendala --';
                defaultOpt.disabled = true;
                defaultOpt.selected = true;
                kategoriSelect.appendChild(defaultOpt);

                unitKategoriMap[selectedUnit].forEach(function(cat) {
                    const opt = document.createElement('option');
                    opt.value = cat;
                    opt.textContent = cat;
                    kategoriSelect.appendChild(opt);
                });
            } else {
                kategoriSelect.disabled = true;
                kategoriSelect.classList.add('bg-slate-100', 'disabled:cursor-not-allowed', 'disabled:text-slate-400');
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = '-- Pilih Unit Tujuan Terlebih Dahulu --';
                opt.disabled = true;
                opt.selected = true;
                kategoriSelect.appendChild(opt);
            }

            // Check if Program Studi
            if (selectedUnit && (selectedUnit.toLowerCase().includes('prodi') || selectedUnit.toLowerCase().includes('program studi'))) {
                containerSubProdi.classList.remove('hidden');
                subProdiSelect.required = true;
            } else {
                containerSubProdi.classList.add('hidden');
                subProdiSelect.required = false;
                subProdiSelect.value = '';
            }

            checkLainnya();
        }

        function checkLainnya() {
            const val = kategoriSelect.value || '';
            if (val.toLowerCase().includes('lain-lain') || val.toLowerCase().includes('lainnya')) {
                containerLainnya.classList.remove('hidden');
                inputLainnya.required = true;
            } else {
                containerLainnya.classList.add('hidden');
                inputLainnya.required = false;
                inputLainnya.value = '';
            }
        }

        unitSelect.addEventListener('change', updateKategoriOptions);
        kategoriSelect.addEventListener('change', checkLainnya);

        if (unitSelect.value) {
            updateKategoriOptions();
        }

        // Dropzone & File Preview
        const fileInput = document.getElementById('lampiran');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const btnRemove = document.getElementById('btnRemoveFile');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const f = this.files[0];
                if (f.size > 5 * 1024 * 1024) {
                    Swal.fire('Ukuran Berkas Terlalu Besar', 'Maksimal ukuran berkas adalah 5MB.', 'warning');
                    this.value = '';
                    filePreview.classList.add('hidden');
                    return;
                }
                fileName.textContent = f.name;
                fileSize.textContent = '(' + (f.size / 1024).toFixed(0) + ' KB)';
                filePreview.classList.remove('hidden');
            }
        });

        btnRemove.addEventListener('click', function() {
            fileInput.value = '';
            filePreview.classList.add('hidden');
        });

        // Initialize TinyMCE
        tinymce.init({
            selector: '#deskripsi',
            menubar: false,
            statusbar: false,
            plugins: 'lists link code table paste autolink',
            toolbar: 'undo redo | formatselect | bold italic underline forecolor | bullist numlist | link blockquote code | removeformat',
            height: 280,
            skin: 'oxide',
            content_style: "body { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; color: #334155; line-height: 1.6; }",
            placeholder: "Tuliskan rincian kendala Anda secara lengkap di sini...",
            setup: function(ed) {
                ed.on('change keyup', function() {
                    tinymce.triggerSave();
                });
            }
        });

        // Form Submit with Progress
        let isSubmitting = false;
        document.getElementById('ticketingForm').addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }

            tinymce.triggerSave();

            const deskripsiVal = document.getElementById('deskripsi').value.replace(/<[^>]*>/g, '').trim();
            if (!deskripsiVal) {
                e.preventDefault();
                Swal.fire('Deskripsi Kosong', 'Harap isi deskripsi kendala Anda.', 'warning');
                return false;
            }

            e.preventDefault();
            isSubmitting = true;

            const modal = document.getElementById('modalTicketingProgress');
            const card = document.getElementById('modalTicketingCard');
            modal.classList.remove('hidden');
            setTimeout(() => {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }, 10);

            const form = this;
            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', function(ev) {
                if (ev.lengthComputable) {
                    const pct = Math.round(20 + (ev.loaded / ev.total) * 60);
                    document.getElementById('ticketProgressBar').style.width = pct + '%';
                    document.getElementById('ticketProgressPercent').textContent = pct + '%';
                    document.getElementById('progressStatusText').textContent = 'Mengunggah formulir (' + Math.round((ev.loaded / ev.total) * 100) + '%)...';
                }
            });

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (res.status === 'success') {
                                document.getElementById('ticketProgressBar').style.width = '100%';
                                document.getElementById('ticketProgressPercent').textContent = '100%';
                                document.getElementById('progressTitle').innerHTML = '<span class="text-emerald-600">Tiket Berhasil Diajukan!</span>';
                                document.getElementById('progressStatusText').textContent = 'Kode Tiket: ' + (res.kode_tiket || '-');
                                
                                setTimeout(() => {
                                    window.location.href = res.redirect_url || '<?= site_url($baseRiwayatUrl); ?>';
                                }, 1200);
                                return;
                            } else {
                                throw new Error(res.message || 'Gagal menyimpan tiket.');
                            }
                        } catch (err) {
                            showError(err.message || 'Respon server tidak valid.');
                        }
                    } else {
                        showError('Gagal terhubung ke server (HTTP ' + xhr.status + ').');
                    }
                }
            };

            xhr.onerror = function() {
                showError('Koneksi internet terputus saat mengirim tiket.');
            };

            function showError(msg) {
                document.getElementById('progressErrorContainer').classList.remove('hidden');
                document.getElementById('progressErrorMessage').textContent = msg;
                document.getElementById('progressTitle').textContent = 'Terjadi Kendala';
                document.getElementById('progressStatusText').textContent = 'Pengiriman gagal.';
            }

            xhr.send(formData);
        });

        window.closeProgressModal = function() {
            const modal = document.getElementById('modalTicketingProgress');
            modal.classList.add('hidden');
            isSubmitting = false;
        };
    </script>
</body>
</html>
