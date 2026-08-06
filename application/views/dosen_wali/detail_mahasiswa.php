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
                    <div class="w-10 h-10 bg-gradient-to-tr from-slate-900 to-slate-800 text-white rounded-2xl font-bold text-xl flex items-center justify-center shadow-md shadow-slate-900/20">
                        W
                    </div>
                    <span class="font-bold text-lg text-slate-900 tracking-tight">Detail & Approval Mahasiswa</span>
                </div>
                <a href="<?= site_url('dosenwali'); ?>" class="text-xs font-bold text-slate-600 hover:text-orange-600 bg-orange-50 hover:bg-orange-100 border border-orange-200 px-4 py-2.5 rounded-xl transition flex items-center gap-2">
                    <i class="bi bi-arrow-left text-sm"></i> Dashboard
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">

        <?php if($this->session->flashdata('success')): ?>
            <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                    <i class="bi bi-check-lg"></i>
                </div>
                <p class="text-sm font-bold"><?= $this->session->flashdata('success'); ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Sidebar Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[2rem] p-8 border border-orange-100 shadow-card-clean text-center sticky top-24">
                    <div class="w-20 h-20 bg-gradient-to-tr from-orange-500 to-amber-400 text-white rounded-3xl flex items-center justify-center text-3xl font-bold mx-auto mb-4 shadow-md shadow-orange-500/20">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight mb-1"><?= $detail['nama_depan'] ?? 'Mahasiswa'; ?> <?= $detail['nama_belakang'] ?? ''; ?></h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">NIM: <?= $detail['nim'] ?? '1301210001'; ?></p>
                    <span class="px-4 py-1.5 bg-orange-50 text-orange-600 text-xs font-bold rounded-full border border-orange-200"><?= $detail['konsentrasi_dkv'] ?? 'Informatika'; ?></span>
                    
                    <div class="border-t border-orange-100 mt-6 pt-5 text-left">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Alamat & Geodata:</span>
                        <p class="text-xs text-slate-700 font-semibold leading-relaxed flex items-start gap-2.5 bg-orange-50/40 p-4 rounded-2xl border border-orange-100">
                            <i class="bi bi-geo-alt-fill text-orange-500 text-base shrink-0 mt-0.5"></i>
                            <span><?= $detail['alamat'] ?? 'Bandung, Jawa Barat'; ?></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Judul & File PDF -->
                <div class="bg-white rounded-[2rem] p-8 md:p-10 border border-orange-100 shadow-card-clean">
                    <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2.5 border-b border-orange-100 pb-4">
                        <i class="bi bi-journal-text text-orange-500"></i> Berkas Usulan Judul & Persyaratan
                    </h2>

                    <div class="space-y-5 text-xs">
                        <div>
                            <span class="font-bold text-slate-700 block uppercase tracking-wider mb-2">Usulan Judul 1 (Utama):</span>
                            <div class="p-4 bg-orange-50/30 rounded-2xl border border-orange-100 font-bold text-slate-900 text-sm leading-relaxed"><?= $detail['judul_1'] ?? 'Pengembangan Sistem Informasi IFIK Berbasis Web'; ?></div>
                        </div>

                        <div>
                            <span class="font-bold text-slate-700 block uppercase tracking-wider mb-2">Usulan Judul 2 (Alternatif 1):</span>
                            <div class="p-4 bg-orange-50/30 rounded-2xl border border-orange-100 font-semibold text-slate-800"><?= $detail['judul_2'] ?? 'Rancang Bangun Modul Mahasiswa dan Dosen Wali IFIK'; ?></div>
                        </div>

                        <div>
                            <span class="font-bold text-slate-700 block uppercase tracking-wider mb-2">Usulan Judul 3 (Alternatif 2):</span>
                            <div class="p-4 bg-orange-50/30 rounded-2xl border border-orange-100 font-semibold text-slate-800"><?= $detail['judul_3'] ?? 'Implementasi Workflow Approval Pendaftaran Tugas Akhir'; ?></div>
                        </div>

                        <div>
                            <span class="font-bold text-slate-700 block uppercase tracking-wider mb-2">Judul (Bahasa Inggris):</span>
                            <div class="p-4 bg-orange-50/30 rounded-2xl border border-orange-100 font-medium italic text-slate-700"><?= $detail['judul_en'] ?? 'Development of Web-Based IFIK Information System'; ?></div>
                        </div>

                        <div class="pt-4 border-t border-orange-100">
                            <span class="font-bold text-slate-700 block uppercase tracking-wider mb-3">Berkas Persyaratan (PDF):</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="p-4 bg-white border border-orange-200/80 rounded-2xl flex items-center justify-between shadow-xs hover:border-orange-400 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </div>
                                        <span class="font-bold text-xs text-slate-900">KSM</span>
                                    </div>
                                    <button class="text-xs bg-orange-500 hover:bg-orange-600 text-white font-bold px-3.5 py-2 rounded-xl shadow-xs transition">Unduh</button>
                                </div>

                                <div class="p-4 bg-white border border-orange-200/80 rounded-2xl flex items-center justify-between shadow-xs hover:border-orange-400 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </div>
                                        <span class="font-bold text-xs text-slate-900">Transkrip Nilai</span>
                                    </div>
                                    <button class="text-xs bg-orange-500 hover:bg-orange-600 text-white font-bold px-3.5 py-2 rounded-xl shadow-xs transition">Unduh</button>
                                </div>

                                <div class="p-4 bg-white border border-orange-200/80 rounded-2xl flex items-center justify-between shadow-xs hover:border-orange-400 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </div>
                                        <span class="font-bold text-xs text-slate-900">Surat Pernyataan</span>
                                    </div>
                                    <button class="text-xs bg-orange-500 hover:bg-orange-600 text-white font-bold px-3.5 py-2 rounded-xl shadow-xs transition">Unduh</button>
                                </div>

                                <div class="p-4 bg-white border border-orange-200/80 rounded-2xl flex items-center justify-between shadow-xs hover:border-orange-400 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </div>
                                        <span class="font-bold text-xs text-slate-900">Bebas Lab</span>
                                    </div>
                                    <button class="text-xs bg-orange-500 hover:bg-orange-600 text-white font-bold px-3.5 py-2 rounded-xl shadow-xs transition">Unduh</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Approval Dosen Wali -->
                <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-orange-100 shadow-card-clean">
                    <h2 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2.5 border-b border-orange-100 pb-4">
                        <i class="bi bi-check-square-fill text-emerald-500"></i> Keputusan Approval Dosen Wali
                    </h2>

                    <form action="" method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="approval">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Keputusan Persetujuan:</label>
                            <select class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 outline-none text-sm font-bold text-slate-800 cursor-pointer" name="status" required>
                                <option value="Approved" <?= (($detail['status_approval_wali'] ?? '') === 'Approved') ? 'selected' : ''; ?>>Setujui (Approve) - Lanjut ke Admin Layanan</option>
                                <option value="Rejected" <?= (($detail['status_approval_wali'] ?? '') === 'Rejected') ? 'selected' : ''; ?>>Tolak (Reject) - Butuh Perbaikan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan Dosen Wali (Opsional / Catatan Revisi):</label>
                            <textarea class="w-full px-5 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-emerald-500/15 focus:border-emerald-500 outline-none text-sm font-semibold text-slate-800 leading-relaxed" name="catatan_wali" rows="3" placeholder="Masukkan catatan atau revisi usulan judul jika ada..."><?= $detail['catatan_wali'] ?? ''; ?></textarea>
                        </div>

                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-4 rounded-2xl shadow-md shadow-emerald-600/20 transition text-sm inline-flex items-center gap-2">
                            <i class="bi bi-send-fill text-base"></i> Simpan Keputusan Approval
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </main>

</body>
</html>
