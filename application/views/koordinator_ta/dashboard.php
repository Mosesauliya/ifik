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
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-orange-500 selection:text-white relative">

    <!-- Header Glass Navbar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-xl border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-gradient-to-tr from-slate-900 to-slate-800 text-white rounded-2xl font-bold text-xl flex items-center justify-center box-3d shadow-md">
                        K
                    </div>
                    <div>
                        <span class="font-bold text-base text-slate-900 tracking-tight block leading-none">IFIK Portal</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-orange-600 mt-1 block">Koordinator Tugas Akhir</span>
                    </div>
                </div>

                <!-- User Profile Pill -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-800 leading-tight">Koordinator TA IFIK</span>
                        <span class="text-[10px] font-semibold text-slate-500">NIP: <?= $nip_koor ?? '19800202002'; ?></span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 text-orange-600 flex items-center justify-center font-bold text-base shadow-xs">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">

        <!-- Welcome Banner & Page Title -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block mb-1">OVERVIEW KOORDINATOR TA</span>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Dashboard Koordinator TA</h1>
                <p class="text-slate-500 text-xs mt-1 font-medium">Kelola persetujuan dan verifikasi usulan Tugas Akhir mahasiswa tingkat fakultas secara praktis.</p>
            </div>
            <div class="px-4 py-2 bg-white rounded-xl border border-slate-200 shadow-xs text-xs font-semibold text-slate-700 flex items-center gap-2 w-fit">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Approval System Active</span>
            </div>
        </div>

        <?php if($this->session->flashdata('success')): ?>
            <div class="mb-8 bg-emerald-50 border border-emerald-200 text-emerald-900 p-4 rounded-2xl shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                    <i class="bi bi-check-lg"></i>
                </div>
                <p class="text-xs font-bold"><?= $this->session->flashdata('success'); ?></p>
            </div>
        <?php endif; ?>

        <!-- Stat Summary Cards Grid (Crisp High-Contrast White Cards) -->
        <?php
            $totalMhs = !empty($list_mahasiswa) ? count($list_mahasiswa) : 0;
            $pendingCount = 0;
            $approvedCount = 0;
            $rejectedCount = 0;

            if(!empty($list_mahasiswa)) {
                foreach($list_mahasiswa as $row) {
                    $st = $row['status_approval_koor'] ?? 'Pending';
                    if($st === 'Approved') $approvedCount++;
                    else if($st === 'Rejected') $rejectedCount++;
                    else $pendingCount++;
                }
            }
        ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Total Pengajuan TA -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Mahasiswa TA</span>
                    <span class="text-3xl font-extrabold text-slate-900 tracking-tight block"><?= $totalMhs; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Pengajuan Tugas Akhir</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center text-xl font-bold shrink-0 shadow-md shadow-blue-500/20">
                    <i class="bi bi-journal-check"></i>
                </div>
            </div>

            <!-- Menunggu Approval Koordinator -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block mb-1">Menunggu Approval</span>
                    <span class="text-3xl font-extrabold text-amber-600 tracking-tight block"><?= $pendingCount; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Perlu Ditolak / Disetujui</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-white flex items-center justify-center text-xl font-bold shrink-0 shadow-md shadow-amber-500/20">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>

            <!-- Disetujui (Approved) -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block mb-1">Disetujui</span>
                    <span class="text-3xl font-extrabold text-emerald-600 tracking-tight block"><?= $approvedCount; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Lanjut ke Ketua KK</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-600 text-white flex items-center justify-center text-xl font-bold shrink-0 shadow-md shadow-emerald-500/20">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>

            <!-- Ditolak / Revisi (Rejected) -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 block mb-1">Perlu Revisi</span>
                    <span class="text-3xl font-extrabold text-rose-600 tracking-tight block"><?= $rejectedCount; ?></span>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">Telah Ditolak</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-rose-500 to-pink-600 text-white flex items-center justify-center text-xl font-bold shrink-0 shadow-md shadow-rose-500/20">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
            </div>
        </div>

        <!-- Table Container Card (Clean High-Contrast White Surface) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            
            <!-- Table Header & Controls Bar -->
            <div class="p-6 border-b border-slate-200 bg-white space-y-4">
                
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5 tracking-tight">
                            <i class="bi bi-card-checklist text-orange-600 text-lg"></i> Daftar Pengajuan Tugas Akhir
                        </h2>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Pilih mahasiswa untuk meninjau berkas dan melakukan persetujuan Koordinator TA.</p>
                    </div>

                    <!-- phpMyAdmin Style Controls: Records Per Page & Filter Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Per Page Dropdown -->
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl shadow-xs text-xs">
                            <i class="bi bi-list-ol text-slate-500"></i>
                            <span class="font-semibold text-slate-600">Tampilkan</span>
                            <select id="selectPerPage" class="bg-white border border-slate-300 rounded-lg px-2.5 py-1 font-bold text-slate-800 text-xs focus:ring-2 focus:ring-orange-500/20 outline-none">
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span class="font-semibold text-slate-600">records / hal</span>
                        </div>

                        <!-- Add Filter Rule Button -->
                        <button type="button" id="btnAddFilter" class="bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-300 font-bold px-3.5 py-2 rounded-xl text-xs flex items-center gap-1.5 transition shadow-xs">
                            <i class="bi bi-plus-lg"></i> Tambah Filter <span id="filterCountBadge" class="bg-orange-200 text-orange-800 px-1.5 py-0.5 rounded-full text-[10px]">1/4</span>
                        </button>

                        <!-- Reset Filter Button -->
                        <button type="button" id="btnResetFilters" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-3 py-2 rounded-xl text-xs flex items-center gap-1.5 transition">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- Dynamic Multi-Filter Rows Container (Max 4 Filters) -->
                <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-200 space-y-3" id="filterContainer">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="bi bi-funnel-fill text-orange-600"></i> Dynamic Multi-Filter (Maksimal 4 Kriteria)
                        </span>
                        <div class="flex items-center gap-1">
                            <!-- Quick Filter Pills -->
                            <span class="text-[11px] text-slate-500 font-medium mr-1 hidden sm:inline">Pintas Status:</span>
                            <button type="button" class="btn-quick-filter px-2.5 py-1 rounded-lg text-xs bg-slate-900 text-white font-bold transition shadow-xs" data-status="all">Semua</button>
                            <button type="button" class="btn-quick-filter px-2.5 py-1 rounded-lg text-xs bg-white text-slate-700 hover:bg-amber-50 hover:text-amber-700 border border-slate-200 transition" data-status="Pending">Pending</button>
                            <button type="button" class="btn-quick-filter px-2.5 py-1 rounded-lg text-xs bg-white text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 border border-slate-200 transition" data-status="Approved">Approved</button>
                            <button type="button" class="btn-quick-filter px-2.5 py-1 rounded-lg text-xs bg-white text-slate-700 hover:bg-rose-50 hover:text-rose-700 border border-slate-200 transition" data-status="Rejected">Rejected</button>
                        </div>
                    </div>

                    <!-- Filter Rows List injected dynamically via JS -->
                    <div id="filterRowsList" class="space-y-2"></div>
                </div>

            </div>
            
            <!-- Table View -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100/90 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                            <th class="py-3.5 px-5 pl-6">NIM</th>
                            <th class="py-3.5 px-5">Nama Mahasiswa</th>
                            <th class="py-3.5 px-5">Usulan Judul TA (Utama)</th>
                            <th class="py-3.5 px-5">Status Approval</th>
                            <th class="py-3.5 px-5">Tahap Saat Ini</th>
                            <th class="py-3.5 px-5 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium bg-white" id="tableBodyMhs">
                        <?php if(!empty($list_mahasiswa)): ?>
                            <?php foreach($list_mahasiswa as $mhs): ?>
                                <?php 
                                    $st = $mhs['status_approval_koor'] ?? 'Pending';
                                    $badgeStyle = ($st === 'Approved') ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : (($st === 'Rejected') ? 'bg-rose-50 text-rose-700 border-rose-300' : 'bg-amber-50 text-amber-700 border-amber-300');
                                ?>
                                <tr class="hover:bg-slate-50/80 transition-colors duration-150 mhs-row" 
                                    data-nim="<?= strtolower($mhs['nim']); ?>"
                                    data-nama="<?= strtolower($mhs['nama_depan'] . ' ' . $mhs['nama_belakang']); ?>"
                                    data-judul="<?= strtolower($mhs['judul_1']); ?>"
                                    data-status="<?= $st; ?>"
                                    data-stage="<?= strtolower($mhs['current_stage'] ?? 'Koordinator TA'); ?>"
                                    data-prodi="<?= strtolower($mhs['konsentrasi_dkv'] ?? 'Informatika'); ?>">
                                    
                                    <td class="py-4 px-5 pl-6 font-bold text-slate-900 mhs-nim"><?= $mhs['nim']; ?></td>
                                    <td class="py-4 px-5 font-semibold text-slate-800 mhs-nama"><?= $mhs['nama_depan'] . ' ' . $mhs['nama_belakang']; ?></td>
                                    <td class="py-4 px-5 text-slate-600 max-w-xs truncate font-normal"><?= !empty($mhs['judul_1']) ? character_limiter($mhs['judul_1'], 45) : '<span class="text-slate-400 italic">Belum Mendaftar</span>'; ?></td>
                                    <td class="py-4 px-5">
                                        <span class="px-3 py-1 font-bold text-[11px] rounded-full border shadow-xs inline-block <?= $badgeStyle; ?>"><?= $st; ?></span>
                                    </td>
                                    <td class="py-4 px-5">
                                        <span class="px-3 py-1 font-semibold text-[11px] rounded-full bg-slate-100 text-slate-700 border border-slate-200 inline-block"><?= $mhs['current_stage'] ?? 'Koordinator TA'; ?></span>
                                    </td>
                                    <td class="py-4 px-5 pr-6 text-right">
                                        <a href="<?= site_url('koordinatorta/detail_mahasiswa/' . $mhs['nim']); ?>" class="bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-md shadow-orange-600/20 inline-flex items-center gap-1.5 transition-all hover:-translate-y-0.5 active:translate-y-0">
                                            <i class="bi bi-search text-xs"></i> Detail & Approval
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="emptyRow">
                                <td colspan="6" class="py-8 text-center text-slate-500 font-medium">
                                    Belum ada data pengajuan Tugas Akhir.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- phpMyAdmin Style Pagination Footer Bar -->
            <div class="p-4 border-t border-slate-200 bg-slate-50/60 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
                <!-- Info Left -->
                <div id="paginationInfo" class="font-semibold text-slate-600 text-xs">
                    Menampilkan <span id="pageStart">1</span> - <span id="pageEnd">10</span> dari <span id="totalRecords"><?= $totalMhs; ?></span> data
                </div>

                <!-- Navigation Controls Right -->
                <div class="flex items-center gap-1.5" id="paginationNav">
                    <!-- Dynamic Pagination Buttons Rendered by JS -->
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-5 text-center text-xs text-slate-500 font-medium mt-12">
        &copy; <?= date('Y'); ?> IFIK Portal — Fakultas Industri Kreatif, Telkom University
    </footer>

    <!-- Dynamic Multi-Filter & Pagination JS Engine -->
    <script>
    let filterRules = [
        { id: 1, category: 'all', key: '' }
    ];
    let currentPage = 1;
    let perPage = 10;
    let nextFilterId = 2;

    const maxFilters = 4;
    const filterRowsList = document.getElementById('filterRowsList');
    const btnAddFilter = document.getElementById('btnAddFilter');
    const btnResetFilters = document.getElementById('btnResetFilters');
    const filterCountBadge = document.getElementById('filterCountBadge');
    const selectPerPage = document.getElementById('selectPerPage');
    const allRows = Array.from(document.querySelectorAll('.mhs-row'));

    // Option Definitions for Categories
    const categoryOptions = [
        { value: 'all', label: 'Semua Kolom (Pencarian Umum)' },
        { value: 'nim', label: 'NIM Mahasiswa' },
        { value: 'nama', label: 'Nama Mahasiswa' },
        { value: 'judul', label: 'Usulan Judul TA' },
        { value: 'status', label: 'Status Approval (Pending/Approved/Rejected)' },
        { value: 'stage', label: 'Tahap Saat Ini' },
        { value: 'prodi', label: 'Konsentrasi / Prodi' }
    ];

    // Render Filter Rule Inputs in UI
    function renderFilterUI() {
        filterRowsList.innerHTML = '';

        filterRules.forEach((rule, index) => {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'flex flex-col sm:flex-row items-stretch sm:items-center gap-2 bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs';

            // Category Select Dropdown
            let categorySelectHTML = `<select class="rule-cat bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-orange-500/20" data-id="${rule.id}">`;
            categoryOptions.forEach(opt => {
                const selected = (opt.value === rule.category) ? 'selected' : '';
                categorySelectHTML += `<option value="${opt.value}" ${selected}>${opt.label}</option>`;
            });
            categorySelectHTML += `</select>`;

            // Key Input or Select
            let keyInputHTML = '';
            if (rule.category === 'status') {
                keyInputHTML = `
                    <select class="rule-key bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-800 outline-none flex-grow focus:ring-2 focus:ring-orange-500/20" data-id="${rule.id}">
                        <option value="" ${rule.key === '' ? 'selected' : ''}>-- Semua Status --</option>
                        <option value="Pending" ${rule.key === 'Pending' ? 'selected' : ''}>Pending</option>
                        <option value="Approved" ${rule.key === 'Approved' ? 'selected' : ''}>Approved</option>
                        <option value="Rejected" ${rule.key === 'Rejected' ? 'selected' : ''}>Rejected</option>
                    </select>
                `;
            } else if (rule.category === 'prodi') {
                keyInputHTML = `
                    <select class="rule-key bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-800 outline-none flex-grow focus:ring-2 focus:ring-orange-500/20" data-id="${rule.id}">
                        <option value="" ${rule.key === '' ? 'selected' : ''}>-- Semua Konsentrasi --</option>
                        <option value="informatika" ${rule.key === 'informatika' ? 'selected' : ''}>Informatika</option>
                        <option value="desain grafis" ${rule.key === 'desain grafis' ? 'selected' : ''}>Desain Grafis</option>
                        <option value="multimedia" ${rule.key === 'multimedia' ? 'selected' : ''}>Multimedia</option>
                    </select>
                `;
            } else {
                keyInputHTML = `
                    <input type="text" class="rule-key bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-medium text-slate-800 outline-none flex-grow focus:bg-white focus:ring-2 focus:ring-orange-500/20" data-id="${rule.id}" placeholder="Ketik kata kunci pencarian..." value="${escapeHtml(rule.key)}">
                `;
            }

            // Remove Button
            const removeBtnHTML = filterRules.length > 1 ? `
                <button type="button" class="btn-remove-rule text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 p-1.5 rounded-lg border border-rose-200 transition text-xs font-bold shrink-0 flex items-center gap-1" data-id="${rule.id}">
                    <i class="bi bi-x-lg"></i> <span class="sm:hidden">Hapus Filter</span>
                </button>
            ` : '';

            rowDiv.innerHTML = `
                <div class="text-[11px] font-bold text-slate-400 px-1 shrink-0">Filter #${index + 1}:</div>
                ${categorySelectHTML}
                ${keyInputHTML}
                ${removeBtnHTML}
            `;

            filterRowsList.appendChild(rowDiv);
        });

        // Update Badge & Button State
        filterCountBadge.textContent = `${filterRules.length}/${maxFilters}`;
        if (filterRules.length >= maxFilters) {
            btnAddFilter.disabled = true;
            btnAddFilter.className = 'bg-slate-100 text-slate-400 border border-slate-200 font-bold px-3.5 py-2 rounded-xl text-xs flex items-center gap-1.5 cursor-not-allowed';
            btnAddFilter.title = 'Maksimal 4 filter tercapai';
        } else {
            btnAddFilter.disabled = false;
            btnAddFilter.className = 'bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-300 font-bold px-3.5 py-2 rounded-xl text-xs flex items-center gap-1.5 transition shadow-xs';
            btnAddFilter.title = 'Tambah Kriteria Filter';
        }

        attachRuleEvents();
    }

    function escapeHtml(str) {
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    // Attach Event Listeners to Category & Key inputs
    function attachRuleEvents() {
        document.querySelectorAll('.rule-cat').forEach(sel => {
            sel.addEventListener('change', (e) => {
                const id = parseInt(e.target.getAttribute('data-id'));
                const rule = filterRules.find(r => r.id === id);
                if (rule) {
                    rule.category = e.target.value;
                    rule.key = ''; // reset key when category changes
                    currentPage = 1;
                    renderFilterUI();
                    applyFilterAndPaginate();
                }
            });
        });

        document.querySelectorAll('.rule-key').forEach(input => {
            const handler = (e) => {
                const id = parseInt(input.getAttribute('data-id'));
                const rule = filterRules.find(r => r.id === id);
                if (rule) {
                    rule.key = e.target.value;
                    currentPage = 1;
                    applyFilterAndPaginate();
                }
            };
            input.addEventListener('input', handler);
            input.addEventListener('change', handler);
        });

        document.querySelectorAll('.btn-remove-rule').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = parseInt(btn.getAttribute('data-id'));
                filterRules = filterRules.filter(r => r.id !== id);
                if (filterRules.length === 0) {
                    filterRules = [{ id: nextFilterId++, category: 'all', key: '' }];
                }
                currentPage = 1;
                renderFilterUI();
                applyFilterAndPaginate();
            });
        });
    }

    // Add New Filter Rule
    if (btnAddFilter) {
        btnAddFilter.addEventListener('click', () => {
            if (filterRules.length < maxFilters) {
                filterRules.push({ id: nextFilterId++, category: 'all', key: '' });
                renderFilterUI();
                applyFilterAndPaginate();
            }
        });
    }

    // Reset All Filters
    if (btnResetFilters) {
        btnResetFilters.addEventListener('click', () => {
            filterRules = [{ id: nextFilterId++, category: 'all', key: '' }];
            currentPage = 1;
            renderFilterUI();
            
            // Reset Quick Filter Pills styling
            document.querySelectorAll('.btn-quick-filter').forEach(b => {
                b.className = 'btn-quick-filter px-2.5 py-1 rounded-lg text-xs bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 transition';
            });
            const allPill = document.querySelector('.btn-quick-filter[data-status="all"]');
            if (allPill) allPill.className = 'btn-quick-filter px-2.5 py-1 rounded-lg text-xs bg-slate-900 text-white font-bold transition shadow-xs';

            applyFilterAndPaginate();
        });
    }

    // Quick Filter Status Pills Handler
    document.querySelectorAll('.btn-quick-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.btn-quick-filter').forEach(b => {
                b.className = 'btn-quick-filter px-2.5 py-1 rounded-lg text-xs bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 transition';
            });
            btn.className = 'btn-quick-filter px-2.5 py-1 rounded-lg text-xs bg-slate-900 text-white font-bold transition shadow-xs';

            const statusVal = btn.getAttribute('data-status');
            // Set Filter #1 to Status
            filterRules[0].category = (statusVal === 'all') ? 'all' : 'status';
            filterRules[0].key = (statusVal === 'all') ? '' : statusVal;
            
            currentPage = 1;
            renderFilterUI();
            applyFilterAndPaginate();
        });
    });

    // Select Per Page Event
    if (selectPerPage) {
        selectPerPage.addEventListener('change', (e) => {
            perPage = parseInt(e.target.value) || 10;
            currentPage = 1;
            applyFilterAndPaginate();
        });
    }

    // Filter Logic & Pagination Calculation Engine
    function applyFilterAndPaginate() {
        const matchingRows = allRows.filter(row => {
            const nim = row.getAttribute('data-nim') || '';
            const nama = row.getAttribute('data-nama') || '';
            const judul = row.getAttribute('data-judul') || '';
            const status = (row.getAttribute('data-status') || '').toLowerCase();
            const stage = row.getAttribute('data-stage') || '';
            const prodi = row.getAttribute('data-prodi') || '';

            // AND logic across all filter rules
            return filterRules.every(rule => {
                const query = rule.key.toLowerCase().trim();
                if (!query) return true; // Empty rule matches everything

                switch(rule.category) {
                    case 'nim':
                        return nim.includes(query);
                    case 'nama':
                        return nama.includes(query);
                    case 'judul':
                        return judul.includes(query);
                    case 'status':
                        return status === query;
                    case 'stage':
                        return stage.includes(query);
                    case 'prodi':
                        return prodi.includes(query);
                    case 'all':
                    default:
                        return nim.includes(query) || nama.includes(query) || judul.includes(query) || status.includes(query) || stage.includes(query) || prodi.includes(query);
                }
            });
        });

        const totalRecords = matchingRows.length;
        const totalPages = Math.ceil(totalRecords / perPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIndex = (currentPage - 1) * perPage;
        const endIndex = Math.min(startIndex + perPage, totalRecords);

        // Hide all rows first
        allRows.forEach(row => row.classList.add('hidden'));

        // Show sliced rows for current page
        const pagedRows = matchingRows.slice(startIndex, endIndex);
        pagedRows.forEach(row => row.classList.remove('hidden'));

        // Render Empty state if no records match
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) {
            if (totalRecords === 0) emptyRow.classList.remove('hidden');
            else emptyRow.classList.add('hidden');
        }

        // Update Pagination Info Bar
        document.getElementById('pageStart').textContent = totalRecords > 0 ? (startIndex + 1) : 0;
        document.getElementById('pageEnd').textContent = endIndex;
        document.getElementById('totalRecords').textContent = totalRecords;

        // Render Pagination Controls
        renderPaginationNav(totalPages);
    }

    // Render Pagination Buttons
    function renderPaginationNav(totalPages) {
        const navContainer = document.getElementById('paginationNav');
        if (!navContainer) return;
        navContainer.innerHTML = '';

        if (totalPages <= 1) return; // No pagination controls needed if only 1 page

        // First Button
        const btnFirst = document.createElement('button');
        btnFirst.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${currentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnFirst.innerHTML = '&laquo; Awal';
        btnFirst.disabled = (currentPage === 1);
        btnFirst.addEventListener('click', () => { currentPage = 1; applyFilterAndPaginate(); });
        navContainer.appendChild(btnFirst);

        // Prev Button
        const btnPrev = document.createElement('button');
        btnPrev.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${currentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnPrev.innerHTML = '&lsaquo; Prev';
        btnPrev.disabled = (currentPage === 1);
        btnPrev.addEventListener('click', () => { currentPage--; applyFilterAndPaginate(); });
        navContainer.appendChild(btnPrev);

        // Numbered Pages
        const maxVisibleButtons = 5;
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);
        if (endPage - startPage + 1 < maxVisibleButtons) {
            startPage = Math.max(1, endPage - maxVisibleButtons + 1);
        }

        for (let p = startPage; p <= endPage; p++) {
            const btnPage = document.createElement('button');
            const isActive = (p === currentPage);
            btnPage.className = `px-3 py-1 rounded-lg text-xs font-bold transition ${isActive ? 'bg-orange-600 text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'}`;
            btnPage.textContent = p;
            btnPage.addEventListener('click', () => { currentPage = p; applyFilterAndPaginate(); });
            navContainer.appendChild(btnPage);
        }

        // Next Button
        const btnNext = document.createElement('button');
        btnNext.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${currentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnNext.innerHTML = 'Next &rsaquo;';
        btnNext.disabled = (currentPage === totalPages);
        btnNext.addEventListener('click', () => { currentPage++; applyFilterAndPaginate(); });
        navContainer.appendChild(btnNext);

        // Last Button
        const btnLast = document.createElement('button');
        btnLast.className = `px-2.5 py-1 rounded-lg border text-xs font-bold transition ${currentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100'}`;
        btnLast.innerHTML = 'Akhir &raquo;';
        btnLast.disabled = (currentPage === totalPages);
        btnLast.addEventListener('click', () => { currentPage = totalPages; applyFilterAndPaginate(); });
        navContainer.appendChild(btnLast);
    }

    // Initial Setup on Page Load
    document.addEventListener('DOMContentLoaded', () => {
        renderFilterUI();
        applyFilterAndPaginate();
    });
    </script>
</body>
</html>
