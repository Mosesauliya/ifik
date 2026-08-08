<style>
    /* Styling khusus Sesi 1 */
    #section-carousel {
        position: relative;
        background-color: #d97706; /* Fallback color */
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
        background-size: cover;
        background-position: center;
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
        bottom: 75px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 12px;
        z-index: 20;
    }
    
    .carousel-indicators .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.4);
    }
    
    .carousel-indicators .dot.active {
        background: #fff;
        transform: scale(1.4);
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translate(-50%, 0); }
        40% { transform: translate(-50%, -10px); }
        60% { transform: translate(-50%, -5px); }
    }

    /* --- KARTU PUTIH STATIS DI BAWAH --- */
    .static-white-card {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 1100px;
        background: #ffffff;
        border-radius: 32px 32px 0 0; /* Sudut atas membulat, ujung bawah menyentuh layar */
        display: flex; /* Membagi kartu menjadi dua kolom mutlak */
        align-items: flex-end; /* Elemen di dalam kartu bersandar ke bawah */
        justify-content: space-between;
        padding: 0 40px; /* Padding horizontal card. Atas bawah ditangani child */
        box-shadow: 0 -15px 40px rgba(0,0,0,0.2);
        z-index: 5; /* Di bawah logo 3D (yang z-index: 8) agar logo tidak terhalangi saat geser ke sesi 2 */
        min-height: 250px;
        pointer-events: auto;
    }

    /* Kolom Kiri: Teks */
    .card-left-section {
        flex: 1; /* Mengambil semua sisa ruang */
        padding: 40px 30px 40px 0; /* Memberi jarak ke atas, bawah, dan ruang antar kolom */
        text-align: left;
        z-index: 11;
    }

    /* Kolom Kanan: Ruang eksklusif untuk gambar */
    .card-right-section {
        flex: 0 0 350px; /* Lebar mutlak untuk wadah gambar (350px) */
        position: relative;
    }

    .greeting-text {
        font-size: 0.85rem;
        font-weight: 800;
        letter-spacing: 2px;
        color: #f59e0b; /* Oranye */
        margin-bottom: 6px;
    }

    .main-title {
        font-size: 2.2rem;
        line-height: 1.1;
        font-weight: 900;
        color: #1e293b; /* Teks gelap di atas putih */
        margin-bottom: 12px;
    }

    .description {
        font-size: 0.9rem;
        line-height: 1.6;
        color: #475569; /* Teks abu-abu */
        font-weight: 500;
        text-align: justify;
    }

    .dekanat-popout {
        position: absolute;
        bottom: 0; 
        right: 0; /* Menempel ke batas kanan ruang eksklusifnya */
        width: 380px; /* Membatasi lebar maksimum gambar agar tidak melebar ke kiri */
        height: 380px; /* Tinggi gambar agar pop-out */
        object-fit: contain; /* Memastikan gambar menyesuaikan rasio kotak (380x380) tanpa distorsi */
        object-position: bottom right; /* Menjangkarkan gambar di pojok kanan bawah kotak */
        z-index: 12;
        pointer-events: none;
        filter: drop-shadow(-10px 10px 15px rgba(0,0,0,0.2));
    }
</style>

<!-- Sesi 1: Carousel -->
<div class="section-wrapper" id="section-carousel">
    
    <!-- Carousel Horizontal yang bisa digeser (Scroll Hijacking) -->
    <div class="carousel-container">
        <!-- Slide 1 (Hanya Background) -->
        <div class="carousel-slide"></div>
        
        <!-- Slide 2 (Hanya Background) -->
        <div class="carousel-slide"></div>
        
        <!-- Slide 3 (Hanya Background) -->
        <div class="carousel-slide"></div>
    </div>
    
    <!-- Kartu Putih Statis (Tidak ikut ke-scroll saat geser horizontal) -->
    <div class="static-white-card">
        <!-- Bagian Kiri: Kolom Teks -->
        <div class="card-left-section">
            <h4 class="greeting-text">HI THERE, WELCOME TO</h4>
            <h1 class="main-title">Fakultas Industri Kreatif</h1>
            <p class="description">Seiring dengan berkembangnya kebutuhan pelayanan untuk mahasiswa, dosen dan pegawai FIK maka diperlukan peningkatan layanan yang mengusung efisiensi dan efektifitas. Ifik lahir dari keresahan dan kesulitan mahasiswa maupun dosen dalam beberapa layanan, antara lain pendaftaran TA, bimbingan online, dokumen online, peminjaman ruangan dan lain sebagainya. Sejak dibuat tahun 2021 oleh tim unit lab FIK, aplikasi berbasis web ini telah digunakan hingga saat ini untuk mempermudah layanan untuk kalangan internal FIK, baik untuk mahasiswa, dosen maupun pegawai FIK.</p>
        </div>
        
        <!-- Bagian Kanan: Kolom Wadah Gambar -->
        <div class="card-right-section">
            <img src="<?= base_url('assets/images/dekanat.png') ?>" alt="Dekanat" class="dekanat-popout">
        </div>
    </div>
    
    <!-- Indikator Dots -->
    <div class="carousel-indicators" id="carouselDots">
        <div class="dot active" data-index="0"></div>
        <div class="dot" data-index="1"></div>
        <div class="dot" data-index="2"></div>
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

        // Fungsi memperbarui titik (dot) aktif
        const updateDots = (index) => {
            dots.forEach(dot => dot.classList.remove('active'));
            if(dots[index]) dots[index].classList.add('active');
        };

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
