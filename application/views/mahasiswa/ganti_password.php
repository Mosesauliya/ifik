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

    <!-- Header Glass Navbar with Animated Sliding Indicator -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-24">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-tr from-orange-600 to-amber-500 text-white rounded-2xl font-bold text-2xl flex items-center justify-center shadow-md shadow-orange-500/25">
                        I
                    </div>
                    <div>
                        <span class="font-bold text-xl text-slate-900 tracking-tight block leading-none">Ganti Password</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-orange-500 mt-1 block">Akademik Mahasiswa</span>
                    </div>
                </div>
                <nav class="hidden md:flex items-center gap-3 p-2 bg-orange-50/70 rounded-2xl border border-orange-200/70 relative" id="mainNav">
                    <div class="nav-indicator-pill opacity-0" id="navIndicator"></div>

                    <a href="<?= site_url('mahasiswa'); ?>" class="nav-link relative z-10 font-bold px-5 py-3 rounded-xl text-xs flex items-center gap-2 tracking-wide text-slate-700">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/pendaftaran_ta'); ?>" class="nav-link relative z-10 font-bold px-5 py-3 rounded-xl text-xs flex items-center gap-2 tracking-wide text-slate-700">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Pendaftaran TA</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/geodata'); ?>" class="nav-link relative z-10 font-bold px-5 py-3 rounded-xl text-xs flex items-center gap-2 tracking-wide text-slate-700">
                        <i class="bi bi-geo-alt"></i>
                        <span>Geodata</span>
                    </a>
                    <a href="<?= site_url('mahasiswa/ganti_password'); ?>" class="nav-link active-link relative z-10 font-bold px-5 py-3 rounded-xl text-xs flex items-center gap-2 tracking-wide text-slate-700">
                        <i class="bi bi-key-fill"></i>
                        <span>Ganti Password</span>
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-md mx-auto px-4 py-12 flex-grow w-full">

        <div class="bg-white rounded-[2rem] p-8 border border-orange-100 shadow-card-clean">
            
            <div class="text-center mb-8">
                <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-4 shadow-xs">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ganti Password</h1>
                <p class="text-xs text-slate-500 mt-1 font-medium">Perbarui kata sandi akun Anda demi keamanan.</p>
            </div>

            <?php if(validation_errors()): ?>
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-bold">
                    <?= validation_errors(); ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('mahasiswa/ganti_password'); ?>" method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Password Baru</label>
                    <input type="password" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 outline-none text-sm font-medium text-slate-800" name="password_baru" placeholder="Minimal 6 karakter..." required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                    <input type="password" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 outline-none text-sm font-medium text-slate-800" name="konfirmasi_password" placeholder="Ulangi password baru..." required>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold px-6 py-3.5 rounded-2xl shadow-md shadow-orange-500/20 transition text-sm flex items-center justify-center gap-2">
                        <i class="bi bi-check-lg text-base"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="<?= base_url('assets/js/navbar_animated.js'); ?>"></script>
</body>
</html>
