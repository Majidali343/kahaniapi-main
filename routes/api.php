<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KahaniController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RatingsController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\Web\YoutubeVideoController;
use App\Http\Controllers\Web\PartnerController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('/allpackages', [PackagesController::class, 'simplepackages']);

Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::post('forgot-password-mobile', [AuthController::class, 'sendOtpEmail']);
Route::post('otp-verify-mobile', [AuthController::class, 'verifyMobileOtp']);
Route::post('mobile-password-change', [AuthController::class, 'changeMobilePassword']);


Route::get('/gettestimonials', [TestimonialController::class, 'gettestimonials']);
Route::get('/package/{id}', [PackagesController::class, 'find']);
Route::get('/freekahani', [KahaniController::class, 'freekahani']);


// youtube embeded video
Route::get('/youtubevideo',[YoutubeVideoController::class,'getapi']);
Route::get('/partners',[PartnerController::class,'getapi']);
Route::get('/getfamouskahanis', [KahaniController::class, 'getfamouskahanis']);

Route::post('x', [OrderController::class, 'updateOrderStatus']);

Route::group(['middleware' => ['auth:sanctum']], function() {

    Route::post('/createorder', [OrderController::class, 'createOrder']);
    Route::post('/manual-payment', [OrderController::class, 'storeManualPayment']); // add manual payment
    Route::post('/coupon/discount', [OrderController::class, 'getDiscount']);

    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/updatepassword', [AuthController::class, 'updatePassword']);
    Route::post('/updateProfile', [AuthController::class, 'updateProfile']);
    Route::get('/usershow', [AuthController::class, 'usershow']);
    Route::get('/getmembership', [OrderController::class, 'getmembership']);

    Route::post('/addFavourite', [KahaniController::class, 'addFavourite']);
    Route::get('/isFavourite/{id}', [KahaniController::class, 'isfavourite']);
    Route::get('/kahani/addview/{id}', [KahaniController::class, 'addviewkahani']);
    Route::get('/getkahanis', [KahaniController::class, 'getkahanis']);
    Route::get('/favouritekahanis', [KahaniController::class, 'getFavouriteKahanis']);
    
 
    Route::get('/getsinglekahani/{id}', [KahaniController::class, 'getsinglekahani']);

    // reviews 

    Route::get('/kahanireviews/{kahaniId}', [ReviewController::class, 'getReviewsByKahani']);
    Route::post('/savereview', [ReviewController::class, 'postReview']);
    Route::post('/postreply', [ReviewController::class, 'postreply']);
    
    //  Ratigs

    Route::get('/kahanisrating/{kahaniId}', [RatingsController::class, 'getAverageRating']);
    Route::post('/ratings', [RatingsController::class, 'postRating']);

    //testimonials
    Route::post('/savetestimonials', [TestimonialController::class, 'storetestimonial']);
    
    //packages
    Route::get('/getpackage', [PackagesController::class, 'index']);
   

   

});
