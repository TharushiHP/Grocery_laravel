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
        /* Fix for large icons */
        svg, img.icon {
            width: 1.5rem !important;
            height: 1.5rem !important;
        }
        .hero-icon {
            width: 2rem !important;
            height: 2rem !important;
        }
        /* Ensure icons in cards are properly sized */
        .product-card svg,
        .cart-icon svg,
        .nav-icon svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
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

        <!-- Toast Notifications -->
        @if(session('success'))
            <div id="success-toast" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div id="error-toast" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- Toast Notification Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show success toast
            const successToast = document.getElementById('success-toast');
            if (successToast) {
                setTimeout(() => {
                    successToast.classList.remove('translate-x-full');
                    successToast.classList.add('translate-x-0');
                }, 100);
                
                setTimeout(() => {
                    successToast.classList.add('translate-x-full');
                    successToast.classList.remove('translate-x-0');
                }, 5000);
            }
            
            // Show error toast
            const errorToast = document.getElementById('error-toast');
            if (errorToast) {
                setTimeout(() => {
                    errorToast.classList.remove('translate-x-full');
                    errorToast.classList.add('translate-x-0');
                }, 100);
                
                setTimeout(() => {
                    errorToast.classList.add('translate-x-full');
                    errorToast.classList.remove('translate-x-0');
                }, 5000);
            }
        });
    </script>

    @livewireScripts
</body>
</html>