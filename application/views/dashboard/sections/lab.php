<style>
    /* Permanent Hide for Google Model-Viewer Built-in Blue Arrow Interaction Prompt Graphic */
    model-viewer::part(user-prompt),
    model-viewer::part(prompt),
    model-viewer::part(interaction-prompt),
    model-viewer::part(ar-button),
    model-viewer .slot.user-prompt,
    model-viewer #prompt,
    model-viewer [slot="user-prompt"],
    model-viewer img,
    .user-prompt,
    #user-prompt {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    /* Styling Sesi Laboratorium Fakultas (100% Silk-Smooth Apple TV Slider) */
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
        margin: 0 auto;
        width: 95vw;
        max-width: 1450px;
        max-height: calc(100vh - 40px);
        padding: 20px 32px 16px 32px;
        z-index: 2;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 28px;
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
        margin-bottom: 8px;
    }

    .lab-header h1 {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--text-color);
        letter-spacing: -1px;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .lab-header p {
        color: #64748b;
        font-size: 0.95rem;
        font-weight: 500;
    }

    /* Apple TV+ Style Viewport & Track */
    .lab-viewport {
        width: 100%;
        overflow: hidden;
        position: relative;
        padding: 6px 0;
        cursor: grab;
        user-select: none;
        touch-action: pan-y;
    }

    .lab-viewport.active-drag {
        cursor: grabbing;
    }

    .lab-track {
        display: flex;
        gap: 24px;
        align-items: center;
        transition: transform 0.48s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform;
    }

    /* Apple TV+ Style Card (GPU Accelerated) */
    .lab-card {
        flex: 0 0 68vw;
        max-width: 820px;
        height: 390px;
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
        position: relative;
        overflow: hidden;
        background: #0f172a;
        opacity: 0.6;
        transform: scale(0.93) translateZ(0);
        will-change: transform, opacity;
    }

    .lab-card.active-card {
        opacity: 1;
        transform: scale(1) translateZ(0);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.45);
        border-color: rgba(234, 88, 12, 0.5);
    }

    .lab-card-bg-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 1;
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }

    .lab-card:hover .lab-card-bg-img {
        transform: scale(1.04);
    }

    /* Gradient Overlay Khas Apple TV */
    .lab-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
        background: linear-gradient(0deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.4) 45%, rgba(0, 0, 0, 0) 80%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 24px 32px;
    }

    .badge-3d-tag {
        align-self: flex-start;
        background: rgba(15, 23, 42, 0.68);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
    }

    .badge-3d-tag svg {
        width: 13px;
        height: 13px;
        fill: #f97316;
    }

    /* Bottom Info Layout */
    .lab-card-bottom-info {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .lab-title {
        font-size: 2.2rem;
        font-weight: 900;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.5px;
        text-transform: uppercase;
        text-shadow: 0 4px 15px rgba(0, 0, 0, 0.8);
        line-height: 1.1;
    }

    .lab-cta-row {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 2px;
    }

    .btn-apple-tv {
        background: #ffffff;
        color: #0f172a;
        font-weight: 800;
        font-size: 0.9rem;
        padding: 11px 26px;
        border-radius: 30px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        transition: transform 0.25s ease, background 0.25s ease, color 0.25s ease;
    }

    .btn-apple-tv:hover {
        background: #ea580c;
        color: #ffffff;
        transform: scale(1.05);
    }

    .lab-apple-desc-inline {
        color: rgba(255, 255, 255, 0.95);
        font-size: 0.95rem;
        font-weight: 500;
        line-height: 1.35;
        max-width: 580px;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.7);
    }

    .lab-apple-desc-inline strong {
        color: #ffffff;
        font-weight: 700;
    }

    /* Dynamic Orange Pill Progress Lines (PERSIS TANGKAPAN LAYAR) */
    .lab-indicators {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 16px;
        z-index: 5;
    }

    .lab-indicator-pill {
        height: 5px;
        width: 24px;
        border-radius: 6px;
        background: rgba(220, 220, 225, 0.45);
        cursor: pointer;
        transition: width 0.38s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.35s ease, box-shadow 0.35s ease;
        will-change: width, background-color;
    }

    .lab-indicator-pill:hover {
        background: rgba(220, 220, 225, 0.7);
    }

    .lab-indicator-pill.active {
        width: 64px;
        background: #f97316;
        box-shadow: 0 3px 10px rgba(249, 115, 22, 0.65);
    }

    /* Premium Glassmorphic Left/Right Navigation Arrow Buttons ("Longgar Di Pinggir") */
    .lab-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .lab-nav-btn svg {
        width: 24px;
        height: 24px;
        fill: #ffffff;
        transition: transform 0.25s ease, fill 0.25s ease;
    }

    .lab-nav-btn.prev-btn {
        left: 20px;
    }

    .lab-nav-btn.next-btn {
        right: 20px;
    }

    .lab-nav-btn:hover {
        background: #ea580c;
        border-color: rgba(234, 88, 12, 0.6);
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 10px 30px rgba(234, 88, 12, 0.5);
    }

    .lab-nav-btn:active {
        transform: translateY(-50%) scale(0.95);
    }

    @media (max-width: 768px) {
        .lab-card {
            flex: 0 0 88vw;
            height: 350px;
        }
        .lab-title {
            font-size: 1.5rem;
        }
        .lab-cta-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .lab-nav-btn {
            width: 40px;
            height: 40px;
        }
        .lab-nav-btn.prev-btn {
            left: 8px;
        }
        .lab-nav-btn.next-btn {
            right: 8px;
        }
    }
