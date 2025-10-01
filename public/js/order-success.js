/**
 * Order Success Handler
 * Provides real-time success feedback for order placement
 */

class OrderSuccessHandler {
    constructor() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Listen for checkout form submissions
        const checkoutForm = document.querySelector('form[action*="checkout"]');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', (e) => {
                this.handleOrderSubmission(e);
            });
        }

        // Listen for API order submissions
        this.setupApiHandlers();
    }

    handleOrderSubmission(event) {
        const form = event.target;
        
        // Show loading state only (no popup)
        this.showLoadingState(form);
        
        // Remove popup - let the form redirect naturally to success page
        // The success page will handle all the celebration
    }

    showLoadingState(form) {
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing Order...
            `;
            
            // Store original text for restoration
            submitButton.dataset.originalText = originalText;
        }
    }

    showInstantSuccessMessage() {
        this.createSuccessModal();
        this.playSuccessSound();
        this.showConfetti();
    }

    createSuccessModal() {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-3xl p-8 max-w-md mx-4 text-center transform scale-0 transition-transform duration-300">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Order Placed Successfully! 🎉</h2>
                <p class="text-gray-600 mb-6">Your order is being processed and you'll receive confirmation shortly.</p>
                <div class="space-y-2 text-left bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex items-center text-sm text-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Order confirmation sent
                    </div>
                    <div class="flex items-center text-sm text-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Processing within 2-4 hours
                    </div>
                    <div class="flex items-center text-sm text-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Delivery in 2-3 business days
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" 
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg w-full transition-colors">
                    Continue Shopping
                </button>
            </div>
        `;

        document.body.appendChild(modal);

        // Animate in
        setTimeout(() => {
            modal.querySelector('div').classList.remove('scale-0');
            modal.querySelector('div').classList.add('scale-100');
        }, 100);

        // Auto close after 8 seconds
        setTimeout(() => {
            if (modal.parentElement) {
                modal.remove();
            }
        }, 8000);
    }

    playSuccessSound() {
        // Create success sound (optional)
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(1000, audioContext.currentTime + 0.1);
            oscillator.frequency.setValueAtTime(1200, audioContext.currentTime + 0.2);
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);
        } catch (e) {
            console.log('Audio not supported');
        }
    }

    showConfetti() {
        // Simple confetti animation
        for (let i = 0; i < 50; i++) {
            this.createConfettiPiece();
        }
    }

    createConfettiPiece() {
        const confetti = document.createElement('div');
        confetti.style.cssText = `
            position: fixed;
            width: 10px;
            height: 10px;
            background: ${['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#feca57'][Math.floor(Math.random() * 5)]};
            top: -10px;
            left: ${Math.random() * 100}vw;
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            animation: confetti-fall 3s linear forwards;
        `;
        
        document.body.appendChild(confetti);
        
        setTimeout(() => {
            confetti.remove();
        }, 3000);
    }

    setupApiHandlers() {
        // Override fetch for API calls
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            const response = await originalFetch(...args);
            
            // Check if this is an order API call
            if (args[0]?.includes('/api/') && args[0]?.includes('order')) {
                const clonedResponse = response.clone();
                try {
                    const data = await clonedResponse.json();
                    if (data.status === 'success' && data.message?.includes('Order')) {
                        this.showApiSuccessMessage(data);
                    }
                } catch (e) {
                    // Ignore parsing errors
                }
            }
            
            return response;
        };
    }

    showApiSuccessMessage(data) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
        toast.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <div>
                    <div class="font-semibold">${data.message || 'Order placed successfully!'}</div>
                    ${data.order?.id ? `<div class="text-sm opacity-90">Order #${data.order.id}</div>` : ''}
                </div>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
        }, 100);
        
        // Animate out
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            toast.classList.remove('translate-x-0');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // Static method to show success message programmatically
    static showSuccess(message = 'Order placed successfully!', details = {}) {
        const handler = new OrderSuccessHandler();
        handler.createSuccessModal();
        handler.playSuccessSound();
        handler.showConfetti();
    }
}

// Add CSS for confetti animation
const style = document.createElement('style');
style.textContent = `
    @keyframes confetti-fall {
        0% {
            transform: translateY(-10px) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(360deg);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new OrderSuccessHandler();
});

// Make available globally
window.OrderSuccessHandler = OrderSuccessHandler;