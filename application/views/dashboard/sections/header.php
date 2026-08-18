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
        bottom: 50px;
        left: 50%;
        transform: translateX(-50%);
        width: 50vw; /* Menutupi 50% layar secara horizontal */
        display: flex;
        align-items: flex-end; /* Presisi sejajar di baseline yang sama */
        justify-content: space-between;
        gap: 20px;
        z-index: 30;
    }
    
    .carousel-indicators .dot {
        flex: 1;
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
        font-size: 0.85rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #fff;
        letter-spacing: 1.5px;
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
    
    .carousel-indicators .dot.active .progress {
        /* Animasi ditangani oleh JS agar dinamis berdasarkan video/gambar */
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
        gap: 4px; /* Potongan segmen 6 ruangan */
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

    /* --- MODAL TAMBAH RUANGAN KHUSUS ADMIN --- */
    .lab-modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(9, 13, 22, 0.75);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 999999;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .lab-modal-overlay.active {
        display: flex;
    }

    .lab-modal-card {
        width: 90%;
        max-width: 480px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        overflow: hidden;
    }

    .lab-modal-header {
        padding: 18px 24px;
        background: #0f172a;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .lab-modal-header h3 {
        font-size: 1.05rem;
        font-weight: 800;
        margin: 0;
    }

    .lab-modal-close {
        background: none; border: none;
        color: #94a3b8; font-size: 1.5rem;
        cursor: pointer; transition: color 0.2s;
    }

    .lab-modal-close:hover { color: #ffffff; }

    .lab-modal-body {
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .lab-form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .lab-form-group label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .lab-input {
        width: 100%;
        height: 38px;
        padding: 0 12px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        font-size: 0.85rem;
        color: #0f172a;
        transition: border-color 0.2s;
    }

    .lab-input:focus {
        outline: none;
        border-color: #ea580c;
    }

    .lab-modal-footer {
        padding: 14px 24px 18px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    .btn-cancel {
        padding: 8px 16px;
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-save {
        padding: 8px 20px;
        border-radius: 8px;
        border: none;
        background: #ea580c;
        color: #ffffff;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.4);
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

    <!-- Carousel Horizontal yang bisa digeser (Scroll Hijacking) -->
    <div class="carousel-container">
        <!-- Slide 1 (Overview - Fakultas Industri Kreatif) -->
        <div class="carousel-slide slide-1">
            <div class="slide1-layout">
                <div class="slide1-text-container">
                    <div class="slide1-title-box">
                        <h1>Fakultas Industri Kreatif</h1>
                    </div>
                    <div class="slide1-content-box">
                        Seiring dengan berkembangnya kebutuhan pelayanan untuk mahasiswa, dosen dan pegawai FIK maka diperlukan peningkatan layanan yang mengusung efisiensi dan efektifitas. Ifik lahir dari keresahan dan kesulitan mahasiswa maupun dosen dalam beberapa layanan, antara lain pendaftaran TA, bimbingan online, dokumen online, peminjaman ruangan dan lain sebagainya. Sejak dibuat tahun 2021 oleh tim unit lab FIK, aplikasi berbasis web ini telah digunakan hingga saat ini untuk mempermudah layanan untuk kalangan internal FIK, baik untuk mahasiswa, dosen maupun pegawai FIK.
                    </div>
                </div>
            </div>
            <img src="<?= base_url('assets/images/dekanat2.png') ?>" alt="Dekanat" class="dekanat-img-right">
        </div>
        
        <!-- Slide 2 (Fasilitas: Laboratorium Fakultas) -->
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
    
    <!-- Indikator Dots dengan Label (Lebar 50%) -->
    <div class="carousel-indicators" id="carouselDots">
        <div class="dot active" data-index="0">
            <span class="dot-label">Overview</span>
            <div class="dot-track"><div class="progress"></div></div>
        </div>

        <!-- Dot 1: Fasilitas (Track Dipotong-potong Menjadi 6 Segmen Ruangan) -->
        <div class="dot dot-fasilitas" data-index="1">
            <div class="dot-label-row">
                <span class="dot-label">Fasilitas</span>
                <div class="fasilitas-controls-group">
                    <?php if ($this->session->userdata('role_id') == 1): ?>
                        <!-- Tombol Tambah Ruangan Khusus Admin System (Navigasi ke Halaman Khusus /kelolaruangan) -->
                        <a href="<?= base_url('kelolaruangan') ?>" class="lab-add-room-btn" title="Halaman Kelola &amp; Tambah Ruangan Admin">
                            <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                            <span>Ruangan</span>
                        </a>
                    <?php endif; ?>

                    <!-- Tombol Play / Pause Kecil di Pinggir Seekbar Fasilitas -->
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

                    // Urutkan persis sesuai urutan sekuensial slide: multimedia -> aula -> cintiq -> greenscreen -> incubator -> mac
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

        <div class="dot" data-index="2">
            <span class="dot-label">Prestasi</span>
            <div class="dot-track"><div class="progress"></div></div>
        </div>
    </div>

    <div class="scroll-hint" onclick="if(window.lenis){window.lenis.scrollTo('#section-about', {duration:1.2});}else{document.getElementById('section-about')?.scrollIntoView({behavior:'smooth'});}" style="cursor: pointer;" title="Scroll ke Informasi Ruangan">Scroll ↓</div>
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

        let activeProgEndListener = null;

        // Fungsi memperbarui titik (dot) aktif, seekbar sekuensial, dan 3D logo
        const updateDots = (index) => {
            currentIndex = index;

            // Reset status active pada semua dot
            dots.forEach((dot, i) => {
                dot.classList.remove('active');
                
                // Track tunggal (Overview & Prestasi)
                const prog = dot.querySelector('.dot-track > .progress');
                if (prog) {
                    prog.style.animation = 'none';
                    prog.offsetHeight; // Force reflow
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

            // 3D Logo logic: Tampil di Slide 0 (Overview) dan Slide 2 (Prestasi), sembunyikan di Slide 1 (Fasilitas)
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

            // Manajemen Seekbar Sekuensial Berdasarkan Index Slide Aktif
            if (index === 0) { // --- SLIDE 0: OVERVIEW ---
                // Reset Fasilitas segmen ke 0%
                const segs = document.querySelectorAll('#labIndicators .seg');
                segs.forEach(seg => {
                    seg.classList.remove('completed', 'active');
                    const p = seg.querySelector('.progress');
                    if (p) { p.style.animation = 'none'; p.style.width = '0%'; }
                });

                if (typeof pauseAutoPlay === 'function') pauseAutoPlay();

                // Jalankan animasi seekbar Overview 0% -> 100% (6.5 Detik)
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
                            goToSlide(1); // Lanjut ke FASILITAS HANYA setelah Overview Selesai!
                        }
                    };
                    overviewProg.addEventListener('animationend', activeProgEndListener);
                }

            } else if (index === 1) { // --- SLIDE 1: FASILITAS ---
                // Overview ditandai SELESAI (100% Penuh)
                const overviewProg = dots[0].querySelector('.dot-track > .progress');
                if (overviewProg) {
                    overviewProg.style.animation = 'none';
                    overviewProg.style.width = '100%';
                }

                // Prestasi di-reset ke 0%
                const prestasiProg = dots[2].querySelector('.dot-track > .progress');
                if (prestasiProg) {
                    prestasiProg.style.animation = 'none';
                    prestasiProg.style.width = '0%';
                }

                // Mulai jalankan urutan 6 ruangan Laboratorium dari room 0
                if (typeof window.startLabSequence === 'function') {
                    window.startLabSequence();
                } else if (typeof startAutoPlay === 'function') {
                    startAutoPlay();
                }

            } else if (index === 2) { // --- SLIDE 2: PRESTASI ---
                // Overview dan seluruh 6 segmen Fasilitas ditandai SELESAI (100% Penuh)
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

                // Jalankan animasi seekbar Prestasi 0% -> 100% (6.5 Detik)
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
                            goToSlide(0); // Kembali loop ke Overview HANYA setelah Prestasi Selesai!
                        }
                    };
                    prestasiProg.addEventListener('animationend', activeProgEndListener);
                }
            }
        };

        // Inisialisasi awal pada Slide 0 (Overview)
        goToSlide(0);

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