</style>

<!-- Sesi: Laboratorium Fakultas (100% Simetris Instant Buffer dengan Tombol Navigasi Kiri & Kanan) -->
<div class="section-wrapper" id="section-lab">
    <div class="lab-container">
        
        <div class="lab-header">
            <div>
                <h1>LABORATORIUM FAKULTAS</h1>
                <p>Fasilitas laboratorium di fakultas industri kreatif</p>
            </div>
        </div>

        <div class="lab-viewport" id="labViewport">
            <!-- Tombol Navigasi Kiri & Kanan -->
            <button class="lab-nav-btn prev-btn" id="labPrevBtn" aria-label="Slide Kiri" title="Foto Sebelumnya">
                <svg viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
            </button>
            <button class="lab-nav-btn next-btn" id="labNextBtn" aria-label="Slide Kanan" title="Foto Selanjutnya">
                <svg viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
            </button>

            <div class="lab-track" id="labTrack">
                
                <!-- Card Index 0: Lab 2 Aula (Far Left) -->
                <div class="lab-card" data-lab="aula">
                    <img src="<?= file_exists(FCPATH . 'assets/images/Aula1.jpg') ? base_url('assets/images/Aula1.jpg') : (file_exists(FCPATH . 'assets/images/aula.jpg') ? base_url('assets/images/aula.jpg') : 'https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1000&auto=format&fit=crop') ?>" alt="Aula Utama Fakultas" class="lab-card-bg-img">
                    <div class="lab-card-overlay">
                        <span class="badge-3d-tag">
                            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            3D Model
                        </span>
                        <div class="lab-card-bottom-info">
                            <h3 class="lab-title">Aula Utama Fakultas</h3>
                            <div class="lab-cta-row">
                                <a href="<?= site_url('dashboard/lab_detail/aula') ?>" class="btn-apple-tv">Lihat Aula &rarr;</a>
                                <div class="lab-apple-desc-inline">
                                    <strong>Acara &amp; Seminar</strong> • Kapasitas 300+ orang dengan Sound System pro &amp; Stage LED.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Index 1: Lab 3 Cintiq (LEFT PEEKING CARD - 100% PRE-RENDERED INSTAN) -->
                <div class="lab-card" data-lab="cintiq">
                    <img src="<?= file_exists(FCPATH . 'assets/images/sintiq.jpg') ? base_url('assets/images/sintiq.jpg') : (file_exists(FCPATH . 'assets/images/cintiq.jpg') ? base_url('assets/images/cintiq.jpg') : 'https://images.unsplash.com/photo-1626785774573-4b799315345d?q=80&w=1000&auto=format&fit=crop') ?>" alt="Lab Tablet Cintiq" class="lab-card-bg-img">
                    <div class="lab-card-overlay">
                        <span class="badge-3d-tag">
                            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            3D Model
                        </span>
                        <div class="lab-card-bottom-info">
                            <h3 class="lab-title">Lab Tablet Cintiq</h3>
                            <div class="lab-cta-row">
                                <a href="<?= site_url('dashboard/lab_detail/cintiq') ?>" class="btn-apple-tv">Lihat Lab &rarr;</a>
                                <div class="lab-apple-desc-inline">
                                    <strong>Desain &amp; Animasi 2D/3D</strong> • Wacom Cintiq Pro Pen Display Stylus 8K.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Index 2: Lab 1 Multimedia & Game (ACTIVE CENTER CARD AT START) -->
                <div class="lab-card active-card" data-lab="multimedia">
                    <img src="<?= file_exists(FCPATH . 'assets/images/multimedia.jpg') ? base_url('assets/images/multimedia.jpg') : 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1000&auto=format&fit=crop' ?>" alt="Lab Multimedia &amp; Game" class="lab-card-bg-img">
                    <div class="lab-card-overlay">
                        <span class="badge-3d-tag">
                            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            3D Model
                        </span>
                        <div class="lab-card-bottom-info">
                            <h3 class="lab-title">Lab Multimedia &amp; Game</h3>
                            <div class="lab-cta-row">
                                <a href="<?= site_url('dashboard/lab_detail/multimedia') ?>" class="btn-apple-tv">Lihat Lab &rarr;</a>
                                <div class="lab-apple-desc-inline">
                                    <strong>Game &amp; VR</strong> • 36 Workstation PC RTX GPU untuk animasi digital &amp; 3D modelling.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Index 3: Lab 2 Aula (RIGHT PEEKING CARD - 100% PRE-RENDERED INSTAN) -->
                <div class="lab-card" data-lab="aula">
                    <img src="<?= file_exists(FCPATH . 'assets/images/Aula1.jpg') ? base_url('assets/images/Aula1.jpg') : (file_exists(FCPATH . 'assets/images/aula.jpg') ? base_url('assets/images/aula.jpg') : 'https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1000&auto=format&fit=crop') ?>" alt="Aula Utama Fakultas" class="lab-card-bg-img">
                    <div class="lab-card-overlay">
                        <span class="badge-3d-tag">
                            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            3D Model
                        </span>
                        <div class="lab-card-bottom-info">
                            <h3 class="lab-title">Aula Utama Fakultas</h3>
                            <div class="lab-cta-row">
                                <a href="<?= site_url('dashboard/lab_detail/aula') ?>" class="btn-apple-tv">Lihat Aula &rarr;</a>
                                <div class="lab-apple-desc-inline">
                                    <strong>Acara &amp; Seminar</strong> • Kapasitas 300+ orang dengan Sound System pro &amp; Stage LED.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Index 4: Lab 3 Cintiq -->
                <div class="lab-card" data-lab="cintiq">
                    <img src="<?= file_exists(FCPATH . 'assets/images/sintiq.jpg') ? base_url('assets/images/sintiq.jpg') : (file_exists(FCPATH . 'assets/images/cintiq.jpg') ? base_url('assets/images/cintiq.jpg') : 'https://images.unsplash.com/photo-1626785774573-4b799315345d?q=80&w=1000&auto=format&fit=crop') ?>" alt="Lab Tablet Cintiq" class="lab-card-bg-img">
                    <div class="lab-card-overlay">
                        <span class="badge-3d-tag">
                            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            3D Model
                        </span>
                        <div class="lab-card-bottom-info">
                            <h3 class="lab-title">Lab Tablet Cintiq</h3>
                            <div class="lab-cta-row">
                                <a href="<?= site_url('dashboard/lab_detail/cintiq') ?>" class="btn-apple-tv">Lihat Lab &rarr;</a>
                                <div class="lab-apple-desc-inline">
                                    <strong>Desain &amp; Animasi 2D/3D</strong> • Wacom Cintiq Pro Pen Display Stylus 8K.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Index 5: Lab 1 Multimedia & Game -->
                <div class="lab-card" data-lab="multimedia">
                    <img src="<?= file_exists(FCPATH . 'assets/images/multimedia.jpg') ? base_url('assets/images/multimedia.jpg') : 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1000&auto=format&fit=crop' ?>" alt="Lab Multimedia &amp; Game" class="lab-card-bg-img">
                    <div class="lab-card-overlay">
                        <span class="badge-3d-tag">
                            <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                            3D Model
                        </span>
                        <div class="lab-card-bottom-info">
                            <h3 class="lab-title">Lab Multimedia &amp; Game</h3>
                            <div class="lab-cta-row">
                                <a href="<?= site_url('dashboard/lab_detail/multimedia') ?>" class="btn-apple-tv">Lihat Lab &rarr;</a>
                                <div class="lab-apple-desc-inline">
                                    <strong>Game &amp; VR</strong> • 36 Workstation PC RTX GPU untuk animasi digital &amp; 3D modelling.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Dynamic Orange Pill Progress Lines (PERSIS TANGKAPAN LAYAR) -->
        <div class="lab-indicators" id="labIndicators">
            <span class="lab-indicator-pill active" data-lab="multimedia" title="Lab Multimedia &amp; Game"></span>
            <span class="lab-indicator-pill" data-lab="aula" title="Aula Utama Fakultas"></span>
            <span class="lab-indicator-pill" data-lab="cintiq" title="Lab Tablet Cintiq"></span>
        </div>

    </div>
