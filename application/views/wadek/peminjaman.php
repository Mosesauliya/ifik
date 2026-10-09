<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Approval Peminjaman Luar Kampus - Wakil Dekan (Wadek)
 * Layout & UI Architecture strictly aligned with Kaprodi Approval module.
 * 
 * @var array $pengajuan
 * @var array $filters
 * @var array $filter_rows
 * @var int $approval_total
 * @var int $approval_actionable
 * @var int $page
 * @var int $per_page
 * @var int $total_pages
 * @var array $notifikasi
 * @var int $unread_notifikasi
 */

$pengajuan = is_array($pengajuan ?? null) ? $pengajuan : [];
$notif_items = isset($notifikasi) && is_array($notifikasi) ? $notifikasi : [];
$notif_count = (int) ($unread_notifikasi ?? 0);
$approval_total = (int) ($approval_total ?? count($pengajuan));
$wadek_actionable = (int) ($approval_actionable ?? count(array_filter($pengajuan, static function ($p) { return scm_loan_can_act($p, 'wadek'); })));
$wadek_page_actionable = count(array_filter($pengajuan, static function ($p) {
    return scm_loan_can_act($p, 'wadek');
}));
$page = max(1, (int) ($page ?? 1));
$per_page = (int) ($per_page ?? 10);
$total_pages = max(1, (int) ($total_pages ?? 1));
$approval_sort = (string) ($filters['sort_by'] ?? '');
$approval_dir = (string) ($filters['sort_dir'] ?? 'desc');
$wadek_query = $_GET;
$wadek_query['per_page'] = $per_page;

if (!function_exists('wadek_loan_status_tone')) {
    function wadek_loan_status_tone($status)
    {
        $normalized = strtolower(trim((string) $status));

        if (strpos($normalized, 'tolak') !== false || strpos($normalized, 'reject') !== false) {
            return 'is-rejected';
        }

        if (
            strpos($normalized, 'dikembalikan') !== false ||
            strpos($normalized, 'selesai') !== false ||
            strpos($normalized, 'disetujui') !== false
        ) {
            return 'is-completed';
        }

        if (
            strpos($normalized, 'menunggu') !== false ||
            strpos($normalized, 'dipinjam') !== false ||
            strpos($normalized, 'wadek') !== false
        ) {
            return 'is-current';
        }

        return 'is-pending';
    }
}

if (!function_exists('wadek_client_filter_fields')) {
    function wadek_client_filter_fields()
    {
        return [
            'number'   => ['label' => 'No. peminjaman', 'placeholder' => 'Cari nomor peminjaman'],
            'peminjam' => ['label' => 'Peminjam / NIM', 'placeholder' => 'Cari nama peminjam atau NIM/NIP'],
            'barang'   => ['label' => 'Nama barang / kode', 'placeholder' => 'Cari nama barang atau kode aset'],
            'lab'      => ['label' => 'Laboratorium', 'placeholder' => 'Cari ruangan / laboratorium'],
            'masa'     => ['label' => 'Masa pinjam', 'placeholder' => 'Pilih tanggal peminjaman (YYYY-MM-DD)', 'type' => 'date'],
            'status'   => ['label' => 'Status', 'placeholder' => 'Cari status approval peminjaman'],
        ];
    }
}

