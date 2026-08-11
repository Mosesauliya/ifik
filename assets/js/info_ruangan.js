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
        
        // Render Time Column (Y-Axis)
        let timeColHTML = `<div class="gcal-time-col">`;
        for (let i = 1; i < 24; i++) {
            let ampm = i < 12 ? 'AM' : (i === 12 ? 'PM' : 'PM');
            let hourStr = i <= 12 ? i : i - 12;
            timeColHTML += `<div class="gcal-time-label"><span>${hourStr} ${ampm}</span></div>`;
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
            
            // Generate Events for this specific day
            dayColsHTML += generateEventsForDay(dateString);
            
            dayColsHTML += `</div>`;
        }
        dayColsHTML += `</div>`;

        gridContainer.innerHTML = timeColHTML + dayColsHTML;
    }

    function generateEventsForDay(targetDateStr) {
        if (typeof bookingData === 'undefined') return '';

        let eventsHTML = '';
        const pxPerHour = 48;

        bookingData.forEach(booking => {
            // Cek apakah event ini terjadi di tanggal target (bisa multi-hari)
            if (booking.tanggal_mulai <= targetDateStr && booking.tanggal_selesai >= targetDateStr) {
                
                let startHour = 0;
                let startMin = 0;
                let endHour = 24;
                let endMin = 0;

                // Jika di hari pertama peminjaman, gunakan jam mulai asli
                if (booking.tanggal_mulai === targetDateStr) {
                    const startParts = booking.jam_mulai.split(':');
                    startHour = parseInt(startParts[0]);
                    startMin = parseInt(startParts[1]);
                }
                
                // Jika di hari terakhir peminjaman, gunakan jam selesai asli
                if (booking.tanggal_selesai === targetDateStr) {
                    const endParts = booking.jam_selesai.split(':');
                    endHour = parseInt(endParts[0]);
                    endMin = parseInt(endParts[1]);
                }

                // Kalkulasi posisi TOP & HEIGHT
                const topPx = (startHour + (startMin / 60)) * pxPerHour;
                const endPx = (endHour + (endMin / 60)) * pxPerHour;
                const heightPx = endPx - topPx;

                eventsHTML += `
                    <div class="gcal-event" style="top: ${topPx}px; height: ${heightPx}px;" title="${booking.nama_ruangan} - ${booking.nama_lengkap}">
                        <div class="gcal-event-title">${booking.nama_ruangan}</div>
                        <div class="gcal-event-time">${startHour}:${startMin.toString().padStart(2, '0')} - ${endHour}:${endMin.toString().padStart(2, '0')}</div>
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
        const submitBtn = form.querySelector('button[type="submit"]');
        
        submitBtn.innerHTML = 'Memproses...';
        submitBtn.disabled = true;

        fetch(window.location.origin + '/dashboard/ajukan_booking', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                closeBookingModal();
                // Tidak perlu render ulang jadwal karena statusnya masih pending dan tidak masuk kalender dulu
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan pada server');
        })
        .finally(() => {
            submitBtn.innerHTML = 'Submit Booking';
            submitBtn.disabled = false;
        });
    }
