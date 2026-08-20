<!-- Global Custom Cursor Partial: Glowing Orange Solid Circle (Strict Hide Default Laptop Cursor Everywhere) -->
<style>
    /* Ensure SweetAlert2 popups always render in front of all modal overlays */
    .swal2-container {
        z-index: 2147483600 !important;
    }

    /* Hide default laptop/OS cursor on ALL elements & pseudo-elements globally */
    html, body, *, *::before, *::after, 
    a, button, input, select, textarea, label, option, optgroup, [role="button"], img, table, tr, td, th, 
    ::-webkit-scrollbar, ::-webkit-scrollbar-thumb, ::-webkit-resizer, .swal2-container, .swal2-popup, .swal2-styled {
        cursor: none !important;
    }

    /* Hide default browser up/down number input spinners */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none !important;
        appearance: none !important;
        margin: 0 !important;
        display: none !important;
    }
    input[type=number] {
        -moz-appearance: textfield !important;
    }

    .custom-cursor-circle {
        position: fixed;
        top: 0;
        left: 0;
        width: 18px;
        height: 18px;
        border: 2.5px solid #ea580c;
        background: rgba(234, 88, 12, 0.45);
        backdrop-filter: blur(1px);
        -webkit-backdrop-filter: blur(1px);
        border-radius: 50%;
        pointer-events: none;
        z-index: 2147483647 !important;
        transform: translate(-50%, -50%);
        transition: width 0.18s cubic-bezier(0.25, 1, 0.5, 1),
                    height 0.18s cubic-bezier(0.25, 1, 0.5, 1),
                    background-color 0.18s ease,
                    border-color 0.18s ease,
                    opacity 0.25s ease;
        box-shadow: 0 0 12px rgba(234, 88, 12, 0.8);
        opacity: 0;
    }

    body.cursor-hover .custom-cursor-circle {
        width: 32px;
        height: 32px;
        background: rgba(234, 88, 12, 0.55);
        border-color: #ea580c;
        border-width: 3px;
        box-shadow: 0 0 18px rgba(234, 88, 12, 0.95);
    }

    body.cursor-active .custom-cursor-circle {
        width: 12px;
        height: 12px;
        background: #ea580c;
        border-color: #ea580c;
        box-shadow: 0 0 10px #ea580c;
    }
</style>

<div class="custom-cursor-dot" id="customCursorDot"></div>
<div class="custom-cursor-circle" id="customCursorCircle"></div>

<script>
    (function() {
        document.addEventListener('DOMContentLoaded', () => {
            const circle = document.getElementById('customCursorCircle');
            if (!circle) return;

            let mouseX = -100, mouseY = -100;
            let circleX = -100, circleY = -100;
            let isVisible = false;

            window.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;

                if (!isVisible) {
                    circle.style.opacity = '1';
                    isVisible = true;
                }
            });

            window.addEventListener('mouseleave', () => {
                circle.style.opacity = '0';
                isVisible = false;
            });

            window.addEventListener('mouseenter', () => {
                circle.style.opacity = '1';
                isVisible = true;
            });

            function renderCursor() {
                circleX += (mouseX - circleX) * 0.24;
                circleY += (mouseY - circleY) * 0.24;

                circle.style.transform = `translate(${circleX}px, ${circleY}px) translate(-50%, -50%)`;
                requestAnimationFrame(renderCursor);
            }
            requestAnimationFrame(renderCursor);

            // Force hide default cursor on pointerdown/focus events everywhere
            ['focusin', 'click', 'pointerdown', 'mousemove'].forEach(evt => {
                document.addEventListener(evt, (e) => {
                    document.documentElement.style.cursor = 'none';
                    document.body.style.cursor = 'none';
                    if (e.target && e.target.style) {
                        e.target.style.cursor = 'none';
                    }
                }, true);
            });

            // Hover state logic for all interactive elements
            const hoverTargets = 'a, button, input, select, textarea, [role="button"], .card, tr, .btn-action, .lab-add-room-btn, .file-pill, label, option';
            document.addEventListener('mouseover', (e) => {
                if (e.target.closest(hoverTargets)) {
                    document.body.classList.add('cursor-hover');
                }
            });

            document.addEventListener('mouseout', (e) => {
                if (e.target.closest(hoverTargets)) {
                    document.body.classList.remove('cursor-hover');
                }
            });

            document.addEventListener('mousedown', () => {
                document.body.classList.add('cursor-active');
            });

            document.addEventListener('mouseup', () => {
                document.body.classList.remove('cursor-active');
            });
        });
    })();
</script>
