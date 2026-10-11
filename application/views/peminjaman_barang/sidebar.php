<?php
/**
 * Dedicated Sidebar Component for Modul Peminjaman Barang & Alat
 * Path: application/views/peminjaman_barang/sidebar.php
 * Unified with standard Curved Animated Sidebar (components/curved_sidebar.php)
 */
?>

<!-- Font Awesome (Ensure available across all peminjaman_barang views) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* =========================================================
   POLISHED HEADER & ACTION BUTTONS (MATCHING IFIK STANDARD)
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

<!-- Load Unified Curved Sidebar Component -->
<?php $this->load->view('components/curved_sidebar'); ?>
