<!-- Sesi 2: Informasi Ruangan -->
<div class="section-wrapper" id="section-about">
    <div class="about-container" id="ruanganCard">
        
        <!-- Tombol Fullscreen -->
        <button class="btn-fullscreen" onclick="toggleFullscreen()" title="Buka Layar Penuh">
            <svg id="fsIcon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
            </svg>
        </button>

        <h1>INFORMASI RUANGAN</h1>
        
        <div class="room-list-wrapper">
            
            <?php if(empty($jadwal_peminjaman)): ?>
                <!-- Empty State -->
                <div class="empty-state" style="text-align: center; padding: 40px 20px; color: rgba(30, 41, 59, 0.5);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 15px; opacity: 0.5;">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                    <h3 style="font-size: 1.1rem; margin: 0 0 5px 0; color: rgba(30, 41, 59, 0.7);">Belum ada jadwal peminjaman</h3>
                    <p style="font-size: 0.9rem; margin: 0;">Jadwal peminjaman ruangan akan tampil di sini.</p>
                </div>
            <?php else: ?>
                <?php 
                    // Limit to maximum 4 rows
                    $limited_jadwal = array_slice($jadwal_peminjaman, 0, 4); 
                ?>
                <?php foreach($limited_jadwal as $j): ?>
                <div class="room-item">
                    <div class="room-item-left">
                        <div class="room-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <div class="room-info">
                            <h3><?= $j->kode_ruangan ?></h3>
                            <p><?= $j->nama_ruangan ?></p>
                        </div>
                    </div>
                    
                    <div class="room-item-tags">
                        <span class="tag">
                            <svg style="margin-right:4px; vertical-align:text-bottom" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <?= $j->nama_lengkap ?>
                        </span>
                        <span class="tag" style="background: rgba(234, 88, 12, 0.1); border-color: #ea580c; color: #ea580c;">
                            <?= substr($j->jam_mulai, 0, 5) ?> - <?= substr($j->jam_selesai, 0, 5) ?>
                        </span>
                    </div>

                    <div class="room-item-date">
                        <?php 
                            if ($j->tanggal_mulai == $j->tanggal_selesai) {
                                echo date('d M Y', strtotime($j->tanggal_mulai));
                            } else {
                                echo date('d M', strtotime($j->tanggal_mulai)) . ' - ' . date('d M Y', strtotime($j->tanggal_selesai));
                            }
                        ?> 
                    </div>

                    <div class="room-item-action">
                        <?php
                            $s = $j->status;
                            $dot   = '';
                            $bg    = '';
                            $color = '';
                            $label = '';

                            if ($s === 'Pending') {
                                $dot = '#f59e0b'; $bg = '#fffbeb'; $color = '#b45309'; $label = 'Menunggu';
                            } elseif (strpos($s, 'Ka. Ur') !== false) {
                                $dot = '#22c55e'; $bg = '#f0fdf4'; $color = '#166534'; $label = 'Disetujui Ka. Ur';
                            } elseif (strpos($s, 'Laboran') !== false) {
                                $dot = '#3b82f6'; $bg = '#eff6ff'; $color = '#1d4ed8'; $label = 'Disetujui Laboran';
                            } elseif (strpos($s, 'Admin') !== false) {
                                $dot = '#8b5cf6'; $bg = '#f5f3ff'; $color = '#6d28d9'; $label = 'Disetujui Admin';
                            } elseif (strpos($s, 'Disetujui') !== false) {
                                $dot = '#22c55e'; $bg = '#f0fdf4'; $color = '#166534'; $label = 'Disetujui';
                            } elseif ($s === 'Selesai') {
                                $dot = '#94a3b8'; $bg = '#f8fafc'; $color = '#475569'; $label = 'Selesai';
                            } else {
                                $dot = '#94a3b8'; $bg = '#f8fafc'; $color = '#475569'; $label = htmlspecialchars($s);
                            }
                        ?>
                        <span style="display:inline-flex; align-items:center; gap:6px; background:<?= $bg ?>; color:<?= $color ?>; border-radius:999px; padding:5px 13px; font-size:0.76rem; font-weight:700; white-space:nowrap; letter-spacing:0.01em;">
                            <span style="width:7px;height:7px;border-radius:50%;background:<?= $dot ?>;flex-shrink:0;"></span>
                            <?= $label ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>



        </div>

        <!-- Kalender Fullscreen (Hidden by default) -->
        <div class="gcal-wrapper" id="gcalWrapper" style="display: none;">
            <div class="gcal-header">
                <div class="gcal-header-left">
                    <button class="gcal-btn-today" onclick="goToToday()">Today</button>
                    <div class="gcal-nav-arrows">
                        <button onclick="prevWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                        <button onclick="nextWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                    </div>
                    <h2 class="gcal-month-title" id="gcalMonthTitle">August 2026</h2>
                </div>
                <div class="gcal-header-right">
                    <button class="gcal-btn-booking" onclick="openBookingModal()">+ Ajukan Booking</button>
                </div>
            </div>
            
            <div class="gcal-body">
                <div class="gcal-days-header" id="gcalDaysHeader">
                    <!-- Digenerate via JS -->
                </div>
                <div class="gcal-grid-scroll">
                    <div class="gcal-grid" id="gcalGrid">
                        <!-- Digenerate via JS -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Ajukan Booking (Public) -->
