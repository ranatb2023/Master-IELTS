<?php

namespace App\Services;

use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RefundReport
{
    /**
     * Get total refunds for a period
     */
    public function getTotalRefunds($startDate = null, $endDate = null)
    {
        $query = Enrollment::where('payment_status', 'refunded');

        if ($startDate) {
            $query->where('refunded_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('refunded_at', '<=', $endDate);
        }

        return [
            'count' => $query->count(),
            'total_amount' => $query->sum('refund_amount'),
        ];
    }

    /**
     * Get refund rate by package
     */
    public function getRefundRateByPackage()
    {
        return DB::table('enrollments')
            ->join('user_package_accesses', 'enrollments.package_access_id', '=', 'user_package_accesses.id')
            ->join('packages', 'user_package_accesses.package_id', '=', 'packages.id')
            ->select(
                'packages.id',
                'packages.name',
                DB::raw('COUNT(*) as total_enrollments'),
                DB::raw('SUM(CASE WHEN enrollments.payment_status = "refunded" THEN 1 ELSE 0 END) as refunded_count'),
                DB::raw('SUM(CASE WHEN enrollments.payment_status = "refunded" THEN enrollments.refund_amount ELSE 0 END) as total_refunded'),
                DB::raw('ROUND((SUM(CASE WHEN enrollments.payment_status = "refunded" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as refund_rate')
            )
            ->groupBy('packages.id', 'packages.name')
            ->having('total_enrollments', '>', 0)
            ->orderBy('refund_rate', 'desc')
            ->get();
    }

    /**
     * Get monthly refund trends
     */
    public function getMonthlyTrends($months = 12)
    {
        $startDate = Carbon::now()->subMonths($months);

        return Enrollment::where('payment_status', 'refunded')
            ->where('refunded_at', '>=', $startDate)
            ->select(
                DB::raw('YEAR(refunded_at) as year'),
                DB::raw('MONTH(refunded_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(refund_amount) as total_amount')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                $item->month_name = Carbon::create($item->year, $item->month)->format('M Y');
                return $item;
            });
    }

    /**
     * Get average refund amount
     */
    public function getAverageRefundAmount()
    {
        return Enrollment::where('payment_status', 'refunded')
            ->avg('refund_amount') ?? 0;
    }

    /**
     * Get refunds by reason
     */
    public function getRefundsByReason()
    {
        return Enrollment::where('payment_status', 'refunded')
            ->whereNotNull('refund_reason')
            ->select('refund_reason', DB::raw('COUNT(*) as count'), DB::raw('SUM(refund_amount) as total_amount'))
            ->groupBy('refund_reason')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get refund statistics for dashboard
     */
    public function getDashboardStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'total_refunds' => [
                'today' => $this->getTotalRefunds($today, Carbon::now()),
                'this_month' => $this->getTotalRefunds($thisMonth, Carbon::now()),
                'last_month' => $this->getTotalRefunds($lastMonth, Carbon::now()->subMonth()->endOfMonth()),
                'all_time' => $this->getTotalRefunds(),
            ],
            'average_refund' => $this->getAverageRefundAmount(),
            'refund_rate' => $this->calculateOverallRefundRate(),
        ];
    }

    /**
     * Calculate overall refund rate
     */
    protected function calculateOverallRefundRate()
    {
        $totalEnrollments = Enrollment::whereNotNull('package_access_id')->count();
        $refundedEnrollments = Enrollment::where('payment_status', 'refunded')->count();

        if ($totalEnrollments == 0) {
            return 0;
        }

        return round(($refundedEnrollments / $totalEnrollments) * 100, 2);
    }
}
