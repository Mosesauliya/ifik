<style>
    /* Styling khusus Sesi 1 */
    #section-carousel {
        /* Menggunakan gradasi oranye seperti di halaman login */
        background: linear-gradient(to bottom right, #f86b1d, #ea580c, #d97706);
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
        z-index: 2; /* Kartu berada di belakang area interaksi 3D jika diperlukan, tapi kita atur posisinya di pinggir */
    }

    /* Kartu Kaca (Glassmorphism) */
    .glass-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        padding: 50px 40px;
        border-radius: 24px;
        width: 400px;
        text-align: center;
        opacity: 0.8;
        transition: transform 0.4s ease, opacity 0.4s ease, box-shadow 0.4s ease;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .carousel-slide:hover .glass-card {
        opacity: 1;
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 210, 255, 0.1);
    }
    
    /* Penempatan Kartu agar tidak tertimpa Logo 3D yang ada di tengah */
    .slide-left .glass-card { margin-right: 45vw; }
    .slide-right .glass-card { margin-left: 45vw; }
    
    .glass-card h2 {
        font-size: 2.2rem;
        margin-bottom: 20px;
        color: var(--text-color);
        font-weight: 800;
    }
    .glass-card p {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #475569;
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
        background: rgba(255, 255, 255, 0.3);
        cursor: pointer;
        transition: all 0.3s ease;
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
</style>

<!-- Sesi 1: Carousel -->
<div class="section-wrapper" id="section-carousel">
    
    <!-- Carousel Horizontal yang bisa digeser (Scroll Hijacking) -->
    <div class="carousel-container">
        
        <!-- Slide 1 (Kartu di sebelah kiri) -->
        <div class="carousel-slide slide-left">
            <div class="glass-card">
                <h2>Selamat Datang di IFIK</h2>
                <p>Geser (scroll) ke kanan untuk melihat fitur-fitur unggulan, atau scroll ke bawah untuk pindah ke halaman berikutnya.</p>
            </div>
        </div>
        
        <!-- Slide 2 (Kartu di sebelah kanan) -->
        <div class="carousel-slide slide-right">
            <div class="glass-card">
                <h2>Interaktif 3D</h2>
                <p>Logo di tengah adalah objek 3D. Anda bisa menggeser, memutar, dan memperbesar logo tersebut secara interaktif.</p>
            </div>
        </div>
        
        <!-- Slide 3 (Kartu di sebelah kiri lagi) -->
        <div class="carousel-slide slide-left">
            <div class="glass-card">
                <h2>Desain Premium</h2>
                <p>Kami menerapkan animasi yang mulus dan palet warna modern yang akan membuat pengalaman pengguna menjadi luar biasa.</p>
            </div>
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
