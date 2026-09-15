<section class="section-wrapper" id="section-virtual-tour">
<style>
    /* ===== SECTION: VIRTUAL TOUR (ENHANCED 3D POP-OUT CARDS) ===== */
    #section-virtual-tour {
        background: #fbf7f1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 40px 50px;
        position: relative;
        z-index: 5;
        overflow: hidden !important; /* Mencegah canvas 3D & card tembus ke Berita */
        box-sizing: border-box;
        height: 100vh;
    }

    #section-virtual-tour::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 65% 55% at 20% 45%, rgba(234, 88, 12, 0.08) 0%, transparent 70%),
            radial-gradient(ellipse 65% 55% at 80% 55%, rgba(234, 88, 12, 0.06) 0%, transparent 70%);
        pointer-events: none;
    }

    .vt-header {
        text-align: center;
        z-index: 20;
        position: relative;
        margin-top: 0;
        margin-bottom: 0;
    }

    .vt-header-label {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #ea580c;
        margin-bottom: 2px;
    }

    .vt-header h2 {
        font-size: clamp(1.8rem, 2.5vw, 2.2rem);
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.5px;
        margin: 0;
    }

    /* Grid Layout - Presisi untuk 3D Pop-Out */
    .vt-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 36px;
        width: 100%;
        max-width: 1040px;
        z-index: 10;
        margin-top: 45px;
    }

    /* Main Card Container */
    .vt-card {
        position: relative;
        height: 380px;
        border-radius: 28px;
        overflow: visible;
        background: #ffffff;
        border: 2.5px solid rgba(255, 255, 255, 0.95);
        box-shadow: 
            0 20px 50px -10px rgba(15, 23, 42, 0.12),
            0 12px 30px -5px rgba(234, 88, 12, 0.18),
            inset 0 1.5px 2px rgba(255, 255, 255, 0.9);
        display: flex;
        flex-direction: column;
        cursor: pointer;
        transition: transform 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275),
                    box-shadow 0.45s ease,
                    border-color 0.45s ease;
        z-index: 10;
    }
    .vt-card:hover {
        transform: translateY(-8px) scale(1.02);
        border-color: rgba(234, 88, 12, 0.45);
        box-shadow: 
            0 30px 75px -10px rgba(234, 88, 12, 0.32),
            0 16px 40px -5px rgba(234, 88, 12, 0.22);
    }

    /* 1. Latar Belakang Top Viewer Box */
    .vt-card-bg-top {
        height: 195px;
        background: linear-gradient(180deg, #ffffff 0%, #f4efe9 100%);
        border-radius: 25px 25px 0 0;
        position: relative;
        z-index: 1;
        box-shadow: inset 0 -4px 12px rgba(0,0,0,0.02);
    }

    /* 2. 3D Model Layer (MENONJOL & BESAR) */
    .vt-card-3d {
        position: absolute;
        top: -55px; /* Menonjol 55px ke atas keluar dari card */
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        height: 275px;
        z-index: 10;
        pointer-events: none; /* Di-disable agar tidak menghalangi klik pada tombol atau berita */
    }
    .vt-card-3d model-viewer {
        width: 100%;
        height: 100%;
        background-color: transparent;
        --poster-color: transparent;
        pointer-events: none; /* Nonaktifkan penangkapan pointer pada canvas 3D */
        transform: scale(1.22);
        transform-origin: bottom center;
        transition: transform 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .vt-card:hover .vt-card-3d model-viewer {
        transform: scale(1.35) translateY(-5px);
    }

    /* Floating Decorative Dots */
    .vt-dot {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        opacity: 0.9;
        z-index: 12;
        animation: vtFloat 4s ease-in-out infinite alternate;
    }
    .vt-dot-1 { width: 18px; height: 18px; background: #ea580c; top: -16px; right: 24px; animation-delay: 0s; box-shadow: 0 4px 12px rgba(234,88,12,0.4); }
    .vt-dot-2 { width: 10px; height: 10px; background: #fbbf24; top: 32px; right: 14px; animation-delay: 0.6s; }
    .vt-dot-3 { width: 12px; height: 12px; background: #fb923c; bottom: 24px; left: 16px; animation-delay: 1.2s; }

    @keyframes vtFloat {
        0%   { transform: translateY(0px) scale(1); }
        100% { transform: translateY(-10px) scale(1.12); }
    }

    /* 3. Konten Oranye Bawah */
    .vt-card-content {
        height: 185px;
        background: linear-gradient(180deg, #f97316 0%, #ea580c 60%, #c2410c 100%);
        padding: 16px 28px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 6px;
        border-radius: 0 0 25px 25px;
        position: relative;
        z-index: 20;
        box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.3);
    }

    .vt-card-title {
        font-size: 1.6rem;
        font-weight: 900;
        color: #ffffff;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin: 0;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .vt-card-desc {
        font-size: 0.88rem;
        color: rgba(255, 255, 255, 0.94);
        line-height: 1.6;
        margin: 0;
        max-width: 330px;
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .vt-card-btn {
        margin-top: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 32px;
        background: #ffffff;
        color: #ea580c;
        font-size: 0.82rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        border-radius: 9999px;
        border: 2.5px solid #ffffff;
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        z-index: 25;
        pointer-events: auto;
    }
    .vt-card-btn:hover {
        background: transparent;
        color: #ffffff;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        transform: translateY(-3px);
    }

    /* Responsive - Tetap 2 Kolom Horizontal di Semua Ukuran Layar */
    @media (max-height: 820px) {
        #section-virtual-tour {
            height: auto !important;
            min-height: 100vh;
            padding: 40px 30px 60px;
        }
        .vt-grid { margin-top: 50px; }
        .vt-card { height: 360px; }
        .vt-card-bg-top { height: 180px; }
        .vt-card-3d { top: -45px; height: 250px; }
        .vt-card-content { height: 180px; padding: 16px 20px; }
    }

    @media (max-width: 900px) {
        #section-virtual-tour {
            height: auto !important;
            min-height: 100vh;
            padding: 35px 20px 50px;
        }
        .vt-grid {
            grid-template-columns: 1fr 1fr !important; /* Tetap 2 card sejajar horizontal */
            max-width: 100%;
            margin-top: 45px;
            gap: 16px;
        }
        .vt-card {
            height: 330px;
            border-radius: 22px;
        }
        .vt-card-bg-top {
            height: 165px;
            border-radius: 19px 19px 0 0;
        }
        .vt-card-3d {
            top: -40px;
            height: 220px;
        }
        .vt-card-content {
            height: 165px;
            padding: 12px 14px 16px;
            border-radius: 0 0 19px 19px;
            gap: 4px;
        }
        .vt-card-title {
            font-size: 1.25rem;
            letter-spacing: 1px;
        }
        .vt-card-desc {
            font-size: 0.78rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .vt-card-btn {
            padding: 8px 22px;
            font-size: 0.74rem;
            margin-top: 4px;
        }
    }

    @media (max-width: 540px) {
        #section-virtual-tour {
            padding: 30px 12px 45px;
        }
        .vt-header h2 {
            font-size: 1.45rem;
        }
        .vt-header-label {
            font-size: 0.65rem;
        }
        .vt-grid {
            grid-template-columns: 1fr 1fr !important; /* Tetap horizontal di HP */
            gap: 10px;
            margin-top: 35px;
        }
        .vt-card {
            height: 290px;
            border-radius: 18px;
        }
        .vt-card-bg-top {
            height: 140px;
            border-radius: 15px 15px 0 0;
        }
        .vt-card-3d {
            top: -30px;
            height: 180px;
        }
        .vt-card-content {
            height: 150px;
            padding: 10px 10px 14px;
            border-radius: 0 0 15px 15px;
            gap: 3px;
        }
        .vt-card-title {
            font-size: 1.05rem;
            letter-spacing: 0.8px;
        }
        .vt-card-desc {
            font-size: 0.72rem;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .vt-card-btn {
            padding: 6px 14px;
            font-size: 0.68rem;
            letter-spacing: 0.8px;
            margin-top: 4px;
        }
        .vt-footer {
            position: relative;
            bottom: auto;
            left: auto;
            transform: none;
            margin-top: 25px;
            text-align: center;
            font-size: 0.72rem;
        }
    }

    @media (max-width: 360px) {
        #section-virtual-tour {
            padding: 24px 8px 36px;
        }
        .vt-header h2 {
            font-size: 1.25rem;
        }
        .vt-grid {
            gap: 6px;
            margin-top: 28px;
        }
        .vt-card {
            height: 265px;
            border-radius: 14px;
        }
        .vt-card-bg-top {
            height: 125px;
            border-radius: 12px 12px 0 0;
        }
        .vt-card-3d {
            top: -25px;
            height: 160px;
        }
        .vt-card-content {
            height: 140px;
            padding: 8px 6px 10px;
            border-radius: 0 0 12px 12px;
        }
        .vt-card-title {
            font-size: 0.92rem;
        }
        .vt-card-desc {
            font-size: 0.66rem;
            line-height: 1.3;
        }
        .vt-card-btn {
            padding: 5px 10px;
            font-size: 0.62rem;
        }
    }
</style>

    <div class="vt-header">
        <span class="vt-header-label">&#x2736; Jelajahi Kampus</span>
        <h2>Virtual Tour IFIK</h2>
    </div>

    <div class="vt-grid">

        <!-- Card 1: FIK TOUR -->
        <div class="vt-card">
            <!-- Latar Belakang Cream Gradient -->
            <div class="vt-card-bg-top"></div>

            <!-- 3D Model Melayang & Menonjol Keluar Card -->
            <div class="vt-card-3d">
                <model-viewer
                    src="<?= base_url('assets/3D/CharIFIK.glb') ?>"
                    alt="Karakter FIK Tour"
                    camera-controls
                    camera-target="auto auto auto"
                    min-camera-orbit="auto 85deg auto"
                    max-camera-orbit="auto 85deg auto"
                    disable-zoom
                    interaction-prompt="none"
                    shadow-intensity="1"
                    exposure="1.2"
                    camera-orbit="0deg 85deg 98%"
                    field-of-view="22deg">
                </model-viewer>
            </div>

            <!-- Decorative Dots -->
            <div class="vt-dot vt-dot-1"></div>
            <div class="vt-dot vt-dot-2"></div>
            <div class="vt-dot vt-dot-3"></div>

            <!-- Konten Oranye Bawah -->
            <div class="vt-card-content">
                <h3 class="vt-card-title">FIK Tour</h3>
                <p class="vt-card-desc">Tour FIK ini merupakan sarana tour ke Fakultas Industri Kreatif menggunakan animasi 3D</p>
                <a href="#" class="vt-card-btn">Start Tour</a>
            </div>
        </div>

        <!-- Card 2: MEET 360 -->
        <div class="vt-card">
            <!-- Latar Belakang Cream Gradient -->
            <div class="vt-card-bg-top"></div>

            <!-- 3D Model Melayang & Menonjol Keluar Card -->
            <div class="vt-card-3d">
                <model-viewer
                    src="<?= base_url('assets/3D/360Preview.glb') ?>"
                    alt="Aset 360 IFIK"
                    camera-controls
                    camera-target="auto auto auto"
                    min-camera-orbit="auto 85deg auto"
                    max-camera-orbit="auto 85deg auto"
                    disable-zoom
                    interaction-prompt="none"
                    shadow-intensity="1"
                    exposure="1.2"
                    camera-orbit="0deg 85deg 90%"
                    field-of-view="20deg">
                </model-viewer>
            </div>

            <!-- Decorative Dots -->
            <div class="vt-dot vt-dot-1"></div>
            <div class="vt-dot vt-dot-2"></div>
            <div class="vt-dot vt-dot-3"></div>

            <!-- Konten Oranye Bawah -->
            <div class="vt-card-content">
                <h3 class="vt-card-title">Meet 360</h3>
                <p class="vt-card-desc">Tour FIK ini merupakan sarana tour ke Fakultas Industri Kreatif menggunakan visualisasi 360</p>
                <a href="#" class="vt-card-btn">More</a>
            </div>
        </div>

    </div>

    <footer class="vt-footer">
        &copy; <?= date('Y') ?> IFIK Dashboard. All rights reserved.
    </footer>

</section>
