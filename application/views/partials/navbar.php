<style>
    /* Topbar Container - Normal Navbar */
    .dashboard-topbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 70px;
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center; /* Menu ada di tengah */
        background: rgba(251, 247, 241, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(234, 88, 12, 0.15);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .nav-list {
        display: flex;
        flex-direction: row; /* Menu menyamping (horizontal) */
        gap: 15px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-item {
        position: relative;
    }

    .nav-link {
        color: var(--text-color, #1e293b); /* Warna gelap teks aslinya */
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-decoration: none;
        padding: 10px 18px;
        display: block;
        border-radius: 999px; /* Bentuk pill saat dihover */
        transition: all 0.3s;
    }

    .nav-link:hover {
        color: #fff;
        background: #ea580c;
        box-shadow: 0 5px 15px rgba(234, 88, 12, 0.3);
    }

    /* Tombol Dashboard Khusus (Kotak Kecil + Hover Overshoot) */
    .dashboard-btn {
        display: inline-flex !important;
        align-items: center;
        gap: 8px;
        background: #ea580c !important;
        color: #ffffff !important;
        padding: 7px 16px 7px 9px !important;
        border-radius: 999px;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), 
                    box-shadow 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), 
                    background 0.3s ease;
        box-shadow: 0 4px 14px rgba(234, 88, 12, 0.3);
        transform-origin: center;
    }

    .dashboard-btn .btn-box {
        width: 24px;
        height: 24px;
        background: #ffffff;
        color: #ea580c;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    }

    /* Hover Rotate + Scale dengan Easing Overshoot */
    .dashboard-btn:hover {
        background: #c2410c !important;
        transform: scale(1.1) rotate(-4deg);
        box-shadow: 0 8px 22px rgba(234, 88, 12, 0.5);
    }

    .dashboard-btn:hover .btn-box {
        transform: scale(1.22) rotate(18deg);
    }

    /* Dropdown Menjadi Pop-down (Muncul di Bawah) */
    .nav-dropdown {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(15px);
        margin-top: 10px;
        background: #ffffff;
        border: 1px solid rgba(234, 88, 12, 0.2);
        border-radius: 16px;
        min-width: 220px;
        box-shadow: 0 15px 35px rgba(234, 88, 12, 0.1);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        padding: 10px 0;
        z-index: 1000;
        pointer-events: none;
    }

    /* Membuat jembatan invisible agar kursor tidak loss saat turun ke dropdown */
    .nav-dropdown::before {
        content: '';
        position: absolute;
        top: -15px;
        left: 0;
        width: 100%;
        height: 15px;
    }

    .nav-item:hover .nav-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
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

<nav class="dashboard-topbar">
    <ul class="nav-list">
        <!-- 0. Tombol Dashboard (Kotak Kecil + Hover Overshoot) -->
        <li class="nav-item">
            <a href="<?= base_url() ?>" class="nav-link dashboard-btn" onclick="scrollToDashboard(event)">
                <span class="btn-box">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                    </svg>
                </span>
                <span>Dashboard</span>
            </a>
        </li>

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

<script>
    function scrollToDashboard(e) {
        const container = document.querySelector('.dashboard-container');
        if (container) {
            e.preventDefault();
            container.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            if (typeof window.goToSlide === 'function') {
                window.goToSlide(0);
            }
        }
    }
</script>
