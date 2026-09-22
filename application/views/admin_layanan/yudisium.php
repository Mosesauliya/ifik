<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Yudisium — Admin LAA'; ?> - IFIK</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed', 100: '#ffedd5', 500: '#f97316',
                            600: '#ea580c', 700: '#c2410c', 900: '#7c2d12'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Icons & Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .unified-search-pill {
            display: flex; align-items: center;
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px);
            border: 1.5px solid #e2e8f0; border-radius: 16px;
            padding: 3px 14px; height: 46px;
            transition: border-color 0.25s, box-shadow 0.25s;
        }
        .unified-search-pill:focus-within {
            border-color: #ea580c !important; background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.14) !important;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Sidebar & Sticky Navbar -->
    <?php $this->load->view('admin_layanan/sidebar'); ?>
    <?php $this->load->view('partials/app_navbar', [
        'user_role_id'      => $this->session->userdata('role_id') ?? 5,
        'user_role_label'   => 'Admin Layanan (LAA)',
        'user_display_name' => 'Unit Layanan FIK',
        'user_display_sub'  => 'Rekapitulasi Yudisium'
    ]); ?>

    <main class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5 sm:pt-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Yudisium</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5 sm:gap-3">
                        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-500 text-white flex items-center justify-center shadow-lg shadow-purple-500/25 shrink-0">
                            <i class="bi bi-award-fill text-lg sm:text-xl"></i>
                        </span>
                        <span>Rekapitulasi Yudisium Kelulusan</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        Daftar mahasiswa yang telah lulus sidang akhir dan siap diproses penetapan kelulusan serta wisuda.
                    </p>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <a href="<?= site_url('adminlayanan/export_yudisium?jenjang=' . ($jenjang ?? 's1')); ?>" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm hover:shadow-md transition flex items-center gap-2">
                        <i class="bi bi-file-earmark-spreadsheet-fill text-sm"></i> Export Data Yudisium
                    </a>
                </div>
            </div>
        </div>

        <!-- 4 Stat Overview Cards (Matching Pendaftaran Sidang Layout) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
            <!-- Total Calon Yudisium -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs sm:shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider block truncate">Total Yudisium</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 block"><?= $stats['total'] ?? 0; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium block">Semua Jenjang</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                    <i class="bi bi-mortarboard"></i>
                </div>
            </div>

            <!-- Program Sarjana (S1) -->
            <a href="<?= site_url('adminlayanan/yudisium?jenjang=s1') ?>" class="bg-white p-4 sm:p-5 rounded-2xl border border-orange-200/80 shadow-xs sm:shadow-sm flex items-center justify-between hover:border-orange-400 transition group">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-orange-600 uppercase tracking-wider block truncate">Program S1</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 block group-hover:text-orange-600 transition"><?= $stats['total_s1'] ?? 0; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium block">Sarjana S1 FIK</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
            </a>

            <!-- Program Magister (S2) -->
            <a href="<?= site_url('adminlayanan/yudisium?jenjang=s2') ?>" class="bg-white p-4 sm:p-5 rounded-2xl border border-purple-200/80 shadow-xs sm:shadow-sm flex items-center justify-between hover:border-purple-400 transition group">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-purple-600 uppercase tracking-wider block truncate">Program S2</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 block group-hover:text-purple-600 transition"><?= $stats['total_s2'] ?? 0; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium block">Magister S2 FIK</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                    <i class="bi bi-award-fill"></i>
                </div>
            </a>

            <!-- Siap Wisuda -->
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-emerald-200/80 shadow-xs sm:shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-emerald-600 uppercase tracking-wider block truncate">Siap Wisuda</span>
                    <span class="text-xl sm:text-3xl font-extrabold text-slate-800 mt-0.5 block"><?= $stats['siap_wisuda'] ?? 0; ?></span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium block">Periode II 2026</span>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg sm:text-xl shrink-0">
                    <i class="bi bi-check2-all"></i>
                </div>
            </div>
        </div>

        <!-- Jenjang Tab Switcher Pills -->
        <div class="flex items-center gap-2 mb-6">
            <a href="<?= site_url('adminlayanan/yudisium?jenjang=s1'); ?>" 
               class="px-5 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 <?= ($jenjang ?? 's1') === 's1' ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
                <i class="bi bi-mortarboard"></i> Program Sarjana (S1)
                <span class="px-2 py-0.5 rounded-full text-[10px] <?= ($jenjang ?? 's1') === 's1' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' ?>">
                    <?= $stats['total_s1'] ?? 0; ?>
                </span>
            </a>
            <a href="<?= site_url('adminlayanan/yudisium?jenjang=s2'); ?>" 
               class="px-5 py-2.5 rounded-2xl text-xs font-extrabold transition flex items-center gap-2 <?= ($jenjang ?? 's1') === 's2' ? 'bg-orange-600 text-white shadow-md shadow-orange-500/20' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
                <i class="bi bi-mortarboard-fill"></i> Program Magister (S2)
                <span class="px-2 py-0.5 rounded-full text-[10px] <?= ($jenjang ?? 's1') === 's2' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' ?>">
                    <?= $stats['total_s2'] ?? 0; ?>
                </span>
            </a>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 mb-6">
            <form action="<?= site_url('adminlayanan/yudisium'); ?>" method="GET" class="flex items-center gap-2.5">
                <input type="hidden" name="jenjang" value="<?= htmlspecialchars($jenjang ?? 's1'); ?>">
                <div class="unified-search-pill flex-1 flex items-center justify-between gap-1 min-w-0">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                    <input type="text" name="q" value="<?= htmlspecialchars($search ?? ''); ?>" placeholder="Cari nama, NIM, atau nomor SK Yudisium..." class="w-full text-xs font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                    <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow-xs transition shrink-0">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Table: Rekap Yudisium -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">
            
            <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-award text-purple-600"></i> Calon Peserta Yudisium <?= strtoupper($jenjang ?? 'S1'); ?>
                    </h3>
                    <span class="text-xs text-slate-400 font-medium">(<?= count($list ?? []); ?> data)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-4 text-center w-12">No</th>
                            <th class="py-3.5 px-5">Nama Mahasiswa & NIM</th>
                            <th class="py-3.5 px-4">Program Studi</th>
                            <th class="py-3.5 px-4 text-center">IPK</th>
                            <th class="py-3.5 px-4 text-center">Tanggal Lulus Sidang</th>
                            <th class="py-3.5 px-4">Nomor SK Yudisium</th>
                            <th class="py-3.5 px-4 text-center">Status Yudisium</th>
                            <th class="py-3.5 px-4 text-center">Status Wisuda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php if (empty($list)): ?>
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <p class="font-bold text-slate-700">Belum Ada Mahasiswa Yudisium</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($list as $r): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-4 text-center font-bold text-slate-400"><?= $r['no']; ?></td>
                                    <td class="py-4 px-5 font-bold text-slate-800">
                                        <div><?= htmlspecialchars($r['nama']); ?></div>
                                        <div class="font-mono text-orange-600 text-[11px]"><?= htmlspecialchars($r['nim']); ?></div>
                                    </td>
                                    <td class="py-4 px-4 text-slate-600"><?= htmlspecialchars($r['prodi']); ?></td>
                                    <td class="py-4 px-4 text-center font-bold text-emerald-600"><?= htmlspecialchars($r['ipk']); ?></td>
                                    <td class="py-4 px-4 text-center font-mono text-[11px] text-slate-500"><?= htmlspecialchars($r['tanggal_lulus']); ?></td>
                                    <td class="py-4 px-4 font-mono text-[11px] text-slate-600 font-bold"><?= htmlspecialchars($r['nomor_sk']); ?></td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($r['status_yudisium']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <?= htmlspecialchars($r['status_wisuda']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </main>
</body>
</html>