<div class="modal-overlay" id="bookingModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Buat Peminjaman</h2>
            <button class="modal-close" type="button" onclick="closeBookingModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="formAjukanBooking" onsubmit="submitBooking(event)">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap / NIM</label>
                    <input type="text" class="form-control" name="nama_lengkap" value="<?= $this->session->userdata('name') ?>" <?= $this->session->userdata('name') ? 'readonly style="background-color: #e2e8f0; cursor: not-allowed; color: #64748b;"' : 'placeholder="Masukkan nama peminjam"' ?> required>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori Ruangan</label>
                    <select class="form-control" id="kategoriSelectPublic" name="id_kategori" required>
                        <option value="">Pilih Kategori</option>
                        <?php if(!empty($kategori)): ?>
                            <?php foreach($kategori as $k): ?>
                                <option value="<?= $k->id ?>"><?= $k->nama_kategori ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Pilih Ruangan</label>
                    <select class="form-control" id="ruanganSelectPublic" name="id_ruangan" required>
                        <option value="">Pilih Kategori Dahulu</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Keterangan / Keperluan</label>
                    <textarea class="form-control" name="keterangan" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Peminjaman</label>
                    <input type="text" class="form-control" name="tanggal_peminjaman" id="tanggalPeminjaman" placeholder="Pilih Rentang Tanggal" required>
                </div>

                <div class="form-group" id="timeSelectionGroup" style="display: none;">
                    <label class="form-label">Waktu Peminjaman</label>
                    <div style="display: flex; gap: 15px; margin-top: 25px;">
                        <input type="text" class="form-control" name="jam_mulai" id="inputJamMulai" placeholder="Jam Mulai" readonly style="cursor: pointer;" onclick="openInlinePicker('mulai')" required>
                        <input type="text" class="form-control" name="jam_selesai" id="inputJamSelesai" placeholder="Jam Selesai" readonly style="cursor: pointer;" onclick="openInlinePicker('selesai')" required>
                    </div>

                    <!-- Inline Clock Picker (di dalam modal) -->
                    <div id="inlineClockPanel" style="display:none; margin-top: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px;">
                        <div style="display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap;">

                            <!-- Kiri: Display waktu & Pilih Cepat -->
                            <div style="flex: 1; min-width: 160px;">
                                <div id="inlineTpLabel" style="font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">Waktu dipilih</div>
                                <div style="font-size: 2.5rem; font-weight: 800; color: #1e293b; line-height: 1; margin-bottom: 6px;">
                                    <span id="tpDisplayHour" onclick="setMode('hour')" style="cursor:pointer;">14</span><span style="color:#e2e8f0;">:</span><span id="tpDisplayMinute" onclick="setMode('minute')" style="cursor:pointer; color:#cbd5e1;">30</span>
                                </div>
                                <div style="display:inline-block; background:#ede9fe; color:#7c3aed; font-size:0.7rem; font-weight:600; border-radius:20px; padding:2px 10px; margin-bottom:12px;">24 Jam</div>

                                <div style="font-size: 0.72rem; color: #7c3aed; font-weight: 700; margin-bottom: 6px;">
                                    ⚡ Pilih Cepat <span style="font-size:0.65rem;color:#94a3b8;font-weight:500;">(drag untuk rentang)</span>
                                </div>
                                <div id="tpTimeSlots" style="display:grid; grid-template-columns:1fr 1fr; gap:6px; user-select:none; margin-top:4px;">
                                    <div class="tp-slot" data-start="08:00" data-end="09:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">08:00 – 09:00</div>
                                    <div class="tp-slot" data-start="09:00" data-end="10:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">09:00 – 10:00</div>
                                    <div class="tp-slot" data-start="10:00" data-end="11:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">10:00 – 11:00</div>
                                    <div class="tp-slot" data-start="11:00" data-end="12:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">11:00 – 12:00</div>
                                    <div class="tp-slot" data-start="12:00" data-end="13:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">12:00 – 13:00</div>
                                    <div class="tp-slot" data-start="13:00" data-end="14:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">13:00 – 14:00</div>
                                    <div class="tp-slot" data-start="14:00" data-end="15:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">14:00 – 15:00</div>
                                    <div class="tp-slot" data-start="15:00" data-end="16:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">15:00 – 16:00</div>
                                    <div class="tp-slot" data-start="16:00" data-end="17:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">16:00 – 17:00</div>
                                    <div class="tp-slot" data-start="17:00" data-end="18:00" style="padding:9px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;font-size:0.72rem;font-weight:700;color:#475569;text-align:center;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);">17:00 – 18:00</div>
                                </div>
                            </div>

                            <!-- Kanan: Clock -->
                            <div style="flex: 1; min-width: 200px;">
                                <div class="tp-tabs" style="margin-bottom: 12px;">
                                    <div class="tp-tab active" id="tpTabHour" onclick="setMode('hour')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> Jam
                                    </div>
                                    <div class="tp-tab" id="tpTabMinute" onclick="setMode('minute')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg> Menit
                                    </div>
                                </div>
                                <div class="tp-clock-container" id="tpClockContainer">
                                    <div class="tp-clock-center"></div>
                                    <div class="tp-clock-hand" id="tpClockHand"></div>
                                    <div id="tpClockNumbers"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer tombol Terapkan -->
                        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:14px; border-top: 1px solid #e2e8f0; padding-top:12px;">
                            <button type="button" onclick="closeInlinePicker()" style="padding: 8px 18px; border-radius: 8px; border: 1px solid #e2e8f0; background:#fff; color:#64748b; font-size:0.85rem; cursor:pointer; font-weight:600;">Batal</button>
                            <button type="button" onclick="applyInlinePicker()" style="padding: 8px 18px; border-radius: 8px; border: none; background: #7c3aed; color:#fff; font-size:0.85rem; cursor:pointer; font-weight:600;">✔ Terapkan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="submit" form="formAjukanBooking" class="btn-primary">Simpan Peminjaman</button>
        </div>
    </div>
