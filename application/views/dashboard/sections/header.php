<style>
    /* Styling khusus Sesi 1 */
    #section-carousel {
        position: relative;
        background-color: #d97706; /* Fallback color asli */
    }

    /* Horizontal Carousel Snapping (Hijacking kiri-kanan) */
    .carousel-container {
        display: flex;
        overflow-x: scroll;
        scroll-snap-type: x mandatory;
        width: 100%;
        height: 100%;
        scrollbar-width: none;
        scroll-behavior: smooth;
    }
    .carousel-container::-webkit-scrollbar { display: none; }

    /* Tiap Layar/Slide Carousel */
    .carousel-slide {
        flex: 0 0 100vw;
        height: 100%;
        scroll-snap-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
        background-image: url('<?= base_url("assets/images/background.png") ?>');
        background-size: cover; 
        background-position: center;
    }

    /* Khusus Slide 1 background-nya fakultas.jpg tanpa filter */
    .carousel-slide.slide-1 {
        background-image: url('<?= base_url("assets/images/Fakultas.jpg") ?>');
    }

    /* Background Video Fullscreen */
    .background-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
    }

    .scroll-hint {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        color: #fff;
        font-size: 0.9rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        animation: bounce 2s infinite;
        z-index: 20;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        font-weight: 700;
    }

    /* Carousel Indicators (Dots) - Modern Glassmorphism Hybrid */
    .carousel-indicators {
        position: absolute;
        bottom: 45px;
        left: 50%;
        transform: translateX(-50%);
        width: 60vw;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        z-index: 30;
    }
    
    .dots-half {
        flex: 1;
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .carousel-indicators .dot {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 8px;
        cursor: pointer;
        opacity: 0.7;
        transition: all 0.3s ease;
        background: rgba(0, 0, 0, 0.45);
        padding: 10px 14px;
        border-radius: 12px;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    
    .carousel-indicators .dot.active, 
    .carousel-indicators .dot:hover {
        opacity: 1;
        background: rgba(0, 0, 0, 0.75);
        border-color: rgba(234, 88, 12, 0.7);
        box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        transform: translateY(-2px);
    }

    .carousel-indicators .dot .dot-label {
        font-size: 0.82rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #fff;
        letter-spacing: 1.2px;
        height: 18px;
        display: flex;
        align-items: center;
    }
    
    .carousel-indicators .dot .dot-track {
        height: 4px;
        width: 100%;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .carousel-indicators .dot .progress {
        position: absolute;
        top: 0; left: 0; height: 100%;
        background: #fff;
        width: 0%;
        border-radius: 4px;
        box-shadow: 0 0 8px #fff, 0 0 15px rgba(255,255,255,0.8);
    }

    /* --- FASILITAS SEGMENTED SEEKBAR & CONTROLS GROUP --- */
    .dot-fasilitas {
        position: relative;
    }

    .dot-label-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        height: 18px;
    }

    .fasilitas-controls-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Tombol Tambah Ruangan Khusus Admin */
    .lab-add-room-btn {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        height: 18px;
        padding: 0 7px;
        border-radius: 12px;
        background: #ea580c;
        border: none;
        color: #ffffff;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.5);
    }

    .lab-add-room-btn:hover {
        background: #ffffff;
        color: #ea580c;
        transform: scale(1.1);
        box-shadow: 0 4px 10px rgba(255, 255, 255, 0.6);
    }

    .lab-add-room-btn svg {
        width: 10px;
        height: 10px;
        fill: currentColor;
    }

    .dot-track-segmented {
        height: 4px;
        width: 100%;
        display: flex;
        gap: 4px;
        border-radius: 4px;
        overflow: hidden;
    }

    .dot-track-segmented .seg {
        flex: 1;
        height: 100%;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .dot-track-segmented .seg:hover {
        background: rgba(255, 255, 255, 0.6);
    }

    .dot-track-segmented .seg .progress {
        position: absolute;
        top: 0; left: 0; height: 100%;
        background: #ffffff;
        width: 0%;
        border-radius: 2px;
        box-shadow: 0 0 8px #ffffff;
    }

    .dot-track-segmented .seg.completed .progress {
        width: 100% !important;
        animation: none !important;
    }

    /* Tombol Play/Pause Kecil di Pinggir Label Fasilitas */
    .lab-play-pause-btn-side {
        width: 18px;
        height: 18px;
        min-width: 18px;
        border-radius: 50%;
        background: #ea580c;
        border: none;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(234, 88, 12, 0.5);
    }

    .lab-play-pause-btn-side:hover {
        background: #ffffff;
        color: #ea580c;
        transform: scale(1.2);
    }

    .lab-play-pause-btn-side svg {
        width: 9px;
        height: 9px;
        fill: currentColor;
    }

    /* --- SLIDE 1 LAYOUT DENGAN PAGINATION TEKS --- */
    .slide1-layout {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        padding: 0 80px;
        display: flex;
        align-items: center;
        z-index: 10;
        pointer-events: none;
    }
    .slide1-text-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
        width: 500px;
        max-width: 90vw;
        z-index: 10;
        margin-top: -40px;
        margin-left: 20px;
    }
    .slide1-title-box {
        background: rgba(255, 255, 255, 0.95);
        padding: 15px 30px;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        backdrop-filter: blur(8px);
        pointer-events: auto;
        width: 100%;
        box-sizing: border-box;
    }
    .slide1-title-box h1 {
        color: #ea580c;
        font-size: 2.1rem;
        font-weight: 800;
        margin: 0;
    }
    .slide1-content-box {
        background: rgba(255, 255, 255, 0.95);
        padding: 22px 28px;
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        color: #334155;
        font-size: 0.85rem;
        line-height: 1.6;
        text-align: justify;
        backdrop-filter: blur(8px);
        pointer-events: auto;
        width: 100%;
        box-sizing: border-box;
    }
    .dekanat-img-right {
        position: absolute;
        bottom: 0;
        right: 20px;
        max-height: 380px;
        z-index: 5;
        pointer-events: none;
    }
    
    /* Pagination Button Teks Deskripsi */
    .header-pagination {
        display: none;
        pointer-events: auto;
        margin-top: 5px;
        align-items: center;
        gap: 8px;
    }
    .header-pagination button {
        background: #ffffff;
        border: none;
        padding: 5px 12px;
        border-radius: 8px;
        font-weight: 700;
        color: #ea580c;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        font-size: 0.75rem;
        transition: all 0.2s;
    }
    .header-pagination button:hover {
        background: #ea580c;
        color: #ffffff;
    }
    .header-pagination span {
        color: #ffffff;
        font-size: 0.8rem;
        font-weight: 800;
        text-shadow: 0 1px 4px rgba(0,0,0,0.8);
    }
    
    @media (max-width: 900px) {
        .slide1-layout { padding: 0 20px; }
        .slide1-text-container { margin-top: 0; width: 100%; }
        .dekanat-img-right { max-height: 250px; opacity: 0.5; }
        .carousel-indicators { width: 90vw; bottom: 25px; }
    }
    
    @keyframes slideProgress {
        0% { width: 0%; }
        100% { width: 100%; }
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translate(-50%, 0); }
        40% { transform: translate(-50%, -10px); }
        60% { transform: translate(-50%, -5px); }
    }
</style>

<!-- Sesi 1: Carousel -->
<div class="section-wrapper" id="section-carousel">

    <!-- Carousel Horizontal (Scroll Hijacking) -->
    <div class="carousel-container">
        <!-- Slide 1 (Overview - Fakultas Industri Kreatif) -->
        <div class="carousel-slide slide-1">
            <div class="slide1-layout">
                <div class="slide1-text-container">
                    <div class="slide1-title-box">
                        <h1><?= htmlspecialchars($header_settings->title ?? 'Fakultas Industri Kreatif') ?></h1>
                    </div>
                    <div class="slide1-content-box" id="headerDescBox">
                        <?= htmlspecialchars($header_settings->description ?? 'Seiring dengan berkembangnya kebutuhan pelayanan untuk mahasiswa, dosen dan pegawai FIK maka diperlukan peningkatan layanan yang mengusung efisiensi dan efektifitas. Ifik lahir dari keresahan dan kesulitan mahasiswa maupun dosen dalam beberapa layanan, antara lain pendaftaran TA, bimbingan online, dokumen online, peminjaman ruangan dan lain sebagainya. Sejak dibuat tahun 2021 oleh tim unit lab FIK, aplikasi berbasis web ini telah digunakan hingga saat ini untuk mempermudah layanan untuk kalangan internal FIK, baik untuk mahasiswa, dosen maupun pegawai FIK.') ?>
                    </div>
                    <div class="header-pagination" id="headerPagination">
                        <button onclick="prevDescPage()">&larr; Prev</button>
                        <span id="headerPageInfo">1/1</span>
                        <button onclick="nextDescPage()">Next &rarr;</button>
                    </div>
                </div>
            </div>
            
            <?php $dekanat_img = $header_settings->dekanat_image ?? 'dekanat2.png'; ?>
            <img src="<?= base_url('assets/images/' . $dekanat_img) ?>" alt="Dekanat" class="dekanat-img-right">
        </div>
        
        <!-- Slide 2 (Fasilitas: Lab Fakultas) -->
        <div class="carousel-slide slide-2" style="position: relative; width: 100vw; height: 100%;">
            <?php $this->load->view('dashboard/sections/lab'); ?>
        </div>
        
        <!-- Slide 3 (Prestasi & Inovasi) -->
        <div class="carousel-slide slide-3">
            <div class="slide1-layout">
                <div class="slide1-text-container">
                    <div class="slide1-title-box">
                        <h1>Prestasi &amp; Inovasi FIK</h1>
                    </div>
                    <div class="slide1-content-box">
                        Fakultas Industri Kreatif secara konsisten mengukir berbagai prestasi baik di tingkat nasional maupun internasional. Melalui fasilitas laboratorium yang canggih dan bimbingan dosen berpengalaman, mahasiswa FIK terus melahirkan karya-karya inovatif di bidang desain, seni, media interaktif, dan teknologi kreatif.
                    </div>
                </div>
            </div>
            <img src="<?= base_url('assets/images/dekanat2.png') ?>" alt="Dekanat" class="dekanat-img-right">
        </div>
    </div>
    
    <!-- Indikator Dots Gabungan -->
    <div class="carousel-indicators" id="carouselDots">
        <!-- Dot 0: Overview -->
        <div class="dot active" data-index="0">
            <span class="dot-label">Overview</span>
            <div class="dot-track"><div class="progress"></div></div>
        </div>

        <!-- Dot 1: Fasilitas (6 Segmen Ruangan) -->
        <div class="dot dot-fasilitas" data-index="1">
            <div class="dot-label-row">
                <span class="dot-label">Fasilitas</span>
                <div class="fasilitas-controls-group">
                    <?php if ($this->session->userdata('role_id') == 1): ?>
                        <a href="<?= base_url('kelolaruangan') ?>" class="lab-add-room-btn" title="Kelola Ruangan">
                            <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                            <span>Ruangan</span>
                        </a>
                    <?php endif; ?>

                    <button class="lab-play-pause-btn-side" id="labAutoPlayBtn" title="Auto Play / Pause">
                        <svg id="playPauseIcon" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </button>
                </div>
            </div>
            <div class="dot-track-segmented" id="labIndicators">
                <?php 
                    $ruangan_with_3d = [];
                    $seen_keys = [];
                    if (!empty($ruangan)) {
                        foreach ($ruangan as $r) {
                            $n = strtolower(trim(isset($r->nama_ruangan) ? $r->nama_ruangan : ''));
                            $c = strtolower(trim(isset($r->kode_ruangan) ? $r->kode_ruangan : ''));
                            if (!empty($r->model_3d) && $n !== 'ss' && $c !== 'ss' && strpos($n, 'test') === false && strpos($n, 'qqq') === false) {
                                if (strpos($n, 'multimedia') !== false) $lab_code = 'multimedia';
                                elseif (strpos($n, 'aula') !== false) $lab_code = 'aula';
                                elseif (strpos($n, 'cintiq') !== false || strpos($n, 'tablet') !== false || strpos($n, 'sablon') !== false) $lab_code = 'cintiq';
                                elseif (strpos($n, 'green') !== false) $lab_code = 'greenscreen';
                                elseif (strpos($n, 'inkubator') !== false || strpos($n, 'incubator') !== false) $lab_code = 'incubator';
                                elseif (strpos($n, 'mac') !== false || strpos($n, '3d printing') !== false) $lab_code = 'mac';
                                else $lab_code = preg_replace('/[^a-z0-9]/', '', $c);

                                if (!empty($lab_code) && !in_array($lab_code, $seen_keys)) {
                                    $seen_keys[] = $lab_code;
                                    $r->mapped_key = $lab_code;
                                    $ruangan_with_3d[] = $r;
                                }
                            }
                        }
                    }

                    $order_keys = ['multimedia', 'aula', 'cintiq', 'greenscreen', 'incubator', 'mac'];
                    usort($ruangan_with_3d, function($a, $b) use ($order_keys) {
                        $posA = array_search($a->mapped_key, $order_keys);
                        $posB = array_search($b->mapped_key, $order_keys);
                        if ($posA === false) $posA = 999;
                        if ($posB === false) $posB = 999;
                        return $posA - $posB;
                    });
                ?>
                <?php if (!empty($ruangan_with_3d)): ?>
                    <?php foreach ($ruangan_with_3d as $idx => $r): ?>
                        <div class="seg <?= $idx === 0 ? 'active' : '' ?>" 
                             data-lab="<?= htmlspecialchars($r->mapped_key) ?>" 
                             data-id="<?= $r->id ?>"
                             title="<?= htmlspecialchars($r->nama_ruangan) ?>">
                            <div class="progress"></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="seg active" data-lab="multimedia" title="Lab Multimedia &amp; Game"><div class="progress"></div></div>
                    <div class="seg" data-lab="aula" title="Aula Utama"><div class="progress"></div></div>
                    <div class="seg" data-lab="cintiq" title="Lab Cintiq"><div class="progress"></div></div>
                    <div class="seg" data-lab="greenscreen" title="Lab Green Screen"><div class="progress"></div></div>
                    <div class="seg" data-lab="incubator" title="Lab Inkubator"><div class="progress"></div></div>
                    <div class="seg" data-lab="mac" title="Lab Mac Workstation"><div class="progress"></div></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Dot 2: Prestasi -->
        <div class="dot" data-index="2">
            <span class="dot-label">Prestasi</span>
            <div class="dot-track"><div class="progress"></div></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.querySelector('.carousel-container');
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('#carouselDots .dot');
        
        let currentIndex = 0;
        let activeProgEndListener = null;

        const goToSlide = (index) => {
            if (!carousel || !slides[index]) return;
            const slideLeftPos = slides[index].offsetLeft;
            carousel.scrollTo({
                left: slideLeftPos,
                behavior: 'smooth'
            });
            updateDots(index);
        };

        window.goToSlide = goToSlide;

        // Video support handler
        document.querySelectorAll('video').forEach((vid) => {
            vid.addEventListener('loadedmetadata', () => {
                const slideIndex = Array.from(slides).indexOf(vid.closest('.carousel-slide'));
                if (currentIndex === slideIndex && dots[slideIndex]) {
                    const activeProg = dots[slideIndex].querySelector('.progress');
                    if (activeProg) {
                        activeProg.style.animation = 'none';
                        activeProg.offsetHeight;
                        activeProg.style.animation = `slideProgress ${vid.duration}s linear forwards`;
                    }
                }
            });
        });

        const updateDots = (index) => {
            currentIndex = index;

            dots.forEach((dot, i) => {
                dot.classList.remove('active');
                const prog = dot.querySelector('.dot-track > .progress');
                if (prog) {
                    prog.style.animation = 'none';
                    prog.offsetHeight;
                    if (i < index) {
                        prog.style.width = '100%';
                    } else {
                        prog.style.width = '0%';
                    }
                }
            });

            if (dots[index]) {
                dots[index].classList.add('active');
            }

            // 3D Logo logic: tampil di Slide 0 dan Slide 2
            const modelContainer = document.getElementById('global-model-container');
            const dashboardContainer = document.querySelector('.dashboard-container');
            if (modelContainer) {
                const currentScrollTop = dashboardContainer ? dashboardContainer.scrollTop : 0;
                const vh = window.innerHeight || 800;
                if (currentScrollTop < vh * 0.45) {
                    modelContainer.style.opacity = (index === 0 || index === 2) ? '1' : '0';
                }
                modelContainer.style.pointerEvents = 'none';
            }

            if (index === 0) {
                const segs = document.querySelectorAll('#labIndicators .seg');
                segs.forEach(seg => {
                    seg.classList.remove('completed', 'active');
                    const p = seg.querySelector('.progress');
                    if (p) { p.style.animation = 'none'; p.style.width = '0%'; }
                });

                if (typeof pauseAutoPlay === 'function') pauseAutoPlay();

                const overviewProg = dots[0].querySelector('.dot-track > .progress');
                if (overviewProg) {
                    if (activeProgEndListener) {
                        overviewProg.removeEventListener('animationend', activeProgEndListener);
                    }
                    void overviewProg.offsetWidth;
                    overviewProg.style.animation = 'slideProgress 6.5s linear forwards';
                    overviewProg.style.animationPlayState = 'running';
                    
                    activeProgEndListener = () => {
                        overviewProg.removeEventListener('animationend', activeProgEndListener);
                        activeProgEndListener = null;
                        if (currentIndex === 0) {
                            goToSlide(1);
                        }
                    };
                    overviewProg.addEventListener('animationend', activeProgEndListener);
                }

            } else if (index === 1) {
                const overviewProg = dots[0].querySelector('.dot-track > .progress');
                if (overviewProg) {
                    overviewProg.style.animation = 'none';
                    overviewProg.style.width = '100%';
                }

                const prestasiProg = dots[2].querySelector('.dot-track > .progress');
                if (prestasiProg) {
                    prestasiProg.style.animation = 'none';
                    prestasiProg.style.width = '0%';
                }

                if (typeof window.startLabSequence === 'function') {
                    window.startLabSequence();
                } else if (typeof startAutoPlay === 'function') {
                    startAutoPlay();
                }

            } else if (index === 2) {
                const overviewProg = dots[0].querySelector('.dot-track > .progress');
                if (overviewProg) {
                    overviewProg.style.animation = 'none';
                    overviewProg.style.width = '100%';
                }

                const segs = document.querySelectorAll('#labIndicators .seg');
                segs.forEach(seg => {
                    seg.classList.add('completed');
                    const p = seg.querySelector('.progress');
                    if (p) { p.style.animation = 'none'; p.style.width = '100%'; }
                });

                if (typeof pauseAutoPlay === 'function') pauseAutoPlay();

                const prestasiProg = dots[2].querySelector('.dot-track > .progress');
                if (prestasiProg) {
                    if (activeProgEndListener) {
                        prestasiProg.removeEventListener('animationend', activeProgEndListener);
                    }
                    void prestasiProg.offsetWidth;
                    prestasiProg.style.animation = 'slideProgress 6.5s linear forwards';
                    prestasiProg.style.animationPlayState = 'running';
                    
                    activeProgEndListener = () => {
                        prestasiProg.removeEventListener('animationend', activeProgEndListener);
                        activeProgEndListener = null;
                        if (currentIndex === 2) {
                            goToSlide(0);
                        }
                    };
                    prestasiProg.addEventListener('animationend', activeProgEndListener);
                }
            }
        };

        goToSlide(0);

        // Click on dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goToSlide(index);
            });
        });

        // Scroll listener synchronization
        carousel.addEventListener('scroll', () => {
            const slideWidth = carousel.clientWidth;
            const newIndex = Math.round(carousel.scrollLeft / slideWidth);
            if (newIndex !== currentIndex && newIndex >= 0 && newIndex < dots.length) {
                currentIndex = newIndex;
                updateDots(currentIndex);
            }
        });
    });
