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
<body class="bg-[#FAF8F5] text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-500 selection:text-white">

    <!-- Header Glass Navbar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-2xl border-b border-orange-100/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-gradient-to-tr from-slate-900 to-slate-800 text-white rounded-xl font-bold text-xl flex items-center justify-center shadow-md shadow-slate-900/20">
                        W
                    </div>
                    <div>
                        <span class="font-bold text-lg text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-orange-500 mt-1 block">Dosen Wali</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-orange-500 block mb-1">OVERVIEW AKADEMIK</span>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">Dashboard Dosen Wali</h1>
                <p class="text-slate-500 text-sm mt-1">Kelola persetujuan pendaftaran Tugas Akhir mahasiswa bimbingan Anda.</p>
            </div>
            <div class="px-4 py-2 bg-white rounded-2xl border border-orange-100 shadow-xs text-xs font-bold text-slate-700 flex items-center gap-2 w-fit">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span>System Active & Real-time</span>
            </div>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                    <i class="bi bi-check-lg"></i>
                </div>
                <p class="text-sm font-bold"><?= $this->session->flashdata('success'); ?></p>
            </div>
        <?php endif; ?>

        <!-- Table Container -->
        <div class="bg-white rounded-[2rem] border border-orange-100 shadow-card-clean overflow-hidden">
            <div class="p-6 md:p-8 border-b border-orange-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                    <i class="bi bi-people-fill text-orange-500 text-lg"></i> Daftar Mahasiswa Bimbingan
                </h2>
                <span class="text-xs font-bold text-orange-600 bg-orange-50 px-3.5 py-1 rounded-full border border-orange-200">Akademik Active</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-orange-50/40 text-slate-500 font-bold uppercase tracking-wider border-b border-orange-100">
                            <th class="p-4 pl-8">NIM</th>
                            <th class="p-4">Nama Mahasiswa</th>
                            <th class="p-4">Usulan Judul TA (Utama)</th>
                            <th class="p-4">Status Approval Wali</th>
                            <th class="p-4">Tahap Saat Ini</th>
                            <th class="p-4 pr-8 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-orange-100/60 font-medium">
                        <?php if(!empty($list_mahasiswa)): ?>
                            <?php foreach($list_mahasiswa as $mhs): ?>
                                <tr class="hover:bg-orange-50/20 transition-all duration-150">
                                    <td class="p-4 pl-8 font-bold text-slate-900"><?= $mhs['nim']; ?></td>
                                    <td class="p-4 font-semibold text-slate-800"><?= $mhs['nama_depan'] . ' ' . $mhs['nama_belakang']; ?></td>
                                    <td class="p-4 text-slate-600 max-w-xs truncate"><?= $mhs['judul_1'] ? character_limiter($mhs['judul_1'], 45) : '<span class="text-slate-400 italic">Belum Mendaftar</span>'; ?></td>
                                    <td class="p-4">
                                        <?php 
                                            $st = $mhs['status_approval_wali'] ?? 'Pending';
                                            $badge = ($st === 'Approved') ? 'bg-emerald-500 text-white' : (($st === 'Rejected') ? 'bg-rose-500 text-white' : 'bg-orange-500 text-white');
                                        ?>
                                        <span class="px-3 py-1 font-bold text-[11px] rounded-full shadow-xs <?= $badge; ?>"><?= $st; ?></span>
                                    </td>
                                    <td class="p-4"><span class="px-3 py-1 font-bold text-[11px] rounded-full bg-slate-100 text-slate-600 border border-slate-200"><?= $mhs['current_stage'] ?? 'Draft'; ?></span></td>
                                    <td class="p-4 pr-8 text-right">
                                        <a href="<?= site_url('dosenwali/detail_mahasiswa/' . $mhs['nim']); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold px-4 py-2.5 rounded-xl shadow-md shadow-orange-500/20 transition text-xs">
                                            <i class="bi bi-search"></i> Detail & Approval
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="hover:bg-orange-50/20 transition-all duration-150">
                                <td class="p-4 pl-8 font-bold text-slate-900">1301210001</td>
                                <td class="p-4 font-semibold text-slate-800">Rivan Arshavin</td>
                                <td class="p-4 text-slate-600">Pengembangan Sistem Informasi IFIK Berbasis Web</td>
                                <td class="p-4"><span class="px-3 py-1 font-bold text-[11px] rounded-full bg-orange-500 text-white shadow-xs">Pending</span></td>
                                <td class="p-4"><span class="px-3 py-1 font-bold text-[11px] rounded-full bg-slate-100 text-slate-600 border border-slate-200">Dosen Wali</span></td>
                                <td class="p-4 pr-8 text-right">
                                    <a href="<?= site_url('dosenwali/detail_mahasiswa/1301210001'); ?>" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold px-4 py-2.5 rounded-xl shadow-md shadow-orange-500/20 transition text-xs">
                                        <i class="bi bi-search"></i> Detail & Approval
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>
