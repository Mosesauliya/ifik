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
        scroll-behavior: smooth; /* Tambahan agar pergeserannya selalu mulus/animasi */
    }
    .carousel-container::-webkit-scrollbar { display: none; }

    /* Tiap Layar/Slide Carousel */
    .carousel-slide {
        flex: 0 0 100vw; /* 100% lebar viewport per slide */
        height: 100%;
        scroll-snap-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2; /* Konten berada di atas background tapi di bawah UI interaktif jika ada */
        
        /* Latar Belakang default tanpa overlay gradient */
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
        z-index: -1; /* Di bawah konten slide */
    }


    


    /* Carousel Indicators (Dots) */
    .carousel-indicators {
        position: absolute;
        bottom: 32px; /* Sesuaikan dengan tinggi tombol agar pas di tengah-tengah vertikal */
        left: 50%;
        transform: translateX(-50%);
        width: 70vw; 
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px; /* Jarak antara dots */
        z-index: 20;
    }
    
    .dots-half {
        flex: 1;
        display: flex;
        gap: 15px;
        align-items: center;
    }
    
    .carousel-indicators .dot {
        flex: 1; /* Meregang memenuhi width 50vw */
        display: flex;
        flex-direction: column;
        gap: 8px;
        cursor: pointer;
        opacity: 0.6;
        transition: all 0.3s ease;
        background: rgba(0, 0, 0, 0.4);
        padding: 10px 15px;
        border-radius: 8px;
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .carousel-indicators .dot.active, 
    .carousel-indicators .dot:hover {
        opacity: 1;
        background: rgba(0, 0, 0, 0.65);
        border-color: rgba(234, 88, 12, 0.6); /* Highlight orange */
        box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        transform: translateY(-2px);
    }

    .carousel-indicators .dot .dot-label {
        font-size: 0.9rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #fff;
        letter-spacing: 1.5px;
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
        box-shadow: 0 0 8px #fff, 0 0 15px rgba(255,255,255,0.8); /* Tambahan Glow */
    }
    
    .carousel-indicators .dot.active .progress {
        /* Animasi ditangani oleh JS agar dinamis berdasarkan video/gambar */
    }
    
    /* --- SLIDE 1 NEW LAYOUT --- */
    .slide1-layout {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        padding: 0 80px;
        display: flex;
        align-items: center;
        z-index: 10;
        pointer-events: none; /* Allow clicks to pass through to carousel */
    }
    .slide1-text-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 500px;
        max-width: 90vw;
        z-index: 10;
        margin-top: -60px; /* geser sedikit ke atas */
        margin-left: 20px;
    }
    .slide1-title-box {
        background: rgba(255, 255, 255, 0.95);
        padding: 15px 30px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        backdrop-filter: blur(5px);
        pointer-events: auto; /* Enable clicks */
        width: 100%;
        box-sizing: border-box;
    }
    .slide1-title-box h1 {
        color: #ea580c;
        font-size: 2.2rem;
        font-weight: 800;
        margin: 0;
    }
    .slide1-content-box {
        background: rgba(255, 255, 255, 0.95);
        padding: 25px 30px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        color: #334155;
        font-size: 0.85rem;
        line-height: 1.6;
        text-align: justify;
        backdrop-filter: blur(5px);
        pointer-events: auto; /* Enable clicks */
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
    .header-pagination {
        display: none;
        pointer-events: auto;
        margin-top: 10px;
        align-items: center;
        gap: 10px;
    }
    .header-pagination button {
        background: #fff; border: none; padding: 5px 12px;
        border-radius: 6px; font-weight: bold; color: #ea580c;
        cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        font-size: 0.75rem;
    }
    .header-pagination button:hover { background: #ea580c; color: #fff; }
    .header-pagination span {
        color: #fff; font-size: 0.8rem; font-weight: bold;
        text-shadow: 0 1px 3px rgba(0,0,0,0.8);
    }
    
    @media (max-width: 900px) {
        .slide1-layout { padding: 0 20px; }
        .slide1-text-container { margin-top: 0; width: 100%; }
        .dekanat-img-right { max-height: 250px; opacity: 0.5; }
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

    /* --- KEYFRAMES ANIMASI DINAMIS --- */
    @keyframes slideUpFade {
        0% { opacity: 0; transform: translateY(50px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes softZoomFade {
        0% { opacity: 0; transform: scale(0.85); filter: blur(10px); }
        100% { opacity: 1; transform: scale(1); filter: blur(0); }
    }
    @keyframes floatIdle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-12px); }
    }
    @keyframes floatIdleSlow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-18px); }
    }
    @keyframes slowPan {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    @keyframes shineCap {
        0% { left: -100%; opacity: 1; }
        100% { left: 150%; opacity: 0.5; }
    }

    /* --- HERO CONTENT PADA SLIDE 1 --- */
    .hero-content-slide {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 50px;
        pointer-events: none; /* Biarkan klik tembus kecuali card */
        z-index: 5;
        perspective: 1500px; /* Kedalaman untuk efek tilt 3D */
    }

    /* Animasi Staggered Entrance (Berjalan HANYA setelah loading splash selesai) */
    .card-left-section, .greeting-text, .main-title, .description, .dekanat-popout {
        opacity: 0; /* Sembunyi sebelum animasi jalan */
    }

    body.play-animations .card-left-section {
        animation: slideUpFade 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    body.play-animations .greeting-text {
        animation: slideUpFade 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.15s forwards;
    }
    body.play-animations .main-title {
        animation: slideUpFade 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s forwards;
    }
    body.play-animations .description {
        animation: slideUpFade 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.45s forwards;
    }
    body.play-animations .dekanat-popout {
        animation: softZoomFade 1.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.25s forwards;
    }

    /* Hover efek bersinar pada Metallic Cap saat kursor mendekat */
    .card-left-section:hover .cap-reflection {
        animation: shineCap 1.2s ease-in-out forwards;
    }

    /* Wrapper untuk Isolasi Transform */
    .floating-wrap {
        display: inline-block;
        pointer-events: none; /* Diteruskan ke anak */
    }
    .parallax-card, .parallax-img {
        pointer-events: none; /* Diteruskan ke anak */
        transition: transform 0.15s ease-out; /* Sangat responsif ke kursor */
        will-change: transform;
    }

    /* Card di Kiri - Desain ID Card Premium (Tanpa Tali) */
    .card-left-section {
        background: #fffbef;
        border-radius: 28px;
        width: 380px;
        box-shadow:
            0 20px 45px rgba(120,53,15,0.3),
            0 6px 18px rgba(0,0,0,0.18);
        border: 1px solid rgba(180,83,9,0.18);
        pointer-events: auto;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
        position: relative;
    }
    
    .card-left-section:hover {
        transform: scale(1.05);
    }

    /* Metallic Cap di bagian atas card */
    .card-metallic-cap {
        height: 54px;
        background: linear-gradient(180deg, 
            #ece6d7 0%, 
            #d4caa7 30%, 
            #b3a78c 70%, 
            #998d72 100%
        );
        border-bottom: 2px solid #857a62;
        position: relative;
        box-shadow: 
            inset 0 1.5px 0 rgba(255,255,255,0.7),
            0 3px 6px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Lubang ID Card */
    .cap-hole-wrapper {
        position: absolute;
        top: 14px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 22;
    }

    .cap-hole {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: radial-gradient(circle at 35% 30%, #2a1200, #0a0400);
        box-shadow:
            inset 0 3px 6px rgba(0,0,0,0.95),
            0 1px 2px rgba(255,255,255,0.4);
        border: 1.5px solid #786d57;
    }

    /* Konten dalam Card */
    .card-body {
        padding: 30px;
    }

    .greeting-text {
        font-size: 0.8rem;
        font-weight: 800;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: #78350f;
        margin-bottom: 12px;
    }

    .main-title {
        font-size: 1.8rem;
        line-height: 1.2;
        font-weight: 900;
        color: #1e293b;
        margin-bottom: 12px;
    }

    .description {
        font-size: 0.85rem;
        line-height: 1.6;
        color: #475569;
        font-weight: 500;
        text-align: justify;
    }

    .dekanat-popout {
        width: 480px; /* Poto diperbesar */
        height: auto;
        object-fit: contain;
        filter: drop-shadow(-10px 10px 15px rgba(0,0,0,0.2));
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        transform-origin: bottom center;
    }

    .dekanat-popout:hover {
        transform: scale(1.08);
    }

    /* --- RESPONSIVE DESIGN FOR SESI 1 (CAROUSEL CARD) --- */
    @media (max-width: 1200px) {
        .hero-content-slide {
            padding: 0 20px;
        }
        .card-left-section {
            width: 340px;
            padding: 20px;
        }
        .main-title { font-size: 1.5rem; }
        .description { font-size: 0.8rem; }
        .dekanat-popout { width: 300px; }
    }

    @media (max-width: 900px) {
        .hero-content-slide {
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }
        .card-left-section {
            width: 90%;
            text-align: center;
        }
        .dekanat-popout {
            width: 250px;
        }
    }
</style>

<!-- Sesi 1: Carousel -->
<div class="section-wrapper" id="section-carousel">

    <!-- Fixed Overlay untuk Text dan Image agar tetap ada di semua slide carousel -->
    <div class="slide1-layout">
        <div class="slide1-text-container">
            <div class="slide1-title-box">
                <h1><?= htmlspecialchars($header_settings->title ?? 'Fakultas Industri Kreatif') ?></h1>
            </div>
            <div class="slide1-content-box" id="headerDescBox">
                <!-- Text will be loaded by JS if pagination is needed -->
                <?= htmlspecialchars($header_settings->description ?? 'Loading...') ?>
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
    
    <!-- Carousel Horizontal yang bisa digeser (Scroll Hijacking) -->
    <div class="carousel-container">
        <?php if(!empty($header_slides)): foreach($header_slides as $index => $slide): ?>
            <div class="carousel-slide">
                <?php if($slide->media_type == 'video'): ?>
                    <video id="slide<?= $index ?>-video" autoplay muted playsinline loop class="background-video">
                        <source src="<?= base_url('assets/vids/' . $slide->media_path) ?>" type="video/mp4">
                    </video>
                <?php else: ?>
                    <img src="<?= base_url('assets/images/' . $slide->media_path) ?>" class="background-video" style="object-fit: cover;">
                <?php endif; ?>
            </div>
        <?php endforeach; else: ?>
            <div class="carousel-slide" style="background-image: url('<?= base_url('assets/images/Fakultas.jpg') ?>'); background-size: cover; background-position: center;"></div>
        <?php endif; ?>
    </div>
    
    <!-- Indikator Dots dengan Label -->
    <div class="carousel-indicators" id="carouselDots">
        <div class="dots-half" style="justify-content: flex-end;">
        <?php 
        $total_slides = !empty($header_slides) ? count($header_slides) : 1;
        $half_index = ceil($total_slides / 2);
        
        if(!empty($header_slides)): foreach($header_slides as $index => $slide): 
            if($index == $half_index) {
                echo '</div>'; // close first half
                
                // Ruang kosong di tengah agar tombol bulat fixed bisa pas
                echo '<div style="width: 75px; flex: 0 0 auto;"></div>';
                
                echo '<div class="dots-half" style="justify-content: flex-start;">'; // open second half
            }
        ?>
            <div class="dot <?= $index == 0 ? 'active' : '' ?>" data-index="<?= $index ?>" data-duration="<?= htmlspecialchars($slide->duration ?? 4) ?>">
                <span class="dot-label"><?= htmlspecialchars($slide->label) ?></span>
                <div class="dot-track"><div class="progress"></div></div>
            </div>
        <?php endforeach; else: ?>
            <div class="dot active" data-index="0" data-duration="4">
                <span class="dot-label">Overview</span>
                <div class="dot-track"><div class="progress"></div></div>
            </div>
            </div>
            <!-- Ruang kosong di tengah untuk 1 slide -->
            <div style="width: 75px; flex: 0 0 auto;"></div>
            <div class="dots-half" style="display:none;">
        <?php endif; ?>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.querySelector('.carousel-container');
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('#carouselDots .dot');
        
        let currentIndex = 0;
        let direction = 1; // 1 = gerak ke kanan, -1 = gerak ke kiri
        let autoScrollTimer;

        // Fungsi berpindah ke slide tertentu dengan halus
        const goToSlide = (index) => {
            if (!carousel || !slides[index]) return;
            
            // Hitung posisi absolut slide yang dituju
            const slideLeftPos = slides[index].offsetLeft;
            
            // Gunakan scrollTo untuk memastikan animasinya presisi dari awal sampai akhir slide
            carousel.scrollTo({
                left: slideLeftPos,
                behavior: 'smooth'
            });
            
            updateDots(index);
        };

        // Ekspos fungsi ke window agar bisa dipanggil dari tombol navbar
        window.goToSlide = goToSlide;

        // Pastikan animasi terupdate saat durasi video sudah diketahui
        document.querySelectorAll('video').forEach((vid, idx) => {
            vid.addEventListener('loadedmetadata', () => {
                const slideIndex = Array.from(slides).indexOf(vid.closest('.carousel-slide'));
                if (currentIndex === slideIndex) {
                    const activeProg = dots[slideIndex].querySelector('.progress');
                    if (activeProg) {
                        activeProg.style.animation = 'none';
                        activeProg.offsetHeight;
                        activeProg.style.animation = `slideProgress ${vid.duration}s linear forwards`;
                    }
                }
            });
        });

        // Fungsi memperbarui titik (dot) aktif dan visibility logo 3D
        const updateDots = (index) => {
            dots.forEach(dot => {
                dot.classList.remove('active');
                
                // Force animation restart for progress bar
                const prog = dot.querySelector('.progress');
                if (prog) {
                    prog.style.animation = 'none';
                    prog.offsetHeight; // trigger reflow
                    prog.style.width = '0%'; // reset width
                }
            });
            if(dots[index]) {
                dots[index].classList.add('active');
                const activeProg = dots[index].querySelector('.progress');
                if (activeProg) {
                    let duration = dots[index].dataset.duration ? parseInt(dots[index].dataset.duration) : 4; // durasi dinamis dari per-slide
                    const currentSlide = slides[index];
                    const videoEl = currentSlide ? currentSlide.querySelector('video') : null;
                    
                    if (videoEl && videoEl.duration && !isNaN(videoEl.duration)) {
                        duration = videoEl.duration;
                        videoEl.currentTime = 0;
                        videoEl.play();
                    }
                    
                    activeProg.style.animation = `slideProgress ${duration}s linear forwards`;
                }
            }

            // 3D Logo logic: Hanya disembunyikan jika berada di Sesi 1 (Header) dan bukan slide 1
            const modelContainer = document.getElementById('global-model-container');
            const dashboardContainer = document.querySelector('.dashboard-container');
            if (modelContainer) {
                const currentScrollTop = dashboardContainer ? dashboardContainer.scrollTop : 0;
                const vh = window.innerHeight || 800;
                if (currentScrollTop < vh * 0.45) {
                    if (index === 0) {
                        modelContainer.style.opacity = '1';
                    } else {
                        modelContainer.style.opacity = '0';
                    }
                } else if (currentScrollTop <= vh * 1.4) {
                    // Sesi 2 (Informasi Ruangan): Logo 3D tampil
                    modelContainer.style.opacity = '1';
                } else {
                    // Sesi di bawah Informasi Ruangan (Lab, Berita, dll): Logo 3D disembunyikan
                    modelContainer.style.opacity = '0';
                }
                modelContainer.style.pointerEvents = 'none';
            }

            // Hentikan video lain dan pause auto-scroll jika video sedang aktif
            let hasActiveVideo = false;
            slides.forEach((slide, i) => {
                const vid = slide.querySelector('video');
                if(vid) {
                    if (i === index) {
                        vid.currentTime = 0;
                        vid.play();
                        hasActiveVideo = true;
                    } else {
                        vid.pause();
                    }
                }
            });

            if (hasActiveVideo) {
                clearInterval(autoScrollTimer);
            }
        };

        // --- MOUSE TRACKING PARALLAX (TERISOLASI) ---
        const pCard = document.querySelector('.parallax-card');
        const pImg = document.querySelector('.parallax-img');
        
        if (pCard) {
            pCard.addEventListener('mousemove', (e) => {
                const rect = pCard.getBoundingClientRect();
                // Hitung posisi kursor relatif terhadap TENGAH CARD
                const xAxis = ((rect.left + rect.width / 2) - e.clientX) / -18;
                const yAxis = ((rect.top + rect.height / 2) - e.clientY) / 18;
                pCard.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
            });

            pCard.addEventListener('mouseleave', () => {
                pCard.style.transform = `rotateY(0deg) rotateX(0deg)`;
            });
        }

        if (pImg) {
            pImg.addEventListener('mousemove', (e) => {
                const rect = pImg.getBoundingClientRect();
                // Hitung posisi kursor relatif terhadap TENGAH GAMBAR
                const xAxis = ((rect.left + rect.width / 2) - e.clientX) / 25;
                const yAxis = ((rect.top + rect.height / 2) - e.clientY) / 25;
                // Gambar bergeser kebalikan dari arah kursor
                pImg.style.transform = `translateX(${xAxis}px) translateY(${yAxis}px)`;
            });

            pImg.addEventListener('mouseleave', () => {
                pImg.style.transform = `translateX(0px) translateY(0px)`;
            });
        }
        // ----------------------------------------

        const startAutoScroll = () => {
            clearTimeout(autoScrollTimer); // Pastikan tidak ada duplikat interval
            
            // Jika slide aktif adalah video, tunggu event ended
            const currentSlide = slides[currentIndex];
            if (currentSlide && currentSlide.querySelector('video')) {
                return;
            }

            let slideDuration = dots[currentIndex] && dots[currentIndex].dataset.duration ? parseInt(dots[currentIndex].dataset.duration) * 1000 : 4000;

            autoScrollTimer = setTimeout(() => {
                currentIndex += direction;
                
                // Logika memantul (0 -> 1 -> 2 -> 1 -> 0)
                if (currentIndex >= slides.length - 1) {
                    currentIndex = slides.length - 1;
                    direction = -1; // Balik arah ke kiri
                } else if (currentIndex <= 0) {
                    currentIndex = 0;
                    direction = 1;  // Balik arah ke kanan
                }
                
                goToSlide(currentIndex);
            }, slideDuration); 
        };

        document.querySelectorAll('video').forEach((vid) => {
            vid.addEventListener('ended', () => {
                const slideIndex = Array.from(slides).indexOf(vid.closest('.carousel-slide'));
                if (currentIndex === slideIndex) {
                    currentIndex++;
                    if (currentIndex >= slides.length) {
                        currentIndex = slides.length - 1;
                        direction = -1;
                    }
                    goToSlide(currentIndex);
                    startAutoScroll();
                }
            });
        });

        startAutoScroll();

        const dashboardContainer = document.querySelector('.dashboard-container');
        let isForcedScrolling = false;

        if (dashboardContainer) {
            dashboardContainer.addEventListener('wheel', (e) => {
                // Jika berada di Sesi 1 (scroll paling atas), scroll ke bawah, TAPI tidak di Slide 1
                if (dashboardContainer.scrollTop < 50 && e.deltaY > 0 && currentIndex > 0) {
                    if (!isForcedScrolling) {
                        e.preventDefault(); // Cegah scroll bawah
                        isForcedScrolling = true;
                        
                        // 1. Pindah ke slide 1 dengan animasi horizontal
                        currentIndex = 0;
                        goToSlide(0);
                        
                        // 2. Tunggu animasi horizontal selesai, lalu otomatis gulir ke bawah (Ke Sesi 2: Info Ruangan)
                        setTimeout(() => {
                            const nextSection = document.getElementById('section-about');
                            if (nextSection) {
                                if (window.lenis) {
                                    window.lenis.scrollTo(nextSection, { duration: 1.2 });
                                } else {
                                    dashboardContainer.scrollTo({
                                        top: nextSection.offsetTop,
                                        behavior: 'smooth'
                                    });
                                }
                            }
                            
                            // Reset flag
                            setTimeout(() => {
                                isForcedScrolling = false;
                            }, 800);
                            
                        }, 600); // Tunggu 600ms (kurang lebih waktu untuk scroll horizontal)
                    } else {
                        // Jika animasi paksa sedang jalan, abaikan scroll tambahan
                        e.preventDefault();
                    }
                }
            }, { passive: false });
        }

        // Hentikan auto-scroll saat disentuh/scroll manual
        carousel.addEventListener('scroll', () => {
            clearTimeout(autoScrollTimer);
            
            // Sinkronisasi index dot dengan posisi scroll manual
            const slideWidth = carousel.clientWidth;
            const newIndex = Math.round(carousel.scrollLeft / slideWidth);
            
            if (newIndex !== currentIndex) {
                currentIndex = newIndex;
                updateDots(currentIndex);
            }
            
            // Lanjutkan otomatis setelah durasi slide aktif
            startAutoScroll();
        });

        // Event listener saat titik (dot) diklik
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentIndex = index;
                goToSlide(currentIndex);
                
                // Reset timer agar tidak tabrakan
                clearTimeout(autoScrollTimer);
                startAutoScroll();
            });
        });
    });
</script>

<script>
    // Teks Pagination Logic
    document.addEventListener('DOMContentLoaded', () => {
        const fullText = <?= json_encode($header_settings->description ?? '') ?>;
        const charLimit = 560; // Karakter maksimal per halaman
        
        let pages = [];
        // Pemecahan kata-kata secara sederhana
        if (fullText.length > charLimit) {
            let currentIdx = 0;
            while(currentIdx < fullText.length) {
                let slice = fullText.slice(currentIdx, currentIdx + charLimit);
                // Usahakan tidak memotong kata di tengah
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
            if (pages.length > 1) {
                paginationBox.style.display = 'flex';
                pageInfo.innerText = (currentDescPage + 1) + '/' + pages.length;
            }
        }

        window.prevDescPage = function() {
            if(currentDescPage > 0) {
                currentDescPage--;
                renderDescPage();
            }
        }
        
        window.nextDescPage = function() {
            if(currentDescPage < pages.length - 1) {
                currentDescPage++;
                renderDescPage();
            }
        }

        renderDescPage();
    });
</script>
