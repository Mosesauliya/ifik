<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK Telkom University</title>
    
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

        .glass-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(234, 88, 12, 0.15);
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.05);
            border-radius: 1rem;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-16">

    <!-- Auto Role-Aware Sidebar Component -->
    <?php 
    $roleId = (int)$this->session->userdata('role_id');
    $this->load->view('components/curved_sidebar');

    $roleLabel = ($roleId === 22) ? 'Super Admin' : (($roleId === 1) ? 'Panel Admin' : 'Administrator');
    $roleSub   = ($roleId === 22 || $roleId === 1) ? 'Pusat Kontrol Super Admin' : 'Kelola Master Sistem';
    $backUrl   = ($roleId === 22) ? site_url('import-email') : site_url('admin');
    $badgeText = ($roleId === 22) ? 'Super Admin' : 'Admin Panel';
    ?>

    <div id="mainPageContent" class="page-wrapper-for-sidebar min-h-screen flex flex-col">
        <!-- Top Sticky Header -->
        <header class="glass-header sticky top-0 z-30 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="<?= $backUrl; ?>" class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 flex items-center justify-center hover:bg-brand-600 hover:text-white transition-all shadow-sm">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide uppercase bg-orange-100 text-brand-700 border border-orange-200">
                                <?= $badgeText; ?>
                            </span>
                            <span class="text-xs text-slate-400">•</span>
                            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Master Data</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <span>Dinamis Kelompok Keahlian (KK)</span>
                            <span class="text-brand-600 font-mono text-base font-bold bg-brand-50 px-2 py-0.5 rounded-lg border border-brand-200">IK Labs</span>
                        </h1>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button onclick="openModalTambah()" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-brand-600 text-white font-semibold text-sm shadow-md hover:from-orange-600 hover:to-brand-700 transition-all transform active:scale-95 space-x-2">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Tambah KK Baru</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 flex-1 w-full">
            
            <!-- Flash Message Alerts -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center space-x-3 text-emerald-800 shadow-sm animate-fade-in">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <div class="text-sm font-semibold flex-1"><?= $this->session->flashdata('success'); ?></div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center space-x-3 text-rose-800 shadow-sm animate-fade-in">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                    <div class="text-sm font-semibold flex-1"><?= $this->session->flashdata('error'); ?></div>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>

            <!-- Metrics Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Total KK -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-brand-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Kelompok Keahlian</p>
                        <h3 class="text-2xl font-extrabold text-slate-800 mt-1"><?= $stats['total_kk'] ?? 0; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Bidang riset terdaftar</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-orange-100 text-brand-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>

                <!-- KK Aktif -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-emerald-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">KK Aktif</p>
                        <h3 class="text-2xl font-extrabold text-emerald-600 mt-1"><?= $stats['active_kk'] ?? 0; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Bisa dipilih saat onboarding</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>

                <!-- KK Non-aktif -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-slate-400 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">KK Non-Aktif</p>
                        <h3 class="text-2xl font-extrabold text-slate-600 mt-1"><?= $stats['inactive_kk'] ?? 0; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Disembunyikan dari pilihan</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-eye-slash"></i>
                    </div>
                </div>

                <!-- Dosen Terpetakan -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-indigo-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Dosen Terpetakan KK</p>
                        <h3 class="text-2xl font-extrabold text-indigo-600 mt-1"><?= $stats['total_dosen'] ?? 0; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Akun dosen dengan KK</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar Section -->
            <div class="card-custom p-5 mb-8">
                <form method="GET" action="<?= site_url('superadmin/kelompok_keahlian'); ?>" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="flex-1 w-full sm:w-auto relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($search); ?>" placeholder="Cari Kode KK, Nama Kelompok Keahlian, atau Ketua KK..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-sm transition">
                    </div>

                    <div class="flex items-center space-x-3 w-full sm:w-auto">
                        <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <option value="all" <?= ($status_filter === 'all') ? 'selected' : ''; ?>>Semua Status</option>
                            <option value="active" <?= ($status_filter === 'active') ? 'selected' : ''; ?>>Hanya Aktif</option>
                            <option value="inactive" <?= ($status_filter === 'inactive') ? 'selected' : ''; ?>>Hanya Non-Aktif</option>
                        </select>

                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-sm hover:bg-slate-800 transition">
                            Filter
                        </button>

                        <?php if (!empty($search) || $status_filter !== 'all'): ?>
                            <a href="<?= site_url('superadmin/kelompok_keahlian'); ?>" class="px-3 py-2.5 rounded-xl bg-slate-100 text-slate-600 hover:text-slate-900 text-sm font-medium transition" title="Reset Filter">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="card-custom overflow-hidden mb-12">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-list-check text-brand-600"></i>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">Daftar Master Kelompok Keahlian (KK)</h2>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-200 text-slate-700">
                        <?= count($kk_list); ?> Data Ditemukan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 text-slate-600 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                                <th class="py-3.5 px-4 text-center w-14">#</th>
                                <th class="py-3.5 px-4 w-32">Kode KK</th>
                                <th class="py-3.5 px-4">Nama Kelompok Keahlian</th>
                                <th class="py-3.5 px-4">Ketua KK</th>
                                <th class="py-3.5 px-4 text-center">Anggota Terhubung</th>
                                <th class="py-3.5 px-4 text-center w-28">Status</th>
                                <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if (empty($kk_list)): ?>
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                                            <i class="fa-solid fa-folder-open"></i>
                                        </div>
                                        <p class="font-semibold text-slate-600">Tidak ada data Kelompok Keahlian</p>
                                        <p class="text-xs text-slate-400 mt-1">Gunakan tombol 'Tambah KK Baru' untuk membuat kelompok keahlian pertama.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($kk_list as $idx => $kk): ?>
                                    <tr class="hover:bg-orange-50/40 transition group">
                                        <!-- No / Urutan -->
                                        <td class="py-4 px-4 text-center font-mono text-xs text-slate-400">
                                            <span class="font-bold text-slate-600"><?= $kk['urutan'] ?? ($idx + 1); ?></span>
                                        </td>

                                        <!-- Kode KK -->
                                        <td class="py-4 px-4">
                                            <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-orange-100 text-brand-700 font-mono font-bold text-xs border border-orange-200 shadow-sm">
                                                <i class="fa-solid fa-hashtag text-[10px] text-brand-500"></i>
                                                <span><?= htmlspecialchars($kk['kode_kk']); ?></span>
                                            </div>
                                        </td>

                                        <!-- Nama KK & Deskripsi -->
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-slate-900 group-hover:text-brand-600 transition">
                                                <?= htmlspecialchars($kk['nama_kk']); ?>
                                            </div>
                                            <?php if (!empty($kk['deskripsi'])): ?>
                                                <div class="text-xs text-slate-500 mt-1 line-clamp-2 max-w-md">
                                                    <?= htmlspecialchars($kk['deskripsi']); ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-xs text-slate-400 italic mt-0.5">Belum ada deskripsi profil riset.</div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Ketua KK -->
                                        <td class="py-4 px-4">
                                            <?php if (!empty($kk['ketua_kk'])): ?>
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                                        <?= substr($kk['ketua_kk'], 0, 2); ?>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-slate-800 text-xs">
                                                            <?= htmlspecialchars($kk['ketua_kk']); ?>
                                                        </div>
                                                        <?php if (!empty($kk['nip_ketua'])): ?>
                                                            <div class="text-[11px] font-mono text-slate-400">
                                                                NIP: <?= htmlspecialchars($kk['nip_ketua']); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-400 italic bg-slate-100 px-2.5 py-1 rounded-md">
                                                    Belum ditentukan
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Anggota Terhubung -->
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button onclick="viewMembers(<?= $kk['id']; ?>)" class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold border border-indigo-200 transition" title="Lihat Dosen">
                                                    <i class="fa-solid fa-chalkboard-user text-[11px]"></i>
                                                    <span><?= $kk['total_dosen']; ?> Dosen</span>
                                                </button>
                                                <button onclick="viewMembers(<?= $kk['id']; ?>)" class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 hover:bg-amber-100 text-xs font-semibold border border-amber-200 transition" title="Lihat Mahasiswa TA">
                                                    <i class="fa-solid fa-graduation-cap text-[11px]"></i>
                                                    <span><?= $kk['total_ta']; ?> TA</span>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Status Toggle -->
                                        <td class="py-4 px-4 text-center">
                                            <button onclick="toggleStatus(<?= $kk['id']; ?>, '<?= htmlspecialchars($kk['kode_kk']); ?>')" class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold transition shadow-sm <?= ($kk['is_active'] == 1) ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 border border-emerald-300' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 border border-slate-300'; ?>">
                                                <i class="fa-solid <?= ($kk['is_active'] == 1) ? 'fa-check-circle text-emerald-500' : 'fa-circle-xmark text-slate-400'; ?>"></i>
                                                <span><?= ($kk['is_active'] == 1) ? 'Aktif' : 'Non-Aktif'; ?></span>
                                            </button>
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button onclick="openModalEdit(<?= $kk['id']; ?>)" class="w-8 h-8 rounded-lg bg-orange-50 hover:bg-brand-600 text-brand-600 hover:text-white border border-orange-200 transition flex items-center justify-center text-xs shadow-sm" title="Edit Kelompok Keahlian">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>

                                                <button onclick="deleteKK(<?= $kk['id']; ?>, '<?= htmlspecialchars($kk['kode_kk']); ?>', <?= $kk['total_dosen']; ?>, <?= $kk['total_ta']; ?>)" class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 transition flex items-center justify-center text-xs shadow-sm" title="Hapus Kelompok Keahlian">
                                                    <i class="fa-solid fa-trash-can"></i>
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
        </main>
    </div>

    <!-- ================= MODAL TAMBAH / EDIT KK ================= -->
    <div id="modalFormKK" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all border border-slate-200 animate-scale-up">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-brand-600 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-folder-tree text-lg"></i>
                    <h3 id="modalTitle" class="text-base font-extrabold tracking-tight">Tambah Kelompok Keahlian</h3>
                </div>
                <button onclick="closeModalForm()" class="text-orange-100 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="formKK" method="POST" action="<?= site_url('superadmin/kelompok_keahlian/simpan'); ?>" class="p-6 space-y-4">
                <input type="hidden" name="id" id="kk_id" value="">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kode KK -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kode KK <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="kode_kk" id="kk_kode" required placeholder="Contoh: DKV, DMI, CPI" maxlength="50" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 uppercase font-mono font-bold text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        <p class="text-[11px] text-slate-400 mt-1">Harus unik & ringkas (huruf kapital).</p>
                    </div>

                    <!-- Urutan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Urutan Tampilan
                        </label>
                        <input type="number" name="urutan" id="kk_urutan" value="1" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition font-mono">
                    </div>
                </div>

                <!-- Nama KK -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Kelompok Keahlian <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_kk" id="kk_nama" required placeholder="Contoh: Creative Design & Visual Communication" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition font-semibold">
                </div>

                <!-- Ketua KK & NIP -->
                <div class="space-y-2 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Ketua Kelompok Keahlian (Opsional)
                        </label>
                        <span class="text-[11px] text-slate-400">Pilih dari dosen atau ketik manual</span>
                    </div>

                    <!-- Dropdown Pemilih Dosen Cepat -->
                    <select id="select_dosen_picker" onchange="applyDosenCandidate(this)" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-700 bg-white focus:outline-none focus:border-brand-500">
                        <option value="">-- Pilih Dosen untuk Ketua KK --</option>
                        <?php if (!empty($dosen_candidates)): ?>
                            <?php foreach ($dosen_candidates as $d): ?>
                                <option value="<?= htmlspecialchars($d['name']); ?>" data-nip="<?= htmlspecialchars($d['nip'] ?? ''); ?>">
                                    <?= htmlspecialchars($d['name']); ?> <?= !empty($d['nip']) ? "({$d['nip']})" : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <input type="text" name="ketua_kk" id="kk_ketua" placeholder="Nama Ketua KK" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        <input type="text" name="nip_ketua" id="kk_nip" placeholder="NIP Ketua KK" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs font-mono focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi / Ruang Lingkup Riset
                    </label>
                    <textarea name="deskripsi" id="kk_deskripsi" rows="3" placeholder="Jelaskan fokus keilmuan, topik riset, atau spesialisasi kelompok keahlian ini..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"></textarea>
                </div>

                <!-- Status Aktif Checkbox -->
                <div class="flex items-center space-x-3 pt-2">
                    <input type="checkbox" name="is_active" id="kk_is_active" value="1" checked class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                    <label for="kk_is_active" class="text-xs font-bold text-slate-700 cursor-pointer select-none">
                        Aktifkan Kelompok Keahlian ini (tampil pada formulir onboarding dosen & pendaftaran)
                    </label>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                    <button type="button" onclick="closeModalForm()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-brand-600 text-white font-semibold text-xs shadow-md hover:from-orange-600 hover:to-brand-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL ANGGOTA KK ================= -->
    <div id="modalMembers" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden transform transition-all border border-slate-200">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-users text-amber-400"></i>
                    <div>
                        <h3 id="memberModalTitle" class="text-sm font-bold">Anggota Kelompok Keahlian</h3>
                        <p id="memberModalSub" class="text-xs text-slate-400 font-mono"></p>
                    </div>
                </div>
                <button onclick="closeModalMembers()" class="text-slate-400 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 max-h-[70vh] overflow-y-auto space-y-6">
                <!-- Section Dosen -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-3 flex items-center space-x-1.5">
                        <i class="fa-solid fa-chalkboard-user text-indigo-600"></i>
                        <span>Daftar Dosen Terdaftar</span>
                        <span id="memberCountDosen" class="ml-2 px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 text-[11px] font-bold">0</span>
                    </h4>
                    <div id="memberListDosen" class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden bg-slate-50/50">
                        <div class="p-4 text-center text-xs text-slate-400">Memuat data...</div>
                    </div>
                </div>

                <!-- Section Mahasiswa TA -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-3 flex items-center space-x-1.5">
                        <i class="fa-solid fa-graduation-cap text-amber-600"></i>
                        <span>Pendaftaran Tugas Akhir Terkini</span>
                        <span id="memberCountTA" class="ml-2 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[11px] font-bold">0</span>
                    </h4>
                    <div id="memberListTA" class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden bg-slate-50/50">
                        <div class="p-4 text-center text-xs text-slate-400">Memuat data...</div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button onclick="closeModalMembers()" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-300 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Script Interaction Handler -->
    <script>
        const baseUrl = '<?= site_url(); ?>';

        function openModalTambah() {
            document.getElementById('modalTitle').textContent = 'Tambah Kelompok Keahlian Baru';
            document.getElementById('kk_id').value = '';
            document.getElementById('kk_kode').value = '';
            document.getElementById('kk_kode').readOnly = false;
            document.getElementById('kk_nama').value = '';
            document.getElementById('kk_ketua').value = '';
            document.getElementById('kk_nip').value = '';
            document.getElementById('kk_deskripsi').value = '';
            document.getElementById('kk_urutan').value = '<?= (count($kk_list) + 1); ?>';
            document.getElementById('kk_is_active').checked = true;
            document.getElementById('select_dosen_picker').value = '';

            document.getElementById('modalFormKK').classList.remove('hidden');
        }

        function openModalEdit(id) {
            Swal.fire({
                title: 'Memuat data...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`${baseUrl}superadmin/kelompok_keahlian/get_json/${id}`)
                .then(res => res.json())
                .then(res => {
                    Swal.close();
                    if (!res.status || !res.data) {
                        Swal.fire('Error', res.message || 'Gagal memuat detail data.', 'error');
                        return;
                    }

                    const d = res.data;
                    document.getElementById('modalTitle').textContent = `Edit Kelompok Keahlian [${d.kode_kk}]`;
                    document.getElementById('kk_id').value = d.id;
                    document.getElementById('kk_kode').value = d.kode_kk;
                    document.getElementById('kk_nama').value = d.nama_kk;
                    document.getElementById('kk_ketua').value = d.ketua_kk || '';
                    document.getElementById('kk_nip').value = d.nip_ketua || '';
                    document.getElementById('kk_deskripsi').value = d.deskripsi || '';
                    document.getElementById('kk_urutan').value = d.urutan || 1;
                    document.getElementById('kk_is_active').checked = (parseInt(d.is_active) === 1);
                    document.getElementById('select_dosen_picker').value = '';

                    document.getElementById('modalFormKK').classList.remove('hidden');
                })
                .catch(err => {
                    Swal.close();
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data.', 'error');
                });
        }

        function closeModalForm() {
            document.getElementById('modalFormKK').classList.add('hidden');
        }

        function applyDosenCandidate(selectEl) {
            const opt = selectEl.options[selectEl.selectedIndex];
            if (opt && opt.value) {
                document.getElementById('kk_ketua').value = opt.value;
                document.getElementById('kk_nip').value = opt.getAttribute('data-nip') || '';
            }
        }

        function toggleStatus(id, kode) {
            Swal.fire({
                title: `Ubah Status KK [${kode}]?`,
                text: 'Status aktif mempengaruhi ketersediaan pilihan di formulir onboarding & pendaftaran.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Ubah Status',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                    fetch(`${baseUrl}superadmin/kelompok_keahlian/toggle/${id}`, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message || 'Gagal mengubah status', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
                    });
                }
            });
        }

        function deleteKK(id, kode, dosenCount, taCount) {
            if (dosenCount > 0 || taCount > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Dapat Menghapus!',
                    html: `Kelompok Keahlian <b>[${kode}]</b> masih memiliki <b>${dosenCount} dosen</b> dan <b>${taCount} pendaftaran TA</b> terkait.<br><br>Untuk menjaga integritas data, silakan non-aktifkan status KK tersebut daripada menghapusnya.`
                });
                return;
            }

            Swal.fire({
                title: `Hapus KK [${kode}]?`,
                text: 'Data yang dihapus tidak dapat dipulihkan kembali.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Hapus Permanen',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                    fetch(`${baseUrl}superadmin/kelompok_keahlian/hapus/${id}`, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message || 'Gagal menghapus data.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
                    });
                }
            });
        }

        function viewMembers(id) {
            document.getElementById('memberModalTitle').textContent = 'Memuat data anggota...';
            document.getElementById('memberModalSub').textContent = '';
            document.getElementById('memberListDosen').innerHTML = '<div class="p-4 text-center text-xs text-slate-400">Memuat daftar dosen...</div>';
            document.getElementById('memberListTA').innerHTML = '<div class="p-4 text-center text-xs text-slate-400">Memuat daftar TA...</div>';
            document.getElementById('modalMembers').classList.remove('hidden');

            fetch(`${baseUrl}superadmin/kelompok_keahlian/members/${id}`)
                .then(res => res.json())
                .then(res => {
                    if (!res.status || !res.data) {
                        document.getElementById('memberListDosen').innerHTML = '<div class="p-4 text-center text-xs text-rose-500">Gagal memuat anggota.</div>';
                        return;
                    }

                    const data = res.data;
                    document.getElementById('memberModalTitle').textContent = data.kk.nama_kk;
                    document.getElementById('memberModalSub').textContent = `Kode KK: ${data.kk.kode_kk}`;

                    // Dosen List
                    document.getElementById('memberCountDosen').textContent = data.dosen.length;
                    if (data.dosen.length === 0) {
                        document.getElementById('memberListDosen').innerHTML = '<div class="p-4 text-center text-xs text-slate-400 italic">Belum ada dosen yang terhubung ke KK ini.</div>';
                    } else {
                        let html = '';
                        data.dosen.forEach(d => {
                            html += `
                                <div class="p-3 flex items-center justify-between bg-white hover:bg-slate-50 transition">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                            ${(d.name || 'D').substring(0, 2).toUpperCase()}
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-800">${escapeHtml(d.name)}</div>
                                            <div class="text-[11px] font-mono text-slate-400">${d.nip ? 'NIP: ' + escapeHtml(d.nip) : (d.email || '')}</div>
                                        </div>
                                    </div>
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-600">${escapeHtml(d.kode_dosen || '-')}</span>
                                </div>
                            `;
                        });
                        document.getElementById('memberListDosen').innerHTML = html;
                    }

                    // Mahasiswa TA List
                    document.getElementById('memberCountTA').textContent = data.mahasiswa.length;
                    if (data.mahasiswa.length === 0) {
                        document.getElementById('memberListTA').innerHTML = '<div class="p-4 text-center text-xs text-slate-400 italic">Belum ada pengajuan pendaftaran TA pada KK ini.</div>';
                    } else {
                        let htmlTA = '';
                        data.mahasiswa.forEach(m => {
                            const nama = (m.nama_depan || '') + ' ' + (m.nama_belakang || '');
                            htmlTA += `
                                <div class="p-3 bg-white hover:bg-slate-50 transition">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="text-xs font-bold text-slate-800">${escapeHtml(nama)}</div>
                                        <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">${escapeHtml(m.nim)}</span>
                                    </div>
                                    <div class="text-xs text-slate-500 line-clamp-1 italic">${escapeHtml(m.judul_1 || 'Judul belum diinput')}</div>
                                </div>
                            `;
                        });
                        document.getElementById('memberListTA').innerHTML = htmlTA;
                    }
                })
                .catch(() => {
                    document.getElementById('memberListDosen').innerHTML = '<div class="p-4 text-center text-xs text-rose-500">Terjadi kesalahan koneksi.</div>';
                });
        }

        function closeModalMembers() {
            document.getElementById('modalMembers').classList.add('hidden');
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text.toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>
