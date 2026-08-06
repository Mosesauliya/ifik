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
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-tr from-orange-600 to-amber-500 text-white rounded-2xl font-bold text-2xl flex items-center justify-center shadow-md shadow-orange-500/25">
                        I
                    </div>
                    <div>
                        <span class="font-bold text-xl text-slate-900 tracking-tight block leading-none">Geodata Mahasiswa</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-orange-500 mt-1 block">Akademik Mahasiswa</span>
                    </div>
                </div>
                <nav class="hidden md:flex items-center gap-3 p-2 bg-orange-50/70 rounded-2xl border border-orange-200/70 relative" id="mainNav">
                    <div class="nav-indicator-pill opacity-0" id="navIndicator"></div>

                    <a href="<?= site_url('mahasiswa'); ?>" class="nav-link relative z-10 font-bold px-6 py-3 rounded-xl text-xs flex items-center gap-2 tracking-wide text-slate-700">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" class="nav-link relative z-10 font-bold px-6 py-3 rounded-xl text-xs flex items-center gap-2 tracking-wide text-slate-700">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Pendaftaran TA</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/geodata'); ?>" class="nav-link active-link relative z-10 font-bold px-6 py-3 rounded-xl text-xs flex items-center gap-2 tracking-wide text-slate-700">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Geodata</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">

        <div class="bg-white rounded-[2rem] p-8 md:p-12 border border-orange-100 shadow-card-clean">
            
            <div class="flex items-center justify-between border-b border-orange-100/80 pb-6 mb-8">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-500 block mb-1">LOKASI & DOMISILI</span>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">Pengaturan Geodata</h1>
                </div>
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-xl font-bold shadow-xs">
                    <i class="bi bi-geo-alt"></i>
                </div>
            </div>

            <?php if($this->session->flashdata('success')): ?>
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl shadow-xs flex items-center gap-3">
                    <i class="bi bi-check-circle-fill text-xl text-emerald-600"></i>
                    <p class="text-sm font-bold"><?= $this->session->flashdata('success'); ?></p>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('mahasiswa/geodata'); ?>" method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Lengkap Tempat Tinggal</label>
                    <textarea class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 outline-none text-sm font-medium text-slate-800 leading-relaxed" name="alamat" rows="3" required><?= $mahasiswa['alamat'] ?? ''; ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kota / Kabupaten</label>
                        <input type="text" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 outline-none text-sm font-medium text-slate-800" name="kota" value="<?= $mahasiswa['kota'] ?? ''; ?>" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Provinsi</label>
                        <input type="text" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 outline-none text-sm font-medium text-slate-800" name="provinsi" value="<?= $mahasiswa['provinsi'] ?? ''; ?>" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Latitude</label>
                        <input type="text" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 outline-none text-sm font-mono text-slate-800" name="latitude" value="<?= $mahasiswa['latitude'] ?? ''; ?>" placeholder="-6.973000">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Longitude</label>
                        <input type="text" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 outline-none text-sm font-mono text-slate-800" name="longitude" value="<?= $mahasiswa['longitude'] ?? ''; ?>" placeholder="107.630000">
                    </div>
                </div>

                <div class="pt-4 border-t border-orange-100">
                    <button type="submit" class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold px-8 py-3.5 rounded-2xl shadow-md shadow-orange-500/20 transition text-sm inline-flex items-center gap-2">
                        <i class="bi bi-save text-base"></i> Simpan Geodata
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>"></script>
</body>
</html>
