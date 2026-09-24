<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Pengaturan Tanda Tangan Digital - Panel Ka. Ur') ?></title>
    
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
    
    <!-- Signature Pad JS -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <style>
        body, button, input, textarea, select {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        body {
            padding-left: 0;
        }
        @media (min-width: 1024px) {
            body {
                padding-left: 76px; /* space for left floating trigger */
            }
        }

        .canvas-container {
            position: relative;
            width: 100%;
            height: 230px;
            background-color: #ffffff;
            border-radius: 1rem;
            border: 2px dashed #cbd5e1;
            overflow: hidden;
            touch-action: none;
            transition: all 0.2s ease;
        }
        .canvas-container:hover {
            border-color: #ea580c;
        }
        .canvas-baseline {
            position: absolute;
            left: 8%;
            right: 8%;
            bottom: 45px;
            height: 1px;
            border-bottom: 1px dashed #cbd5e1;
            pointer-events: none;
        }

        /* Checkerboard pattern for transparent preview */
        .checkerboard-bg {
            background-color: #ffffff;
            background-image: 
                linear-gradient(45deg, #f1f5f9 25%, transparent 25%), 
                linear-gradient(-45deg, #f1f5f9 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #f1f5f9 75%), 
                linear-gradient(-45deg, transparent 75%, #f1f5f9 75%);
            background-size: 14px 14px;
            background-position: 0 0, 0 7px, 7px -7px, -7px 0px;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased selection:bg-orange-500 selection:text-white">

    <!-- Universal Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Content Container -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-7xl mx-auto">

        <!-- Top Header Navigation -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="bi bi-patch-check-fill"></i>
                    <span>Kepala Urusan / Kepala Laboratorium</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Tanda Tangan Digital Ka. Ur
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola tanda tangan resmi Anda untuk disematkan secara otomatis pada <strong>Surat Resmi Persetujuan Peminjaman Ruangan & Lab</strong> saat Anda menyetujui permohonan.
                </p>
            </div>

            <!-- Profile Info Badge -->
            <div class="flex items-center gap-3 bg-white px-4 py-3 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center font-extrabold text-base shadow-sm shadow-orange-500/30">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800 leading-tight"><?= htmlspecialchars($nama ?? $this->session->userdata('name') ?? 'Kepala Urusan Lab'); ?></p>
                    <p class="text-[11px] text-slate-500 mt-0.5">NIP: <?= htmlspecialchars($nip ?? '19820315002'); ?> · <span class="text-orange-600 font-semibold">Ka. Ur Laboratorium</span></p>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm animate-fade-in">
                <div class="w-8 h-8 bg-emerald-500 text-white rounded-xl flex items-center justify-center shrink-0 text-base">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-emerald-900">Berhasil Disimpan</h4>
                    <p class="text-xs text-emerald-700 mt-0.5"><?= $this->session->flashdata('success'); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-2xl flex items-center gap-3 shadow-sm animate-fade-in">
                <div class="w-8 h-8 bg-rose-500 text-white rounded-xl flex items-center justify-center shrink-0 text-base">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-rose-900">Peringatan</h4>
                    <p class="text-xs text-rose-700 mt-0.5"><?= $this->session->flashdata('error'); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT COLUMN: Status & Live Preview TTD (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- 1. Card Status TTD Aktif -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="bi bi-patch-check-fill text-orange-500 text-base"></i>
                            Tanda Tangan Aktif
                        </h3>
                        <?php if (!empty($tanda_tangan)): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Tersimpan & Aktif
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                Belum Ada TTD
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Area Preview Gambar -->
                    <div class="checkerboard-bg rounded-2xl border border-slate-200 p-4 h-48 flex items-center justify-center relative group">
                        <?php if (!empty($tanda_tangan) && file_exists(FCPATH . 'uploads/signatures/' . $tanda_tangan)): ?>
                            <img id="activeTtdImg" src="<?= base_url('uploads/signatures/' . $tanda_tangan) ?>" 
                                 alt="Tanda Tangan Digital" 
                                 class="max-h-36 max-w-full object-contain filter drop-shadow-sm transition-transform duration-300 group-hover:scale-105">
                        <?php else: ?>
                            <div class="text-center py-6">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                    <i class="bi bi-pen"></i>
                                </div>
                                <p class="text-xs font-semibold text-slate-500">Belum Ada Tanda Tangan Tersimpan</p>
                                <p class="text-[11px] text-slate-400 mt-1 max-w-[220px]">Goreskan tanda tangan di panel samping atau unggah file gambar TTD Anda.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Identitas Penandatangan -->
                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <p class="text-slate-400 text-[11px]">Nama Penandatangan</p>
                            <p class="font-bold text-slate-800"><?= htmlspecialchars($nama ?? 'Kepala Urusan Lab'); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-slate-400 text-[11px]">NIP Resmi</p>
                            <p class="font-bold text-slate-700 font-mono"><?= htmlspecialchars($nip ?? '19820315002'); ?></p>
                        </div>
                    </div>

                    <!-- Action Buttons for Saved Signature -->
                    <?php if (!empty($tanda_tangan)): ?>
                        <div class="mt-5 grid grid-cols-2 gap-3 pt-3 border-t border-slate-100">
                            <a href="<?= site_url('kaur/tanda-tangan/download'); ?>" 
                               class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                                <i class="bi bi-download"></i>
                                <span>Unduh File TTD</span>
                            </a>
                            <button type="button" onclick="confirmDeleteTtd()" 
                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors">
                                <i class="bi bi-trash3"></i>
                                <span>Hapus TTD</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 2. Informasi Surat Resmi -->
                <div class="bg-gradient-to-br from-amber-500/10 to-orange-500/5 rounded-3xl p-6 border border-orange-200/60 shadow-xs">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center shrink-0 text-sm">
                            <i class="bi bi-file-earmark-check-fill"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Otomatisasi Surat Persetujuan Resmi</h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Saat Anda menyetujui permohonan peminjaman ruangan dengan status <strong>"Disetujui Ka. Ur"</strong>, tanda tangan digital ini akan otomatis disematkan pada dokumen resmi ber-QR Code yang sah.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Signature Creation / Upload Studio (7 Cols) -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
                    
                    <!-- Tabs Navigation -->
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-6 flex-wrap gap-3">
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-900">Buat atau Perbarui TTD</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Pilih metode goresan langsung atau unggah gambar.</p>
                        </div>

                        <div class="inline-flex p-1 bg-slate-100 rounded-2xl border border-slate-200/80">
                            <button type="button" id="tabBtnCanvas" onclick="switchTab('canvas')" 
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-orange-600 shadow-xs flex items-center gap-1.5">
                                <i class="bi bi-brush-fill"></i>
                                <span>Canvas Gores</span>
                            </button>
                            <button type="button" id="tabBtnUpload" onclick="switchTab('upload')" 
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
                                <i class="bi bi-cloud-arrow-up-fill"></i>
                                <span>Upload File</span>
                            </button>
                        </div>
                    </div>

                    <!-- TAB 1: CANVAS SIGNATURE PAD -->
                    <div id="tabContentCanvas" class="space-y-4">
                        <div class="canvas-container" id="canvasWrapper">
                            <canvas id="signatureCanvas" class="w-full h-full cursor-crosshair"></canvas>
                            <div class="canvas-baseline"></div>
                        </div>

                        <!-- Canvas Controls -->
                        <div class="flex items-center justify-between flex-wrap gap-2 pt-1">
                            <div class="flex items-center gap-2">
                                <button type="button" id="clearCanvasBtn" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 hover:text-slate-800 transition-colors">
                                    <i class="bi bi-eraser-fill"></i>
                                    <span>Bersihkan Kanvas</span>
                                </button>
                                <button type="button" id="undoCanvasBtn" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 hover:text-slate-800 transition-colors">
                                    <i class="bi bi-arrow-left"></i>
                                    <span>Undo Goresan</span>
                                </button>
                            </div>

                            <span class="text-[11px] text-slate-400 italic">
                                *Otomatis disimpan tanpa background (Transparan)
                            </span>
                        </div>

                        <!-- Form Submit Canvas -->
                        <form id="formCanvasTtd" action="<?= site_url('kaur/tanda-tangan/simpan'); ?>" method="POST" class="pt-4 border-t border-slate-100">
                            <input type="hidden" name="tipe" value="canvas">
                            <input type="hidden" name="signature_data" id="signatureDataInput">

                            <button type="submit" id="saveCanvasBtn" 
                                    class="w-full py-3 px-4 rounded-2xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-md shadow-orange-600/25 hover:shadow-lg hover:shadow-orange-600/35 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-shield-check text-base"></i>
                                <span>Simpan Tanda Tangan Hasil Goresan</span>
                            </button>
                        </form>
                    </div>

                    <!-- TAB 2: UPLOAD IMAGE FILE -->
                    <div id="tabContentUpload" class="hidden space-y-5">
                        <form id="formUploadTtd" action="<?= site_url('kaur/tanda-tangan/simpan'); ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="tipe" value="upload">

                            <!-- File Upload Area -->
                            <div id="dropZone" 
                                 class="border-2 border-dashed border-slate-300 hover:border-orange-500 rounded-3xl p-8 text-center bg-slate-50 hover:bg-orange-50/20 transition-all cursor-pointer relative group">
                                <input type="file" name="file_ttd" id="fileTtdInput" accept="image/png, image/jpeg, image/jpg" class="hidden">
                                
                                <div id="uploadPlaceholder">
                                    <div class="w-16 h-16 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center mx-auto mb-3 text-2xl group-hover:scale-110 transition-transform">
                                        <i class="bi bi-cloud-arrow-up"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">Klik atau seret file gambar TTD Anda ke sini</h4>
                                    <p class="text-xs text-slate-500 mt-1">Mendukung format PNG transparan, JPG, atau JPEG (Maks. 3 MB)</p>
                                    <div class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-xs">
                                        <i class="bi bi-folder2-open"></i>
                                        <span>Pilih File dari Komputer</span>
                                    </div>
                                </div>

                                <!-- File Preview Area after selection -->
                                <div id="uploadPreviewBox" class="hidden">
                                    <div class="checkerboard-bg rounded-2xl border border-slate-200 p-4 h-40 flex items-center justify-center mb-3">
                                        <img id="filePreviewImg" src="" alt="Preview Upload" class="max-h-32 max-w-full object-contain">
                                    </div>
                                    <p id="fileNameLabel" class="text-xs font-bold text-slate-700 truncate max-w-xs mx-auto"></p>
                                    <p id="fileSizeLabel" class="text-[11px] text-slate-400 mt-0.5"></p>
                                    <button type="button" onclick="resetFileUpload(event)" class="mt-3 text-xs font-bold text-rose-600 hover:underline">
                                        Ganti File Gambar Lain
                                    </button>
                                </div>
                            </div>

                            <button type="submit" 
                                    class="w-full py-3 px-4 rounded-2xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-sm shadow-md shadow-orange-600/25 hover:shadow-lg hover:shadow-orange-600/35 transition-all flex items-center justify-center gap-2">
                                <i class="bi bi-cloud-check text-base"></i>
                                <span>Unggah & Terapkan Tanda Tangan</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>

    </main>

    <!-- Hidden Form for Delete Action -->
    <form id="deleteTtdForm" action="<?= site_url('kaur/tanda-tangan/hapus'); ?>" method="POST" class="hidden"></form>

    <!-- Interactive Scripts -->
    <script>
        let signaturePad = null;
        let activeTab = 'canvas';

        document.addEventListener('DOMContentLoaded', function() {
            initSignaturePad();
            initFileUpload();
        });

        // Initialize Signature Pad
        function initSignaturePad() {
            const canvas = document.getElementById('signatureCanvas');
            if (!canvas) return;

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                if (signaturePad) {
                    signaturePad.clear();
                }
            }

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)', // Transparent
                penColor: 'rgb(15, 23, 42)', // Slate-900
                minWidth: 1.5,
                maxWidth: 3.5,
                throttle: 16
            });

            // Clear Button
            document.getElementById('clearCanvasBtn').addEventListener('click', function() {
                signaturePad.clear();
            });

            // Undo Button
            document.getElementById('undoCanvasBtn').addEventListener('click', function() {
                const data = signaturePad.toData();
                if (data && data.length > 0) {
                    data.pop();
                    signaturePad.fromData(data);
                }
            });

            // Form Submit Interceptor
            document.getElementById('formCanvasTtd').addEventListener('submit', function(e) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kanvas Masih Kosong',
                        text: 'Silakan goreskan tanda tangan Anda pada kanvas terlebih dahulu.',
                        confirmButtonColor: '#ea580c'
                    });
                    return false;
                }

                // Convert to Trimmed Transparent PNG
                const trimmedDataUrl = getTrimmedCanvasDataUrl(canvas);
                document.getElementById('signatureDataInput').value = trimmedDataUrl;
            });
        }

        // Trim whitespace/transparent pixels around the signature
        function getTrimmedCanvasDataUrl(canvas) {
            const ctx = canvas.getContext('2d');
            const pixels = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const l = pixels.data.length;
            let i,
                bound = {
                    top: null,
                    left: null,
                    right: null,
                    bottom: null
                },
                x, y;

            for (i = 0; i < l; i += 4) {
                if (pixels.data[i + 3] !== 0) {
                    x = (i / 4) % canvas.width;
                    y = ~~((i / 4) / canvas.width);

                    if (bound.top === null) bound.top = y;
                    if (bound.left === null) bound.left = x;
                    else if (x < bound.left) bound.left = x;
                    if (bound.right === null) bound.right = x;
                    else if (bound.right < x) bound.right = x;
                    if (bound.bottom === null) bound.bottom = y;
                    else if (bound.bottom < y) bound.bottom = y;
                }
            }

            if (bound.top === null) return canvas.toDataURL(); // Empty

            const padding = 20 * (Math.max(window.devicePixelRatio || 1, 1));
            bound.top = Math.max(0, bound.top - padding);
            bound.left = Math.max(0, bound.left - padding);
            bound.right = Math.min(canvas.width, bound.right + padding);
            bound.bottom = Math.min(canvas.height, bound.bottom + padding);

            const trimWidth = bound.right - bound.left;
            const trimHeight = bound.bottom - bound.top;

            const trimmedCanvas = document.createElement('canvas');
            trimmedCanvas.width = trimWidth;
            trimmedCanvas.height = trimHeight;

            const trimmedCtx = trimmedCanvas.getContext('2d');
            trimmedCtx.drawImage(
                canvas,
                bound.left, bound.top, trimWidth, trimHeight,
                0, 0, trimWidth, trimHeight
            );

            return trimmedCanvas.toDataURL('image/png');
        }

        // Switch Between Tabs (Canvas vs Upload)
        function switchTab(tab) {
            activeTab = tab;
            const tabBtnCanvas = document.getElementById('tabBtnCanvas');
            const tabBtnUpload = document.getElementById('tabBtnUpload');
            const contentCanvas = document.getElementById('tabContentCanvas');
            const contentUpload = document.getElementById('tabContentUpload');

            if (tab === 'canvas') {
                tabBtnCanvas.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-orange-600 shadow-xs flex items-center gap-1.5";
                tabBtnUpload.className = "px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5";
                contentCanvas.classList.remove('hidden');
                contentUpload.classList.add('hidden');
            } else {
                tabBtnUpload.className = "px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-orange-600 shadow-xs flex items-center gap-1.5";
                tabBtnCanvas.className = "px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5";
                contentUpload.classList.remove('hidden');
                contentCanvas.classList.add('hidden');
            }
        }

        // Initialize File Upload Logic & Drag-Drop
        function initFileUpload() {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileTtdInput');
            const placeholder = document.getElementById('uploadPlaceholder');
            const previewBox = document.getElementById('uploadPreviewBox');
            const previewImg = document.getElementById('filePreviewImg');
            const nameLabel = document.getElementById('fileNameLabel');
            const sizeLabel = document.getElementById('fileSizeLabel');

            if (!dropZone || !fileInput) return;

            dropZone.addEventListener('click', () => fileInput.click());

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.add('border-orange-500', 'bg-orange-50/40');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('border-orange-500', 'bg-orange-50/40');
                }, false);
            });

            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFileSelect(files[0]);
                }
            });

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    handleFileSelect(this.files[0]);
                }
            });

            function handleFileSelect(file) {
                const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Tidak Sesuai',
                        text: 'Silakan pilih file gambar dengan format PNG, JPG, atau JPEG.',
                        confirmButtonColor: '#ea580c'
                    });
                    fileInput.value = '';
                    return;
                }

                if (file.size > 3 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran Terlalu Besar',
                        text: 'Ukuran file gambar maksimal adalah 3 MB.',
                        confirmButtonColor: '#ea580c'
                    });
                    fileInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    nameLabel.textContent = file.name;
                    sizeLabel.textContent = (file.size / 1024).toFixed(1) + ' KB';
                    placeholder.classList.add('hidden');
                    previewBox.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }

            document.getElementById('formUploadTtd').addEventListener('submit', function(e) {
                if (!fileInput.files || fileInput.files.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum Ada File Dipilih',
                        text: 'Silakan pilih file gambar tanda tangan terlebih dahulu.',
                        confirmButtonColor: '#ea580c'
                    });
                    return false;
                }
            });
        }

        function resetFileUpload(e) {
            e.stopPropagation();
            const fileInput = document.getElementById('fileTtdInput');
            const placeholder = document.getElementById('uploadPlaceholder');
            const previewBox = document.getElementById('uploadPreviewBox');
            if (fileInput) fileInput.value = '';
            if (placeholder) placeholder.classList.remove('hidden');
            if (previewBox) previewBox.classList.add('hidden');
        }

        // SweetAlert Delete Confirmation
        function confirmDeleteTtd() {
            Swal.fire({
                title: 'Hapus Tanda Tangan?',
                text: 'Tanda tangan digital Anda akan dihapus permanen dari server. Anda dapat membuat atau mengunggah yang baru kapan saja.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteTtdForm').submit();
                }
            });
        }
    </script>
</body>
</html>
