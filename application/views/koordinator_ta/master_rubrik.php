<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Kelola Master Rubrik Penilaian Sidang TA'; ?> - IFIK Telkom University</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- TailwindCSS CDN -->
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
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background-color: #fbf7f1;
            color: #1e293b;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }
        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            border-radius: 1.25rem;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-violet-500 selection:text-white">

    <!-- Curved Sidebar Navigation -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="page-wrapper-for-sidebar flex flex-col min-h-screen flex-grow pb-16">
        
        <!-- Header Bar -->
        <header class="bg-white/85 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-6 lg:px-8 py-3.5 mb-6">
            <div class="max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <a href="<?= site_url('import-email'); ?>" class="w-10 h-10 rounded-xl bg-violet-50 border border-violet-200 text-violet-700 flex items-center justify-center font-bold text-lg hover:bg-violet-100 transition shrink-0" title="Kembali ke Panel Akun">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Form Dinamis Nilai Sidang (Master Rubrik &amp; Bobot)</h1>
                            <span class="bg-violet-100 text-violet-800 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full border border-violet-200 uppercase tracking-wider">Super Admin / Koordinator</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Formulir pengaturan indikator soal, kriteria penguji, dan bobot persentase (%) nilai sidang tugas akhir per Program Studi &amp; Peminatan.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="confirmResetDefaultMasterRubrik()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold shadow-2xs transition">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Reset Default Kurikulum</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Form Container -->
        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-6">

            <!-- 1. Selection & Version Bar Card -->
            <div class="card-custom p-5 sm:p-6 space-y-4 border-2 border-violet-200/80">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                        <div>
                            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                                <i class="fa-solid fa-graduation-cap text-violet-600 mr-1"></i> Program Studi (Prodi)
                            </label>
                            <select id="masterRubrikProdiSelect" onchange="onMasterRubrikProdiChange(this.value)" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-violet-500 outline-none shadow-2xs cursor-pointer">
                                <?php if (!empty($prodi_list)): ?>
                                    <?php foreach ($prodi_list as $p): ?>
                                        <option value="<?= htmlspecialchars($p['kode_prodi']) ?>"><?= htmlspecialchars($p['kode_prodi'] . ' - ' . $p['nama_prodi']) ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="DKV">DKV - Desain Komunikasi Visual</option>
                                    <option value="DI">DI - Desain Interior</option>
                                    <option value="DP">DP - Desain Produk</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                                <i class="fa-solid fa-tags text-violet-600 mr-1"></i> Peminatan / Konsentrasi
                            </label>
                            <select id="masterRubrikPeminatanSelect" onchange="onMasterRubrikPeminatanChange(this.value)" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-violet-500 outline-none shadow-2xs cursor-pointer">
                                <!-- Injected via JS -->
                            </select>
                        </div>
                    </div>

                    <!-- Version Status Badge -->
                    <div class="flex flex-wrap items-center gap-2 lg:self-end">
                        <span id="masterRubrikVersiBadge" class="px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider bg-violet-100 text-violet-800 border border-violet-300 flex items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-code-branch text-violet-600"></i>
                            <span id="masterRubrikVersiText">Versi 1</span>
                        </span>
                        <span id="masterRubrikStatusBadge" class="px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span id="masterRubrikStatusText">Published (Aktif)</span>
                        </span>
                        <div id="masterRubrikHistoryWrapper" class="hidden items-center gap-1.5 text-xs text-slate-600">
                            <span class="font-bold text-slate-500">Pilih Versi:</span>
                            <select id="masterRubrikVersionSelector" onchange="switchMasterRubrikVersion(this.value)" class="px-2.5 py-1 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-700 outline-none cursor-pointer">
                                <!-- Populated dynamically -->
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Judul Rubrik Input -->
                <div class="pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Judul / Nama Rubrik Penilaian Sidang:</label>
                    <input type="text" id="masterRubrikJudulInput" placeholder="Contoh: Rubrik Sidang Tugas Akhir DKV - Multimedia" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-violet-500 outline-none shadow-2xs">
                </div>

                <!-- Tanggal Publish Info -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-600">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-violet-600"></i>
                        <span><strong>Perlindungan Nilai Mahasiswa:</strong> Pembaharuan butir kriteria disimpan sebagai versi kurikulum baru agar nilai sidang terdahulu tetap valid.</span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <label class="text-[11px] font-bold text-slate-500">Waktu Publish:</label>
                        <input type="datetime-local" id="masterRubrikTglPublishInput" class="px-2 py-1 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-800 outline-none">
                        <button type="button" onclick="setMasterRubrikTglPublishNow()" class="px-2.5 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-lg text-xs transition">Sekarang</button>
                    </div>
                </div>
            </div>

            <!-- 2. Dynamic Criteria Items Container -->
            <div class="card-custom p-5 sm:p-7 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-violet-600"></i>
                            <span>Daftar Indikator / Butir Kriteria Penilaian</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Setiap butir kriteria akan tampil sebagai baris input skor pada lembar penilaian Dewan Penguji &amp; Pembimbing.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="balanceMasterRubrikWeights()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs border border-slate-200 transition shadow-2xs flex items-center gap-1.5">
                            <i class="fa-solid fa-scale-balanced text-slate-500"></i>
                            <span>Bagi Rata (100%)</span>
                        </button>
                        <button type="button" onclick="addMasterRubrikCriterion()" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>Tambah Kriteria Baru</span>
                        </button>
                    </div>
                </div>

                <!-- Criteria List Container -->
                <div id="masterRubrikKriteriaContainer" class="space-y-3.5">
                    <!-- Populated dynamically via JS -->
                </div>

                <!-- Bobot Total Validation Bar -->
                <div class="pt-5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Total Akumulasi Bobot:</div>
                        <div id="masterRubrikValidationCard" class="flex items-center gap-2">
                            <div id="masterRubrikTotalBobotText" class="px-4 py-1.5 rounded-xl font-mono text-sm font-black border transition shadow-xs bg-emerald-100 text-emerald-800 border-emerald-300">
                                100%
                            </div>
                            <span id="masterRubrikValidationStatusText" class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                <i class="fa-solid fa-check-circle"></i> Bobot Sempurna (100%)
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <button type="button" id="btnSaveMasterRubrik" onclick="submitSaveMasterRubrik()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition shadow-2xs flex items-center gap-1.5">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                        <button type="button" id="btnPublishDirectMasterRubrik" onclick="submitPublishMasterRubrik()" class="px-6 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white text-xs font-extrabold rounded-xl shadow-md shadow-violet-500/20 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span id="btnPublishDirectText">Publish Versi Baru</span>
                        </button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- JavaScript Module using Koordinator TA Endpoints -->
    <script>
        const CFG_RUBRIK = {
            ajaxGetAllMasterRubrikUrl: "<?= site_url('koordinatorta/ajax_get_all_master_rubrik'); ?>",
            ajaxSimpanMasterRubrikUrl: "<?= site_url('koordinatorta/ajax_simpan_master_rubrik'); ?>",
            ajaxPublishMasterRubrikUrl: "<?= site_url('koordinatorta/ajax_publish_master_rubrik'); ?>",
            ajaxResetDefaultRubrikUrl: "<?= site_url('koordinatorta/ajax_reset_default_rubrik'); ?>",
            prodiList: <?= json_encode($prodi_list ?? []); ?>,
            peminatanList: <?= json_encode($peminatan_list ?? []); ?>
        };

        const RUBRIK_PENILAIAN_PRODI = {
            'DKV': {
                name: 'Desain Komunikasi Visual',
                peminatan: {
                    'Multimedia': [
                        { kode: 'k1', title: 'Konsep & Storyboard Multimedia', desc: 'Kedalaman gagasan, orisinalitas ide, dan perancangan storyboard interaktif.', bobot: 30 },
                        { kode: 'k2', title: 'Penguasaan Teknis Audio Visual', desc: 'Kualitas komposisi visual, tata suara, animasi, dan ketepatan render.', bobot: 25 },
                        { kode: 'k3', title: 'Interaktivitas & UX Flow', desc: 'Kemudahan navigasi dan pengalaman interaksi pengguna pada karya.', bobot: 25 },
                        { kode: 'k4', title: 'Komprehensi & Presentasi Sidang', desc: 'Kelancaran penyampaian dan kemampuan mempertahankan argumentasi di hadapan penguji.', bobot: 20 }
                    ],
                    'Game': [
                        { kode: 'k1', title: 'Game Design Document (GDD)', desc: 'Struktur mekanisme gameplay, aturan, dan narasi dunia game.', bobot: 30 },
                        { kode: 'k2', title: 'Asset Art & Visual Style', desc: 'Karakter, enviroment, dan konsistensi estetika visual game.', bobot: 25 },
                        { kode: 'k3', title: 'Implementasi & Playability', desc: 'Stabilitas prototipe game, ketiadaan bug fatal, dan keseruan bermain.', bobot: 25 },
                        { kode: 'k4', title: 'Presentasi & Respon Penguji', desc: 'Penguasaan materi dan kemampuan demonstrasi karya secara komprehensif.', bobot: 20 }
                    ],
                    'Designpreneur': [
                        { kode: 'k1', title: 'Analisis Pasar & Validasi Produk', desc: 'Riset pasar, identifikasi target audiens, dan kebaruan value proposition.', bobot: 30 },
                        { kode: 'k2', title: 'Perancangan Brand Identity & Kemasan', desc: 'Sistem identitas visual, kemasan, dan materi promosi produk.', bobot: 30 },
                        { kode: 'k3', title: 'Kelayakan Bisnis & Monetisasi', desc: 'Rencana operasional bisnis, estimasi biaya, dan strategi pemasaran.', bobot: 20 },
                        { kode: 'k4', title: 'Pitching & Argumentasi Sidang', desc: 'Kemampuan pitching profesional dalam menjawab pertanyaan dewan penguji.', bobot: 20 }
                    ],
                    'VIID': [
                        { kode: 'k1', title: 'Identifikasi Masalah & Riset Desain', desc: 'Metodologi perancangan dan ketepatan pemecahan masalah visual.', bobot: 30 },
                        { kode: 'k2', title: 'Kekuatan Visual & Tipografi', desc: 'Keunikan layout, hierarki informasi, tipografi, dan ilustrasi.', bobot: 30 },
                        { kode: 'k3', title: 'Penerapan Media Terintegrasi', desc: 'Konsistensi implementasi desain lintas media cetak dan digital.', bobot: 20 },
                        { kode: 'k4', title: 'Penyampaian & Uji Karya', desc: 'Sistematika presentasi dan kemampuan mempertahankan hasil riset desain.', bobot: 20 }
                    ]
                }
            },
            'DI': {
                name: 'Desain Interior',
                peminatan: {
                    'Desain Interior': [
                        { kode: 'k1', title: 'Konsep Spatial & Program Ruang', desc: 'Kekuatan konsep perancangan interior, zonasi, dan alur sirkulasi.', bobot: 30 },
                        { kode: 'k2', title: 'Gambar Kerja & Detail Konstruksi', desc: 'Kelengkapan gambar denah, potongan, detail furniture, dan ketepatan skala.', bobot: 25 },
                        { kode: 'k3', title: 'Materialitas, Pencahayaan & Akustik', desc: 'Eksplorasi spesifikasi material, pencahayaan alami/buatan, dan tata suara ruang.', bobot: 25 },
                        { kode: 'k4', title: 'Presentasi & Respon Sidang', desc: 'Kerapian maket/render 3D dan argumentasi terhadap pertanyaan dewan penguji.', bobot: 20 }
                    ]
                }
            },
            'DIB': {
                name: 'Desain Interior Bisnis',
                peminatan: {
                    'Desain Interior Bisnis': [
                        { kode: 'k1', title: 'Riset Pasar Komersial & Feasibility', desc: 'Analisis kelayakan bisnis ritel/hospitality dan relevansi brand image.', bobot: 30 },
                        { kode: 'k2', title: 'Perancangan Spatial Komersial', desc: 'Inovasi tata ruang komersial, display produk, dan customer experience.', bobot: 30 },
                        { kode: 'k3', title: 'Estimasi Biaya & Spesifikasi Teknis', desc: 'Rencana anggaran biaya (RAB), spesifikasi fixture, dan standarisasi material.', bobot: 20 },
                        { kode: 'k4', title: 'Presentasi Bisnis & Sidang', desc: 'Pitching rancangan interior berorientasi bisnis kepada dewan penguji.', bobot: 20 }
                    ]
                }
            },
            'DP': {
                name: 'Desain Produk',
                peminatan: {
                    'Desain Produk': [
                        { kode: 'k1', title: 'Problem Solving & Riset Pengguna', desc: 'Metodologi perancangan produk, identifikasi kebutuhan, dan ergonomi.', bobot: 30 },
                        { kode: 'k2', title: 'Eksplorasi Bentuk, Material & Manufaktur', desc: 'Kekuatan sketsa desain, pemilihan material, dan pertimbangan proses produksi massal.', bobot: 25 },
                        { kode: 'k3', title: 'Purwarupa / Prototipe Fungsional', desc: 'Ketepatan skala, fungsionalitas purwarupa produk, dan uji coba pakai.', bobot: 25 },
                        { kode: 'k4', title: 'Presentasi Teknis & Sidang', desc: 'Kualitas portofolio/lembar presentasi dan penguasaan materi teknis saat sidang.', bobot: 20 }
                    ]
                }
            }
        };

        let masterRubrikList = [];
        let activeKriteria = [];

        function fetchMasterRubrikList(callback) {
            fetch(CFG_RUBRIK.ajaxGetAllMasterRubrikUrl)
                .then(r => r.json())
                .then(res => {
                    if (res && res.status && Array.isArray(res.data)) {
                        masterRubrikList = res.data;
                    }
                    if (typeof callback === 'function') callback();
                })
                .catch(err => {
                    console.error('Error fetching rubriks:', err);
                    if (typeof callback === 'function') callback();
                });
        }

        function onMasterRubrikProdiChange(prodiVal) {
            setupMasterRubrikPeminatanOptions(prodiVal);
            onMasterRubrikPeminatanChange();
        }

        function setupMasterRubrikPeminatanOptions(prodiKey) {
            const pemSelect = document.getElementById('masterRubrikPeminatanSelect');
            if (!pemSelect) return;

            let list = [];
            if (Array.isArray(CFG_RUBRIK.peminatanList) && CFG_RUBRIK.peminatanList.length > 0) {
                const filtered = CFG_RUBRIK.peminatanList.filter(item => item.kode_prodi === prodiKey);
                if (filtered.length > 0) {
                    list = filtered.map(item => item.nama_peminatan);
                }
            }

            if (list.length === 0) {
                const prodiData = RUBRIK_PENILAIAN_PRODI[prodiKey] || RUBRIK_PENILAIAN_PRODI['DKV'];
                if (prodiData && prodiData.peminatan) {
                    list = Object.keys(prodiData.peminatan);
                }
            }

            if (list.length === 0) {
                list = ['Umum'];
            }

            let html = '';
            list.forEach(p => {
                html += `<option value="${p}">${p}</option>`;
            });
            pemSelect.innerHTML = html;
        }

        function onMasterRubrikPeminatanChange() {
            const prodi = document.getElementById('masterRubrikProdiSelect').value;
            const peminatan = document.getElementById('masterRubrikPeminatanSelect').value;

            const versions = masterRubrikList.filter(r => r.prodi === prodi && r.peminatan === peminatan);
            const historyWrapper = document.getElementById('masterRubrikHistoryWrapper');
            const versionSelector = document.getElementById('masterRubrikVersionSelector');

            if (versions.length > 1) {
                historyWrapper.classList.remove('hidden');
                historyWrapper.classList.add('flex');
                versionSelector.innerHTML = '';
                versions.sort((a,b) => (b.versi || 1) - (a.versi || 1));
                versions.forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v.versi;
                    opt.textContent = `Versi ${v.versi} (${v.status})`;
                    versionSelector.appendChild(opt);
                });
            } else {
                historyWrapper.classList.add('hidden');
                historyWrapper.classList.remove('flex');
            }

            loadMasterRubrikIntoEditor();
        }

        function switchMasterRubrikVersion(versi) {
            const prodi = document.getElementById('masterRubrikProdiSelect').value;
            const peminatan = document.getElementById('masterRubrikPeminatanSelect').value;
            const item = masterRubrikList.find(r => r.prodi === prodi && r.peminatan === peminatan && parseInt(r.versi) === parseInt(versi));
            if (item) {
                populateEditorFromItem(item);
            }
        }

        function loadMasterRubrikIntoEditor() {
            const prodi = document.getElementById('masterRubrikProdiSelect').value;
            const peminatan = document.getElementById('masterRubrikPeminatanSelect').value;

            let item = masterRubrikList.find(r => r.prodi === prodi && r.peminatan === peminatan && (r.is_active == 1 || r.status === 'published'));
            if (!item) {
                item = masterRubrikList.find(r => r.prodi === prodi && r.peminatan === peminatan);
            }

            if (item) {
                populateEditorFromItem(item);
            } else {
                const presetList = (RUBRIK_PENILAIAN_PRODI[prodi] && RUBRIK_PENILAIAN_PRODI[prodi].peminatan[peminatan]) 
                    ? JSON.parse(JSON.stringify(RUBRIK_PENILAIAN_PRODI[prodi].peminatan[peminatan])) 
                    : [];
                
                document.getElementById('masterRubrikJudulInput').value = `Rubrik Sidang Tugas Akhir ${prodi} - ${peminatan}`;
                document.getElementById('masterRubrikVersiText').textContent = 'Versi 1 (Preset Bawaan)';
                document.getElementById('masterRubrikStatusText').textContent = 'Standar Sistem';
                setMasterRubrikTglPublishNow();
                
                activeKriteria = presetList;
                renderMasterRubrikKriteriaRows();
                calculateMasterRubrikTotal();
            }
        }

        function populateEditorFromItem(item) {
            document.getElementById('masterRubrikJudulInput').value = item.judul_rubrik || `Rubrik Sidang Tugas Akhir ${item.prodi} - ${item.peminatan}`;
            document.getElementById('masterRubrikVersiText').textContent = `Versi ${item.versi || 1}`;
            
            const isPub = (item.status === 'published' || item.is_active == 1);
            const statusBadge = document.getElementById('masterRubrikStatusBadge');
            const statusText = document.getElementById('masterRubrikStatusText');
            
            if (isPub) {
                statusBadge.className = 'px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1.5 shadow-2xs';
                statusText.textContent = 'Published (Aktif Digunakan)';
            } else {
                statusBadge.className = 'px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1.5 shadow-2xs';
                statusText.textContent = 'Draft Kurikulum';
            }

            if (item.tanggal_publish) {
                try {
                    const d = new Date(item.tanggal_publish);
                    document.getElementById('masterRubrikTglPublishInput').value = d.toISOString().slice(0, 16);
                } catch(e) {
                    setMasterRubrikTglPublishNow();
                }
            } else {
                setMasterRubrikTglPublishNow();
            }

            let parsedCriteria = [];
            try {
                if (typeof item.kriteria === 'string') {
                    parsedCriteria = JSON.parse(item.kriteria);
                } else if (Array.isArray(item.kriteria)) {
                    parsedCriteria = item.kriteria;
                }
            } catch(e) {
                console.error(e);
            }

            activeKriteria = Array.isArray(parsedCriteria) && parsedCriteria.length > 0 ? parsedCriteria : [];
            renderMasterRubrikKriteriaRows();
            calculateMasterRubrikTotal();
        }

        function renderMasterRubrikKriteriaRows() {
            const container = document.getElementById('masterRubrikKriteriaContainer');
            if (!container) return;
            container.innerHTML = '';

            if (activeKriteria.length === 0) {
                container.innerHTML = `
                    <div class="p-8 text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl">
                        <i class="fa-solid fa-folder-open text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs font-bold text-slate-500">Belum ada butir kriteria penilaian untuk prodi ini.</p>
                        <button type="button" onclick="addMasterRubrikCriterion()" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-600 text-white font-bold text-xs rounded-lg hover:bg-violet-700">
                            <i class="fa-solid fa-plus"></i> Tambah Kriteria Pertama
                        </button>
                    </div>
                `;
                return;
            }

            activeKriteria.forEach((crit, idx) => {
                const row = document.createElement('div');
                row.className = 'p-4 rounded-xl border border-slate-200 bg-white hover:border-violet-300 transition space-y-3';
                row.innerHTML = `
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-violet-100 text-violet-700 font-black text-xs flex items-center justify-center font-mono">
                                ${idx + 1}
                            </span>
                            <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Kriteria #${idx + 1}</span>
                        </div>
                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            <div class="flex items-center gap-1 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                                <label class="text-[11px] font-bold text-slate-500 uppercase">Bobot:</label>
                                <input type="number" min="1" max="100" value="${crit.bobot || 0}" onchange="updateCriterionWeight(${idx}, this.value)" class="w-16 px-1.5 py-0.5 text-xs font-mono font-black text-slate-900 bg-white border border-slate-300 rounded text-center outline-none focus:ring-1 focus:ring-violet-500">
                                <span class="text-xs font-bold text-slate-500">%</span>
                            </div>
                            <button type="button" onclick="removeMasterRubrikCriterion(${idx})" class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white transition flex items-center justify-center text-xs cursor-pointer" title="Hapus Kriteria">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="md:col-span-1">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nama Indikator / Kriteria:</label>
                            <input type="text" value="${escapeHtml(crit.title || crit.nama_kriteria || '')}" onchange="updateCriterionTitle(${idx}, this.value)" placeholder="Contoh: Konsep & Metodologi" class="w-full px-3 py-1.5 text-xs font-bold text-slate-900 bg-white border border-slate-300 rounded-lg outline-none focus:ring-1 focus:ring-violet-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Deskripsi & Panduan Penguji:</label>
                            <textarea rows="1" onchange="updateCriterionDesc(${idx}, this.value)" placeholder="Panduan rubrik bagi penguji saat memberikan skor..." class="w-full px-3 py-1.5 text-xs text-slate-700 bg-white border border-slate-300 rounded-lg outline-none focus:ring-1 focus:ring-violet-500">${escapeHtml(crit.desc || crit.deskripsi || '')}</textarea>
                        </div>
                    </div>
                `;
                container.appendChild(row);
            });
        }

        function updateCriterionTitle(idx, val) {
            if (activeKriteria[idx]) activeKriteria[idx].title = val;
        }

        function updateCriterionDesc(idx, val) {
            if (activeKriteria[idx]) activeKriteria[idx].desc = val;
        }

        function updateCriterionWeight(idx, val) {
            if (activeKriteria[idx]) activeKriteria[idx].bobot = parseFloat(val) || 0;
            calculateMasterRubrikTotal();
        }

        function addMasterRubrikCriterion() {
            activeKriteria.push({
                kode: 'k' + (activeKriteria.length + 1),
                title: 'Butir Kriteria Baru',
                desc: 'Deskripsi acuan indikator penilaian dewan penguji.',
                bobot: 10
            });
            renderMasterRubrikKriteriaRows();
            calculateMasterRubrikTotal();
        }

        function removeMasterRubrikCriterion(idx) {
            activeKriteria.splice(idx, 1);
            renderMasterRubrikKriteriaRows();
            calculateMasterRubrikTotal();
        }

        function balanceMasterRubrikWeights() {
            const count = activeKriteria.length;
            if (count === 0) return;
            const equal = Math.floor(100 / count);
            const rem = 100 - (equal * count);

            activeKriteria.forEach((item, idx) => {
                item.bobot = equal + (idx === 0 ? rem : 0);
            });

            renderMasterRubrikKriteriaRows();
            calculateMasterRubrikTotal();
        }

        function calculateMasterRubrikTotal() {
            let total = 0;
            activeKriteria.forEach(c => total += parseFloat(c.bobot) || 0);

            const totalText = document.getElementById('masterRubrikTotalBobotText');
            const statusText = document.getElementById('masterRubrikValidationStatusText');

            totalText.textContent = `${total}%`;

            if (total === 100) {
                totalText.className = 'px-4 py-1.5 rounded-xl font-mono text-sm font-black border transition shadow-xs bg-emerald-100 text-emerald-800 border-emerald-300';
                statusText.innerHTML = '<i class="fa-solid fa-check-circle"></i> Bobot Sempurna (100%)';
                statusText.className = 'text-xs font-bold text-emerald-600 flex items-center gap-1';
            } else {
                totalText.className = 'px-4 py-1.5 rounded-xl font-mono text-sm font-black border transition shadow-xs bg-rose-100 text-rose-800 border-rose-300 animate-pulse';
                const diff = 100 - total;
                const sign = diff > 0 ? `Kurang ${diff}%` : `Lebih ${Math.abs(diff)}%`;
                statusText.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> Total harus tepat 100% (${sign})`;
                statusText.className = 'text-xs font-bold text-rose-600 flex items-center gap-1';
            }
        }

        function setMasterRubrikTglPublishNow() {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('masterRubrikTglPublishInput').value = now.toISOString().slice(0, 16);
        }

        function submitSaveMasterRubrik() {
            saveMasterRubrikAction(false);
        }

        function submitPublishMasterRubrik() {
            saveMasterRubrikAction(true);
        }

        function saveMasterRubrikAction(isPublish) {
            const prodi = document.getElementById('masterRubrikProdiSelect').value;
            const peminatan = document.getElementById('masterRubrikPeminatanSelect').value;
            const judul = document.getElementById('masterRubrikJudulInput').value.trim();
            const tgl = document.getElementById('masterRubrikTglPublishInput').value;

            if (!judul) {
                Swal.fire({ icon: 'warning', title: 'Judul Wajib Diisi', text: 'Silakan isi judul rubrik penilaian terlebih dahulu.' });
                return;
            }

            let total = 0;
            activeKriteria.forEach(c => total += parseFloat(c.bobot) || 0);
            if (isPublish && total !== 100) {
                Swal.fire({ icon: 'error', title: 'Total Bobot Tidak 100%', text: `Total bobot saat ini ${total}%. Rubrik hanya dapat dipublikasikan jika total tepat 100%.` });
                return;
            }

            const url = isPublish ? CFG_RUBRIK.ajaxPublishMasterRubrikUrl : CFG_RUBRIK.ajaxSimpanMasterRubrikUrl;
            const formData = new FormData();
            formData.append('prodi', prodi);
            formData.append('peminatan', peminatan);
            formData.append('judul_rubrik', judul);
            formData.append('kriteria', JSON.stringify(activeKriteria));
            formData.append('status', isPublish ? 'published' : 'draft');
            formData.append('is_publish', isPublish ? 1 : 0);
            formData.append('tanggal_publish', tgl);
            formData.append('as_new_version', isPublish ? 1 : 0);

            Swal.fire({
                title: isPublish ? 'Publikasikan Versi Baru?' : 'Simpan Perubahan Rubrik?',
                text: isPublish ? 'Rubrik ini akan dipublikasikan sebagai kurikulum aktif acuan dewan penguji.' : 'Perubahan akan disimpan pada versi ini.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: isPublish ? 'Ya, Publikasikan' : 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    fetch(url, { method: 'POST', body: formData })
                    .then(r => r.json())
                    .then(res => {
                        if (res && res.status) {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message || 'Master rubrik berhasil disimpan.' })
                            .then(() => {
                                fetchMasterRubrikList(() => {
                                    onMasterRubrikPeminatanChange();
                                });
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: (res && res.message) ? res.message : 'Terjadi kesalahan saat menyimpan.' });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server.' });
                    });
                }
            });
        }

        function confirmResetDefaultMasterRubrik() {
            Swal.fire({
                title: 'Reset ke Kurikulum Standar?',
                text: 'Seluruh master rubrik penilaian prodi dan peminatan akan dikembalikan ke standar kurikulum bawaan default!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                confirmButtonText: 'Ya, Reset Default',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    Swal.showLoading();
                    fetch(CFG_RUBRIK.ajaxResetDefaultRubrikUrl, { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.status) {
                            Swal.fire({ icon: 'success', title: 'Berhasil Direset', text: data.message })
                            .then(() => {
                                fetchMasterRubrikList(() => {
                                    onMasterRubrikPeminatanChange();
                                });
                            });
                        }
                    })
                    .catch(err => console.error(err));
                }
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            fetchMasterRubrikList(() => {
                setupMasterRubrikPeminatanOptions('DKV');
                onMasterRubrikPeminatanChange();
            });
        });
    </script>

    <!-- Global Custom Circle Cursor -->
    <?php $this->load->view('partials/custom_cursor'); ?>
</body>
</html>
