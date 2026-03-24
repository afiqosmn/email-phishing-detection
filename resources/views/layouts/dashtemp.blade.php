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

  <style>
    /* =========================
       SIDEBAR COLLAPSE STATE
    ==========================*/
    .sidebar-collapsed .sidebar-content {
      width: 55px;
    }
    .sidebar-collapsed .main-content {
      margin-left: 4rem;
    }
    .sidebar-collapsed .nav-item-text,
    .sidebar-collapsed .user-info,
    .sidebar-collapsed .brand-text {
      display: none;
    }

    /* =========================
       SIDEBAR HOVER EXPAND
    ==========================*/
    .sidebar-collapsed .sidebar-content:hover {
      width: 12rem; /* sama macam w-64 */
    }
    .sidebar-collapsed .sidebar-content:hover ~ .main-content {
      margin-left: 4rem;
    }
    .sidebar-collapsed .sidebar-content:hover .nav-item-text {
      display: inline;
    }
    .sidebar-collapsed .sidebar-content:hover .user-info {
      display: block;
    }
    .sidebar-collapsed .sidebar-content:hover .brand-text {
      display: inline-block;
    }

    /* =========================
       NAV ITEM HOVER EFFECT
    ==========================*/
    .nav-item {
      transition: all 0.3s ease;
    }
    .nav-item:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }

    /* =========================
       DROPDOWN MENU
    ==========================*/
    .dropdown-content {
      opacity: 0;
      visibility: hidden;
      transform: translateY(10px);
      transition: all 0.3s ease;
    }
    .dropdown-trigger:hover .dropdown-content {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    .dropdown-item {
      transition: all 0.2s ease;
    }
    .dropdown-item:hover {
      background-color: #3B82F6;
      color: white;
    }
  </style>
</head>

<body class="bg-gray-100 sidebar-collapsed">
  <div class="wrapper flex">
    
    <!-- =========================
         NAVBAR
    ==========================-->
    <nav class="w-full bg-gray-700 text-white p-3 flex justify-between items-center shadow-md fixed z-10">
      <!-- Left navbar links -->
      <a href="#" class="text-white mx-3" id="sidebarToggle">
          <i class="fas fa-bars"></i>
      </a>

      <!-- Right navbar links -->
      <ul class="flex items-center space-x-5">
        <!--<li><a href="#" class="text-white hover:text-gray-400"><i class="fas fa-search"></i></a></li>-->
        <li class="relative">
          <a href="#" class="text-white hover:text-gray-400">
            <i class="fas fa-bell"></i>
            <span class="absolute -top-2 -right-2 bg-red-500 text-xs rounded-full h-5 w-5 flex items-center justify-center">5</span>
          </a>
        </li>
        
        <li class="relative dropdown-trigger">
            <i class="fa-solid fa-gear"></i>

            <!-- Dropdown Menu -->
            <div class="dropdown-content absolute right-0 top-full mt-2 w-40 py-2 z-50 bg-white rounded-lg shadow-lg border border-gray-200">
                <a href="{{ route('userprofile') }}" class="dropdown-item block px-4 py-2 text-black text-center hover:bg-gray-500">Profile</a>
                <a href="#" class="dropdown-item block px-4 py-2 text-black text-center hover:bg-gray-500">Settings</a>

                <!-- Logout -->
                <a href="{{ route('logout') }}"
                  class="dropdown-item block px-4 py-2 text-red-600 text-center hover:bg-gray-500"
                  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  Logout
                </a>

                <!-- Hidden Logout Form -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </li>

      </ul>
    </nav>

    <!-- =========================
         SIDEBAR
    ==========================-->
    <aside class="sidebar bg-gray-700 text-white h-screen fixed mt-12 z-10 transition-all duration-300 sidebar-content w-48">
      <!-- Brand Logo -->
      <div class="brand-link flex items-center justify-center py-4 border-b border-white">
        <i class="fa-solid fa-shield"></i>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-4">
        <ul class="space-y-1 px-2">
          <li class="nav-item rounded">
            <a href="{{  route('dashboard') }}" class="flex items-center py-3 px-3 text-white">
              <i class="fa-solid fa-house text-blue-300 w-6 text-center"></i>
              <span class="nav-item-text ml-3">Dashboard</span>
            </a>
          </li>
          <!-- List Results -->
          <li class="nav-item rounded">
            <a href="{{  route('result') }}" class="flex items-center py-3 px-3 text-white">
              <i class="fas fa-clock text-blue-300 w-6 text-center"></i>
              <span class="nav-item-text ml-3">Detection Result</span>
            </a>
          </li>
          <li class="nav-item rounded">
            <a href="{{ route('status') }}" class="flex items-center py-3 px-3 text-white">
              <i class="fas fa-envelope text-blue-300 w-6 text-center"></i>
              <span class="nav-item-text ml-3">Email Status</span>
            </a>
          </li>
          <!--<li class="nav-item rounded">
            <a href="{{  route('discussion') }}" class="flex items-center py-3 px-3 text-white">
              <i class="fa-solid fa-comment text-blue-300 w-6 text-center"></i>
              <span class="nav-item-text ml-3">Discussion</span> 
            </a>
          </li>-->
          <li class="nav-item rounded">
            <a href="{{  route('reports.index') }}" class="flex items-center py-3 px-3 text-white">
              <i class="fa-solid fa-flag text-blue-300 w-6 text-center"></i>
              <span class="nav-item-text ml-3">My Reports</span>
            </a>
          </li>
          <li class="nav-item rounded">
            <a href="{{  route('help') }}" class="flex items center py-3 px-3 text-white">
              <i class="fa-solid fa-question text-blue-300 w-6 text-center"></i>
              <span class="nav-item-text ml-3">Help</span>
            </a>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- =========================
         MAIN CONTENT
    ==========================-->
    <div class="main-content mt-12 ml-16 p-3 transition-all duration-300 w-full">
      <div class="content-header bg-white p-3 rounded-t-lg shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          @yield('page-header')
          <x-breadcrumb />
        </div>
      </div>

      <div class="bg-white p-4 rounded-b-lg shadow-sm">
        @yield('content')
      </div>
      
    </div>
  </div>

  <!-- =========================
       SCRIPT
  ==========================-->
  <script>
    // Toggle sidebar collapse on click
    document.getElementById('sidebarToggle').addEventListener('click', function(e) {
      e.preventDefault();
      document.body.classList.toggle('sidebar-collapsed');
    });
  </script>
</body>
</html>