</div>

<script>
    // Passing data dari PHP ke JS (prevent redeclaration error)
    window.bookingData = <?= json_encode($jadwal_peminjaman ? $jadwal_peminjaman : []) ?: '[]' ?>;
    window.ajukanBookingUrl = '<?= base_url('dashboard/ajukan_booking') ?>';
</script>

<script>
    // ===== INLINE CLOCK PICKER FUNCTIONS =====
    var inlinePickerTarget = 'mulai';

    function openInlinePicker(target) {
        inlinePickerTarget = target;
        var panel = document.getElementById('inlineClockPanel');
        var label = document.getElementById('inlineTpLabel');
        
        // Update label
        label.innerText = (target === 'mulai') ? 'Jam Mulai' : 'Jam Selesai';

        // Pre-fill jam yang sudah ada
        var existingVal = (target === 'mulai')
            ? document.getElementById('inputJamMulai').value
            : document.getElementById('inputJamSelesai').value;

        if (existingVal) {
            var parts = existingVal.split(':');
            selectedHour   = parseInt(parts[0]) || 14;
            selectedMinute = parseInt(parts[1]) || 0;
        } else {
            selectedHour   = 14;
            selectedMinute = 0;
        }

        // Reset ke mode jam
        isSelectingHour = true;
        document.getElementById('tpTabHour').classList.add('active');
        document.getElementById('tpTabMinute').classList.remove('active');

        updateDisplay();
        panel.style.display = 'block';

        // Scroll ke bawah panel supaya kelihatan
        setTimeout(function() {
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 50);
    }

    function closeInlinePicker() {
        document.getElementById('inlineClockPanel').style.display = 'none';
    }

    function applyInlinePicker() {
        var hh = selectedHour.toString().padStart(2, '0');
        var mm = selectedMinute.toString().padStart(2, '0');
        var timeStr = hh + ':' + mm;

        if (inlinePickerTarget === 'mulai') {
            document.getElementById('inputJamMulai').value = timeStr;
        } else {
            document.getElementById('inputJamSelesai').value = timeStr;
        }
        closeInlinePicker();
    }
</script>

<script>
    $(document).ready(function() {
        // Inisialisasi Flatpickr
        var fpMode = "<?= ($this->session->userdata('role_id') == 1) ? 'range' : 'single' ?>";
        
        flatpickr("#tanggalPeminjaman", {
            mode: fpMode,
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    $('#timeSelectionGroup').slideDown();
                    
                    let displayStr = selectedDates[0].toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
                    if(selectedDates.length === 2 && selectedDates[0].getTime() !== selectedDates[1].getTime()) {
                        displayStr += " - " + selectedDates[1].toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
                    }
                    $('#tpDateDisplay').text(displayStr);
                    
                } else {
                    $('#timeSelectionGroup').slideUp();
                }
            }
        });

        // Dependent Dropdown for Ruangan
        $('#kategoriSelectPublic').change(function() {
            let id_kategori = $(this).val();
            if(id_kategori != '') {
                $.ajax({
                    url: "<?= base_url('kelolabooking/get_ruangan') ?>",
                    type: "POST",
                    data: {id_kategori: id_kategori},
                    dataType: "json",
                    success: function(data) {
                        if(data.length === 1) {
                            let room = data[0];
                            let html = `<option value="${room.id}" selected>${room.kode_ruangan} - ${room.nama_ruangan}</option>`;
                            $('#ruanganSelectPublic').html(html);
                            $('#ruanganSelectPublic').css({'background-color': '#e2e8f0', 'pointer-events': 'none', 'color': '#64748b'});
                        } else {
                            let html = '<option value="">Pilih Ruangan</option>';
                            $.each(data, function(i, room) {
                                html += `<option value="${room.id}">${room.kode_ruangan} - ${room.nama_ruangan}</option>`;
                            });
                            $('#ruanganSelectPublic').html(html);
                            $('#ruanganSelectPublic').css({'background-color': '#f8fafc', 'pointer-events': 'auto', 'color': 'var(--text-color)'});
                        }
                    }
                });
            } else {
                $('#ruanganSelectPublic').html('<option value="">Pilih Kategori Dahulu</option>');
                $('#ruanganSelectPublic').css({'background-color': '#f8fafc', 'pointer-events': 'auto', 'color': 'var(--text-color)'});
            }
        });
    });
</script>
