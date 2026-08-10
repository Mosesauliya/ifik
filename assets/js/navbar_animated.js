/**
 * Multi-Page Navbar Navigation — Underline Hover & Click Fade Transition
 */
document.addEventListener('DOMContentLoaded', function () {

    // ── Page Fade-In on Load ──────────────────────────────────────────────
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.22s ease-in-out';
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            document.body.style.opacity = '1';
        });
    });

    // ── Navbar Setup ──────────────────────────────────────────────────────
    const navContainer = document.getElementById('mainNav');
    if (!navContainer) return;

    const navLinks = navContainer.querySelectorAll('.nav-link');

    // Handle Click Event for page transition
    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const targetUrl = this.getAttribute('href');

            if (targetUrl && targetUrl !== '#' && !this.classList.contains('active-link')) {
                e.preventDefault();

                // Set active link underline instantly on click
                navLinks.forEach(l => l.classList.remove('active-link'));
                this.classList.add('active-link');

                // Fade out page smoothly
                document.body.style.opacity = '0';

                // Navigate after fade animation completes (200ms)
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 200);
            }
        });
    });
});
