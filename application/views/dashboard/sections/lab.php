<style>
    /* Hide model-viewer hand interaction prompt graphic permanently */
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

    /* Styling khusus Sesi Laboratorium Fakultas */
    #section-lab {
        background-color: var(--bg-color);
        display: flex;
        align-items: center;
        overflow: hidden;
        position: relative;
        background-image: 
            radial-gradient(at 100% 0%, rgba(234, 88, 12, 0.08) 0px, transparent 50%),
            radial-gradient(at 0% 100%, rgba(234, 88, 12, 0.08) 0px, transparent 50%);
    }

    .lab-container {
        /* Menggeser container ke sisi kanan layar, menyisakan ruang di kiri untuk 3D Logo */
        margin-left: auto;
        margin-right: 5vw;
        width: calc(100% - 45vw); /* Memaksa menyisakan 45% layar kiri untuk logo 3D */
        max-width: 1000px;
        max-height: calc(100vh - 40px);
        padding: 32px 40px 24px 40px;
        z-index: 2;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .lab-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 10px;
    }

    .lab-header h1 {
        font-size: 2.8rem;
        font-weight: 800;
        color: var(--text-color);
        letter-spacing: -1px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .lab-header p {
        color: #64748b;
        font-size: 1.05rem;
        font-weight: 500;
    }

    .lab-nav-btns {
        display: flex;
        gap: 10px;
    }

    .lab-nav-btn {
        background: rgba(234, 88, 12, 0.1);
        border: 1px solid rgba(234, 88, 12, 0.3);
        color: #ea580c;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.1rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .lab-nav-btn:hover {
        background: #ea580c;
        color: #ffffff;
        transform: scale(1.08);
    }

    .lab-grid {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE/Edge */
        width: 100%;
        padding-top: 75px;
        padding-bottom: 15px;
        margin-top: 5px;
        scroll-behavior: smooth;
    }

    .lab-grid::-webkit-scrollbar {
        display: none;
    }

    .lab-card {
        flex: 0 0 320px;
        min-width: 320px;
        scroll-snap-align: start;
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.92) 0%, rgba(254, 243, 237, 0.85) 100%);
        border-radius: 24px;
        padding: 24px;
        border: 1px solid rgba(234, 88, 12, 0.18);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: visible; /* PENTING: Izinkan objek 3D keluar dari garis batas atas KARTU UTAMA */
    }

    .lab-card:hover {
        transform: translateY(-6px);
        background: linear-gradient(145deg, #ffffff 0%, rgba(255, 237, 224, 0.95) 100%);
        border-color: rgba(234, 88, 12, 0.4);
        box-shadow: 0 20px 40px rgba(234, 88, 12, 0.16);
    }

    .card-3d-model-wrapper {
        width: 100%;
        height: 150px;
        background: transparent;
        border: none;
        margin-bottom: 18px;
        position: relative;
        overflow: visible;
        box-shadow: none;
    }

    /* Pendaran Cahaya Studio (Soft Ambient Backlight Glow) di Belakang 3D Model */
    .card-3d-model-wrapper::before {
        content: '';
        position: absolute;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 180px;
        height: 140px;
        background: radial-gradient(circle, rgba(234, 88, 12, 0.22) 0%, rgba(251, 146, 60, 0.08) 50%, transparent 75%);
        filter: blur(25px);
        border-radius: 50%;
        pointer-events: none;
        z-index: 1;
        transition: all 0.35s ease;
    }

    .lab-card:hover .card-3d-model-wrapper::before {
        width: 210px;
        height: 160px;
        background: radial-gradient(circle, rgba(234, 88, 12, 0.32) 0%, rgba(251, 146, 60, 0.12) 50%, transparent 75%);
        filter: blur(28px);
    }

    .card-3d-model-wrapper model-viewer {
        position: absolute;
        top: -90px; /* MENYEMBUL KELUAR 90px DI ATAS GARIS KARTU UTAMA */
        left: 50%;
        transform: translateX(-50%);
        width: 120%;
        height: 245px;
        z-index: 20;
        pointer-events: auto;
    }

    .lab-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .lab-desc {
        font-size: 0.88rem;
        color: #64748b;
        line-height: 1.55;
        margin-bottom: 16px;
    }

    .lab-specs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .spec-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 12px;
        background: #f1f5f9;
        color: #475569;
    }

    .lab-action {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 14px;
        border-top: 1px solid rgba(0, 0, 0, 0.06);
    }

    .status-pill {
        font-size: 0.78rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
    }

    .status-open { background: #dcfce7; color: #15803d; }
    .status-busy { background: #fef3c7; color: #b45309; }

    .btn-detail-lab {
        color: #ea580c;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: transform 0.2s ease;
    }

    .btn-detail-lab:hover {
        transform: translateX(4px);
    }

    /* Indicators Garis Dempetan dengan 3 State: Passed (Border Oren), Active (Solid Lebar Tebal), Upcoming (Biasa) */
    .lab-slide-indicators {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 4px; /* Dempetan seperti di gambar */
        margin-top: 18px;
    }

    .slide-line {
        width: 36px;
        height: 5px;
        border-radius: 4px;
        background: rgba(0, 0, 0, 0.12);
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);
        box-sizing: border-box;
    }

    .slide-line:hover {
        background: rgba(234, 88, 12, 0.3);
    }

    /* State 1: Line sebelum nya yang sudah dilewati (Hanya Border Oren) */
    .slide-line.passed {
        background: transparent;
        border: 1.5px solid #ea580c;
    }

    /* State 2: Line aktif saat ini (Solid Oren Lebar & Tebal) */
    .slide-line.active {
        width: 80px;
        height: 6px;
        background: #ea580c;
        border: 1px solid #ea580c;
        border-radius: 6px;
        box-shadow: 0 2px 10px rgba(234, 88, 12, 0.45);
    }

    @media (max-width: 992px) {
        .lab-container {
            margin: 20px;
            width: calc(100% - 40px);
        }
        .lab-card {
            flex: 0 0 280px;
            min-width: 280px;
        }
    }
</style>

<!-- Sesi: Laboratorium Fakultas -->
<div class="section-wrapper" id="section-lab">
    <div class="lab-container">
        
        <div class="lab-header">
            <div>
                <h1>LABORATORIUM FAKULTAS</h1>
                <p>Fasilitas laboratorium di fakultas industri kreatif</p>
            </div>
            <div class="lab-nav-btns">
                <button class="lab-nav-btn" onclick="scrollLabGrid(-1)" title="Geser Kiri">&#8592;</button>
                <button class="lab-nav-btn" onclick="scrollLabGrid(1)" title="Geser Kanan">&#8594;</button>
            </div>
        </div>

        <div class="lab-grid" id="labGrid">
            
            <!-- Lab 1: Multimedia & Game -->
            <div class="lab-card">
                <div>
                    <!-- 3D Model Header khusus Lab 1 -->
                    <div class="card-3d-model-wrapper">
                        <model-viewer 
                            src="<?= base_url('assets/3D/' . rawurlencode('lab.multi media (1).glb')) ?>" 
                            alt="3D Lab Multimedia" 
                            camera-orbit="45deg 75deg 75%"
                            min-camera-orbit="auto 75deg 75%"
                            max-camera-orbit="auto 75deg 75%"
                            field-of-view="15.5deg"
                            camera-controls 
                            disable-zoom 
                            disable-pan
                            touch-action="pan-y"
                            shadow-intensity="1.5" 
                            shadow-softness="0.8"
                            exposure="1.2"
                            interaction-prompt="none"
                            style="background-color: transparent;">
                        </model-viewer>
                    </div>
                    <h3 class="lab-title">Lab Multimedia &amp; Game</h3>
                    <p class="lab-desc">Fasilitas komputer spesifikasi tinggi untuk pengembangan game 3D, animasi digital, dan realitas virtual (VR).</p>
                    <div class="lab-specs">
                        <span class="spec-badge">36 Workstation</span>
                        <span class="spec-badge">RTX GPU</span>
                        <span class="spec-badge">VR Headsets</span>
                    </div>
                </div>
                <div class="lab-action">
                    <span class="status-pill status-open">● Tersedia</span>
                    <a href="<?= site_url('dashboard/lab_detail/multimedia') ?>" class="btn-detail-lab">Lihat Lab &rarr;</a>
                </div>
            </div>

            <!-- Lab 2: Aula Utama Fakultas -->
            <div class="lab-card">
                <div>
                    <!-- 3D Model Header khusus Aula -->
                    <div class="card-3d-model-wrapper">
                        <model-viewer 
                            src="<?= base_url('assets/3D/Aula.glb') ?>" 
                            alt="3D Aula Utama Fakultas" 
                            camera-orbit="-135deg 75deg 75%"
                            min-camera-orbit="auto 75deg 75%"
                            max-camera-orbit="auto 75deg 75%"
                            field-of-view="15.5deg"
                            camera-controls 
                            disable-zoom 
                            disable-pan
                            touch-action="pan-y"
                            shadow-intensity="1.5" 
                            shadow-softness="0.8"
                            exposure="1.2"
                            interaction-prompt="none"
                            style="background-color: transparent;">
                        </model-viewer>
                    </div>
                    <h3 class="lab-title">Aula Utama Fakultas</h3>
                    <p class="lab-desc">Ruang aula serbaguna berkapasitas besar untuk acara seminar, pameran seni &amp; desain, sidang komprehensif, dan event fakultas.</p>
                    <div class="lab-specs">
                        <span class="spec-badge">Kapasitas 300+</span>
                        <span class="spec-badge">Sound System</span>
                        <span class="spec-badge">Stage LED</span>
                    </div>
                </div>
                <div class="lab-action">
                    <span class="status-pill status-open">● Tersedia</span>
                    <a href="<?= site_url('dashboard/lab_detail/aula') ?>" class="btn-detail-lab">Lihat Aula &rarr;</a>
                </div>
            </div>

            <!-- Lab 3: Lab Tablet Cintiq -->
            <div class="lab-card">
                <div>
                    <!-- 3D Model Header khusus Lab Tablet Cintiq -->
                    <div class="card-3d-model-wrapper">
                        <model-viewer 
                            src="<?= base_url('assets/3D/' . rawurlencode('lab tab cintiq (1).glb')) ?>" 
                            alt="3D Lab Tablet Cintiq" 
                            camera-orbit="45deg 75deg 75%"
                            min-camera-orbit="auto 75deg 75%"
                            max-camera-orbit="auto 75deg 75%"
                            field-of-view="15.5deg"
                            camera-controls 
                            disable-zoom 
                            disable-pan
                            touch-action="pan-y"
                            shadow-intensity="1.5" 
                            shadow-softness="0.8"
                            exposure="1.2"
                            interaction-prompt="none"
                            style="background-color: transparent;">
                        </model-viewer>
                    </div>
                    <h3 class="lab-title">Lab Tablet Cintiq</h3>
                    <p class="lab-desc">Fasilitas pen display Wacom Cintiq profesional untuk ilustrasi digital, concept art, komik, 2D animation, dan 3D sculpting.</p>
                    <div class="lab-specs">
                        <span class="spec-badge">Wacom Cintiq Pro</span>
                        <span class="spec-badge">Stylus Pen 8K</span>
                        <span class="spec-badge">High Color Display</span>
                    </div>
                </div>
                <div class="lab-action">
                    <span class="status-pill status-open">● Tersedia</span>
                    <a href="<?= site_url('dashboard/lab_detail/cintiq') ?>" class="btn-detail-lab">Lihat Lab &rarr;</a>
                </div>
            </div>

        </div>

        <!-- Slide Line Indicators Bar (Desain Garis Aktif Tebal & Lebih Lebar) -->
        <div class="lab-slide-indicators" id="labIndicators">
            <span class="slide-line active" onclick="scrollToLabSlide(0)" title="Lab 1: Multimedia & Game"></span>
            <span class="slide-line" onclick="scrollToLabSlide(1)" title="Lab 2: Aula Utama Fakultas"></span>
            <span class="slide-line" onclick="scrollToLabSlide(2)" title="Lab 3: Lab Tablet Cintiq"></span>
        </div>

    </div>
</div>

<script>
    function scrollToLabSlide(index) {
        const grid = document.getElementById('labGrid');
        if (grid) {
            const cardWidth = 340; // Card width (320px) + gap (20px)
            grid.scrollTo({ left: index * cardWidth, behavior: 'smooth' });
            updateLabIndicators(index);
        }
    }

    function updateLabIndicators(activeIndex) {
        const lines = document.querySelectorAll('#labIndicators .slide-line');
        lines.forEach((line, idx) => {
            if (idx < activeIndex) {
                line.classList.add('passed');
                line.classList.remove('active');
            } else if (idx === activeIndex) {
                line.classList.add('active');
                line.classList.remove('passed');
            } else {
                line.classList.remove('active');
                line.classList.remove('passed');
            }
        });
    }

    function scrollLabGrid(direction) {
        const grid = document.getElementById('labGrid');
        if (grid) {
            const scrollAmount = 340; // Card width (320px) + gap (20px)
            grid.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const grid = document.getElementById('labGrid');
        if (grid) {
            grid.addEventListener('scroll', () => {
                const cardWidth = 340;
                const activeIndex = Math.min(2, Math.max(0, Math.round(grid.scrollLeft / cardWidth)));
                updateLabIndicators(activeIndex);
            });
        }
        const cardViewers = document.querySelectorAll('.card-3d-model-wrapper model-viewer');
        
        cardViewers.forEach(viewer => {
            const defaultOrbit = viewer.getAttribute('camera-orbit') || '45deg 75deg 100%';
            const defaultTarget = viewer.getAttribute('camera-target') || 'auto auto auto';
            let resetTimer = null;

            // Kembalikan ke kecepatan gerak normal saat kursor disentuh/ditekan
            const resetToNormalSpeed = () => {
                viewer.interpolationDecay = 100;
            };

            viewer.addEventListener('pointerdown', resetToNormalSpeed);
            viewer.addEventListener('touchstart', resetToNormalSpeed);

            viewer.addEventListener('camera-change', (e) => {
                if (e.detail && e.detail.source === 'user-interaction') {
                    // Respon gerakan manual user tetap cepat & normal (100)
                    viewer.interpolationDecay = 100;
                    clearTimeout(resetTimer);

                    // 4 detik setelah user berhenti mendrag, perlambat transisi dan kembalikan ke default
                    resetTimer = setTimeout(() => {
                        viewer.interpolationDecay = 450; // Kecepatan kembali diperlambat (smooth & halus)
                        viewer.cameraOrbit = defaultOrbit;
                        viewer.cameraTarget = defaultTarget;
                    }, 4000);
                }
            });
        });
    });
</script>
