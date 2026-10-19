<?php
/**
 * Dedicated Sidebar Component for Modul Peminjaman Barang & Alat
 * Path: application/views/peminjaman_barang/sidebar.php
 * Fully aligned with Curved Sidebar Layout (Screenshot 2)
 */

$isLoggedIn    = (bool)$this->session->userdata('logged_in');
$sessionRoleId = $isLoggedIn ? (int)$this->session->userdata('role_id') : 0;
$session_name  = (string)($this->session->userdata('nama') ?: ($this->session->userdata('name') ?: ($this->session->userdata('username') ?: '')));
$session_username = (string)$this->session->userdata('username');
$session_nidn_nim = (string)($this->session->userdata('nidn_nim') ?: ($this->session->userdata('nim') ?: ''));

$roleBadgeMap = [
    0 => 'Publik / Tamu',
    1 => 'Panel Admin',
    2 => 'Ka. Ur / Ka Lab',
    3 => 'Portal Dosen',
    4 => 'Mahasiswa',
    5 => 'Admin Layanan',
    6 => 'Koordinator TA',
    7 => 'PIC KK',
    9 => 'Ketua KK',
    11 => 'Sekretariat',
    15 => 'SDM dan Keuangan',
    16 => 'Program Studi',
    17 => 'Kemahasiswaan',
    21 => 'Laboran',
    22 => 'Panel Super Admin'
];
$activeRoleBadge = $roleBadgeMap[$sessionRoleId] ?? ($this->session->userdata('role') ?: 'Portal IFIK');

$curr_uri = trim(uri_string(), '/');
$is_laboran_or_admin = in_array($sessionRoleId, [1, 21]);

$barangNavGroups = [
    [
        'category' => 'Layanan Peminjaman Alat',
        'items' => [
            [
                'heading' => 'Katalog Alat',
                'href'    => site_url('peminjaman_barang'),
                'icon_3d' => 'assets/images/icons_3d/ruangan.png',
                'is_active' => ($curr_uri === 'peminjaman_barang' || $curr_uri === 'peminjaman_barang/index')
            ],
            [
                'heading' => 'Riwayat Peminjaman',
                'href'    => site_url('peminjaman_barang/riwayat'),
                'icon_3d' => 'assets/images/icons_3d/riwayat_booking.png',
                'is_active' => (strpos($curr_uri, 'peminjaman_barang/riwayat') === 0)
            ],
            [
                'heading' => 'Kalender & Jadwal',
                'href'    => site_url('peminjaman_barang/kalender'),
                'icon_3d' => 'assets/images/icons_3d/kalender.png',
                'is_active' => (strpos($curr_uri, 'peminjaman_barang/kalender') === 0)
            ],
        ]
    ]
];



$barangNavGroups[] = [
    'category' => 'Navigasi Utama',
    'has_divider' => true,
    'items' => [
        [
            'heading' => 'Dashboard Utama',
            'href'    => site_url('dashboard'),
            'icon_3d' => 'assets/images/icons_3d/home.png',
            'is_active' => ($curr_uri === 'dashboard')
        ]
    ]
];

$barangNavGroups[] = [
    'category' => 'Akun',
    'has_divider' => true,
    'items' => [
        [
            'heading' => 'Keluar',
            'href'    => site_url('login/logout'),
            'icon_3d' => 'assets/images/icons_3d/logout.png',
            'is_active' => false
        ]
    ]
];
?>

<!-- Curved Sidebar Stylesheet -->
<link rel="stylesheet" href="<?= base_url('assets/css/curved_sidebar.css?v=' . time()); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* =========================================================
   CURVED SIDEBAR TYPOGRAPHY & DIVIDERS (MATCHING SCREENSHOT 2)
   ========================================================= */
.curved-nav-category {
    font-size: 0.62rem;
    font-weight: 800;
    color: #78350f;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 6px 8px 3px 8px;
    margin-top: 3px;
    user-select: none;
}
.curved-nav-category.has-divider {
    border-top: 1px solid rgba(245, 158, 11, 0.2);
    margin-top: 6px;
    padding-top: 8px;
}

.curved-header-role-badge {
    display: inline-flex;
    align-items: center;
    padding: 1px 7px;
    background: rgba(245, 158, 11, 0.22);
    border: 1px solid rgba(245, 158, 11, 0.38);
    border-radius: 9999px;
    font-size: 0.60rem;
    font-weight: 800;
    color: #78350f;
    letter-spacing: 0.02em;
}

