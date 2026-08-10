<style>
    /* Styling khusus Sesi 2 */
    #section-about {
        background-color: var(--bg-color);
        display: flex;
        align-items: center;
        background-image: 
            radial-gradient(at 0% 0%, rgba(234, 88, 12, 0.1) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(234, 88, 12, 0.1) 0px, transparent 50%);
    }

    .about-container {
        /* Menggeser container ke sisi kanan layar secara dinamis */
        margin-left: auto;
        margin-right: 5vw;
        width: calc(100% - 45vw); /* Memaksa menyisakan 45% layar kiri untuk logo 3D */
        max-width: 1000px;
        padding: 50px 40px;
        z-index: 2;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.1);
        position: relative; /* Untuk penempatan tombol fullscreen absolute */
        transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    }

    /* State ketika Fullscreen Aktif */
    .about-container.is-fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        margin: 0;
        max-width: 100vw;
        width: 100vw;
        height: 100vh;
        border-radius: 0;
        z-index: 9999; /* Agar menutupi seluruh layar dan 3D model */
        background: var(--bg-color); /* Hapus transparan agar tidak tumpang tindih */
        display: flex;
        flex-direction: column;
    }

    /* Tombol Fullscreen di Kanan Atas */
    .btn-fullscreen {
        position: absolute;
        top: 25px;
        right: 25px;
        background: rgba(234, 88, 12, 0.1);
        border: 1px solid rgba(234, 88, 12, 0.3);
        color: #ea580c;
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-fullscreen:hover {
        background: #ea580c;
        color: #fff;
        transform: scale(1.1);
    }

    .about-container h1 {
        font-size: 3.5rem;
        margin-bottom: 25px;
        letter-spacing: -1px;
        font-weight: 800;
        color: var(--text-color);
    }

    /* Styling Tabel Informasi Ruangan */
    .room-table-wrapper {
        width: 100%;
        margin-top: 20px;
    }

    /* Table Carousel (Pagination) */
    .table-carousel-container {
        display: flex;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scrollbar-width: none; /* Firefox */
        width: 100%;
        scroll-behavior: smooth;
    }
    .table-carousel-container::-webkit-scrollbar { display: none; }

    .table-slide {
        flex: 0 0 100%;
        scroll-snap-align: start;
        padding-right: 10px;
    }

    .room-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .room-table th, .room-table td {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(234, 88, 12, 0.2);
    }

    .room-table th {
        font-weight: 700;
        color: #ea580c;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 1px;
    }

    .room-table td {
        color: var(--text-color);
        font-size: 1rem;
        font-weight: 500;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
    }
    .status-available { background: #dcfce7; color: #166534; }
    .status-booked { background: #fee2e2; color: #991b1b; }

    /* Pagination Controls */
    .table-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding: 0 10px;
    }
    
    .page-btn {
        background: transparent;
        border: 1px solid #ea580c;
        color: #ea580c;
        padding: 8px 20px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .page-btn:hover {
        background: #ea580c;
        color: #fff;
    }
    .page-dots {
        display: flex;
        gap: 8px;
    }
    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(234, 88, 12, 0.3);
        transition: 0.3s;
    }
    .dot.active {
        background: #ea580c;
        width: 24px;
        border-radius: 4px;
    }
</style>

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
        
        <div class="room-table-wrapper">
            
            <div class="table-carousel-container" id="roomTableCarousel">
                
                <!-- Halaman 1 (Slide 1) -->
                <div class="table-slide">
                    <table class="room-table">
                        <thead>
                            <tr>
                                <th>Ruangan</th>
                                <th>Waktu</th>
                                <th>Peminjaman</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- TODO: Koneksi ke Database XAMPP (CodeIgniter Model) -->
                            <!-- Logic Pagination Carousel di PHP:
                            <?php 
                            /* 
                            $chunks = array_chunk($ruangan_data, 5); // Pisah 5 baris per slide
                            foreach($chunks as $index => $page_data): 
                            */
                            ?>
                            <div class="table-slide">
                                <table class="room-table">
                                    ... loop baris ...
                                </table>
                            </div>
                            <?php /* endforeach; */ ?>
                            -->

                            <!-- Data Statis Sementara (Halaman 1) -->
                            <tr>
                                <td>Lab Multimedia 01</td>
                                <td>08:00 - 10:00</td>
                                <td>UKM FOTOGRAFI</td>
                                <td>Rapat Rutin Mingguan</td>
                                <td><span class="status-badge status-booked">Dipinjam</span></td>
                            </tr>
                            <tr>
                                <td>Studio Audio 02</td>
                                <td>10:00 - 12:00</td>
                                <td>Mahasiswa DKV</td>
                                <td>Tugas Akhir</td>
                                <td><span class="status-badge status-booked">Dipinjam</span></td>
                            </tr>
                            <tr>
                                <td>Lab Komputer Dasar</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td><span class="status-badge status-available">Tersedia</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Halaman 2 (Slide 2) -->
                <div class="table-slide">
                    <table class="room-table">
                        <thead>
                            <tr>
                                <th>Ruangan</th>
                                <th>Waktu</th>
                                <th>Peminjaman</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data Statis Sementara (Halaman 2) -->
                            <tr>
                                <td>Ruang Rapat 03</td>
                                <td>13:00 - 15:00</td>
                                <td>Dosen</td>
                                <td>Rapat Evaluasi</td>
                                <td><span class="status-badge status-booked">Dipinjam</span></td>
                            </tr>
                            <tr>
                                <td>Co-working Space</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td><span class="status-badge status-available">Tersedia</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div> <!-- End Carousel Container -->

            <!-- Pagination Controls -->
            <div class="table-pagination">
                <button class="page-btn" onclick="scrollRoomTable(-1)">&#8592; Prev</button>
                <div class="page-dots" id="tableDots">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                </div>
                <button class="page-btn" onclick="scrollRoomTable(1)">Next &#8594;</button>
            </div>

        </div>
    </div>
</div>

<script>
    // Fungsi untuk navigasi tombol Prev / Next pada tabel
    function scrollRoomTable(direction) {
        const carousel = document.getElementById('roomTableCarousel');
        const dots = document.querySelectorAll('#tableDots .dot');
        const slideWidth = carousel.clientWidth;
        
        // Geser carousel
        carousel.scrollBy({ left: direction * slideWidth, behavior: 'smooth' });
        
        // Update Dots (estimasi kasar berbasis timer)
        setTimeout(() => {
            let currentIndex = Math.round(carousel.scrollLeft / slideWidth);
            
            // Batasi index agar tidak error
            if(currentIndex < 0) currentIndex = 0;
            if(currentIndex >= dots.length) currentIndex = dots.length - 1;

            dots.forEach(dot => dot.classList.remove('active'));
            if(dots[currentIndex]) {
                dots[currentIndex].classList.add('active');
            }
        }, 300);
    }

    // Update Dots ketika di-scroll manual (menggunakan touchpad)
    document.getElementById('roomTableCarousel').addEventListener('scroll', (e) => {
        const carousel = e.target;
        const slideWidth = carousel.clientWidth;
        let currentIndex = Math.round(carousel.scrollLeft / slideWidth);
        const dots = document.querySelectorAll('#tableDots .dot');
        
        dots.forEach(dot => dot.classList.remove('active'));
        if(dots[currentIndex]) {
            dots[currentIndex].classList.add('active');
        }
    });

    // Fungsi Toggle Fullscreen
    function toggleFullscreen() {
        const card = document.getElementById('ruanganCard');
        const icon = document.getElementById('fsIcon');
        
        card.classList.toggle('is-fullscreen');
        
        if (card.classList.contains('is-fullscreen')) {
            // Ubah icon jadi minimize
            icon.innerHTML = '<path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"></path>';
        } else {
            // Ubah icon jadi maximize
            icon.innerHTML = '<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>';
        }
    }
</script>
