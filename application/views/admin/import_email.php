<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Import Email & Token Dispatcher | IFIK Telkom University</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
    
    <!-- SheetJS for XLSX parsing & export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    
    <!-- PapaParse for CSV parsing -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/papaparse/5.4.1/papaparse.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Canvas Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

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

        .drop-zone {
            border: 2px dashed #ea580c;
            background: linear-gradient(180deg, #fff7ed 0%, #ffffff 100%);
            transition: all 0.25s ease-in-out;
        }

        .drop-zone:hover, .drop-zone.dragover {
            background-color: #ffedd5;
            border-color: #c2410c;
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -6px rgba(234, 88, 12, 0.2);
        }

        .token-badge {
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 1.5px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
            text-shadow: 0 0 8px rgba(56, 189, 248, 0.4);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #ea580c;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.4); }
            50% { box-shadow: 0 0 0 10px rgba(234, 88, 12, 0); }
        }
        .pulse-glow {
            animation: pulseGlow 2s infinite;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased pb-16">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 glass-header px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="<?= site_url('dashboard') ?>" class="w-10 h-10 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center hover:bg-brand-600 hover:text-white transition-all shadow-sm">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Import Email & Dispatcher Token</h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold bg-orange-100 text-brand-700 rounded-full border border-orange-200">
                            Frontend Prototype
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola impor CSV/XLSX, generate token 8 karakter, dan kirim email pemberitahuan.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="downloadSampleTemplate('csv')" class="px-3.5 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-file-csv text-green-600 text-sm"></i>
                    <span>Template CSV</span>
                </button>
                <button onclick="downloadSampleTemplate('xlsx')" class="px-3.5 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-file-excel text-emerald-600 text-sm"></i>
                    <span>Template XLSX</span>
                </button>
                <button onclick="openEmailTemplateModal()" class="px-3.5 py-2 text-xs font-medium text-white bg-slate-800 hover:bg-slate-900 rounded-lg transition-all flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-sliders text-amber-400"></i>
                    <span>Template Email</span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6">

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Total Accounts -->
            <div class="card-custom p-5 border-l-4 border-l-brand-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Total Akun</p>
                        <h3 id="stat-total" class="text-2xl font-extrabold text-slate-900 mt-1">0</h3>
                        <p id="stat-total-desc" class="text-xs text-slate-500 mt-1">0 Dosen, 0 Mahasiswa</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>

            <!-- Token Generated -->
            <div class="card-custom p-5 border-l-4 border-l-cyan-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Token Generated</p>
                        <h3 id="stat-token" class="text-2xl font-extrabold text-slate-900 mt-1">0 <span class="text-xs font-semibold text-cyan-600 font-normal">(0%)</span></h3>
                        <p id="stat-token-desc" class="text-xs text-slate-500 mt-1">0 akun sudah siap token</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-key"></i>
                    </div>
                </div>
            </div>

            <!-- Email Sent -->
            <div class="card-custom p-5 border-l-4 border-l-emerald-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Email Terkirim</p>
                        <h3 id="stat-sent" class="text-2xl font-extrabold text-slate-900 mt-1">0 <span class="text-xs font-semibold text-emerald-600 font-normal">(0%)</span></h3>
                        <p id="stat-sent-desc" class="text-xs text-slate-500 mt-1">0 email berhasil dikirim</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                </div>
            </div>

            <!-- Email Pending -->
            <div class="card-custom p-5 border-l-4 border-l-amber-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Belum Terkirim</p>
                        <h3 id="stat-pending" class="text-2xl font-extrabold text-slate-900 mt-1">0</h3>
                        <p id="stat-pending-desc" class="text-xs text-slate-500 mt-1">Memerlukan pengiriman</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Upload Drag & Drop Area -->
        <div class="card-custom p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-file-import"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-900">Upload File Import Email (CSV / XLSX)</h2>
                </div>
                <button onclick="openAddAccountModal()" class="text-xs font-semibold text-brand-600 hover:text-brand-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Tambah Manual</span>
                </button>
            </div>

            <div id="drop-zone" class="drop-zone rounded-xl p-8 text-center cursor-pointer relative overflow-hidden">
                <input type="file" id="file-input" accept=".csv, .xlsx, .xls" class="hidden" onchange="handleFileSelect(event)">
                
                <div class="flex flex-col items-center justify-center gap-3">
                    <div class="w-16 h-16 rounded-2xl bg-white text-brand-600 shadow-md border border-orange-100 flex items-center justify-center text-2xl mb-1">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">
                            Tarik & Lepas File CSV / XLSX di sini, atau <span class="text-brand-600 underline">Pilih File</span>
                        </p>
                        <p class="text-xs text-slate-500 mt-1">Mendukung format .CSV, .XLSX, .XLS hingga 10MB (Kolom: Nama, Email, Role, NIM/NIP)</p>
                    </div>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="px-2.5 py-1 text-[11px] font-semibold bg-emerald-50 text-emerald-700 rounded-md border border-emerald-200">
                            <i class="fa-solid fa-[#xlsx] text-emerald-600 mr-1"></i> Auto Detect Column Headers
                        </span>
                        <span class="px-2.5 py-1 text-[11px] font-semibold bg-blue-50 text-blue-700 rounded-md border border-blue-200">
                            <i class="fa-solid fa-[#check] text-blue-600 mr-1"></i> Instant Browser Validation
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Toolbar & Filters -->
        <div class="card-custom p-5 mb-8 space-y-4">
            <!-- Row 1: Search Bar & Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="relative lg:col-span-4">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" id="search-input" oninput="handleSearch(this.value)" placeholder="Cari Nama, Email, Token, NIM..." class="w-full pl-10 pr-4 h-9 text-xs font-medium bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition-all">
                </div>

                <!-- Role Filter -->
                <div class="lg:col-span-2">
                    <select id="filter-role" onchange="handleFilter()" class="w-full h-9 px-3 text-xs font-medium bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Role</option>
                        <option value="Dosen">Dosen</option>
                        <option value="Mahasiswa">Mahasiswa</option>
                        <option value="Staf">Staf</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>

                <!-- Token Status Filter -->
                <div class="lg:col-span-3">
                    <select id="filter-token" onchange="handleFilter()" class="w-full h-9 px-3 text-xs font-medium bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Status Token</option>
                        <option value="ready">Ready (Sudah Ada Token)</option>
                        <option value="empty">Belum Generated</option>
                        <option value="password_changed">🔒 Password Diubah (Protected)</option>
                    </select>
                </div>

                <!-- Email Status Filter -->
                <div class="lg:col-span-3">
                    <select id="filter-email" onchange="handleFilter()" class="w-full h-9 px-3 text-xs font-medium bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Status Email</option>
                        <option value="terkirim">Terkirim</option>
                        <option value="belum">Belum Terkirim</option>
                        <option value="gagal">Gagal</option>
                    </select>
                </div>
            </div>

            <!-- Row 2: Batch Actions & Export Tools -->
            <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <!-- Batch Actions Left -->
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="bulkGenerateTokenSelected()" class="h-9 px-3.5 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-all flex items-center gap-2 shadow-xs">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Generate Token (Selected)</span>
                    </button>
                    <button onclick="bulkGenerateTokenAll()" class="h-9 px-3.5 text-xs font-semibold text-slate-700 bg-orange-100 hover:bg-orange-200 rounded-lg transition-all flex items-center gap-1.5 border border-orange-200">
                        <i class="fa-solid fa-key text-brand-600"></i>
                        <span>Generate All (Kosong)</span>
                    </button>
                    <button onclick="bulkSendEmailSelected()" class="h-9 px-3.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-all flex items-center gap-2 shadow-xs">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Email (Selected)</span>
                    </button>
                </div>

                <!-- Tools Right -->
                <div class="flex items-center gap-2">
                    <button onclick="exportData('csv')" class="h-9 px-3 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-all flex items-center gap-1.5 border border-slate-200" title="Export to CSV">
                        <i class="fa-solid fa-file-csv text-slate-700 text-sm"></i>
                        <span>CSV</span>
                    </button>
                    <button onclick="exportData('xlsx')" class="h-9 px-3 text-xs font-medium text-emerald-800 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-all flex items-center gap-1.5 border border-emerald-200" title="Export to Excel XLSX">
                        <i class="fa-solid fa-file-excel text-emerald-600 text-sm"></i>
                        <span>Excel</span>
                    </button>
                    <div class="h-6 w-px bg-slate-200 mx-1"></div>
                    <button onclick="bulkDeleteSelected()" class="h-9 px-3 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-all flex items-center gap-1.5 border border-rose-200" title="Hapus Selected">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Accounts Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100/80 text-slate-600 uppercase font-bold tracking-wider text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="p-3.5 w-10 text-center">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)" class="rounded text-brand-600 focus:ring-brand-500">
                        </th>
                        <th class="p-3.5 w-12 text-center whitespace-nowrap">No</th>
                        <th class="p-3.5 whitespace-nowrap">Akun / Pengguna</th>
                        <th class="p-3.5 whitespace-nowrap">NIM / NIP / ID</th>
                        <th class="p-3.5 whitespace-nowrap text-center">Token Access (8-Char)</th>
                        <th class="p-3.5 whitespace-nowrap text-center">Status Token</th>
                        <th class="p-3.5 whitespace-nowrap text-center">Status Kirim Email</th>
                        <th class="p-3.5 whitespace-nowrap text-center">Tgl Import</th>
                        <th class="p-3.5 text-center min-w-[120px] whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="accounts-table-body" class="divide-y divide-slate-200 bg-white font-medium">
                    <!-- Rows rendered dynamically via JS -->
                </tbody>
            </table>
        </div>

            <!-- Table Footer & Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-5 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <span>Menampilkan</span>
                    <select id="page-size" onchange="changePageSize(this.value)" class="py-1 px-2 text-xs bg-slate-50 border border-slate-300 rounded font-semibold">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>dari <strong id="total-rows-count" class="text-slate-800">0</strong> data</span>
                    <span id="selected-rows-count" class="hidden text-brand-600 font-bold ml-2">(0 terpilih)</span>
                </div>

                <div class="flex items-center gap-1" id="pagination-controls">
                    <!-- Pagination buttons rendered via JS -->
                </div>
            </div>
        </div>

    </main>

    <!-- MODAL: Email Template Editor & Visual Preview -->
    <div id="modal-template" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500 text-slate-900 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm">Pengaturan & Template Email Dispatcher</h3>
                        <p class="text-xs text-slate-400">Kustomisasi konten email pemberitahuan token akun</p>
                    </div>
                </div>
                <button onclick="closeEmailTemplateModal()" class="text-slate-400 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-5 flex-1">
                <!-- Variables Tags -->
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Tag Variabel Dinamis (Klik untuk menyisipkan):</label>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="insertTag('{NAMA}')" class="px-2.5 py-1 text-xs font-mono bg-orange-50 text-brand-700 border border-orange-200 rounded-md hover:bg-orange-100">{NAMA}</button>
                        <button onclick="insertTag('{EMAIL}')" class="px-2.5 py-1 text-xs font-mono bg-orange-50 text-brand-700 border border-orange-200 rounded-md hover:bg-orange-100">{EMAIL}</button>
                        <button onclick="insertTag('{TOKEN}')" class="px-2.5 py-1 text-xs font-mono bg-orange-50 text-brand-700 border border-orange-200 rounded-md hover:bg-orange-100">{TOKEN}</button>
                        <button onclick="insertTag('{ROLE}')" class="px-2.5 py-1 text-xs font-mono bg-orange-50 text-brand-700 border border-orange-200 rounded-md hover:bg-orange-100">{ROLE}</button>
                        <button onclick="insertTag('{NIM_NIP}')" class="px-2.5 py-1 text-xs font-mono bg-orange-50 text-brand-700 border border-orange-200 rounded-md hover:bg-orange-100">{NIM_NIP}</button>
                    </div>
                </div>

                <!-- Email Subject -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Subjek Email:</label>
                    <input type="text" id="template-subject" class="w-full px-3.5 py-2 text-xs font-semibold bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <!-- Template Body -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pesan Email:</label>
                    <textarea id="template-body" rows="6" class="w-full px-3.5 py-2 text-xs font-medium bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 font-mono"></textarea>
                </div>

                <!-- Visual Live HTML Preview -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Live Visual Email Preview:</label>
                    <div class="border border-slate-200 rounded-xl p-5 bg-slate-50">
                        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-lg mx-auto">
                            <!-- Header Banner -->
                            <div class="border-b border-slate-100 pb-4 mb-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center font-black text-sm">IF</div>
                                    <span class="font-extrabold text-sm text-slate-900">IFIK Telkom University</span>
                                </div>
                                <span class="text-[10px] text-slate-400">Pemberitahuan Resmi</span>
                            </div>

                            <p class="text-xs text-slate-500 mb-1">Subjek: <strong id="preview-subject-text" class="text-slate-800"></strong></p>
                            
                            <div id="preview-html-body" class="text-xs text-slate-700 space-y-3 my-4 bg-orange-50/50 p-4 rounded-lg border border-orange-100">
                                <!-- Rendered dynamically -->
                            </div>

                            <div class="border-t border-slate-100 pt-3 text-[11px] text-slate-400 text-center">
                                Telecommunication University &bull; Informatika & Custom Systems
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-3.5 bg-slate-100 border-t border-slate-200 flex items-center justify-end gap-3">
                <button onclick="closeEmailTemplateModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 rounded-lg transition-all">
                    Batal
                </button>
                <button onclick="saveEmailTemplate()" class="px-4 py-2 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg transition-all shadow-sm">
                    Simpan Template
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: Email Sending Progress Simulation Modal -->
    <div id="modal-send-progress" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-200">
            <div class="px-6 py-4 bg-brand-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/20 text-white flex items-center justify-center font-bold">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm">Mengirim Email Dispatcher</h3>
                        <p class="text-xs text-orange-100" id="send-modal-subtitle">Proses pengiriman email sedang berjalan...</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Circular/Linear Progress -->
                <div class="mb-5">
                    <div class="flex items-center justify-between text-xs font-bold mb-2">
                        <span id="send-progress-status" class="text-slate-700">Menginisialisasi SMTP...</span>
                        <span id="send-progress-percent" class="text-brand-600 font-extrabold text-sm">0%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden p-0.5 border border-slate-200">
                        <div id="send-progress-bar" class="bg-gradient-to-r from-brand-500 to-emerald-500 h-full rounded-full transition-all duration-300 w-0"></div>
                    </div>
                </div>

                <!-- Activity Terminal Output -->
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">Live Log Dispatcher:</label>
                <div id="send-terminal-log" class="bg-slate-950 text-slate-200 font-mono text-[11px] p-4 rounded-xl h-48 overflow-y-auto space-y-1.5 border border-slate-800 shadow-inner">
                    <div class="text-slate-500">[SYSTEM] Ready to send emails...</div>
                </div>
            </div>

            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs text-slate-500" id="send-count-summary">0 / 0 Terkirim</span>
                <button id="send-modal-close-btn" disabled onclick="closeSendProgressModal()" class="px-4 py-2 text-xs font-semibold text-white bg-slate-400 cursor-not-allowed rounded-lg transition-all">
                    Selesai
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: Add / Edit Account Manual -->
    <div id="modal-account" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-200">
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <h3 id="modal-account-title" class="font-bold text-sm">Tambah Akun Manual</h3>
                <button onclick="closeAccountModal()" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="account-form" onsubmit="saveAccountForm(event)" class="p-6 space-y-4">
                <input type="hidden" id="account-id">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" id="acc-name" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email *</label>
                    <input type="email" id="acc-email" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Peran / Role *</label>
                        <select id="acc-role" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            <option value="Dosen">Dosen</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                            <option value="Staf">Staf</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">NIM / NIP / ID</label>
                        <input type="text" id="acc-nim-nip" placeholder="Contoh: 1301210001" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Token Access (8 Karakter: Besar, Kecil, Angka, Simbol)</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="acc-token" maxlength="8" placeholder="Otomatis / Isi manual" class="flex-1 px-3 py-2 text-xs font-mono bg-slate-50 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <button type="button" onclick="generateTokenForInput()" class="px-3 py-2 text-xs font-semibold text-brand-600 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100">
                            Generate 8-Char
                        </button>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeAccountModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT APPLICATION LOGIC -->
    <script>
        // Initial Mock State Data for demonstration
        let state = {
            accounts: [
                { id: 'ACC-101', name: 'Dr. Ir. Ahmad Sudrajat, M.T.', email: 'ahmad.sudrajat@telkomuniversity.ac.id', role: 'Dosen', nim_nip: '197804122005011002', token: 'X8#kP2w!', token_status: 'ready', password_changed: false, email_status: 'terkirim', email_sent_at: '2026-08-09 14:20', date_imported: '2026-08-09 10:00' },
                { id: 'ACC-102', name: 'Budi Santoso', email: 'budi.santoso@student.telkomuniversity.ac.id', role: 'Mahasiswa', nim_nip: '1301210045', token: 'L7$mP9r#', token_status: 'ready', password_changed: true, password_changed_at: '2026-08-09 12:30', email_status: 'terkirim', email_sent_at: '2026-08-09 11:00', date_imported: '2026-08-09 10:00' },
                { id: 'ACC-103', name: 'Siti Rahmawati, S.Kom.', email: 'siti.rahmawati@telkomuniversity.ac.id', role: 'Staf', nim_nip: '2019080104', token: '', token_status: 'empty', password_changed: false, email_status: 'belum', email_sent_at: '-', date_imported: '2026-08-09 10:00' },
                { id: 'ACC-104', name: 'Dewi Lestari', email: 'dewi.lestari@student.telkomuniversity.ac.id', role: 'Mahasiswa', nim_nip: '1301210088', token: 'V3@nQ5z!', token_status: 'ready', password_changed: true, password_changed_at: '2026-08-09 15:10', email_status: 'terkirim', email_sent_at: '2026-08-09 14:00', date_imported: '2026-08-09 10:30' },
                { id: 'ACC-105', name: 'Fikri Haikal', email: 'fikri.haikal@student.telkomuniversity.ac.id', role: 'Mahasiswa', nim_nip: '1301210102', token: '', token_status: 'empty', password_changed: false, email_status: 'belum', email_sent_at: '-', date_imported: '2026-08-09 11:00' },
                { id: 'ACC-106', name: 'Prof. Dr. Hendra Wijaya', email: 'hendra.wijaya@telkomuniversity.ac.id', role: 'Dosen', nim_nip: '196503151990021001', token: 'G4#jK2l8', token_status: 'ready', password_changed: false, email_status: 'belum', email_sent_at: '-', date_imported: '2026-08-09 11:15' },
                { id: 'ACC-107', name: 'Nadia Putri', email: 'nadia.putri@student.telkomuniversity.ac.id', role: 'Mahasiswa', nim_nip: '1301210156', token: 'A9!b8C#d', token_status: 'ready', password_changed: false, email_status: 'terkirim', email_sent_at: '2026-08-09 15:45', date_imported: '2026-08-09 11:30' }
            ],
            selectedIds: [],
            searchQuery: '',
            filterRole: '',
            filterTokenStatus: '',
            filterEmailStatus: '',
            currentPage: 1,
            pageSize: 10,
            emailTemplate: {
                subject: '[IFIK Telkom University] Token Akses Portal Akun Anda: {TOKEN}',
                body: 'Halo {NAMA},\n\nAkun portal IFIK Telkom University Anda telah didaftarkan sebagai {ROLE}.\n\nBerikut adalah Kode Token Akses 8-Karakter unik Anda:\n===============================\nKODE TOKEN : {TOKEN}\nNIM / NIP  : {NIM_NIP}\nEMAIL      : {EMAIL}\n===============================\n\nGunakan token ini untuk melakukan verifikasi awal dan aktivasi kata sandi akun Anda.\n\nSalam hangat,\nTim Layanan Informatika (IFIK) Telkom University'
            }
        };

        // Initialize application on DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            initDropZone();
            renderStats();
            renderTable();
            initEmailTemplateFields();
        });

        // 1. GENERATE 8-CHARACTER TOKEN UTILITY (Huruf Besar, Huruf Kecil, Simbol, Angka)
        function generate8CharToken() {
            const uppers = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
            const lowers = 'abcdefghijkmnpqrstuvwxyz';
            const numbers = '23456789';
            const symbols = '!@#$%^&*_-';
            const allChars = uppers + lowers + numbers + symbols;

            // Minimal 1 karakter dari tiap kelompok (Huruf Besar, Huruf Kecil, Angka, Simbol)
            let tokenArr = [
                uppers.charAt(Math.floor(Math.random() * uppers.length)),
                lowers.charAt(Math.floor(Math.random() * lowers.length)),
                numbers.charAt(Math.floor(Math.random() * numbers.length)),
                symbols.charAt(Math.floor(Math.random() * symbols.length))
            ];

            // 4 karakter sisanya acak dari seluruh kombinasi
            for (let i = 0; i < 4; i++) {
                tokenArr.push(allChars.charAt(Math.floor(Math.random() * allChars.length)));
            }

            // Acak urutan posisi (Fisher-Yates Shuffle)
            for (let i = tokenArr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [tokenArr[i], tokenArr[j]] = [tokenArr[j], tokenArr[i]];
            }

            return tokenArr.join('');
        }

        // 1.5 DOMAIN VALIDATION HELPER (@telkomuniversity.ac.id or @student.telkomuniversity.ac.id)
        function isValidTelkomEmail(email) {
            if (!email) return false;
            const lower = email.trim().toLowerCase();
            return lower.endsWith('@telkomuniversity.ac.id') || lower.endsWith('@student.telkomuniversity.ac.id');
        }

        // 2. FILE DRAG & DROP & PARSING LOGIC
        function initDropZone() {
            const dropZone = document.getElementById('drop-zone');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
            });

            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    processUploadedFile(files[0]);
                }
            });

            dropZone.addEventListener('click', () => {
                document.getElementById('file-input').click();
            });
        }

        function handleFileSelect(event) {
            const files = event.target.files;
            if (files.length > 0) {
                processUploadedFile(files[0]);
            }
        }

        function processUploadedFile(file) {
            const ext = file.name.split('.').pop().toLowerCase();
            Swal.fire({
                title: 'Membaca File...',
                html: `Memproses data dari file <b>${file.name}</b>`,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            if (ext === 'csv') {
                Papa.parse(file, {
                    header: true,
                    skipEmptyLines: true,
                    complete: function(results) {
                        parseAndImportRawData(results.data, file.name);
                    },
                    error: function(err) {
                        Swal.fire('Format Error', 'Gagal membaca CSV: ' + err.message, 'error');
                    }
                });
            } else if (ext === 'xlsx' || ext === 'xls') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        const data = new Uint8Array(e.target.result);
                        const workbook = XLSX.read(data, { type: 'array' });
                        const firstSheetName = workbook.SheetNames[0];
                        const worksheet = workbook.Sheets[firstSheetName];
                        const json = XLSX.utils.sheet_to_json(worksheet);
                        parseAndImportRawData(json, file.name);
                    } catch(err) {
                        Swal.fire('File Error', 'Gagal mengekstrak Excel file: ' + err.message, 'error');
                    }
                };
                reader.readAsArrayBuffer(file);
            } else {
                Swal.fire('Format Tidak Didukung', 'Silakan pilih file berektensi .CSV atau .XLSX', 'warning');
            }
        }

        function parseAndImportRawData(rows, filename) {
            if (!rows || rows.length === 0) {
                Swal.fire('File Kosong', 'Tidak ada data yang dapat dibaca pada file tersebut.', 'info');
                return;
            }

            let newEntries = [];
            let duplicateCount = 0;
            let invalidDomainList = [];
            let nowStr = new Date().toISOString().slice(0, 16).replace('T', ' ');

            rows.forEach((row, idx) => {
                let name = row.Nama || row.nama || row.Name || row.name || 'User ' + (idx + 1);
                let email = row.Email || row.email || row.EmailAddress || '';
                let role = row.Role || row.role || row.Peran || row.peran || 'Mahasiswa';
                let nim_nip = row.NIM || row.nim || row.NIP || row.nip || row.ID || row.id || '';
                let token = row.Token || row.token || '';

                if (email) {
                    email = email.trim();
                    
                    // Domain Validation Check (@telkomuniversity.ac.id or @student.telkomuniversity.ac.id)
                    if (!isValidTelkomEmail(email)) {
                        invalidDomainList.push(email);
                        return;
                    }

                    // Check duplicate in current dataset
                    const exists = state.accounts.some(a => a.email.toLowerCase() === email.toLowerCase());
                    if (!exists) {
                        newEntries.push({
                            id: 'ACC-' + (Date.now() + idx).toString().slice(-5),
                            name: name.trim(),
                            email: email,
                            role: role.trim(),
                            nim_nip: nim_nip.toString().trim(),
                            token: token.trim(),
                            token_status: token.trim() ? 'ready' : 'empty',
                            password_changed: false,
                            email_status: 'belum',
                            email_sent_at: '-',
                            date_imported: nowStr
                        });
                    } else {
                        duplicateCount++;
                    }
                }
            });

            if (newEntries.length > 0) {
                state.accounts = [...newEntries, ...state.accounts];
                renderStats();
                renderTable();

                confetti({ particleCount: 60, spread: 70, origin: { y: 0.6 } });

                let alertHtml = `Berhasil mengimpor <b>${newEntries.length}</b> akun baru dari <code>${filename}</code>.`;
                if (duplicateCount > 0) {
                    alertHtml += `<br><span class="text-amber-600 font-semibold text-xs">• ${duplicateCount} data dilewati karena email duplikat.</span>`;
                }
                if (invalidDomainList.length > 0) {
                    const sampleList = invalidDomainList.slice(0, 3).join(', ') + (invalidDomainList.length > 3 ? '...' : '');
                    alertHtml += `<br><span class="text-rose-600 font-semibold text-xs">• ${invalidDomainList.length} email ditolak (bukan domain @telkomuniversity.ac.id): <code class="text-rose-700 font-mono">${sampleList}</code></span>`;
                }

                Swal.fire({
                    icon: invalidDomainList.length > 0 ? 'warning' : 'success',
                    title: invalidDomainList.length > 0 ? 'Import Selesai (Dengan Penolakan)' : 'Import Berhasil!',
                    html: alertHtml,
                    confirmColor: '#ea580c'
                });
            } else {
                let alertHtml = `Tidak ada data baru yang dapat diimpor.`;
                if (invalidDomainList.length > 0) {
                    const sampleList = invalidDomainList.slice(0, 3).join(', ') + (invalidDomainList.length > 3 ? '...' : '');
                    alertHtml += `<br><span class="text-rose-600 font-semibold text-xs">• ${invalidDomainList.length} email ditolak karena bukan domain resmi (@telkomuniversity.ac.id): <code class="text-rose-700 font-mono">${sampleList}</code></span>`;
                }
                if (duplicateCount > 0) {
                    alertHtml += `<br><span class="text-amber-600 font-semibold text-xs">• ${duplicateCount} email dilewati (duplikat).</span>`;
                }
                Swal.fire('Impor Gagal / Dilewati', alertHtml, 'error');
            }
        }

        // 3. STATS CALCULATOR
        function renderStats() {
            const total = state.accounts.length;
            const tokenReadyCount = state.accounts.filter(a => a.token_status === 'ready' && a.token.length > 0 && !a.password_changed).length;
            const pwdChangedCount = state.accounts.filter(a => a.password_changed).length;
            const sentCount = state.accounts.filter(a => a.email_status === 'terkirim').length;
            const pendingCount = state.accounts.filter(a => a.email_status !== 'terkirim').length;

            const dosenCount = state.accounts.filter(a => a.role === 'Dosen').length;
            const mhsCount = state.accounts.filter(a => a.role === 'Mahasiswa').length;

            const tokenPct = total > 0 ? Math.round(((tokenReadyCount + pwdChangedCount) / total) * 100) : 0;
            const sentPct = total > 0 ? Math.round((sentCount / total) * 100) : 0;

            document.getElementById('stat-total').innerText = total;
            document.getElementById('stat-total-desc').innerText = `${dosenCount} Dosen, ${mhsCount} Mahasiswa`;

            document.getElementById('stat-token').innerHTML = `${tokenReadyCount + pwdChangedCount} <span class="text-xs font-semibold text-cyan-600 font-normal">(${tokenPct}%)</span>`;
            document.getElementById('stat-token-desc').innerText = `${tokenReadyCount} token ready, ${pwdChangedCount} custom password (locked)`;

            document.getElementById('stat-sent').innerHTML = `${sentCount} <span class="text-xs font-semibold text-emerald-600 font-normal">(${sentPct}%)</span>`;
            document.getElementById('stat-sent-desc').innerText = `${sentCount} email berhasil dikirim`;

            document.getElementById('stat-pending').innerText = pendingCount;
        }

        // 4. RENDER TABLE DATA
        function getFilteredAccounts() {
            return state.accounts.filter(acc => {
                // Search Filter
                const q = state.searchQuery.toLowerCase();
                const matchQuery = !q || acc.name.toLowerCase().includes(q) || acc.email.toLowerCase().includes(q) || acc.token.toLowerCase().includes(q) || acc.nim_nip.toLowerCase().includes(q);

                // Role Filter
                const matchRole = !state.filterRole || acc.role === state.filterRole;

                // Token Status Filter
                const matchToken = !state.filterTokenStatus || 
                    (state.filterTokenStatus === 'ready' && acc.token_status === 'ready' && !acc.password_changed) ||
                    (state.filterTokenStatus === 'empty' && acc.token_status === 'empty' && !acc.password_changed) ||
                    (state.filterTokenStatus === 'password_changed' && acc.password_changed === true);

                // Email Status Filter
                const matchEmail = !state.filterEmailStatus || acc.email_status === state.filterEmailStatus;

                return matchQuery && matchRole && matchToken && matchEmail;
            });
        }

        function renderTable() {
            const tbody = document.getElementById('accounts-table-body');
            const filtered = getFilteredAccounts();

            document.getElementById('total-rows-count').innerText = filtered.length;

            // Pagination slice
            const totalPages = Math.ceil(filtered.length / state.pageSize) || 1;
            if (state.currentPage > totalPages) state.currentPage = totalPages;
            
            const startIdx = (state.currentPage - 1) * state.pageSize;
            const pageData = filtered.slice(startIdx, startIdx + state.pageSize);

            if (pageData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="p-8 text-center text-slate-400">
                            <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300"></i>
                            <p class="font-medium text-xs">Tidak ada data akun yang ditemukan.</p>
                        </td>
                    </tr>
                `;
                renderPagination(totalPages);
                return;
            }

            let html = '';
            pageData.forEach((acc, idx) => {
                const isSelected = state.selectedIds.includes(acc.id);
                const rowNo = startIdx + idx + 1;

                // Role Badge Style
                let roleClass = 'bg-slate-100 text-slate-700 border-slate-200';
                if (acc.role === 'Dosen') roleClass = 'bg-blue-50 text-blue-700 border-blue-200';
                else if (acc.role === 'Mahasiswa') roleClass = 'bg-amber-50 text-amber-700 border-amber-200';
                else if (acc.role === 'Admin') roleClass = 'bg-purple-50 text-purple-700 border-purple-200';

                // Token Badge Column
                let tokenHtml = '';
                if (acc.password_changed) {
                    tokenHtml = `
                        <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg shadow-2xs w-[145px] cursor-pointer hover:bg-emerald-100 transition-colors" onclick="showProtectedAccountInfo('${acc.id}')" title="Password diubah mandiri pada ${acc.password_changed_at || 'portal'}">
                            <i class="fa-solid fa-user-lock text-emerald-600 text-xs"></i>
                            <span>Custom Password</span>
                        </span>
                    `;
                } else if (acc.token && acc.token.length > 0) {
                    tokenHtml = `
                        <div class="inline-flex items-center justify-between w-[145px] px-2.5 py-1 bg-slate-900 border border-slate-700 rounded-lg text-xs font-mono font-bold text-cyan-400 shadow-2xs">
                            <span class="tracking-widest cursor-pointer" onclick="copyToClipboard('${acc.token}')" title="Klik untuk Salin Token">${acc.token}</span>
                            <button onclick="copyToClipboard('${acc.token}')" class="text-slate-400 hover:text-white transition-colors p-0.5" title="Salin Token">
                                <i class="fa-regular fa-copy text-xs"></i>
                            </button>
                        </div>
                    `;
                } else {
                    tokenHtml = `
                        <button onclick="generateIndividualToken('${acc.id}')" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-brand-700 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-lg transition-all w-[145px] shadow-2xs">
                            <i class="fa-solid fa-bolt text-brand-600 text-xs"></i>
                            <span>Generate Token</span>
                        </button>
                    `;
                }

                // Status Token Column
                let tokenStatusBadge = '';
                if (acc.password_changed) {
                    tokenStatusBadge = `<span class="inline-flex items-center justify-center gap-1 w-[100px] py-1 text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full cursor-pointer hover:bg-indigo-100 transition-colors" onclick="showProtectedAccountInfo('${acc.id}')" title="Password diubah oleh pengguna">
                        <i class="fa-solid fa-lock text-[10px]"></i> Protected
                    </span>`;
                } else if (acc.token_status === 'ready') {
                    tokenStatusBadge = `<span class="inline-flex items-center justify-center gap-1 w-[100px] py-1 text-[11px] font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200 rounded-full">
                        <i class="fa-solid fa-circle-check text-[10px]"></i> Ready
                    </span>`;
                } else {
                    tokenStatusBadge = `<span class="inline-flex items-center justify-center gap-1 w-[100px] py-1 text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200 rounded-full">
                        <i class="fa-solid fa-circle-minus text-[10px]"></i> Kosong
                    </span>`;
                }

                // Email Status Badge
                let emailBadge = '';
                if (acc.email_status === 'terkirim') {
                    emailBadge = `<span class="inline-flex items-center justify-center gap-1.5 w-[140px] py-1 text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i> Terkirim (${acc.email_sent_at.split(' ')[1] || ''})
                    </span>`;
                } else if (acc.email_status === 'mengirim') {
                    emailBadge = `<span class="inline-flex items-center justify-center gap-1.5 w-[140px] py-1 text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 rounded-full animate-pulse">
                        <i class="fa-solid fa-spinner fa-spin text-blue-500"></i> Mengirim...
                    </span>`;
                } else if (acc.email_status === 'gagal') {
                    emailBadge = `<span class="inline-flex items-center justify-center gap-1.5 w-[140px] py-1 text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 rounded-full">
                        <i class="fa-solid fa-circle-xmark text-rose-500"></i> Gagal Kirim
                    </span>`;
                } else {
                    emailBadge = `<span class="inline-flex items-center justify-center gap-1.5 w-[140px] py-1 text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 rounded-full">
                        <i class="fa-solid fa-clock text-slate-400"></i> Belum Terkirim
                    </span>`;
                }

                html += `
                    <tr class="hover:bg-orange-50/40 transition-colors ${isSelected ? 'bg-orange-50/60' : ''}">
                        <td class="p-3.5 text-center">
                            <input type="checkbox" value="${acc.id}" ${isSelected ? 'checked' : ''} onchange="toggleSelectRow('${acc.id}', this.checked)" class="rounded text-brand-600 focus:ring-brand-500">
                        </td>
                        <td class="p-3.5 text-center text-slate-400 font-mono whitespace-nowrap">${rowNo}</td>
                        <td class="p-3.5 whitespace-nowrap">
                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                <span>${acc.name}</span>
                                ${acc.password_changed ? '<i class="fa-solid fa-circle-check text-emerald-500 text-xs" title="Password diubah mandiri oleh pengguna"></i>' : ''}
                            </div>
                            <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                <span>${acc.email}</span>
                                <span class="px-2 py-0.5 text-[10px] font-semibold border rounded-md ${roleClass}">${acc.role}</span>
                            </div>
                        </td>
                        <td class="p-3.5 font-mono text-slate-600 whitespace-nowrap">${acc.nim_nip || '-'}</td>
                        <td class="p-3.5 text-center whitespace-nowrap">${tokenHtml}</td>
                        <td class="p-3.5 text-center whitespace-nowrap">${tokenStatusBadge}</td>
                        <td class="p-3.5 text-center whitespace-nowrap">${emailBadge}</td>
                        <td class="p-3.5 text-center text-slate-500 font-mono text-[11px] whitespace-nowrap">${acc.date_imported || '-'}</td>
                        <td class="p-3.5 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="sendIndividualEmail('${acc.id}')" class="w-7 h-7 flex items-center justify-center ${acc.password_changed ? 'text-slate-300 hover:text-indigo-600 hover:bg-indigo-50' : 'text-emerald-600 hover:bg-emerald-50'} rounded-lg transition-all" title="${acc.password_changed ? 'Akun Telah Mengubah Password (Protected)' : 'Kirim Email Akun Ini'}">
                                    <i class="fa-solid ${acc.password_changed ? 'fa-user-shield' : 'fa-paper-plane'} text-xs"></i>
                                </button>
                                <button onclick="openEditAccountModal('${acc.id}')" class="w-7 h-7 flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit Akun">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="deleteSingleAccount('${acc.id}')" class="w-7 h-7 flex items-center justify-center text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Hapus Akun">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            renderPagination(totalPages);
            updateSelectedCounter();
        }

        function renderPagination(totalPages) {
            const container = document.getElementById('pagination-controls');
            let html = '';

            html += `
                <button onclick="goToPage(${state.currentPage - 1})" ${state.currentPage === 1 ? 'disabled' : ''} class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-600 hover:bg-slate-200 disabled:opacity-40">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                </button>
            `;

            for (let i = 1; i <= totalPages; i++) {
                if (i === state.currentPage) {
                    html += `<button class="px-2.5 py-1 text-xs font-bold rounded bg-brand-600 text-white">${i}</button>`;
                } else {
                    html += `<button onclick="goToPage(${i})" class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-600 hover:bg-slate-200">${i}</button>`;
                }
            }

            html += `
                <button onclick="goToPage(${state.currentPage + 1})" ${state.currentPage === totalPages || totalPages === 0 ? 'disabled' : ''} class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-600 hover:bg-slate-200 disabled:opacity-40">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            `;

            container.innerHTML = html;
        }

        function goToPage(page) {
            if (page < 1) return;
            state.currentPage = page;
            renderTable();
        }

        function changePageSize(size) {
            state.pageSize = parseInt(size);
            state.currentPage = 1;
            renderTable();
        }

        // 5. SEARCH & FILTER HANDLERS
        function handleSearch(val) {
            state.searchQuery = val;
            state.currentPage = 1;
            renderTable();
        }

        function handleFilter() {
            state.filterRole = document.getElementById('filter-role').value;
            state.filterTokenStatus = document.getElementById('filter-token').value;
            state.filterEmailStatus = document.getElementById('filter-email').value;
            state.currentPage = 1;
            renderTable();
        }

        // 6. SELECTION HANDLERS
        function toggleSelectAll(checkbox) {
            const filtered = getFilteredAccounts();
            if (checkbox.checked) {
                state.selectedIds = filtered.map(a => a.id);
            } else {
                state.selectedIds = [];
            }
            renderTable();
        }

        function toggleSelectRow(id, checked) {
            if (checked) {
                if (!state.selectedIds.includes(id)) state.selectedIds.push(id);
            } else {
                state.selectedIds = state.selectedIds.filter(item => item !== id);
            }
            updateSelectedCounter();
        }

        function updateSelectedCounter() {
            const counter = document.getElementById('selected-rows-count');
            if (state.selectedIds.length > 0) {
                counter.innerText = `(${state.selectedIds.length} terpilih)`;
                counter.classList.remove('hidden');
            } else {
                counter.classList.add('hidden');
            }
        }

        // 7. INDIVIDUAL & BULK TOKEN GENERATION
        function generateIndividualToken(id) {
            const acc = state.accounts.find(a => a.id === id);
            if (acc) {
                const newToken = generate8CharToken();
                acc.token = newToken;
                acc.token_status = 'ready';

                renderStats();
                renderTable();

                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
                toast.fire({
                    icon: 'success',
                    title: `Token [ ${newToken} ] berhasil dibuat untuk ${acc.name}`
                });
            }
        }

        function bulkGenerateTokenSelected() {
            if (state.selectedIds.length === 0) {
                Swal.fire('Pilih Akun', 'Silakan centang minimal satu akun untuk generate token.', 'warning');
                return;
            }

            let generatedCount = 0;
            let skippedProtected = 0;
            state.accounts.forEach(acc => {
                if (state.selectedIds.includes(acc.id)) {
                    if (acc.password_changed) {
                        skippedProtected++;
                        return;
                    }
                    if (!acc.token || acc.token.length === 0) {
                        acc.token = generate8CharToken();
                        acc.token_status = 'ready';
                        generatedCount++;
                    }
                }
            });

            renderStats();
            renderTable();

            confetti({ particleCount: 50, spread: 60 });
            let resultHtml = `Berhasil membangkitkan token 8 karakter untuk <b>${generatedCount}</b> akun terpilih.`;
            if (skippedProtected > 0) {
                resultHtml += `<br><span class="text-indigo-600 font-semibold text-xs">• ${skippedProtected} akun dilewati karena sudah mengubah password (Protected).</span>`;
            }
            Swal.fire({
                icon: 'success',
                title: 'Token Generated!',
                html: resultHtml,
                confirmColor: '#ea580c'
            });
        }

        function bulkGenerateTokenAll() {
            const emptyAccounts = state.accounts.filter(a => (!a.token || a.token.length === 0) && !a.password_changed);
            if (emptyAccounts.length === 0) {
                Swal.fire('Semua Sudah Memiliki Token', 'Seluruh akun dalam sistem telah memiliki token akses 8 karakter atau telah mengubah password.', 'info');
                return;
            }

            emptyAccounts.forEach(acc => {
                acc.token = generate8CharToken();
                acc.token_status = 'ready';
            });

            renderStats();
            renderTable();

            confetti({ particleCount: 70, spread: 80 });
            Swal.fire({
                icon: 'success',
                title: 'Bulk Token Success!',
                text: `Berhasil membangkitkan token 8-Karakter untuk ${emptyAccounts.length} akun yang belum memiliki token.`,
                confirmColor: '#ea580c'
            });
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
                toast.fire({
                    icon: 'success',
                    title: `Token [ ${text} ] disalin ke clipboard!`
                });
            });
        }

        // 8. EMAIL DISPATCHER & SIMULATION MODAL
        function sendIndividualEmail(id) {
            const acc = state.accounts.find(a => a.id === id);
            if (!acc) return;

            if (!acc.token || acc.token.length === 0) {
                Swal.fire({
                    title: 'Token Belum Ada',
                    text: `Akun ${acc.name} belum memiliki token. Generate token secara otomatis sekarang?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Generate & Kirim',
                    confirmColor: '#ea580c'
                }).then((res) => {
                    if (res.isConfirmed) {
                        acc.token = generate8CharToken();
                        acc.token_status = 'ready';
                        renderStats();
                        renderTable();
                        startEmailDispatchSimulation([acc]);
                    }
                });
                return;
            }

            startEmailDispatchSimulation([acc]);
        }

        function bulkSendEmailSelected() {
            if (state.selectedIds.length === 0) {
                Swal.fire('Pilih Akun', 'Silakan centang minimal satu akun untuk pengiriman email.', 'warning');
                return;
            }

            const targetAccounts = state.accounts.filter(a => state.selectedIds.includes(a.id));
            
            // Auto generate token for selected accounts missing tokens
            targetAccounts.forEach(acc => {
                if (!acc.token || acc.token.length === 0) {
                    acc.token = generate8CharToken();
                    acc.token_status = 'ready';
                }
            });

            startEmailDispatchSimulation(targetAccounts);
        }

        function startEmailDispatchSimulation(targetList) {
            const modal = document.getElementById('modal-send-progress');
            const subTitle = document.getElementById('send-modal-subtitle');
            const progressStatus = document.getElementById('send-progress-status');
            const progressPercent = document.getElementById('send-progress-percent');
            const progressBar = document.getElementById('send-progress-bar');
            const terminalLog = document.getElementById('send-terminal-log');
            const closeBtn = document.getElementById('send-modal-close-btn');
            const countSummary = document.getElementById('send-count-summary');

            modal.classList.remove('hidden');
            subTitle.innerText = `Mengirim email ke ${targetList.length} akun...`;
            progressPercent.innerText = '0%';
            progressBar.style.width = '0%';
            closeBtn.disabled = true;
            closeBtn.className = 'px-4 py-2 text-xs font-semibold text-white bg-slate-400 cursor-not-allowed rounded-lg transition-all';
            terminalLog.innerHTML = `<div class="text-slate-500">[SYSTEM] Memulai Email Dispatcher Engine...</div>`;

            let current = 0;
            const total = targetList.length;

            const interval = setInterval(() => {
                if (current >= total) {
                    clearInterval(interval);

                    progressPercent.innerText = '100%';
                    progressBar.style.width = '100%';
                    progressStatus.innerText = 'Selesai! Seluruh email telah diproses.';
                    countSummary.innerText = `${total} / ${total} Terkirim`;

                    terminalLog.innerHTML += `<div class="text-emerald-400 font-bold">[SUCCESS] Dispatch completed! ${total} emails sent cleanly via SMTP gateway.</div>`;
                    terminalLog.scrollTop = terminalLog.scrollHeight;

                    closeBtn.disabled = false;
                    closeBtn.className = 'px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm cursor-pointer';

                    renderStats();
                    renderTable();
                    confetti({ particleCount: 80, spread: 90 });
                    return;
                }

                const acc = targetList[current];
                acc.email_status = 'mengirim';
                renderTable();

                setTimeout(() => {
                    acc.email_status = 'terkirim';
                    acc.email_sent_at = new Date().toISOString().slice(0, 16).replace('T', ' ');

                    current++;
                    const pct = Math.round((current / total) * 100);
                    progressPercent.innerText = pct + '%';
                    progressBar.style.width = pct + '%';
                    progressStatus.innerText = `Mengirim ke: ${acc.email}`;
                    countSummary.innerText = `${current} / ${total} Diproses`;

                    terminalLog.innerHTML += `
                        <div class="text-slate-300">
                            <span class="text-slate-500">[${new Date().toLocaleTimeString()}]</span>
                            <span class="text-cyan-400">EMAIL SENT</span> -> 
                            <strong class="text-white">${acc.email}</strong> 
                            <span class="text-amber-300">[Token: ${acc.token}]</span>
                        </div>
                    `;
                    terminalLog.scrollTop = terminalLog.scrollHeight;

                    renderStats();
                    renderTable();
                }, 300);

            }, 700);
        }

        function closeSendProgressModal() {
            document.getElementById('modal-send-progress').classList.add('hidden');
        }

        // 9. EMAIL TEMPLATE MODAL & LIVE PREVIEW
        function openEmailTemplateModal() {
            document.getElementById('modal-template').classList.remove('hidden');
            document.getElementById('template-subject').value = state.emailTemplate.subject;
            document.getElementById('template-body').value = state.emailTemplate.body;
            updateEmailPreview();
        }

        function closeEmailTemplateModal() {
            document.getElementById('modal-template').classList.add('hidden');
        }

        function initEmailTemplateFields() {
            const subjInput = document.getElementById('template-subject');
            const bodyInput = document.getElementById('template-body');

            if (subjInput && bodyInput) {
                subjInput.addEventListener('input', updateEmailPreview);
                bodyInput.addEventListener('input', updateEmailPreview);
            }
        }

        function insertTag(tag) {
            const bodyInput = document.getElementById('template-body');
            bodyInput.value += tag;
            updateEmailPreview();
        }

        function updateEmailPreview() {
            const subjVal = document.getElementById('template-subject').value;
            const bodyVal = document.getElementById('template-body').value;

            // Replace mock tags with sample values
            let renderedBody = bodyVal
                .replace(/{NAMA}/g, '<strong>Budi Santoso</strong>')
                .replace(/{EMAIL}/g, '<u>budi.santoso@student.telkomuniversity.ac.id</u>')
                .replace(/{TOKEN}/g, '<span class="token-badge px-2 py-0.5 rounded text-cyan-300 font-bold">X8K9P2W4</span>')
                .replace(/{ROLE}/g, '<span class="bg-amber-100 text-amber-800 font-semibold px-2 py-0.5 rounded">Mahasiswa</span>')
                .replace(/{NIM_NIP}/g, '<code>1301210045</code>')
                .replace(/\n/g, '<br>');

            let renderedSubj = subjVal.replace(/{TOKEN}/g, 'X8K9P2W4');

            document.getElementById('preview-subject-text').innerText = renderedSubj;
            document.getElementById('preview-html-body').innerHTML = renderedBody;
        }

        function saveEmailTemplate() {
            state.emailTemplate.subject = document.getElementById('template-subject').value;
            state.emailTemplate.body = document.getElementById('template-body').value;
            closeEmailTemplateModal();

            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            });
            toast.fire({
                icon: 'success',
                title: 'Template Email berhasil disimpan!'
            });
        }

        // 10. ADD / EDIT MANUAL ACCOUNT MODAL
        function openAddAccountModal() {
            document.getElementById('modal-account-title').innerText = 'Tambah Akun Manual';
            document.getElementById('account-id').value = '';
            document.getElementById('account-form').reset();
            document.getElementById('acc-token').value = generate8CharToken();
            document.getElementById('modal-account').classList.remove('hidden');
        }

        function openEditAccountModal(id) {
            const acc = state.accounts.find(a => a.id === id);
            if (!acc) return;

            document.getElementById('modal-account-title').innerText = 'Edit Data Akun';
            document.getElementById('account-id').value = acc.id;
            document.getElementById('acc-name').value = acc.name;
            document.getElementById('acc-email').value = acc.email;
            document.getElementById('acc-role').value = acc.role;
            document.getElementById('acc-nim-nip').value = acc.nim_nip || '';
            
            const tokenInput = document.getElementById('acc-token');
            tokenInput.value = acc.password_changed ? '••• Custom Password (Protected) •••' : (acc.token || '');
            tokenInput.disabled = acc.password_changed;
            if (acc.password_changed) {
                tokenInput.classList.add('bg-indigo-50', 'text-indigo-600', 'cursor-not-allowed');
            } else {
                tokenInput.classList.remove('bg-indigo-50', 'text-indigo-600', 'cursor-not-allowed');
            }

            document.getElementById('modal-account').classList.remove('hidden');
        }

        function closeAccountModal() {
            document.getElementById('modal-account').classList.add('hidden');
        }

        function generateTokenForInput() {
            document.getElementById('acc-token').value = generate8CharToken();
        }

        function saveAccountForm(e) {
            e.preventDefault();
            const id = document.getElementById('account-id').value;
            const name = document.getElementById('acc-name').value.trim();
            const emailInput = document.getElementById('acc-email');
            const email = emailInput.value.trim();
            const role = document.getElementById('acc-role').value;
            const nim_nip = document.getElementById('acc-nim-nip').value.trim();
            const token = document.getElementById('acc-token').value.trim();

            // Strict Domain Validation (@telkomuniversity.ac.id or @student.telkomuniversity.ac.id)
            if (!isValidTelkomEmail(email)) {
                emailInput.focus();
                emailInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-300');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Domain Email Ditolak!',
                    html: `Alamat email <code>${email}</code> ditolak.<br><br><b>Alasan:</b> Email harus menggunakan domain resmi <b>@telkomuniversity.ac.id</b> atau <b>@student.telkomuniversity.ac.id</b>.`,
                    confirmColor: '#e11d48'
                });
                return;
            }

            emailInput.classList.remove('border-rose-500', 'ring-2', 'ring-rose-300');

            if (id) {
                // Edit
                const acc = state.accounts.find(a => a.id === id);
                if (acc) {
                    acc.name = name;
                    acc.email = email;
                    acc.role = role;
                    acc.nim_nip = nim_nip;
                    acc.token = token;
                    acc.token_status = token.length > 0 ? 'ready' : 'empty';
                }
            } else {
                // New
                const newAcc = {
                    id: 'ACC-' + Date.now().toString().slice(-5),
                    name: name,
                    email: email,
                    role: role,
                    nim_nip: nim_nip,
                    token: token,
                    token_status: token.length > 0 ? 'ready' : 'empty',
                    email_status: 'belum',
                    email_sent_at: '-',
                    date_imported: new Date().toISOString().slice(0, 16).replace('T', ' ')
                };
                state.accounts.unshift(newAcc);
            }

            closeAccountModal();
            renderStats();
            renderTable();

            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            toast.fire({
                icon: 'success',
                title: 'Data akun berhasil disimpan!'
            });
        }

        // 11. DELETE HANDLERS
        function deleteSingleAccount(id) {
            Swal.fire({
                title: 'Hapus Akun?',
                text: 'Data akun akan dihapus dari sistem import.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmColor: '#e11d48'
            }).then((res) => {
                if (res.isConfirmed) {
                    state.accounts = state.accounts.filter(a => a.id !== id);
                    state.selectedIds = state.selectedIds.filter(i => i !== id);
                    renderStats();
                    renderTable();
                }
            });
        }

        function bulkDeleteSelected() {
            if (state.selectedIds.length === 0) {
                Swal.fire('Pilih Akun', 'Centang akun yang ingin dihapus.', 'warning');
                return;
            }

            Swal.fire({
                title: `Hapus ${state.selectedIds.length} Akun?`,
                text: 'Data terpilih akan dihapus permanen dari memori browser.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Semua Terpilih',
                confirmColor: '#e11d48'
            }).then((res) => {
                if (res.isConfirmed) {
                    state.accounts = state.accounts.filter(a => !state.selectedIds.includes(a.id));
                    state.selectedIds = [];
                    renderStats();
                    renderTable();
                }
            });
        }

        // 12. EXPORT & SAMPLE TEMPLATE DOWNLOADERS
        function exportData(format) {
            const dataToExport = state.selectedIds.length > 0 ? 
                state.accounts.filter(a => state.selectedIds.includes(a.id)) : 
                getFilteredAccounts();

            if (dataToExport.length === 0) {
                Swal.fire('Data Kosong', 'Tidak ada data untuk diexport.', 'info');
                return;
            }

            const cleanRows = dataToExport.map((a, idx) => ({
                'No': idx + 1,
                'Nama Lengkap': a.name,
                'Email': a.email,
                'Role': a.role,
                'NIM/NIP': a.nim_nip,
                'Token Access (8-Char)': a.token,
                'Status Token': a.token_status,
                'Status Email': a.email_status,
                'Tgl Email Terkirim': a.email_sent_at,
                'Tgl Import': a.date_imported
            }));

            if (format === 'csv') {
                const csv = Papa.unparse(cleanRows);
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', `ifik_export_email_token_${Date.now()}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else if (format === 'xlsx') {
                const worksheet = XLSX.utils.json_to_sheet(cleanRows);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Email & Token Data');
                XLSX.writeFile(workbook, `ifik_export_email_token_${Date.now()}.xlsx`);
            }
        }

        function downloadSampleTemplate(type) {
            const sampleData = [
                { 'Nama': 'Dr. Ir. Ahmad Sudrajat, M.T.', 'Email': 'ahmad.sudrajat@telkomuniversity.ac.id', 'Role': 'Dosen', 'NIM': '197804122005011002' },
                { 'Nama': 'Budi Santoso', 'Email': 'budi.santoso@student.telkomuniversity.ac.id', 'Role': 'Mahasiswa', 'NIM': '1301210045' },
                { 'Nama': 'Siti Rahmawati, S.Kom.', 'Email': 'siti.rahmawati@telkomuniversity.ac.id', 'Role': 'Staf', 'NIM': '2019080104' },
                { 'Nama': 'Dewi Lestari', 'Email': 'dewi.lestari@student.telkomuniversity.ac.id', 'Role': 'Mahasiswa', 'NIM': '1301210088' },
                { 'Nama': 'Prof. Dr. Hendra Wijaya', 'Email': 'hendra.wijaya@telkomuniversity.ac.id', 'Role': 'Dosen', 'NIM': '196503151990021001' }
            ];

            if (type === 'csv') {
                const csv = Papa.unparse(sampleData);
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.setAttribute('download', 'template_import_email_telkom.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                const worksheet = XLSX.utils.json_to_sheet(sampleData);
                // Formatting column widths for professional Excel preview
                worksheet['!cols'] = [
                    { wch: 32 }, // Nama
                    { wch: 48 }, // Email
                    { wch: 16 }, // Role
                    { wch: 22 }  // NIM/NIP
                ];
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, 'Template Import');
                XLSX.writeFile(workbook, 'template_import_email_telkom.xlsx');
            }
        }

        // 13. PROTECTED ACCOUNT INFO POPUP
        function showProtectedAccountInfo(id) {
            const acc = state.accounts.find(a => a.id === id);
            if (!acc) return;

            Swal.fire({
                icon: 'info',
                title: '<i class="fa-solid fa-user-lock text-indigo-500"></i> Akun Dilindungi',
                html: `
                    <div class="text-left text-xs space-y-2 mt-2">
                        <p><strong>Nama:</strong> ${acc.name}</p>
                        <p><strong>Email:</strong> ${acc.email}</p>
                        <p><strong>Role:</strong> ${acc.role}</p>
                        <hr class="border-slate-200">
                        <div class="bg-indigo-50 border border-indigo-200 p-3 rounded-lg">
                            <p class="font-bold text-indigo-700"><i class="fa-solid fa-shield-halved mr-1"></i> Status: Password Diubah Mandiri</p>
                            <p class="text-indigo-600 mt-1">Mahasiswa/pengguna ini telah mengubah password awal (token) dengan password buatan sendiri pada <b>${acc.password_changed_at || 'waktu tidak tercatat'}</b>.</p>
                        </div>
                        <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg">
                            <p class="font-bold text-amber-700"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Perhatian Admin</p>
                            <p class="text-amber-600 mt-1">Token awal akun ini sudah <b>tidak berlaku lagi</b>. Admin <b>tidak dapat mereset</b> atau mengubah password pengguna ini. Hanya pengguna yang bisa mengelola passwordnya.</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Mengerti',
                confirmColor: '#6366f1',
                width: 480
            });
        }
    </script>
</body>
</html>
