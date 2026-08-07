<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - IFIK</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="<?= base_url('assets/css/style.css'); ?>" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-amber-100/80 via-orange-50 to-amber-100/90 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-500 selection:text-white relative">

    <!-- Header Glass Navbar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-gradient-to-tr from-slate-900 to-slate-800 text-white rounded-2xl font-bold text-xl flex items-center justify-center box-3d">
                        W
                    </div>
                    <div>
                        <span class="font-bold text-base text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-orange-600 mt-1 block">Dosen Wali Akademik</span>
                    </div>
                </div>

                <!-- User Profile Pill -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-800 leading-tight">Dosen Wali IFIK</span>
                        <span class="text-[10px] font-semibold text-slate-500">NIP: 19850101001</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-orange-100 border border-orange-200 text-orange-600 flex items-center justify-center font-bold text-base box-3d">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">

        <!-- Welcome Banner & Page Title -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block mb-1">OVERVIEW BIMBINGAN</span>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Dashboard Dosen Wali</h1>
                <p class="text-slate-600 text-xs mt-1 font-normal">Kelola persetujuan pendaftaran Tugas Akhir mahasiswa bimbingan Anda secara praktis.</p>
            </div>
            <div class="px-4 py-2 bg-white/90 rounded-xl border border-orange-200 shadow-xs text-xs font-semibold text-slate-700 flex items-center gap-2 w-fit">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Approval System Active</span>
            </div>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm box-3d">
                    <i class="bi bi-check-lg"></i>
                </div>
                <p class="text-xs font-bold"><?= $this->session->flashdata('success'); ?></p>
            </div>
        <?php endif; ?>

        <!-- Stat Summary Cards Grid (3D Claymorphic) -->
        <?php
            $totalMhs = !empty($list_mahasiswa) ? count($list_mahasiswa) : 1;
            $pendingCount = 0;
            $approvedCount = 0;
            $rejectedCount = 0;

            if(!empty($list_mahasiswa)) {
                foreach($list_mahasiswa as $row) {
                    $st = $row['status_approval_wali'] ?? 'Pending';
                    if($st === 'Approved') $approvedCount++;
                    else if($st === 'Rejected') $rejectedCount++;
                    else $pendingCount++;
                }
            } else {
                $pendingCount = 1; // mock default
            }
        ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Total Bimbingan -->
            <div class="card-3d-warm rounded-2xl p-5 border border-orange-200/60 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1">Total Mahasiswa</span>
                    <span class="text-2xl font-bold text-slate-900 tracking-tight block"><?= $totalMhs; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Bimbingan Akademik</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 text-white flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>

            <!-- Menunggu Approval -->
            <div class="card-3d-warm rounded-2xl p-5 border border-orange-200/60 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block mb-1">Menunggu Approval</span>
                    <span class="text-2xl font-bold text-amber-600 tracking-tight block"><?= $pendingCount; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Perlu Ditolak / Disetujui</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-400 text-white flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>

            <!-- Disetujui (Approved) -->
            <div class="card-3d-warm rounded-2xl p-5 border border-orange-200/60 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block mb-1">Disetujui</span>
                    <span class="text-2xl font-bold text-emerald-600 tracking-tight block"><?= $approvedCount; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Lanjut ke Admin</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>

            <!-- Ditolak / Revisi (Rejected) -->
            <div class="card-3d-warm rounded-2xl p-5 border border-orange-200/60 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 block mb-1">Perlu Revisi</span>
                    <span class="text-2xl font-bold text-rose-600 tracking-tight block"><?= $rejectedCount; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Telah Ditolak</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-rose-500 to-pink-500 text-white flex items-center justify-center text-xl font-bold shrink-0 box-3d">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
            </div>
        </div>

        <!-- Table Container Card (3D Warm) -->
        <div class="card-3d-warm rounded-2xl border border-orange-200/60 shadow-card-clean overflow-hidden">
            
            <!-- Table Header & Search Filter Bar -->
            <div class="p-6 border-b border-orange-200/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5 tracking-tight">
                        <i class="bi bi-people-fill text-orange-500 text-lg"></i> Daftar Mahasiswa Bimbingan
                    </h2>
                    <p class="text-xs text-slate-500 font-normal mt-0.5">Pilih mahasiswa untuk meninjau berkas dan melakukan persetujuan.</p>
                </div>

                <!-- Search & Status Filter Pills -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="inputSearchMhs" placeholder="Cari nama atau NIM..." class="w-full sm:w-56 pl-9 pr-4 py-2 rounded-xl border border-orange-200 bg-white/90 text-xs font-medium focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition">
                    </div>

                    <!-- Status Filter Badges -->
                    <div class="flex items-center gap-1 bg-white/80 p-1 rounded-xl border border-orange-200 text-[11px] font-semibold">
                        <button type="button" class="btn-filter px-3 py-1.5 rounded-lg text-orange-600 bg-orange-100 font-bold transition" data-filter="all">Semua</button>
                        <button type="button" class="btn-filter px-3 py-1.5 rounded-lg text-slate-600 hover:bg-orange-50 transition" data-filter="Pending">Pending</button>
                        <button type="button" class="btn-filter px-3 py-1.5 rounded-lg text-slate-600 hover:bg-orange-50 transition" data-filter="Approved">Approved</button>
                        <button type="button" class="btn-filter px-3 py-1.5 rounded-lg text-slate-600 hover:bg-orange-50 transition" data-filter="Rejected">Rejected</button>
                    </div>
                </div>
            </div>
            
            <!-- Table View -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-orange-100/40 text-slate-600 font-semibold uppercase tracking-wider border-b border-orange-200/60">
                            <th class="py-3.5 px-5 pl-6">NIM</th>
                            <th class="py-3.5 px-5">Nama Mahasiswa</th>
                            <th class="py-3.5 px-5">Usulan Judul TA (Utama)</th>
                            <th class="py-3.5 px-5">Status Approval</th>
                            <th class="py-3.5 px-5">Tahap Saat Ini</th>
                            <th class="py-3.5 px-5 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-100/80 font-medium" id="tableBodyMhs">
                        <?php if(!empty($list_mahasiswa)): ?>
                            <?php foreach($list_mahasiswa as $mhs): ?>
                                <?php 
                                    $st = $mhs['status_approval_wali'] ?? 'Pending';
                                    $badgeStyle = ($st === 'Approved') ? 'bg-emerald-100 text-emerald-700 border-emerald-300' : (($st === 'Rejected') ? 'bg-rose-100 text-rose-700 border-rose-300' : 'bg-amber-100 text-amber-700 border-amber-300');
                                ?>
                                <tr class="hover:bg-orange-50/50 transition-all duration-150 mhs-row" data-status="<?= $st; ?>">
                                    <td class="py-4 px-5 pl-6 font-bold text-slate-900 mhs-nim"><?= $mhs['nim']; ?></td>
                                    <td class="py-4 px-5 font-semibold text-slate-800 mhs-nama"><?= $mhs['nama_depan'] . ' ' . $mhs['nama_belakang']; ?></td>
                                    <td class="py-4 px-5 text-slate-600 max-w-xs truncate"><?= $mhs['judul_1'] ? character_limiter($mhs['judul_1'], 45) : '<span class="text-slate-400 italic">Belum Mendaftar</span>'; ?></td>
                                    <td class="py-4 px-5">
                                        <span class="px-3 py-1 font-semibold text-[11px] rounded-full border shadow-xs inline-block <?= $badgeStyle; ?>"><?= $st; ?></span>
                                    </td>
                                    <td class="py-4 px-5">
                                        <span class="px-3 py-1 font-semibold text-[11px] rounded-full bg-slate-100 text-slate-700 border border-slate-200 shadow-xs inline-block"><?= $mhs['current_stage'] ?? 'Draft'; ?></span>
                                    </td>
                                    <td class="py-4 px-5 pr-6 text-right">
                                        <a href="<?= site_url('dosenwali/detail_mahasiswa/' . $mhs['nim']); ?>" class="btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-4 py-2 rounded-xl text-xs">
                                            <i class="bi bi-search text-xs"></i> Detail & Approval
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback Mock Data -->
                            <tr class="hover:bg-orange-50/50 transition-all duration-150 mhs-row" data-status="Pending">
                                <td class="py-4 px-5 pl-6 font-bold text-slate-900 mhs-nim">1301210001</td>
                                <td class="py-4 px-5 font-semibold text-slate-800 mhs-nama">Rivan Arshavin</td>
                                <td class="py-4 px-5 text-slate-600 max-w-xs truncate">Pengembangan Sistem Informasi IFIK Berbasis Web</td>
                                <td class="py-4 px-5">
                                    <span class="px-3 py-1 font-semibold text-[11px] rounded-full bg-amber-100 text-amber-700 border border-amber-300 shadow-xs inline-block">Pending</span>
                                </td>
                                <td class="py-4 px-5">
                                    <span class="px-3 py-1 font-semibold text-[11px] rounded-full bg-slate-100 text-slate-700 border border-slate-200 shadow-xs inline-block">Dosen Wali</span>
                                </td>
                                <td class="py-4 px-5 pr-6 text-right">
                                    <a href="<?= site_url('dosenwali/detail_mahasiswa/1301210001'); ?>" class="btn-3d-orange inline-flex items-center gap-1.5 text-white font-bold px-4 py-2 rounded-xl text-xs">
                                        <i class="bi bi-search text-xs"></i> Detail & Approval
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white/90 border-t border-orange-100 py-5 text-center text-xs text-slate-400 font-medium mt-12">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <!-- Search & Filter Script -->
    <script>
    const searchInput = document.getElementById('inputSearchMhs');
    const filterButtons = document.querySelectorAll('.btn-filter');
    const rows = document.querySelectorAll('.mhs-row');
    let currentFilter = 'all';

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

        rows.forEach(row => {
            const nim = row.querySelector('.mhs-nim')?.textContent.toLowerCase() || '';
            const nama = row.querySelector('.mhs-nama')?.textContent.toLowerCase() || '';
            const status = row.getAttribute('data-status') || '';

            const matchesSearch = nim.includes(query) || nama.includes(query);
            const matchesFilter = (currentFilter === 'all') || (status.toLowerCase() === currentFilter.toLowerCase());

            if (matchesSearch && matchesFilter) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => {
                b.classList.remove('bg-orange-100', 'text-orange-600', 'font-bold');
                b.classList.add('text-slate-600');
            });
            btn.classList.add('bg-orange-100', 'text-orange-600', 'font-bold');
            btn.classList.remove('text-slate-600');

            currentFilter = btn.getAttribute('data-filter');
            filterTable();
        });
    });
    </script>
</body>
</html>
