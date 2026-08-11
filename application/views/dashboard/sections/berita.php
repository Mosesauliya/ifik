<style>
    /* Styling khusus Sesi 3 (Berita & Footer) */
    #section-contact {
        background-color: #fbf7f1; /* Mengikuti tema utama */
        min-height: 100vh !important;
        height: auto !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        padding-top: 50px;
        padding-bottom: 0;
        position: relative;
    }

    .news-header {
        margin-bottom: 10px;
        text-align: center;
        z-index: 2;
    }

    .news-header h1 {
        font-size: 2.5rem;
        color: #1e293b;
        font-weight: 900;
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }
    
    .news-header p {
        color: #64748b;
        font-size: 1.05rem;
    }

    .news-carousel-container {
        display: flex;
        gap: 30px;
        width: 100%;
        max-width: 1400px;
        padding: 30px 60px;
        margin-top: 15px;
        margin-bottom: 60px; /* Jarak Jauh Berjarak Lega (60px) antara Berita & Footer */
        overflow-x: auto;
        /* Sembunyikan scrollbar */
        scrollbar-width: none;
        -ms-overflow-style: none;
        align-items: center;
        overscroll-behavior-x: contain;
    }
    
    .news-carousel-container::-webkit-scrollbar {
        display: none;
    }

    .news-card {
        flex: 0 0 300px;
        height: 360px;
        background: #fff;
        border-radius: 20px;
        box-shadow: -12px 12px 30px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        
        /* EFEK 3D SEBELUM DI-HOVER */
        transform: perspective(1200px) rotateY(-25deg) scale(0.9);
        transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.5s ease, opacity 0.5s ease, filter 0.5s ease;
        cursor: pointer;
        z-index: 1;
    }

    /* Saat di-hover: Menghadap ke depan (normal) dan membesar */
    .news-card:hover {
        transform: perspective(1200px) rotateY(0deg) scale(1.05);
        box-shadow: 0 25px 50px rgba(234, 88, 12, 0.25);
        z-index: 10;
    }
    
    /* Efek Blur & Mundur untuk kartu yang tidak di-hover (Fokus Mode) */
    .news-carousel-container:hover .news-card:not(:hover) {
        transform: perspective(1200px) rotateY(-35deg) scale(0.85);
        opacity: 0.6;
        filter: blur(2px);
    }

    .news-image {
        width: 100%;
        height: 44%;
        background-color: #ddd; 
        background-size: cover;
        background-position: center;
        position: relative;
    }

    /* Gradient overlay agar transisi ke teks halus */
    .news-image::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; width: 100%; height: 25px;
        background: linear-gradient(to top, #ffffff, transparent);
    }
    
    .news-content {
        padding: 18px;
        height: 56%;
        display: flex;
        flex-direction: column;
    }
    
    .news-date {
        font-size: 0.78rem;
        color: #ea580c;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 6px;
        letter-spacing: 0.8px;
    }
    
    .news-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 6px;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .news-excerpt {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.5;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    /* --- RESPONSIVE DESIGN FOR SESI 3 (NEWS) --- */
    @media (max-width: 1200px) {
        .news-header h1 {
            font-size: 2.2rem;
        }
        .news-card {
            flex: 0 0 280px;
            height: 400px;
        }
        .news-title {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 900px) {
        .news-header {
            top: 60px;
        }
        .news-header h1 {
            font-size: 1.8rem;
        }
        .news-header p {
            font-size: 0.9rem;
        }
        .news-card {
            flex: 0 0 240px; 
            height: 360px; 
        }
        .news-title {
            font-size: 1rem;
        }
        .news-excerpt {
            font-size: 0.85rem;
            -webkit-line-clamp: 2;
        }
    }
</style>

<!-- Sesi 3: Carousel Berita 3D & Footer -->
<!-- ID dipertahankan sebagai 'section-contact' agar IntersectionObserver di index.php tetap berfungsi -->
<div class="section-wrapper" id="section-contact">
    
    <div class="news-header">
        <h1>Berita & Informasi Terkini</h1>
        <p>Tetap terhubung dengan kabar terbaru dari Fakultas Industri Kreatif</p>
    </div>

    <!-- Container Carousel -->
    <div class="news-carousel-container">
        
        <!-- Kartu Berita 1 (Terkoneksi ke Halaman Detail) -->
        <div class="news-card" onclick="window.location.href='<?= base_url('index.php/news/detail') ?>'">
            <div class="news-image" style="background-image: url('<?= base_url('assets/images/background.png') ?>');"></div>
            <div class="news-content">
                <span class="news-date">12 Agustus 2026</span>
                <h3 class="news-title">Pameran Karya Mahasiswa FIK 2026 Sukses Digelar</h3>
                <p class="news-excerpt">Ratusan karya inovatif dari mahasiswa dipamerkan dalam ajang tahunan yang dihadiri oleh praktisi industri kreatif terkemuka. Acara ini berlangsung meriah selama tiga hari berturut-turut.</p>
            </div>
        </div>
        
        <!-- Kartu Berita 2 -->
        <div class="news-card">
            <div class="news-image" style="background-image: url('<?= base_url('assets/images/ifik_portal_3d_render.jpg') ?>');"></div>
            <div class="news-content">
                <span class="news-date">05 Agustus 2026</span>
                <h3 class="news-title">Workshop Desain Interaktif Bersama Pakar UI/UX</h3>
                <p class="news-excerpt">Mahasiswa diajak untuk mendalami tren UI/UX dan interaksi 3D web modern dalam workshop intensif selama dua hari bersama narasumber berpengalaman dari startup ternama.</p>
            </div>
        </div>
        
        <!-- Kartu Berita 3 -->
        <div class="news-card">
            <div class="news-image" style="background-image: url('<?= base_url('assets/images/ik_3d_illustration.jpg') ?>');"></div>
            <div class="news-content">
                <span class="news-date">28 Juli 2026</span>
                <h3 class="news-title">Peluncuran Sistem Layanan Terpadu IFIK Versi Baru</h3>
                <p class="news-excerpt">Sistem IFIK kini hadir dengan wajah baru yang lebih premium, responsif, dan interaktif menggunakan teknologi WebGL untuk memudahkan seluruh civitas akademika.</p>
            </div>
        </div>
        
        <!-- Kartu Berita 4 -->
        <div class="news-card">
            <div class="news-image" style="background-image: url('<?= base_url('assets/images/logo-dummy.webp') ?>'); background-size: contain; background-repeat: no-repeat; background-color: #f1f5f9;"></div>
            <div class="news-content">
                <span class="news-date">15 Juli 2026</span>
                <h3 class="news-title">Prestasi Gemilang Tim Riset FIK di Tingkat Nasional</h3>
                <p class="news-excerpt">Penelitian kolaboratif dosen dan mahasiswa tentang pemanfaatan AI dalam desain komunikasi visual berhasil memenangkan hibah penelitian bergengsi tahun ini.</p>
            </div>
        </div>

        <!-- Kartu Berita 5 -->
        <div class="news-card">
            <div class="news-image" style="background-image: url('<?= base_url('assets/images/background.png') ?>');"></div>
            <div class="news-content">
                <span class="news-date">02 Juli 2026</span>
                <h3 class="news-title">Kunjungan Studi Industri Kreatif ke Studio Animasi</h3>
                <p class="news-excerpt">Mahasiswa semester akhir berkesempatan melihat langsung alur kerja produksi animasi 3D kelas dunia dan berdiskusi dengan para profesional di bidang tersebut.</p>
            </div>
        </div>
        
    </div>

    <!-- Partial Footer -->
    <?php $this->load->view('partials/footer'); ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const carousel = document.querySelector('.news-carousel-container');
        if (!carousel) return;

        let isHovered = false;
        
        // Hentikan scroll jika user sedang mengarahkan kursor (hover)
        carousel.addEventListener('mouseenter', () => isHovered = true);
        carousel.addEventListener('mouseleave', () => isHovered = false);

        // Hentikan scroll saat disentuh di perangkat mobile
        carousel.addEventListener('touchstart', () => isHovered = true);
        carousel.addEventListener('touchend', () => isHovered = false);

        let lastTime = 0;
        let accumulator = 0;
        // Kecepatan gerak: 50 piksel per detik (cukup lambat dan smooth)
        const pixelsPerSecond = 50; 

        function scrollLoop(timestamp) {
            if (!lastTime) lastTime = timestamp;
            const deltaTime = timestamp - lastTime;
            lastTime = timestamp;

            if (!isHovered) {
                accumulator += (pixelsPerSecond * deltaTime) / 1000;
                
                // Jika sudah saatnya menggeser 1 pixel atau lebih
                if (accumulator >= 1) {
                    const step = Math.floor(accumulator);
                    carousel.scrollLeft += step;
                    accumulator -= step;
                    
                    // Deteksi elemen pertama
                    const firstChild = carousel.firstElementChild;
                    if (firstChild) {
                        // Lebar 1 kartu (320px) + gap (30px) = 350px
                        // Menggunakan getComputedStyle untuk memastikan nilai gap dinamis jika nanti diubah
                        const gap = parseInt(window.getComputedStyle(carousel).gap) || 30;
                        const totalItemWidth = firstChild.offsetWidth + gap;
                        
                        // Jika elemen pertama sudah sepenuhnya keluar layar di sisi kiri
                        if (carousel.scrollLeft >= totalItemWidth) {
                            // Pindahkan elemen pertama ke paling belakang untuk infinite loop
                            carousel.appendChild(firstChild);
                            // Sesuaikan posisi scroll mundur sebesar elemen yang dipindah
                            // Sehingga visualnya terlihat tetap diam tanpa lompatan
                            carousel.scrollLeft -= totalItemWidth;
                        }
                    }
                }
            }
            requestAnimationFrame(scrollLoop);
        }
        
        // Mulai animasi
        requestAnimationFrame(scrollLoop);
    });
</script>
