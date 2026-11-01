<?php
/**
 * Template Berita Acara Pelaksanaan Sidang Format iGracias - Admin LAA
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BERITA ACARA PENYELENGGARAAN SIDANG TA/ PA'; ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0 !important;
        }
        html, body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
            background: #e2e8f0;
            margin: 0;
            padding: 0;
            font-size: 10.5pt;
            line-height: 1.4;
        }
        .page-container {
            max-width: 750px;
            margin: 20px auto;
            background: #ffffff;
            padding: 25px 35px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-radius: 4px;
        }
        .main-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .main-header h1 {
            font-size: 13.5pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-table td.label-col {
            width: 150px;
            font-weight: bold;
        }
        .info-table td.colon-col {
            width: 15px;
        }
        .section-intro {
            margin: 14px 0 8px 0;
        }
        .sub-info-table {
            width: 100%;
            margin-left: 20px;
            margin-bottom: 14px;
        }
        .sub-info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .sub-info-table td.label-sub {
            width: 120px;
        }
        .student-table {
            width: 100%;
            margin-left: 20px;
            margin-bottom: 20px;
        }
        .student-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .student-table td.label-std {
            width: 80px;
        }
        .student-table td.colon-std {
            width: 15px;
        }
        .title-box {
            line-height: 1.4;
        }
        .title-lang {
            color: #1e293b;
        }
        .examiners-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
        }
        .examiners-table th, .examiners-table td {
            border: 1px solid #000000;
            padding: 8px 10px;
            vertical-align: middle;
        }
        .examiners-table th {
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }
        .examiners-table td.no-col {
            width: 35px;
            text-align: center;
        }
        .examiners-table td.sig-col {
            width: 180px;
            text-align: center;
        }
        .sig-img {
            max-height: 48px;
            width: auto;
            display: block;
            margin: 0 auto;
        }
        .sig-fallback {
            font-family: 'Brush Script MT', cursive, sans-serif;
            font-size: 16pt;
            color: #0284c7;
        }
        .result-section {
            margin: 20px 0 35px 0;
            font-size: 10.5pt;
        }
        .result-option {
            font-weight: bold;
            margin-top: 4px;
        }
        .result-option.active {
            text-decoration: none;
        }
        .result-option.inactive {
            text-decoration: line-through;
            color: #475569;
        }
        .footer-grid {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .footer-col-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .footer-col-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .notes-box {
            font-size: 10pt;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 4px;
        }
        @media print {
            html, body {
                background: #ffffff !important;
                padding: 12mm 15mm !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .page-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .no-print, #bapToolbar {
                display: none !important;
            }
        }
        /* Mode Embed / Iframe: Hapus padding luar dan sembunyikan toolbar */
        body.is-embedded {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        body.is-embedded .page-container {
            margin: 0 auto !important;
            padding: 15px 25px !important;
            box-shadow: none !important;
        }
        body.is-embedded #bapToolbar {
            display: none !important;
        }
    </style>
