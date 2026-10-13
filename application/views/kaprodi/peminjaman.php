<?php
$pengajuan = is_array($pengajuan ?? null) ? $pengajuan : [];
$pengembalian = is_array($pengembalian ?? null) ? $pengembalian : [];
$filter_keyword = trim((string) ($filters['q'] ?? ''));
$notif_items = isset($notifikasi) && is_array($notifikasi) ? $notifikasi : [];
$notif_count = (int) ($unread_notifikasi ?? 0);
$approval_total = (int) ($approval_total ?? count($pengajuan));
$kaprodi_stock_readiness = static function ($loan) {
    $required_by_asset = [];
    foreach ((array) ($loan->detail_barang ?? []) as $detail) {
        $state = (string) ($detail->stock_allocation_status ?? 'none');
        if (in_array($state, ['reserved', 'borrowed'], true)) continue;

        $asset_id = (int) ($detail->id_aset ?? 0);
        if (!isset($required_by_asset[$asset_id])) {
            $required_by_asset[$asset_id] = [
                'name' => (string) ($detail->nama_aset ?? 'Aset'),
                'required' => 0,
                'available' => max(0, min(
                    (int) ($detail->jumlah_tersedia ?? 0),
                    (int) ($detail->jumlah_total ?? 0) - (int) ($detail->jumlah_reserved ?? 0) - (int) ($detail->jumlah_dipinjam ?? 0)
                )),
            ];
        }
        $required_by_asset[$asset_id]['required'] += max(0, (int) ($detail->jumlah_pinjam ?? 0));
    }

    $shortages = array_values(array_filter($required_by_asset, static function ($item) {
        return (int) $item['required'] > (int) $item['available'];
    }));
    if (empty($shortages)) return ['ready' => true, 'message' => ''];

    $items = [];
    foreach (array_slice($shortages, 0, 2) as $shortage) {
        $items[] = $shortage['name'] . ': butuh ' . (int) $shortage['required'] . ', tersedia ' . (int) $shortage['available'];
    }
    return ['ready' => false, 'message' => 'Stok belum cukup. ' . implode('; ', $items) . '.'];
};
$kaprodi_actionable = (int) ($approval_actionable ?? count(array_filter($pengajuan, static function ($p) { return scm_loan_can_act($p, 'kaprodi'); })));
$kaprodi_page_actionable = count(array_filter($pengajuan, static function ($p) use ($kaprodi_stock_readiness) {
    return scm_loan_can_act($p, 'kaprodi') && $kaprodi_stock_readiness($p)['ready'];
}));
$page = max(1, (int) ($page ?? 1));
$per_page = (int) ($per_page ?? 10);
$total_pages = max(1, (int) ($total_pages ?? 1));
$return_total = (int) ($return_total ?? count($pengembalian));
$return_page = max(1, (int) ($return_page ?? 1));
$return_total_pages = max(1, (int) ($return_total_pages ?? 1));
$approval_sort = (string) ($approval_sort ?? '');
$approval_dir = (string) ($approval_dir ?? 'desc');
$return_sort = (string) ($return_sort ?? 'masa');
$return_dir = (string) ($return_dir ?? 'desc');

