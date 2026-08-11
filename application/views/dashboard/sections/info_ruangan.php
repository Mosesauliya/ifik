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
                        <span class="btn-status booked" style="cursor: default;">Terjadwal</span>
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
<div id="bookingModal" class="gcal-modal">
    <div class="gcal-modal-content">
        <span class="gcal-modal-close" onclick="closeBookingModal()">&times;</span>
        <h2 style="margin-top:0; margin-bottom: 20px; font-weight: 700; color: var(--text-color);">Ajukan Booking Ruangan</h2>
        <form id="formAjukanBooking" onsubmit="submitBooking(event)">
            <div class="form-group-gcal">
                <label>Nama Lengkap / NIM</label>
                <input type="text" name="nama_lengkap" required placeholder="Masukkan nama peminjam">
            </div>
            <div class="form-group-gcal">
                <label>Pilih Ruangan</label>
                <select name="id_ruangan" required>
                    <option value="" disabled selected>-- Pilih Ruangan --</option>
                    <?php foreach($ruangan as $r): ?>
                        <option value="<?= $r->id ?>"><?= $r->nama_ruangan ?> (<?= $r->kode_ruangan ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group-gcal">
                <label>Tanggal Peminjaman (1 Hari)</label>
                <input type="date" name="tanggal_peminjaman" required>
            </div>
            <div class="form-row-gcal">
                <div class="form-group-gcal">
                    <label>Jam Mulai</label>
                    <input type="time" name="jam_mulai" required>
                </div>
                <div class="form-group-gcal">
                    <label>Jam Selesai</label>
                    <input type="time" name="jam_selesai" required>
                </div>
            </div>
            <div class="form-group-gcal">
                <label>Keterangan / Keperluan</label>
                <textarea name="keterangan" rows="3" placeholder="Contoh: Rapat HIMA"></textarea>
            </div>
            <button type="submit" class="gcal-btn-submit">Submit Booking</button>
        </form>
    </div>
</div>

<script>
    // Passing data dari PHP ke JS
    const bookingData = <?= json_encode($jadwal_peminjaman ? $jadwal_peminjaman : []) ?>;
</script>
