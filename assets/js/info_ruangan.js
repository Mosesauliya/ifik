    let currentWeekStart = new Date(); // Hari ini sebagai acuan minggu
    currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay()); // Set ke hari Minggu

    // ==========================================
    // FUNGSI TOGGLE FULLSCREEN
    // ==========================================
    function toggleFullscreen() {
        const card = document.getElementById('ruanganCard');
        const icon = document.getElementById('fsIcon');
        const listWrapper = document.querySelector('.room-list-wrapper');
        const gcalWrapper = document.getElementById('gcalWrapper');
        
        card.classList.toggle('is-fullscreen');
        
        if (card.classList.contains('is-fullscreen')) {
            icon.innerHTML = '<path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"></path>';
            listWrapper.style.display = 'none';
            gcalWrapper.style.display = 'flex';
            
            // Inisialisasi Kalender saat dibuka
            renderCalendar();
        } else {
            icon.innerHTML = '<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>';
            listWrapper.style.display = 'flex';
            gcalWrapper.style.display = 'none';
        }
    }

    // ==========================================
    // LOGIKA RENDER GOOGLE CALENDAR GRID
    // ==========================================

    function renderCalendar() {
        renderHeaderAndDays();
        renderTimeGridAndEvents();
    }

    function renderHeaderAndDays() {
        const daysHeaderContainer = document.getElementById('gcalDaysHeader');
        const monthTitle = document.getElementById('gcalMonthTitle');
        const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
        
        let headerHTML = '';
        let monthName = currentWeekStart.toLocaleString('en-US', { month: 'long' });
        let year = currentWeekStart.getFullYear();
        monthTitle.innerText = `${monthName} ${year}`;

        const today = new Date();

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
        
        // Render Time Column (Y-Axis) — jam 07:00 s/d 22:00
        let timeColHTML = `<div class="gcal-time-col">`;
        for (let i = 7; i <= 22; i++) {
            const hourStr = i.toString().padStart(2, '0') + ':00';
            timeColHTML += `<div class="gcal-time-label"><span>${hourStr}</span></div>`;
        }
        timeColHTML += `</div>`;

        // Render Day Columns
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

    // Mapping status ke warna dan label singkat
    function getStatusStyle(status) {
        const s = (status || '').toLowerCase();
        if (s === 'pending') {
            return { bg: '#f59e0b', border: '#d97706', label: 'Menunggu Persetujuan' };
        } else if (s.includes('ka. ur')) {
            return { bg: '#10b981', border: '#059669', label: 'Disetujui Ka. Ur' };
        } else if (s.includes('laboran')) {
            return { bg: '#3b82f6', border: '#2563eb', label: 'Disetujui Laboran' };
        } else if (s.includes('admin')) {
            return { bg: '#8b5cf6', border: '#7c3aed', label: 'Disetujui Admin' };
        } else if (s.includes('disetujui')) {
            return { bg: '#10b981', border: '#059669', label: 'Disetujui' };
        } else if (s === 'selesai') {
            return { bg: '#64748b', border: '#475569', label: 'Selesai' };
        }
        return { bg: '#7c3aed', border: '#6d28d9', label: status };
    }

    function generateEventsForDay(targetDateStr) {
        if (typeof bookingData === 'undefined') return '';

        let eventsHTML = '';
        const pxPerHour = 48;

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

                const gridStartHour = 7; // Grid dimulai dari jam 07:00
                // Label CSS pakai bottom:-6px (Google Calendar style), sehingga label "07:00"
                // muncul di batas BAWAH row 07:00 = batas ATAS row 08:00.
                // Perlu tambah 1 row (pxPerHour) agar event 08:00 align dengan label 08:00.
                const topPx    = ((startHour - gridStartHour + 1) + startMin / 60) * pxPerHour;
                const endPx    = ((endHour   - gridStartHour + 1) + endMin   / 60) * pxPerHour;
                const heightPx = Math.max(endPx - topPx, 24);

                const st = getStatusStyle(booking.status);
                const timeLabel = `${startHour}:${startMin.toString().padStart(2,'0')} - ${endHour}:${endMin.toString().padStart(2,'0')}`;

                eventsHTML += `
                    <div class="gcal-event" style="top:${topPx}px; height:${heightPx}px; background:${st.bg}; border-left:3px solid ${st.border};"
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

    // ==========================================
    // NAVIGASI MINGGU
    // ==========================================
    function nextWeek() {
        currentWeekStart.setDate(currentWeekStart.getDate() + 7);
        renderCalendar();
    }

    function prevWeek() {
        currentWeekStart.setDate(currentWeekStart.getDate() - 7);
        renderCalendar();
    }

    function goToToday() {
        currentWeekStart = new Date();
        currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
        renderCalendar();
    }

    // ==========================================
    // LOGIKA MODAL BOOKING PUBLIK
    // ==========================================
    function openBookingModal() {
        document.getElementById('bookingModal').classList.add('show');
    }

    function closeBookingModal() {
        document.getElementById('bookingModal').classList.remove('show');
        document.getElementById('formAjukanBooking').reset();
    }

    function submitBooking(e) {
        e.preventDefault();
        
        const form = document.getElementById('formAjukanBooking');
        const formData = new FormData(form);

        // Ambil tombol submit (bisa di luar form via form= attribute)
        const submitBtn = document.querySelector('[form="formAjukanBooking"]') || form.querySelector('[type="submit"]');
        
        if (submitBtn) {
            submitBtn.textContent = 'Memproses...';
            submitBtn.disabled = true;
        }

        const url = (window.ajukanBookingUrl || window.location.origin + '/dashboard/ajukan_booking');

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                closeBookingModal();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan pada server');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.textContent = 'Simpan Peminjaman';
                submitBtn.disabled = false;
            }
        });
    }

