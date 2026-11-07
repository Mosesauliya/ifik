<?php
/**
 * Modul Perwalian & Kurikulum Admin Prodi
 * Monitoring perwalian, plotting dosen wali, dan master mata kuliah.
 */
?>
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
                            <button id="btnBulkDeleteMK" onclick="hapusMKMultiple()" class="hidden px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1.5 transition cursor-pointer">
                                <span>🗑️</span> Hapus Terpilih (<span id="countSelectedMK">0</span>)
                            </button>
                            <button onclick="openModalTambahMK()" class="btn-brand-primary px-3.5 py-2 text-xs flex items-center gap-1.5">
                                <span>+</span> Tambah MK
                            </button>
                            <button onclick="openModalImportMK()" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1.5 transition">
                                <span>📥</span> Import Excel
                            </button>
                            <button onclick="downloadTemplateMK()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold border border-slate-200 flex items-center gap-1.5 transition">
                                <span>📄</span> Template XLSX
                            </button>
                            <button onclick="openModalImportEkuivalensi()" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1.5 transition">
                                <span>🔄</span> Import Ekuivalensi
                            </button>
                            <button onclick="downloadTemplateEkuivalensi()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold border border-slate-200 flex items-center gap-1.5 transition" title="Unduh format excel pemetaan ekuivalensi">
                                <span>📄</span> Template Ekuivalensi
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

                    <!-- Bulk Action Bar -->
                    <div id="bulkActionMKBar" class="hidden mt-3 p-3 bg-rose-50 border border-rose-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-600 animate-pulse"></span>
                            <span class="font-bold text-rose-900"><span id="countSelectedMKText">0</span> mata kuliah dipilih</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openModalEkuivalensiMultiple()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer" title="Atur pemetaan ekuivalensi untuk mata kuliah yang dipilih">
                                <span>🔄</span> Atur Ekuivalensi (<span id="countSelectedMKEkuiBtn">0</span> MK)
                            </button>
                            <button type="button" onclick="deselectAllMK()" class="px-3 py-1.5 text-slate-600 hover:text-slate-800 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg font-semibold transition cursor-pointer">
                                Batal Pilih
                            </button>
                            <button type="button" onclick="hapusMKMultiple()" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold shadow-xs transition flex items-center gap-1 cursor-pointer">
                                <span>🗑️</span> Hapus <span id="countSelectedMKBtn">0</span> MK Terpilih
                            </button>
                        </div>
                    </div>

                    <!-- Table MK -->
                    <div class="overflow-x-auto mt-4 rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs" id="tableMK">
                            <thead class="bg-slate-100/80 text-slate-700 uppercase font-bold text-[11px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="p-3 text-center w-10">
                                        <input type="checkbox" id="checkAllMK" onchange="toggleCheckAllMK(this)" class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-slate-300 cursor-pointer" title="Pilih Semua">
                                    </th>
                                    <th class="p-3 text-center w-12">No</th>
                                    <th class="p-3">Kode MK</th>
                                    <th class="p-3">Nama Mata Kuliah</th>
                                    <th class="p-3 text-center">SKS</th>
                                    <th class="p-3 text-center">Semester</th>
                                    <th class="p-3">Prodi</th>
                                    <th class="p-3 text-center">Jenis</th>
                                    <th class="p-3 text-center">Ekuivalensi</th>
                                    <th class="p-3 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if(empty($mata_kuliah)): ?>
                                    <tr>
                                        <td colspan="10" class="p-8 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <span class="text-3xl mb-2">📂</span>
                                                <p class="font-semibold text-slate-600">Belum ada data mata kuliah</p>
                                                <p class="text-[11px] text-slate-400 mt-0.5">Silakan tambahkan manual atau import dari file Excel kurikulum.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach($mata_kuliah as $mk): ?>
                                        <tr class="hover:bg-orange-50/40 transition mk-row" data-id="<?= $mk['id'] ?>" data-kode="<?= htmlspecialchars($mk['kode_mk']) ?>" data-search="<?= strtolower(htmlspecialchars($mk['kode_mk'] . ' ' . $mk['nama_mk'])) ?>" data-semester="<?= (int)$mk['semester'] ?>">
                                            <td class="p-3 text-center">
                                                <input type="checkbox" class="check-item-mk w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-slate-300 cursor-pointer" value="<?= $mk['id'] ?>" data-kode="<?= htmlspecialchars($mk['kode_mk']) ?>" onchange="updateBulkDeleteMKUI()">
                                            </td>
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
                                            <td class="p-3 text-center" id="col-ekuivalensi-<?= $mk['id'] ?>">
                                                <?php 
                                                    $sumEkui = $ekuivalensi_summary[$mk['id']] ?? null;
                                                    if ($sumEkui && $sumEkui['count'] > 0): 
                                                        $tSks = (int)$sumEkui['total_sks'];
                                                        $targetSks = (int)$mk['sks'];
                                                        $badgeClass = ($tSks === $targetSks) 
                                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' 
                                                            : (($tSks < $targetSks) 
                                                                ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' 
                                                                : 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100');
                                                        $iconStatus = ($tSks === $targetSks) ? '✅' : (($tSks < $targetSks) ? '⚠️' : 'ℹ️');
                                                ?>
                                                    <button onclick='openModalEkuivalensiSingle(<?= json_encode($mk) ?>)' class="px-2 py-0.5 rounded-lg border text-[11px] font-bold inline-flex items-center gap-1 transition cursor-pointer <?= $badgeClass ?>" title="Klik untuk mengelola ekuivalensi: <?= $sumEkui['count'] ?> MK ekuivalen (Total <?= $tSks ?> dari <?= $targetSks ?> SKS)">
                                                        <span><?= $iconStatus ?></span> <?= $sumEkui['count'] ?> MK (<?= $tSks ?>/<?= $targetSks ?> SKS)
                                                    </button>
                                                <?php else: ?>
                                                    <button onclick='openModalEkuivalensiSingle(<?= json_encode($mk) ?>)' class="px-2 py-0.5 rounded-lg border border-dashed border-slate-300 text-[11px] font-semibold text-slate-400 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50/50 transition cursor-pointer" title="Belum ada ekuivalensi. Klik untuk menambahkan.">
                                                        + Atur
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <td class="p-3 text-center">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <button onclick='openModalEkuivalensiSingle(<?= json_encode($mk) ?>)' class="p-1.5 hover:bg-indigo-100 text-indigo-600 rounded-lg transition" title="Kelola Ekuivalensi">
                                                        🔄
                                                    </button>
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
                        <!-- Stat Cards -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <div onclick="filterKRSPreview('all')" class="p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition shadow-2xs">
                                <span class="text-[11px] font-bold text-slate-500 block">Total Baris</span>
                                <span class="text-lg font-extrabold text-slate-800 font-mono" id="statKRSTotal">0</span>
                            </div>
                            <div onclick="filterKRSPreview('valid')" class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl cursor-pointer hover:bg-emerald-100 transition shadow-2xs">
                                <span class="text-[11px] font-bold text-emerald-700 block">✓ Valid (Siap Simpan)</span>
                                <span class="text-lg font-extrabold text-emerald-700 font-mono" id="statKRSValid">0</span>
                            </div>
                            <div onclick="filterKRSPreview('duplicate')" class="p-3 bg-amber-50 border border-amber-200 rounded-xl cursor-pointer hover:bg-amber-100 transition shadow-2xs">
                                <span class="text-[11px] font-bold text-amber-700 block">⚠️ Duplikat</span>
                                <span class="text-lg font-extrabold text-amber-700 font-mono" id="statKRSDuplicate">0</span>
                            </div>
                            <div onclick="filterKRSPreview('error')" class="p-3 bg-rose-50 border border-rose-200 rounded-xl cursor-pointer hover:bg-rose-100 transition shadow-2xs">
                                <span class="text-[11px] font-bold text-rose-700 block">✕ Salah Format</span>
                                <span class="text-lg font-extrabold text-rose-700 font-mono" id="statKRSError">0</span>
                            </div>
                        </div>

                        <!-- Alerts -->
                        <div id="alertKRSDuplicate" class="hidden px-3.5 py-2.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span>⚠️</span>
                                <span>Ditemukan data KRS duplikat (NIM & Kode MK ganda). Data duplikat otomatis ditandai agar tidak terjadi double input.</span>
                            </div>
                            <label class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-900 cursor-pointer">
                                <input type="checkbox" id="chkSkipKRSDuplicate" checked onchange="renderKRSPreviewTable()" class="rounded text-brand-600 focus:ring-brand-500">
                                Lewati Duplikat
                            </label>
                        </div>

                        <div id="alertKRSError" class="hidden px-3.5 py-2.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 flex items-center gap-2">
                            <span>❌</span>
                            <span>Terdapat baris KRS tidak valid (NIM terindikasi nama orang / format salah). Baris ini akan otomatis dilewati.</span>
                        </div>

                        <!-- Filter Tabs & Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                            <div class="flex items-center gap-1.5 text-xs">
                                <button type="button" onclick="filterKRSPreview('all')" id="tabKRSFilter-all" class="px-3 py-1 font-bold rounded-lg bg-brand-500 text-white transition">Semua (<span id="countTabKRS-all">0</span>)</button>
                                <button type="button" onclick="filterKRSPreview('valid')" id="tabKRSFilter-valid" class="px-3 py-1 font-bold rounded-lg text-slate-600 hover:bg-slate-100 transition">Hanya Valid (<span id="countTabKRS-valid">0</span>)</button>
                                <button type="button" onclick="filterKRSPreview('duplicate')" id="tabKRSFilter-duplicate" class="px-3 py-1 font-bold rounded-lg text-amber-700 hover:bg-amber-50 transition">Duplikat (<span id="countTabKRS-duplicate">0</span>)</button>
                                <button type="button" onclick="filterKRSPreview('error')" id="tabKRSFilter-error" class="px-3 py-1 font-bold rounded-lg text-rose-700 hover:bg-rose-50 transition">Error (<span id="countTabKRS-error">0</span>)</button>
                            </div>
                            <button onclick="submitImportKRS()" id="btnSubmitKRS" disabled class="btn-brand-primary px-5 py-2 text-xs flex items-center gap-2 font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                                <span>🚀</span> Simpan & Masukkan ke Perwalian Mahasiswa
                            </button>
                        </div>

                        <div class="overflow-x-auto max-h-80 rounded-xl border border-slate-200">
                            <table class="w-full text-left text-xs" id="krsPreviewTable">
                                <thead class="bg-slate-100 text-slate-700 sticky top-0 font-bold uppercase text-[11px] shadow-xs">
                                    <tr>
                                        <th class="p-2.5 text-center w-12">No</th>
                                        <th class="p-2.5 w-28">Status</th>
                                        <th class="p-2.5 w-32">NIM</th>
                                        <th class="p-2.5 w-28">Kode MK</th>
                                        <th class="p-2.5">Tahun Ajaran</th>
                                        <th class="p-2.5">Semester</th>
                                        <th class="p-2.5">Keterangan / Diagnosa</th>
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
                                    <th class="p-3 text-center w-36">Aksi Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if(empty($monitoring)): ?>
                                    <tr>
                                        <td colspan="8" class="p-8 text-center text-slate-400">Belum ada pengajuan perwalian pada periode ini.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach($monitoring as $row): 
                                        $st = $row['status_tahap1'] ?? 'Draft';
                                        $badgeClass = 'bg-slate-100 text-slate-600';
                                        if ($st === 'Diajukan') $badgeClass = 'bg-amber-100 text-amber-800';
                                        elseif ($st === 'Disetujui') $badgeClass = 'bg-emerald-100 text-emerald-800';
                                        elseif ($st === 'Revisi') $badgeClass = 'bg-rose-100 text-rose-800';
                                        $mhsName = $row['nama_mahasiswa'] ?: ($row['nama_lengkap'] ?: $row['nim']);
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
                                            <td class="p-3 text-center">
                                                <?php if ($st === 'Disetujui'): ?>
                                                    <button type="button" onclick="confirmResetKRS(<?= (int)$row['id_perwalian'] ?>, '<?= htmlspecialchars($mhsName, ENT_QUOTES) ?>', 'Disetujui')" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 rounded-lg text-[11px] font-bold inline-flex items-center gap-1 transition shadow-2xs cursor-pointer" title="Buka kunci status agar mahasiswa bisa mengubah KRS">
                                                        <span>🔓</span> Buka Kunci
                                                    </button>
                                                <?php elseif ($st === 'Diajukan'): ?>
                                                    <button type="button" onclick="confirmResetKRS(<?= (int)$row['id_perwalian'] ?>, '<?= htmlspecialchars($mhsName, ENT_QUOTES) ?>', 'Diajukan')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 rounded-lg text-[11px] font-bold inline-flex items-center gap-1 transition shadow-2xs cursor-pointer" title="Kembalikan pengajuan ke Draft">
                                                        <span>↩️</span> Batal Ajukan
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" onclick="confirmResetKRS(<?= (int)$row['id_perwalian'] ?>, '<?= htmlspecialchars($mhsName, ENT_QUOTES) ?>', '<?= htmlspecialchars($st, ENT_QUOTES) ?>')" class="px-2 py-0.5 text-slate-400 hover:text-rose-600 rounded text-[11px] inline-flex items-center gap-1 transition cursor-pointer" title="Opsi kelola status">
                                                        <span>⚙️</span> Reset
                                                    </button>
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
    <!-- MODAL 2: IMPORT EXCEL MATA KULIAH (VALIDASI DUPLIKAT & FIELD KETAT) -->
    <!-- ======================================================================= -->
    <div id="modalImportMK" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5" onclick="if(event.target === this) closeModalImportMK()">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center text-xl shadow-xs">
                        📥
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800">Import Master Mata Kuliah</h3>
                        <p class="text-xs text-slate-500">Dilengkapi validasi duplikasi data & proteksi kesalahan pengisian kolom</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="downloadTemplateMK()" class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-2xs transition flex items-center gap-1.5">
                        <span>📄</span> Unduh Template
                    </button>
                    <button type="button" onclick="closeModalImportMK()" class="w-8 h-8 rounded-xl bg-white hover:bg-slate-100 text-slate-400 hover:text-slate-600 border border-slate-200 flex items-center justify-center font-bold text-sm transition">✕</button>
                </div>
            </div>
            
            <!-- Modal Body Scrollable -->
            <div class="p-6 overflow-y-auto space-y-4 flex-1">
                <!-- Dropzone Area -->
                <input type="file" id="fileMKInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleMKFileSelect(event)">
                <div onclick="document.getElementById('fileMKInput').click()" class="dropzone-box p-6 text-center cursor-pointer transition hover:border-brand-500 hover:bg-orange-50/30" id="mkDropzone">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-brand-600 mx-auto flex items-center justify-center text-2xl mb-2">📊</div>
                    <h4 class="text-xs font-bold text-slate-800">Klik atau Tarik File Excel (.xlsx / .csv) ke Sini</h4>
                    <p class="text-[11px] text-slate-500 mt-1">Template kolom: <code>Kode MK</code>, <code>Nama Mata Kuliah</code>, <code>SKS</code>, <code>Semester</code>, <code>Prodi</code>, <code>Jenis</code></p>
                    <div class="mt-2 text-[10px] text-amber-600 bg-amber-50 inline-block px-2.5 py-1 rounded-full border border-amber-200 font-medium">
                        🛡️ Sistem otomatis memeriksa kolom tertukar, format angka, dan mata kuliah ganda.
                    </div>
                </div>

                <!-- Preview Container -->
                <div id="previewMKContainer" class="hidden space-y-4">
                    <!-- Stat Cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div onclick="filterMKPreview('all')" class="p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition shadow-2xs">
                            <span class="text-[11px] font-bold text-slate-500 block">Total Baris</span>
                            <span class="text-lg font-extrabold text-slate-800 font-mono" id="statMKTotal">0</span>
                        </div>
                        <div onclick="filterMKPreview('valid')" class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl cursor-pointer hover:bg-emerald-100 transition shadow-2xs">
                            <span class="text-[11px] font-bold text-emerald-700 block">✓ Valid (Siap Simpan)</span>
                            <span class="text-lg font-extrabold text-emerald-700 font-mono" id="statMKValid">0</span>
                        </div>
                        <div onclick="filterMKPreview('duplicate')" class="p-3 bg-amber-50 border border-amber-200 rounded-xl cursor-pointer hover:bg-amber-100 transition shadow-2xs">
                            <span class="text-[11px] font-bold text-amber-700 block">⚠️ Duplikat</span>
                            <span class="text-lg font-extrabold text-amber-700 font-mono" id="statMKDuplicate">0</span>
                        </div>
                        <div onclick="filterMKPreview('error')" class="p-3 bg-rose-50 border border-rose-200 rounded-xl cursor-pointer hover:bg-rose-100 transition shadow-2xs">
                            <span class="text-[11px] font-bold text-rose-700 block">✕ Salah Format</span>
                            <span class="text-lg font-extrabold text-rose-700 font-mono" id="statMKError">0</span>
                        </div>
                    </div>

                    <!-- Alert Banner jika ada duplikat atau error -->
                    <div id="alertMKDuplicate" class="hidden px-3.5 py-2.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span>⚠️</span>
                            <span id="alertMKDuplicateText">Ditemukan data duplikat. Data duplikat otomatis ditandai agar tidak terjadi penggandaan mata kuliah.</span>
                        </div>
                        <label class="inline-flex items-center gap-1.5 text-[11px] font-bold text-amber-900 cursor-pointer">
                            <input type="checkbox" id="chkSkipMKDuplicate" checked onchange="renderMKPreviewTable()" class="rounded text-brand-600 focus:ring-brand-500">
                            Lewati Duplikat
                        </label>
                    </div>

                    <div id="alertMKError" class="hidden px-3.5 py-2.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 flex items-center gap-2">
                        <span>❌</span>
                        <span id="alertMKErrorText">Terdapat baris dengan kesalahan pengisian (kolom tertukar / format angka salah). Baris ini tidak akan dimasukkan.</span>
                    </div>

                    <!-- Tab Filter Pills -->
                    <div class="flex items-center gap-1.5 border-b border-slate-200 pb-2 text-xs">
                        <button type="button" onclick="filterMKPreview('all')" id="tabMKFilter-all" class="px-3 py-1 font-bold rounded-lg bg-brand-500 text-white transition">Semua (<span id="countTabMK-all">0</span>)</button>
                        <button type="button" onclick="filterMKPreview('valid')" id="tabMKFilter-valid" class="px-3 py-1 font-bold rounded-lg text-slate-600 hover:bg-slate-100 transition">Hanya Valid (<span id="countTabMK-valid">0</span>)</button>
                        <button type="button" onclick="filterMKPreview('duplicate')" id="tabMKFilter-duplicate" class="px-3 py-1 font-bold rounded-lg text-amber-700 hover:bg-amber-50 transition">Duplikat (<span id="countTabMK-duplicate">0</span>)</button>
                        <button type="button" onclick="filterMKPreview('error')" id="tabMKFilter-error" class="px-3 py-1 font-bold rounded-lg text-rose-700 hover:bg-rose-50 transition">Error (<span id="countTabMK-error">0</span>)</button>
                    </div>

                    <!-- Table Preview -->
                    <div class="max-h-72 overflow-y-auto rounded-xl border border-slate-200 text-xs">
                        <table class="w-full text-left" id="tablePreviewMK">
                            <thead class="bg-slate-100 text-slate-700 uppercase text-[10px] font-bold sticky top-0 shadow-xs">
                                <tr>
                                    <th class="p-2.5 text-center w-12">No</th>
                                    <th class="p-2.5 w-28">Status</th>
                                    <th class="p-2.5 w-24">Kode MK</th>
                                    <th class="p-2.5">Nama Mata Kuliah</th>
                                    <th class="p-2.5 text-center w-16">SKS</th>
                                    <th class="p-2.5 text-center w-16">Smt</th>
                                    <th class="p-2.5 w-20">Jenis</th>
                                    <th class="p-2.5">Keterangan / Diagnosa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="tbodyPreviewMK"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
                <div class="text-xs text-slate-500">
                    <span id="footerMKValidNote">Pilih file excel untuk memulai validasi data.</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeModalImportMK()" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition">Batal</button>
                    <button type="button" onclick="sendImportMK()" id="btnSaveImportMK" disabled class="btn-brand-primary px-5 py-2 text-xs font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                        Simpan ke Database
                    </button>
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

    <!-- ======================================================================= -->
    <!-- MODAL 3: KELOLA EKUIVALENSI MATA KULIAH (SINGLE & MULTIPLE SELECT) -->
    <!-- ======================================================================= -->
    <div id="modalEkuivalensi" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5" onclick="if(event.target === this) closeModalEkuivalensi()">
        <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50/90 via-purple-50/50 to-white border-b border-indigo-100 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl shadow-md shadow-indigo-200">
                        🔄
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800">Kelola Ekuivalensi Mata Kuliah</h3>
                        <p class="text-xs text-slate-500">Petakan mata kuliah kurikulum lama / setara ke mata kuliah kurikulum utama.</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalEkuivalensi()" class="w-8 h-8 rounded-xl bg-white hover:bg-slate-100 text-slate-400 hover:text-slate-600 border border-slate-200 flex items-center justify-center font-bold text-sm transition cursor-pointer">✕</button>
            </div>

            <!-- Multiple MK Tabs Strip (Visible only when multiple MKs were selected from table) -->
            <div id="ekuiMultiTabsBar" class="hidden px-6 py-2.5 bg-slate-50 border-b border-slate-200 overflow-x-auto flex items-center gap-2 text-xs scrollbar-none shrink-0">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider shrink-0 mr-1">Mata Kuliah:</span>
                <div id="ekuiMultiTabsList" class="flex items-center gap-1.5 flex-nowrap"></div>
            </div>

            <!-- Modal Body Scrollable -->
            <div class="p-6 overflow-y-auto space-y-5 flex-1">
                <!-- Target Course Card with Live SKS Status Bar -->
                <div class="p-4 rounded-2xl bg-gradient-to-br from-indigo-50/60 to-purple-50/30 border border-indigo-100/80 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-indigo-100/60">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-100/80 px-2 py-0.5 rounded-md">Mata Kuliah Utama</span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span id="ekuiTargetKode" class="font-mono font-extrabold text-indigo-900 text-base">CS101</span>
                                <h4 id="ekuiTargetNama" class="font-bold text-slate-800 text-sm">Algoritma & Pemrograman</h4>
                            </div>
                            <span id="ekuiTargetMeta" class="text-xs text-slate-500 mt-0.5 block">S1 Informatika • Kurikulum 2024 • Semester 1</span>
                        </div>
                        <div class="text-left sm:text-right shrink-0">
                            <span class="text-[11px] font-bold text-slate-500 block uppercase">Target SKS</span>
                            <span id="ekuiTargetSksBadge" class="text-2xl font-black text-indigo-700 font-mono">4 <span class="text-xs font-semibold text-slate-500">SKS</span></span>
                        </div>
                    </div>

                    <!-- Live SKS Balance Bar (Kombinasi / Many-to-One Status) -->
                    <div class="mt-3 pt-1">
                        <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                            <div class="flex items-center gap-1.5">
                                <span>⚖️ Pemenuhan SKS:</span>
                                <span id="ekuiTotalSksText" class="font-mono text-indigo-950 font-extrabold">0 / 4 SKS</span>
                            </div>
                            <div id="ekuiStatusBadge" class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">
                                Memuat...
                            </div>
                        </div>
                        <!-- Progress bar -->
                        <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden">
                            <div id="ekuiProgressBar" class="h-full bg-indigo-600 transition-all duration-300 rounded-full" style="width: 0%"></div>
                        </div>
                        <p id="ekuiStatusHint" class="text-[11px] text-slate-500 mt-1.5">
                            💡 Total SKS dari mata kuliah ekuivalen harus mencukupi target SKS mata kuliah utama.
                        </p>
                    </div>
                </div>

                <!-- Existing Linked Ekuivalensi Courses -->
                <div>
                    <div class="flex items-center justify-between mb-2.5">
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <span>📋</span> Mata Kuliah Yang Ditautkan Sebagai Ekuivalen (<span id="ekuiLinkedCount">0</span>)
                        </h4>
                    </div>
                    
                    <div class="rounded-xl border border-slate-200 overflow-hidden bg-white">
                        <div id="ekuiEmptyNotice" class="p-6 text-center text-slate-400">
                            <span class="text-2xl block mb-1">🔗</span>
                            <p class="font-semibold text-xs text-slate-600">Belum ada mata kuliah ekuivalen yang ditautkan</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Pilih dari master mata kuliah lain di bawah atau masukkan manual.</p>
                        </div>
                        <table id="ekuiLinkedTable" class="w-full text-left text-xs hidden">
                            <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                                <tr>
                                    <th class="p-2.5 w-24">Kode MK</th>
                                    <th class="p-2.5">Nama MK Ekuivalen</th>
                                    <th class="p-2.5 text-center w-16">SKS</th>
                                    <th class="p-2.5 w-32">Kurikulum Asal</th>
                                    <th class="p-2.5">Keterangan</th>
                                    <th class="p-2.5 text-center w-14">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="ekuiLinkedBody" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Section: Tambah Ekuivalensi Baru (2 Mode: Multi-Select dari Master & Input Manual) -->
                <div class="pt-2 border-t border-slate-200">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <span>➕</span> Tambah Mata Kuliah Ekuivalen
                        </h4>
                        <!-- Sub tabs -->
                        <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-xl text-xs">
                            <button type="button" onclick="switchEkuiAddMode('multiselect')" id="btnEkuiModeMulti" class="px-3 py-1 font-bold rounded-lg bg-white text-indigo-700 shadow-2xs transition cursor-pointer">
                                <span>☑️</span> Pilih dari Master MK
                            </button>
                            <button type="button" onclick="switchEkuiAddMode('manual')" id="btnEkuiModeManual" class="px-3 py-1 font-bold rounded-lg text-slate-600 hover:text-slate-900 transition cursor-pointer">
                                <span>✍️</span> Input Manual
                            </button>
                        </div>
                    </div>

                    <!-- Panel A: Multi-Select from Master MK Candidates -->
                    <div id="ekuiPanelMulti" class="space-y-3">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <input type="text" id="searchEkuiCandidate" onkeyup="filterEkuiCandidates()" placeholder="Cari Kode atau Nama MK master untuk dijadikan ekuivalen..." class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                                <span class="absolute left-2.5 top-2.5 text-slate-400">🔍</span>
                            </div>
                            <button type="button" onclick="linkSelectedEkuiCandidates()" id="btnLinkCandidate" disabled class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-1.5 shrink-0 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                <span>🔗</span> Tautkan Terpilih (<span id="countSelectedCandidate">0</span>)
                            </button>
                        </div>

                        <!-- Candidates List with Checkboxes -->
                        <div class="max-h-56 overflow-y-auto rounded-xl border border-slate-200 divide-y divide-slate-100 bg-white" id="ekuiCandidateList">
                            <!-- Populated dynamically via JS -->
                        </div>
                        <div class="text-[11px] text-slate-500 flex items-center justify-between">
                            <span>Tip: Kamu bisa centang beberapa MK sekaligus jika SKS MK utama membutuhkan gabungan ekuivalensi.</span>
                            <span id="ekuiSelectedCandidateSksInfo" class="font-bold text-indigo-700"></span>
                        </div>
                    </div>

                    <!-- Panel B: Manual Form Input -->
                    <div id="ekuiPanelManual" class="hidden p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Kode MK Ekuivalen <span class="text-rose-500">*</span></label>
                                <input type="text" id="manual_kode_ekuivalen" placeholder="Misal: IF101" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl uppercase font-mono outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block font-bold text-slate-700 mb-1">Nama MK Ekuivalen <span class="text-rose-500">*</span></label>
                                <input type="text" id="manual_nama_ekuivalen" placeholder="Misal: Dasar Algoritma & Pemrograman" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">SKS Ekuivalen <span class="text-rose-500">*</span></label>
                                <input type="number" id="manual_sks_ekuivalen" min="1" max="10" value="2" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Kurikulum Asal</label>
                                <input type="text" id="manual_kurikulum_asal" placeholder="Misal: Kurikulum 2020 / MBKM" value="Kurikulum 2020" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Keterangan (Opsional)</label>
                                <input type="text" id="manual_keterangan" placeholder="Catatan konversi" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="flex justify-end pt-1">
                            <button type="button" onclick="simpanEkuiManual()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                <span>+</span> Simpan MK Ekuivalen
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
                <span class="text-xs text-slate-500">Perubahan ekuivalensi otomatis tersimpan ke sistem perwalian.</span>
                <button type="button" onclick="closeModalEkuivalensi()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                    Selesai
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 4: IMPORT EXCEL EKUIVALENSI MATA KULIAH -->
    <!-- ======================================================================= -->
    <div id="modalImportEkuivalensi" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5" onclick="if(event.target === this) closeModalImportEkuivalensi()">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl shadow-xs">
                        📥
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800">Import Pemetaan Ekuivalensi Excel</h3>
                        <p class="text-xs text-slate-500">Unggah pemetaan mata kuliah ekuivalen secara massal (.xlsx / .csv)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="downloadTemplateEkuivalensi()" class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
                        <span>📄</span> Unduh Template
                    </button>
                    <button type="button" onclick="closeModalImportEkuivalensi()" class="w-8 h-8 rounded-xl bg-white hover:bg-slate-100 text-slate-400 hover:text-slate-600 border border-slate-200 flex items-center justify-center font-bold text-sm transition cursor-pointer">✕</button>
                </div>
            </div>
            
            <!-- Modal Body Scrollable -->
            <div class="p-6 overflow-y-auto space-y-4 flex-1">
                <!-- Dropzone Area -->
                <input type="file" id="fileEkuiInput" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleEkuiFileSelect(event)">
                <div onclick="document.getElementById('fileEkuiInput').click()" class="dropzone-box p-6 text-center cursor-pointer transition hover:border-indigo-500 hover:bg-indigo-50/30" id="ekuiDropzone">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 mx-auto flex items-center justify-center text-2xl mb-2">📊</div>
                    <h4 class="text-xs font-bold text-slate-800">Klik atau Tarik File Excel Ekuivalensi ke Sini</h4>
                    <p class="text-[11px] text-slate-500 mt-1">Kolom yang dibutuhkan: <code>Kode MK Utama</code>, <code>Kode MK Ekuivalen</code>, <code>Nama MK Ekuivalen</code>, <code>SKS Ekuivalen</code>, <code>Kurikulum Asal</code>, <code>Keterangan</code></p>
                </div>

                <!-- Preview Container -->
                <div id="previewEkuiContainer" class="hidden space-y-4">
                    <div class="grid grid-cols-3 gap-2.5 text-xs">
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-[11px] font-bold text-slate-500 block">Total Baris</span>
                            <span class="text-lg font-extrabold text-slate-800 font-mono" id="statEkuiTotal">0</span>
                        </div>
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                            <span class="text-[11px] font-bold text-emerald-700 block">✓ Valid</span>
                            <span class="text-lg font-extrabold text-emerald-700 font-mono" id="statEkuiValid">0</span>
                        </div>
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl">
                            <span class="text-[11px] font-bold text-rose-700 block">✕ Tidak Lengkap</span>
                            <span class="text-lg font-extrabold text-rose-700 font-mono" id="statEkuiError">0</span>
                        </div>
                    </div>

                    <!-- Table Preview -->
                    <div class="max-h-64 overflow-y-auto rounded-xl border border-slate-200 text-xs">
                        <table class="w-full text-left" id="tablePreviewEkui">
                            <thead class="bg-slate-100 text-slate-700 uppercase text-[10px] font-bold sticky top-0 shadow-xs">
                                <tr>
                                    <th class="p-2.5 text-center w-10">No</th>
                                    <th class="p-2.5 w-24">MK Utama</th>
                                    <th class="p-2.5 w-28">MK Ekuivalen</th>
                                    <th class="p-2.5">Nama MK Ekuivalen</th>
                                    <th class="p-2.5 text-center w-14">SKS</th>
                                    <th class="p-2.5 w-28">Kurikulum</th>
                                    <th class="p-2.5">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="tbodyPreviewEkui"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
                <span class="text-xs text-slate-500" id="footerEkuiNote">Pilih file excel untuk memproses ekuivalensi.</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeModalImportEkuivalensi()" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition cursor-pointer">Batal</button>
                    <button type="button" onclick="sendImportEkuivalensi()" id="btnSaveImportEkui" disabled class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs transition disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                        Simpan ke Database
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
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
            updateBulkDeleteMKUI();
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

        // --- MULTIPLE DELETE MATA KULIAH ---
        function toggleCheckAllMK(master) {
            const isChecked = master.checked;
            const rows = document.querySelectorAll('.mk-row');
            rows.forEach(r => {
                if (r.style.display !== 'none') {
                    const cb = r.querySelector('.check-item-mk');
                    if (cb) cb.checked = isChecked;
                }
            });
            updateBulkDeleteMKUI();
        }

        function updateBulkDeleteMKUI() {
            const checkedBoxes = document.querySelectorAll('.check-item-mk:checked');
            const totalVisibleBoxes = Array.from(document.querySelectorAll('.mk-row'))
                .filter(r => r.style.display !== 'none')
                .map(r => r.querySelector('.check-item-mk'))
                .filter(Boolean);

            const count = checkedBoxes.length;

            const btnTop = document.getElementById('btnBulkDeleteMK');
            const bar = document.getElementById('bulkActionMKBar');
            const cntTop = document.getElementById('countSelectedMK');
            const cntText = document.getElementById('countSelectedMKText');
            const cntBtn = document.getElementById('countSelectedMKBtn');
            const cntEkui = document.getElementById('countSelectedMKEkuiBtn');
            if (cntEkui) cntEkui.innerText = count;
            const masterCb = document.getElementById('checkAllMK');

            if (cntTop) cntTop.innerText = count;
            if (cntText) cntText.innerText = count;
            if (cntBtn) cntBtn.innerText = count;

            if (count > 0) {
                if (btnTop) btnTop.classList.remove('hidden');
                if (bar) bar.classList.remove('hidden');
            } else {
                if (btnTop) btnTop.classList.add('hidden');
                if (bar) bar.classList.add('hidden');
            }

            if (masterCb && totalVisibleBoxes.length > 0) {
                const visibleChecked = totalVisibleBoxes.filter(cb => cb.checked).length;
                masterCb.checked = (visibleChecked === totalVisibleBoxes.length);
                masterCb.indeterminate = (visibleChecked > 0 && visibleChecked < totalVisibleBoxes.length);
            }
        }

        function deselectAllMK() {
            document.querySelectorAll('.check-item-mk').forEach(cb => cb.checked = false);
            const masterCb = document.getElementById('checkAllMK');
            if (masterCb) {
                masterCb.checked = false;
                masterCb.indeterminate = false;
            }
            updateBulkDeleteMKUI();
        }

        function hapusMKMultiple() {
            const checkedBoxes = Array.from(document.querySelectorAll('.check-item-mk:checked'));
            if (checkedBoxes.length === 0) {
                Swal.fire('Perhatian', 'Pilih minimal satu mata kuliah untuk dihapus.', 'info');
                return;
            }

            const ids = checkedBoxes.map(cb => parseInt(cb.value, 10));
            const kodes = checkedBoxes.map(cb => cb.getAttribute('data-kode') || '').filter(Boolean);
            const sampleKodes = kodes.slice(0, 5).join(', ') + (kodes.length > 5 ? ` dan ${kodes.length - 5} lainnya` : '');

            Swal.fire({
                title: `Hapus ${ids.length} Mata Kuliah?`,
                html: `Anda akan menghapus mata kuliah:<br><strong class="text-rose-600 font-mono">${sampleKodes}</strong><br><br><span class="text-xs text-slate-500">Mata kuliah yang dihapus tidak akan dapat dikontrak lagi. Tindakan ini tidak dapat dibatalkan.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: `Ya, Hapus ${ids.length} MK!`,
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang memproses penghapusan data mata kuliah terpilih.',
                        didOpen: () => Swal.showLoading(),
                        allowOutsideClick: false
                    });

                    try {
                        const res = await fetch('<?= site_url("prodi/perwalian/hapus_mata_kuliah_multiple") ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ ids: ids })
                        });
                        const json = await res.json();
                        Swal.close();

                        if (json.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Dihapus!',
                                text: json.message || `${ids.length} mata kuliah berhasil dihapus.`,
                                timer: 1800,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', json.message || 'Gagal menghapus mata kuliah.', 'error');
                        }
                    } catch (err) {
                        Swal.close();
                        Swal.fire('Error', 'Terjadi kesalahan server saat menghapus.', 'error');
                    }
                }
            });
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

        const EXISTING_MK_CODES = <?= json_encode(array_values(array_filter(array_map('strtoupper', array_column($mata_kuliah, 'kode_mk'))))) ?>;
        const EXISTING_MK_NAMES = <?= json_encode(array_values(array_filter(array_map('strtolower', array_column($mata_kuliah, 'nama_mk'))))) ?>;
        const ALL_MASTER_MKS = <?= json_encode($mata_kuliah) ?>;

        let parsedMKData = [];
        let mkPreviewFilter = 'all';

        let parsedKRSData = [];
        let krsPreviewFilter = 'all';

        // --- VALIDASI CLIENT-SIDE MASTER MATA KULIAH ---
        function validateCourseClient(r, index, seenCodes, seenNames) {
            const rowNum = index + 1;
            const errors = [];
            let isDuplicate = false;
            let duplicateType = '';

            let rawKode = String(r['Kode MK'] || r['kode_mk'] || r['Kode'] || r['KODE'] || r['Kode Matakuliah'] || '').trim();
            let rawNama = String(r['Nama Mata Kuliah'] || r['nama_mk'] || r['Nama'] || r['NAMA'] || r['Mata Kuliah'] || r['Nama MK'] || '').trim();
            let rawSks  = r['SKS'] !== undefined ? r['SKS'] : (r['sks'] !== undefined ? r['sks'] : (r['Sks'] !== undefined ? r['Sks'] : ''));
            let rawSmt  = r['Semester'] !== undefined ? r['Semester'] : (r['semester'] !== undefined ? r['semester'] : (r['Smt'] !== undefined ? r['Smt'] : ''));
            let rawProdi= String(r['Program Studi'] || r['Prodi'] || r['prodi'] || '<?= htmlspecialchars($user_prodi) ?>').trim();
            let rawJenis= String(r['Jenis'] || r['Sifat'] || r['jenis'] || 'Wajib').trim();
            let rawKur  = String(r['Kurikulum'] || r['kurikulum'] || '2024').trim();

            // 1. Validasi Kode MK & Cek Tertukar
            if (!rawKode) {
                errors.push('Kode MK wajib diisi');
            } else {
                if (rawKode.length > 25 || rawKode.split(/\s+/).length >= 3) {
                    errors.push(`Kolom Kode MK tertukar dengan Nama MK ('${rawKode.substring(0, 22)}...')`);
                } else if (rawKode.length < 2) {
                    errors.push('Kode MK terlalu pendek (< 2 karakter)');
                }
            }

            // 2. Validasi Nama MK & Cek Tertukar
            if (!rawNama) {
                errors.push('Nama Mata Kuliah wajib diisi');
            } else {
                if (rawNama.length <= 5 && !rawNama.includes(' ') && /^[A-Za-z0-9_-]+$/.test(rawNama)) {
                    errors.push(`Kolom Nama MK terindikasi tertukar dengan Kode ('${rawNama}')`);
                } else if (rawNama.length < 3) {
                    errors.push('Nama Mata Kuliah terlalu pendek (< 3 karakter)');
                }
            }

            // 3. Validasi SKS
            if (rawSks === '' || rawSks === null || rawSks === undefined) {
                errors.push('SKS wajib diisi');
            } else {
                const numSks = Number(rawSks);
                if (isNaN(numSks) || !Number.isInteger(numSks)) {
                    errors.push(`Kolom SKS harus angka (1-10), bukan '${rawSks}' (kemungkinan kolom tertukar)`);
                } else if (numSks <= 0 || numSks > 10) {
                    errors.push(`SKS tidak wajar (${numSks} SKS). Harus antara 1-10`);
                }
            }

            // 4. Validasi Semester
            if (rawSmt !== '' && rawSmt !== null && rawSmt !== undefined) {
                const numSmt = Number(rawSmt);
                if (isNaN(numSmt) || !Number.isInteger(numSmt)) {
                    errors.push(`Kolom Semester harus angka (1-8), bukan '${rawSmt}'`);
                } else if (numSmt <= 0 || numSmt > 14) {
                    errors.push(`Semester tidak wajar (${numSmt}). Harus antara 1-8`);
                }
            }

            // 5. Validasi Jenis
            const normJenis = rawJenis.charAt(0).toUpperCase() + rawJenis.slice(1).toLowerCase();
            if (rawJenis && !['Wajib', 'Pilihan'].includes(normJenis)) {
                errors.push(`Jenis harus 'Wajib' atau 'Pilihan', bukan '${rawJenis}'`);
            }

            const upperKode = rawKode.toUpperCase();
            const lowerNama = rawNama.toLowerCase();

            // 6. Validasi Duplikasi
            if (errors.length === 0) {
                if (seenCodes[upperKode]) {
                    isDuplicate = true;
                    duplicateType = `Duplikat di File: Kode MK ${upperKode} sama dengan baris ${seenCodes[upperKode]}`;
                } else {
                    seenCodes[upperKode] = rowNum;
                }

                if (!isDuplicate && seenNames[lowerNama]) {
                    isDuplicate = true;
                    duplicateType = `Duplikat di File: Nama '${rawNama}' sama dengan baris ${seenNames[lowerNama]}`;
                } else if (!isDuplicate) {
                    seenNames[lowerNama] = rowNum;
                }

                if (!isDuplicate) {
                    if (EXISTING_MK_CODES.includes(upperKode)) {
                        isDuplicate = true;
                        duplicateType = `Sudah ada di Database (Kode ${upperKode})`;
                    } else if (EXISTING_MK_NAMES.includes(lowerNama)) {
                        isDuplicate = true;
                        duplicateType = `Nama mata kuliah sudah terdaftar di database`;
                    }
                }
            }

            let status = 'valid';
            if (errors.length > 0) status = 'error';
            else if (isDuplicate) status = 'duplicate';

            return {
                rowNum: rowNum,
                kode_mk: upperKode,
                nama_mk: rawNama,
                sks: Number(rawSks) || 3,
                semester: Number(rawSmt) || 1,
                prodi: rawProdi,
                jenis: ['Wajib', 'Pilihan'].includes(normJenis) ? normJenis : 'Wajib',
                kurikulum: rawKur || '2024',
                status: status,
                errors: errors,
                duplicateInfo: duplicateType,
            };
        }

        // --- MODAL IMPORT MK ---
        function openModalImportMK() {
            document.getElementById('previewMKContainer').classList.add('hidden');
            document.getElementById('fileMKInput').value = '';
            parsedMKData = [];
            mkPreviewFilter = 'all';
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
                const json = XLSX.utils.sheet_to_json(firstSheet, { defval: '' });

                if (!json || json.length === 0) {
                    Swal.fire('File Kosong', 'Tidak ada baris data yang ditemukan dalam file.', 'warning');
                    return;
                }

                const seenCodes = {};
                const seenNames = {};
                parsedMKData = json.map((r, i) => validateCourseClient(r, i, seenCodes, seenNames));

                updateMKStats();
                renderMKPreviewTable();
                document.getElementById('previewMKContainer').classList.remove('hidden');
            };
            reader.readAsArrayBuffer(file);
        }

        function updateMKStats() {
            const total = parsedMKData.length;
            const validCount = parsedMKData.filter(r => r.status === 'valid').length;
            const dupCount = parsedMKData.filter(r => r.status === 'duplicate').length;
            const errCount = parsedMKData.filter(r => r.status === 'error').length;

            document.getElementById('statMKTotal').innerText = total;
            document.getElementById('statMKValid').innerText = validCount;
            document.getElementById('statMKDuplicate').innerText = dupCount;
            document.getElementById('statMKError').innerText = errCount;

            document.getElementById('countTabMK-all').innerText = total;
            document.getElementById('countTabMK-valid').innerText = validCount;
            document.getElementById('countTabMK-duplicate').innerText = dupCount;
            document.getElementById('countTabMK-error').innerText = errCount;

            const alertDup = document.getElementById('alertMKDuplicate');
            if (dupCount > 0) alertDup.classList.remove('hidden');
            else alertDup.classList.add('hidden');

            const alertErr = document.getElementById('alertMKError');
            if (errCount > 0) alertErr.classList.remove('hidden');
            else alertErr.classList.add('hidden');
        }

        function filterMKPreview(filterType) {
            mkPreviewFilter = filterType;
            ['all', 'valid', 'duplicate', 'error'].forEach(t => {
                const btn = document.getElementById('tabMKFilter-' + t);
                if (t === filterType) {
                    btn.className = 'px-3 py-1 font-bold rounded-lg bg-brand-500 text-white transition';
                } else {
                    btn.className = 'px-3 py-1 font-bold rounded-lg text-slate-600 hover:bg-slate-100 transition';
                }
            });
            renderMKPreviewTable();
        }

        function renderMKPreviewTable() {
            const tbody = document.getElementById('tbodyPreviewMK');
            const skipDup = document.getElementById('chkSkipMKDuplicate')?.checked ?? true;

            let filtered = parsedMKData;
            if (mkPreviewFilter === 'valid') {
                filtered = parsedMKData.filter(r => r.status === 'valid');
            } else if (mkPreviewFilter === 'duplicate') {
                filtered = parsedMKData.filter(r => r.status === 'duplicate');
            } else if (mkPreviewFilter === 'error') {
                filtered = parsedMKData.filter(r => r.status === 'error');
            }

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-slate-400 italic">Tidak ada baris pada filter ini.</td></tr>`;
            } else {
                tbody.innerHTML = filtered.slice(0, 100).map(r => {
                    let statusBadge = '';
                    let rowBg = '';
                    let note = '';

                    if (r.status === 'valid') {
                        statusBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">✓ Valid</span>`;
                        note = `<span class="text-emerald-700 font-medium">Format sesuai template</span>`;
                    } else if (r.status === 'duplicate') {
                        statusBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">⚠️ Duplikat</span>`;
                        rowBg = 'bg-amber-50/40';
                        note = `<span class="text-amber-800 font-semibold">${r.duplicateInfo}</span>`;
                    } else {
                        statusBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">✕ Error</span>`;
                        rowBg = 'bg-rose-50/50';
                        note = `<span class="text-rose-700 font-bold">${r.errors.join('<br>')}</span>`;
                    }

                    return `
                        <tr class="${rowBg}">
                            <td class="p-2.5 text-center text-slate-400 font-mono text-[11px]">${r.rowNum}</td>
                            <td class="p-2.5">${statusBadge}</td>
                            <td class="p-2.5 font-mono font-bold ${r.status === 'error' && r.errors.some(e=>e.includes('Kode')) ? 'text-rose-600 bg-rose-100/50 px-1 rounded' : 'text-brand-700'}">${r.kode_mk || '-'}</td>
                            <td class="p-2.5 font-semibold text-slate-800 ${r.status === 'error' && r.errors.some(e=>e.includes('Nama')) ? 'text-rose-600 bg-rose-100/50 px-1 rounded' : ''}">${r.nama_mk || '-'}</td>
                            <td class="p-2.5 text-center font-mono ${r.status === 'error' && r.errors.some(e=>e.includes('SKS')) ? 'text-rose-600 font-bold bg-rose-100/50 rounded' : ''}">${r.sks}</td>
                            <td class="p-2.5 text-center ${r.status === 'error' && r.errors.some(e=>e.includes('Semester')) ? 'text-rose-600 font-bold bg-rose-100/50 rounded' : ''}">Smt ${r.semester}</td>
                            <td class="p-2.5 text-slate-600">${r.jenis}</td>
                            <td class="p-2.5 text-[11px] leading-tight">${note}</td>
                        </tr>
                    `;
                }).join('');
            }

            // Hitung data yang siap disimpan
            const readyRows = parsedMKData.filter(r => r.status === 'valid' || (!skipDup && r.status === 'duplicate'));
            const btnSave = document.getElementById('btnSaveImportMK');
            const footerNote = document.getElementById('footerMKValidNote');

            if (readyRows.length > 0) {
                btnSave.disabled = false;
                btnSave.innerText = `Simpan (${readyRows.length} Data) ke Database`;
                footerNote.innerHTML = `<span class="text-emerald-700 font-bold">✓ ${readyRows.length} baris siap disimpan</span>${skipDup && parsedMKData.some(r=>r.status==='duplicate') ? ' (duplikat dilewati)' : ''}.`;
            } else {
                btnSave.disabled = true;
                btnSave.innerText = 'Tidak Ada Data Valid';
                footerNote.innerHTML = `<span class="text-rose-600 font-bold">Tidak ada baris valid yang dapat disimpan.</span> Perbaiki file excel sesuai template.`;
            }
        }

        async function sendImportMK() {
            const skipDup = document.getElementById('chkSkipMKDuplicate')?.checked ?? true;
            const readyRows = parsedMKData.filter(r => r.status === 'valid' || (!skipDup && r.status === 'duplicate'));
            if (readyRows.length === 0) return;

            const btn = document.getElementById('btnSaveImportMK');
            btn.disabled = true;
            btn.innerText = 'Menyimpan ke database...';

            try {
                const res = await fetch('<?= site_url("prodi/perwalian/import_mata_kuliah") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        courses: readyRows,
                        allow_update: !skipDup
                    })
                });
                const json = await res.json();
                if (json.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Berhasil!',
                        html: `
                            <div class="text-left text-xs space-y-1 mt-2">
                                <p class="font-bold text-slate-800">${json.message}</p>
                                ${json.skipped_duplicate > 0 ? `<p class="text-amber-700 font-medium">⚠️ ${json.skipped_duplicate} data duplikat dilewati.</p>` : ''}
                                ${json.skipped_invalid > 0 ? `<p class="text-rose-700 font-medium">✕ ${json.skipped_invalid} baris tidak valid dilewati.</p>` : ''}
                            </div>
                        `,
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Peringatan Import', json.message, json.status === 'warning' ? 'warning' : 'error');
                }
            } catch (err) {
                Swal.fire('Error', 'Gagal memproses data import ke server.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Simpan ke Database';
            }
        }

        function downloadTemplateMK() {
            const sampleData = [
                { 'Kode MK': 'CS101', 'Nama Mata Kuliah': 'Algoritma & Pemrograman', 'SKS': 3, 'Semester': 1, 'Program Studi': '<?= htmlspecialchars($user_prodi ?: "S1 Informatika") ?>', 'Jenis': 'Wajib', 'Kurikulum': '2024' },
                { 'Kode MK': 'CS102', 'Nama Mata Kuliah': 'Struktur Data & Analisis Algoritma', 'SKS': 4, 'Semester': 2, 'Program Studi': '<?= htmlspecialchars($user_prodi ?: "S1 Informatika") ?>', 'Jenis': 'Wajib', 'Kurikulum': '2024' },
                { 'Kode MK': 'CS201', 'Nama Mata Kuliah': 'Pemrograman Berbasis Web', 'SKS': 3, 'Semester': 3, 'Program Studi': '<?= htmlspecialchars($user_prodi ?: "S1 Informatika") ?>', 'Jenis': 'Wajib', 'Kurikulum': '2024' },
                { 'Kode MK': 'CS305', 'Nama Mata Kuliah': 'Kecerdasan Buatan Terapan', 'SKS': 3, 'Semester': 5, 'Program Studi': '<?= htmlspecialchars($user_prodi ?: "S1 Informatika") ?>', 'Jenis': 'Pilihan', 'Kurikulum': '2024' },
            ];
            const ws = XLSX.utils.json_to_sheet(sampleData);
            ws['!cols'] = [
                { wch: 16 }, // Kode MK
                { wch: 38 }, // Nama Mata Kuliah
                { wch: 10 }, // SKS
                { wch: 12 }, // Semester
                { wch: 28 }, // Program Studi
                { wch: 14 }, // Jenis
                { wch: 14 }  // Kurikulum
            ];
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Template Master MK');
            XLSX.writeFile(wb, 'template_master_mata_kuliah.xlsx');
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // --- EKUIVALENSI MATA KULIAH MANAGEMENT ---
        let currentEkuiMK = null;
        let ekuiSelectedMKList = [];
        let ekuiActiveIndex = 0;
        let ekuiCandidates = [];
        let selectedCandidateIds = new Set();
        let parsedEkuiExcelData = [];

        function openModalEkuivalensiSingle(mk) {
            if (typeof mk === 'string') {
                try { mk = JSON.parse(mk); } catch (e) { console.error(e); }
            }
            if (!mk || !mk.id) return;
            ekuiSelectedMKList = [mk];
            ekuiActiveIndex = 0;
            renderEkuiMultiTabs();
            loadEkuivalensiForMk(mk);
            document.getElementById('modalEkuivalensi').classList.remove('hidden');
        }

        function openModalEkuivalensiMultiple() {
            const checkedBoxes = Array.from(document.querySelectorAll('.check-item-mk:checked'));
            if (checkedBoxes.length === 0) {
                Swal.fire('Pilih Mata Kuliah', 'Centang minimal satu mata kuliah pada tabel untuk mengatur ekuivalensi.', 'info');
                return;
            }

            const selectedIds = new Set(checkedBoxes.map(cb => String(cb.value)));
            ekuiSelectedMKList = (typeof ALL_MASTER_MKS !== 'undefined' ? ALL_MASTER_MKS : []).filter(m => selectedIds.has(String(m.id)));

            if (ekuiSelectedMKList.length === 0) {
                Swal.fire('Error', 'Data mata kuliah terpilih tidak ditemukan.', 'error');
                return;
            }

            ekuiActiveIndex = 0;
            renderEkuiMultiTabs();
            loadEkuivalensiForMk(ekuiSelectedMKList[0]);
            document.getElementById('modalEkuivalensi').classList.remove('hidden');
        }

        function closeModalEkuivalensi() {
            const modal = document.getElementById('modalEkuivalensi');
            if (modal) modal.classList.add('hidden');
            currentEkuiMK = null;
            selectedCandidateIds.clear();
        }

        function renderEkuiMultiTabs() {
            const bar = document.getElementById('ekuiMultiTabsBar');
            const list = document.getElementById('ekuiMultiTabsList');
            if (!bar || !list) return;

            if (ekuiSelectedMKList.length <= 1) {
                bar.classList.add('hidden');
                list.innerHTML = '';
                return;
            }

            bar.classList.remove('hidden');
            list.innerHTML = ekuiSelectedMKList.map((mk, idx) => {
                const isActive = (idx === ekuiActiveIndex);
                const activeCls = isActive 
                    ? 'bg-indigo-600 text-white font-bold shadow-xs' 
                    : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 font-semibold';
                return `
                    <button type="button" onclick="selectEkuiTab(${idx})" class="px-3 py-1 rounded-xl text-xs flex items-center gap-1.5 transition shrink-0 cursor-pointer ${activeCls}">
                        <span class="font-mono font-bold">${escapeHtml(mk.kode_mk)}</span>
                        <span class="text-[11px] opacity-80">(${mk.sks} SKS)</span>
                    </button>
                `;
            }).join('');
        }

        function selectEkuiTab(idx) {
            if (idx < 0 || idx >= ekuiSelectedMKList.length) return;
            ekuiActiveIndex = idx;
            renderEkuiMultiTabs();
            loadEkuivalensiForMk(ekuiSelectedMKList[idx]);
        }

        async function loadEkuivalensiForMk(mk) {
            currentEkuiMK = mk;
            selectedCandidateIds.clear();

            // Populate header target info
            const elKode = document.getElementById('ekuiTargetKode');
            const elNama = document.getElementById('ekuiTargetNama');
            const elMeta = document.getElementById('ekuiTargetMeta');
            const elSks = document.getElementById('ekuiTargetSksBadge');

            if (elKode) elKode.innerText = mk.kode_mk || '-';
            if (elNama) elNama.innerText = mk.nama_mk || '-';
            if (elMeta) elMeta.innerText = `${mk.prodi || 'Program Studi'} • Kurikulum ${mk.kurikulum || '2024'} • Semester ${mk.semester || 1}`;
            if (elSks) elSks.innerHTML = `${mk.sks || 0} <span class="text-xs font-semibold text-slate-500">SKS</span>`;

            // Reset status while loading
            const bar = document.getElementById('ekuiProgressBar');
            const badge = document.getElementById('ekuiStatusBadge');
            const txt = document.getElementById('ekuiTotalSksText');
            if (bar) { bar.style.width = '20%'; bar.className = 'h-full bg-slate-300 transition-all duration-300 rounded-full animate-pulse'; }
            if (badge) { badge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200'; badge.innerText = 'Memuat...'; }
            if (txt) txt.innerText = `... / ${mk.sks} SKS`;

            try {
                const res = await fetch(`<?= site_url("prodi/perwalian/get_ekuivalensi_ajax/") ?>${mk.id}`);
                const json = await res.json();
                if (json.status === 'success') {
                    renderEkuiData(json);
                } else {
                    Swal.fire('Error', json.message || 'Gagal memuat data ekuivalensi.', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan jaringan saat memuat data ekuivalensi.', 'error');
            }
        }

        function renderEkuiData(data) {
            const targetSks = parseInt(data.target_sks, 10) || parseInt(currentEkuiMK?.sks, 10) || 0;
            const totalSks = parseInt(data.total_sks, 10) || 0;
            const items = data.items || [];

            // Progress calculation
            const percent = targetSks > 0 ? Math.min(100, Math.round((totalSks / targetSks) * 100)) : 0;
            const bar = document.getElementById('ekuiProgressBar');
            const badge = document.getElementById('ekuiStatusBadge');
            const txt = document.getElementById('ekuiTotalSksText');
            const hint = document.getElementById('ekuiStatusHint');

            if (txt) txt.innerText = `${totalSks} / ${targetSks} SKS`;
            if (bar) {
                bar.style.width = `${percent}%`;
                bar.classList.remove('animate-pulse');
            }

            if (items.length === 0) {
                if (bar) bar.className = 'h-full bg-slate-300 transition-all duration-300 rounded-full';
                if (badge) {
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200';
                    badge.innerText = 'Belum Ada Ekuivalensi';
                }
                if (hint) hint.innerHTML = '💡 Belum ada mata kuliah yang dipetakan sebagai ekuivalen untuk mata kuliah ini.';
            } else if (totalSks === targetSks) {
                if (bar) bar.className = 'h-full bg-emerald-500 transition-all duration-300 rounded-full';
                if (badge) {
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-300';
                    badge.innerText = `✅ SKS Terpenuhi (${totalSks}/${targetSks} SKS)`;
                }
                if (hint) hint.innerHTML = `🎉 SKS ekuivalensi telah seimbang (pas ${targetSks} SKS). Pemetaan kurikulum telah lengkap.`;
            } else if (totalSks < targetSks) {
                const def = targetSks - totalSks;
                if (bar) bar.className = 'h-full bg-amber-500 transition-all duration-300 rounded-full';
                if (badge) {
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-800 border border-amber-300';
                    badge.innerText = `⚠️ Kurang ${def} SKS (${totalSks}/${targetSks} SKS)`;
                }
                if (hint) hint.innerHTML = `⚠️ SKS MK ekuivalen (${totalSks} SKS) masih kurang <strong>${def} SKS</strong> dari MK utama (${targetSks} SKS). Silakan tambahkan MK ekuivalen pendamping di bawah agar genap.`;
            } else {
                const sur = totalSks - targetSks;
                if (bar) bar.className = 'h-full bg-indigo-600 transition-all duration-300 rounded-full';
                if (badge) {
                    badge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-300';
                    badge.innerText = `ℹ️ Lebih ${sur} SKS (${totalSks}/${targetSks} SKS)`;
                }
                if (hint) hint.innerHTML = `ℹ️ Total SKS ekuivalen (${totalSks} SKS) melebihi target SKS utama (${targetSks} SKS). Mahasiswa diakui sesuai aturan konversi.`;
            }

            // Linked Items Table
            const countEl = document.getElementById('ekuiLinkedCount');
            const emptyNotice = document.getElementById('ekuiEmptyNotice');
            const linkedTable = document.getElementById('ekuiLinkedTable');
            const linkedBody = document.getElementById('ekuiLinkedBody');

            if (countEl) countEl.innerText = items.length;

            if (items.length === 0) {
                if (emptyNotice) emptyNotice.classList.remove('hidden');
                if (linkedTable) linkedTable.classList.add('hidden');
                if (linkedBody) linkedBody.innerHTML = '';
            } else {
                if (emptyNotice) emptyNotice.classList.add('hidden');
                if (linkedTable) linkedTable.classList.remove('hidden');
                if (linkedBody) {
                    linkedBody.innerHTML = items.map(item => `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-2.5 font-mono font-bold text-indigo-700">${escapeHtml(item.kode_mk_ekuivalen)}</td>
                            <td class="p-2.5 font-semibold text-slate-800">${escapeHtml(item.nama_mk_ekuivalen)}</td>
                            <td class="p-2.5 text-center">
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded-md font-mono text-[11px]">${parseInt(item.sks_ekuivalen, 10)} SKS</span>
                            </td>
                            <td class="p-2.5 text-slate-600">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[11px] font-medium">${escapeHtml(item.kurikulum_asal || '-')}</span>
                            </td>
                            <td class="p-2.5 text-slate-500 text-[11px]">${escapeHtml(item.keterangan || '-')}</td>
                            <td class="p-2.5 text-center">
                                <button type="button" onclick="hapusEkuiItem(${item.id})" class="w-7 h-7 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition flex items-center justify-center mx-auto cursor-pointer" title="Hapus ekuivalensi ini">
                                    🗑️
                                </button>
                            </td>
                        </tr>
                    `).join('');
                }
            }

            // Update row badge on background master table
            updateMainTableRowEkuiBadge(currentEkuiMK.id, items.length, totalSks, targetSks);

            // Populate candidates
            ekuiCandidates = data.master_candidates || [];
            renderEkuiCandidatesList();
        }

        function updateMainTableRowEkuiBadge(mkId, count, totalSks, targetSks) {
            const col = document.getElementById(`col-ekuivalensi-${mkId}`);
            if (!col) return;

            const mkObj = (typeof ALL_MASTER_MKS !== 'undefined' ? ALL_MASTER_MKS : []).find(m => String(m.id) === String(mkId)) || currentEkuiMK;
            const mkJsonStr = escapeHtml(JSON.stringify(mkObj));

            if (count > 0) {
                const isPas = (totalSks === targetSks);
                const isKurang = (totalSks < targetSks);
                const badgeClass = isPas
                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                    : (isKurang ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' : 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100');
                const icon = isPas ? '✅' : (isKurang ? '⚠️' : 'ℹ️');

                col.innerHTML = `
                    <button onclick='openModalEkuivalensiSingle(${JSON.stringify(mkObj)})' class="px-2 py-0.5 rounded-lg border text-[11px] font-bold inline-flex items-center gap-1 transition cursor-pointer ${badgeClass}" title="${count} MK ekuivalen (${totalSks}/${targetSks} SKS)">
                        <span>${icon}</span> ${count} MK (${totalSks}/${targetSks} SKS)
                    </button>
                `;
            } else {
                col.innerHTML = `
                    <button onclick='openModalEkuivalensiSingle(${JSON.stringify(mkObj)})' class="px-2 py-0.5 rounded-lg border border-dashed border-slate-300 text-[11px] font-semibold text-slate-400 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50/50 transition cursor-pointer" title="Belum ada ekuivalensi. Klik untuk menambahkan.">
                        + Atur
                    </button>
                `;
            }
        }

        function switchEkuiAddMode(mode) {
            const panelMulti = document.getElementById('ekuiPanelMulti');
            const panelManual = document.getElementById('ekuiPanelManual');
            const btnMulti = document.getElementById('btnEkuiModeMulti');
            const btnManual = document.getElementById('btnEkuiModeManual');

            if (mode === 'multiselect') {
                if (panelMulti) panelMulti.classList.remove('hidden');
                if (panelManual) panelManual.classList.add('hidden');
                if (btnMulti) { btnMulti.className = 'px-3 py-1 font-bold rounded-lg bg-white text-indigo-700 shadow-2xs transition cursor-pointer'; }
                if (btnManual) { btnManual.className = 'px-3 py-1 font-bold rounded-lg text-slate-600 hover:text-slate-900 transition cursor-pointer'; }
            } else {
                if (panelMulti) panelMulti.classList.add('hidden');
                if (panelManual) panelManual.classList.remove('hidden');
                if (btnManual) { btnManual.className = 'px-3 py-1 font-bold rounded-lg bg-white text-indigo-700 shadow-2xs transition cursor-pointer'; }
                if (btnMulti) { btnMulti.className = 'px-3 py-1 font-bold rounded-lg text-slate-600 hover:text-slate-900 transition cursor-pointer'; }
            }
        }

        function filterEkuiCandidates() {
            renderEkuiCandidatesList();
        }

        function renderEkuiCandidatesList() {
            const listEl = document.getElementById('ekuiCandidateList');
            if (!listEl) return;

            const q = (document.getElementById('searchEkuiCandidate')?.value || '').toLowerCase().trim();
            const filtered = ekuiCandidates.filter(c => {
                if (!q) return true;
                const txt = `${c.kode_mk || ''} ${c.nama_mk || ''} ${c.prodi || ''}`.toLowerCase();
                return txt.includes(q);
            });

            if (filtered.length === 0) {
                listEl.innerHTML = `
                    <div class="p-6 text-center text-slate-400 text-xs">
                        Tidak ada mata kuliah master yang cocok.
                    </div>
                `;
                updateCandidateSelectionInfo();
                return;
            }

            listEl.innerHTML = filtered.map(c => {
                const isChecked = selectedCandidateIds.has(c.id);
                return `
                    <label class="flex items-center justify-between p-2.5 hover:bg-slate-50 transition cursor-pointer select-none ${isChecked ? 'bg-indigo-50/50' : ''}">
                        <div class="flex items-center gap-2.5">
                            <input type="checkbox" value="${c.id}" ${isChecked ? 'checked' : ''} onchange="toggleCandidateCheckbox(${c.id}, this.checked)" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-slate-800 text-xs">${escapeHtml(c.kode_mk)}</span>
                                    <span class="font-semibold text-slate-800 text-xs">${escapeHtml(c.nama_mk)}</span>
                                </div>
                                <span class="text-[11px] text-slate-400">${escapeHtml(c.prodi || 'Program Studi')} • Kurikulum ${escapeHtml(c.kurikulum || '2024')} • Semester ${c.semester || 1}</span>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-xs bg-indigo-50 text-indigo-700">${c.sks} SKS</span>
                        </div>
                    </label>
                `;
            }).join('');

            updateCandidateSelectionInfo();
        }

        function toggleCandidateCheckbox(id, checked) {
            if (checked) {
                selectedCandidateIds.add(id);
            } else {
                selectedCandidateIds.delete(id);
            }
            updateCandidateSelectionInfo();
        }

        function updateCandidateSelectionInfo() {
            const count = selectedCandidateIds.size;
            const btn = document.getElementById('btnLinkCandidate');
            const cntSpan = document.getElementById('countSelectedCandidate');
            const sksInfo = document.getElementById('ekuiSelectedCandidateSksInfo');

            if (cntSpan) cntSpan.innerText = count;

            let totalSelSks = 0;
            selectedCandidateIds.forEach(id => {
                const c = ekuiCandidates.find(item => item.id == id);
                if (c) totalSelSks += (parseInt(c.sks, 10) || 0);
            });

            if (sksInfo) {
                sksInfo.innerText = count > 0 ? `+${totalSelSks} SKS dari ${count} MK terpilih` : '';
            }

            if (btn) {
                btn.disabled = (count === 0);
            }
        }

        async function linkSelectedEkuiCandidates() {
            if (!currentEkuiMK || selectedCandidateIds.size === 0) return;

            const candidateIds = Array.from(selectedCandidateIds);
            const btn = document.getElementById('btnLinkCandidate');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Menautkan...';
            }

            try {
                const res = await fetch('<?= site_url("prodi/perwalian/simpan_ekuivalensi_ajax") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_mk_utama: currentEkuiMK.id,
                        kode_mk_utama: currentEkuiMK.kode_mk,
                        candidate_ids: candidateIds
                    })
                });
                const json = await res.json();
                if (json.status === 'success') {
                    selectedCandidateIds.clear();
                    await loadEkuivalensiForMk(currentEkuiMK);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Ditautkan',
                        text: json.message || `${candidateIds.length} mata kuliah berhasil dijadikan ekuivalen.`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal', json.message || 'Gagal menautkan mata kuliah.', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan sistem saat menautkan mata kuliah.', 'error');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = `<span>🔗</span> Tautkan Terpilih (<span id="countSelectedCandidate">${selectedCandidateIds.size}</span>)`;
                }
            }
        }

        async function simpanEkuiManual() {
            if (!currentEkuiMK) return;

            const kode = (document.getElementById('manual_kode_ekuivalen')?.value || '').trim();
            const nama = (document.getElementById('manual_nama_ekuivalen')?.value || '').trim();
            const sks = parseInt(document.getElementById('manual_sks_ekuivalen')?.value, 10);
            const kur = (document.getElementById('manual_kurikulum_asal')?.value || '').trim() || 'Kurikulum 2020';
            const ket = (document.getElementById('manual_keterangan')?.value || '').trim();

            if (!kode || !nama) {
                Swal.fire('Form Belum Lengkap', 'Kode MK dan Nama MK Ekuivalen wajib diisi.', 'warning');
                return;
            }

            if (isNaN(sks) || sks <= 0 || sks > 10) {
                Swal.fire('Format SKS Tidak Valid', 'SKS harus berupa angka antara 1 sampai 10.', 'warning');
                return;
            }

            try {
                const res = await fetch('<?= site_url("prodi/perwalian/simpan_ekuivalensi_ajax") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_mk_utama: currentEkuiMK.id,
                        kode_mk_utama: currentEkuiMK.kode_mk,
                        items: [{
                            kode_mk_ekuivalen: kode,
                            nama_mk_ekuivalen: nama,
                            sks_ekuivalen: sks,
                            kurikulum_asal: kur,
                            keterangan: ket
                        }]
                    })
                });
                const json = await res.json();
                if (json.status === 'success') {
                    // Reset input
                    document.getElementById('manual_kode_ekuivalen').value = '';
                    document.getElementById('manual_nama_ekuivalen').value = '';
                    document.getElementById('manual_keterangan').value = '';
                    await loadEkuivalensiForMk(currentEkuiMK);
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan',
                        text: 'Mata kuliah ekuivalen berhasil ditambahkan.',
                        timer: 1300,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal', json.message || 'Gagal menyimpan ekuivalensi.', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan sistem saat menyimpan ekuivalensi.', 'error');
            }
        }

        async function hapusEkuiItem(idItem) {
            if (!idItem) return;

            Swal.fire({
                title: 'Hapus Ekuivalensi?',
                text: 'Mata kuliah ini tidak akan lagi dipetakan sebagai ekuivalen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const fd = new FormData();
                        fd.append('id', idItem);
                        const res = await fetch('<?= site_url("prodi/perwalian/hapus_ekuivalensi_ajax") ?>', {
                            method: 'POST',
                            body: fd
                        });
                        const json = await res.json();
                        if (json.status === 'success') {
                            if (currentEkuiMK) {
                                await loadEkuivalensiForMk(currentEkuiMK);
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus',
                                text: json.message || 'Ekuivalensi berhasil dihapus.',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Gagal', json.message || 'Gagal menghapus ekuivalensi.', 'error');
                        }
                    } catch (err) {
                        console.error(err);
                        Swal.fire('Error', 'Terjadi kesalahan saat menghapus.', 'error');
                    }
                }
            });
        }

        // --- EKUIVALENSI EXCEL BULK IMPORT ---
        function openModalImportEkuivalensi() {
            parsedEkuiExcelData = [];
            const fileInput = document.getElementById('fileEkuiInput');
            if (fileInput) fileInput.value = '';

            const preview = document.getElementById('previewEkuiContainer');
            if (preview) preview.classList.add('hidden');

            const btnSave = document.getElementById('btnSaveImportEkui');
            if (btnSave) {
                btnSave.disabled = true;
                btnSave.innerText = 'Simpan ke Database';
            }

            const note = document.getElementById('footerEkuiNote');
            if (note) note.innerText = 'Pilih file excel untuk memproses ekuivalensi.';

            document.getElementById('modalImportEkuivalensi').classList.remove('hidden');
        }

        function closeModalImportEkuivalensi() {
            document.getElementById('modalImportEkuivalensi').classList.add('hidden');
        }

        function handleEkuiFileSelect(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                processEkuiFile(files[0]);
            }
        }

        function processEkuiFile(file) {
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                    const rawRows = XLSX.utils.sheet_to_json(firstSheet, { defval: '' });

                    if (!rawRows || rawRows.length === 0) {
                        Swal.fire('File Kosong', 'Tidak ada data pada sheet pertama file Excel.', 'warning');
                        return;
                    }

                    parseAndPreviewEkuiRows(rawRows);
                } catch (err) {
                    console.error(err);
                    Swal.fire('Gagal Membaca File', 'Pastikan file berekstensi .xlsx, .xls, atau .csv yang valid.', 'error');
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function parseAndPreviewEkuiRows(rawRows) {
            parsedEkuiExcelData = [];
            let validCount = 0;
            let errorCount = 0;

            rawRows.forEach((r, idx) => {
                const kodeUtama = String(r['Kode MK Utama'] || r['kode_mk_utama'] || r['Kode Utama'] || r['Kode MK'] || '').trim().toUpperCase();
                const kodeEkui = String(r['Kode MK Ekuivalen'] || r['kode_mk_ekuivalen'] || r['Kode Ekuivalen'] || '').trim().toUpperCase();
                const namaEkui = String(r['Nama MK Ekuivalen'] || r['nama_mk_ekuivalen'] || r['Nama Ekuivalen'] || r['Nama MK'] || '').trim();
                const sksRaw = r['SKS Ekuivalen'] !== undefined ? r['SKS Ekuivalen'] : (r['sks_ekuivalen'] !== undefined ? r['sks_ekuivalen'] : r['SKS']);
                const sksEkui = parseInt(sksRaw, 10) || 0;
                const kurAsal = String(r['Kurikulum Asal'] || r['kurikulum_asal'] || r['Kurikulum'] || 'Kurikulum 2020').trim();
                const ket = String(r['Keterangan'] || r['keterangan'] || '').trim();

                const errors = [];
                if (!kodeUtama) errors.push('Kode MK Utama kosong');
                if (!kodeEkui) errors.push('Kode MK Ekuivalen kosong');
                if (!namaEkui) errors.push('Nama MK Ekuivalen kosong');
                if (sksEkui <= 0 || sksEkui > 10) errors.push('SKS tidak valid (1-10)');

                const isValid = (errors.length === 0);
                if (isValid) validCount++; else errorCount++;

                parsedEkuiExcelData.push({
                    rowNum: idx + 1,
                    kode_mk_utama: kodeUtama,
                    kode_mk_ekuivalen: kodeEkui,
                    nama_mk_ekuivalen: namaEkui,
                    sks_ekuivalen: sksEkui || 2,
                    kurikulum_asal: kurAsal,
                    keterangan: ket,
                    isValid: isValid,
                    errors: errors
                });
            });

            // Update stats
            const totalEl = document.getElementById('statEkuiTotal');
            const validEl = document.getElementById('statEkuiValid');
            const errorEl = document.getElementById('statEkuiError');
            const tbody = document.getElementById('tbodyPreviewEkui');
            const previewContainer = document.getElementById('previewEkuiContainer');
            const btnSave = document.getElementById('btnSaveImportEkui');
            const note = document.getElementById('footerEkuiNote');

            if (totalEl) totalEl.innerText = parsedEkuiExcelData.length;
            if (validEl) validEl.innerText = validCount;
            if (errorEl) errorEl.innerText = errorCount;

            if (previewContainer) previewContainer.classList.remove('hidden');

            if (tbody) {
                tbody.innerHTML = parsedEkuiExcelData.map(row => `
                    <tr class="hover:bg-slate-50 transition ${row.isValid ? '' : 'bg-rose-50/60'}">
                        <td class="p-2.5 text-center font-mono text-slate-400">${row.rowNum}</td>
                        <td class="p-2.5 font-mono font-bold ${row.kode_mk_utama ? 'text-indigo-900' : 'text-rose-600'}">${escapeHtml(row.kode_mk_utama || 'KOSONG')}</td>
                        <td class="p-2.5 font-mono font-bold ${row.kode_mk_ekuivalen ? 'text-indigo-600' : 'text-rose-600'}">${escapeHtml(row.kode_mk_ekuivalen || 'KOSONG')}</td>
                        <td class="p-2.5">
                            <div class="font-medium text-slate-800">${escapeHtml(row.nama_mk_ekuivalen || '-')}</div>
                            ${row.isValid ? '' : `<div class="text-[10px] text-rose-600 font-bold mt-0.5">✕ ${escapeHtml(row.errors.join(', '))}</div>`}
                        </td>
                        <td class="p-2.5 text-center font-mono font-bold">${row.sks_ekuivalen}</td>
                        <td class="p-2.5 text-slate-600 text-[11px]">${escapeHtml(row.kurikulum_asal)}</td>
                        <td class="p-2.5 text-slate-500 text-[11px]">${escapeHtml(row.keterangan || '-')}</td>
                    </tr>
                `).join('');
            }

            if (btnSave) {
                if (validCount > 0) {
                    btnSave.disabled = false;
                    btnSave.innerText = `Simpan (${validCount} Baris Valid) ke Database`;
                } else {
                    btnSave.disabled = true;
                    btnSave.innerText = 'Tidak Ada Data Valid';
                }
            }

            if (note) {
                note.innerText = `${validCount} baris valid siap diimport. ${errorCount > 0 ? `${errorCount} baris tidak lengkap akan dilewati.` : ''}`;
            }
        }

        async function sendImportEkuivalensi() {
            const validRows = parsedEkuiExcelData.filter(r => r.isValid);
            if (validRows.length === 0) {
                Swal.fire('Peringatan', 'Tidak ada data ekuivalensi valid untuk disimpan.', 'warning');
                return;
            }

            const btn = document.getElementById('btnSaveImportEkui');
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Memproses ke database...';
            }

            try {
                const res = await fetch('<?= site_url("prodi/perwalian/import_ekuivalensi_bulk") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ rows: validRows })
                });
                const json = await res.json();
                if (json.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Berhasil!',
                        html: `
                            <div class="text-left text-xs space-y-1 mt-2">
                                <p class="font-bold text-slate-800">${json.message}</p>
                                <p class="text-emerald-700 font-medium">✓ ${json.imported || validRows.length} pemetaan ekuivalensi berhasil dimasukkan/diperbarui.</p>
                            </div>
                        `
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal Import', json.message || 'Terjadi kesalahan saat mengimport data.', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Gagal memproses import data ekuivalensi.', 'error');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = 'Simpan ke Database';
                }
            }
        }

        function downloadTemplateEkuivalensi() {
            const sampleData = [
                {
                    'Kode MK Utama': 'CS101',
                    'Kode MK Ekuivalen': 'IF101',
                    'Nama MK Ekuivalen': 'Algoritma & Pemrograman I',
                    'SKS Ekuivalen': 2,
                    'Kurikulum Asal': 'Kurikulum 2020',
                    'Keterangan': 'MK Pendamping 1 (Kombinasi SKS)'
                },
                {
                    'Kode MK Utama': 'CS101',
                    'Kode MK Ekuivalen': 'IF102',
                    'Nama MK Ekuivalen': 'Praktikum Algoritma & Pemrograman',
                    'SKS Ekuivalen': 1,
                    'Kurikulum Asal': 'Kurikulum 2020',
                    'Keterangan': 'MK Pendamping 2 (Kombinasi SKS)'
                },
                {
                    'Kode MK Utama': 'CS102',
                    'Kode MK Ekuivalen': 'IF201',
                    'Nama MK Ekuivalen': 'Struktur Data & Algoritma',
                    'SKS Ekuivalen': 4,
                    'Kurikulum Asal': 'Kurikulum 2020',
                    'Keterangan': 'Ekuivalensi Penuh'
                },
                {
                    'Kode MK Utama': 'CS201',
                    'Kode MK Ekuivalen': 'IF301',
                    'Nama MK Ekuivalen': 'Pemrograman Berbasis Web',
                    'SKS Ekuivalen': 3,
                    'Kurikulum Asal': 'Kurikulum MBKM',
                    'Keterangan': 'Ekuivalensi MBKM'
                }
            ];

            const ws = XLSX.utils.json_to_sheet(sampleData);
            ws['!cols'] = [
                { wch: 18 }, // Kode MK Utama
                { wch: 20 }, // Kode MK Ekuivalen
                { wch: 38 }, // Nama MK Ekuivalen
                { wch: 14 }, // SKS Ekuivalen
                { wch: 20 }, // Kurikulum Asal
                { wch: 36 }  // Keterangan
            ];

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Template Ekuivalensi MK');
            XLSX.writeFile(wb, 'template_ekuivalensi_mata_kuliah.xlsx');
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

        // --- RESET / BUKA KUNCI KRS MAHASISWA ---
        function confirmResetKRS(id, name, currentStatus) {
            const isApproved = currentStatus === 'Disetujui';
            Swal.fire({
                title: isApproved ? 'Buka Kunci / Reset KRS' : 'Kelola Status KRS',
                html: `
                    <div class="text-left text-xs space-y-3 mt-1">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <span class="text-slate-500 block">Mahasiswa:</span>
                            <span class="font-extrabold text-slate-800 text-sm block">${name}</span>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="text-slate-500">Status Saat Ini:</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${isApproved ? 'bg-emerald-100 text-emerald-800' : (currentStatus === 'Diajukan' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700')}">${currentStatus}</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block font-bold text-slate-700">Pilih Tindakan:</label>
                            <label class="flex items-start gap-2.5 p-3 bg-amber-50/70 border border-amber-200 rounded-xl cursor-pointer hover:bg-amber-100/60 transition">
                                <input type="radio" name="resetMode" value="unlock" checked class="mt-0.5 text-brand-600 focus:ring-brand-500">
                                <div>
                                    <span class="font-bold text-amber-900 block text-xs">Buka Kunci (Kembalikan ke Draft / Revisi)</span>
                                    <span class="text-[11px] text-amber-700 block leading-relaxed mt-0.5">Mata kuliah yang sudah dipilih mahasiswa <strong>tetap aman</strong>. Mahasiswa dapat langsung menambah atau mengurangi MK dan mengunggah ulang bukti KRS.</span>
                                </div>
                            </label>
                            <label class="flex items-start gap-2.5 p-3 bg-rose-50/70 border border-rose-200 rounded-xl cursor-pointer hover:bg-rose-100/60 transition">
                                <input type="radio" name="resetMode" value="total" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                                <div>
                                    <span class="font-bold text-rose-900 block text-xs">Reset Total (Kosongkan Berkas & MK)</span>
                                    <span class="text-[11px] text-rose-700 block leading-relaxed mt-0.5">Menghapus seluruh mata kuliah yang telah dipilih dan berkas KRS. Mahasiswa akan memulai pengisian perwalian dari kosong.</span>
                                </div>
                            </label>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Catatan ke Mahasiswa / Dosen Wali (Opsional):</label>
                            <textarea id="swalResetCatatan" rows="2" placeholder="Contoh: Silakan revisi mata kuliah pilihan atau sesuaikan SKS..." class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Proses Tindakan',
                cancelButtonText: 'Batal',
                focusConfirm: false,
                preConfirm: () => {
                    const selectedMode = document.querySelector('input[name="resetMode"]:checked')?.value || 'unlock';
                    const catatan = document.getElementById('swalResetCatatan')?.value || '';
                    return { action: selectedMode, catatan: catatan };
                }
            }).then(async (result) => {
                if (result.isConfirmed && result.value) {
                    const { action, catatan } = result.value;
                    const fd = new FormData();
                    fd.append('id_perwalian', id);
                    fd.append('action', action);
                    fd.append('catatan', catatan);

                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memperbarui status perwalian...',
                        didOpen: () => Swal.showLoading(),
                        allowOutsideClick: false
                    });

                    try {
                        const res = await fetch('<?= site_url("prodi/perwalian/reset_perwalian") ?>', {
                            method: 'POST',
                            body: fd
                        });
                        const json = await res.json();
                        if (json.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: json.message,
                                timer: 1800,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Gagal', json.message, 'error');
                        }
                    } catch (err) {
                        Swal.fire('Error', 'Terjadi kesalahan sistem saat menghubungi server.', 'error');
                    }
                }
            });
        }

        // --- VALIDASI & IMPORT REKAP KRS KOLEKTIF ---
        function validateKRSClient(r, index, seenKRS) {
            const rowNum = index + 1;
            const errors = [];
            let isDuplicate = false;
            let duplicateType = '';

            let rawNim   = String(r['NIM'] || r['nim'] || r['nim_mahasiswa'] || r['Nim'] || '').trim();
            let rawKode  = String(r['Kode MK'] || r['kode_mk'] || r['Kode'] || r['KODE'] || r['Kode Matakuliah'] || '').trim().toUpperCase();
            let rawTahun = String(r['Tahun Ajaran'] || r['tahun_ajaran'] || r['Tahun'] || '<?= htmlspecialchars($tahun_ajaran) ?>').trim();
            let rawSmt   = String(r['Semester'] || r['semester'] || r['Smt'] || '<?= htmlspecialchars($semester) ?>').trim();

            // 1. Validasi NIM
            if (!rawNim) {
                errors.push('NIM wajib diisi');
            } else {
                if (!/^[0-9A-Za-z]{6,25}$/.test(rawNim) || rawNim.includes(' ')) {
                    errors.push(`Format NIM tidak valid ('${rawNim}'). Kemungkinan kolom tertukar dengan Nama`);
                }
            }

            // 2. Validasi Kode MK
            if (!rawKode) {
                errors.push('Kode MK wajib diisi');
            } else {
                if (rawKode.length > 25 || rawKode.split(/\s+/).length >= 2) {
                    errors.push(`Format Kode MK tidak valid ('${rawKode}'). Kemungkinan kolom tertukar dengan Nama MK`);
                }
            }

            // 3. Validasi Tahun Ajaran
            if (rawTahun && !/^\d{4}\/\d{4}$/.test(rawTahun)) {
                errors.push(`Tahun Ajaran harus format YYYY/YYYY (misal 2024/2025), bukan '${rawTahun}'`);
            }

            // 4. Validasi Semester
            const normSmt = rawSmt.charAt(0).toUpperCase() + rawSmt.slice(1).toLowerCase();
            if (rawSmt && !['Ganjil', 'Genap'].includes(normSmt)) {
                errors.push(`Semester harus 'Ganjil' atau 'Genap', bukan '${rawSmt}'`);
            }

            // 5. Cek Duplikat di File
            if (errors.length === 0) {
                const key = `${rawNim}_${rawKode}_${rawTahun}_${normSmt}`;
                if (seenKRS[key]) {
                    isDuplicate = true;
                    duplicateType = `Duplikat di File: NIM ${rawNim} & Kode ${rawKode} sudah ada di baris ${seenKRS[key]}`;
                } else {
                    seenKRS[key] = rowNum;
                }
            }

            let status = 'valid';
            if (errors.length > 0) status = 'error';
            else if (isDuplicate) status = 'duplicate';

            return {
                rowNum: rowNum,
                nim: rawNim,
                kode_mk: rawKode,
                tahun_ajaran: rawTahun,
                semester: ['Ganjil', 'Genap'].includes(normSmt) ? normSmt : 'Ganjil',
                status: status,
                errors: errors,
                duplicateInfo: duplicateType,
            };
        }

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
                const json = XLSX.utils.sheet_to_json(firstSheet, { defval: '' });

                if (!json || json.length === 0) {
                    Swal.fire('File Kosong', 'Tidak ada data dalam file rekap KRS.', 'warning');
                    return;
                }

                const seenKRS = {};
                parsedKRSData = json.map((r, i) => validateKRSClient(r, i, seenKRS));

                updateKRSStats();
                renderKRSPreviewTable();
                document.getElementById('krsPreviewContainer').classList.remove('hidden');
            };
            reader.readAsArrayBuffer(file);
        }

        function updateKRSStats() {
            const total = parsedKRSData.length;
            const validCount = parsedKRSData.filter(r => r.status === 'valid').length;
            const dupCount = parsedKRSData.filter(r => r.status === 'duplicate').length;
            const errCount = parsedKRSData.filter(r => r.status === 'error').length;

            document.getElementById('statKRSTotal').innerText = total;
            document.getElementById('statKRSValid').innerText = validCount;
            document.getElementById('statKRSDuplicate').innerText = dupCount;
            document.getElementById('statKRSError').innerText = errCount;

            document.getElementById('countTabKRS-all').innerText = total;
            document.getElementById('countTabKRS-valid').innerText = validCount;
            document.getElementById('countTabKRS-duplicate').innerText = dupCount;
            document.getElementById('countTabKRS-error').innerText = errCount;

            const alertDup = document.getElementById('alertKRSDuplicate');
            if (dupCount > 0) alertDup.classList.remove('hidden');
            else alertDup.classList.add('hidden');

            const alertErr = document.getElementById('alertKRSError');
            if (errCount > 0) alertErr.classList.remove('hidden');
            else alertErr.classList.add('hidden');
        }

        function filterKRSPreview(filterType) {
            krsPreviewFilter = filterType;
            ['all', 'valid', 'duplicate', 'error'].forEach(t => {
                const btn = document.getElementById('tabKRSFilter-' + t);
                if (t === filterType) {
                    btn.className = 'px-3 py-1 font-bold rounded-lg bg-brand-500 text-white transition';
                } else {
                    btn.className = 'px-3 py-1 font-bold rounded-lg text-slate-600 hover:bg-slate-100 transition';
                }
            });
            renderKRSPreviewTable();
        }

        function renderKRSPreviewTable() {
            const tbody = document.getElementById('krsPreviewTbody');
            const skipDup = document.getElementById('chkSkipKRSDuplicate')?.checked ?? true;

            let filtered = parsedKRSData;
            if (krsPreviewFilter === 'valid') {
                filtered = parsedKRSData.filter(r => r.status === 'valid');
            } else if (krsPreviewFilter === 'duplicate') {
                filtered = parsedKRSData.filter(r => r.status === 'duplicate');
            } else if (krsPreviewFilter === 'error') {
                filtered = parsedKRSData.filter(r => r.status === 'error');
            }

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="p-6 text-center text-slate-400 italic">Tidak ada baris pada filter ini.</td></tr>`;
            } else {
                tbody.innerHTML = filtered.slice(0, 100).map(r => {
                    let statusBadge = '';
                    let rowBg = '';
                    let note = '';

                    if (r.status === 'valid') {
                        statusBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">✓ Valid</span>`;
                        note = `<span class="text-emerald-700 font-medium">Format sesuai</span>`;
                    } else if (r.status === 'duplicate') {
                        statusBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">⚠️ Duplikat</span>`;
                        rowBg = 'bg-amber-50/40';
                        note = `<span class="text-amber-800 font-semibold">${r.duplicateInfo}</span>`;
                    } else {
                        statusBadge = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">✕ Error</span>`;
                        rowBg = 'bg-rose-50/50';
                        note = `<span class="text-rose-700 font-bold">${r.errors.join('<br>')}</span>`;
                    }

                    return `
                        <tr class="${rowBg}">
                            <td class="p-2.5 text-center text-slate-400 font-mono text-[11px]">${r.rowNum}</td>
                            <td class="p-2.5">${statusBadge}</td>
                            <td class="p-2.5 font-mono font-bold ${r.status === 'error' && r.errors.some(e=>e.includes('NIM')) ? 'text-rose-600 bg-rose-100/50 px-1 rounded' : 'text-slate-900'}">${r.nim}</td>
                            <td class="p-2.5 font-mono font-bold ${r.status === 'error' && r.errors.some(e=>e.includes('Kode')) ? 'text-rose-600 bg-rose-100/50 px-1 rounded' : 'text-brand-700'}">${r.kode_mk}</td>
                            <td class="p-2.5 text-slate-600">${r.tahun_ajaran}</td>
                            <td class="p-2.5 text-slate-600">${r.semester}</td>
                            <td class="p-2.5 text-[11px] leading-tight">${note}</td>
                        </tr>
                    `;
                }).join('');
            }

            const readyRows = parsedKRSData.filter(r => r.status === 'valid' || (!skipDup && r.status === 'duplicate'));
            const btnSubmit = document.getElementById('btnSubmitKRS');

            if (readyRows.length > 0) {
                btnSubmit.disabled = false;
                btnSubmit.innerText = `Simpan (${readyRows.length} Entri KRS) ke Database`;
            } else {
                btnSubmit.disabled = true;
                btnSubmit.innerText = 'Tidak Ada Data Valid';
            }
        }

        async function submitImportKRS() {
            const skipDup = document.getElementById('chkSkipKRSDuplicate')?.checked ?? true;
            const readyRows = parsedKRSData.filter(r => r.status === 'valid' || (!skipDup && r.status === 'duplicate'));
            if (readyRows.length === 0) return;

            const btn = document.getElementById('btnSubmitKRS');
            btn.disabled = true;
            btn.innerText = 'Memproses ke database...';

            try {
                const res = await fetch('<?= site_url("prodi/perwalian/import_krs_kolektif") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        krs_rows: readyRows,
                        tahun_ajaran: '<?= htmlspecialchars($tahun_ajaran) ?>',
                        semester: '<?= htmlspecialchars($semester) ?>'
                    })
                });
                const json = await res.json();
                if (json.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: `
                            <div class="text-left text-xs space-y-1 mt-2">
                                <p class="font-bold text-slate-800">${json.message}</p>
                                ${json.skipped_duplicate > 0 ? `<p class="text-amber-700 font-medium">⚠️ ${json.skipped_duplicate} data duplikat dilewati.</p>` : ''}
                                ${json.skipped_invalid > 0 ? `<p class="text-rose-700 font-medium">✕ ${json.skipped_invalid} baris tidak valid dilewati.</p>` : ''}
                            </div>
                        `
                    }).then(() => {
                        switchTab('monitoring');
                        location.reload();
                    });
                } else {
                    Swal.fire('Peringatan Import', json.message, json.status === 'warning' ? 'warning' : 'error');
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
                { 'NIM': '1301210001', 'Kode MK': 'CS101', 'Tahun Ajaran': '<?= htmlspecialchars($tahun_ajaran) ?>', 'Semester': '<?= htmlspecialchars($semester) ?>' },
                { 'NIM': '1301210001', 'Kode MK': 'CS102', 'Tahun Ajaran': '<?= htmlspecialchars($tahun_ajaran) ?>', 'Semester': '<?= htmlspecialchars($semester) ?>' },
                { 'NIM': '1301210002', 'Kode MK': 'CS201', 'Tahun Ajaran': '<?= htmlspecialchars($tahun_ajaran) ?>', 'Semester': '<?= htmlspecialchars($semester) ?>' },
                { 'NIM': '1301210003', 'Kode MK': 'CS305', 'Tahun Ajaran': '<?= htmlspecialchars($tahun_ajaran) ?>', 'Semester': '<?= htmlspecialchars($semester) ?>' },
            ];
            const ws = XLSX.utils.json_to_sheet(sampleData);
            ws['!cols'] = [
                { wch: 18 }, // NIM
                { wch: 16 }, // Kode MK
                { wch: 18 }, // Tahun Ajaran
                { wch: 14 }  // Semester
            ];
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
            setupDropzone(document.getElementById('ekuiDropzone'), document.getElementById('fileEkuiInput'), processEkuiFile);
        });
    </script>
</body>
</html>
