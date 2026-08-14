<section class="section-wrapper" id="section-virtual-tour">
<style>
    /* ===== SECTION: VIRTUAL TOUR ===== */
    #section-virtual-tour {
        background: #fbf7f1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 50px 80px 40px;
        gap: 20px;
        position: relative;
        overflow: hidden;
    }

    #section-virtual-tour::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 50% at 25% 50%, rgba(234, 88, 12, 0.06) 0%, transparent 70%),
            radial-gradient(ellipse 60% 50% at 75% 50%, rgba(234, 88, 12, 0.04) 0%, transparent 70%);
        pointer-events: none;
    }

    .vt-header {
        text-align: center;
        z-index: 1;
    }

    .vt-header-label {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #ea580c;
        margin-bottom: 8px;
    }

    .vt-header h2 {
        font-size: clamp(1.8rem, 3vw, 2.5rem);
        font-weight: 800;
        color: #1e293b;
        line-height: 1.2;
    }

    /* Grid Layout */
    .vt-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 36px;
        width: 100%;
        max-width: 1100px;
        z-index: 1;
        margin-top: 45px; /* Memberikan sela untuk 3D Pop-Out di atas */
    }

    /* Main Card Container */
    .vt-card {
        position: relative;
        height: 450px;
        border-radius: 28px;
        overflow: visible; /* 3D Model Bebas Menembus Frame */
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        cursor: pointer;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275),
                    box-shadow 0.4s ease;
    }
    .vt-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 30px 75px rgba(234, 88, 12, 0.22);
    }

    /* 1. Latar Belakang Abu-abu Atas (Viewer Box) */
    .vt-card-viewer-bg {
        height: 240px;
        background: #f0ece8;
        border-radius: 28px 28px 0 0;
        position: relative;
        z-index: 1;
    }

    /* 2. 3D Model Layer (Terpisah & Melayang di Atas Card) */
    .vt-card-3d {
        position: absolute;
        top: -55px; /* Menonjol 55px keluar dari frame atas */
        left: 0;
        right: 0;
        height: 320px; /* Canvas tinggi melampaui area abu-abu agar kaki tidak terpotong */
        z-index: 10;
        pointer-events: none;
    }
    .vt-card-3d model-viewer {
        width: 100%;
        height: 100%;
        background-color: transparent;
        --poster-color: transparent;
        pointer-events: auto;
        transition: transform 0.4s ease;
    }
    .vt-card:hover .vt-card-3d model-viewer {
        transform: scale(1.05);
    }

    /* Floating Decorative Dots */
    .vt-dot {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        opacity: 0.85;
        z-index: 12;
        animation: vtFloat 4s ease-in-out infinite alternate;
    }
    .vt-dot-1 { width: 18px; height: 18px; background: #ea580c; top: -15px; right: 24px; animation-delay: 0s; }
    .vt-dot-2 { width: 10px; height: 10px; background: #fbbf24; top: 35px; right: 14px; animation-delay: 0.6s; }
    .vt-dot-3 { width: 12px; height: 12px; background: #fb923c; bottom: 25px; left: 16px; animation-delay: 1.2s; }

    @keyframes vtFloat {
        0%   { transform: translateY(0px) scale(1); }
        100% { transform: translateY(-12px) scale(1.1); }
    }

    /* 3. Konten Oranye Bawah */
    .vt-card-content {
        height: 210px;
        background: #ea580c;
        padding: 24px 32px 28px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 8px;
        border-radius: 0 0 28px 28px;
        position: relative;
        z-index: 2;
    }

    .vt-card-title {
        font-size: 1.45rem;
        font-weight: 900;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
    }

    .vt-card-desc {
        font-size: 0.85rem;
        color: rgba(255,255,255,0.88);
        line-height: 1.55;
        margin: 0;
        max-width: 320px;
    }

    .vt-card-btn {
        margin-top: 6px;
        display: inline-block;
        padding: 9px 26px;
        background: #fff;
        color: #ea580c;
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        border-radius: 6px;
        border: 2px solid #fff;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.25s ease;
    }
    .vt-card-btn:hover {
        background: transparent;
        color: #fff;
    }

    /* Responsive */
    @media (max-height: 820px) {
        #section-virtual-tour { padding: 35px 60px 30px; gap: 15px; }
        .vt-card { height: 400px; }
        .vt-card-bg-top { height: 200px; }
        .vt-card-3d { top: -45px; height: 270px; }
        .vt-card-content { height: 200px; padding: 18px 24px 22px; }
    }
    @media (max-width: 900px) {
        .vt-grid { grid-template-columns: 1fr; max-width: 480px; }
        #section-virtual-tour { padding: 40px 24px; }
    }

    .vt-footer {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.8rem;
        color: #94a3b8;
        font-weight: 500;
        z-index: 2;
        white-space: nowrap;
    }
</style>

    <div class="vt-header">
        <span class="vt-header-label">&#x2736; Jelajahi Kampus</span>
        <h2>Virtual Tour IFIK</h2>
    </div>

    <div class="vt-grid">

        <!-- Card 1: FIK TOUR -->
        <div class="vt-card">
            <!-- Latar Belakang Abu-abu -->
            <div class="vt-card-bg-top"></div>

            <!-- 3D Model Melayang Bebas -->
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
                    exposure="1.15"
                    camera-orbit="0deg 85deg 98%"
                    field-of-view="30deg">
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
            <!-- Latar Belakang Abu-abu -->
            <div class="vt-card-bg-top"></div>

            <!-- 3D Model Melayang Bebas -->
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
                    exposure="1.15"
                    camera-orbit="0deg 85deg 90%"
                    field-of-view="26deg">
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
