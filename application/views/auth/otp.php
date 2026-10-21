<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifikasi OTP (2FA) — IK Labs Portal</title>
  <meta name="description" content="Verifikasi Two-Factor Authentication untuk akun IK Labs Portal Telkom University.">

  <!-- Tailwind CSS v3 -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
        }
      }
    }
  </script>

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Custom CSS dari Login -->
  <link rel="stylesheet" href="<?= base_url('assets/css/login.css'); ?>?v=<?= time(); ?>">

  <style>
    .otp-input-box {
      width: 46px;
      height: 54px;
      text-align: center;
      font-size: 24px;
      font-weight: 800;
      color: #78350f;
      background: #ffffff;
      border: 2px solid #fed7aa;
      border-radius: 12px;
      outline: none;
      transition: all 0.2s ease;
      box-shadow: 0 2px 6px rgba(180, 83, 9, 0.05);
    }
    .otp-input-box:focus {
      border-color: #f59e0b;
      box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2);
      transform: translateY(-2px);
      background: #fffbeb;
    }
    .otp-input-box.is-filled {
      border-color: #b45309;
      background: #fffdfa;
    }
    @media (max-width: 420px) {
      .otp-input-box {
        width: 38px;
        height: 48px;
        font-size: 20px;
      }
    }
  </style>
</head>

