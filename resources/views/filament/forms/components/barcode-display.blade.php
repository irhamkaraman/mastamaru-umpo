<div class="space-y-4">
    @if($getRecord() && $getRecord()->raw_barcode)
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="text-center">
                <!-- Toggle Mode Button -->
                <div class="mb-4 flex justify-end">
                    <button
                        type="button"
                        onclick="toggleDarkMode()"
                        class="inline-flex items-center px-3 py-1 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        Mode
                    </button>
                </div>

                <div class="mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">QR Code Peserta</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $getRecord()->name }} - {{ $getRecord()->student_id }}</p>
                </div>

                <!-- QR Code Image -->
                <div class="mb-4 flex justify-center">
                    <div id="qrcode-{{ $getRecord()->id }}" class="bg-white dark:bg-gray-100 p-4 border rounded"></div>
                </div>

                <!-- Download Button -->
                <div class="flex justify-center space-x-2">
                    <button
                        type="button"
                        onclick="downloadQRCode('{{ $getRecord()->id }}', '{{ $getRecord()->name }}_{{ $getRecord()->student_id }}')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Unduh QR Code
                    </button>
                </div>

                <!-- Raw Data -->
                <div class="mt-4 text-left">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data Raw QR Code:</label>
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 border border-gray-300 dark:border-gray-600 rounded-md overflow-hidden">
                        <div class="text-sm font-mono text-gray-800 dark:text-gray-200 whitespace-pre-wrap break-words overflow-wrap-break-word word-break-break-all max-w-full">{{ $getRecord()->raw_barcode }}</div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <p>QR Code belum tersedia</p>
        </div>
    @endif
</div>

<!-- Include QRCode library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.2.2/build/qrcode.min.js"></script>

<script>
    // Dark mode toggle
    function toggleDarkMode() {
        document.documentElement.classList.toggle('dark');
        localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
    }

    // Initialize dark mode from localStorage
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }

        @if($getRecord() && $getRecord()->raw_barcode)
            // Generate QR Code
            const qrData = @json($getRecord()->raw_barcode);
            const qrElement = document.getElementById('qrcode-{{ $getRecord()->id }}');

            if (qrElement && qrData) {
                // Create canvas for QR code
                const canvas = document.createElement('canvas');
                qrElement.appendChild(canvas);

                try {
                    QRCode.toCanvas(canvas, qrData, {
                        width: 200,
                        margin: 2,
                        color: {
                            dark: '#000000',
                            light: '#FFFFFF'
                        }
                    }, function (error) {
                        if (error) {
                            console.error('Error generating QR code:', error);
                            qrElement.innerHTML = '<p class="text-red-500 dark:text-red-400">Error generating QR code</p>';
                        }
                    });
                } catch (error) {
                    console.error('Error generating QR code:', error);
                    qrElement.innerHTML = '<p class="text-red-500 dark:text-red-400">Error generating QR code</p>';
                }
            }
        @endif
    });

    function downloadQRCode(recordId, filename) {
        const canvas = document.querySelector('#qrcode-' + recordId + ' canvas');
        if (canvas) {
            // Create download link
            const link = document.createElement('a');
            link.download = filename + '_qrcode.png';
            link.href = canvas.toDataURL('image/png');

            // Trigger download
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else {
            alert('QR Code tidak ditemukan');
        }
    }
</script>
