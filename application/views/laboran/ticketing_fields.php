<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pengaturan Dropdown Tiket Dinamis — Panel Laboran'; ?> - IFIK</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-orange-50/20 to-slate-100 min-h-screen text-slate-800 antialiased">

    <!-- Include Curved Sidebar (Panel Laboran) -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Main Content -->
    <main class="min-h-screen p-6 sm:p-8 lg:p-10 max-w-7xl mx-auto pl-16">
        
        <!-- Header & Breadcrumb -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-2">
                <a href="<?= site_url('laboran') ?>" class="hover:text-orange-600 transition-colors">Panel Laboran</a>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Layanan Ticketing</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-orange-600 font-bold">Pengaturan Dropdown Dinamis</span>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/25">
                            <i class="bi bi-menu-app-fill text-xl"></i>
                        </span>
                        Pengaturan Dropdown Tiket Dinamis
                    </h1>
                    <p class="text-sm text-slate-500 mt-1.5 max-w-2xl">
                        Atur pilihan dropdown <b>Unit yang Dituju</b> dan <b>Kategori Kendala</b> di setiap unit secara dinamis. Pilihan yang diatur di sini otomatis muncul di formulir tiket.
                    </p>
                </div>

                <div class="flex items-center gap-2.5">
                    <a href="<?= site_url('laboran/ticketing/input') ?>" target="_blank"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:text-orange-600 hover:border-orange-200 hover:bg-orange-50/50 text-sm font-bold shadow-xs transition-all">
                        <i class="bi bi-box-arrow-up-right text-xs"></i>
                        <span>Cek Form Tiket</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-exclamation-octagon-fill text-lg text-rose-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('error'); ?></div>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-start gap-3 shadow-xs">
                <i class="bi bi-check-circle-fill text-lg text-emerald-500 shrink-0 mt-0.5"></i>
                <div class="flex-1"><?= $this->session->flashdata('success'); ?></div>
            </div>
        <?php endif; ?>

        <!-- Stat Overview Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Unit -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Unit</span>
                    <span class="text-2xl font-extrabold text-slate-800 mt-1 block font-mono"><?= $stats['total_units'] ?? 0; ?></span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Pilihan Unit Tujuan</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl shrink-0">
                    <i class="bi bi-building"></i>
                </div>
            </div>

            <!-- Unit Aktif -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">Unit Aktif</span>
                    <span class="text-2xl font-extrabold text-emerald-600 mt-1 block font-mono"><?= $stats['active_units'] ?? 0; ?></span>
                    <span class="text-[11px] text-emerald-700 mt-0.5 block">Tampil di dropdown</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>

            <!-- Total Kategori -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block">Total Kategori</span>
                    <span class="text-2xl font-extrabold text-blue-600 mt-1 block font-mono"><?= $stats['total_kategori'] ?? 0; ?></span>
                    <span class="text-[11px] text-blue-700 mt-0.5 block">Masalah terdaftar</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                    <i class="bi bi-tags-fill"></i>
                </div>
            </div>

            <!-- Kategori Aktif -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider block">Kategori Aktif</span>
                    <span class="text-2xl font-extrabold text-amber-600 mt-1 block font-mono"><?= $stats['active_kategori'] ?? 0; ?></span>
                    <span class="text-[11px] text-amber-700 mt-0.5 block">Siap dipilih pelapor</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="bi bi-list-check"></i>
                </div>
            </div>
        </div>

        <!-- Info Banner: Panduan Nonaktif vs Hapus & Kategori Lain-lain -->
        <div class="bg-gradient-to-r from-blue-50/90 via-indigo-50/50 to-white border border-blue-200/80 rounded-2xl p-4 sm:p-5 mb-6 shadow-xs">
            <div class="flex flex-col sm:flex-row items-start gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-base shrink-0 shadow-xs mt-0.5">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div class="space-y-1.5 flex-1">
                    <h4 class="font-extrabold text-slate-800 text-xs sm:text-sm">Panduan Fitur: Nonaktif vs Hapus & Ketentuan Kategori "Lain-lain"</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1 text-xs text-slate-600">
                        <div class="p-2.5 rounded-xl bg-white border border-blue-100/80 shadow-2xs">
                            <span class="font-bold text-emerald-700 block mb-0.5 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Fitur Nonaktif
                            </span>
                            <p class="text-[11px] text-slate-500 leading-snug">Menyembunyikan pilihan dari dropdown form pelapor tanpa menghapus data. Riwayat tiket lama tetap utuh & bisa diaktifkan kembali kapan saja.</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white border border-blue-100/80 shadow-2xs">
                            <span class="font-bold text-rose-700 block mb-0.5 flex items-center gap-1.5">
                                <i class="bi bi-trash-fill text-[11px]"></i> Fitur Hapus
                            </span>
                            <p class="text-[11px] text-slate-500 leading-snug">Menghapus pilihan secara permanen dari database sistem jika sudah tidak digunakan lagi.</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white border border-amber-200/80 bg-amber-50/30 shadow-2xs">
                            <span class="font-bold text-amber-800 block mb-0.5 flex items-center gap-1.5">
                                <i class="bi bi-pin-angle-fill text-[11px]"></i> Kategori "Lain-lain"
                            </span>
                            <p class="text-[11px] text-slate-600 leading-snug"><strong>Wajib selalu ada</strong> di setiap unit dan <strong>otomatis selalu di posisi paling bawah</strong>. Opsi ini dilindungi agar tidak terhapus.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/40 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-800">Daftar Pilihan Unit yang Dituju</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Pilihan utama yang akan muncul di dropdown nomor 2 pada formulir tiket.</p>
                    </div>
                    <button type="button" onclick="bukaModalUnit()" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md shadow-orange-600/20 transition-all cursor-pointer">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Tambah Unit Baru</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">
                                <th class="py-3.5 px-6 text-center w-16">No</th>
                                <th class="py-3.5 px-6">Nama Unit Tujuan</th>
                                <th class="py-3.5 px-6">Deskripsi / Keterangan</th>
                                <th class="py-3.5 px-6 text-center">Jumlah Kategori</th>
                                <th class="py-3.5 px-6 text-center">Status</th>
                                <th class="py-3.5 px-6 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if (empty($units)): ?>
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <p class="font-bold text-slate-700">Belum ada Unit Tujuan terdaftar.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($units as $u): ?>
                                    <tr class="hover:bg-slate-50/70 transition-colors <?= $u['is_active'] ? '' : 'opacity-60 bg-slate-50/30' ?>">
                                        <td class="py-4 px-6 text-center font-mono font-bold text-slate-600">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 text-xs">
                                                <?= (int)$u['sort_order']; ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="font-bold text-slate-800 block text-sm"><?= htmlspecialchars($u['nama_unit']); ?></span>
                                        </td>
                                        <td class="py-4 px-6 text-xs text-slate-500 max-w-sm">
                                            <?= htmlspecialchars($u['deskripsi'] ?: '-'); ?>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <button type="button" onclick="toggleAccordionUnit(<?= $u['id']; ?>)" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 text-orange-700 border border-orange-200 text-xs font-bold hover:bg-orange-100 transition-all cursor-pointer">
                                                <i class="bi bi-list-nested text-[11px]"></i>
                                                <span>Kelola <?= count($u['kategori'] ?? []); ?> Kategori</span>
                                                <i class="bi bi-chevron-down text-[10px]" id="chevron_unit_<?= $u['id']; ?>"></i>
                                            </button>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <a href="<?= site_url('laboran/ticketing/unit_toggle/' . $u['id']); ?>"
                                               title="Klik untuk mengubah status aktif"
                                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border transition-all <?= $u['is_active'] ? 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 border-slate-300 text-slate-500 hover:bg-slate-200' ?>">
                                                <span class="w-2 h-2 rounded-full <?= $u['is_active'] ? 'bg-emerald-500' : 'bg-slate-400' ?>"></span>
                                                <?= $u['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                            </a>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <div class="flex items-center justify-center">

                                                <!-- Delete -->
                                                <button type="button"
                                                        onclick="confirmHapusUnit(<?= $u['id']; ?>, '<?= htmlspecialchars(addslashes($u['nama_unit'])); ?>')"
                                                        class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                                                        title="Hapus Unit">
                                                    <i class="bi bi-trash-fill text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Expandable Sub-Row: Kelola Kategori Dropdown Unit Ini Secara Langsung -->
                                    <tr id="unit_kategori_accordion_<?= $u['id']; ?>" class="bg-orange-50/20 border-b border-orange-100/60 hidden">
                                        <td colspan="6" class="p-5 sm:p-6">
                                            <div class="bg-white rounded-2xl border border-orange-200/80 p-5 sm:p-6 shadow-xs">
                                                <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-slate-100">
                                                    <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-sm font-bold shrink-0">
                                                        <i class="bi bi-tag-fill"></i>
                                                    </span>
                                                    <div>
                                                        <h4 class="text-sm font-extrabold text-slate-800">
                                                            Pilihan Kategori Kendala: <span class="text-orange-600"><?= htmlspecialchars($u['nama_unit']); ?></span>
                                                        </h4>
                                                        <p class="text-[11px] text-slate-400 mt-0.5">Daftar ini yang otomatis muncul di dropdown nomor 3 pada formulir tiket saat pelapor memilih unit ini.</p>
                                                    </div>
                                                </div>

                                                <!-- Daftar Kategori -->
                                                <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden mb-4 bg-white">
                                                    <?php if (empty($u['kategori'])): ?>
                                                        <div class="p-6 text-center text-xs text-slate-400 font-medium">
                                                            Belum ada pilihan kategori kendala untuk unit ini. Tambahkan sekarang di bawah ini!
                                                        </div>
                                                    <?php else: ?>
                                                        <?php foreach ($u['kategori'] as $kIdx => $kat): 
                                                            $isLainLain = (stripos($kat['nama_kategori'], 'Lain-lain') === 0 || stripos($kat['nama_kategori'], 'Lainnya') === 0);
                                                        ?>
                                                            <div class="flex items-center justify-between p-3 sm:px-4 hover:bg-orange-50/30 transition-colors <?= $kat['is_active'] ? '' : 'opacity-60 bg-slate-50/50' ?>">
                                                                <div class="flex items-center gap-3">
                                                                    <span class="w-6 text-center text-xs font-mono font-bold text-slate-400"><?= $kIdx + 1; ?>.</span>
                                                                    <span class="text-xs font-bold text-slate-800"><?= htmlspecialchars($kat['nama_kategori']); ?></span>
                                                                    <?php if ($isLainLain): ?>
                                                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[10px] font-semibold border border-slate-200">
                                                                            Default
                                                                        </span>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($kat['deskripsi'])): ?>
                                                                        <span class="text-[11px] text-slate-400 hidden md:inline">- <?= htmlspecialchars($kat['deskripsi']); ?></span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="flex items-center gap-2">
                                                                    <!-- Toggle Aktif / Nonaktif -->
                                                                    <a href="<?= site_url('laboran/ticketing/kategori_toggle/' . $kat['id']); ?>" 
                                                                       title="<?= $kat['is_active'] ? 'Klik untuk Nonaktifkan (sembunyikan dari form pelapor tanpa menghapus data)' : 'Klik untuk Aktifkan kembali'; ?>"
                                                                       class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border transition-all <?= $kat['is_active'] ? 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 border-slate-300 text-slate-500 hover:bg-slate-200' ?>">
                                                                        <span class="w-1.5 h-1.5 rounded-full inline-block mr-1 <?= $kat['is_active'] ? 'bg-emerald-500' : 'bg-slate-400' ?>"></span>
                                                                        <?= $kat['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                                                    </a>

                                                                    <!-- Hapus (Proteksi Kategori Wajib) -->
                                                                    <?php if ($isLainLain): ?>
                                                                        <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center cursor-not-allowed border border-slate-200" 
                                                                              title="Kategori Wajib Sistem (Tidak dapat dihapus. Gunakan tombol Nonaktif jika tidak ingin dimunculkan di formulir pelapor)">
                                                                            <i class="bi bi-shield-lock-fill text-[11px] text-amber-600"></i>
                                                                        </span>
                                                                    <?php else: ?>
                                                                        <button type="button"
                                                                                title="Hapus Kategori Permanen"
                                                                                onclick="confirmHapusKategori(<?= $kat['id']; ?>, '<?= htmlspecialchars(addslashes($kat['nama_kategori'])); ?>')"
                                                                                class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-colors cursor-pointer">
                                                                            <i class="bi bi-trash-fill text-[11px]"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Quick Add Input Langsung untuk Unit Ini (Tanpa Koma) -->
                                                <form action="<?= site_url('laboran/ticketing/kategori_save'); ?>" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                                    <input type="hidden" name="unit_id" value="<?= $u['id']; ?>">
                                                    <div class="relative flex-1">
                                                        <input type="text" name="nama_kategori" required 
                                                               placeholder="Ketik opsi kendala baru untuk <?= htmlspecialchars($u['nama_unit']); ?>..." 
                                                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-xs font-semibold text-slate-800 placeholder-slate-400 transition-all outline-hidden">
                                                    </div>
                                                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer whitespace-nowrap">
                                                        <i class="bi bi-plus-lg"></i>
                                                        <span>+ Tambah ke Dropdown <?= htmlspecialchars($u['nama_unit']); ?></span>
                                                    </button>
                                                </form>
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

    </main>

    <!-- ==================== MODAL TAMBAH / EDIT UNIT ==================== -->
    <div id="modalUnit" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div onclick="tutupModalUnit()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-orange-50/50 via-amber-50/30 to-transparent flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg font-bold">
                            <i id="unitModalIcon" class="bi bi-building-add"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800" id="unitModalTitle">Tambah Unit Tujuan Baru</h3>
                            <p class="text-xs text-slate-400">Pilihan unit yang akan muncul di dropdown formulir tiket</p>
                        </div>
                    </div>
                    <button type="button" onclick="tutupModalUnit()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form action="<?= site_url('laboran/ticketing/unit_save'); ?>" method="POST" class="p-6 space-y-4">
                    <input type="hidden" name="id" id="unit_id" value="0">

                    <?php if (!empty($available_roles)): ?>
                    <div id="role_template_box" class="p-3.5 bg-gradient-to-r from-orange-50 to-amber-50/50 rounded-2xl border border-orange-200/80">
                        <label for="select_role_template" class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-orange-950 font-bold">
                                <i class="bi bi-person-badge-fill text-orange-600"></i>
                                Pilih dari Role Sistem (Rekomendasi)
                            </span>
                            <span class="text-[10px] text-orange-700 bg-orange-100 px-2 py-0.5 rounded-full font-semibold">Belum terdaftar</span>
                        </label>
                        <select id="select_role_template" onchange="pilihRoleTemplate(this)"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-orange-300/80 focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 text-xs font-semibold text-slate-700 transition-all outline-hidden cursor-pointer shadow-xs">
                            <option value="">-- Pilih Role yang belum ada di ticketing (atau ketik manual) --</option>
                            <?php foreach ($available_roles as $ar): ?>
                                <option value="<?= htmlspecialchars($ar['role']); ?>" data-id="<?= $ar['id']; ?>">
                                    Role: <?= htmlspecialchars($ar['role']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1.5 flex items-center gap-1">
                            <i class="bi bi-info-circle text-orange-500"></i>
                            <span>Memilih role akan otomatis mengisi Nama Unit &amp; Deskripsi di bawah.</span>
                        </p>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label for="modal_nama_unit" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Unit Tujuan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_unit" id="modal_nama_unit" required
                               placeholder="Contoh: Perpustakaan & Pusat Sumber Belajar"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden">
                    </div>

                    <div>
                        <label for="modal_unit_deskripsi" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Deskripsi / Keterangan <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" name="deskripsi" id="modal_unit_deskripsi"
                               placeholder="Contoh: Layanan peminjaman buku, bebas pustaka, dan jurnal"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-medium text-slate-800 transition-all outline-hidden">
                    </div>

                    <div>
                        <label for="modal_unit_sort_order" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nomor Urutan Tampilan
                        </label>
                        <input type="number" name="sort_order" id="modal_unit_sort_order" value="1" min="1" max="999"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-sm font-semibold text-slate-800 transition-all outline-hidden">
                    </div>

                    <div id="unit_active_container" class="hidden">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition-all">
                            <input type="checkbox" name="is_active" id="modal_unit_is_active" value="1" checked
                                   class="w-4 h-4 text-emerald-600 rounded-md border-slate-300 focus:ring-emerald-500">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Status Aktif</span>
                                <span class="text-[10px] text-slate-400 block">Tampilkan unit ini di dropdown tiket</span>
                            </div>
                        </label>
                    </div>


                    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
                        <button type="button" onclick="tutupModalUnit()" 
                                class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs font-bold transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md shadow-orange-600/20 transition-all cursor-pointer">
                            <i class="bi bi-check2-circle mr-1"></i> Simpan Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Handling Modals & Dynamic Accordion -->
    <script>
        // --- Dynamic Row Handlers for Modal Unit (Initial Categories - Tanpa Koma) ---
        function tambahBarisUnitKategori(val = '') {
            const container = document.getElementById('unit_kat_rows_container');
            const currentRows = container.querySelectorAll('.unit-kat-dyn-row').length;
            const newIndex = currentRows + 1;

            const rowDiv = document.createElement('div');
            rowDiv.className = 'flex items-center gap-2 unit-kat-dyn-row';
            rowDiv.innerHTML = `
                <span class="w-6 text-center text-xs font-bold text-slate-400 unit-row-number">${newIndex}.</span>
                <input type="text" name="kategori_items[]" value="${val}"
                       placeholder="Contoh Kategori ${newIndex}..."
                       class="flex-1 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 text-xs font-semibold text-slate-800 transition-all outline-hidden">
                <button type="button" onclick="hapusBarisUnitKategori(this)" 
                        title="Hapus baris ini"
                        class="w-8 h-8 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-700 flex items-center justify-center transition-colors cursor-pointer shrink-0">
                    <i class="bi bi-trash text-xs"></i>
                </button>
            `;
            container.appendChild(rowDiv);
            renumberUnitKatRows();
        }

        function hapusBarisUnitKategori(btn) {
            btn.closest('.unit-kat-dyn-row').remove();
            renumberUnitKatRows();
        }

        function renumberUnitKatRows() {
            const rows = document.querySelectorAll('#unit_kat_rows_container .unit-kat-dyn-row');
            rows.forEach((r, idx) => {
                const numSpan = r.querySelector('.unit-row-number');
                if (numSpan) numSpan.innerText = (idx + 1) + '.';
            });
        }

        // --- Modal Unit Handling ---
        function bukaModalUnit() {
            document.getElementById('modalUnit').classList.remove('hidden');
            document.getElementById('unitModalTitle').innerText = 'Tambah Unit Tujuan Baru';
            document.getElementById('unit_id').value = '0';
            document.getElementById('modal_nama_unit').value = '';
            document.getElementById('modal_unit_deskripsi').value = '';
            document.getElementById('modal_unit_sort_order').value = '<?= count($units) + 1; ?>';
            document.getElementById('unit_active_container').classList.add('hidden');
            
            const selRole = document.getElementById('select_role_template');
            if (selRole) selRole.selectedIndex = 0;
            const roleBox = document.getElementById('role_template_box');
            if (roleBox) roleBox.classList.remove('hidden');
        }

        function pilihRoleTemplate(selectEl) {
            const role = selectEl.value;
            if (!role) return;
            const namaInput = document.getElementById('modal_nama_unit');
            const deskripsiInput = document.getElementById('modal_unit_deskripsi');
            if (namaInput) {
                namaInput.value = role;
                namaInput.focus();
            }
            if (deskripsiInput) {
                deskripsiInput.value = 'Layanan dan penanganan kendala seputar ' + role;
            }
        }

        function tutupModalUnit() {
            document.getElementById('modalUnit').classList.add('hidden');
        }

        function toggleAccordionUnit(unitId) {
            const el = document.getElementById('unit_kategori_accordion_' + unitId);
            const icon = document.getElementById('chevron_unit_' + unitId);
            if (el) {
                el.classList.toggle('hidden');
                if (icon) {
                    icon.classList.toggle('rotate-180');
                }
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                tutupModalUnit();
            }
        });

        // --- SweetAlert2 Konfirmasi Hapus ---
        function confirmHapusUnit(id, namaUnit) {
            Swal.fire({
                title: 'Hapus Unit Ini?',
                html: `Apakah Anda yakin ingin menghapus unit <b>"${namaUnit}"</b>?<br><small class="text-rose-600 font-semibold">Perhatian: Seluruh pilihan kategori di bawah unit ini juga akan ikut terhapus permanen!</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-trash-fill mr-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-4 py-2 font-bold text-xs shadow-md',
                    cancelButton: 'rounded-xl px-4 py-2 font-bold text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= site_url("laboran/ticketing/unit_delete/"); ?>' + id;
                }
            });
        }

        function confirmHapusKategori(id, namaKategori) {
            Swal.fire({
                title: 'Hapus Kategori?',
                html: `Apakah Anda yakin ingin menghapus pilihan kategori <b>"${namaKategori}"</b> secara permanen?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-trash-fill mr-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-4 py-2 font-bold text-xs shadow-md',
                    cancelButton: 'rounded-xl px-4 py-2 font-bold text-xs'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= site_url("laboran/ticketing/kategori_delete/"); ?>' + id;
                }
            });
        }
    </script>
</body>
</html>