if (!function_exists('render_wadek_client_filter')) {
    function render_wadek_client_filter($id, $selected_rows = [], $per_page = 10)
    {
        $fields = wadek_client_filter_fields();
        $default_field = (string) array_key_first($fields);
        $selected_rows = is_array($selected_rows) && !empty($selected_rows) ? array_slice($selected_rows, 0, 4) : [['field' => $default_field, 'value' => '']];
        ?>
        <form method="get" action="<?= current_url() ?>">
            <input type="hidden" name="page" value="1">
            <input type="hidden" name="per_page" value="<?= (int) $per_page ?>">
            <input type="hidden" name="sort_by" value="<?= html_escape($_GET['sort_by'] ?? '') ?>">
            <input type="hidden" name="sort_dir" value="<?= html_escape($_GET['sort_dir'] ?? 'desc') ?>">
            <div id="<?= html_escape($id) ?>" class="kp-multi-filter scm-search-filter" data-kp-multi-filter data-max-filters="4">
                <div class="kp-multi-filter-heading">
                    <h3><i class="bi bi-funnel me-2" aria-hidden="true"></i>Filter pencarian</h3>
                </div>
                <div class="kp-multi-filter-list" data-filter-list>
                    <?php foreach ($selected_rows as $row_index => $selected_row): 
                        $selected_field = (string) ($selected_row['field'] ?? $default_field);
                        $selected_value = (string) ($selected_row['value'] ?? '');
                        if (!isset($fields[$selected_field])) $selected_field = $default_field;
                        $selected_meta = $fields[$selected_field];
                    ?>
                    <div class="kp-multi-filter-row" data-filter-row>
                        <select class="form-select kp-multi-filter-field" name="filter_field[]" aria-label="Jenis filter <?= $row_index + 1 ?>">
                            <?php foreach ($fields as $key => $meta): ?>
                                <option value="<?= html_escape($key) ?>" data-input-type="<?= html_escape($meta['type'] ?? 'search') ?>" data-placeholder="<?= html_escape($meta['placeholder']) ?>" <?= $selected_field === $key ? 'selected' : '' ?>><?= html_escape($meta['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="<?= html_escape($selected_meta['type'] ?? 'search') ?>" class="form-control kp-multi-filter-value" name="filter_value[]" value="<?= html_escape($selected_value) ?>" placeholder="<?= html_escape($selected_meta['placeholder']) ?>" autocomplete="off" aria-label="Nilai filter <?= $row_index + 1 ?>">
                        <div class="kp-multi-filter-tools">
                            <button type="button" class="btn btn-outline-secondary kp-multi-filter-icon kp-multi-filter-remove" aria-label="Hapus filter"><i class="bi bi-dash-lg" aria-hidden="true"></i></button>
                            <button type="button" class="btn btn-outline-primary kp-multi-filter-icon kp-multi-filter-add" aria-label="Tambah filter"><i class="bi bi-plus-lg" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="scm-search-filter__actions mt-3">
                    <button type="submit" class="btn scm-search-filter__apply" data-kp-filter-apply><i class="bi bi-search"></i> Terapkan filter</button>
                    <a href="<?= current_url() ?>" class="btn scm-search-filter__reset"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                </div>
            </div>
        </form>
        <?php
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= html_escape($title ?? 'Approval Peminjaman - Wakil Dekan (Wadek)') ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/dashboard-theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/loan-progress.css'); ?>?v=<?= @filemtime(FCPATH . 'assets/css/loan-progress.css'); ?>">
    <?php include APPPATH . 'views/shared/theme_assets.php'; ?>
    
    <style>
        .modal { display: none; }
        
        .wadek-loan-page {
            --kp-bg: var(--scm-theme-bg, #f4f5f7);
            --kp-surface: var(--scm-theme-surface, #ffffff);
            --kp-surface-soft: var(--scm-theme-surface-soft, #f7f8fa);
            --kp-border: var(--scm-theme-border, #dfe3e8);
            --kp-text: var(--scm-theme-text, #18202b);
            --kp-muted: var(--scm-theme-muted, #6c7784);
            --kp-orange: #ff6b00;
            --kp-orange-soft: #fff5e9;
            min-height: 100vh;
            background: var(--kp-bg);
            color: var(--kp-text);
            font-family: Poppins, sans-serif;
            font-size: 14px;
        }

        .kp-page-content {
            padding-top: 22px;
            padding-bottom: 32px;
        }

        .brand-mark {
            display: inline-flex;
            width: 42px;
            height: 42px;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(234, 91, 26, .16);
            color: #ea5b1a;
            font-size: 1.35rem;
        }

        .notif-bell {
            display: inline-flex;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            align-items: center;
            justify-content: center;
        }

        .notif-menu {
            width: min(380px, calc(100vw - 32px));
            max-height: min(420px, calc(100vh - 110px));
            overflow-y: auto;
        }

        .theme-toggle {
            width: 38px;
            height: 38px;
            flex: 0 0 38px !important;
            padding: 0 !important;
        }

        html.scm-theme-light {
            --scm-bg: #f3f4f6;
            --scm-surface: #ffffff;
            --scm-surface-strong: #eef0f2;
            --scm-border: #dfe3e6;
            --scm-text: #1c2024;
            --scm-muted: #68727b;
            --scm-orange-soft: rgba(234, 91, 26, .1);
        }

        html.scm-theme-light .topbar {
            border-color: #e3e6e8 !important;
            background: #ffffff !important;
            box-shadow: 0 5px 18px rgba(35, 42, 47, .06);
        }

        .kp-page-heading {
            margin-bottom: 18px;
        }

        .kp-page-heading h1 {
            margin: 0 0 4px;
            color: var(--kp-text);
            font-size: clamp(1.4rem, 2vw, 1.85rem);
            font-weight: 700;
        }

        .kp-context {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            color: var(--kp-muted);
            font-size: .76rem;
        }

        .kp-card {
            overflow: hidden;
            border: 1px solid var(--kp-border);
            border-radius: 9px;
            background: var(--kp-surface);
            box-shadow: 0 5px 18px rgba(25, 36, 50, .045);
        }

        .kp-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 17px 20px;
        }

        .kp-section-title {
            margin: 0 0 3px;
            color: var(--kp-text);
            font-size: 1rem;
            font-weight: 700;
        }

        .kp-section-copy {
            margin: 0;
            color: var(--kp-muted);
            font-size: .76rem;
        }

        .kp-count-badge,
        .kp-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            width: max-content;
            min-height: 29px;
            padding: 5px 11px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
        }

        .kp-count-badge,
        .kp-status-badge.is-current {
            border-color: #f4bd70;
            background: var(--kp-orange-soft);
            color: #965600;
        }

        .kp-status-badge.is-completed {
            border-color: #9ed7bd;
            background: #eaf8f1;
            color: #13734d;
        }

        .kp-status-badge.is-pending {
            border-color: #d6dbe1;
            background: #f2f4f6;
            color: #66717d;
        }

        .kp-status-badge.is-rejected {
            border-color: #efb2b2;
            background: #fff0f0;
            color: #b4232d;
        }

        .kp-status-dot {
            width: 7px;
            height: 7px;
            flex: 0 0 7px;
            border-radius: 50%;
            background: currentColor;
        }

        .kp-multi-filter { padding: 18px 20px; }
        .kp-filter-card .kp-multi-filter { padding: 18px 20px; }
        .kp-multi-filter-heading { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 14px; }
        .kp-multi-filter-heading h3 { margin: 0; color: var(--kp-text); font-size: .95rem; font-weight: 700; }
        .kp-multi-filter-heading h3 i { color: var(--kp-orange); }
        .kp-multi-filter-list { display: grid; gap: 9px; }
        .kp-multi-filter-row { display: grid; grid-template-columns: minmax(205px, .72fr) minmax(270px, 1.55fr) auto; align-items: center; gap: 9px; }
        .kp-multi-filter-row .form-select, .kp-multi-filter-row .form-control { min-height: 42px; border-color: #cbd3dc; background-color: var(--kp-surface); color: var(--kp-text); font-size: .74rem; box-shadow: none; }
        .kp-multi-filter-row .form-select:focus, .kp-multi-filter-row .form-control:focus { border-color: var(--kp-orange); box-shadow: 0 0 0 .2rem rgba(255, 107, 0, .12); }
        .kp-multi-filter-tools { display: flex; align-items: center; gap: 8px; }
        .kp-multi-filter-icon { width: 42px; height: 42px; display: inline-flex; flex: 0 0 42px; align-items: center; justify-content: center; padding: 0; border-radius: 50%; }
        .kp-multi-filter-add { border-color: var(--kp-orange); color: var(--kp-orange); }
        .kp-multi-filter-add:hover { border-color: var(--kp-orange); background: var(--kp-orange); color: #fff; }
        .kp-multi-filter-icon:disabled { opacity: .38; }

        .kp-table-card {
            margin-top: 12px;
        }

        .kp-table {
            min-width: 1120px;
            margin: 0;
            color: var(--kp-text);
        }

        .kp-table > :not(caption) > * > * {
            padding: 11px 14px;
            border-bottom-color: var(--kp-border);
            vertical-align: middle;
        }

        .kp-table thead th {
            border-bottom-width: 1px;
            background: var(--kp-surface-soft);
            color: #5f6974;
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .025em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .kp-table tbody td {
            background: var(--kp-surface);
            color: var(--kp-text);
            font-size: .72rem;
        }

        .kp-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .kp-table tbody tr:hover td {
            background: var(--kp-surface-soft);
        }

        .kp-sort-button {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 0;
            border: 0;
            background: transparent;
            color: inherit;
            font: inherit;
            font-weight: inherit;
            letter-spacing: inherit;
            text-transform: inherit;
        }

        .kp-sort-button i {
            color: #8c96a1;
            font-size: .68rem;
        }

        .kp-primary-text {
            color: var(--kp-text);
            font-size: .72rem;
            font-weight: 700;
        }

        .kp-secondary-text {
            margin-top: 2px;
            color: var(--kp-muted);
            font-size: .61rem;
        }

        .kp-item-list {
            display: grid;
            gap: 3px;
        }

        .kp-item-meta {
            margin-left: 8px;
            color: var(--kp-muted);
            font-size: .61rem;
            white-space: nowrap;
        }

        .kp-detail-button {
            min-height: 30px;
            padding: 5px 13px;
            border-color: #0d6efd;
            border-radius: 999px;
            font-size: .67rem;
            font-weight: 600;
        }

        .kp-empty-row td {
            padding: 38px 18px !important;
            color: var(--kp-muted) !important;
            text-align: center;
        }

        .kp-section-header {
            padding: 16px 19px;
            border-bottom: 1px solid var(--kp-border);
        }

        .kp-pagination-footer {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 14px;
            padding: 14px 19px;
            border-top: 1px solid var(--kp-border);
            background: var(--kp-surface);
        }

        .kp-page-size {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--kp-muted);
            font-size: .7rem;
        }

        .kp-page-size select {
            width: auto;
            min-width: 66px;
            height: 31px;
            border-color: #cbd3dc;
            background: var(--kp-surface);
            color: var(--kp-text);
            font-size: .7rem;
        }

        .kp-page-status {
            color: var(--kp-muted);
            font-size: .7rem;
            text-align: center;
        }

        .kp-pagination-nav {
            display: flex;
            justify-content: flex-end;
        }

        .kp-pagination {
            display: flex;
            gap: 4px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .kp-page-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 31px;
            height: 31px;
            padding: 0 7px;
            border: 1px solid #cbd3dc;
            border-radius: 6px;
            background: var(--kp-surface);
            color: var(--kp-text);
            font-size: .69rem;
            font-weight: 500;
            text-decoration: none;
            transition: all .16s ease;
        }

        .kp-page-button:hover:not(.disabled):not(.is-active) {
            border-color: var(--kp-orange);
            background: var(--kp-orange-soft);
            color: #965600;
        }

        .kp-page-button.is-active {
            border-color: var(--kp-orange);
            background: var(--kp-orange);
            color: #fff;
            font-weight: 700;
        }

        .kp-page-button.disabled {
            border-color: var(--kp-border);
            color: #9da7b2;
            pointer-events: none;
        }

        /* Modal Styles */
        .kp-loan-modal .modal-dialog {
            max-width: 780px;
        }

        .kp-loan-modal .modal-content {
            border: 1px solid var(--kp-border);
            border-radius: 12px;
            background: var(--kp-surface);
            box-shadow: 0 16px 45px rgba(25, 36, 50, .14);
        }

        .kp-loan-modal .modal-header {
            padding: 17px 21px 15px;
            border-bottom-color: var(--kp-border);
            background: var(--kp-surface-soft);
        }

        .kp-modal-kicker {
            color: var(--kp-muted);
            font-size: .64rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .kp-loan-modal .modal-title {
            margin-top: 1px;
            color: var(--kp-text);
            font-size: 1.15rem;
            font-weight: 700;
        }

        .kp-modal-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .kp-meta-box {
            padding: 9px 12px;
            border: 1px solid var(--kp-border);
            border-radius: 7px;
            background: var(--kp-surface);
        }

        .kp-meta-label {
            color: var(--kp-muted);
            font-size: .6rem;
            font-weight: 700;
            letter-spacing: .045em;
            text-transform: uppercase;
        }

        .kp-meta-value {
            margin-top: 3px;
            color: var(--kp-text);
            font-size: .75rem;
            font-weight: 600;
        }

        .kp-loan-modal .modal-body {
            padding: 19px 21px;
        }

        .kp-modal-section + .kp-modal-section {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid var(--kp-border);
        }

        .kp-modal-section-title {
            margin: 0 0 8px;
            color: var(--kp-text);
            font-size: .82rem;
            font-weight: 700;
        }

        .kp-purpose {
            margin: 7px 0 0;
            color: var(--kp-text);
            font-size: .76rem;
            line-height: 1.65;
        }

        .kp-detail-table {
            overflow: hidden;
            margin-top: 9px;
            border: 1px solid var(--kp-border);
            border-radius: 8px;
        }

        .kp-detail-table table {
            margin: 0;
            color: var(--kp-text);
        }

        .kp-detail-table th,
        .kp-detail-table td {
            padding: 9px 10px;
            border-bottom-color: var(--kp-border);
            font-size: .67rem;
            vertical-align: middle;
        }

        .kp-detail-table th {
            background: var(--kp-surface-soft);
            color: var(--kp-muted);
            font-size: .59rem;
            letter-spacing: .035em;
            text-transform: uppercase;
        }

        .kp-detail-table td {
            background: var(--kp-surface);
            color: var(--kp-text);
        }

        .kp-loan-modal textarea {
            margin-top: 8px;
            min-height: 92px;
            resize: vertical;
            border-color: #cbd3dc;
            background: var(--kp-surface);
            color: var(--kp-text);
            font-size: .73rem;
        }

        .kp-loan-modal textarea:focus {
            border-color: var(--kp-orange);
            box-shadow: 0 0 0 .2rem rgba(255, 107, 0, .12);
        }

        .kp-loan-modal .modal-footer {
            gap: 8px;
            padding: 14px 21px;
            border-top-color: var(--kp-border);
            background: var(--kp-surface-soft);
        }

        .kp-loan-modal .modal-footer .btn {
            min-width: 92px;
            min-height: 36px;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 600;
        }

        @media (max-width: 767.98px) {
            .kp-multi-filter { padding: 14px; }
            .kp-multi-filter-heading { flex-direction: column; gap: 7px; }
            .kp-multi-filter-row { grid-template-columns: 1fr; gap: 8px; padding: 11px; border: 1px solid var(--kp-border); border-radius: 9px; }
            .kp-multi-filter-tools { justify-content: flex-end; }
            .kp-page-content { padding-top: 16px; }
            .kp-page-heading, .kp-summary { align-items: flex-start; }
            .kp-context { justify-content: flex-start; }
            .kp-summary { flex-direction: column; padding: 15px; }
            .kp-pagination-footer { grid-template-columns: 1fr; justify-items: center; padding-block: 12px; }
            .kp-page-size, .kp-pagination-nav { justify-content: center; }
            .kp-modal-meta { grid-template-columns: 1fr; }
            .kp-loan-modal .modal-header, .kp-loan-modal .modal-body, .kp-loan-modal .modal-footer { padding-inline: 16px; }
            .kp-loan-modal .modal-footer .btn { flex: 1 1 auto; min-width: 0; }
        }
    </style>
</head>
<body class="scm-dashboard scm-dashboard-kaprodi wadek-loan-page">

    <!-- Curved Sidebar Navigation -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div id="laaMainContentWrapper" class="page-wrapper-for-sidebar">
        <!-- Topbar -->
        <header class="topbar sticky-top">
            <div class="container-fluid px-3 px-lg-4 py-3">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div class="dashboard-topbar-brand d-flex align-items-center gap-3">
                        <span class="brand-mark"><i class="bi bi-shield-check"></i></span>
                        <div>
                            <div class="fw-bold">Panel Wakil Dekan (Wadek)</div>
                        </div>
                    </div>
                    <div class="topbar-actions d-flex align-items-center gap-2 ms-auto">
                        <div class="dropdown">
                            <button
                                id="wadekNotificationButton"
                                class="btn btn-outline-light btn-sm rounded-circle notif-bell position-relative"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                aria-label="Notifikasi"
                            >
                                <i class="bi bi-bell"></i>
                                <?php if ($notif_count > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $notif_count ?></span>
                                <?php endif; ?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow border-0 p-2 notif-menu">
                                <div class="fw-bold px-2 py-1">Notifikasi</div>
                                <?php if (empty($notif_items)): ?>
                                    <div class="small text-muted px-2 py-3">Belum ada notifikasi.</div>
                                <?php else: ?>
                                    <?php foreach ($notif_items as $notification): ?>
                                        <div class="dropdown-item rounded-3 py-2 <?= empty($notification->is_read) ? 'bg-light' : '' ?>">
                                            <div class="fw-semibold small"><?= html_escape($notification->judul) ?></div>
                                            <div class="small text-muted text-wrap"><?= html_escape($notification->pesan) ?></div>
                                            <div class="small text-muted mt-1"><?= html_escape(waktu_indonesia($notification->created_at)) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn btn-outline-light btn-sm rounded-circle theme-toggle"
                            data-theme-toggle
                            aria-label="Ubah tema"
                            title="Ubah tema"
                        >
                            <i class="bi bi-moon-stars" aria-hidden="true"></i>
                        </button>
                        <a href="<?= site_url('peminjaman_barang') ?>" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="bi bi-box-seam me-1"></i> Web User
                        </a>
                        <a href="<?= site_url('login/logout') ?>" class="btn btn-sm btn-fik rounded-pill px-3">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="container-fluid kp-page-content px-3 px-lg-4">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3"><?= html_escape($this->session->flashdata('success')) ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3"><?= html_escape($this->session->flashdata('error')) ?></div>
            <?php endif; ?>

            <div class="kp-page-heading">
                <div class="row align-items-end">
                    <div class="col-lg">
                        <h1>Approval Peminjaman</h1>
                    </div>
                    <div class="col-lg-auto">
                        <div class="kp-context">
                            <span><i class="bi bi-mortarboard me-1"></i>Wakil Dekan 1 (Wadek)</span>
                            <span><i class="bi bi-calendar3 me-1"></i><?= tanggal_indonesia(date('Y-m-d')) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Seluruh Peminjaman Summary Card -->
            <section class="kp-card kp-summary">
                <div>
                    <h2 class="kp-section-title">Progress Seluruh Peminjaman</h2>
                    <p class="kp-section-copy mt-1">Semua tahap dapat dipantau; aksi hanya tersedia saat menunggu persetujuan Wadek.</p>
                </div>
                <span class="kp-count-badge">
                    <span class="kp-status-dot"></span>
                    <span><?= $wadek_actionable ?> perlu aksi · <?= number_format($approval_total, 0, ',', '.') ?> total</span>
                </span>
            </section>

            <!-- Filter Pencarian Card -->
            <section class="kp-card kp-filter-card mt-3" aria-label="Filter pengajuan peminjaman luar kampus">
                <?php render_wadek_client_filter('wadekApprovalFilters', $filter_rows ?? [], $per_page); ?>
            </section>

            <!-- Table Card -->
            <section class="kp-card kp-table-card mb-3" aria-labelledby="wadekApprovalTitle">
                <div class="kp-section-header">
                    <h2 id="wadekApprovalTitle" class="kp-section-title">Pengajuan Menunggu ACC</h2>
                </div>
                <div class="table-responsive">
                    <table class="table kp-table">
                        <thead>
                            <tr>
                                <?php foreach (['number' => 'No. Peminjaman', 'peminjam' => 'Nama Peminjam', 'barang' => 'Barang', 'lab' => 'Laboratorium', 'masa' => 'Masa Pinjam', 'status' => 'Status'] as $sort_key => $sort_label): ?>
                                    <th scope="col" aria-sort="<?= scm_sort_aria($sort_key, $approval_sort, $approval_dir) ?>">
                                        <a class="kp-sort-button scm-sort-control <?= $approval_sort === $sort_key ? 'is-active' : '' ?>" href="<?= scm_sort_url($sort_key, $approval_sort, $approval_dir) ?>">
                                            <?= html_escape($sort_label) ?>
                                            <i class="bi <?= scm_sort_icon_class($sort_key, $approval_sort, $approval_dir) ?>" aria-hidden="true"></i>
                                        </a>
                                    </th>
                                <?php endforeach; ?>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="wadekApprovalBody">
                            <?php if (empty($pengajuan)): ?>
                                <tr class="kp-empty-row">
                                    <td colspan="7">Belum ada data pengajuan peminjaman luar kampus.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pengajuan as $p): ?>
                                    <?php
                                    $number = $p->group_id ?? $p->id_peminjaman ?? '-';
                                    $item_names = [];
                                    $laboratories = [];
                                    foreach (($p->detail_barang ?? []) as $detail) {
                                        $item_names[] = $detail->nama_aset ?? '-';
                                        if (!empty($detail->nama_ruangan)) {
                                            $laboratories[] = $detail->nama_ruangan;
                                        }
                                    }
                                    $laboratories = array_values(array_unique($laboratories));
                                    $period = masa_pinjam_indonesia($p->tanggal_pinjam ?? null, $p->tanggal_kembali_rencana ?? null);
                                    $status = $p->status ?? '-';
                                    $can_act = scm_loan_can_act($p, 'wadek');
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="kp-primary-text"><?= html_escape($number) ?></div>
                                            <div class="kp-secondary-text">ID <?= html_escape($p->id_peminjaman ?? '-') ?></div>
                                        </td>
                                        <td>
                                            <div class="kp-primary-text"><?= html_escape($p->nama_peminjam ?? '-') ?></div>
                                            <div class="kp-secondary-text"><?= html_escape($p->nim_nip ?? '-') ?> &bull; <?= html_escape($p->prodi ?? 'FIK') ?></div>
                                        </td>
                                        <td>
                                            <div class="kp-item-list">
                                                <?php if (empty($p->detail_barang)): ?>
                                                    <span><?= html_escape($p->nama_aset ?? 'Barang') ?> (<?= (int)($p->jumlah_pinjam ?? 1) ?> unit)</span>
                                                <?php else: ?>
                                                    <?php foreach ($p->detail_barang as $detail): ?>
                                                        <div>
                                                            <span class="kp-primary-text"><?= html_escape($detail->nama_aset ?? '-') ?></span>
                                                            <span class="kp-item-meta"><?= (int) ($detail->jumlah_pinjam ?? 0) ?> unit</span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?= html_escape(implode(', ', $laboratories) ?: '-') ?></td>
                                        <td>
                                            <div class="kp-primary-text"><?= html_escape(tanggal_indonesia($p->tanggal_pinjam ?? null)) ?></div>
                                            <div class="kp-secondary-text">s.d. <?= html_escape(tanggal_indonesia($p->tanggal_kembali_rencana ?? null)) ?></div>
                                        </td>
                                        <td>
                                            <?php 
                                                $loan_progress_item = $p; 
                                                $loan_progress_compact = true; 
                                                $loan_progress_detail_target = '#wadekApproval' . (int)$p->id_peminjaman; 
                                                include APPPATH . 'views/shared/loan_progress.php'; 
                                                unset($loan_progress_detail_target);
                                            ?>
                                        </td>
                                        <td class="text-end">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary kp-detail-button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#wadekApproval<?= (int) $p->id_peminjaman ?>"
                                            >
                                                <i class="bi bi-sliders me-1"></i>Proses
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <?php if (!empty($pengajuan)): ?>
                    <div class="kp-pagination-footer">
                        <div class="kp-page-size">
                            <label for="wadekApprovalPageSize">Tampilkan:</label>
                            <select id="wadekApprovalPageSize" class="form-select form-select-sm" onchange="var u=new URL(window.location.href);u.searchParams.set('per_page',this.value);u.searchParams.set('page','1');window.location.href=u.toString();">
                                <?php foreach ([10, 25, 50, 100] as $size): ?>
                                    <option value="<?= $size ?>" <?= $per_page === $size ? 'selected' : '' ?>><?= $size ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span id="wadekApprovalTotal">Total item: <?= number_format($approval_total, 0, ',', '.') ?></span>
                        </div>
                        <?php 
                            $first_item = $approval_total > 0 ? (($page - 1) * $per_page) + 1 : 0; 
                            $last_item = min($approval_total, $page * $per_page); 
                        ?>
                        <div id="wadekApprovalPageStatus" class="kp-page-status">Menampilkan <?= $first_item ?>–<?= $last_item ?> dari <?= number_format($approval_total, 0, ',', '.') ?> data</div>
                        <nav class="kp-pagination-nav" aria-label="Paging pengajuan">
                            <ul id="wadekApprovalPagination" class="kp-pagination">
                                <?php $wadek_query['page'] = max(1, $page - 1); ?>
                                <li><a class="kp-page-button <?= $page <= 1 ? 'disabled' : '' ?>" href="<?= current_url() . '?' . http_build_query($wadek_query) ?>">Previous</a></li>
                                <?php foreach (scm_pagination_tokens($page, $total_pages) as $token): ?>
                                    <?php if (is_string($token)): ?>
                                        <li><span class="kp-page-button disabled">...</span></li>
                                    <?php else: 
                                        $wadek_query['page'] = $token; 
                                    ?>
                                        <li><a class="kp-page-button <?= $token === $page ? 'is-active' : '' ?>" href="<?= current_url() . '?' . http_build_query($wadek_query) ?>"><?= $token ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php $wadek_query['page'] = min($total_pages, $page + 1); ?>
                                <li><a class="kp-page-button <?= $page >= $total_pages ? 'disabled' : '' ?>" href="<?= current_url() . '?' . http_build_query($wadek_query) ?>">Next</a></li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <!-- Modals for Processing Approval / Details -->
    <?php foreach ($pengajuan as $p): ?>
        <?php
        $number = $p->group_id ?? $p->id_peminjaman ?? '-';
        $status = $p->status ?? '-';
        $can_act = scm_loan_can_act($p, 'wadek');
        $modal_total_units = 0;
        foreach (($p->detail_barang ?? []) as $modal_detail) {
            $modal_total_units += (int) ($modal_detail->jumlah_pinjam ?? 0);
        }
        $modal_loan_days = durasi_pinjam_hari($p->tanggal_pinjam ?? null, $p->tanggal_kembali_rencana ?? null);
        ?>
        <div
            class="modal fade kp-loan-modal"
            id="wadekApproval<?= (int) $p->id_peminjaman ?>"
            tabindex="-1"
            aria-labelledby="wadekApprovalTitle<?= (int) $p->id_peminjaman ?>"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form
                    method="post"
                    class="modal-content"
                    action="<?= site_url('wadek/peminjaman/setujui/' . $p->id_peminjaman) ?>"
                >
                    <div class="modal-header">
                        <div class="w-100 pe-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <div class="kp-modal-kicker">Peminjaman Luar Kampus</div>
                                    <h2 id="wadekApprovalTitle<?= (int) $p->id_peminjaman ?>" class="modal-title">
                                        <?= html_escape($number) ?>
                                    </h2>
                                </div>
                                <span class="kp-status-badge <?= wadek_loan_status_tone($status) ?>">
                                    <span class="kp-status-dot"></span><?= html_escape($status) ?>
                                </span>
                            </div>
                            <div class="kp-modal-meta">
                                <div class="kp-meta-box">
                                    <div class="kp-meta-label">Peminjam</div>
                                    <div class="kp-meta-value"><?= html_escape($p->nama_peminjam ?? '-') ?></div>
                                    <div class="kp-secondary-text">NIM/NIP: <?= html_escape($p->nim_nip ?? '-') ?></div>
                                </div>
                                <div class="kp-meta-box">
                                    <div class="kp-meta-label">Periode</div>
                                    <div class="kp-meta-value">
                                        <?= html_escape(masa_pinjam_indonesia($p->tanggal_pinjam ?? null, $p->tanggal_kembali_rencana ?? null)) ?>
                                    </div>
                                    <div class="kp-secondary-text"><?= $modal_loan_days > 0 ? $modal_loan_days . ' hari' : '-' ?></div>
                                </div>
                                <div class="kp-meta-box">
                                    <div class="kp-meta-label">Barang</div>
                                    <div class="kp-meta-value"><?= count((array) ($p->detail_barang ?? [])) ?> jenis / <?= $modal_total_units ?: (int)($p->jumlah_pinjam ?? 1) ?> unit</div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <section class="kp-modal-section">
                            <div class="kp-modal-section-title">Progress Peminjaman</div>
                            <?php 
                                $loan_progress_item = $p; 
                                $loan_progress_compact = false; 
                                include APPPATH . 'views/shared/loan_progress.php'; 
                            ?>
                            <?php if (!$can_act): ?>
                                <div class="alert alert-info small mt-3 mb-0">
                                    <i class="bi bi-info-circle me-1"></i>Data ini ditampilkan untuk pemantauan. Tombol persetujuan hanya aktif saat status menunggu ACC Wadek.
                                </div>
                            <?php endif; ?>
                        </section>

                        <section class="kp-modal-section">
                            <div class="kp-modal-section-title">Keperluan &amp; Lokasi Kegiatan</div>
                            <p class="kp-purpose"><?= nl2br(html_escape($p->keperluan ?? '-')) ?></p>
                        </section>

                        <section class="kp-modal-section">
                            <div class="kp-modal-section-title">Detail Barang yang Diajukan</div>
                            <div class="kp-detail-table table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Barang</th>
                                            <th>Kode</th>
                                            <th>Laboratorium</th>
                                            <th class="text-center">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($p->detail_barang)): ?>
                                            <tr>
                                                <td class="fw-semibold"><?= html_escape($p->nama_aset ?? 'Barang') ?></td>
                                                <td><?= html_escape($p->kode_aset ?? '-') ?></td>
                                                <td>-</td>
                                                <td class="text-center"><?= (int) ($p->jumlah_pinjam ?? 1) ?></td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($p->detail_barang as $detail): ?>
                                                <tr>
                                                    <td class="fw-semibold"><?= html_escape($detail->nama_aset ?? '-') ?></td>
                                                    <td><?= html_escape($detail->kode_aset ?? '-') ?></td>
                                                    <td><?= html_escape($detail->nama_ruangan ?? '-') ?></td>
                                                    <td class="text-center"><?= (int) ($detail->jumlah_pinjam ?? 0) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                        <section class="kp-modal-section">
                            <label for="wadekNote<?= (int) $p->id_peminjaman ?>" class="kp-modal-section-title">Catatan / Arahan Wakil Dekan</label>
                            <textarea
                                id="wadekNote<?= (int) $p->id_peminjaman ?>"
                                name="catatan_wadek"
                                class="form-control"
                                rows="3"
                                placeholder="Tuliskan catatan persetujuan atau alasan penolakan..."
                                <?= $can_act ? '' : 'disabled'; ?>
                            ></textarea>
                        </section>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <?php if ($can_act): ?>
                            <button
                                type="submit"
                                formaction="<?= site_url('wadek/peminjaman/tolak/' . $p->id_peminjaman) ?>"
                                class="btn btn-outline-danger"
                            >
                                <i class="bi bi-x-lg me-1"></i>Tolak
                            </button>
                            <button
                                type="submit"
                                class="btn btn-success"
                            >
                                <i class="bi bi-check2 me-1"></i>Setujui
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/loan-progress.js'); ?>?v=<?= @filemtime(FCPATH . 'assets/js/loan-progress.js'); ?>"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
            new bootstrap.Tooltip(element);
        });

        function initMultiFilter(rootId) {
            var root = document.getElementById(rootId);
            if (!root) return null;
            var list = root.querySelector('[data-filter-list]');
            var maxFilters = Number(root.dataset.maxFilters || 4);
            if (!list) return root;

            function syncInput(row, clearValue) {
                var select = row.querySelector('.kp-multi-filter-field');
                var input = row.querySelector('.kp-multi-filter-value');
                var option = select && select.options[select.selectedIndex];
                if (!select || !input || !option) return;
                if (clearValue) input.value = '';
                input.type = option.dataset.inputType || 'search';
                input.placeholder = option.dataset.placeholder || 'Ketik untuk mencari';
            }

            function updateButtons() {
                var rows = Array.prototype.slice.call(list.querySelectorAll('[data-filter-row]'));
                rows.forEach(function (row, index) {
                    row.querySelector('.kp-multi-filter-remove').disabled = rows.length === 1;
                    row.querySelector('.kp-multi-filter-add').disabled = rows.length >= maxFilters || index !== rows.length - 1;
                });
            }

            function addRow() {
                var rows = list.querySelectorAll('[data-filter-row]');
                if (!rows.length || rows.length >= maxFilters) return null;
                var sourceSelect = rows[0].querySelector('.kp-multi-filter-field');
                var row = document.createElement('div');
                row.className = 'kp-multi-filter-row';
                row.dataset.filterRow = '';
                row.innerHTML =
                    '<select class="form-select kp-multi-filter-field" name="filter_field[]" aria-label="Jenis filter ' + (rows.length + 1) + '">' + sourceSelect.innerHTML + '</select>' +
                    '<input type="search" class="form-control kp-multi-filter-value" name="filter_value[]" autocomplete="off" aria-label="Nilai filter ' + (rows.length + 1) + '">' +
                    '<div class="kp-multi-filter-tools">' +
                        '<button type="button" class="btn btn-outline-secondary kp-multi-filter-icon kp-multi-filter-remove" aria-label="Hapus filter"><i class="bi bi-dash-lg" aria-hidden="true"></i></button>' +
                        '<button type="button" class="btn btn-outline-primary kp-multi-filter-icon kp-multi-filter-add" aria-label="Tambah filter"><i class="bi bi-plus-lg" aria-hidden="true"></i></button>' +
                    '</div>';
                list.appendChild(row);
                syncInput(row, false);
                updateButtons();
                return row;
            }

            list.querySelectorAll('[data-filter-row]').forEach(function (row) { syncInput(row, false); });
            updateButtons();
            list.addEventListener('click', function (event) {
                var row = event.target.closest('[data-filter-row]');
                if (!row) return;
                if (event.target.closest('.kp-multi-filter-add')) {
                    var added = addRow();
                    if (added) added.querySelector('.kp-multi-filter-value').focus();
                } else if (event.target.closest('.kp-multi-filter-remove') && list.querySelectorAll('[data-filter-row]').length > 1) {
                    row.remove();
                    updateButtons();
                }
            });
            list.addEventListener('change', function (event) {
                if (!event.target.matches('.kp-multi-filter-field')) return;
                var row = event.target.closest('[data-filter-row]');
                syncInput(row, true);
                row.querySelector('.kp-multi-filter-value').focus();
            });
            root.querySelector('[data-kp-filter-reset]')?.addEventListener('click', function () {
                var rows = Array.prototype.slice.call(list.querySelectorAll('[data-filter-row]'));
                rows.slice(1).forEach(function (row) { row.remove(); });
                if (rows[0]) {
                    var select = rows[0].querySelector('.kp-multi-filter-field');
                    if (select) select.selectedIndex = 0;
                    syncInput(rows[0], true);
                }
                updateButtons();
            });
            return root;
        }

        initMultiFilter('wadekApprovalFilters');
    });
    </script>
</body>
</html>