.curved-sidebar-panel {
    background: rgba(255, 255, 255, 0.85) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border-right: 1.5px solid rgba(255, 255, 255, 0.85) !important;
    border-radius: 0 20px 20px 0 !important;
    box-shadow: inset 0 2px 0 rgba(255, 255, 255, 0.9), 0 20px 45px rgba(15, 23, 42, 0.08), 8px 0 25px rgba(0, 0, 0, 0.04) !important;
}

.curved-sidebar-svg {
    fill: rgba(255, 255, 255, 0.85) !important;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
}

.curved-sidebar-toggle-btn {
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.9) !important;
}

.curved-sidebar-inner {
    padding: 58px 10px 18px 10px !important;
}

.curved-nav-item {
    padding: 5px 6px !important;
    overflow: visible !important;
}

.curved-nav-content {
    gap: 8px !important;
}

.curved-nav-heading {
    font-size: 0.78rem !important;
    letter-spacing: -0.015em !important;
    white-space: nowrap !important;
}

.curved-nav-item.is-current {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
}
.curved-nav-item.is-current .curved-nav-heading,
.curved-nav-item.is-current .curved-nav-letter {
    color: #ea580c !important;
    font-weight: 800 !important;
    font-size: 0.78rem !important;
    letter-spacing: -0.015em !important;
}
.curved-nav-item.is-current .curved-nav-3d-wrap {
    transform: translateY(-1px) scale(1.08);
    filter: drop-shadow(0 4px 8px rgba(234, 88, 12, 0.22));
}

.curved-nav-item:hover {
    background: rgba(245, 158, 11, 0.12) !important;
    backdrop-filter: blur(8px) !important;
}
.curved-nav-item:hover .curved-nav-heading {
    transform: translateX(1px);
    color: #ea580c;
}

/* User Profile Card inside Sidebar */
.curved-sidebar-user-card {
    margin: 8px 0 6px 0;
    padding: 7px 9px;
    background: rgba(255, 255, 255, 0.65);
    border-radius: 10px;
    border: 1px solid rgba(226, 232, 240, 0.9);
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}
.curved-sidebar-user-avatar {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
    color: #ffffff;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(234, 88, 12, 0.25);
}
.curved-sidebar-user-info {
    flex: 1;
    min-width: 0;
}
.curved-sidebar-user-name {
    font-size: 0.78rem;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}
.curved-sidebar-user-role {
    font-size: 0.62rem;
    color: #64748b;
    font-weight: 600;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* =========================================================
   HEADER & TOPBAR SPACING (CLEARANCE FOR TOGGLE BUTTON)
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

/* Push Content Layout for Peminjaman Barang (Unified 270px width) */
#laaMainContentWrapper,
.page-wrapper-for-sidebar {
    width: 100%;
    min-width: 0;
    transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1), width 0.75s cubic-bezier(0.76, 0, 0.24, 1);
    box-sizing: border-box;
}

@media (min-width: 992px) {
    body:not(.curved-sidebar-desktop-collapsed) #laaMainContentWrapper,
    body:not(.curved-sidebar-desktop-collapsed) .page-wrapper-for-sidebar {
        margin-left: 270px;
        width: calc(100% - 270px);
    }
    body.curved-sidebar-desktop-collapsed #laaMainContentWrapper,
    body.curved-sidebar-desktop-collapsed .page-wrapper-for-sidebar {
        margin-left: 0;
        width: 100%;
    }
}

