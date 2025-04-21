<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\Web\AdminUsers;
use App\Http\Controllers\Web\UserOrdersController;
use App\Http\Controllers\Web\StoryController;
use App\Http\Controllers\Web\CouponController;
use App\Http\Controllers\Web\PackageController;
use App\Http\Controllers\Web\YoutubeVideoController;
use App\Http\Controllers\Web\PartnerController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [AuthenticatedSessionController::class, 'create']);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/admin/dashboard', [AdminProfileController::class , 'dashboard'])->middleware(['auth:admin', 'verified'])->name('admin.dashboard');
Route::get('/dashboard/filter', [AdminProfileController::class, 'filterDashboard'])->middleware(['auth:admin', 'verified'])->name('dashboard.filter');


Route::middleware(['auth:admin','Adminpermissions'])->group(function () {
   
    Route::post('register', [RegisteredUserController::class, 'store'])->name('registeruser');

    Route::get('newcustomer', [RegisteredUserController::class, 'create'])
    ->name('newcustomer');

    Route::get('/users/export', [AdminUsers::class, 'export'])->name('users.export');
    Route::get('/allusers', [AdminUsers::class,'allusers'])->name('allusers');

    Route::get('/changeuserpassword/{id}', [AdminUsers::class, 'resetpassword'])->name('resetuser.password');
    Route::put('/user/{id}/update-password', [AdminUsers::class, 'updatePassword'])->name('password.update');

    Route::post('/users/{id}/status', [AdminUsers::class, 'updateusersStatus'])->name('users.updateStatus');
    // Route to handle the password update
    
    Route::get('/allorders', [UserOrdersController::class,'allorders'])->name('allorders');
    Route::get('/allpayments', [UserOrdersController::class,'allpayemnts'])->name('allpayemnts');
    Route::get('/allordersplaced', [UserOrdersController::class,'allorderspending'])->name('allorderspending');
    Route::post('/memberships/{id}/status', [UserOrdersController::class, 'updateMembershipStatus'])->name('memberships.updateStatus');
    Route::post('/payment/{id}/status', [UserOrdersController::class, 'updatepaymentStatus'])->name('payment.updateStatus');
    
    Route::get('/manualpayments', [UserOrdersController::class, 'manualpayments'])->name('manualpayments.get');
    Route::get('/manualpayment', [UserOrdersController::class, 'manualstoreget'])->name('manualpayment.get');
    Route::post('/manualpaymentstore', [UserOrdersController::class, 'createOrdermanual'])->name('manualpayment.store');
    Route::post('/manualpayment/{id}/status', [UserOrdersController::class, 'updateManualpaymentStatus'])->name('manualpayment.updateStatus');

    
    Route::post('/get-suitable-packages', [UserOrdersController::class, 'getsuitablepackage'])->name('get.suitable.packages');


    Route::get('/allstories', [StoryController::class,'allkahanis'])->name('allstories');
    Route::get('/reviews/find/{id}', [StoryController::class,'allreviews'])->name('allreviews.find');
    Route::post('/review/{id}/status', [StoryController::class, 'updateReviewStatus'])->name('review.updateStatus');
    Route::delete('/review/delete/{id}', [StoryController::class, 'deletereview'])->name('review.delete');

    // for Testimonials
    Route::get('/testimonials',[TestimonialController::class,'index'])->name('testimonial.index');
    Route::delete('/testimonial/delete/{id}', [TestimonialController::class, 'deletetestimonial'])->name('testimonial.delete');
    Route::post('/testimonial/{id}/status', [TestimonialController::class, 'updateStatus'])->name('testimonial.updateStatus');

    // for logos of partner
    Route::get('/partner',[PartnerController::class,'index'])->name('partner.index');
    Route::get('/createpartner',[PartnerController::class,'create'])->name('partner.create');
    Route::post('/addpartner',[PartnerController::class,'store'])->name('partner.store');
    Route::get('/partner/find/{id}', [PartnerController::class, 'youtubevideofind'])->name('partner.find');
    Route::post('/partner/update/{id}', [PartnerController::class, 'youtubevideoUpdate'])->name('partnerupdate');
    Route::delete('/partner/delete/{id}', [PartnerController::class, 'deletetestimonial'])->name('partner.delete');
    

    // for youtube embeded videos
    Route::get('/youtubevideo',[YoutubeVideoController::class,'index'])->name('youtubevideo.index');
    Route::get('/addvideo',[YoutubeVideoController::class,'create'])->name('youtubevideo.create');
    Route::post('/create',[YoutubeVideoController::class,'store'])->name('youtubevideo.store');
    Route::get('/youtubevideo/find/{id}', [YoutubeVideoController::class, 'youtubevideofind'])->name('youtubevideo.find');
    Route::post('/youtubevideo/update/{id}', [YoutubeVideoController::class, 'youtubevideoUpdate'])->name('youtubevideoupdate');
    Route::delete('/youtubevideo/delete/{id}', [YoutubeVideoController::class, 'deletetestimonial'])->name('youtubevideo.delete');

    // for packages 
    Route::post('/packages/{id}/status', [PackageController::class, 'updatePackageStatus'])->name('packages.updateStatus');
    Route::get('/packages',[PackageController::class,'index'])->name('packages.index');
    Route::get('/packages/new', [PackageController::class, 'newpackage'])->name('package.new');
    Route::post('/packages/store', [PackageController::class, 'packagestore'])->name('package.store');
    Route::delete('/package/delete/{id}', [PackageController::class, 'deletepackage'])->name('package.delete');
    Route::get('/package/find/{id}', [PackageController::class, 'findpackage'])->name('package.find');
    Route::post('/package/update/{id}', [PackageController::class, 'packageUpdate'])->name('packageupdate');

    Route::get('/kahani/new', [StoryController::class, 'newkahani'])->name('addnew.story');
    Route::get('/kahani/find/{id}', [StoryController::class, 'findkahani'])->name('kahani.find');
    Route::post('/kahani/update/{id}', [StoryController::class, 'updatekahani'])->name('kahaniupdate');
    Route::delete('/kahani/delete/{id}', [StoryController::class, 'deletekahani'])->name('kahani.delete');
    Route::post('/kahani/store', [StoryController::class, 'store'])->name('kahani.store');


    Route::post('/assign-admin', [CouponController::class, 'assignAdmin'])->name('assign.admin');
    Route::get('/coupon/new', [CouponController::class, 'newcoupon'])->name('addnew.coupon');
    Route::post('/coupon/store', [CouponController::class, 'couponstore'])->name('coupon.store');
    Route::delete('/Coupon/delete/{id}', [CouponController::class, 'deletecoupon'])->name('coupon.delete');
    Route::get('/Coupon/find/{id}', [CouponController::class, 'findCoupon'])->name('coupon.find');
    Route::post('/Coupon/update/{id}', [CouponController::class, 'updateCoupon'])->name('Couponupdate');


});

Route::middleware('auth:admin')->group(function () {

    Route::get('/admin/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/admin/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/admin/profile', [AdminProfileController::class, 'destroy'])->name('admin.profile.destroy');

    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.get');
    Route::get('/Coupon/get/{coupon}', [CouponController::class, 'couponorders'])->name('couponorders');

    Route::get('/orgpayments', [UserOrdersController::class,'allorgpayments'])->name('allorgpayemnts');
    Route::post('/orgpayment/{id}/status', [UserOrdersController::class, 'updateorgpaymentStatus'])->name('orgpayment.updateStatus');

    Route::get('/changeadminpassword/{id}', [AdminUsers::class, 'resetpasswordadmin'])->name('resetadmin.password');
    Route::put('/admin/{id}/update-password', [AdminUsers::class, 'updatePasswordadmin'])->name('passwordadmin.update');
    
});

require __DIR__.'/admin-auth.php';
