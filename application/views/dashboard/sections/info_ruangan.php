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
    </div>
</div>
