<?php
namespace App\Http\Controllers\Api\Approver;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $userIds = User::where('company_id', $companyId)
            ->where('role', 'traveler')
            ->pluck('id');

        $bookings = Booking::whereIn('user_id', $userIds)->get();

        $byModal = $bookings->groupBy('modal')->map(fn($group) => [
            'count'   => $group->count(),
            'savings' => round($group->sum('company_savings'), 2),
        ]);

        // Ensure all modals are present
        foreach (['hotel', 'flight', 'bus', 'car'] as $modal) {
            if (!isset($byModal[$modal])) {
                $byModal[$modal] = ['count' => 0, 'savings' => 0];
            }
        }

        return response()->json([
            'total_company_savings'       => round($bookings->sum('company_savings'), 2),
            'total_onhappy_coins_distributed' => round($bookings->sum('onhappy_coins_amount'), 2),
            'total_bookings_with_savings' => $bookings->where('savings_total', '>', 0)->count(),
            'active_travelers'            => $bookings->pluck('user_id')->unique()->count(),
            'by_modal'                    => $byModal,
        ]);
    }
}
