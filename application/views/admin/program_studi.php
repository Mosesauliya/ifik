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
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(234, 88, 12, 0.15);
        }

        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            border-radius: 1rem;
        }

        .badge-prodi {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-16">

    <!-- Auto Role-Aware Sidebar Component -->
    <?php 
    $roleId = (int)$this->session->userdata('role_id');
    $this->load->view('components/curved_sidebar');

    $roleLabel = ($roleId === 22) ? 'Super Admin' : (($roleId === 1) ? 'Panel Admin' : 'Administrator');
    $backUrl   = ($roleId === 22) ? site_url('import-email') : site_url('admin');
    $badgeText = ($roleId === 22) ? 'Super Admin' : 'Admin Panel';
    ?>

    <!-- Main Page Content Wrapped for Sidebar Offset -->
    <div id="mainPageContent" class="page-wrapper-for-sidebar min-h-screen flex flex-col">
        
        <!-- Top Sticky Header -->
        <header class="glass-header sticky top-0 z-30 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="<?= $backUrl; ?>" class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-brand-600 flex items-center justify-center hover:bg-brand-600 hover:text-white transition-all shadow-xs" title="Kembali">
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
                            <span>Dinamis Program Studi (Prodi)</span>
                            <span class="text-brand-600 font-mono text-base font-bold bg-brand-50 px-2 py-0.5 rounded-lg border border-brand-200">FIK Portal</span>
                        </h1>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button type="button" onclick="openModalTambah()" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-brand-600 text-white font-semibold text-sm shadow-md hover:from-orange-600 hover:to-brand-700 transition-all transform active:scale-95 space-x-2 cursor-pointer">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Tambah Prodi Baru</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 flex-1 w-full space-y-6">
            
            <!-- Flash Message Alerts -->
            <?php if ($this->session->flashdata('success')): ?>
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center space-x-3 text-emerald-800 shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <div class="text-sm font-semibold flex-1"><?= $this->session->flashdata('success'); ?></div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center space-x-3 text-rose-800 shadow-xs">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                    <div class="text-sm font-semibold flex-1"><?= $this->session->flashdata('error'); ?></div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>

            <!-- Metrics Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Total Prodi -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-brand-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Program Studi</p>
                        <h3 class="text-2xl font-extrabold text-slate-800 mt-1"><?= $stats['total']; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Tercatat di sistem</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-orange-100 text-brand-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                </div>

                <!-- Prodi Aktif -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-emerald-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Prodi Aktif</p>
                        <h3 class="text-2xl font-extrabold text-emerald-600 mt-1"><?= $stats['active']; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Tersedia di dropdown</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>

                <!-- Prodi Non-aktif -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-slate-400 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Prodi Non-Aktif</p>
                        <h3 class="text-2xl font-extrabold text-slate-600 mt-1"><?= $stats['nonactive']; ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Disembunyikan dari pilihan</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-eye-slash"></i>
                    </div>
                </div>

                <!-- Mahasiswa Terdata -->
                <div class="card-custom p-5 flex items-center justify-between border-l-4 border-l-indigo-500 hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mahasiswa Terdata</p>
                        <h3 class="text-2xl font-extrabold text-indigo-600 mt-1"><?= number_format($stats['total_mhs']); ?></h3>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Terdaftar di prodi</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Toolbar Section -->
            <div class="card-custom p-5">
                <form method="GET" action="<?= site_url('superadmin/program_studi'); ?>" class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div class="flex-1 w-full sm:w-auto relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($search); ?>" placeholder="Cari Kode Prodi, Nama Program Studi, atau Keterangan..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-sm transition">
                    </div>

                    <div class="flex items-center space-x-3 w-full sm:w-auto">
                        <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                            <option value="all" <?= ($status_filter === 'all') ? 'selected' : ''; ?>>Semua Status</option>
                            <option value="active" <?= ($status_filter === 'active') ? 'selected' : ''; ?>>Hanya Aktif</option>
                            <option value="nonactive" <?= ($status_filter === 'nonactive') ? 'selected' : ''; ?>>Hanya Non-Aktif</option>
                        </select>

                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-sm hover:bg-slate-800 transition">
                            Filter
                        </button>

                        <?php if (!empty($search) || $status_filter !== 'all'): ?>
                            <a href="<?= site_url('superadmin/program_studi'); ?>" class="px-3 py-2.5 rounded-xl bg-slate-100 text-slate-600 hover:text-slate-900 text-sm font-medium transition" title="Reset Filter">
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
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700">Daftar Master Program Studi (Prodi)</h2>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-200 text-slate-700">
                        <?= count($prodi_list); ?> Data Ditemukan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100/70 text-slate-600 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                                <th class="py-3.5 px-4 text-center w-14">#</th>
                                <th class="py-3.5 px-4 w-28">Kode Prodi</th>
                                <th class="py-3.5 px-5">Nama Program Studi</th>
                                <th class="py-3.5 px-4 text-center w-24">Jenjang</th>
                                <th class="py-3.5 px-4">Keterangan</th>
                                <th class="py-3.5 px-4 text-center w-36">Mahasiswa</th>
                                <th class="py-3.5 px-4 text-center w-28">Status</th>
                                <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if (empty($prodi_list)): ?>
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400">
                                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
                                            <i class="fa-solid fa-folder-open"></i>
                                        </div>
                                        <p class="font-semibold text-slate-600">Tidak ada data Program Studi</p>
                                        <p class="text-xs text-slate-400 mt-1">Gunakan tombol 'Tambah Prodi Baru' untuk menambahkan prodi baru.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($prodi_list as $idx => $p): ?>
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-4 px-4 text-center font-mono font-bold text-slate-400">
                                            <?= $p['urutan'] ?: ($idx + 1); ?>
                                        </td>
                                        <td class="py-4 px-4 font-bold">
                                            <span class="badge-prodi px-2.5 py-1 rounded-lg bg-orange-100 text-brand-700 text-xs border border-orange-200">
                                                <?= htmlspecialchars($p['kode_prodi']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-5">
                                            <span class="font-extrabold text-slate-900 block">
                                                <?= htmlspecialchars($p['nama_prodi']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="px-2.5 py-0.5 rounded-md font-bold text-xs <?= ($p['jenjang'] === 'S2') ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700' ?>">
                                                <?= htmlspecialchars($p['jenjang'] ?: 'S1'); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-slate-500 max-w-xs truncate text-xs">
                                            <?= htmlspecialchars($p['keterangan'] ?: '-'); ?>
                                        </td>
                                        <td class="py-4 px-4 text-center font-mono font-bold">
                                            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs">
                                                <i class="fa-solid fa-user-graduate mr-1.5 text-slate-400"></i><?= number_format($p['total_mahasiswa']); ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <button type="button" 
                                                    onclick="toggleStatus(<?= $p['id']; ?>, '<?= htmlspecialchars($p['nama_prodi'], ENT_QUOTES); ?>')"
                                                    class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-bold transition shadow-2xs <?= ($p['is_active'] == 1) ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 border border-emerald-300' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 border border-slate-300' ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?= ($p['is_active'] == 1) ? 'bg-emerald-500' : 'bg-slate-400' ?>"></span>
                                                <span><?= ($p['is_active'] == 1) ? 'Aktif' : 'Non-Aktif'; ?></span>
                                            </button>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="inline-flex items-center space-x-2">
                                                <button type="button" 
                                                        onclick="openModalEdit(<?= $p['id']; ?>)"
                                                        class="w-8 h-8 rounded-lg bg-orange-50 hover:bg-orange-100 text-brand-600 flex items-center justify-center transition border border-orange-200" title="Edit Program Studi">
                                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                                </button>
                                                <button type="button" 
                                                        onclick="hapusProdi(<?= $p['id']; ?>, '<?= htmlspecialchars($p['nama_prodi'], ENT_QUOTES); ?>', <?= (int)$p['total_mahasiswa']; ?>)"
                                                        class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition border border-rose-200" title="Hapus Program Studi">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
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

    <!-- MODAL TAMBAH / EDIT PRODI -->
    <div id="modalProdi" class="modal-overlay fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-100 transform transition-all">
            
            <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-brand-600 text-white flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-graduation-cap text-lg"></i>
                    <div>
                        <h3 id="modalTitle" class="text-base font-bold">Tambah Program Studi</h3>
                        <p class="text-orange-100 text-xs">Lengkapi data master prodi</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="formProdi" onsubmit="submitFormProdi(event)" class="p-6 space-y-4 text-xs font-semibold">
                <input type="hidden" name="id" id="prodi_id">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Kode Prodi <span class="text-rose-500">*</span></label>
                        <input type="text" name="kode_prodi" id="prodi_kode" required placeholder="Contoh: DKV, DI, IF" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold uppercase focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Jenjang <span class="text-rose-500">*</span></label>
                        <select name="jenjang" id="prodi_jenjang" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                            <option value="S1">S1 (Sarjana)</option>
                            <option value="S2">S2 (Magister)</option>
                            <option value="D3">D3 (Diploma)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1">Nama Lengkap Program Studi <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_prodi" id="prodi_nama" required placeholder="Contoh: S1 Desain Komunikasi Visual (DKV)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Urutan Tampilan</label>
                        <input type="number" name="urutan" id="prodi_urutan" value="1" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Status Keaktifan</label>
                        <select name="is_active" id="prodi_is_active" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none">
                            <option value="1">Aktif (Tampil di Dropdown)</option>
                            <option value="0">Non-Aktif (Disembunyikan)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 font-bold mb-1">Keterangan / Catatan (Opsional)</label>
                    <textarea name="keterangan" id="prodi_keterangan" rows="2.5" placeholder="Keterangan singkat mengenai prodi ini..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none resize-none"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-bold transition">Batal</button>
                    <button type="submit" id="btnSubmitProdi" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold shadow-md shadow-brand-600/20 transition flex items-center space-x-2 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Cursor -->
    <?php $this->load->view('partials/custom_cursor'); ?>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        const baseUrl = '<?= site_url(); ?>';

        function openModalTambah() {
            document.getElementById('modalTitle').innerText = 'Tambah Program Studi Baru';
            document.getElementById('formProdi').reset();
            document.getElementById('prodi_id').value = '';
            document.getElementById('prodi_urutan').value = '<?= count($prodi_list) + 1; ?>';
            document.getElementById('modalProdi').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modalProdi').classList.add('hidden');
        }

        function openModalEdit(id) {
            Swal.fire({
                title: 'Memuat Data...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`${baseUrl}superadmin/program_studi/get_json/${id}`)
                .then(r => r.json())
                .then(res => {
                    Swal.close();
                    if (!res.status) {
                        Swal.fire('Error', res.message || 'Gagal memuat data prodi.', 'error');
                        return;
                    }

                    const d = res.data;
                    document.getElementById('modalTitle').innerText = 'Edit Program Studi - ' + d.kode_prodi;
                    document.getElementById('prodi_id').value = d.id;
                    document.getElementById('prodi_kode').value = d.kode_prodi;
                    document.getElementById('prodi_nama').value = d.nama_prodi;
                    document.getElementById('prodi_jenjang').value = d.jenjang || 'S1';
                    document.getElementById('prodi_urutan').value = d.urutan || 1;
                    document.getElementById('prodi_is_active').value = d.is_active;
                    document.getElementById('prodi_keterangan').value = d.keterangan || '';

                    document.getElementById('modalProdi').classList.remove('hidden');
                })
                .catch(err => {
                    Swal.fire('Error', 'Terjadi kesalahan koneksi server.', 'error');
                });
        }

        function submitFormProdi(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitProdi');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Menyimpan...';

            const form = document.getElementById('formProdi');
            const fd   = new FormData(form);

            fetch(`${baseUrl}superadmin/program_studi/simpan`, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = origHtml;

                if (res.status === 'success' || res.success) {
                    closeModal();
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
                    Swal.fire('Perhatian!', res.message || 'Gagal menyimpan data.', 'warning');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
            });
        }

        function toggleStatus(id, nama) {
            Swal.fire({
                title: 'Ubah Status Prodi?',
                text: `Anda akan mengubah status keaktifan untuk "${nama}".`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ea580c',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${baseUrl}superadmin/program_studi/toggle/${id}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.status === 'success' || res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    });
                }
            });
        }

        function hapusProdi(id, nama, mhsCount) {
            if (mhsCount > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Bisa Dihapus!',
                    text: `Terdapat ${mhsCount} mahasiswa yang terhubung dengan prodi "${nama}". Silakan nonaktifkan statusnya saja agar data riwayat mahasiswa tidak rusak.`
                });
                return;
            }

            Swal.fire({
                title: 'Hapus Program Studi?',
                text: `Data prodi "${nama}" akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus Permanen',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${baseUrl}superadmin/program_studi/hapus/${id}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.status === 'success' || res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
