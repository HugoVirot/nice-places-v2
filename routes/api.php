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


// ************* Pour toutes les routes : apiResource = mode ressource version API, sans create et edit ****************

// **************************************** routes USERS ***************************************************

Route::apiResource("users", App\Http\Controllers\API\UserController::class);


// **************************************** routes LIEUX ***************************************************

Route::apiResource("lieus", App\Http\Controllers\API\LieuController::class);

//récupérer tous les lieux mêmes ceux non validés (pour l'admin)
Route::get('getallplaces', [App\Http\Controllers\API\LieuController::class, 'getAllPlaces'])->name('getallplaces');

// récupérer les lieux postés par l'utilisateur
Route::post('lieus/getplacesbyuser', [App\Http\Controllers\API\LieuController::class, 'getPlacesByUser'])->name('getplacesbyuser');

// récupérer le nombre d'images par lieu
Route::get('lieus/getimagesnumberbyplace/{lieu}', [App\Http\Controllers\API\LieuController::class, 'getImagesNumberByPlace'])->name('getImagesNumberByPlace');


// ******************************************* routes AVIS *********************************************

Route::apiResource("avis", App\Http\Controllers\API\AvisController::class);


// ************************************** routes CATEGORIES *************************************************

Route::apiResource("categories", App\Http\Controllers\API\CategorieController::class);


// **************************************** routes FAVORIS ********************************************

Route::apiResource("favoris", App\Http\Controllers\API\FavoriController::class)->except('index', 'update', 'show', 'delete');
Route::get('favoris/{user}', [App\Http\Controllers\API\FavoriController::class, 'index'])->name('index');
Route::delete('favoris/{user}/{lieu}', [App\Http\Controllers\API\FavoriController::class, 'destroy'])->name('destroy');


//**************************************** routes IMAGES ***********************************************

Route::apiResource("images", App\Http\Controllers\API\ImageController::class);


//************************************** routes NOTIFICATIONS *************************************************

Route::apiResource("notifications", App\Http\Controllers\API\NotificationController::class)->except('index');
Route::get('getnotificationsbyuser/{user}', [App\Http\Controllers\API\NotificationController::class, 'getNotificationsByUser'])->name('getNotificationsByUser');


//************************************** route DEPARTEMENTS *************************************************

route::get("/departements", [App\Http\Controllers\API\DepartementController::class, 'index'])->name("getDepartements");


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