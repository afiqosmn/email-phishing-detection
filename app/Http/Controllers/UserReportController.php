<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\UserReport;
use App\Models\DetectionResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReportController extends Controller
{
    /**
     * Show all user reports for the authenticated user
     */
    public function index()
    {
        $reports = UserReport::where('user_id', auth()->id())
            ->with(['email', 'email.detectionResult'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('reports.index', compact('reports'));
    }

    /**
     * Show form to create a new report
     */
    public function create($emailId)
    {
        $email = Email::findOrFail($emailId);

        // Ensure user can only report their own emails
        if ($email->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $detectionResult = $email->detectionResult;

        return view('reports.create', compact('email', 'detectionResult'));
    }

    /**
     * Store a new user report
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email_id' => 'required|exists:emails,id',
            'report_type' => 'required|in:false_positive,false_negative,unrequested_phishing,whitelist_request,other',
            'reason' => 'nullable|string|max:1000',
        ]);

        $email = Email::findOrFail($validated['email_id']);

        // Ensure user can only report their own emails
        if ($email->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Prevent duplicate reports for the same email
        $existingReport = UserReport::where('user_id', auth()->id())
            ->where('email_id', $validated['email_id'])
            ->first();

        if ($existingReport) {
            return redirect()->back()->with('info', 'You have already reported this email.');
        }

        UserReport::create([
            'user_id' => auth()->id(),
            'email_id' => $validated['email_id'],
            'report_type' => $validated['report_type'],
            'reason' => $validated['reason'] ?? null,
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'Report submitted successfully. Thank you for helping us improve our detection system!');
    }

    /**
     * Show details of a specific report
     */
    public function show($id)
    {
        $report = UserReport::findOrFail($id);

        // Ensure user can only view their own reports
        if ($report->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('reports.show', compact('report'));
    }

    /**
     * Delete a report (only if not yet reviewed)
     */
    public function destroy($id)
    {
        $report = UserReport::findOrFail($id);

        // Ensure user can only delete their own reports
        if ($report->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Allow deletion only if not reviewed
        if ($report->status !== 'submitted') {
            return redirect()->back()->with('error', 'Cannot delete a report that has been reviewed.');
        }

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Report deleted successfully.');
    }
}
