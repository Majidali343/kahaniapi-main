<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Hautelook\Phpass\PasswordHash;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminUsers extends Controller
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



    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(new UsersExport($startDate, $endDate), 'users.xlsx');
    }

    public function allusers(Request $request)
    {
        // Set online status for users who haven't logged in within the last hour
        User::where('last_login_at', '<', now()->subHour(1))
            ->update(['is_online' => false]);

        // Get the date range from the request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Build the query
        $query = User::query();

        // Apply date filters if provided
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Fetch the users based on the filters
        $users = $query->orderBy('created_at', 'ASC')->get();

        // Check if the users collection is empty
        if ($users->isEmpty()) {
            return view('users', ['message' => 'No user found']);
        }

        // Pass the users collection to the view
        return view('users', ['users' => $users]);
    }



    public function resetpassword($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }

        return view('userchangepassword', compact('user'));
    }

    public function resetpasswordadmin($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return redirect()->back()->with('error', 'Admin not found');
        }

        return view('adminchangepassword', compact('admin'));
    }
    
    public function updatePasswordadmin(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8', // Minimum length of 8 characters
                'confirmed', // Ensures password and confirmation match
            ],
        ], [
            'password.min' => 'The password must be at least 8 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

       
        $Admin = Admin::find($id);

        if (!$Admin) {
            return redirect()->back()->with('error', 'Admin not found');
        }

        $Admin->password = Hash::make($validated['password']);

        $Admin->save();

        return redirect()->route('resetadmin.password', $id)->with('success', 'Password updated successfully');
    }


    public function updateUsersStatus(Request $request, $id)
    {
        // Validate the request to ensure 'status' is provided
        $request->validate([
            'status' => 'required|string|max:255',
        ]);

        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Update the user's status
        $user->status = $request->input('status');
        $user->save();

        // Revoke all tokens if the status is updated
        if ($user->wasChanged('status')) {
            $user->tokens()->delete();
            return response()->json(['message' => 'User status updated successfully.']);
        } else {
            return response()->json(['message' => 'Failed to update user status.'], 500);
        }
    }

    public function updatePassword(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8', // Minimum length of 8 characters
                'confirmed', // Ensures password and confirmation match
            ],
        ], [
            'password.min' => 'The password must be at least 8 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);


        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }


        $hasher = new AdminUsers();

        // Update the password
        $user->password = $hasher->make($validated['password']);

        $user->save();

        $user->tokens()->delete();

        return redirect()->route('resetuser.password', $id)->with('success', 'Password updated successfully');
    }
}