</script>

<script>
    // Script Logika Pagination Teks Deskripsi (Fitur Teman)
    document.addEventListener('DOMContentLoaded', () => {
        const fullText = <?= json_encode($header_settings->description ?? 'Seiring dengan berkembangnya kebutuhan pelayanan untuk mahasiswa, dosen dan pegawai FIK maka diperlukan peningkatan layanan yang mengusung efisiensi dan efektifitas. Ifik lahir dari keresahan dan kesulitan mahasiswa maupun dosen dalam beberapa layanan, antara lain pendaftaran TA, bimbingan online, dokumen online, peminjaman ruangan dan lain sebagainya. Sejak dibuat tahun 2021 oleh tim unit lab FIK, aplikasi berbasis web ini telah digunakan hingga saat ini untuk mempermudah layanan untuk kalangan internal FIK, baik untuk mahasiswa, dosen maupun pegawai FIK.') ?>;
        const charLimit = 420;
        
        let pages = [];
        if (fullText.length > charLimit) {
            let currentIdx = 0;
            while(currentIdx < fullText.length) {
                let slice = fullText.slice(currentIdx, currentIdx + charLimit);
                if (currentIdx + charLimit < fullText.length) {
                    let lastSpace = slice.lastIndexOf(' ');
                    if (lastSpace > -1) {
                        slice = slice.slice(0, lastSpace);
                        currentIdx += lastSpace + 1;
                    } else {
                        currentIdx += charLimit;
                    }
                } else {
                    currentIdx += charLimit;
                }
                pages.push(slice);
            }
        } else {
            pages.push(fullText);
        }

        let currentDescPage = 0;
        const descBox = document.getElementById('headerDescBox');
        const paginationBox = document.getElementById('headerPagination');
        const pageInfo = document.getElementById('headerPageInfo');

        function renderDescPage() {
            if(!descBox) return;
            descBox.innerHTML = pages[currentDescPage];
            if (pages.length > 1 && paginationBox) {
                paginationBox.style.display = 'flex';
                if (pageInfo) pageInfo.innerText = (currentDescPage + 1) + '/' + pages.length;
            }
        }

        window.prevDescPage = function() {
            if(currentDescPage > 0) {
                currentDescPage--;
                renderDescPage();
            }
        };
        
        window.nextDescPage = function() {
            if(currentDescPage < pages.length - 1) {
                currentDescPage++;
                renderDescPage();
            }
        };

        renderDescPage();
    });
</script>
