<style>
    /* Global Universal Cursor Override */
    @media (pointer: fine) {
        *, *::before, *::after, html, body, a, button, input, select, textarea, label, summary, model-viewer, model-viewer::part(default-canvas), [role="button"], [onclick] {
            cursor: none !important;
        }
    }

    /* Sidebar Container (Tersembunyi secara default) */
    .dashboard-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 260px;
        height: 100vh;
        padding: 20px;
        z-index: 9;
        display: flex;
        flex-direction: column;
        background: rgba(251, 247, 241, 0.6);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border-right: 1px solid rgba(234, 88, 12, 0.15);
        box-shadow: 10px 0 30px rgba(0, 0, 0, 0.05);
        transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        
        /* Geser ke kiri sejauh 245px, sisakan 15px sebagai area pancingan (trigger) hover */
        transform: translateX(-245px);
    }

    /* Saat kursor diarahkan ke area 15px tersebut, sidebar akan keluar */
    .dashboard-sidebar:hover {
        transform: translateX(0);
    }

    /* Indikator visual (Garis kecil) agar user tahu ada menu di sebelah kiri */
    .dashboard-sidebar::after {
        content: '';
        position: absolute;
        top: 50%;
        right: 5px; /* Nempel di ujung kanan sidebar yang masih terlihat */
        transform: translateY(-50%);
        width: 4px;
        height: 50px;
        background: rgba(234, 88, 12, 0.5);
        border-radius: 10px;
        transition: opacity 0.3s;
    }

    /* Sembunyikan indikator saat sidebar terbuka penuh */
    .dashboard-sidebar:hover::after {
        opacity: 0;
    }

    .nav-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
        list-style: none;
        margin: 0;
        padding: 0;
        margin-top: 180px; /* Menyediakan ruang kosong di atas untuk tempat mendaratnya 3D Logo di Sesi 3 */
    }

    .nav-item {
        position: relative;
    }

    .nav-link {
        color: var(--text-color);
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-decoration: none;
        padding: 15px 20px;
        display: block;
        border-radius: 12px;
        transition: all 0.3s;
    }

    .nav-link:hover {
        color: #fff;
        background: #ea580c;
        box-shadow: 0 5px 15px rgba(234, 88, 12, 0.3);
    }

    /* Dropdown Menjadi Side-Popup (Muncul di Kanan Sidebar) */
    .nav-dropdown {
        position: absolute;
        top: 0;
        left: 100%; /* Muncul tepat di sebelah kanan item menu */
        margin-left: 15px;
        background: #ffffff;
        border: 1px solid rgba(234, 88, 12, 0.2);
        border-radius: 16px;
        min-width: 220px;
        box-shadow: 15px 15px 35px rgba(234, 88, 12, 0.1);
        opacity: 0;
        visibility: hidden;
        transform: translateX(-15px);
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        padding: 10px 0;
        z-index: 1000;
        pointer-events: none;
    }

    .nav-item:hover .nav-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
        pointer-events: auto;
    }

    /* Dropdown Items */
    .nav-dropdown a {
        display: block;
        padding: 12px 20px;
        color: #475569;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        transition: background 0.2s, color 0.2s;
        text-transform: capitalize;
    }

    .nav-dropdown a:hover {
        background: rgba(234, 88, 12, 0.05);
        color: #ea580c;
        padding-left: 25px; /* Animasi geser sedikit ke kanan saat dihover */
    }
</style>

<nav class="dashboard-sidebar">
    <ul class="nav-list">
        <!-- 1. Layanan LAB -->
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link">Layanan LAB</a>
            <div class="nav-dropdown">
                <a href="<?= site_url('welcome') ?>">Peminjaman Ruang</a>
                <a href="<?= site_url('welcome') ?>">Peminjaman Barang</a>
                <a href="<?= site_url('welcome') ?>">Pengajuan</a>
                <a href="<?= site_url('welcome') ?>">Gallery Lab</a>
            </div>
        </li>

        <!-- 2. Layanan LAA -->
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link">Layanan LAA</a>
            <div class="nav-dropdown">
                <a href="<?= site_url('welcome') ?>">Tugas Akhir Online</a>
                <a href="<?= site_url('welcome') ?>">Kerja Praktek</a>
                <a href="<?= site_url('welcome') ?>">Perwalian</a>
            </div>
        </li>

        <!-- 3. Center of Excelent -->
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link">Center of Excelent</a>
            <div class="nav-dropdown">
                <a href="<?= site_url('welcome') ?>">Mikro Credential</a>
                <a href="<?= site_url('welcome') ?>">Sertifikasi</a>
                <a href="<?= site_url('welcome') ?>">Pelatihan</a>
                <a href="<?= site_url('welcome') ?>">Workshop</a>
            </div>
        </li>

        <!-- 4. Ticketing -->
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link">Ticketing</a>
            <div class="nav-dropdown">
                <a href="<?= site_url('welcome') ?>">Research Group</a>
            </div>
        </li>

        <!-- 5. Galeri Karya FIK -->
        <li class="nav-item">
            <a href="<?= site_url('welcome') ?>" class="nav-link">Galeri Karya FIK</a>
        </li>

        <!-- 6. Admin Panel -->
        <li class="nav-item">
            <a href="<?= site_url('import-email') ?>" class="nav-link" style="color: #ea580c; font-weight: 800;">Admin Panel</a>
            <div class="nav-dropdown">
                <a href="<?= site_url('import-email') ?>">📧 Import Email & Token</a>
            </div>
        </li>
    </ul>
</nav>
