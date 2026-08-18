<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Peminjaman Ruangan - IFIK</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- jQuery & SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Info Ruangan & Calendar CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/info_ruangan.css?v=' . filemtime(FCPATH . 'assets/css/info_ruangan.css')) ?>">

    <style>
        :root {
            --bg-color: #fbf7f1;
            --text-color: #1e293b;
            --primary: #ea580c;
            --primary-hover: #c2410c;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            overflow: hidden;
        }

        .gcal-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 28px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            height: 70px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            gap: 16px;
        }

        .btn-back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 8px 18px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .btn-back-home:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: #fff;
            transform: translateX(-2px);
        }

        .btn-ajukan-booking {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 9px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.3);
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-ajukan-booking:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(234, 88, 12, 0.4);
        }

        /* ===== SEARCH BAR & AUTOCOMPLETE STYLES ===== */
        .calendar-search-wrap {
            position: relative;
            width: 320px;
        }

        #calendarSearchInput {
            width: 100%;
            padding: 9px 36px 9px 40px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.86rem;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }
        #calendarSearchInput:focus {
            border-color: var(--primary) !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.12) !important;
        }
        #calendarSearchInput::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        #calendarAutocompleteDropdown {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 14px 35px rgba(0,0,0,0.12);
            max-height: 340px;
            overflow-y: auto;
            z-index: 10000;
            padding: 6px;
        }

        .search-autocomplete-item {
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s ease;
            border-bottom: 1px solid #f1f5f9;
        }
        .search-autocomplete-item:last-child {
            border-bottom: none;
        }
        .search-autocomplete-item:hover {
            background: #fff7ed;
        }

        .search-autocomplete-item .match-highlight {
            background: #ffedd5;
            color: #ea580c;
            font-weight: 700;
            border-radius: 2px;
            padding: 0 2px;
        }

        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        .modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-content {
            background: #ffffff;
            width: 100%;
            max-width: 520px;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #94a3b8;
            cursor: pointer;
        }
        .modal-close:hover { color: #1e293b; }

        .swal2-container { z-index: 999999 !important; }

        @media (max-width: 900px) {
            .calendar-search-wrap { width: 200px; }
        }
        @media (max-width: 640px) {
            .gcal-page-header { flex-wrap: wrap; height: auto; padding: 12px 16px; }
            .calendar-search-wrap { width: 100%; order: 3; }
        }
    </style>
</head>
<body>

    <!-- Header Kalender Full Page -->
    <div class="gcal-page-header">
        <div class="gcal-header-left" style="display: flex; align-items: center; gap: 16px;">
            <button class="gcal-btn-today" onclick="goToToday()">Today</button>
            <div class="gcal-nav-arrows" style="display: flex; gap: 4px;">
                <button onclick="prevWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                <button onclick="nextWeek()"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
            </div>
            <h2 class="gcal-month-title" id="gcalMonthTitle" style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;">-</h2>
        </div>

        <!-- SEARCH BAR WITH AUTOCOMPLETE -->
        <div class="calendar-search-wrap">
            <div style="position: relative; display: flex; align-items: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5" style="position: absolute; left: 14px; pointer-events: none;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" id="calendarSearchInput" placeholder="Cari ruangan, peminjam, atau kode..." 
                       oninput="handleCalendarSearchInput(this.value)"
                       onfocus="handleCalendarSearchInput(this.value)"
                       autocomplete="off">
                <button id="clearSearchBtn" onclick="clearCalendarSearch()" style="display: none; position: absolute; right: 10px; background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 1.1rem; line-height: 1; padding: 2px;">&times;</button>
            </div>

            <!-- Autocomplete Popup Dropdown -->
            <div id="calendarAutocompleteDropdown"></div>
        </div>

        <div class="gcal-header-right" style="display: flex; align-items: center; gap: 12px;">
            <a href="<?= base_url() ?>" class="btn-back-home">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Kembali
            </a>

            <?php if ($this->session->userdata('logged_in')): ?>
                <a href="<?= base_url('ajukan-booking') ?>" class="btn-ajukan-booking">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Ajukan Booking
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Container Utama Grid Kalender -->
    <div class="gcal-body" style="height: calc(100vh - 70px);">
        <div class="gcal-days-header" id="gcalDaysHeader">
            <!-- Digenerate via JS -->
        </div>
        <div class="gcal-grid-scroll">
            <div class="gcal-grid" id="gcalGrid">
                <!-- Digenerate via JS -->
            </div>
        </div>
    </div>

    <!-- Modal Detail & Approval Peminjaman -->
    <div class="modal-overlay" id="detailBookingModal">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Detail Peminjaman Ruangan</h2>
                <button class="modal-close" type="button" onclick="closeDetailBookingModal()">&times;</button>
            </div>
            <div class="modal-body" style="padding: 20px 24px; max-height: 80vh; overflow-y: auto;">
                <input type="hidden" id="detailBookingId">
                
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 10px;">
                        <div>
                            <span id="detailKodeRuangan" style="display: inline-block; background: #ede9fe; color: #7c3aed; font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-bottom: 4px;"></span>
                            <h3 id="detailNamaRuangan" style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a;"></h3>
                        </div>
                        <div id="detailStatusBadge"></div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px; font-size: 0.88rem; color: #334155; border-top: 1px solid #e2e8f0; padding-top: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <strong>Peminjam:</strong> <span id="detailNamaLengkap"></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <strong>Tanggal:</strong> <span id="detailTanggal"></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <strong>Waktu:</strong> <span id="detailWaktu"></span>
                        </div>
                        <div style="display: flex; align-items: flex-start; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" style="margin-top: 2px; flex-shrink:0;"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                            <strong>Keterangan:</strong> <span id="detailKeterangan" style="color: #475569;"></span>
                        </div>
                        <div id="detailAlasanContainer" style="display: none; background: #fef2f2; border-left: 3px solid #ef4444; padding: 8px 12px; border-radius: 6px; margin-top: 4px;">
                            <strong style="color: #991b1b;">Alasan Penolakan:</strong> <span id="detailAlasanPenolakan" style="color: #7f1d1d;"></span>
                        </div>
                    </div>
                </div>

                <!-- Panel Aksi Approval -->
                <div id="approvalActionPanel" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 16px; margin-bottom: 12px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 0.88rem; font-weight: 700; color: #166534; display: flex; align-items: center; gap: 6px;">
                        ⚡ Persetujuan Peminjaman (<span id="approvalRoleLabel"></span>)
                    </h4>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" onclick="approveBookingAction()" style="flex: 1; background: #16a34a; color: #fff; border: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Setujui
                        </button>
                        <button type="button" onclick="toggleRejectInput()" style="flex: 1; background: #dc2626; color: #fff; border: none; padding: 10px 14px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            Tolak
                        </button>
                    </div>

                    <div id="rejectReasonBox" style="display: none; margin-top: 12px; border-top: 1px dashed #cbd5e1; padding-top: 12px;">
                        <label style="font-size: 0.8rem; font-weight: 600; color: #991b1b; display: block; margin-bottom: 6px;">Alasan Penolakan (Opsional):</label>
                        <textarea id="rejectReasonInput" rows="2" style="width: 100%; font-size: 0.85rem; padding: 8px 12px; border: 1px solid #fca5a5; border-radius: 8px; margin-bottom: 8px; box-sizing: border-box;" placeholder="Tuliskan alasan penolakan..."></textarea>
                        <button type="button" onclick="rejectBookingAction()" style="width: 100%; background: #991b1b; color: #fff; border: none; padding: 8px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer;">Konfirmasi Penolakan</button>
                    </div>
                </div>

                <!-- Panel Aksi Hapus Jadwal -->
                <div id="deleteActionPanel" style="display: none; margin-top: 8px;">
                    <button type="button" onclick="deleteBookingAction()" style="width: 100%; background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; padding: 10px 14px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        Hapus Jadwal Peminjaman
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Logic -->
    <script>
        window.bookingData = <?= json_encode($jadwal_peminjaman ? $jadwal_peminjaman : []) ?: '[]' ?>;
        window.isLoggedIn = <?= $this->session->userdata('logged_in') ? 'true' : 'false' ?>;
        window.userRoleId = <?= json_encode($this->session->userdata('role_id')) ?>;

        window.ajukanBookingUrl = '<?= base_url('ajukan-booking') ?>';
        window.approveBookingUrl = '<?= base_url('dashboard/approve_booking') ?>';
        window.rejectBookingUrl = '<?= base_url('dashboard/reject_booking') ?>';
        window.deleteBookingUrl = '<?= base_url('dashboard/delete_booking') ?>';
        window.getUpdatedBookingsUrl = '<?= base_url('dashboard/get_updated_bookings') ?>';

        let currentWeekStart = new Date();
        currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
        let currentSearchQuery = '';

        function renderCalendar() {
            renderHeaderAndDays();
            renderTimeGridAndEvents();
        }

        function renderHeaderAndDays() {
            const daysHeaderContainer = document.getElementById('gcalDaysHeader');
            const monthTitle = document.getElementById('gcalMonthTitle');
            const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];

            let monthName = currentWeekStart.toLocaleString('en-US', { month: 'long' });
            let year = currentWeekStart.getFullYear();
            monthTitle.innerText = `${monthName} ${year}`;

            const today = new Date();
            let headerHTML = '';

            for (let i = 0; i < 7; i++) {
                let dayDate = new Date(currentWeekStart);
                dayDate.setDate(currentWeekStart.getDate() + i);

                let isToday = (dayDate.toDateString() === today.toDateString()) ? 'active' : '';

                headerHTML += `
                    <div class="gcal-day-header">
                        <span class="gcal-day-name">${days[i]}</span>
                        <span class="gcal-day-num ${isToday}">${dayDate.getDate()}</span>
                    </div>
                `;
            }
            daysHeaderContainer.innerHTML = headerHTML;
        }

        function renderTimeGridAndEvents() {
            const gridContainer = document.getElementById('gcalGrid');

            let timeColHTML = `<div class="gcal-time-col">`;
            for (let i = 7; i <= 22; i++) {
                const hourStr = i.toString().padStart(2, '0') + ':00';
                timeColHTML += `<div class="gcal-time-label"><span>${hourStr}</span></div>`;
            }
            timeColHTML += `</div>`;

            let dayColsHTML = `<div class="gcal-day-cols">`;
            for (let i = 0; i < 7; i++) {
                let dayDate = new Date(currentWeekStart);
                dayDate.setDate(currentWeekStart.getDate() + i);
                const y = dayDate.getFullYear();
                const m = String(dayDate.getMonth() + 1).padStart(2, '0');
                const d = String(dayDate.getDate()).padStart(2, '0');
                let dateString = `${y}-${m}-${d}`;

                dayColsHTML += `<div class="gcal-day-col" id="col-${dateString}">`;
                dayColsHTML += generateEventsForDay(dateString);
                dayColsHTML += `</div>`;
            }
            dayColsHTML += `</div>`;

            gridContainer.innerHTML = timeColHTML + dayColsHTML;
        }

        function getStatusStyle(status) {
            const s = (status || '').toLowerCase();
            if (s === 'pending') {
                return { bg: '#f59e0b', border: '#d97706', badgeBg: '#fffbeb', badgeColor: '#b45309', dot: '#f59e0b', label: 'Menunggu Persetujuan' };
            } else if (s.includes('ka. ur')) {
                return { bg: '#10b981', border: '#059669', badgeBg: '#f0fdf4', badgeColor: '#166534', dot: '#22c55e', label: 'Disetujui Ka. Ur' };
            } else if (s.includes('laboran')) {
                return { bg: '#3b82f6', border: '#2563eb', badgeBg: '#eff6ff', badgeColor: '#1d4ed8', dot: '#3b82f6', label: 'Disetujui Laboran' };
            } else if (s.includes('admin')) {
                return { bg: '#8b5cf6', border: '#7c3aed', badgeBg: '#f5f3ff', badgeColor: '#6d28d9', dot: '#8b5cf6', label: 'Disetujui Admin' };
            } else if (s.includes('disetujui')) {
                return { bg: '#10b981', border: '#059669', badgeBg: '#f0fdf4', badgeColor: '#166534', dot: '#22c55e', label: 'Disetujui' };
            } else if (s === 'ditolak') {
                return { bg: '#ef4444', border: '#dc2626', badgeBg: '#fef2f2', badgeColor: '#991b1b', dot: '#ef4444', label: 'Ditolak' };
            } else if (s === 'selesai') {
                return { bg: '#64748b', border: '#475569', badgeBg: '#f8fafc', badgeColor: '#475569', dot: '#94a3b8', label: 'Selesai' };
            }
            return { bg: '#7c3aed', border: '#6d28d9', badgeBg: '#f5f3ff', badgeColor: '#6d28d9', dot: '#7c3aed', label: status };
        }

        function generateEventsForDay(targetDateStr) {
            if (typeof bookingData === 'undefined') return '';
            let eventsHTML = '';
            const pxPerHour = 48;
            const q = currentSearchQuery.trim().toLowerCase();

            bookingData.forEach(booking => {
                if (booking.tanggal_mulai <= targetDateStr && booking.tanggal_selesai >= targetDateStr) {
                    let startHour = 0, startMin = 0, endHour = 24, endMin = 0;

                    if (booking.tanggal_mulai === targetDateStr) {
                        const p = booking.jam_mulai.split(':');
                        startHour = parseInt(p[0]);
                        startMin  = parseInt(p[1]);
                    }
                    if (booking.tanggal_selesai === targetDateStr) {
                        const p = booking.jam_selesai.split(':');
                        endHour = parseInt(p[0]);
                        endMin  = parseInt(p[1]);
                    }

                    const gridStartHour = 7;
                    const topPx    = ((startHour - gridStartHour + 1) + startMin / 60) * pxPerHour;
                    const endPx    = ((endHour   - gridStartHour + 1) + endMin   / 60) * pxPerHour;
                    const heightPx = Math.max(endPx - topPx, 24);

                    const st = getStatusStyle(booking.status);
                    const timeLabel = `${startHour}:${startMin.toString().padStart(2,'0')} - ${endHour}:${endMin.toString().padStart(2,'0')}`;

                    // Realtime search filter on grid events
                    let isMatched = true;
                    if (q !== '') {
                        const kode = (booking.kode_ruangan || '').toLowerCase();
                        const namaR = (booking.nama_ruangan || '').toLowerCase();
                        const namaL = (booking.nama_lengkap || '').toLowerCase();
                        const ket = (booking.keterangan || '').toLowerCase();
                        isMatched = kode.includes(q) || namaR.includes(q) || namaL.includes(q) || ket.includes(q);
                    }

                    let opacityStyle = isMatched ? 'opacity: 1;' : 'opacity: 0.15; filter: grayscale(90%);';
                    let highlightStyle = (q !== '' && isMatched) ? 'box-shadow: 0 0 0 3px #ea580c; z-index: 20;' : '';

                    eventsHTML += `
                        <div class="gcal-event" onclick="openDetailBookingModal(${booking.id})" 
                             style="top:${topPx}px; height:${heightPx}px; background:${st.bg}; border-left:3px solid ${st.border}; cursor:pointer; ${opacityStyle} ${highlightStyle}"
                             title="${booking.nama_ruangan} — ${booking.nama_lengkap} (${st.label})">
                            <div class="gcal-event-title">${booking.nama_ruangan}</div>
                            <div class="gcal-event-time">${timeLabel}</div>
                            <div class="gcal-event-status">${st.label}</div>
                        </div>
                    `;
                }
            });

            return eventsHTML;
        }

        // ===== SEARCH & AUTOCOMPLETE LOGIC =====
        function handleCalendarSearchInput(query) {
            currentSearchQuery = query;
            const dropdown = document.getElementById('calendarAutocompleteDropdown');
            const clearBtn = document.getElementById('clearSearchBtn');
            const q = query.trim().toLowerCase();

            if (clearBtn) clearBtn.style.display = (query.length > 0) ? 'block' : 'none';

            // Filter grid events in realtime
            renderTimeGridAndEvents();

            if (!q) {
                dropdown.style.display = 'none';
                return;
            }

            if (typeof bookingData === 'undefined' || bookingData.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            // Search matching bookings
            const matches = bookingData.filter(b => {
                const kode = (b.kode_ruangan || '').toLowerCase();
                const namaR = (b.nama_ruangan || '').toLowerCase();
                const namaL = (b.nama_lengkap || '').toLowerCase();
                const ket = (b.keterangan || '').toLowerCase();
                return kode.includes(q) || namaR.includes(q) || namaL.includes(q) || ket.includes(q);
            });

            if (matches.length === 0) {
                dropdown.innerHTML = `
                    <div style="padding: 14px; text-align: center; color: #94a3b8; font-size: 0.85rem; font-weight: 600;">
                        🔍 Tidak ditemukan hasil untuk "${escapeHtml(query)}"
                    </div>
                `;
                dropdown.style.display = 'block';
                return;
            }

            // Limit to top 8 matches for clean UI
            const limitedMatches = matches.slice(0, 8);
            let html = '';

            limitedMatches.forEach(b => {
                const jMulai = b.jam_mulai ? b.jam_mulai.substring(0, 5) : '00:00';
                const jSelesai = b.jam_selesai ? b.jam_selesai.substring(0, 5) : '00:00';
                const st = getStatusStyle(b.status);

                html += `
                    <div class="search-autocomplete-item" onclick="selectSearchResult(${b.id}, '${b.tanggal_mulai}')">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="background: #ede9fe; color: #7c3aed; font-size: 0.72rem; font-weight: 700; padding: 2px 8px; border-radius: 12px;">${highlightMatchText(b.kode_ruangan || '', q)}</span>
                                <strong style="font-size: 0.88rem; color: #0f172a;">${highlightMatchText(b.nama_ruangan || '', q)}</strong>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 700; color: #64748b;">${b.tanggal_mulai}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; color: #475569;">
                            <span>👤 ${highlightMatchText(b.nama_lengkap || '', q)}</span>
                            <span style="display: flex; align-items: center; gap: 6px;">
                                ⏱ ${jMulai} - ${jSelesai}
                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: ${st.dot};"></span>
                            </span>
                        </div>
                    </div>
                `;
            });

            dropdown.innerHTML = html;
            dropdown.style.display = 'block';
        }

        function highlightMatchText(text, query) {
            if (!query) return escapeHtml(text);
            const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp(`(${escapedQuery})`, 'gi');
            return escapeHtml(text).replace(regex, '<span class="match-highlight">$1</span>');
        }

        function escapeHtml(str) {
            return (str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function selectSearchResult(bookingId, targetDateStr) {
            // Hide autocomplete dropdown
            document.getElementById('calendarAutocompleteDropdown').style.display = 'none';

            // Jump week view to target date
            if (targetDateStr) {
                const parts = targetDateStr.split('-');
                if (parts.length === 3) {
                    const targetDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    currentWeekStart = new Date(targetDate);
                    currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
                    renderCalendar();
                }
            }

            // Open Detail Modal
            openDetailBookingModal(bookingId);
        }

        function clearCalendarSearch() {
            const input = document.getElementById('calendarSearchInput');
            if (input) input.value = '';
            handleCalendarSearchInput('');
        }

        // Close autocomplete when clicking outside
        document.addEventListener('click', function(e) {
            const wrap = document.querySelector('.calendar-search-wrap');
            if (wrap && !wrap.contains(e.target)) {
                const dropdown = document.getElementById('calendarAutocompleteDropdown');
                if (dropdown) dropdown.style.display = 'none';
            }
        });

        function nextWeek() { currentWeekStart.setDate(currentWeekStart.getDate() + 7); renderCalendar(); }
        function prevWeek() { currentWeekStart.setDate(currentWeekStart.getDate() - 7); renderCalendar(); }
        function goToToday() { currentWeekStart = new Date(); currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay()); renderCalendar(); }

        function openDetailBookingModal(id) {
            if (typeof bookingData === 'undefined') return;
            const booking = bookingData.find(b => parseInt(b.id) === parseInt(id));
            if (!booking) return;

            document.getElementById('detailBookingId').value = booking.id;
            document.getElementById('detailKodeRuangan').innerText = booking.kode_ruangan || '';
            document.getElementById('detailNamaRuangan').innerText = booking.nama_ruangan || '';
            document.getElementById('detailNamaLengkap').innerText = booking.nama_lengkap || '-';

            let tglStr = booking.tanggal_mulai;
            if (booking.tanggal_selesai && booking.tanggal_selesai !== booking.tanggal_mulai) {
                tglStr += ' s/d ' + booking.tanggal_selesai;
            }
            document.getElementById('detailTanggal').innerText = tglStr;

            const jMulai = booking.jam_mulai ? booking.jam_mulai.substring(0, 5) : '00:00';
            const jSelesai = booking.jam_selesai ? booking.jam_selesai.substring(0, 5) : '00:00';
            document.getElementById('detailWaktu').innerText = jMulai + ' - ' + jSelesai;
            document.getElementById('detailKeterangan').innerText = booking.keterangan || '-';

            const st = getStatusStyle(booking.status);
            document.getElementById('detailStatusBadge').innerHTML = `
                <span style="display:inline-flex; align-items:center; gap:6px; background:${st.badgeBg}; color:${st.badgeColor}; border-radius:999px; padding:5px 13px; font-size:0.76rem; font-weight:700; white-space:nowrap;">
                    <span style="width:7px;height:7px;border-radius:50%;background:${st.dot};flex-shrink:0;"></span>
                    ${st.label}
                </span>
            `;

            const alasBox = document.getElementById('detailAlasanContainer');
            if (booking.status === 'Ditolak' && booking.alasan_penolakan) {
                document.getElementById('detailAlasanPenolakan').innerText = booking.alasan_penolakan;
                alasBox.style.display = 'block';
            } else {
                alasBox.style.display = 'none';
            }

            const roleId = parseInt(window.userRoleId);
            const approvePanel = document.getElementById('approvalActionPanel');
            const deletePanel = document.getElementById('deleteActionPanel');
            const rejectBox = document.getElementById('rejectReasonBox');
            if (rejectBox) rejectBox.style.display = 'none';

            const isAuthorized = [1, 2, 3].includes(roleId);

            if (isAuthorized && booking.status === 'Pending') {
                let roleName = 'Admin';
                if (roleId === 3) roleName = 'Ka. Ur';
                else if (roleId === 2) roleName = 'Laboran';

                document.getElementById('approvalRoleLabel').innerText = roleName;
                approvePanel.style.display = 'block';
            } else {
                approvePanel.style.display = 'none';
            }

            if (isAuthorized && deletePanel) {
                deletePanel.style.display = 'block';
            } else if (deletePanel) {
                deletePanel.style.display = 'none';
            }

            document.getElementById('detailBookingModal').classList.add('show');
        }

        function closeDetailBookingModal() {
            document.getElementById('detailBookingModal').classList.remove('show');
        }

        function approveBookingAction() {
            const id = document.getElementById('detailBookingId').value;
            if (!id) return;

            Swal.fire({
                title: 'Setujui Peminjaman',
                text: 'Apakah Anda yakin ingin menyetujui peminjaman ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(window.approveBookingUrl + '/' + id, { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Disetujui!', data.message, 'success');
                            closeDetailBookingModal();
                            reloadBookingData();
                        } else {
                            Swal.fire('Gagal', data.message, 'error');
                        }
                    }).catch(err => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
                }
            });
        }

        function toggleRejectInput() {
            const box = document.getElementById('rejectReasonBox');
            box.style.display = (box.style.display === 'none') ? 'block' : 'none';
        }

        function rejectBookingAction() {
            const id = document.getElementById('detailBookingId').value;
            const alasan = document.getElementById('rejectReasonInput').value;
            if (!id) return;

            const formData = new FormData();
            formData.append('alasan_penolakan', alasan);

            fetch(window.rejectBookingUrl + '/' + id, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Ditolak', data.message, 'success');
                    closeDetailBookingModal();
                    reloadBookingData();
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            }).catch(err => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
        }

        function deleteBookingAction() {
            const id = document.getElementById('detailBookingId').value;
            if (!id) return;

            Swal.fire({
                title: 'Hapus Jadwal',
                text: 'Apakah Anda yakin ingin menghapus jadwal peminjaman ini secara permanen?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(window.deleteBookingUrl + '/' + id, { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Terhapus!', data.message, 'success');
                            closeDetailBookingModal();
                            reloadBookingData();
                        } else {
                            Swal.fire('Gagal', data.message, 'error');
                        }
                    }).catch(err => Swal.fire('Error', 'Terjadi kesalahan pada server', 'error'));
                }
            });
        }

        function reloadBookingData() {
            fetch(window.getUpdatedBookingsUrl)
            .then(r => r.json())
            .then(data => {
                window.bookingData = data;
                renderCalendar();
            }).catch(err => console.error(err));
        }

        $(document).ready(function() {
            renderCalendar();
        });
    </script>

</body>
</html>