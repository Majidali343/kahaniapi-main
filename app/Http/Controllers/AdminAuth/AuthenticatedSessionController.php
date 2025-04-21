<?php

namespace App\Http\Controllers\AdminAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAuth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use app\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use PhpParser\Node\Stmt\Return_;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('admin.auth.login');
    }
    
    public function createadmin(Request $request){
 
        return view('admin.auth.register');
    }

    public function storeadmin(Request $request){

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Admin::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $Admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'subadmin',
        ]);

        return redirect()->back()->with(['success' => 'Subadmin Created Sucessfully']);
     
    }

    public function manageadmins(Request $request){
    
        $admins = Admin::where('type', '!=', 'superadmin')->get();

        if(!$admins){

         return view('admin.manageadmins')->with(['message' => 'No subadmins found']);
        }

        return view('admin.manageadmins', ['admins' =>$admins ]);

    }

    public function deleteadmin(Request $request, $id){
    
        $admin = Admin::where('id', $id)->first();
  
        // If the kahani is not found, return a response
        if (!$admin) {
            return redirect()->back()->with(['message' => 'Package not found', 'status' => false]);
        }
    
        $admin->delete();
  
        return redirect()->route('admins.manage')->with('success', 'Admin deleted successfully.');
  

    }

    
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::ADMIN_DASHBOARD);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
