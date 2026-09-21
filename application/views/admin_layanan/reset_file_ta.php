<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Reset File TA'; ?> - IFIK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { brand: { 50:'#fff7ed',100:'#ffedd5',500:'#f97316',600:'#ea580c',700:'#c2410c',900:'#7c2d12' } } } } }</script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body,button,input,textarea,select{font-family:'Plus Jakarta Sans',-apple-system,sans-serif!important;}</style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <?php $this->load->view('admin_layanan/sidebar'); ?>
    <?php $this->load->view('partials/app_navbar', [
        'user_role_id'      => $this->session->userdata('role_id') ?? 5,
        'user_role_label'   => 'Admin Layanan (LAA)',
        'user_display_name' => 'Unit Layanan FIK',
        'user_display_sub'  => 'Reset File TA Mahasiswa'
    ]); ?>

    <main class="min-h-screen p-4 sm:p-6 lg:p-10 max-w-7xl mx-auto">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-semibold text-slate-400 mb-2 pl-11 sm:pl-0">
                <a href="<?= site_url('adminlayanan') ?>" class="hover:text-orange-600 transition-colors">Portal LAA</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Reset File TA</span>
            </div>
            <div class="flex items-center gap-3 pl-11 sm:pl-0">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shadow-sm shrink-0">
                    <i class="bi bi-arrow-counterclockwise text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight leading-none">Reset File TA</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Hapus file yang sudah diupload mahasiswa agar bisa upload ulang</p>
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

        <!-- Search Card -->
        <div class="bg-white/80 backdrop-blur-sm border border-slate-200/80 rounded-2xl shadow-sm p-5 sm:p-6 mb-6">
            <h2 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                <i class="bi bi-search text-orange-500"></i> Cari Mahasiswa
            </h2>
            <form method="GET" action="<?= site_url('adminlayanan/reset_file_ta') ?>" class="flex gap-2">
                <input type="text" name="nim" value="<?= htmlspecialchars($nim ?? '') ?>"
                    placeholder="Masukkan NIM mahasiswa..."
                    class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-400 transition">
                <button type="submit" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <i class="bi bi-search"></i> Cari
                </button>
            </form>
        </div>

        <?php if (!empty($nim) && !$detail): ?>
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5 text-center">
            <i class="bi bi-person-x text-red-400 text-3xl mb-2"></i>
            <p class="text-red-700 font-semibold">Mahasiswa dengan NIM <strong><?= htmlspecialchars($nim) ?></strong> tidak ditemukan dalam data pendaftaran TA.</p>
        </div>
        <?php endif; ?>

        <?php if ($detail): ?>
        <!-- Student Info Card -->
        <div class="bg-white/80 backdrop-blur-sm border border-slate-200/80 rounded-2xl shadow-sm p-5 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center text-xl font-extrabold shadow-md shrink-0">
                    <?= strtoupper(substr($detail['nama_depan'] ?? 'M', 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-slate-900">
                        <?= htmlspecialchars(($detail['nama_depan'] ?? '') . ' ' . ($detail['nama_belakang'] ?? '')) ?>
                    </h3>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-xs text-slate-500 font-medium">
                        <span><i class="bi bi-person-badge mr-1"></i><?= htmlspecialchars($detail['nim'] ?? '') ?></span>
                        <span><i class="bi bi-mortarboard mr-1"></i><?= htmlspecialchars($detail['prodi'] ?? '-') ?></span>
                        <span><i class="bi bi-diagram-3 mr-1"></i><?= htmlspecialchars($detail['kode_kk'] ?? '-') ?></span>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <?php
                        $status_admin = $detail['status_approval_admin'] ?? 'Pending';
                        $badge_class = match($status_admin) {
                            'Approved'  => 'bg-green-100 text-green-700 border-green-200',
                            'Rejected'  => 'bg-red-100 text-red-700 border-red-200',
                            default     => 'bg-amber-100 text-amber-700 border-amber-200'
                        };
                        ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border <?= $badge_class ?>">
                            Status LAA: <?= $status_admin ?>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border bg-slate-100 text-slate-600 border-slate-200">
                            Tahap: <?= htmlspecialchars($detail['current_stage'] ?? '-') ?>
                        </span>
                    </div>
                </div>
                <!-- Reset All Button -->
                <button onclick="confirmResetAll('<?= htmlspecialchars($detail['nim']) ?>', '<?= htmlspecialchars(($detail['nama_depan'] ?? '') . ' ' . ($detail['nama_belakang'] ?? '')) ?>')"
                    class="shrink-0 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-bold transition flex items-center gap-2 shadow-sm">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Semua File
                </button>
            </div>
        </div>

        <!-- File List -->
        <?php if (!empty($berkas)): ?>
        <div class="bg-white/80 backdrop-blur-sm border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <i class="bi bi-files text-orange-500"></i>
                    File Terupload (<?= count($berkas) ?> file)
                </h2>
            </div>
            <div class="divide-y divide-slate-100">
                <?php foreach ($berkas as $kode => $bk): ?>
                <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50/60 transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">
                                <?= htmlspecialchars($bk['nama_berkas'] ?? strtoupper($kode)) ?>
                            </p>
                            <p class="text-[11px] text-slate-400 font-medium truncate">
                                <?= htmlspecialchars($bk['file_name'] ?? '-') ?>
                                <?php
                                $st = $bk['status'] ?? 'Pending';
                                $sc = match($st) {
                                    'Valid'   => 'text-green-600',
                                    'Invalid' => 'text-red-600',
                                    default   => 'text-amber-600'
                                };
                                ?>
                                · <span class="font-bold <?= $sc ?>"><?= $st ?></span>
                            </p>
                        </div>
                    </div>
                    <button onclick="confirmResetOne('<?= htmlspecialchars($detail['nim']) ?>', '<?= $kode ?>', '<?= htmlspecialchars($bk['nama_berkas'] ?? strtoupper($kode)) ?>')"
                        class="shrink-0 ml-3 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="bi bi-trash3"></i> Reset
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center">
            <i class="bi bi-folder-x text-slate-300 text-4xl mb-2"></i>
            <p class="text-slate-500 font-semibold text-sm">Mahasiswa ini belum mengupload file apapun.</p>
        </div>
        <?php endif; ?>
        <?php endif; ?>

    </main>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="fixed inset-0 z-[99999] flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4 z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                    <i class="bi bi-exclamation-triangle-fill text-lg"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base" id="modalTitle">Konfirmasi Reset</h3>
                    <p class="text-xs text-slate-500" id="modalSubtitle">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            <p class="text-sm text-slate-600 mb-5" id="modalBody">Apakah kamu yakin?</p>
            <div class="flex gap-2">
                <button onclick="closeModal()" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                    Batal
                </button>
                <button id="modalConfirmBtn" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-bold transition flex items-center justify-center gap-2">
                    <i class="bi bi-arrow-counterclockwise"></i> Ya, Reset
                </button>
            </div>
        </div>
    </div>

    <script>
    let _pendingNim = '', _pendingKode = '';
    const AJAX_URL = '<?= site_url('adminlayanan/ajax_reset_file_ta') ?>';

    function confirmResetAll(nim, nama) {
        _pendingNim = nim;
        _pendingKode = '';
        document.getElementById('modalTitle').textContent = 'Reset Semua File TA';
        document.getElementById('modalSubtitle').textContent = 'Semua file akan dihapus';
        document.getElementById('modalBody').innerHTML =
            'Semua file TA milik <strong>' + nama + '</strong> (NIM: ' + nim + ') akan direset. Mahasiswa harus upload ulang dari awal.';
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    function confirmResetOne(nim, kode, nama_berkas) {
        _pendingNim = nim;
        _pendingKode = kode;
        document.getElementById('modalTitle').textContent = 'Reset File: ' + nama_berkas;
        document.getElementById('modalSubtitle').textContent = 'File ini akan dihapus dari sistem';
        document.getElementById('modalBody').innerHTML =
            'File <strong>' + nama_berkas + '</strong> milik NIM <strong>' + nim + '</strong> akan direset. Mahasiswa bisa upload ulang file ini saja.';
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('confirmModal').classList.add('hidden');
        _pendingNim = ''; _pendingKode = '';
    }

    document.getElementById('modalConfirmBtn').addEventListener('click', function() {
        if (!_pendingNim) return;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';

        fetch(AJAX_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'nim=' + encodeURIComponent(_pendingNim) + '&kode_berkas=' + encodeURIComponent(_pendingKode)
        })
        .then(r => r.json())
        .then(data => {
            closeModal();
            if (data.success) {
                // Reload halaman dengan NIM yang sama
                window.location.href = '<?= site_url('adminlayanan/reset_file_ta') ?>?nim=' + encodeURIComponent(_pendingNim) + '&msg=success';
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
            }
        })
        .catch(() => {
            closeModal();
            alert('Terjadi kesalahan jaringan.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> Ya, Reset';
        });
    });

    // Show success banner from redirect
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('msg') === 'success') {
        const banner = document.createElement('div');
        banner.className = 'fixed top-4 right-4 z-[999999] bg-green-500 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-bold flex items-center gap-2';
        banner.innerHTML = '<i class="bi bi-check-circle-fill"></i> File berhasil direset!';
        document.body.appendChild(banner);
        setTimeout(() => banner.remove(), 3500);
    }
    </script>
</body>
</html>
