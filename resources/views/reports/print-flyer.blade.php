<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brosur Kuesioner Survei Kepuasan - RSUD dr. Loekmono Hadi</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap');
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
            color: #1e293b;
        }

        /* Poster Container (A4 Portrait Ratio in CSS) */
        .poster {
            background: #ffffff;
            width: 794px; /* A4 width in pixels at 96 DPI */
            height: 1123px; /* A4 height in pixels at 96 DPI */
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            border-radius: 24px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Top Header Brand Grid */
        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 25px;
        }

        .logo-container {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            color: #ffffff;
        }

        .brand-text h1 {
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }

        /* Main Content Title */
        .main-title {
            text-align: center;
            margin-top: 30px;
        }

        .main-title h2 {
            font-size: 38px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .main-title h2 span {
            color: #10b981;
            display: block;
            font-size: 42px;
            margin-top: 4px;
        }

        .main-title p {
            font-size: 16px;
            color: #475569;
            margin-top: 12px;
            font-weight: 400;
            max-width: 580px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        /* QR Code Container Frame */
        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 40px 0;
            position: relative;
        }

        .qr-frame {
            background: #ffffff;
            padding: 24px;
            border-radius: 28px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 4px solid #10b981;
            position: relative;
            transition: all 0.3s ease;
        }

        .qr-frame::before, .qr-frame::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            border: 4px solid #059669;
        }

        /* Custom decorative scanner brackets */
        .qr-frame::before {
            top: -12px;
            left: -12px;
            border-right: none;
            border-bottom: none;
            border-top-left-radius: 12px;
        }

        .qr-frame::after {
            bottom: -12px;
            right: -12px;
            border-left: none;
            border-top: none;
            border-bottom-right-radius: 12px;
        }

        .qr-code-image {
            width: 220px;
            height: 220px;
            display: block;
        }

        .scan-badge {
            margin-top: 24px;
            background-color: #f0fdf4;
            color: #166534;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: 1px solid #bbf7d0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        }

        /* Step by Step Guide */
        .steps-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 10px;
        }

        .step-card {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .step-number {
            width: 32px;
            height: 32px;
            background: #10b981;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .step-card h4 {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .step-card p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }

        /* Bottom Footer Brand Info */
        .footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        .footer-note {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #059669;
            font-weight: 600;
        }

        .footer-note svg {
            width: 16px;
            height: 16px;
        }

        /* Float Action Button for Printing */
        .print-fab {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            padding: 16px 28px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 700;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
            z-index: 999;
        }

        .print-fab:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(16, 185, 129, 0.5);
        }

        /* Print Media CSS Overrides */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                display: block;
            }

            .poster {
                width: 100%;
                height: 100%;
                border-radius: 0;
                box-shadow: none;
                border: none;
                padding: 0;
            }

            .print-fab {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Print Button -->
    <button onclick="window.print()" class="print-fab">
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak Poster Brosur
    </button>

    <!-- Poster Content -->
    <div class="poster">
        
        <!-- Header -->
        <div class="header">
            <div class="logo-container">
                <svg class="logo-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 10.5V20a2 2 0 01-2 2H7a2 2 0 01-2-2v-9.5m14 0a2 2 0 00-2-2h-3.5m5 2a2 2 0 01-2 2h-3.5m0-4H7m5 4v6m-3-3h6m3-10V4a2 2 0 00-2-2H9a2 2 0 00-2 2v3m10 0H7"/>
                </svg>
            </div>
            <div class="brand-text">
                <h1>RSUD dr. Loekmono Hadi</h1>
                <p>Pemerintah Kabupaten Kudus • Melayani dengan Hati</p>
            </div>
        </div>

        <!-- Main Title -->
        <div class="main-title">
            <h2>Survei Kepuasan<span>Pelayanan Rawat Inap</span></h2>
            <p>Penilaian dan masukan Anda sangat berharga untuk membantu kami terus mengevaluasi dan meningkatkan mutu pelayanan pasien di setiap bangsal perawatan.</p>
        </div>

        <!-- QR Section -->
        <div class="qr-section">
            <div class="qr-frame">
                <img class="qr-code-image" src="{{ $qrCodeUrl }}" alt="QR Code Survei">
            </div>
            <div class="scan-badge">Scan QR Code Untuk Mengisi</div>
        </div>

        <!-- Steps Guide -->
        <div class="steps-container">
            <div class="step-card">
                <div class="step-number">1</div>
                <h4>Buka Kamera</h4>
                <p>Aktifkan kamera smartphone atau aplikasi pemindai QR Code Anda.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h4>Pindai QR</h4>
                <p>Arahkan kamera ke QR Code di atas hingga tautan kuesioner muncul.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h4>Isi Form</h4>
                <p>Lengkapi tanggapan Anda secara jujur dan kirim jawaban Anda.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>
                <strong>Unit Peningkatan Mutu Pelayanan</strong>
                <p>RSUD dr. Loekmono Hadi Kudus</p>
            </div>
            <div class="footer-note">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                100% Anonim & Aman
            </div>
        </div>

    </div>

</body>
</html>
