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
<body class="bg-[#FAF8F5] text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-500 selection:text-white">

    <!-- Header Glass Navbar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-24">
                <!-- Brand -->
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-tr from-orange-600 to-amber-500 text-white rounded-2xl font-bold text-2xl flex items-center justify-center shadow-md shadow-orange-500/25">
                        I
                    </div>
                    <div>
                        <span class="font-bold text-xl text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-orange-500 mt-1 block">Akademik Mahasiswa</span>
                    </div>
                </div>

                <!-- Nav Menu -->
                <nav class="hidden md:flex items-center gap-3 p-2 bg-orange-50/70 rounded-2xl border border-orange-200/70 relative" id="mainNav">
                    <div class="nav-indicator-pill opacity-0" id="navIndicator"></div>

                    <a href="<?= site_url('mahasiswa'); ?>" class="nav-link active-link relative z-10 font-bold px-7 py-3 rounded-xl text-xs flex items-center gap-2 tracking-wide text-slate-700">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" class="nav-link relative z-10 font-bold px-7 py-3 rounded-xl text-xs flex items-center gap-2 tracking-wide text-slate-700">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Pendaftaran TA</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full space-y-10">

        <?php if($this->session->flashdata('success')): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <p class="text-sm font-semibold"><?= $this->session->flashdata('success'); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Hero Welcome Card & Quick Stats Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Hero Panel (2 Cols) -->
            <div class="lg:col-span-2 bg-white rounded-[2rem] p-8 md:p-10 border border-orange-100 shadow-card-clean relative overflow-hidden flex flex-col justify-between">
                <div class="absolute -top-32 -right-32 w-96 h-96 bg-orange-400/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 mb-8">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-orange-50 border border-orange-200/80 rounded-full text-xs font-bold text-orange-600 mb-4 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                        Portal Tugas Akhir IFIK
                    </div>

                    <h1 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight mb-3">
                        Selamat Datang, <span class="bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 bg-clip-text text-transparent"><?= $mahasiswa['nama_depan'] ?? 'Mahasiswa'; ?></span>! 👋
                    </h1>
                    <p class="text-slate-500 text-sm md:text-base max-w-xl leading-relaxed font-normal">
                        Pantau status usulan judul Tugas Akhir, kelengkapan berkas persyaratan PDF, dan progres persetujuan 4 tahap secara real-time.
                    </p>
                </div>

                <div class="relative z-10 pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-base">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">NIM MAHASISWA</span>
                            <span class="text-sm font-bold text-slate-800"><?= $mahasiswa['nim'] ?? '1301210001'; ?></span>
                        </div>
                    </div>

                    <a href="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold px-6 py-3 rounded-2xl shadow-md shadow-orange-500/20 transition text-sm">
                        <span>Pendaftaran TA</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Right Profile Info Card (1 Col) -->
            <div class="bg-white rounded-[2rem] p-8 border border-orange-100 shadow-card-clean flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-orange-500 mb-6 flex items-center gap-2">
                        <i class="bi bi-info-circle-fill"></i> Data Akademik
                    </h3>

                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">PROGRAM STUDI</span>
                            <span class="text-sm font-bold text-slate-800 block"><?= $mahasiswa['prodi'] ?? 'Informatika / DKV'; ?></span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">STATUS MAHASISWA</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif Bimbingan
                            </span>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 text-center">
                    <span class="text-xs text-slate-400 font-medium">Tahun Akademik 2025/2026</span>
                </div>
            </div>
        </div>

        <!-- Workflow Approval Chain Tracker -->
        <div class="bg-white rounded-[2rem] p-8 md:p-10 border border-orange-100 shadow-card-clean">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-500 block mb-1">TAHAP PERSETUJUAN</span>
                    <h2 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <i class="bi bi-diagram-3-fill text-orange-500"></i> Status Approval 4 Tahap
                    </h2>
                </div>
                <span class="text-xs text-orange-600 font-bold bg-orange-50 px-3.5 py-1.5 rounded-full border border-orange-200">Real-time Tracker</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Stage 1 -->
                <?php 
                    $w_status = $pendaftaran['status_approval_wali'] ?? 'Pending';
                    $w_badge = ($w_status === 'Approved') ? 'bg-emerald-500 text-white' : (($w_status === 'Rejected') ? 'bg-rose-500 text-white' : 'bg-orange-500 text-white');
                ?>
                <div class="bg-white p-6 rounded-2xl border border-orange-100 shadow-xs flex flex-col justify-between hover:border-orange-300 transition-all duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold tracking-wider uppercase text-slate-400">Tahap 01</span>
                        <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-base font-bold"><i class="bi bi-person-check"></i></div>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 mb-2">Dosen Wali</h3>
                        <span class="inline-block px-3.5 py-1 text-[11px] font-bold rounded-full shadow-xs <?= $w_badge; ?>"><?= $w_status; ?></span>
                    </div>
                </div>

                <!-- Stage 2 -->
                <?php 
                    $a_status = $pendaftaran['status_approval_admin'] ?? 'Pending';
                    $a_badge = ($a_status === 'Approved') ? 'bg-emerald-500 text-white' : (($a_status === 'Rejected') ? 'bg-rose-500 text-white' : 'bg-slate-100 text-slate-600');
                ?>
                <div class="bg-white p-6 rounded-2xl border border-orange-100 shadow-xs flex flex-col justify-between hover:border-orange-300 transition-all duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold tracking-wider uppercase text-slate-400">Tahap 02</span>
                        <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-base font-bold"><i class="bi bi-shield-check"></i></div>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 mb-2">Admin Layanan</h3>
                        <span class="inline-block px-3.5 py-1 text-[11px] font-bold rounded-full shadow-xs <?= $a_badge; ?>"><?= $a_status; ?></span>
                    </div>
                </div>

                <!-- Stage 3 -->
                <?php 
                    $k_status = $pendaftaran['status_approval_koor'] ?? 'Pending';
                    $k_badge = ($k_status === 'Approved') ? 'bg-emerald-500 text-white' : (($k_status === 'Rejected') ? 'bg-rose-500 text-white' : 'bg-slate-100 text-slate-600');
                ?>
                <div class="bg-white p-6 rounded-2xl border border-orange-100 shadow-xs flex flex-col justify-between hover:border-orange-300 transition-all duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold tracking-wider uppercase text-slate-400">Tahap 03</span>
                        <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-base font-bold"><i class="bi bi-award"></i></div>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 mb-2">Koordinator TA</h3>
                        <span class="inline-block px-3.5 py-1 text-[11px] font-bold rounded-full shadow-xs <?= $k_badge; ?>"><?= $k_status; ?></span>
                    </div>
                </div>

                <!-- Stage 4 -->
                <?php 
                    $kk_status = $pendaftaran['status_approval_kk'] ?? 'Pending';
                    $kk_badge = ($kk_status === 'Approved') ? 'bg-emerald-500 text-white' : (($kk_status === 'Rejected') ? 'bg-rose-500 text-white' : 'bg-slate-100 text-slate-600');
                ?>
                <div class="bg-white p-6 rounded-2xl border border-orange-100 shadow-xs flex flex-col justify-between hover:border-orange-300 transition-all duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold tracking-wider uppercase text-slate-400">Tahap 04</span>
                        <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-base font-bold"><i class="bi bi-mortarboard"></i></div>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 mb-2">Ketua KK</h3>
                        <span class="inline-block px-3.5 py-1 text-[11px] font-bold rounded-full shadow-xs <?= $kk_badge; ?>"><?= $kk_status; ?></span>
                    </div>
                </div>
            </div>

            <!-- Unlock Access Indicator Bar -->
            <div class="mt-8 pt-6 border-t border-orange-100 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                        <i class="bi bi-key-fill text-lg"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Status Akses Bimbingan Akademik</span>
                        <span class="text-xs text-slate-400">Memerlukan persetujuan hingga Tahap 04 Ketua KK</span>
                    </div>
                </div>

                <?php if(($pendaftaran['status_approval_kk'] ?? '') === 'Approved'): ?>
                    <span class="px-5 py-2.5 bg-emerald-500 text-white text-xs font-bold rounded-2xl flex items-center gap-2 shadow-md shadow-emerald-500/20">
                        <i class="bi bi-unlock-fill text-base"></i> UNLOCKED (Akses Terbuka)
                    </span>
                <?php else: ?>
                    <span class="px-5 py-2.5 bg-orange-500 text-white text-xs font-bold rounded-2xl flex items-center gap-2 shadow-md shadow-orange-500/20">
                        <i class="bi bi-lock-fill text-base"></i> LOCKED (Terkunci)
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Proposal Status Summary Widget (If Already Submitted) -->
        <?php if(!empty($pendaftaran['judul_1'])): ?>
            <div class="bg-white rounded-[2rem] p-8 md:p-10 border border-orange-100 shadow-card-clean">
                <div class="flex items-center justify-between border-b border-slate-100 pb-6 mb-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-orange-500 block mb-1">RINGKASAN USULAN</span>
                        <h3 class="text-xl font-bold text-slate-900">Judul Tugas Akhir Terdaftar</h3>
                    </div>
                    <span class="text-xs bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold px-3.5 py-1.5 rounded-full">
                        <i class="bi bi-check-circle-fill"></i> Terkirim
                    </span>
                </div>

                <div class="space-y-4">
                    <div class="p-5 rounded-2xl bg-orange-50/40 border border-orange-100">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block mb-1">JUDUL UTAMA (PROPOSAL 1)</span>
                        <h4 class="font-bold text-slate-900 text-base"><?= $pendaftaran['judul_1']; ?></h4>
                        <?php if(!empty($pendaftaran['judul_en'])): ?>
                            <p class="text-xs text-slate-500 italic mt-1">"<?= $pendaftaran['judul_en']; ?>"</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-orange-100 py-6 text-center text-xs text-slate-400 font-medium">
        &copy; <?= date('Y'); ?> IFIK Portal — Modul Mahasiswa & Dosen Wali
    </footer>

    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>"></script>
</body>
</html>
