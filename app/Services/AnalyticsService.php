<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationAssignment;
use App\Models\DonationItem;
use App\Models\DonationRequest;
use App\Models\Rating;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getUserStats(?Carbon $start = null, ?Carbon $end = null): array
    {
        $query = User::query();
        $this->applyDateRange($query, $start, $end, 'created_at');

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];
    }

    public function getDonationStats(?Carbon $start = null, ?Carbon $end = null): array
    {
        $query = Donation::query();
        $this->applyDateRange($query, $start, $end, 'created_at');

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
        ];
    }

    public function getDeliveryStats(): array
    {
        return [
            'total' => DonationAssignment::count(),
            'pending' => DonationAssignment::where('status', 'pending')->count(),
            'accepted' => DonationAssignment::where('status', 'accepted')->count(),
            'in_progress' => DonationAssignment::where('status', 'in_progress')->count(),
            'completed' => DonationAssignment::where('status', 'completed')->count(),
            'cancelled' => DonationAssignment::where('status', 'cancelled')->count(),
            'external' => DonationAssignment::where('is_external_delivery', true)->count(),
        ];
    }

    public function getRequestStats(): array
    {
        return [
            'total' => DonationRequest::count(),
            'pending' => DonationRequest::where('status', 'pending')->count(),
            'approved' => DonationRequest::where('status', 'approved')->count(),
            'rejected' => DonationRequest::where('status', 'rejected')->count(),
        ];
    }

    public function getFoodSavedStats(?Carbon $start = null, ?Carbon $end = null): array
    {
        $itemRowsQuery = DonationItem::query()
            ->join('donations', 'donations.id', '=', 'donation_items.donation_id')
            ->where('donations.status', Donation::STATUS_COMPLETED)
            ->selectRaw('LOWER(COALESCE(NULLIF(donation_items.quantity_unit, ""), "kg")) as unit_key')
            ->selectRaw('SUM(CAST(donation_items.quantity as DECIMAL(14,2))) as total_quantity')
            ->selectRaw('COUNT(*) as item_count')
            ->groupBy('unit_key');

        $this->applyDateRange($itemRowsQuery, $start, $end, 'donations.created_at');
        $itemRows = $itemRowsQuery->get();

        $legacyRowsQuery = Donation::query()
            ->where('status', Donation::STATUS_COMPLETED)
            ->doesntHave('items')
            ->selectRaw('LOWER(COALESCE(NULLIF(quantity_unit, ""), "kg")) as unit_key')
            ->selectRaw('SUM(CAST(quantity as DECIMAL(14,2))) as total_quantity')
            ->selectRaw('COUNT(*) as item_count')
            ->groupBy('unit_key');

        $this->applyDateRange($legacyRowsQuery, $start, $end, 'created_at');
        $legacyRows = $legacyRowsQuery->get();

        $grouped = [];

        foreach ($itemRows as $row) {
            $unit = (string) $row->unit_key;
            $grouped[$unit] = [
                'unit' => $unit,
                'quantity' => (float) $row->total_quantity,
                'count' => (int) $row->item_count,
            ];
        }

        foreach ($legacyRows as $row) {
            $unit = (string) $row->unit_key;
            if (! isset($grouped[$unit])) {
                $grouped[$unit] = [
                    'unit' => $unit,
                    'quantity' => 0.0,
                    'count' => 0,
                ];
            }
            $grouped[$unit]['quantity'] += (float) $row->total_quantity;
            $grouped[$unit]['count'] += (int) $row->item_count;
        }

        uasort($grouped, fn (array $a, array $b) => $b['quantity'] <=> $a['quantity']);

        $totalItems = array_sum(array_column($grouped, 'count'));
        $totalQuantity = array_sum(array_column($grouped, 'quantity'));
        $unitBreakdown = array_values(array_map(function (array $entry) {
            return [
                'unit' => $entry['unit'],
                'quantity' => round($entry['quantity'], 2),
                'count' => $entry['count'],
            ];
        }, $grouped));

        return [
            'formatted' => number_format($totalItems) . ' items',
            'items_count' => $totalItems,
            'total_quantity' => round((float) $totalQuantity, 2),
            'by_unit' => $unitBreakdown,
        ];
    }

    public function getMonthlyDonations(int $months = 12, ?Carbon $anchorDate = null): array
    {
        $anchor = ($anchorDate ? $anchorDate->copy() : now())->startOfMonth();
        $start = $anchor->copy()->subMonths($months - 1);
        $end = $anchor->copy()->endOfMonth();

        $counts = Donation::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_key'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('count', 'month_key')
            ->toArray();

        $out = [];
        $cursor = $start->copy();
        for ($i = 0; $i < $months; $i++) {
            $key = $cursor->format('Y-m');
            $out[$key] = (int) ($counts[$key] ?? 0);
            $cursor->addMonth();
        }

        return $out;
    }

    public function getMonthlyCompletedDonations(int $months = 12): array
    {
        return Donation::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_key'),
                DB::raw('COUNT(*) as count')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('count', 'month_key')
            ->toArray();
    }

    public function getUsersByRole(?Carbon $start = null, ?Carbon $end = null): array
    {
        $query = DB::table('role_user')
            ->join('users', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('roles.display_name', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.display_name');

        $this->applyDateRange($query, $start, $end, 'users.created_at');

        return $query->pluck('count', 'display_name')->toArray();
    }

    public function getDonationsByStatus(): array
    {
        return Donation::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function getTopDonors(int $limit = 10): array
    {
        return User::select('users.id', 'users.name', 'users.city')
            ->selectRaw('COUNT(donations.id) as donations_count')
            ->selectRaw('SUM(donations.quantity) as total_quantity')
            ->join('donations', 'donations.user_id', '=', 'users.id')
            ->where('donations.deleted_at', null)
            ->groupBy('users.id', 'users.name', 'users.city')
            ->orderByDesc('donations_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getTopCharities(int $limit = 10): array
    {
        return User::select('users.id', 'users.name', 'users.organization_name')
            ->selectRaw('COUNT(donation_requests.id) as requests_count')
            ->join('donation_requests', 'donation_requests.charity_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name', 'users.organization_name')
            ->orderByDesc('requests_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getTopVolunteers(int $limit = 10): array
    {
        return User::select('users.id', 'users.name', 'users.role_type', 'users.points')
            ->selectRaw('COUNT(donation_assignments.id) as assignments_count')
            ->selectRaw('SUM(CASE WHEN donation_assignments.status = "completed" THEN 1 ELSE 0 END) as completed_count')
            ->selectRaw('(SELECT ROUND(AVG(r.rating), 1) FROM ratings r WHERE r.rateable_id = users.id AND r.rateable_type = "App\\\\Models\\\\User") as avg_rating')
            ->selectRaw('(SELECT COUNT(*) FROM ratings r WHERE r.rateable_id = users.id AND r.rateable_type = "App\\\\Models\\\\User") as ratings_count')
            ->join('donation_assignments', 'donation_assignments.volunteer_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name', 'users.role_type', 'users.points')
            ->orderByDesc('completed_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getTopDonorsByPoints(int $limit = 10): array
    {
        return User::select('users.id', 'users.name', 'users.city', 'users.points')
            ->selectRaw('COUNT(donations.id) as donations_count')
            ->join('donations', 'donations.user_id', '=', 'users.id')
            ->where('donations.deleted_at', null)
            ->groupBy('users.id', 'users.name', 'users.city', 'users.points')
            ->orderByDesc('users.points')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getAverageRating(): float
    {
        return round((float) Rating::avg('rating'), 1);
    }

    public function getTopFoodTypes(int $limit = 10): array
    {
        return DonationItem::select('food_type', DB::raw('COUNT(*) as count'))
            ->groupBy('food_type')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('count', 'food_type')
            ->toArray();
    }

    public function getSubscriptionRevenueSummary(int $year, int $month): array
    {
        $monthCents = (int) SubscriptionPayment::query()
            ->whereYear('paid_at', $year)
            ->whereMonth('paid_at', $month)
            ->sum('amount_cents');

        $totalCents = (int) SubscriptionPayment::query()->sum('amount_cents');

        return [
            'month_cents' => $monthCents,
            'total_cents' => $totalCents,
            'month_formatted' => '$'.number_format($monthCents / 100, 2),
            'total_formatted' => '$'.number_format($totalCents / 100, 2),
        ];
    }

    /**
     * @return array<string,int> month_key => amount_cents
     */
    public function getMonthlySubscriptionRevenue(int $months = 12, ?Carbon $anchorDate = null): array
    {
        $out = [];
        $anchor = ($anchorDate ? $anchorDate->copy() : now())->startOfMonth();
        $cursor = $anchor->copy()->subMonths($months - 1);
        for ($i = 0; $i < $months; $i++) {
            $key = $cursor->format('Y-m');
            $out[$key] = (int) SubscriptionPayment::query()
                ->whereYear('paid_at', $cursor->year)
                ->whereMonth('paid_at', $cursor->month)
                ->sum('amount_cents');
            $cursor->addMonth();
        }

        return $out;
    }

    public function getDashboardData(int $year, int $month): array
    {
        $periodStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $userStats = $this->getUserStats($periodStart, $periodEnd);
        $donationStats = $this->getDonationStats($periodStart, $periodEnd);
        $foodSaved = $this->getFoodSavedStats($periodStart, $periodEnd);
        $rev = $this->getSubscriptionRevenueSummary($year, $month);

        return [
            'total_users' => $userStats['total'],
            'total_donations' => $donationStats['total'],
            'pending_approvals' => $userStats['pending'],
            'food_saved' => $foodSaved['formatted'],
            'food_saved_chart' => $foodSaved['by_unit'],
            'food_saved_total_items' => $foodSaved['items_count'],
            'monthly_donations' => $this->getMonthlyDonations(12, $periodStart),
            'monthly_subscription_cents' => $this->getMonthlySubscriptionRevenue(12, $periodStart),
            'users_by_role' => $this->getUsersByRole($periodStart, $periodEnd),
            'subscription_month_cents' => $rev['month_cents'],
            'subscription_total_cents' => $rev['total_cents'],
            'subscription_month_formatted' => $rev['month_formatted'],
            'subscription_total_formatted' => $rev['total_formatted'],
            'revenue_filter_year' => $year,
            'revenue_filter_month' => $month,
            'accepted_donations' => $donationStats['in_progress'],
        ];
    }

    private function applyDateRange($query, ?Carbon $start, ?Carbon $end, string $column = 'created_at'): void
    {
        if ($start && $end) {
            $query->whereBetween($column, [$start, $end]);
        } elseif ($start) {
            $query->where($column, '>=', $start);
        } elseif ($end) {
            $query->where($column, '<=', $end);
        }
    }

    public function getReportsData(): array
    {
        return [
            'user_stats' => $this->getUserStats(),
            'donation_stats' => $this->getDonationStats(),
            'delivery_stats' => $this->getDeliveryStats(),
            'request_stats' => $this->getRequestStats(),
            'food_saved' => $this->getFoodSavedStats(),
            'monthly_donations' => $this->getMonthlyDonations(),
            'monthly_completed' => $this->getMonthlyCompletedDonations(),
            'users_by_role' => $this->getUsersByRole(),
            'donations_by_status' => $this->getDonationsByStatus(),
            'top_donors' => $this->getTopDonors(),
            'top_donors_by_points' => $this->getTopDonorsByPoints(),
            'top_charities' => $this->getTopCharities(),
            'top_volunteers' => $this->getTopVolunteers(),
            'average_rating' => $this->getAverageRating(),
            'top_food_types' => $this->getTopFoodTypes(),
        ];
    }
}
