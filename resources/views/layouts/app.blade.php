<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: true }" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Expense Manager') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="dark:bg-gray-900 text-white min-h-screen antialiased">

    <!-- Mobile Toggle Button -->
    <div class="block md:hidden p-4">
        <header>
            <button @click="sidebarOpen = !sidebarOpen" class="text-white">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </header>
    </div>

    <div class="flex">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'block' : 'hidden md:block'" class="w-72 bg-gray-900 min-h-screen shadow-lg p-6 space-y-6">
            <div class="flex flex-col items-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff"
                     class="w-24 h-24 rounded-full border-4 border-indigo-500 shadow" alt="User Avatar">
                <h2 class="mt-4 text-xl font-bold">{{ Auth::user()->name }}</h2>
                <p class="text-sm text-gray-400">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</p>
            </div>

            <nav class="space-y-2 text-sm">
                <!-- Admin Only: Dashboard -->
                @role('Administrator')
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-4 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('dashboard') ? 'bg-gray-800 font-semibold' : '' }}">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>

                <hr class="border-gray-700 mx-4">

                <!-- Admin Only: User Management -->
                <h3 class="px-4 text-xs uppercase tracking-wide text-gray-500 mt-4">User Management</h3>
                <a href="{{ route('users.index') }}"
                   class="flex items-center px-4 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('users.*') ? 'bg-gray-800 font-semibold' : '' }}">
                    <i class="fas fa-users mr-2"></i> Users
                </a>
                <a href="{{ route('roles.index') }}"
                   class="flex items-center px-4 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('roles.*') ? 'bg-gray-800 font-semibold' : '' }}">
                    <i class="fas fa-user-shield mr-2"></i> Roles
                </a>

                <hr class="border-gray-700 mx-4">
                @endrole

                <!-- Shared: Expense Management -->
                <h3 class="px-4 text-xs uppercase tracking-wide text-gray-500 mt-4">Expense Management</h3>
                @role('Administrator')
                <a href="{{ route('expense-categories.index') }}"
                   class="flex items-center px-4 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('expense-categories.*') ? 'bg-gray-800 font-semibold' : '' }}">
                    <i class="fas fa-tags mr-2"></i> Categories
                </a>
                @endrole
                <a href="{{ route('expenses.index') }}"
                   class="flex items-center px-4 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('expenses.*') ? 'bg-gray-800 font-semibold' : '' }}">
                    <i class="fas fa-receipt mr-2"></i> Expenses
                </a>

                <hr class="border-gray-700 mx-4">

                <!-- Profile & Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center w-full px-4 py-2 rounded hover:bg-gray-800 text-left">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
