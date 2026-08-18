<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Pengaturan Header</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
          colors: { brand: '#ea580c' }
        }
      }
    }
  </script>
  <style>
    body { background-color: #fbf7f1; color: #1e293b; }
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(234, 88, 12, 0.15);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border-radius: 20px;
    }
    .btn-brand {
        background: #ea580c; color: #fff;
        transition: all 0.3s;
    }
    .btn-brand:hover {
        background: #c2410c; transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
    }
    .form-input {
        width: 100%; padding: 12px 16px; border-radius: 10px;
        border: 1px solid #cbd5e1; background: #fff;
        transition: all 0.3s; outline: none;
    }
    .form-input:focus {
        border-color: #ea580c; box-shadow: 0 0 0 3px rgba(234,88,12,0.1);
    }
    .dropzone {
        border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px 20px;
        text-align: center; background: #f8fafc; transition: all 0.3s;
        position: relative; cursor: pointer;
    }
    .dropzone.dragover { border-color: #ea580c; background: #fff7ed; }
    .dropzone input[type="file"] {
        position: absolute; width: 100%; height: 100%;
        top: 0; left: 0; opacity: 0; cursor: pointer;
    }
    .preview-container img, .preview-container video {
        max-height: 150px; border-radius: 8px; margin: 10px auto; object-fit: cover;
    }
  </style>
</head>
<body class="p-6 md:p-12">

  <div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900">Pengaturan Header</h1>
            <p class="text-gray-500 mt-1">Kelola teks, gambar dekanat, dan slider carousel di halaman depan.</p>
        </div>
        <a href="<?= base_url('dashboard') ?>" class="text-brand font-semibold hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
        <?= $this->session->flashdata('success') ?>
    </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
        <?= $this->session->flashdata('error') ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- KOLOM 1: Teks & Dekanat -->
        <div class="glass-card p-6 md:p-8">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Teks & Gambar Utama
            </h2>
            
            <form action="<?= base_url('adminheader/update_settings') ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-5">
                    <label class="block text-sm font-semibold mb-2">Judul Utama</label>
                    <input type="text" name="title" class="form-input" value="<?= htmlspecialchars($settings->title ?? '') ?>" required>
                </div>
                
                <div class="mb-5">
                    <label class="block text-sm font-semibold mb-2">Deskripsi (Mendukung Pagination Otomatis)</label>
                    <textarea name="description" class="form-input h-40" required><?= htmlspecialchars($settings->description ?? '') ?></textarea>
                    <p class="text-xs text-gray-500 mt-2">Teks yang sangat panjang akan otomatis dibagi menjadi beberapa slide "Read More" di frontend.</p>
                </div>


                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">Gambar Dekanat Saat Ini</label>
                    <?php if(!empty($settings->dekanat_image)): ?>
                        <div class="mb-3 bg-gray-50 rounded-lg p-4 border border-gray-200 inline-block">
                            <img src="<?= base_url('assets/images/' . $settings->dekanat_image) ?>" class="h-32 object-contain" alt="Dekanat">
                        </div>
                    <?php endif; ?>
                    <label class="block text-sm font-semibold mb-2">Upload Gambar Dekanat Baru (Opsional)</label>
                    <input type="file" name="dekanat_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-brand hover:file:bg-orange-100">
                </div>

                <button type="submit" class="btn-brand w-full py-3 rounded-xl font-bold text-lg">Simpan Perubahan Teks & Dekanat</button>
            </form>
        </div>

        <!-- KOLOM 2: Manajemen Slide -->
        <div class="flex flex-col gap-8">
            <div class="glass-card p-6 md:p-8">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Daftar Slide Background
                </h2>

                <div class="flex flex-col gap-4">
                    <?php if(!empty($slides)): foreach($slides as $slide): ?>
                    <div class="flex flex-col p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <?php if($slide->media_type == 'video'): ?>
                                    <div class="w-16 h-12 bg-gray-900 rounded flex items-center justify-center text-white text-xs font-bold overflow-hidden">
                                        VIDEO
                                    </div>
                                <?php else: ?>
                                    <img src="<?= base_url('assets/images/' . $slide->media_path) ?>" class="w-16 h-12 object-cover rounded border border-gray-100">
                                <?php endif; ?>
                                <div>
                                    <h4 class="font-bold text-gray-800"><?= htmlspecialchars($slide->label) ?></h4>
                                    <p class="text-xs text-gray-400 uppercase tracking-wide"><?= $slide->media_type ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="toggleEdit(<?= $slide->id ?>)" class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center hover:bg-blue-500 hover:text-white transition-colors" title="Edit Slide">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <a href="<?= base_url('adminheader/delete_slide/'.$slide->id) ?>" onclick="return confirm('Yakin ingin menghapus slide ini?')" class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors" title="Hapus Slide">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Form Edit (Hidden Default) -->
                        <div id="editForm_<?= $slide->id ?>" class="hidden mt-4 pt-4 border-t border-gray-100">
                            <form action="<?= base_url('adminheader/edit_slide/'.$slide->id) ?>" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="block text-xs font-semibold mb-1">Update Label</label>
                                    <input type="text" name="label" class="form-input text-sm" value="<?= htmlspecialchars($slide->label) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-semibold mb-1">Durasi (Detik)</label>
                                    <input type="number" name="duration" class="form-input text-sm" value="<?= htmlspecialchars($slide->duration ?? 4) ?>" min="1" required <?= ($slide->media_type == 'video') ? 'disabled title="Video otomatis menyesuaikan panjang aslinya"' : '' ?>>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-semibold mb-1">Ganti File Media (Opsional, seret file ke area ini)</label>
                                    <div class="dropzone dropzone-edit" id="dropzoneEdit_<?= $slide->id ?>">
                                        <input type="file" name="media_file" accept="image/*,video/*">
                                        <div class="preview-container text-gray-400 text-sm">
                                            <p>Klik atau seret file baru (Image/Video) ke sini</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" onclick="toggleEdit(<?= $slide->id ?>)" class="px-3 py-1 bg-gray-200 rounded text-sm text-gray-700 font-semibold hover:bg-gray-300">Batal</button>
                                    <button type="submit" class="px-3 py-1 bg-brand rounded text-sm text-white font-semibold hover:bg-orange-700">Simpan Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <p class="text-gray-500 text-sm">Belum ada slide. Silakan tambahkan.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="glass-card p-6 md:p-8">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Slide Baru
                </h2>
                
                <form action="<?= base_url('adminheader/add_slide') ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Label Indikator (Teks Tombol Bawah)</label>
                        <input type="text" name="label" class="form-input" placeholder="Contoh: Prestasi" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Durasi Slide (Detik)</label>
                        <input type="number" name="duration" class="form-input" value="4" min="1" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2">Media File (Gambar atau Video)</label>
                        <div class="dropzone" id="dropzoneAdd">
                            <input type="file" name="media_file" id="mediaFile" accept="image/*,video/*" required>
                            <div class="preview-container text-gray-400" id="previewAdd">
                                <svg class="w-10 h-10 mx-auto mb-2 text-brand opacity-50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"></path></svg>
                                <p class="font-semibold text-gray-700">Tarik & Lepas File di Sini</p>
                                <p class="text-xs mt-1">atau klik untuk mencari file (JPG, PNG, MP4)</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-brand w-full py-3 rounded-xl font-bold text-lg">Tambah Slide</button>
                </form>
            </div>
        </div>

    </div>
  </div>

  <script>
      function toggleEdit(id) {
          const form = document.getElementById('editForm_' + id);
          if(form.classList.contains('hidden')) {
              form.classList.remove('hidden');
          } else {
              form.classList.add('hidden');
          }
      }

      function setupDropzone(dropzoneId) {
          const dropzone = document.getElementById(dropzoneId);
          if (!dropzone) return;
          
          const fileInput = dropzone.querySelector('input[type="file"]');
          const previewContainer = dropzone.querySelector('.preview-container');

          ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
              dropzone.addEventListener(eventName, preventDefaults, false);
          });

          function preventDefaults (e) {
              e.preventDefault();
              e.stopPropagation();
          }

          ['dragenter', 'dragover'].forEach(eventName => {
              dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
          });

          ['dragleave', 'drop'].forEach(eventName => {
              dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
          });

          dropzone.addEventListener('drop', (e) => {
              let dt = e.dataTransfer;
              let files = dt.files;
              if(files.length) {
                  fileInput.files = files;
                  handleFiles(files[0], previewContainer);
              }
          });

          fileInput.addEventListener('change', function() {
              if (this.files && this.files[0]) {
                  handleFiles(this.files[0], previewContainer);
              }
          });
      }

      function handleFiles(file, container) {
          container.innerHTML = ''; // clear preview
          
          if (file.type.startsWith('video/')) {
              const video = document.createElement('video');
              video.src = URL.createObjectURL(file);
              video.controls = true;
              container.appendChild(video);

              const form = container.closest('form');
              if (form) {
                  const durationInput = form.querySelector('input[name="duration"]');
                  if (durationInput) {
                      durationInput.disabled = true;
                      durationInput.title = "Video otomatis menyesuaikan panjang aslinya";
                  }
              }
          } else if (file.type.startsWith('image/')) {
              const img = document.createElement('img');
              img.src = URL.createObjectURL(file);
              container.appendChild(img);

              const form = container.closest('form');
              if (form) {
                  const durationInput = form.querySelector('input[name="duration"]');
                  if (durationInput) {
                      durationInput.disabled = false;
                      durationInput.title = "";
                  }
              }
          }
          
          const text = document.createElement('p');
          text.className = "text-xs mt-2 font-bold text-gray-700";
          text.innerText = file.name;
          container.appendChild(text);
      }

      // Initialize dropzones
      document.addEventListener('DOMContentLoaded', () => {
          setupDropzone('dropzoneAdd');
          const editDropzones = document.querySelectorAll('.dropzone-edit');
          editDropzones.forEach(dz => setupDropzone(dz.id));
      });
  </script>
</body>
</html>
