<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Membership;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use function PHPUnit\Framework\isEmpty;

class PackagesController extends Controller
{
    public function index(Request $request)
    {
        $packages = Package::where('status','active')->get();

        $user = Auth::user();

        $checkmembership = Membership::where('user_id', $user->id)->first();

        if($checkmembership){
        
        if($checkmembership->membershipvalidity != "lifetime"){
            if (Carbon::now()->greaterThan($checkmembership->membershipvalidity)) {
                // Update status to 'expired'
                $checkmembership->status = 'expired';
                $checkmembership->save();
            }
        }

        }

        $usermembership = Membership::where('user_id', $user->id)
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
            
        }

        if ($packages->isEmpty()) {
            return response()->json(['message' => "No packages found"], 401);
        }

        return response()->json($packages, 200);
    }

     public function simplepackages(Request $request)
    {
        $packages = Package::where('status','active')->get();

        if ($packages->isEmpty()) {
            return response()->json(['message' => "No packages found"], 401);
        }

        return response()->json($packages, 200);
    }


    public function find($id)
    {
        $package = Package::find($id);
        
        if (!$package) {
            return response()->json(['message' => "Package not found"], 404);
        }

        $user = Auth::user();

        if($user){
            $usermembership = Membership::where('user_id', $user->id)
            ->where('status', 'completed')
            ->first();
      
            if ($usermembership) {
    
                $userpackage = Package::where('id', $usermembership->package_id)->first();
    
                $amountToSubtract =  $userpackage->price;
    
                $package->price = $package->price -  $amountToSubtract;
                
            }
        }else{
            $package = Package::where('id', $id )->first();
        }

        // Return the package data
        return response()->json($package, 200);
    }
    
}
