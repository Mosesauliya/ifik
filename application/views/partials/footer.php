<style>
    /* Premium Ultra-Modern Footer System (Previous Styling System) */
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
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        gap: 50px;
        align-items: center;
    }

    /* Left Column - Contact Us & Address */
    .footer-col-contact h2 {
        color: #ffffff;
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 22px;
        letter-spacing: -0.5px;
    }

    .footer-social-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 20px;
    }

    .social-item {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        color: #cbd5e1;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 10px 16px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .social-item:hover {
        background: rgba(234, 88, 12, 0.1);
        border-color: rgba(234, 88, 12, 0.35);
        color: #ffffff;
        transform: translateX(8px) translateY(-2px);
        box-shadow: 0 8px 20px rgba(234, 88, 12, 0.2);
    }

    .social-icon-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        flex-shrink: 0;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .social-item:hover .social-icon-circle {
        background: #ea580c;
        color: #ffffff;
        border-color: #ea580c;
        box-shadow: 0 4px 15px rgba(234, 88, 12, 0.4);
        transform: scale(1.1);
    }

    .social-icon-circle svg {
        width: 17px;
        height: 17px;
        fill: currentColor;
    }

    .footer-address {
        margin-top: 14px;
        padding: 16px 20px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .footer-address:hover {
        background: rgba(234, 88, 12, 0.06);
        border-color: rgba(234, 88, 12, 0.3);
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(234, 88, 12, 0.15);
    }

    .footer-address h3 {
        color: #ffffff;
        font-size: 1.1rem;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .footer-address p {
        color: #94a3b8;
        font-size: 0.92rem;
        line-height: 1.65;
        max-width: 480px;
        margin: 0;
    }

    /* Right Column - Embedded Google Maps */
    .footer-map-container {
        position: relative;
        width: 100%;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.02);
        line-height: 0;
        transition: all 0.3s ease;
    }

    .footer-map-container:hover {
        border-color: rgba(234, 88, 12, 0.3);
        box-shadow: 0 20px 40px rgba(234, 88, 12, 0.15);
    }

    .footer-map-container iframe {
        width: 100%;
        height: 290px;
        border: 0;
        display: block;
        pointer-events: none !important;
    }

    /* Google Maps Info Overlay Card */
    .map-info-card {
        position: absolute;
        top: 14px;
        left: 14px;
        z-index: 10;
        background: #ffffff;
        border-radius: 12px;
        padding: 14px 16px;
        max-width: 310px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        text-decoration: none;
        color: #1e293b;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        display: flex;
        flex-direction: column;
        gap: 6px;
        line-height: 1.4;
    }

    .map-info-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 14px 35px rgba(234, 88, 12, 0.3);
        border: 1px solid rgba(234, 88, 12, 0.3);
    }

    .map-info-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .map-info-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
        line-height: 1.3;
    }

    .map-info-address {
        font-size: 0.76rem;
        color: #64748b;
        margin: 0;
        line-height: 1.4;
    }

    .map-info-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .map-action-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease, background-color 0.2s ease;
    }

    .map-action-btn.btn-open {
        background: #f1f5f9;
        color: #2563eb;
    }

    .map-action-btn.btn-open:hover {
        background: #e2e8f0;
        transform: scale(1.1);
    }

    .map-action-btn.btn-open svg {
        width: 16px;
        height: 16px;
    }

    .map-action-btn.btn-directions {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.4);
    }

    .map-action-btn.btn-directions:hover {
        background: #1d4ed8;
        transform: scale(1.1);
    }

    .map-action-btn.btn-directions svg {
        width: 18px;
        height: 18px;
    }

    .map-info-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 0.8rem;
        color: #475569;
        font-weight: 600;
        margin-top: 2px;
    }

    .rating-score {
        color: #1e293b;
        font-weight: 700;
    }

    .rating-star {
        color: #eab308;
        font-size: 0.85rem;
    }

    .rating-count {
        color: #2563eb;
        text-decoration: underline;
    }

    .rating-info-icon {
        width: 13px;
        height: 13px;
        color: #94a3b8;
        margin-left: 2px;
    }

    /* Footer Bottom Section */
    .footer-bottom-wrapper {
        max-width: 1200px;
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
        padding: 8px 18px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .footer-back-top:hover {
        background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
        color: #ffffff;
        border-color: #ea580c;
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(234, 88, 12, 0.45);
    }

    /* Custom Orange Circle Cursor System - Total Override */
    @media (pointer: fine) {
        *,
        *::before,
        *::after,
        html,
        body,
        a,
        button,
        input,
        select,
        textarea,
        label,
        summary,
        iframe,
        model-viewer,
        model-viewer::part(default-canvas),
        [role="button"],
        [onclick] {
            cursor: none !important;
        }
    }

    /* Permanent Hide for Model Viewer Interaction Hand Prompt Graphic */
    model-viewer::part(user-prompt),
    model-viewer::part(prompt),
    model-viewer::part(interaction-prompt),
    model-viewer .slot.user-prompt,
    model-viewer #prompt,
    model-viewer [slot="user-prompt"],
    model-viewer img {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    .custom-cursor-dot {
        position: fixed;
        top: 0;
        left: 0;
        width: 8px;
        height: 8px;
        background-color: #ea580c;
        border-radius: 50%;
        pointer-events: none;
        z-index: 999999;
        transform: translate(-50%, -50%);
        transition: opacity 0.3s ease, transform 0.1s ease;
        box-shadow: 0 0 10px rgba(234, 88, 12, 0.8);
        opacity: 0;
    }

    .custom-cursor-circle {
        position: fixed;
        top: 0;
        left: 0;
        width: 34px;
        height: 34px;
        border: 2px solid rgba(234, 88, 12, 0.7);
        background: rgba(234, 88, 12, 0.08);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        border-radius: 50%;
        pointer-events: none;
        z-index: 999998;
        transform: translate(-50%, -50%);
        transition: width 0.25s cubic-bezier(0.25, 1, 0.5, 1),
                    height 0.25s cubic-bezier(0.25, 1, 0.5, 1),
                    background-color 0.25s ease,
                    border-color 0.25s ease,
                    opacity 0.3s ease;
        box-shadow: 0 0 15px rgba(234, 88, 12, 0.25);
        opacity: 0;
    }

    /* Hover State on Interactive Elements */
    body.cursor-hover .custom-cursor-circle {
        width: 52px;
        height: 52px;
        background: rgba(234, 88, 12, 0.18);
        border-color: #ea580c;
        box-shadow: 0 0 25px rgba(234, 88, 12, 0.5);
    }

    body.cursor-hover .custom-cursor-dot {
        transform: translate(-50%, -50%) scale(1.5);
        background-color: #ffffff;
    }

    /* Active Click State */
    body.cursor-active .custom-cursor-circle {
        width: 26px;
        height: 26px;
        background: rgba(234, 88, 12, 0.35);
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
        <!-- Kolom Kiri: Contact Us, Social Media & Alamat -->
        <div class="footer-col-contact">
            <h2>Contact us</h2>

            <div class="footer-social-list">
                <a href="https://facebook.com" target="_blank" class="social-item">
                    <div class="social-icon-circle">
                        <svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </div>
                    <span>LABFIK</span>
                </a>
                <a href="https://twitter.com" target="_blank" class="social-item">
                    <div class="social-icon-circle">
                        <svg viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.936 9.936 0 0024 4.59z"/></svg>
                    </div>
                    <span>LABFIK</span>
                </a>
                <a href="https://instagram.com" target="_blank" class="social-item">
                    <div class="social-icon-circle">
                        <svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </div>
                    <span>LABFIK</span>
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" class="social-item">
                    <div class="social-icon-circle">
                        <svg viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-1.143 4.174 4.275-1.121z"/></svg>
                    </div>
                    <span>LABFIK</span>
                </a>
            </div>

            <div class="footer-address">
                <h3>Address :</h3>
                <p>Jl. Telekomunikasi Jl. Terusan Buah Batu, Sukapura, Kec. Dayeuhkolot, Kota Bandung, Jawa Barat 40257</p>
            </div>
        </div>

        <!-- Kolom Kanan: Google Maps Embed dengan Card Info Interaktif -->
        <div class="footer-map-container">
            <a href="https://maps.google.com/?q=Telkom+University+Fakultas+Industri+Kreatif" target="_blank" class="map-info-card" title="Buka Telkom University Fakultas Industri Kreatif di Google Maps">
                <div class="map-info-header">
                    <div class="map-info-title-group">
                        <h4 class="map-info-title">Telkom University Fakultas Industri Kreatif</h4>
                        <p class="map-info-address">Jl. Telekomunikasi No.1, Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung, Jawa Barat 40553</p>
                    </div>
                    <div class="map-info-actions">
                        <span class="map-action-btn btn-open" title="Buka di Maps">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        </span>
                        <span class="map-action-btn btn-directions" title="Petunjuk Arah">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22.43 10.57L13.43 1.57C12.65.79 11.35.79 10.57 1.57L1.57 10.57C.79 11.35.79 12.65 1.57 13.43L10.57 22.43C11.35 23.21 12.65 23.21 13.43 22.43L22.43 13.43C23.21 12.65 23.21 11.35 22.43 10.57ZM14.5 14.5V12H10.5V15H9V11C9 10.45 9.45 10 10 10H14.5V7.5L18 11L14.5 14.5Z"/></svg>
                        </span>
                    </div>
                </div>
                <div class="map-info-rating">
                    <span class="rating-score">4.6</span>
                    <span class="rating-star">★</span>
                    <span class="rating-count">(99)</span>
                    <svg class="rating-info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                </div>
            </a>
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.301384074211!2d107.63211517587637!3d-6.973715893026955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e9ad2c8c67c5%3A0xf6031fa15c26e108!2sTelkom%20University%20Fakultas%20Industri%20Kreatif!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                width="100%" 
                height="290" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>

    <!-- Divider & Area Bawah -->
    <div class="footer-bottom-wrapper">
        <div class="footer-copyright">
            &copy; <?= date('Y') ?> Fakultas Industri Kreatif - Telkom University. All rights reserved.
        </div>
        
        <a href="javascript:void(0)" onclick="scrollToTopSection()" class="footer-back-top">
            Kembali ke Atas
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="18 15 12 9 6 15"></polyline>
            </svg>
        </a>
    </div>
</footer>

<!-- Custom Orange Circle Cursor Elements -->
<div class="custom-cursor-dot" id="customCursorDot"></div>
<div class="custom-cursor-circle" id="customCursorCircle"></div>

<script>
    function scrollToTopSection() {
        const dashboardContainer = document.querySelector('.dashboard-container');
        if (dashboardContainer) {
            dashboardContainer.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    // Custom Smooth Circle Cursor Engine
    document.addEventListener('DOMContentLoaded', () => {
        const dot = document.getElementById('customCursorDot');
        const circle = document.getElementById('customCursorCircle');
        if (!dot || !circle) return;

        let mouseX = -100, mouseY = -100;
        let circleX = -100, circleY = -100;
        let isVisible = false;

        window.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;

            if (!isVisible) {
                dot.style.opacity = '1';
                circle.style.opacity = '1';
                isVisible = true;
            }

            dot.style.transform = `translate(${mouseX}px, ${mouseY}px) translate(-50%, -50%)`;
        });

        // Ensure custom cursor stays visible during screenshots / window blur
        window.addEventListener('blur', () => {
            if (mouseX > 0 && mouseY > 0) {
                dot.style.opacity = '1';
                circle.style.opacity = '1';
                isVisible = true;
            }
        });

        function renderCursor() {
            circleX += (mouseX - circleX) * 0.18;
            circleY += (mouseY - circleY) * 0.18;

            circle.style.transform = `translate(${circleX}px, ${circleY}px) translate(-50%, -50%)`;
            requestAnimationFrame(renderCursor);
        }
        requestAnimationFrame(renderCursor);

        const interactiveSelector = 'a, button, input, select, textarea, label, [role="button"], [onclick], model-viewer, .lab-card, .btn-detail-lab, .slide-line, .detail-line, .social-btn, .lab-nav-btn, .nav-link, .nav-item, img, svg';

        document.addEventListener('mouseover', (e) => {
            if (e.target.closest(interactiveSelector)) {
                document.body.classList.add('cursor-hover');
            }
        });

        document.addEventListener('mouseout', (e) => {
            if (e.target.closest(interactiveSelector)) {
                document.body.classList.remove('cursor-hover');
            }
        });

        // Kill grab/grabbing cursor and hand interaction prompt graphic on model-viewer elements permanently
        const killModelViewerCursor = () => {
            document.querySelectorAll('model-viewer').forEach(viewer => {
                viewer.removeAttribute('interaction-prompt');
                viewer.setAttribute('interaction-prompt', 'none');
                viewer.interactionPrompt = 'none';

                if (viewer.shadowRoot) {
                    // Inject style tag inside Shadow Root to override browser cursor boundary
                    if (!viewer.shadowRoot.querySelector('#force-no-cursor-style')) {
                        const styleEl = document.createElement('style');
                        styleEl.id = 'force-no-cursor-style';
                        styleEl.textContent = `
                            *, *::before, *::after, canvas, .container, #prompt, .user-prompt, [slot="user-prompt"], img, div {
                                cursor: none !important;
                            }
                            #prompt, .user-prompt, [slot="user-prompt"] {
                                display: none !important;
                                opacity: 0 !important;
                                visibility: hidden !important;
                                pointer-events: none !important;
                            }
                        `;
                        viewer.shadowRoot.appendChild(styleEl);
                    }

                    const canvas = viewer.shadowRoot.querySelector('canvas');
                    if (canvas) canvas.style.setProperty('cursor', 'none', 'important');

                    const container = viewer.shadowRoot.querySelector('.container');
                    if (container) container.style.setProperty('cursor', 'none', 'important');

                    // Kill hand interaction prompt graphic elements
                    const promptElems = viewer.shadowRoot.querySelectorAll('#prompt, .prompt, .user-prompt, [slot="user-prompt"], img');
                    promptElems.forEach(el => {
                        el.style.setProperty('display', 'none', 'important');
                        el.style.setProperty('opacity', '0', 'important');
                        el.style.setProperty('visibility', 'hidden', 'important');
                        el.style.setProperty('pointer-events', 'none', 'important');
                    });
                }

                viewer.style.setProperty('cursor', 'none', 'important');

                ['pointerdown', 'pointermove', 'pointerup', 'dragstart', 'camera-change', 'mousedown', 'mousemove', 'mouseup', 'touchmove', 'touchstart', 'mouseenter', 'mouseover'].forEach(evt => {
                    viewer.addEventListener(evt, () => {
                        viewer.style.setProperty('cursor', 'none', 'important');
                        if (viewer.shadowRoot) {
                            const c = viewer.shadowRoot.querySelector('canvas');
                            if (c) c.style.setProperty('cursor', 'none', 'important');
                        }
                    }, { passive: true });
                });
            });
        };

        killModelViewerCursor();
        setInterval(killModelViewerCursor, 250);

        document.addEventListener('mousedown', () => {
            document.body.classList.add('cursor-active');
        });

        document.addEventListener('mouseup', () => {
            document.body.classList.remove('cursor-active');
        });
    });
</script>