if (!function_exists('kaprodi_loan_status_tone')) {
    function kaprodi_loan_status_tone($status)
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
            strpos($normalized, 'kaprodi') !== false
        ) {
            return 'is-current';
        }

        return 'is-pending';
    }
}
if (!function_exists('kaprodi_client_filter_fields')) {
    function kaprodi_client_filter_fields($scope)
    {
        if ($scope === 'return') {
            return [
                'peminjam' => ['label' => 'Peminjam / NIM', 'placeholder' => 'Cari peminjam / NIM'],
                'barang' => ['label' => 'Nama barang', 'placeholder' => 'Cari nama barang'],
                'masa' => ['label' => 'Masa pinjam', 'placeholder' => 'Pilih tanggal atau rentang tanggal', 'type' => 'date'],
                'status' => ['label' => 'Status pengembalian', 'placeholder' => 'Cari status pengembalian'],
            ];
        }
        return [
            'number' => ['label' => 'No. peminjaman', 'placeholder' => 'Cari nomor peminjaman'],
            'peminjam' => ['label' => 'Peminjam / NIM', 'placeholder' => 'Cari peminjam / NIM'],
            'barang' => ['label' => 'Nama barang / kode', 'placeholder' => 'Cari nama barang / kode'],
            'lab' => ['label' => 'Laboratorium', 'placeholder' => 'Cari laboratorium'],
            'masa' => ['label' => 'Masa pinjam', 'placeholder' => 'Pilih tanggal atau rentang tanggal', 'type' => 'date'],
            'status' => ['label' => 'Status', 'placeholder' => 'Cari status approval'],
        ];
    }
}
if (!function_exists('render_kaprodi_client_filter')) {
    function render_kaprodi_client_filter($scope, $id, $selected_rows = [], $per_page = 10)
    {
        $fields = kaprodi_client_filter_fields($scope);
        $default_field = (string) array_key_first($fields);
        $selected_rows = is_array($selected_rows) && !empty($selected_rows) ? array_slice($selected_rows, 0, 4) : [['field' => $default_field, 'value' => '']];
        $is_server = $scope === 'approval';
        ?>
        <?php if ($is_server): ?><form method="get" action="<?= current_url() ?>"><input type="hidden" name="page" value="1"><input type="hidden" name="per_page" value="<?= (int) $per_page ?>"><input type="hidden" name="sort_by" value="<?= html_escape($_GET['sort_by'] ?? '') ?>"><input type="hidden" name="sort_dir" value="<?= html_escape($_GET['sort_dir'] ?? 'desc') ?>"><?php endif; ?>
        <div id="<?= html_escape($id) ?>" class="kp-multi-filter scm-search-filter" data-kp-multi-filter data-max-filters="4">
            <div class="kp-multi-filter-heading">
                <h3><i class="bi bi-funnel me-2" aria-hidden="true"></i>Filter pencarian</h3>
            </div>
            <div class="kp-multi-filter-list" data-filter-list>
                <?php foreach ($selected_rows as $row_index => $selected_row): $selected_field = (string) ($selected_row['field'] ?? $default_field); $selected_value = (string) ($selected_row['value'] ?? ''); if (!isset($fields[$selected_field])) $selected_field = $default_field; $selected_meta = $fields[$selected_field]; ?>
                <div class="kp-multi-filter-row" data-filter-row>
                    <select class="form-select kp-multi-filter-field" <?= $is_server ? 'name="filter_field[]"' : '' ?> aria-label="Jenis filter <?= $row_index + 1 ?>">
                        <?php foreach ($fields as $key => $meta): ?>
                            <option value="<?= html_escape($key) ?>" data-input-type="<?= html_escape($meta['type'] ?? 'search') ?>" data-placeholder="<?= html_escape($meta['placeholder']) ?>" <?= $selected_field === $key ? 'selected' : '' ?>><?= html_escape($meta['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="<?= html_escape($selected_meta['type'] ?? 'search') ?>" class="form-control kp-multi-filter-value" <?= $is_server ? 'name="filter_value[]"' : '' ?> value="<?= html_escape($selected_value) ?>" placeholder="<?= html_escape($selected_meta['placeholder']) ?>" autocomplete="off" aria-label="Nilai filter <?= $row_index + 1 ?>">
                    <div class="kp-multi-filter-tools">
                        <button type="button" class="btn btn-outline-secondary kp-multi-filter-icon kp-multi-filter-remove" aria-label="Hapus filter"><i class="bi bi-dash-lg" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-outline-primary kp-multi-filter-icon kp-multi-filter-add" aria-label="Tambah filter"><i class="bi bi-plus-lg" aria-hidden="true"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="scm-search-filter__actions mt-3">
                <button type="<?= $is_server ? 'submit' : 'button' ?>" class="btn scm-search-filter__apply" data-kp-filter-apply><i class="bi bi-search"></i> Terapkan filter</button>
                <?php if ($is_server): ?><a href="<?= current_url() ?>" class="btn scm-search-filter__reset"><i class="bi bi-arrow-counterclockwise"></i> Reset</a><?php else: ?><button type="button" class="btn scm-search-filter__reset" data-kp-filter-reset><i class="bi bi-arrow-counterclockwise"></i> Reset</button><?php endif; ?>
            </div>
        </div>
        <?php if ($is_server): ?></form><?php endif; ?>
        <?php
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= html_escape($title ?? 'Approval Peminjaman Kaprodi') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/dashboard-theme.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/loan-progress.css'); ?>?v=<?= @filemtime(FCPATH . 'assets/css/loan-progress.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/approval-bulk-select.css'); ?>?v=<?= @filemtime(FCPATH . 'assets/css/approval-bulk-select.css'); ?>">
    <?php include APPPATH . 'views/shared/theme_assets.php'; ?>
    <style>
        .kaprodi-loan-page {
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

        .kp-topbar {
            background: #202020;
            border-bottom: 3px solid var(--kp-orange);
            color: #fff;
        }

        .kp-topbar-inner {
            min-height: 66px;
        }

        .kp-topbar-title {
            font-size: .96rem;
            font-weight: 700;
        }

        .kp-topbar-subtitle {
            color: rgba(255, 255, 255, .62);
            font-size: .72rem;
        }

        .kp-topbar .btn {
            min-height: 30px;
            padding: .3rem .8rem;
            font-size: .75rem;
            font-weight: 600;
        }

        .kp-topbar .kp-logout-button {
            border-color: var(--kp-orange);
            background: var(--kp-orange);
            color: #fff;
        }

        .kp-topbar .kp-logout-button:hover,
        .kp-topbar .kp-logout-button:focus {
            border-color: #e96000;
            background: #e96000;
            color: #fff;
        }

        .kp-page-content {
            padding-top: 0;
            padding-bottom: 32px;
        }

        .scm-dashboard-kaprodi .topbar-actions {
            margin-left: auto;
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

        html.scm-theme-light .scm-dashboard {
            background: var(--scm-bg) !important;
            color: var(--scm-text) !important;
        }

        html.scm-theme-light .scm-dashboard .dashboard-sidebar {
            border-color: #e3e6e8;
            background: #ffffff;
        }

        html.scm-theme-light .scm-dashboard .sidebar-link {
            color: #59636b;
        }

        html.scm-theme-light .scm-dashboard .sidebar-link i {
            color: #7e878e;
        }

        html.scm-theme-light .scm-dashboard .sidebar-link:hover,
        html.scm-theme-light .scm-dashboard .sidebar-link.active {
            color: #ffffff;
        }

        html.scm-theme-light .scm-dashboard .sidebar-link:hover i,
        html.scm-theme-light .scm-dashboard .sidebar-link.active i {
            color: #ffffff;
        }

        html.scm-theme-light .scm-dashboard .sidebar-footer {
            border-color: #e3e6e8;
        }

        html.scm-theme-light .scm-dashboard .topbar {
            border-color: #e3e6e8 !important;
            background: #ffffff !important;
            box-shadow: 0 5px 18px rgba(35, 42, 47, .06);
        }

        html.scm-theme-light .scm-dashboard .topbar .btn-outline-light {
            border-color: #cbd2d7;
            color: #4e5961;
        }

        html.scm-theme-light .scm-dashboard .topbar .btn-outline-light:hover {
            border-color: #aeb7bd;
            background: #f1f3f4;
            color: #1c2024;
        }

        html.scm-theme-light .scm-dashboard .form-control,
        html.scm-theme-light .scm-dashboard .form-select,
        html.scm-theme-light .scm-dashboard .input-group-text,
        html.scm-theme-light .scm-dashboard .kp-control,
        html.scm-theme-light .scm-dashboard .kp-page-size select {
            border-color: #cfd6da !important;
            background-color: #ffffff !important;
            color: #283138 !important;
        }

        html.scm-theme-light .scm-dashboard .form-control::placeholder {
            color: #89939a !important;
            opacity: 1;
        }

        html.scm-theme-light .scm-dashboard input[type="date"] {
            color-scheme: light;
        }

        html.scm-theme-light .scm-dashboard main,
        html.scm-theme-light .scm-dashboard .dashboard-content {
            background: var(--scm-bg);
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

        .kp-page-heading p {
            margin: 0;
            color: var(--kp-muted);
            font-size: .88rem;
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

        .kp-context-badge {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 0 13px;
            border: 1px solid #bdc6d0;
            border-radius: 999px;
            background: var(--kp-surface);
            color: var(--kp-text);
            font-weight: 500;
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

        .kp-filter-card {
            position: relative;
            z-index: 20;
            overflow: visible;
            padding: 14px 18px 13px;
        }

        .kp-card.kp-filter-host {
            position: relative;
            z-index: 20;
            overflow: visible;
        }

        .kp-multi-filter {
            padding: 17px 18px;
            border-bottom: 1px solid var(--kp-border);
            background: var(--kp-surface);
        }

        .kp-filter-card .kp-multi-filter {
            padding: 0;
            border: 0;
        }

        .kp-multi-filter-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 14px;
        }

        .kp-multi-filter-heading h3 {
            margin: 0;
            color: var(--kp-text);
            font-size: .92rem;
            font-weight: 700;
        }

        .kp-multi-filter-heading h3 i { color: var(--kp-orange); }
        .kp-multi-filter-heading p { margin: 3px 0 0; color: var(--kp-muted); font-size: .69rem; }
        .kp-multi-filter-note { color: var(--kp-muted); font-size: .68rem; white-space: nowrap; }
        .kp-multi-filter-list { display: grid; gap: 9px; }
        .kp-multi-filter-row { display: grid; grid-template-columns: minmax(205px, .72fr) minmax(270px, 1.55fr) auto; align-items: center; gap: 9px; }
        .kp-multi-filter-row .form-select, .kp-multi-filter-row .form-control { min-height: 42px; border-color: #cbd3dc; background-color: var(--kp-surface); color: var(--kp-text); font-size: .74rem; box-shadow: none; }
        .kp-multi-filter-row .form-select:focus, .kp-multi-filter-row .form-control:focus { border-color: var(--kp-orange); box-shadow: 0 0 0 .2rem rgba(255, 107, 0, .12); }
        .kp-multi-filter-tools { display: flex; align-items: center; gap: 8px; }
        .kp-multi-filter-icon { width: 42px; height: 42px; display: inline-flex; flex: 0 0 42px; align-items: center; justify-content: center; padding: 0; border-radius: 50%; }
        .kp-multi-filter-add { border-color: var(--kp-orange); color: var(--kp-orange); }
        .kp-multi-filter-add:hover { border-color: var(--kp-orange); background: var(--kp-orange); color: #fff; }
        .kp-multi-filter-icon:disabled { opacity: .38; }

        .kp-filter-grid {
            display: grid;
            grid-template-columns: minmax(280px, 1.7fr) minmax(170px, .72fr) minmax(170px, .72fr) auto;
            gap: 10px;
            align-items: end;
        }

        .kp-field-label {
            display: block;
            margin-bottom: 6px;
            color: var(--kp-text);
            font-size: .66rem;
            font-weight: 700;
        }

        .kp-control {
            height: 40px;
            border-color: #cbd3dc;
            background-color: var(--kp-surface);
            color: var(--kp-text);
            font-size: .75rem;
            box-shadow: none !important;
        }

        .kp-control:focus {
            border-color: var(--kp-orange);
        }

        .kp-search-group .input-group-text {
            width: 42px;
            justify-content: center;
            border-color: #cbd3dc;
            background: var(--kp-surface);
            color: var(--kp-text);
        }

        .kp-search-group .form-control {
            border-left: 0;
        }

        .kp-filter-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .kp-filter-actions .btn {
            height: 40px;
            padding-inline: 14px;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .kp-btn-primary {
            border-color: var(--kp-orange);
            background: var(--kp-orange);
            color: #fff;
        }

        .kp-btn-primary:hover,
        .kp-btn-primary:focus {
            border-color: #e96000;
            background: #e96000;
            color: #fff;
        }

        .kp-btn-reset {
            border-color: #c6ced7;
            background: var(--kp-surface);
            color: var(--kp-muted);
        }

        .kp-filter-hint {
            margin-top: 7px;
            color: var(--kp-muted);
            font-size: .62rem;
        }

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

        .kp-return-table {
            min-width: 1180px;
        }

        .kp-return-table th:nth-child(1),
        .kp-return-table td:nth-child(1) {
            width: 64px;
            min-width: 64px;
            text-align: center;
        }

        .kp-return-table th:nth-child(2),
        .kp-return-table td:nth-child(2) { min-width: 260px; }
        .kp-return-table th:nth-child(3),
        .kp-return-table td:nth-child(3) { min-width: 300px; }
        .kp-return-table th:nth-child(4),
        .kp-return-table td:nth-child(4) { min-width: 300px; }
        .kp-return-table th:nth-child(5),
        .kp-return-table td:nth-child(5) { min-width: 260px; }

        .kp-number-cell {
            color: var(--kp-muted) !important;
            font-weight: 600;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
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

        .kp-toolbar {
            display: flex;
            gap: 9px;
            align-items: center;
            padding: 10px 18px 9px;
            border-bottom: 1px solid var(--kp-border);
            background: var(--kp-surface);
        }

        .kp-toolbar .kp-search-group {
            flex: 1 1 auto;
        }

        .kp-toolbar .btn {
            height: 40px;
            padding-inline: 16px;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 600;
        }

        .kp-toolbar-hint {
            padding: 0 19px 9px;
            border-bottom: 1px solid var(--kp-border);
            background: var(--kp-surface);
            color: var(--kp-muted);
            font-size: .61rem;
        }

        .kp-pagination-footer {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 14px;
            min-height: 52px;
            padding: 8px 14px;
            border-top: 1px solid var(--kp-border);
            background: var(--kp-surface-soft);
            color: var(--kp-muted);
            font-size: .64rem;
        }

        .kp-page-size {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .kp-page-size select {
            width: 62px;
            height: 31px;
            border-color: #cbd3dc;
            background-color: var(--kp-surface);
            color: var(--kp-text);
            font-size: .65rem;
        }

        .kp-page-status {
            text-align: center;
        }

        .kp-pagination-nav {
            display: flex;
            justify-content: flex-end;
        }

        .kp-pagination {
            display: inline-flex;
            overflow: hidden;
            margin: 0;
            padding: 0;
            border: 1px solid var(--kp-border);
            border-radius: 4px;
            background: var(--kp-surface);
            list-style: none;
        }

        .kp-page-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 32px;
            padding: 0 10px;
            border: 0;
            border-right: 1px solid var(--kp-border);
            background: var(--kp-surface);
            color: var(--kp-text);
            font-size: .67rem;
            text-decoration: none;
        }

        .kp-pagination li:last-child .kp-page-button {
            border-right: 0;
        }

        .kp-page-button.is-active {
            background: var(--kp-orange);
            color: #fff;
        }

        .kp-page-button:disabled,
        .kp-page-button.disabled {
            background: var(--kp-surface);
            color: #a7afb9;
            cursor: default;
            pointer-events: none;
        }

        .kp-loan-modal .modal-dialog {
            max-width: 760px;
        }

        .kp-loan-modal .modal-content {
            overflow: hidden;
            border: 1px solid var(--kp-border);
            border-radius: 13px;
            background: var(--kp-surface);
            color: var(--kp-text);
            box-shadow: 0 22px 60px rgba(15, 23, 42, .2);
        }

        .kp-loan-modal .modal-header {
            align-items: flex-start;
            padding: 19px 21px 17px;
            border-bottom-color: var(--kp-border);
        }

        .kp-modal-kicker {
            margin-bottom: 4px;
            color: var(--kp-muted);
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .kp-loan-modal .modal-title {
            color: var(--kp-text);
            font-size: 1rem;
            font-weight: 700;
        }

        .kp-modal-meta {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 14px;
            margin-top: 13px;
        }

        .kp-meta-box {
            padding: 11px 13px;
            border: 1px solid var(--kp-border);
            border-radius: 8px;
            background: var(--kp-surface-soft);
        }

        .kp-meta-label,
        .kp-modal-section-title {
            color: var(--kp-muted);
            font-size: .62rem;
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

        html.scm-theme-dark .kp-topbar {
            background: #15181d;
        }

        html.scm-theme-dark .kp-search-group .input-group-text,
        html.scm-theme-dark .kp-control,
        html.scm-theme-dark .kp-page-size select,
        html.scm-theme-dark .kp-loan-modal textarea {
            border-color: var(--kp-border);
            background-color: var(--kp-surface);
            color: var(--kp-text);
        }

        html.scm-theme-dark .kp-loan-modal .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        @media (max-width: 991.98px) {
            .kp-filter-grid {
                grid-template-columns: 1fr 1fr;
            }

            .kp-keyword-field,
            .kp-filter-actions {
                grid-column: 1 / -1;
            }

            .kp-filter-actions {
                justify-content: flex-end;
            }
        }

        @media (max-width: 767.98px) {
            .kp-multi-filter { padding: 14px; }
            .kp-multi-filter-heading { flex-direction: column; gap: 7px; }
            .kp-multi-filter-note { white-space: normal; }
            .kp-multi-filter-row { grid-template-columns: 1fr; gap: 8px; padding: 11px; border: 1px solid var(--kp-border); border-radius: 9px; }
            .kp-multi-filter-tools { justify-content: flex-end; }
            .topbar-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .topbar-actions .btn,
            .topbar-actions .notif-bell {
                flex: 0 0 auto;
            }

            .kp-page-content {
                padding-top: 16px;
            }

            .kp-page-heading,
            .kp-summary {
                align-items: flex-start;
            }

            .kp-page-heading > .row {
                gap: 12px;
            }

            .kp-context {
                justify-content: flex-start;
            }

            .kp-summary {
                flex-direction: column;
                padding: 15px;
            }

            .kp-filter-card,
            .kp-section-header {
                padding-inline: 14px;
            }

            .kp-filter-grid {
                grid-template-columns: 1fr;
            }

            .kp-keyword-field,
            .kp-filter-actions {
                grid-column: auto;
            }

            .kp-filter-actions {
                justify-content: stretch;
            }

            .kp-filter-actions .btn {
                flex: 1;
            }

            .kp-toolbar {
                align-items: stretch;
                flex-direction: column;
                padding-inline: 14px;
            }

            .kp-toolbar .btn {
                align-self: flex-end;
            }

            .kp-pagination-footer {
                grid-template-columns: 1fr;
                justify-items: center;
                padding-block: 12px;
            }

            .kp-page-size,
            .kp-pagination-nav {
                justify-content: center;
            }

            .kp-modal-meta {
                grid-template-columns: 1fr;
            }

            .kp-loan-modal .modal-header,
            .kp-loan-modal .modal-body,
            .kp-loan-modal .modal-footer {
                padding-inline: 16px;
            }

            .kp-loan-modal .modal-footer .btn {
                flex: 1 1 auto;
                min-width: 0;
            }
        }

        /* =========================================================
           POLISHED GLASS HEADER (MATCHING PEMINJAMAN_BARANG)
           ========================================================= */
        .glass-header-ifik {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(16px) !important;
            -webkit-backdrop-filter: blur(16px) !important;
            border-bottom: 1.5px solid rgba(226, 232, 240, 0.9) !important;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04) !important;
            position: sticky;
            top: 0;
            z-index: 999;
            padding-top: 14px !important;
            padding-bottom: 14px !important;
            min-height: 72px;
        }

        .header-inner-pad {
            padding-left: 56px;
            padding-right: 12px;
        }
        @media (min-width: 992px) {
            body:not(.curved-sidebar-desktop-collapsed) .header-inner-pad {
                padding-left: 12px;
                padding-right: 12px;
            }
            body.curved-sidebar-desktop-collapsed .header-inner-pad {
                padding-left: 56px;
                padding-right: 12px;
            }
        }

        /* Polished User Profile Button */
        .btn-ifik-profile {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 3px 14px 3px 4px;
            height: 40px;
            border-radius: 9999px;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            color: #1e293b;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }
        .btn-ifik-profile:hover,
        .btn-ifik-profile[aria-expanded="true"] {
            background: #ffffff;
            border-color: #fdba74;
            box-shadow: 0 6px 16px rgba(234, 88, 12, 0.12);
        }
        .btn-ifik-profile::after {
            display: none !important;
        }
        .profile-avatar-box {
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #ea580c, #f97316);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.88rem;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(234, 88, 12, 0.3);
        }
        .profile-text-group {
            display: flex;
            flex-direction: column;
            text-align: left;
            line-height: 1.2;
        }
        .profile-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1e293b;
            max-width: 260px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .profile-role {
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #ea580c;
        }
        .profile-chevron {
            font-size: 0.72rem;
            color: #94a3b8;
            transition: transform 0.25s ease, color 0.2s ease;
            margin-left: 2px;
        }
        .dropdown.show .profile-chevron,
        .btn-ifik-profile[aria-expanded="true"] .profile-chevron {
            transform: rotate(180deg);
            color: #ea580c;
        }

        /* Polished Dropdown Menu */
        .dropdown-menu-ifik {
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 18px !important;
            box-shadow: 0 20px 40px -8px rgba(15, 23, 42, 0.15) !important;
            padding: 8px !important;
            min-width: 230px !important;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(12px) !important;
        }
        .dropdown-menu-ifik .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #334155;
            transition: all 0.18s ease;
        }
        .dropdown-menu-ifik .dropdown-item:hover {
            background: #fff7ed;
            color: #ea580c;
            transform: translateX(2px);
        }
        .dropdown-menu-ifik .dropdown-item.text-danger:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        /* Dark mode overrides for glass header */
        html.scm-theme-dark .glass-header-ifik {
            background: rgba(30, 41, 59, 0.95) !important;
            border-bottom: 1.5px solid rgba(51, 65, 85, 0.9) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.2) !important;
        }
        html.scm-theme-dark .glass-header-ifik h1 {
            color: #f8fafc !important;
        }
        html.scm-theme-dark .btn-ifik-profile {
            background: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }
        html.scm-theme-dark .btn-ifik-profile:hover,
        html.scm-theme-dark .btn-ifik-profile[aria-expanded="true"] {
            background: #334155;
            border-color: #ea580c;
        }
        html.scm-theme-dark .profile-name {
            color: #f8fafc;
        }
        html.scm-theme-dark .dropdown-menu-ifik {
            background: rgba(30, 41, 59, 0.98) !important;
            border-color: #334155 !important;
        }
        html.scm-theme-dark .dropdown-menu-ifik .dropdown-item {
            color: #e2e8f0;
        }
        html.scm-theme-dark .dropdown-menu-ifik .dropdown-item:hover {
            background: rgba(234, 88, 12, 0.15);
            color: #fb923c;
        }
        html.scm-theme-dark .dropdown-menu-ifik .dropdown-item.text-danger:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }

        /* =========================================================
           UNIFIED SEARCH PILL & MULTI-FILTER (PEMINJAMAN_BARANG/RIWAYAT)
           ========================================================= */
        .unified-search-pill {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 3px 14px;
            height: 48px;
            transition: border-color 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
            position: relative;
        }
        .unified-search-pill:focus-within, .unified-search-pill.active {
            border-color: #ea580c !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.14), 0 10px 25px -5px rgba(234, 88, 12, 0.12) !important;
        }
        .unified-divider {
            width: 1px;
            height: 24px;
            background: #e2e8f0;
            margin: 0 10px;
        }
        .custom-dropdown-menu {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            transform-origin: top left;
        }
        .custom-dropdown-menu.hidden {
            display: none !important;
        }
        .dropdown-item-opt {
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 500;
            color: #334155;
            transition: background-color 0.15s ease, color 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dropdown-item-opt:hover, .dropdown-item-opt.active {
            background-color: #fff7ed;
            color: #ea580c;
            font-weight: 600;
        }
        .autocomplete-box {
            max-height: 340px;
            overflow-y: auto;
            border-radius: 18px;
            background: #ffffff !important;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.25), 0 8px 24px -4px rgba(234, 88, 12, 0.2) !important;
            z-index: 2050 !important;
            position: absolute !important;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .autocomplete-item-row {
            padding: 10px 16px;
            transition: all 0.18s ease;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
        }
        .autocomplete-item-row:last-child {
            border-bottom: none;
        }
        .autocomplete-item-row:hover, .autocomplete-item-row.active-nav {
            background-color: #fff7ed;
            color: #ea580c;
        }
        .autocomplete-item-row mark {
            background: #ffedd5;
            color: #ea580c;
            font-weight: 800;
            border-radius: 4px;
            padding: 0 3px;
        }
        .btn-standalone-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff7ed;
            border: 1.5px solid #ffedd5;
            border-radius: 16px;
            padding: 6px 14px;
            height: 48px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #ea580c;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(234, 88, 12, 0.06);
        }
        .btn-standalone-add:hover {
            background: #ffedd5;
            border-color: #fdba74;
            transform: scale(1.02);
        }
        .badge-standalone-count {
            background: #ea580c;
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 800;
            padding: 1.5px 8px;
            border-radius: 99px;
        }
        .btn-remove-row {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: #fff1f2;
            border: 1.5px solid #fecdd3;
            border-radius: 14px;
            color: #e11d48;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .btn-remove-row:hover {
            background: #ffe4e6;
            border-color: #fda4af;
            color: #be123c;
            transform: scale(1.05);
        }
        .extra-rows-card {
            display: none;
            position: relative;
            margin-top: 12px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 14px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.25s ease;
        }
        .extra-rows-card.open {
            display: block !important;
        }
        .extra-filter-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dropdown-arrow.rotate-180 {
            transform: rotate(180deg);
        }

        /* Dark mode overrides for search pill */
        html.scm-theme-dark .unified-search-pill {
            background: rgba(30, 41, 59, 0.95) !important;
            border-color: #334155 !important;
        }
        html.scm-theme-dark .unified-search-pill input {
            color: #f8fafc !important;
        }
        html.scm-theme-dark .unified-divider {
            background: #334155 !important;
        }
        html.scm-theme-dark .custom-dropdown-menu {
            background: #1e293b !important;
            border-color: #334155 !important;
        }
        html.scm-theme-dark .dropdown-item-opt {
            color: #e2e8f0 !important;
        }
        html.scm-theme-dark .dropdown-item-opt:hover, html.scm-theme-dark .dropdown-item-opt.active {
            background-color: rgba(234, 88, 12, 0.2) !important;
            color: #fb923c !important;
        }
        html.scm-theme-dark .btn-standalone-add {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #fb923c !important;
        }
        html.scm-theme-dark .extra-rows-card {
            background: #0f172a !important;
            border-color: #334155 !important;
        }
        html.scm-theme-dark .card.bg-white {
            background: #1e293b !important;
        }
        html.scm-theme-dark .autocomplete-box {
            background: #1e293b !important;
            border-color: #334155 !important;
        }
        html.scm-theme-dark .autocomplete-item-row {
            border-color: #334155 !important;
        }
        html.scm-theme-dark .autocomplete-item-row:hover, html.scm-theme-dark .autocomplete-item-row.active-nav {
            background-color: rgba(234, 88, 12, 0.2) !important;
        }
    </style>
</head>
<body class="scm-dashboard scm-dashboard-kaprodi kaprodi-loan-page">
<!-- SIDEBAR LAMA (DIKOMENTARI):
<aside class="dashboard-sidebar" aria-label="Navigasi Panel Kaprodi">
    <a class="sidebar-brand" href="<?= base_url('index.php/kaprodi/dashboard?tab=panel') ?>">
        <span class="sidebar-brand-mark"><i class="bi bi-building-check"></i></span>
        <span><strong>SCM FIK</strong><small>Panel Kaprodi</small></span>
    </a>
    <div class="sidebar-caption">Pengajuan prodi</div>
    <nav class="sidebar-nav">
        <a class="sidebar-link" href="<?= base_url('index.php/kaprodi/dashboard?tab=panel') ?>">
            <i class="bi bi-grid-1x2"></i><span>Panel</span>
        </a>
        <a class="sidebar-link" href="<?= base_url('index.php/kaprodi/dashboard?tab=ajukan') ?>">
            <i class="bi bi-plus-square"></i><span>Ajukan Kebutuhan</span>
        </a>
        <a class="sidebar-link" href="<?= base_url('index.php/kaprodi/dashboard?tab=riwayat') ?>">
            <i class="bi bi-clock-history"></i><span>Riwayat Pengajuan</span>
        </a>
        <a class="sidebar-link active" href="<?= site_url('prodi/peminjaman') ?>" aria-current="page">
            <i class="bi bi-patch-check"></i><span>Approval Peminjaman</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <span class="sidebar-status-dot"></span><span>System operational</span>
    </div>
</aside>
-->

<?php $this->load->view('components/curved_sidebar'); ?>

<div class="dashboard-content page-wrapper-for-sidebar">
<header class="glass-header-ifik mb-4">
    <div class="container-fluid px-3 px-lg-4 d-flex flex-wrap align-items-center justify-content-between gap-3 header-inner-pad">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 44px; height: 44px; background: rgba(234, 91, 26, 0.12); color: #ea5b1a; font-size: 1.35rem;">
                <i class="bi bi-patch-check"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="h5 fw-bold text-dark mb-0 tracking-tight">Approval Peminjaman Barang</h1>
                    <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(234, 91, 26, 0.12); color: #ea5b1a; font-weight: 700; font-size: 11px;"><?= html_escape($kaprodi_prodi ?: 'Program Studi') ?></span>
                </div>
                <p class="text-muted small mb-0 d-none d-sm-block" style="font-size: 12px;">Persetujuan dan pemantauan pengajuan peminjaman barang program studi · <?= tanggal_indonesia(date('Y-m-d')) ?></p>
            </div>
        </div>

        <!-- Profile Dropdown -->
        <div class="d-flex align-items-center gap-2 ms-auto">
            <div class="dropdown">
                <button class="btn-ifik-profile" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="profile-avatar-box">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="profile-text-group d-none d-sm-flex">
                        <span class="profile-name"><?= html_escape($this->session->userdata('nama') ?: ($this->session->userdata('username') ?: 'Program Studi')); ?></span>
                        <span class="profile-role"><?= html_escape($kaprodi_prodi ?: 'Program Studi'); ?></span>
                    </div>
                    <i class="bi bi-chevron-down profile-chevron"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-ifik mt-2">
                    <li class="px-3 py-2 border-bottom mb-1">
                        <span class="d-block text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Login Sebagai</span>
                        <span class="fw-bold text-dark d-block text-truncate" style="font-size: 13px;"><?= html_escape($this->session->userdata('username') ?: ($this->session->userdata('nama') ?: 'Program Studi')); ?></span>
                        <span class="badge rounded-pill mt-1" style="background: #fff7ed; color: #ea580c; font-size: 10px; font-weight: 700;"><?= html_escape($kaprodi_prodi ?: 'Program Studi'); ?></span>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('prodi/peminjaman') ?>">
                            <i class="bi bi-patch-check text-warning"></i>
                            <span>Approval Peminjaman</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('prodi/respon-ticketing') ?>">
                            <i class="bi bi-ticket-perforated text-primary"></i>
                            <span>Respon Ticketing</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('prodi/ticketing/input') ?>">
                            <i class="bi bi-plus-circle text-success"></i>
                            <span>Membuat Ticketing</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item text-danger fw-bold" href="<?= site_url('login/logout') ?>">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Keluar</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<main class="container-fluid kp-page-content px-3 px-lg-4">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= html_escape($this->session->flashdata('success')) ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= html_escape($this->session->flashdata('error')) ?></div>
    <?php endif; ?>

    <section class="kp-card kp-summary">
        <div><h2 class="kp-section-title">Progress Seluruh Peminjaman</h2><p class="kp-section-copy mt-1">Semua tahap dapat dipantau; aksi hanya tersedia saat menunggu Kaprodi.</p></div>
        <span class="kp-count-badge">
            <span class="kp-status-dot"></span>
            <span data-bulk-actionable-count><?= $kaprodi_actionable ?></span> perlu aksi · <?= number_format($approval_total, 0, ',', '.') ?> total
        </span>
    </section>

    <!-- Unified Multi-Search Bar (Model Pemesanan & Riwayat Peminjaman Barang) -->
    <?php
        $cat_labels = [
            'all'      => '🔍 Semua Pengajuan',
            'barang'   => '📦 Nama Barang / Kode',
            'peminjam' => '👤 Peminjam / NIM',
            'number'   => '📄 No. Peminjaman',
            'lab'      => '🏛️ Laboratorium',
            'status'   => '⏳ Status Approval',
            'masa'     => '📅 Masa Pinjam'
        ];
        $cat_placeholders = [
            'all'      => 'Cari nama peminjam, barang, kode aset, status, atau nomor...',
            'barang'   => 'Cari nama barang atau kode aset...',
            'peminjam' => 'Cari nama peminjam atau NIM/NIP...',
            'number'   => 'Cari nomor peminjaman / group ID...',
            'lab'      => 'Cari laboratorium / studio...',
            'status'   => 'Cari status approval...',
            'masa'     => 'Cari tanggal pinjam (YYYY-MM-DD)...'
        ];
        $current_main_cat = $filter_rows[0]['field'] ?? 'all';
        if (!isset($cat_labels[$current_main_cat])) $current_main_cat = 'all';
        $current_main_val = $filter_rows[0]['value'] ?? '';
        $extra_rows = array_slice($filter_rows ?? [], 1);
        $total_active_rows = 1 + count($extra_rows);
    ?>
    <div class="card border-0 shadow-sm p-3 mb-4 rounded-4 position-relative bg-white" style="position: relative; z-index: 1050;">
        <form action="<?= site_url('prodi/peminjaman'); ?>" method="GET" id="formSearchApproval" class="position-relative" style="position: relative; z-index: 1051;">
            <input type="hidden" name="page" value="1">
            <input type="hidden" name="per_page" value="<?= (int)$per_page; ?>">
            <input type="hidden" name="sort_by" value="<?= html_escape($approval_sort ?? ''); ?>">
            <input type="hidden" name="sort_dir" value="<?= html_escape($approval_dir ?? 'desc'); ?>">
            <input type="hidden" name="filter_field[]" id="mainCategorySelectApproval" value="<?= html_escape($current_main_cat); ?>">
            
            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                <!-- Main Search Pill -->
                <div class="unified-search-pill flex-grow-1 d-flex align-items-center justify-content-between min-w-0" id="mainSearchPillApproval">
                    <!-- Main Category Selector Dropdown -->
                    <div class="position-relative flex-shrink-0">
                        <button type="button" onclick="toggleApprovalCustomDropdown('main-cat', event)" class="d-flex align-items-center gap-1 bg-transparent border-0 text-dark fw-bold cursor-pointer py-1 px-1" style="font-size: 0.8rem;">
                            <span id="label-filter-main-cat" class="text-truncate" style="max-width: 140px;">
                                <?= $cat_labels[$current_main_cat] ?? '🔍 Semua Pengajuan'; ?>
                            </span>
                            <i class="fa-solid fa-chevron-down text-secondary ms-1 dropdown-arrow transition-all" id="arrow-filter-main-cat" style="font-size: 9px;"></i>
                        </button>
                        <div id="menu-filter-main-cat" class="custom-dropdown-menu hidden position-absolute top-100 start-0 mt-2 bg-white border border-light-subtle rounded-3 shadow-lg p-1" style="width: 220px; z-index: 1050;">
                            <?php foreach($cat_labels as $ck => $clabel): ?>
                                <div onclick="selectApprovalMainCategory('<?= $ck ?>', '<?= htmlspecialchars($clabel, ENT_QUOTES) ?>', '<?= htmlspecialchars($cat_placeholders[$ck], ENT_QUOTES) ?>', this)" 
                                     class="dropdown-item-opt <?= $current_main_cat === $ck ? 'active' : '' ?>">
                                    <span><?= $clabel ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="unified-divider flex-shrink-0"></div>

                    <!-- Input Text Container -->
                    <div id="mainValueContainerApproval" class="flex-grow-1 d-flex align-items-center min-w-0 px-1">
                        <i id="searchIconApproval" class="fa-solid fa-magnifying-glass text-secondary me-2 flex-shrink-0" style="font-size: 13px;"></i>
                        <input type="text" name="filter_value[]" id="inputSearchApproval" autocomplete="off" value="<?= html_escape($current_main_val); ?>" 
                               placeholder="<?= html_escape($cat_placeholders[$current_main_cat] ?? $cat_placeholders['all']); ?>" 
                               class="form-control border-0 shadow-none bg-transparent p-0 text-dark fw-semibold" style="font-size: 0.82rem;">
                        
                        <button type="button" id="btnClearSearchApproval" onclick="clearApprovalSearch()" class="<?= empty($current_main_val) ? 'd-none' : ''; ?> btn btn-link p-0 text-secondary text-decoration-none me-2" title="Hapus pencarian">
                            <i class="fa-solid fa-circle-xmark fs-6"></i>
                        </button>
                    </div>

                    <!-- Tombol Cari -->
                    <button type="submit" id="btnSubmitSearchApproval" class="btn text-white fw-bold d-flex align-items-center gap-1 rounded-3 px-3 py-1 flex-shrink-0 shadow-sm" style="background: linear-gradient(135deg, #ea580c, #f97316); font-size: 0.78rem;">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 11px;"></i>
                        <span class="d-none d-sm-inline">Cari</span>
                    </button>
                </div>

                <!-- Standalone Add Filter Button (+ 1/4) -->
                <button type="button" id="standaloneAddBtnApproval" onclick="toggleApprovalMultiFilter(event)" class="btn-standalone-add flex-shrink-0 justify-content-center" title="Buka / Tambah Filter Baru (Maks 4)">
                    <i class="fa-solid fa-plus fs-6"></i>
                    <span id="filterCountBadgeApproval" class="badge-standalone-count"><?= min(4, max(1, $total_active_rows)) ?>/4</span>
                </button>
            </div>

            <!-- Autocomplete Dropdown -->
            <div id="autocompleteDropdownApproval" class="d-none position-absolute start-0 end-0 top-100 mt-2 bg-white border border-light-subtle rounded-4 shadow-lg overflow-hidden autocomplete-box" style="z-index: 1060;">
                <div id="autocompleteResultsApproval" class="p-0"></div>
            </div>

            <!-- Extra Filter Rows Card Popover -->
            <div id="extraRowsCardApproval" class="extra-rows-card <?= !empty($extra_rows) ? 'open' : '' ?>">
                <div id="additionalFilterRowsContainerApproval" class="d-flex flex-column gap-2 mb-2">
                    <?php if(!empty($extra_rows)): ?>
                        <?php foreach($extra_rows as $idx => $erow): 
                            $erow_id = 'extra-row-' . ($idx + 1);
                            $erow_cat = $erow['field'] ?? 'barang';
                            $erow_val = $erow['value'] ?? '';
                        ?>
                            <div class="extra-filter-row" id="<?= $erow_id ?>">
                                <input type="hidden" name="filter_field[]" id="field-<?= $erow_id ?>" value="<?= html_escape($erow_cat) ?>">
                                <div class="unified-search-pill flex-grow-1 d-flex align-items-center justify-content-between min-w-0" style="height: 44px;">
                                    <div class="position-relative flex-shrink-0">
                                        <button type="button" onclick="toggleApprovalCustomDropdown('<?= $erow_id ?>', event)" class="d-flex align-items-center gap-1 bg-transparent border-0 text-dark fw-bold cursor-pointer py-1 px-1" style="font-size: 0.8rem;">
                                            <span id="label-filter-<?= $erow_id ?>" class="text-truncate" style="max-width: 140px;"><?= $cat_labels[$erow_cat] ?? '📦 Nama Barang / Kode' ?></span>
                                            <i class="fa-solid fa-chevron-down text-secondary ms-1 dropdown-arrow transition-all" id="arrow-filter-<?= $erow_id ?>" style="font-size: 9px;"></i>
                                        </button>
                                        <div id="menu-filter-<?= $erow_id ?>" class="custom-dropdown-menu hidden position-absolute top-100 start-0 mt-2 bg-white border border-light-subtle rounded-3 shadow-lg p-1" style="width: 220px; z-index: 1050;">
                                            <?php foreach($cat_labels as $ck => $clabel): ?>
                                                <div onclick="selectApprovalExtraCategory('<?= $erow_id ?>', '<?= $ck ?>', '<?= htmlspecialchars($clabel, ENT_QUOTES) ?>', '<?= htmlspecialchars($cat_placeholders[$ck], ENT_QUOTES) ?>', this)" 
                                                     class="dropdown-item-opt <?= $erow_cat === $ck ? 'active' : '' ?>">
                                                    <span><?= $clabel ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="unified-divider flex-shrink-0"></div>
                                    <div class="flex-grow-1 d-flex align-items-center min-w-0 px-1">
                                        <i class="fa-solid fa-magnifying-glass text-secondary me-2 flex-shrink-0" style="font-size: 12px;"></i>
                                        <input type="text" name="filter_value[]" value="<?= html_escape($erow_val) ?>" placeholder="<?= html_escape($cat_placeholders[$erow_cat] ?? '') ?>" class="form-control border-0 shadow-none bg-transparent p-0 text-dark fw-semibold extra-row-input" style="font-size: 0.82rem;">
                                    </div>
                                </div>
                                <button type="button" onclick="removeApprovalFilterRow(this)" class="btn-remove-row" title="Hapus Kriteria Ini">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex align-items-center justify-content-between border-top border-light-subtle pt-2 mt-2" style="font-size: 0.78rem;">
                    <span class="text-muted"><i class="fa-solid fa-circle-info me-1"></i>Gunakan kombinasi kriteria untuk mempersempit pencarian pengajuan peminjaman.</span>
                    <button type="button" onclick="resetApprovalMultiSearch()" class="btn btn-link btn-sm text-danger fw-bold text-decoration-none p-0">
                        Reset All Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <section class="kp-card kp-table-card mb-3" aria-labelledby="kaprodiApprovalTitle" data-bulk-approval>
        <div class="kp-section-header">
            <h2 id="kaprodiApprovalTitle" class="kp-section-title">Pengajuan Menunggu ACC</h2>
        </div>
        <?php if (!empty($pengajuan)): ?>
        <form id="kaprodiBulkForm" method="post" action="<?= site_url('prodi/peminjaman/bulk') ?>" class="approval-bulk-toolbar m-3" data-bulk-form data-bulk-toolbar hidden>
            <input type="hidden" name="bulk_note" value="">
            <span class="approval-bulk-toolbar__count" data-bulk-count>0 data terpilih</span>
            <button type="submit" name="action" value="approve" class="btn btn-sm btn-success rounded-pill px-3" data-bulk-approve-action><i class="bi bi-check2-circle me-1"></i>ACC Terpilih</button>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#kaprodiBulkRejectModal"><i class="bi bi-x-circle me-1"></i>Tolak Terpilih</button>
            <button type="submit" name="action" value="reject" data-bulk-reject-action hidden></button>
        </form>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table kp-table">
                <thead>
                <tr>
                    <th scope="col" class="approval-bulk-cell"><label class="approval-bulk-select-all" title="Pilih semua data yang dapat di-ACC pada halaman ini"><input type="checkbox" class="form-check-input approval-bulk-check" data-bulk-select-all <?= $kaprodi_page_actionable > 0 ? '' : 'disabled' ?>><span>Pilih</span></label></th>
                    <?php foreach (['number' => 'No. Peminjaman', 'peminjam' => 'Nama Peminjam', 'barang' => 'Barang', 'lab' => 'Laboratorium', 'masa' => 'Masa Pinjam', 'status' => 'Status'] as $sort_key => $sort_label): ?>
                    <th scope="col" aria-sort="<?= scm_sort_aria($sort_key, $approval_sort, $approval_dir) ?>"><a class="kp-sort-button scm-sort-control <?= $approval_sort === $sort_key ? 'is-active' : '' ?>" href="<?= scm_sort_url($sort_key, $approval_sort, $approval_dir) ?>"><?= html_escape($sort_label) ?><i class="bi <?= scm_sort_icon_class($sort_key, $approval_sort, $approval_dir) ?>" aria-hidden="true"></i></a></th>
                    <?php endforeach; ?>
                    <th scope="col" class="text-end">Aksi</th>
                </tr>
                </thead>
                <tbody id="kaprodiApprovalBody">
                <?php if (empty($pengajuan)): ?>
                    <tr class="kp-empty-row">
                        <td colspan="8">Belum ada data peminjaman.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pengajuan as $p): ?>
                        <?php
                        $number = $p->group_id ?? $p->id_peminjaman ?? '-';
                        $item_names = [];
                        $item_search = [];
                        $laboratories = [];
                        foreach (($p->detail_barang ?? []) as $detail) {
                            $item_names[] = $detail->nama_aset ?? '-';
                            $item_search[] = trim((string) ($detail->nama_aset ?? '') . ' ' . (string) ($detail->kode_aset ?? ''));
                            if (!empty($detail->nama_ruangan)) {
                                $laboratories[] = $detail->nama_ruangan;
                            }
                        }
                        $laboratories = array_values(array_unique($laboratories));
                        $period = masa_pinjam_indonesia($p->tanggal_pinjam ?? null, $p->tanggal_kembali_rencana ?? null);
                        $status = $p->status ?? '-';
                        $can_kaprodi_review = scm_loan_can_act($p, 'kaprodi');
                        $kaprodi_stock_status = $kaprodi_stock_readiness($p);
                        $can_kaprodi_act = $can_kaprodi_review && $kaprodi_stock_status['ready'];
                        $kaprodi_action_disabled_reason = $can_kaprodi_review
                            ? $kaprodi_stock_status['message']
                            : 'Belum berada pada tahap Kaprodi';
                        $search_text = implode(' ', [
                            $number,
                            $p->id_peminjaman ?? '',
                            $p->nama_peminjam ?? '',
                            $p->nim_nip ?? '',
                            implode(' ', $item_names),
                            implode(' ', $laboratories),
                            $period,
                            $status,
                        ]);
                        ?>
                        <tr
                            data-approval-row
                            data-search="<?= html_escape($search_text) ?>"
                            data-date-start="<?= html_escape(substr((string) ($p->tanggal_pinjam ?? ''), 0, 10)) ?>"
                            data-filter-number="<?= html_escape(trim((string) $number . ' ' . (string) ($p->id_peminjaman ?? ''))) ?>"
                            data-filter-peminjam="<?= html_escape(trim((string) ($p->nama_peminjam ?? '') . ' ' . (string) ($p->nim_nip ?? ''))) ?>"
                            data-filter-barang="<?= html_escape(implode(' ', $item_search)) ?>"
                            data-filter-lab="<?= html_escape(implode(' ', $laboratories)) ?>"
                            data-filter-masa="<?= html_escape(implode(' ', [substr((string) ($p->tanggal_pinjam ?? ''), 0, 10), substr((string) ($p->tanggal_kembali_rencana ?? ''), 0, 10), $period])) ?>"
                            data-filter-status="<?= html_escape($status) ?>"
                            data-sort-number="<?= html_escape($number) ?>"
                            data-sort-peminjam="<?= html_escape($p->nama_peminjam ?? '') ?>"
                            data-sort-barang="<?= html_escape(implode(' ', $item_names)) ?>"
                            data-sort-lab="<?= html_escape(implode(' ', $laboratories)) ?>"
                            data-sort-masa="<?= (int) strtotime((string) ($p->tanggal_pinjam ?? '')) ?>"
                            data-sort-status="<?= html_escape($status) ?>"
                        >
                            <td class="approval-bulk-cell"><input type="checkbox" class="form-check-input approval-bulk-check" name="loan_ids[]" value="<?= (int) $p->id_peminjaman ?>" form="kaprodiBulkForm" data-bulk-row aria-label="Pilih peminjaman <?= (int) $p->id_peminjaman ?>" <?= $can_kaprodi_act ? '' : 'disabled title="' . html_escape($kaprodi_action_disabled_reason) . '"' ?>></td>
                            <td>
                                <div class="kp-primary-text"><?= html_escape($number) ?></div>
                                <div class="kp-secondary-text">ID <?= html_escape($p->id_peminjaman ?? '-') ?></div>
                            </td>
                            <td>
                                <div class="kp-primary-text"><?= html_escape($p->nama_peminjam ?? '-') ?></div>
                                <div class="kp-secondary-text"><?= html_escape($p->nim_nip ?? '-') ?></div>
                            </td>
                            <td>
                                <div class="kp-item-list">
                                    <?php if (empty($p->detail_barang)): ?>
                                        <span>-</span>
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
                            <td data-bulk-status><?php $loan_progress_item = $p; $loan_progress_compact = true; include APPPATH . 'views/shared/loan_progress.php'; ?></td>
                            <td class="text-end">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary kp-detail-button"
                                    data-bulk-action
                                    data-bs-toggle="modal"
                                    data-bs-target="#kaprodiApproval<?= (int) $p->id_peminjaman ?>"
                                >
                                    <i class="bi bi-sliders me-1"></i>Proses
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr id="kaprodiApprovalEmpty" class="kp-empty-row d-none">
                        <td colspan="8">Tidak ada hasil yang sesuai dengan filter.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($pengajuan)): ?>
            <div class="kp-pagination-footer">
                <div class="kp-page-size">
                    <label for="kaprodiApprovalPageSize">Tampilkan:</label>
                    <select id="kaprodiApprovalPageSize" class="form-select form-select-sm" onchange="var u=new URL(window.location.href);u.searchParams.set('per_page',this.value);u.searchParams.set('page','1');window.location.href=u.toString();">
                        <?php foreach ([10, 25] as $size): ?><option value="<?= $size ?>" <?= $per_page === $size ? 'selected' : '' ?>><?= $size ?></option><?php endforeach; ?>
                    </select>
                    <span id="kaprodiApprovalTotal">Total item: <?= number_format($approval_total, 0, ',', '.') ?></span>
                </div>
                <?php $first_item = $approval_total > 0 ? (($page - 1) * $per_page) + 1 : 0; $last_item = min($approval_total, $page * $per_page); ?>
                <div id="kaprodiApprovalPageStatus" class="kp-page-status">Menampilkan <?= $first_item ?>–<?= $last_item ?> dari <?= number_format($approval_total, 0, ',', '.') ?> data</div>
                <nav class="kp-pagination-nav" aria-label="Paging pengajuan">
                    <ul id="kaprodiApprovalPagination" class="kp-pagination">
                        <?php $kp_query = $_GET; $kp_query['per_page'] = $per_page; $kp_query['page'] = max(1, $page - 1); ?><li><a class="kp-page-button <?= $page <= 1 ? 'disabled' : '' ?>" href="<?= current_url() . '?' . http_build_query($kp_query) ?>">Previous</a></li>
                        <?php foreach (scm_pagination_tokens($page, $total_pages) as $token): ?><?php if (is_string($token)): ?><li><span class="kp-page-button disabled">...</span></li><?php else: $kp_query['page'] = $token; ?><li><a class="kp-page-button <?= $token === $page ? 'is-active' : '' ?>" href="<?= current_url() . '?' . http_build_query($kp_query) ?>"><?= $token ?></a></li><?php endif; ?><?php endforeach; $kp_query['page'] = min($total_pages, $page + 1); ?>
                        <li><a class="kp-page-button <?= $page >= $total_pages ? 'disabled' : '' ?>" href="<?= current_url() . '?' . http_build_query($kp_query) ?>">Next</a></li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
        <div class="modal fade" id="kaprodiBulkRejectModal" tabindex="-1" aria-labelledby="kaprodiBulkRejectTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                <div class="modal-header"><h2 class="modal-title h5 fw-bold" id="kaprodiBulkRejectTitle">Tolak Peminjaman Terpilih</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body"><label for="kaprodiBulkRejectReason" class="form-label fw-semibold">Alasan penolakan</label><textarea id="kaprodiBulkRejectReason" class="form-control" rows="4" placeholder="Alasan berlaku untuk seluruh data terpilih." data-bulk-reject-reason></textarea></div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger rounded-pill px-4" data-bulk-reject-submit>Tolak Terpilih</button></div>
            </div></div>
        </div>
    </section>

    <section class="kp-card kp-filter-host" aria-labelledby="kaprodiReturnTitle">
        <div class="kp-section-header">
            <h2 id="kaprodiReturnTitle" class="kp-section-title">Status Pengembalian (Read-only)</h2>
        </div>
        <?php if (!empty($pengembalian)): ?>
            <?php render_kaprodi_client_filter('return', 'kaprodiReturnFilters'); ?>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table kp-table kp-return-table">
                <thead>
                <tr>
                    <th scope="col">No</th>
                    <?php foreach (['peminjam' => 'Peminjam', 'barang' => 'Nama Barang', 'masa' => 'Masa Pinjam', 'status' => 'Status Pengembalian'] as $sort_key => $sort_label): ?>
                    <th scope="col" aria-sort="<?= scm_sort_aria($sort_key, $return_sort, $return_dir) ?>"><a class="kp-sort-button scm-sort-control <?= $return_sort === $sort_key ? 'is-active' : '' ?>" href="<?= scm_sort_url($sort_key, $return_sort, $return_dir, 'return_sort', 'return_dir', 'return_page') ?>"><?= html_escape($sort_label) ?><i class="bi <?= scm_sort_icon_class($sort_key, $return_sort, $return_dir) ?>" aria-hidden="true"></i></a></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody id="kaprodiReturnBody">
                <?php if (empty($pengembalian)): ?>
                    <tr class="kp-empty-row">
                        <td colspan="5">Belum ada data pengembalian.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pengembalian as $return_index => $p): ?>
                        <?php
                        $item_names = [];
                        foreach (($p->detail_barang ?? []) as $detail) {
                            $item_names[] = $detail->nama_aset ?? '-';
                        }
                        $period = masa_pinjam_indonesia($p->tanggal_pinjam ?? null, $p->tanggal_kembali_rencana ?? null);
                        $status = $p->status ?? '-';
                        $search_text = implode(' ', [
                            $p->nama_peminjam ?? '',
                            $p->nim_nip ?? '',
                            implode(' ', $item_names),
                            $period,
                            $status,
                        ]);
                        ?>
                        <tr
                            data-return-row
                            data-search="<?= html_escape($search_text) ?>"
                            data-filter-peminjam="<?= html_escape(trim((string) ($p->nama_peminjam ?? '') . ' ' . (string) ($p->nim_nip ?? ''))) ?>"
                            data-filter-barang="<?= html_escape(implode(' ', $item_names)) ?>"
                            data-filter-masa="<?= html_escape(implode(' ', [substr((string) ($p->tanggal_pinjam ?? ''), 0, 10), substr((string) ($p->tanggal_kembali_rencana ?? ''), 0, 10), $period])) ?>"
                            data-filter-status="<?= html_escape($status) ?>"
                            data-sort-peminjam="<?= html_escape($p->nama_peminjam ?? '') ?>"
                            data-sort-barang="<?= html_escape(implode(' ', $item_names)) ?>"
                            data-sort-masa="<?= (int) strtotime((string) ($p->tanggal_pinjam ?? '')) ?>"
                            data-sort-status="<?= html_escape($status) ?>"
                        >
                            <td class="kp-number-cell" data-row-number><?= (($return_page - 1) * $per_page) + (int) $return_index + 1 ?></td>
                            <td>
                                <div class="kp-primary-text"><?= html_escape($p->nama_peminjam ?? '-') ?></div>
                                <div class="kp-secondary-text"><?= html_escape($p->nim_nip ?? '-') ?></div>
                            </td>
                            <td><?= html_escape(implode(', ', $item_names) ?: '-') ?></td>
                            <td><?= html_escape($period) ?></td>
                            <td>
                                <span class="kp-status-badge <?= kaprodi_loan_status_tone($status) ?>">
                                    <span class="kp-status-dot"></span><?= html_escape($status) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr id="kaprodiReturnEmpty" class="kp-empty-row d-none">
                        <td colspan="5">Tidak ada hasil yang sesuai dengan pencarian.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($pengembalian)): ?>
            <div class="kp-pagination-footer">
                <div class="kp-page-size">
                    <label for="kaprodiReturnPageSize">Tampilkan:</label>
                    <select id="kaprodiReturnPageSize" class="form-select form-select-sm" onchange="var u=new URL(window.location.href);u.searchParams.set('per_page',this.value);u.searchParams.set('return_page','1');window.location.href=u.toString();">
                        <?php foreach ([10, 25] as $size): ?><option value="<?= $size ?>" <?= $per_page === $size ? 'selected' : '' ?>><?= $size ?></option><?php endforeach; ?>
                    </select>
                    <span id="kaprodiReturnTotal">Total item: <?= number_format($return_total, 0, ',', '.') ?></span>
                </div>
                <?php $return_first = $return_total > 0 ? (($return_page - 1) * $per_page) + 1 : 0; $return_last = min($return_total, $return_page * $per_page); ?>
                <div id="kaprodiReturnPageStatus" class="kp-page-status">Menampilkan <?= $return_first ?>–<?= $return_last ?> dari <?= number_format($return_total, 0, ',', '.') ?> data</div>
                <nav class="kp-pagination-nav" aria-label="Paging status pengembalian">
                    <ul id="kaprodiReturnPagination" class="kp-pagination">
                        <?php $return_query = $_GET; $return_query['per_page'] = $per_page; $return_query['return_page'] = max(1, $return_page - 1); ?><li><a class="kp-page-button <?= $return_page <= 1 ? 'disabled' : '' ?>" href="<?= current_url() . '?' . http_build_query($return_query) ?>">Previous</a></li>
                        <?php foreach (scm_pagination_tokens($return_page, $return_total_pages) as $token): ?><?php if (is_string($token)): ?><li><span class="kp-page-button disabled">...</span></li><?php else: $return_query['return_page'] = $token; ?><li><a class="kp-page-button <?= $token === $return_page ? 'is-active' : '' ?>" href="<?= current_url() . '?' . http_build_query($return_query) ?>"><?= $token ?></a></li><?php endif; ?><?php endforeach; $return_query['return_page'] = min($return_total_pages, $return_page + 1); ?>
                        <li><a class="kp-page-button <?= $return_page >= $return_total_pages ? 'disabled' : '' ?>" href="<?= current_url() . '?' . http_build_query($return_query) ?>">Next</a></li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </section>
</main>
</div>

<?php foreach ($pengajuan as $p): ?>
    <?php
    $number = $p->group_id ?? $p->id_peminjaman ?? '-';
    $status = $p->status ?? '-';
    $can_kaprodi_review = scm_loan_can_act($p, 'kaprodi');
    $kaprodi_stock_status = $kaprodi_stock_readiness($p);
    $can_kaprodi_act = $can_kaprodi_review && $kaprodi_stock_status['ready'];
    $modal_total_units = 0;
    foreach (($p->detail_barang ?? []) as $modal_detail) $modal_total_units += (int) ($modal_detail->jumlah_pinjam ?? 0);
    $modal_loan_days = durasi_pinjam_hari($p->tanggal_pinjam ?? null, $p->tanggal_kembali_rencana ?? null);
    ?>
    <div
        class="modal fade kp-loan-modal"
        id="kaprodiApproval<?= (int) $p->id_peminjaman ?>"
        tabindex="-1"
        aria-labelledby="kaprodiApprovalTitle<?= (int) $p->id_peminjaman ?>"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form
                method="post"
                class="modal-content"
                action="<?= site_url('prodi/peminjaman/setujui/' . $p->id_peminjaman) ?>"
            >
                <div class="modal-header">
                    <div class="w-100 pe-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <div class="kp-modal-kicker">Peminjaman</div>
                                <h2 id="kaprodiApprovalTitle<?= (int) $p->id_peminjaman ?>" class="modal-title">
                                    <?= html_escape($number) ?>
                                </h2>
                            </div>
                            <span class="kp-status-badge <?= kaprodi_loan_status_tone($status) ?>">
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
                                <div class="kp-meta-value"><?= count((array) ($p->detail_barang ?? [])) ?> jenis / <?= $modal_total_units ?> unit</div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <section class="kp-modal-section">
                        <div class="kp-modal-section-title">Progress Peminjaman</div>
                        <?php $loan_progress_item = $p; $loan_progress_compact = false; include APPPATH . 'views/shared/loan_progress.php'; ?>
                        <?php if (!$can_kaprodi_review): ?><div class="alert alert-info small mt-3 mb-0">Data ini ditampilkan untuk pemantauan. Tombol persetujuan hanya aktif saat status menunggu Kaprodi.</div><?php elseif (!$kaprodi_stock_status['ready']): ?><div class="alert alert-warning small mt-3 mb-0"><i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i><?= html_escape($kaprodi_stock_status['message']) ?> Persetujuan tidak dapat diteruskan sebelum stok tersedia.</div><?php endif; ?>
                    </section>
                    <section class="kp-modal-section">
                        <div class="kp-modal-section-title">Keperluan</div>
                        <p class="kp-purpose"><?= nl2br(html_escape($p->keperluan ?? '-')) ?></p>
                    </section>

                    <section class="kp-modal-section">
                        <div class="kp-modal-section-title">Detail Barang</div>
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
                                    <tr><td colspan="4" class="text-center">Detail barang tidak tersedia.</td></tr>
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
                        <label for="kaprodiNote<?= (int) $p->id_peminjaman ?>" class="kp-modal-section-title">Catatan ACC Kaprodi</label>
                        <textarea
                            id="kaprodiNote<?= (int) $p->id_peminjaman ?>"
                            name="catatan_kaprodi"
                            class="form-control"
                            rows="3"
                            placeholder="Catatan persetujuan atau alasan penolakan"
                            <?= $can_kaprodi_review ? '' : 'disabled'; ?>
                        ></textarea>
                    </section>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button
                        type="submit"
                        formaction="<?= site_url('prodi/peminjaman/tolak/' . $p->id_peminjaman) ?>"
                        class="btn btn-outline-danger"
                        <?= $can_kaprodi_review ? '' : 'disabled'; ?>
                    >
                        <i class="bi bi-x-lg me-1"></i>Tolak
                    </button>
                    <button
                        type="submit"
                        class="btn btn-success"
                        <?= $can_kaprodi_act ? '' : 'disabled'; ?>
                    >
                        <i class="bi bi-check2 me-1"></i>Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/loan-progress.js'); ?>?v=<?= @filemtime(FCPATH . 'assets/js/loan-progress.js'); ?>"></script>
<script src="<?= base_url('assets/js/approval-bulk-select.js'); ?>?v=<?= @filemtime(FCPATH . 'assets/js/approval-bulk-select.js'); ?>"></script>
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

        function notify() {
            root.dispatchEvent(new CustomEvent('kp:filterchange'));
        }

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
            var serverNames = root.closest('form') ? ' name="filter_field[]"' : '';
            var serverValueName = root.closest('form') ? ' name="filter_value[]"' : '';
            row.innerHTML =
                '<select class="form-select kp-multi-filter-field"' + serverNames + ' aria-label="Jenis filter ' + (rows.length + 1) + '">' + sourceSelect.innerHTML + '</select>' +
                '<input type="search" class="form-control kp-multi-filter-value"' + serverValueName + ' autocomplete="off" aria-label="Nilai filter ' + (rows.length + 1) + '">' +
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
                notify();
            }
        });
        list.addEventListener('change', function (event) {
            if (!event.target.matches('.kp-multi-filter-field')) return;
            var row = event.target.closest('[data-filter-row]');
            syncInput(row, true);
            row.querySelector('.kp-multi-filter-value').focus();
            notify();
        });
        list.addEventListener('input', function (event) {
            if (event.target.matches('.kp-multi-filter-value')) notify();
        });
        root.querySelector('[data-kp-filter-apply]')?.addEventListener('click', notify);
        root.querySelector('[data-kp-filter-reset]')?.addEventListener('click', function () {
            var rows = Array.prototype.slice.call(list.querySelectorAll('[data-filter-row]'));
            rows.slice(1).forEach(function (row) { row.remove(); });
            if (rows[0]) {
                var select = rows[0].querySelector('.kp-multi-filter-field');
                if (select) select.selectedIndex = 0;
                syncInput(rows[0], true);
            }
            updateButtons();
            notify();
        });
        return root;
    }

    initMultiFilter('kaprodiApprovalFilters');
    initMultiFilter('kaprodiReturnFilters');
});

// =========================================================
// SEARCH PILL & AUTOCOMPLETE (PEMINJAMAN_BARANG/RIWAYAT SYSTEM)
// =========================================================
const approvalCatLabels = {
    'all': '🔍 Semua Pengajuan',
    'barang': '📦 Nama Barang / Kode',
    'peminjam': '👤 Peminjam / NIM',
    'number': '📄 No. Peminjaman',
    'lab': '🏛️ Laboratorium',
    'status': '⏳ Status Approval',
    'masa': '📅 Masa Pinjam'
};

const approvalCatPlaceholders = {
    'all': 'Cari nama peminjam, barang, kode aset, status, atau nomor...',
    'barang': 'Cari nama barang atau kode aset...',
    'peminjam': 'Cari nama peminjam atau NIM/NIP...',
    'number': 'Cari nomor peminjaman / group ID...',
    'lab': 'Cari laboratorium / studio...',
    'status': 'Cari status approval...',
    'masa': 'Cari tanggal pinjam (YYYY-MM-DD)...'
};

function toggleApprovalCustomDropdown(id, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const menu = document.getElementById('menu-filter-' + id);
    const arrow = document.getElementById('arrow-filter-' + id);
    
    document.querySelectorAll('.custom-dropdown-menu').forEach(m => {
        if (m !== menu) m.classList.add('hidden');
    });
    document.querySelectorAll('.dropdown-arrow').forEach(a => {
        if (a !== arrow) a.classList.remove('rotate-180');
    });

    if (menu) {
        menu.classList.toggle('hidden');
        if (arrow) arrow.classList.toggle('rotate-180');
    }
}

function selectApprovalMainCategory(catKey, catLabel, placeholder, el) {
    const inputCat = document.getElementById('mainCategorySelectApproval');
    const labelEl = document.getElementById('label-filter-main-cat');
    const inputSearch = document.getElementById('inputSearchApproval');

    if (inputCat) inputCat.value = catKey;
    if (labelEl) labelEl.textContent = catLabel;
    if (inputSearch) {
        inputSearch.placeholder = placeholder;
        inputSearch.focus();
    }

    document.querySelectorAll('#menu-filter-main-cat .dropdown-item-opt').forEach(opt => opt.classList.remove('active'));
    if (el) el.classList.add('active');

    const menu = document.getElementById('menu-filter-main-cat');
    const arrow = document.getElementById('arrow-filter-main-cat');
    if (menu) menu.classList.add('hidden');
    if (arrow) arrow.classList.remove('rotate-180');
}

function updateApprovalFilterBadge() {
    const container = document.getElementById('additionalFilterRowsContainerApproval');
    const rowsCount = container ? container.querySelectorAll('.extra-filter-row').length : 0;
    const total = 1 + rowsCount;
    const badge = document.getElementById('filterCountBadgeApproval');
    if (badge) {
        badge.textContent = Math.min(4, total) + '/4';
    }
}

function toggleApprovalMultiFilter(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    const extraCard = document.getElementById('extraRowsCardApproval');
    const container = document.getElementById('additionalFilterRowsContainerApproval');
    const currentRows = container ? container.querySelectorAll('.extra-filter-row').length : 0;

    if (extraCard) {
        if (!extraCard.classList.contains('open')) {
            extraCard.classList.add('open');
            if (currentRows === 0) {
                addApprovalFilterRow();
            }
        } else {
            if (currentRows < 3) {
                addApprovalFilterRow();
            } else {
                extraCard.classList.remove('open');
            }
        }
    }
    updateApprovalFilterBadge();
}

let approvalExtraRowSeq = Date.now();

function addApprovalFilterRow() {
    const container = document.getElementById('additionalFilterRowsContainerApproval');
    if (!container) return;

    const existingRows = container.querySelectorAll('.extra-filter-row').length;
    if (existingRows >= 3) return; // Total max 4 rows (1 main + 3 extra)

    approvalExtraRowSeq++;
    const rowId = 'extra-row-' + approvalExtraRowSeq;
    
    const defaultCat = existingRows === 0 ? 'barang' : (existingRows === 1 ? 'peminjam' : 'status');

    let dropdownOptsHtml = '';
    for (const [key, label] of Object.entries(approvalCatLabels)) {
        const isActive = (key === defaultCat) ? 'active' : '';
        dropdownOptsHtml += `
            <div onclick="selectApprovalExtraCategory('${rowId}', '${key}', '${label}', '${approvalCatPlaceholders[key]}', this)" class="dropdown-item-opt ${isActive}">
                <span>${label}</span>
            </div>
        `;
    }

    const rowDiv = document.createElement('div');
    rowDiv.className = 'extra-filter-row';
    rowDiv.id = rowId;
    rowDiv.innerHTML = `
        <input type="hidden" name="filter_field[]" id="field-${rowId}" value="${defaultCat}">
        <div class="unified-search-pill flex-grow-1 d-flex align-items-center justify-content-between min-w-0" style="height: 44px;">
            <div class="position-relative flex-shrink-0">
                <button type="button" onclick="toggleApprovalCustomDropdown('${rowId}', event)" class="d-flex align-items-center gap-1 bg-transparent border-0 text-dark fw-bold cursor-pointer py-1 px-1" style="font-size: 0.8rem;">
                    <span id="label-filter-${rowId}" class="text-truncate" style="max-width: 140px;">${approvalCatLabels[defaultCat]}</span>
                    <i class="fa-solid fa-chevron-down text-secondary ms-1 dropdown-arrow transition-all" id="arrow-filter-${rowId}" style="font-size: 9px;"></i>
                </button>
                <div id="menu-filter-${rowId}" class="custom-dropdown-menu hidden position-absolute top-100 start-0 mt-2 bg-white border border-light-subtle rounded-3 shadow-lg p-1" style="width: 220px; z-index: 1050;">
                    ${dropdownOptsHtml}
                </div>
            </div>
            <div class="unified-divider flex-shrink-0"></div>
            <div class="flex-grow-1 d-flex align-items-center min-w-0 px-1">
                <i class="fa-solid fa-magnifying-glass text-secondary me-2 flex-shrink-0" style="font-size: 12px;"></i>
                <input type="text" name="filter_value[]" placeholder="${approvalCatPlaceholders[defaultCat]}" class="form-control border-0 shadow-none bg-transparent p-0 text-dark fw-semibold extra-row-input" style="font-size: 0.82rem;">
            </div>
        </div>
        <button type="button" onclick="removeApprovalFilterRow(this)" class="btn-remove-row" title="Hapus Kriteria Ini">
            <i class="fa-solid fa-trash text-xs"></i>
        </button>
    `;

    container.appendChild(rowDiv);
    updateApprovalFilterBadge();

    const newInput = rowDiv.querySelector('input.extra-row-input');
    if (newInput) newInput.focus();
}

function removeApprovalFilterRow(btn) {
    const row = btn.closest('.extra-filter-row');
    if (row) row.remove();

    const extraCard = document.getElementById('extraRowsCardApproval');
    const remainingRows = document.querySelectorAll('#additionalFilterRowsContainerApproval .extra-filter-row').length;
    if (remainingRows === 0 && extraCard) {
        extraCard.classList.remove('open');
    }

    updateApprovalFilterBadge();
}

function selectApprovalExtraCategory(rowId, catKey, catLabel, placeholder, el) {
    const fieldEl = document.getElementById('field-' + rowId);
    const labelEl = document.getElementById('label-filter-' + rowId);
    const inputEl = document.querySelector(`#${rowId} input.extra-row-input`);

    if (fieldEl) fieldEl.value = catKey;
    if (labelEl) labelEl.textContent = catLabel;
    if (inputEl) {
        inputEl.placeholder = placeholder;
        inputEl.focus();
    }

    document.querySelectorAll(`#menu-filter-${rowId} .dropdown-item-opt`).forEach(m => m.classList.remove('active'));
    if (el) el.classList.add('active');

    const menu = document.getElementById('menu-filter-' + rowId);
    const arrow = document.getElementById('arrow-filter-' + rowId);
    if (menu) menu.classList.add('hidden');
    if (arrow) arrow.classList.remove('rotate-180');
}

function clearApprovalSearch() {
    const input = document.getElementById('inputSearchApproval');
    const btnClear = document.getElementById('btnClearSearchApproval');
    if (input) {
        input.value = '';
        input.focus();
    }
    if (btnClear) btnClear.classList.add('d-none');
    
    const form = document.getElementById('formSearchApproval');
    if (form) form.submit();
}

function resetApprovalMultiSearch() {
    window.location.href = '<?= site_url("prodi/peminjaman"); ?>';
}

document.addEventListener('DOMContentLoaded', () => {
    const inputSearch = document.getElementById('inputSearchApproval');
    const btnClear = document.getElementById('btnClearSearchApproval');
    const autoDropdown = document.getElementById('autocompleteDropdownApproval');
    const autoResults = document.getElementById('autocompleteResultsApproval');
    const searchIcon = document.getElementById('searchIconApproval');
    const formSearch = document.getElementById('formSearchApproval');

    let autoDebounceTimer = null;
    let currentFocusIdx = -1;

    if (inputSearch) {
        inputSearch.addEventListener('input', function() {
            const q = this.value.trim();
            const currentCat = document.getElementById('mainCategorySelectApproval')?.value || 'all';

            if (btnClear) {
                if (q.length > 0) {
                    btnClear.classList.remove('d-none');
                } else {
                    btnClear.classList.add('d-none');
                }
            }

            if (searchIcon) {
                searchIcon.className = 'fa-solid fa-spinner fa-spin text-orange-500 text-xs me-2 flex-shrink-0';
            }

            clearTimeout(autoDebounceTimer);
            autoDebounceTimer = setTimeout(() => {
                if (searchIcon) {
                    searchIcon.className = 'fa-solid fa-magnifying-glass text-secondary me-2 flex-shrink-0';
                }

                if (q.length >= 1 && autoDropdown && autoResults) {
                    fetch(`<?= site_url('prodi/peminjaman/autocomplete'); ?>?q=${encodeURIComponent(q)}&cat=${encodeURIComponent(currentCat)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                let html = '';
                                data.forEach((item) => {
                                    const regex = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                                    const highlightedBarang = (item.nama_aset || '').replace(regex, '<mark>$1</mark>');
                                    const highlightedKode = (item.kode_aset || '').replace(regex, '<mark>$1</mark>');
                                    const highlightedPeminjam = (item.nama_peminjam || '').replace(regex, '<mark>$1</mark>');
                                    const highlightedStatus = (item.status || '').replace(regex, '<mark>$1</mark>');

                                    html += `
                                        <div class="autocomplete-item-row d-flex align-items-center justify-content-between gap-3" data-name="${item.nama_aset}" data-peminjam="${item.nama_peminjam}">
                                            <div class="d-flex align-items-center gap-2.5 min-w-0">
                                                <span class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; background: rgba(234, 91, 26, 0.12); color: #ea5b1a; font-size: 13px;">
                                                    <i class="fa-solid fa-box"></i>
                                                </span>
                                                <div class="min-w-0">
                                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;">
                                                        ${highlightedBarang} <span class="badge bg-light text-secondary border ms-1" style="font-size: 10px;">${highlightedKode}</span>
                                                    </div>
                                                    <div class="text-muted text-truncate" style="font-size: 11px;">
                                                        <i class="fa-solid fa-user me-1 text-secondary"></i>${highlightedPeminjam} &bull; <i class="fa-solid fa-receipt ms-1 me-1 text-secondary"></i>${item.group_id}
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="badge bg-orange-subtle text-fik-orange border border-warning-subtle flex-shrink-0" style="font-size: 10.5px; border-radius: 99px;">
                                                ${highlightedStatus}
                                            </span>
                                        </div>
                                    `;
                                });
                                autoResults.innerHTML = html;
                                autoDropdown.classList.remove('d-none');
                                autoDropdown.style.display = 'block';
                                currentFocusIdx = -1;

                                autoResults.querySelectorAll('.autocomplete-item-row').forEach(row => {
                                    row.onclick = function() {
                                        const selectedVal = currentCat === 'peminjam' ? this.getAttribute('data-peminjam') : this.getAttribute('data-name');
                                        inputSearch.value = selectedVal;
                                        autoDropdown.classList.add('d-none');
                                        autoDropdown.style.display = 'none';
                                        if (formSearch) formSearch.submit();
                                    };
                                });
                            } else {
                                autoResults.innerHTML = `
                                    <div class="px-4 py-3 text-muted text-center italic" style="font-size: 0.8rem;">
                                        Tidak ada hasil untuk "<strong>${q}</strong>"
                                    </div>
                                `;
                                autoDropdown.classList.remove('d-none');
                                autoDropdown.style.display = 'block';
                            }
                        })
                        .catch(() => {
                            autoDropdown.classList.add('d-none');
                            autoDropdown.style.display = 'none';
                        });
                } else {
                    if (autoDropdown) {
                        autoDropdown.classList.add('d-none');
                        autoDropdown.style.display = 'none';
                    }
                }
            }, 220);
        });
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.position-relative') && !e.target.closest('.custom-dropdown-menu')) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.dropdown-arrow').forEach(a => a.classList.remove('rotate-180'));
        }
        if (autoDropdown && !e.target.closest('#mainSearchPillApproval') && !e.target.closest('#autocompleteDropdownApproval')) {
            autoDropdown.classList.add('d-none');
            autoDropdown.style.display = 'none';
        }
    });
});
</script>
</body>
</html>
