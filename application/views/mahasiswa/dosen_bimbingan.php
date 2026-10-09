<?php
/**
 * DASHBOARD UNIFIED — 4 Role: P1, P2, U1, U2
 *
 * Controller wajib mengirimkan variabel: $role ('p1'|'p2'|'u1'|'u2')
 * Jika tidak, default ke 'p1'.
 */

$role             = $role ?? 'p1';
$is_p1            = ($role === 'p1');
$is_p2            = ($role === 'p2');
$is_u1            = ($role === 'u1');
$is_u2            = ($role === 'u2');
$is_pembimbing    = $is_p1 || $is_p2;
$is_penguji       = $is_u1 || $is_u2;
$display_posisi   = ($is_p1 || $is_u1) ? 1 : 2;
$model_posisi     = ['p1' => 1, 'p2' => 2, 'u1' => 3, 'u2' => 4][$role];

$role_labels = [
    'p1' => 'Pembimbing 1',
    'p2' => 'Pembimbing 2',
    'u1' => 'Penguji 1',
    'u2' => 'Penguji 2',
];
$role_label       = $role_labels[$role];
$dashboard_title  = $is_pembimbing ? 'Dashboard Bimbingan Dosen' : 'Dashboard Dosen Penguji';
$default_tahap    = $is_pembimbing ? 'Preview 1' : 'Preview 2';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? ($dashboard_title . ' — IFIK Portal'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .search-pill-container { position: relative; display: flex; align-items: center; gap: 8px; width: 100%; }
        .unified-search-pill {
            display: flex; align-items: center; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px;
            padding: 2px 12px; flex: 1; height: 46px; transition: all 0.2s ease;
        }
        .unified-search-pill:focus-within {
            border-color: #ea580c !important; background: #ffffff !important; box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12) !important;
        }
        .unified-divider { width: 1.5px; height: 20px; background-color: #cbd5e1; margin: 0 10px; flex-shrink: 0; }
        .search-cat-btn {
            display: flex; align-items: center; gap: 5px; background: transparent; border: none;
            font-size: 0.75rem; font-weight: 700; color: #1e293b; cursor: pointer; padding: 4px 2px;
            white-space: nowrap; flex-shrink: 0;
        }
        .search-cat-btn:hover { color: #ea580c; }
        .search-cat-menu {
            position: absolute; top: calc(100% + 8px); left: 0; width: 220px;
            background: #fff; border: 1.5px solid #e2e8f0; border-radius: 14px;
            box-shadow: 0 16px 40px -8px rgba(15,23,42,0.16); z-index: 200;
            padding: 6px; display: none;
        }
        .search-cat-menu.open { display: block; }
        .search-cat-item {
            padding: 8px 12px; border-radius: 10px; cursor: pointer; font-size: 0.75rem;
            font-weight: 600; color: #475569; display: flex; align-items: center; gap: 8px;
        }
        .search-cat-item:hover, .search-cat-item.active { background: #fff7ed; color: #ea580c; font-weight: 700; }
        .btn-search-cari {
            padding: 6px 16px; background: linear-gradient(135deg, #ea580c, #f97316);
            color: #fff; font-size: 0.75rem; font-weight: 700; border: none; border-radius: 12px;
            cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s ease;
            flex-shrink: 0; height: 36px; box-shadow: 0 2px 8px rgba(234,88,12,0.25);
        }
        .btn-search-cari:hover { transform: scale(1.03); box-shadow: 0 4px 14px rgba(234,88,12,0.4); }

        @keyframes spinRotatingBorder { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .table-rotating-border-wrap {
            position: relative; border-radius: 16px; padding: 2px; overflow: hidden;
            box-shadow: 0 12px 36px -8px rgba(234, 88, 12, 0.15), 0 4px 16px rgba(71, 85, 105, 0.06); background: #ffffff;
        }
        .table-rotating-border-spin {
            position: absolute; inset: -350%; pointer-events: none; opacity: 0.95;
            background: conic-gradient(from 90deg at 50% 50%, #ea580c 0%, #f97316 12%, #ffffff 22%, #cbd5e1 35%, #475569 48%, #1e293b 58%, #ea580c 68%, #ffffff 80%, #94a3b8 90%, #ea580c 100%);
            animation: spinRotatingBorder 7s linear infinite;
        }
        .table-rotating-border-inner { position: relative; z-index: 10; width: 100%; background: #ffffff; border-radius: 14px; overflow: hidden; }
        .table-custom-rounded { border-collapse: separate !important; border-spacing: 0 !important; width: 100%; }
        .table-custom-rounded thead tr th:first-child { border-top-left-radius: 14px; }
        .table-custom-rounded thead tr th:last-child { border-top-right-radius: 14px; }

        .badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 700; font-size: 0.75rem; gap: 0.375rem; border: 1px solid transparent; }
        .badge-success { background-color: #d1fae5; color: #065f46; border-color: #34d399; }
        .badge-warning { background-color: #fef3c7; color: #92400e; border-color: #fbbf24; }
        .badge-danger { background-color: #ffe4e6; color: #9f1239; border-color: #fb7185; }
        .badge-secondary { background-color: #f1f5f9; color: #475569; border-color: #cbd5e1; }

        .pagination-btn {
            width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px; border: 1.5px solid #e2e8f0; background: #fff; color: #475569;
            font-size: 0.72rem; font-weight: 800; cursor: pointer; transition: all 0.15s ease;
        }
        .pagination-btn:hover:not(:disabled):not(.active) { background: #fff7ed; border-color: #fed7aa; color: #ea580c; }
        .pagination-btn.active {
            background: linear-gradient(135deg, #ea580c, #f97316); border-color: #ea580c;
            color: #fff; box-shadow: 0 3px 10px rgba(234,88,12,0.3); cursor: default;
        }
        .pagination-btn:disabled { opacity: 0.35; cursor: not-allowed; }
        .pagination-ellipsis { padding: 0 4px; color: #94a3b8; font-weight: 800; font-size: 0.75rem; }

        @media (max-width: 768px) {
            .table-rotating-border-inner { overflow-x: visible !important; }
            .table-custom-rounded thead { display: none !important; }
            .table-custom-rounded,
            .table-custom-rounded tbody { display: block !important; width: 100% !important; }
            .table-custom-rounded tr {
                display: block !important;
                margin: 0 0 0.85rem 0 !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 16px !important;
                padding: 0.85rem 0.75rem 0.6rem !important;
                background: #fff !important;
                box-shadow: 0 4px 14px -4px rgba(15,23,42,0.08) !important;
                position: relative !important;
            }
            .table-custom-rounded tbody tr:last-child { margin-bottom: 0 !important; }
            .table-custom-rounded tbody tr:hover { background: #fff !important; }
            .table-custom-rounded td {
                display: block !important;
                width: 100% !important;
                padding: 0.45rem 0.4rem !important;
                text-align: left !important;
                border-bottom: 1px dashed #f1f5f9 !important;
                position: relative !important;
                padding-left: 40% !important;
                min-height: 36px !important;
                font-size: 0.8rem !important;
                vertical-align: top !important;
            }
            .table-custom-rounded td:last-child { border-bottom: none !important; padding-bottom: 0.2rem !important; }
            .table-custom-rounded td::before {
                content: attr(data-label);
                position: absolute;
                left: 0.4rem;
                top: 0.5rem;
                width: 36%;
                font-weight: 800;
                font-size: 0.62rem;
                text-transform: uppercase;
                color: #64748b;
                letter-spacing: 0.05em;
                line-height: 1.2;
            }
            .table-custom-rounded td.dosen-cb-cell {
                position: absolute !important;
                top: 0.7rem !important;
                right: 0.7rem !important;
                width: auto !important;
                padding: 0 !important;
                border: none !important;
                padding-left: 0 !important;
                min-height: 0 !important;
                z-index: 2 !important;
            }
            .table-custom-rounded td.dosen-cb-cell::before { display: none !important; }
            .table-custom-rounded td.mhs-info-cell {
                padding: 0.2rem 2.6rem 0.6rem 0.4rem !important;
                border-bottom: 1px solid #e2e8f0 !important;
                margin-bottom: 0.3rem;
            }
            .table-custom-rounded td.mhs-info-cell::before { display: none !important; }
            .table-custom-rounded td.aksi-cell {
                text-align: right !important;
                padding: 0.6rem 0.4rem 0.2rem !important;
                padding-left: 0.4rem !important;
                border-top: 1px solid #f1f5f9 !important;
                border-bottom: none !important;
                margin-top: 0.3rem;
            }
            .table-custom-rounded td.aksi-cell::before { display: none !important; }
            .table-custom-rounded td[colspan] {
                display: block !important;
                padding: 2rem 1rem !important;
                padding-left: 1rem !important;
                text-align: center !important;
                border: none !important;
                min-height: 0 !important;
            }
            .table-custom-rounded td[colspan]::before { display: none !important; }
            .table-custom-rounded tbody tr:has(td[colspan]) {
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                background: transparent !important;
            }
            .table-custom-rounded td .badge { font-size: 0.7rem; }
            .table-custom-rounded td button { font-size: 0.7rem !important; }
            .table-custom-rounded td.aksi-cell > div { justify-content: flex-end !important; flex-wrap: wrap; }
        }

        #hoverPreviewPanel {
            position: fixed; z-index: 999; width: 520px; max-width: 95vw; background: #fff;
            border-radius: 20px;
            box-shadow: 0 24px 64px -12px rgba(234,88,12,0.18), 0 8px 24px rgba(71,85,105,0.10);
            border: 1.5px solid #fed7aa; overflow: hidden; display: none;
            pointer-events: auto; transition: opacity 0.18s, transform 0.18s;
        }
        #hoverPreviewPanel.visible { display: block; }
        #hoverPreviewPanel .panel-header {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            color: #fff; padding: 14px 18px; font-weight: 800; font-size: 13px;
            display: flex; align-items: center; gap: 8px;
        }
        #hoverPreviewPanel .pdf-frame-wrap {
            background: #f1f5f9; border-bottom: 1px solid #e2e8f0; height: 200px;
            position: relative; overflow: hidden;
        }
        #hoverPreviewPanel .pdf-frame-wrap iframe { width: 100%; height: 100%; border: none; }
        #hoverPreviewPanel .pdf-no-file {
            display: flex; align-items: center; justify-content: center; height: 200px;
            color: #94a3b8; font-size: 13px; font-weight: 600;
            flex-direction: column; gap: 8px;
        }
        #hoverPreviewPanel .panel-body { padding: 16px 18px 18px; max-height: 400px; overflow-y: auto; }
        #hoverPreviewPanel .panel-label {
            font-size: 10px; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 6px;
        }

        @keyframes popInCard { 0% { opacity: 0; transform: scale(0.9) translateY(24px); } 100% { opacity: 1; transform: scale(1) translateY(0); } }
        @keyframes fadeInSlideRight { 0% { opacity: 0; transform: scale(0.95) translateX(60px); } 100% { opacity: 1; transform: scale(1) translateX(0); } }
        @keyframes fadeInDownSmooth { 0% { opacity: 0; transform: translateY(-16px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-pop-in { animation: popInCard 0.8s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }
        .animate-preview-in { animation: fadeInSlideRight 1.1s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }
        .animate-bar-in { animation: fadeInDownSmooth 0.8s cubic-bezier(0.2, 0.9, 0.2, 1) forwards; }

        #lihatBerkasContainer {
            position: fixed; inset: 0; pointer-events: none; z-index: 50; display: none;
            align-items: stretch; justify-content: center; padding: 1rem; gap: 1.5rem;
            overflow: hidden; background: rgba(15, 23, 42, 0.15); backdrop-filter: blur(0.5px);
        }
        #lihatBerkasContainer.active { display: flex; }

        /* === CHANGED: card diperbesar 20% (width: 30% → 36%, min/max disesuaikan) === */
        #wrapperDaftarMhs {
            display: flex; flex-direction: column; gap: 1rem;
            width: 36%; min-width: 384px; max-width: 504px;
            overflow-y: auto; overflow-x: hidden; flex-shrink: 0;
            padding: 0.25rem; max-height: 90vh; pointer-events: none;
        }
        #wrapperDaftarMhs::-webkit-scrollbar { width: 4px; }
        #wrapperDaftarMhs::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.5); border-radius: 9999px; }

        #wrapperPreviewBerkas {
            display: none; flex: 1; overflow-x: auto; overflow-y: hidden; gap: 1rem;
            padding: 0.25rem; align-items: stretch; max-height: 90vh; pointer-events: none;
        }
        #wrapperPreviewBerkas.active { display: flex; }
        #wrapperPreviewBerkas::-webkit-scrollbar { height: 4px; }
        #wrapperPreviewBerkas::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.5); border-radius: 9999px; }

        .student-card-item {
            pointer-events: auto; background: white; border-radius: 1.5rem;
            box-shadow: 0 20px 40px -12px rgba(0,0,0,0.25);
            border: 1px solid #e2e8f0; overflow: hidden; display: flex;
            flex-direction: column; flex-shrink: 0; width: 100%; max-height: 90vh;
        }

        /* === CHANGED: preview card diperbesar 20% (520px → 624px) === */
        .preview-card-item {
            pointer-events: auto; background: white; border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);
            border: 1px solid #e2e8f0; overflow: hidden; display: flex;
            flex-direction: column; flex-shrink: 0;
            width: 624px; max-width: 75vw; height: 85vh; max-height: 90vh;
        }
        /* Fullscreen state */
        .preview-card-item:fullscreen {
            width: 100vw !important;
            max-width: 100vw !important;
            height: 100vh !important;
            max-height: 100vh !important;
            border-radius: 0 !important;
            border: none !important;
        }
        .preview-card-item:-webkit-full-screen {
            width: 100vw !important;
            max-width: 100vw !important;
            height: 100vh !important;
            max-height: 100vh !important;
            border-radius: 0 !important;
            border: none !important;
        }
        .preview-card-item .preview-body {
            flex: 1; min-height: 0; position: relative; background: #e2e8f0; overflow: hidden;
        }
        .preview-card-item .preview-body iframe {
            width: 100%; height: 100%; border: 0; position: relative; z-index: 10;
        }
        .preview-card-item .preview-body .loader {
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            z-index: 5; background: #e2e8f0; color: #94a3b8;
            font-size: 0.75rem; font-weight: 600; gap: 0.5rem;
        }
        .preview-card-item .preview-body .loader i { font-size: 1.5rem; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .preview-card-item .preview-header { flex-shrink: 0; }
        .preview-card-item .preview-footer { flex-shrink: 0; gap: 0.5rem; flex-wrap: wrap; }
        .btn-3d-orange {
            background: linear-gradient(135deg, #ea580c, #f97316); color: #fff; border: none;
            transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(234,88,12,0.3);
        }
        .btn-3d-orange:hover { transform: scale(1.03); box-shadow: 0 6px 20px rgba(234,88,12,0.4); }

        /* Inline comment editor wrapper (muncul di bawah preview file) */
        .inline-comment-slot { display: block; }
        .inline-comment-slot.hidden { display: none !important; }

        .role-switch-btn {
            display: flex; flex-direction: column; align-items: center; gap: 6px;
            padding: 12px 8px; border-radius: 16px; border: 1.5px solid;
            font-weight: 700; font-size: 11px; text-align: center;
            transition: all 0.2s ease; cursor: pointer; text-decoration: none;
        }
        .role-switch-btn i { font-size: 18px; }
        .role-switch-btn.active {
            background: linear-gradient(135deg, #ea580c, #f97316);
            border-color: #ea580c; color: #fff;
            box-shadow: 0 6px 18px rgba(234,88,12,0.45);
            transform: translateY(-2px);
        }
        .role-switch-btn.inactive {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.22);
            color: #fff;
        }
        .role-switch-btn.inactive:hover {
            background: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.4);
        }

        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            min-height: 100vh;
            transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1),
                        width 0.75s cubic-bezier(0.76, 0, 0.24, 1);
            box-sizing: border-box;
        }
        @media (min-width: 1024px) {
            .page-wrapper-for-sidebar {
                margin-left: 270px;
                width: calc(100% - 270px);
            }
            body.curved-sidebar-desktop-collapsed .page-wrapper-for-sidebar {
                margin-left: 0;
                width: 100%;
            }
        }
        @media (max-width: 1023.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        .filter-row-dw {
            display: flex;
            align-items: center;
            gap: 8px;
            animation: fadeInDownSmooth 0.35s cubic-bezier(0.2, 0.9, 0.2, 1) forwards;
        }
        .filter-col-dw {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: 700;
            background: #fff;
            color: #1e293b;
            outline: none;
            cursor: pointer;
            transition: all 0.18s ease;
            flex-shrink: 0;
            min-width: 170px;
        }
        .filter-col-dw:focus {
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }
        .filter-val-dw {
            flex: 1;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 600;
            background: #f8fafc;
            color: #1e293b;
            outline: none;
            transition: all 0.18s ease;
        }
        .filter-val-dw:focus {
            border-color: #ea580c;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }
        .btn-remove-filter-dw {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            background: #ffe4e6;
            color: #e11d48;
            border: 1.5px solid #fecdd3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }
        .btn-remove-filter-dw:hover { background: #fecdd3; transform: scale(1.05); }
        .btn-remove-filter-dw:active { transform: scale(0.95); }

        @media (max-width: 768px) {
            #lihatBerkasContainer {
                flex-direction: column !important;
                align-items: stretch !important;
                justify-content: flex-start !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                padding: 0.75rem !important;
                gap: 0.75rem !important;
                background: rgba(15, 23, 42, 0.25) !important;
            }
            #wrapperDaftarMhs {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                max-height: none !important;
                overflow-y: visible !important;
                padding: 0 !important;
            }
            #wrapperPreviewBerkas {
                width: 100% !important;
                flex: none !important;
                max-height: none !important;
                overflow-x: auto !important;
                overflow-y: visible !important;
                padding: 0 !important;
            }
            .preview-card-item {
                width: 86vw !important;
                max-width: 86vw !important;
                height: 55vh !important;
                max-height: 55vh !important;
            }
            .student-card-item { max-height: none !important; }
            .filter-row-dw { flex-wrap: wrap; }
            .filter-col-dw { min-width: 100%; }
        }

        #modalPenilaianSidang {
            position: fixed; left: 1.5rem; bottom: 1.5rem; z-index: 10000; display: none;
            align-items: flex-end; justify-content: flex-start;
            background: transparent; pointer-events: none; padding: 0;
        }
        #modalPenilaianSidang.active { display: flex; }
        #modalPenilaianSidang .penilaian-box {
            pointer-events: auto;
            background: #fff; border-radius: 1.5rem; width: 500px; max-width: 95vw;
            max-height: 85vh; overflow: hidden; display: flex; flex-direction: column;
            box-shadow: 0 25px 65px -10px rgba(15,23,42,0.45), 0 0 0 1px rgba(226,232,240,0.8);
            animation: popInCard 0.3s cubic-bezier(0.2, 0.9, 0.2, 1) forwards;
        }
        #modalPenilaianSidang .penilaian-body { overflow-y: auto; flex: 1; padding: 1.5rem; }
        .rubrik-row {
            display: flex; flex-direction: column; gap: 10px;
            padding: 14px; border: 1px solid #e2e8f0; border-radius: 14px;
            background: #fff; transition: all 0.15s ease;
        }
        .rubrik-row:hover { border-color: #fbbf24; box-shadow: 0 4px 14px -6px rgba(251,191,36,0.4); }
        .rubrik-score-input {
            width: 110px; padding: 8px 12px; border: 1.5px solid #cbd5e1;
            border-radius: 10px; text-align: center; font-weight: 800; font-size: 14px;
            background: #f8fafc; color: #0f172a; outline: none;
        }
        .rubrik-score-input:focus { background: #fff; border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.2); }
        .grade-badge { display: inline-flex; align-items: center; justify-content: center;
            padding: 4px 16px; border-radius: 12px; font-weight: 900; font-size: 18px;
            border: 1px solid transparent; min-width: 60px; }
        .grade-A  { background:#d1fae5; color:#065f46; border-color:#6ee7b7; }
        .grade-AB { background:#ccfbf1; color:#115e59; border-color:#5eead4; }
        .grade-B  { background:#cffafe; color:#155e75; border-color:#67e8f9; }
        .grade-BC { background:#fef3c7; color:#92400e; border-color:#fcd34d; }
        .grade-C  { background:#fef9c3; color:#713f12; border-color:#fde047; }
        .grade-D  { background:#ffe4e6; color:#9f1239; border-color:#fda4af; }
        .grade-E  { background:#fecdd3; color:#881337; border-color:#fb7185; }
        .grade-none { background:#f1f5f9; color:#64748b; border-color:#cbd5e1; }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50/40 via-orange-50/25 to-slate-100 min-h-screen text-slate-800 antialiased flex flex-col justify-between selection:bg-orange-500 selection:text-white">

    <?php $this->load->view('partials/dosen_sidebar'); ?>

    <div class="page-wrapper-for-sidebar flex flex-col min-h-screen flex-grow">
    <main class="w-full px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex-grow space-y-7">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="p-5 rounded-3xl bg-emerald-50 border-2 border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center justify-between shadow-md shadow-emerald-500/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 box-3d">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <span><?= $this->session->flashdata('success'); ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold text-2xl leading-none">&times;</button>
            </div>
        <?php endif; ?>

        <div class="bg-gradient-to-r from-[#9a3412] via-[#ea580c] to-[#c2410c] rounded-3xl p-7 sm:p-9 relative overflow-hidden shadow-2xl text-white">
            <div class="relative z-10 flex flex-col xl:flex-row items-start xl:items-center justify-between gap-8">
                <div class="space-y-4 max-w-3xl">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                        <?= $dashboard_title ?>
                    </h1>
                    <p class="text-sm sm:text-base text-orange-100/95 font-normal leading-relaxed">
                        <?= $is_pembimbing 
                            ? 'Kelola dan evaluasi dokumen Tugas Akhir mahasiswa bimbingan Anda.' 
                            : 'Berikan komentar dan penilaian sebagai dosen penguji Tugas Akhir mahasiswa.' ?>
                    </p>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/15 border border-white/25 backdrop-blur-sm">
                        <i class="bi bi-person-badge-fill text-orange-200 text-sm"></i>
                        <span class="text-xs font-bold">Peran aktif: <?= $role_label ?></span>
                    </div>
                </div>

                <div class="w-full xl:w-[520px] bg-black/25 backdrop-blur-xl rounded-3xl p-5 border border-white/20 shadow-2xl">
                    <p class="text-[10px] font-black uppercase tracking-widest text-orange-200 mb-3 text-center">Pilih Peran Anda</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <a href="?role=p1" class="role-switch-btn <?= $is_p1 ? 'active' : 'inactive' ?>">
                            <i class="bi bi-person-fill-check"></i>
                            <span>Pembimbing 1</span>
                        </a>
                        <a href="?role=p2" class="role-switch-btn <?= $is_p2 ? 'active' : 'inactive' ?>">
                            <i class="bi bi-person-fill"></i>
                            <span>Pembimbing 2</span>
                        </a>
                        <a href="?role=u1" class="role-switch-btn <?= $is_u1 ? 'active' : 'inactive' ?>">
                            <i class="bi bi-shield-fill-check"></i>
                            <span>Penguji 1</span>
                        </a>
                        <a href="?role=u2" class="role-switch-btn <?= $is_u2 ? 'active' : 'inactive' ?>">
                            <i class="bi bi-shield-fill"></i>
                            <span>Penguji 2</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-7 sm:p-9 shadow-lg shadow-orange-500/5 space-y-7 border border-slate-200">
            <div class="flex flex-wrap items-center justify-between border-b border-orange-100 pb-5">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-900">
                        <i class="bi bi-people-fill text-orange-500 text-xl"></i>
                        Daftar Mahasiswa (<?= $role_label ?>)
                    </h3>
                </div>
            </div>

            <div class="flex flex-wrap gap-4 border-b border-slate-200 pb-4">
                <?php if ($is_pembimbing): ?>
                    <button onclick="switchDosenTab('preview1')" id="dosenTab1"
                        class="px-5 py-2.5 rounded-xl font-bold text-sm <?= $is_p1 || $is_p2 ? 'bg-orange-100 text-orange-700 border border-orange-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200' ?>">
                        Preview 1
                    </button>
                <?php endif; ?>
                <button onclick="switchDosenTab('preview2')" id="dosenTab2"
                    class="px-5 py-2.5 rounded-xl font-bold text-sm <?= $is_penguji ? 'bg-orange-100 text-orange-700 border border-orange-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200' ?>">
                    Preview 2
                </button>
                <button onclick="switchDosenTab('preview3')" id="dosenTab3"
                    class="px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200">
                    Preview 3
                </button>
                <button onclick="switchDosenTab('sidang')" id="dosenTab4"
                    class="px-5 py-2.5 rounded-xl font-bold text-sm bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200">
                    Sidang
                </button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-4 mb-2" id="filterCardsContainer">
                <div onclick="setDosenFilter('all')" id="fCard_all" class="p-3 rounded-xl border border-orange-300 bg-orange-50 cursor-pointer transition text-center shadow-xs">
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Semua</div>
                    <div class="text-xl font-black text-slate-800" id="fCount_all">0</div>
                </div>
                <div onclick="setDosenFilter('approved')" id="fCard_approved" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-200 cursor-pointer transition text-center">
                    <div class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">Disetujui</div>
                    <div class="text-xl font-black text-emerald-700" id="fCount_approved">0</div>
                </div>
                <div onclick="setDosenFilter('pending')" id="fCard_pending" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50 hover:border-amber-200 cursor-pointer transition text-center">
                    <div class="text-[10px] text-amber-600 font-bold uppercase tracking-wider">Pending</div>
                    <div class="text-xl font-black text-amber-700" id="fCount_pending">0</div>
                </div>
                <div onclick="setDosenFilter('revision')" id="fCard_revision" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-rose-50 hover:border-rose-200 cursor-pointer transition text-center">
                    <div class="text-[10px] text-rose-600 font-bold uppercase tracking-wider">Revisi</div>
                    <div class="text-xl font-black text-rose-700" id="fCount_revision">0</div>
                </div>
                <div onclick="setDosenFilter('empty')" id="fCard_empty" class="p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 cursor-pointer transition text-center">
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Kosong</div>
                    <div class="text-xl font-black text-slate-700" id="fCount_empty">0</div>
                </div>
            </div>

            <div id="dosenTableContainer" class="space-y-4">
                <div id="multiFilterPanelDW" class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-700 flex items-center gap-1.5">
                            <i class="bi bi-funnel-fill"></i> Filter Multi-Kriteria (Maksimal 4 Kriteria)
                        </span>
                        <div class="flex items-center gap-2">
                            <button type="button" id="btnAddFilterDW" onclick="addFilterRowDW()"
                                    class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-xs flex items-center gap-1.5 transition cursor-pointer">
                                <i class="bi bi-plus-lg"></i> Tambah Filter
                                <span id="filterCountBadgeDW" class="bg-white/30 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">1/4</span>
                            </button>
                            <button type="button" onclick="resetFiltersDW()"
                                    class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-orange-50 transition cursor-pointer flex items-center gap-1.5">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </div>
                    </div>

                    <div id="filterRowsDW" class="space-y-2 mb-3"></div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                        <p class="text-[10px] text-slate-400 font-medium italic">
                            <i class="bi bi-info-circle"></i> Tekan <strong>Enter</strong> atau klik <strong>Cari</strong> untuk menerapkan filter.
                        </p>
                        <button type="button" onclick="doMultiSearchDW()"
                                class="px-5 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs font-bold shadow-md flex items-center gap-1.5 transition cursor-pointer">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-1 px-1" id="paginationControls">
                    <div class="flex items-center gap-2 flex-wrap">
                        <label class="text-[11px] font-bold text-slate-600 whitespace-nowrap">Tampilkan:</label>
                        <select id="perPageSelect" onchange="changePerPage()" class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs font-bold bg-white focus:ring-orange-500 focus:border-orange-500 cursor-pointer">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="text-[11px] text-slate-500 font-medium" id="paginationInfo"></span>
                    </div>
                    <div class="flex items-center gap-1 flex-wrap" id="paginationButtons"></div>
                </div>

                <div class="table-rotating-border-wrap mt-2">
                    <span class="table-rotating-border-spin"></span>
                    <div class="table-rotating-border-inner overflow-x-auto">
                        <table class="table-custom-rounded text-left text-sm w-full">
                            <thead class="bg-slate-50 text-slate-700 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="py-4 px-4 text-center w-12">
                                        <input type="checkbox" id="checkAllDosenStudents" class="w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">
                                    </th>
                                    <th class="py-4 px-4 font-bold">Mahasiswa & Judul TA</th>
                                    <th class="py-4 px-4 text-center">Waktu Upload</th>
                                    <th class="py-4 px-4 text-center">Status Review</th>
                                    <?php if ($is_p1): ?>
                                        <th class="py-4 px-4 text-center">Rekomendasi</th>
                                    <?php endif; ?>
                                    <th class="py-4 px-4 text-center">Komentar</th>
                                    <th id="thBapCol" class="py-4 px-4 text-center" style="display: none;">BAP</th>
                                    <th class="py-4 px-4 text-center">Aksi</th>
                                    <th class="py-4 px-4 pr-6 text-center">
                                        <i class="bi bi-mortarboard-fill text-purple-500 mr-1"></i>Lulus Tahap
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium bg-white" id="bimbinganTableBody">
                                <tr><td colspan="8" class="text-center py-10 text-slate-500"><i class="bi bi-arrow-repeat animate-spin mr-2"></i> Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="lihatBerkasContainer" class="fixed inset-0 pointer-events-none z-50 flex items-stretch p-3 sm:p-5 gap-4 overflow-hidden" style="display: none;">
                <div id="wrapperDaftarMhs" class="flex flex-col gap-3 overflow-y-auto flex-shrink-0"></div>
                <div id="wrapperPreviewBerkas" class="flex-1 overflow-x-auto overflow-y-hidden gap-4 flex items-stretch hidden"></div>
            </div>

            <div id="commentModal" class="hidden fixed inset-0 z-[200] items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeCommentModal()"></div>
                <div class="relative bg-white rounded-3xl p-6 sm:p-8 w-full max-w-lg shadow-2xl flex flex-col max-h-[90vh]">
                    <button onclick="closeCommentModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 text-2xl leading-none">&times;</button>
                    <h3 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2" id="commentModalTitle">
                        <i class="bi bi-chat-quote-fill text-indigo-500"></i> Komentar Dosen
                    </h3>
                    <p class="text-xs text-slate-500 font-semibold mb-4" id="commentModalName">Nama Mahasiswa</p>

                    <div class="flex flex-wrap gap-1.5 mb-4 border-b border-slate-200 pb-3" id="commentTabsContainer">
                        <button onclick="switchCommentTab('p1')" id="commentTabP1" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-orange-100 text-orange-700 border border-orange-300 transition">Pembimbing 1</button>
                        <button onclick="switchCommentTab('p2')" id="commentTabP2" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200 transition">Pembimbing 2</button>
                        <button onclick="switchCommentTab('u1')" id="commentTabU1" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200 transition">Penguji 1</button>
                        <button onclick="switchCommentTab('u2')" id="commentTabU2" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200 transition">Penguji 2</button>
                    </div>

                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-800 font-medium leading-relaxed overflow-y-auto flex-1" id="commentModalContent"></div>
                </div>
            </div>

        </div>
    </main>
    </div><!-- /.page-wrapper-for-sidebar -->

    <div id="hoverPreviewPanel">
        <div class="panel-header">
            <i class="bi bi-person-circle"></i>
            <span id="hoverPanelName">Nama Mahasiswa</span>
        </div>
        <div class="pdf-frame-wrap" id="hoverPdfWrap">
            <div class="pdf-no-file"><i class="bi bi-file-earmark-x text-3xl"></i><span>Belum ada berkas</span></div>
        </div>
        <div class="panel-body">
            <div id="hoverFormWrap"></div>
        </div>
    </div>

    <div id="dosenBatchActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900/95 text-white px-5 py-3 rounded-2xl shadow-2xl backdrop-blur-md border border-slate-700 hidden flex-wrap items-center gap-4 transition-all duration-300">
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-orange-500 text-white font-black text-xs flex items-center justify-center shadow-xs" id="dosenSelectedCountBadge">0</span>
            <span class="text-xs font-bold tracking-tight">Mahasiswa Terpilih</span>
        </div>
        <div class="h-5 w-px bg-slate-700 hidden sm:block"></div>
        <div class="flex items-center gap-2.5">
            <button type="button" onclick="openDosenBatchModal()" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white rounded-xl text-xs font-extrabold shadow-md flex items-center gap-2 transition-all active:scale-95 cursor-pointer">
                <i class="bi bi-files"></i> Preview Massal
            </button>
            <button type="button" onclick="submitDosenBatchApprove()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md flex items-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                <i class="bi bi-check2-all"></i> <?= $is_p1 ? 'Approve Massal' : 'Tinjau Massal' ?>
            </button>
            <button type="button" onclick="unselectAllDosenStudents()" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition-all cursor-pointer">
                <i class="bi bi-x-lg"></i> Batal
            </button>
        </div>
    </div>

    <div id="dosenBatchReviewModal" class="hidden fixed inset-0 z-[100] overflow-y-auto p-4 sm:p-6">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeDosenBatchModal()"></div>
        <div class="relative bg-white rounded-3xl w-full max-w-6xl mx-auto flex flex-col overflow-hidden shadow-2xl my-4 sm:my-8">
            <div class="p-4 px-6 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <h3 class="text-sm font-extrabold flex items-center gap-2">
                    <i class="bi bi-files text-orange-500"></i> Review Preview Massal
                    <span class="text-[10px] font-normal text-slate-400 ml-1">— Setiap kartu berisi preview langsung, komentar, dan tombol simpan</span>
                </h3>
                <button type="button" onclick="closeDosenBatchModal()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all cursor-pointer">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>
            <div class="p-5 sm:p-6 flex-1 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="dosenBatchModalBody"></div>
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
                <span class="text-xs text-slate-500 font-medium">Simpan setiap kartu secara individual, atau gunakan aksi massal dari toolbar bawah.</span>
                <button type="button" onclick="closeDosenBatchModal()" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl transition cursor-pointer">Tutup</button>
            </div>
        </div>
    </div>

    <?php $this->load->view('partials/modal_rekomendasi_sidang'); ?>

    <div id="modalPenilaianSidang">
        <div class="penilaian-box relative z-10">
            <div class="p-5 sm:p-6 px-7 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-amber-50/90 via-orange-50/40 to-white shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-amber-500/25 shrink-0">
                        <i class="bi bi-clipboard-check-fill"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Form Penilaian Sidang Tugas Akhir</h3>
                            <span id="penilaianRoleBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200"><?= $role_label ?></span>
                        </div>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Nilai akan disimpan sesuai peran Anda.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalPenilaianSidang()" class="w-9 h-9 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 hover:bg-slate-200 flex items-center justify-center transition cursor-pointer">
                    <i class="bi bi-x-lg text-base"></i>
                </button>
            </div>

            <form id="formPenilaianSidang" onsubmit="submitPenilaianSidang(event)" class="penilaian-body space-y-5">
                <input type="hidden" id="penilaianNim">

                <div class="bg-slate-50/80 rounded-2xl p-4 sm:p-5 border border-slate-200/80 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/60 pb-3">
                        <div>
                            <h4 id="penilaianNamaMhs" class="text-sm font-extrabold text-slate-900">-</h4>
                            <p class="text-xs font-mono font-bold text-slate-500" id="penilaianNimMhs">-</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap text-[11px]">
                            <span class="px-3 py-1 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold flex items-center gap-1.5">
                                <i class="bi bi-calendar-day text-amber-500"></i>
                                <span id="penilaianTglText">Belum Ada Jadwal</span>
                            </span>
                            <span class="px-3 py-1 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-800 font-bold flex items-center gap-1.5">
                                <i class="bi bi-door-open text-cyan-600"></i>
                                <span id="penilaianRuanganText">-</span>
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Judul Tugas Akhir:</p>
                        <p id="penilaianJudulTa" class="text-xs font-medium text-slate-800 mt-0.5 leading-relaxed">-</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-[11px]">
                        <div class="p-2.5 rounded-xl bg-white border border-slate-200/70 space-y-1">
                            <span class="font-bold text-orange-800 block text-[10px] uppercase tracking-wider"><i class="bi bi-person-fill text-orange-600 mr-1"></i> Dosen Pembimbing</span>
                            <p class="text-slate-700 truncate" id="penilaianPembimbing1">Pembimbing 1: -</p>
                            <p class="text-slate-700 truncate" id="penilaianPembimbing2">Pembimbing 2: -</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white border border-slate-200/70 space-y-1">
                            <span class="font-bold text-indigo-800 block text-[10px] uppercase tracking-wider"><i class="bi bi-shield-fill text-indigo-600 mr-1"></i> Dewan Penguji</span>
                            <p class="text-slate-700 truncate" id="penilaianPenguji1">Penguji 1: -</p>
                            <p class="text-slate-700 truncate" id="penilaianPenguji2">Penguji 2: -</p>
                        </div>
                    </div>
                    <div id="penilaianPrevNilaiWrap" class="hidden p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px]">
                        <span class="font-bold text-amber-800 uppercase tracking-wider text-[10px] block mb-0.5">
                            <i class="bi bi-info-circle-fill"></i> Nilai Tersimpan
                        </span>
                        <p class="text-amber-900">
                            Nilai Anda sebelumnya: <strong id="penilaianPrevNilai">-</strong>.
                            Isi form di bawah untuk memperbarui.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 block mb-1.5">Program Studi</label>
                        <select id="penilaianProdiSelect" onchange="onPenilaianProdiChange(this.value)"
                            class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 outline-none cursor-pointer">
                            <option value="DKV">DKV - Desain Komunikasi Visual</option>
                            <option value="SI">SI - Sistem Informasi</option>
                            <option value="IF">IF - Informatika</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 block mb-1.5">Peminatan / Konsentrasi</label>
                        <select id="penilaianPeminatanSelect" onchange="onPenilaianPeminatanChange(this.value)"
                            class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 outline-none cursor-pointer"></select>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                            <i class="bi bi-list-check text-amber-600"></i> Rubrik Penilaian
                        </label>
                        <span class="text-[11px] font-semibold text-slate-400">Total Bobot: <strong class="text-slate-700" id="penilaianTotalBobotLabel">100%</strong></span>
                    </div>
                    <div id="penilaianRubrikContainer" class="space-y-3"></div>
                </div>

                <div class="bg-gradient-to-br from-amber-500/10 via-orange-500/5 to-white rounded-2xl p-5 border border-amber-300/80">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                        <div class="text-center sm:text-left">
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">Nilai Akhir</span>
                            <div class="flex items-baseline gap-1.5 justify-center sm:justify-start mt-0.5">
                                <span id="penilaianTotalScore" class="text-3xl font-black text-slate-900">0.00</span>
                                <span class="text-xs font-bold text-slate-400">/ 100</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">Grade</span>
                            <div class="mt-1 flex items-center justify-center">
                                <span id="penilaianGradeBadge" class="grade-badge grade-none">-</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block mb-1">Status Kelulusan</span>
                            <select id="penilaianStatusKelulusan"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-amber-500 outline-none cursor-pointer">
                                <option value="Lulus">✅ Lulus</option>
                                <option value="Lulus dengan Revisi">⚠️ Lulus dengan Revisi</option>
                                <option value="Tidak Lulus">❌ Tidak Lulus</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700 block mb-1.5">Catatan / Berita Acara (Opsional)</label>
                    <textarea id="penilaianCatatan" rows="3" placeholder="Masukkan poin-poin revisi naskah atau catatan sidang..."
                        class="w-full text-xs p-3 border border-slate-300 rounded-xl bg-white focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 resize-none"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3 flex-wrap">
                    <button type="button" onclick="closeModalPenilaianSidang()"
                        class="px-5 py-3 bg-white border border-slate-300 text-slate-700 font-bold text-xs sm:text-sm rounded-2xl hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitPenilaianSidang"
                        class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold text-xs sm:text-sm rounded-2xl shadow-md shadow-amber-500/20 transition flex items-center gap-2 cursor-pointer active:scale-95">
                        <i class="bi bi-save-fill"></i> Simpan Penilaian Sidang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ============================================================
        // ROLE CONSTANTS
        // ============================================================
        const ROLE            = '<?= $role ?>';
        const IS_P1           = <?= $is_p1 ? 'true' : 'false' ?>;
        const IS_P2           = <?= $is_p2 ? 'true' : 'false' ?>;
        const IS_U1           = <?= $is_u1 ? 'true' : 'false' ?>;
        const IS_U2           = <?= $is_u2 ? 'true' : 'false' ?>;
        const IS_PEMBIMBING   = IS_P1 || IS_P2;
        const IS_PENGUJI      = IS_U1 || IS_U2;
        const DISPLAY_POSISI  = <?= $display_posisi ?>;
        const MODEL_POSISI    = <?= $model_posisi ?>;

        const FETCH_QUERY = IS_PEMBIMBING
            ? `posisi=${DISPLAY_POSISI}`
            : `model_posisi=${MODEL_POSISI}`;

        // ============================================================
        // PIC (Penanggung Jawab) per Tahap
        // Preview 1 → P1, Preview 2/3/Sidang → U1
        // ============================================================
        function getResponsibleRole(tahap) {
            return (tahap === 'Preview 1') ? 'p1' : 'u1';
        }
        function isCurrentUserPIC() {
            const pic = getResponsibleRole(currentTahap);
            if (pic === 'p1') return IS_P1;
            if (pic === 'u1') return IS_U1;
            return false;
        }
        function getFileStatus(preview, fileType) {
            const col = 'status_file_' + fileType;
            return (preview && preview[col]) ? preview[col] : 'Pending';
        }
        function getFileCatatan(preview, fileType) {
            const col = 'catatan_file_' + fileType;
            return (preview && preview[col]) ? preview[col] : '';
        }
        function getTahapKey(tahap) {
            return ({ 'Preview 1':'preview1', 'Preview 2':'preview2', 'Preview 3':'preview3', 'Sidang':'sidang' })[tahap] || 'preview1';
        }
        function isStageLulus(preview) {
            if (!preview) return false;
            const key = getTahapKey(currentTahap);
            return String(preview['lulus_' + key] ?? 0) === '1';
        }
        function areAllFilesApproved(preview, fileList) {
            if (!fileList || fileList.length === 0) return false;
            return fileList.every(f => getFileStatus(preview, f.type) === 'Approved');
        }
        function renderFileStatusBadge(preview, fileType) {
            const st = getFileStatus(preview, fileType);
            if (st === 'Approved') return `<span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">✓ ACC</span>`;
            if (st === 'Revision') return `<span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200">✗ Revisi</span>`;
            return `<span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">⏱ Pending</span>`;
        }

        // ============================================================
        // FILE-TYPE HELPERS (NEW)
        // ============================================================
        function getFileTypesForTahap(tahap) {
            if (tahap === 'Preview 3') return ['sitasi', 'bimbingan', 'persyaratan'];
            if (tahap === 'Sidang')    return ['sidang'];
            return ['draft'];
        }
        function getFileNameByType(preview, type) {
            if (!preview) return '';
            if (type === 'draft')       return preview.file_draft || '';
            if (type === 'sitasi')      return preview.file_sitasi || '';
            if (type === 'bimbingan')   return preview.file_bimbingan || '';
            if (type === 'persyaratan') return preview.file_persyaratan || '';
            if (type === 'sidang')      return preview.file_sidang || preview.file_draft || '';
            return '';
        }
        function countFileStatuses(preview, tahap) {
            const types = getFileTypesForTahap(tahap);
            let approved = 0, revision = 0, pending = 0, missing = 0;
            types.forEach(t => {
                if (!preview) { missing++; return; }
                const fname = getFileNameByType(preview, t);
                if (!fname) { missing++; return; }
                const st = getFileStatus(preview, t);
                if (st === 'Approved') approved++;
                else if (st === 'Revision') revision++;
                else pending++;
            });
            return { approved, revision, pending, missing, total: types.length };
        }
        function getTableColspan() {
            let cols = 7; // checkbox, mhs, waktu, status, komentar, aksi, lulus-tahap
            if (IS_P1) cols++;
            const showBap = IS_U1 && currentTahap === 'Sidang';
            if (showBap) cols++;
            return cols;
        }

        function getUploadBaseUrl(tahap) {
            return (tahap === 'Sidang')
                ? '<?= base_url('uploads/sidang/') ?>'
                : '<?= base_url('uploads/preview_ta/') ?>';
        }

        function getCurrentComment(preview) {
            if (IS_P1) return preview.catatan_pembimbing   || '';
            if (IS_P2) return preview.catatan_pembimbing_2 || '';
            if (IS_U1) return preview.catatan_penguji_1    || '';
            if (IS_U2) return preview.catatan_penguji_2    || '';
            return '';
        }

        function setCurrentComment(preview, value) {
            if (IS_P1) preview.catatan_pembimbing   = value;
            else if (IS_P2) preview.catatan_pembimbing_2 = value;
            else if (IS_U1) preview.catatan_penguji_1    = value;
            else if (IS_U2) preview.catatan_penguji_2    = value;
        }

        let currentTahap        = '<?= $default_tahap ?>';
        let bimbinganData       = [];
        let currentDosenFilter  = 'all';
        let filtersDW         = [{ col: 'all', val: '' }];
        let appliedFiltersDW  = [{ col: 'all', val: '' }];
        let currentPage = 1;
        let perPage     = 25;

        window.activeLihatBerkasIndices = [];
        window.activePreviews           = [];
        window.activeInlineEditorKey    = null;
        let pendingAction = null;

        // ============================================================
        // MULTI-CRITERIA FILTER
        // ============================================================
        function addFilterRowDW(col = 'all', val = '') {
            if (filtersDW.length >= 4) { showToast('Maksimal 4 kriteria filter.', 'error'); return; }
            filtersDW.push({ col, val });
            renderFilterRowsDW();
        }
        function removeFilterRowDW(idx) {
            if (filtersDW.length <= 1) return;
            filtersDW.splice(idx, 1);
            renderFilterRowsDW();
        }
        function renderFilterRowsDW() {
            const container = document.getElementById('filterRowsDW');
            if (!container) return;
            const COLUMNS = [
                { label: 'Semua Kolom', value: 'all' },
                { label: 'Nama Mahasiswa', value: 'nama' },
                { label: 'NIM', value: 'nim' },
                { label: 'Judul TA', value: 'judul' },
                { label: 'Status Review', value: 'status' },
            ];
            container.innerHTML = filtersDW.map((f, i) => `
                <div class="filter-row-dw">
                    <span class="text-[10px] font-bold text-slate-500 w-14 shrink-0">Filter #${i + 1}:</span>
                    <select class="filter-col-dw" data-idx="${i}">
                        ${COLUMNS.map(c => `<option value="${c.value}" ${c.value === f.col ? 'selected' : ''}>${c.label}</option>`).join('')}
                    </select>
                    <input type="text" class="filter-val-dw" data-idx="${i}"
                           value="${(f.val || '').replace(/"/g, '&quot;')}"
                           placeholder="Ketik kata kunci pencarian..."
                           onkeydown="if(event.key==='Enter'){event.preventDefault();doMultiSearchDW();}">
                    ${i > 0 ? `<button type="button" class="btn-remove-filter-dw" onclick="removeFilterRowDW(${i})" title="Hapus filter"><i class="bi bi-x-lg"></i></button>` : ''}
                </div>
            `).join('');
            updateFilterBadgeDW();
        }
        function updateFilterBadgeDW() {
            const badge = document.getElementById('filterCountBadgeDW');
            const btnAdd = document.getElementById('btnAddFilterDW');
            if (badge) badge.textContent = `${filtersDW.length}/4`;
            if (btnAdd) {
                btnAdd.style.opacity = filtersDW.length >= 4 ? '0.5' : '1';
                btnAdd.style.pointerEvents = filtersDW.length >= 4 ? 'none' : 'auto';
            }
        }
        function doMultiSearchDW() {
            document.querySelectorAll('.filter-col-dw').forEach(sel => {
                const idx = parseInt(sel.dataset.idx);
                if (filtersDW[idx]) filtersDW[idx].col = sel.value;
            });
            document.querySelectorAll('.filter-val-dw').forEach(inp => {
                const idx = parseInt(inp.dataset.idx);
                if (filtersDW[idx]) filtersDW[idx].val = inp.value;
            });
            appliedFiltersDW = filtersDW.map(f => ({ ...f }));
            currentPage = 1;
            renderTable();
        }
        function resetFiltersDW() {
            filtersDW = [{ col: 'all', val: '' }];
            appliedFiltersDW = [{ col: 'all', val: '' }];
            currentPage = 1;
            renderFilterRowsDW();
            setDosenFilter('all');
            renderTable();
        }
        window.doSearch = doMultiSearchDW;

        // ============================================================
        // PAGINATION
        // ============================================================
        function changePerPage() {
            const val = document.getElementById('perPageSelect').value;
            perPage = val === 'all' ? 999999 : parseInt(val);
            currentPage = 1;
            renderTable();
        }
        function goToPage(page) {
            if (page < 1) return;
            currentPage = page;
            renderTable();
            const top = document.getElementById('dosenTableContainer');
            if (top) top.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        function renderPagination(totalItems, startIdx, endIdx) {
            const info = document.getElementById('paginationInfo');
            const btns = document.getElementById('paginationButtons');
            if (!info || !btns) return;
            if (totalItems === 0) { info.textContent = 'Tidak ada data'; btns.innerHTML = ''; return; }
            info.textContent = `Menampilkan ${startIdx + 1}-${endIdx} dari ${totalItems}`;
            if (perPage >= 999999) { btns.innerHTML = ''; return; }
            const totalPages = Math.ceil(totalItems / perPage);
            let html = '';
            html += `<button class="pagination-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><i class="bi bi-chevron-left"></i></button>`;
            const maxButtons = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage + 1 < maxButtons) startPage = Math.max(1, endPage - maxButtons + 1);
            if (startPage > 1) {
                html += `<button class="pagination-btn" onclick="goToPage(1)">1</button>`;
                if (startPage > 2) html += `<span class="pagination-ellipsis">…</span>`;
            }
            for (let i = startPage; i <= endPage; i++) {
                html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
            }
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) html += `<span class="pagination-ellipsis">…</span>`;
                html += `<button class="pagination-btn" onclick="goToPage(${totalPages})">${totalPages}</button>`;
            }
            html += `<button class="pagination-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><i class="bi bi-chevron-right"></i></button>`;
            btns.innerHTML = html;
        }

        // ============================================================
        // INIT
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            if (IS_PEMBIMBING) {
                if (document.getElementById('dosenTab1')) setTabActive('dosenTab1');
            } else if (IS_PENGUJI) {
                setTabActive('dosenTab2');
            }
            renderFilterRowsDW();
            fetchBimbinganData();
            startDosenSSE();
        });

        function setTabActive(id) {
            const activeCls   = ['bg-orange-100','text-orange-700','border','border-orange-300'];
            const inactiveCls = ['bg-slate-100','text-slate-600','hover:bg-slate-200','border','border-slate-200'];
            ['dosenTab1','dosenTab2','dosenTab3','dosenTab4'].forEach(tid => {
                const el = document.getElementById(tid);
                if (!el) return;
                activeCls.forEach(c => el.classList.remove(c));
                inactiveCls.forEach(c => el.classList.remove(c));
                if (tid === id) activeCls.forEach(c => el.classList.add(c));
                else inactiveCls.forEach(c => el.classList.add(c));
            });
        }

        function switchDosenTab(tab) {
            // Close any open inline editor before switching
            closeInlineComment();

            if (tab === 'preview1') {
                if (!IS_PEMBIMBING) return;
                currentTahap = 'Preview 1';
                setTabActive('dosenTab1');
            } else if (tab === 'preview2') {
                currentTahap = 'Preview 2';
                setTabActive('dosenTab2');
            } else if (tab === 'preview3') {
                currentTahap = 'Preview 3';
                setTabActive('dosenTab3');
            } else if (tab === 'sidang') {
                currentTahap = 'Sidang';
                setTabActive('dosenTab4');
            }
            currentPage = 1;
            fetchBimbinganData();
        }

        // ============================================================
        // FETCH DATA
        // ============================================================
        function fetchBimbinganData(silent = false) {
            const tbody = document.getElementById('bimbinganTableBody');
            const colspan = getTableColspan();
            if (!silent) tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-10 text-slate-500"><i class="bi bi-arrow-repeat animate-spin text-xl"></i> Memuat data...</td></tr>`;

            fetch(`<?= site_url('mahasiswa/ajax_get_dosen_bimbingan') ?>?${FETCH_QUERY}&tahap=${encodeURIComponent(currentTahap)}`)
                .then(res => {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.text();
                })
                .then(text => {
                    let res;
                    try { res = JSON.parse(text); }
                    catch(e) {
                        console.error('Server response (not JSON):', text);
                        tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-10 text-rose-500 text-xs">Server mengembalikan response tidak valid. Cek Console (F12).</td></tr>`;
                        return;
                    }
                    if (res.status) {
                        bimbinganData = res.data;
                        bimbinganData.sort((a, b) => {
                            let dateA = a.latest_preview ? new Date(a.latest_preview.created_at).getTime() : 0;
                            let dateB = b.latest_preview ? new Date(b.latest_preview.created_at).getTime() : 0;
                            return dateB - dateA;
                        });
                        updateFilterCounts();
                        renderTable();
                    } else {
                        tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-10 text-rose-500">${res.message}</td></tr>`;
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-10 text-rose-500">Terjadi kesalahan koneksi: ${err.message}</td></tr>`;
                });
        }

        function setDosenFilter(filter) {
            currentDosenFilter = filter;
            currentPage = 1;
            ['all', 'approved', 'pending', 'revision', 'empty'].forEach(f => {
                let card = document.getElementById('fCard_' + f);
                if (!card) return;
                card.classList.remove('border-orange-300', 'bg-orange-50', 'shadow-xs');
                if (f === filter) card.classList.add('border-orange-300', 'bg-orange-50', 'shadow-xs');
                else card.classList.add('border-slate-200', 'bg-slate-50');
            });
            renderTable();
        }

        function updateFilterCounts() {
            let counts = { all: 0, approved: 0, pending: 0, revision: 0, empty: 0 };
            bimbinganData.forEach(mhs => {
                counts.all++;
                if (!mhs.latest_preview) counts.empty++;
                else {
                    let st = mhs.latest_preview.status_pembimbing;
                    if (st === 'Approved') counts.approved++;
                    else if (st === 'Revision') counts.revision++;
                    else counts.pending++;
                }
            });
            for (const [key, val] of Object.entries(counts)) {
                const el = document.getElementById('fCount_' + key);
                if (el) el.textContent = val;
            }
        }

        // ============================================================
        // RENDER CELLS (NEW)
        // ============================================================
        function renderStatusReviewCell(mhs) {
            if (!mhs.latest_preview) {
                return `<span class="badge badge-secondary"><i class="bi bi-dash"></i> Kosong</span>`;
            }
            const latest = mhs.latest_preview;
            const isLulus = isStageLulus(latest);
            const c = countFileStatuses(latest, currentTahap);

            const badges = [];
            if (c.approved > 0) badges.push(`<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1"><i class="bi bi-check-circle-fill"></i> ${c.approved} ACC</span>`);
            if (c.revision > 0) badges.push(`<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center gap-1"><i class="bi bi-x-circle-fill"></i> ${c.revision} Revisi</span>`);
            if (c.pending > 0)  badges.push(`<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1"><i class="bi bi-clock-fill"></i> ${c.pending} Pending</span>`);

            if (badges.length === 0 && c.missing === c.total) {
                badges.push(`<span class="badge badge-secondary"><i class="bi bi-dash"></i> Kosong</span>`);
            }

            const lulusBadge = isLulus
                ? `<span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 inline-flex items-center gap-1"><i class="bi bi-mortarboard-fill"></i> Lulus Tahap</span>`
                : '';

            return `
                <div class="flex flex-col gap-1 items-center">
                    ${lulusBadge}
                    <div class="flex flex-wrap gap-1 justify-center">${badges.join('')}</div>
                </div>
            `;
        }

        function renderLulusTahapCell(mhs) {
            if (!mhs.latest_preview) return `<span class="text-slate-400 italic text-[11px]">—</span>`;
            if (!isCurrentUserPIC())  return `<span class="text-slate-400 italic text-[11px]">—</span>`;

            const latest = mhs.latest_preview;
            const isLulus = isStageLulus(latest);
            const c = countFileStatuses(latest, currentTahap);
            const order = ['Preview 1', 'Preview 2', 'Preview 3', 'Sidang'];
            const currIdx = order.indexOf(currentTahap);

            let btnLulus = '';
            if (currentTahap !== 'Sidang') {
                if (isLulus) {
                    btnLulus = `<span class="px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 border border-emerald-300 text-[11px] font-bold inline-flex items-center gap-1.5 whitespace-nowrap"><i class="bi bi-mortarboard-fill"></i> Sudah Lulus</span>`;
                } else if (c.approved > 0) {
                    btnLulus = `<button onclick="handleLulusStage('${mhs.nim}')" class="px-3 py-1.5 rounded-lg bg-gradient-to-r from-purple-500 to-fuchsia-600 hover:from-purple-600 hover:to-fuchsia-700 text-white font-bold text-[11px] shadow-md inline-flex items-center gap-1.5 cursor-pointer transition whitespace-nowrap"><i class="bi bi-mortarboard-fill"></i> Lulus Tahap</button>`;
                } else {
                    btnLulus = `<button disabled title="Belum ada berkas yang di-ACC" class="px-3 py-1.5 rounded-lg bg-slate-200 text-slate-400 font-bold text-[11px] cursor-not-allowed inline-flex items-center gap-1.5 whitespace-nowrap"><i class="bi bi-lock-fill"></i> Lulus Tahap</button>`;
                }
            }

            let btnDowngrade = '';
            if (currIdx > 0) {
                const targetTahap = order[currIdx - 1];
                btnDowngrade = `<button onclick="handleDowngradeStage('${mhs.nim}')" class="px-3 py-1.5 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold text-[11px] shadow-md inline-flex items-center gap-1.5 cursor-pointer transition whitespace-nowrap"><i class="bi bi-arrow-down-circle"></i> Ke ${targetTahap}</button>`;
            }

            if (!btnLulus && !btnDowngrade) return `<span class="text-slate-400 italic text-[11px]">—</span>`;

            return `<div class="flex flex-col gap-1.5 items-center max-w-[150px] mx-auto">${btnLulus}${btnDowngrade}</div>`;
        }

        // ============================================================
        // RENDER TABLE
        // ============================================================
        function renderTable() {
            const tbody = document.getElementById('bimbinganTableBody');

            const showBapCol = (typeof IS_U1 !== 'undefined' && IS_U1) && currentTahap === 'Sidang';
            const thBapEl = document.getElementById('thBapCol');
            if (thBapEl) thBapEl.style.display = showBapCol ? '' : 'none';

            const filteredData = [];
            bimbinganData.forEach((mhs, originalIndex) => {
                let matchMulti = true;
                for (const f of appliedFiltersDW) {
                    if (!f || !f.val || !f.val.trim()) continue;
                    const q = f.val.toLowerCase().trim();
                    let hay = '';
                    if (f.col === 'all') {
                        hay = [mhs.nim, mhs.nama_mahasiswa, mhs.judul || ''].join(' ').toLowerCase();
                    } else if (f.col === 'nim') hay = (mhs.nim || '').toLowerCase();
                    else if (f.col === 'nama') hay = (mhs.nama_mahasiswa || '').toLowerCase();
                    else if (f.col === 'judul') hay = (mhs.judul || '').toLowerCase();
                    else if (f.col === 'status') {
                        let st = mhs.latest_preview ? (mhs.latest_preview.status_pembimbing || 'Pending') : 'Kosong';
                        hay = st.toLowerCase();
                    }
                    if (!hay.includes(q)) { matchMulti = false; break; }
                }
                if (!matchMulti) return;

                if (currentDosenFilter !== 'all') {
                    if (currentDosenFilter === 'empty' && mhs.latest_preview) return;
                    if (currentDosenFilter !== 'empty' && !mhs.latest_preview) return;
                    if (currentDosenFilter !== 'empty' && mhs.latest_preview) {
                        let st = mhs.latest_preview.status_pembimbing;
                        if (currentDosenFilter === 'approved' && st !== 'Approved') return;
                        if (currentDosenFilter === 'revision' && st !== 'Revision') return;
                        if (currentDosenFilter === 'pending'  && (st === 'Approved' || st === 'Revision')) return;
                    }
                }
                filteredData.push({ mhs, originalIndex });
            });

            const totalItems = filteredData.length;
            let startIdx = 0, endIdx = totalItems;
            if (perPage < 999999 && totalItems > 0) {
                const totalPages = Math.ceil(totalItems / perPage);
                if (currentPage > totalPages) currentPage = totalPages;
                if (currentPage < 1) currentPage = 1;
                startIdx = (currentPage - 1) * perPage;
                endIdx = Math.min(startIdx + perPage, totalItems);
            }
            const pageData = filteredData.slice(startIdx, endIdx);

            let html = '';
            pageData.forEach(({ mhs, originalIndex }) => {
                const index = originalIndex;

                let previewHtml = `<span class="text-slate-400 italic text-xs">Belum ada berkas</span>`;
                let timeHtml    = `-`;
                let statusReviewHtml = renderStatusReviewCell(mhs);
                let rekomenCell = '';
                let btnHtml = `<button disabled class="px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg text-xs font-bold cursor-not-allowed border border-slate-200">Belum ada file</button>`;

                if (mhs.latest_preview) {
                    const latest = mhs.latest_preview;
                    const btnClass = latest.file_missing ? 'bg-rose-100 text-rose-700 opacity-80' : 'btn-3d-orange scale-95 hover:scale-100';
                    const icon     = latest.file_missing ? 'bi-exclamation-triangle-fill' : 'fa-solid fa-folder-open';
                    const label    = latest.file_missing ? 'File Hilang' : 'Lihat Berkas';
                    const countBadge = (mhs.riwayat_previews && mhs.riwayat_previews.length > 1)
                        ? ` <span class="bg-white/30 text-white px-1.5 py-0.5 rounded-md ml-1 text-[10px] font-black">${mhs.riwayat_previews.length}</span>` : '';

                    previewHtml = `<button onclick="toggleLihatBerkasPanel(${index})" class="${btnClass} inline-flex items-center gap-1.5 text-white font-bold px-3 py-1.5 rounded-xl text-xs cursor-pointer shadow-md transition-transform"><i class="${icon} text-xs"></i> ${label}${countBadge}</button>`;

                    const dt = new Date(latest.created_at);
                    timeHtml = `<div class="text-xs font-semibold text-slate-700">${dt.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'})}</div><div class="text-[10px] text-slate-500">${dt.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})} WIB</div>`;

                    if (IS_P1) {
                        let rekomenBadge = `<button onclick="openRekomendasiModal('${mhs.nim}', '${latest.id}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer whitespace-nowrap"><i class="bi bi-plus-circle-fill"></i> Rekomendasi</button>`;
                        if (mhs.rekomendasi) {
                            if (mhs.rekomendasi.recommendation_type === 'sidang') {
                                rekomenBadge = `<span onclick="openRekomendasiModal('${mhs.nim}', '${latest.id}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-100 text-purple-800 border border-purple-300 rounded-xl text-xs font-extrabold cursor-pointer hover:bg-purple-200 transition whitespace-nowrap shadow-2xs" title="Klik untuk ubah rekomendasi"><i class="bi bi-mortarboard-fill text-purple-600"></i> Sidang TA</span>`;
                            } else if (mhs.rekomendasi.recommendation_type === 'non_sidang') {
                                const titleText = mhs.rekomendasi.jalur_title || 'Non-Sidang';
                                rekomenBadge = `<span onclick="openRekomendasiModal('${mhs.nim}', '${latest.id}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-900 border border-amber-300 rounded-xl text-xs font-extrabold cursor-pointer hover:bg-amber-200 transition whitespace-nowrap shadow-2xs" title="Jalur: ${titleText} (Klik untuk ubah)"><i class="bi bi-award-fill text-amber-600 text-sm"></i> ${titleText}</span>`;
                            }
                        }
                        rekomenCell = `<td data-label="Rekomendasi" class="py-4 px-4 text-center">${rekomenBadge}</td>`;
                    }

                    btnHtml = previewHtml;
                }

                // P1 without preview: masih butuh kolom rekomendasi kosong
                if (IS_P1 && !mhs.latest_preview) {
                    rekomenCell = `<td data-label="Rekomendasi" class="py-4 px-4 text-center"><span class="text-slate-400 italic text-xs">-</span></td>`;
                }

                let checkboxHtml = '';
                if (mhs.latest_preview && !mhs.latest_preview.file_missing) {
                    if (mhs.latest_preview.status_pembimbing !== 'Approved') {
                        checkboxHtml = `<input type="checkbox" value="${mhs.latest_preview.id}" data-name="${mhs.nama_mahasiswa}" data-file="${mhs.latest_preview.file_draft}" data-id="${mhs.latest_preview.id}" data-status="${mhs.latest_preview.status_pembimbing || 'Pending'}" data-catatan="${encodeURIComponent(mhs.latest_preview.catatan_pembimbing || '')}" data-catatan2="${encodeURIComponent(mhs.latest_preview.catatan_pembimbing_2 || '')}" class="dosen-student-cb w-4 h-4 text-orange-600 rounded border-slate-300 focus:ring-orange-500 cursor-pointer">`;
                    } else {
                        checkboxHtml = `<input type="checkbox" disabled class="w-4 h-4 rounded border-slate-200 cursor-not-allowed opacity-50" title="Sudah Disetujui">`;
                    }
                } else {
                    checkboxHtml = `<input type="checkbox" disabled class="w-4 h-4 rounded border-slate-200 cursor-not-allowed opacity-50" title="Belum ada berkas atau file hilang">`;
                }

                let p1Comment = mhs.latest_preview ? (mhs.latest_preview.catatan_pembimbing   || '') : '';
                let p2Comment = mhs.latest_preview ? (mhs.latest_preview.catatan_pembimbing_2 || '') : '';
                let u1Comment = mhs.latest_preview ? (mhs.latest_preview.catatan_penguji_1    || '') : '';
                let u2Comment = mhs.latest_preview ? (mhs.latest_preview.catatan_penguji_2    || '') : '';
                let hasAnyComment = p1Comment.trim().length > 0 || p2Comment.trim().length > 0 || u1Comment.trim().length > 0 || u2Comment.trim().length > 0;

                let commentCountParts = [];
                if (p1Comment.trim()) commentCountParts.push('P1');
                if (p2Comment.trim()) commentCountParts.push('P2');
                if (u1Comment.trim()) commentCountParts.push('U1');
                if (u2Comment.trim()) commentCountParts.push('U2');

                let commentBtnHtml = hasAnyComment
                    ? `<button onclick="showUnifiedCommentModal('${mhs.nama_mahasiswa}', '${encodeURIComponent(p1Comment)}', '${encodeURIComponent(p2Comment)}', '${encodeURIComponent(u1Comment)}', '${encodeURIComponent(u2Comment)}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-100 hover:bg-violet-200 text-violet-700 border border-violet-200 rounded-lg text-xs font-bold transition cursor-pointer"><i class="bi bi-chat-quote-fill"></i> ${commentCountParts.join(', ')}</button>`
                    : `<span class="text-slate-400 text-xs italic">-</span>`;

                const namaEscaped = (mhs.nama_mahasiswa || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");

                const lulusTahapHtml = renderLulusTahapCell(mhs);

                html += `
                    <tr class="hover:bg-slate-50 transition-colors" data-index="${index}" data-nim="${mhs.nim}">
                        <td class="dosen-cb-cell py-4 px-4 text-center">${checkboxHtml}</td>
                        <td class="mhs-info-cell py-4 px-4">
                            <div class="relative inline-block group">
                                <span class="font-bold text-slate-900 cursor-pointer hover:text-orange-600 transition-colors underline decoration-dotted decoration-orange-300 underline-offset-2" onmouseenter="showHoverPanel(event, ${index})" onmouseleave="scheduleHidePanel()">${mhs.nama_mahasiswa}</span>
                            </div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">${mhs.nim}</div>
                            ${mhs.judul ? `<div class="text-[10px] text-slate-500 font-medium italic mt-1 line-clamp-2 max-w-[250px]" title="${mhs.judul}">"${mhs.judul}"</div>` : ''}
                        </td>
                        <td data-label="Waktu Upload" class="py-4 px-4 text-center">${timeHtml}</td>
                        <td data-label="Status Review" class="py-4 px-4 text-center">${statusReviewHtml}</td>
                        ${rekomenCell}
                        <td data-label="Komentar" class="py-4 px-4 text-center">${commentBtnHtml}</td>
                        ${showBapCol ? `
                            <td data-label="BAP" class="py-4 px-4 text-center">
                                <div class="flex flex-col gap-1.5 items-stretch max-w-[150px] mx-auto">
                                    <button type="button" id="btn_bap_igracias_${mhs.nim}" onclick="toggleBapPopup('${mhs.nim}', '${namaEscaped}', 'igracias')" class="btn-bap-action px-2.5 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-bold text-[10.5px] shadow-2xs hover:shadow-xs transition flex items-center justify-center gap-1 cursor-pointer">
                                        <i class="bi bi-file-earmark-text-fill"></i> <span>BAP (IGRACIAS)</span>
                                    </button>
                                    <button type="button" id="btn_bap_fakultas_${mhs.nim}" onclick="toggleBapPopup('${mhs.nim}', '${namaEscaped}', 'fakultas')" class="btn-bap-action px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-bold text-[10.5px] shadow-2xs hover:shadow-xs transition flex items-center justify-center gap-1 cursor-pointer">
                                        <i class="bi bi-award-fill"></i> <span>BAP FAKULTAS</span>
                                    </button>
                                </div>
                            </td>
                        ` : ''}
                        <td class="aksi-cell py-4 px-4 text-center">${btnHtml}</td>
                        <td data-label="Lulus Tahap" class="py-4 px-4 text-center">${lulusTahapHtml}</td>
                    </tr>
                `;
            });

            const emptyColspan = getTableColspan();
            if (pageData.length === 0) {
                html = `<tr><td colspan="${emptyColspan}" class="text-center py-10 text-slate-500 font-medium">Tidak ada data mahasiswa ditemukan.</td></tr>`;
            }

            tbody.innerHTML = html;
            renderPagination(totalItems, startIdx, endIdx);
            rebindDosenCheckboxes();
            updateTableButtonHighlights();
            updateBapTableButtonHighlights();
        }

        // ============================================================
        // TOAST
        // ============================================================
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-5 right-5 z-[9999] p-4 rounded-xl text-white font-bold shadow-lg transition-opacity ${type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'}`;
            toast.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} mr-2"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(()=>toast.remove(), 300); }, 3000);
        }

        // ============================================================
        // SSE
        // ============================================================
        let dosenEventSource = null;
        function startDosenSSE() {
            if (dosenEventSource) dosenEventSource.close();
            dosenEventSource = new EventSource(`<?= site_url('mahasiswa/sse_dosen_bimbingan') ?>?posisi=${DISPLAY_POSISI}`);
            dosenEventSource.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    if (data && data.length !== undefined) fetchBimbinganData(true);
                } catch(e) { console.error('SSE Error:', e); }
            };
        }

        // ============================================================
        // BATCH CHECKBOXES
        // ============================================================
        function rebindDosenCheckboxes() {
            const checkAll   = document.getElementById('checkAllDosenStudents');
            const studentCbs = document.querySelectorAll('.dosen-student-cb');
            if (checkAll) {
                checkAll.checked = false;
                checkAll.onchange = function() {
                    studentCbs.forEach(cb => { if (!cb.disabled) cb.checked = this.checked; });
                    updateDosenBatchBar();
                };
            }
            studentCbs.forEach(cb => {
                cb.onchange = () => {
                    updateDosenBatchBar();
                    if (checkAll) {
                        const enabledCbs = Array.from(studentCbs).filter(c => !c.disabled);
                        const checkedCount = enabledCbs.filter(c => c.checked).length;
                        checkAll.checked = (enabledCbs.length > 0 && checkedCount === enabledCbs.length);
                    }
                };
            });
            updateDosenBatchBar();
        }

        function updateDosenBatchBar() {
            const checkedCbs = document.querySelectorAll('.dosen-student-cb:checked');
            const batchBar   = document.getElementById('dosenBatchActionBar');
            const countBadge = document.getElementById('dosenSelectedCountBadge');
            if (checkedCbs.length > 0) {
                countBadge.textContent = checkedCbs.length;
                batchBar.classList.remove('hidden');
                batchBar.classList.add('flex');
            } else {
                batchBar.classList.add('hidden');
                batchBar.classList.remove('flex');
            }
        }

        function unselectAllDosenStudents() {
            document.querySelectorAll('.dosen-student-cb').forEach(cb => cb.checked = false);
            const checkAll = document.getElementById('checkAllDosenStudents');
            if (checkAll) checkAll.checked = false;
            updateDosenBatchBar();
        }

        function openDosenBatchModal() {
            const checkedCbs = document.querySelectorAll('.dosen-student-cb:checked');
            if (checkedCbs.length === 0) return;
            renderBatchModal(checkedCbs);
        }

        function renderBatchModal(checkedCbs) {
            let html = '';
            checkedCbs.forEach((cb, i) => {
                const name             = cb.getAttribute('data-name');
                const file             = cb.getAttribute('data-file');
                const idPreview        = cb.getAttribute('data-id');
                const statusCurrent    = cb.getAttribute('data-status') || 'Pending';
                const catatanCurrent   = decodeURIComponent(cb.getAttribute('data-catatan')  || '');
                const catatan2Current  = decodeURIComponent(cb.getAttribute('data-catatan2') || '');
                const fileUrl          = getUploadBaseUrl(currentTahap) + encodeURIComponent(file);

                const statusOptions = IS_P1 ? `
                    <div class="mb-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Penilaian (P1)</label>
                        <select id="batchStatus_${i}" class="w-full p-2.5 rounded-xl border border-slate-200 focus:ring-orange-500 focus:border-orange-500 text-xs font-semibold bg-white">
                            <option value="Approved" ${statusCurrent === 'Approved' ? 'selected' : ''}>Disetujui (ACC)</option>
                            <option value="Revision" ${statusCurrent === 'Revision' ? 'selected' : ''}>Perlu Revisi</option>
                        </select>
                    </div>
                ` : '';

                let catatanLabel, catatanVal, btnColor, btnIcon, btnLabel;
                if (IS_P1) {
                    catatanLabel = 'Komentar / Feedback';
                    catatanVal   = catatanCurrent;
                    btnColor     = 'bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600';
                    btnIcon      = 'bi-send-fill';
                    btnLabel     = 'Simpan Review';
                } else if (IS_P2) {
                    catatanLabel = 'Komentar untuk Pembimbing 1';
                    catatanVal   = catatan2Current;
                    btnColor     = 'bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700';
                    btnIcon      = 'bi-chat-dots-fill';
                    btnLabel     = 'Simpan Komentar';
                } else if (IS_U1) {
                    catatanLabel = 'Komentar Penguji 1';
                    catatanVal   = '';
                    btnColor     = 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600';
                    btnIcon      = 'bi-chat-dots-fill';
                    btnLabel     = 'Simpan Komentar';
                } else {
                    catatanLabel = 'Komentar Penguji 2';
                    catatanVal   = '';
                    btnColor     = 'bg-gradient-to-r from-purple-500 to-fuchsia-500 hover:from-purple-600 hover:to-fuchsia-600';
                    btnIcon      = 'bi-chat-dots-fill';
                    btnLabel     = 'Simpan Komentar';
                }

                if (IS_PENGUJI) {
                    const found = bimbinganData.find(m => m.latest_preview && m.latest_preview.id == idPreview);
                    if (found) {
                        const prev = found.latest_preview;
                        catatanVal = IS_U1 ? (prev.catatan_penguji_1 || '') : (prev.catatan_penguji_2 || '');
                    }
                }

                const headerBg = IS_P1 ? 'bg-slate-900' : (IS_P2 ? 'bg-indigo-900' : (IS_U1 ? 'bg-emerald-900' : 'bg-purple-900'));
                const badgeBg  = IS_P1 ? 'bg-orange-500' : (IS_P2 ? 'bg-indigo-500' : (IS_U1 ? 'bg-emerald-500' : 'bg-purple-500'));

                html += `
                    <div class="mb-6 rounded-2xl border border-slate-200 overflow-hidden shadow-sm bg-white">
                        <div class="flex items-center gap-3 px-4 py-3 ${headerBg} text-white">
                            <span class="w-7 h-7 rounded-lg ${badgeBg} text-white font-black text-xs flex items-center justify-center shrink-0">${i+1}</span>
                            <div class="min-w-0">
                                <div class="font-bold text-sm truncate">${name}</div>
                                <div class="text-[10px] text-slate-400 font-mono truncate"><i class="bi bi-file-earmark-pdf-fill text-orange-400 mr-1"></i>${file}</div>
                            </div>
                        </div>

                        <div class="relative bg-slate-100" style="height:220px;">
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 text-xs z-0" id="batchPdfLoader_${i}">
                                <i class="bi bi-arrow-repeat animate-spin text-2xl mb-1"></i> Memuat dokumen...
                            </div>
                            <iframe src="${fileUrl}" class="w-full h-full border-0 relative z-10" onload="document.getElementById('batchPdfLoader_${i}').style.display='none'"></iframe>
                        </div>

                        <div class="p-4 space-y-2 border-t border-slate-100">
                            ${statusOptions}
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">${catatanLabel}</label>
                                <textarea id="batchCatatan_${i}" rows="2" class="batch-textarea w-full p-2.5 rounded-xl border border-slate-200 text-xs font-medium resize-none focus:outline-none focus:ring-2 focus:ring-orange-400/25 focus:border-orange-400" placeholder="Tuliskan komentar...">${catatanVal}</textarea>
                            </div>
                            <button onclick="submitBatchItemReview('${idPreview}', ${MODEL_POSISI}, ${i})" class="w-full py-2.5 px-4 rounded-xl ${btnColor} text-white font-bold text-xs shadow transition cursor-pointer flex items-center justify-center gap-1.5 mt-1">
                                <i class="bi ${btnIcon}"></i> ${btnLabel}
                            </button>
                        </div>
                    </div>
                `;
            });

            document.getElementById('dosenBatchModalBody').innerHTML = html;
            document.getElementById('dosenBatchReviewModal').classList.remove('hidden');

            tinymce.remove('.batch-textarea');
            tinymce.init({
                selector: '.batch-textarea',
                menubar: false, statusbar: false,
                plugins: 'lists link',
                toolbar: 'bold italic underline | bullist numlist | link',
                height: 200, skin: 'oxide',
                setup: function (editor) { editor.on('change', function () { tinymce.triggerSave(); }); }
            });
        }

        function submitBatchItemReview(idPreview, posisi, idx) {
            tinymce.triggerSave();
            const catatan  = document.getElementById('batchCatatan_' + idx)?.value || '';
            const statusEl = document.getElementById('batchStatus_' + idx);
            const status   = statusEl ? statusEl.value : null;

            const fd = new FormData();
            fd.append('id_preview', idPreview);
            fd.append('posisi', posisi);
            fd.append('catatan_pembimbing', catatan);
            if (status) fd.append('status_pembimbing', status);

            const btn = document.querySelector(`button[onclick="submitBatchItemReview('${idPreview}', ${posisi}, ${idx})"]`);
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...'; }

            fetch('<?= site_url('mahasiswa/review_preview_ajax') ?>', {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) {
                    showToast(data.message, 'success');
                    if (btn) {
                        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Tersimpan';
                        btn.classList.add('bg-emerald-500');
                    }
                    fetchBimbinganData(true);
                } else {
                    showToast(data.message || 'Gagal menyimpan', 'error');
                    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-send-fill"></i> Simpan'; }
                }
            })
            .catch(() => {
                showToast('Kesalahan koneksi', 'error');
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-send-fill"></i> Simpan'; }
            });
        }

        function closeDosenBatchModal() {
            document.getElementById('dosenBatchReviewModal').classList.add('hidden');
        }

        function submitDosenBatchApprove() {
            const checkedCbs = document.querySelectorAll('.dosen-student-cb:checked');
            if (checkedCbs.length === 0) return;

            if (!IS_P1) { openDosenBatchModal(); return; }

            Swal.fire({
                title: 'Konfirmasi Approve Massal',
                text: `Apakah anda yakin ingin approve massal ${checkedCbs.length} mahasiswa terpilih?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Approve!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const ids = Array.from(checkedCbs).map(cb => cb.value);
                    const formData = new FormData();
                    ids.forEach(id => formData.append('ids[]', id));
                    formData.append('posisi', MODEL_POSISI);

                    fetch('<?= site_url("mahasiswa/review_preview_batch_ajax") ?>', {
                        method: 'POST', body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            Swal.fire('Berhasil!', data.message, 'success');
                            unselectAllDosenStudents();
                            fetchBimbinganData(true);
                        } else {
                            Swal.fire('Gagal!', data.message || 'Gagal approve massal', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error!', 'Kesalahan koneksi saat approve massal', 'error');
                    });
                }
            });
        }

        // ============================================================
        // HOVER PREVIEW PANEL
        // ============================================================
        let hoverHideTimer = null;
        let hoverShowTimer = null;
        const panel = document.getElementById('hoverPreviewPanel');

        function showHoverPanel(event, index) {
            clearTimeout(hoverHideTimer);
            clearTimeout(hoverShowTimer);

            hoverShowTimer = setTimeout(() => {
                const mhs = bimbinganData[index];
                if (!mhs) return;
                const latest = mhs.latest_preview;

                document.getElementById('hoverPanelName').textContent = mhs.nama_mahasiswa + ' (' + mhs.nim + ')';

                const pdfWrap = document.getElementById('hoverPdfWrap');
                if (latest && (latest.file_draft || latest.file_sidang)) {
                    const fname = (currentTahap === 'Sidang')
                        ? (latest.file_sidang || latest.file_draft)
                        : latest.file_draft;
                    const fileUrl = getUploadBaseUrl(currentTahap) + encodeURIComponent(fname);
                    pdfWrap.innerHTML = `<iframe src="${fileUrl}" class="w-full h-full" frameborder="0"></iframe>`;
                } else {
                    pdfWrap.innerHTML = `<div class="pdf-no-file"><i class="bi bi-file-earmark-x text-3xl"></i><span>Belum ada berkas diunggah</span></div>`;
                }

                const formWrap = document.getElementById('hoverFormWrap');
                if (!latest) {
                    formWrap.innerHTML = `<p class="text-xs text-slate-500 italic">Tidak ada berkas untuk dikomentari.</p>`;
                } else {
                    const curComment = getCurrentComment(latest);

                    if (IS_P1) {
                        formWrap.innerHTML = `
                            <div class="panel-label">Status Penilaian (P1)</div>
                            <select id="hoverStatusSelect" class="w-full mb-3 p-2.5 rounded-xl border border-slate-300 focus:ring-orange-500 focus:border-orange-500 text-sm font-semibold">
                                <option value="Approved" ${latest.status_pembimbing === 'Approved' ? 'selected' : ''}>Disetujui (ACC)</option>
                                <option value="Revision" ${latest.status_pembimbing === 'Revision' ? 'selected' : ''}>Perlu Revisi</option>
                            </select>
                            <div class="panel-label">Komentar / Feedback</div>
                            <textarea id="hoverCatatanTA" rows="3" class="w-full p-2.5 rounded-xl border border-slate-300 text-sm font-medium resize-none focus:outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-500" placeholder="Tuliskan feedback...">${curComment}</textarea>
                            <button onclick="submitHoverReview(${latest.id})" class="mt-2 w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs shadow-md transition cursor-pointer"><i class="bi bi-send-fill mr-1"></i> Simpan Review</button>
                        `;
                    } else {
                        const btnCls = IS_P2 ? 'from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700'
                                    : IS_U1 ? 'from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600'
                                    :         'from-purple-500 to-fuchsia-500 hover:from-purple-600 hover:to-fuchsia-600';
                        const label  = IS_P2 ? 'Komentar untuk Pembimbing 1' : `Komentar Penguji ${DISPLAY_POSISI}`;
                        formWrap.innerHTML = `
                            <div class="panel-label">${label}</div>
                            <textarea id="hoverCatatanTA" rows="3" class="w-full p-2.5 rounded-xl border border-slate-300 text-sm font-medium resize-none focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-500" placeholder="Tuliskan komentar...">${curComment}</textarea>
                            <button onclick="submitHoverReview(${latest.id})" class="mt-2 w-full py-2.5 px-4 rounded-xl bg-gradient-to-r ${btnCls} text-white font-bold text-xs shadow-md transition cursor-pointer"><i class="bi bi-chat-dots-fill mr-1"></i> Simpan Komentar</button>
                        `;
                    }
                }

                const rect = event.target.getBoundingClientRect();
                let left = rect.left + window.scrollX;
                let top  = rect.bottom + window.scrollY + 8;

                const panelW = 520;
                if (left + panelW > window.innerWidth - 16) left = window.innerWidth - panelW - 16;
                if (left < 8) left = 8;

                const panelEstH = 480;
                if (top + panelEstH > window.innerHeight + window.scrollY - 16) {
                    top = rect.top + window.scrollY - panelEstH - 8;
                }

                panel.style.left = left + 'px';
                panel.style.top  = top + 'px';
                panel.classList.add('visible');

                tinymce.remove('#hoverCatatanTA');
                tinymce.init({
                    selector: '#hoverCatatanTA',
                    menubar: false, statusbar: false,
                    plugins: 'lists link',
                    toolbar: 'bold italic underline | bullist numlist | link',
                    height: 150, skin: 'oxide',
                    setup: function (editor) { editor.on('change', function () { tinymce.triggerSave(); }); }
                });
            }, 350);
        }

        function scheduleHidePanel() {
            clearTimeout(hoverShowTimer);
            hoverHideTimer = setTimeout(() => { panel.classList.remove('visible'); }, 300);
        }

        panel.addEventListener('mouseenter', () => clearTimeout(hoverHideTimer));
        panel.addEventListener('mouseleave', scheduleHidePanel);

        function submitHoverReview(idPreview) {
            tinymce.triggerSave();
            const catatan = document.getElementById('hoverCatatanTA')?.value || '';
            const status  = IS_P1 ? (document.getElementById('hoverStatusSelect')?.value || 'Pending') : null;

            const fd = new FormData();
            fd.append('id_preview', idPreview);
            fd.append('posisi', MODEL_POSISI);
            fd.append('catatan_pembimbing', catatan);
            if (status) fd.append('status_pembimbing', status);

            fetch('<?= site_url('mahasiswa/review_preview_ajax') ?>', {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) {
                    showToast(data.message, 'success');
                    panel.classList.remove('visible');
                    fetchBimbinganData(true);
                } else {
                    showToast(data.message || 'Gagal menyimpan', 'error');
                }
            })
            .catch(() => showToast('Kesalahan koneksi', 'error'));
        }

        // ============================================================
        // COMMENT MODAL UNIFIED (READ-ONLY)
        // ============================================================
        let _commentData = { p1: '', p2: '', u1: '', u2: '' };
        let _activeCommentTab = 'p1';

        function showUnifiedCommentModal(name, encodedP1, encodedP2, encodedU1, encodedU2) {
            _commentData.p1 = decodeURIComponent(encodedP1 || '');
            _commentData.p2 = decodeURIComponent(encodedP2 || '');
            _commentData.u1 = decodeURIComponent(encodedU1 || '');
            _commentData.u2 = decodeURIComponent(encodedU2 || '');

            document.getElementById('commentModalName').textContent = name;

            if (_commentData.p1.trim()) _activeCommentTab = 'p1';
            else if (_commentData.p2.trim()) _activeCommentTab = 'p2';
            else if (_commentData.u1.trim()) _activeCommentTab = 'u1';
            else if (_commentData.u2.trim()) _activeCommentTab = 'u2';
            else _activeCommentTab = 'p1';

            switchCommentTab(_activeCommentTab);

            const modal = document.getElementById('commentModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function switchCommentTab(tab) {
            _activeCommentTab = tab;
            const content = document.getElementById('commentModalContent');
            const tabs = { p1: 'commentTabP1', p2: 'commentTabP2', u1: 'commentTabU1', u2: 'commentTabU2' };
            const activeClasses = 'px-3 py-1.5 rounded-lg text-xs font-bold border transition';
            const colors = {
                p1: { active: 'bg-orange-100 text-orange-700 border-orange-300', inactive: 'bg-slate-100 text-slate-600 hover:bg-slate-200 border-slate-200' },
                p2: { active: 'bg-indigo-100 text-indigo-700 border-indigo-300', inactive: 'bg-slate-100 text-slate-600 hover:bg-slate-200 border-slate-200' },
                u1: { active: 'bg-emerald-100 text-emerald-700 border-emerald-300', inactive: 'bg-slate-100 text-slate-600 hover:bg-slate-200 border-slate-200' },
                u2: { active: 'bg-purple-100 text-purple-700 border-purple-300', inactive: 'bg-slate-100 text-slate-600 hover:bg-slate-200 border-slate-200' }
            };
            for (const [key, elId] of Object.entries(tabs)) {
                const el = document.getElementById(elId);
                if (el) el.className = activeClasses + ' ' + (key === tab ? colors[key].active : colors[key].inactive);
            }
            const labels = { p1: 'Pembimbing 1', p2: 'Pembimbing 2', u1: 'Penguji 1', u2: 'Penguji 2' };
            const comment = _commentData[tab] || '';
            if (comment.trim()) content.innerHTML = comment;
            else content.innerHTML = `<em class="text-slate-400">Belum ada komentar dari ${labels[tab]}.</em>`;
        }

        function closeCommentModal() {
            const modal = document.getElementById('commentModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // ============================================================
        // INLINE COMMENT EDITOR (NEW — menggantikan modal komentar lama)
        // ============================================================
        function fileItemKey(scope, nim, fileIndex, fileType) {
            return `${scope}__${nim}__${fileIndex}__${fileType}`;
        }

        function openInlineComment(scope, nim, fileIndex, fileType, action) {
            const key = fileItemKey(scope, nim, fileIndex, fileType);
            const slot = document.getElementById('inlineSlot_' + key);
            if (!slot) {
                console.warn('Inline slot not found:', key);
                return;
            }

            // Toggle close jika slot yang sama
            if (window.activeInlineEditorKey === key) {
                closeInlineComment();
                return;
            }
            // Tutup editor lain terlebih dahulu
            closeInlineComment();

            const mhs = bimbinganData.find(m => m.nim === nim);
            if (!mhs) return;
            const preview = mhs.riwayat_previews[fileIndex];
            if (!preview) return;

            const existingComment = getFileCatatan(preview, fileType) || '';
            const editorId = 'inlineEditor__' + key;

            const headerLabels = {
                'Approved': '✓ ACC Berkas',
                'Revision': '✗ Revisi Berkas',
                'Reset':    '↺ Reset Status Berkas',
                'Comment':  '💬 Komentar Berkas',
            };
            const headerColors = {
                'Approved': 'from-emerald-500 to-teal-500',
                'Revision': 'from-rose-500 to-pink-500',
                'Reset':    'from-slate-500 to-slate-700',
                'Comment':  'from-indigo-500 to-purple-500',
            };

            const headerLabel = headerLabels[action] || 'Komentar';
            const headerColor = headerColors[action] || 'from-slate-500 to-slate-700';

            slot.innerHTML = `
                <div class="mt-1.5 p-3 rounded-xl border-2 border-dashed border-orange-300 bg-orange-50/40 animate-pop-in" data-inline-key="${key}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-md bg-gradient-to-r ${headerColor} text-white shadow-xs">${headerLabel}</span>
                        <button type="button" onclick="closeInlineComment()" class="w-6 h-6 rounded-md bg-white hover:bg-slate-100 text-slate-500 flex items-center justify-center text-[10px] cursor-pointer border border-slate-200">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <textarea id="${editorId}" rows="3" class="w-full p-2.5 rounded-lg border border-slate-300 text-xs font-medium resize-none focus:outline-none focus:ring-2 focus:ring-orange-400/25 focus:border-orange-400" placeholder="Tuliskan komentar (opsional)...">${existingComment}</textarea>
                    <div class="flex items-center justify-end gap-1.5 mt-2">
                        <button type="button" onclick="closeInlineComment()" class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 text-[11px] font-bold cursor-pointer">Batal</button>
                        <button type="button" onclick="submitInlineComment('${nim}', ${fileIndex}, '${fileType}', '${action}')" class="px-3 py-1.5 rounded-lg bg-gradient-to-r ${headerColor} text-white text-[11px] font-bold cursor-pointer shadow-xs inline-flex items-center gap-1">
                            <i class="bi bi-send-fill text-[10px]"></i> Simpan
                        </button>
                    </div>
                </div>
            `;
            slot.classList.remove('hidden');
            slot.classList.add('inline-comment-slot');
            window.activeInlineEditorKey = key;

            // Init TinyMCE dengan delay supaya DOM siap
            setTimeout(() => {
                const el = document.getElementById(editorId);
                if (!el) return;
                try {
                    const existing = tinymce.get(editorId);
                    if (existing) existing.remove();
                } catch(e){}
                tinymce.init({
                    selector: '#' + editorId,
                    menubar: false, statusbar: false,
                    plugins: 'lists link',
                    toolbar: 'bold italic underline | bullist numlist | link',
                    height: 130, skin: 'oxide',
                    setup: function(editor) {
                        editor.on('change', function() { tinymce.triggerSave(); });
                    }
                });
            }, 30);
        }

        function closeInlineComment() {
            if (!window.activeInlineEditorKey) return;
            const key = window.activeInlineEditorKey;
            const slot = document.getElementById('inlineSlot_' + key);
            if (slot) {
                const ta = slot.querySelector('textarea');
                if (ta && ta.id) {
                    try {
                        const ed = tinymce.get(ta.id);
                        if (ed) ed.remove();
                    } catch(e){ console.warn(e); }
                }
                slot.innerHTML = '';
                slot.classList.add('hidden');
                slot.classList.remove('inline-comment-slot');
            }
            window.activeInlineEditorKey = null;
        }

        function submitInlineComment(nim, fileIndex, fileType, action) {
            if (!window.activeInlineEditorKey) return;
            const key = window.activeInlineEditorKey;
            const editorId = 'inlineEditor__' + key;

            if (tinymce.get(editorId)) tinymce.triggerSave();
            const catatan = document.getElementById(editorId)?.value || '';

            const mhs = bimbinganData.find(m => m.nim === nim);
            if (!mhs) return;
            const preview = mhs.riwayat_previews[fileIndex];
            if (!preview) return;

            const fd = new FormData();
            fd.append('id_preview', preview.id);
            fd.append('file_type', fileType);
            fd.append('action', action);
            fd.append('catatan', catatan);

            Swal.fire({ title: 'Menyimpan...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

            fetch('<?= site_url("dosen_bimbingan/review_file_ajax") ?>', {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1400, showConfirmButton: false })
                        .then(() => {
                            closeInlineComment();
                            fetchBimbinganData(true);
                        });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menyimpan.' });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Kesalahan koneksi.' });
            });
        }

        // ============================================================
        // FULLSCREEN PREVIEW (NEW)
        // ============================================================
        function toggleFullscreenPreview(uniqueKey) {
            const card = document.getElementById('previewCard_' + uniqueKey);
            if (!card) return;

            const isFs = document.fullscreenElement === card || document.webkitFullscreenElement === card;
            if (!isFs) {
                if (card.requestFullscreen) card.requestFullscreen();
                else if (card.webkitRequestFullscreen) card.webkitRequestFullscreen();
            } else {
                if (document.exitFullscreen) document.exitFullscreen();
                else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
            }
        }

        document.addEventListener('fullscreenchange', updateFullscreenIcons);
        document.addEventListener('webkitfullscreenchange', updateFullscreenIcons);

        function updateFullscreenIcons() {
            document.querySelectorAll('[data-fs-icon]').forEach(icon => {
                const key = icon.getAttribute('data-fs-key');
                const card = document.getElementById('previewCard_' + key);
                const isFs = (document.fullscreenElement === card) || (document.webkitFullscreenElement === card);
                icon.className = isFs ? 'bi bi-fullscreen-exit' : 'bi bi-arrows-fullscreen';
            });
        }

        // ============================================================
        // HELPERS: Multi-file Preview 3 & Sidang
        // ============================================================
        function getPreviewFiles(preview) {
            if (!preview) return [];
            if (currentTahap === 'Sidang') {
                const fname = preview.file_sidang || preview.file_draft;
                if (fname) return [{ type: 'sidang', label: 'File Sidang Akhir', file: fname, icon: 'bi-file-earmark-text-fill' }];
                return [];
            }
            if (currentTahap === 'Preview 3') {
                const files = [];
                if (preview.file_sitasi)      files.push({ type: 'sitasi',      label: 'File Sitasi',      file: preview.file_sitasi,      icon: 'bi-quote' });
                if (preview.file_bimbingan)   files.push({ type: 'bimbingan',   label: 'File Bimbingan',   file: preview.file_bimbingan,   icon: 'bi-file-earmark-check-fill' });
                if (preview.file_persyaratan) files.push({ type: 'persyaratan', label: 'File Persyaratan', file: preview.file_persyaratan, icon: 'bi-signpost-split-fill' });
                if (files.length === 0 && preview.file_draft) files.push({ type: 'draft', label: 'File Draft', file: preview.file_draft, icon: 'bi-file-earmark-pdf' });
                return files;
            }
            if (preview.file_draft) return [{ type: 'draft', label: 'File Draft', file: preview.file_draft, icon: 'bi-file-earmark-pdf' }];
            return [];
        }

        function isPreviewItemActive(nim, fileIndex, fileType) {
            return window.activePreviews.some(p => p.nim === nim && p.fileIndex === fileIndex && (p.fileType || 'draft') === fileType);
        }
        function previewItemKey(nim, fileIndex, fileType) { return `${nim}_${fileIndex}_${fileType}`; }

        // ============================================================
        // FLOATING PANEL
        // ============================================================
        function refreshLihatBerkasView() {
            updateLihatBerkasLayout();
            renderAllLihatBerkasCards();
            renderAllPreviewCards();
            updateTableButtonHighlights();
        }

        function updateLihatBerkasLayout() {
            const container      = document.getElementById('lihatBerkasContainer');
            const wrapperDaftar  = document.getElementById('wrapperDaftarMhs');
            const wrapperPreview = document.getElementById('wrapperPreviewBerkas');
            if (!container || !wrapperDaftar || !wrapperPreview) return;

            const isMobile = window.innerWidth <= 768;
            const isPreviewActive = window.activePreviews && window.activePreviews.length > 0;

            container.style.display = 'flex';
            if (isMobile) {
                container.style.flexDirection = 'column';
                container.style.alignItems = 'stretch';
                container.style.justifyContent = 'flex-start';
                container.style.overflowY = 'auto';
                container.style.overflowX = 'hidden';
            } else {
                container.style.flexDirection = 'row';
                container.style.alignItems = 'stretch';
                container.style.justifyContent = 'center';
                container.style.overflowY = 'hidden';
                container.style.overflowX = 'hidden';
            }

            if (isMobile) wrapperDaftar.className = 'flex flex-col gap-3 w-full overflow-y-auto flex-shrink-0';
            else          wrapperDaftar.className = 'flex flex-col gap-3 w-[36%] min-w-[384px] max-w-[504px] overflow-y-auto flex-shrink-0';

            if (isPreviewActive) {
                wrapperPreview.classList.add('active');
                if (isMobile) wrapperPreview.className = 'flex w-full overflow-x-auto overflow-y-visible gap-3 flex-row items-stretch flex-shrink-0';
                else          wrapperPreview.className = 'flex-1 overflow-x-auto overflow-y-hidden gap-4 flex items-stretch';
            } else {
                wrapperPreview.classList.remove('active');
                wrapperPreview.className = 'hidden';
            }
        }

        function renderAllLihatBerkasCards() {
            const wrapper = document.getElementById('wrapperDaftarMhs');
            if (!wrapper) return;
            if (!window.activeLihatBerkasIndices || window.activeLihatBerkasIndices.length === 0) {
                wrapper.innerHTML = '';
                return;
            }

            const totalActive = window.activeLihatBerkasIndices.length;
            let headerSummaryBar = '';
            if (totalActive > 1) {
                headerSummaryBar = `
                    <div class="bg-slate-900 text-white px-3.5 py-2 rounded-2xl flex items-center justify-between shadow-lg border border-slate-800 shrink-0 pointer-events-auto w-full animate-bar-in">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-people-fill text-orange-400 text-xs"></i>
                            <span class="text-xs font-bold">${totalActive} Mahasiswa <span class="text-slate-400 font-normal text-[10px]">(Maks. 4)</span></span>
                        </div>
                        <button type="button" onclick="closeLihatBerkasPanel()" class="text-[11px] text-slate-300 hover:text-rose-400 font-bold transition cursor-pointer">Tutup Semua</button>
                    </div>
                `;
            }

            let cardsHtml = '';
            window.activeLihatBerkasIndices.forEach((index, cardIdx) => {
                const mhs = bimbinganData[index];
                if (!mhs) return;
                const allPreviews = mhs.riwayat_previews || [];
                const fileList = currentTahap === 'Preview 3'
                    ? allPreviews.slice(0, 3).reverse()
                    : allPreviews.slice(0, 2);
                const cardNum  = cardIdx + 1;

                let itemsHtml = '';
                if (fileList.length === 0) {
                    itemsHtml = `<div class="p-3 text-center text-slate-400 text-xs">Tidak ada riwayat berkas.</div>`;
                } else {
                    fileList.forEach((preview, idx) => {
                        const realIdx = allPreviews.findIndex(p => p.id === preview.id);
                        const files = getPreviewFiles(preview);
                        const status = preview.status_pembimbing || 'Pending';

                        let statusBadge = '';
                        if (status === 'Approved') statusBadge = '<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Disetujui</span>';
                        else if (status === 'Revision') statusBadge = '<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Revisi</span>';
                        else statusBadge = '<span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Pending</span>';

                        // Action buttons di header pengajuan — untuk non-PIC (komentar per-preview via modal lama)
                        let actionButtons = '';
                        if (currentTahap === 'Sidang') {
                            let nilaiTersimpan = null;
                            if (MODEL_POSISI === 1 && preview.nilaisidang_pembimbing1 && parseFloat(preview.nilaisidang_pembimbing1) > 0) nilaiTersimpan = parseFloat(preview.nilaisidang_pembimbing1);
                            else if (MODEL_POSISI === 2 && preview.nilaisidang_pembimbing2 && parseFloat(preview.nilaisidang_pembimbing2) > 0) nilaiTersimpan = parseFloat(preview.nilaisidang_pembimbing2);
                            else if (MODEL_POSISI === 3 && preview.nilaisidang_penguji1 && parseFloat(preview.nilaisidang_penguji1) > 0) nilaiTersimpan = parseFloat(preview.nilaisidang_penguji1);
                            else if (MODEL_POSISI === 4 && preview.nilaisidang_penguji2 && parseFloat(preview.nilaisidang_penguji2) > 0) nilaiTersimpan = parseFloat(preview.nilaisidang_penguji2);

                            const label = nilaiTersimpan ? 'Edit Nilai' : 'Nilai';
                            const icon  = nilaiTersimpan ? 'bi-pencil-square' : 'bi-star-fill';
                            actionButtons = `
                                <button onclick="openModalPenilaianSidang('${mhs.nim}')"
                                        class="px-2.5 py-1 rounded-lg bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white border-none text-[10px] font-bold transition cursor-pointer flex items-center gap-1 shadow-2xs">
                                    <i class="bi ${icon} text-[10px]"></i><span>${label}</span>
                                </button>
                            `;
                        }
                        // Untuk non-Sidang, action buttons dipindah ke per-file item di bawah

                        itemsHtml += `
                            <div class="mb-1.5 flex items-center justify-between gap-2 px-1">
                                <div class="flex items-center gap-1.5">
                                    <i class="bi bi-folder2-open text-orange-500 text-xs"></i>
                                    <span class="text-[11px] font-bold text-slate-700">Pengajuan #${realIdx + 1}</span>
                                    ${statusBadge}
                                </div>
                                <div class="flex items-center gap-1">${actionButtons}</div>
                            </div>
                        `;

                        if (files.length === 0) {
                            itemsHtml += `<div class="p-2 text-center text-slate-400 text-[11px] italic">Tidak ada file pada pengajuan ini.</div>`;
                        } else {
                            files.forEach((f) => {
                                const fileUrl   = getUploadBaseUrl(currentTahap) + encodeURIComponent(f.file);
                                const fileName  = f.file;
                                const isActive  = isPreviewItemActive(mhs.nim, realIdx, f.type);
                                const activeCardBorder = isActive ? 'ring-2 ring-orange-500 border-orange-300 bg-orange-50/45' : 'border-slate-200 bg-white hover:border-slate-300';
                                const previewBtnStyle  = isActive ? 'bg-orange-600 text-white border-orange-600 shadow-xs ring-2 ring-orange-400' : 'bg-orange-50 hover:bg-orange-100 text-orange-700 border-orange-200 shadow-2xs';
                                const previewBtnLabel  = isActive ? 'Tutup' : 'Lihat';
                                const previewBtnIcon   = isActive ? 'bi-x-circle-fill' : 'bi-eye-fill';

                                const fileStatus = getFileStatus(preview, f.type);
                                const fileStatusBadge = renderFileStatusBadge(preview, f.type);

                                // Action buttons per-file (inline comment)
                                let fileActionBtns = '';
                                if (currentTahap !== 'Sidang' && isCurrentUserPIC()) {
                                    if (fileStatus === 'Pending') {
                                        fileActionBtns = `
                                            <button type="button" onclick="event.stopPropagation(); openInlineComment('card', '${mhs.nim}', ${realIdx}, '${f.type}', 'Approved')" class="px-2 h-7 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-300 text-[10px] font-bold transition cursor-pointer flex items-center gap-1"><i class="bi bi-check-lg"></i>ACC</button>
                                            <button type="button" onclick="event.stopPropagation(); openInlineComment('card', '${mhs.nim}', ${realIdx}, '${f.type}', 'Revision')" class="px-2 h-7 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-300 text-[10px] font-bold transition cursor-pointer flex items-center gap-1"><i class="bi bi-x-lg"></i>Revisi</button>
                                            <button type="button" onclick="event.stopPropagation(); openInlineComment('card', '${mhs.nim}', ${realIdx}, '${f.type}', 'Comment')" class="px-2 h-7 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border border-indigo-300 text-[10px] font-bold transition cursor-pointer flex items-center gap-1"><i class="bi bi-chat-dots"></i>Komentar</button>
                                        `;
                                    } else {
                                        fileActionBtns = `
                                            <button type="button" onclick="event.stopPropagation(); openInlineComment('card', '${mhs.nim}', ${realIdx}, '${f.type}', 'Reset')" class="px-2 h-7 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 border border-slate-300 text-[10px] font-bold transition cursor-pointer flex items-center gap-1"><i class="bi bi-arrow-counterclockwise"></i>Reset</button>
                                            <button type="button" onclick="event.stopPropagation(); openInlineComment('card', '${mhs.nim}', ${realIdx}, '${f.type}', 'Comment')" class="px-2 h-7 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border border-indigo-300 text-[10px] font-bold transition cursor-pointer flex items-center gap-1"><i class="bi bi-chat-dots"></i>Komentar</button>
                                        `;
                                    }
                                }

                                const cardKey = fileItemKey('card', mhs.nim, realIdx, f.type);

                                itemsHtml += `
                                    <div class="p-2 px-2.5 rounded-xl border shadow-2xs hover:shadow-xs transition-all flex flex-col gap-1.5 ${activeCardBorder}">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <i class="bi ${f.icon} text-orange-500 text-xs shrink-0"></i>
                                                    <span class="font-bold text-slate-800 text-[11px] truncate">${f.label}</span>
                                                    ${fileStatusBadge}
                                                </div>
                                                <p class="text-[10px] font-mono text-slate-400 truncate mt-0.5" title="${fileName}">
                                                    <i class="bi bi-file-pdf text-rose-500 mr-1 text-[9px]"></i>${fileName}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <button type="button"
                                                        onclick="event.stopPropagation(); previewBerkasItem('${mhs.nim}', ${realIdx}, '${f.type}')"
                                                        class="px-2.5 h-7 rounded-lg border text-[10px] font-bold transition flex items-center justify-center gap-1 cursor-pointer active:scale-95 whitespace-nowrap ${previewBtnStyle}">
                                                    <i class="bi ${previewBtnIcon} text-[11px]"></i><span>${previewBtnLabel}</span>
                                                </button>
                                                <a href="${fileUrl}" download="${fileName}" target="_blank"
                                                   class="w-7 h-7 rounded-lg bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold transition flex items-center justify-center cursor-pointer shadow-2xs active:scale-95"
                                                   title="Unduh Berkas">
                                                    <i class="bi bi-download text-xs"></i>
                                                </a>
                                            </div>
                                        </div>
                                        ${fileActionBtns ? `<div class="flex items-center gap-1.5 flex-wrap pl-5">${fileActionBtns}</div>` : ''}
                                        <div id="inlineSlot_${cardKey}" class="hidden"></div>
                                    </div>
                                `;
                            });
                        }
                    });
                }

                cardsHtml += `
                    <div class="student-card-item pointer-events-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 w-full animate-pop-in" id="cardMhs_${mhs.nim}">
                        <div class="p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2 shrink-0 border-b border-slate-800">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 rounded-lg bg-orange-600/30 border border-orange-500/50 text-orange-400 flex items-center justify-center font-bold text-[11px] shrink-0 shadow-2xs">${cardNum}</div>
                                <div class="min-w-0 flex items-center gap-2">
                                    <h4 class="text-xs font-bold text-white truncate max-w-[150px] sm:max-w-[180px]">${mhs.nama_mahasiswa}</h4>
                                    <span class="px-1.5 py-0.2 rounded bg-white/10 text-orange-300 font-mono text-[10px] font-bold">${mhs.nim}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="removeStudentFromLihatBerkas(${index})" class="w-6 h-6 rounded-md bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-[11px] font-bold transition-colors cursor-pointer" title="Tutup Mahasiswa Ini">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="p-2.5 space-y-1.5 bg-slate-50/50 overflow-y-auto">${itemsHtml}</div>
                    </div>
                `;
            });

            wrapper.innerHTML = headerSummaryBar + cardsHtml;
        }

        function renderAllPreviewCards() {
            const wrapper = document.getElementById('wrapperPreviewBerkas');
            if (!wrapper) return;
            if (!window.activePreviews || window.activePreviews.length === 0) {
                wrapper.innerHTML = '';
                wrapper.classList.remove('active');
                return;
            }

            wrapper.classList.add('active');
            let html = '';
            window.activePreviews.forEach((p, idx) => {
                const mhs = bimbinganData.find(m => m.nim === p.nim);
                if (!mhs) return;
                const preview = mhs.riwayat_previews[p.fileIndex];
                if (!preview) return;

                const fileType = p.fileType || 'draft';
                const files    = getPreviewFiles(preview);
                const fileObj  = files.find(f => f.type === fileType) || files[0];
                if (!fileObj) return;

                const fileUrl   = getUploadBaseUrl(currentTahap) + encodeURIComponent(fileObj.file);
                const fileName  = fileObj.file;
                const fileLabel = fileObj.label;
                const fullName  = mhs.nama_mahasiswa || 'Mahasiswa';
                const slotNum   = idx + 1;
                const status    = preview.status_pembimbing || 'Pending';
                const uniqueKey = previewItemKey(p.nim, p.fileIndex, fileType);

                if (preview.file_missing) {
                    html += `
                        <div class="preview-card-item pointer-events-auto bg-white rounded-3xl shadow-2xl border border-rose-200 overflow-hidden flex flex-col shrink-0 animate-preview-in" id="previewCard_${uniqueKey}">
                            <div class="preview-header p-2.5 px-3.5 bg-rose-900 text-white flex items-center justify-between gap-2.5 shrink-0 border-b border-rose-800">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-rose-600/30 border border-rose-500/50 text-rose-300 flex items-center justify-center shrink-0">
                                        <i class="bi bi-exclamation-triangle-fill text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold truncate">File Tidak Ditemukan</h4>
                                        <p class="text-[10px] text-rose-200 truncate">${fileName || '-'}</p>
                                    </div>
                                </div>
                                <button type="button" onclick="closeSinglePreview(${idx})" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-rose-600/80 text-slate-200 hover:text-white flex items-center justify-center text-xs font-bold transition cursor-pointer">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="flex-1 flex flex-col items-center justify-center p-6 bg-slate-50">
                                <i class="bi bi-file-earmark-x text-5xl text-rose-400 mb-3"></i>
                                <h4 class="font-bold text-slate-700 text-sm">Berkas tidak ditemukan di server</h4>
                                <p class="text-xs text-slate-500 mt-1 text-center max-w-xs">
                                    File <strong>${fileName || '-'}</strong> hilang atau telah dipindahkan.
                                    Silakan minta mahasiswa mengunggah ulang.
                                </p>
                                <button type="button" onclick="closeSinglePreview(${idx})" class="mt-4 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition cursor-pointer shadow-md">
                                    <i class="bi bi-x-lg mr-1"></i> Tutup
                                </button>
                            </div>
                        </div>`;
                    return;
                }

                // Action buttons per-file
                let actionButtons = '';
                if (currentTahap === 'Sidang') {
                    let nilaiTersimpan = null;
                    if (MODEL_POSISI === 1 && preview.nilaisidang_pembimbing1 && parseFloat(preview.nilaisidang_pembimbing1) > 0) nilaiTersimpan = parseFloat(preview.nilaisidang_pembimbing1);
                    else if (MODEL_POSISI === 2 && preview.nilaisidang_pembimbing2 && parseFloat(preview.nilaisidang_pembimbing2) > 0) nilaiTersimpan = parseFloat(preview.nilaisidang_pembimbing2);
                    else if (MODEL_POSISI === 3 && preview.nilaisidang_penguji1 && parseFloat(preview.nilaisidang_penguji1) > 0) nilaiTersimpan = parseFloat(preview.nilaisidang_penguji1);
                    else if (MODEL_POSISI === 4 && preview.nilaisidang_penguji2 && parseFloat(preview.nilaisidang_penguji2) > 0) nilaiTersimpan = parseFloat(preview.nilaisidang_penguji2);

                    const label = nilaiTersimpan ? 'Edit Nilai' : 'Nilai';
                    const icon  = nilaiTersimpan ? 'bi-pencil-square' : 'bi-star-fill';
                    actionButtons = `
                        <button onclick="openModalPenilaianSidang('${p.nim}')"
                                class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white border-none text-xs font-bold transition cursor-pointer flex items-center gap-1.5 shadow-md">
                            <i class="bi ${icon}"></i><span>${label}</span>
                        </button>
                    `;
                } else if (isCurrentUserPIC()) {
                    const ftype = fileType;
                    const fst   = getFileStatus(preview, ftype);
                    if (fst === 'Pending') {
                        actionButtons = `
                            <button onclick="openInlineComment('preview', '${p.nim}', ${p.fileIndex}, '${ftype}', 'Approved')" class="px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-300 text-[11px] font-bold transition cursor-pointer flex items-center gap-1">✓ ACC</button>
                            <button onclick="openInlineComment('preview', '${p.nim}', ${p.fileIndex}, '${ftype}', 'Revision')" class="px-2.5 py-1 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-300 text-[11px] font-bold transition cursor-pointer flex items-center gap-1">✗ Revisi</button>
                            <button onclick="openInlineComment('preview', '${p.nim}', ${p.fileIndex}, '${ftype}', 'Comment')" class="px-2.5 py-1 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border border-indigo-300 text-[11px] font-bold transition cursor-pointer flex items-center gap-1">💬 Komentar</button>
                        `;
                    } else {
                        actionButtons = `
                            <button onclick="openInlineComment('preview', '${p.nim}', ${p.fileIndex}, '${ftype}', 'Reset')" class="px-2.5 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 border border-slate-300 text-[11px] font-bold transition cursor-pointer flex items-center gap-1"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
                            <button onclick="openInlineComment('preview', '${p.nim}', ${p.fileIndex}, '${ftype}', 'Comment')" class="px-2.5 py-1 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border border-indigo-300 text-[11px] font-bold transition cursor-pointer flex items-center gap-1">💬 Komentar</button>
                        `;
                    }
                } else {
                    // Non-PIC: hanya komentar
                    actionButtons = `
                        <button onclick="openInlineComment('preview', '${p.nim}', ${p.fileIndex}, '${ftype || fileType}', 'Comment')" class="px-2.5 py-1 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-700 border border-indigo-300 text-[11px] font-bold transition cursor-pointer flex items-center gap-1">💬 Komentar</button>
                    `;
                }

                const previewKey = fileItemKey('preview', p.nim, p.fileIndex, fileType);

                html += `
                    <div class="preview-card-item pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col shrink-0 animate-preview-in" id="previewCard_${uniqueKey}">
                        <div class="preview-header p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2.5 shrink-0 border-b border-slate-800">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="preview-slot-badge w-7 h-7 rounded-lg bg-rose-600/30 border border-rose-500/50 text-rose-400 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                    ${window.activePreviews.length > 1 ? slotNum : '<i class="bi bi-file-pdf"></i>'}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate max-w-[190px] sm:max-w-[240px]">${fileLabel} · Pengajuan #${p.fileIndex + 1}</h4>
                                    <p class="text-[10px] text-slate-300 font-medium truncate">${fullName} · <span class="font-mono text-slate-400">${fileName}</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" onclick="togglePreviewIframeInteraction('${p.nim}', ${p.fileIndex}, '${fileType}')" id="btnPreviewInteract_${uniqueKey}" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Kursor Terkunci (Normal)">
                                    <i class="bi bi-arrow-pointer" id="iconPreviewInteract_${uniqueKey}"></i>
                                </button>
                                <button type="button" onclick="toggleFullscreenPreview('${uniqueKey}')" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Toggle Fullscreen">
                                    <i class="bi bi-arrows-fullscreen" data-fs-icon data-fs-key="${uniqueKey}"></i>
                                </button>
                                <a href="${fileUrl}" target="_blank" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Buka di tab baru">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <button type="button" onclick="closeSinglePreview(${idx})" class="preview-close-btn w-7 h-7 rounded-lg bg-white/10 hover:bg-rose-600/80 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer ml-0.5" title="Tutup Pratinjau Ini">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="preview-body" style="min-height: 200px;">
                            <div class="loader" id="previewLoader_${uniqueKey}">
                                <i class="bi bi-arrow-repeat"></i> Memuat dokumen...
                            </div>
                            <iframe id="iframePreviewBerkas_${uniqueKey}" src="${fileUrl}#toolbar=0&navpanes=0" class="w-full h-full border-0 relative z-10 pointer-events-none" onload="document.getElementById('previewLoader_${uniqueKey}').style.display='none'" title="Pratinjau Berkas"></iframe>
                        </div>

                        <div id="inlineSlot_${previewKey}" class="hidden"></div>

                        <div class="preview-footer p-2 px-3 bg-white flex items-center justify-between text-xs shrink-0 gap-2 border-t border-slate-200">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                ${actionButtons}
                            </div>
                            <div class="flex items-center gap-1.5">
                                <a href="${fileUrl}" download="${fileName}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs text-[11px]">
                                    <i class="bi bi-download"></i><span>Unduh</span>
                                </a>
                                <button type="button" onclick="closeSinglePreview(${idx})" class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold transition cursor-pointer text-[11px] shadow-2xs">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            wrapper.innerHTML = html;
        }

        function previewBerkasItem(nim, fileIndex, fileType = 'draft') {
            const existingIdx = window.activePreviews.findIndex(p =>
                p.nim === nim && p.fileIndex === fileIndex && (p.fileType || 'draft') === fileType
            );
            if (existingIdx > -1) {
                window.activePreviews.splice(existingIdx, 1);
                refreshLihatBerkasView();
                return;
            }
            if (window.activePreviews.length >= 5) window.activePreviews.shift();
            window.activePreviews.push({ nim, fileIndex, fileType });
            refreshLihatBerkasView();

            if (window.innerWidth <= 768) {
                setTimeout(() => {
                    const previewWrap = document.getElementById('wrapperPreviewBerkas');
                    if (previewWrap) previewWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 150);
            }
        }

        function closeSinglePreview(index) {
            if (index < 0 || index >= window.activePreviews.length) return;
            window.activePreviews.splice(index, 1);
            refreshLihatBerkasView();
        }

        function togglePreviewIframeInteraction(nim, fileIndex, fileType = 'draft') {
            const uniqueKey = previewItemKey(nim, fileIndex, fileType);
            const iframe = document.getElementById(`iframePreviewBerkas_${uniqueKey}`);
            const btn    = document.getElementById(`btnPreviewInteract_${uniqueKey}`);
            const icon   = document.getElementById(`iconPreviewInteract_${uniqueKey}`);
            if (!iframe) return;

            const isLocked = iframe.classList.contains('pointer-events-none');
            if (isLocked) {
                iframe.classList.remove('pointer-events-none');
                if (btn) {
                    btn.className = 'w-7 h-7 rounded-lg bg-orange-600 text-white flex items-center justify-center text-xs transition cursor-pointer shadow-2xs';
                    btn.title = 'Mode Scroll Aktif';
                }
                if (icon) icon.className = 'bi bi-hand';
            } else {
                iframe.classList.add('pointer-events-none');
                if (btn) {
                    btn.className = 'w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs transition cursor-pointer';
                    btn.title = 'Kursor Normal (Terkunci)';
                }
                if (icon) icon.className = 'bi bi-arrow-pointer';
            }
        }

        function removeStudentFromLihatBerkas(index) {
            const idx = window.activeLihatBerkasIndices.indexOf(index);
            if (idx === -1) return;
            window.activeLihatBerkasIndices.splice(idx, 1);
            const mhs = bimbinganData[index];
            if (mhs) window.activePreviews = window.activePreviews.filter(p => p.nim !== mhs.nim);
            if (window.activeLihatBerkasIndices.length === 0) closeLihatBerkasPanel();
            else refreshLihatBerkasView();
            updateTableButtonHighlights();
        }

        function toggleLihatBerkasPanel(index) {
            const mhs = bimbinganData[index];
            if (!mhs || !mhs.riwayat_previews || mhs.riwayat_previews.length === 0) {
                showToast('Mahasiswa ini belum memiliki berkas yang diunggah.', 'error');
                return;
            }
            const idx = window.activeLihatBerkasIndices.indexOf(index);
            if (idx > -1) { removeStudentFromLihatBerkas(index); return; }

            if (window.activeLihatBerkasIndices.length >= 4) {
                const removed = window.activeLihatBerkasIndices.shift();
                const removedMhs = bimbinganData[removed];
                if (removedMhs) window.activePreviews = window.activePreviews.filter(p => p.nim !== removedMhs.nim);
            }
            window.activeLihatBerkasIndices.push(index);

            showLihatBerkasContainer();
            refreshLihatBerkasView();
        }

        function showLihatBerkasContainer() {
            const container = document.getElementById('lihatBerkasContainer');
            if (container) { container.style.display = 'flex'; container.classList.add('active'); }
            const wrapperPreview = document.getElementById('wrapperPreviewBerkas');
            if (wrapperPreview) { wrapperPreview.classList.remove('active'); wrapperPreview.innerHTML = ''; }
            updateLihatBerkasLayout();
        }

        function closeLihatBerkasPanel() {
            closeInlineComment();
            const container = document.getElementById('lihatBerkasContainer');
            if (container) { container.style.display = 'none'; container.classList.remove('active'); }
            const wrapperDaftar = document.getElementById('wrapperDaftarMhs');
            if (wrapperDaftar) wrapperDaftar.innerHTML = '';
            const wrapperPreview = document.getElementById('wrapperPreviewBerkas');
            if (wrapperPreview) { wrapperPreview.innerHTML = ''; wrapperPreview.classList.remove('active'); }
            window.activeLihatBerkasIndices = [];
            window.activePreviews = [];
            updateTableButtonHighlights();
        }

        function updateTableButtonHighlights() {
            const activeIndices = window.activeLihatBerkasIndices || [];
            document.querySelectorAll('#bimbinganTableBody tr').forEach((tr) => {
                const dataIdx = tr.getAttribute('data-index');
                if (dataIdx === null) return;
                const idx = parseInt(dataIdx);
                const btn = tr.querySelector('button[onclick^="toggleLihatBerkasPanel"]');
                if (btn) {
                    const isActive = activeIndices.includes(idx);
                    if (isActive) {
                        btn.className = 'inline-flex items-center gap-1.5 text-white font-bold px-3 py-1.5 rounded-xl text-xs cursor-pointer shadow-md transition-transform bg-orange-600 ring-2 ring-orange-400 scale-105';
                        btn.innerHTML = '<i class="bi bi-eye-fill text-xs"></i> Melihat';
                    } else {
                        btn.className = 'inline-flex items-center gap-1.5 text-white font-bold px-3 py-1.5 rounded-xl text-xs cursor-pointer shadow-md transition-transform btn-3d-orange';
                        btn.innerHTML = '<i class="bi bi-folder-open text-xs"></i> Lihat Berkas';
                    }
                }
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const mp = document.getElementById('modalPenilaianSidang');
                if (mp && mp.classList.contains('active')) { closeModalPenilaianSidang(); return; }
                if (window.activeInlineEditorKey) { closeInlineComment(); return; }
                if (window.activeLihatBerkasIndices && window.activeLihatBerkasIndices.length > 0) closeLihatBerkasPanel();
            }
        });

        window.addEventListener('resize', () => {
            if (window.activeLihatBerkasIndices && window.activeLihatBerkasIndices.length > 0) updateLihatBerkasLayout();
        });

        // ============================================================
        // ACTION: Lulus Tahap
        // ============================================================
        function handleLulusStage(nim) {
            Swal.fire({
                title: `Lulus Tahap ${currentTahap}?`,
                html: `Mahasiswa akan dinyatakan <strong>LULUS</strong> di tahap <strong>${currentTahap}</strong>.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#a855f7',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-mortarboard-fill"></i> Ya, Luluskan',
                cancelButtonText: 'Batal',
            }).then((res) => {
                if (!res.isConfirmed) return;

                const fd = new FormData();
                fd.append('nim', nim);
                fd.append('tahap', currentTahap);

                Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

                fetch('<?= site_url("dosen_bimbingan/lulus_stage_ajax") ?>', {
                    method: 'POST', body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1800, showConfirmButton: false })
                            .then(() => fetchBimbinganData(true));
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal meluluskan.' });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Kesalahan koneksi.' });
                });
            });
        }

        // ============================================================
        // ACTION: Downgrade Tahap
        // ============================================================
        function handleDowngradeStage(nim) {
            const mhs = bimbinganData.find(m => m.nim === nim);
            if (!mhs) return;

            const order = ['Preview 1', 'Preview 2', 'Preview 3', 'Sidang'];
            const currIdx = order.indexOf(currentTahap);
            if (currIdx <= 0) {
                Swal.fire({ icon: 'info', title: 'Tidak Bisa Downgrade', text: 'Preview 1 adalah tahap paling awal.' });
                return;
            }

            const targetTahap = order[currIdx - 1];

            Swal.fire({
                title: 'Downgrade Tahap?',
                html: `Mahasiswa akan diturunkan dari <strong>${currentTahap}</strong> ke <strong>${targetTahap}</strong>.<br><br>
                       <div style="text-align:left;font-size:11px;color:#64748b;background:#f1f5f9;padding:8px 12px;border-radius:8px;">
                           <strong>Efek:</strong><br>
                           • Semua berkas di <strong>${targetTahap}</strong> di-reset ke <strong>Pending</strong><br>
                           • Data berkas di <strong>${currentTahap}</strong> akan dihapus<br>
                           • Flag "Lulus" akan direset
                       </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-arrow-down-circle"></i> Ya, Downgrade',
                cancelButtonText: 'Batal',
            }).then((res) => {
                if (!res.isConfirmed) return;

                const fd = new FormData();
                fd.append('nim', nim);
                fd.append('dari', currentTahap);
                fd.append('ke', targetTahap);
                fd.append('alasan', 'Downgrade oleh ' + ROLE.toUpperCase());

                Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });

                fetch('<?= site_url("dosen_bimbingan/downgrade_stage_ajax") ?>', {
                    method: 'POST', body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message, timer: 1800, showConfirmButton: false })
                            .then(() => fetchBimbinganData(true));
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal downgrade.' });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Kesalahan koneksi.' });
                });
            });
        }

        // Expose globals
        window.toggleLihatBerkasPanel = toggleLihatBerkasPanel;
        window.closeLihatBerkasPanel   = closeLihatBerkasPanel;
        window.previewBerkasItem       = previewBerkasItem;
        window.closeSinglePreview      = closeSinglePreview;
        window.togglePreviewIframeInteraction = togglePreviewIframeInteraction;
        window.updateTableButtonHighlights    = updateTableButtonHighlights;
        window.removeStudentFromLihatBerkas   = removeStudentFromLihatBerkas;
        window.showHoverPanel          = showHoverPanel;
        window.scheduleHidePanel       = scheduleHidePanel;
        window.submitHoverReview       = submitHoverReview;
        window.openDosenBatchModal     = openDosenBatchModal;
        window.closeDosenBatchModal    = closeDosenBatchModal;
        window.submitBatchItemReview   = submitBatchItemReview;
        window.submitDosenBatchApprove = submitDosenBatchApprove;
        window.unselectAllDosenStudents= unselectAllDosenStudents;
        window.switchDosenTab          = switchDosenTab;
        window.setDosenFilter          = setDosenFilter;
        window.changePerPage           = changePerPage;
        window.goToPage                = goToPage;
        window.doSearch                = doMultiSearchDW;
        window.showUnifiedCommentModal = showUnifiedCommentModal;
        window.switchCommentTab        = switchCommentTab;
        window.closeCommentModal       = closeCommentModal;
        window.addFilterRowDW          = addFilterRowDW;
        window.removeFilterRowDW       = removeFilterRowDW;
        window.renderFilterRowsDW      = renderFilterRowsDW;
        window.doMultiSearchDW         = doMultiSearchDW;
        window.resetFiltersDW          = resetFiltersDW;
        window.getUploadBaseUrl        = getUploadBaseUrl;
        window.handleLulusStage        = handleLulusStage;
        window.handleDowngradeStage    = handleDowngradeStage;
        window.getResponsibleRole      = getResponsibleRole;
        window.isCurrentUserPIC        = isCurrentUserPIC;
        // NEW: inline editor & fullscreen
        window.openInlineComment       = openInlineComment;
        window.closeInlineComment      = closeInlineComment;
        window.submitInlineComment     = submitInlineComment;
        window.toggleFullscreenPreview = toggleFullscreenPreview;
    </script>

    <script>
        // ============================================================
        // RUBRIK PENILAIAN PRODI (Hardcoded)
        // ============================================================
        const RUBRIK_PENILAIAN_PRODI = {
            'DKV': {
                peminatan: {
                    'Desain Grafis': [
                        { id:'k1', title:'Kreativitas & Orisinalitas', desc:'Kemampuan menghasilkan ide desain yang baru dan unik.', bobot:20 },
                        { id:'k2', title:'Eksekusi Visual', desc:'Penguasaan elemen & prinsip desain (tipografi, warna, komposisi).', bobot:25 },
                        { id:'k3', title:'Keselarasan Konsep & Solusi', desc:'Seberapa baik karya menjawab permasalahan/brief.', bobot:25 },
                        { id:'k4', title:'Keterampilan Teknis & Media', desc:'Penguasaan perangkat lunak atau teknik manual.', bobot:15 },
                        { id:'k5', title:'Presentasi & Komunikasi', desc:'Kemampuan mempresentasikan dan mempertahankan argumen.', bobot:15 }
                    ],
                    'Multimedia': [
                        { id:'k1', title:'Kreativitas Ide & Narasi', desc:'Kekuatan storytelling dan orisinalitas ide.', bobot:20 },
                        { id:'k2', title:'Eksekusi Audio-Visual', desc:'Kualitas sinematografi, animasi, suara, penyuntingan.', bobot:25 },
                        { id:'k3', title:'Fungsionalitas & Interaktivitas', desc:'Tingkat kebergunaan dan interaksi media.', bobot:25 },
                        { id:'k4', title:'Keterampilan Teknis Media', desc:'Penguasaan teknologi, software, atau peralatan.', bobot:15 },
                        { id:'k5', title:'Presentasi Karya', desc:'Sistematika penyajian dan kemampuan menjawab.', bobot:15 }
                    ]
                }
            },
            'IF': {
                peminatan: {
                    'Rekayasa Perangkat Lunak': [
                        { id:'k1', title:'Analisis & Perancangan Sistem', desc:'Kedalaman analisis kebutuhan dan perancangan.', bobot:25 },
                        { id:'k2', title:'Implementasi & Kualitas Kode', desc:'Kualitas implementasi teknis dan rekayasa perangkat lunak.', bobot:30 },
                        { id:'k3', title:'Pengujian & Validasi', desc:'Metodologi pengujian dan validasi hasil.', bobot:20 },
                        { id:'k4', title:'Dokumentasi & Laporan', desc:'Kualitas dokumentasi teknis dan laporan TA.', bobot:15 },
                        { id:'k5', title:'Presentasi & Sikap', desc:'Kemampuan menyampaikan dan mempertahankan hasil.', bobot:10 }
                    ],
                    'Kecerdasan Artifisial': [
                        { id:'k1', title:'Pemahaman Konsep AI', desc:'Penguasaan teori dan konsep AI/ML.', bobot:25 },
                        { id:'k2', title:'Perancangan Model', desc:'Ketepatan pemilihan model dan metrik.', bobot:25 },
                        { id:'k3', title:'Eksperimen & Evaluasi', desc:'Kualitas eksperimen dan evaluasi hasil.', bobot:25 },
                        { id:'k4', title:'Dokumentasi & Laporan', desc:'Kualitas laporan dan dokumentasi.', bobot:15 },
                        { id:'k5', title:'Presentasi & Sikap', desc:'Kemampuan menyampaikan hasil.', bobot:10 }
                    ]
                }
            },
            'SI': {
                peminatan: {
                    'Sistem Informasi Enterprise': [
                        { id:'k1', title:'Analisis Proses Bisnis', desc:'Kedalaman analisis proses bisnis & kebutuhan sistem.', bobot:25 },
                        { id:'k2', title:'Perancangan Sistem', desc:'Kualitas perancangan arsitektur & data.', bobot:25 },
                        { id:'k3', title:'Implementasi & Integrasi', desc:'Keberhasilan implementasi dan integrasi sistem.', bobot:20 },
                        { id:'k4', title:'Dokumentasi & Laporan', desc:'Kualitas dokumentasi dan laporan.', bobot:15 },
                        { id:'k5', title:'Presentasi & Sikap', desc:'Kemampuan menyampaikan hasil.', bobot:15 }
                    ]
                }
            }
        };

        function _penilaianEscapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#039;");
        }

        function detectProdiKey(prodiName) {
            if (!prodiName) return 'DKV';
            const p = String(prodiName).toLowerCase();
            if (p.includes('sistem informasi')) return 'SI';
            if (p.includes('informatika') || p.includes('ilmu komputer')) return 'IF';
            if (p.includes('dkv') || p.includes('desain komunikasi visual')) return 'DKV';
            return 'DKV';
        }

        window.openModalPenilaianSidang = function (nim) {
            const modal = document.getElementById('modalPenilaianSidang');
            if (!modal) return;
            document.getElementById('penilaianNim').value = '';
            document.getElementById('penilaianNamaMhs').textContent = 'Memuat...';
            document.getElementById('penilaianNimMhs').textContent = '-';
            document.getElementById('penilaianJudulTa').textContent = '-';
            document.getElementById('penilaianTglText').textContent = 'Belum Ada Jadwal';
            document.getElementById('penilaianRuanganText').textContent = '-';
            document.getElementById('penilaianPembimbing1').textContent = 'Pembimbing 1: -';
            document.getElementById('penilaianPembimbing2').textContent = 'Pembimbing 2: -';
            document.getElementById('penilaianPenguji1').textContent = 'Penguji 1: -';
            document.getElementById('penilaianPenguji2').textContent = 'Penguji 2: -';
            document.getElementById('penilaianCatatan').value = '';
            document.getElementById('penilaianTotalScore').textContent = '0.00';
            document.getElementById('penilaianGradeBadge').textContent = '-';
            document.getElementById('penilaianGradeBadge').className = 'grade-badge grade-none';
            document.getElementById('penilaianPrevNilaiWrap').classList.add('hidden');
            document.getElementById('penilaianRubrikContainer').innerHTML = '<div class="text-center py-6 text-slate-400 text-xs"><i class="bi bi-arrow-repeat inline-block animate-spin mr-1"></i> Memuat rubrik...</div>';
            modal.classList.add('active');

            const url = `<?= site_url('mahasiswa/get_detail_nilai_sidang_ajax') ?>?nim=${encodeURIComponent(nim)}`;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(res => {
                    if (!res.status || !res.data) {
                        showToast(res.message || 'Gagal memuat data.', 'error');
                        closeModalPenilaianSidang();
                        return;
                    }
                    fillModalPenilaianSidang(res.data);
                })
                .catch(err => {
                    console.error(err);
                    showToast('Kesalahan koneksi saat memuat data.', 'error');
                    closeModalPenilaianSidang();
                });
        };

        function fillModalPenilaianSidang(data) {
            document.getElementById('penilaianNim').value = data.nim;
            document.getElementById('penilaianNamaMhs').textContent = data.nama || '-';
            document.getElementById('penilaianNimMhs').textContent = 'NIM: ' + (data.nim || '-');
            document.getElementById('penilaianJudulTa').textContent = data.judul || '-';

            let tglText = 'Belum Ada Jadwal';
            if (data.tgl_sidang) {
                try {
                    const dt = new Date(data.tgl_sidang + (data.waktu_sidang ? 'T' + data.waktu_sidang : ''));
                    tglText = dt.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
                        + (data.waktu_sidang ? ' — ' + data.waktu_sidang + ' WIB' : '');
                } catch(e) { tglText = data.tgl_sidang; }
            }
            document.getElementById('penilaianTglText').textContent = tglText;
            document.getElementById('penilaianRuanganText').textContent = data.ruangan_sidang || 'Belum Ditentukan';
            document.getElementById('penilaianPembimbing1').textContent = 'Pembimbing 1: ' + (data.pembimbing_1 || '-');
            document.getElementById('penilaianPembimbing2').textContent = 'Pembimbing 2: ' + (data.pembimbing_2 || '-');
            document.getElementById('penilaianPenguji1').textContent = 'Penguji 1: ' + (data.penguji_1 || '-');
            document.getElementById('penilaianPenguji2').textContent = 'Penguji 2: ' + (data.penguji_2 || '-');

            const prodiKey = detectProdiKey(data.prodi || data.peminatan);
            document.getElementById('penilaianProdiSelect').value = prodiKey;
            setupPenilaianPeminatanOptions(prodiKey, data.peminatan);

            const myDetail = (data.detail && data.detail[MODEL_POSISI]) || null;
            const myNilai  = (data.nilai && data.nilai[MODEL_POSISI]) || 0;

            if (myNilai > 0) {
                document.getElementById('penilaianPrevNilaiWrap').classList.remove('hidden');
                document.getElementById('penilaianPrevNilai').textContent = myNilai.toFixed(2);
            } else {
                document.getElementById('penilaianPrevNilaiWrap').classList.add('hidden');
            }

            document.getElementById('penilaianCatatan').value = (myDetail && myDetail.catatan) ? myDetail.catatan : '';
            document.getElementById('penilaianStatusKelulusan').value = 'Lulus';

            renderPenilaianRubrik((myDetail && myDetail.detail) ? myDetail.detail : null, myNilai);

            if (myDetail && Array.isArray(myDetail.detail) && myDetail.detail.length > 0) {
                setTimeout(() => {
                    myDetail.detail.forEach((d) => {
                        const inp = document.querySelector(`.rubrik-score-input[data-crit-id="${d.id}"]`);
                        if (inp && d.nilai !== undefined && d.nilai !== null) inp.value = d.nilai;
                    });
                    calculatePenilaianScore();
                }, 30);
            } else {
                calculatePenilaianScore();
            }
        }

        window.closeModalPenilaianSidang = function () {
            const modal = document.getElementById('modalPenilaianSidang');
            if (modal) modal.classList.remove('active');
        };

        function setupPenilaianPeminatanOptions(prodiKey, selectedPeminatan) {
            const sel = document.getElementById('penilaianPeminatanSelect');
            if (!sel) return;
            const prodiData = RUBRIK_PENILAIAN_PRODI[prodiKey] || RUBRIK_PENILAIAN_PRODI['DKV'];
            const list = Object.keys(prodiData.peminatan || {});
            let html = '';
            list.forEach(p => {
                const isSel = (p === selectedPeminatan) || (!selectedPeminatan && p === list[0]);
                html += `<option value="${p}" ${isSel ? 'selected' : ''}>${p}</option>`;
            });
            sel.innerHTML = html;
        }

        window.onPenilaianProdiChange = function (prodiVal) {
            const prodiKey = detectProdiKey(prodiVal);
            setupPenilaianPeminatanOptions(prodiKey, null);
            renderPenilaianRubrik(null, 0);
            calculatePenilaianScore();
        };
        window.onPenilaianPeminatanChange = function () {
            renderPenilaianRubrik(null, 0);
            calculatePenilaianScore();
        };

        window.renderPenilaianRubrik = function (existingDetail, myNilai) {
            const container = document.getElementById('penilaianRubrikContainer');
            const prodiSelect = document.getElementById('penilaianProdiSelect');
            const peminatanSelect = document.getElementById('penilaianPeminatanSelect');
            const totalBobotLabel = document.getElementById('penilaianTotalBobotLabel');
            if (!container) return;
            const prodiKey = detectProdiKey(prodiSelect ? prodiSelect.value : 'DKV');
            const peminatanKey = peminatanSelect ? peminatanSelect.value : '';
            const prodiData = RUBRIK_PENILAIAN_PRODI[prodiKey] || RUBRIK_PENILAIAN_PRODI['DKV'];
            const criteriaList = prodiData.peminatan[peminatanKey] || prodiData.peminatan[Object.keys(prodiData.peminatan)[0]] || [];

            let mapNilaiTersimpan = {};
            if (existingDetail && Array.isArray(existingDetail)) {
                existingDetail.forEach((d) => { if (d && d.id) mapNilaiTersimpan[d.id] = d.nilai; });
            }

            let totalBobot = 0;
            let html = '';
            criteriaList.forEach((crit, idx) => {
                const bobot = parseFloat(crit.bobot) || 25;
                totalBobot += bobot;
                const val = (mapNilaiTersimpan[crit.id] !== undefined && mapNilaiTersimpan[crit.id] !== null) ? mapNilaiTersimpan[crit.id] : '';
                html += `
                    <div class="rubrik-row">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-600 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-md shadow-amber-500/20 mt-0.5">${idx + 1}</div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">Kriteria #${idx + 1}</span>
                                        <h5 class="text-xs sm:text-sm font-extrabold text-slate-900">${_penilaianEscapeHtml(crit.title)}</h5>
                                    </div>
                                    <p class="text-[11px] text-slate-500 leading-relaxed mt-1">${_penilaianEscapeHtml(crit.desc)}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0 self-end sm:self-center">
                                <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">Bobot: <strong class="text-amber-700">${bobot}%</strong></span>
                                <input type="number" min="0" max="100" step="0.5" data-bobot="${bobot}" data-crit-id="${_penilaianEscapeHtml(crit.id)}" data-crit-title="${_penilaianEscapeHtml(crit.title)}" data-crit-desc="${_penilaianEscapeHtml(crit.desc)}" value="${val}" placeholder="0-100" oninput="calculatePenilaianScore()" class="rubrik-score-input" required>
                            </div>
                        </div>
                    </div>
                `;
            });
            if (totalBobotLabel) {
                totalBobotLabel.textContent = totalBobot + '%';
                totalBobotLabel.className = (totalBobot === 100) ? 'text-slate-700 font-extrabold' : 'text-rose-600 font-extrabold';
            }
            container.innerHTML = html;
        };

        window.calculatePenilaianScore = function () {
            const inputs = document.querySelectorAll('.rubrik-score-input');
            const totalScoreEl = document.getElementById('penilaianTotalScore');
            const gradeBadgeEl = document.getElementById('penilaianGradeBadge');
            const statusKelEl  = document.getElementById('penilaianStatusKelulusan');
            let totalWeighted = 0;
            let hasAnyInput = false;
            inputs.forEach(inp => {
                const val = parseFloat(inp.value);
                const bobot = parseFloat(inp.getAttribute('data-bobot')) || 0;
                if (!isNaN(val)) { totalWeighted += (val * bobot) / 100; hasAnyInput = true; }
            });
            const finalScore = hasAnyInput ? totalWeighted : 0;
            if (totalScoreEl) totalScoreEl.textContent = finalScore.toFixed(2);
            let grade = '-';
            let gradeClass = 'grade-badge grade-none';
            if (hasAnyInput) {
                if (finalScore >= 85)         { grade = 'A';  gradeClass = 'grade-badge grade-A'; }
                else if (finalScore >= 77.5)  { grade = 'AB'; gradeClass = 'grade-badge grade-AB'; }
                else if (finalScore >= 70)    { grade = 'B';  gradeClass = 'grade-badge grade-B'; }
                else if (finalScore >= 62.5)  { grade = 'BC'; gradeClass = 'grade-badge grade-BC'; }
                else if (finalScore >= 55)    { grade = 'C';  gradeClass = 'grade-badge grade-C'; }
                else if (finalScore >= 45)    { grade = 'D';  gradeClass = 'grade-badge grade-D'; }
                else                          { grade = 'E';  gradeClass = 'grade-badge grade-E'; }
            }
            if (gradeBadgeEl) {
                gradeBadgeEl.textContent = grade;
                gradeBadgeEl.className = gradeClass;
            }
            if (statusKelEl && hasAnyInput) {
                if (finalScore >= 70) statusKelEl.value = 'Lulus';
                else if (finalScore >= 55) statusKelEl.value = 'Lulus dengan Revisi';
                else statusKelEl.value = 'Tidak Lulus';
            }
            return { score: finalScore, grade: grade };
        };

        window.submitPenilaianSidang = function (e) {
            e.preventDefault();
            const nim = document.getElementById('penilaianNim')?.value;
            const catatan = document.getElementById('penilaianCatatan')?.value || '';
            const calc = calculatePenilaianScore();
            if (!nim) { Swal.fire({ icon:'error', title:'Error', text:'NIM tidak valid.' }); return; }
            const scoreInputs = document.querySelectorAll('.rubrik-score-input');
            const details = [];
            let allValid = true;
            scoreInputs.forEach(inp => {
                const val = parseFloat(inp.value);
                if (isNaN(val) || val < 0 || val > 100) allValid = false;
                details.push({
                    id: inp.getAttribute('data-crit-id'),
                    title: inp.getAttribute('data-crit-title'),
                    desc: inp.getAttribute('data-crit-desc') || '',
                    bobot: parseFloat(inp.getAttribute('data-bobot')),
                    nilai: isNaN(val) ? 0 : val,
                });
            });
            if (!allValid) {
                Swal.fire({ icon:'warning', title:'Nilai Belum Lengkap', text:'Pastikan seluruh kolom nilai terisi (0-100).' });
                return;
            }
            const btn = document.getElementById('btnSubmitPenilaianSidang');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-arrow-repeat inline-block animate-spin"></i> Menyimpan...'; }
            const fd = new FormData();
            fd.append('nim', nim);
            fd.append('posisi', MODEL_POSISI);
            fd.append('nilai_akhir', calc.score.toFixed(2));
            fd.append('catatan', catatan);
            fd.append('detail_penilaian', JSON.stringify(details));
            fetch('<?= site_url('mahasiswa/simpan_nilai_sidang_ajax') ?>', {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res && res.status) {
                    closeModalPenilaianSidang();
                    Swal.fire({ icon:'success', title:'Berhasil', text: res.message || 'Nilai sidang tersimpan.' }).then(() => { fetchBimbinganData(true); });
                } else {
                    Swal.fire({ icon:'error', title:'Gagal', text: res.message || 'Gagal menyimpan nilai.' });
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({ icon:'error', title:'Error', text:'Terjadi kesalahan sistem.' });
            })
            .finally(() => {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-save-fill"></i> Simpan Penilaian Sidang'; }
            });
        };
    </script>

    <div id="floatingBapContainer" class="fixed bottom-0 left-16 sm:left-20 top-16 pointer-events-none z-[100000] p-3 sm:p-5 flex flex-col items-start justify-start gap-4 overflow-y-auto max-h-[calc(100vh-4rem)] scroll-smooth" style="display: none; max-width: 65vw;"></div>

    <script>
        window.activeBapPopups = [];

        function toggleBapPopup(nim, nama, type) {
            const popupId = 'bap_' + type + '_' + nim;
            const existingIdx = window.activeBapPopups.findIndex(p => p.id === popupId);
            if (existingIdx > -1) { closeBapPopup(popupId); return; }
            window.activeBapPopups.push({ id: popupId, nim, nama, type, isMinimized: false });
            renderBapFloatingPopups();
            highlightBapCard(popupId);
            updateBapTableButtonHighlights();
        }

        function switchBapType(id, newType) {
            const p = window.activeBapPopups.find(item => item.id === id);
            if (p) {
                p.type = newType;
                p.id = 'bap_' + newType + '_' + p.nim;
                renderBapFloatingPopups();
                highlightBapCard(p.id);
                updateBapTableButtonHighlights();
            }
        }
        function toggleMinimizeBap(id) {
            const p = window.activeBapPopups.find(x => x.id === id);
            if (p) { p.isMinimized = !p.isMinimized; renderBapFloatingPopups(); }
        }
        function closeBapPopup(id) {
            window.activeBapPopups = window.activeBapPopups.filter(p => p.id !== id);
            renderBapFloatingPopups();
            updateBapTableButtonHighlights();
        }
        function closeAllBapPopups() {
            window.activeBapPopups = [];
            renderBapFloatingPopups();
            updateBapTableButtonHighlights();
        }
        function highlightBapCard(id) {
            setTimeout(() => {
                const el = document.getElementById('bapCard_' + id);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', inline: 'end', block: 'nearest' });
                    el.classList.add('ring-4', 'ring-orange-400', 'scale-[1.01]');
                    setTimeout(() => el.classList.remove('ring-4', 'ring-orange-400', 'scale-[1.01]'), 800);
                }
            }, 50);
        }
        function updateBapTableButtonHighlights() {
            document.querySelectorAll('.btn-bap-action').forEach(btn => {
                const isIgraciasBtn = btn.id.startsWith('btn_bap_igracias_');
                const nim = btn.id.replace('btn_bap_igracias_', '').replace('btn_bap_fakultas_', '');
                const targetType = isIgraciasBtn ? 'igracias' : 'fakultas';
                const isActive = window.activeBapPopups.some(p => p.nim === nim && p.type === targetType);
                if (isActive) btn.classList.add('ring-2', 'ring-orange-300', 'ring-offset-1', 'scale-[1.03]');
                else btn.classList.remove('ring-2', 'ring-orange-300', 'ring-offset-1', 'scale-[1.03]');
            });
        }
        function renderBapFloatingPopups() {
            const container = document.getElementById('floatingBapContainer');
            if (!container) return;
            if (window.activeBapPopups.length === 0) { container.style.display = 'none'; container.innerHTML = ''; return; }
            container.style.display = 'flex';
            const total = window.activeBapPopups.length;
            let html = '';

            if (total > 1) {
                html += `
                    <div class="pointer-events-auto shrink-0 self-start mb-1 bg-slate-900/95 backdrop-blur text-white px-3.5 py-1.5 rounded-2xl shadow-xl border border-slate-700 flex items-center gap-2 text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-bold">${total} Dokumen BAP Terbuka</span>
                        <button type="button" onclick="closeAllBapPopups()" class="ml-1 px-2 py-0.5 rounded-lg bg-rose-600/80 hover:bg-rose-600 text-white font-bold text-[10px] transition cursor-pointer">Tutup Semua</button>
                    </div>
                `;
            }

            window.activeBapPopups.forEach(p => {
                const isIgracias = p.type === 'igracias';
                const previewUrl = isIgracias ? '<?= site_url('adminlayanan/preview_bap_igracias/'); ?>' + p.nim : '<?= site_url('adminlayanan/preview_bap_fakultas/'); ?>' + p.nim;
                const printUrl   = isIgracias ? '<?= site_url('adminlayanan/cetak_bap_igracias/'); ?>'   + p.nim : '<?= site_url('adminlayanan/cetak_bap_fakultas/'); ?>'   + p.nim;
                const title = isIgracias ? 'BAP IGrACIAS — ' + p.nama : 'BAP Fakultas — ' + p.nama;
                const sub   = isIgracias ? 'Dokumen 1: Berita Acara Sidang (IGrACIAS)' : 'Dokumen 2: Berita Acara & Nilai FIK Tel-U';
                const badgeColor = isIgracias ? 'bg-orange-500/20 text-orange-400 border border-orange-500/40' : 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/40';
                const icon  = isIgracias ? 'bi bi-file-earmark-text-fill' : 'bi bi-award-fill';

                if (p.isMinimized) {
                    html += `
                        <div id="bapCard_${p.id}" class="pointer-events-auto bg-slate-900 text-white rounded-2xl shadow-2xl border border-slate-700 shrink-0 w-72 p-2.5 flex items-center justify-between gap-2 hover:border-orange-500 cursor-pointer" onclick="toggleMinimizeBap('${p.id}')">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 rounded-lg ${badgeColor} flex items-center justify-center text-xs shrink-0"><i class="${icon}"></i></div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold truncate max-w-[150px]">${title}</h4>
                                    <p class="text-[9px] text-slate-400 truncate">${p.nim}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation()">
                                <button type="button" onclick="toggleMinimizeBap('${p.id}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs cursor-pointer"><i class="bi bi-arrows-angle-expand text-[10px]"></i></button>
                                <button type="button" onclick="closeBapPopup('${p.id}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center text-xs ml-0.5 cursor-pointer"><i class="bi bi-x-lg text-[10px]"></i></button>
                            </div>
                        </div>`;
                } else {
                    html += `
                        <div id="bapCard_${p.id}" class="pointer-events-auto bg-white rounded-3xl shadow-2xl border border-slate-300 flex flex-col overflow-hidden shrink-0 w-[92vw] sm:w-[480px] xl:w-[520px] h-[560px] max-h-[78vh]">
                            <div class="p-2.5 px-3.5 bg-slate-900 text-white flex items-center justify-between gap-2 shrink-0 border-b border-slate-800">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-6 h-6 rounded-lg ${badgeColor} flex items-center justify-center font-bold text-xs shrink-0"><i class="${icon} text-[11px]"></i></div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-white truncate max-w-[170px] sm:max-w-[210px]">${title}</h4>
                                        <p class="text-[10px] text-slate-300 truncate">${sub}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <a href="${printUrl}" target="_blank" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs cursor-pointer" title="Buka Cetak / PDF"><i class="bi bi-arrow-up-right-square text-[10px]"></i></a>
                                    <button type="button" onclick="toggleMinimizeBap('${p.id}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-xs cursor-pointer" title="Minimize"><i class="bi bi-dash-lg text-[10px] font-bold"></i></button>
                                    <button type="button" onclick="closeBapPopup('${p.id}')" class="w-6 h-6 rounded-lg bg-white/10 hover:bg-rose-600 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold ml-0.5 cursor-pointer"><i class="bi bi-x-lg text-[10px]"></i></button>
                                </div>
                            </div>
                            <div class="bg-slate-950 p-1.5 px-3 flex items-center gap-2 border-b border-slate-800 shrink-0">
                                <button type="button" onclick="switchBapType('${p.id}', 'igracias')" class="flex-1 py-1 px-2.5 rounded-lg text-[10.5px] font-bold flex items-center justify-center gap-1.5 cursor-pointer ${isIgracias ? 'bg-orange-600 text-white shadow-xs' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white'}">
                                    <i class="bi bi-file-earmark-text-fill"></i><span>1. BAP (IGRACIAS)</span>
                                </button>
                                <button type="button" onclick="switchBapType('${p.id}', 'fakultas')" class="flex-1 py-1 px-2.5 rounded-lg text-[10.5px] font-bold flex items-center justify-center gap-1.5 cursor-pointer ${!isIgracias ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-white'}">
                                    <i class="bi bi-award-fill"></i><span>2. BAP FAKULTAS</span>
                                </button>
                            </div>
                            <div class="flex-1 bg-slate-100 p-1.5 overflow-hidden flex flex-col relative">
                                <iframe src="${previewUrl}" class="w-full h-full bg-white rounded-2xl shadow-inner border border-slate-200" frameborder="0"></iframe>
                            </div>
                            <div class="p-2 px-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs shrink-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="font-mono text-[11px] font-bold text-orange-600">${p.nim}</span>
                                    <span class="text-slate-400 text-[11px]">|</span>
                                    <span class="text-[11px] text-slate-600 truncate font-semibold">${p.nama}</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0 flex-wrap">
                                    <a href="${printUrl}" target="_blank" class="px-3 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-bold text-[11px] transition flex items-center gap-1 cursor-pointer"><i class="bi bi-printer-fill text-[10px]"></i> Cetak PDF</a>
                                    ${(typeof IS_U1 !== 'undefined' && IS_U1) ? `
                                        <button type="button" onclick="triggerBapUpload('${p.nim}')" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] transition flex items-center gap-1 cursor-pointer" title="Upload dokumen BAP untuk mahasiswa ini">
                                            <i class="bi bi-cloud-arrow-up-fill text-[10px]"></i> Upload BAP
                                        </button>
                                    ` : ''}
                                    <button type="button" onclick="closeBapPopup('${p.id}')" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 font-bold text-[11px] transition cursor-pointer">Tutup</button>
                                </div>
                            </div>
                        </div>`;
                }
            });
            container.innerHTML = html;
        }

        window.toggleBapPopup = toggleBapPopup;
        window.switchBapType = switchBapType;
        window.toggleMinimizeBap = toggleMinimizeBap;
        window.closeBapPopup = closeBapPopup;
        window.closeAllBapPopups = closeAllBapPopups;
        window.updateBapTableButtonHighlights = updateBapTableButtonHighlights;

        function triggerBapUpload(nim) {
            if (!nim) return;
            Swal.fire({
                title: 'Publikasikan BAP?',
                html: 'Dokumen <strong>Berita Acara Sidang (IGrACIAS + Fakultas)</strong> akan dipublikasikan ke halaman mahasiswa.<br><br><span style="font-size:11px;color:#64748b;">Mahasiswa akan dapat melihat & mengunduh BAP.</span>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-cloud-arrow-up-fill"></i> Ya, Publikasikan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;
                const fd = new FormData();
                fd.append('nim', nim);
                showToast('Mempublikasikan BAP...', 'success');
                fetch('<?= site_url("dosen_bimbingan/upload_bap_ajax") ?>', {
                    method: 'POST', body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(data => {
                    if (data && data.status) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message || 'BAP berhasil dipublikasikan ke mahasiswa.', timer: 1800, showConfirmButton: false }).then(() => window.location.reload());
                    } else {
                        Swal.fire('Gagal!', (data && data.message) || 'Gagal mempublikasikan BAP.', 'error');
                    }
                })
                .catch(err => {
                    console.error('Publish BAP error:', err);
                    Swal.fire('Error!', 'Kesalahan koneksi: ' + err.message, 'error');
                });
            });
        }

        window.triggerBapUpload = triggerBapUpload;
    </script>

    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>