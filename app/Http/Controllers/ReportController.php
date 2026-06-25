<?php

namespace App\Http\Controllers;

use App\Models\SurveyPeriod;
use App\Models\Criterion;
use App\Services\SawService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Print the AHP-SAW analysis report.
     */
    public function print(Request $request)
    {
        $request->validate([
            'period_id' => 'required|integer',
            'category' => 'required|string|in:regular,vip',
        ]);

        $periodId = $request->query('period_id');
        $category = $request->query('category');

        $period = SurveyPeriod::findOrFail($periodId);
        $criteria = Criterion::orderBy('code')->get();

        $sawService = app(SawService::class);
        $sawResults = $sawService->calculate($periodId, $category);

        if (!$sawResults['has_data']) {
            return redirect()->back()->withErrors('Tidak ada data penilaian untuk dicetak.');
        }

        return view('reports.print-saw', [
            'period' => $period,
            'category' => $category,
            'criteria' => $criteria,
            'sawResults' => $sawResults,
            'datePrinted' => now()->translatedFormat('d F Y H:i'),
        ]);
    }

    /**
     * Print the survey flyer with QR Code.
     */
    public function printFlyer()
    {
        $googleFormUrl = 'https://docs.google.com/forms/d/e/1FAIpQLSf5iie_PLj5DF7ogS1nrO8wUCT7yFs4XfMPIXdjo_ecJgKF5g/viewform?usp=dialog';

        return view('reports.print-flyer', [
            'googleFormUrl' => $googleFormUrl,
            'qrCodeUrl' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($googleFormUrl),
        ]);
    }
}
