<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Persetujuan Resmi & Surat QR - Ka. Ur / Kepala Lab') ?></title>
    
    <!-- Google Fonts & FontAwesome & SweetAlert2 -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --bg-color: #f8fafc;
            --surface-color: #ffffff;
            --text-color: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary: #16a34a; /* Green for Kaur Official ACC */
            --primary-hover: #15803d;
            --accent-orange: #ea580c;
            --accent-blue: #2563eb;
            --accent-red: #dc2626;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            padding: 24px 32px 100px 76px;
        }

        .main-container { max-width: 1440px; margin: 0 auto; }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .header-title-wrap h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-title-wrap h1 .role-badge {
            font-size: 0.72rem;
            font-weight: 700;
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 3px 10px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .header-title-wrap p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Stat Cards */
        /* =========================================================
           3D CLAYMORPHIC / GLASS STAT CARDS (IMPORT-EMAIL STYLE)
           ========================================================= */
        .stat-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .stat-card-highlight {
            position: relative;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 20px;
            padding: 20px 22px 16px 22px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            backdrop-filter: blur(12px);
            overflow: hidden;
            cursor: default;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card-highlight:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 30px -8px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(0,0,0,0.02);
        }

        /* Ambient Glow Backdrop */
        .stat-card-glow {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }
        .stat-card-glow .glow-bubble {
            position: absolute;
            bottom: -40px;
            right: -40px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--card-accent-soft, rgba(234, 88, 12, 0.12));
            filter: blur(28px);
            transition: transform 0.5s ease;
        }
        .stat-card-highlight:hover .glow-bubble {
            transform: scale(1.4);
        }

        /* Top Row Content */
        .stat-card-top {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-card-meta {
            flex: 1;
            min-width: 0;
        }

        .stat-card-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            margin-bottom: 4px;
            transition: color 0.2s ease;
        }
        .stat-card-highlight:hover .stat-card-label {
            color: var(--card-accent, #ea580c);
        }

        .stat-card-val {
            font-size: 1.85rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
            letter-spacing: -0.02em;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .stat-card-percent {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--card-accent, #ea580c);
        }

        .stat-card-desc {
            font-size: 0.76rem;
            font-weight: 500;
            color: #64748b;
            margin-top: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* 3D Claymorphic Icon Box */
        .stat-card-3d-icon {
            position: relative;
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            background: var(--card-icon-bg, linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%));
            border: 1px solid var(--card-icon-border, #fed7aa);
            color: var(--card-accent, #ea580c);
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.9), 0 6px 16px var(--card-icon-shadow, rgba(234, 88, 12, 0.16));
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease;
        }
        .stat-card-highlight:hover .stat-card-3d-icon {
            transform: rotate(6deg) scale(1.1);
        }

        /* Bottom Row Divider & Pulse Dots */
        .stat-card-bottom {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px solid rgba(241, 245, 249, 0.9);
        }

        .stat-card-bar {
            width: 38%;
            height: 2.5px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--card-accent, #ea580c), transparent);
            transition: width 0.4s ease;
        }
        .stat-card-highlight:hover .stat-card-bar {
            width: 65%;
        }

        .stat-card-dots {
            display: flex;
            align-items: center;
            gap: 4px;
            opacity: 0.55;
            transition: opacity 0.3s ease;
        }
        .stat-card-highlight:hover .stat-card-dots {
            opacity: 1;
        }

        .stat-card-dots span {
            width: 4.5px;
            height: 4.5px;
            border-radius: 50%;
            background: var(--card-accent, #ea580c);
            display: inline-block;
        }

        /* Theme Variants */
        .theme-orange {
            --card-accent: #ea580c;
            --card-accent-soft: rgba(234, 88, 12, 0.14);
            --card-icon-bg: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            --card-icon-border: #fed7aa;
            --card-icon-shadow: rgba(234, 88, 12, 0.18);
        }
        .theme-amber {
            --card-accent: #d97706;
            --card-accent-soft: rgba(245, 158, 11, 0.14);
            --card-icon-bg: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            --card-icon-border: #fde68a;
            --card-icon-shadow: rgba(245, 158, 11, 0.18);
        }
        .theme-sky {
            --card-accent: #0284c7;
            --card-accent-soft: rgba(2, 132, 199, 0.14);
            --card-icon-bg: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            --card-icon-border: #bae6fd;
            --card-icon-shadow: rgba(2, 132, 199, 0.18);
        }
        .theme-emerald {
            --card-accent: #059669;
            --card-accent-soft: rgba(16, 185, 129, 0.14);
            --card-icon-bg: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            --card-icon-border: #a7f3d0;
            --card-icon-shadow: rgba(16, 185, 129, 0.18);
        }
        .theme-rose {
            --card-accent: #e11d48;
            --card-accent-soft: rgba(225, 29, 72, 0.14);
            --card-icon-bg: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
            --card-icon-border: #fecdd3;
            --card-icon-shadow: rgba(225, 29, 72, 0.18);
        }

        /* Toolbar */
        .toolbar-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }

        .filter-pills-wrap { display: flex; gap: 8px; flex-wrap: wrap; }
        .filter-pill {
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            border: 1.5px solid var(--border-color);
            background: #ffffff;
            color: #475569;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .filter-pill:hover { border-color: #cbd5e1; background: #f8fafc; }
        .filter-pill.active {
            background: #166534;
            border-color: #166534;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(22, 101, 52, 0.2);
        }

        .filter-pill.active .pill-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .pill-count {
            background: #f1f5f9;
            color: #475569;
            border-radius: 999px;
            padding: 1px 7px;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .search-box-wrap { position: relative; min-width: 260px; }
        .search-box-wrap input {
            width: 100%;
            padding: 9px 16px 9px 38px;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--border-color);
            font-size: 0.84rem;
            font-family: inherit;
            color: var(--text-color);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .search-box-wrap input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12);
        }
        .search-box-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.85rem;
        }

        /* Table */
        .table-card {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }

        .table-responsive { width: 100%; overflow-x: auto; }
        table.kaur-table { width: 100%; border-collapse: collapse; text-align: left; }
        table.kaur-table th {
            background: #f8fafc;
            padding: 14px 18px;
            font-size: 0.76rem;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1.5px solid var(--border-color);
            white-space: nowrap;
        }

        table.kaur-table td {
            padding: 14px 18px;
            font-size: 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        table.kaur-table tr:hover td { background: #f8fafc; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 11px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .status-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }

        .btn-action-qr {
            background: #16a34a;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-action-qr:hover { background: #15803d; }

        .btn-action-acc {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background 0.2s;
        }
        .btn-action-acc:hover { background: #1d4ed8; }

        .btn-action-rej {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fca5a5;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-action-rej:hover { background: #dc2626; color: #fff; }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 100000;
            backdrop-filter: blur(4px);
            padding: 16px;
        }
        .modal-overlay.active { display: flex; }
        .modal-card {
            background: #ffffff;
            width: 100%;
            max-width: 500px;
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.2);
            overflow: hidden;
        }
        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-body { padding: 24px; }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #f8fafc;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--border-color);
            font-size: 0.85rem;
            font-family: inherit;
        }
    </style>
</head>
<body>

    <!-- =========================================================
         CURVED SIDEBAR INTEGRATION (ROLE KAUR / KA LAB)
         ========================================================= -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <div class="main-container">
        
        <!-- Header -->
        <div class="page-header">
            <div class="header-title-wrap">
                <h1>
                    <span>Persetujuan Resmi & Surat Ber-QR Code</span>
                    <span class="role-badge">Kaur / Ka Lab</span>
                </h1>
                <p>Persetujuan tingkat akhir untuk peminjaman laboratorium dan penerbitan surat resmi ber-QR Code tervalidasi.</p>
            </div>
        </div>

        <?php
            $totalCount = count($peminjaman);
            $readyForKaurCount = 0;
            $approvedKaurCount = 0;
            $rejectedCount = 0;

            foreach ($peminjaman as $p) {
                $st = $p->status;
                if (stripos($st, 'Laboran') !== false || $st === 'Pending') $readyForKaurCount++;
                if (stripos($st, 'Ka. Ur') !== false || stripos($st, 'Kaur') !== false) $approvedKaurCount++;
                if ($st === 'Ditolak') $rejectedCount++;
            }
        ?>

        <!-- Stat Cards Grid (3D Claymorphic Highlight Style) -->
        <div class="stat-cards-grid">
            <!-- 1. Total Permohonan -->
            <div class="stat-card-highlight theme-orange">
                <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                <div class="stat-card-top">
                    <div class="stat-card-meta">
                        <div class="stat-card-label">Total Permohonan</div>
                        <div class="stat-card-val" id="statTotalCount"><?= $totalCount ?></div>
                        <div class="stat-card-desc">Semua berkas pengajuan lab</div>
                    </div>
                    <div class="stat-card-3d-icon">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>
                <div class="stat-card-bottom">
                    <div class="stat-card-bar"></div>
                    <div class="stat-card-dots"><span></span><span></span><span></span></div>
                </div>
            </div>

            <!-- 2. Perlu ACC Ka. Ur -->
            <div class="stat-card-highlight theme-sky">
                <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                <div class="stat-card-top">
                    <div class="stat-card-meta">
                        <div class="stat-card-label">Perlu ACC Ka. Ur</div>
                        <div class="stat-card-val">
                            <?= $readyForKaurCount ?>
                            <?php if ($totalCount > 0): ?>
                                <span class="stat-card-percent">(<?= round(($readyForKaurCount / $totalCount) * 100) ?>%)</span>
                            <?php endif; ?>
                        </div>
                        <div class="stat-card-desc">Disetujui Laboran, siap ACC</div>
                    </div>
                    <div class="stat-card-3d-icon">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                </div>
                <div class="stat-card-bottom">
                    <div class="stat-card-bar"></div>
                    <div class="stat-card-dots"><span></span><span></span><span></span></div>
                </div>
            </div>

            <!-- 3. Surat QR Terbit -->
            <div class="stat-card-highlight theme-emerald">
                <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                <div class="stat-card-top">
                    <div class="stat-card-meta">
                        <div class="stat-card-label">Surat QR Terbit (ACC)</div>
                        <div class="stat-card-val">
                            <?= $approvedKaurCount ?>
                            <?php if ($totalCount > 0): ?>
                                <span class="stat-card-percent">(<?= round(($approvedKaurCount / $totalCount) * 100) ?>%)</span>
                            <?php endif; ?>
                        </div>
                        <div class="stat-card-desc">Surat resmi & QR aktif</div>
                    </div>
                    <div class="stat-card-3d-icon">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                </div>
                <div class="stat-card-bottom">
                    <div class="stat-card-bar"></div>
                    <div class="stat-card-dots"><span></span><span></span><span></span></div>
                </div>
            </div>

            <!-- 4. Ditolak -->
            <div class="stat-card-highlight theme-rose">
                <div class="stat-card-glow"><div class="glow-bubble"></div></div>
                <div class="stat-card-top">
                    <div class="stat-card-meta">
                        <div class="stat-card-label">Ditolak</div>
                        <div class="stat-card-val">
                            <?= $rejectedCount ?>
                            <?php if ($totalCount > 0): ?>
                                <span class="stat-card-percent">(<?= round(($rejectedCount / $totalCount) * 100) ?>%)</span>
                            <?php endif; ?>
                        </div>
                        <div class="stat-card-desc">Pengajuan tidak disetujui</div>
                    </div>
                    <div class="stat-card-3d-icon">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                </div>
                <div class="stat-card-bottom">
                    <div class="stat-card-bar"></div>
                    <div class="stat-card-dots"><span></span><span></span><span></span></div>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="toolbar-card">
            <div class="filter-pills-wrap">
                <button type="button" class="filter-pill active" data-status="all" onclick="setFilterStatus('all')">
                    <span>Semua Permohonan</span>
                    <span class="pill-count"><?= $totalCount ?></span>
                </button>
                <button type="button" class="filter-pill" data-status="ready" onclick="setFilterStatus('ready')">
                    <span>⚡ Perlu ACC Ka. Ur</span>
                    <span class="pill-count"><?= $readyForKaurCount ?></span>
                </button>
                <button type="button" class="filter-pill" data-status="kaur" onclick="setFilterStatus('kaur')">
                    <span>📄 Surat QR Terbit</span>
                    <span class="pill-count"><?= $approvedKaurCount ?></span>
                </button>
                <button type="button" class="filter-pill" data-status="rejected" onclick="setFilterStatus('rejected')">
                    <span>🔴 Ditolak</span>
                    <span class="pill-count"><?= $rejectedCount ?></span>
                </button>
            </div>

            <div class="search-box-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="kaurSearchInput" placeholder="Cari permohonan ruangan..." onkeyup="filterKaurRows()">
            </div>
        </div>

        <!-- Table -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="kaur-table" id="kaurTable">
                    <thead>
                        <tr>
                            <th>Peminjam</th>
                            <th>Ruangan Laboratorium</th>
                            <th>Jadwal Pelaksanaan</th>
                            <th>Keperluan / Agenda</th>
                            <th>Status Verifikasi</th>
                            <th style="text-align: right;">Aksi Persetujuan & Surat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($peminjaman as $p): 
                            $s = $p->status;
                            $isApprovedKaur = (stripos($s, 'Ka. Ur') !== false || stripos($s, 'Kaur') !== false);
                            $isLaboranAcc = (stripos($s, 'Laboran') !== false);
                            $isPending = ($s === 'Pending');
                            $isRejected = ($s === 'Ditolak');

                            $statusCategory = 'other';
                            if ($isApprovedKaur) {
                                $statusCategory = 'kaur';
                                $dot = '#22c55e'; $bg = '#f0fdf4'; $color = '#166534'; $label = 'Disetujui Ka. Ur (Surat QR Terbit)';
                            } elseif ($isLaboranAcc) {
                                $statusCategory = 'ready';
                                $dot = '#3b82f6'; $bg = '#eff6ff'; $color = '#1d4ed8'; $label = 'ACC Laboran (Menunggu Ka. Ur)';
                            } elseif ($isPending) {
                                $statusCategory = 'ready';
                                $dot = '#f59e0b'; $bg = '#fffbeb'; $color = '#b45309'; $label = 'Pending (Menunggu)';
                            } elseif ($isRejected) {
                                $statusCategory = 'rejected';
                                $dot = '#ef4444'; $bg = '#fef2f2'; $color = '#991b1b'; $label = 'Ditolak';
                            } else {
                                $dot = '#8b5cf6'; $bg = '#f5f3ff'; $color = '#6d28d9'; $label = htmlspecialchars($s);
                            }

                            $dateFormatted = ($p->tanggal_mulai === $p->tanggal_selesai)
                                ? date('d M Y', strtotime($p->tanggal_mulai))
                                : date('d M', strtotime($p->tanggal_mulai)) . ' - ' . date('d M Y', strtotime($p->tanggal_selesai));
                            $timeFormatted = substr($p->jam_mulai, 0, 5) . ' - ' . substr($p->jam_selesai, 0, 5);
                        ?>
                        <tr class="kaur-row" data-status-category="<?= $statusCategory ?>" data-search="<?= strtolower(htmlspecialchars($p->nama_lengkap . ' ' . $p->kode_ruangan . ' ' . $p->nama_ruangan . ' ' . $p->keterangan)) ?>">
                            <td>
                                <strong style="color: #0f172a; display: block;"><?= htmlspecialchars($p->nama_lengkap) ?></strong>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #16a34a;"><?= htmlspecialchars($p->kode_ruangan ?: '-') ?></div>
                                <div style="font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($p->nama_ruangan ?: '-') ?></div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;"><?= $dateFormatted ?></div>
                                <div style="font-size: 0.78rem; color: #64748b; font-weight: 600;">⏰ <?= $timeFormatted ?></div>
                            </td>
                            <td>
                                <div style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #334155;" title="<?= htmlspecialchars($p->keterangan) ?>">
                                    <?= htmlspecialchars($p->keterangan ?: '-') ?>
                                </div>
                                <?php if ($isRejected && !empty($p->alasan_penolakan)): ?>
                                    <div style="font-size: 0.75rem; color: #dc2626; margin-top: 2px;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($p->alasan_penolakan) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge" style="background: <?= $bg ?>; color: <?= $color ?>;">
                                    <span class="status-dot" style="background: <?= $dot ?>;"></span>
                                    <?= $label ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    <?php if ($isApprovedKaur): ?>
                                        <a href="<?= site_url('kaur/surat/' . $p->id) ?>" target="_blank" class="btn-action-qr" title="Cetak Surat Resmi Ber-QR Code">
                                            <i class="fa-solid fa-qrcode"></i> Cetak Surat QR
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn-action-acc" onclick="kaurApprove(<?= $p->id ?>)" title="Beri Persetujuan Resmi & Terbitkan Surat QR">
                                            <i class="fa-solid fa-stamp"></i> ACC Resmi
                                        </button>
                                        <button type="button" class="btn-action-rej" onclick="openKaurRejectModal(<?= $p->id ?>)" title="Tolak Permohonan">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Tolak -->
    <div class="modal-overlay" id="kaurRejectModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3><i class="fa-solid fa-triangle-exclamation text-red-500"></i> Penolakan Permohonan Peminjaman</h3>
                <button type="button" onclick="closeKaurRejectModal()" style="background:none;border:none;font-size:1.2rem;cursor:pointer;">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="kaurRejectId">
                <label style="font-size:0.82rem; font-weight:700; color:#991b1b; display:block; margin-bottom:6px;">Alasan Penolakan Resmi (Wajib):</label>
                <textarea id="kaurAlasanInput" rows="3" class="form-control" placeholder="Tuliskan catatan alasan penolakan..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" style="background:#f1f5f9; color:#475569; border:none; padding:8px 16px; border-radius:8px; cursor:pointer;" onclick="closeKaurRejectModal()">Batal</button>
                <button type="button" class="btn" style="background:#dc2626; color:#fff; border:none; padding:8px 16px; border-radius:8px; cursor:pointer; font-weight:700;" onclick="submitKaurReject()">Konfirmasi Tolak</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const BASE_URL = '<?= base_url() ?>';
        let currentFilter = 'all';

        function setFilterStatus(st) {
            currentFilter = st;
            document.querySelectorAll('.filter-pill').forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-status') === st);
            });
            filterKaurRows();
        }

        function filterKaurRows() {
            const query = (document.getElementById('kaurSearchInput').value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('.kaur-row');

            rows.forEach(row => {
                const cat = row.getAttribute('data-status-category');
                const searchTxt = row.getAttribute('data-search') || '';

                const matchStatus = (currentFilter === 'all') || (cat === currentFilter);
                const matchQuery  = !query || searchTxt.includes(query);

                row.style.display = (matchStatus && matchQuery) ? '' : 'none';
            });
        }

        function kaurApprove(id) {
            Swal.fire({
                title: 'Setujui Resmi Peminjaman?',
                text: 'Permohonan akan berstatus Disetujui Ka. Ur dan langsung menerbitkan Surat Resmi ber-QR Code.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Setujui & Terbitkan QR',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    $.post(BASE_URL + 'kaur/approve/' + id, function(resp) {
                        if (resp.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil Disetujui!',
                                text: resp.message,
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#16a34a',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: '📄 Lihat Surat QR',
                                cancelButtonText: 'Tutup'
                            }).then((action) => {
                                if (action.isConfirmed && resp.surat_url) {
                                    window.open(resp.surat_url, '_blank');
                                }
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', resp.message, 'error');
                        }
                    }, 'json').fail(() => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'));
                }
            });
        }

        function openKaurRejectModal(id) {
            document.getElementById('kaurRejectId').value = id;
            document.getElementById('kaurAlasanInput').value = '';
            document.getElementById('kaurRejectModal').classList.add('active');
        }

        function closeKaurRejectModal() {
            document.getElementById('kaurRejectModal').classList.remove('active');
        }

        function submitKaurReject() {
            const id = document.getElementById('kaurRejectId').value;
            const alasan = document.getElementById('kaurAlasanInput').value.trim();

            if (!alasan) {
                Swal.fire('Catatan Wajib', 'Harap masukkan alasan penolakan!', 'warning');
                return;
            }

            $.post(BASE_URL + 'kaur/reject/' + id, { alasan_penolakan: alasan }, function(resp) {
                if (resp.status === 'success') {
                    closeKaurRejectModal();
                    Swal.fire('Ditolak', resp.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', resp.message, 'error');
                }
            }, 'json').fail(() => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'));
        }
    </script>
</body>
</html>