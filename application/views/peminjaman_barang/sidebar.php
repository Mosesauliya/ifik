<?php
/**
 * Dedicated Sidebar Component for Modul Peminjaman Barang & Alat
 * Path: application/views/peminjaman_barang/sidebar.php
 * Mengadopsi pola curved sidebar mandiri seperti di Admin Layanan (LAA)
 */

$current_full = rtrim(current_url(), '/');
$session_role_id = (int)($this->session->userdata('role_id') ?? 0);
$is_laboran_or_admin = in_array($session_role_id, [1, 21]);

$barangNavItems = [
    [
        'heading' => 'Katalog Alat Studio',
        'href'    => site_url('peminjaman_barang'),
        'icon_3d' => 'assets/images/icons_3d/ruangan.png'
    ],
    [
        'heading' => 'Riwayat Peminjaman',
        'href'    => site_url('peminjaman_barang/riwayat'),
        'icon_3d' => 'assets/images/icons_3d/riwayat_booking.png'
    ],
];

// Menu khusus Laboran / Admin untuk scanner serah terima fisik barang
if ($is_laboran_or_admin) {
    $barangNavItems[] = [
        'heading' => 'Scanner QR Serah Terima',
        'href'    => site_url('peminjamanbarang/scanner'),
        'icon_3d' => 'assets/images/icons_3d/preview2.png'
    ];
}

// Navigasi umum IFIK
$barangNavItems[] = [
    'heading' => 'Dashboard Utama',
    'href'    => site_url('dashboard'),
    'icon_3d' => 'assets/images/icons_3d/home.png'
];

$barangNavItems[] = [
    'heading' => 'Kalender Jadwal',
    'href'    => site_url('kalender'),
    'icon_3d' => 'assets/images/icons_3d/kalender.png'
];

$barangNavItems[] = [
    'heading' => 'Keluar',
    'href'    => site_url('login/logout'),
    'icon_3d' => 'assets/images/icons_3d/logout.png'
];
?>

<!-- LAA Standalone Sidebar Stylesheet -->
<link rel="stylesheet" href="<?= base_url('assets/css/laa_sidebar.css?v=' . time()); ?>">

<style>
/* Dedicated Accordion Animation */
.laa-nav-submenu-wrapper {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows 0.38s cubic-bezier(0.16, 1, 0.3, 1);
}

.laa-nav-dropdown.is-open .laa-nav-submenu-wrapper {
    grid-template-rows: 1fr;
}

