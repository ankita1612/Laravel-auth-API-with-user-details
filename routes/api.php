<?php
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;
  
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
  
  
Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('error_page', 'error_page')->name("error_page"); 
    //Route::post('error_page', 'error_page')->name("error_page");
    
});
        
Route::middleware('auth:sanctum')->group( function () {    
    Route::get('user_detail', [RegisterController::class,'user_detail']);
    Route::get('logout', [RegisterController::class,'logout']);
    
    Route::get('list', [ProductController::class,'index']);
    //Route::resource('products', ProductController::class);
});

// Route for invalid call
Route::fallback(function(){
    return response()->json([ 'success'=>'false','message' => 'Page not found.'], 404);
});