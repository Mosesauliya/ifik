<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Manajemen Perwalian & Data Akademik - IFIK' ?></title>

    <!-- Tailwind CSS & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- SheetJS for client-side Excel handling -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74',
                            400: '#fb923c', 500: '#f97316', 600: '#ea580c', 700: '#c2410c',
                            800: '#9a3412', 900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
        
        .clay-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(226, 232, 240, 0.8);
            transition: all 0.2s ease;
        }

        .stat-card-3d {
            border-radius: 18px;
            padding: 18px 20px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06);
        }

        .tab-btn {
            padding: 10px 20px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .tab-btn.active {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(234, 88, 12, 0.3);
        }
        .tab-btn:not(.active) {
            background: #ffffff;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .tab-btn:not(.active):hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .btn-brand-primary {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            color: white;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.3);
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-brand-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(234, 88, 12, 0.4);
        }

        .dropzone-box {
            border: 2px dashed #cbd5e1;
            border-radius: 18px;
            background: #f8fafc;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .dropzone-box:hover, .dropzone-box.dragover {
            border-color: #ea580c;
            background: #fff7ed;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50">

    <!-- Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Container -->
    <main class="page-wrapper-for-sidebar transition-all duration-300 min-h-screen p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Header Card -->
            <div class="clay-card p-6 sm:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6 border-l-8 border-brand-500">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="p-2.5 bg-orange-100 text-brand-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </span>
                        <div>
                            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Perwalian & Akademik</h1>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Setup Master Data Mata Kuliah, Mahasiswa & Import Rekap KRS Kolektif</p>
                        </div>
                    </div>
                </div>

                <!-- Periode & Prodi Filter Badges -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="px-3.5 py-1.5 bg-orange-50 border border-orange-200 text-brand-700 text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm">
                        <span>🗓️</span> <?= htmlspecialchars($tahun_ajaran) ?> (<?= htmlspecialchars($semester) ?>)
                    </span>
                    <div class="flex items-center gap-1.5">
                        <select onchange="window.location.href='<?= site_url('prodi/perwalian') ?>?prodi=' + encodeURIComponent(this.value) + '&tab=<?= htmlspecialchars($active_tab) ?>'" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-800 text-xs font-bold rounded-xl shadow-xs outline-none focus:ring-2 focus:ring-brand-500 cursor-pointer">
                            <option value="all" <?= ($selected_prodi === 'all') ? 'selected' : '' ?>>🎓 Semua Program Studi</option>
                            <?php foreach($prodi_list as $p): 
                                $pName = $p['nama_prodi'] ?? '';
                            ?>
                                <option value="<?= htmlspecialchars($pName) ?>" <?= ($selected_prodi === $pName) ? 'selected' : '' ?>>
                                    🎓 <?= htmlspecialchars($pName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 4 Metric Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="stat-card-3d bg-gradient-to-br from-amber-500/10 to-orange-500/10 border-orange-200">
                    <p class="text-xs font-semibold text-orange-700 uppercase tracking-wider">Total MK Terdaftar</p>
                    <div class="text-2xl sm:text-3xl font-extrabold text-orange-900 mt-1"><?= count($mata_kuliah) ?></div>
                    <span class="text-[11px] text-orange-600 font-medium">Mata kuliah prodi aktif</span>
                </div>
                <div class="stat-card-3d bg-gradient-to-br from-blue-500/10 to-indigo-500/10 border-blue-200">
                    <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Total Mahasiswa</p>
                    <div class="text-2xl sm:text-3xl font-extrabold text-blue-900 mt-1"><?= count($list_mahasiswa) ?></div>
                    <span class="text-[11px] text-blue-600 font-medium">Mahasiswa terdata</span>
                </div>
                <div class="stat-card-3d bg-gradient-to-br from-emerald-500/10 to-teal-500/10 border-emerald-200">
                    <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Perwalian Disetujui</p>
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-900 mt-1"><?= (int)($stats['disetujui'] ?? 0) ?></div>
                    <span class="text-[11px] text-emerald-600 font-medium">Tahap 1 selesai</span>
                </div>
                <div class="stat-card-3d bg-gradient-to-br from-rose-500/10 to-red-500/10 border-rose-200">
                    <p class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Menunggu Persetujuan</p>
                    <div class="text-2xl sm:text-3xl font-extrabold text-rose-900 mt-1"><?= (int)($stats['diajukan'] ?? 0) ?></div>
                    <span class="text-[11px] text-rose-600 font-medium">Perlu review Dosen Wali</span>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <button onclick="switchTab('matakuliah')" id="tab-btn-matakuliah" class="tab-btn active">
                    <span>📚</span> Master Mata Kuliah
                </button>
                <button onclick="switchTab('mahasiswa')" id="tab-btn-mahasiswa" class="tab-btn">
                    <span>👥</span> Mahasiswa & Dosen Wali
                </button>
                <button onclick="switchTab('importkrs')" id="tab-btn-importkrs" class="tab-btn">
                    <span>📥</span> Import Rekap KRS Kolektif
                </button>
                <button onclick="switchTab('monitoring')" id="tab-btn-monitoring" class="tab-btn">
                    <span>📊</span> Monitoring Perwalian
                </button>
            </div>

            <!-- =================================================================== -->
            <!-- TAB 1: MASTER MATA KULIAH -->
            <!-- =================================================================== -->
            <div id="tab-content-matakuliah" class="tab-pane space-y-4">
                <div class="clay-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-200">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Daftar Kurikulum & Mata Kuliah</h2>
                            <p class="text-xs text-slate-500">Master mata kuliah yang dapat dikontrak oleh mahasiswa atau dinilai dosen.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button onclick="openModalTambahMK()" class="btn-brand-primary px-3.5 py-2 text-xs flex items-center gap-1.5">
                                <span>+</span> Tambah MK
                            </button>
                            <button onclick="openModalImportMK()" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1.5 transition">
                                <span>📥</span> Import Excel
                            </button>
                            <button onclick="downloadTemplateMK()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold border border-slate-200 flex items-center gap-1.5 transition">
                                <span>📄</span> Template XLSX
                            </button>
                        </div>
                    </div>

                    <!-- Search & Filter Filter -->
                    <div class="mt-4 flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <input type="text" id="searchMK" onkeyup="filterTableMK()" placeholder="Cari Kode atau Nama Mata Kuliah..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500 outline-none">
                            <span class="absolute left-3 top-2.5 text-slate-400">🔍</span>
                        </div>
                        <select id="filterSemesterMK" onchange="filterTableMK()" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none">
                            <option value="all">Semua Semester</option>
                            <?php for($i=1; $i<=8; $i++): ?>
                                <option value="<?= $i ?>">Semester <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Table MK -->
                    <div class="overflow-x-auto mt-4 rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs" id="tableMK">
                            <thead class="bg-slate-100/80 text-slate-700 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="p-3 text-center w-12">No</th>
                                    <th class="p-3">Kode MK</th>
                                    <th class="p-3">Nama Mata Kuliah</th>
                                    <th class="p-3 text-center">SKS</th>
                                    <th class="p-3 text-center">Semester</th>
                                    <th class="p-3">Prodi</th>
                                    <th class="p-3 text-center">Jenis</th>
                                    <th class="p-3 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if(empty($mata_kuliah)): ?>
                                    <tr>
                                        <td colspan="8" class="p-8 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <span class="text-3xl mb-2">📂</span>
                                                <p class="font-semibold text-slate-600">Belum ada data mata kuliah</p>
                                                <p class="text-[11px] text-slate-400 mt-0.5">Silakan tambahkan manual atau import dari file Excel kurikulum.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach($mata_kuliah as $mk): ?>
                                        <tr class="hover:bg-orange-50/40 transition mk-row" data-search="<?= strtolower(htmlspecialchars($mk['kode_mk'] . ' ' . $mk['nama_mk'])) ?>" data-semester="<?= (int)$mk['semester'] ?>">
                                            <td class="p-3 text-center text-slate-400 font-mono"><?= $no++ ?></td>
                                            <td class="p-3 font-mono font-bold text-brand-700"><?= htmlspecialchars($mk['kode_mk']) ?></td>
                                            <td class="p-3 font-semibold text-slate-800"><?= htmlspecialchars($mk['nama_mk']) ?></td>
                                            <td class="p-3 text-center font-bold text-slate-700"><?= (int)$mk['sks'] ?></td>
                                            <td class="p-3 text-center">
                                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md font-semibold text-[11px]">Smt <?= (int)$mk['semester'] ?></span>
                                            </td>
                                            <td class="p-3 text-slate-600"><?= htmlspecialchars($mk['prodi'] ?: '-') ?></td>
                                            <td class="p-3 text-center">
                                                <span class="px-2 py-0.5 <?= ($mk['jenis'] === 'Wajib') ? 'bg-orange-100 text-brand-800' : 'bg-slate-100 text-slate-600' ?> rounded-md font-semibold text-[11px]"><?= htmlspecialchars($mk['jenis'] ?: 'Wajib') ?></span>
                                            </td>
                                            <td class="p-3 text-center">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <button onclick='openModalEditMK(<?= json_encode($mk) ?>)' class="p-1.5 hover:bg-slate-200 text-slate-600 rounded-lg transition" title="Edit Mata Kuliah">
                                                        ✏️
                                                    </button>
                                                    <button onclick="hapusMK(<?= $mk['id'] ?>, '<?= htmlspecialchars($mk['kode_mk']) ?>')" class="p-1.5 hover:bg-rose-100 text-rose-600 rounded-lg transition" title="Hapus">
                                                        🗑️
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- =================================================================== -->
            <!-- TAB 2: MAHASISWA & DOSEN WALI -->
            <!-- =================================================================== -->
            <div id="tab-content-mahasiswa" class="tab-pane hidden space-y-4">
                <div class="clay-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-200">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Plotting Mahasiswa & Dosen Wali</h2>
                            <p class="text-xs text-slate-500">Atur penugasan Dosen Wali untuk setiap mahasiswa agar perwalian langsung masuk ke akun dosen yang bersangkutan.</p>
                        </div>
                        <div>
                            <a href="<?= site_url('import-email') ?>" class="btn-brand-primary px-3.5 py-2 text-xs flex items-center gap-1.5 inline-flex">
                                <span>📥</span> Import Akun Mahasiswa
                            </a>
                        </div>
                    </div>

                    <!-- Filter & Search Mahasiswa -->
                    <div class="mt-4 flex gap-3">
                        <div class="relative flex-1">
                            <input type="text" id="searchMhs" onkeyup="filterTableMhs()" placeholder="Cari NIM, Nama Mahasiswa, atau Dosen Wali..." class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500 outline-none">
                            <span class="absolute left-3 top-2.5 text-slate-400">🔍</span>
                        </div>
                    </div>

                    <!-- Table Mahasiswa -->
                    <div class="overflow-x-auto mt-4 rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs" id="tableMhs">
                            <thead class="bg-slate-100/80 text-slate-700 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="p-3 text-center w-12">No</th>
                                    <th class="p-3">NIM</th>
                                    <th class="p-3">Nama Mahasiswa</th>
                                    <th class="p-3">Program Studi</th>
                                    <th class="p-3">Dosen Wali Ditugaskan</th>
                                    <th class="p-3 text-center w-28">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if(empty($list_mahasiswa)): ?>
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data mahasiswa terdaftar.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach($list_mahasiswa as $mhs): 
                                        $currentNip = trim($mhs['dosen_wali'] ?? '');
                                    ?>
                                        <tr class="hover:bg-slate-50/60 transition mhs-row" data-search="<?= strtolower(htmlspecialchars($mhs['nim'] . ' ' . $mhs['name'] . ' ' . $currentNip)) ?>">
                                            <td class="p-3 text-center text-slate-400 font-mono"><?= $no++ ?></td>
                                            <td class="p-3 font-mono font-bold text-slate-800"><?= htmlspecialchars($mhs['nim'] ?: ($mhs['username'] ?: '-')) ?></td>
                                            <td class="p-3 font-semibold text-slate-800"><?= htmlspecialchars($mhs['name']) ?></td>
                                            <td class="p-3 text-slate-600"><?= htmlspecialchars($mhs['prodi'] ?: '-') ?></td>
                                            <td class="p-3">
                                                <select onchange="updateDosenWali('<?= htmlspecialchars($mhs['nim'] ?: $mhs['username']) ?>', this.value)" class="w-full max-w-xs px-2.5 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-brand-500">
                                                    <option value="">-- Belum Ditentukan --</option>
                                                    <?php foreach($dosen_wali as $dw): ?>
                                                        <option value="<?= htmlspecialchars($dw['nip'] ?: $dw['username']) ?>" <?= ($currentNip === ($dw['nip'] ?: $dw['username'])) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($dw['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="p-3 text-center">
                                                <?php if(!empty($currentNip)): ?>
                                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full font-semibold text-[11px]">Terpetakan</span>
                                                <?php else: ?>
                                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded-full font-semibold text-[11px]">Belum Ada</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- =================================================================== -->
            <!-- TAB 3: IMPORT REKAP KRS KOLEKTIF DARI SIAKAD -->
            <!-- =================================================================== -->
            <div id="tab-content-importkrs" class="tab-pane hidden space-y-4">
                <div class="clay-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-200">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Import Rekapitulasi KRS (SIAKAD / i-Gracias)</h2>
                            <p class="text-xs text-slate-500">Upload file Excel rekap KRS mahasiswa dari sistem pusat kampus. Sistem akan otomatis mengisi daftar mata kuliah yang diambil mahasiswa.</p>
                        </div>
                        <button onclick="downloadTemplateKRS()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold border border-slate-200 flex items-center gap-1.5 transition">
                            <span>📄</span> Unduh Template Excel KRS
                        </button>
                    </div>

                    <!-- Dropzone -->
                    <div class="mt-6">
                        <input type="file" id="fileKRSInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleKRSFileSelect(event)">
                        <div onclick="document.getElementById('fileKRSInput').click()" class="dropzone-box p-8 text-center" id="krsDropzone">
                            <span class="text-4xl">📊</span>
                            <h3 class="text-sm font-bold text-slate-800 mt-2">Klik atau Tarik File Rekap KRS Excel (.xlsx / .csv) ke Sini</h3>
                            <p class="text-xs text-slate-500 mt-1">Kolom yang dikenali: <strong>NIM</strong>, <strong>Kode MK</strong>, <strong>Tahun Ajaran</strong> (opsional), <strong>Semester</strong> (opsional).</p>
                        </div>
                    </div>

                    <!-- Preview Container -->
                    <div id="krsPreviewContainer" class="hidden mt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <span>📋</span> Preview Data KRS (<span id="krsPreviewCount">0</span> baris terdeteksi)
                            </h3>
                            <button onclick="submitImportKRS()" id="btnSubmitKRS" class="btn-brand-primary px-4 py-2 text-xs flex items-center gap-2">
                                <span>🚀</span> Simpan & Masukkan ke Perwalian Mahasiswa
                            </button>
                        </div>

                        <div class="overflow-x-auto max-h-80 rounded-xl border border-slate-200">
                            <table class="w-full text-left text-xs" id="krsPreviewTable">
                                <thead class="bg-slate-100 text-slate-700 sticky top-0 font-bold uppercase text-[11px]">
                                    <tr>
                                        <th class="p-2.5 text-center w-12">No</th>
                                        <th class="p-2.5">NIM</th>
                                        <th class="p-2.5">Kode MK</th>
                                        <th class="p-2.5">Tahun Ajaran</th>
                                        <th class="p-2.5">Semester</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100" id="krsPreviewTbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- =================================================================== -->
            <!-- TAB 4: MONITORING PERWALIAN -->
            <!-- =================================================================== -->
            <div id="tab-content-monitoring" class="tab-pane hidden space-y-4">
                <div class="clay-card p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-200">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Monitoring Status Pengajuan Perwalian Mahasiswa</h2>
                            <p class="text-xs text-slate-500">Pantau progres pengajuan Tahap 1 (Kontrak KRS) dan persetujuan Dosen Wali secara real-time.</p>
                        </div>
                    </div>

                    <!-- Table Monitoring -->
                    <div class="overflow-x-auto mt-4 rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-100/80 text-slate-700 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="p-3 text-center w-12">No</th>
                                    <th class="p-3">Mahasiswa</th>
                                    <th class="p-3">Dosen Wali</th>
                                    <th class="p-3 text-center">MK / SKS</th>
                                    <th class="p-3 text-center">Bukti KRS Asli</th>
                                    <th class="p-3 text-center">Status Tahap 1</th>
                                    <th class="p-3">Tgl Pengajuan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if(empty($monitoring)): ?>
                                    <tr>
                                        <td colspan="7" class="p-8 text-center text-slate-400">Belum ada pengajuan perwalian pada periode ini.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach($monitoring as $row): 
                                        $st = $row['status_tahap1'] ?? 'Draft';
                                        $badgeClass = 'bg-slate-100 text-slate-600';
                                        if ($st === 'Diajukan') $badgeClass = 'bg-amber-100 text-amber-800';
                                        elseif ($st === 'Disetujui') $badgeClass = 'bg-emerald-100 text-emerald-800';
                                        elseif ($st === 'Revisi') $badgeClass = 'bg-rose-100 text-rose-800';
                                    ?>
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="p-3 text-center text-slate-400 font-mono"><?= $no++ ?></td>
                                            <td class="p-3">
                                                <div class="font-bold text-slate-900"><?= htmlspecialchars($row['nama_mahasiswa'] ?: ($row['nama_lengkap'] ?: 'Mahasiswa')) ?></div>
                                                <div class="text-[11px] font-mono text-slate-400"><?= htmlspecialchars($row['nim']) ?></div>
                                            </td>
                                            <td class="p-3 font-medium text-slate-700"><?= htmlspecialchars($row['nama_dosen_wali'] ?: '-') ?></td>
                                            <td class="p-3 text-center font-bold text-slate-800">
                                                <?= (int)$row['total_mk'] ?> MK <span class="text-slate-400 font-normal">(<?= (int)$row['total_sks'] ?> SKS)</span>
                                            </td>
                                            <td class="p-3 text-center">
                                                <?php if(!empty($row['file_krs'])): ?>
                                                    <button type="button" onclick="previewKRSModal('<?= base_url('uploads/krs/' . $row['file_krs']) ?>', '<?= htmlspecialchars($row['file_krs']) ?>')" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-[11px] font-bold inline-flex items-center gap-1 transition cursor-pointer">
                                                        <span>📎</span> Lihat KRS
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-slate-400 text-[11px] italic">Belum Upload</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="p-3 text-center">
                                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] <?= $badgeClass ?>">
                                                    <?= htmlspecialchars($st) ?>
                                                </span>
                                            </td>
                                            <td class="p-3 text-slate-500 font-mono text-[11px]">
                                                <?= !empty($row['tgl_pengajuan']) ? date('d M Y, H:i', strtotime($row['tgl_pengajuan'])) : '-' ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- ======================================================================= -->
    <!-- MODAL 1: TAMBAH / EDIT MATA KULIAH -->
    <!-- ======================================================================= -->
    <div id="modalMK" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 id="modalMKTitle" class="text-base font-bold text-slate-800">Tambah Mata Kuliah</h3>
                <button onclick="closeModalMK()" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>
            <form id="formMK" onsubmit="submitFormMK(event)" class="mt-4 space-y-4 text-xs">
                <input type="hidden" id="mk_id" name="id" value="">
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kode MK <span class="text-rose-500">*</span></label>
                        <input type="text" id="mk_kode" name="kode_mk" required placeholder="Contoh: CS101" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl uppercase font-mono outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">SKS <span class="text-rose-500">*</span></label>
                        <input type="number" id="mk_sks" name="sks" required min="1" max="12" value="3" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Mata Kuliah <span class="text-rose-500">*</span></label>
                    <input type="text" id="mk_nama" name="nama_mk" required placeholder="Nama lengkap mata kuliah" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Semester Paket</label>
                        <select id="mk_semester" name="semester" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-500">
                            <?php for($i=1; $i<=8; $i++): ?>
                                <option value="<?= $i ?>">Semester <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Sifat Mata Kuliah</label>
                        <select id="mk_jenis" name="jenis" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="Wajib">Wajib</option>
                            <option value="Pilihan">Pilihan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Program Studi</label>
                        <input type="text" id="mk_prodi" name="prodi" value="<?= htmlspecialchars($user_prodi) ?>" placeholder="Contoh: S1 Informatika" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kurikulum</label>
                        <input type="text" id="mk_kurikulum" name="kurikulum" value="2024" placeholder="2024" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModalMK()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">Batal</button>
                    <button type="submit" class="btn-brand-primary px-5 py-2 text-xs font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 2: IMPORT EXCEL MATA KULIAH -->
    <!-- ======================================================================= -->
    <div id="modalImportMK" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Import Master Mata Kuliah (Excel / CSV)</h3>
                <button onclick="closeModalImportMK()" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>
            
            <div class="mt-4 space-y-4">
                <input type="file" id="fileMKInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleMKFileSelect(event)">
                <div onclick="document.getElementById('fileMKInput').click()" class="dropzone-box p-6 text-center" id="mkDropzone">
                    <span class="text-3xl">📥</span>
                    <h4 class="text-xs font-bold text-slate-800 mt-2">Pilih File Excel Kurikulum (.xlsx / .csv)</h4>
                    <p class="text-[11px] text-slate-500">Header: <code>Kode MK</code>, <code>Nama Mata Kuliah</code>, <code>SKS</code>, <code>Semester</code>, <code>Prodi</code>, <code>Jenis</code></p>
                </div>

                <div id="previewMKContainer" class="hidden space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-700">Preview Data (<span id="countParsedMK">0</span> mata kuliah)</span>
                        <button onclick="sendImportMK()" id="btnSaveImportMK" class="btn-brand-primary px-4 py-2 text-xs font-bold">Simpan ke Database</button>
                    </div>
                    <div class="max-h-60 overflow-y-auto rounded-xl border border-slate-200 text-xs">
                        <table class="w-full text-left" id="tablePreviewMK">
                            <thead class="bg-slate-100 text-slate-700 uppercase text-[10px] font-bold sticky top-0">
                                <tr>
                                    <th class="p-2">Kode</th>
                                    <th class="p-2">Nama</th>
                                    <th class="p-2 text-center">SKS</th>
                                    <th class="p-2 text-center">Smt</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="tbodyPreviewMK"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL PREVIEW BUKTI KRS (POPUP) -->
    <!-- ======================================================================= -->
    <div id="modalPreviewKRS" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5" onclick="if(event.target === this) closePreviewKRSModal()">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in duration-200">
            <!-- Header -->
            <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2.5 truncate">
                    <span class="text-xl">📄</span>
                    <div class="truncate">
                        <h3 class="text-sm font-bold text-slate-800 truncate" id="krsModalTitle">Preview Bukti Resmi KRS</h3>
                        <span class="text-[11px] font-mono text-slate-500 truncate block" id="krsModalFilename">file</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a id="krsModalDownloadBtn" href="#" download class="px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-brand-700 text-xs font-bold rounded-lg border border-orange-200 transition flex items-center gap-1.5">
                        <span>⬇️</span> Unduh
                    </a>
                    <button type="button" onclick="closePreviewKRSModal()" class="w-8 h-8 rounded-lg bg-slate-200/80 hover:bg-slate-300 text-slate-600 flex items-center justify-center text-sm font-bold transition cursor-pointer">
                        ✕
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 bg-slate-100 overflow-auto flex items-center justify-center min-h-[50vh] max-h-[75vh] p-2 relative">
                <!-- If Image -->
                <div id="krsPreviewImageContainer" class="hidden w-full h-full flex items-center justify-center p-2">
                    <img id="krsPreviewImage" src="" alt="Bukti KRS" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-md border border-slate-200">
                </div>
                <!-- If PDF / Iframe -->
                <iframe id="krsPreviewIframe" src="about:blank" class="hidden w-full h-[70vh] border-0 rounded-lg shadow-inner bg-white"></iframe>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        let parsedMKData = [];
        let parsedKRSData = [];

        function switchTab(tabId) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

            const targetPane = document.getElementById('tab-content-' + tabId);
            const targetBtn = document.getElementById('tab-btn-' + tabId);
            if (targetPane) targetPane.classList.remove('hidden');
            if (targetBtn) targetBtn.classList.add('active');
        }

        // --- FILTER TABLE MK ---
        function filterTableMK() {
            const query = (document.getElementById('searchMK').value || '').toLowerCase();
            const semester = document.getElementById('filterSemesterMK').value;
            const rows = document.querySelectorAll('.mk-row');

            rows.forEach(r => {
                const searchTxt = r.getAttribute('data-search') || '';
                const rowSmt = r.getAttribute('data-semester') || '';
                const matchQ = query === '' || searchTxt.includes(query);
                const matchSmt = semester === 'all' || rowSmt === semester;
                r.style.display = (matchQ && matchSmt) ? '' : 'none';
            });
        }

        // --- FILTER TABLE MHS ---
        function filterTableMhs() {
            const query = (document.getElementById('searchMhs').value || '').toLowerCase();
            const rows = document.querySelectorAll('.mhs-row');
            rows.forEach(r => {
                const searchTxt = r.getAttribute('data-search') || '';
                r.style.display = (query === '' || searchTxt.includes(query)) ? '' : 'none';
            });
        }

        // --- MODAL MK ---
        function openModalTambahMK() {
            document.getElementById('formMK').reset();
            document.getElementById('mk_id').value = '';
            document.getElementById('modalMKTitle').innerText = 'Tambah Mata Kuliah Baru';
            document.getElementById('modalMK').classList.remove('hidden');
        }

        function openModalEditMK(mk) {
            document.getElementById('mk_id').value = mk.id || '';
            document.getElementById('mk_kode').value = mk.kode_mk || '';
            document.getElementById('mk_nama').value = mk.nama_mk || '';
            document.getElementById('mk_sks').value = mk.sks || 3;
            document.getElementById('mk_semester').value = mk.semester || 1;
            document.getElementById('mk_jenis').value = mk.jenis || 'Wajib';
            document.getElementById('mk_prodi').value = mk.prodi || '';
            document.getElementById('mk_kurikulum').value = mk.kurikulum || '2024';
            document.getElementById('modalMKTitle').innerText = 'Edit Mata Kuliah';
            document.getElementById('modalMK').classList.remove('hidden');
        }

        function closeModalMK() {
            document.getElementById('modalMK').classList.add('hidden');
        }

        async function submitFormMK(e) {
            e.preventDefault();
            const formData = new FormData(document.getElementById('formMK'));

            try {
                const res = await fetch('<?= site_url("prodi/perwalian/simpan_mata_kuliah") ?>', {
                    method: 'POST',
                    body: formData
                });
                const json = await res.json();
                if (json.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: json.message, timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1200);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: json.message });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
            }
        }

        function hapusMK(id, kode) {
            Swal.fire({
                title: 'Hapus Mata Kuliah?',
                text: `Yakin ingin menghapus mata kuliah ${kode}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const fd = new FormData();
                    fd.append('id', id);
                    const res = await fetch('<?= site_url("prodi/perwalian/hapus_mata_kuliah") ?>', { method: 'POST', body: fd });
                    const json = await res.json();
                    if (json.status === 'success') {
                        location.reload();
                    } else {
                        Swal.fire('Gagal', json.message, 'error');
                    }
                }
            });
        }

        // --- MODAL IMPORT MK ---
        function openModalImportMK() {
            document.getElementById('previewMKContainer').classList.add('hidden');
            document.getElementById('fileMKInput').value = '';
            parsedMKData = [];
            document.getElementById('modalImportMK').classList.remove('hidden');
        }
        function closeModalImportMK() {
            document.getElementById('modalImportMK').classList.add('hidden');
        }

        function handleMKFileSelect(e) {
            const file = (e && e.target && e.target.files) ? e.target.files[0] : e;
            processMKFile(file);
        }

        function processMKFile(file) {
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(evt) {
                const data = new Uint8Array(evt.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const json = XLSX.utils.sheet_to_json(firstSheet);

                parsedMKData = json.map(r => ({
                    kode_mk: r['Kode MK'] || r['kode_mk'] || r['Kode'] || '',
                    nama_mk: r['Nama Mata Kuliah'] || r['nama_mk'] || r['Nama'] || '',
                    sks: r['SKS'] || r['sks'] || 3,
                    semester: r['Semester'] || r['semester'] || 1,
                    prodi: r['Program Studi'] || r['Prodi'] || r['prodi'] || '<?= htmlspecialchars($user_prodi) ?>',
                    jenis: r['Jenis'] || r['Sifat'] || 'Wajib',
                    kurikulum: r['Kurikulum'] || '2024'
                })).filter(r => r.kode_mk && r.nama_mk);

                if (parsedMKData.length === 0) {
                    Swal.fire('Format Kosong', 'Tidak ada baris mata kuliah yang valid ditemukan dalam file.', 'warning');
                    return;
                }

                document.getElementById('countParsedMK').innerText = parsedMKData.length;
                const tbody = document.getElementById('tbodyPreviewMK');
                tbody.innerHTML = parsedMKData.slice(0, 50).map(r => `
                    <tr>
                        <td class="p-2 font-mono font-bold text-brand-700">${r.kode_mk}</td>
                        <td class="p-2 font-semibold text-slate-800">${r.nama_mk}</td>
                        <td class="p-2 text-center">${r.sks}</td>
                        <td class="p-2 text-center">Smt ${r.semester}</td>
                    </tr>
                `).join('');

                document.getElementById('previewMKContainer').classList.remove('hidden');
            };
            reader.readAsArrayBuffer(file);
        }

        async function sendImportMK() {
            if (parsedMKData.length === 0) return;
            const btn = document.getElementById('btnSaveImportMK');
            btn.disabled = true;
            btn.innerText = 'Menyimpan...';

            try {
                const res = await fetch('<?= site_url("prodi/perwalian/import_mata_kuliah") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ courses: parsedMKData })
                });
                const json = await res.json();
                if (json.status === 'success') {
                    Swal.fire('Sukses', json.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', json.message, 'error');
                }
            } catch (err) {
                Swal.fire('Error', 'Gagal memproses data import.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Simpan ke Database';
            }
        }

        function downloadTemplateMK() {
            const sampleData = [
                { 'Kode MK': 'CS101', 'Nama Mata Kuliah': 'Algoritma & Pemrograman', 'SKS': 3, 'Semester': 1, 'Program Studi': 'S1 Informatika', 'Jenis': 'Wajib', 'Kurikulum': '2024' },
                { 'Kode MK': 'CS102', 'Nama Mata Kuliah': 'Struktur Data', 'SKS': 4, 'Semester': 2, 'Program Studi': 'S1 Informatika', 'Jenis': 'Wajib', 'Kurikulum': '2024' },
                { 'Kode MK': 'CS201', 'Nama Mata Kuliah': 'Pemrograman Web', 'SKS': 3, 'Semester': 3, 'Program Studi': 'S1 Informatika', 'Jenis': 'Wajib', 'Kurikulum': '2024' },
            ];
            const ws = XLSX.utils.json_to_sheet(sampleData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Template MK');
            XLSX.writeFile(wb, 'template_master_mata_kuliah.xlsx');
        }

        // --- DOSEN WALI ASSIGNMENT ---
        async function updateDosenWali(nim, nip) {
            const fd = new FormData();
            fd.append('nim', nim);
            fd.append('nip_dosen_wali', nip);

            try {
                const res = await fetch('<?= site_url("prodi/perwalian/update_dosen_wali") ?>', { method: 'POST', body: fd });
                const json = await res.json();
                if (json.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Tersimpan', text: json.message, timer: 1000, showConfirmButton: false });
                }
            } catch (err) {
                Swal.fire('Error', 'Gagal memperbarui dosen wali', 'error');
            }
        }

        // --- IMPORT REKAP KRS KOLEKTIF ---
        function handleKRSFileSelect(e) {
            const file = (e && e.target && e.target.files) ? e.target.files[0] : e;
            processKRSFile(file);
        }

        function processKRSFile(file) {
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(evt) {
                const data = new Uint8Array(evt.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const json = XLSX.utils.sheet_to_json(firstSheet);

                parsedKRSData = json.map(r => ({
                    nim: String(r['NIM'] || r['nim'] || r['nim_mahasiswa'] || '').trim(),
                    kode_mk: String(r['Kode MK'] || r['kode_mk'] || r['Kode'] || '').trim().toUpperCase(),
                    tahun_ajaran: r['Tahun Ajaran'] || r['tahun_ajaran'] || '<?= htmlspecialchars($tahun_ajaran) ?>',
                    semester: r['Semester'] || r['semester'] || '<?= htmlspecialchars($semester) ?>'
                })).filter(r => r.nim && r.kode_mk);

                if (parsedKRSData.length === 0) {
                    Swal.fire('File Kosong', 'Tidak ada data NIM & Kode MK yang valid dalam file.', 'warning');
                    return;
                }

                document.getElementById('krsPreviewCount').innerText = parsedKRSData.length;
                const tbody = document.getElementById('krsPreviewTbody');
                tbody.innerHTML = parsedKRSData.slice(0, 50).map((r, i) => `
                    <tr>
                        <td class="p-2.5 text-center text-slate-400 font-mono">${i + 1}</td>
                        <td class="p-2.5 font-mono font-bold text-slate-900">${r.nim}</td>
                        <td class="p-2.5 font-mono font-bold text-brand-700">${r.kode_mk}</td>
                        <td class="p-2.5 text-slate-600">${r.tahun_ajaran}</td>
                        <td class="p-2.5 text-slate-600">${r.semester}</td>
                    </tr>
                `).join('');

                document.getElementById('krsPreviewContainer').classList.remove('hidden');
            };
            reader.readAsArrayBuffer(file);
        }

        async function submitImportKRS() {
            if (parsedKRSData.length === 0) return;
            const btn = document.getElementById('btnSubmitKRS');
            btn.disabled = true;
            btn.innerText = 'Memproses ke database...';

            try {
                const res = await fetch('<?= site_url("prodi/perwalian/import_krs_kolektif") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        krs_rows: parsedKRSData,
                        tahun_ajaran: '<?= htmlspecialchars($tahun_ajaran) ?>',
                        semester: '<?= htmlspecialchars($semester) ?>'
                    })
                });
                const json = await res.json();
                if (json.status === 'success') {
                    Swal.fire('Berhasil!', json.message, 'success').then(() => {
                        switchTab('monitoring');
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', json.message, 'error');
                }
            } catch (err) {
                Swal.fire('Error', 'Gagal memproses import data KRS.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Simpan & Masukkan ke Perwalian Mahasiswa';
            }
        }

        function downloadTemplateKRS() {
            const sampleData = [
                { 'NIM': '1301210001', 'Kode MK': 'CS101', 'Tahun Ajaran': '2024/2025', 'Semester': 'Ganjil' },
                { 'NIM': '1301210001', 'Kode MK': 'CS102', 'Tahun Ajaran': '2024/2025', 'Semester': 'Ganjil' },
                { 'NIM': '1301210002', 'Kode MK': 'CS201', 'Tahun Ajaran': '2024/2025', 'Semester': 'Ganjil' },
            ];
            const ws = XLSX.utils.json_to_sheet(sampleData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Template Rekap KRS');
            XLSX.writeFile(wb, 'template_rekap_krs_siakad.xlsx');
        }

        // --- MODAL PREVIEW KRS POPUP ---
        function previewKRSModal(url, filename) {
            if (!url) return;
            const modal = document.getElementById('modalPreviewKRS');
            const filenameEl = document.getElementById('krsModalFilename');
            const downloadBtn = document.getElementById('krsModalDownloadBtn');
            const imgContainer = document.getElementById('krsPreviewImageContainer');
            const imgEl = document.getElementById('krsPreviewImage');
            const iframeEl = document.getElementById('krsPreviewIframe');

            filenameEl.innerText = filename || 'Bukti_KRS';
            downloadBtn.href = url;

            const cleanName = (filename || url).split('?')[0];
            const ext = cleanName.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                imgEl.src = url;
                imgContainer.classList.remove('hidden');
                iframeEl.classList.add('hidden');
                iframeEl.src = 'about:blank';
            } else {
                iframeEl.src = url;
                iframeEl.classList.remove('hidden');
                imgContainer.classList.add('hidden');
                imgEl.src = '';
            }

            modal.classList.remove('hidden');
        }

        function closePreviewKRSModal() {
            const modal = document.getElementById('modalPreviewKRS');
            const imgEl = document.getElementById('krsPreviewImage');
            const iframeEl = document.getElementById('krsPreviewIframe');
            if (modal) modal.classList.add('hidden');
            if (imgEl) imgEl.src = '';
            if (iframeEl) iframeEl.src = 'about:blank';
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closePreviewKRSModal();
        });

        // --- DRAG AND DROP SETUP ---
        function setupDropzone(dropzoneEl, fileInputEl, fileHandler) {
            if (!dropzoneEl) return;
            
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzoneEl.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneEl.classList.add('border-brand-500', 'bg-orange-50', 'ring-4', 'ring-orange-200');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzoneEl.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzoneEl.classList.remove('border-brand-500', 'bg-orange-50', 'ring-4', 'ring-orange-200');
                }, false);
            });

            dropzoneEl.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                if (dt && dt.files && dt.files.length > 0) {
                    fileHandler(dt.files[0]);
                }
            }, false);
        }

        document.addEventListener('DOMContentLoaded', () => {
            setupDropzone(document.getElementById('krsDropzone'), document.getElementById('fileKRSInput'), processKRSFile);
            setupDropzone(document.getElementById('mkDropzone'), document.getElementById('fileMKInput'), processMKFile);
        });
    </script>
</body>
</html>
