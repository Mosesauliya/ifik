<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Buat Tiket Kendala — Mahasiswa IFIK'; ?> - IFIK</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- TinyMCE Rich Text Editor -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.2/tinymce.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .tox-tinymce {
            border-radius: 0.75rem !important;
            border-color: #e2e8f0 !important;
            box-shadow: none !important;
        }
        .tox-tinymce:focus-within {
            border-color: #ea580c !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.1) !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Include Curved Sidebar (Panel Mahasiswa) -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Content -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-5xl mx-auto pl-16">
        
        <!-- Header & Breadcrumb -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
                <a href="<?= site_url('mahasiswa') ?>" class="hover:text-orange-600 transition-colors">Portal Mahasiswa</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Layanan Ticketing</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Buat Tiket Kendala</span>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25">
                            <i class="bi bi-ticket-detailed-fill text-xl"></i>
                        </span>
                        Buat Tiket Kendala Mahasiswa
                    </h1>
                    <p class="text-sm text-slate-500 mt-1.5 max-w-2xl">
                        Sampaikan kendala akademik, fasilitas laboratorium, permohonan ke LAA, atau pengajuan bantuan ke unit kerja terkait.
                    </p>
                </div>

                <a href="<?= site_url('mahasiswa/ticketing/riwayat') ?>" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:text-orange-600 hover:border-orange-200 hover:bg-orange-50/50 text-sm font-bold shadow-xs transition-all">
                    <i class="bi bi-clock-history text-base"></i>
                    <span>Riwayat Tiket Saya</span>
                </a>
            </div>
        </div>

        <!-- Flash Alert Messages -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-exclamation-octagon-fill text-lg text-rose-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('error'); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-check-circle-fill text-lg text-emerald-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('success'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Form Card Container -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">
            
            <!-- Card Header Notice -->
            <div class="px-6 sm:px-8 py-5 border-b border-slate-100 bg-gradient-to-r from-orange-50/40 via-amber-50/20 to-transparent flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-400">Akun Pelapor:</div>
                        <div class="text-sm font-bold text-slate-800">
                            <?= htmlspecialchars($user['nama'] ?: 'Mahasiswa'); ?> 
                            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 ml-1.5">
                                NIM: <?= htmlspecialchars($user['nim'] ?: '-'); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-xs font-semibold text-slate-400">
                    Email: <span class="text-slate-700 font-bold"><?= htmlspecialchars($user['email'] ?: '-'); ?></span>
                </div>
            </div>

            <!-- Form -->
            <form id="ticketingForm" action="<?= site_url('mahasiswa/ticketing/simpan') ?>" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                
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
                               placeholder="Masukkan nama lengkap..."
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden">
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5">Nama mahasiswa yang mengajukan tiket kendala ini.</p>
                </div>

                <!-- 2. Unit yang Dituju (Dropdown Dinamis) -->
                <div>
                    <label for="unit_tujuan" class="block text-sm font-bold text-slate-700 mb-2">
                        2. Unit yang Dituju <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-building-gear text-base"></i>
                        </span>
                        <select id="unit_tujuan" name="unit_tujuan" required onchange="handleUnitChange(this.value)"
                                class="w-full pl-11 pr-10 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden appearance-none cursor-pointer">
                            <option value="">-- Pilih Unit yang Dituju --</option>
                            <?php foreach ($unit_kategori_map as $unitName => $kategoriList): ?>
                                <option value="<?= htmlspecialchars($unitName); ?>"><?= htmlspecialchars($unitName); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5">Pilih unit kerja tujuan kendala (misal: Layanan Akademik / LAA, Laboratorium, Sarpras, IT Support).</p>
                </div>

                <!-- 3. Kategori Kendala (Dropdown Dinamis Berdasarkan Unit) -->
                <div>
                    <label for="kategori" class="block text-sm font-bold text-slate-700 mb-2">
                        3. Kategori Kendala <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="bi bi-tags-fill text-base"></i>
                        </span>
                        <select id="kategori" name="kategori" required disabled onchange="handleKategoriChange(this.value)"
                                class="w-full pl-11 pr-10 py-3 rounded-xl bg-slate-100 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden appearance-none cursor-not-allowed disabled:opacity-75">
                            <option value="">-- Silakan pilih Unit yang Dituju terlebih dahulu --</option>
                        </select>
                        <i class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                    </div>
                    <p id="kategori-hint" class="text-xs text-slate-400 mt-1.5">Kategori akan otomatis disesuaikan dengan unit yang dipilih di atas.</p>
                </div>

                <!-- 3b. Detail / Keterangan Kategori Lainnya -->
                <div id="container_kategori_lainnya" class="hidden transition-all duration-300">
                    <label for="kategori_lainnya" class="block text-sm font-bold text-slate-700 mb-2 flex items-center justify-between">
                        <span>Detail Kategori Lainnya <span class="text-rose-500">*</span></span>
                        <span class="text-[11px] font-normal text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200">
                            Wajib Diisi Khusus Opsi Lainnya
                        </span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-orange-500 pointer-events-none">
                            <i class="bi bi-pencil-square text-base"></i>
                        </span>
                        <input type="text" id="kategori_lainnya" name="kategori_lainnya" maxlength="150"
                               placeholder="Tuliskan kategori kendala yang Anda maksud..."
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-orange-50/40 border border-orange-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all outline-hidden">
                    </div>
                </div>

                <!-- 4 & 5. Grid: Prioritas & Subjek -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    
                    <!-- 4. Tingkat Prioritas -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            4. Tingkat Prioritas <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5" id="prioritasGroup">
                            
                            <!-- Rendah -->
                            <label id="card_prioritas_Rendah" class="relative flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-slate-300 hover:bg-slate-50/50 transition-all text-center select-none shadow-2xs">
                                <input type="radio" name="prioritas" value="Rendah" onchange="updatePrioritasUI(this.value)" class="sr-only">
                                <span class="p-dot w-2.5 h-2.5 rounded-full bg-slate-400 mb-1.5 transition-all"></span>
                                <span class="p-title text-xs font-semibold text-slate-700">Rendah</span>
                                <span class="p-desc text-[10px] text-slate-400 leading-tight mt-0.5">Umum</span>
                            </label>

                            <!-- Sedang (Default) -->
                            <label id="card_prioritas_Sedang" class="relative flex flex-col items-center justify-center p-3 rounded-xl border border-blue-500 bg-blue-50/80 ring-2 ring-blue-500/20 shadow-xs cursor-pointer transition-all text-center select-none">
                                <input type="radio" name="prioritas" value="Sedang" checked onchange="updatePrioritasUI(this.value)" class="sr-only">
                                <span class="p-dot w-2.5 h-2.5 rounded-full bg-blue-600 mb-1.5 scale-125 transition-all"></span>
                                <span class="p-title text-xs font-bold text-blue-700">Sedang</span>
                                <span class="p-desc text-[10px] text-blue-600 leading-tight mt-0.5">Standar</span>
                            </label>

                            <!-- Tinggi -->
                            <label id="card_prioritas_Tinggi" class="relative flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-amber-300 hover:bg-amber-50/20 transition-all text-center select-none shadow-2xs">
                                <input type="radio" name="prioritas" value="Tinggi" onchange="updatePrioritasUI(this.value)" class="sr-only">
                                <span class="p-dot w-2.5 h-2.5 rounded-full bg-amber-400 mb-1.5 transition-all"></span>
                                <span class="p-title text-xs font-semibold text-slate-700">Tinggi</span>
                                <span class="p-desc text-[10px] text-slate-400 leading-tight mt-0.5">Mendesak</span>
                            </label>

                            <!-- Darurat -->
                            <label id="card_prioritas_Darurat" class="relative flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-rose-300 hover:bg-rose-50/20 transition-all text-center select-none shadow-2xs">
                                <input type="radio" name="prioritas" value="Darurat" onchange="updatePrioritasUI(this.value)" class="sr-only">
                                <span class="p-dot w-2.5 h-2.5 rounded-full bg-rose-400 mb-1.5 transition-all"></span>
                                <span class="p-title text-xs font-semibold text-slate-700">Darurat</span>
                                <span class="p-desc text-[10px] text-slate-400 leading-tight mt-0.5">Kritis</span>
                            </label>

                        </div>
                        <p class="text-xs text-slate-400 mt-2">Pilih tingkat urgensi penanganan kendala yang dialami.</p>
                    </div>

                    <!-- 5. Subjek / Judul Kendala -->
                    <div>
                        <label for="subjek" class="block text-sm font-bold text-slate-700 mb-2">
                            5. Subjek / Ringkasan Kendala <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <i class="bi bi-chat-left-dots-fill text-base"></i>
                            </span>
                            <input type="text" id="subjek" name="subjek" required maxlength="150"
                                   placeholder="Contoh: Terkendala saat verifikasi berkas sidang TA..."
                                   class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 placeholder-slate-400 transition-all outline-hidden">
                        </div>
                        <p class="text-xs text-slate-400 mt-1.5">Tuliskan ringkasan inti persoalan secara singkat & jelas.</p>
                    </div>

                </div>

                <!-- 6. Deskripsi Masalah (TinyMCE Rich Text Editor) -->
                <div class="pt-2">
                    <label for="deskripsi" class="block text-sm font-bold text-slate-700 mb-2">
                        6. Deskripsi Kendala Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="6"
                              placeholder="Jelaskan detail kendala Anda selengkap mungkin..."></textarea>
                    <p class="text-xs text-slate-400 mt-2">
                        Jelaskan kronologi kendala, pesan error yang muncul, atau langkah yang sudah dicoba.
                    </p>
                </div>

                <!-- 7. Lampiran Berkas / Tangkapan Layar -->
                <div class="pt-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        7. Lampiran Pendukung (Opsional)
                    </label>
                    <div class="relative border-2 border-dashed border-slate-200 hover:border-orange-400 rounded-2xl p-6 transition-all bg-slate-50/50 hover:bg-orange-50/20 text-center">
                        <input type="file" id="lampiran" name="lampiran" 
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip,.rar"
                               onchange="handleFileSelected(this)"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        
                        <div id="upload-placeholder" class="space-y-2">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl shadow-xs">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700">
                                Klik atau seret file tangkapan layar / dokumen ke sini
                            </p>
                            <p class="text-[11px] text-slate-400">
                                Format: JPG, PNG, PDF, DOC, DOCX, ZIP (Maksimal 5 MB)
                            </p>
                        </div>

                        <div id="file-preview" class="hidden flex items-center justify-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center text-lg">
                                <i class="bi bi-file-earmark-check-fill"></i>
                            </div>
                            <div class="text-left">
                                <span id="file-name" class="block text-xs font-bold text-slate-800 truncate max-w-xs"></span>
                                <span id="file-size" class="block text-[10px] text-slate-400 font-mono"></span>
                            </div>
                            <button type="button" onclick="clearSelectedFile()" class="p-1 text-rose-500 hover:text-rose-700 relative z-20">
                                <i class="bi bi-x-circle-fill text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Container -->
                <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="<?= site_url('mahasiswa') ?>" 
                       class="w-full sm:w-auto px-6 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold text-center transition-all">
                        Batal
                    </a>
                    <button type="submit" id="btnSubmitTicket"
                            class="w-full sm:w-auto px-8 py-3 rounded-xl bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-xs font-bold shadow-lg shadow-orange-500/25 flex items-center justify-center gap-2 transition-all cursor-pointer">
                        <i class="bi bi-send-fill text-sm"></i>
                        <span>Kirim Tiket Kendala</span>
                    </button>
                </div>

            </form>

        </div>

    </main>

    <!-- Client-side Logic (Dinamis Unit & Kategori) -->
    <script>
        const unitKategoriMap = <?= json_encode($unit_kategori_map); ?>;

        function handleUnitChange(selectedUnit) {
            const kategoriSelect = document.getElementById('kategori');
            const kategoriHint = document.getElementById('kategori-hint');
            const containerLainnya = document.getElementById('container_kategori_lainnya');
            const inputLainnya = document.getElementById('kategori_lainnya');

            kategoriSelect.innerHTML = '';
            containerLainnya.classList.add('hidden');
            if (inputLainnya) {
                inputLainnya.required = false;
                inputLainnya.value = '';
            }

            if (!selectedUnit || !unitKategoriMap[selectedUnit]) {
                kategoriSelect.disabled = true;
                kategoriSelect.className = 'w-full pl-11 pr-10 py-3 rounded-xl bg-slate-100 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden appearance-none cursor-not-allowed disabled:opacity-75';
                
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = '-- Silakan pilih Unit yang Dituju terlebih dahulu --';
                kategoriSelect.appendChild(opt);
                kategoriHint.textContent = 'Kategori akan otomatis disesuaikan dengan unit yang dipilih di atas.';
                return;
            }

            kategoriSelect.disabled = false;
            kategoriSelect.className = 'w-full pl-11 pr-10 py-3 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden appearance-none cursor-pointer';

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Pilih Kategori Kendala (' + selectedUnit + ') --';
            kategoriSelect.appendChild(defaultOpt);

            const categories = unitKategoriMap[selectedUnit];
            categories.forEach(function(cat) {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                kategoriSelect.appendChild(opt);
            });

            kategoriHint.innerHTML = '<span class="text-emerald-600 font-semibold"><i class="bi bi-check-circle-fill mr-1"></i>Menampilkan ' + categories.length + ' pilihan kategori aktif untuk ' + selectedUnit + '</span>';
        }

        function handleKategoriChange(val) {
            const container = document.getElementById('container_kategori_lainnya');
            const inputLainnya = document.getElementById('kategori_lainnya');
            if (!container || !inputLainnya) return;

            const isLainnya = /lain/i.test((val || '').trim());

            if (isLainnya) {
                container.classList.remove('hidden');
                inputLainnya.required = true;
                setTimeout(function() { inputLainnya.focus(); }, 100);
            } else {
                container.classList.add('hidden');
                inputLainnya.required = false;
                inputLainnya.value = '';
            }
        }

        const prioritasConfig = {
            'Rendah': { activeCard: 'border-slate-500 bg-slate-100 ring-2 ring-slate-400/20 shadow-xs', inactiveCard: 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50 shadow-2xs', dotActive: 'bg-slate-600 scale-125', dotInactive: 'bg-slate-400', titleActive: 'text-slate-800 font-bold', titleInactive: 'text-slate-700 font-semibold', descActive: 'text-slate-600', descInactive: 'text-slate-400' },
            'Sedang': { activeCard: 'border-blue-500 bg-blue-50/80 ring-2 ring-blue-500/20 shadow-xs', inactiveCard: 'border-slate-200 bg-white hover:border-blue-300 hover:bg-blue-50/20 shadow-2xs', dotActive: 'bg-blue-600 scale-125', dotInactive: 'bg-blue-400', titleActive: 'text-blue-700 font-bold', titleInactive: 'text-slate-700 font-semibold', descActive: 'text-blue-600', descInactive: 'text-slate-400' },
            'Tinggi': { activeCard: 'border-amber-500 bg-amber-50/80 ring-2 ring-amber-500/20 shadow-xs', inactiveCard: 'border-slate-200 bg-white hover:border-amber-300 hover:bg-amber-50/20 shadow-2xs', dotActive: 'bg-amber-500 scale-125', dotInactive: 'bg-amber-400', titleActive: 'text-amber-700 font-bold', titleInactive: 'text-slate-700 font-semibold', descActive: 'text-amber-600', descInactive: 'text-slate-400' },
            'Darurat': { activeCard: 'border-rose-500 bg-rose-50/80 ring-2 ring-rose-500/20 shadow-xs', inactiveCard: 'border-slate-200 bg-white hover:border-rose-300 hover:bg-rose-50/20 shadow-2xs', dotActive: 'bg-rose-500 scale-125', dotInactive: 'bg-rose-400', titleActive: 'text-rose-700 font-bold', titleInactive: 'text-slate-700 font-semibold', descActive: 'text-rose-600', descInactive: 'text-slate-400' }
        };

        function updatePrioritasUI(selectedVal) {
            ['Rendah', 'Sedang', 'Tinggi', 'Darurat'].forEach(function(key) {
                const card = document.getElementById('card_prioritas_' + key);
                if (!card) return;
                const dot = card.querySelector('.p-dot');
                const title = card.querySelector('.p-title');
                const desc = card.querySelector('.p-desc');
                const cfg = prioritasConfig[key];

                if (key === selectedVal) {
                    card.className = 'relative flex flex-col items-center justify-center p-3 rounded-xl cursor-pointer transition-all text-center select-none ' + cfg.activeCard;
                    dot.className = 'p-dot w-2.5 h-2.5 rounded-full mb-1.5 transition-all ' + cfg.dotActive;
                    title.className = 'p-title text-xs ' + cfg.titleActive;
                    desc.className = 'p-desc text-[10px] leading-tight mt-0.5 ' + cfg.descActive;
                } else {
                    card.className = 'relative flex flex-col items-center justify-center p-3 rounded-xl cursor-pointer transition-all text-center select-none ' + cfg.inactiveCard;
                    dot.className = 'p-dot w-2.5 h-2.5 rounded-full mb-1.5 transition-all ' + cfg.dotInactive;
                    title.className = 'p-title text-xs ' + cfg.titleInactive;
                    desc.className = 'p-desc text-[10px] leading-tight mt-0.5 ' + cfg.descInactive;
                }
            });
        }

        function handleFileSelected(input) {
            const placeholder = document.getElementById('upload-placeholder');
            const preview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran berkas melebihi batas maksimal 5 MB!');
                    input.value = '';
                    return;
                }

                fileName.textContent = file.name;
                fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
                placeholder.classList.add('hidden');
                preview.classList.remove('hidden');
            }
        }

        function clearSelectedFile() {
            const input = document.getElementById('lampiran');
            const placeholder = document.getElementById('upload-placeholder');
            const preview = document.getElementById('file-preview');
            input.value = '';
            placeholder.classList.remove('hidden');
            preview.classList.add('hidden');
        }

        // Init TinyMCE
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#deskripsi',
                height: 260,
                menubar: false,
                branding: false,
                statusbar: true,
                plugins: 'lists link autoresize',
                toolbar: 'undo redo | bold italic underline | bullist numlist | link | removeformat',
                content_style: 'body { font-family: Plus Jakarta Sans, sans-serif; font-size: 14px; color: #1e293b; line-height: 1.6; margin: 12px; } p { margin: 0 0 8px 0; }',
                setup: function(editor) {
                    editor.on('change keyup', function() {
                        tinymce.triggerSave();
                    });
                }
            });

            // Form validation before submit
            document.getElementById('ticketingForm').addEventListener('submit', function(e) {
                if (tinymce.get('deskripsi')) {
                    const content = tinymce.get('deskripsi').getContent({ format: 'text' }).trim();
                    if (!content) {
                        e.preventDefault();
                        alert('Harap isi deskripsi kendala dengan rinci.');
                        tinymce.get('deskripsi').focus();
                        return false;
                    }
                }
            });
        });
    </script>
</body>
</html>