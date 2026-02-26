<?php
session_start();

if (isset($_SESSION['steamid'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="et" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>takenncs UCP - Eesti FiveM RP Server</title>

  <meta name="description" content="Free ucp created by takenncs.">
  <meta name="keywords" content="eesti, gta, v, rp, server, ucp, takenncs, eesti rp, fivem ucp, gta v rp, ucp, gta roleplay, createdbytakenncs rp, takenncs">
  <meta name="robots" content="index, follow">
  <meta name="theme-color" content="#0f172a">
  <link rel="icon" href="img/logo.png" type="image/png">
  <link rel="apple-touch-icon" href="img/logo.png">
  <meta property="og:title" content="takenncs UCP">
  <meta property="og:description" content="Eesti FiveM RP serveri kasutajapaneel">
  <meta property="og:type" content="website">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#f0f9ff',
              100: '#e0f2fe',
              200: '#bae6fd',
              300: '#7dd3fc',
              400: '#38bdf8',
              500: '#0ea5e9',
              600: '#0284c7',
              700: '#0369a1',
              800: '#075985',
              900: '#0c4a6e',
            },
            dark: {
              900: '#0f172a',
              800: '#1e293b',
              700: '#334155',
              600: '#475569',
              500: '#64748b',
            }
          },
          fontFamily: {
            sans: ['"Poppins"', 'sans-serif'],
          },
          animation: {
            'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
            'float': 'float 6s ease-in-out infinite',
          },
          keyframes: {
            fadeInUp: {
              '0%': { opacity: '0', transform: 'translateY(20px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            },
            float: {
              '0%, 100%': { transform: 'translateY(0px)' },
              '50%': { transform: 'translateY(-20px)' },
            }
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .delay-100 {
      animation-delay: 0.1s;
      opacity: 0;
      animation-fill-mode: forwards;
    }
    .delay-200 {
      animation-delay: 0.2s;
      opacity: 0;
      animation-fill-mode: forwards;
    }
    .delay-300 {
      animation-delay: 0.3s;
      opacity: 0;
      animation-fill-mode: forwards;
    }
    .hero-bg {
      background-image: linear-gradient(135deg, rgba(15,23,42,0.95) 0%, rgba(15,23,42,0.98) 100%);
    }
  </style>
</head>

<body class="font-sans antialiased overflow-hidden">
  <div class="flex h-screen relative" style="background-image: linear-gradient(rgba(15,23,42,0.85), rgba(15,23,42,0.95)), url('img/background.jpg'); background-size: cover; background-position: center;">
    
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-20 left-10 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl animate-float"></div>
      <div class="absolute bottom-20 right-10 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-float" style="animation-delay: -2s;"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-blue-400/5 rounded-full blur-3xl animate-float" style="animation-delay: -4s;"></div>
    </div>

    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 pointer-events-none"></div>
    
    <div class="container mx-auto px-6 relative z-10 flex items-center">
      <div class="max-w-3xl">
        <div class="mb-8 animate-fade-in-up">
          <div class="inline-flex items-center gap-3 bg-white/5 backdrop-blur-sm border border-white/10 rounded-full px-4 py-2">
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
            <span class="text-sm text-gray-300">UCP Created by takenncs</span>
          </div>
        </div>

        <h1 class="text-6xl md:text-7xl font-bold mb-6 leading-tight animate-fade-in-up">
          <span class="bg-gradient-to-r from-blue-400 via-blue-500 to-purple-500 bg-clip-text text-transparent">
            QB UCP
          </span>
          <br>
          <span class="text-white"></span>
          <span class="text-blue-400"></span>
        </h1>
        
        <p class="text-xl text-gray-300 mb-8 max-w-2xl animate-fade-in-up delay-100">
         Sinu enda lisatud tekst!!!!!!!!!!
          <span class="text-blue-400 font-semibold">devs:!</span> 
          UCP kaudu saad hallata oma karaktereid, sõidukeid ja kinnisvara.
        </p>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10 animate-fade-in-up delay-200">
          <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-4 text-center group hover:bg-white/10 transition-all">
            <div class="text-blue-400 text-2xl mb-2 group-hover:scale-110 transition-transform">
              <i class="fas fa-users"></i>
            </div>
            <div class="text-white font-semibold">150+</div>
            <div class="text-xs text-gray-400">Mängijat</div>
          </div>
          <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-4 text-center group hover:bg-white/10 transition-all">
            <div class="text-blue-400 text-2xl mb-2 group-hover:scale-110 transition-transform">
              <i class="fas fa-car"></i>
            </div>
            <div class="text-white font-semibold">50+</div>
            <div class="text-xs text-gray-400">Sõidukeid</div>
          </div>
          <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-4 text-center group hover:bg-white/10 transition-all">
            <div class="text-blue-400 text-2xl mb-2 group-hover:scale-110 transition-transform">
              <i class="fas fa-home"></i>
            </div>
            <div class="text-white font-semibold">100+</div>
            <div class="text-xs text-gray-400">Kinnisvara</div>
          </div>
          <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-4 text-center group hover:bg-white/10 transition-all">
            <div class="text-blue-400 text-2xl mb-2 group-hover:scale-110 transition-transform">
              <i class="fas fa-clock"></i>
            </div>
            <div class="text-white font-semibold">24/7</div>
            <div class="text-xs text-gray-400">Serveri aeg</div>
          </div>
        </div>
        
        <div class="flex flex-wrap gap-4 animate-fade-in-up delay-300">

        <a href="steamauth/login.php" 
             class="relative group transform transition-all hover:scale-105 inline-block">
            <span class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl opacity-75 group-hover:opacity-100 blur-lg transition-opacity"></span>
            <span class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl"></span>
            <span class="relative z-10 flex items-center justify-center px-8 py-4 bg-gray-900/90 text-white rounded-xl font-bold transition-all duration-300 group-hover:bg-transparent min-w-[220px]">
              <i class="fab fa-steam mr-3 text-xl group-hover:rotate-12 transition-transform"></i>
              <span>Logi sisse Steamiga</span>
              <svg class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </span>
          </a>

          <a href="https://discord.gg/rgxudCenxy" target="_blank" 
             class="relative group transform transition-all hover:scale-105 inline-block">
            <span class="absolute inset-0 bg-[#5865F2] rounded-xl opacity-50 group-hover:opacity-75 blur-lg transition-opacity"></span>
            <span class="absolute inset-0 bg-[#5865F2] rounded-xl opacity-90"></span>
            <span class="relative z-10 flex items-center justify-center px-8 py-4 bg-gray-900/90 text-white rounded-xl font-bold transition-all duration-300 group-hover:bg-transparent min-w-[200px]">
              <i class="fab fa-discord mr-3 text-xl group-hover:rotate-12 transition-transform"></i>
              <span>Liitu Discordiga</span>
            </span>
          </a>
        </div>

        <?php if (isset($_GET['error'])): ?>
          <div class="mt-6 p-4 bg-red-500/20 border border-red-500/50 rounded-xl animate-fade-in-up">
            <p class="text-red-400 flex items-center gap-2">
              <i class="fas fa-exclamation-circle"></i>
              <?php 
                if ($_GET['error'] == 'auth_failed') echo 'Steam autentimine ebaõnnestus. Palun proovi uuesti.';
                else if ($_GET['error'] == 'no_steamid') echo 'Steam ID puudub. Palun proovi uuesti.';
                else echo 'Midagi läks valesti. Palun proovi uuesti.';
              ?>
            </p>
          </div>
        <?php endif; ?>

        <div class="mt-12 flex items-center gap-6 text-sm text-gray-400 animate-fade-in-up delay-300">
          <div class="flex items-center gap-2">
          </div>
          <div class="flex items-center gap-2">
          </div>
          <div class="flex items-center gap-2">
            <i class="fas fa-clock text-gray-500"></i>
            <span>UCP versioon: <span class="text-white font-semibold">2.0.0</span></span>
          </div>
        </div>
      </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-dark-900 to-transparent pointer-events-none"></div>
  </div>

  <script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });

    function showLoginNotify() {
      console.log('Redirecting to Steam...');
    }

    document.querySelector('a[href*="steamcommunity.com"]')?.addEventListener('click', function(e) {
      const btn = this.querySelector('.relative.z-10');
      const originalContent = btn.innerHTML;
      
      btn.innerHTML = `
        <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Suunan Steamisse...
      `;
      
      btn.style.opacity = '0.8';
    });
  </script>
</body>
</html>