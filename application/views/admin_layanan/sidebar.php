<?php
/**
 * Dedicated Sidebar Component for Admin Layanan (LAA)
 * Path: application/views/admin_layanan/sidebar.php
 */

$current_full = rtrim(current_url(), '/');

$laaNavItems = [
    [
        'heading' => 'Pendaftaran TA',
        'icon_3d' => 'assets/images/icons_3d/daftar.png',
        'children' => [
            ['heading' => 'Pendaftaran TA',    'href' => site_url('adminlayanan')],
            ['heading' => 'Sudah Lulus Sidang','href' => site_url('adminlayanan/lulus_sidang')],
            ['heading' => 'Status Peserta TA', 'href' => site_url('adminlayanan/status_peserta_ta')],
            ['heading' => 'Reset File TA',     'href' => site_url('adminlayanan/reset_file_ta')],
        ]
    ],
    [
        'heading' => 'Pendaftaran Sidang',
        'href'    => site_url('adminlayanan/pendaftaran_sidang'),
        'icon_3d' => 'assets/images/icons_3d/sidang.png'
    ],
    [
        'heading' => 'Lihat Pembimbing',
        'href'    => site_url('adminlayanan/lihat_pembimbing'),
        'icon_3d' => 'assets/images/icons_3d/preview.png'
    ],
    [
        'heading' => 'Yudisium',
        'href'    => site_url('adminlayanan/yudisium'),
        'icon_3d' => 'assets/images/icons_3d/approval.png'
    ],
    [
        'heading' => 'Jadwal Sidang',
        'href'    => site_url('adminlayanan/jadwal_sidang'),
        'icon_3d' => 'assets/images/icons_3d/kalender.png'
    ],
    [
        'heading' => 'BAP Sidang',
        'href'    => site_url('adminlayanan/bap_sidang'),
        'icon_3d' => 'assets/images/icons_3d/tanda_tangan.png'
    ],
    [
        'heading' => 'Kelola Tiket LAA',
        'href'    => site_url('adminlayanan/ticketing'),
        'icon_3d' => 'assets/images/icons_3d/unit_ticketing.png'
    ],
    [
        'heading' => 'Kelola Berita',
        'href'    => site_url('news/newsroom'),
        'icon_3d' => 'assets/images/icons_3d/email_token.png'
    ],
    [
        'heading' => 'Keluar',
        'href'    => site_url('login/logout'),
        'icon_3d' => 'assets/images/icons_3d/logout.png'
    ]
];
?>

<!-- LAA Standalone Sidebar Stylesheet -->
<link rel="stylesheet" href="<?= base_url('assets/css/laa_sidebar.css?v=' . time()); ?>">

<style>
/* Dedicated LAA Accordion Animation */
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
</style>

<!-- Floating Trigger Button (Top Left) -->
<button type="button" id="curvedSidebarToggle" class="curved-sidebar-toggle-btn" aria-label="Toggle Sidebar Menu" title="Buka Menu Navigasi LAA">
    <div class="curved-sidebar-burger">
        <span></span>
        <span></span>
        <span></span>
    </div>
</button>

<!-- Backdrop Blur Overlay -->
<div id="curvedSidebarBackdrop" class="curved-sidebar-backdrop"></div>

<!-- Sliding Sidebar Panel with Morphing Curved SVG (Left Side) -->
<aside id="curvedSidebarPanel" class="curved-sidebar-panel" aria-label="Sidebar Navigasi LAA">
    <div class="curved-sidebar-inner">
        <!-- Top Section: Header & Nav Links -->
        <div>
            <div class="curved-sidebar-header">
                <p>Navigasi Admin LAA</p>
            </div>
            
            <nav class="curved-sidebar-nav">
                <?php foreach ($laaNavItems as $idx => $item): 
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
                            <button type="button" class="curved-nav-item laa-nav-dropdown-toggle w-full text-left flex items-center justify-between" onclick="toggleLaaSidebarDropdown(this)">
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
                <i class="fa-solid fa-graduation-cap text-orange-500"></i>
                <span>Admin Layanan (LAA) • IFIK</span>
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
function toggleLaaSidebarDropdown(btn) {
    const parent = btn.closest('.laa-nav-dropdown');
    if (!parent) return;
    parent.classList.toggle('is-open');
}
</script>

<!-- LAA Sidebar Standalone Styles -->
<link rel="stylesheet" href="<?= base_url('assets/css/laa_sidebar.css?v=' . time()); ?>">

<!-- LAA Sidebar Standalone Script -->
<script src="<?= base_url('assets/js/laa_sidebar.js?v=' . time()); ?>"></script>
