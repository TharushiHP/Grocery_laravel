<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-r from-green-50 to-green-100">
        <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex flex-col items-center mb-6">
                <!-- Grocery Store Logo -->
                <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-green-600 mb-2">Create Account</h2>
                <p class="text-gray-600 text-sm">Join  Grocery Cart for great deals!</p>
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <x-label for="name" value="{{ __('Full Name') }}" class="text-gray-700" />
                    <x-input id="name" class="block mt-1 w-full rounded-md border-green-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
                </div>

                <div>
                    <x-label for="email" value="{{ __('Email Address') }}" class="text-gray-700" />
                    <x-input id="email" class="block mt-1 w-full rounded-md border-green-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="john@example.com" />
                </div>

                <div>
                    <x-label for="password" value="{{ __('Password') }}" class="text-gray-700" />
                    <x-input id="password" class="block mt-1 w-full rounded-md border-green-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                </div>

                <div>
                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-gray-700" />
                    <x-input id="password_confirmation" class="block mt-1 w-full rounded-md border-green-300 focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mt-4">
                        <x-label for="terms">
                            <div class="flex items-center">
                                <x-checkbox name="terms" id="terms" required class="rounded border-green-300 text-green-600 focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50" />
                                <div class="ms-2 text-sm text-gray-600">
                                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-green-600 hover:text-green-800 hover:underline">'.__('Terms of Service').'</a>',
                                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-green-600 hover:text-green-800 hover:underline">'.__('Privacy Policy').'</a>',
                                    ]) !!}
                                </div>
                            </div>
                        </x-label>
                    </div>
                @endif

                <div class="flex items-center justify-between mt-6">
                    <a class="text-sm text-green-600 hover:text-green-800 hover:underline" href="{{ route('login') }}">
                    {{ __('Already have an account?') }}
                </a>

                <x-button class="bg-green-600 hover:bg-green-700 focus:ring-green-500">
                    {{ __('Create Account') }}
                </x-button>
            </div>

            <div class="text-center mt-6 border-t border-gray-200 pt-4">
                <p class="text-xs text-gray-600">By creating an account, you'll get access to exclusive deals and personalized shopping recommendations!</p>
            </div>
        </form>
    </div>
</div>
</x-guest-layout>
