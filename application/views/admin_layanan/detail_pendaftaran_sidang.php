<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Detail Pendaftaran Sidang — Admin LAA'; ?> - IFIK</title>

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
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Sidebar & Sticky Navbar -->
    <?php $this->load->view('admin_layanan/sidebar'); ?>
    <?php $this->load->view('partials/app_navbar', [
        'user_role_id'      => $this->session->userdata('role_id') ?? 5,
        'user_role_label'   => 'Admin Layanan (LAA)',
        'user_display_name' => 'Unit Layanan FIK',
        'user_display_sub'  => 'Detail Berkas Pendaftaran Sidang'
    ]); ?>

    <main class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0 pt-0.5 sm:pt-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <a href="<?= site_url('adminlayanan/pendaftaran_sidang') ?>" class="hover:text-orange-600 transition-colors">Pendaftaran Sidang</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Detail Mahasiswa</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div class="flex items-center gap-3">
                    <a href="<?= site_url('adminlayanan/pendaftaran_sidang') ?>" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-orange-600 flex items-center justify-center shadow-xs hover:shadow-md transition shrink-0">
                        <i class="bi bi-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                            <span>File Pendaftaran Sidang</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Verifikasi berkas persyaratan sidang mahasiswa: <strong><?= htmlspecialchars($detail['nama'] ?? 'Mahasiswa'); ?></strong>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <?php if ($detail['is_approved']): ?>
                        <span class="px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200 flex items-center gap-1.5 shadow-xs">
                            <i class="bi bi-check-circle-fill text-emerald-500"></i> Status: Disetujui Admin LAA
                        </span>
                    <?php else: ?>
                        <span class="px-3.5 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-xs font-bold border border-amber-200 flex items-center gap-1.5 shadow-xs">
                            <i class="bi bi-clock-fill text-amber-500"></i> Status: Pending Verifikasi
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if($this->session->flashdata('success')): ?>
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-semibold flex items-center gap-2">
            <i class="bi bi-check-circle-fill text-green-500"></i> <?= $this->session->flashdata('success') ?>
        </div>
        <?php endif; ?>
        <?php if($this->session->flashdata('error')): ?>
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-semibold flex items-center gap-2">
            <i class="bi bi-x-circle-fill text-red-500"></i> <?= $this->session->flashdata('error') ?>
        </div>
        <?php endif; ?>

        <!-- Student Profile Information Card (Matching Photo 2) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-md p-5 sm:p-6 mb-6">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3 mb-4">
                <i class="bi bi-person-badge text-orange-600 text-lg"></i>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Informasi Pendaftar Sidang</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3.5 gap-x-8 text-xs">
                <!-- Left Column -->
                <div class="space-y-2.5">
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Nama</span>
                        <span class="font-bold text-slate-800">: <?= htmlspecialchars($detail['nama']); ?></span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-36 text-slate-400 font-medium shrink-0">NIM</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-orange-600">: <?= htmlspecialchars($detail['nim']); ?></span>
                            <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold text-[10px]"><?= htmlspecialchars($detail['konsentrasi'] ?? 'DKV'); ?></span>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Prodi</span>
                        <span class="font-semibold text-slate-700">: <?= htmlspecialchars($detail['prodi']); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Konsentrasi</span>
                        <span class="font-semibold text-slate-700">: <?= htmlspecialchars($detail['konsentrasi']); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Alamat</span>
                        <span class="text-slate-600">: <?= htmlspecialchars($detail['alamat'] ?? 'Jl. Telekomunikasi No. 1, Terusan Buah Batu, Bandung'); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">No. Telp</span>
                        <span class="text-slate-600">: <?= htmlspecialchars($detail['no_hp'] ?? '087808487798'); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Tempat Lahir</span>
                        <span class="text-slate-600">: Jakarta</span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Tanggal Lahir</span>
                        <span class="text-slate-600">: 2002-04-24</span>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-2.5">
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Judul Tugas Akhir</span>
                        <span class="font-bold text-slate-800 leading-snug">: <?= htmlspecialchars($detail['judul']); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Dosen Pembimbing 1</span>
                        <span class="font-semibold text-slate-700">: <?= htmlspecialchars($detail['pembimbing_1']); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Dosen Pembimbing 2</span>
                        <span class="font-semibold text-slate-700">: <?= htmlspecialchars($detail['pembimbing_2']); ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Total SKS</span>
                        <span class="font-bold text-slate-800">: <?= $detail['totalsks'] ?? 141; ?> SKS</span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">IPK</span>
                        <span class="font-bold text-emerald-600">: <?= $detail['ipk'] ?? 3.72; ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Score EPRT</span>
                        <span class="font-bold text-indigo-600">: <?= $detail['scoreeprt'] ?? 567; ?></span>
                    </div>
                    <div class="flex items-start">
                        <span class="w-36 text-slate-400 font-medium shrink-0">Score TAK</span>
                        <span class="font-bold text-purple-600">: <?= $detail['scoretak'] ?? 146; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7 Berkas Pendaftaran Sidang Table (Matching Photo 2) -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden mb-6">
            
            <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-files text-orange-600"></i> Berkas Persyaratan Pendaftaran Sidang (7 Dokumen)
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-4 text-center w-12">#</th>
                            <th class="py-3.5 px-5">Nama File</th>
                            <th class="py-3.5 px-4">File Terlampir</th>
                            <th class="py-3.5 px-4 text-center">Lihat</th>
                            <th class="py-3.5 px-4 text-center">Action</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php foreach ($berkas as $bk): 
                            $isValid = $bk['is_valid'];
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- # -->
                                <td class="py-4 px-4 text-center font-bold text-slate-400"><?= $bk['no']; ?></td>

                                <!-- Nama File -->
                                <td class="py-4 px-5 font-bold text-slate-800">
                                    <?= htmlspecialchars($bk['nama_file']); ?>
                                </td>

                                <!-- File Terlampir -->
                                <td class="py-4 px-4 font-mono text-[11px] text-slate-500 truncate max-w-[180px]">
                                    <?= htmlspecialchars($bk['file_name']); ?>
                                </td>

                                <!-- Tombol Lihat (Preview Modal) -->
                                <td class="py-4 px-4 text-center">
                                    <button type="button" onclick="openPdfModal('<?= htmlspecialchars($bk['nama_file']); ?>', '<?= $bk['file_url']; ?>')" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold shadow-xs transition">
                                        <i class="bi bi-eye"></i> Lihat
                                    </button>
                                </td>

                                <!-- Action Buttons (Approve / Reject) -->
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" onclick="updateBerkasStatus('<?= $detail['nim']; ?>', '<?= $bk['kode']; ?>', 'Disetujui Admin LAA')"
                                                class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[11px] font-bold transition">
                                            <i class="bi bi-check-lg"></i> ACC
                                        </button>
                                        <button type="button" onclick="updateBerkasStatus('<?= $detail['nim']; ?>', '<?= $bk['kode']; ?>', 'Revisi Admin LAA')"
                                                class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[11px] font-bold transition">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </button>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-4 text-center">
                                    <?php if ($isValid): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="bi bi-check-circle-fill"></i> Disetujui Admin LAA
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="bi bi-clock-fill"></i> Pending Verifikasi
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Global Verification Action Bar -->
            <div class="p-4 sm:p-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-slate-500 font-medium">
                    Pastikan seluruh 7 berkas telah dicek dan valid sebelum menyetujui pendaftaran sidang mahasiswa.
                </p>
                <form action="<?= site_url('adminlayanan/submit_approval_sidang/' . $detail['nim']); ?>" method="POST" class="flex gap-2">
                    <button type="submit" name="status" value="Disetujui Admin LAA" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                        <i class="bi bi-check2-circle text-sm"></i> Setujui Seluruh Berkas Sidang
                    </button>
                </form>
            </div>

        </div>

    </main>

    <!-- PDF Modal Viewer -->
    <div id="pdfModal" class="fixed inset-0 z-[99999] flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closePdfModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl h-[85vh] mx-4 flex flex-col overflow-hidden z-10">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill text-rose-600 text-lg"></i>
                    <h3 class="font-bold text-slate-800 text-sm" id="pdfModalTitle">Preview Dokumen</h3>
                </div>
                <button onclick="closePdfModal()" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-slate-800 flex items-center justify-center transition">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="flex-1 bg-slate-100 p-2">
                <iframe id="pdfFrame" src="" class="w-full h-full rounded-2xl border border-slate-200" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    <script>
        function openPdfModal(title, url) {
            document.getElementById('pdfModalTitle').textContent = title;
            document.getElementById('pdfFrame').src = url;
            document.getElementById('pdfModal').classList.remove('hidden');
        }

        function closePdfModal() {
            document.getElementById('pdfModal').classList.add('hidden');
            document.getElementById('pdfFrame').src = '';
        }

        function updateBerkasStatus(nim, kodeBerkas, status) {
            fetch('<?= site_url('adminlayanan/ajax_update_berkas_sidang'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'nim=' + encodeURIComponent(nim) + '&kode_berkas=' + encodeURIComponent(kodeBerkas) + '&status=' + encodeURIComponent(status)
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.message || 'Gagal mengubah status berkas.');
                }
            })
            .catch(() => alert('Terjadi kesalahan jaringan.'));
        }
    </script>
</body>
</html>
