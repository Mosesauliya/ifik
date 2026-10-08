<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($title ?? 'Scanner QR Serah Terima & Pengembalian - Panel Laboran') ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- jsQR Library (Fast & Accurate Canvas Scanner) -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            min-height: 100vh;
            transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1), width 0.75s cubic-bezier(0.76, 0, 0.24, 1);
            box-sizing: border-box;
        }

        @media (min-width: 1024px) {
            .page-wrapper-for-sidebar {
                margin-left: 270px;
                width: calc(100% - 270px);
            }

            body.curved-sidebar-desktop-collapsed .page-wrapper-for-sidebar {
                margin-left: 0;
                width: 100%;
            }
        }

        @media (max-width: 1023.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
                padding-top: 48px;
            }
        }

        /* Clean Scanner Box */
        .camera-video-stream {
            width: 100%;
            height: 100%;
            min-height: 300px;
            max-height: 380px;
            object-fit: cover;
            display: block;
        }
        .camera-video-stream.is-mirrored {
            transform: scaleX(-1);
        }

        /* Scanning Laser Line */
        @keyframes scanBeam {
            0% { top: 6%; opacity: 0.8; }
            50% { top: 94%; opacity: 1; }
            100% { top: 6%; opacity: 0.8; }
        }
        .laser-line {
            position: absolute;
            left: 4%;
            right: 4%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #ea580c, #f97316, #ea580c, transparent);
            box-shadow: 0 0 14px #ea580c, 0 0 5px #f97316;
            border-radius: 2px;
            animation: scanBeam 2.2s ease-in-out infinite;
            pointer-events: none;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased selection:bg-orange-500 selection:text-white">

    <!-- Universal Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar">
        <!-- Main Content Container -->
        <main class="min-h-screen p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto">

            <!-- Top Header Navigation -->
            <div class="mb-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="bi bi-qr-code-scan"></i>
                    <span>Peminjaman Aset &amp; Barang</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Scanner QR Serah Terima &amp; Pengembalian
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Arahkan kamera ke QR Code bukti ACC peminjaman atau pengembalian barang dosen/mahasiswa.
                </p>
            </div>

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="text-sm font-medium leading-relaxed">
                        <?= html_escape($this->session->flashdata('error')) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div class="text-sm font-medium leading-relaxed">
                        <?= html_escape($this->session->flashdata('success')) ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Scanner Section (Left / Center) -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6">
                        
                        <!-- Header Scanner Controls -->
                        <div class="flex flex-wrap items-center justify-between gap-2 pb-3.5 mb-3.5 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="flex h-3 w-3 relative">
                                    <span id="pingDot" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span id="solidDot" class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </span>
                                <h2 class="text-sm font-bold text-slate-800">Kamera Scanner</h2>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" id="btnSwitchCamera" title="Ganti Kamera Depan / Belakang" class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white transition-all flex items-center gap-1.5 shadow-sm shadow-orange-600/25 active:scale-95">
                                    <i class="bi bi-arrow-repeat text-sm"></i>
                                    <span>Ganti Kamera</span>
                                </button>
                                
                                <button type="button" id="btnToggleMirror" title="Balik arah kamera (Mirror)" class="text-xs font-semibold px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-orange-100 text-slate-700 hover:text-orange-700 transition-colors flex items-center gap-1.5">
                                    <i class="bi bi-symmetry-vertical"></i>
                                    <span class="hidden sm:inline">Mirror</span>
                                </button>
                            </div>
                        </div>

                        <!-- Camera Selection Dropdown -->
                        <div class="mb-3.5">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs">
                                    <i class="bi bi-camera"></i>
                                </span>
                                <select id="cameraSelect" class="w-full pl-8 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all appearance-none cursor-pointer">
                                    <option value="">Memuat perangkat kamera...</option>
                                </select>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400 text-xs">
                                    <i class="bi bi-chevron-down"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Live Camera Viewport Box -->
                        <div class="relative rounded-2xl overflow-hidden border border-slate-200 bg-black flex items-center justify-center min-h-[300px]">
                            
                            <!-- Video Stream -->
                            <video id="webcamVideo" autoplay playsinline muted class="camera-video-stream"></video>

                            <!-- Offscreen Processing Canvas (hidden) -->
                            <canvas id="processingCanvas" class="hidden"></canvas>

                            <!-- Clean Scanning Reticle Frame -->
                            <div class="absolute inset-0 pointer-events-none flex items-center justify-center p-6">
                                <div class="relative w-52 h-52 sm:w-60 sm:h-60">
                                    <!-- Scanning Laser Line -->
                                    <div class="laser-line"></div>

                                    <!-- 4 Clean Corner Focus Brackets -->
                                    <span class="absolute top-0 left-0 w-7 h-7 border-t-4 border-l-4 border-orange-500 rounded-tl-xl"></span>
                                    <span class="absolute top-0 right-0 w-7 h-7 border-t-4 border-r-4 border-orange-500 rounded-tr-xl"></span>
                                    <span class="absolute bottom-0 left-0 w-7 h-7 border-b-4 border-l-4 border-orange-500 rounded-bl-xl"></span>
                                    <span class="absolute bottom-0 right-0 w-7 h-7 border-b-4 border-r-4 border-orange-500 rounded-br-xl"></span>
                                </div>
                            </div>

                            <!-- Floating Status Overlay -->
                            <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 z-20 pointer-events-none">
                                <span id="statusPill" class="text-xs font-bold px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-emerald-400 border border-white/10 shadow-lg flex items-center gap-1.5">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Mendeteksi QR...</span>
                                </span>
                            </div>
                        </div>

                        <!-- File Upload Scan Option -->
                        <div class="mt-4 flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="flex items-center gap-2.5 text-xs text-slate-600">
                                <div class="w-7 h-7 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0 font-bold">
                                    <i class="bi bi-image"></i>
                                </div>
                                <span class="font-medium">Scan langsung dari file foto / screenshot QR</span>
                            </div>
                            <button type="button" id="btnScanFile" class="px-3.5 py-1.5 bg-white hover:bg-orange-50 hover:text-orange-600 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 transition-all shadow-sm flex items-center gap-1.5 flex-shrink-0">
                                <i class="bi bi-upload"></i>
                                <span>Pilih Gambar</span>
                            </button>
                            <input type="file" id="qrFileInput" class="hidden" accept="image/*">
                        </div>

                        <!-- Manual Input Fallback -->
                        <div class="mt-6 pt-5 border-t border-slate-100">
                            <label for="manualGroupId" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                                <i class="bi bi-keyboard me-1 text-orange-500"></i> Atau Masukkan Kode / Nomor Peminjaman Manual
                            </label>
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400 text-sm">
                                        <i class="bi bi-upc-scan"></i>
                                    </span>
                                    <input type="text"
                                           id="manualGroupId"
                                           placeholder="Contoh: PINJAM-2026-001"
                                           class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                                </div>
                                <button type="button"
                                        id="btnBukaManual"
                                        class="px-5 py-2.5 bg-slate-900 hover:bg-orange-600 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-1.5 flex-shrink-0 shadow-sm">
                                    <span>Buka</span>
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guidance & Instructions (Right) -->
                <div class="lg:col-span-5 space-y-4">
                    <!-- Quick Test Transactions Card -->
                    <div class="bg-gradient-to-br from-orange-500 to-amber-500 text-white rounded-3xl p-5 shadow-lg shadow-orange-500/20">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-extrabold flex items-center gap-2">
                                <i class="bi bi-lightning-charge-fill"></i>
                                <span>Simulasi Peminjaman (Demo)</span>
                            </h3>
                            <span class="text-[10px] bg-white/20 font-bold px-2 py-0.5 rounded-full uppercase">Demo</span>
                        </div>
                        <p class="text-xs text-orange-100 mb-3 leading-relaxed">
                            Klik tombol di bawah untuk langsung mencoba alur peminjaman tanpa scan kamera:
                        </p>
                        <div class="space-y-2">
                            <a href="<?= site_url('peminjamanbarang/serah_terima/PINJAM-2026-001') ?>"
                               class="flex items-center justify-between p-3 rounded-2xl bg-white text-slate-800 hover:bg-orange-50 transition-all font-semibold text-xs shadow-sm">
                                <div>
                                    <div class="font-extrabold text-slate-900 flex items-center gap-1.5">
                                        <i class="bi bi-box-seam text-orange-600"></i>
                                        <span>PINJAM-2026-001</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Dr. Budi Santoso (Serah Terima)</div>
                                </div>
                                <i class="bi bi-chevron-right text-orange-600 font-bold"></i>
                            </a>

                            <a href="<?= site_url('peminjamanbarang/serah_terima/PINJAM-2026-002') ?>"
                               class="flex items-center justify-between p-3 rounded-2xl bg-white text-slate-800 hover:bg-orange-50 transition-all font-semibold text-xs shadow-sm">
                                <div>
                                    <div class="font-extrabold text-slate-900 flex items-center gap-1.5">
                                        <i class="bi bi-arrow-counterclockwise text-blue-600"></i>
                                        <span>PINJAM-2026-002</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Ahmad Dzaky (Pengembalian)</div>
                                </div>
                                <i class="bi bi-chevron-right text-blue-600 font-bold"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Workflow Steps Card -->
                    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-6">
                        <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <i class="bi bi-info-circle-fill text-orange-500"></i>
                            <span>Tips Scan QR</span>
                        </h3>
                        <ul class="space-y-3 text-xs text-slate-600">
                            <li class="flex items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-emerald-500 text-sm mt-0.5 flex-shrink-0"></i>
                                <span><strong>Dual-Scan Otomatis:</strong> Scanner membaca otomatis baik posisi normal maupun terbalik (mirror) tanpa perlu diatur manual.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-emerald-500 text-sm mt-0.5 flex-shrink-0"></i>
                                <span><strong>Kecerahan Layar HP:</strong> Naikkan brightness layar HP peminjam agar kontras QR tajam dan jelas.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-emerald-500 text-sm mt-0.5 flex-shrink-0"></i>
                                <span><strong>Jarak Ideal:</strong> Jaga jarak sekitar 15–25 cm dari lensa kamera.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('processingCanvas');
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        const statusPill = document.getElementById('statusPill');
        const cameraSelect = document.getElementById('cameraSelect');
        const btnToggleMirror = document.getElementById('btnToggleMirror');
        const manualInput = document.getElementById('manualGroupId');
        const btnManual = document.getElementById('btnBukaManual');
        const btnScanFile = document.getElementById('btnScanFile');
        const qrFileInput = document.getElementById('qrFileInput');

        let currentStream = null;
        let isScanning = true;
        let isMirrored = false;
        let animationFrameId = null;

        // Sound Feedback using Web Audio API
        function playSuccessBeep() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
                gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.15);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.15);
            } catch (e) {}
        }

        function extractGroupId(raw) {
            if (!raw) return '';
            let clean = raw.trim();
            try {
                if (/^https?:\/\//i.test(clean)) {
                    const url = new URL(clean);
                    if (url.searchParams.has('data')) {
                        return url.searchParams.get('data').trim();
                    }
                    if (url.searchParams.has('group_id')) {
                        return url.searchParams.get('group_id').trim();
                    }
                    if (url.searchParams.has('id')) {
                        return url.searchParams.get('id').trim();
                    }
                    const segments = url.pathname.split('/').filter(Boolean);
                    if (segments.length > 0) {
                        const lastSegment = segments[segments.length - 1].trim();
                        if (lastSegment) return lastSegment;
                    }
                }
            } catch (e) {}
            return clean;
        }

        function handleOpenPeminjaman(code) {
            if (!code || !code.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kode Peminjaman Kosong',
                    text: 'Silakan masukkan kode / nomor peminjaman yang valid.',
                    confirmButtonColor: '#ea580c'
                });
                return;
            }

            isScanning = false;
            if (animationFrameId) cancelAnimationFrame(animationFrameId);
            playSuccessBeep();

            if (statusPill) {
                statusPill.className = 'text-xs font-bold px-3.5 py-1.5 rounded-full bg-emerald-500 text-white shadow-xl flex items-center gap-1.5';
                statusPill.innerHTML = '<i class="bi bi-check2-circle"></i><span>QR Ditemukan! Mengalihkan...</span>';
            }

            const cleanCode = extractGroupId(code);
            setTimeout(() => {
                window.location.href = '<?= site_url('peminjamanbarang/serah_terima/') ?>' + encodeURIComponent(cleanCode);
            }, 300);
        }

        // Toggle Mirror Button
        if (btnToggleMirror && video) {
            btnToggleMirror.addEventListener('click', () => {
                isMirrored = !isMirrored;
                if (isMirrored) {
                    video.classList.add('is-mirrored');
                    btnToggleMirror.classList.add('bg-orange-500', 'text-white');
                    btnToggleMirror.classList.remove('bg-slate-100', 'text-slate-700');
                } else {
                    video.classList.remove('is-mirrored');
                    btnToggleMirror.classList.remove('bg-orange-500', 'text-white');
                    btnToggleMirror.classList.add('bg-slate-100', 'text-slate-700');
                }
            });
        }

        // Manual Input Submit
        if (btnManual && manualInput) {
            btnManual.addEventListener('click', () => handleOpenPeminjaman(manualInput.value));
            manualInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleOpenPeminjaman(manualInput.value);
                }
            });
        }

        // File Upload Scan
        if (btnScanFile && qrFileInput) {
            btnScanFile.addEventListener('click', () => qrFileInput.click());
            qrFileInput.addEventListener('change', (e) => {
                if (e.target.files && e.target.files.length > 0) {
                    const file = e.target.files[0];
                    const img = new Image();
                    img.onload = () => {
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0);
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        
                        // Normal scan
                        let code = jsQR(imageData.data, imageData.width, imageData.height);
                        if (code && code.data) {
                            handleOpenPeminjaman(code.data);
                            return;
                        }

                        // Flipped scan
                        const flippedCanvas = document.createElement('canvas');
                        flippedCanvas.width = img.width;
                        flippedCanvas.height = img.height;
                        const fCtx = flippedCanvas.getContext('2d');
                        fCtx.translate(img.width, 0);
                        fCtx.scale(-1, 1);
                        fCtx.drawImage(img, 0, 0);
                        const flippedData = fCtx.getImageData(0, 0, img.width, img.height);
                        code = jsQR(flippedData.data, flippedData.width, flippedData.height);
                        
                        if (code && code.data) {
                            handleOpenPeminjaman(code.data);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'QR Tidak Terbaca',
                                text: 'Gambar tidak memuat QR Code yang jelas.',
                                confirmButtonColor: '#ea580c'
                            });
                        }
                    };
                    img.src = URL.createObjectURL(file);
                }
            });
        }

        // High-speed Dual-Orientation Scanner Loop
        const offCanvasFlipped = document.createElement('canvas');
        const offCtxFlipped = offCanvasFlipped.getContext('2d', { willReadFrequently: true });

        // Native BarcodeDetector if supported
        const nativeDetector = ('BarcodeDetector' in window) ? new BarcodeDetector({ formats: ['qr_code'] }) : null;

        async function scanFrame() {
            if (!isScanning) return;

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                const w = video.videoWidth;
                const h = video.videoHeight;

                if (w > 0 && h > 0) {
                    // 1. Try Native BarcodeDetector (Ultra-fast & handles all angles/mirrors)
                    if (nativeDetector) {
                        try {
                            const barcodes = await nativeDetector.detect(video);
                            if (barcodes && barcodes.length > 0 && barcodes[0].rawValue) {
                                handleOpenPeminjaman(barcodes[0].rawValue);
                                return;
                            }
                        } catch (e) {}
                    }

                    // 2. jsQR Normal Scan
                    canvas.width = w;
                    canvas.height = h;
                    ctx.drawImage(video, 0, 0, w, h);
                    const imgData = ctx.getImageData(0, 0, w, h);
                    let code = jsQR(imgData.data, w, h, { inversionAttempts: "dontInvert" });

                    if (code && code.data) {
                        handleOpenPeminjaman(code.data);
                        return;
                    }

                    // 3. jsQR Flipped / Mirrored Scan (Solves USB Webcam horizontal mirror!)
                    offCanvasFlipped.width = w;
                    offCanvasFlipped.height = h;
                    offCtxFlipped.save();
                    offCtxFlipped.translate(w, 0);
                    offCtxFlipped.scale(-1, 1);
                    offCtxFlipped.drawImage(canvas, 0, 0);
                    offCtxFlipped.restore();
                    const flippedImgData = offCtxFlipped.getImageData(0, 0, w, h);
                    code = jsQR(flippedImgData.data, w, h, { inversionAttempts: "dontInvert" });

                    if (code && code.data) {
                        handleOpenPeminjaman(code.data);
                        return;
                    }
                }
            }

            if (isScanning) {
                animationFrameId = requestAnimationFrame(scanFrame);
            }
        }

        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || (window.innerWidth <= 768);
        const btnSwitchCamera = document.getElementById('btnSwitchCamera');
        let availableCameras = [];
        let currentCameraIndex = 0;

        // Camera Management & Initialization
        async function switchCameraTo(deviceId, index) {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
            }

            const constraints = {
                video: deviceId ? { deviceId: { exact: deviceId } } : { facingMode: { ideal: "environment" } },
                audio: false
            };

            try {
                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                currentStream = stream;
                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                isScanning = true;
                requestAnimationFrame(scanFrame);
                if (cameraSelect && deviceId) cameraSelect.value = deviceId;
                if (typeof index === 'number') currentCameraIndex = index;
                if (statusPill) statusPill.innerHTML = '<i class="bi bi-camera-video-fill text-emerald-400"></i><span>Mendeteksi QR...</span>';
            } catch (err) {
                console.error("Gagal ganti kamera:", err);
                if (statusPill) statusPill.innerHTML = '<i class="bi bi-camera-video-off text-rose-400"></i><span>Kamera Gagal</span>';
            }
        }

        async function initCameras() {
            try {
                // Request initial stream with ideal environment (back camera) so permissions are granted
                const initialConstraints = {
                    video: { facingMode: { ideal: "environment" } },
                    audio: false
                };
                const stream = await navigator.mediaDevices.getUserMedia(initialConstraints);
                currentStream = stream;
                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                isScanning = true;
                requestAnimationFrame(scanFrame);

                // Enumerate devices with granted permissions to get full labels
                if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    availableCameras = devices.filter(d => d.kind === 'videoinput');

                    if (availableCameras.length > 0) {
                        cameraSelect.innerHTML = '';
                        
                        const currentTrack = stream.getVideoTracks()[0];
                        const currentTrackSettings = currentTrack ? currentTrack.getSettings() : {};
                        const activeDeviceId = currentTrackSettings.deviceId;

                        let defaultIndex = 0;
                        availableCameras.forEach((cam, idx) => {
                            let label = cam.label || `Kamera ${idx + 1}`;
                            const lower = label.toLowerCase();
                            if (lower.includes('back') || lower.includes('rear') || lower.includes('environment') || lower.includes('belakang') || lower.includes('0')) {
                                label = `📷 ${label} (Kamera Belakang)`;
                                if (isMobile) defaultIndex = idx;
                            } else if (lower.includes('front') || lower.includes('user') || lower.includes('depan') || lower.includes('selfie') || lower.includes('1')) {
                                label = `🤳 ${label} (Kamera Depan)`;
                            }

                            const opt = document.createElement('option');
                            opt.value = cam.deviceId;
                            opt.text = label;
                            cameraSelect.appendChild(opt);

                            if (activeDeviceId && cam.deviceId === activeDeviceId) {
                                defaultIndex = idx;
                            }
                        });

                        currentCameraIndex = defaultIndex;
                        cameraSelect.value = availableCameras[defaultIndex].deviceId;
                    }
                }
            } catch (err) {
                console.warn("Init camera error:", err);
                if (statusPill) statusPill.innerHTML = '<i class="bi bi-camera-video-off text-rose-400"></i><span>Kamera Gagal</span>';
            }
        }

        // Switch camera button (1-click cycle front/back)
        if (btnSwitchCamera) {
            btnSwitchCamera.addEventListener('click', () => {
                if (availableCameras.length > 1) {
                    currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
                    const targetCam = availableCameras[currentCameraIndex];
                    switchCameraTo(targetCam.deviceId, currentCameraIndex);
                } else {
                    // Fallback toggle facingMode between environment and user
                    const currentFacing = video.getAttribute('data-facing') === 'user' ? 'environment' : 'user';
                    video.setAttribute('data-facing', currentFacing);
                    switchCameraTo(null, 0);
                }
            });
        }

        if (cameraSelect) {
            cameraSelect.addEventListener('change', () => {
                const idx = availableCameras.findIndex(c => c.deviceId === cameraSelect.value);
                switchCameraTo(cameraSelect.value, idx !== -1 ? idx : 0);
            });
        }

        // Start initial camera setup
        initCameras();
    });
    </script>
</body>
</html>
