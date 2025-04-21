<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kahani;
use App\Models\Membership;
use App\Models\Coupon;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AdminProfileController extends Controller
{

    public function dashboard(Request $request)
    {
        $logedinuser = Auth::user();

        $users = User::count();
        $kahanis = Kahani::count();
        $reviews = Review::count();
        if ($logedinuser->type == 'superadmin') {
            $coupon = Coupon::count();
            $admincoupon = Coupon::where('admin_id', $logedinuser->id)->count();
        } else {
            $coupon = Coupon::where('admin_id', $logedinuser->id)->count();
            $usermemberships = DB::table('coupons')
                ->rightJoin('memberships', 'memberships.coupon', '=', 'coupons.coupon_code')
                ->where('memberships.status', 'completed')
                ->where('admin_id', $logedinuser->id)->count();

            $subadminearnings = DB::table('coupons')
                ->leftjoin('memberships', 'memberships.coupon', '=', 'coupons.coupon_code')
                ->join('packages', 'memberships.package_id', '=', 'packages.id')
                ->where('memberships.status', 'completed')
                ->where('admin_id', $logedinuser->id)
                ->selectRaw('SUM(
                    CASE
                        WHEN coupons.discount_percentage IS NOT NULL THEN 
                            packages.price - (packages.price * (coupons.discount_percentage / 100))
                        ELSE
                            packages.price
                    END
                ) as total_earnings')
                ->first()->total_earnings;

            $organizationpercentage = DB::table('memberships')
                ->leftJoin('packages', 'memberships.package_id', '=', 'packages.id')
                ->leftJoin('coupons', 'memberships.coupon', '=', 'coupons.coupon_code')
                ->where('memberships.status', 'completed')
                ->where('coupons.admin_id' , $logedinuser->id)
                ->selectRaw('SUM(
                    CASE
                        WHEN coupons.organization_stake IS NOT NULL THEN 
                            (packages.price * (coupons.organization_stake / 100))
                        ELSE
                            0
                    END
                ) as total_discount')
                ->first()->total_discount;

                $totalpurchasesubadmin = DB::table('memberships')
                ->leftJoin('packages', 'memberships.package_id', '=', 'packages.id')
                ->leftJoin('coupons', 'memberships.coupon', '=', 'coupons.coupon_code')
                ->where('memberships.status', 'completed')
                ->where('coupons.admin_id' , $logedinuser->id)
                ->sum('packages.price');
        }

        $membershipCompleted = Membership::where('status', 'completed')->count();
        $membershippending = Membership::where('status', 'pending')->count();

        $earnings = DB::table('memberships')
            ->leftJoin('packages', 'memberships.package_id', '=', 'packages.id')
            ->leftJoin('coupons', 'memberships.coupon', '=', 'coupons.coupon_code')
            ->where('memberships.status', 'completed')
            ->selectRaw('SUM(
        CASE
            WHEN coupons.discount_percentage IS NOT NULL THEN 
                packages.price - (packages.price * (coupons.discount_percentage / 100))
            WHEN coupons.organization_stake IS NOT NULL THEN
                packages.price - (packages.price * (coupons.organization_stake / 100))
            ELSE
                packages.price
        END
        ) as total_earnings') ->first()->total_earnings;

        $totalpurchase = DB::table('memberships')
            ->leftJoin('packages', 'memberships.package_id', '=', 'packages.id')
            ->leftJoin('coupons', 'memberships.coupon', '=', 'coupons.coupon_code')
            ->where('memberships.status', 'completed')
            ->sum('packages.price');

        return view('admin.dashboard',   [
            'users' => $users,
            'kahanis' => $kahanis,
            'reviews' => $reviews,
            'coupon' => $coupon,
            'totalpurchasesubadmin' => $totalpurchasesubadmin ?? 0,
            'totalpurchase' => $totalpurchase,
            'admincoupon' => $admincoupon ?? 0,
            'usermemberships' => $usermemberships ?? 0,
            'subadminearnings' => $subadminearnings ?? 0,
            'subadminprofits' =>  $organizationpercentage ?? 0,
            'earnings' => $earnings,
            'membershipCompleted' => $membershipCompleted,
            'membershipPending' => $membershippending,
        ]);
    }

    public function filterDashboard(Request $request)
    {
        $logedinuser = Auth::user();

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Base queries for filtering
        $usersQuery = User::query();
        $kahanisQuery = Kahani::query();
        $reviewsQuery = Review::query();
        $membershipsQuery = Membership::query();
        $couponQuery = Coupon::query();
        $admincouponQuery = Coupon::query();
        $userMembershipsQuery = Membership::query();
        $totalpurchasequery = DB::table('memberships')
        ->leftJoin('packages', 'memberships.package_id', '=', 'packages.id')
        ->leftJoin('coupons', 'memberships.coupon', '=', 'coupons.coupon_code')
        ->where('memberships.status', 'completed');
        

        // Earnings query with discount calculation
        $earningsQuery = DB::table('memberships')
            ->leftJoin('packages', 'memberships.package_id', '=', 'packages.id')
            ->leftJoin('coupons', 'memberships.coupon', '=', 'coupons.coupon_code')
            ->where('memberships.status', 'completed')
            ->selectRaw('SUM(
                CASE
                    WHEN coupons.discount_percentage IS NOT NULL THEN 
                        packages.price - (packages.price * (coupons.discount_percentage / 100))
                    WHEN coupons.organization_stake IS NOT NULL THEN
                        packages.price - (packages.price * (coupons.organization_stake / 100))
                    ELSE
                        packages.price
                END
            ) as total_earnings');

        // Apply admin check for coupons
        if ($logedinuser->type != 'superadmin') {
            $couponQuery->where('admin_id', $logedinuser->id);

            // Query memberships linked to coupons where admin_id matches
            $userMembershipsQuery->rightJoin('coupons', 'memberships.coupon', '=', 'coupons.coupon_code')
                ->where('coupons.admin_id', $logedinuser->id);
        } else {
            // Admin-specific coupon query
            $admincouponQuery->where('admin_id', $logedinuser->id);
        }

        // Apply date filter if dates are provided
        if ($startDate && $endDate) {
            $usersQuery->whereBetween('created_at', [$startDate, $endDate]);
            $kahanisQuery->whereBetween('created_at', [$startDate, $endDate]);
            $reviewsQuery->whereBetween('created_at', [$startDate, $endDate]);
            $couponQuery->whereBetween('created_at', [$startDate, $endDate]);
            $userMembershipsQuery->whereBetween('memberships.created_at', [$startDate, $endDate]);
            $admincouponQuery->whereBetween('created_at', [$startDate, $endDate]);
            $membershipsQuery->whereBetween('created_at', [$startDate, $endDate]);
            $earningsQuery->whereBetween('memberships.created_at', [$startDate, $endDate]);
            $totalpurchasequery->whereBetween('memberships.created_at', [$startDate, $endDate]);
        }

        // Sub-admin earnings query (filtered by admin_id and status completed)
        $subadminearningsQuery = DB::table('coupons')
            ->leftJoin('memberships', 'memberships.coupon', '=', 'coupons.coupon_code')
            ->join('packages', 'memberships.package_id', '=', 'packages.id')
            ->where('memberships.status', 'completed')
            ->where('coupons.admin_id', $logedinuser->id);

        $subadminprofitQuery = DB::table('coupons')
            ->leftJoin('memberships', 'memberships.coupon', '=', 'coupons.coupon_code')
            ->join('packages', 'memberships.package_id', '=', 'packages.id')
            ->where('memberships.status', 'completed')
            ->where('coupons.admin_id', $logedinuser->id);

        $totalpurchasesubadminquery = DB::table('memberships')
        ->leftJoin('packages', 'memberships.package_id', '=', 'packages.id')
        ->leftJoin('coupons', 'memberships.coupon', '=', 'coupons.coupon_code')
        ->where('memberships.status', 'completed')
        ->where('coupons.admin_id' , $logedinuser->id);
       

        // Apply date filter to sub-admin earnings if dates are provided
        if ($startDate && $endDate) {

            $subadminearningsQuery->whereBetween('memberships.created_at', [$startDate, $endDate]);
            $subadminprofitQuery->whereBetween('memberships.created_at', [$startDate, $endDate]);
            $totalpurchasesubadminquery->whereBetween('memberships.created_at', [$startDate, $endDate]);
        }

        // Calculate the sub-admin earnings

        $subadminearnings = $subadminearningsQuery->selectRaw('SUM(
                CASE
                    WHEN coupons.discount_percentage IS NOT NULL THEN 
                        packages.price - (packages.price * (coupons.discount_percentage / 100))
                    ELSE
                        packages.price
                END
            ) as total_earnings');

        $subadminprofit = $subadminprofitQuery ->selectRaw('SUM(
            CASE
                WHEN coupons.organization_stake IS NOT NULL THEN 
                    (packages.price * (coupons.organization_stake / 100))
                ELSE
                    0
            END
        ) as total_discount');

        // Count the filtered data
        $totalpurchasesubadminquery =  $totalpurchasesubadminquery->sum('packages.price');
        $totalpurchasequery = $totalpurchasequery->sum('packages.price');
        $users = $usersQuery->count();
        $kahanis = $kahanisQuery->count();
        $reviews = $reviewsQuery->count();
        $coupon = $couponQuery->count(); // Count after applying filters
        $userMemberships = $userMembershipsQuery->count(); // Count after applying filters
        $admincoupon = $admincouponQuery->count(); // Count after applying filters
        $membershipCompleted = $membershipsQuery->where('status', 'completed')->count();
        $membershipPending = $membershipsQuery->where('status', 'pending')->count();
        $earnings = $earningsQuery->first()->total_earnings; // Use the discounted earnings
        $subadminearnings = $subadminearnings->first()->total_earnings;
        $subadminprofits = $subadminprofit->first()->total_discount;

        // Return JSON response for AJAX
        return response()->json([
            'users' => $users,
            'kahanis' => $kahanis,
            'reviews' => $reviews,
            'coupon' => $coupon,
            'totalpurchase' => $totalpurchasequery,
            'totalpurchasesubadminquery' => $totalpurchasesubadminquery,
            'admincoupon' => $admincoupon ?? 0,
            'usermemberships' => $userMemberships ?? 0,
            'earnings' => $earnings ?? 0, // Include the discounted earnings
            'subadminearnings' => $subadminearnings ?? 0, 
            'subadminprofits' => $subadminprofits ?? 0, 
            'membershipCompleted' => $membershipCompleted,
            'membershipPending' => $membershipPending,
        ]);
    }






    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(AdminProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('admin.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/admin/login');
    }
}