@media (max-width: 991.98px) {
    #laaMainContentWrapper,
    .page-wrapper-for-sidebar {
        margin-left: 0 !important;
        width: 100% !important;
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

<!-- Floating Trigger Button (Top Left - Frosted Glass with Morphing Burger) -->
<button type="button" id="curvedSidebarToggle" class="curved-sidebar-toggle-btn is-active" aria-expanded="true" aria-label="Toggle Sidebar Menu" title="Buka Menu Navigasi">
    <div class="curved-sidebar-burger">
        <span></span>
        <span></span>
        <span></span>
    </div>
</button>

<!-- Backdrop Blur Overlay -->
<div id="curvedSidebarBackdrop" class="curved-sidebar-backdrop"></div>

<!-- Sliding Sidebar Panel (Left Side - 270px Width, Frosted Glass, Matching Screenshot 2) -->
<aside id="curvedSidebarPanel" class="curved-sidebar-panel is-active" aria-label="Sidebar Navigasi Peminjaman Barang">
    <div class="curved-sidebar-inner">
        <!-- Top Section: Header & Nav Links -->
        <div>
            <div class="curved-sidebar-header">
                <p>NAVIGATION</p>
                <div class="curved-header-role-badge">
                    <span><?= htmlspecialchars($activeRoleBadge); ?></span>
                </div>
            </div>
            
            <nav class="curved-sidebar-nav">
                <?php foreach ($barangNavGroups as $group): ?>
                    <div class="curved-nav-category <?= !empty($group['has_divider']) ? 'has-divider' : '' ?>">
                        <?= htmlspecialchars($group['category']); ?>
                    </div>
                    <?php foreach ($group['items'] as $item): ?>
                        <a href="<?= htmlspecialchars($item['href']); ?>" class="curved-nav-item <?= !empty($item['is_active']) ? 'is-current' : ''; ?>">
                            <div class="curved-nav-content">
                                <div class="curved-nav-3d-wrap">
                                    <img src="<?= base_url($item['icon_3d']); ?>" alt="" class="curved-nav-3d-img" loading="lazy" />
                                </div>
                                <div class="curved-nav-text">
                                    <span class="curved-nav-heading"><?= htmlspecialchars($item['heading']); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Bottom Section: User Profile Summary Card & Footer -->
        <div>
            <?php if ($isLoggedIn): ?>
            <!-- User Profile Summary Card (Matching Screenshot 2) -->
            <div class="curved-sidebar-user-card">
                <div class="curved-sidebar-user-avatar">
                    <?= strtoupper(substr($session_name ?: ($session_username ?: 'U'), 0, 1)) ?>
                </div>
                <div class="curved-sidebar-user-info">
                    <div class="curved-sidebar-user-name" title="<?= htmlspecialchars($session_name ?: 'Pengguna') ?>">
                        <?= htmlspecialchars($session_name ?: ($session_username ?: 'Pengguna')) ?>
                    </div>
                    <div class="curved-sidebar-user-role">
                        <?= htmlspecialchars($session_nidn_nim ?: '') ?><?= !empty($session_nidn_nim) ? ' • ' : '' ?><?= htmlspecialchars($activeRoleBadge) ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <a href="<?= site_url('login'); ?>" class="btn-sidebar-login" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 10px 14px; background: linear-gradient(135deg, #ea580c, #c2410c); color: #fff; border-radius: 12px; font-weight: 700; font-size: 0.82rem; text-decoration: none; margin-bottom: 10px; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25); transition: all 0.2s ease;">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Masuk ke Akun</span>
            </a>
            <?php endif; ?>

            <div class="curved-sidebar-footer">
                <div class="curved-sidebar-footer-brand">
                    <i class="fa-solid fa-graduation-cap text-orange-500"></i>
                    <span>Portal Tugas Akhir • IFIK</span>
                </div>
                <span class="curved-sidebar-footer-version">v2.0</span>
            </div>
        </div>
    </div>

    <!-- Morphing Bezier Curve SVG (Right Edge of Left Sidebar) -->
    <svg id="curvedSidebarSvg" class="curved-sidebar-svg">
        <path id="curvedSidebarPath" />
    </svg>
</aside>

<!-- Instant Pre-init to ensure desktop is open by default -->
<script>
(function() {
    try {
        localStorage.removeItem('ifik_curved_sidebar_state');
        var isDesktop = (window.matchMedia && window.matchMedia('(min-width: 1024px)').matches) || window.innerWidth >= 992;
        if (!isDesktop) {
            document.body.classList.add('curved-sidebar-desktop-collapsed');
            document.body.classList.remove('curved-sidebar-desktop-open');
            var btn = document.getElementById('curvedSidebarToggle');
            if (btn) {
                btn.classList.remove('is-active');
                btn.setAttribute('aria-expanded', 'false');
            }
            var panel = document.getElementById('curvedSidebarPanel');
            if (panel) {
                panel.classList.remove('is-active');
            }
        }
    } catch(e) {}
})();
</script>

<!-- Curved Sidebar Core Script -->
<script src="<?= base_url('assets/js/curved_sidebar.js?v=' . time()); ?>"></script>
