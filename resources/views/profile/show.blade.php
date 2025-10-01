<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div style="background: linear-gradient(to bottom, #f3f4f6, #ffffff); min-height: 100vh; padding: 2rem 0;">
        <div class="max-w-7xl mx-auto px-4">
            
            <!-- Profile Overview Card -->
            <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 1.5rem; overflow: hidden;">
                <div style="background: linear-gradient(135deg, #16a34a, #15803d); color: white; padding: 2rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 5rem; height: 5rem; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 style="font-size: 2rem; font-weight: bold; margin: 0; color: white;">{{ Auth::user()->name }}</h3>
                            <p style="color: #bbf7d0; margin: 0.25rem 0;">{{ ucfirst(Auth::user()->role ?? 'Customer') }}</p>
                            <p style="color: #bbf7d0; font-size: 0.875rem; margin: 0;">Member since {{ Auth::user()->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 1.5rem;">
                    @livewire('profile.update-profile-information-form')
                </div>
            @endif

            <!-- Delivery Address Section -->
            <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 1.5rem; padding: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Delivery Addresses
                </h3>
                
                <!-- Primary Address -->
                <div id="address-display" style="border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <span style="background: #dcfce7; color: #16a34a; font-size: 0.75rem; font-weight: 500; padding: 0.25rem 0.625rem; border-radius: 0.25rem;">Primary</span>
                            <p style="margin-top: 0.5rem; color: #374151; line-height: 1.5;">
                                <strong>{{ Auth::user()->name }}</strong><br>
                                @if(Auth::user()->phone_number)
                                    {{ Auth::user()->phone_number }}<br>
                                @endif
                                <span id="address-text" style="color: #6b7280;">No address saved yet</span>
                            </p>
                        </div>
                        <button onclick="editAddress()" style="color: #16a34a; font-size: 0.875rem; background: none; border: none; cursor: pointer;">Edit</button>
                    </div>
                </div>
                
                <!-- Address Edit Form (Hidden by default) -->
                <div id="address-form" style="border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; display: none;">
                    <span style="background: #dcfce7; color: #16a34a; font-size: 0.75rem; font-weight: 500; padding: 0.25rem 0.625rem; border-radius: 0.25rem; margin-bottom: 1rem; display: inline-block;">Primary Address</span>
                    
                    <div style="display: grid; gap: 1rem; margin-top: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Street Address</label>
                            <input type="text" id="street-address" placeholder="Enter your street address" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">City</label>
                                <input type="text" id="city" placeholder="Enter city" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Postal Code</label>
                                <input type="text" id="postal-code" placeholder="Enter postal code" style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                            <button onclick="saveAddress()" style="background: #16a34a; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; font-size: 0.875rem; font-weight: 500; cursor: pointer;">Save</button>
                            <button onclick="cancelEdit()" style="background: #f3f4f6; color: #374151; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; font-size: 0.875rem; font-weight: 500; cursor: pointer;">Cancel</button>
                        </div>
                    </div>
                </div>
                
                <button style="background: #16a34a; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                    + Add New Address
                </button>
            </div>

            <!-- Order History Section -->
            <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 1.5rem; padding: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Recent Orders
                </h3>
                
                @php
                    $recentOrders = Auth::user()->orders()->latest()->take(3)->get();
                @endphp
                
                @if($recentOrders->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($recentOrders as $order)
                            <div style="border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div>
                                        <p style="font-weight: 600; color: #1f2937; margin: 0;">Order #{{ $order->id }}</p>
                                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0;">{{ $order->created_at->format('M d, Y') }}</p>
                                        <p style="font-size: 0.875rem; color: #374151; margin: 0;">{{ $order->orderDetails->count() }} items</p>
                                    </div>
                                    <div style="text-align: right;">
                                        <p style="font-weight: 600; color: #1f2937; margin: 0;">Rs. {{ number_format($order->total_amount, 2) }}</p>
                                        @if($order->status === 'pending')
                                            <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: #fef3c7; color: #92400e;">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @elseif($order->status === 'processing')
                                            <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: #dbeafe; color: #1e40af;">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @elseif($order->status === 'delivered')
                                            <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: #dcfce7; color: #16a34a;">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @else
                                            <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: #f3f4f6; color: #374151;">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div style="margin-top: 1rem;">
                        <a href="{{ route('orders.index') }}" style="color: #16a34a; font-size: 0.875rem; font-weight: 500; text-decoration: none;">
                            View All Orders →
                        </a>
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem 0; color: #6b7280;">
                        <svg style="width: 3rem; height: 3rem; margin: 0 auto 0.75rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l-2.5 5m0 0h3M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                        </svg>
                        <p style="margin: 0; font-weight: 500;">No orders yet</p>
                        <p style="font-size: 0.875rem; margin: 0.25rem 0 0.75rem;">Start shopping to see your orders here</p>
                        <a href="{{ route('home') }}" style="display: inline-block; background: #16a34a; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                            Start Shopping
                        </a>
                    </div>
                @endif
            </div>

            <!-- Account Preferences -->
            <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 1.5rem; padding: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Preferences
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <label style="font-size: 0.875rem; font-weight: 500; color: #374151;">Email notifications</label>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Receive updates about your orders</p>
                        </div>
                        <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" value="" style="position: absolute; opacity: 0;" checked>
                            <div style="width: 2.75rem; height: 1.5rem; background: #16a34a; border-radius: 9999px; position: relative; transition: all 0.3s;">
                                <div style="position: absolute; top: 0.125rem; left: 1.375rem; width: 1.25rem; height: 1.25rem; background: white; border-radius: 50%; transition: all 0.3s;"></div>
                            </div>
                        </label>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <label style="font-size: 0.875rem; font-weight: 500; color: #374151;">SMS notifications</label>
                            <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Get delivery updates via SMS</p>
                        </div>
                        <label style="position: relative; display: inline-flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" value="" style="position: absolute; opacity: 0;">
                            <div style="width: 2.75rem; height: 1.5rem; background: #d1d5db; border-radius: 9999px; position: relative; transition: all 0.3s;">
                                <div style="position: absolute; top: 0.125rem; left: 0.125rem; width: 1.25rem; height: 1.25rem; background: white; border-radius: 50%; transition: all 0.3s;"></div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 1.5rem;">
                    @livewire('profile.update-password-form')
                </div>
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 1.5rem;">
                    @livewire('profile.two-factor-authentication-form')
                </div>
            @endif

            <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 1.5rem;">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 1.5rem;">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>

    <script>
        // Address editing functionality
        function editAddress() {
            document.getElementById('address-display').style.display = 'none';
            document.getElementById('address-form').style.display = 'block';
            
            // Load current address if exists
            const currentAddress = document.getElementById('address-text').textContent;
            if (currentAddress !== 'No address saved yet') {
                // You can parse and populate the form fields here if needed
            }
        }
        
        function cancelEdit() {
            document.getElementById('address-display').style.display = 'block';
            document.getElementById('address-form').style.display = 'none';
            
            // Clear form fields
            document.getElementById('street-address').value = '';
            document.getElementById('city').value = '';
            document.getElementById('postal-code').value = '';
        }
        
        function saveAddress() {
            const streetAddress = document.getElementById('street-address').value;
            const city = document.getElementById('city').value;
            const postalCode = document.getElementById('postal-code').value;
            
            if (!streetAddress || !city) {
                alert('Please fill in at least the street address and city.');
                return;
            }
            
            // Update the display
            let addressText = streetAddress;
            if (city) addressText += ', ' + city;
            if (postalCode) addressText += ' ' + postalCode;
            
            document.getElementById('address-text').textContent = addressText;
            document.getElementById('address-text').style.color = '#374151';
            
            // Hide form and show display
            cancelEdit();
            
            // Here you could add AJAX call to save to database
            // For now, it's just saved in the session/display
            alert('Address saved successfully!');
        }
        
        // Toggle switches functionality
        document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
            const toggle = checkbox.nextElementSibling;
            const slider = toggle.querySelector('div');
            
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    toggle.style.backgroundColor = '#16a34a';
                    slider.style.transform = 'translateX(1.25rem)';
                } else {
                    toggle.style.backgroundColor = '#d1d5db';
                    slider.style.transform = 'translateX(0)';
                }
            });
            
            // Set initial state
            if (checkbox.checked) {
                toggle.style.backgroundColor = '#16a34a';
                slider.style.transform = 'translateX(1.25rem)';
            }
        });
    </script>
</x-app-layout>