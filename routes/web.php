<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\TempImagesController;
use Illuminate\Http\Request;

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

Route::get('/', function () {
    return view('welcome');
});


//whatever routes will be created admin in the prefix is mandatory
Route::group(['prefix' => 'admin'], function(){

    Route::group(['middleware' => 'admin.guest'], function(){

        //Admin Routes
        Route::get('/login',[AdminLoginController::class,'index'])->name('admin.login');
        Route::post('/authenticate',[AdminLoginController::class,'authenticate'])->name('admin.authenticate');
        //form submission will be send to this route
    });

    Route::group(['middleware' => 'admin.auth'], function(){

        //Home Routes
        Route::get('/dashboard',[HomeController::class,'index'])->name('admin.dashboard');
        //authenticated routes where users are essential to login
        Route::get('/logout',[HomeController::class,'logout'])->name('admin.logout');

        //Category routes
        Route::get('/categories',[CategoryController::class,'index'])->name('categories.index');
        Route::get('/categories/create',[CategoryController::class,'create'])->name('categories.create');
        Route::post('/categories',[CategoryController::class,'store'])->name('categories.store');

        //temp-images.create
        Route::post('/upload-temp-image', [TempImagesController::class,'create'])->name('temp-images.create');

        Route::get('/getSlug', function(Request $request){
            $slug = '';
        //if slug is not in if condition then slug will not be created and passes a blank value  
            if(!empty($request->title)){
                $slug = Str::slug($request->title);   //to implement this we use ajax in create.blade.php file 
            }
            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        })->name('getSlug');
    });
});