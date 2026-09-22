<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Lihat Pembimbing — Admin LAA'; ?> - IFIK</title>

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
            transition: border-color 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important; background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.14), 0 10px 25px -5px rgba(234, 88, 12, 0.12) !important;
        }
        .unified-divider { width: 1px; height: 22px; background: #e2e8f0; margin: 0 8px; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Sidebar & Sticky Navbar -->
    <?php $this->load->view('admin_layanan/sidebar'); ?>
    <?php $this->load->view('partials/app_navbar', [
        'user_role_id'      => $this->session->userdata('role_id') ?? 5,
        'user_role_label'   => 'Admin Layanan (LAA)',
        'user_display_name' => 'Unit Layanan FIK',
        'user_display_sub'  => 'Rekap Pembimbing Tugas Akhir'
    ]); ?>

    <main class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5 sm:pt-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Lihat Pembimbing</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5 sm:gap-3">
                        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25 shrink-0">
                            <i class="bi bi-people text-lg sm:text-xl"></i>
                        </span>
                        <span>Rekapitulasi Dosen Pembimbing TA</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        Daftar lengkap alokasi Dosen Pembimbing 1 dan Dosen Pembimbing 2 seluruh mahasiswa Tugas Akhir.
                    </p>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <!-- Export to Excel Button -->
                    <a href="<?= site_url('adminlayanan/export_pembimbing'); ?>" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm hover:shadow-md transition flex items-center gap-2">
                        <i class="bi bi-file-earmark-spreadsheet-fill text-sm"></i> Export ke Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Search Bar & Filter -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 mb-6">
            <form action="<?= site_url('adminlayanan/lihat_pembimbing'); ?>" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <div class="unified-search-pill flex-1 flex items-center justify-between gap-1 min-w-0">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                    <input type="text" name="q" value="<?= htmlspecialchars($search ?? ''); ?>" placeholder="Cari nama mahasiswa, NIM, atau dosen pembimbing..." class="w-full text-xs font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                    <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow-xs transition shrink-0">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Table View: Rekap Pembimbing -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">
            
            <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-person-lines-fill text-orange-600"></i> Data Alokasi Pembimbing
                    </h3>
                    <span class="text-xs text-slate-400 font-medium">(<?= count($list ?? []); ?> Mahasiswa)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-4 text-center w-12">No</th>
                            <th class="py-3.5 px-5">Nama Mahasiswa</th>
                            <th class="py-3.5 px-4">Prodi</th>
                            <th class="py-3.5 px-4">Konsentrasi</th>
                            <th class="py-3.5 px-4">Dosen Wali</th>
                            <th class="py-3.5 px-4">Pembimbing 1</th>
                            <th class="py-3.5 px-4">Pembimbing 2</th>
                            <th class="py-3.5 px-4 text-center">Tgl Approve</th>
                            <th class="py-3.5 px-4 text-center">Tahapan</th>
                            <th class="py-3.5 px-4 text-center">Jenis TA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php if (empty($list)): ?>
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400">
                                    <p class="font-bold text-slate-700">Tidak Ada Data Pembimbing</p>
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
                                    <td class="py-4 px-4 text-slate-500"><?= htmlspecialchars($r['konsentrasi']); ?></td>
                                    <td class="py-4 px-4 text-slate-600"><?= htmlspecialchars($r['dosen_wali']); ?></td>
                                    <td class="py-4 px-4 font-semibold text-slate-800"><?= htmlspecialchars($r['pembimbing_1']); ?></td>
                                    <td class="py-4 px-4 font-semibold text-slate-800"><?= htmlspecialchars($r['pembimbing_2']); ?></td>
                                    <td class="py-4 px-4 text-center text-slate-500 font-mono text-[11px]"><?= htmlspecialchars($r['tanggal_approve']); ?></td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                                            <?= htmlspecialchars($r['tahapan']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                            <?= htmlspecialchars($r['jenis_ta']); ?>
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
