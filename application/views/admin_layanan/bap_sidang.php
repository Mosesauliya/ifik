<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BAP Sidang — Admin LAA'; ?> - IFIK</title>

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
        'user_display_sub'  => 'Berita Acara Sidang (BAP)'
    ]); ?>

    <main class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5 sm:pt-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">BAP Sidang</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5 sm:gap-3">
                        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-rose-600 to-pink-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/25 shrink-0">
                            <i class="bi bi-file-earmark-text-fill text-lg sm:text-xl"></i>
                        </span>
                        <span>Pengajuan BAP Sidang Tugas Akhir</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        Daftar dokumen BAP IGrACIAS dan Berita Acara Resmi Fakultas Industri Kreatif beserta nilai evaluasi sidang.
                    </p>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="px-3.5 py-2 rounded-xl bg-orange-50 text-orange-800 text-xs font-extrabold uppercase tracking-wider border border-orange-200 shadow-xs flex items-center gap-1.5">
                        <i class="bi bi-check2-circle text-orange-600"></i> Total: <?= count($list ?? []); ?> BAP
                    </span>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 mb-6">
            <form action="<?= site_url('adminlayanan/bap_sidang'); ?>" method="GET" class="flex items-center gap-2.5">
                <div class="unified-search-pill flex-1 flex items-center justify-between gap-1 min-w-0">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2 shrink-0"></i>
                    <input type="text" name="q" value="<?= htmlspecialchars($search ?? ''); ?>" placeholder="Cari nama peserta sidang, NIM, atau dosen penguji..." class="w-full text-xs font-semibold bg-transparent border-none focus:outline-none text-slate-800 placeholder:text-slate-400">
                    <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow-xs transition shrink-0">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Table View: BAP Sidang (Matching Photo 3 & 4) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">
            
            <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-file-earmark-check text-rose-600"></i> Daftar Berita Acara Sidang (BAP)
                    </h3>
                    <span class="text-xs text-slate-400 font-medium">(<?= count($list ?? []); ?> data)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-5">Peserta Sidang</th>
                            <th class="py-3.5 px-4">Prodi / Peminatan</th>
                            <th class="py-3.5 px-5">Dosen Pembimbing</th>
                            <th class="py-3.5 px-5">Dosen Penguji</th>
                            <th class="py-3.5 px-4">Dokumen</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php if (empty($list)): ?>
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <p class="font-bold text-slate-700">Belum Ada Pengajuan BAP Sidang</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($list as $r): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    
                                    <!-- Peserta Sidang -->
                                    <td class="py-4 px-5">
                                        <div class="font-mono text-orange-600 text-[11px] font-bold"><?= htmlspecialchars($r['nim']); ?></div>
                                        <div class="font-bold text-slate-800"><?= htmlspecialchars($r['nama']); ?></div>
                                    </td>

                                    <!-- Prodi / Peminatan -->
                                    <td class="py-4 px-4 text-slate-600">
                                        <div><?= htmlspecialchars($r['prodi']); ?> /</div>
                                        <div class="text-[11px] text-slate-400"><?= htmlspecialchars($r['konsentrasi']); ?></div>
                                    </td>

                                    <!-- Dosen Pembimbing -->
                                    <td class="py-4 px-5 space-y-1">
                                        <div>
                                            <span class="font-bold text-slate-500 text-[10px] uppercase block">Dosen Pembimbing 1 :</span>
                                            <span class="text-slate-800"><?= htmlspecialchars($r['pembimbing_1']); ?></span>
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-500 text-[10px] uppercase block">Dosen Pembimbing 2 :</span>
                                            <span class="text-slate-800"><?= htmlspecialchars($r['pembimbing_2']); ?></span>
                                        </div>
                                    </td>

                                    <!-- Dosen Penguji -->
                                    <td class="py-4 px-5 space-y-1">
                                        <div>
                                            <span class="font-bold text-slate-500 text-[10px] uppercase block">Dosen Penguji 1 :</span>
                                            <span class="text-slate-800"><?= htmlspecialchars($r['penguji_1']); ?></span>
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-500 text-[10px] uppercase block">Dosen Penguji 2 :</span>
                                            <span class="text-slate-800"><?= htmlspecialchars($r['penguji_2']); ?></span>
                                        </div>
                                    </td>

                                    <!-- Dokumen BAP -->
                                    <td class="py-4 px-4 font-mono text-[11px] space-y-1">
                                        <div class="flex items-center gap-1.5 text-rose-600 font-bold">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            <span class="truncate max-w-[140px]"><?= htmlspecialchars($r['dokumen_bap']); ?></span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-blue-600 font-bold">
                                            <i class="bi bi-file-earmark-check"></i>
                                            <span class="truncate max-w-[140px]">BAP_Fakultas_<?= htmlspecialchars($r['nim']); ?>.pdf</span>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4 px-4 text-center space-y-1">
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 block">
                                            <?= htmlspecialchars($r['status_igracias']); ?>
                                        </span>
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 block">
                                            <?= htmlspecialchars($r['status_fakultas']); ?>
                                        </span>
                                    </td>

                                    <!-- Action: 2 Distinct Buttons (Matching Photo 3 & 4) -->
                                    <td class="py-4 px-4 text-center space-y-1.5">
                                        <!-- Tombol Dokumen 1: BAP IGrACIAS (Foto 4) -->
                                        <button type="button" onclick="openBapIgracias('<?= $r['nim']; ?>', '<?= htmlspecialchars($r['nama']); ?>')"
                                                class="w-full px-3 py-1 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-bold text-[10.5px] shadow-2xs hover:shadow-xs transition flex items-center justify-center gap-1">
                                            <i class="bi bi-file-earmark-text-fill"></i> BAP (IGRACIAS)
                                        </button>

                                        <!-- Tombol Dokumen 2: BAP FAKULTAS (Foto 5 & 6) -->
                                        <button type="button" onclick="openBapFakultas('<?= $r['nim']; ?>', '<?= htmlspecialchars($r['nama']); ?>')"
                                                class="w-full px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-bold text-[10.5px] shadow-2xs hover:shadow-xs transition flex items-center justify-center gap-1">
                                            <i class="bi bi-award-fill"></i> BAP FAKULTAS
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </main>

    <!-- Modal Preview Dokumen BAP Interaktif -->
    <div id="bapModal" class="fixed inset-0 z-[99999] flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeBapModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl h-[90vh] mx-4 flex flex-col overflow-hidden z-10">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill text-rose-600 text-xl"></i>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm" id="bapModalTitle">Dokumen BAP Sidang</h3>
                        <p class="text-[11px] text-slate-400" id="bapModalSub">Pratinjau Dokumen Berita Acara Sidang</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a id="btnCetakBap" href="#" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                        <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
                    </a>
                    <button onclick="closeBapModal()" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-slate-800 flex items-center justify-center transition">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            
            <div class="flex-1 bg-slate-200/70 p-4 overflow-y-auto">
                <iframe id="bapIframe" src="" class="w-full min-h-[850px] bg-white rounded-2xl shadow-md border border-slate-200" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    <script>
        function openBapIgracias(nim, nama) {
            document.getElementById('bapModalTitle').textContent = 'BAP IGrACIAS — ' + nama + ' (' + nim + ')';
            document.getElementById('bapModalSub').textContent = 'Dokumen 1: Berita Acara Penyelenggaraan Sidang TA/PA (Sistem IGrACIAS)';
            document.getElementById('bapIframe').src = '<?= site_url('adminlayanan/preview_bap_igracias/'); ?>' + nim;
            document.getElementById('btnCetakBap').href = '<?= site_url('adminlayanan/cetak_bap_igracias/'); ?>' + nim;
            document.getElementById('bapModal').classList.remove('hidden');
        }

        function openBapFakultas(nim, nama) {
            document.getElementById('bapModalTitle').textContent = 'BAP Fakultas — ' + nama + ' (' + nim + ')';
            document.getElementById('bapModalSub').textContent = 'Dokumen 2: Berita Acara & Lembar Nilai Komprehensif FIK Telkom University (2 Halaman)';
            document.getElementById('bapIframe').src = '<?= site_url('adminlayanan/preview_bap_fakultas/'); ?>' + nim;
            document.getElementById('btnCetakBap').href = '<?= site_url('adminlayanan/cetak_bap_fakultas/'); ?>' + nim;
            document.getElementById('bapModal').classList.remove('hidden');
        }

        function closeBapModal() {
            document.getElementById('bapModal').classList.add('hidden');
            document.getElementById('bapIframe').src = '';
        }
    </script>
</body>
</html>
