<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


// ****************** Route Sanctum pour test utilisateur ****************
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ****************** Inscription et connexion ****************

// inscription 
Route::post('register', [App\Http\Controllers\API\UserController::class, 'store'])->name('register');

// connexion 
Route::post('login', [App\Http\Controllers\API\LoginController::class, 'login'])->name('login');

// déconnexion 
Route::post('logout', [App\Http\Controllers\API\LoginController::class, 'logout'])->name('logout');


// ************* Pour toutes les routes : apiResource = mode ressource version API, sans create et edit ****************

// **************************************** routes USERS ***************************************************

Route::apiResource("users", App\Http\Controllers\API\UserController::class);
Route::put('users/{user}/updatepassword', [App\Http\Controllers\API\UserController::class, 'updatePassword'])->name('updatePassword');

// **************************************** routes LIEUX ***************************************************

Route::apiResource("places", App\Http\Controllers\API\PlaceController::class);

//récupérer tous les lieux mêmes ceux non validés (pour l'admin)
Route::get('getallplaces', [App\Http\Controllers\API\PlaceController::class, 'getAllPlaces'])->name('getallplaces');

// récupérer les lieux postés par l'utilisateur
Route::post('lieus/getplacesbyuser', [App\Http\Controllers\API\PlaceController::class, 'getPlacesByUser'])->name('getplacesbyuser');

// récupérer le nombre d'images par lieu
Route::get('lieus/getimagesnumberbyplace/{lieu}', [App\Http\Controllers\API\PlaceController::class, 'getImagesNumberByPlace'])->name('getImagesNumberByPlace');


// ******************************************* routes AVIS *********************************************

Route::apiResource("reviews", App\Http\Controllers\API\ReviewController::class);


// ************************************** routes CATEGORIES *************************************************

Route::apiResource("categories", App\Http\Controllers\API\CategoryController::class);


// **************************************** routes FAVORIS ********************************************

Route::apiResource("favorites", App\Http\Controllers\API\FavoriteController::class)->except('index', 'update', 'show', 'delete');
Route::get('favorites/{user}', [App\Http\Controllers\API\FavoriteController::class, 'index'])->name('index');
Route::delete('favorites/{user}/{lieu}', [App\Http\Controllers\API\FavoriteController::class, 'destroy'])->name('destroy');


//**************************************** routes IMAGES ***********************************************

Route::apiResource("images", App\Http\Controllers\API\ImageController::class);


//************************************** routes NOTIFICATIONS *************************************************

Route::apiResource("notifications", App\Http\Controllers\API\NotificationController::class)->except('index');
Route::get('getnotificationsbyuser/{user}', [App\Http\Controllers\API\NotificationController::class, 'getNotificationsByUser'])->name('getNotificationsByUser');


//************************************** route DEPARTEMENTS *************************************************

route::get("/departments", [App\Http\Controllers\API\DepartmentController::class, 'index'])->name("getDepartements");


//************************************** route REGIONS *************************************************

route::get("/regions", [App\Http\Controllers\API\RegionController::class, 'index'])->name("getRegions");


//************************************** token pour application mobile *********************************
 
Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);
 
    $user = User::where('email', $request->email)->first();
 
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Les informations fournies sont incorrectes.'],
        ]);
    }
 
    return $user->createToken($request->device_name)->plainTextToken;
});