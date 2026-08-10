<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IFIK - Dashboard Premium</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- NProgress (Web Loading Bar) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    
    <!-- Model Viewer for 3D -->
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.3.0/model-viewer.min.js"></script>

    <script>
        // Mulai loading web saat halaman pertama kali diproses
        NProgress.start(); 
        window.addEventListener('load', () => {
            // Selesai saat seluruh aset (termasuk 3D model besar) sudah termuat sepenuhnya
            NProgress.done(); 
        });
    </script>
    
    <style>
        :root {
            --bg-color: #fbf7f1; /* Off-white theme dari login */
            --text-color: #1e293b;
            --glass-bg: rgba(255, 255, 255, 0.4);
            --glass-border: rgba(234, 88, 12, 0.2);
        }

        /* NProgress Customization (Orange Premium) */
        #nprogress .bar {
            background: #ea580c !important;
            height: 4px !important;
            z-index: 10001 !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 10px #ea580c, 0 0 5px #ea580c !important;
        }
        #nprogress .spinner-icon {
            border-top-color: #ea580c !important;
            border-left-color: #ea580c !important;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            overflow: hidden; /* Prevent default window scrolling */
        }

        /* Scroll Hijacking Container for Vertical Sections */
        .dashboard-container {
            height: 100vh;
            width: 100vw;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
            scroll-behavior: smooth;
            scrollbar-width: none; /* Firefox */
            position: relative;
        }
        
        .dashboard-container::-webkit-scrollbar {
            display: none; /* Chrome/Safari/Edge */
        }

        /* Individual Vertical Sections */
        .section-wrapper {
            height: 100vh;
            width: 100vw;
            scroll-snap-align: start;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* 3D Model Center Piece - Global Fixed Position */
        #global-model-container {
            position: fixed;
            width: 600px;
            height: 600px;
            pointer-events: none;
            /* Animasi transisi yang mengatur perpindahan, skala (ukuran), dan putaran (rotasi) */
            transition: all 1.2s cubic-bezier(0.25, 1, 0.5, 1);
        }

        /* [KODE ANIMASI] Modifiers untuk posisi model berdasarkan Sesi aktif */
        
        /* Posisi Sesi 1: Di Tengah */
        #global-model-container.pos-center {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(1) rotateY(0deg);
            z-index: 8; /* Di bawah Sidebar (Sidebar z-index: 9) */
        }
        
        /* Posisi Sesi 2: Di Kiri (Sambil berputar 360 derajat horizontal) */
        #global-model-container.pos-left {
            top: 50%;
            left: 22%;
            transform: translate(-50%, -50%) scale(0.9) rotateY(360deg);
            z-index: 8; /* Di bawah Sidebar agar Sidebar tidak tertutup logo */
        }
        
        /* Posisi Sesi 3: Mengecil jadi Logo di Kiri Atas (Masuk ke dalam Sidebar) */
        #global-model-container.pos-top-left {
            top: 90px;
            left: 130px;
            transform: translate(-50%, -50%) scale(0.2) rotateY(720deg);
            z-index: 10; /* Di atas Sidebar agar Logo jelas dan tidak terhalang blur Glassmorphism */
        }
        
        #global-model-container model-viewer {
            width: 100%;
            height: 100%;
        }

        /* Ambient Glow di belakang Model 3D */
        .glow-effect {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(0,0,0,0) 70%);
            z-index: 1;
            filter: blur(50px);
            animation: pulse 4s infinite alternate;
            transition: opacity 0.8s ease;
        }

        /* Hilangkan efek cahaya (glow) saat logo mengecil di pojok kiri atas */
        #global-model-container.pos-top-left .glow-effect {
            opacity: 0;
        }

        /* Scroll Progress Bar di Atas */
        #scroll-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, #f86b1d, #ea580c);
            width: 0%;
            z-index: 10000;
            transition: width 0.1s ease-out;
            box-shadow: 0 0 10px rgba(234, 88, 12, 0.6);
        }

        @keyframes pulse {
            0% { transform: translate(-50%, -50%) scale(0.9); opacity: 0.8; }
            100% { transform: translate(-50%, -50%) scale(1.1); opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Progress Bar (Pengganti Scrollbar) -->
    <div id="scroll-progress-bar"></div>

    <!-- Navigation Bar Menu -->
    <?php $this->load->view('partials/navbar'); ?>

    <!-- 3D Model diletakkan di luar container scroll -->
    <div id="global-model-container" class="pos-center">
        <div class="glow-effect"></div>
        <model-viewer 
            id="ifik3dModel"
            src="<?= base_url('assets/3D/ifik.glb') ?>" 
            alt="3D Logo IFIK" 
            disable-zoom 
            shadow-intensity="1.5" 
            shadow-softness="0.8"
            exposure="1.15"
            camera-orbit="90deg 85deg 100%"
            field-of-view="24deg"
            interaction-prompt="none"
            style="background-color: transparent; width: 100%; height: 100%;">
        </model-viewer>
    </div>

    <!-- Main Container that handles vertical Scroll Snapping -->
    <div class="dashboard-container">
        
        <!-- Sesi 1: Carousel Horizontal dengan Logo 3D statis -->
        <?php $this->load->view('dashboard/sections/carousel'); ?>

        <!-- Sesi 2: Informasi Ruangan -->
        <?php $this->load->view('dashboard/sections/about'); ?>

        <!-- Sesi 3: Laboratorium Fakultas -->
        <?php $this->load->view('dashboard/sections/lab'); ?>

        <!-- Sesi 4: Berita & Informasi Terkini (Contact) -->
        <?php $this->load->view('dashboard/sections/contact'); ?>

    </div>

    <!-- JS untuk Parallax dan Deteksi Scroll (Scroll Hijacking State) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ifikModel = document.getElementById('ifik3dModel');
            const modelContainer = document.getElementById('global-model-container');
            const sections = document.querySelectorAll('.section-wrapper');

            // 1. [KODE DETEKSI SCROLL] Intersection Observer untuk mendeteksi sesi mana yang aktif
            const observerOptions = {
                root: document.querySelector('.dashboard-container'),
                rootMargin: '0px',
                threshold: 0.5 // Memicu ketika 50% area sesi terlihat
            };

            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const activeSectionId = entry.target.id;
                        
                        // Logika perubahan CSS Class berdasarkan sesi aktif
                        if (activeSectionId === 'section-carousel') {
                            modelContainer.className = 'pos-center'; // Sesi 1
                        } else if (activeSectionId === 'section-about') {
                            modelContainer.className = 'pos-left';   // Sesi 2
                        } else if (activeSectionId === 'section-lab') {
                            modelContainer.className = 'pos-left';   // Sesi 3
                        } else if (activeSectionId === 'section-contact') {
                            modelContainer.className = 'pos-top-left'; // Sesi 4
                        }
                    }
                });
            }, observerOptions);

            sections.forEach(section => {
                sectionObserver.observe(section);
            });

            // 2. Parallax Camera Tracking (Mouse Movement)
            const dashboardContainer = document.querySelector('.dashboard-container');
            if (dashboardContainer && ifikModel) {
                dashboardContainer.addEventListener('mousemove', (e) => {
                    const centerX = window.innerWidth / 2;
                    const centerY = window.innerHeight / 2;
                    
                    const rotateY = ((e.clientX - centerX) / (window.innerWidth / 2)) * 18; 
                    const rotateX = -((e.clientY - centerY) / (window.innerHeight / 2)) * 18;

                    const orbitAzimuth = 90 + (rotateY * 0.6); 
                    const orbitElevation = 85 - (rotateX * 0.5); 
                    ifikModel.cameraOrbit = `${orbitAzimuth}deg ${orbitElevation}deg 100%`;
                });
            }

            // 3. Scroll Progress Bar (Top)
            const progressBar = document.getElementById('scroll-progress-bar');
            if (dashboardContainer && progressBar) {
                // Set awal, karena saat refresh mungkin ada di tengah
                const updateProgress = () => {
                    const totalHeight = dashboardContainer.scrollHeight - dashboardContainer.clientHeight;
                    const scrollPosition = dashboardContainer.scrollTop;
                    const progressPercentage = (scrollPosition / totalHeight) * 100;
                    progressBar.style.width = progressPercentage + '%';
                };
                
                dashboardContainer.addEventListener('scroll', updateProgress);
                window.addEventListener('resize', updateProgress);
                // Inisialisasi awal
                updateProgress();
            }
        });
    </script>
</body>
</html>
