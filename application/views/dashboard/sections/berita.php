<style>
    /* ===== SESI 3: BERITA ===== */
    #section-contact {
        background-color: #fbf7f1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        /* overflow dibiarkan visible agar kartu tidak terpotong */
        overflow: visible;
        position: relative;
        padding: 60px 0 48px;
    }

    .news-header {
        margin-bottom: 20px;
        text-align: center;
        z-index: 2;
    }

    .news-header h1 {
        font-size: 3rem;
        color: #1e293b;
        font-weight: 900;
        margin-bottom: 10px;
    }
    
    .news-header p {
        color: #64748b;
        font-size: 1.1rem;
    }

    /* ===== WRAPPER UTAMA BERITA (Fan + Kontrol) ===== */
    .news-stage {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    /* ===== KONTAINER KARTU ===== */
    .news-fan-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 560px;
        position: relative;
        overflow: visible;
        clip-path: inset(-20px -9999px);
    }

    /* ===== KARTU BERITA ===== */
    .news-card {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 280px;
        height: 400px;
        background: #fff;
        border-radius: 24px;
        box-shadow: -6px 10px 30px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        cursor: pointer;
        transform-origin: bottom center;

        /* CSS Variables */
        --angle-per-card: 12deg;
        --center-index: calc((var(--total) - 1) / 2);
        --offset: calc(var(--index) - var(--center-index));
        --angle: calc(var(--offset) * var(--angle-per-card));

        /* DEFAULT: sudah terbuka/mekar, tidak rapat */
        --spread-x: calc(var(--offset) * 120px);
        --arc-y: calc(var(--offset) * var(--offset) * 7px);

        transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y))) rotate(var(--angle));
        transition: transform 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275),
                    box-shadow 0.45s ease,
                    filter 0.45s ease,
                    opacity 0.45s ease;
        z-index: calc(10 + var(--index));
    }

    /* Saat kartu di-hover: tegak, naik, geser menjauhi tumpukan */
    .news-card:hover {
        --hover-shift-x: calc(var(--offset) * 40px);
        transform: translate(calc(-50% + var(--spread-x) + var(--hover-shift-x)), calc(-50% - 55px)) rotate(0deg) scale(1.06) !important;
        box-shadow: 0 32px 70px rgba(234, 88, 12, 0.35);
        z-index: 100 !important;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease, z-index 0s 0s;
    }

    /* Parting: kartu sesudah yang di-hover dorong ke kanan */
    .news-card:hover ~ .news-card {
        transform: translate(calc(-50% + var(--spread-x) + 150px), calc(-50% + var(--arc-y) + 25px)) rotate(calc(var(--angle) + 8deg)) !important;
    }

    /* Parting: kartu sebelum yang di-hover dorong ke kiri */
    .news-fan-container:has(.news-card:hover) .news-card:not(:hover):not(.news-card:hover ~ .news-card) {
        transform: translate(calc(-50% + var(--spread-x) - 150px), calc(-50% + var(--arc-y) + 25px)) rotate(calc(var(--angle) - 8deg)) !important;
    }

    /* Kartu lain meredup saat salah satu di-hover */
    .news-fan-container:has(.news-card:hover) .news-card:not(:hover) {
        opacity: 0.8;
        filter: blur(1px) brightness(0.85);
    }

    /* ===== ANIMASI KELUAR ke BAWAH (Next) ===== */
    .news-card.anim-exit {
        animation: cardExitDown 0.45s cubic-bezier(0.55, 0, 1, 0.45) forwards !important;
        pointer-events: none;
    }
    @keyframes cardExitDown {
        0%   { opacity: 1; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y))) rotate(var(--angle)) scale(1); }
        100% { opacity: 0; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y) + 200px)) rotate(var(--angle)) scale(0.85); }
    }

    /* ===== ANIMASI MASUK dari BAWAH (Next) ===== */
    .news-card.anim-enter {
        animation: cardEnterUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards !important;
        pointer-events: none;
    }
    @keyframes cardEnterUp {
        0%   { opacity: 0; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y) + 200px)) rotate(var(--angle)) scale(0.85); }
        100% { opacity: 1; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y))) rotate(var(--angle)) scale(1); }
    }

    /* ===== ANIMASI KELUAR ke ATAS (Prev) ===== */
    .news-card.anim-exit-up {
        animation: cardExitUp 0.45s cubic-bezier(0.55, 0, 1, 0.45) forwards !important;
        pointer-events: none;
    }
    @keyframes cardExitUp {
        0%   { opacity: 1; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y))) rotate(var(--angle)) scale(1); }
        100% { opacity: 0; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y) - 200px)) rotate(var(--angle)) scale(0.85); }
    }

    /* ===== ANIMASI MASUK dari ATAS (Prev) ===== */
    .news-card.anim-enter-down {
        animation: cardEnterDown 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards !important;
        pointer-events: none;
    }
    @keyframes cardEnterDown {
        0%   { opacity: 0; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y) - 200px)) rotate(var(--angle)) scale(0.85); }
        100% { opacity: 1; transform: translate(calc(-50% + var(--spread-x)), calc(-50% + var(--arc-y))) rotate(var(--angle)) scale(1); }
    }

    /* ===== KONTROL: MENEMPEL DI KANAN SECTION ===== */
    .news-controls {
        position: absolute;
        right: 28px;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 12px;
        z-index: 20;
        width: 56px;
    }

    .news-arrow-btn {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 2px solid #ea580c;
        background: transparent;
        color: #ea580c;
        font-size: 1.3rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .news-arrow-btn:hover {
        background: #ea580c;
        color: #fff;
        transform: scale(1.1);
        box-shadow: 0 8px 24px rgba(234, 88, 12, 0.35);
    }

    .news-arrow-btn:disabled {
        border-color: #e2e8f0;
        color: #cbd5e1;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Dots vertikal */
    .news-dots {
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: center;
    }

    .news-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .news-dot.active {
        background: #ea580c;
        height: 24px; /* Memanjang ke bawah (vertikal) */
        border-radius: 4px;
    }

    .news-page-info {
        font-size: 0.85rem;
        color: #94a3b8;
        font-weight: 600;
        text-align: center;
        line-height: 1.3;
    }

    /* ===== ISI KARTU ===== */
    .news-image {
        width: 100%;
        height: 45%;
        background-color: #ddd; 
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .news-image::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; width: 100%; height: 30px;
        background: linear-gradient(to top, #ffffff, transparent);
    }
    
    .news-content {
        padding: 20px 22px;
        height: 55%;
        display: flex;
        flex-direction: column;
    }
    
    .news-date {
        font-size: 0.75rem;
        color: #ea580c;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: 1px;
    }
    
    .news-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 10px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .news-excerpt {
        font-size: 0.88rem;
        color: #64748b;
        line-height: 1.6;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }


    /* ===== RESPONSIVE ===== */
    /* ===== VIEW ALL BUTTON ===== */
    .news-view-all-btn {
        position: absolute;
        bottom: 28px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        border-radius: 50px;
        border: 2px solid #ea580c;
        background: transparent;
        color: #ea580c;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        z-index: 20;
        white-space: nowrap;
    }
    .news-view-all-btn:hover {
        background: #ea580c;
        color: #fff;
        box-shadow: 0 8px 28px rgba(234, 88, 12, 0.35);
        transform: translateX(-50%) translateY(-2px);
    }
    .news-view-all-btn svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    /* ===== MODAL OVERLAY (LIGHT THEME) ===== */
    #newsModal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        background: rgba(251, 247, 241, 0.97); /* Light theme off-white */
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.35s ease;
        overflow-y: auto;
        padding: 48px 40px 60px;
    }
    #newsModal.open {
        opacity: 1;
        pointer-events: all;
    }

    .news-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        max-width: 1300px;
        margin-bottom: 36px;
        flex-shrink: 0;
    }
    .news-modal-header h2 {
        font-size: 2rem;
        font-weight: 900;
        color: #1e293b;
    }
    .news-modal-header span {
        font-size: 0.95rem;
        color: #64748b;
        margin-top: 4px;
    }
    .news-modal-close {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 2px solid rgba(30, 41, 59, 0.15);
        background: #ffffff;
        color: #1e293b;
        font-size: 1.4rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    }
    .news-modal-close:hover {
        background: #ea580c;
        border-color: #ea580c;
        color: #fff;
        transform: rotate(90deg);
    }

    .news-modal-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* Selalu 3 kolom */
        gap: 24px;
        width: 100%;
        max-width: 1300px;
        align-items: start;
    }

    /* ===== MODAL CARD: Circular Corner Notch Design ===== */

    /* === Variant Alternatif: cutout di pojok KANAN BAWAH === */
    .news-modal-card--alt .news-modal-card-body {
        -webkit-mask-image: radial-gradient(
            circle at calc(100% - 36px) calc(100% - 36px),
            transparent var(--cutout-r),
            #000 calc(var(--cutout-r) + 1px)
        );
        mask-image: radial-gradient(
            circle at calc(100% - 36px) calc(100% - 36px),
            transparent var(--cutout-r),
            #000 calc(var(--cutout-r) + 1px)
        );
    }
    /* Button alt: terpasang di pojok kanan bawah */
    .news-modal-card--alt .news-modal-card-btn {
        top: auto;
        bottom: 14px;
        left: auto;
        right: 14px;
        animation-delay: 0.6s;
    }


    @property --cutout-r {
        syntax: '<length>';
        inherits: false;
        initial-value: 46px;
    }

    /* Outer wrapper: fixed full size 330px */
    .news-modal-card {
        position: relative;
        height: 330px;
        cursor: pointer;
        transition: transform 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275),
                    box-shadow 0.35s ease;
        opacity: 0;
        transform: translateY(30px);
        filter: drop-shadow(0 10px 24px rgba(0,0,0,0.4));
    }
    .news-modal-card.visible {
        opacity: 1;
        transform: translateY(0);
    }
    .news-modal-card:hover {
        transform: translateY(-8px);
        filter: drop-shadow(0 22px 40px rgba(234, 88, 12, 0.22));
    }

    /* Inner card body: circular notch mask (tetap ada saat hover) */
    .news-modal-card-body {
        position: absolute;
        inset: 0;
        border-radius: 24px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        background: #0f172a; /* Dark slate card base */

        /* Lingkaran transparan cutout (stay di 46px) */
        --cutout-r: 46px;
        -webkit-mask-image: radial-gradient(
            circle at calc(100% - 36px) 36px,
            transparent var(--cutout-r),
            #000 calc(var(--cutout-r) + 1px)
        );
        mask-image: radial-gradient(
            circle at calc(100% - 36px) 36px,
            transparent var(--cutout-r),
            #000 calc(var(--cutout-r) + 1px)
        );
    }

    .news-modal-card-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 165px;
        overflow: hidden;
        background: #0f172a;
    }
    .news-modal-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
    }
    .news-modal-card:hover .news-modal-card-img {
        transform: scale(1.08);
    }
    .news-modal-card-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 50%, rgba(15, 23, 42, 0.9) 100%);
        pointer-events: none;
    }

    /* Date badge */
    .news-modal-card-badge {
        position: relative;
        z-index: 2;
        color: #34d399;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 0 22px;
        margin-bottom: 6px;
    }

    /* Text content (selalu tampil penuh) */
    .news-modal-card-content {
        position: relative;
        z-index: 2;
        padding: 0 22px 22px;
        width: 100%;
        box-sizing: border-box;
    }
    .news-modal-card-title {
        color: #fff;
        font-size: 1.05rem;
        font-weight: 800;
        line-height: 1.4;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .news-modal-card-excerpt {
        color: #94a3b8;
        font-size: 0.83rem;
        line-height: 1.55;
        margin-top: 10px;
        opacity: 0.9;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Button: selalu visible di area cutout (posisi stay, lingkaran diam) */
    .news-modal-card-btn {
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 20;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2px solid rgba(15, 23, 42, 0.15);
        background: #1e293b;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        outline: none;
        padding: 0;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
        transition: background 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease;
    }
    .news-modal-card--alt .news-modal-card-btn {
        top: auto;
        bottom: 14px;
        left: auto;
        right: 14px;
    }

    /* Animasi denyut halus KHUSUS untuk ikon arrow di dalam (lingkaran luar tidak ikut gerak) */
    .news-modal-card-btn-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        animation: arrowIdlePulse 2s ease-in-out infinite;
    }
    .news-modal-card--alt .news-modal-card-btn-icon {
        animation-delay: 0.6s;
    }

    /* Idle: maju-mundur diagonal ke kanan-atas ↗ */
    @keyframes arrowIdlePulse {
        0%, 100% {
            transform: translate(0, 0);
        }
        50% {
            transform: translate(3px, -3px);
        }
    }

    /* Hover: maju-mundur lurus ke kanan -> */
    @keyframes arrowHoverPulse {
        0%, 100% {
            transform: translate(0, 0);
        }
        50% {
            transform: translate(4px, 0);
        }
    }

    .news-modal-card:hover .news-modal-card-btn-icon {
        animation: arrowHoverPulse 1.2s ease-in-out infinite;
    }

    .news-modal-card-btn svg {
        width: 16px;
        height: 16px;
        transition: transform 0.35s ease;
    }
    .news-modal-card:hover .news-modal-card-btn {
        background: #ea580c;
        border-color: #ea580c;
        box-shadow: 0 4px 16px rgba(234, 88, 12, 0.5);
    }
    .news-modal-card:hover .news-modal-card-btn svg {
        transform: rotate(45deg); /* Berputar dari ↗ ke -> saat hover */
    }

    /* ===== SEARCH & AUTOCOMPLETE (LIGHT THEME) ===== */
    .news-search-wrapper {
        position: relative;
        flex: 1;
        max-width: 450px;
        margin: 0 40px;
        z-index: 50;
    }
    .news-search-input-container {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
    }
    #newsSearchInput {
        width: 100%;
        padding: 12px 40px 12px 44px;
        border-radius: 30px;
        border: 2px solid rgba(234, 88, 12, 0.25);
        background: #ffffff;
        color: #1e293b;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        outline: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
    }
    #newsSearchInput::placeholder {
        color: #94a3b8;
    }
    #newsSearchInput:focus {
        border-color: #ea580c;
        background: #ffffff;
        box-shadow: 0 0 20px rgba(234, 88, 12, 0.2);
    }
    .news-search-icon {
        position: absolute;
        left: 16px;
        font-size: 1.1rem;
        color: #ea580c;
        pointer-events: none;
    }
    .news-search-clear {
        position: absolute;
        right: 16px;
        background: none;
        border: none;
        color: #64748b;
        font-size: 1.3rem;
        cursor: pointer;
        outline: none;
        padding: 0;
        line-height: 1;
        transition: color 0.2s ease;
    }
    .news-search-clear:hover {
        color: #ea580c;
    }
    
    .news-autocomplete-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: #ffffff;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(234, 88, 12, 0.15);
        border-radius: 16px;
        max-height: 250px;
        overflow-y: auto;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        display: none;
        scrollbar-width: thin;
        scrollbar-color: rgba(234, 88, 12, 0.3) transparent;
    }
    .news-autocomplete-dropdown.open {
        display: block;
    }
    .news-autocomplete-item {
        padding: 12px 18px;
        color: #334155;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
    }
    .news-autocomplete-item:last-child {
        border-bottom: none;
    }
    .news-autocomplete-item:hover {
        background: rgba(234, 88, 12, 0.08);
        color: #ea580c;
    }
    .news-autocomplete-item strong {
        color: #ea580c;
    }
    .news-autocomplete-no-match {
        padding: 12px 18px;
        color: #64748b;
        font-size: 0.9rem;
        font-style: italic;
        text-align: left;
    }

    @media (max-width: 1200px) {
        .news-header h1 { font-size: 2.2rem; }
        .news-card { width: 270px; height: 390px; }
        .news-title { font-size: 1.05rem; }
    }

    @media (max-width: 900px) {
        .news-header h1 { font-size: 1.8rem; }
        .news-header p { font-size: 0.9rem; }
        .news-card { width: 230px; height: 350px; }
        .news-title { font-size: 1rem; }
        .news-excerpt { font-size: 0.85rem; -webkit-line-clamp: 2; }
        .news-modal-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
        .news-modal-header {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }
        .news-search-wrapper {
            margin: 0;
            max-width: 100%;
        }
        .news-modal-close {
            align-self: flex-end;
            position: absolute;
            top: 24px;
            right: 24px;
        }
    }
