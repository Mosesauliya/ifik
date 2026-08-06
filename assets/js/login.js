document.addEventListener('DOMContentLoaded', () => {
  // Password Visibility Toggle
  const togglePasswordBtn = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('passwordInput');
  const eyeIcon = document.getElementById('eyeIcon');
  const eyeOffIcon = document.getElementById('eyeOffIcon');

  if (togglePasswordBtn && passwordInput) {
    togglePasswordBtn.addEventListener('click', () => {
      const isPassword = passwordInput.type === 'password';
      passwordInput.type = isPassword ? 'text' : 'password';
      
      if (eyeIcon && eyeOffIcon) {
        eyeIcon.classList.toggle('hidden', isPassword);
        eyeOffIcon.classList.toggle('hidden', !isPassword);
      }
    });
  }

  // Global Window Mouse Movement Parallax (3D Model & Spheres)
  const heroContainer = document.getElementById('hero3dContainer');
  const ifikModel = document.getElementById('ifik3dModel');
  const sphereTop = document.getElementById('sphereTop');
  const sphereMain = document.getElementById('sphereMain');

  if (heroContainer) {
    window.addEventListener('mousemove', (e) => {
      const rect = heroContainer.getBoundingClientRect();
      const centerX = rect.left + rect.width / 2;
      const centerY = rect.top + rect.height / 2;
      
      // Normalized mouse offset from center (-1 to 1)
      const rotateY = ((e.clientX - centerX) / (window.innerWidth / 2)) * 18; 
      const rotateX = -((e.clientY - centerY) / (window.innerHeight / 2)) * 18;

      // 3D Model Camera Follows Mouse Movement
      if (ifikModel) {
        const orbitAzimuth = 90 + (rotateY * 0.6); 
        const orbitElevation = 85 - (rotateX * 0.5); 
        ifikModel.cameraOrbit = `${orbitAzimuth}deg ${orbitElevation}deg 100%`;
      }

      // Parallax Movement for 3D Spheres
      if (sphereTop) {
        sphereTop.style.transform = `translate3d(${rotateY * 1.5}px, ${rotateX * 1.5}px, 40px)`;
      }

      if (sphereMain) {
        sphereMain.style.transform = `translate3d(${rotateY * 1.8}px, ${rotateX * 1.8}px, 60px)`;
      }
    });
  }

  // Form Submit Handler
  const loginForm = document.getElementById('loginForm');
  const submitBtn = document.getElementById('submitBtn');

  if (loginForm && submitBtn) {
    loginForm.addEventListener('submit', (e) => {
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = `
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>Memproses...</span>
      `;

      if (!loginForm.getAttribute('action') || loginForm.getAttribute('action') === '#') {
        e.preventDefault();
        setTimeout(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
          alert('Login sukses! (Demo Mode)');
        }, 1200);
      }
    });
  }
});
