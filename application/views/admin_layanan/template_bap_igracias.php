<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BAP IGrACIAS — Berita Acara Penyelenggaraan Sidang TA/PA'; ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 24px 36px;
            font-size: 10pt;
            line-height: 1.4;
        }
        .page-container {
            max-width: 780px;
            margin: 0 auto;
            background: #ffffff;
        }
        .header-title {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .header-title h2 {
            font-size: 13pt;
            font-weight: 800;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .meta-info-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }
        .meta-info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .meta-label {
            width: 130px;
            font-weight: bold;
            color: #475569;
        }
        .section-desc {
            margin: 12px 0 6px 0;
            font-size: 9.5pt;
            color: #334155;
        }
        .student-box {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .student-table {
            width: 100%;
            font-size: 9.5pt;
        }
        .student-table td {
            padding: 2.5px 0;
            vertical-align: top;
        }
        .examiner-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 16px 0;
        }
        .examiner-table th, .examiner-table td {
            border: 1px solid #334155;
            padding: 6px 8px;
            font-size: 9pt;
        }
        .examiner-table th {
            background: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
        .sig-cell {
            font-family: 'Brush Script MT', cursive, sans-serif;
            font-size: 16pt;
            color: #0284c7;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .decision-box {
            border: 1.5px solid #0f172a;
            padding: 10px 14px;
            margin: 16px 0;
            background: #ffffff;
            font-size: 9.5pt;
        }
        .footer-signatures {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            text-align: right;
            font-size: 9.5pt;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="page-container">

        <!-- Header (Matching Foto 4) -->
        <div class="header-title">
            <h2>BERITA ACARA PENYELENGGARAAN SIDANG TA/PA</h2>
            <div style="font-size: 9pt; color: #64748b; margin-top: 3px;">SISTEM INFORMASI AKADEMIK TERPADU (IGRACIAS)</div>
        </div>

        <table class="meta-info-table">
            <tr>
                <td class="meta-label">FAKULTAS</td>
                <td style="width: 15px;">:</td>
                <td><strong>FAKULTAS INDUSTRI KREATIF</strong></td>
            </tr>
            <tr>
                <td class="meta-label">PROGRAM STUDI</td>
                <td>:</td>
                <td><strong><?= htmlspecialchars($bap['prodi'] ?? 'Desain Komunikasi Visual'); ?></strong></td>
            </tr>
        </table>

        <div class="section-desc">
            Telah diselenggarakan Ujian Sidang Tugas Akhir / Proyek Akhir pada :
        </div>

        <table class="meta-info-table" style="margin-left: 10px;">
            <tr>
                <td style="width: 120px; color: #64748b;">Hari / Tanggal</td>
                <td style="width: 15px;">:</td>
                <td><strong><?= htmlspecialchars($bap['hari'] ?? 'Selasa'); ?>, <?= htmlspecialchars($bap['tanggal_text'] ?? '30 Juni 2026'); ?></strong></td>
            </tr>
            <tr>
                <td style="color: #64748b;">Waktu</td>
                <td>:</td>
                <td><strong>09:15:00 WIB - Selesai</strong></td>
            </tr>
            <tr>
                <td style="color: #64748b;">Tempat / Ruangan</td>
                <td>:</td>
                <td><strong><?= htmlspecialchars($bap['tempat'] ?? 'Kampus FIK Lt. 2 (Ruang Sidang L07)'); ?></strong></td>
            </tr>
        </table>

        <div class="section-desc">
            Terhadap Peserta Sidang dengan data sebagai berikut :
        </div>

        <div class="student-box">
            <table class="student-table">
                <tr>
                    <td style="width: 110px; font-weight: bold; color: #475569;">Nama</td>
                    <td style="width: 15px;">:</td>
                    <td><strong><?= htmlspecialchars($bap['nama']); ?></strong></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #475569;">NIM</td>
                    <td>:</td>
                    <td style="font-family: monospace; font-weight: bold; color: #ea580c;"><?= htmlspecialchars($bap['nim']); ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #475569;">Judul TA / PA</td>
                    <td>:</td>
                    <td style="font-style: italic;"><?= htmlspecialchars($bap['judul']); ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #475569;">Bahasa Pengantar</td>
                    <td>:</td>
                    <td>Bahasa Indonesia / English</td>
                </tr>
            </table>
        </div>

        <div class="section-desc">
            <strong>Tim Dosen Penguji yang bertindak memeriksa & menilai :</strong>
        </div>

        <!-- Tabel Tim Penguji IGrACIAS (Matching Foto 4) -->
        <table class="examiner-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No.</th>
                    <th>Nama Dosen</th>
                    <th style="width: 150px;">Posisi Sidang</th>
                    <th style="width: 130px;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">1</td>
                    <td><strong><?= htmlspecialchars($bap['penguji_1'] ?? 'Dr. Samsul Alam, S.Pd., M.Pd.'); ?></strong></td>
                    <td>Ketua Sidang / Penguji 1</td>
                    <td><div class="sig-cell">~ Samsul A. ~</div></td>
                </tr>
                <tr>
                    <td align="center">2</td>
                    <td><strong><?= htmlspecialchars($bap['penguji_2'] ?? 'Putu Raka Setya Putra, S.Ds., M.Ds.'); ?></strong></td>
                    <td>Penguji 2</td>
                    <td><div class="sig-cell">~ Putu Raka ~</div></td>
                </tr>
                <tr>
                    <td align="center">3</td>
                    <td><strong><?= htmlspecialchars($bap['pembimbing_1'] ?? 'Nina Nursetia Ningrum, S.Pd., M.Pd.'); ?></strong></td>
                    <td>Pembimbing 1</td>
                    <td><div class="sig-cell">~ Nina N. ~</div></td>
                </tr>
                <tr>
                    <td align="center">4</td>
                    <td><strong><?= htmlspecialchars($bap['pembimbing_2'] ?? 'I Gusti Agung Rangga Lawe, S.Ds., M.Ds.'); ?></strong></td>
                    <td>Pembimbing 2</td>
                    <td><div class="sig-cell">~ Rangga L. ~</div></td>
                </tr>
            </tbody>
        </table>

        <!-- Keputusan Kelulusan (Matching Foto 4) -->
        <div class="decision-box">
            <div style="font-weight: bold; margin-bottom: 4px;">KEPUTUSAN SIDANG TUGAS AKHIR :</div>
            <div>Peserta Sidang TA/PA dinyatakan : <strong style="font-size: 11pt; color: #047857; text-decoration: underline;">LULUS / [ TIDAK LULUS ]</strong></div>
            <div style="font-size: 8.5pt; color: #64748b; margin-top: 4px;">Catatan: Berita Acara ini sah dan diterbitkan secara digital melalui Portal Akademik IGrACIAS.</div>
        </div>

        <div class="footer-signatures">
            <div>
                <div>Bandung, <?= htmlspecialchars($bap['tanggal_text'] ?? '30 Juni 2026'); ?></div>
                <div style="margin-top: 4px; font-weight: bold;">Ketua Sidang Tugas Akhir,</div>
                <div class="sig-cell" style="height: 48px; font-size: 20pt; margin: 4px 0;">
                    ~ Samsul Alam ~
                </div>
                <div><strong><?= htmlspecialchars($bap['penguji_1'] ?? 'Dr. Samsul Alam, S.Pd., M.Pd.'); ?></strong></div>
            </div>
        </div>

    </div>

    <?php if (!empty($auto_print)): ?>
    <script>
        window.addEventListener('load', function() { window.print(); });
    </script>
    <?php endif; ?>
</body>
</html>
