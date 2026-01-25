<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Recovery Codes') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="max-w-xl mx-auto">
                    @if(session('success'))
                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium mb-2">{{ __('Recovery Codes') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Store these codes in a safe place. Each code can only be used once to recover your account if you lose access to your authenticator app.') }}
                        </p>
                    </div>

                    <!-- Warning -->
                    <div class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-red-800 dark:text-red-200">
                                    {{ __('Important:') }}
                                </p>
                                <p class="text-xs text-red-700 dark:text-red-300 mt-1">
                                    {{ __('Download or print these codes now. You will not be able to see them again.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Recovery Codes -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6" id="recovery-codes-container">
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($recoveryCodes as $code)
                                <div class="bg-white dark:bg-gray-800 p-3 rounded border font-mono text-sm text-center select-all">
                                    {{ $code }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button onclick="downloadCodes()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            {{ __('Download') }}
                        </button>
                        
                        <button onclick="printCodes()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            {{ __('Print') }}
                        </button>
                        
                        <button onclick="copyCodes()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            {{ __('Copy All') }}
                        </button>
                    </div>

                    <div class="text-center mt-8">
                        <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            {{ __('← Back to Profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const recoveryCodes = @json($recoveryCodes);
        
        function downloadCodes() {
            const content = `{{ config('app.name') }} - Two-Factor Recovery Codes\n\nGenerated: {{ now()->format('Y-m-d H:i:s') }}\n\n` + 
                recoveryCodes.join('\n') + 
                '\n\nIMPORTANT: Store these codes safely. Each can only be used once.';
            
            const blob = new Blob([content], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = '{{ config("app.name") }}_recovery_codes_{{ now()->format("Y-m-d") }}.txt';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
        
        function printCodes() {
            const printContent = `
                <html>
                <head>
                    <title>{{ config('app.name') }} - Recovery Codes</title>
                    <style>
                        body { font-family: monospace; padding: 20px; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .codes { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 20px 0; }
                        .code { border: 1px solid #ccc; padding: 10px; text-align: center; }
                        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>{{ config('app.name') }}</h1>
                        <h2>Two-Factor Authentication Recovery Codes</h2>
                        <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div class="codes">
                        ${recoveryCodes.map(code => `<div class="code">${code}</div>`).join('')}
                    </div>
                    <div class="warning">
                        <strong>IMPORTANT:</strong> Store these codes safely. Each code can only be used once to recover your account.
                    </div>
                </body>
                </html>
            `;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }
        
        function copyCodes() {
            const content = recoveryCodes.join('\n');
            navigator.clipboard.writeText(content).then(function() {
                // Show success feedback
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>{{ __('Copied!') }}`;
                setTimeout(() => {
                    button.innerHTML = originalText;
                }, 2000);
            });
        }
    </script>
</x-app-layout>