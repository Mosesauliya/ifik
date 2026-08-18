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

    /* Styling Sesi Laboratorium Fakultas (Pergeseran Super Lambat 1.6s Ultra Silk-Smooth) */
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
        padding: 20px 32px 14px 32px;
        z-index: 2;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 28px;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.08);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.5s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .lab-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 6px;
    }

    .lab-header h1 {
        font-size: 2.1rem;
        font-weight: 800;
        color: var(--text-color);
        letter-spacing: -1px;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .lab-header p {
        color: #64748b;
        font-size: 0.92rem;
        font-weight: 500;
    }

    /* Viewport Container */
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

    /* Track Pergeseran Super Lambat & Ultra Silk-Smooth */
    .lab-track {
        display: flex;
        gap: 14px;
        align-items: center;
        transition: transform 1.0s cubic-bezier(0.25, 1, 0.35, 1);
        will-change: transform;
    }

    /* Card Crisp Clear */
    .lab-card {
        flex: 0 0 65vw;
        max-width: 820px;
        height: 440px;
        border-radius: 28px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        position: relative;
        overflow: hidden;
        background: #0f172a;
        opacity: 1 !important;
        transform: translateZ(0);
        transition: border-color 0.4s ease, opacity 0.4s ease;
        will-change: transform;
        cursor: pointer;
    }

    .lab-card.active-card {
        opacity: 1 !important;
        border-color: rgba(234, 88, 12, 0.6);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.45);
    }

    /* Foto Jernih Murni */
    .lab-card-bg-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 1;
        transform: none !important;
        filter: brightness(0.9) !important;
        pointer-events: none;
    }

    /* Gradient Overlay */
    .lab-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.82) 0%, rgba(15, 23, 42, 0.35) 45%, rgba(15, 23, 42, 0.88) 100%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 32px 36px;
    }

    /* Content Flat Clean */
    .lab-card-top-content {
        max-width: 520px;
        text-align: left;
        transform: none !important;
        opacity: 1 !important;
    }

    .lab-card-title-text {
        font-size: 1.65rem;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 8px 0;
        letter-spacing: -0.4px;
        line-height: 1.25;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
    }

    .lab-card-desc-text {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.95rem;
        line-height: 1.4;
        font-weight: 500;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
    }

    /* 3D Model Tag */
    .badge-3d-tag-overlay {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        font-size: 0.76rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .badge-3d-tag-overlay svg {
        width: 12px;
        height: 12px;
        fill: #f97316;
    }

    /* Bottom Action Row & Button */
    .lab-card-bottom-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    .btn-apple-action {
        background: #ffffff;
        color: #0f172a;
        font-weight: 800;
        font-size: 0.88rem;
        padding: 10px 26px;
        border-radius: 24px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        transform: none !important;
        opacity: 1 !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-apple-action:hover {
        background: #ea580c;
        color: #ffffff;
    }

    /* Apple Controls Capsule Bar & Seek Progress Fill Oranye (6 Detik Fill) */
    .apple-controls-capsule-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        margin-top: 14px;
        z-index: 10;
    }

    .apple-controls-capsule {
        background: rgba(30, 41, 59, 0.45);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 10px 22px;
        border-radius: 30px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
    }

    .apple-indicator-dots {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .apple-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.35);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: width 2.5s cubic-bezier(0.25, 1, 0.35, 1), background-color 0.6s ease, border-color 0.6s ease;
    }

    .apple-dot:hover {
        background: rgba(255, 255, 255, 0.6);
    }

    /* Active Pill Bar Expands to 38px Seek Progress Track */
    .apple-dot.active {
        width: 38px;
        height: 8px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(249, 115, 22, 0.4);
    }

    /* Inside Filler Animation (Progres Oranye Tepat 6 Detik) */
    .apple-dot-fill {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 0%;
        background: #f97316;
        background: linear-gradient(135deg, #ff8c00 0%, #ea580c 100%);
        border-radius: 6px;
        box-shadow: 0 0 10px rgba(249, 115, 22, 0.8);
        pointer-events: none;
    }

    .apple-dot.active .apple-dot-fill.animating {
        animation: appleSeekProgress 6s linear forwards;
    }

    @keyframes appleSeekProgress {
        from { width: 0%; }
        to { width: 100%; }
    }

    /* Play / Pause Toggle Button */
    .apple-play-pause-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(30, 41, 59, 0.45);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: transform 0.25s ease, background-color 0.25s ease, border-color 0.25s ease;
    }

    .apple-play-pause-btn svg {
        width: 15px;
        height: 15px;
        fill: #ffffff;
        transition: fill 0.25s ease;
    }

    .apple-play-pause-btn:hover {
        background: #ea580c;
        border-color: rgba(234, 88, 12, 0.6);
        transform: scale(1.08);
    }

    /* Premium Glassmorphic Left/Right Navigation Arrow Buttons */
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
        transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), background-color 0.3s ease, box-shadow 0.3s ease;
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
            height: 360px;
        }
        .lab-card-title-text {
            font-size: 1.25rem;
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

<!-- Sesi: Laboratorium Fakultas (Pergeseran Super Lambat 2.5s & Ultra Silk-Smooth) -->
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

            <!-- 7 Slot Kartu Flat Presisi Tengah -->
            <div class="lab-track" id="labTrack">
                <div class="lab-card" id="cardSlot0"></div>
                <div class="lab-card" id="cardSlot1"></div>
                <div class="lab-card" id="cardSlot2"></div>
                <div class="lab-card active-card" id="cardSlot3"></div>
                <div class="lab-card" id="cardSlot4"></div>
                <div class="lab-card" id="cardSlot5"></div>
                <div class="lab-card" id="cardSlot6"></div>
            </div>
        </div>

        <!-- Apple Controls Capsule Bar -->
        <div class="apple-controls-capsule-wrapper">
            <div class="apple-controls-capsule">
                <div class="apple-indicator-dots" id="labIndicators">
                    <div class="apple-dot active" data-lab="multimedia" title="Lab Multimedia &amp; Game">
                        <div class="apple-dot-fill"></div>
                    </div>
                    <div class="apple-dot" data-lab="aula" title="Aula Utama Fakultas">
                        <div class="apple-dot-fill"></div>
                    </div>
                    <div class="apple-dot" data-lab="cintiq" title="Lab Tablet Cintiq">
                        <div class="apple-dot-fill"></div>
                    </div>
                    <div class="apple-dot" data-lab="greenscreen" title="Lab Green Screen Studio">
                        <div class="apple-dot-fill"></div>
                    </div>
                    <div class="apple-dot" data-lab="incubator" title="Lab Inkubator Bisnis &amp; Tech">
                        <div class="apple-dot-fill"></div>
                    </div>
                    <div class="apple-dot" data-lab="mac" title="Lab Workstation Apple Mac">
                        <div class="apple-dot-fill"></div>
                    </div>
                </div>
            </div>

            <!-- Tombol Play / Pause -->
            <button class="apple-play-pause-btn" id="labAutoPlayBtn" title="Auto Play / Pause">
                <svg id="playPauseIcon" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </button>
        </div>

    </div>
</div>

<script>
    // DATA 6 LABORATORIUM UNIK
    const LAB_DATA = {
        multimedia: {
            key: 'multimedia',
            title: 'Lab Multimedia & Game',
            desc: '36 Workstation PC RTX GPU untuk animasi digital &amp; 3D modelling.',
            btnText: 'Lihat Lab &rarr;',
            url: '<?= site_url('dashboard/lab_detail/multimedia') ?>',
            img: '<?= base_url('assets/images/multimedia.jpg') ?>'
        },
        aula: {
            key: 'aula',
            title: 'Aula Utama Fakultas',
            desc: 'Kapasitas 300+ orang dengan Sound System pro &amp; Stage LED.',
            btnText: 'Lihat Aula &rarr;',
            url: '<?= site_url('dashboard/lab_detail/aula') ?>',
            img: '<?= file_exists(FCPATH . 'assets/images/Aula1.jpg') ? base_url('assets/images/Aula1.jpg') : base_url('assets/images/aula.jpg') ?>'
        },
        cintiq: {
            key: 'cintiq',
            title: 'Lab Tablet Cintiq',
            desc: 'Studio Wacom Cintiq Pro 8K Pen Display untuk komik &amp; 2D art.',
            btnText: 'Lihat Lab &rarr;',
            url: '<?= site_url('dashboard/lab_detail/cintiq') ?>',
            img: '<?= file_exists(FCPATH . 'assets/images/sintiq.jpg') ? base_url('assets/images/sintiq.jpg') : base_url('assets/images/cintiq.jpg') ?>'
        },
        greenscreen: {
            key: 'greenscreen',
            title: 'Lab Green Screen Studio',
            desc: 'Dinding Cyclorama Chroma Key &amp; Lighting Rig DMX.',
            btnText: 'Lihat Lab &rarr;',
            url: '<?= site_url('dashboard/lab_detail/greenscreen') ?>',
            img: '<?= base_url('assets/images/greenscreen.jpg') ?>'
        },
        incubator: {
            key: 'incubator',
            title: 'Lab Inkubator Bisnis & Tech',
            desc: 'Ruang Pitching Investor, Co-Working Space, &amp; Wifi 6E.',
            btnText: 'Lihat Lab &rarr;',
            url: '<?= site_url('dashboard/lab_detail/incubator') ?>',
            img: '<?= base_url('assets/images/incubator.jpg') ?>'
        },
        mac: {
            key: 'mac',
            title: 'Lab Workstation Apple Mac',
            desc: 'Apple Mac Studio M2 Max &amp; Studio Display Retina 5K.',
            btnText: 'Lihat Lab &rarr;',
            url: '<?= site_url('dashboard/lab_detail/mac') ?>',
            img: '<?= base_url('assets/images/mac.jpg') ?>'
        }
    };

    const LAB_KEYS = ['multimedia', 'aula', 'cintiq', 'greenscreen', 'incubator', 'mac'];
    const TOTAL_LABS = LAB_KEYS.length;
    let activeLabIndex = 0; // Mulai dari Multimedia (Indeks 0)

    let isMoving = false;
    let isDragging = false;
    let isPlaying = false;
    let startX = 0;
    let dragOffset = 0;
    let baseTargetX = 0;
    const CENTER_SLOT_INDEX = 3; // Slot DOM Index 3 SELALU kartu aktif pas di tengah

    function buildCardHTML(labKey) {
        const data = LAB_DATA[labKey];
        if (!data) return '';
        return `
            <img src="${data.img}" alt="${data.title}" class="lab-card-bg-img">
            <div class="lab-card-overlay">
                <div class="lab-card-top-content">
                    <span class="badge-3d-tag-overlay">
                        <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        3D Model
                    </span>
                    <h3 class="lab-card-title-text" style="margin-top: 14px;">${data.title}</h3>
                    <div class="lab-card-desc-text">${data.desc}</div>
                </div>

                <div class="lab-card-bottom-row">
                    <div></div>
                    <a href="${data.url}" class="btn-apple-action">${data.btnText}</a>
                </div>
            </div>
        `;
    }

    // UPDATE INDIKATOR TITIK & SEEK BAR SECARA SIMULTAN DENGAN KURVA SMOOTH 1.2S
    function syncIndicators(targetIdx) {
        const targetLabKey = LAB_KEYS[targetIdx];
        const pills = document.querySelectorAll('#labIndicators .apple-dot');
        
        pills.forEach(pill => {
            const isMatch = pill.getAttribute('data-lab') === targetLabKey;
            pill.classList.toggle('active', isMatch);
            const fill = pill.querySelector('.apple-dot-fill');
            if (fill) {
                fill.classList.remove('animating');
                fill.style.width = '0%';
            }
        });

        const activeDot = document.querySelector(`#labIndicators .apple-dot[data-lab="${targetLabKey}"]`);
        if (activeDot) {
            const activeFill = activeDot.querySelector('.apple-dot-fill');
            if (activeFill && isPlaying) {
                void activeFill.offsetWidth; // Force reflow
                const onAnimationEnd = () => {
                    activeFill.removeEventListener('animationend', onAnimationEnd);
                    if (isPlaying && !isMoving && !isDragging) {
                        setTimeout(() => {
                            if (isPlaying && !isMoving && !isDragging) {
                                shiftNext();
                            }
                        }, 500); // Jeda 0,5 detik setelah seekbar selesai sebelum kartu perpindah
                    }
                };
                activeFill.addEventListener('animationend', onAnimationEnd);
                activeFill.classList.add('animating');
            }
        }
    }

    function updateAllSlots(activeIdx) {
        const slots = [
            document.getElementById('cardSlot0'),
            document.getElementById('cardSlot1'),
            document.getElementById('cardSlot2'),
            document.getElementById('cardSlot3'),
            document.getElementById('cardSlot4'),
            document.getElementById('cardSlot5'),
            document.getElementById('cardSlot6')
        ];

        const relativeIndices = [-3, -2, -1, 0, 1, 2, 3];

        relativeIndices.forEach((rel, slotIdx) => {
            const computedLabIdx = (activeIdx + rel + TOTAL_LABS * 100) % TOTAL_LABS;
            const labKey = LAB_KEYS[computedLabIdx];
            const slotEl = slots[slotIdx];

            if (slotEl) {
                if (slotEl.getAttribute('data-lab') !== labKey) {
                    slotEl.setAttribute('data-lab', labKey);
                    slotEl.innerHTML = buildCardHTML(labKey);
                }
                slotEl.classList.toggle('active-card', slotIdx === CENTER_SLOT_INDEX);
            }
        });
    }

    function getCardGeometry() {
        const track = document.getElementById('labTrack');
        if (!track) return { cardWidth: 820, stepOffset: 834 };
        const cards = track.querySelectorAll('.lab-card');
        if (!cards.length) return { cardWidth: 820, stepOffset: 834 };

        const cardWidth = cards[0].getBoundingClientRect().width;
        let gap = 14;
        if (cards.length > 1) {
            const r0 = cards[0].getBoundingClientRect();
            const r1 = cards[1].getBoundingClientRect();
            gap = r1.left - r0.right;
        }
        return { cardWidth, stepOffset: cardWidth + gap };
    }

    function getTargetXForIndex(slotIndex = CENTER_SLOT_INDEX) {
        const viewport = document.getElementById('labViewport');
        const track = document.getElementById('labTrack');
        if (!viewport || !track) return 0;
        const cards = track.querySelectorAll('.lab-card');
        if (!cards.length) return 0;

        const { cardWidth, stepOffset } = getCardGeometry();
        const viewportWidth = viewport.clientWidth;
        return (viewportWidth / 2) - (slotIndex * stepOffset + cardWidth / 2);
    }

    function setTrackTransform(x, animate = true) {
        const track = document.getElementById('labTrack');
        const viewport = document.getElementById('labViewport');
        if (!track || !viewport) return;

        if (animate) {
            // PERGESERAN SMOOTH 1.0s ULTRA SILK-SMOOTH
            track.style.transition = 'transform 1.0s cubic-bezier(0.25, 1, 0.35, 1)';
        } else {
            track.style.transition = 'none';
        }

        track.style.transform = `translate3d(${x}px, 0, 0)`;

        const cards = track.querySelectorAll('.lab-card');
        const { cardWidth, stepOffset } = getCardGeometry();
        const viewportCenter = viewport.clientWidth / 2;

        let closestIndex = CENTER_SLOT_INDEX;
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
    }

    function renderTrackPosition(slotIndex = CENTER_SLOT_INDEX, animate = true) {
        const targetX = getTargetXForIndex(slotIndex);
        setTrackTransform(targetX, animate);
    }

    function startAutoPlay() {
        isPlaying = true;
        const playPauseIcon = document.getElementById('playPauseIcon');
        if (playPauseIcon) {
            playPauseIcon.innerHTML = `<path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>`; // Icon Pause
        }
        syncIndicators(activeLabIndex);
    }

    function pauseAutoPlay() {
        isPlaying = false;
        const playPauseIcon = document.getElementById('playPauseIcon');
        if (playPauseIcon) {
            playPauseIcon.innerHTML = `<path d="M8 5v14l11-7z"/>`; // Icon Play
        }

        const dots = document.querySelectorAll('#labIndicators .apple-dot');
        dots.forEach(dot => {
            const fill = dot.querySelector('.apple-dot-fill');
            if (fill) {
                fill.classList.remove('animating');
                fill.style.width = '0%';
            }
        });
    }

    function toggleAutoPlay() {
        if (isPlaying) {
            pauseAutoPlay();
        } else {
            startAutoPlay();
        }
    }

    // NAVIGASI SMOOTH UNTUK SETIAP PERPINDAHAN MANUAL ATAU OTOMATIS
    function navigateToIndex(targetIdx) {
        if (isMoving || targetIdx === activeLabIndex || targetIdx < 0 || targetIdx >= TOTAL_LABS) return;
        isMoving = true;
        const track = document.getElementById('labTrack');
        if (!track) {
            isMoving = false;
            return;
        }

        // Hitung jarak terdekat (forward/backward)
        let diffForward = (targetIdx - activeLabIndex + TOTAL_LABS) % TOTAL_LABS;
        let diffBackward = (activeLabIndex - targetIdx + TOTAL_LABS) % TOTAL_LABS;

        let targetSlotIndex;
        if (diffForward <= diffBackward) {
            targetSlotIndex = CENTER_SLOT_INDEX + diffForward;
        } else {
            targetSlotIndex = CENTER_SLOT_INDEX - diffBackward;
        }

        // Langsung update titik indikator & seek bar secara bersamaan
        syncIndicators(targetIdx);

        // Mulai meluncurkan track dengan animasi smooth
        renderTrackPosition(targetSlotIndex, true);

        const onEnd = (e) => {
            if (e.target !== track) return;
            track.removeEventListener('transitionend', onEnd);

            const cards = track.querySelectorAll('.lab-card');
            cards.forEach(c => c.style.transition = 'none');

            activeLabIndex = targetIdx;
            updateAllSlots(activeLabIndex);

            renderTrackPosition(CENTER_SLOT_INDEX, false);

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    cards.forEach(c => c.style.transition = '');
                    isMoving = false;
                });
            });
        };

        track.addEventListener('transitionend', onEnd);
    }

    function shiftNext() {
        const nextLabIndex = (activeLabIndex + 1) % TOTAL_LABS;
        navigateToIndex(nextLabIndex);
    }

    function shiftPrev() {
        const prevLabIndex = (activeLabIndex - 1 + TOTAL_LABS) % TOTAL_LABS;
        navigateToIndex(prevLabIndex);
    }

    // 1:1 REAL-TIME PHYSICAL DRAG ENGINE
    function onDragStart(clientX) {
        if (isMoving) return;
        isDragging = true;
        startX = clientX;
        dragOffset = 0;
        baseTargetX = getTargetXForIndex(CENTER_SLOT_INDEX);
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
            renderTrackPosition(CENTER_SLOT_INDEX, true);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const track = document.getElementById('labTrack');
        const viewport = document.getElementById('labViewport');

        if (track && viewport) {
            updateAllSlots(activeLabIndex);
            renderTrackPosition(CENTER_SLOT_INDEX, false);

            window.addEventListener('resize', () => renderTrackPosition(CENTER_SLOT_INDEX, false));
            window.addEventListener('load', () => renderTrackPosition(CENTER_SLOT_INDEX, false));

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

            // Klik Indikator Titik Apple
            document.querySelectorAll('#labIndicators .apple-dot').forEach(pill => {
                pill.addEventListener('click', () => {
                    const targetLab = pill.getAttribute('data-lab');
                    const targetIdx = LAB_KEYS.indexOf(targetLab);
                    if (targetIdx !== -1) {
                        navigateToIndex(targetIdx);
                    }
                });
            });

            // Klik langsung pada kartu di samping untuk berpindah dengan animasi smooth
            document.querySelectorAll('.lab-card').forEach((card) => {
                card.addEventListener('click', (e) => {
                    if (e.target.closest('.btn-apple-action') || e.target.closest('.lab-nav-btn')) return;

                    const targetLab = card.getAttribute('data-lab');
                    const targetIdx = LAB_KEYS.indexOf(targetLab);
                    if (targetIdx !== -1 && targetIdx !== activeLabIndex) {
                        navigateToIndex(targetIdx);
                    }
                });
            });

            // Tombol Navigasi Kanan & Kiri
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

            // Tombol Auto Play / Pause
            const playPauseBtn = document.getElementById('labAutoPlayBtn');
            if (playPauseBtn) {
                playPauseBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleAutoPlay();
                });
            }
        }
    });
</script>
