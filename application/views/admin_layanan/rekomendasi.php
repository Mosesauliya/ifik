<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK Telkom University</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6, Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- TailwindCSS CDN -->
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
    
    <style>
        body {
            background-color: #fbf7f1;
            color: #1e293b;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(234, 88, 12, 0.15);
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.05);
            border-radius: 1.25rem;
        }

        .btn-3d-orange {
            background: linear-gradient(180deg, #fb923c 0%, #ea580c 60%, #c2410c 100%);
            box-shadow: 0 4px 12px -2px rgba(234, 88, 12, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.5);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-3d-orange:hover {
            background: linear-gradient(180deg, #fdba74 0%, #f97316 55%, #ea580c 100%);
            box-shadow: 0 6px 18px -2px rgba(234, 88, 12, 0.55);
            transform: translateY(-1px);
        }

        .btn-3d-kinetic {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            text-decoration: none !important;
            user-select: none;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
        }
        .btn-3d-kinetic:hover {
            transform: translateY(-2px) scale(1.03);
        }
        .btn-3d-kinetic .bg {
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            background: linear-gradient(180deg, #fb923c 0%, #ea580c 60%, #c2410c 100%);
            box-shadow: 0 10px 24px -4px rgba(234, 88, 12, 0.5), inset 0 1.5px 2px rgba(255, 255, 255, 0.7);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-3d-kinetic .wrap {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px 16px;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-16">

    <!-- Dedicated Admin LAA Sidebar Component -->
    <?php $this->load->view('admin_layanan/sidebar'); ?>

    <!-- Sub Navigation Page Title Bar -->
    <div class="glass-header sticky top-0 z-30 px-4 sm:px-6 py-3.5 mb-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="<?= site_url('adminlayanan'); ?>" class="w-10 h-10 rounded-xl bg-orange-100/70 border border-orange-200 text-brand-600 flex items-center justify-center font-bold text-sm shadow-xs shrink-0 hover:bg-orange-200/80 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-orange-100 text-brand-600 text-[10px] font-extrabold uppercase tracking-wide border border-orange-200">
                            Admin Layanan LAA
                        </span>
                        <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Rekomendasi &amp; Kelola Tahap TA</h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Peninjauan berkas submission rekomendasi Pembimbing 1 dan kontrol penyesuaian (downgrade) tahap bimbingan.</p>
                </div>
            </div>

            <!-- Profile & Quick Actions -->
            <div class="flex items-center gap-2.5 sm:gap-3 justify-end">
                <a href="<?= site_url('adminlayanan/pengaturan_jalur'); ?>" class="btn-3d-kinetic inline-flex items-center gap-2 shrink-0">
                    <span class="bg"></span>
                    <span class="wrap">
                        <i class="bi bi-sliders text-white text-xs mr-1.5"></i>
                        <span class="char">Pengaturan Jalur</span>
                    </span>
                </a>
                <div class="hidden lg:flex flex-col text-right ml-2">
                    <span class="text-xs font-bold text-slate-800 leading-tight">Admin Layanan LAA</span>
                    <span class="text-[10px] font-semibold text-slate-500">Layanan Akademik FIK</span>
                </div>
                <div class="hidden sm:flex w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 items-center justify-center font-bold text-base shadow-xs shrink-0">
                    <i class="fa-solid fa-user-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6">

        <!-- Stats Overview Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
            <!-- 1. Total Mahasiswa -->
            <a href="<?= site_url('adminlayanan/rekomendasi?filter=all'); ?>" class="group block text-left">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-orange-50/20 to-white shadow-md hover:shadow-xl relative overflow-hidden p-5 transition-all duration-300 hover:scale-[1.02]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-brand-600 transition-colors">Total Mahasiswa TA</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1"><?= $stats['total']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Peserta Bimbingan</p>
                        </div>
                        <div class="p-3 rounded-2xl border border-orange-200 bg-orange-50 text-brand-600 shadow-sm">
                            <i class="fa-solid fa-users text-lg"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 2. Jalur Sidang Reguler -->
            <a href="<?= site_url('adminlayanan/rekomendasi?filter=sidang'); ?>" class="group block text-left">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-emerald-50/20 to-white shadow-md hover:shadow-xl relative overflow-hidden p-5 transition-all duration-300 hover:scale-[1.02]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-emerald-600 transition-colors">Rekomendasi Sidang</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 text-emerald-600"><?= $stats['sidang']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Sidang Majelis Reguler</p>
                        </div>
                        <div class="p-3 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-600 shadow-sm">
                            <i class="bi bi-mortarboard-fill text-lg"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 3. Jalur Non-Sidang (Ekuivalensi) -->
            <a href="<?= site_url('adminlayanan/rekomendasi?filter=non_sidang'); ?>" class="group block text-left">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-purple-50/20 to-white shadow-md hover:shadow-xl relative overflow-hidden p-5 transition-all duration-300 hover:scale-[1.02]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-purple-600 transition-colors">Rekomendasi Non-Sidang</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 text-purple-600"><?= $stats['non_sidang']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Prestasi / HKI / Industri</p>
                        </div>
                        <div class="p-3 rounded-2xl border border-purple-200 bg-purple-50 text-purple-600 shadow-sm">
                            <i class="bi bi-award-fill text-lg"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- 4. Menunggu Rekomendasi P1 -->
            <a href="<?= site_url('adminlayanan/rekomendasi?filter=pending'); ?>" class="group block text-left">
                <div class="rounded-2xl border border-slate-200/90 bg-gradient-to-br from-white via-amber-50/20 to-white shadow-md hover:shadow-xl relative overflow-hidden p-5 transition-all duration-300 hover:scale-[1.02]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-amber-600 transition-colors">Belum Rekomendasi</p>
                            <h3 class="text-2xl font-black text-slate-900 mt-1 text-amber-600"><?= $stats['pending']; ?></h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Menunggu Pembimbing 1</p>
                        </div>
                        <div class="p-3 rounded-2xl border border-amber-200 bg-amber-50 text-amber-600 shadow-sm">
                            <i class="fa-solid fa-hourglass-half text-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Filter & Search Bar Container -->
        <div class="card-custom p-4 sm:p-5 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <!-- Filter Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                    <a href="<?= site_url('adminlayanan/rekomendasi?filter=all' . (!empty($search) ? '&q=' . urlencode($search) : '')); ?>" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= $filter_type === 'all' ? 'bg-slate-900 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Semua
                    </a>
                    <a href="<?= site_url('adminlayanan/rekomendasi?filter=non_sidang' . (!empty($search) ? '&q=' . urlencode($search) : '')); ?>" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= $filter_type === 'non_sidang' ? 'bg-purple-600 text-white shadow-md shadow-purple-500/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        <i class="bi bi-award-fill mr-1"></i> Non-Sidang (<?= $stats['non_sidang']; ?>)
                    </a>
                    <a href="<?= site_url('adminlayanan/rekomendasi?filter=sidang' . (!empty($search) ? '&q=' . urlencode($search) : '')); ?>" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= $filter_type === 'sidang' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        <i class="bi bi-mortarboard-fill mr-1"></i> Sidang Reguler (<?= $stats['sidang']; ?>)
                    </a>
                    <a href="<?= site_url('adminlayanan/rekomendasi?filter=pending' . (!empty($search) ? '&q=' . urlencode($search) : '')); ?>" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= $filter_type === 'pending' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        <i class="fa-solid fa-clock mr-1"></i> Belum Rekomendasi (<?= $stats['pending']; ?>)
                    </a>
                </div>

                <!-- Search Input Form -->
                <form action="<?= site_url('adminlayanan/rekomendasi'); ?>" method="GET" class="flex items-center gap-2 w-full md:w-80">
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter_type); ?>">
                    <div class="relative w-full">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($search); ?>" placeholder="Cari nama, NIM, judul, jalur..." 
                               class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-500 focus:bg-white transition">
                    </div>
                    <?php if(!empty($search)): ?>
                        <a href="<?= site_url('adminlayanan/rekomendasi?filter=' . htmlspecialchars($filter_type)); ?>" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 text-xs shrink-0" title="Reset Pencarian">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php endif; ?>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shadow-sm shrink-0 transition">
                        Cari
                    </button>
                </form>
            </div>
        </div>

        <!-- Table Data Card -->
        <div class="card-custom overflow-hidden mb-12">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4 min-w-[240px]">Mahasiswa &amp; Judul Tugas Akhir</th>
                            <th class="py-3.5 px-4 min-w-[180px]">Tim Pembimbing &amp; Penguji</th>
                            <th class="py-3.5 px-4 min-w-[120px] text-center">Tahap Bimbingan</th>
                            <th class="py-3.5 px-4 min-w-[200px]">Rekomendasi P1</th>
                            <th class="py-3.5 px-4 min-w-[150px] text-center">Aksi LAA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <?php if(empty($list)): ?>
                            <tr>
                                <td colspan="6" class="py-16 text-center text-slate-400 font-medium">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                                        <i class="bi bi-inbox-fill"></i>
                                    </div>
                                    <p class="font-bold text-slate-600">Tidak ada data mahasiswa ditemukan</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau tab filter.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($list as $r): ?>
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    
                                    <!-- 1. No -->
                                    <td class="py-4 px-4 text-center font-bold text-slate-400">
                                        <?= $no++; ?>
                                    </td>

                                    <!-- 2. Mahasiswa & Judul -->
                                    <td class="py-4 px-4">
                                        <div class="font-extrabold text-slate-900 group-hover:text-brand-600 transition-colors">
                                            <?= htmlspecialchars($r['nama_mahasiswa']); ?>
                                        </div>
                                        <div class="font-mono text-[11px] text-slate-500 font-semibold mt-0.5">
                                            NIM: <?= htmlspecialchars($r['nim']); ?>
                                        </div>
                                        <?php if(!empty($r['judul']) && $r['judul'] !== '-'): ?>
                                            <div class="mt-1 text-[11px] text-slate-600 line-clamp-2 italic leading-relaxed" title="<?= htmlspecialchars($r['judul']); ?>">
                                                "<?= htmlspecialchars($r['judul']); ?>"
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 3. Tim Dosen -->
                                    <td class="py-4 px-4 space-y-1">
                                        <div class="flex items-center gap-1.5" title="Pembimbing 1">
                                            <span class="w-5 h-5 rounded-md bg-orange-100 text-brand-700 font-black text-[9px] flex items-center justify-center shrink-0">P1</span>
                                            <span class="truncate text-slate-700 font-medium max-w-[180px]"><?= htmlspecialchars($r['pembimbing_1']); ?></span>
                                        </div>
                                        <div class="flex items-center gap-1.5" title="Pembimbing 2">
                                            <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-700 font-black text-[9px] flex items-center justify-center shrink-0">P2</span>
                                            <span class="truncate text-slate-700 font-medium max-w-[180px]"><?= htmlspecialchars($r['pembimbing_2']); ?></span>
                                        </div>
                                        <div class="flex items-center gap-1.5" title="Penguji 1">
                                            <span class="w-5 h-5 rounded-md bg-purple-100 text-purple-700 font-black text-[9px] flex items-center justify-center shrink-0">U1</span>
                                            <span class="truncate text-slate-700 font-medium max-w-[180px]"><?= htmlspecialchars($r['penguji_1']); ?></span>
                                        </div>
                                    </td>

                                    <!-- 4. Tahap Bimbingan Terkini -->
                                    <td class="py-4 px-4 text-center">
                                        <?php
                                            $tKey = $r['tahap_key'];
                                            $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                            if ($tKey === 'preview1') $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                            elseif ($tKey === 'preview2') $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                            elseif ($tKey === 'preview3') $badgeClass = 'bg-purple-50 text-purple-700 border-purple-200';
                                            elseif ($tKey === 'sidang')   $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-300 font-extrabold';
                                        ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold border <?= $badgeClass; ?>">
                                            <i class="bi bi-mortarboard-fill"></i>
                                            <?= htmlspecialchars($r['tahap_terkini']); ?>
                                        </span>
                                    </td>

                                    <!-- 5. Rekomendasi P1 -->
                                    <td class="py-4 px-4">
                                        <?php if(empty($r['id_rekomen'])): ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 font-semibold text-[11px]">
                                                <i class="fa-solid fa-hourglass-start animate-pulse"></i>
                                                Belum Ada Rekomendasi
                                            </span>
                                        <?php else: ?>
                                            <div class="space-y-1.5">
                                                <div class="flex items-center gap-2">
                                                    <?php if($r['recommendation_type'] === 'sidang'): ?>
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold text-[10px] border border-emerald-300">
                                                            <i class="bi bi-mortarboard-fill"></i> SIDANG REGULER
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-purple-100 text-purple-800 font-bold text-[10px] border border-purple-300">
                                                            <i class="bi bi-award-fill"></i> NON-SIDANG
                                                        </span>
                                                    <?php endif; ?>

                                                    <?php if(!empty($r['jalur_title']) && $r['jalur_title'] !== 'SIDANG'): ?>
                                                        <span class="font-bold text-slate-800 text-[11px] truncate max-w-[130px]" title="<?= htmlspecialchars($r['jalur_title']); ?>">
                                                            <?= htmlspecialchars($r['jalur_title']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if(!empty($r['catatan_dosen'])): ?>
                                                    <div class="text-[11px] text-slate-500 italic line-clamp-1" title="<?= htmlspecialchars($r['catatan_dosen']); ?>">
                                                        "<?= htmlspecialchars($r['catatan_dosen']); ?>"
                                                    </div>
                                                <?php endif; ?>

                                                <!-- File Berkas Submission -->
                                                <?php if(!empty($r['files_uploaded'])): ?>
                                                    <div class="flex flex-wrap gap-1.5 pt-1">
                                                        <?php foreach($r['files_uploaded'] as $fkey => $fitem): ?>
                                                            <?php if(is_array($fitem) && !empty($fitem['file'])): ?>
                                                                <button type="button" 
                                                                        onclick="openPdfPreview('<?= htmlspecialchars($fitem['file']); ?>', '<?= htmlspecialchars(addslashes($fitem['label'] ?? $fkey)); ?>', '<?= htmlspecialchars(addslashes($r['nama_mahasiswa'])); ?> (<?= htmlspecialchars($r['nim']); ?>)')"
                                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-orange-50 hover:bg-orange-100 text-brand-700 border border-orange-200 text-[10px] font-bold transition cursor-pointer"
                                                                        title="Buka Dokumen di Pop-up">
                                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                                    <span><?= htmlspecialchars($fitem['label'] ?? $fkey); ?></span>
                                                                    <i class="bi bi-eye-fill text-[8px] opacity-70"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 6. Aksi LAA (Downgrade Button) -->
                                    <td class="py-4 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Tombol Downgrade -->
                                            <button type="button" 
                                                    onclick="openModalDowngrade('<?= htmlspecialchars($r['nim']); ?>', '<?= htmlspecialchars(addslashes($r['nama_mahasiswa'])); ?>', '<?= htmlspecialchars($r['tahap_key']); ?>', '<?= htmlspecialchars($r['tahap_terkini']); ?>')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 hover:border-rose-300 font-bold text-[11px] shadow-xs hover:shadow transition cursor-pointer"
                                                    title="Turunkan / Downgrade Tahap Bimbingan">
                                                <i class="bi bi-arrow-down-circle-fill text-rose-600"></i>
                                                <span>Downgrade</span>
                                            </button>

                                            <?php if(!empty($r['files_uploaded'])): ?>
                                                <button type="button"
                                                        onclick="openModalDetailBerkas('<?= htmlspecialchars($r['nim']); ?>', '<?= htmlspecialchars(addslashes($r['nama_mahasiswa'])); ?>', '<?= htmlspecialchars($r['jalur_title'] ?? ''); ?>', '<?= htmlspecialchars(addslashes($r['catatan_dosen'] ?? '')); ?>', <?= htmlspecialchars(json_encode($r['files_uploaded'])); ?>)"
                                                        class="w-7 h-7 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition cursor-pointer"
                                                        title="Lihat Detail Rekomendasi">
                                                    <i class="bi bi-eye-fill text-xs"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- ============================================================== -->
    <!-- MODAL DOWNGRADE TAHAP PREVIEW (LAA) -->
    <!-- ============================================================== -->
    <div id="modalDowngrade" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
        <div class="relative bg-white rounded-3xl p-6 sm:p-7 w-full max-w-lg shadow-2xl border border-slate-100 transform transition-all">
            <button type="button" onclick="closeModalDowngrade()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center font-bold text-lg leading-none transition">&times;</button>
            
            <div class="flex items-center gap-3 mb-5 border-b border-slate-100 pb-4">
                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="bi bi-arrow-down-circle-fill"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Downgrade Tahap Bimbingan</h3>
                    <p class="text-xs text-slate-500">Penurunan tahapan bimbingan Tugas Akhir mahasiswa.</p>
                </div>
            </div>

            <form id="formDowngradeSubmit" onsubmit="submitDowngrade(event)" class="space-y-4">
                <input type="hidden" name="nim" id="dgNim" value="">
                <input type="hidden" id="dgTahapCurrentKey" value="">
                
                <!-- Info Mahasiswa -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-semibold">Nama Mahasiswa:</span>
                        <span class="font-bold text-slate-800 text-right" id="dgNama">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-semibold">NIM:</span>
                        <span class="font-mono font-bold text-slate-700" id="dgNimText">-</span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-200/60">
                        <span class="text-slate-400 font-semibold">Tahap Saat Ini:</span>
                        <span class="font-extrabold text-brand-600" id="dgTahapCurrent">-</span>
                    </div>
                </div>

                <!-- Pilihan Tujuan Downgrade -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">Pilih Tujuan Downgrade ke Tahap: <span class="text-rose-500">*</span></label>
                    <select name="ke_tahap" id="dgKeTahap" onchange="updateDowngradeImpacts()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition">
                        <!-- Options populated dynamically based on current stage -->
                    </select>
                </div>

                <!-- Notification Box: Data yang akan dihapus / di-reset -->
                <div id="dgImpactContainer" class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-950">
                    <div class="flex items-start gap-2.5">
                        <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <span class="font-bold text-xs text-rose-900 block mb-1">Pemberitahuan Data yang Akan Dihapus / Di-reset:</span>
                            <ul id="dgImpactList" class="space-y-1 list-disc list-inside text-[11px] font-medium text-rose-900">
                                <!-- Populated dynamically based on selected target stage -->
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Alasan Downgrade -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">Alasan / Catatan Downgrade: <span class="text-rose-500">*</span></label>
                    <textarea name="alasan" id="dgAlasan" rows="3" required placeholder="Tuliskan alasan penurunan tahap bimbingan..." 
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModalDowngrade()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitDowngrade" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition flex items-center gap-1.5">
                        <i class="bi bi-check2-circle text-sm"></i>
                        <span>Konfirmasi Downgrade</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL DETAIL REKOMENDASI & BERKAS -->
    <!-- ============================================================== -->
    <div id="modalDetailBerkas" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
        <div class="relative bg-white rounded-3xl p-6 sm:p-7 w-full max-w-lg shadow-2xl border border-slate-100 transform transition-all">
            <button type="button" onclick="closeModalDetailBerkas()" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center font-bold text-lg leading-none transition">&times;</button>
            
            <div class="flex items-center gap-3 mb-5 border-b border-slate-100 pb-4">
                <div class="w-10 h-10 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Berkas Rekomendasi</h3>
                    <p class="text-xs text-slate-500" id="detailMhsSub">-</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jalur Rekomendasi</span>
                    <span class="text-sm font-extrabold text-purple-700" id="detailJalurTitle">-</span>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Pembimbing 1</span>
                    <p class="text-xs text-slate-700 font-medium bg-slate-50 p-3 rounded-xl border border-slate-200 mt-1" id="detailCatatanDosen">-</p>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Berkas Pendukung Terlampir</span>
                    <div id="detailListFiles" class="space-y-2">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>

            <div class="pt-5 mt-5 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closeModalDetailBerkas()" class="px-5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL PRATINJAU DOKUMEN PDF (POP-UP CEK DOKUMEN) -->
    <!-- ============================================================== -->
    <div id="pdfModal" class="fixed inset-0 z-[10001] bg-slate-900/80 backdrop-blur-xs hidden items-center justify-center p-3 sm:p-5">
        <div class="bg-white rounded-2xl max-w-5xl w-full h-[88vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
            <div class="p-3.5 px-5 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3 truncate mr-4">
                    <div class="w-8 h-8 rounded-xl bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div class="truncate">
                        <h3 class="text-xs font-bold text-white flex items-center gap-2 truncate" id="pdfModalTitle">Pratinjau Dokumen</h3>
                        <p class="text-[10px] text-slate-400 truncate" id="pdfModalSubtitle">Memuat tampilan dokumen...</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a id="pdfModalNewTab" href="#" target="_blank" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-colors cursor-pointer" title="Buka di Tab Baru">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                    <button type="button" onclick="closePdfModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-colors cursor-pointer" title="Tutup">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            <div class="flex-1 bg-slate-100 relative overflow-hidden">
                <iframe id="pdfFrame" src="about:blank" class="w-full h-full border-none"></iframe>
            </div>
        </div>
    </div>

    <!-- Script Handling -->
    <script>
        // Modal PDF Viewer Handler (mirip Cek Dokumen Massal Popup)
        function openPdfPreview(url, title, subtitle) {
            const modal = document.getElementById('pdfModal');
            const frame = document.getElementById('pdfFrame');
            const titleEl = document.getElementById('pdfModalTitle');
            const subEl = document.getElementById('pdfModalSubtitle');
            const newTabEl = document.getElementById('pdfModalNewTab');

            if (titleEl) titleEl.textContent = title || 'Pratinjau Dokumen';
            if (subEl) subEl.textContent = subtitle || url;
            if (newTabEl) newTabEl.href = url;
            if (frame) frame.src = url;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closePdfModal() {
            const modal = document.getElementById('pdfModal');
            const frame = document.getElementById('pdfFrame');
            if (frame) frame.src = 'about:blank';
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            document.body.style.overflow = '';
        }

        // Logic dampak data yang dihapus / di-reset berdasarkan tahap asal & tujuan
        function getDowngradeImpacts(fromKey, toKey) {
            const impacts = [];
            const from = (fromKey || '').toLowerCase();
            const to = (toKey || '').toLowerCase();

            // Jika dari Sidang
            if (from === 'sidang') {
                impacts.push('Seluruh nilai sidang dari 4 dosen (Pembimbing 1, Pembimbing 2, Penguji 1, Penguji 2) dihapus.');
                impacts.push('Form penilaian sidang, evaluasi dosen, dan skor akhir kelulusan sidang di-reset.');
                impacts.push('Status pendaftaran TA dan kelulusan sidang dicabut (lulus_sidang = 0).');
            }

            // Jika target Preview 2 atau Preview 1 (dari Sidang atau Preview 3)
            if ((from === 'sidang' || from === 'preview3') && (to === 'preview2' || to === 'preview1')) {
                impacts.push('Seluruh berkas naskah draft tesis Preview 3 dihapus dari sistem.');
                impacts.push('Form rekomendasi Pembimbing 1 (Sidang/Non-Sidang) dan berkas pendukung dibatalkan.');
                impacts.push('Status kelulusan Preview 3 dicabut (lulus_preview3 = 0).');
            }

            // Jika target Preview 1 (dari Sidang, Preview 3, atau Preview 2)
            if (to === 'preview1') {
                impacts.push('Seluruh berkas naskah draft tesis Preview 2 dihapus dari sistem.');
                impacts.push('Nilai Desk Evaluation (DE) dari 4 dosen dan jadwal DE Preview 2 di-reset.');
                impacts.push('Status kelulusan Preview 2 dicabut (lulus_preview2 = 0).');
            }

            // Target stage itu sendiri
            const stageLabels = { 'preview1': 'Preview 1', 'preview2': 'Preview 2', 'preview3': 'Preview 3' };
            const targetLabel = stageLabels[to] || to;
            impacts.push(`Status kelulusan ${targetLabel} dicabut dan berkas di-reset ke "Pending" untuk bimbingan ulang.`);

            return impacts;
        }

        function updateDowngradeImpacts() {
            const fromKey = document.getElementById('dgTahapCurrentKey').value;
            const toKey   = document.getElementById('dgKeTahap').value;
            const listEl  = document.getElementById('dgImpactList');
            if (!listEl) return;

            listEl.innerHTML = '';
            const impacts = getDowngradeImpacts(fromKey, toKey);
            impacts.forEach(text => {
                const li = document.createElement('li');
                li.textContent = text;
                listEl.appendChild(li);
            });
        }

        function openModalDowngrade(nim, nama, tahapKey, tahapDisplay) {
            document.getElementById('dgNim').value = nim;
            document.getElementById('dgNimText').innerText = nim;
            document.getElementById('dgNama').innerText = nama;
            document.getElementById('dgTahapCurrent').innerText = tahapDisplay;
            document.getElementById('dgTahapCurrentKey').value = tahapKey;
            document.getElementById('dgAlasan').value = '';

            const sel = document.getElementById('dgKeTahap');
            sel.innerHTML = '';

            // Hierarchy: preview1=1, preview2=2, preview3=3, sidang=4
            const stages = [
                { key: 'preview1', label: 'Preview 1' },
                { key: 'preview2', label: 'Preview 2' },
                { key: 'preview3', label: 'Preview 3' }
            ];

            const rankMap = { 'preview1': 1, 'preview2': 2, 'preview3': 3, 'sidang': 4 };
            const currRank = rankMap[tahapKey] || 1;

            const allowed = stages.filter(s => rankMap[s.key] < currRank);

            if (allowed.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Dapat Downgrade',
                    text: 'Mahasiswa sudah berada di tahap terendah (Preview 1).'
                });
                return;
            }

            allowed.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.key;
                opt.innerText = s.label;
                sel.appendChild(opt);
            });

            // Perbarui list dampak secara langsung
            updateDowngradeImpacts();

            document.getElementById('modalDowngrade').classList.remove('hidden');
        }

        function closeModalDowngrade() {
            document.getElementById('modalDowngrade').classList.add('hidden');
        }

        function submitDowngrade(e) {
            e.preventDefault();

            const nim     = document.getElementById('dgNim').value;
            const keTahap = document.getElementById('dgKeTahap').value;
            const alasan  = document.getElementById('dgAlasan').value;
            const btn     = document.getElementById('btnSubmitDowngrade');

            if (!alasan.trim()) {
                Swal.fire({ icon: 'warning', title: 'Catatan Diperlukan', text: 'Mohon masukkan alasan downgrade.' });
                return;
            }

            const currentTahapKey = document.getElementById('dgTahapCurrentKey').value;
            const targetSelect    = document.getElementById('dgKeTahap');
            const targetLabel     = targetSelect.options[targetSelect.selectedIndex]?.text || keTahap;
            const currentDisplay  = document.getElementById('dgTahapCurrent').innerText;
            const namaMhs         = document.getElementById('dgNama').innerText;
            const impacts         = getDowngradeImpacts(currentTahapKey, keTahap);

            const impactHtml = impacts.map(item => `<li class="flex items-start gap-1.5 text-xs text-rose-900"><i class="bi bi-x-circle-fill text-rose-600 mt-0.5 shrink-0 text-[10px]"></i><span>${item}</span></li>`).join('');

            Swal.fire({
                title: 'Konfirmasi Penurunan Tahap',
                html: `
                    <div class="space-y-3 text-left">
                        <p class="text-xs text-slate-600">
                            Apakah Anda yakin ingin menurunkan mahasiswa <strong>${nim}</strong> (${namaMhs}) dari <strong>${currentDisplay}</strong> ke <strong>${targetLabel}</strong>?
                        </p>
                        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200">
                            <p class="text-[11px] font-extrabold text-rose-800 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                <i class="bi bi-exclamation-triangle-fill text-rose-600"></i>
                                Data yang akan dihapus / di-reset:
                            </p>
                            <ul class="space-y-1.5">
                                ${impactHtml}
                            </ul>
                        </div>
                        <p class="text-[11px] text-slate-400 italic text-center">Tindakan ini tidak dapat dibatalkan secara otomatis.</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-arrow-down-circle-fill mr-1"></i> Ya, Downgrade!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses...';

                    const formData = new FormData();
                    formData.append('nim', nim);
                    formData.append('ke_tahap', keTahap);
                    formData.append('alasan', alasan);

                    fetch('<?= site_url("adminlayanan/downgrade_preview_ajax") ?>', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                                timer: 1800,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan sistem.' });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Kesalahan komunikasi server.' });
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-check2-circle text-sm"></i> <span>Konfirmasi Downgrade</span>';
                    });
                }
            });
        }

        function openModalDetailBerkas(nim, nama, jalur, catatan, files) {
            document.getElementById('detailMhsSub').innerText = `${nama} (${nim})`;
            document.getElementById('detailJalurTitle').innerText = jalur || 'Non-Sidang';
            document.getElementById('detailCatatanDosen').innerText = catatan || 'Tidak ada catatan khusus dari dosen.';

            const container = document.getElementById('detailListFiles');
            container.innerHTML = '';

            let hasFiles = false;
            if (files && typeof files === 'object') {
                Object.keys(files).forEach(k => {
                    const item = files[k];
                    if (item && item.file) {
                        hasFiles = true;
                        const labelText = item.label || k;
                        const safeLabel = labelText.replace(/'/g, "\\'");
                        const safeNama  = nama.replace(/'/g, "\\'");
                        const div = document.createElement('div');
                        div.className = 'flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs';
                        div.innerHTML = `
                            <div class="flex items-center gap-2.5 truncate mr-2">
                                <div class="w-8 h-8 rounded-lg bg-orange-100 text-brand-600 flex items-center justify-center font-bold text-sm shrink-0">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="truncate">
                                    <span class="font-bold text-slate-800 block truncate">${labelText}</span>
                                    <span class="text-[10px] text-slate-400 truncate block">${item.original_name || 'Dokumen'}</span>
                                </div>
                            </div>
                            <button type="button" onclick="openPdfPreview('${item.file}', '${safeLabel}', '${safeNama} (${nim})')" class="px-3 py-1.5 rounded-lg bg-brand-500 hover:bg-brand-600 text-white font-bold text-[11px] shrink-0 transition flex items-center gap-1 shadow-xs cursor-pointer">
                                <i class="bi bi-eye-fill text-[11px]"></i>
                                <span>Buka File</span>
                            </button>
                        `;
                        container.appendChild(div);
                    }
                });
            }

            if (!hasFiles) {
                container.innerHTML = '<div class="text-center py-4 text-slate-400 italic text-xs">Tidak ada file fisik yang diunggah.</div>';
            }

            document.getElementById('modalDetailBerkas').classList.remove('hidden');
        }

        function closeModalDetailBerkas() {
            document.getElementById('modalDetailBerkas').classList.add('hidden');
        }

        // Close modals on dark backdrop click & Escape key
        document.addEventListener('DOMContentLoaded', () => {
            const pdfModal = document.getElementById('pdfModal');
            if (pdfModal) {
                pdfModal.addEventListener('click', (e) => {
                    if (e.target === pdfModal) closePdfModal();
                });
            }

            const modalDowngrade = document.getElementById('modalDowngrade');
            if (modalDowngrade) {
                modalDowngrade.addEventListener('click', (e) => {
                    if (e.target === modalDowngrade) closeModalDowngrade();
                });
            }

            const modalDetail = document.getElementById('modalDetailBerkas');
            if (modalDetail) {
                modalDetail.addEventListener('click', (e) => {
                    if (e.target === modalDetail) closeModalDetailBerkas();
                });
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const pdfModal = document.getElementById('pdfModal');
                if (pdfModal && !pdfModal.classList.contains('hidden')) {
                    closePdfModal();
                    return;
                }
                const modalDetail = document.getElementById('modalDetailBerkas');
                if (modalDetail && !modalDetail.classList.contains('hidden')) {
                    closeModalDetailBerkas();
                    return;
                }
                const modalDowngrade = document.getElementById('modalDowngrade');
                if (modalDowngrade && !modalDowngrade.classList.contains('hidden')) {
                    closeModalDowngrade();
                    return;
                }
            }
        });
    </script>

</body>
</html>
