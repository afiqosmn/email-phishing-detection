<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Admin Login</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/logohybridphish.ico') }}" type="image/x-icon"/>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8">
        
        <!-- Logo / Title -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Admin Panel</h1>
            <p class="text-sm text-gray-500 mt-1">Login to manage system</p>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input 
                    type="email" 
                    name="email" 
                    required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input 
                    type="password" 
                    name="password" 
                    required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
            </div>

            <!-- Remember -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded">
                    Remember me
                </label>
            </div>

            <!-- Login Button -->
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition">
                <a href="{{ route('admin.dashboard') }}">Login</a>
            </button>
            
            <!-- Forgot Password -->
            <a href="#" class="block text-center text-sm text-gray-500 hover:text-gray-700">
                Forgot your password?
            </a>

            <!-- Back to Home -->
            <a href="{{ route('welcome') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700 mt-4">
                &larr; Back to Home
            </a>

        </form>

    </div>

</body>
</html>
