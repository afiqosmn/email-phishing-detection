<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HybridPhish - User Reports</title>

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
      width: 12rem;
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
        <li><a href="#" class="text-white hover:text-gray-400"><i class="fas fa-search"></i></a></li>
        <li class="relative dropdown-trigger group">
          <a href="#" class="text-white hover:text-gray-400"><i class="fas fa-bell"></i></a>
          <div class="dropdown-content absolute right-0 mt-2 w-48 bg-white rounded shadow-lg hidden group-hover:block text-gray-700 text-sm z-20">
            <a href="#" class="block px-4 py-2 hover:bg-gray-100">No notifications</a>
          </div>
        </li>
        <li class="relative dropdown-trigger group">
          <a href="#" class="text-white hover:text-gray-400"><i class="fas fa-user-circle text-xl"></i></a>
          <div class="dropdown-content absolute right-0 mt-2 w-48 bg-white rounded shadow-lg hidden group-hover:block text-gray-700 text-sm z-20">
            <a href="{{ route('userprofile') }}" class="block px-4 py-2 hover:bg-gray-100">Profile</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
              @csrf
              <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
            </form>
          </div>
        </li>
      </ul>
    </nav>

    <!-- =========================
         SIDEBAR
    ==========================-->
    <aside class="sidebar-content fixed left-0 top-0 w-64 h-screen bg-gray-800 text-white pt-14 overflow-y-auto transition-all duration-300 z-9">
      <div class="sidebar-branding p-4 border-b border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center">
          <img src="{{ asset('images/logohybridphish.ico') }}" alt="Logo" class="w-8 h-8 mr-2 rounded">
          <span class="brand-text font-bold text-lg">HybridPhish</span>
        </a>
      </div>

      <!-- User Info -->
      <div class="user-info p-4 border-b border-gray-700">
        <p class="text-xs text-gray-400">Logged in as</p>
        <p class="font-semibold text-white text-sm">{{ Auth::user()->email ?? 'User' }}</p>
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
          <li class="nav-item rounded">
            <a href="{{  route('discussion') }}" class="flex items-center py-3 px-3 text-white">
              <i class="fa-solid fa-comment text-blue-300 w-6 text-center"></i>
              <span class="nav-item-text ml-3">Discussion</span> 
            </a>
          </li>
          <li class="nav-item rounded">
            <a href="{{  route('reports.index') }}" class="flex items-center py-3 px-3 text-white bg-blue-600 rounded">
              <i class="fa-solid fa-flag text-blue-300 w-6 text-center"></i>
              <span class="nav-item-text ml-3">My Reports</span>
            </a>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- =========================
         MAIN CONTENT
    ==========================-->
    <main class="main-content w-full ml-16 mt-12 p-4 transition-all duration-300">
      @yield('content')
    </main>

  </div>

  <script>
    // Sidebar toggle functionality
    document.getElementById('sidebarToggle').addEventListener('click', function(e) {
      e.preventDefault();
      document.body.classList.toggle('sidebar-collapsed');
      localStorage.setItem('sidebarCollapsed', document.body.classList.contains('sidebar-collapsed'));
    });

    // Load sidebar state from localStorage
    if (localStorage.getItem('sidebarCollapsed') === 'false') {
      document.body.classList.remove('sidebar-collapsed');
    }
  </script>
</body>
</html>
