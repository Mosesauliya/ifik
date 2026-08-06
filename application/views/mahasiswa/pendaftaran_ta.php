<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body class="bg-white text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-500 selection:text-white">

    <!-- High-Visibility Floating Web Toast Notification (Placed Below Sticky Header h-24) -->
    <div id="inPageToastAlert" class="fixed top-28 right-6 z-[9999] max-w-md bg-rose-600 text-white p-4 rounded-2xl shadow-2xl flex items-start gap-3 transition-all duration-300 transform translate-y-[-20px] opacity-0 hidden ring-4 ring-rose-300/50">
        <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center text-sm font-bold shrink-0">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="flex-grow pt-0.5">
            <h4 class="text-xs font-bold uppercase tracking-wider text-rose-200">Pemberitahuan Validasi</h4>
            <p class="text-xs font-bold text-white mt-0.5 leading-relaxed" id="toastAlertMessage">Pesan validasi...</p>
        </div>
        <button type="button" class="text-white/80 hover:text-white p-1 text-sm transition" id="btnCloseToast">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Header Glass Navbar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-24">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-tr from-orange-600 to-amber-500 text-white rounded-2xl font-bold text-2xl flex items-center justify-center shadow-md shadow-orange-500/25">
                        I
                    </div>
                    <div>
                        <span class="font-bold text-xl text-slate-900 tracking-tight block leading-none">Pendaftaran TA</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-orange-500 mt-1 block">Akademik Mahasiswa</span>
                    </div>
                </div>
                <nav class="hidden md:flex items-center gap-3 p-2 bg-orange-50/70 rounded-2xl border border-orange-200/70 relative" id="mainNav">
                    <div class="nav-indicator-pill opacity-0" id="navIndicator"></div>

                    <a href="<?= site_url('mahasiswa'); ?>" class="nav-link relative z-10 font-bold px-7 py-3 rounded-xl text-xs flex items-center gap-2 tracking-wide text-slate-700">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" class="nav-link active-link relative z-10 font-bold px-7 py-3 rounded-xl text-xs flex items-center gap-2 tracking-wide text-slate-700">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Pendaftaran TA</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 w-full flex-grow">
        
        <!-- Section Title & Step Counter -->
        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-orange-500 block mb-1">FORMULIR PENDAFTARAN</span>
                <h2 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">Selesaikan data Anda</h2>
            </div>
            <span class="text-xs font-bold tracking-wider text-slate-400 uppercase" id="stepCounterText">LANGKAH 1 / 6</span>
        </div>

        <!-- Horizontal Stepper Progress Bar -->
        <div class="relative mb-14 px-4 py-2">
            <div class="absolute top-[22px] left-8 right-8 h-[2px] bg-slate-200/80 -translate-y-1/2 z-0"></div>
            <div class="absolute top-[22px] left-8 h-[2px] bg-orange-500 -translate-y-1/2 z-0 transition-all duration-300 shadow-xs" id="stepperProgressLine" style="width: 0%;"></div>

            <div class="relative z-10 flex justify-between items-center">
                <!-- Step 1 -->
                <div class="step-item active flex flex-col items-center" id="step-item-1">
                    <div class="step-counter w-11 h-11 rounded-full bg-orange-500 text-white font-bold flex items-center justify-center text-sm shadow-md ring-8 ring-orange-100 transition-all duration-300 z-10">1</div>
                    <span class="step-title font-bold text-xs text-orange-500 mt-2.5 text-center transition-all duration-300">Jenis TA</span>
                </div>

                <!-- Step 2 -->
                <div class="step-item flex flex-col items-center" id="step-item-2">
                    <div class="step-counter w-11 h-11 rounded-full bg-slate-100 text-slate-400 font-semibold flex items-center justify-center text-sm transition-all duration-300 z-10">2</div>
                    <span class="step-title font-medium text-xs text-slate-400 mt-2.5 text-center transition-all duration-300">Judul</span>
                </div>

                <!-- Step 3 -->
                <div class="step-item flex flex-col items-center" id="step-item-3">
                    <div class="step-counter w-11 h-11 rounded-full bg-slate-100 text-slate-400 font-semibold flex items-center justify-center text-sm transition-all duration-300 z-10">3</div>
                    <span class="step-title font-medium text-xs text-slate-400 mt-2.5 text-center transition-all duration-300">KSM</span>
                </div>

                <!-- Step 4 -->
                <div class="step-item flex flex-col items-center" id="step-item-4">
                    <div class="step-counter w-11 h-11 rounded-full bg-slate-100 text-slate-400 font-semibold flex items-center justify-center text-sm transition-all duration-300 z-10">4</div>
                    <span class="step-title font-medium text-xs text-slate-400 mt-2.5 text-center transition-all duration-300">Transkrip</span>
                </div>

                <!-- Step 5 -->
                <div class="step-item flex flex-col items-center" id="step-item-5">
                    <div class="step-counter w-11 h-11 rounded-full bg-slate-100 text-slate-400 font-semibold flex items-center justify-center text-sm transition-all duration-300 z-10">5</div>
                    <span class="step-title font-medium text-xs text-slate-400 mt-2.5 text-center transition-all duration-300">Pernyataan</span>
                </div>

                <!-- Step 6 -->
                <div class="step-item flex flex-col items-center" id="step-item-6">
                    <div class="step-counter w-11 h-11 rounded-full bg-slate-100 text-slate-400 font-semibold flex items-center justify-center text-sm transition-all duration-300 z-10">6</div>
                    <span class="step-title font-medium text-xs text-slate-400 mt-2.5 text-center transition-all duration-300">Bebas Lab</span>
                </div>
            </div>
        </div>

        <!-- Main Card Container -->
        <div class="bg-white rounded-[2rem] border border-slate-200/80 shadow-sm overflow-hidden">
            <form action="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" method="POST" enctype="multipart/form-data" id="formPendaftaranTA">
                
                <div class="p-8 md:p-12">
                    <!-- STEP 1 -->
                    <div id="step-content-1" class="step-content space-y-6">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-bold">Harap lengkapi data!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100/70 text-orange-600 font-bold text-lg flex items-center justify-center shrink-0">
                                01
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-orange-500 block">TA REGISTRATION</span>
                                <h3 class="text-2xl font-bold text-slate-900 tracking-tight">Jenis TA</h3>
                            </div>
                        </div>

                        <p class="text-sm text-slate-500 leading-relaxed">
                            Tentukan jenis tugas akhir yang sesuai dengan jalur akademik Anda.
                        </p>

                        <div class="space-y-4 pt-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Jenis tugas akhir <span class="text-orange-500">*</span>
                                </label>

                                <div class="relative">
                                    <select class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-slate-800 font-semibold text-sm appearance-none cursor-pointer pr-12 transition" name="jenis_ta" id="selectJenisTA" required>
                                        <option value="">-- Pilih Jenis TA --</option>
                                        <option value="Proyek Akhir">Proyek Akhir</option>
                                        <option value="Tugas Akhir Reguler">Tugas Akhir Reguler</option>
                                        <option value="Tugas Akhir jalur Magang (MBKM)">Tugas Akhir jalur Magang (MBKM)</option>
                                        <option value="Tugas Akhir jalur Prestasi / Lomba">Tugas Akhir jalur Prestasi / Lomba</option>
                                    </select>
                                    <div class="absolute right-5 top-1/2 -translate-y-1/2 text-orange-500 pointer-events-none">
                                        <i class="bi bi-chevron-down font-bold text-lg"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Choice Badge Card -->
                            <div class="hidden p-4 rounded-2xl bg-orange-50/50 border border-orange-200/70 flex items-center gap-3 transition-all duration-200" id="previewJenisTA">
                                <div class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center text-sm font-bold shrink-0 shadow-xs">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">JENIS TUGAS AKHIR DIPILIH</span>
                                    <span class="text-sm font-bold text-slate-900" id="previewTextJenisTA">Proyek Akhir</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2 -->
                    <div id="step-content-2" class="step-content space-y-6 hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-bold">Harap lengkapi usulan judul!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100/70 text-orange-600 font-bold text-lg flex items-center justify-center shrink-0">
                                02
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-orange-500 block">TA REGISTRATION</span>
                                <h3 class="text-2xl font-bold text-slate-900 tracking-tight">Judul Usulan</h3>
                            </div>
                        </div>

                        <p class="text-sm text-slate-500 leading-relaxed">
                            Masukkan 3 usulan alternatif judul Bahasa Indonesia, Bahasa Inggris, dan pilihan konsentrasi.
                        </p>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Usulan 1 (Utama) <span class="text-orange-500">*</span></label>
                                <input type="text" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-sm font-medium" name="judul_1" placeholder="Masukkan judul utama..." required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Usulan 2 (Alternatif 1) <span class="text-orange-500">*</span></label>
                                <input type="text" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-sm font-medium" name="judul_2" placeholder="Masukkan alternatif judul ke-2..." required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Usulan 3 (Alternatif 2) <span class="text-orange-500">*</span></label>
                                <input type="text" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-sm font-medium" name="judul_3" placeholder="Masukkan alternatif judul ke-3..." required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul dalam Bahasa Inggris <span class="text-orange-500">*</span></label>
                                <input type="text" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-sm font-medium" name="judul_en" placeholder="Title in English..." required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Konsentrasi</label>
                                <select class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none text-sm font-medium" name="konsentrasi_dkv">
                                    <option value="">-- Pilih Konsentrasi --</option>
                                    <option value="Desain Grafis">Desain Grafis</option>
                                    <option value="Multimedia">Multimedia & Animation</option>
                                    <option value="Illustrasi">Illustrasi & Desain Karakter</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div id="step-content-3" class="step-content space-y-6 hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-bold">Harap unggah berkas KSM PDF!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100/70 text-orange-600 font-bold text-lg flex items-center justify-center shrink-0">
                                03
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-orange-500 block">TA REGISTRATION</span>
                                <h3 class="text-2xl font-bold text-slate-900 tracking-tight">Unggah KSM</h3>
                            </div>
                        </div>

                        <p class="text-sm text-slate-500 leading-relaxed">
                            Unggah berkas Kartu Studi Mahasiswa (KSM) terkini berformat PDF.
                        </p>

                        <!-- Drop Zone Container -->
                        <div class="drop-zone border-2 border-dashed border-orange-300/80 rounded-3xl p-8 sm:p-12 text-center bg-orange-50/20 hover:bg-orange-50/50 transition-all duration-300 cursor-pointer group relative">
                            <input type="file" name="file_ksm" class="hidden" accept=".pdf" required>

                            <!-- Unselected Default Prompt -->
                            <div class="drop-zone-prompt">
                                <div class="w-16 h-16 bg-orange-500/10 text-orange-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 group-hover:scale-105 transition-transform">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 text-sm">Drag & Drop file PDF KSM di sini</h3>
                                <p class="text-xs text-slate-400 mt-1">atau klik untuk memilih file dari komputer (Hanya .PDF)</p>
                            </div>

                            <!-- Selected File Card (Integrated inside box) -->
                            <div class="drop-zone-selected hidden flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-white border border-emerald-300/90 rounded-2xl shadow-xs transition-all duration-300">
                                <div class="flex items-center gap-4 text-left min-w-0">
                                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl font-bold shrink-0 shadow-2xs">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider bg-emerald-100/80 px-2.5 py-0.5 rounded-full border border-emerald-300">File Terpilih</span>
                                            <h4 class="file-name font-bold text-sm text-slate-900 truncate">file.pdf</h4>
                                        </div>
                                        <p class="file-size text-xs text-slate-500 font-medium mt-1">0.00 MB • PDF Terverifikasi</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" class="btn-change-file text-xs bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                                        <i class="bi bi-arrow-repeat text-sm"></i> Ganti File
                                    </button>
                                    <button type="button" class="btn-reset-file text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                                        <i class="bi bi-trash3 text-sm"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4 -->
                    <div id="step-content-4" class="step-content space-y-6 hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-bold">Harap unggah berkas Transkrip Nilai PDF!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100/70 text-orange-600 font-bold text-lg flex items-center justify-center shrink-0">
                                04
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-orange-500 block">TA REGISTRATION</span>
                                <h3 class="text-2xl font-bold text-slate-900 tracking-tight">Transkrip Nilai</h3>
                            </div>
                        </div>

                        <p class="text-sm text-slate-500 leading-relaxed">
                            Unggah berkas Transkrip Nilai terakhir berformat PDF.
                        </p>

                        <!-- Drop Zone Container -->
                        <div class="drop-zone border-2 border-dashed border-orange-300/80 rounded-3xl p-8 sm:p-12 text-center bg-orange-50/20 hover:bg-orange-50/50 transition-all duration-300 cursor-pointer group relative">
                            <input type="file" name="file_transkrip" class="hidden" accept=".pdf" required>

                            <!-- Unselected Default Prompt -->
                            <div class="drop-zone-prompt">
                                <div class="w-16 h-16 bg-rose-500/10 text-rose-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 group-hover:scale-105 transition-transform">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 text-sm">Drag & Drop file PDF Transkrip di sini</h3>
                                <p class="text-xs text-slate-400 mt-1">Hanya file berformat PDF</p>
                            </div>

                            <!-- Selected File Card (Integrated inside box) -->
                            <div class="drop-zone-selected hidden flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-white border border-emerald-300/90 rounded-2xl shadow-xs transition-all duration-300">
                                <div class="flex items-center gap-4 text-left min-w-0">
                                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl font-bold shrink-0 shadow-2xs">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider bg-emerald-100/80 px-2.5 py-0.5 rounded-full border border-emerald-300">File Terpilih</span>
                                            <h4 class="file-name font-bold text-sm text-slate-900 truncate">file.pdf</h4>
                                        </div>
                                        <p class="file-size text-xs text-slate-500 font-medium mt-1">0.00 MB • PDF Terverifikasi</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" class="btn-change-file text-xs bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                                        <i class="bi bi-arrow-repeat text-sm"></i> Ganti File
                                    </button>
                                    <button type="button" class="btn-reset-file text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                                        <i class="bi bi-trash3 text-sm"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 5 -->
                    <div id="step-content-5" class="step-content space-y-6 hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-bold">Harap unggah berkas Surat Pernyataan PDF!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100/70 text-orange-600 font-bold text-lg flex items-center justify-center shrink-0">
                                05
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-orange-500 block">TA REGISTRATION</span>
                                <h3 class="text-2xl font-bold text-slate-900 tracking-tight">Surat Pernyataan</h3>
                            </div>
                        </div>

                        <p class="text-sm text-slate-500 leading-relaxed">
                            Unggah berkas Surat Pernyataan Keaslian & Orisinalitas Judul (PDF).
                        </p>

                        <!-- Drop Zone Container -->
                        <div class="drop-zone border-2 border-dashed border-orange-300/80 rounded-3xl p-8 sm:p-12 text-center bg-orange-50/20 hover:bg-orange-50/50 transition-all duration-300 cursor-pointer group relative">
                            <input type="file" name="file_pernyataan" class="hidden" accept=".pdf" required>

                            <!-- Unselected Default Prompt -->
                            <div class="drop-zone-prompt">
                                <div class="w-16 h-16 bg-amber-500/10 text-amber-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 group-hover:scale-105 transition-transform">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 text-sm">Drag & Drop file PDF Surat Pernyataan di sini</h3>
                                <p class="text-xs text-slate-400 mt-1">Hanya file berformat PDF</p>
                            </div>

                            <!-- Selected File Card (Integrated inside box) -->
                            <div class="drop-zone-selected hidden flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-white border border-emerald-300/90 rounded-2xl shadow-xs transition-all duration-300">
                                <div class="flex items-center gap-4 text-left min-w-0">
                                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl font-bold shrink-0 shadow-2xs">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider bg-emerald-100/80 px-2.5 py-0.5 rounded-full border border-emerald-300">File Terpilih</span>
                                            <h4 class="file-name font-bold text-sm text-slate-900 truncate">file.pdf</h4>
                                        </div>
                                        <p class="file-size text-xs text-slate-500 font-medium mt-1">0.00 MB • PDF Terverifikasi</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" class="btn-change-file text-xs bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                                        <i class="bi bi-arrow-repeat text-sm"></i> Ganti File
                                    </button>
                                    <button type="button" class="btn-reset-file text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                                        <i class="bi bi-trash3 text-sm"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 6 -->
                    <div id="step-content-6" class="step-content space-y-6 hidden">
                        <!-- Inline Validation Alert Box -->
                        <div class="step-inline-alert hidden p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl flex items-center gap-3">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600 text-lg"></i>
                            <span class="step-inline-alert-text text-xs font-bold">Harap unggah berkas Surat Bebas Lab PDF!</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-orange-100/70 text-orange-600 font-bold text-lg flex items-center justify-center shrink-0">
                                06
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-orange-500 block">TA REGISTRATION</span>
                                <h3 class="text-2xl font-bold text-slate-900 tracking-tight">Bebas Lab</h3>
                            </div>
                        </div>

                        <p class="text-sm text-slate-500 leading-relaxed">
                            Unggah berkas Surat Bebas Tanggungan Laboratorium (PDF).
                        </p>

                        <!-- Drop Zone Container -->
                        <div class="drop-zone border-2 border-dashed border-orange-300/80 rounded-3xl p-8 sm:p-12 text-center bg-orange-50/20 hover:bg-orange-50/50 transition-all duration-300 cursor-pointer group relative">
                            <input type="file" name="file_bebas_lab" class="hidden" accept=".pdf" required>

                            <!-- Unselected Default Prompt -->
                            <div class="drop-zone-prompt">
                                <div class="w-16 h-16 bg-emerald-500/10 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 group-hover:scale-105 transition-transform">
                                    <i class="bi bi-journal-check"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 text-sm">Drag & Drop file PDF Surat Bebas Lab di sini</h3>
                                <p class="text-xs text-slate-400 mt-1">Hanya file berformat PDF</p>
                            </div>

                            <!-- Selected File Card (Integrated inside box) -->
                            <div class="drop-zone-selected hidden flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-white border border-emerald-300/90 rounded-2xl shadow-xs transition-all duration-300">
                                <div class="flex items-center gap-4 text-left min-w-0">
                                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl font-bold shrink-0 shadow-2xs">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider bg-emerald-100/80 px-2.5 py-0.5 rounded-full border border-emerald-300">File Terpilih</span>
                                            <h4 class="file-name font-bold text-sm text-slate-900 truncate">file.pdf</h4>
                                        </div>
                                        <p class="file-size text-xs text-slate-500 font-medium mt-1">0.00 MB • PDF Terverifikasi</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" class="btn-change-file text-xs bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                                        <i class="bi bi-arrow-repeat text-sm"></i> Ganti File
                                    </button>
                                    <button type="button" class="btn-reset-file text-xs bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-1.5">
                                        <i class="bi bi-trash3 text-sm"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation -->
                <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" class="hidden text-slate-500 hover:text-slate-900 font-semibold px-4 py-3 rounded-xl transition flex items-center gap-2 text-sm" id="btnPrev">
                        <i class="bi bi-arrow-left text-base"></i> Kembali
                    </button>
                    
                    <div class="ml-auto flex gap-3">
                        <button type="button" class="flex items-center gap-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold px-7 py-3.5 rounded-2xl shadow-md shadow-orange-500/20 transition text-sm" id="btnNext">
                            <span>Lanjutkan</span> <i class="bi bi-arrow-right text-base"></i>
                        </button>
                        <button type="submit" class="hidden flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-7 py-3.5 rounded-2xl shadow-md shadow-emerald-600/20 transition text-sm" id="btnSubmit">
                            <i class="bi bi-send-fill text-base"></i> Kirim Pendaftaran
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- Help Footer Link -->
        <div class="text-center mt-8 text-xs text-slate-500">
            Butuh bantuan? <a href="#" class="text-orange-600 font-bold hover:underline">Hubungi administrasi akademik</a>
        </div>

    </div>

    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>"></script>
    <script src="<?= base_url('assets/js/pendaftaran_ta_stepper.js'); ?>"></script>
</body>
</html>
