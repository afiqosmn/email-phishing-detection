<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HybridPhish - Secure Your Email</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <!-- Favicon -->
  <link rel="icon" href="{{ asset('images/logohybridphish.ico') }}" type="image/x-icon"/>
</head>
<nav class="relative bg-blue-700 after:pointer-events-none after:absolute after:inset-x-0 after:bottom-0 after:h-px">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div class="relative flex h-16 items-center">
        
        <!-- Logo (Left) -->
        <div class="flex items-center">
            <img src="{{ asset('images/logo_white.png') }}" alt="HybridPhish" class="h-9 w-auto" />
        </div>

        <!-- Navigation (Center) -->
        <div class="hidden sm:flex absolute left-1/2 -translate-x-1/2 gap-10">
            <a href="{{ route('welcome') }}" class="text-md font-bold text-white hover:text-blue-200">Home</a>
            <a href="{{ route('about') }}" class="text-md font-bold text-white hover:text-gray-200">About</a>
            <a href="{{ route('service') }}" class="text-md font-bold text-white hover:text-gray-200">Service</a>
            <a href="{{ route('contact') }}" class="text-md font-bold text-white hover:text-gray-200">Contact</a>
        </div>

        <!-- Admin (Right) -->
        <div class="flex items-center ml-auto">
            <a href="{{ route('admin.login') }}" class="ml-2 text-md font-bold text-white hover:text-blue-200" alt="Admin" title="Admin">
                <i class="fas fa-user-shield"></i>
            </a>
        </div>
    </div>

  <el-disclosure id="mobile-menu" hidden class="block sm:hidden">
    <div class="space-y-1 px-2 pt-2 pb-3">
      <!-- Current: "bg-gray-950/50 text-white", Default: "text-gray-300 hover:bg-white/5 hover:text-white" -->
      <a href="#" aria-current="page" class="block rounded-md bg-gray-950/50 px-3 py-2 text-base font-medium text-white">Dashboard</a>
      <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white">Team</a>
      <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white">Projects</a>
      <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white">Calendar</a>
    </div>
  </el-disclosure>
  
</nav>
<body class="bg-white min-h-screen flex flex-col">
    <main class="grow">
        @yield('content')
    </main>

    <footer class="bg-linear-to-r from-blue-800 to-blue-900 text-white mt-20">

        <!-- Bottom Bar -->
        <div class="border-t border-blue-700">
            <div class="max-w-7xl mx-auto px-6 py-3 flex flex-col md:flex-row justify-between items-center text-sm text-blue-200">
                <p>© {{ date('Y') }} HybridPhish. All rights reserved.</p>
                <p>Developed as a Final Year Project • Hybrid Email Phishing Detection System</p>
            </div>
        </div>
    </footer>
</body>

</html>