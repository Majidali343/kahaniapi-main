<?php

namespace App\Http\Controllers;

use App\Models\User;
use Session;
use DB;
use App\Models\Membership;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Hautelook\Phpass\PasswordHash;


class AuthController extends Controller
{
    protected $hasher;

    public function __construct()
    {
        // Initialize WordPress password hashing class with 8 iterations
        $this->hasher = new PasswordHash(8, true);
    }

    public function check($password, $hashedPassword)
    {
        return $this->hasher->CheckPassword($password, $hashedPassword);
    }

    public function make($password)
    {
        return $this->hasher->HashPassword($password);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'username' => 'required|string|max:255|unique:users',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $hasher = new AuthController();

        $user = User::create([
            'email' => $request->email,
            'username' => $request->username,
            'password' => $hasher->make($request->password),
            'phone' => $request->phone,
        ]);

        // Compose the email content
        $messageContent = "Hi " . $user->username . ",\n\n";
        $messageContent .= "Welcome to Kahanify ! We're thrilled to have you as a part of our system.\n\n";
        $messageContent .= "Here are some things you can do to get started:\n";
        $messageContent .= "1. Explore our Kahanis.\n";
        $messageContent .= "2. Your childen can enjoy reading kahanis.\n";
        $messageContent .= "3. Enjoy listening kahanis.\n\n";
        $messageContent .= "Thank you for joining us!\n";
        $messageContent .= "Best Regards,\n";
        $messageContent .= "The Team";

        // Send the email
        Mail::raw($messageContent, function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Welcome to Our Kahanify');
        });


        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required', // Ensure this field is required
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $hasher = new AuthController();

        if (!$hasher->check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 403);
        }

        // Update the password
        $user->password = $hasher->make($request->new_password);
        $user->update();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }


    // Forgot Password: Send Reset Link Email
    public function sendResetLinkEmail(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Reset link sent to your email.']);
        } else {
            return response()->json(['message' => 'Unable to send reset link.'], 500);
        }
    }

    public function sendOtpEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $otp = random_int(100000, 999999);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->otp = $otp;
            $user->save();

            $messageContent = "Hi " . $user->username . ",\n\n";
            $messageContent .= "Welcome to Kahanify ! This is your one time password .\n\n";
            $messageContent .= "Please use this otp to rest your password:   $otp  \n\n";
            $messageContent .= "Best Regards,\n";
            $messageContent .= "The Team";

            // Send the email
            Mail::raw($messageContent, function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Forgot Password Otp');
            });

            return response()->json(['message' => 'Otp sended sucessfully'], 200);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function changeMobilePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8',
            'SecretCode' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userid =   $request->SecretCode / 2398 ;

        $user = User::where('id', $userid)->first();
       
        if(!$user){
            return response()->json(['message' => 'Wrong Secret Key'], 404);
        }

        $hasher = new AuthController();

        // Update the password
        $user->password = $hasher->make($request->new_password);
        $user->update();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }


    public function verifyMobileOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->otp == $request->otp) {

                $secreCode = 2398 * $user->id;

                return response()->json(['message' => 'Otp Verified Use SecretKey To Change Password', 'SecretKey' => $secreCode], 200);

            }

            return response()->json(['message' => 'Wrong Otp Please try again'], 404);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    // Reset Password
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $hasher = new AuthController();
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $hasher = new AuthController();
                $user->forceFill([
                    'password' =>   $hasher->make($password)
                ])->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset.']);
        } else {
            return response()->json(['message' => 'Unable to reset password.'], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users',
        ]);

        if ($request->hasFile('profileimage')) {
            // Delete the old image file if it exists
            if ($user->profileimage) {
                Storage::disk('public')->delete($user->profileimage);
            }
            $imagePath = $request->file('profileimage')->store('profile', 'public');
            $user->profileimage = $imagePath;
        }

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        // Update user details
        $user->name = $request->name ?? $user->name;
        $user->username = $request->username ?? $user->username;
        $user->email = $request->email ?? $user->email;
        $user->phone = $request->phone ?? $user->phone;


        $user->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
    }

    public function userShow(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $membership = Membership::where('user_id', $user->id)
            ->join('packages', 'memberships.package_id', '=', 'packages.id')
            ->select('memberships.*', 'packages.Permissions')
            ->first();

        $ismember = false;

        if (!$membership) {
            $ismember = false;
        } else {



            if ($membership->membershipvalidity == "lifetime" && $membership->status == 'completed') {
                $ismember = true;
            }

            if ($membership->membershipvalidity > Carbon::now() && $membership->status == 'completed') {
                $ismember = true;
            }

            // $ipAddress = $request->ip();

            // if ( $membership->ipaddress != $ipAddress  && $membership->status == 'completed' ) {
            //     $ismember = false;
            // }

        }

        if ($ismember == true) {
            return response()->json(['message' => 'User profile', 'user' => $user, 'membership' => $ismember, 'permissions' => $membership->Permissions], 200);
        }


        return response()->json(['message' => 'User profile', 'user' => $user, 'membership' => $ismember], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username_or_email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->username_or_email)
            ->orWhere('username', $request->username_or_email)
            ->orWhere('phone', $request->username_or_email)
            ->first();

        $hasher = new AuthController();


        if (!$user || !$hasher->check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Revoke all tokens previously issued to the user
        $user->tokens()->delete();

        if ($user->status == 'false') {
            return response()->json(['message' => 'Your account is restricted'], 401);
        }

        $user->update([
            'last_login_at' => now(),
            'is_online' => true
        ]);

        // Generate a new token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Log the user in (optional, depending on your application flow)
        Auth::login($user);

        return response()->json(['message' => 'Login successful', 'token' => $token, 'user' => $user], 200);
    }

    public function logout(Request $request)
    {
        // Get the currently authenticated user
        $user = $request->user();

        if ($user) {

            $user->currentAccessToken()->delete();

            $user->update(['is_online' => false]);

            return response()->json(['message' => 'Logout successful'], 200);
        }

        return response()->json(['message' => 'User is not logged in'], 401);
    }
}
