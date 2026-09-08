<!-- Global Custom Cursor Partial: Glowing Orange Solid Circle (Ultra-Fast 60+ FPS GPU Accelerated) -->
<style>
    /* Ensure SweetAlert2 popups always render in front of all modal overlays */
    .swal2-container {
        z-index: 2147483600 !important;
    }

    /* Hide default laptop/OS cursor on ALL elements globally ONLY on Desktop */
    @media (min-width: 901px) and (pointer: fine) {
        *, *::before, *::after, html, body, a, button, input, select, textarea, label, [role="button"], tr, td, th {
            cursor: none !important;
        }
    }

    /* Di Responsif Layar HP / Touchscreen: Kembalikan kursor standar & sembunyikan bulatan custom kursor */
    @media (max-width: 900px), (pointer: coarse) {
        *, *::before, *::after, html, body, a, button, input, select, textarea, label, [role="button"], tr, td, th {
            cursor: auto !important;
        }
        a, button, [role="button"], [onclick], select, summary {
            cursor: pointer !important;
        }
        input[type="text"], input[type="password"], input[type="email"], textarea {
            cursor: text !important;
        }
        #customCursorCircle {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }
    }

    /* Pastikan iframe file (seperti PDF viewer) mengizinkan kursor sistem bawaan */
    iframe {
        cursor: auto !important;
    }

    #customCursorCircle {
        position: fixed !important;
        top: 0;
        left: 0;
        width: 22px;
        height: 22px;
        margin-top: -11px;
        margin-left: -11px;
        border: 2px solid #ea580c !important;
        background: rgba(234, 88, 12, 0.3) !important;
        border-radius: 50% !important;
        pointer-events: none !important;
        z-index: 2147483647 !important;
        box-shadow: 0 0 14px rgba(234, 88, 12, 0.75), inset 0 0 4px rgba(234, 88, 12, 0.2) !important;
        opacity: 0;
        transition: width 0.16s cubic-bezier(0.2, 0.9, 0.2, 1),
                    height 0.16s cubic-bezier(0.2, 0.9, 0.2, 1),
                    margin 0.16s cubic-bezier(0.2, 0.9, 0.2, 1),
                    background-color 0.16s ease,
                    border-color 0.16s ease,
                    box-shadow 0.16s ease,
                    opacity 0.12s ease !important;
        will-change: transform;
        transform: translate3d(-100px, -100px, 0);
    }

    /* Status kursor disembunyikan (misal saat masuk ke area file PDF iframe atau kursor keluar window) */
    #customCursorCircle.cursor-hidden {
        opacity: 0 !important;
        visibility: hidden !important;
    }

    /* Membesar saat mendekat / hover ke button, link, atau elemen interaktif */
    #customCursorCircle.hovered {
        width: 44px !important;
        height: 44px !important;
        margin-top: -22px !important;
        margin-left: -22px !important;
        background: rgba(234, 88, 12, 0.38) !important;
        border-color: #ea580c !important;
        border-width: 2.5px !important;
        box-shadow: 0 0 22px rgba(234, 88, 12, 0.9), inset 0 0 8px rgba(234, 88, 12, 0.3) !important;
    }

    /* Efek klik ditekan */
    #customCursorCircle.active {
        width: 14px !important;
        height: 14px !important;
        margin-top: -7px !important;
        margin-left: -7px !important;
        background: #ea580c !important;
        border-color: #ea580c !important;
        border-width: 2.5px !important;
        box-shadow: 0 0 10px rgba(234, 88, 12, 0.8) !important;
    }
</style>

<div id="customCursorCircle"></div>

<script>
(function() {
    function setupCursor() {
        if (window.innerWidth <= 900 || window.matchMedia('(pointer: coarse)').matches) {
            const el = document.getElementById('customCursorCircle');
            if (el) el.style.display = 'none';
            return;
        }

        let circle = document.getElementById('customCursorCircle');
        const rootContainer = document.documentElement || document.body;

        if (!circle) {
            circle = document.createElement('div');
            circle.id = 'customCursorCircle';
            rootContainer.appendChild(circle);
        } else if (circle.parentElement !== rootContainer) {
            rootContainer.appendChild(circle);
        }

        let mouseX = -100, mouseY = -100;
        let isVisible = false;

        function hideCircleCursor() {
            if (circle) circle.classList.add('cursor-hidden');
            isVisible = false;
        }

        function showCircleCursor() {
            if (circle) {
                circle.classList.remove('cursor-hidden');
                circle.style.opacity = '1';
            }
            isVisible = true;
        }

        window.hideCircleCursor = hideCircleCursor;
        window.showCircleCursor = showCircleCursor;

        // Update posisi 1:1 instan tanpa delay menggunakan GPU Compositor translate3d
        function updateCursorPos(e) {
            mouseX = e.clientX;
            mouseY = e.clientY;
            if (!isVisible) showCircleCursor();
            circle.style.transform = 'translate3d(' + mouseX + 'px, ' + mouseY + 'px, 0)';
        }

        window.addEventListener('pointermove', updateCursorPos, { passive: true });

        // Sembunyikan kursor kustom saat mouse keluar jendela atau masuk ke iframe PDF
        document.addEventListener('mouseleave', hideCircleCursor);
        document.addEventListener('mouseenter', showCircleCursor);
        window.addEventListener('blur', hideCircleCursor);
        window.addEventListener('focus', showCircleCursor);

        // Delegasi hover elemen interaktif tanpa getComputedStyle dan tanpa merusak style tree halaman
        const interactiveSelector = 'a, button, input, select, textarea, label, [role="button"], [onclick], .cursor-pointer, .box-3d, .btn-action, .nav-link, .card-3d-orange, .btn-3d-orange, img, svg';

        document.addEventListener('mouseover', function(e) {
            const target = e.target;
            if (!target) return;

            // Sembunyikan kursor saat di dalam iframe (misal viewer PDF)
            if (target.tagName === 'IFRAME' || (target.closest && (target.closest('iframe') || target.closest('#quickDocFileViewerWrapper') || target.closest('.pdf-viewer-container')))) {
                hideCircleCursor();
                return;
            }

            // Cek elemen interaktif (hovered) hanya menempel pada elemen kursor itu sendiri
            if (target.closest && target.closest(interactiveSelector)) {
                circle.classList.add('hovered');
            } else {
                circle.classList.remove('hovered');
            }
        }, { passive: true });

        // Efek klik ditekan
        window.addEventListener('pointerdown', function() {
            circle.classList.add('active');
        }, { passive: true });

        window.addEventListener('pointerup', function() {
            circle.classList.remove('active');
        }, { passive: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupCursor);
    } else {
        setupCursor();
    }
})();
</script>
