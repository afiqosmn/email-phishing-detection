<?php

namespace App\Http\Controllers;

use App\Models\DetectionResult;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DetectionResultController extends Controller
{
    // List detection results (ikut user)
    public function index()
    {
        $results = DetectionResult::with(['email', 'urlEvidences', 'authenticationEvidences', 'keywordEvidences', 'htmlAnomalyEvidences'])
            ->whereHas('email', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('/end-user/result', compact('results'));
    }

    // View single result
    public function show($id)
    {
        $result = DetectionResult::with(['email', 'urlEvidences', 'authenticationEvidences', 'keywordEvidences', 'htmlAnomalyEvidences'])
            ->whereHas('email', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->findOrFail($id);

        return view('emails.result-view', compact('result'));
    }

    // Download detection result as PDF
    public function downloadPdf($id)
    {
        $result = DetectionResult::with(['email', 'urlEvidences', 'authenticationEvidences', 'keywordEvidences', 'htmlAnomalyEvidences'])
            ->whereHas('email', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->findOrFail($id);

        $pdf = PDF::loadView('pdfs.detection-report', compact('result'));
        
        $filename = 'detection_result_' . $result->id . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    // Delete result
    public function destroy($id)
    {
        $result = DetectionResult::with('email')
            ->whereHas('email', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->findOrFail($id);

        $result->delete();

        return redirect()
            ->route('result')
            ->with('success', 'Detection result deleted.');
    }
}