.laa-nav-submenu-inner {
    overflow: hidden;
    opacity: 0;
    transform: translateY(-6px);
    transition: opacity 0.3s ease, transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.laa-nav-dropdown.is-open .laa-nav-submenu-inner {
    opacity: 1;
    transform: translateY(0);
}

.laa-dropdown-chevron {
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.3s ease;
}

.laa-nav-dropdown.is-open .laa-dropdown-chevron {
    transform: rotate(180deg);
    color: #ea580c;
}

/* =========================================================
   POLISHED HEADER & ACTION BUTTONS (ADMIN LAA STYLE)
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
    padding-left: 78px;
    padding-right: 12px;
}
@media (min-width: 1024px) {
    body.laa-sidebar-pushed .header-inner-pad {
        padding-left: 12px;
        padding-right: 12px;
    }
}

/* Polished Action Pill Button (Riwayat / Katalog) */
.btn-ifik-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    height: 40px;
    border-radius: 9999px;
    border: 1.5px solid #fed7aa;
    background: #fff7ed;
    color: #c2410c;
    font-size: 0.8rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 2px 6px rgba(234, 88, 12, 0.06);
    white-space: nowrap;
}
.btn-ifik-action i {
    font-size: 0.95rem;
    color: #ea580c;
    transition: transform 0.2s ease;
}
.btn-ifik-action:hover {
    background: #ffedd5;
    border-color: #fdba74;
    color: #9a3412;
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(234, 88, 12, 0.14);
}
.btn-ifik-action:hover i {
    transform: scale(1.12);
}
.btn-ifik-action:active {
    transform: scale(0.97);
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
</style>

<!-- Floating Trigger Button (Top Left) -->
<button type="button" id="curvedSidebarToggle" class="curved-sidebar-toggle-btn" aria-label="Toggle Sidebar Menu" title="Buka Menu Navigasi Peminjaman">
    <div class="curved-sidebar-burger">
        <span></span>
        <span></span>
        <span></span>
    </div>
</button>

<!-- Backdrop Blur Overlay -->
<div id="curvedSidebarBackdrop" class="curved-sidebar-backdrop"></div>

<!-- Sliding Sidebar Panel with Morphing Curved SVG (Left Side) -->
<aside id="curvedSidebarPanel" class="curved-sidebar-panel" aria-label="Sidebar Navigasi Peminjaman Barang">
    <div class="curved-sidebar-inner">
        <!-- Top Section: Header & Nav Links -->
        <div>
            <div class="curved-sidebar-header">
                <p>Peminjaman Barang &amp; Alat</p>
            </div>
            
            <nav class="curved-sidebar-nav">
                <?php foreach ($barangNavItems as $idx => $item): 
                    $num = sprintf('%02d', $idx + 1);
                    $icon3d = $item['icon_3d'] ?? null;
                    $hasChildren = !empty($item['children']);
                ?>
                    <?php if ($hasChildren): 
                        $hasActiveChild = false;
                        foreach ($item['children'] as $child) {
                            if (rtrim($child['href'], '/') === $current_full) {
                                $hasActiveChild = true;
                                break;
                            }
                        }
                    ?>
                        <div class="laa-nav-dropdown <?= $hasActiveChild ? 'is-open' : ''; ?>">
                            <button type="button" class="curved-nav-item laa-nav-dropdown-toggle w-full text-left flex items-center justify-between" onclick="toggleBarangSidebarDropdown(this)">
                                <div class="curved-nav-content">
                                    <?php if (!empty($icon3d)): ?>
                                        <div class="curved-nav-3d-wrap">
                                            <img src="<?= base_url($icon3d); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                                        </div>
                                    <?php else: ?>
                                        <span class="curved-nav-index"><?= $num ?>.</span>
                                    <?php endif; ?>
                                    <div class="curved-nav-text">
                                        <span class="curved-nav-heading"><?= htmlspecialchars($item['heading']); ?></span>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-down laa-dropdown-chevron text-slate-400 text-xs"></i>
                            </button>
                            <div class="laa-nav-submenu-wrapper">
                                <div class="laa-nav-submenu-inner pl-11 pr-2 py-1 space-y-1">
                                    <?php foreach ($item['children'] as $child): 
                                        $isChildActive = (rtrim($child['href'], '/') === $current_full);
                                    ?>
                                        <a href="<?= htmlspecialchars($child['href']); ?>" class="block px-3 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 <?= $isChildActive ? 'bg-orange-100/90 text-orange-700 font-bold border-l-2 border-orange-500 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80' ?>">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $isChildActive ? 'bg-orange-500' : 'bg-slate-300' ?>"></span>
                                            <?= htmlspecialchars($child['heading']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: 
                        $isItemActive = (rtrim($item['href'], '/') === $current_full);
                    ?>
                        <a href="<?= htmlspecialchars($item['href']); ?>" class="curved-nav-item <?= $isItemActive ? 'active' : ''; ?>">
                            <div class="curved-nav-content">
                                <?php if (!empty($icon3d)): ?>
                                    <div class="curved-nav-3d-wrap">
                                        <img src="<?= base_url($icon3d); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                                    </div>
                                <?php else: ?>
                                    <span class="curved-nav-index"><?= $num ?>.</span>
                                <?php endif; ?>
                                <div class="curved-nav-text">
                                    <span class="curved-nav-heading"><?= htmlspecialchars($item['heading']); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Bottom Section: Portal Info & Version -->
        <div class="curved-sidebar-footer">
            <div class="curved-sidebar-footer-brand">
                <i class="bi bi-box-seam text-orange-500"></i>
                <span>Peminjaman Barang • IFIK</span>
            </div>
            <span class="curved-sidebar-footer-version">v2.0</span>
        </div>
    </div>

    <!-- Morphing Bezier Curve SVG (Right Edge of Left Sidebar) -->
    <svg id="curvedSidebarSvg" class="curved-sidebar-svg">
        <path id="curvedSidebarPath" />
    </svg>
</aside>

<script>
function toggleBarangSidebarDropdown(btn) {
    const parent = btn.closest('.laa-nav-dropdown');
    if (!parent) return;
    parent.classList.toggle('is-open');
}
</script>

<!-- LAA Sidebar Standalone Script -->
<script src="<?= base_url('assets/js/laa_sidebar.js?v=' . time()); ?>"></script>