</div>

<script>
    let isMoving = false;
    let isDragging = false;
    let startX = 0;
    let dragOffset = 0;
    let baseTargetX = 0;
    const CENTER_INDEX = 2; // Index DOM 2 adalah posisi simetris kartu tengah (Index 1 kiri, Index 3 kanan)

    function getCardGeometry() {
        const track = document.getElementById('labTrack');
        if (!track) return { cardWidth: 820, stepOffset: 844 };
        const cards = track.querySelectorAll('.lab-card');
        if (!cards.length) return { cardWidth: 820, stepOffset: 844 };

        const cardWidth = cards[0].getBoundingClientRect().width;
        let gap = 24;
        if (cards.length > 1) {
            const r0 = cards[0].getBoundingClientRect();
            const r1 = cards[1].getBoundingClientRect();
            gap = r1.left - r0.right;
        }
        return { cardWidth, stepOffset: cardWidth + gap };
    }

    function getTargetXForIndex(domIndex = CENTER_INDEX) {
        const viewport = document.getElementById('labViewport');
        const track = document.getElementById('labTrack');
        if (!viewport || !track) return 0;
        const cards = track.querySelectorAll('.lab-card');
        if (!cards.length) return 0;

        const { cardWidth, stepOffset } = getCardGeometry();
        const viewportWidth = viewport.clientWidth;
        return (viewportWidth / 2) - (domIndex * stepOffset + cardWidth / 2);
    }

    function setTrackTransform(x, animate = true) {
        const track = document.getElementById('labTrack');
        const viewport = document.getElementById('labViewport');
        if (!track || !viewport) return;

        if (animate) {
            track.style.transition = 'transform 0.48s cubic-bezier(0.16, 1, 0.3, 1)';
        } else {
            track.style.transition = 'none';
        }

        track.style.transform = `translate3d(${x}px, 0, 0)`;

        // Highlight active center card & orange pill indicators secara REAL-TIME (0ms delay)
        const cards = track.querySelectorAll('.lab-card');
        const { cardWidth, stepOffset } = getCardGeometry();
        const viewportCenter = viewport.clientWidth / 2;

        let closestIndex = CENTER_INDEX;
        let minDistance = Infinity;

        cards.forEach((card, idx) => {
            const cardCenterX = x + (idx * stepOffset + cardWidth / 2);
            const dist = Math.abs(viewportCenter - cardCenterX);
            if (dist < minDistance) {
                minDistance = dist;
                closestIndex = idx;
            }
        });

        cards.forEach((card, idx) => {
            card.classList.toggle('active-card', idx === closestIndex);
        });

        // Sync Dynamic Orange Pill Line Indicators (Persis Tangkapan Layar)
        const activeCard = cards[closestIndex];
        const activeLabKey = activeCard ? activeCard.getAttribute('data-lab') : 'multimedia';

        const pills = document.querySelectorAll('#labIndicators .lab-indicator-pill');
        pills.forEach(pill => {
            const pillLabKey = pill.getAttribute('data-lab');
            pill.classList.toggle('active', pillLabKey === activeLabKey);
        });
    }

    function renderTrackPosition(domIndex = CENTER_INDEX, animate = true) {
        const targetX = getTargetXForIndex(domIndex);
        setTrackTransform(targetX, animate);
    }

    function shiftNext() {
        if (isMoving) return;
        isMoving = true;
        const track = document.getElementById('labTrack');
        if (!track) return;

        renderTrackPosition(3, true);

        const onEnd = (e) => {
            if (e.target !== track) return;
            track.removeEventListener('transitionend', onEnd);

            track.appendChild(track.firstElementChild);

            renderTrackPosition(CENTER_INDEX, false);
            void track.offsetHeight;

            isMoving = false;
        };

        track.addEventListener('transitionend', onEnd);
    }

    function shiftPrev() {
        if (isMoving) return;
        isMoving = true;
        const track = document.getElementById('labTrack');
        if (!track) return;

        renderTrackPosition(1, true);

        const onEnd = (e) => {
            if (e.target !== track) return;
            track.removeEventListener('transitionend', onEnd);

            track.insertBefore(track.lastElementChild, track.firstElementChild);

            renderTrackPosition(CENTER_INDEX, false);
            void track.offsetHeight;

            isMoving = false;
        };

        track.addEventListener('transitionend', onEnd);
    }

    // 1:1 REAL-TIME PHYSICAL DRAG ENGINE
    function onDragStart(clientX) {
        if (isMoving) return;
        isDragging = true;
        startX = clientX;
        dragOffset = 0;
        baseTargetX = getTargetXForIndex(CENTER_INDEX);
        const viewport = document.getElementById('labViewport');
        if (viewport) viewport.classList.add('active-drag');
    }

    function onDragMove(clientX) {
        if (!isDragging) return;
        dragOffset = clientX - startX;
        const currentX = baseTargetX + dragOffset;
        setTrackTransform(currentX, false);
    }

    function onDragEnd() {
        if (!isDragging) return;
        isDragging = false;
        const viewport = document.getElementById('labViewport');
        if (viewport) viewport.classList.remove('active-drag');

        if (dragOffset < -50) {
            shiftNext();
        } else if (dragOffset > 50) {
            shiftPrev();
        } else {
            renderTrackPosition(CENTER_INDEX, true);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const track = document.getElementById('labTrack');
        const viewport = document.getElementById('labViewport');

        if (track && viewport) {
            renderTrackPosition(CENTER_INDEX, false);

            window.addEventListener('resize', () => renderTrackPosition(CENTER_INDEX, false));
            window.addEventListener('load', () => renderTrackPosition(CENTER_INDEX, false));

            // POINTER EVENTS (1:1 Real-Time Mouse & Touchpad Dragging)
            viewport.addEventListener('pointerdown', (e) => {
                onDragStart(e.clientX);
            });

            window.addEventListener('pointermove', (e) => {
                onDragMove(e.clientX);
            });

            window.addEventListener('pointerup', () => {
                onDragEnd();
            });

            window.addEventListener('pointercancel', () => {
                onDragEnd();
            });

            // Trackpad horizontal wheel swipe
            let wheelThrottle = false;
            viewport.addEventListener('wheel', (e) => {
                if (Math.abs(e.deltaX) > Math.abs(e.deltaY) && Math.abs(e.deltaX) > 15) {
                    e.preventDefault();
                    if (!wheelThrottle && !isMoving) {
                        wheelThrottle = true;
                        if (e.deltaX > 0) {
                            shiftNext();
                        } else {
                            shiftPrev();
                        }
                        setTimeout(() => { wheelThrottle = false; }, 500);
                    }
                }
            }, { passive: false });

            // Klik Indikator Garis Pill Oranye (Persis Screenshot)
            document.querySelectorAll('#labIndicators .lab-indicator-pill').forEach(pill => {
                pill.addEventListener('click', () => {
                    if (isMoving) return;
                    const targetLab = pill.getAttribute('data-lab');
                    const cards = Array.from(track.querySelectorAll('.lab-card'));
                    const currentCenterLab = cards[CENTER_INDEX].getAttribute('data-lab');

                    if (targetLab === currentCenterLab) return;

                    if (cards[3] && cards[3].getAttribute('data-lab') === targetLab) {
                        shiftNext();
                    } else if (cards[1] && cards[1].getAttribute('data-lab') === targetLab) {
                        shiftPrev();
                    }
                });
            });

            // Tombol Navigasi Kanan & Kiri (Glassmorphism Apple TV)
            const prevBtn = document.getElementById('labPrevBtn');
            const nextBtn = document.getElementById('labNextBtn');

            if (prevBtn) {
                prevBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    shiftPrev();
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    shiftNext();
                });
            }
        }
    });
</script>
