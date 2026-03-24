<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Email;
use App\Models\DetectionResult;
use App\Models\UserReport;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Admin Dashboard - Overview Tab
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_emails_scanned' => Email::count(),
            'total_detections' => DetectionResult::count(),
            'phishing_emails' => DetectionResult::where('final_decision', 'phishing')->count(),
            'legitimate_emails' => DetectionResult::where('final_decision', 'legitimate')->count(),
            'pending_reports' => UserReport::where('admin_status', 'pending')->count(),
        ];

        $recent_emails = Email::with('user')
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        $recent_detections = DetectionResult::with('email')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_emails', 'recent_detections'));
    }

    // Users Management Tab
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.users', compact('users'));
    }

    // View specific user details
    public function userDetail($id)
    {
        $user = User::findOrFail($id);
        
        $stats = [
            'emails_scanned' => Email::where('user_id', $id)->count(),
            'detections_run' => DetectionResult::whereHas('email', function ($q) use ($id) {
                $q->where('user_id', $id);
            })->count(),
            'reports_filed' => UserReport::where('user_id', $id)->count(),
            'phishing_detected' => DetectionResult::whereHas('email', function ($q) use ($id) {
                $q->where('user_id', $id);
            })->where('final_decision', 'phishing')->count(),
        ];

        $recent_emails = Email::where('user_id', $id)
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        return view('admin.user-detail', compact('user', 'stats', 'recent_emails'));
    }

    // Change user role
    public function updateUserRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|in:user,admin']);

        $user = User::findOrFail($id);
        $old_role = $user->role;
        $user->update(['role' => $request->role]);

        return redirect()->route('admin.user-detail', $id)
            ->with('success', "User role changed from {$old_role} to {$request->role}");
    }

    // Detection Reports Tab
    public function reports(Request $request)
    {
        $query = UserReport::with(['user', 'email']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('admin_status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('email', function ($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                  ->orWhere('from', 'like', "%{$request->search}%");
            });
        }

        $reports = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.reports', compact('reports'));
    }

    // View report details
    public function reportDetail($id)
    {
        $report = UserReport::with(['user', 'email'])->findOrFail($id);
        $detection = DetectionResult::where('email_id', $report->email_id)->first();

        return view('admin.report-detail', compact('report', 'detection'));
    }

    // Update report status
    public function updateReportStatus(Request $request, $id)
    {
        $request->validate([
            'admin_status' => 'required|in:pending,investigating,resolved,false_positive',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $report = UserReport::findOrFail($id);
        $report->update([
            'admin_status' => $request->admin_status,
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.report-detail', $id)
            ->with('success', 'Report status updated successfully');
    }

    // System Health Tab
    public function health()
    {
        $health_data = [
            'ml_service' => $this->checkMLServiceHealth(),
            'database' => $this->checkDatabaseHealth(),
            'cache' => $this->checkCacheHealth(),
            'queue' => $this->checkQueueHealth(),
        ];

        $recent_errors = $this->getRecentErrors();

        return view('admin.health', compact('health_data', 'recent_errors'));
    }

    // Detection Analytics Tab
    public function analytics(Request $request)
    {
        $period = $request->query('period', '30'); // days
        $from_date = now()->subDays($period);

        $detection_trend = DetectionResult::where('created_at', '>=', $from_date)
            ->selectRaw('DATE(created_at) as date, final_decision, COUNT(*) as count')
            ->groupBy('date', 'final_decision')
            ->orderBy('date')
            ->get();

        $detection_stats = [
            'total_detections' => DetectionResult::where('created_at', '>=', $from_date)->count(),
            'phishing_count' => DetectionResult::where('created_at', '>=', $from_date)
                ->where('final_decision', 'phishing')->count(),
            'legitimate_count' => DetectionResult::where('created_at', '>=', $from_date)
                ->where('final_decision', 'legitimate')->count(),
            'avg_rule_score' => DetectionResult::where('created_at', '>=', $from_date)
                ->avg('rule_score') ?? 0,
            'avg_ml_confidence' => DetectionResult::where('created_at', '>=', $from_date)
                ->avg('ml_confidence') ?? 0,
        ];

        $rule_performance = DetectionResult::selectRaw('rule_result, COUNT(*) as count')
            ->where('created_at', '>=', $from_date)
            ->groupBy('rule_result')
            ->get();

        $ml_performance = DetectionResult::selectRaw('ml_result, COUNT(*) as count')
            ->where('created_at', '>=', $from_date)
            ->groupBy('ml_result')
            ->get();

        return view('admin.analytics', compact('detection_trend', 'detection_stats', 'rule_performance', 'ml_performance', 'period'));
    }

    // Helper methods
    private function checkMLServiceHealth()
    {
        try {
            // Implement actual ML service check
            return ['status' => 'online', 'response_time' => '150ms'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'error' => $e->getMessage()];
        }
    }

    private function checkDatabaseHealth()
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'healthy'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }

    private function checkCacheHealth()
    {
        try {
            \Cache::put('health_check', true, 60);
            $result = \Cache::get('health_check');
            return ['status' => $result ? 'healthy' : 'unhealthy'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'error' => $e->getMessage()];
        }
    }

    private function checkQueueHealth()
    {
        try {
            $jobs = \DB::table('jobs')->count();
            return ['status' => 'running', 'pending_jobs' => $jobs];
        } catch (\Exception $e) {
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    private function getRecentErrors()
    {
        // Read recent error logs
        $log_file = storage_path('logs/laravel.log');
        if (file_exists($log_file)) {
            $lines = file($log_file);
            $recent = array_slice($lines, -20);
            return array_map('trim', $recent);
        }
        return [];
    }
}
