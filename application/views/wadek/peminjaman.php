<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Approval Peminjaman Luar Kampus - Wakil Dekan (Wadek)
 * Clean, high-contrast UI aligned with Kaprodi approval module.
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
            <div id="<?= html_escape($id) ?>" class="kp-multi-filter" data-kp-multi-filter data-max-filters="4">
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
                <div class="scm-search-filter__actions mt-3 d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-orange-primary px-4 py-2" data-kp-filter-apply><i class="bi bi-search me-1"></i> Terapkan filter</button>
                    <a href="<?= current_url() ?>" class="btn btn-outline-secondary px-3 py-2 rounded-pill"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/loan-progress.css'); ?>?v=<?= @filemtime(FCPATH . 'assets/css/loan-progress.css'); ?>">
    
    <style>
        .modal { display: none; }

        body {
            background-color: #f4f5f7 !important;
            color: #18202b !important;
            font-family: Poppins, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        /* =========================================================
           POLISHED GLASS HEADER (MATCHING SCREENSHOT 2)
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

        /* =========================================================
           UNIFIED SEARCH PILL & MULTI-FILTER (MATCHING SCREENSHOT 2)
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

        /* Dark mode overrides */
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
        html.scm-theme-dark .profile-name {
            color: #f8fafc;
        }
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
        html.scm-theme-dark .btn-standalone-add {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #fb923c !important;
        }
        html.scm-theme-dark .card.bg-white {
            background: #1e293b !important;
        }

        /* Page Content */
        .kp-page-content {
            padding-top: 24px;
            padding-bottom: 36px;
        }

        .kp-page-heading {
            margin-bottom: 18px;
        }

        .kp-page-heading h1 {
            margin: 0 0 4px;
            color: #0f172a;
            font-size: clamp(1.4rem, 2vw, 1.85rem);
            font-weight: 700;
        }

        .kp-context {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            color: #64748b;
            font-size: 0.78rem;
        }

        /* Cards */
        .kp-card {
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .kp-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 18px 22px;
        }

        .kp-section-title {
            margin: 0 0 3px;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 700;
        }

        .kp-section-copy {
            margin: 0;
            color: #64748b;
            font-size: 0.78rem;
        }

        .kp-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            width: max-content;
            min-height: 29px;
            padding: 6px 14px;
            border: 1px solid #fed7aa;
            background: #fff7ed;
            color: #c2410c;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
        }

        .kp-status-dot {
            width: 7px;
            height: 7px;
            flex: 0 0 7px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Filter Section */
        .kp-multi-filter { padding: 18px 22px; }
        .kp-multi-filter-heading { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 14px; }
        .kp-multi-filter-heading h3 { margin: 0; color: #0f172a; font-size: 0.95rem; font-weight: 700; }
        .kp-multi-filter-heading h3 i { color: #ea580c; }
        .kp-multi-filter-list { display: grid; gap: 10px; }
        .kp-multi-filter-row { display: grid; grid-template-columns: minmax(205px, .72fr) minmax(270px, 1.55fr) auto; align-items: center; gap: 10px; }
        
        .kp-multi-filter-row .form-select, 
        .kp-multi-filter-row .form-control,
        .kp-page-size select,
        .kp-loan-modal textarea {
            min-height: 42px;
            border: 1.5px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            color: #0f172a !important;
            font-size: 0.8rem;
            box-shadow: none !important;
            border-radius: 8px;
        }

        .kp-multi-filter-row .form-control::placeholder,
        .kp-loan-modal textarea::placeholder {
            color: #94a3b8 !important;
            opacity: 1;
        }

        .kp-multi-filter-row .form-select:focus, 
        .kp-multi-filter-row .form-control:focus,
        .kp-loan-modal textarea:focus {
            border-color: #ea580c !important;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12) !important;
        }

        .kp-multi-filter-tools { display: flex; align-items: center; gap: 8px; }
        .kp-multi-filter-icon { width: 42px; height: 42px; display: inline-flex; flex: 0 0 42px; align-items: center; justify-content: center; padding: 0; border-radius: 50%; border: 1.5px solid #cbd5e1; background: #ffffff; color: #64748b; }
        .kp-multi-filter-add { border-color: #ea580c !important; color: #ea580c !important; }
        .kp-multi-filter-add:hover { background: #ea580c !important; color: #ffffff !important; }
        .kp-multi-filter-icon:disabled { opacity: 0.38; }

        .btn-orange-primary {
            background-color: #ea580c;
            border: 1px solid #ea580c;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.78rem;
            border-radius: 999px;
            transition: all 0.2s ease;
        }

        .btn-orange-primary:hover {
            background-color: #c2410c;
            border-color: #c2410c;
            color: #ffffff;
        }

        /* Table Card */
        .kp-table-card { margin-top: 14px; }
        .kp-section-header { padding: 16px 22px; border-bottom: 1px solid #e2e8f0; }

        .kp-table {
            min-width: 1120px;
            margin: 0;
            color: #0f172a;
        }

        .kp-table > :not(caption) > * > * {
            padding: 13px 16px;
            border-bottom-color: #f1f5f9;
            vertical-align: middle;
        }

        .kp-table thead th {
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #475569;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .kp-table tbody td {
            background: #ffffff;
            color: #0f172a;
            font-size: 0.78rem;
        }

        .kp-table tbody tr:hover td {
            background: #f8fafc;
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
            text-decoration: none;
        }

        .kp-sort-button i { color: #94a3b8; font-size: 0.72rem; }

        .kp-primary-text { color: #0f172a; font-size: 0.8rem; font-weight: 700; }
        .kp-secondary-text { margin-top: 2px; color: #64748b; font-size: 0.68rem; }
        .kp-item-list { display: grid; gap: 3px; }
        .kp-item-meta { margin-left: 8px; color: #ea580c; font-weight: 600; font-size: 0.7rem; }

        .kp-detail-button {
            min-height: 32px;
            padding: 5px 16px;
            border: 1.5px solid #0284c7;
            color: #0284c7;
            background: transparent;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .kp-detail-button:hover {
            background: #0284c7;
            color: #ffffff;
        }

        .kp-empty-row td {
            padding: 42px 18px !important;
            color: #64748b !important;
            text-align: center;
        }

        /* Pagination Footer */
        .kp-pagination-footer {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 14px;
            padding: 14px 22px;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
        }

        .kp-page-size { display: flex; align-items: center; gap: 9px; color: #64748b; font-size: 0.75rem; }
        .kp-page-size select { width: auto; min-width: 68px; height: 32px; min-height: 32px; font-size: 0.75rem; }
        .kp-page-status { color: #64748b; font-size: 0.75rem; text-align: center; }
        .kp-pagination-nav { display: flex; justify-content: flex-end; }
        .kp-pagination { display: flex; gap: 4px; margin: 0; padding: 0; list-style: none; }

        .kp-page-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #ffffff;
            color: #334155;
            font-size: 0.72rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.16s ease;
        }

        .kp-page-button:hover:not(.disabled):not(.is-active) {
            border-color: #ea580c;
            background: #fff7ed;
            color: #c2410c;
        }

        .kp-page-button.is-active {
            border-color: #ea580c;
            background: #ea580c;
            color: #ffffff;
            font-weight: 700;
        }

        .kp-page-button.disabled {
            border-color: #e2e8f0;
            color: #94a3b8;
            pointer-events: none;
        }

        /* Modal Styles */
        .kp-loan-modal .modal-dialog { max-width: 780px; }
        .kp-loan-modal .modal-content { border: 1px solid #e2e8f0; border-radius: 16px; background: #ffffff; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15); overflow: hidden; }
        .kp-loan-modal .modal-header { padding: 18px 24px 16px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
        .kp-modal-kicker { color: #64748b; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; }
        .kp-loan-modal .modal-title { margin-top: 2px; color: #0f172a; font-size: 1.2rem; font-weight: 700; }
        .kp-modal-meta { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-top: 12px; }
        .kp-meta-box { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; }
        .kp-meta-label { color: #64748b; font-size: 0.62rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; }
        .kp-meta-value { margin-top: 3px; color: #0f172a; font-size: 0.78rem; font-weight: 700; }
        .kp-loan-modal .modal-body { padding: 22px 24px; }
        .kp-modal-section + .kp-modal-section { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .kp-modal-section-title { margin: 0 0 10px; color: #0f172a; font-size: 0.85rem; font-weight: 700; }
        .kp-purpose { margin: 7px 0 0; color: #1e293b; font-size: 0.8rem; line-height: 1.6; }

        .kp-detail-table { overflow: hidden; margin-top: 9px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .kp-detail-table table { margin: 0; color: #0f172a; }
        .kp-detail-table th, .kp-detail-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.75rem; vertical-align: middle; }
        .kp-detail-table th { background: #f8fafc; color: #64748b; font-size: 0.65rem; letter-spacing: 0.04em; text-transform: uppercase; }
        .kp-detail-table td { background: #ffffff; color: #0f172a; }

        .kp-loan-modal .modal-footer { gap: 8px; padding: 16px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc; }
        .kp-loan-modal .modal-footer .btn { min-width: 95px; min-height: 38px; border-radius: 999px; font-size: 0.75rem; font-weight: 700; }

        .kp-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
        }
        .kp-status-badge.is-current { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
        .kp-status-badge.is-completed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .kp-status-badge.is-rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .kp-status-badge.is-pending { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

        @media (max-width: 767.98px) {
            .kp-multi-filter { padding: 14px; }
            .kp-multi-filter-row { grid-template-columns: 1fr; gap: 8px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; }
            .kp-multi-filter-tools { justify-content: flex-end; }
            .kp-pagination-footer { grid-template-columns: 1fr; justify-items: center; }
            .kp-modal-meta { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Curved Sidebar Navigation -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div id="laaMainContentWrapper" class="page-wrapper-for-sidebar">
        <!-- Modern Glass Header (Matching Screenshot 2) -->
        <header class="glass-header-ifik mb-4">
            <div class="container-fluid px-3 px-lg-4 d-flex flex-wrap align-items-center justify-content-between gap-3 header-inner-pad">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 44px; height: 44px; background: rgba(234, 91, 26, 0.12); color: #ea5b1a; font-size: 1.35rem;">
                        <i class="bi bi-patch-check"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h1 class="h5 fw-bold text-dark mb-0 tracking-tight">Approval Peminjaman Barang</h1>
                            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(234, 91, 26, 0.12); color: #ea5b1a; font-weight: 700; font-size: 11px;">Wakil Dekan (Wadek)</span>
                        </div>
                        <p class="text-muted small mb-0 d-none d-sm-block" style="font-size: 12px;">Persetujuan dan pemantauan pengajuan peminjaman barang luar kampus · <?= tanggal_indonesia(date('Y-m-d')) ?></p>
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
                                <span class="profile-name"><?= html_escape($this->session->userdata('nama') ?: ($this->session->userdata('username') ?: 'Wakil Dekan')); ?></span>
                                <span class="profile-role">WAKIL DEKAN 1</span>
                            </div>
                            <i class="bi bi-chevron-down profile-chevron"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-ifik mt-2">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <span class="d-block text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Login Sebagai</span>
                                <span class="fw-bold text-dark d-block text-truncate" style="font-size: 13px;"><?= html_escape($this->session->userdata('username') ?: ($this->session->userdata('nama') ?: 'Wakil Dekan')); ?></span>
                                <span class="badge rounded-pill mt-1" style="background: #fff7ed; color: #ea580c; font-size: 10px; font-weight: 700;">Wakil Dekan 1</span>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= site_url('wadek/peminjaman') ?>">
                                    <i class="bi bi-patch-check text-warning"></i>
                                    <span>Approval Peminjaman</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= site_url('peminjaman_barang') ?>">
                                    <i class="bi bi-box-seam text-primary"></i>
                                    <span>Katalog Alat</span>
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

        <!-- Main Content -->
        <main class="container-fluid kp-page-content px-3 px-lg-4">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3"><?= html_escape($this->session->flashdata('success')) ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3"><?= html_escape($this->session->flashdata('error')) ?></div>
            <?php endif; ?>

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

            <!-- Unified Multi-Search Bar (Matching Screenshot 2) -->
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
                <form action="<?= site_url('wadek/peminjaman'); ?>" method="GET" id="formSearchApproval" class="position-relative" style="position: relative; z-index: 1051;">
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
                                        <a class="kp-sort-button <?= $approval_sort === $sort_key ? 'text-primary fw-bold' : '' ?>" href="<?= scm_sort_url($sort_key, $approval_sort, $approval_dir) ?>">
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
                                                    <span><?= html_escape($p->nama_aset ?? 'Barang') ?> <span class="kp-item-meta"><?= (int)($p->jumlah_pinjam ?? 1) ?> unit</span></span>
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
                                                class="btn kp-detail-button"
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
        if (existingRows >= 3) return;

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
        window.location.href = '<?= site_url("wadek/peminjaman"); ?>';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
            new bootstrap.Tooltip(element);
        });

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
                        fetch(`<?= site_url('wadek/peminjaman/autocomplete'); ?>?q=${encodeURIComponent(q)}&cat=${encodeURIComponent(currentCat)}`)
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
                                if (autoDropdown) autoDropdown.classList.add('d-none');
                            });
                    } else if (autoDropdown) {
                        autoDropdown.classList.add('d-none');
                        autoDropdown.style.display = 'none';
                    }
                }, 250);
            });

            inputSearch.addEventListener('keydown', function(e) {
                if (!autoDropdown || autoDropdown.classList.contains('d-none')) return;
                const items = autoResults.querySelectorAll('.autocomplete-item-row');
                if (!items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentFocusIdx = (currentFocusIdx + 1) % items.length;
                    updateFocusItem(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentFocusIdx = (currentFocusIdx - 1 + items.length) % items.length;
                    updateFocusItem(items);
                } else if (e.key === 'Enter') {
                    if (currentFocusIdx > -1 && items[currentFocusIdx]) {
                        e.preventDefault();
                        items[currentFocusIdx].click();
                    }
                } else if (e.key === 'Escape') {
                    autoDropdown.classList.add('d-none');
                    autoDropdown.style.display = 'none';
                }
            });

            function updateFocusItem(items) {
                items.forEach((item, idx) => {
                    if (idx === currentFocusIdx) {
                        item.classList.add('active-nav');
                        item.scrollIntoView({ block: 'nearest' });
                    } else {
                        item.classList.remove('active-nav');
                    }
                });
            }
        }

        document.addEventListener('click', function(e) {
            if (autoDropdown && !autoDropdown.contains(e.target) && e.target !== inputSearch) {
                autoDropdown.classList.add('d-none');
                autoDropdown.style.display = 'none';
            }

            if (!e.target.closest('.custom-dropdown-menu') && !e.target.closest('button[onclick*="toggleApprovalCustomDropdown"]')) {
                document.querySelectorAll('.custom-dropdown-menu').forEach(m => m.classList.add('hidden'));
                document.querySelectorAll('.dropdown-arrow').forEach(a => a.classList.remove('rotate-180'));
            }
        });
    });
    </script>
</body>
</html>