</head>
<body class="<?= (!empty($_GET['embed']) || !empty($is_embed)) ? 'is-embedded' : ''; ?>">

    <!-- Toolbar Layar Standalone (Hanya tampil saat dibuka langsung di tab baru, tersembunyi saat di-embed/iframe/cetak) -->
    <div id="bapToolbar" class="no-print" style="position: sticky; top: 0; left: 0; right: 0; z-index: 9999; background: #0f172a; padding: 10px 24px; display: flex; justify-content: flex-end; align-items: center; gap: 10px; border-bottom: 1px solid #334155; margin-bottom: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
        <div style="color: #cbd5e1; font-size: 13px; margin-right: auto; font-family: sans-serif; display: flex; align-items: center; gap: 8px;">
            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #ea580c;"></span>
            <strong><?= htmlspecialchars($title ?? 'Berita Acara Sidang (IGrACIAS)'); ?></strong>
        </div>
        <button type="button" id="btnDownloadPdf" onclick="downloadBapPdf()" style="background: #ea580c; color: #ffffff; border: none; padding: 7px 16px; border-radius: 8px; font-weight: 700; font-size: 12.5px; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(234, 88, 12, 0.4); font-family: inherit;">
            📥 Download PDF
        </button>
        <button type="button" onclick="window.print()" style="background: #334155; color: #ffffff; border: 1px solid #475569; padding: 7px 14px; border-radius: 8px; font-weight: 700; font-size: 12.5px; cursor: pointer; display: flex; align-items: center; gap: 6px; font-family: inherit;">
            🖨️ Cetak Fisik
        </button>
    </div>

    <script>
        // Otomatis sembunyikan toolbar jika berada di dalam iframe preview
        if (window.self !== window.top) {
            document.body.classList.add('is-embedded');
            const tb = document.getElementById('bapToolbar');
            if (tb) tb.style.display = 'none';
        }
    </script>

    <div class="page-container">

        <!-- Judul Berita Acara -->
        <div class="main-header">
            <h1>BERITA ACARA PENYELENGGARAAN SIDANG TA/ PA</h1>
        </div>

        <!-- Meta Fakultas & Prodi -->
        <table class="info-table">
            <tr>
                <td class="label-col">FAKULTAS</td>
                <td class="colon-col">:</td>
                <td><strong><?= htmlspecialchars($bap['fakultas'] ?? 'Fakultas Industri Kreatif'); ?></strong></td>
            </tr>
            <tr>
                <td class="label-col">PROGRAM STUDI</td>
                <td class="colon-col">:</td>
                <td><strong><?= htmlspecialchars($bap['prodi'] ?? 'Desain Komunikasi Visual'); ?></strong></td>
            </tr>
        </table>

        <div class="section-intro">
            Telah diselenggarakan Ujian Skripsi pada,
        </div>

        <!-- Detail Jadwal Sidang -->
        <table class="sub-info-table">
            <tr>
                <td class="label-sub">Hari/ Tanggal</td>
                <td style="width: 15px;">:</td>
                <td><?= htmlspecialchars($bap['tanggal_text'] ?? $bap['tanggal_sidang'] ?? '2026-07-22'); ?></td>
            </tr>
            <tr>
                <td class="label-sub">Waktu</td>
                <td>:</td>
                <td><?= htmlspecialchars($bap['waktu_sidang'] ?? '13:30:00'); ?></td>
            </tr>
            <tr>
                <td class="label-sub">Tempat</td>
                <td>:</td>
                <td><?= htmlspecialchars($bap['tempat'] ?? $bap['ruang_sidang'] ?? '4.08'); ?></td>
            </tr>
        </table>

        <div class="section-intro">
            Terhadap Peserta sidang dengan data sebagai berikut,
        </div>

        <!-- Detail Mahasiswa & Judul TA -->
        <table class="student-table">
            <tr>
                <td class="label-std">Nama</td>
                <td class="colon-std">:</td>
                <td><?= htmlspecialchars($bap['nama']); ?></td>
            </tr>
            <tr>
                <td class="label-std">NIM</td>
                <td class="colon-std">:</td>
                <td><?= htmlspecialchars($bap['nim']); ?></td>
            </tr>
            <tr>
                <td class="label-std">Judul<br>TA/PA</td>
                <td class="colon-std">:</td>
                <td class="title-box">
                    <div class="title-lang">(Bahasa Indonesia)</div>
                    <div><?= htmlspecialchars($bap['judul']); ?></div>
                    <?php if (!empty($bap['judul_en'])): ?>
                        <div class="title-lang" style="margin-top: 4px;">(Bahasa Inggris)</div>
                        <div><?= htmlspecialchars($bap['judul_en']); ?></div>
                    <?php else: ?>
                        <div class="title-lang" style="margin-top: 4px;">(Bahasa Inggris)</div>
                        <div><?= htmlspecialchars($bap['judul']); ?></div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <div class="section-intro">
            Oleh Tim Penguji yang namanya tercantum di bawah ini:
        </div>

        <!-- Tabel Tim Penguji -->
        <table class="examiners-table">
            <thead>
                <tr>
                    <th class="no-col">No</th>
                    <th>NAMA</th>
                    <th class="sig-col">TANDA TANGAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="no-col">1</td>
                    <td>
                        Penguji 1 :<br>
                        <strong><?= htmlspecialchars($bap['penguji_1_kode'] ?? 'LGM'); ?> - <?= htmlspecialchars($bap['penguji_1']); ?></strong>
                    </td>
                    <td class="sig-col">
                        <?php if (!empty($bap['ttd_penguji_1']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_penguji_1'])): ?>
                            <img src="<?= base_url('uploads/signatures/' . $bap['ttd_penguji_1']); ?>" class="sig-img">
                        <?php else: ?>
                            <span class="sig-fallback">~ TTD Digital ~</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="no-col">2</td>
                    <td>
                        Penguji 2 :<br>
                        <strong><?= htmlspecialchars($bap['penguji_2_kode'] ?? 'ALW'); ?> - <?= htmlspecialchars($bap['penguji_2']); ?></strong>
                    </td>
                    <td class="sig-col">
                        <?php if (!empty($bap['ttd_penguji_2']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_penguji_2'])): ?>
                            <img src="<?= base_url('uploads/signatures/' . $bap['ttd_penguji_2']); ?>" class="sig-img">
                        <?php else: ?>
                            <span class="sig-fallback">~ TTD Digital ~</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="no-col">3</td>
                    <td>
                        <?php if (!empty($bap['penguji_3'])): ?>
                            Penguji 3 :<br>
                            <strong><?= htmlspecialchars($bap['penguji_3_kode'] ?? 'ISI'); ?> - <?= htmlspecialchars($bap['penguji_3']); ?></strong>
                        <?php else: ?>
                            Pembimbing 1 :<br>
                            <strong><?= htmlspecialchars($bap['pembimbing_1_kode'] ?? 'ISI'); ?> - <?= htmlspecialchars($bap['pembimbing_1']); ?></strong>
                        <?php endif; ?>
                    </td>
                    <td class="sig-col">
                        <?php 
                        $ttd_p3 = !empty($bap['ttd_penguji_3']) ? $bap['ttd_penguji_3'] : (!empty($bap['ttd_pembimbing_1']) ? $bap['ttd_pembimbing_1'] : null);
                        if (!empty($ttd_p3) && file_exists(FCPATH . 'uploads/signatures/' . $ttd_p3)): 
                        ?>
                            <img src="<?= base_url('uploads/signatures/' . $ttd_p3); ?>" class="sig-img">
                        <?php else: ?>
                            <span class="sig-fallback">~ TTD Digital ~</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Keputusan Hasil Evaluasi (Layak / Tidak Layak) -->
        <div class="result-section">
            <div>Peserta Sidang TA/PA mendapat nilai rata-rata :</div>
            <?php 
                if (isset($bap['is_layak_referensi']) && $bap['is_layak_referensi'] !== null) {
                    $is_layak = (bool)$bap['is_layak_referensi'];
                } else {
                    $is_layak = ($bap['indeks_huruf'] ?? 'B') !== 'E' && ($bap['indeks_huruf'] ?? 'B') !== 'D';
                }
            ?>
            <div class="result-option <?= $is_layak ? 'active' : 'inactive'; ?>" style="margin-left: 15px; margin-top: 6px;">
                LAYAK dijadikan sebagai Referensi
            </div>
            <div class="result-option <?= !$is_layak ? 'active' : 'inactive'; ?>" style="margin-left: 15px; margin-top: 3px;">
                TIDAK LAYAK dijadikan sebagai Referensi
            </div>
        </div>

        <!-- Footer TTD Ketua Penguji & Catatan Penting -->
        <div class="footer-grid">
            <div class="footer-col-left">
                <div>Ketua Tim Penguji</div>
                <div style="height: 65px; margin: 6px 0; display: flex; align-items: center;">
                    <?php if (!empty($bap['ttd_penguji_1']) && file_exists(FCPATH . 'uploads/signatures/' . $bap['ttd_penguji_1'])): ?>
                        <img src="<?= base_url('uploads/signatures/' . $bap['ttd_penguji_1']); ?>" style="max-height: 60px; width: auto; display: block;">
                    <?php else: ?>
                        <span class="sig-fallback" style="font-size: 18pt;">~ TTD Digital ~</span>
                    <?php endif; ?>
                </div>
                <div>( <?= htmlspecialchars($bap['penguji_1']); ?> )</div>
            </div>

            <div class="footer-col-right">
                <div class="notes-box">
                    <div class="notes-title">CATATAN PENTING KETUA PENGUJI :</div>
                    <div><?= !empty($bap['catatan_penguji']) ? htmlspecialchars($bap['catatan_penguji']) : '-'; ?></div>
                </div>
            </div>
        </div>

    </div>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadBapPdf() {
            const element = document.querySelector('.page-container');
            const btn = document.getElementById('btnDownloadPdf');
            if (btn) {
                btn.disabled = true;
                btn.innerText = '⏳ Mengunduh PDF...';
            }

            const opt = {
                margin:       [10, 15, 10, 15],
                filename:     'BAP_iGracias_<?= htmlspecialchars($bap['nim'] ?? 'dokumen') ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, logging: false },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(function() {
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = '✅ Terunduh (Klik Ulang)';
                }
            });
        }

        <?php if (!empty($auto_print)): ?>
        window.addEventListener('load', function() {
            setTimeout(downloadBapPdf, 600);
        });
        <?php endif; ?>
    </script>
</body>
</html>
