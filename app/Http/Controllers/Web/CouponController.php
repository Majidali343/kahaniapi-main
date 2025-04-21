<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function index(Request $request){
   
      $admins = Admin::where('type','!=','superadmin')->get();

      if(Auth::user()->type == 'superadmin'){
        $coupons = Coupon::leftJoin('memberships', function($join) {
            $join->on('coupons.coupon_code', '=', 'memberships.coupon')
                 ->where('memberships.status', '=', 'completed');
 
        })
        ->where('coupons.status', '=', 'active')
        ->select('coupons.id','coupons.admin_id' , 'coupons.coupon_code', 'coupons.discount_percentage', 'coupons.updated_at', 'coupons.organization_stake' ,DB::raw('COUNT(memberships.coupon) as usage_count'))
        ->groupBy('coupons.id', 'coupons.admin_id' ,'coupons.coupon_code', 'coupons.discount_percentage',  'coupons.organization_stake','coupons.updated_at')
          ->orderBy('coupons.updated_at', 'ASC')->get();
      }else{
        $coupons = Coupon::leftJoin('memberships', function($join) {
            $join->on('coupons.coupon_code', '=', 'memberships.coupon')
                 ->where('memberships.status', '=', 'completed');
 
        })
        ->where('coupons.status', '=', 'active')
        ->where('coupons.admin_id', '=', Auth::user()->id)
        ->select('coupons.id', 'coupons.admin_id' ,'coupons.coupon_code', 'coupons.discount_percentage', 'coupons.updated_at','coupons.organization_stake', DB::raw('COUNT(memberships.coupon) as usage_count'))
        ->groupBy('coupons.id', 'coupons.admin_id' , 'coupons.coupon_code', 'coupons.discount_percentage','coupons.organization_stake', 'coupons.updated_at')
         ->orderBy('coupons.updated_at', 'ASC')->get();
      }
         
        return view('Coupon.index',['Coupons'=> $coupons , 'admins' => $admins]);
    }

    public function couponorders(Request $request ,$coupon)
    {
        $orders = DB::table('memberships')
            ->Join('users', 'memberships.user_id', '=', 'users.id')
            ->Join('packages', 'memberships.package_id', '=', 'packages.id')
            ->select('memberships.*', 'packages.*', 'users.username', 'users.email')
            ->where('memberships.status', 'completed')
            ->where('memberships.coupon', $coupon)
              ->orderBy('memberships.updated_at', 'ASC')->get();

        if ($orders->isEmpty()) {
            return view('Coupon.coupondetail', ['message' => 'No Orders found']);
        }

        return view('Coupon.coupondetail', ['orders' => $orders]);
    }

    public function assignAdmin(Request $request)
    {
      
        // Validate incoming request
        $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'coupon_id' => 'required|exists:coupons,id'
        ]);
    
        // Fetch the coupon and update the admin assignment
        $coupon = Coupon::findOrFail($request->coupon_id);
        $coupon->admin_id = $request->admin_id; // Assuming you have an 'admin_id' column in your coupons table
        $coupon->save();
    
        // Return a success message
        return response()->json(['message' => 'Admin assigned successfully!']);
    }
    

    public function deletecoupon(Request $request, $id){
      $coupon = Coupon::where('id', $id)->first();
  
      // If the kahani is not found, return a response
      if (!$coupon) {
          return response()->json(['message' => 'Coupon not found', 'status' => false]);
      }
  
      $coupon->status = 'inactive';

      $coupon->save();

      return redirect()->route('coupons.get')->with('success', 'coupon deleted successfully.');

   }

   public function findCoupon(Request $request,$id){

    $coupon = Coupon::where('id',$id)->first();

    if (!$coupon) {
        return response()->json(['message' => 'coupon not found', 'status' => false]);
    }

    return view('Coupon.updatecoupon',['coupon'=> $coupon]);

  }



  public function couponstore(Request $request)
  {
    $validatedData = $request->validate([
        'coupon_code' => 'required|string|unique:coupons,coupon_code|max:255',
        'discount_percentage' => 'required|numeric|min:0|max:100',
        'organization_stake' => 'required|numeric|min:0|max:100',
    ]);

    $user = Auth::user();
  
    // Create a new coupon
    $coupon = new Coupon();
    $coupon->coupon_code = $validatedData['coupon_code'];
    $coupon->discount_percentage = $validatedData['discount_percentage'];
    $coupon->organization_stake = $validatedData['organization_stake'];
    $coupon->admin_id = $user->id;

    $coupon->save();

    // Redirect back with a success message
    return redirect()->route('coupons.get')->with('success', 'Coupon created successfully!');
  }


  public function newcoupon(){
     return view('Coupon.createcoupon');
  }
 
  public function updateCoupon(Request $request, $id)
    {

        $coupon = Coupon::where('id', $id)->first();
    
        if (!$coupon) {
            return response()->json(['message' => 'coupon not found', 'status' => false]);
        }
    
        $validatedData = $request->validate([
            'coupon_code' => 'sometimes|required|string|max:255',
            'discount_percentage' => 'sometimes|required|integer',
            'organization_stake' => 'sometimes|numeric|min:0|max:100',
        ]);
    
        if (isset($validatedData['coupon_code'])) {
            $coupon->coupon_code = $validatedData['coupon_code'];
        }
        if (isset($validatedData['discount_percentage'])) {
            $coupon->discount_percentage = $validatedData['discount_percentage'];
        }
    
        if (isset($validatedData['organization_stake'])) {
            $coupon->organization_stake = $validatedData['organization_stake'];
        }
   
        // Save the updated kahani
        $coupon->save();
    
        // Return a success response
        return redirect()->route('coupons.get')->with('success', 'coupon updated successfully.');
    }

}
