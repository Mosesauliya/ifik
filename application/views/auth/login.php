<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - IK LABS PORTAL</title>
  
  <!-- Tailwind CSS v3 (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#fff7ed',
              100: '#ffedd5',
              200: '#fed7aa',
              300: '#fdba74',
              400: '#fb923c',
              500: '#f46b1d',
              600: '#ea580c',
              700: '#c2410c',
              800: '#9a3412',
              900: '#7c2d12',
            }
          },
          fontFamily: {
            sans: ['Plus Jakarta Sans', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <!-- Google Model Viewer (3D GLB Renderer) -->
  <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.4.0/model-viewer.min.js"></script>

  <!-- Custom 3D Animations & Styles -->
  <link rel="stylesheet" href="<?= base_url('assets/css/login.css'); ?>">
</head>
<body class="bg-[#fbf7f1] min-h-screen flex items-center justify-center p-4 lg:p-8 antialiased selection:bg-brand-500 selection:text-white">

  <!-- Main Outer Login Card Container -->
  <main class="w-full max-w-6xl bg-white rounded-3xl lg:rounded-[2.5rem] shadow-2xl shadow-orange-950/10 overflow-hidden border border-orange-100/60 transition-all duration-300">
    <div class="grid grid-cols-1 lg:grid-cols-12 min-h-[640px]">
      
      <!-- LEFT HERO SECTION (3D Visual Area) -->
      <section id="hero3dContainer" class="lg:col-span-6 xl:col-span-6 bg-gradient-to-br from-[#f86b1d] via-[#ea580c] to-[#d97706] p-8 lg:p-12 text-white relative flex flex-col justify-between overflow-hidden hero-polygon scene-3d select-none">
        
        <!-- Background Concentric Circles Overlay -->
        <div class="absolute inset-0 concentric-rings pointer-events-none opacity-40"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-orange-400/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- 3D Scene Elements Container (Centered Vertically) -->
        <div class="relative w-full h-80 lg:h-96 my-auto flex items-center justify-center preserve-3d">
          
          <!-- Top Left Floating 3D Sphere -->
          <div id="sphereTop" class="absolute top-2 left-6 lg:left-10 w-10 h-10 lg:w-12 lg:h-12 rounded-full sphere-3d-top animate-float-slow transition-transform duration-200 ease-out z-30 pointer-events-none"></div>

          <!-- Main Floating 3D Sphere (Right) -->
          <div id="sphereMain" class="absolute top-2 right-4 lg:right-8 w-24 h-24 lg:w-28 lg:h-28 rounded-full sphere-3d-main animate-float-medium transition-transform duration-200 ease-out z-30 pointer-events-none"></div>

          <!-- 3D GLB Model Viewer (ifik.glb) - Enlarged & Centered -->
          <model-viewer 
            id="ifik3dModel"
            src="<?= base_url('assets/3D/ifik.glb'); ?>" 
            alt="3D Logo IFIK" 
            disable-zoom 
            shadow-intensity="1.5" 
            shadow-softness="0.8"
            exposure="1.15"
            camera-orbit="90deg 85deg 100%"
            field-of-view="24deg"
            interaction-prompt="none"
            style="background-color: transparent; width: 100%; height: 100%;"
            class="z-20 cursor-default scale-135 lg:scale-145"
          >
          </model-viewer>

        </div>

        <!-- Hero Typography & Description (Raised Upwards) -->
        <div class="relative z-20 -mt-8 lg:-mt-10 space-y-2">
          <p class="text-xs font-bold uppercase tracking-widest text-orange-100/90">IFIK LABS PORTAL</p>
          <h1 class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight">
            Your next idea starts here.
          </h1>
          <p class="text-sm text-orange-100/80 max-w-md leading-relaxed font-medium">
            Satu ruang untuk terhubung, berkarya, dan mengembangkan ide terbaikmu.
          </p>
        </div>

      </section>

      <!-- RIGHT FORM SECTION (Login Form) -->
      <section class="lg:col-span-6 xl:col-span-6 p-8 lg:p-14 flex flex-col justify-between bg-white">
        
        <div class="max-w-md mx-auto w-full my-auto">
          
          <!-- Header Badge & Title -->
          <div class="mb-8">
            <span class="text-[12px] font-extrabold tracking-widest text-brand-500 uppercase block mb-1">
              WELCOME BACK
            </span>
            <h2 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight leading-tight">
              Masuk ke workspace.
            </h2>
            <p class="text-sm font-medium text-slate-500 mt-2">
              Masukkan Email dan password untuk melanjutkan.
            </p>
          </div>

          <!-- Error Alert (If Any) -->
          <?php if (isset($error) && !empty($error)): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium flex items-center gap-2 animate-shake">
              <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span><?= htmlspecialchars($error); ?></span>
            </div>
          <?php endif; ?>

          <!-- Login Form -->
          <form id="loginForm" action="<?= site_url('login/authenticate'); ?>" method="POST" class="space-y-5">
            
            <!-- Email / ID Input -->
            <div>
              <label for="identityInput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                Email
              </label>
              <div class="relative rounded-xl border border-slate-200 bg-slate-50/50 transition-all duration-200 input-focus-ring">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <!-- User Icon -->
                  <svg class="w-5 h-5 text-brand-500/80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <input 
                  type="text" 
                  id="identityInput" 
                  name="identity" 
                  required
                  placeholder="example@telkomuniversity.ac.id" 
                  class="w-full pl-11 pr-4 py-3.5 bg-transparent text-sm text-slate-900 font-medium placeholder-slate-400 focus:outline-none rounded-xl"
                >
              </div>
            </div>

            <!-- Password Input -->
            <div>
              <label for="passwordInput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                Password
              </label>
              <div class="relative rounded-xl border border-slate-200 bg-slate-50/50 transition-all duration-200 input-focus-ring">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <!-- Lock Icon -->
                  <svg class="w-5 h-5 text-brand-500/80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input 
                  type="password" 
                  id="passwordInput" 
                  name="password" 
                  required
                  placeholder="Masukkan password" 
                  class="w-full pl-11 pr-12 py-3.5 bg-transparent text-sm text-slate-900 font-medium placeholder-slate-400 focus:outline-none rounded-xl"
                >
                <!-- Toggle Password Visibility -->
                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-brand-500 focus:outline-none">
                  <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.03 10.03 0 014.122-.963c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m-6.84-6.84a3 3 0 004.243 4.243M9.878 9.878l4.242 4.242M3 3l18 18"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
              <button 
                type="submit" 
                id="submitBtn"
                class="w-full py-4 px-6 rounded-xl text-white font-bold text-base flex items-center justify-center gap-2 btn-3d"
              >
                <span>Masuk</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
              </button>
            </div>

            <!-- Forgot Password Link -->
            <div class="text-center pt-2">
              <a href="#" class="text-sm font-semibold text-brand-500 hover:text-brand-600 hover:underline transition-colors">
                Lupa password?
              </a>
            </div>

          </form>
        </div>

        <!-- Footer Text -->
        <footer class="mt-8 text-center">
          <p class="text-xs font-semibold text-slate-400">
            &copy; 2025 Fakultas Industri Kreatif
          </p>
        </footer>

      </section>

    </div>
  </main>

  <!-- External JS for 3D Parallax & Interactions -->
  <script src="<?= base_url('assets/js/login.js'); ?>"></script>
</body>
</html>
