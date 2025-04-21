<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\BsecureService;
use Illuminate\Support\Facades\Auth;
use App\Models\Membership;
use App\Models\Package;
use App\Models\Coupon;
use App\Models\ManualPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;


class OrderController extends Controller
{
    protected $bsecureService;

    public function __construct(BsecureService $bsecureService)
    {
        $this->bsecureService = $bsecureService;
    }

    function getAccessToken()
    {
        // URL of the bSecure API
        $url = 'https://api.bsecure.pk/v1/oauth/token';

        // Data to be sent in the request
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => 'ba238cfa-1d9a-4a5d-bda4-78bf1f5befc4:ST-008049908',
            'client_secret' => 'eO0gbH/o0k+tt4OfraIYozKHlIqK5CgoQwwiT3JXfUU='
        ];

        // Make the POST request to the API
        $response = Http::post($url, $data);

        // Check if the request was successful
        if ($response->successful()) {
            // Decode the JSON response to get the access token
            $result = $response->json();
            $accessToken = $result['body']['access_token'];

            // Return or store the token as needed
            return $accessToken;
        } else {
            // Handle the error if the request fails
            $error = $response->body();
            return 'Error: ' . $error;
        }
    }

    public function createOrder(Request $request)
    {
        $user = Auth::user();
        $token = $this->getAccessToken();

        $url = 'https://api.bsecure.pk/v1/order/create';

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validatedData = $request->validate([
            'package_id' => 'required|numeric|exists:packages,id',
            'price' => 'required|numeric',

        ]);


        $package = Package::find($validatedData['package_id']);

        if (!$package) {
            return response()->json(['message' => "Package not found"], 404);
        }

        $existingMembership = Membership::where('user_id', $user->id)
            ->orderBy('purchase_date', 'desc')
            ->first();

        $datevalidity = Carbon::now();
        $orderId = Str::uuid()->toString();

        $data = [
            'lang' => 'en',
            'order_id' => $orderId,
            'total_amount' => $validatedData['price'],
            'sub_total_amount' => $validatedData['price'],
            'discount_amount' => 0,
            'products' => [
                [
                    'id' => $orderId,
                    'name' => $package->name,
                    'variant_id' => $package->id,
                    'quantity' => 1,
                    'price' => $package->price,
                    'sale_price' => $package->price,
                    'image' => asset('/storage') . '/' . $package->image,
                    'show_discount_tag' =>  $request->coupon_code ?? null,
                    'product_sku' =>  $request->coupon_code ?? null,

                ]
            ],
            'customer' => [
                'name' => $user->name,
                'email' => $user->email,
                'country_code' => '92',

            ]
        ];

        $response = Http::withToken($token)->post($url, $data);


        // Check for existing membership conditions
        if ($existingMembership) {
            if ($existingMembership->membershipvalidity === 'lifetime'  && $existingMembership->status === 'completed') {
                return response()->json(['message' => 'You already have a lifetime membership.'], 403);
            } elseif ($existingMembership->status === 'completed' && Carbon::parse($existingMembership->membershipvalidity)->isFuture()) {

                $updateData = [
                    'order_id' => $orderId,
                ];

                // Check if the coupon_code is present in the request
                if ($request->has('coupon_code')) {
                    $updateData['coupon'] = $request->coupon_code;
                }

                $existingMembership->update($updateData);


                if ($response->successful()) {
                    if (!empty($response['body']['checkout_url'])) {
                        return response()->json(['checkout_url' => $response['body']['checkout_url']]);
                    } else {
                        return response()->json(['error' => 'Order creation failed', 'details' => $response], 500);
                    }
                } else {
                    // Handle error
                    return 'Error: ' . $response->body();
                }
            }
        }

        // Determine the new membership validity
        if ($package->validity == "lifetime") {
            $membershipvalidity = "lifetime";
        } elseif ($package->validity == "yearly") {
            $membershipvalidity = $datevalidity->copy()->addYears(1);
        } elseif ($package->validity == "quarter") {
            $membershipvalidity = $datevalidity->copy()->addMonths(3);
        } elseif ($package->validity == "monthly") {
            $membershipvalidity = $datevalidity->copy()->addMonths(1);
        }


        if ($existingMembership) {
            // Update existing membership if it's expired or incomplete
            $updateData = [
                'order_id' => $orderId,
                'package_id' => $validatedData['package_id'],
                'membershipvalidity' => $membershipvalidity,
                'purchase_date' => Carbon::now(),
                'status' => 'pending',
            ];

            // Check if the coupon_code is present in the request
            if ($request->has('coupon_code')) {
                $updateData['coupon'] = $request->coupon_code;
            }

            $existingMembership->update($updateData);
        } else {
            // Create new membership
            $membershipData = [
                'order_id' => $orderId,
                'user_id' => $user->id,
                'package_id' => $validatedData['package_id'],
                'membershipvalidity' => $membershipvalidity,
                'purchase_date' => Carbon::now(),
                'status' => 'pending',
            ];

            // Check if the coupon_code is present in the request
            if ($request->has('coupon_code')) {
                $membershipData['coupon'] = $request->coupon_code;
            }

            // Create the membership record with the data array
            $membership = Membership::create($membershipData);

            if (!$membership) {
                return response()->json(['error' => 'Membership creation failed'], 500);
            }
        }

        if ($response->successful()) {
            if (!empty($response['body']['checkout_url'])) {
                return response()->json(['checkout_url' => $response['body']['checkout_url']]);
            } else {
                return response()->json(['error' => 'Order creation failed', 'details' => $response], 500);
            }
        } else {
            // Handle error
            return 'Error: ' . $response->body();
        }
    }



     public function updateOrderStatus(Request $request)
    {
    
        $orderId = $request->merchant_order_id;
        $membership = Membership::where('order_id', $orderId)->first();

        // Check if membership exists
        if ($membership) {
            // Update membership based on payment status
            if ( $request->payment_status == 1) {
                $membership->status = 'completed';
                // Safely access variant_id from items array
                $membership->package_id = $request->items[0]['variant_id'] ;
            } else {
                $membership->status = 'cancelled';
            }

            // Save updated membership
            $membership->save();
        } else {
            return response()->json(['error' => 'Membership not found'], 404);
        }

        return response()->json(['message' => 'Order status updated successfully'], 200);
    }



    public function getmembership(Request $request)
    {

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $membership = DB::table('memberships')
            ->join('packages', 'memberships.package_id', '=', 'packages.id')
            ->select('memberships.*', 'packages.name','packages.price')
            ->where('memberships.user_id', $user->id)
            //  ->where('memberships.status', 'completed')
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'you does not have any membership'], 401);
        }

        return response()->json(['data' => $membership], 200);
    }


    public function storeManualPayment(Request $request)
    {
        $request->validate([
            'package_id' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'payment_image' => 'required|image|mimes:jpeg,png,jpg',
            'paidamount' => 'required|numeric',
        ]);


        $user = Auth::user();
        $existingMembership = Membership::where('user_id', $user->id)
            ->orderBy('purchase_date', 'desc')
            ->first();

        if ($existingMembership) {
            if ($existingMembership->membershipvalidity === 'lifetime'  && $existingMembership->status === 'completed') {
                return response()->json(['message' => 'You already have a lifetime membership.'], 403);
            } elseif ($existingMembership->status === 'completed' && Carbon::parse($existingMembership->membershipvalidity)->isFuture()) {




                $manualPayment = ManualPayment::where('user_id',  $user->id)->first();

                if ($manualPayment) {
                    // Delete the previous image if a new image is uploaded
                    if ($request->hasFile('payment_image')) {
                        if (Storage::disk('public')->exists($manualPayment->payment_image)) {
                            Storage::disk('public')->delete($manualPayment->payment_image);
                        }
                        $imagePath = $request->file('payment_image')->store('manual_payments', 'public');
                    } else {
                        $imagePath = $manualPayment->payment_image;
                    }

                    // Update the existing record
                    $manualPayment->update([
                        'user_id' => $user->id,
                        'package_id' => $request->package_id,
                        'bank_name' => $request->bank_name,
                        'payment_image' => $imagePath,
                        'paidamount' => $request->paidamount,
                        'type' => 'Update Package',
                        'status' => 'pending',
                    ]);
                } else {
                    // Handle image upload for new record
                    $imagePath = $request->file('payment_image')->store('manual_payments', 'public');

                    // Create a new manual payment record
                    $manualPayment = ManualPayment::create([
                        'user_id' => $user->id,
                        'package_id' => $request->package_id,
                        'bank_name' => $request->bank_name,
                        'payment_image' => $imagePath,
                        'paidamount' => $request->paidamount,
                        'type' => 'Update Package',
                        'status' => 'pending',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Manual payment has been recorded successfully.',
                    'data' => $manualPayment,
                ]);
            }
        }

        // Check if the user already has a manual payment record
        $manualPayment = ManualPayment::where('user_id',  $user->id)->first();

        if ($manualPayment) {
            // Delete the previous image if a new image is uploaded
            if ($request->hasFile('payment_image')) {
                if (Storage::disk('public')->exists($manualPayment->payment_image)) {
                    Storage::disk('public')->delete($manualPayment->payment_image);
                }
                $imagePath = $request->file('payment_image')->store('manual_payments', 'public');
            } else {
                $imagePath = $manualPayment->payment_image;
            }

            // Update the existing record
            $manualPayment->update([
                'status' => 'pending',
                'user_id' => $user->id,
                'package_id' => $request->package_id,
                'bank_name' => $request->bank_name,
                'payment_image' => $imagePath,
                'paidamount' => $request->paidamount,
                'type' => 'New Package',

            ]);
        } else {
            // Handle image upload for new record
            $imagePath = $request->file('payment_image')->store('manual_payments', 'public');

            // Create a new manual payment record
            $manualPayment = ManualPayment::create([
                'user_id' => $user->id,
                'package_id' => $request->package_id,
                'bank_name' => $request->bank_name,
                'payment_image' => $imagePath,
                'paidamount' => $request->paidamount,
                'type' => 'New Package',
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Manual payment has been recorded successfully.',
            'data' => $manualPayment,
        ]);
    }




    public function getDiscount(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'coupon_code' => 'required|string',
        ]);

        // Find the coupon by code
        $coupon = Coupon::where('coupon_code', $validatedData['coupon_code'])
            ->where('status', 'active')
            ->first();

        // Check if the coupon exists
        if ($coupon) {
            return response()->json([
                'success' => true,
                'discount_percentage' => $coupon->discount_percentage ,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found',
            ], 404);
        }
    }
}
