<style>
    /* Premium Ultra-Modern Footer System */
    .site-footer {
        position: relative;
        width: 100%;
        flex-shrink: 0;
        background-color: #090d16; /* Deep Midnight Slate */
        background-image: radial-gradient(circle at 50% 0%, rgba(234, 88, 12, 0.12) 0%, transparent 65%);
        color: #cbd5e1;
        padding: 50px 60px 25px 60px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        box-shadow: 0 -20px 40px rgba(0, 0, 0, 0.25);
        z-index: 10;
        margin-top: auto;
        overflow: hidden;
    }

    /* Top Glowing Accent Line */
    .footer-top-accent {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, rgba(234, 88, 12, 0) 0%, #ea580c 35%, #f86b1d 65%, rgba(234, 88, 12, 0) 100%);
    }

    .footer-content {
        max-width: 1300px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 2.2fr 1.3fr 1.8fr;
        gap: 50px;
        align-items: start;
    }

    /* Column 1 - Brand */
    .footer-brand-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 14px;
        border-radius: 20px;
        background: rgba(234, 88, 12, 0.12);
        border: 1px solid rgba(234, 88, 12, 0.3);
        color: #f97316;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 16px;
    }

    .footer-brand-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ea580c;
        box-shadow: 0 0 8px #ea580c;
    }

    .footer-col h2 {
        color: #ffffff;
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }

    .footer-col p.brand-desc {
        color: #94a3b8;
        font-size: 0.9rem;
        line-height: 1.7;
        max-width: 420px;
        margin-bottom: 24px;
    }

    /* Social Buttons */
    .footer-socials {
        display: flex;
        gap: 12px;
    }

    .social-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .social-btn svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }

    .social-btn:hover {
        background: #ea580c;
        color: #ffffff;
        border-color: #ea580c;
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(234, 88, 12, 0.35);
    }

    /* Section Titles */
    .footer-col-title {
        color: #f87171;
        color: #ea580c;
        font-size: 0.95rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 22px;
        position: relative;
        display: inline-block;
    }

    .footer-col-title::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 0;
        width: 28px;
        height: 2px;
        background: #ea580c;
        border-radius: 2px;
    }

    /* Column 2 - Links */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .footer-links li a {
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.92rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.25s ease;
    }

    .footer-links li a .link-dash {
        width: 8px;
        height: 2px;
        background: rgba(234, 88, 12, 0.4);
        border-radius: 2px;
        transition: all 0.25s ease;
    }

    .footer-links li a:hover {
        color: #f86b1d;
        transform: translateX(6px);
    }

    .footer-links li a:hover .link-dash {
        width: 16px;
        background: #ea580c;
        box-shadow: 0 0 8px rgba(234, 88, 12, 0.6);
    }

    /* Column 3 - Contact Cards */
    .footer-contact-cards {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .contact-card-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        transition: all 0.3s ease;
    }

    .contact-card-item:hover {
        background: rgba(234, 88, 12, 0.06);
        border-color: rgba(234, 88, 12, 0.25);
        transform: translateY(-2px);
    }

    .contact-icon-wrapper {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(234, 88, 12, 0.12);
        border: 1px solid rgba(234, 88, 12, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .contact-icon-wrapper svg {
        width: 18px;
        height: 18px;
        fill: #ea580c;
    }

    .contact-text {
        display: flex;
        flex-direction: column;
    }

    .contact-text label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #ea580c;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .contact-text span {
        font-size: 0.88rem;
        color: #cbd5e1;
        font-weight: 500;
        line-height: 1.4;
    }

    /* Footer Bottom Section */
    .footer-bottom-wrapper {
        max-width: 1300px;
        margin: 45px auto 0 auto;
        padding-top: 25px;
        border-top: 1px solid rgba(255, 255, 255, 0.07);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .footer-copyright {
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .footer-back-top {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #ea580c;
        background: rgba(234, 88, 12, 0.08);
        border: 1px solid rgba(234, 88, 12, 0.2);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .footer-back-top:hover {
        background: #ea580c;
        color: #ffffff;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(234, 88, 12, 0.3);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .site-footer {
            padding: 45px 30px 25px 30px;
        }
        .footer-content {
            grid-template-columns: 1fr;
            gap: 35px;
        }
        .footer-bottom-wrapper {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<footer class="site-footer">
    <div class="footer-top-accent"></div>

    <div class="footer-content">
        <!-- Kolom 1: Brand & Profil -->
        <div class="footer-col">
            <div class="footer-brand-badge">Telkom University</div>
            <h2>Fakultas Industri Kreatif</h2>
            <p class="brand-desc">Pusat unggulan pendidikan industri kreatif yang menghasilkan lulusan berkarakter, inovatif, dan siap bersaing di tingkat global.</p>
            
            <div class="footer-socials">
                <a href="https://instagram.com" target="_blank" class="social-btn" title="Instagram">
                    <svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>
                <a href="https://youtube.com" target="_blank" class="social-btn" title="YouTube">
                    <svg viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </a>
                <a href="https://linkedin.com" target="_blank" class="social-btn" title="LinkedIn">
                    <svg viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
            </div>
        </div>

        <!-- Kolom 2: Tautan Cepat (Menyesuaikan Sidebar Navbar) -->
        <div class="footer-col">
            <span class="footer-col-title">Tautan Cepat</span>
            <ul class="footer-links">
                <li><a href="<?= site_url('welcome') ?>"><span class="link-dash"></span> Layanan LAB</a></li>
                <li><a href="<?= site_url('welcome') ?>"><span class="link-dash"></span> Layanan LAA</a></li>
                <li><a href="<?= site_url('welcome') ?>"><span class="link-dash"></span> Center of Excelent</a></li>
                <li><a href="<?= site_url('welcome') ?>"><span class="link-dash"></span> Ticketing</a></li>
                <li><a href="<?= site_url('welcome') ?>"><span class="link-dash"></span> Galeri Karya FIK</a></li>
                <li><a href="<?= site_url('import-email') ?>"><span class="link-dash"></span> Admin Panel</a></li>
            </ul>
        </div>

        <!-- Kolom 3: Kontak Kami -->
        <div class="footer-col">
            <span class="footer-col-title">Kontak Kami</span>
            <div class="footer-contact-cards">
                
                <div class="contact-card-item">
                    <div class="contact-icon-wrapper">
                        <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    </div>
                    <div class="contact-text">
                        <label>Alamat Kampus</label>
                        <span>Gedung Sebatik (FIK), Telkom University, Bandung, Jawa Barat 40287</span>
                    </div>
                </div>

                <div class="contact-card-item">
                    <div class="contact-icon-wrapper">
                        <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                    </div>
                    <div class="contact-text">
                        <label>Email Resmi</label>
                        <span>fik@telkomuniversity.ac.id</span>
                    </div>
                </div>

                <div class="contact-card-item">
                    <div class="contact-icon-wrapper">
                        <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                    </div>
                    <div class="contact-text">
                        <label>Layanan Telepon</label>
                        <span>(022) 756 5923</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Divider & Bottom Area -->
    <div class="footer-bottom-wrapper">
        <div class="footer-copyright">
            &copy; <?= date('Y') ?> Fakultas Industri Kreatif - Telkom University. All rights reserved.
        </div>
        
        <!-- Kembali ke Atas Smooth Scroll -->
        <a href="javascript:void(0)" onclick="scrollToTopSection()" class="footer-back-top">
            Kembali ke Atas
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
        </a>
    </div>
</footer>

<script>
    function scrollToTopSection() {
        const dashboardContainer = document.querySelector('.dashboard-container');
        if (dashboardContainer) {
            dashboardContainer.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
</script>
