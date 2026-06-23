<div class="flex flex-col items-center justify-center p-4 text-center">
    <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">{{ $name }}</h3>
    
    <div class="bg-white p-4 rounded-xl shadow-md inline-block mb-4 border border-gray-100">
        <img id="qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($url) }}" alt="QR Code" class="w-48 h-48">
    </div>
    
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 break-all max-w-xs bg-gray-50 dark:bg-gray-800 p-2 rounded-lg border border-gray-100 dark:border-gray-700 font-mono">
        {{ $url }}
    </p>
    
    <div class="flex gap-3">
        <a href="https://api.qrserver.com/v1/create-qr-code/?size=1000x1000&data={{ urlencode($url) }}" 
           target="_blank" 
           download="QR-{{ Str::slug($name) }}.png"
           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-500 dark:bg-primary-500 dark:hover:bg-primary-400 rounded-lg shadow transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Unduh QR (HD)
        </a>
        
        <button onclick="printQr()" 
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:text-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak QR
        </button>
    </div>
</div>

<script>
function printQr() {
    const qrSrc = document.getElementById('qr-image').src;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Cetak QR Code - {{ $name }}</title>
            <style>
                body {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    font-family: Arial, sans-serif;
                    margin: 0;
                }
                .container {
                    text-align: center;
                    border: 2px solid #ccc;
                    padding: 40px;
                    border-radius: 20px;
                }
                img {
                    width: 300px;
                    height: 300px;
                }
                h1 {
                    font-size: 24px;
                    margin-bottom: 5px;
                }
                p {
                    font-size: 14px;
                    color: #555;
                }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            <div class="container">
                <h1>KUESIONER KUALITAS PELAYANAN</h1>
                <h2>{{ $name }}</h2>
                <img src="${qrSrc.replace('size=250x250', 'size=500x500')}" alt="QR">
                <p>Silakan scan QR Code di atas untuk mengisi survei kepuasan pelayanan rawat inap</p>
                <p style="font-weight: bold; margin-top: 20px;">RSUD dr. Loekmono Hadi Kudus</p>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
