<?php
$master_filters = isset($filters) && is_array($filters) ? $filters : ['criteria' => []];
$master_pagination = isset($pagination) && is_array($pagination) ? $pagination : ['page' => 1, 'per_page' => 10, 'total' => count($barang ?? []), 'total_pages' => 1];
$master_page = (int) ($master_pagination['page'] ?? 1);
$master_total_pages = (int) ($master_pagination['total_pages'] ?? 1);
$master_compact_pages = static function ($current, $last) {
    $current = max(1, min((int) $current, max(1, (int) $last)));
    $last = max(1, (int) $last);
    if ($last <= 7) return range(1, $last);
    if ($current <= 3) return array_merge(range(1, 5), ['ellipsis-after', $last]);
    if ($current >= $last - 2) return array_merge([1, 'ellipsis-before'], range($last - 4, $last));
    return array_merge([1, 'ellipsis-before'], range($current - 2, $current + 2), ['ellipsis-after', $last]);
};
$master_base_query = ['filter_field' => array_column($master_filters['criteria'] ?? [], 'field'), 'filter_value' => array_column($master_filters['criteria'] ?? [], 'value'), 'per_page' => $master_pagination['per_page'] ?? 10];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Master Data Barang — IFIK</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
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

        /* Responsive Page Wrapper with Sidebar Interaction */
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

        .text-fik-orange { color: #ea580c; }
        .btn-fik-orange { background-color: #ea580c; color: white; border: none; }
        .btn-fik-orange:hover { background-color: #c2410c; color: white; }

        .btn-back-dashboard {
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

        .btn-back-dashboard:hover {
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

        .master-pagination-footer {
            display: grid;
            grid-template-columns: minmax(0, auto) 1fr minmax(0, auto);
            align-items: center;
            gap: 1rem;
            min-height: 64px;
            padding: .75rem 1rem;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            background: #f9fafb;
        }
        .master-pagination-summary { display: flex; align-items: center; flex-wrap: wrap; gap: .55rem; font-size: .8rem; }
        .master-pagination-summary .form-select { width: 92px; min-height: 34px; font-size: .8rem; }
        .master-pagination-status { text-align: center; font-size: .8rem; }
        .master-pagination { margin: 0; }
        .master-pagination .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            min-height: 34px;
            padding: .35rem .58rem;
            border-color: #e5e7eb;
            color: #1f2937;
            background: #fff;
            font-size: .8rem;
            line-height: 1;
            transition: all .16s ease;
        }
        .master-pagination .page-link:hover { color: #ea580c; background: #fff7ed; }
        .master-pagination .page-item.active .page-link { color: #fff; background: #ea580c; border-color: #ea580c; }
        .master-pagination .page-item.disabled .page-link { color: #9ca3af; background: #f9fafb; }
        @media (max-width: 767.98px) {
            .master-pagination-footer { grid-template-columns: 1fr; justify-items: center; gap: .65rem; }
            .master-pagination-footer nav { max-width: 100%; overflow-x: auto; padding-bottom: 2px; }
        }

        .img-thumbnail-table { width: 56px; height: 56px; object-fit: cover; border-radius: 10px; border: 1px solid #e2e8f0; }
        .model-thumbnail-table { width: 56px; height: 56px; display: block; margin: auto; border-radius: 10px; border: 1px solid #e2e8f0; background: #eef1f5; }
        .img-placeholder { width: 56px; height: 56px; background-color: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 1.4rem; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <?php include APPPATH . 'views/admin/panel_sidebar.php'; ?>

    <main class="page-wrapper-for-sidebar">
        <!-- Top Header Navigation & Title -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= site_url('dashboard') ?>" class="btn-back-dashboard">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <div>
                    <h1 class="page-title mb-1">Kelola Master Data Barang</h1>
                    <p class="text-muted small mb-0">Daftar lengkap inventaris alat laboratorium, stok fisik, dan lokasi penyimpanan</p>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="<?= site_url('admin/barang/import') ?>" class="btn btn-outline-success fw-bold px-4 py-2 rounded-pill shadow-sm bg-white d-inline-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Import Inventory
                </a>
                <a href="<?= site_url('admin/barang/tambah') ?>" class="btn btn-fik-orange fw-bold px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle"></i> Tambah Barang Baru
                </a>
            </div>
        </div>

        <?php
        $multi_filter_id = 'masterMultiFilter';
        $multi_filter_mode = 'server';
        $multi_filter_action = site_url('admin/barang');
        $multi_filter_rows = $master_filters['criteria'] ?? [];
        $multi_filter_hidden = ['per_page' => (int) ($master_pagination['per_page'] ?? 10), 'page' => 1];
        $multi_filter_fields = [
            'kode' => ['label' => 'Kode aset', 'placeholder' => 'Cari kode aset'],
            'nama' => ['label' => 'Nama barang', 'placeholder' => 'Cari nama barang'],
            'ruangan' => ['label' => 'Lokasi / Lab', 'placeholder' => 'Cari ruangan atau laboratorium'],
            'total' => ['label' => 'Total fisik', 'placeholder' => 'Cari jumlah unit', 'type' => 'number'],
            'kondisi' => ['label' => 'Kondisi', 'placeholder' => 'Cari kondisi barang'],
        ];
        include APPPATH . 'views/admin/_multi_filter.php';
        ?>

        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div class="fw-semibold"><?= $this->session->flashdata('success'); ?></div>
            </div>
        <?php endif; ?>
        <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <div class="fw-semibold"><?= $this->session->flashdata('error'); ?></div>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="p-3 text-center text-muted small fw-bold">NO</th>
                                <th class="p-3 text-center text-muted small fw-bold">GAMBAR</th>
                                <th class="text-muted small fw-bold">KODE ASET</th>
                                <th class="text-muted small fw-bold">NAMA BARANG</th>
                                <th class="text-muted small fw-bold">LOKASI / LAB</th>
                                <th class="text-muted small fw-bold">TOTAL FISIK</th>
                                <th class="text-muted small fw-bold">RESERVED</th>
                                <th class="text-muted small fw-bold">DIPINJAM</th>
                                <th class="text-muted small fw-bold">TERSEDIA</th>
                                <th class="text-muted small fw-bold">KONDISI</th>
                                <th class="text-center text-muted small fw-bold">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($barang)): ?>
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    Belum ada data barang di Master Data.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($barang as $loop_index => $b): ?>
                                <tr>
                                    <td class="p-3 text-center fw-semibold text-muted"><?= (($master_page - 1) * max(1, (int) ($master_pagination['per_page'] ?? 10))) + $loop_index + 1 ?></td>
                                    <td class="p-3 text-center">
                                        <?php if(!empty($b->gambar) && file_exists('./assets/uploads/barang/'.$b->gambar)): ?>
                                            <?php $is_3d_asset = in_array(strtolower(pathinfo($b->gambar, PATHINFO_EXTENSION)), ['glb', 'gltf'], true); ?>
                                            <?php if ($is_3d_asset): ?>
                                                <model-viewer src="<?= base_url('assets/uploads/barang/'.rawurlencode($b->gambar)) ?>" alt="Model 3D <?= html_escape($b->nama_aset) ?>" class="model-thumbnail-table" camera-controls disable-pan disable-zoom interaction-prompt="none" touch-action="pan-y" shadow-intensity="0.45" loading="lazy"></model-viewer>
                                            <?php else: ?>
                                                <img src="<?= base_url('assets/uploads/barang/'.rawurlencode($b->gambar)) ?>" alt="<?= html_escape($b->nama_aset) ?>" class="img-thumbnail-table" loading="lazy" decoding="async">
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="img-placeholder mx-auto" title="Tidak ada gambar">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark border font-monospace px-2 py-1"><?= $b->kode_aset ?></span></td>
                                    <td class="fw-semibold text-dark"><?= $b->nama_aset ?></td>
                                    <td class="text-muted small"><i class="bi bi-geo-alt-fill text-fik-orange me-1"></i><?= $b->nama_ruangan ?></td>
                                    <td><b class="text-primary"><?= $b->jumlah_total ?></b> Unit</td>
                                    <td><b class="text-warning"><?= (int) ($b->jumlah_reserved ?? 0) ?></b> Unit</td>
                                    <td><b class="text-danger"><?= (int) ($b->jumlah_dipinjam ?? 0) ?></b> Unit</td>
                                    <td><b class="text-success"><?= (int) $b->jumlah_tersedia ?></b> Unit</td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-1 <?= ($b->kondisi == 'Baik') ? 'bg-success-subtle text-success border border-success-subtle' : (($b->kondisi == 'Rusak') ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle') ?>">
                                            <?= $b->kondisi ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('admin/barang/edit/'.$b->id_aset) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a href="<?= site_url('admin/barang/hapus/'.$b->id_aset) ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('PERINGATAN!\n\nMenghapus master data ini akan menghilangkan barang dari halaman peminjaman secara permanen. Lanjutkan?');">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="master-pagination-footer">
                <div class="master-pagination-summary">
                    <label for="masterPerPage">Tampilkan:</label>
                    <select id="masterPerPage" class="form-select form-select-sm rounded-3" aria-label="Jumlah data aset per halaman">
                        <?php foreach (($per_page_options ?? [10, 25, 50, 100]) as $option): ?><option value="<?= (int) $option ?>" <?= (int) $option === (int) ($master_pagination['per_page'] ?? 10) ? 'selected' : '' ?>><?= (int) $option ?></option><?php endforeach; ?>
                    </select>
                    <span>Total item: <b><?= (int) ($master_pagination['total'] ?? 0) ?></b></span>
                </div>
                <div class="master-pagination-status">Halaman: <b><?= $master_page ?></b> dari <b><?= $master_total_pages ?></b></div>
                <nav aria-label="Pagination master data">
                    <ul class="pagination pagination-sm master-pagination">
                        <?php $master_prev = http_build_query(array_merge($master_base_query, ['page' => max(1, $master_page - 1)])); ?>
                        <li class="page-item <?= $master_page <= 1 ? 'disabled' : '' ?>"><a class="page-link rounded-start-3" href="<?= site_url('admin/barang?' . $master_prev) ?>">Previous</a></li>
                        <?php foreach ($master_compact_pages($master_page, $master_total_pages) as $page_index): ?>
                            <?php if (is_string($page_index)): ?>
                                <li class="page-item disabled" aria-hidden="true"><span class="page-link">...</span></li>
                            <?php else: $master_query = http_build_query(array_merge($master_base_query, ['page' => $page_index])); ?>
                                <li class="page-item <?= $master_page === $page_index ? 'active' : '' ?>"><a class="page-link" href="<?= site_url('admin/barang?' . $master_query) ?>"><?= $page_index ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php $master_next = http_build_query(array_merge($master_base_query, ['page' => min($master_total_pages, $master_page + 1)])); ?>
                        <li class="page-item <?= $master_page >= $master_total_pages ? 'disabled' : '' ?>"><a class="page-link rounded-end-3" href="<?= site_url('admin/barang?' . $master_next) ?>">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const masterPerPage = document.getElementById('masterPerPage');
        masterPerPage?.addEventListener('change', () => {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', masterPerPage.value);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        });
    </script>
</body>
</html>
