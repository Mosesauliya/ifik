<?php
$pengajuan = is_array($pengajuan ?? null) ? $pengajuan : [];
$notif_items = isset($notifikasi) && is_array($notifikasi) ? $notifikasi : [];
$notif_count = (int) ($unread_notifikasi ?? 0);
$approval_total = (int) ($approval_total ?? count($pengajuan));
$approval_actionable = (int) ($approval_actionable ?? count(array_filter($pengajuan, static function ($p) { return scm_loan_can_act($p, 'wadek'); })));
$page = max(1, (int) ($page ?? 1));
$per_page = (int) ($per_page ?? 10);
$total_pages = max(1, (int) ($total_pages ?? 1));
$display_nama = $this->session->userdata('nama') ?: $this->session->userdata('username') ?: 'Wakil Dekan';
$user_role_label = 'Wakil Dekan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'Approval Peminjaman Luar Kampus - Wadek') ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/loan-progress.css'); ?>?v=<?= @filemtime(FCPATH . 'assets/css/loan-progress.css'); ?>">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }

        .navbar-custom { background-color: #ffffff; padding: 12px 0; border-bottom: 2px solid #7c3aed; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); }
        .btn-wadek { background: linear-gradient(45deg, #6d28d9, #7c3aed); color: white; font-weight: 600; border: none; border-radius: 8px; padding: 8px 20px; }
        .table-custom { border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); width: 100%; }
        .table-custom thead th { background-color: #4c1d95; color: white; font-weight: 600; border: none; padding: 15px; font-size: 0.85rem; }
        .table-custom tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #eee; background: white; font-size: 0.85rem; }
        .table-custom tbody tr:hover td { background-color: #faf5ff; }

        .stat-card {
            border: none;
            border-radius: 16px;
            background: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border-left: 5px solid #7c3aed;
            transition: all 0.25s ease;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(124, 58, 237, 0.12); }
    </style>
    <?php include APPPATH . 'views/shared/theme_assets.php'; ?>
</head>
<body>

<div id="laaMainContentWrapper">
    <!-- Header -->
    <header class="glass-header-ifik mb-4" style="border-bottom: 3px solid #7c3aed;">
        <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 46px; height: 46px; background: rgba(124, 58, 237, 0.12); color: #7c3aed; font-size: 1.4rem;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h1 class="h5 fw-bold text-dark mb-0 tracking-tight">Approval Peminjaman Luar Kampus</h1>
                        <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(124, 58, 237, 0.12); color: #7c3aed; font-weight: 700; font-size: 11px;">Wakil Dekan (Wadek)</span>
                    </div>
                    <p class="text-muted small mb-0 d-none d-sm-block" style="font-size: 12px;">Validasi dan persetujuan resmi izin peminjaman alat &amp; fasilitas fakultas untuk kegiatan di luar kampus.</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 ms-auto">
                <a href="<?= site_url('dashboard'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-house me-1"></i> Dashboard Utama
                </a>
            </div>
        </div>
    </header>

    <div class="container py-4">
        <!-- Notifikasi Flash -->
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center rounded-3 mb-4" data-aos="zoom-in">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div><?= $this->session->flashdata('success'); ?></div>
            </div>
        <?php endif; ?>
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center rounded-3 mb-4" data-aos="zoom-in">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                <div><?= $this->session->flashdata('error'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4" data-aos="fade-up">
            <div class="col-md-6 col-lg-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Menunggu ACC Wadek</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1"><?= number_format($approval_actionable, 0, ',', '.') ?></h3>
                        </div>
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #f5f3ff; color: #7c3aed;">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="stat-card" style="border-left-color: #0284c7;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Total Pengajuan Luar Kampus</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1"><?= number_format($approval_total, 0, ',', '.') ?></h3>
                        </div>
                        <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #e0f2fe; color: #0284c7;">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Pengajuan -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" data-aos="fade-up">
            <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-list-check text-primary"></i> Daftar Pengajuan Peminjaman Eksternal
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Waktu &amp; Peminjam</th>
                            <th>Barang &amp; Jumlah</th>
                            <th>Keperluan / Lokasi</th>
                            <th style="width: 180px;">Jadwal Pinjam</th>
                            <th style="width: 280px;">Alur &amp; Progres</th>
                            <th style="width: 150px;" class="text-center">Aksi Wadek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pengajuan)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inboxes display-4 text-muted mb-3 d-block"></i>
                                    Tidak ada pengajuan peminjaman luar kampus yang ditemukan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pengajuan as $p): 
                                $can_act = scm_loan_can_act($p, 'wadek');
                                $items = (array) ($p->detail_barang ?? []);
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= html_escape($p->nama_peminjam ?? 'Mahasiswa') ?></div>
                                    <div class="text-muted small font-monospace"><?= html_escape($p->nim_nip ?? '-') ?></div>
                                    <div class="badge bg-light text-secondary border mt-1" style="font-size: 10.5px;"><?= html_escape($p->prodi ?? '-') ?></div>
                                </td>
                                <td>
                                    <?php if (!empty($items)): ?>
                                        <?php foreach ($items as $it): ?>
                                            <div class="fw-semibold text-dark"><?= html_escape($it->nama_aset ?? 'Aset') ?></div>
                                            <div class="text-muted small">Kode: <?= html_escape($it->kode_aset ?? '-') ?> &bull; Jml: <strong class="text-fik-orange"><?= (int) ($it->jumlah_pinjam ?? 1) ?></strong> unit</div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="fw-semibold text-dark"><?= html_escape($p->nama_aset ?? 'Aset') ?></div>
                                        <div class="text-muted small">Jml: <strong><?= (int) ($p->jumlah_pinjam ?? 1) ?></strong> unit</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small text-dark fw-medium" style="max-width: 250px;"><?= html_escape($p->keperluan ?? '-') ?></div>
                                    <span class="badge bg-purple-subtle text-primary border border-primary-subtle mt-1" style="font-size: 10px;">🚀 Luar Kampus</span>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark"><?= html_escape(date('d M Y', strtotime($p->tanggal_pinjam))) ?></div>
                                    <div class="text-muted small">s.d. <?= html_escape(date('d M Y', strtotime($p->tanggal_kembali_rencana))) ?></div>
                                </td>
                                <td>
                                    <?php $loan_progress_item = $p; $loan_progress_compact = true; include APPPATH . 'views/shared/loan_progress.php'; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($can_act): ?>
                                        <div class="d-flex flex-column gap-2">
                                            <a href="<?= site_url('wadek/peminjaman/setujui/' . $p->id_peminjaman); ?>" class="btn btn-sm btn-success fw-bold w-100" onclick="return confirm('Apakah Anda yakin ingin menyetujui peminjaman luar kampus ini?')">
                                                <i class="bi bi-check-circle me-1"></i> Setujui
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold w-100" data-bs-toggle="modal" data-bs-target="#modalTolak<?= $p->id_peminjaman ?>">
                                                <i class="bi bi-x-circle me-1"></i> Tolak
                                            </button>
                                        </div>

                                        <!-- Modal Tolak -->
                                        <div class="modal fade text-start" id="modalTolak<?= $p->id_peminjaman ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <form action="<?= site_url('wadek/peminjaman/tolak/' . $p->id_peminjaman); ?>" method="POST">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h6 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Tolak Peminjaman Luar Kampus</h6>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <p class="small text-muted mb-3">Berikan alasan penolakan peminjaman alat luar kampus untuk <strong><?= html_escape($p->nama_peminjam); ?></strong>:</p>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                                                                <textarea name="catatan_wadek" class="form-control" rows="3" required placeholder="Contoh: Izin kegiatan di luar kampus belum lengkap atau risiko keamanan alat..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger btn-sm fw-bold">Konfirmasi Tolak</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">
                                            <?= html_escape($p->status) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="<?= base_url('assets/js/loan-progress.js'); ?>?v=<?= @filemtime(FCPATH . 'assets/js/loan-progress.js'); ?>"></script>
<script>
    AOS.init({ once: true, offset: 20 });
</script>
</body>
</html>
