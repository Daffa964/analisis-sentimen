<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display: flex; flex-direction: row; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 24px;">
            <!-- Left Content Info -->
            <div style="flex: 1; min-width: 280px; display: flex; align-items: flex-start; gap: 16px;">
                <div style="padding: 12px; background: rgba(16, 185, 129, 0.1); border-radius: 12px; color: #10b981;">
                    <!-- SVG QR Icon -->
                    <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 700; color: inherit; margin-bottom: 4px;">Cetak Brosur & QR Code Kuesioner</h3>
                    <p style="font-size: 13px; color: gray; line-height: 1.5; max-width: 600px;">
                        Gunakan brosur A4 potret resmi ini untuk dipasang di area tunggu, ruang rawat inap, atau meja administrasi RSUD. Pasien cukup scan QR Code di brosur untuk mengisi kuesioner secara online menggunakan HP mereka.
                    </p>
                </div>
            </div>

            <!-- Right Call to Action -->
            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <!-- Mini QR Code Preview -->
                <div style="padding: 8px; background: #ffffff; border: 1px solid rgba(128,128,128,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data={{ urlencode('https://docs.google.com/forms/d/e/1FAIpQLSf5iie_PLj5DF7ogS1nrO8wUCT7yFs4XfMPIXdjo_ecJgKF5g/viewform?usp=dialog') }}" 
                         alt="Mini QR Code Preview"
                         style="width: 80px; height: 80px; display: block; border-radius: 6px;">
                </div>

                <a href="{{ route('report.flyer') }}" 
                   target="_blank"
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: var(--primary-600); color: #ffffff; font-size: 13px; font-weight: 700; border-radius: 8px; text-decoration: none; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2); transition: all 0.2s;"
                   onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 15px rgba(16, 185, 129, 0.3)';"
                   onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 10px rgba(16, 185, 129, 0.2)';">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Buka & Cetak Brosur
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
