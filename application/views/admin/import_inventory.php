<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'Import Inventory') ?> — IFIK</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #ea580c;
            --primary-hover: #c2410c;
            --bg-color: #fcfbf9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            padding: 40px 60px;
            min-height: 100vh;
            transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1), width 0.75s cubic-bezier(0.76, 0, 0.24, 1);
            box-sizing: border-box;
        }

        @media (min-width: 992px) {
            .page-wrapper-for-sidebar {
                margin-left: 270px;
                width: calc(100% - 270px);
            }

            body.curved-sidebar-desktop-collapsed .page-wrapper-for-sidebar {
                margin-left: 0;
                width: 100%;
            }
        }

        @media (max-width: 991.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 30px 20px;
                padding-top: 56px;
            }
        }

        .btn-back-header {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.04);
        }

        .btn-back-header:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-3px);
        }

        .page-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .panel-card { background: #fff; border: 1px solid var(--border-color); border-radius: 16px; box-shadow: 0 4px 16px rgba(15,23,42,.04); }
        .btn-fik { background: var(--primary); color: #fff; border: 0; }
        .btn-fik:hover { background: var(--primary-hover); color: #fff; }
        textarea { font-family: Consolas, monospace; }
        .format-notice { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 14px; color: #7c2d12; }
        .template-box { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 14px; }
        .preview-summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem; }
        .summary-item { border: 1px solid var(--border-color); border-radius: 12px; padding: .8rem 1rem; background: #fff; }
        .summary-item__value { display: block; font-size: 1.35rem; font-weight: 800; line-height: 1.1; }
        .summary-item__label { color: var(--text-muted); font-size: .78rem; font-weight: 600; }
        .preview-table-wrap { max-height: min(62vh, 650px); border: 1px solid var(--border-color); border-radius: 12px; overflow: auto; }
        .preview-table { min-width: 1120px; margin-bottom: 0; }
        .preview-table thead th { position: sticky; top: 0; z-index: 2; padding: .85rem .75rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: .75rem; letter-spacing: .045em; text-transform: uppercase; white-space: nowrap; font-weight: 700; }
        .preview-table tbody td { padding: .8rem .75rem; border-color: #f1f5f9; vertical-align: top; font-size: .85rem; }
        .preview-table tbody tr:hover { background: #fffaf7; }
        @media (max-width: 767.98px) {
            .preview-summary { grid-template-columns: 1fr; }
            .preview-table-wrap { max-height: none; }
        }
    </style>
</head>
<body>
    <?php include APPPATH . 'views/admin/panel_sidebar.php'; ?>

    <main class="page-wrapper-for-sidebar">
        <!-- Top Header Navigation -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= site_url('admin/barang') ?>" class="btn-back-header">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Barang
                </a>
                <div>
                    <h1 class="page-title mb-1">Import Data Inventory</h1>
                    <p class="text-muted small mb-0">Impor data banyak barang sekaligus dari file CSV atau tempel tabel Excel</p>
                </div>
            </div>
        </div>

        <?php if($this->session->flashdata('success')): ?><div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"><?= html_escape($this->session->flashdata('success')) ?></div><?php endif; ?>
        <?php if($this->session->flashdata('error')): ?><div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4"><?= html_escape($this->session->flashdata('error')) ?></div><?php endif; ?>

        <?php
            $preview_total = count((array) $preview_rows);
            $duplicate_total = 0;
            foreach ((array) $preview_rows as $preview_row) {
                if (!empty($preview_row['duplicate_id'])) $duplicate_total++;
            }
            $new_total = max(0, $preview_total - $duplicate_total);
        ?>

        <div class="row g-4 align-items-start mb-5">
            <div class="col-lg-4">
                <form class="panel-card p-4" method="post" enctype="multipart/form-data" action="<?= site_url('admin/barang/preview_import') ?>">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1">Sumber Data</h2>
                            <p class="small text-muted mb-0">Unggah file atau tempel dari Excel.</p>
                        </div>
                        <i class="bi bi-filetype-csv fs-2 text-success"></i>
                    </div>

                    <div class="format-notice p-3 mb-3">
                        <div class="fw-bold small mb-1"><i class="bi bi-exclamation-circle me-1"></i> File wajib berformat CSV</div>
                        <div class="small">Template CSV dapat dibuka dan diisi menggunakan Microsoft Excel.</div>
                    </div>

                    <div class="template-box p-3 mb-3">
                        <div class="fw-semibold small mb-1">Mulai dari template kosong</div>
                        <div class="small text-muted mb-2">Header kolom sudah disesuaikan secara otomatis.</div>
                        <a href="<?= base_url('assets/templates/template_import_inventory.csv') ?>" download="template_import_inventory.csv" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
                            <i class="bi bi-download me-1"></i> Download Template CSV
                        </a>
                    </div>

                    <label class="form-label fw-bold small text-dark" for="inventoryCsv">Pilih File CSV</label>
                    <input id="inventoryCsv" type="file" name="file_import" class="form-control rounded-3 mb-2" accept=".csv,text/csv">
                    <div class="small text-muted mb-3" style="font-size: 0.75rem;"><strong>Urutan kolom:</strong> kode_aset, nama_aset, ruangan, jumlah_total, jumlah_tersedia, kondisi, deskripsi.</div>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="border-top flex-grow-1"></span><span class="small text-muted">atau</span><span class="border-top flex-grow-1"></span>
                    </div>
                    <label class="form-label fw-bold small text-dark" for="pasteData">Copy-paste dari Excel</label>
                    <textarea id="pasteData" name="paste_data" class="form-control rounded-3" rows="6" placeholder="Tempel tabel dari Excel di sini, termasuk baris header..."></textarea>
                    <button class="btn btn-fik rounded-pill px-4 mt-3 w-100 fw-bold py-2"><i class="bi bi-eye me-1"></i> Preview Data</button>
                </form>
            </div>
            <div class="col-lg-8">
                <section class="panel-card p-4">
                    <div class="d-flex flex-column gap-3 mb-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1">Preview Import</h2>
                            <p class="small text-muted mb-0">Periksa isi, lokasi, stok, dan status duplikat sebelum diproses.</p>
                        </div>
                        <?php if(!empty($preview_rows)): ?>
                            <form method="post" action="<?= site_url('admin/barang/proses_import') ?>" class="import-actions d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                                <label class="small fw-semibold text-muted mb-0" for="duplicateAction">Jika duplikat:</label>
                                <select id="duplicateAction" name="duplicate_action" class="form-select form-select-sm rounded-pill flex-grow-1">
                                    <option value="skip">Lewati data duplikat</option>
                                    <option value="update">Perbarui data lama</option>
                                    <option value="cancel">Batalkan import</option>
                                </select>
                                <button class="btn btn-success btn-sm rounded-pill px-4 fw-bold" onclick="return confirm('Proses data preview ke inventory?')"><i class="bi bi-check2-circle me-1"></i> Proses Import</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php if(!empty($preview_rows)): ?>
                        <div class="preview-summary mb-3">
                            <div class="summary-item"><span class="summary-item__value"><?= $preview_total ?></span><span class="summary-item__label">Total Baris</span></div>
                            <div class="summary-item"><span class="summary-item__value text-success"><?= $new_total ?></span><span class="summary-item__label">Data Baru</span></div>
                            <div class="summary-item"><span class="summary-item__value text-warning"><?= $duplicate_total ?></span><span class="summary-item__label">Data Duplikat</span></div>
                        </div>
                    <?php endif; ?>

                    <div class="preview-table-wrap">
                        <table class="table preview-table align-middle">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Kode Aset</th>
                                    <th>Nama Barang</th>
                                    <th>Ruangan</th>
                                    <th>Total</th>
                                    <th>Tersedia</th>
                                    <th>Kondisi</th>
                                    <th>Deskripsi</th>
                                    <th>Status Data</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(empty($preview_rows)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="bi bi-table d-block fs-1 mb-2 text-secondary opacity-50"></i>
                                        <span class="d-block fw-semibold text-dark">Belum ada data untuk ditampilkan</span>
                                        <span class="small">Unggah file CSV atau tempel tabel, lalu klik Preview Data.</span>
                                    </td>
                                </tr>
                            <?php else: foreach($preview_rows as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td class="font-monospace"><?= html_escape($row['kode_aset'] ?: 'Auto') ?></td>
                                    <td class="fw-semibold text-dark"><?= html_escape($row['nama_aset']) ?></td>
                                    <td><?= html_escape($row['ruangan_label'] ?: 'Ruangan default') ?></td>
                                    <td class="fw-semibold text-primary"><?= (int) $row['jumlah_total'] ?></td>
                                    <td class="text-success"><?= (int) $row['jumlah_tersedia'] ?></td>
                                    <td><span class="badge rounded-pill <?= $row['kondisi'] === 'Baik' ? 'bg-success' : ($row['kondisi'] === 'Rusak' ? 'bg-warning text-dark' : 'bg-danger') ?>"><?= html_escape($row['kondisi']) ?></span></td>
                                    <td><?= html_escape($row['deskripsi'] ?: '-') ?></td>
                                    <td><?php if (!empty($row['duplicate_id'])): ?><span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i>Duplikat: <?= html_escape($row['duplicate_label'] ?? 'data lama') ?></span><?php else: ?><span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Data baru</span><?php endif; ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
