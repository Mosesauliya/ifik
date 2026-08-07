/**
 * Multi-Page Navbar Navigation with Click Sliding Orange Pill Animation
 */
document.addEventListener('DOMContentLoaded', function () {
    const navContainer = document.getElementById('mainNav');
    if (!navContainer) return;

    const navLinks = navContainer.querySelectorAll('.nav-link');
    const indicator = document.getElementById('navIndicator');
    if (!indicator) return;

    let activeLink = navContainer.querySelector('.nav-link.active-link');
    if (!activeLink && navLinks.length > 0) {
        activeLink = navLinks[0];
    }

    function moveIndicatorTo(element) {
        if (!element) return;

        const navRect = navContainer.getBoundingClientRect();
        const elemRect = element.getBoundingClientRect();

        const leftOffset = elemRect.left - navRect.left;
        const width = elemRect.width;

        indicator.style.left = leftOffset + 'px';
        indicator.style.width = width + 'px';
        indicator.style.opacity = '1';

        // Update text colors: only the element covered by indicator gets white text
        navLinks.forEach(link => {
            if (link === element) {
                link.classList.add('text-white');
                link.classList.remove('text-slate-700');
            } else {
                link.classList.remove('text-white');
                link.classList.add('text-slate-700');
            }
        });
    }

    // Set initial position on active page link
    if (activeLink) {
        setTimeout(() => moveIndicatorTo(activeLink), 50);
    }

    // Handle Click Event for smooth sliding animation before page load
    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const targetUrl = this.getAttribute('href');

            // If clicking a different page than current active page
            if (targetUrl && targetUrl !== '#' && !this.classList.contains('active-link')) {
                e.preventDefault();

                // Smoothly slide orange indicator to clicked menu item
                moveIndicatorTo(this);

                // Navigate after 200ms animation delay
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 200);
            }
        });
    });

    // Resize recalculation
    window.addEventListener('resize', function () {
        if (activeLink) {
            moveIndicatorTo(activeLink);
        }
    });
});
