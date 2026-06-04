<x-layout>
    <div class="max-w-4xl mx-auto py-20 text-center flex flex-col items-center justify-center min-h-[40vh]">

        <div class="flex flex-col items-center justify-center space-y-5 max-w-md mx-auto">

            <x-paddle-button :checkout="$checkout" id="auto-pay-btn"
                class="inline-flex items-center gap-3 px-8 py-4 text-lg font-semibold tracking-wide text-white bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl shadow-lg shadow-indigo-200 hover:from-indigo-700 hover:to-violet-700 hover:shadow-xl hover:shadow-indigo-300 active:scale-[0.98] transition-all duration-300 group cursor-pointer">

                <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>

                <span>Securing Connection to Paddle...</span>

                <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                    </path>
                </svg>
            </x-paddle-button>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-left shadow-sm">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-amber-900">Important Order Notice:</p>
                        <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                            Once your payment is complete, <strong>please wait and do not close or refresh this
                                window</strong>. The system will automatically safely redirect you to your Order
                            Confirmation page .
                        </p>
                    </div>
                </div>
            </div>

            <p class="text-xs text-gray-400">
                If the payment window doesn't open within 3 seconds, please click the button manually.
            </p>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payBtn = document.getElementById('auto-pay-btn');

            if (payBtn) {
                // Instantly trigger the Paddle overlay on page load
                payBtn.click();
            }
        });
    </script>
</x-layout>
