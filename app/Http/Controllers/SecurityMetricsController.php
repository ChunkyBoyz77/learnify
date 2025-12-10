<?php

namespace App\Http\Controllers;

use App\Services\PaymentSecurityService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class SecurityMetricsController extends Controller
{
    protected PaymentSecurityService $securityService;

    public function __construct(PaymentSecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Check if user is authorized to access security metrics.
     */
    protected function authorizeAccess(): void
    {
        if (!auth()->check()) {
            abort(403, 'You must be logged in to view security metrics.');
        }

        if (auth()->user()->role !== 'instructor' && auth()->user()->role !== 'admin') {
            abort(403, 'Only instructors and administrators can view security metrics.');
        }
    }

    /**
     * Display security metrics dashboard.
     */
    public function index(Request $request): View
    {
        $this->authorizeAccess();

        // Get date range from request or default to last 30 days
        $startDate = $request->has('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->subDays(30);
        
        $endDate = $request->has('end_date') 
            ? Carbon::parse($request->end_date) 
            : Carbon::now();

        // Calculate metrics
        $metrics = $this->securityService->calculateSecurityMetrics($startDate, $endDate);
        $summary = $this->securityService->getSecurityMetricsSummary();
        $recentEvents = $this->securityService->getRecentSecurityEvents(20);

        return view('security.metrics', [
            'metrics' => $metrics,
            'summary' => $summary,
            'recentEvents' => $recentEvents,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Generate and download security report.
     */
    public function report(Request $request)
    {
        $this->authorizeAccess();

        $startDate = $request->has('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->subDays(30);
        
        $endDate = $request->has('end_date') 
            ? Carbon::parse($request->end_date) 
            : Carbon::now();

        $report = $this->securityService->generateSecurityReport($startDate, $endDate);

        // Return JSON response (can be extended to PDF/Excel export)
        return response()->json($report, 200, [], JSON_PRETTY_PRINT);
    }
}
