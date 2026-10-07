<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($aset) ? 'Edit Master Data Barang' : 'Tambah Barang Baru' ?> — IFIK</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
    <style>
        :root {
            --primary: #ea580c;
            --primary-hover: #c2410c;
            --bg-color: #fcfbf9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .page-wrapper-for-sidebar {
            width: 100%;
            min-width: 0;
            padding: 40px 60px;
            min-height: 100vh;
            transition: margin-left 0.75s cubic-bezier(0.76, 0, 0.24, 1), width 0.75s cubic-bezier(0.76, 0, 0.24, 1);
            box-sizing: border-box;
        }

        @media (min-width: 992px) {
            .page-wrapper-for-sidebar {
                margin-left: 270px;
                width: calc(100% - 270px);
            }

            body.curved-sidebar-desktop-collapsed .page-wrapper-for-sidebar {
                margin-left: 0;
                width: 100%;
            }
        }

        @media (max-width: 991.98px) {
            .page-wrapper-for-sidebar {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 30px 20px;
                padding-top: 56px;
            }
        }

        .btn-back-header {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.04);
        }

        .btn-back-header:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-3px);
        }

        .page-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        /* Drag & Drop Zone */
        .drop-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 2.5rem 1.5rem;
            text-align: center;
            background-color: #f8fafc;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .drop-zone:hover,
        .drop-zone.dragover {
            background-color: #fff7ed;
            border-color: var(--primary);
            transform: scale(1.005);
        }

        .drop-zone input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .preview-container {
            position: relative;
            z-index: 3;
            pointer-events: none;
        }

        .preview-wrapper {
            position: relative;
            display: inline-block;
        }

        .btn-remove-preview {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            pointer-events: auto;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .btn-remove-preview:hover {
            background-color: #dc2626;
            transform: scale(1.1);
        }

        .asset-model-preview {
            width: min(240px, 100%);
            height: 160px;
            display: inline-block;
            border-radius: 12px;
            background: linear-gradient(145deg, #f7f9fc 0%, #e4eaf1 100%);
        }

        .btn-fik-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
            font-weight: 700;
            border-radius: 999px;
            padding: 10px 28px;
            transition: all 0.2s ease;
        }

        .btn-fik-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <?php include APPPATH . 'views/admin/panel_sidebar.php'; ?>

    <main class="page-wrapper-for-sidebar">
        <!-- Top Header Navigation -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= site_url('admin/barang') ?>" class="btn-back-header">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Barang
                </a>
                <div>
                    <h1 class="page-title mb-1"><?= isset($aset) ? 'Edit Master Data Barang' : 'Tambah Barang Baru' ?></h1>
                    <p class="text-muted small mb-0"><?= isset($aset) ? 'Perbarui informasi spesifikasi dan penempatan alat laboratorium' : 'Input data alat laboratorium baru beserta foto atau model 3D' ?></p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white mb-5">
                    <div class="card-body p-4 p-md-5">
                        <?php if ($form_error = $this->session->flashdata('error')): ?>
                            <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-start gap-2 mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle-fill text-danger mt-1 fs-5"></i>
                                <div><?= html_escape($form_error) ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('admin/barang/simpan') ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_aset" value="<?= isset($aset) ? $aset->id_aset : '' ?>">

                            <div class="row g-3 mb-4">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold text-dark small">KODE BARANG / ASET <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_aset" class="form-control rounded-3 font-monospace bg-light" value="<?= isset($aset) ? $aset->kode_aset : '' ?>" placeholder="Contoh: MTL-001" required>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label fw-bold text-dark small">NAMA LENGKAP BARANG <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_aset" class="form-control rounded-3" value="<?= isset($aset) ? $aset->nama_aset : '' ?>" placeholder="Contoh: Air Compressor Orange..." required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark small">DESKRIPSI / SPESIFIKASI ASET</label>
                                <textarea name="deskripsi" class="form-control rounded-3" rows="3" placeholder="Masukkan spesifikasi teknis atau keterangan lengkap barang..."><?= isset($aset) ? $aset->deskripsi : '' ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark small">LOKASI RUANGAN / LABORATORIUM <span class="text-danger">*</span></label>
                                <select name="id_ruangan" class="form-select rounded-3" required>
                                    <option value="">-- Pilih Penempatan Laboratorium --</option>
                                    <?php foreach ($ruangan as $r): ?>
                                        <?php 
                                            $isSelected = false;
                                            if (isset($aset) && !empty($aset->id_ruangan)) {
                                                $isSelected = ($aset->id_ruangan == $r->id_ruangan) || 
                                                              (!empty($r->all_ids) && in_array((string)$aset->id_ruangan, $r->all_ids, true));
                                            }
                                            $selected = $isSelected ? 'selected' : ''; 
                                        ?>
                                        <option value="<?= $r->id_ruangan ?>" <?= $selected ?>><?= $r->nama_ruangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark small">MEDIA UTAMA ASET (FOTO / 3D GLB)</label>
                                <div class="drop-zone shadow-sm" id="dropZone">
                                    <input type="file" name="gambar" id="fileInput" accept="image/jpeg, image/png, image/jpg, image/webp,.glb,.gltf,model/gltf-binary,model/gltf+json">

                                    <?php
                                    $ada_gambar = (isset($aset) && !empty($aset->gambar));
                                    $primary_is_3d = $ada_gambar && in_array(strtolower(pathinfo($aset->gambar, PATHINFO_EXTENSION)), ['glb', 'gltf'], true);
                                    $gambar_url = $ada_gambar ? base_url('assets/uploads/barang/' . rawurlencode($aset->gambar)) : '#';
                                    $gambar_text = $ada_gambar ? '<i class="bi bi-info-circle me-1"></i>Media saat ini (Abaikan jika tidak diubah)' : '';
                                    ?>

                                    <div id="previewContainer" class="preview-container <?= $ada_gambar ? 'd-block' : 'd-none' ?>">
                                        <div class="preview-wrapper">
                                            <img id="imagePreview" src="<?= $gambar_url ?>" data-default-src="<?= $gambar_url ?>" alt="Preview media aset" class="img-thumbnail shadow-sm mb-2 <?= $primary_is_3d ? 'd-none' : '' ?>" style="max-height: 160px; border-radius: 12px;">
                                            <model-viewer id="modelPreview" src="<?= $primary_is_3d ? $gambar_url : '' ?>" alt="Preview model 3D aset" class="asset-model-preview shadow-sm mb-2 <?= $primary_is_3d ? '' : 'd-none' ?>" camera-controls disable-pan disable-zoom interaction-prompt="none" touch-action="pan-y" shadow-intensity="0.55"></model-viewer>
                                            <button type="button" id="btnRemovePreview" class="btn-remove-preview" title="Batal Pilih Media">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                        <p class="small text-muted mb-0 fw-medium" id="fileName" data-default-text="<?= htmlspecialchars($gambar_text) ?>">
                                            <?= $gambar_text ?>
                                        </p>
                                    </div>

                                    <div id="placeholderContainer" class="preview-container <?= $ada_gambar ? 'd-none' : 'd-block' ?>">
                                        <i class="bi bi-cloud-arrow-up display-5 text-secondary opacity-50 mb-2 d-block"></i>
                                        <h6 class="mb-1 text-dark fw-bold"><span class="text-primary">Pilih file</span> atau tarik file ke sini</h6>
                                        <p class="text-muted small mb-0 mt-1">Foto: JPG, PNG, WEBP (maks 2MB) | Model 3D: GLB, GLTF (maks 15MB)</p>
                                    </div>
                                </div>
                            </div>

                            <?php
                            $gallery_filenames = [];
                            if (isset($aset) && !empty($aset->foto)) {
                                $gallery_source = json_decode((string) $aset->foto, true);
                                $gallery_filenames = is_array($gallery_source) ? $gallery_source : [$aset->foto];
                                $gallery_filenames = array_values(array_unique(array_filter(array_map('basename', $gallery_filenames))));
                            }
                            ?>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark small">MEDIA GALERI TAMBAHAN <span class="text-muted fw-normal">(opsional)</span></label>
                                <input type="file" name="galeri_tambahan[]" class="form-control rounded-3" accept="image/jpeg, image/png, image/jpg, image/webp,.glb,.gltf,model/gltf-binary,model/gltf+json" multiple>
                                <div class="form-text small text-muted">Pilih hingga 5 media tambahan (Foto maks 2MB, Model 3D GLB maks 15MB per file).</div>
                                <?php if (!empty($gallery_filenames)): ?>
                                    <div class="d-flex flex-wrap gap-3 mt-3">
                                        <?php foreach ($gallery_filenames as $gallery_filename): ?>
                                            <?php $gallery_is_3d = in_array(strtolower(pathinfo($gallery_filename, PATHINFO_EXTENSION)), ['glb', 'gltf'], true); ?>
                                            <label class="border rounded-4 p-2 bg-light text-center shadow-sm" style="width: 132px; cursor: pointer;">
                                                <?php if ($gallery_is_3d): ?>
                                                    <model-viewer src="<?= base_url('assets/uploads/barang/' . rawurlencode($gallery_filename)) ?>" alt="Model 3D galeri aset" class="rounded-3 mb-2" camera-controls disable-pan disable-zoom interaction-prompt="none" touch-action="pan-y" style="width: 112px; height: 82px; background: #eef1f5;"></model-viewer>
                                                <?php else: ?>
                                                    <img src="<?= base_url('assets/uploads/barang/' . rawurlencode($gallery_filename)) ?>" alt="Gambar galeri aset" class="img-fluid rounded-3 mb-2" style="width: 112px; height: 82px; object-fit: cover;">
                                                <?php endif; ?>
                                                <span class="d-flex align-items-center justify-content-center gap-1 small text-danger fw-semibold">
                                                    <input class="form-check-input m-0" type="checkbox" name="hapus_galeri[]" value="<?= html_escape($gallery_filename) ?>"> Hapus
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="row g-3 mb-4 p-3 bg-light rounded-4 border">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small">TOTAL UNIT FISIK <span class="text-danger">*</span></label>
                                    <input type="number" name="jumlah_total" class="form-control rounded-3" min="1" value="<?= isset($aset) ? $aset->jumlah_total : '1' ?>" <?= isset($aset) ? 'readonly title="Stok awal tidak bisa diubah via form edit dasar."' : 'required' ?>>
                                    <?php if (isset($aset)): ?>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Stok fisik tidak dapat diubah dari form edit dasar.</small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small">KONDISI FISIK <span class="text-danger">*</span></label>
                                    <select name="kondisi" class="form-select rounded-3" required>
                                        <option value="Baik" <?= (isset($aset) && $aset->kondisi == 'Baik') ? 'selected' : '' ?>>Baik & Berfungsi</option>
                                        <option value="Rusak" <?= (isset($aset) && in_array($aset->kondisi, ['Rusak','Rusak Ringan','Rusak Berat'], true)) ? 'selected' : '' ?>>Rusak (Butuh tindak lanjut)</option>
                                        <option value="Hilang" <?= (isset($aset) && $aset->kondisi == 'Hilang') ? 'selected' : '' ?>>Hilang</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                <a href="<?= site_url('admin/barang') ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold">Batal</a>
                                <button type="submit" class="btn btn-fik-primary shadow-sm"><i class="bi bi-check2-circle me-1"></i> Simpan ke Database</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('previewContainer');
            const placeholderContainer = document.getElementById('placeholderContainer');
            const imagePreview = document.getElementById('imagePreview');
            const modelPreview = document.getElementById('modelPreview');
            const fileNameDisplay = document.getElementById('fileName');
            const btnRemovePreview = document.getElementById('btnRemovePreview');

            const defaultSrc = imagePreview ? imagePreview.getAttribute('data-default-src') : '#';
            const defaultText = fileNameDisplay ? fileNameDisplay.getAttribute('data-default-text') : '';
            const defaultIs3d = <?= $primary_is_3d ? 'true' : 'false' ?>;
            let previewObjectUrl = null;

            if (dropZone && fileInput) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                    document.body.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
                });

                dropZone.addEventListener('drop', function(e) {
                    let dt = e.dataTransfer;
                    let files = dt.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        updatePreview(files[0]);
                    }
                }, false);

                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        updatePreview(this.files[0]);
                    }
                });

                function updatePreview(file) {
                    const is3d = /\.(glb|gltf)$/i.test(file.name);
                    const isImage = /\.(gif|jpe?g|png|webp)$/i.test(file.name) || String(file.type || '').toLowerCase().indexOf('image/') === 0;
                    const maxBytes = is3d ? 15 * 1024 * 1024 : 2 * 1024 * 1024;

                    if (!is3d && !isImage) {
                        alert('Format file ditolak! Gunakan JPG, JPEG, PNG, WEBP, GLB, atau GLTF.');
                        fileInput.value = '';
                        return;
                    }
                    if (file.size > maxBytes) {
                        alert(is3d ? 'Ukuran model terlalu besar. Maksimal 15MB per file.' : 'Ukuran gambar terlalu besar. Maksimal 2MB per file.');
                        fileInput.value = '';
                        return;
                    }

                    if (previewObjectUrl) {
                        URL.revokeObjectURL(previewObjectUrl);
                        previewObjectUrl = null;
                    }
                    if (is3d) {
                        previewObjectUrl = URL.createObjectURL(file);
                        modelPreview.src = previewObjectUrl;
                        modelPreview.classList.remove('d-none');
                        imagePreview.classList.add('d-none');
                        setFileName(file.name);
                        previewContainer.classList.remove('d-none');
                        previewContainer.classList.add('d-block');
                        placeholderContainer.classList.remove('d-block');
                        placeholderContainer.classList.add('d-none');
                        return;
                    }

                    let reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('d-none');
                        modelPreview.classList.add('d-none');
                        setFileName(file.name);
                        previewContainer.classList.remove('d-none');
                        previewContainer.classList.add('d-block');
                        placeholderContainer.classList.remove('d-block');
                        placeholderContainer.classList.add('d-none');
                    }
                }

                function setFileName(name) {
                    fileNameDisplay.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> File dipilih: <b class="text-dark"></b>';
                    fileNameDisplay.querySelector('b').textContent = name;
                }

                if (btnRemovePreview) {
                    btnRemovePreview.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        fileInput.value = '';

                        if (defaultSrc !== '' && defaultSrc !== '#') {
                            if (defaultIs3d) {
                                modelPreview.src = defaultSrc;
                                modelPreview.classList.remove('d-none');
                                imagePreview.classList.add('d-none');
                            } else {
                                imagePreview.src = defaultSrc;
                                imagePreview.classList.remove('d-none');
                                modelPreview.classList.add('d-none');
                            }
                            fileNameDisplay.innerHTML = defaultText;
                        } else {
                            imagePreview.src = '#';
                            modelPreview.removeAttribute('src');
                            imagePreview.classList.add('d-none');
                            modelPreview.classList.add('d-none');
                            fileNameDisplay.innerHTML = '';
                            previewContainer.classList.remove('d-block');
                            previewContainer.classList.add('d-none');
                            placeholderContainer.classList.remove('d-none');
                            placeholderContainer.classList.add('d-block');
                        }
                    });
                }
            }
        });
    </script>
</body>
</html>
