<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #fbf7f1; 
            --text-color: #1e293b;
            --border-color: rgba(30, 41, 59, 0.1);
            --primary: #ea580c;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.4);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            width: 100%;
            max-width: 600px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.1rem;
            color: rgba(30, 41, 59, 0.7);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: rgba(30, 41, 59, 0.5);
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            font-size: 0.75rem;
            color: rgba(30, 41, 59, 0.5);
            position: absolute;
            top: 10px;
            left: 16px;
            z-index: 1;
        }

        .form-control {
            width: 100%;
            padding: 28px 16px 10px;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--text-color);
            background: #f8fafc;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: #fff;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Time slot grid */
        .time-slots-container {
            margin-top: 10px;
        }

        .time-slots-label {
            font-size: 0.85rem;
            color: rgba(30, 41, 59, 0.5);
            margin-bottom: 10px;
        }

        .time-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .time-slot {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            background: #f8fafc;
            font-size: 0.75rem;
            color: rgba(30, 41, 59, 0.6);
        }

        .time-slot.active {
            border-color: var(--primary);
            background: rgba(234, 88, 12, 0.05);
            color: var(--primary);
        }

        .time-slot input[type="checkbox"] {
            display: none;
        }

        /* Checkbox pseudo-element */
        .custom-checkbox {
            width: 14px;
            height: 14px;
            border: 1px solid var(--border-color);
            border-radius: 3px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;
        }

        .time-slot.active .custom-checkbox {
            background: var(--primary);
            border-color: var(--primary);
        }

        .time-slot.active .custom-checkbox::after {
            content: "✓";
            color: #fff;
            font-size: 10px;
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
        }
        
        /* Dashboard Table */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .table th, .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .table th {
            background: #f8fafc;
            font-size: 0.85rem;
            color: rgba(30, 41, 59, 0.6);
            text-transform: uppercase;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-pending { background: #fef08a; color: #854d0e; }
        .badge-approved { background: #dcfce7; color: #166534; }
        
        /* Action Buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-approve { background: #dcfce7; color: #166534; }
        .btn-approve:hover { background: #bbf7d0; }
        .btn-reject { background: #fee2e2; color: #991b1b; }
        .btn-reject:hover { background: #fecaca; }
        .btn-delete { background: #f1f5f9; color: #64748b; }
        .btn-delete:hover { background: #e2e8f0; color: #0f172a; }
        
        /* Custom Flatpickr Theme */
        .flatpickr-calendar { font-family: 'Plus Jakarta Sans', sans-serif !important; border: 1px solid var(--border-color) !important; border-radius: 12px !important; box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay { background: var(--primary) !important; border-color: var(--primary) !important; }
    </style>
    <link rel="stylesheet" href="<?= base_url('assets/css/timepicker.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>

    <div class="header">
        <h1>Dashboard Peminjaman</h1>
        <button class="btn-primary" onclick="document.getElementById('bookingModal').classList.add('active')">
            + Buat Peminjaman
        </button>
    </div>

    <!-- Data Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Ruangan</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($peminjaman as $p): ?>
            <tr>
                <td><?= $p->nama_lengkap ?></td>
                <td><?= $p->kode_ruangan ?> - <?= $p->nama_ruangan ?></td>
                <td>
                    <?php 
                        if ($p->tanggal_mulai == $p->tanggal_selesai) {
                            echo date('d M Y', strtotime($p->tanggal_mulai));
                        } else {
                            echo date('d M', strtotime($p->tanggal_mulai)) . ' - ' . date('d M Y', strtotime($p->tanggal_selesai));
                        }
                    ?> 
                    <br>
                    <small style="color: #64748b;"><?= substr($p->jam_mulai, 0, 5) ?> - <?= substr($p->jam_selesai, 0, 5) ?></small>
                </td>
                <td><?= $p->keterangan ?></td>
                <td>
                    <?php if($p->status == 'Pending'): ?>
                        <span class="badge badge-pending">Pending</span>
                    <?php elseif($p->status == 'Disetujui'): ?>
                        <span class="badge badge-approved">Disetujui</span>
                    <?php elseif($p->status == 'Ditolak'): ?>
                        <span class="badge" style="background: #fee2e2; color: #991b1b;">Ditolak</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <?php if($p->status == 'Pending'): ?>
                            <button class="btn-action btn-approve" onclick="approveBooking(<?= $p->id ?>)" title="Setujui">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </button>
                            <button class="btn-action btn-reject" onclick="openRejectModal(<?= $p->id ?>)" title="Tolak">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        <?php endif; ?>
                        <button class="btn-action btn-delete" onclick="deleteBooking(<?= $p->id ?>)" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal Form -->
    <div class="modal-overlay" id="bookingModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Buat Peminjaman</h2>
                <button class="modal-close" onclick="document.getElementById('bookingModal').classList.remove('active')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formBooking">
                    
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" value="<?= $this->session->userdata('name') ?>" readonly style="background-color: #e2e8f0; cursor: not-allowed; color: #64748b;" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kategori Ruangan</label>
                        <select class="form-control" id="kategoriSelect" name="id_kategori" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach($kategori as $k): ?>
                                <option value="<?= $k->id ?>"><?= $k->nama_kategori ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ruangan</label>
                        <select class="form-control" id="ruanganSelect" name="id_ruangan" required>
                            <option value="">Pilih Ruangan</option>
                            <!-- Diisi via AJAX -->
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Peminjaman</label>
                        <input type="text" class="form-control" name="tanggal_peminjaman" id="tanggalPeminjaman" placeholder="Pilih Rentang Tanggal" required>
                    </div>

                    <div class="form-group" id="timeSelectionGroup" style="display: none;">
                        <label class="form-label">Waktu Peminjaman</label>
                        <div style="display: flex; gap: 15px; margin-top: 25px;">
                            <input type="text" class="form-control" name="jam_mulai" id="inputJamMulai" placeholder="Jam Mulai" readonly style="cursor: pointer;" onclick="openTimePicker('mulai')" required>
                            <input type="text" class="form-control" name="jam_selesai" id="inputJamSelesai" placeholder="Jam Selesai" readonly style="cursor: pointer;" onclick="openTimePicker('selesai')" required>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-primary" onclick="submitForm()">Simpan Peminjaman</button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal-overlay" id="rejectModal">
        <div class="modal">
            <div class="modal-header">
                <h2>Alasan Penolakan</h2>
                <button class="modal-close" onclick="closeRejectModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tuliskan alasan penolakan</label>
                    <textarea class="form-control" id="alasanPenolakan" placeholder="Ruangan sedang direnovasi..."></textarea>
                    <input type="hidden" id="rejectBookingId">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-primary" style="background: #ef4444;" onclick="submitReject()">Tolak Peminjaman</button>
            </div>
        </div>
    </div>

    <!-- Time Picker Custom Modal -->
    <div class="tp-modal-overlay" id="timePickerModal">
        <div class="tp-modal">
            <div class="tp-header">
                <div class="tp-header-title">
                    <div class="tp-icon-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div class="tp-header-text">
                        <h3>Pilih Waktu</h3>
                        <p>Pilih waktu yang sesuai</p>
                    </div>
                </div>
                <button class="modal-close" onclick="closeTimePicker()" style="border:none;background:none;font-size:1.5rem;cursor:pointer;">&times;</button>
            </div>
            
            <div class="tp-body">
                <div class="tp-left">
                    <div class="tp-selected-label">Waktu dipilih</div>
                    <div class="tp-time-display">
                        <span id="tpDisplayHour" onclick="setMode('hour')" style="cursor:pointer;">14</span><span class="colon">:</span><span id="tpDisplayMinute" onclick="setMode('minute')" style="cursor:pointer; color: #cbd5e1;">30</span>
                    </div>
                    <div class="tp-badge-24h">24 Jam</div>
                    
                    <div class="tp-date-display">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span id="tpDateDisplay">-</span>
                    </div>
                    
                    <div class="tp-quick-select-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg> Pilih Cepat
                    </div>
                    <div class="tp-quick-grid">
                        <button type="button" class="tp-quick-btn" onclick="setQuickTime(8,0)">08:00</button>
                        <button type="button" class="tp-quick-btn" onclick="setQuickTime(9,0)">09:00</button>
                        <button type="button" class="tp-quick-btn" onclick="setQuickTime(10,0)">10:00</button>
                        <button type="button" class="tp-quick-btn" onclick="setQuickTime(13,0)">13:00</button>
                        <button type="button" class="tp-quick-btn" onclick="setQuickTime(14,0)">14:00</button>
                        <button type="button" class="tp-quick-btn" onclick="setQuickTime(15,0)">15:00</button>
                        <button type="button" class="tp-quick-btn" onclick="setQuickTime(16,0)">16:00</button>
                        <button type="button" class="tp-quick-btn" onclick="setQuickTime(17,0)">17:00</button>
                        <button type="button" class="tp-quick-btn" onclick="setQuickTime(18,0)">18:00</button>
                    </div>
                </div>
                
                <div class="tp-right">
                    <div class="tp-tabs">
                        <div class="tp-tab active" id="tpTabHour" onclick="setMode('hour')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> Jam
                        </div>
                        <div class="tp-tab" id="tpTabMinute" onclick="setMode('minute')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg> Menit
                        </div>
                    </div>
                    
                    <div class="tp-clock-container" id="tpClockContainer">
                        <div class="tp-clock-center"></div>
                        <div class="tp-clock-hand" id="tpClockHand"></div>
                        <div id="tpClockNumbers">
                            <!-- JS Generated -->
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tp-footer">
                <button type="button" class="tp-btn tp-btn-cancel" onclick="closeTimePicker()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Batal
                </button>
                <button type="button" class="tp-btn tp-btn-apply" onclick="applyTimePicker()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> Terapkan
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="<?= base_url('assets/js/timepicker.js') ?>"></script>
    <script>
        $(document).ready(function() {
            // Initialize Flatpickr for date range
            flatpickr("#tanggalPeminjaman", {
                mode: "range",
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange: function(selectedDates, dateStr, instance) {
                    // Jika range tanggal sudah terisi (baik beda hari atau hari yang sama)
                    if (selectedDates.length > 0) {
                        $('#timeSelectionGroup').slideDown();
                        
                        // Set the date display in time picker modal
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
            $('#kategoriSelect').change(function() {
                let id_kategori = $(this).val();
                if(id_kategori != '') {
                    $.ajax({
                        url: "<?= base_url('adminbooking/get_ruangan') ?>",
                        method: "POST",
                        data: {id_kategori: id_kategori},
                        dataType: "json",
                        success: function(data) {
                            if(data.length === 1) {
                                // Hanya ada 1 ruangan, otomatis pilih dan beri style disabled
                                let room = data[0];
                                let html = `<option value="${room.id}" selected>${room.kode_ruangan} - ${room.nama_ruangan}</option>`;
                                $('#ruanganSelect').html(html);
                                $('#ruanganSelect').css({'background-color': '#e2e8f0', 'pointer-events': 'none', 'color': '#64748b'});
                            } else {
                                // Lebih dari 1 ruangan, munculkan dropdown normal
                                let html = '<option value="">Pilih Ruangan</option>';
                                $.each(data, function(i, room) {
                                    html += `<option value="${room.id}">${room.kode_ruangan} - ${room.nama_ruangan}</option>`;
                                });
                                $('#ruanganSelect').html(html);
                                $('#ruanganSelect').css({'background-color': '#f8fafc', 'pointer-events': 'auto', 'color': 'var(--text-color)'});
                            }
                        }
                    });
                } else {
                    $('#ruanganSelect').html('<option value="">Pilih Ruangan</option>');
                    $('#ruanganSelect').css({'background-color': '#f8fafc', 'pointer-events': 'auto', 'color': 'var(--text-color)'});
                }
            });
        });

        // Submit form via AJAX
        function submitForm() {
            const form = document.getElementById('formBooking');
            if(!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            
            $.ajax({
                url: '<?= base_url('adminbooking/submit_booking') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if(res.status == 'success') {
                        alert(res.message);
                        window.location.reload();
                    } else {
                        alert(res.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan pada server');
                }
            });
        }

        // Aksi Approve
        function approveBooking(id) {
            if(confirm('Apakah Anda yakin ingin menyetujui peminjaman ini?')) {
                $.ajax({
                    url: '<?= base_url('adminbooking/approve/') ?>' + id,
                    type: 'POST',
                    success: function(response) {
                        const res = JSON.parse(response);
                        if(res.status === 'success') {
                            window.location.reload();
                        } else {
                            alert(res.message);
                        }
                    }
                });
            }
        }

        // Aksi Reject
        function openRejectModal(id) {
            document.getElementById('rejectBookingId').value = id;
            document.getElementById('alasanPenolakan').value = '';
            document.getElementById('rejectModal').classList.add('active');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('active');
        }

        function submitReject() {
            const id = document.getElementById('rejectBookingId').value;
            const alasan = document.getElementById('alasanPenolakan').value;
            
            $.ajax({
                url: '<?= base_url('adminbooking/reject/') ?>' + id,
                type: 'POST',
                data: { alasan_penolakan: alasan },
                success: function(response) {
                    const res = JSON.parse(response);
                    if(res.status === 'success') {
                        window.location.reload();
                    } else {
                        alert(res.message);
                    }
                }
            });
        }

        // Aksi Delete
        function deleteBooking(id) {
            if(confirm('Data akan dihapus permanen. Apakah Anda yakin?')) {
                $.ajax({
                    url: '<?= base_url('adminbooking/delete/') ?>' + id,
                    type: 'POST',
                    success: function(response) {
                        const res = JSON.parse(response);
                        if(res.status === 'success') {
                            window.location.reload();
                        } else {
                            alert(res.message);
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
