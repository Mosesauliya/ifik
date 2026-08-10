let activeTarget = 'mulai'; // 'mulai' or 'selesai'
let selectedHour = 14;
let selectedMinute = 30;
let isSelectingHour = true; // true = hour mode, false = minute mode

function initTimePicker() {
    renderClock();
    updateDisplay();
}

function openTimePicker(target) {
    activeTarget = target;
    document.getElementById('timePickerModal').classList.add('active');
    
    // Set date label
    let d = document.querySelector('input[name="tanggal_peminjaman"]').value;
    if(d) {
        document.getElementById('tpDateDisplay').innerText = d; // Simple format
    }
}

function closeTimePicker() {
    document.getElementById('timePickerModal').classList.remove('active');
}

function applyTimePicker() {
    let hh = selectedHour.toString().padStart(2, '0');
    let mm = selectedMinute.toString().padStart(2, '0');
    
    if(activeTarget === 'mulai') {
        document.getElementById('inputJamMulai').value = hh + ':' + mm;
    } else {
        document.getElementById('inputJamSelesai').value = hh + ':' + mm;
    }
    
    closeTimePicker();
}

function setMode(mode) {
    isSelectingHour = (mode === 'hour');
    document.getElementById('tpTabHour').classList.toggle('active', isSelectingHour);
    document.getElementById('tpTabMinute').classList.toggle('active', !isSelectingHour);
    renderClock();
}

function setQuickTime(hh, mm) {
    selectedHour = hh;
    selectedMinute = mm;
    updateDisplay();
}

function updateDisplay() {
    let hh = selectedHour.toString().padStart(2, '0');
    let mm = selectedMinute.toString().padStart(2, '0');
    document.getElementById('tpDisplayHour').innerText = hh;
    document.getElementById('tpDisplayMinute').innerText = mm;
    renderClock();
}

function drawNumber(container, val, isInner) {
    let el = document.createElement('div');
    el.className = 'tp-clock-number ' + (isInner ? 'inner' : '');
    el.innerText = val.toString().padStart(isInner ? 2 : 1, '0');
    
    let radius = isInner ? 60 : 95;
    
    let angleBase = isSelectingHour ? (val % 12) * 30 : val * 6;
    let rad = (angleBase - 90) * (Math.PI / 180);
    
    let x = 120 + radius * Math.cos(rad);
    let y = 120 + radius * Math.sin(rad);
    
    el.style.left = x + 'px';
    el.style.top = y + 'px';
    
    let isActive = false;
    if(isSelectingHour && selectedHour === val) isActive = true;
    if(!isSelectingHour && selectedMinute === val) isActive = true;
    
    if(isActive) {
        el.style.color = '#fff';
        el.style.zIndex = 20;
    }
    
    container.appendChild(el);
}

function renderClock() {
    const container = document.getElementById('tpClockNumbers');
    const hand = document.getElementById('tpClockHand');
    container.innerHTML = '';
    
    let items = isSelectingHour ? 12 : 60;
    let step = isSelectingHour ? 1 : 5;
    
    // Draw numbers
    for(let i = step; i <= items; i += step) {
        let val = (i === 60) ? 0 : i;
        if(isSelectingHour) {
            drawNumber(container, val, false);
            drawNumber(container, val + 12 === 24 ? 0 : val + 12, true);
        } else {
            drawNumber(container, val, false);
        }
    }
    
    // If minute is not multiple of 5, draw it dynamically so it shows in the purple dot
    if (!isSelectingHour && selectedMinute % 5 !== 0) {
        drawNumber(container, selectedMinute, false);
    }
    
    // Position hand
    let val = isSelectingHour ? selectedHour : selectedMinute;
    let angle = isSelectingHour ? (val % 12) * 30 : val * 6;
    
    // Determine if we point to inner circle (hours 13-00)
    let isInner = isSelectingHour && (val === 0 || val > 12);
    let handHeight = isInner ? '60px' : '95px';
    
    hand.style.height = handHeight;
    hand.style.transform = `translate(-50%, 0) rotate(${angle}deg)`;
}

let isDragging = false;

function handleClockEvent(e) {
    if(e.type === 'mousemove' && !isDragging) return;
    
    // Prevent default to avoid text selection while dragging
    if (e.cancelable) e.preventDefault();
    
    // Support touch and mouse
    let clientX = e.clientX;
    let clientY = e.clientY;
    
    if(e.touches && e.touches.length > 0) {
        clientX = e.touches[0].clientX;
        clientY = e.touches[0].clientY;
    }
    
    const rect = document.getElementById('tpClockContainer').getBoundingClientRect();
    const x = clientX - rect.left - 120; // 120 is center
    const y = clientY - rect.top - 120;
    
    // Calculate angle in degrees (0 is top, clockwise)
    let angle = Math.atan2(y, x) * (180 / Math.PI) + 90;
    if (angle < 0) angle += 360;
    
    const distance = Math.sqrt(x*x + y*y);
    
    if (isSelectingHour) {
        let hour = Math.round(angle / 30);
        if (hour === 0) hour = 12;
        if (hour === 12 && angle > 345) hour = 12;
        
        // Inner circle for 24h mode (13-00)
        if (distance < 75) {
            hour += 12;
            if (hour === 24) hour = 0;
        }
        
        if (selectedHour !== hour) {
            selectedHour = hour;
            updateDisplay();
        }
    } else {
        let minute = Math.round(angle / 6);
        if (minute === 60) minute = 0;
        
        if (selectedMinute !== minute) {
            selectedMinute = minute;
            updateDisplay();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initTimePicker();
    
    const clockContainer = document.getElementById('tpClockContainer');
    
    // Mouse Events
    clockContainer.addEventListener('mousedown', function(e) {
        isDragging = true;
        handleClockEvent(e);
    });
    document.addEventListener('mousemove', handleClockEvent);
    document.addEventListener('mouseup', function(e) {
        if(isDragging && isSelectingHour) {
            setMode('minute'); // Auto switch after hour drop
        }
        isDragging = false;
    });
    
    // Touch Events
    clockContainer.addEventListener('touchstart', function(e) {
        isDragging = true;
        handleClockEvent(e);
    }, {passive: false});
    document.addEventListener('touchmove', handleClockEvent, {passive: false});
    document.addEventListener('touchend', function(e) {
        if(isDragging && isSelectingHour) {
            setMode('minute');
        }
        isDragging = false;
    });
    
    let dateInput = document.querySelector('input[name="tanggal_peminjaman"]');
    if(dateInput) {
        dateInput.addEventListener('change', function() {
            if(this.value) {
                document.getElementById('timeSelectionGroup').style.display = 'block';
            } else {
                document.getElementById('timeSelectionGroup').style.display = 'none';
            }
        });
    }
});
