<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Grocery Cart' }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(to bottom, #f3f4f6, #ffffff);
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body style="font-family: 'Inter', sans-serif; background: linear-gradient(to bottom, #f3f4f6, #ffffff); margin: 0; padding: 0;">
    <div style="min-height: 100vh;">
        <!-- Header with Nav - Forced Green Styling -->
        <header style="background-color: #16a34a; color: white; padding: 1rem 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 50;">
            <div style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <!-- Logo and Title -->
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" style="width: 24px; height: 24px; border-radius: 4px;">
                        <h1 style="font-size: 1.25rem; font-weight: bold; color: white; margin: 0;">Grocery Cart</h1>
                    </div>
                
                    <!-- Navigation Links -->
                    <nav style="display: flex; align-items: center; gap: 1.5rem; font-size: 0.875rem; font-weight: 500;">
                        <a href="{{ route('home') }}" 
                           style="color: white; text-decoration: none; padding: 0.25rem 0.5rem; {{ request()->routeIs('home') ? 'text-decoration: underline; font-weight: bold;' : '' }}"
                           onmouseover="this.style.color='#bbf7d0'; this.style.textDecoration='underline';"
                           onmouseout="this.style.color='white'; {{ request()->routeIs('home') ? 'this.style.textDecoration=\'underline\';' : 'this.style.textDecoration=\'none\';' }}">
                            Home
                        </a>
                        
                        @auth
                            @livewire('shopping-cart')
                            
                            <a href="{{ route('profile.show') }}" 
                               style="color: white; text-decoration: none; padding: 0.25rem 0.5rem;"
                               onmouseover="this.style.color='#bbf7d0'; this.style.textDecoration='underline';"
                               onmouseout="this.style.color='white'; this.style.textDecoration='none';">
                                Profile
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                                @csrf
                                <button type="submit" 
                                        style="color: #fecaca; background: none; border: none; font-size: 0.875rem; cursor: pointer; padding: 0.25rem 0.5rem; font-family: inherit;"
                                        onmouseover="this.style.color='#fee2e2'; this.style.textDecoration='underline';"
                                        onmouseout="this.style.color='#fecaca'; this.style.textDecoration='none';">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" 
                               style="color: white; text-decoration: none; padding: 0.25rem 0.5rem; font-weight: 500;"
                               onmouseover="this.style.color='#bbf7d0'; this.style.textDecoration='underline';"
                               onmouseout="this.style.color='white'; this.style.textDecoration='none';">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" 
                               style="color: white; text-decoration: none; padding: 0.25rem 0.5rem; font-weight: 500;"
                               onmouseover="this.style.color='#bbf7d0'; this.style.textDecoration='underline';"
                               onmouseout="this.style.color='white'; this.style.textDecoration='none';">
                                Register
                            </a>
                        @endauth
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>