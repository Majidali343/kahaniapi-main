<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserOrdersController extends Controller
{
    public function allorders(Request $request)
    {

        $memberships = Membership::where('status', '!=', 'expired')->get();

        foreach ($memberships as $membership) {
            if($membership->membershipvalidity == "lifetime"){
               
            }else{
                if (Carbon::now()->greaterThan($membership->membershipvalidity)) {
                    // Update status to 'expired'
                    $membership->status = 'expired';
                    $membership->save();
                }
            }
           
        }

       $orders = DB::table('memberships')
            ->Join('users', 'memberships.user_id', '=', 'users.id')
            ->Join('packages', 'memberships.package_id', '=', 'packages.id')
            ->select('memberships.*', 'packages.name', 'packages.Permissions','packages.price','users.username', 'users.email')
            ->where('memberships.status', 'completed')
            ->orwhere('memberships.status', 'inactive') // Select specific columns and alias the status 
           ->orderBy('memberships.purchase_date','DESC')
            ->get();


        if ($orders->isEmpty()) {
            return view('memberships.index', ['message' => 'No memberships found']);
        }

        return view('memberships.index', ['orders' => $orders]);
    }

    public function allpayemnts(Request $request)
    {


       $orders = DB::table('coupons')
            ->Join('memberships', 'coupons.coupon_code', '=', 'memberships.coupon')
            ->Join('users', 'memberships.user_id', '=', 'users.id')
            ->Join('packages', 'memberships.package_id', '=', 'packages.id')
            ->select('memberships.*','coupons.discount_percentage','coupons.organization_stake','packages.name', 'packages.Permissions','packages.price','users.username', 'users.email')
            ->where('memberships.status', 'completed')
            ->orwhere('memberships.status', 'inactive') // Select specific columns and alias the status 
           ->orderBy('memberships.updated_at','DESC')
            ->get();


        if ($orders->isEmpty()) {
            return view('memberships.index', ['message' => 'No memberships found']);
        }

        return view('memberships.adminpayments', ['orders' => $orders]);
    }

    public function allorgpayments(Request $request)
    {

       $orders = DB::table('coupons')
            ->Join('memberships', 'coupons.coupon_code', '=', 'memberships.coupon')
            ->Join('users', 'memberships.user_id', '=', 'users.id')
            ->Join('packages', 'memberships.package_id', '=', 'packages.id')
            ->select('memberships.*','coupons.discount_percentage','coupons.organization_stake','packages.name', 'packages.Permissions','packages.price','users.username', 'users.email')
            ->where('memberships.status', 'completed')
            ->where('coupons.admin_id', Auth::user()->id)
            ->orwhere('memberships.status', 'inactive') // Select specific columns and alias the status 
           ->orderBy('memberships.updated_at','DESC')
            ->get();


        if ($orders->isEmpty()) {
            return view('memberships.index', ['message' => 'No memberships found']);
        }

        return view('memberships.organizationpayments', ['orders' => $orders]);
    }



    public function  manualpayments(Request $request)
    {

        $manualpayments = DB::table('manual_payments')
            ->leftJoin('users', 'manual_payments.user_id', '=', 'users.id')
            ->Join('packages', 'manual_payments.package_id', '=', 'packages.id')
            ->select('manual_payments.*', 'packages.name', 'users.username', 'users.email')
            ->orderBy('manual_payments.created_at', 'DESC')->get();
           

      

        if ($manualpayments->isEmpty()) {
            return view('memberships.manualpayamnt', ['message' => 'No Manual membership found']);
        }

        return view('memberships.manualpayamnt', ['manualpayments' => $manualpayments]);
    }


    public function allorderspending(Request $request)
    {
        $orders = DB::table('memberships')
            ->Join('users', 'memberships.user_id', '=', 'users.id')
            ->Join('packages', 'memberships.package_id', '=', 'packages.id')
             ->select('memberships.*', 'packages.name', 'packages.Permissions','packages.price', 'users.username', 'users.email')
             ->orderBy('memberships.purchase_date','DESC')
             ->get();


        if ($orders->isEmpty()) {
            return view('memberships.orders', ['message' => 'No Orders found']);
        }

        return view('memberships.orders', ['orders' => $orders]);
    }

  

   public function updateMembershipStatus(Request $request, $id)
 {
    // Validate the request to ensure 'status' is provided
    $request->validate([
        'status' => 'required|string|max:255', // You can adjust the validation rules as needed
    ]);

    // Fetch the membership record
    $membership = DB::table('memberships')
        ->where('user_id', $id)
        ->first();
        
    if ($membership) {
        // Perform the update query
        DB::table('memberships')
            ->where('user_id', $id)
            ->update(['status' => $request->input('status')]);

        return response()->json(['message' => 'Membership status updated successfully.']);
    } else {
        return response()->json(['message' => 'Membership not found.'], 404);
    }
 }


   public function updatepaymentStatus(Request $request, $id)
 {
    // Validate the request to ensure 'status' is provided
    $request->validate([
        'status' => 'required|string|max:255', // You can adjust the validation rules as needed
    ]);

    // Fetch the membership record
    $membership = DB::table('memberships')
        ->where('user_id', $id)
        ->first();
        
    if ($membership) {
        // Perform the update query
        DB::table('memberships')
            ->where('user_id', $id)
            ->update(['admin_paid' => $request->input('status') ,'updated_at' =>now()]);

        return response()->json(['message' => 'Payment status updated successfully.']);
    } else {
        return response()->json(['message' => 'Payment not found.'], 404);
    }
 }

   public function updateorgpaymentStatus(Request $request, $id)
 {
    // Validate the request to ensure 'status' is provided
    $request->validate([
        'status' => 'required|string|max:255', // You can adjust the validation rules as needed
    ]);

    // Fetch the membership record
    $membership = DB::table('memberships')
        ->where('user_id', $id)
        ->first();
        
    if ($membership) {
        // Perform the update query
        DB::table('memberships')
            ->where('user_id', $id)
            ->update(['organization_request' => $request->input('status') ,'updated_at' =>now()]);

        return response()->json(['message' => 'Request status updated successfully.']);
    } else {
        return response()->json(['message' => 'Payment not found.'], 404);
    }
 }



    public function updateManualpaymentStatus(Request $request, $id)
    {
        // Validate the request to ensure 'status' is provided
        $request->validate([
            'status' => 'required|string|max:255', // You can adjust the validation rules as needed
        ]);


        $packageuser = DB::table('manual_payments')
            ->where('user_id', $id)
            ->first();
           
        if(!$packageuser){
            return response()->json(['message'=> 'No payment found',500]);
        } 

        $package = Package::find($packageuser->package_id);

        if (!$package) {
            return response()->json(['message'=> 'package not found',500]);
        }

        // Retrieve values from the request
        $customerId = $id;
       
        $orderId = Str::uuid()->toString();

        $existingMembership = Membership::where('user_id', $customerId)
            ->orderBy('purchase_date', 'desc')
            ->first();

        // Check for existing membership conditions
        if ($existingMembership) {
            if ($existingMembership->membershipvalidity === 'lifetime'  && $existingMembership->status === 'completed') {
                return response()->json(['message'=> 'This user already have a lifetime membership.']);
            } elseif ($existingMembership->status === 'completed' && Carbon::parse($existingMembership->membershipvalidity)->isFuture()) {

                  $updateData = [
                    'order_id' => $orderId,
                    'package_id' => $packageuser->package_id,
                    'status' => 'completed',
                ];
                
                
                $existingMembership->update($updateData);

                DB::table('manual_payments')
                ->where('user_id', $id)
                ->update(['status' => $request->input('status')]);

                return response()->json(['message'=> 'Your Membership is Updated.']);
            }
        }

        $datevalidity = Carbon::now();
        $orderId = Str::uuid()->toString();
      
        // Determine the new membership validity
        if ($package->validity == "lifetime") {
            $membershipvalidity = "lifetime";
        } elseif ($package->validity == "yearly") {
            $membershipvalidity = $datevalidity->copy()->addYears(1);
        } elseif ($package->validity == "monthly") {
            $membershipvalidity = $datevalidity->copy()->addMonths(1);
        } elseif ($package->validity == "quarter") {
            $membershipvalidity = $datevalidity->copy()->addMonths(3);
        }

        if ($existingMembership) {
            // Update existing membership if it's expired or incomplete
            $existingMembership->update([
                'order_id' => $orderId,
                'package_id' => $package->id,
                'membershipvalidity' => $membershipvalidity,
                'purchase_date' => Carbon::now(),
                'status' => 'completed',
            ]);

        
            DB::table('manual_payments')
            ->where('user_id', $id)
            ->update(['status' => $request->input('status')]);

            return response()->json(['message'=> 'Old membership of this User is Updated.']);
        } else {
            // Create new membership
            $membership = Membership::create([
                'order_id' => $orderId,
                'user_id' => $customerId,
                'package_id' => $package->id,
                'membershipvalidity' => $membershipvalidity,
                'purchase_date' => Carbon::now(),
                'status' => 'completed',
            ]);

            if (!$membership) {
                
                return response()->json(['message'=> 'Membership creation failed',500]);
            }

            DB::table('manual_payments')
                ->where('user_id', $id)
                ->update(['status' => $request->input('status')]);

            return response()->json(['message'=> 'Membership created sucessfully']);
        }
    }

    public function  manualstoreget(Request $request)
    {

    $user = User::orderBy('phone', 'asc')->get();

        return view('memberships.manualpayamntadd', ['users' => $user]);
    }


    public function getsuitablepackage(Request $request)
    {
        $packages = Package::all();

        $validatedData = $request->validate([
            'user_id' => 'required|numeric',
        ]);

        $usermembership = Membership::where('user_id', $validatedData['user_id'] )
        ->where('status', 'completed')
        ->first();

        if ($usermembership) {

            $userpackage = Package::where('id', $usermembership->package_id)->first();

            $packages =  Package::where('validity', $userpackage->validity)
                ->where('price', '>', $userpackage->price)
                ->wherenot('id', $usermembership->package_id)
                ->get();

            $amountToSubtract =  $userpackage->price;

            $packages->transform(function ($package) use ($amountToSubtract) {
                $package->price -= $amountToSubtract;
                return $package;
            });

            if (!$packages->isEmpty()) {
                return response()->json($packages, 200);
            }
           
                return response()->json($packages, 200);  
        }

        if ($packages->isEmpty()) {
            return response()->json(['message' => "No packages found"], 401);
        }

        return response()->json($packages, 200);
    }

    public function createOrdermanual(Request $request)
    {

        $validatedData = $request->validate([
            'user_id' => 'required|numeric',
            'package_id' => 'required|numeric',
        ]);

        // Retrieve values from the request
        $customerId = $validatedData['user_id'];

        $orderId = Str::uuid()->toString();

        $package = Package::find($validatedData['package_id']);

        if (!$package) {
            return redirect()->back()->with('message', 'Package not found');
        }

        $existingMembership = Membership::where('user_id', $customerId)
            ->orderBy('purchase_date', 'desc')
            ->first();

        // Check for existing membership conditions
        if ($existingMembership) {
            if ($existingMembership->membershipvalidity === 'lifetime'  && $existingMembership->status === 'completed') {
                return redirect()->back()->with('message', 'You already have a lifetime membership.');
            } elseif ($existingMembership->status === 'completed' && Carbon::parse($existingMembership->membershipvalidity)->isFuture()) {

                $updateData = [
                    'order_id' => $orderId,
                    'package_id' => $validatedData['package_id'],
                    'status' => 'completed',
                ];
                             
                $existingMembership->update($updateData);

                return redirect()->back()->with('success', 'Your Membership is Updated.');
            }
        }

        $datevalidity = Carbon::now();
        $orderId = Str::uuid()->toString();

        // Determine the new membership validity
        if ($package->validity == "lifetime") {
            $membershipvalidity = "lifetime";
        } elseif ($package->validity == "yearly") {
            $membershipvalidity = $datevalidity->copy()->addYears(1);
        } elseif ($package->validity == "monthly") {
            $membershipvalidity = $datevalidity->copy()->addMonths(1);
        }elseif ($package->validity == "quarter") {
            $membershipvalidity = $datevalidity->copy()->addMonths(3);
        }

        if ($existingMembership) {
            // Update existing membership if it's expired or incomplete
            $existingMembership->update([
                'order_id' => $orderId,
                'package_id' => $validatedData['package_id'],
                'membershipvalidity' => $membershipvalidity,
                'purchase_date' => Carbon::now(),
                'status' => 'completed',
            ]);

            return redirect()->back()->with('success', 'Old membership of this User is Updated');
        } else {
            // Create new membership
            $membership = Membership::create([
                'order_id' => $orderId,
                'user_id' => $customerId,
                'package_id' => $validatedData['package_id'],
                'membershipvalidity' => $membershipvalidity,
                'purchase_date' => Carbon::now(),
                'status' => 'completed',
            ]);

            if (!$membership) {
                return redirect()->back()->with('message', 'Membership creation failed');
            }

            return redirect()->back()->with('success', 'Membership created sucessfully');
        }
    }
}