</style>

<!-- Sesi 3: Berita dengan Paging Fan -->
<!-- ID dipertahankan sebagai 'section-contact' agar IntersectionObserver di index.php tetap berfungsi -->
<div class="section-wrapper" id="section-contact">
    
    <div class="news-header">
        <h1>Berita &amp; Informasi Terkini</h1>
        <p>Tetap terhubung dengan kabar terbaru dari Fakultas Industri Kreatif</p>
    </div>

    <div class="news-stage">

        <!-- Container Fan Kartu -->
        <div class="news-fan-container" id="newsFanContainer">
            <!-- Kartu akan di-render oleh JavaScript -->
        </div>

        <!-- Kontrol Navigasi Vertikal Kanan -->
        <div class="news-controls">
            <button class="news-arrow-btn" id="newsPrevBtn" title="Halaman Sebelumnya">&#8593;</button>
            <div class="news-dots" id="newsDots"></div>
            <span class="news-page-info" id="newsPageInfo"></span>
            <button class="news-arrow-btn" id="newsNextBtn" title="Halaman Berikutnya">&#8595;</button>
        </div>

        <!-- Tombol View All -->
        <button class="news-view-all-btn" id="newsViewAllBtn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
            </svg>
            Lihat Semua Berita
        </button>

    </div>
