<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RefundReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $refundReport;

    public function __construct(RefundReport $refundReport)
    {
        $this->refundReport = $refundReport;
    }

    /**
     * Display refund analytics
     */
    public function refunds(Request $request)
    {
        // Get date range from request or default to last 12 months
        $months = $request->get('months', 12);

        // Get all refund analytics data
        $stats = [
            'total' => $this->refundReport->getTotalRefunds(),
            'average' => $this->refundReport->getAverageRefundAmount(),
            'monthly_trends' => $this->refundReport->getMonthlyTrends($months),
            'by_package' => $this->refundReport->getRefundRateByPackage(),
            'by_reason' => $this->refundReport->getRefundsByReason(),
            'dashboard_stats' => $this->refundReport->getDashboardStats(),
        ];

        return view('admin.reports.refunds', compact('stats', 'months'));
    }
}
