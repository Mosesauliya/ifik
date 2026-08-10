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
        
        /* Latar Belakang dan Overlay dipindah ke sini agar ikut bergeser */
        background-image: 
            linear-gradient(135deg, rgba(251,191,36,0.6) 0%, rgba(245,158,11,0.5) 40%, rgba(180,83,9,0.5) 100%),
            url('<?= base_url("assets/images/background.png") ?>');
        background-size: 110%; /* Lebih besar untuk panning */
        background-position: center;
        animation: slowPan 30s ease-in-out infinite;
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

    /* Carousel Indicators (Dots) */
    .carousel-indicators {
        position: absolute;
        bottom: 60px;
        left: 50%;
        transform: translateX(-50%);
        width: 50vw; /* Menutupi 50% layar secara horizontal */
        display: flex;
        justify-content: space-between;
        gap: 20px;
        z-index: 20;
    }
    
    .carousel-indicators .dot {
        flex: 1; /* Meregang memenuhi width 50vw */
        display: flex;
        flex-direction: column;
        gap: 8px;
        cursor: pointer;
        opacity: 0.5;
        transition: all 0.3s ease;
    }
    
    .carousel-indicators .dot.active, 
    .carousel-indicators .dot:hover {
        opacity: 1;
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
        animation: slideProgress 4s linear forwards;
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
    
    <!-- Carousel Horizontal yang bisa digeser (Scroll Hijacking) -->
    <div class="carousel-container">
        <!-- Slide 1 -->
        <div class="carousel-slide slide-1">
            <div class="hero-content-slide">
                <!-- Bagian Kiri: Kolom Teks (Desain ID Card Premium) -->
                <div class="floating-wrap" style="animation: floatIdle 5s ease-in-out infinite;">
                    <div class="parallax-card">
                        <div class="card-left-section">
                            <div class="card-metallic-cap">
                                <div class="cap-reflection"></div>
                                <div class="cap-hole-wrapper"><div class="cap-hole"></div></div>
                            </div>
                            <div class="card-body">
                                <h4 class="greeting-text">HI THERE, WELCOME TO</h4>
                                <h1 class="main-title">Fakultas Industri Kreatif</h1>
                                <p class="description">Seiring dengan berkembangnya kebutuhan pelayanan untuk mahasiswa, dosen dan pegawai FIK maka diperlukan peningkatan layanan yang mengusung efisiensi dan efektifitas. Ifik lahir dari keresahan dan kesulitan mahasiswa maupun dosen dalam beberapa layanan, antara lain pendaftaran TA, bimbingan online, dokumen online, peminjaman ruangan dan lain sebagainya. Sejak dibuat tahun 2021 oleh tim unit lab FIK, aplikasi berbasis web ini telah digunakan hingga saat ini untuk mempermudah layanan untuk kalangan internal FIK, baik untuk mahasiswa, dosen maupun pegawai FIK.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bagian Kanan: Kolom Wadah Gambar -->
                <div class="floating-wrap" style="animation: floatIdleSlow 6s ease-in-out infinite 0.5s;">
                    <div class="parallax-img">
                        <img src="<?= base_url('assets/images/dekanat.png') ?>" alt="Dekanat" class="dekanat-popout">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slide 2 (Hanya Background) -->
        <div class="carousel-slide"></div>
        
        <!-- Slide 3 (Hanya Background) -->
        <div class="carousel-slide"></div>
    </div>
    
    <!-- Indikator Dots dengan Label (Lebar 50%) -->
    <div class="carousel-indicators" id="carouselDots">
        <div class="dot active" data-index="0">
            <span class="dot-label">Overview</span>
            <div class="dot-track"><div class="progress"></div></div>
        </div>
        <div class="dot" data-index="1">
            <span class="dot-label">Fasilitas</span>
            <div class="dot-track"><div class="progress"></div></div>
        </div>
        <div class="dot" data-index="2">
            <span class="dot-label">Prestasi</span>
            <div class="dot-track"><div class="progress"></div></div>
        </div>
    </div>

    <div class="scroll-hint">Scroll ↓</div>
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

        // Fungsi memperbarui titik (dot) aktif dan visibility logo 3D
        const updateDots = (index) => {
            dots.forEach(dot => {
                dot.classList.remove('active');
                
                // Force animation restart for progress bar
                const prog = dot.querySelector('.progress');
                if (prog) {
                    prog.style.animation = 'none';
                    prog.offsetHeight; // trigger reflow
                    prog.style.animation = null; 
                }
            });
            if(dots[index]) {
                dots[index].classList.add('active');
            }

            // 3D Logo logic: Hanya tampil di slide 1
            const modelContainer = document.getElementById('global-model-container');
            if (modelContainer) {
                if (index === 0) {
                    modelContainer.style.opacity = '1';
                    modelContainer.style.pointerEvents = 'none';
                } else {
                    modelContainer.style.opacity = '0';
                    modelContainer.style.pointerEvents = 'none';
                }
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
            autoScrollTimer = setInterval(() => {
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
            }, 4000); // 4 detik tiap slide
        };

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
                                dashboardContainer.scrollTo({
                                    top: nextSection.offsetTop,
                                    behavior: 'smooth'
                                });
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
            clearInterval(autoScrollTimer);
            clearTimeout(carousel.scrollTimeout);
            
            // Sinkronisasi index dot dengan posisi scroll manual
            const slideWidth = carousel.clientWidth;
            const newIndex = Math.round(carousel.scrollLeft / slideWidth);
            
            if (newIndex !== currentIndex) {
                currentIndex = newIndex;
                updateDots(currentIndex);
            }
            
            // Lanjutkan otomatis setelah 5 detik didiamkan
            carousel.scrollTimeout = setTimeout(startAutoScroll, 5000);
        });

        // Event listener saat titik (dot) diklik
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentIndex = index;
                goToSlide(currentIndex);
                
                // Reset timer agar tidak tabrakan
                clearInterval(autoScrollTimer);
                clearTimeout(carousel.scrollTimeout);
                carousel.scrollTimeout = setTimeout(startAutoScroll, 5000);
            });
        });
    });
</script>