</div>

<!-- ===== MODAL VIEW ALL ===== -->
<div id="newsModal">
    <div class="news-modal-header">
        <div>
            <h2>Semua Berita &amp; Informasi</h2>
            <span id="newsModalCount"></span>
        </div>
        <div class="news-search-wrapper">
            <div class="news-search-input-container">
                <input type="text" id="newsSearchInput" placeholder="Cari judul berita..." autocomplete="off">
                <button id="newsSearchClear" class="news-search-clear" style="display:none;">&times;</button>
            </div>
            <div id="newsAutocomplete" class="news-autocomplete-dropdown"></div>
        </div>
        <button class="news-modal-close" id="newsModalClose" title="Tutup">&times;</button>
    </div>
    <div class="news-modal-grid" id="newsModalGrid"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ===== DATA SEMUA BERITA =====
    const allNews = [
        {
            date: '12 Agustus 2026',
            title: 'Pameran Karya Mahasiswa FIK 2026 Sukses Digelar',
            excerpt: 'Ratusan karya inovatif dari mahasiswa dipamerkan dalam ajang tahunan yang dihadiri oleh praktisi industri kreatif terkemuka. Acara ini berlangsung meriah selama tiga hari berturut-turut.',
            image: '<?= base_url('assets/images/background.png') ?>',
            url: '<?= base_url('index.php/news/detail') ?>'
        },
        {
            date: '05 Agustus 2026',
            title: 'Workshop Desain Interaktif Bersama Pakar UI/UX',
            excerpt: 'Mahasiswa diajak untuk mendalami tren UI/UX dan interaksi 3D web modern dalam workshop intensif selama dua hari bersama narasumber berpengalaman dari startup ternama.',
            image: '<?= base_url('assets/images/ifik_portal_3d_render.jpg') ?>'
        },
        {
            date: '28 Juli 2026',
            title: 'Peluncuran Sistem Layanan Terpadu IFIK Versi Baru',
            excerpt: 'Sistem IFIK kini hadir dengan wajah baru yang lebih premium, responsif, dan interaktif menggunakan teknologi WebGL untuk memudahkan seluruh civitas akademika.',
            image: '<?= base_url('assets/images/ik_3d_illustration.jpg') ?>'
        },
        {
            date: '15 Juli 2026',
            title: 'Prestasi Gemilang Tim Riset FIK di Tingkat Nasional',
            excerpt: 'Penelitian kolaboratif dosen dan mahasiswa tentang pemanfaatan AI dalam desain komunikasi visual berhasil memenangkan hibah penelitian bergengsi tahun ini.',
            image: '<?= base_url('assets/images/BackgroundTrain.jpg') ?>'
        },
        {
            date: '02 Juli 2026',
            title: 'Kunjungan Studi Industri Kreatif ke Studio Animasi',
            excerpt: 'Mahasiswa semester akhir berkesempatan melihat langsung alur kerja produksi animasi 3D kelas dunia dan berdiskusi dengan para profesional di bidang tersebut.',
            image: '<?= base_url('assets/images/background.png') ?>'
        },
        {
            date: '25 Juni 2026',
            title: 'Seminar Nasional Industri Kreatif dan Ekonomi Digital',
            excerpt: 'Para pakar dari berbagai universitas berkumpul membahas peluang dan tantangan industri kreatif di era transformasi digital yang terus berkembang pesat.',
            image: '<?= base_url('assets/images/ik_3d_illustration.jpg') ?>'
        },
        {
            date: '18 Juni 2026',
            title: 'Mahasiswa FIK Raih Juara 1 Kompetisi Desain Asia',
            excerpt: 'Tim mahasiswa Desain Komunikasi Visual FIK berhasil mengharumkan nama universitas di ajang kompetisi desain internasional bertaraf Asia Tenggara.',
            image: '<?= base_url('assets/images/ifik_portal_3d_render.jpg') ?>'
        },
        {
            date: '10 Juni 2026',
            title: 'Kerja Sama FIK dengan Perusahaan Game Terkemuka',
            excerpt: 'Perjanjian kerja sama ditandatangani antara FIK dan studio game ternama untuk program magang, penelitian bersama, dan pengembangan kurikulum berbasis industri.',
            image: '<?= base_url('assets/images/background.png') ?>'
        },
        {
            date: '03 Juni 2026',
            title: 'Dosen FIK Terima Penghargaan Riset Inovatif 2026',
            excerpt: 'Dua dosen dari Program Studi Animasi berhasil meraih penghargaan riset inovatif dari Kementerian Pendidikan atas penelitian mereka di bidang motion capture dan AI generatif.',
            image: '<?= base_url('assets/images/ik_3d_illustration.jpg') ?>'
        },
        {
            date: '27 Mei 2026',
            title: 'Pembukaan Laboratorium Realitas Virtual FIK',
            excerpt: 'Fasilitas lab VR terbaru resmi dibuka, dilengkapi dengan 20 unit headset generasi terbaru dan ruang motion capture khusus untuk keperluan penelitian dan pembelajaran.',
            image: '<?= base_url('assets/images/logo-dummy.webp') ?>',
            imageFit: 'contain',
            imageBg: '#fff7ed'
        },
        {
            date: '20 Mei 2026',
            title: 'Film Pendek Mahasiswa FIK Tayang di Festival Internasional',
            excerpt: 'Karya film pendek berjudul "Ruang Imaji" yang diproduksi oleh mahasiswa Sinematografi FIK berhasil masuk seleksi dan ditayangkan di festival film internasional bergengsi.',
            image: '<?= base_url('assets/images/ifik_portal_3d_render.jpg') ?>'
        },
        {
            date: '13 Mei 2026',
            title: 'FIK Buka Program Sertifikasi Desain untuk Umum',
            excerpt: 'Masyarakat umum kini bisa mengikuti program sertifikasi desain grafis dan UI/UX yang diselenggarakan oleh FIK bekerjasama dengan lembaga internasional terpercaya.',
            image: '<?= base_url('assets/images/background.png') ?>'
        },
        {
            date: '05 Mei 2026',
            title: 'Kolaborasi Lintas Prodi Hasilkan Karya Multimedia Epik',
            excerpt: 'Proyek kolaborasi lintas program studi antara Animasi, DKV, dan Musik menghasilkan karya multimedia interaktif yang memukau di pameran akhir semester genap.',
            image: '<?= base_url('assets/images/ik_3d_illustration.jpg') ?>'
        },
        {
            date: '28 April 2026',
            title: 'Beasiswa Penuh untuk Mahasiswa Berprestasi FIK',
            excerpt: 'Sebanyak 30 mahasiswa berprestasi FIK menerima beasiswa penuh dari mitra industri untuk melanjutkan studi dan mengikuti program pertukaran pelajar internasional.',
            image: '<?= base_url('assets/images/ifik_portal_3d_render.jpg') ?>'
        },
        {
            date: '20 April 2026',
            title: 'Hackathon Desain 48 Jam di Kampus FIK',
            excerpt: 'Lebih dari 200 peserta mengikuti ajang Hackathon Desain selama 48 jam nonstop, menghasilkan puluhan prototipe produk digital inovatif yang siap dikembangkan.',
            image: '<?= base_url('assets/images/background.png') ?>'
        },
        {
            date: '12 April 2026',
            title: 'Talkshow Karir Kreatif Bersama Alumni Sukses FIK',
            excerpt: 'Puluhan alumni FIK yang kini berkarir di perusahaan multinasional berbagi pengalaman dan motivasi kepada mahasiswa aktif dalam sesi talkshow yang sangat inspiratif.',
            image: '<?= base_url('assets/images/ik_3d_illustration.jpg') ?>'
        },
        {
            date: '04 April 2026',
            title: 'FIK Resmikan Pusat Inovasi dan Inkubasi Startup Kreatif',
            excerpt: 'Pusat inkubasi baru resmi dibuka untuk mendukung ekosistem startup mahasiswa, dilengkapi dengan fasilitas co-working, mentoring dari investor, dan akses pendanaan awal.',
            image: '<?= base_url('assets/images/logo-dummy.webp') ?>',
            imageFit: 'contain',
            imageBg: '#f0fdf4'
        },
        {
            date: '28 Maret 2026',
            title: 'Pelatihan AI Generatif untuk Dosen dan Mahasiswa FIK',
            excerpt: 'Program pelatihan intensif penggunaan AI generatif dalam proses desain dan produksi konten kreatif diikuti oleh seluruh civitas akademika FIK dengan antusias.',
            image: '<?= base_url('assets/images/ifik_portal_3d_render.jpg') ?>'
        },
    ];

    // ===== KONFIGURASI =====
    const CARDS_PER_PAGE = 5;
    const container      = document.getElementById('newsFanContainer');
    const prevBtn        = document.getElementById('newsPrevBtn');
    const nextBtn        = document.getElementById('newsNextBtn');
    const dotsEl         = document.getElementById('newsDots');
    const pageInfoEl     = document.getElementById('newsPageInfo');

    const totalPages = Math.ceil(allNews.length / CARDS_PER_PAGE);
    let currentPage  = 0;
    let isAnimating  = false;

    // ===== BUILD DOTS =====
    function buildDots() {
        dotsEl.innerHTML = '';
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('div');
            dot.className = 'news-dot' + (i === currentPage ? ' active' : '');
            dot.addEventListener('click', () => goToPage(i));
            dotsEl.appendChild(dot);
        }
    }

    // ===== UPDATE UI =====
    function updateControls() {
        prevBtn.disabled = currentPage === 0;
        nextBtn.disabled = currentPage === totalPages - 1;
        pageInfoEl.textContent = `${currentPage + 1} / ${totalPages}`;
        // Update dots
        dotsEl.querySelectorAll('.news-dot').forEach((d, i) => {
            d.classList.toggle('active', i === currentPage);
        });
    }

    // ===== RENDER KARTU (tanpa animasi, untuk inisialisasi) =====
    function renderPage(pageIndex) {
        container.innerHTML = '';
        const start  = pageIndex * CARDS_PER_PAGE;
        const slice  = allNews.slice(start, start + CARDS_PER_PAGE);
        const total  = slice.length;

        slice.forEach((news, i) => {
            const card = createCard(news, i, total);
            container.appendChild(card);
        });
    }

    // ===== BUAT ELEMEN KARTU =====
    function createCard(news, index, total) {
        const card = document.createElement('div');
        card.className = 'news-card';
        card.style.setProperty('--index', index);
        card.style.setProperty('--total', total);

        const imgStyle = news.imageFit
            ? `background-image:url('${news.image}'); background-size:contain; background-repeat:no-repeat; background-color:${news.imageBg || '#f1f5f9'};`
            : `background-image:url('${news.image}');`;

        card.innerHTML = `
            <div class="news-image" style="${imgStyle}"></div>
            <div class="news-content">
                <span class="news-date">${news.date}</span>
                <h3 class="news-title">${news.title}</h3>
                <p class="news-excerpt">${news.excerpt}</p>
            </div>`;

        if (news.url) {
            card.addEventListener('click', () => window.location.href = news.url);
        }
        return card;
    }

    // ===== ANIMASI PINDAH HALAMAN =====
    function goToPage(targetPage, direction = null) {
        if (isAnimating || targetPage === currentPage) return;
        if (targetPage < 0 || targetPage >= totalPages) return;

        isAnimating = true;

        // Tentukan arah animasi
        const goingForward = direction !== null ? direction === 'next' : targetPage > currentPage;
        const exitClass   = goingForward ? 'anim-exit'       : 'anim-exit-up';
        const enterClass  = goingForward ? 'anim-enter'      : 'anim-enter-down';

        // Ambil kartu-kartu yang sedang tampil
        const currentCards = Array.from(container.querySelectorAll('.news-card'));

        // 1. Animasikan kartu lama keluar
        currentCards.forEach(card => {
            card.classList.add(exitClass);
        });

        // 2. Setelah animasi keluar selesai, render halaman baru
        setTimeout(() => {
            currentPage = targetPage;
            renderPage(currentPage);
            updateControls();

            // 3. Animasikan kartu baru masuk
            const newCards = Array.from(container.querySelectorAll('.news-card'));
            newCards.forEach((card, i) => {
                // Stagger: kartu masuk satu per satu dengan delay
                card.style.animationDelay = `${i * 60}ms`;
                card.classList.add(enterClass);
            });

            // 4. Lepas class animasi setelah selesai
            setTimeout(() => {
                newCards.forEach(card => {
                    card.classList.remove(enterClass);
                    card.style.animationDelay = '';
                });
                isAnimating = false;
            }, 650 + (newCards.length * 60));

        }, 480);
    }

    // ===== EVENT LISTENER TOMBOL =====
    prevBtn.addEventListener('click', () => goToPage(currentPage - 1, 'prev'));
    nextBtn.addEventListener('click', () => goToPage(currentPage + 1, 'next'));

    // ===== MODAL VIEW ALL LOGIC =====
    const newsModal      = document.getElementById('newsModal');
    const newsModalClose = document.getElementById('newsModalClose');
    const newsViewAllBtn = document.getElementById('newsViewAllBtn');
    const newsModalGrid  = document.getElementById('newsModalGrid');
    const newsModalCount = document.getElementById('newsModalCount');
    const newsSearchInput  = document.getElementById('newsSearchInput');
    const newsSearchClear  = document.getElementById('newsSearchClear');
    const newsAutocomplete = document.getElementById('newsAutocomplete');

    // Populate modal cards
    function populateModal(newsList = allNews, animate = false) {
        newsModalGrid.innerHTML = '';
        newsModalCount.textContent = `Total: ${newsList.length} berita`;

        if (newsList.length === 0) {
            newsModalGrid.innerHTML = '<div class="news-autocomplete-no-match" style="grid-column: 1/-1; text-align: center; color: #94a3b8; font-size: 1.1rem; padding: 40px 0; width: 100%;">Tidak ada berita yang cocok dengan pencarian Anda.</div>';
            return;
        }

        newsList.forEach((news, i) => {
            const card = document.createElement('div');
            // Selang-seling: genap = cutout kanan atas, ganjil = cutout kiri bawah
            card.className = 'news-modal-card' + (i % 2 !== 0 ? ' news-modal-card--alt' : '');
            if (!animate) {
                card.classList.add('visible');
            }
            
            const isContain = news.imageFit === 'contain';
            const imgStyle = isContain
                ? `object-fit: contain; padding: 12px; background: ${news.imageBg || '#1e293b'};`
                : `object-fit: cover;`;

            card.innerHTML = `
                <div class="news-modal-card-body">
                    <div class="news-modal-card-bg" style="${isContain ? 'background:' + (news.imageBg || '#1e293b') : ''}">
                        <img class="news-modal-card-img" src="${news.image}" alt="${news.title}" loading="lazy" style="${imgStyle}" />
                    </div>
                    <div class="news-modal-card-overlay"></div>
                    <div class="news-modal-card-badge">${news.date}</div>
                    <div class="news-modal-card-content">
                        <h3 class="news-modal-card-title">${news.title}</h3>
                        <p class="news-modal-card-excerpt">${news.excerpt}</p>
                    </div>
                </div>
                <button class="news-modal-card-btn" title="Baca Selengkapnya">
                    <div class="news-modal-card-btn-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </div>
                </button>
            `;

            if (news.url) {
                card.addEventListener('click', () => window.location.href = news.url);
            }
            newsModalGrid.appendChild(card);

            if (animate) {
                setTimeout(() => {
                    card.classList.add('visible');
                }, i * 30);
            }
        });
    }

    // Open Modal
    newsViewAllBtn.addEventListener('click', () => {
        populateModal(allNews, true);
        newsModal.classList.add('open');
    });

    // Close Modal
    function closeModal() {
        newsModal.classList.remove('open');
        const modalCards = newsModalGrid.querySelectorAll('.news-modal-card');
        modalCards.forEach(card => card.classList.remove('visible'));
        
        // Reset search input and search results
        newsSearchInput.value = '';
        newsSearchClear.style.display = 'none';
        newsAutocomplete.classList.remove('open');
    }

    newsModalClose.addEventListener('click', closeModal);
    newsModal.addEventListener('click', (e) => {
        if (e.target === newsModal) {
            closeModal();
        }
    });

    // Handle escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && newsModal.classList.contains('open')) {
            closeModal();
        }
    });

    // Search and Autocomplete functionality
    function performSearch(query) {
        const filtered = allNews.filter(news => 
            news.title.toLowerCase().includes(query.toLowerCase()) || 
            news.excerpt.toLowerCase().includes(query.toLowerCase())
        );
        populateModal(filtered, false);
    }

    function showAutocomplete(query) {
        if (!query) {
            newsAutocomplete.classList.remove('open');
            newsAutocomplete.innerHTML = '';
            return;
        }

        const matches = allNews.filter(news => 
            news.title.toLowerCase().includes(query.toLowerCase())
        ).slice(0, 5); // Limit suggestions to 5

        if (matches.length === 0) {
            newsAutocomplete.innerHTML = '<div class="news-autocomplete-no-match">Tidak ada kecocokan</div>';
        } else {
            newsAutocomplete.innerHTML = '';
            matches.forEach(news => {
                const item = document.createElement('div');
                item.className = 'news-autocomplete-item';
                
                // Highlight matching characters
                const regex = new RegExp(`(${query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&')})`, 'gi');
                const highlighted = news.title.replace(regex, `<strong>$1</strong>`);
                
                item.innerHTML = highlighted;
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    newsSearchInput.value = news.title;
                    performSearch(news.title);
                    newsAutocomplete.classList.remove('open');
                    newsSearchClear.style.display = 'block';
                });
                newsAutocomplete.appendChild(item);
            });
        }
        newsAutocomplete.classList.add('open');
    }

    newsSearchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        performSearch(query);
        showAutocomplete(query);
        newsSearchClear.style.display = query ? 'block' : 'none';
    });

    newsSearchInput.addEventListener('focus', (e) => {
        const query = e.target.value.trim();
        if (query) {
            showAutocomplete(query);
        }
    });

    // Hide dropdown on blur with delay to allow clicks to register
    newsSearchInput.addEventListener('blur', () => {
        setTimeout(() => {
            newsAutocomplete.classList.remove('open');
        }, 200);
    });

    newsSearchClear.addEventListener('click', () => {
        newsSearchInput.value = '';
        performSearch('');
        newsAutocomplete.classList.remove('open');
        newsSearchClear.style.display = 'none';
        newsSearchInput.focus();
    });

    // ===== INISIALISASI =====
    buildDots();
    renderPage(0);
    updateControls();
});
</script>