<body>

  <!-- ========== BACKGROUND ========== -->
  <div class="bg-layer" id="bgLayer">
    <img src="<?= base_url('assets/images/background.png'); ?>" alt="" class="bg-img" id="bgImg">
    <div class="bg-overlay"></div>
  </div>

  <!-- ========== MAIN LAYOUT ========== -->
  <div class="page-wrap">

    <!-- LEFT HERO TEXT -->
    <div class="hero-text" id="heroText">
      <img src="<?= base_url('assets/images/logo-dummy.webp'); ?>" alt="Logo IFIK" class="hero-logo-img">
      <h1 class="hero-title">Keamanan Akun<br>Terlindungi.</h1>
      <p class="hero-sub">Kami menerapkan verifikasi keamanan 2 langkah (<span class="hero-highlight">Two-Factor Authentication</span>) untuk memastikan akun Anda terlindungi dari akses tidak sah.</p>
      
      <!-- Pesan Informasi Keamanan -->
      <div class="hero-laa-notice">
        <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        <span>Jangan pernah membagikan kode OTP ini kepada siapa pun.</span>
      </div>
    </div>

    <!-- RIGHT — HANGING CARD -->
    <div class="hang-root" id="hangRoot">

      <!-- Login Card Container -->
      <div class="login-card-container">
        
        <!-- Card Cap -->
        <div class="card-cap">
          <div class="cap-progress-bar" id="capProgressBar"></div>
          <div class="cap-reflection"></div>
        </div>

        <!-- Card Body Content -->
        <div class="card-body">
          
          <!-- Tombol Batalkan / Kembali ke Login -->
          <div class="flex items-center justify-start mb-3">
            <a href="<?= base_url('login/cancel_otp'); ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-800/70 hover:text-amber-900 transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
              <span>Ganti Akun</span>
            </a>
          </div>

          <!-- Icon & Header -->
          <div class="text-center mb-4">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-gradient-to-tr from-amber-600 to-amber-400 text-white flex items-center justify-center shadow-md shadow-amber-500/20 mb-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h2 class="text-xl font-extrabold text-stone-900 tracking-tight">Verifikasi OTP</h2>
            <p class="text-xs text-stone-600 mt-1">Masukkan 6 digit kode yang dikirim ke email:</p>
            
            <div class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200/80 px-3 py-1 rounded-full text-xs font-bold text-amber-900 mt-2">
              <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              <span><?= htmlspecialchars($masked_email); ?></span>
            </div>

            <!-- Live Status Pengiriman Email OTP -->
            <div id="otpLiveStatus" class="mt-2.5 text-xs flex items-center justify-center gap-1.5 text-amber-800/80">
              <svg class="animate-spin w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <span>Mengirimkan kode OTP ke email Anda...</span>
            </div>

            <?php if (!empty($dev_otp)): ?>
            <!-- Fallback Alert jika Port SMTP Server NAS Diblokir ISP -->
            <div class="mt-3 p-2.5 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs text-center shadow-sm">
              <span class="font-bold text-[11px] text-amber-700">⚠️ KONEKSI SMTP SERVER DI-LIMIT / OFFLINE</span>
              <p class="text-[11px] text-amber-800 mt-0.5">Kode OTP Anda untuk login:</p>
              <div class="mt-1 font-mono text-base font-extrabold tracking-widest text-amber-900 bg-amber-100/80 py-1 px-3 rounded-lg inline-block border border-amber-300">
                <?= htmlspecialchars($dev_otp); ?>
              </div>
            </div>
            <?php endif; ?>
          </div>

          <!-- Flash Alerts -->
          <?php if ($this->session->flashdata('error')): ?>
          <div class="flash-error mb-3">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?= $this->session->flashdata('error'); ?></span>
          </div>
          <?php endif; ?>

          <?php if ($this->session->flashdata('success')): ?>
          <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-3.5 py-2.5 rounded-xl text-xs flex items-center gap-2 mb-3">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span><?= $this->session->flashdata('success'); ?></span>
          </div>
          <?php endif; ?>

          <?php if ($this->session->flashdata('warning')): ?>
          <div class="bg-amber-50 border border-amber-200 text-amber-800 px-3.5 py-2.5 rounded-xl text-xs flex items-center gap-2 mb-3">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?= $this->session->flashdata('warning'); ?></span>
          </div>
          <?php endif; ?>

          <!-- OTP Form -->
          <form action="<?= base_url('login/verify_otp'); ?>" method="POST" id="otpForm">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="otp_code" id="finalOtp">

            <!-- 6 Input Digits -->
            <div class="flex justify-center items-center gap-2 my-4" id="otpInputs">
              <input type="text" inputmode="numeric" maxlength="1" class="otp-input-box" data-index="0" autofocus autocomplete="one-time-code">
              <input type="text" inputmode="numeric" maxlength="1" class="otp-input-box" data-index="1" autocomplete="one-time-code">
              <input type="text" inputmode="numeric" maxlength="1" class="otp-input-box" data-index="2" autocomplete="one-time-code">
              <input type="text" inputmode="numeric" maxlength="1" class="otp-input-box" data-index="3" autocomplete="one-time-code">
              <input type="text" inputmode="numeric" maxlength="1" class="otp-input-box" data-index="4" autocomplete="one-time-code">
              <input type="text" inputmode="numeric" maxlength="1" class="otp-input-box" data-index="5" autocomplete="one-time-code">
            </div>

            <!-- Countdown Timer -->
            <div class="text-center text-xs text-stone-500 mb-4">
              Kode berlaku: <span id="countdownDisplay" class="font-bold text-amber-700">05:00</span>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="verifyBtn" class="btn-submit w-full" disabled>
              Verifikasi & Masuk &rarr;
            </button>
          </form>

          <!-- Resend Section -->
          <div class="mt-4 pt-3 border-t border-stone-200/60 text-center">
            <p class="text-xs text-stone-500">Tidak menerima email verifikasi?</p>
            <a href="<?= base_url('login/resend_otp'); ?>" id="resendBtn" class="text-xs font-bold text-amber-800 hover:text-amber-900 hover:underline inline-block mt-1 opacity-50 pointer-events-none transition-opacity">
              Kirim Ulang Kode (<span id="resendSec">30</span>s)
            </a>
          </div>

        </div><!-- /card-body -->
      </div><!-- /login-card-container -->
    </div><!-- /hang-root -->

  </div><!-- /page-wrap -->

  <p class="footer-copy">&copy; 2025 Fakultas Industri Kreatif &mdash; Telkom University</p>

  <!-- Interactive Script for OTP Handling -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const inputs = document.querySelectorAll('.otp-input-box');
      const finalOtp = document.getElementById('finalOtp');
      const verifyBtn = document.getElementById('verifyBtn');
      const otpForm = document.getElementById('otpForm');
      const capProgressBar = document.getElementById('capProgressBar');
      const hangRoot = document.getElementById('hangRoot');

      // Update full OTP and button state
      function checkOtpState() {
        let code = '';
        inputs.forEach(input => {
          code += input.value.trim();
        });
        finalOtp.value = code;

        if (code.length === 6 && /^\d{6}$/.test(code)) {
          verifyBtn.disabled = false;
          verifyBtn.style.opacity = '1';
          return true;
        } else {
          verifyBtn.disabled = true;
          return false;
        }
      }

      // Handle Key & Paste Events
      inputs.forEach((input, idx) => {
        // Input event (typing digit)
        input.addEventListener('input', (e) => {
          const val = input.value.replace(/[^0-9]/g, '');
          input.value = val ? val[0] : '';
          
          if (input.value) {
            input.classList.add('is-filled');
            if (idx < inputs.length - 1) {
              inputs[idx + 1].focus();
            }
          } else {
            input.classList.remove('is-filled');
          }

          if (checkOtpState()) {
            // Auto submit when all 6 digits entered
            setTimeout(() => {
              otpForm.dispatchEvent(new Event('submit'));
            }, 200);
          }
        });

        // Keydown event (backspace navigation)
        input.addEventListener('keydown', (e) => {
          if (e.key === 'Backspace') {
            if (!input.value && idx > 0) {
              inputs[idx - 1].focus();
              inputs[idx - 1].value = '';
              inputs[idx - 1].classList.remove('is-filled');
            } else {
              input.value = '';
              input.classList.remove('is-filled');
            }
            checkOtpState();
          } else if (e.key === 'ArrowLeft' && idx > 0) {
            inputs[idx - 1].focus();
          } else if (e.key === 'ArrowRight' && idx < inputs.length - 1) {
            inputs[idx + 1].focus();
          }
        });

        // Paste event (support full 6-digit paste)
        input.addEventListener('paste', (e) => {
          e.preventDefault();
          const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim().replace(/[^0-9]/g, '');
          if (!pasteData) return;

          for (let i = 0; i < inputs.length; i++) {
            if (i < pasteData.length) {
              inputs[i].value = pasteData[i];
              inputs[i].classList.add('is-filled');
            } else {
              inputs[i].value = '';
              inputs[i].classList.remove('is-filled');
            }
          }

          const targetFocus = Math.min(pasteData.length, inputs.length - 1);
          inputs[targetFocus].focus();

          if (checkOtpState()) {
            setTimeout(() => {
              otpForm.dispatchEvent(new Event('submit'));
            }, 250);
          }
        });
      });

      // Submit Form Animation
      let isSubmitting = false;
      otpForm.addEventListener('submit', (e) => {
        if (!checkOtpState() || isSubmitting) {
          e.preventDefault();
          return;
        }
        isSubmitting = true;
        verifyBtn.disabled = true;

        if (hangRoot) {
          hangRoot.classList.remove('is-error');
          hangRoot.classList.add('is-loading');
        }

        verifyBtn.innerHTML = `
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Memverifikasi Kode OTP...</span>
        `;
        if (capProgressBar) capProgressBar.style.width = '100%';
        otpForm.submit();
      });

      // Background AJAX Send Email
      fetch('<?= base_url('login/send_otp_ajax'); ?>')
        .then(res => res.json())
        .then(data => {
          const liveStatus = document.getElementById('otpLiveStatus');
          if (!liveStatus) return;
          if (data.status === 'success') {
            liveStatus.className = 'mt-2.5 text-xs flex items-center justify-center gap-1.5 text-emerald-700 font-semibold';
            liveStatus.innerHTML = `
              <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
              <span>Kode OTP telah terkirim ke email Anda</span>
            `;
          } else if (data.status === 'fallback') {
            liveStatus.className = 'mt-2 text-xs text-center';
            liveStatus.innerHTML = `
              <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs text-center shadow-sm">
                <span class="font-bold text-[11px] text-amber-700">⚠️ KONEKSI SMTP SERVER DI-LIMIT / OFFLINE</span>
                <p class="text-[11px] text-amber-800 mt-0.5">Kode OTP Anda untuk login:</p>
                <div class="mt-1 font-mono text-base font-extrabold tracking-widest text-amber-900 bg-amber-100/80 py-1 px-3 rounded-lg inline-block border border-amber-300">
                  ${data.dev_otp}
                </div>
              </div>
            `;
          }
        })
        .catch(err => {
          console.log('Background OTP check done');
        });

      // 1. Countdown Expire Timer (5 Menit)
      let timeLeft = <?= (int)($expires_in ?? 300); ?>;
      const countdownDisplay = document.getElementById('countdownDisplay');

      function updateCountdown() {
        if (timeLeft <= 0) {
          countdownDisplay.textContent = 'Kedaluwarsa';
          countdownDisplay.classList.add('text-red-600');
          verifyBtn.disabled = true;
          return;
        }
        const m = Math.floor(timeLeft / 60);
        const s = timeLeft % 60;
        countdownDisplay.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        timeLeft--;
      }
      updateCountdown();
      const timerInterval = setInterval(updateCountdown, 1000);

      // 2. Resend Cooldown Timer (30 Detik)
      let resendCooldown = 30;
      const resendBtn = document.getElementById('resendBtn');
      const resendSec = document.getElementById('resendSec');

      const resendInterval = setInterval(() => {
        resendCooldown--;
        if (resendCooldown <= 0) {
          clearInterval(resendInterval);
          resendBtn.classList.remove('opacity-50', 'pointer-events-none');
          resendBtn.textContent = 'Kirim Ulang Kode';
        } else {
          resendSec.textContent = resendCooldown;
        }
      }, 1000);

      // Parallax effect on mouse move
      const bgImg = document.getElementById('bgImg');
      window.addEventListener('mousemove', (e) => {
        const cx = window.innerWidth / 2;
        const cy = window.innerHeight / 2;
        const dx = (e.clientX - cx) / cx;
        const dy = (e.clientY - cy) / cy;
        if (bgImg) bgImg.style.transform = `translate(${dx * -10}px, ${dy * -7}px) scale(1.05)`;
      });
    });
  </script>
</body>
</html>
