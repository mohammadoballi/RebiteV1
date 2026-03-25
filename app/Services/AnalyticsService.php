<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationAssignment;
use App\Models\DonationItem;
use App\Models\DonationRequest;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getUserStats(): array
    {
        return [
            'total' => User::count(),
            'pending' => User::where('status', 'pending')->count(),
            'approved' => User::where('status', 'approved')->count(),
            'rejected' => User::where('status', 'rejected')->count(),
        ];
    }

    public function getDonationStats(): array
    {
        return [
            'total' => Donation::count(),
            'pending' => Donation::where('status', 'pending')->count(),
            'accepted' => Donation::where('status', 'accepted')->count(),
            'assigned' => Donation::where('status', 'assigned')->count(),
            'in_transit' => Donation::where('status', 'in_transit')->count(),
            'delivered' => Donation::where('status', 'delivered')->count(),
            'completed' => Donation::where('status', 'completed')->count(),
            'cancelled' => Donation::where('status', 'cancelled')->count(),
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

    public function getFoodSavedStats(): array
    {
        $totalQty = (float) Donation::where('status', 'completed')->sum('quantity');
        $totalItems = DonationItem::whereHas('donation', fn($q) => $q->where('status', 'completed'))->count();

        return [
            'quantity' => $totalQty,
            'formatted' => number_format($totalQty) . ' kg',
            'items_count' => $totalItems,
        ];
    }

    public function getMonthlyDonations(int $months = 12): array
    {
        return Donation::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_key'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('count', 'month_key')
            ->toArray();
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

    public function getUsersByRole(): array
    {
        return DB::table('role_user')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('roles.display_name', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.display_name')
            ->pluck('count', 'display_name')
            ->toArray();
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
        return User::select('users.id', 'users.name', 'users.role_type')
            ->selectRaw('COUNT(donation_assignments.id) as assignments_count')
            ->selectRaw('SUM(CASE WHEN donation_assignments.status = "completed" THEN 1 ELSE 0 END) as completed_count')
            ->join('donation_assignments', 'donation_assignments.volunteer_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name', 'users.role_type')
            ->orderByDesc('completed_count')
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

    public function getDashboardData(): array
    {
        $userStats = $this->getUserStats();
        $donationStats = $this->getDonationStats();
        $foodSaved = $this->getFoodSavedStats();

        return [
            'total_users' => $userStats['total'],
            'total_donations' => $donationStats['total'],
            'pending_approvals' => $userStats['pending'],
            'food_saved' => $foodSaved['formatted'],
            'monthly_donations' => $this->getMonthlyDonations(),
            'users_by_role' => $this->getUsersByRole(),
            'recent_donations' => Donation::with('donor:id,name')
                ->latest()
                ->limit(5)
                ->get(),
        ];
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
            'top_charities' => $this->getTopCharities(),
            'top_volunteers' => $this->getTopVolunteers(),
            'average_rating' => $this->getAverageRating(),
            'top_food_types' => $this->getTopFoodTypes(),
        ];
    }
}
