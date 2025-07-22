<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PrestayController;
use App\Models\Contact;

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
Route::post('login', [AuthController::class, 'signin']);
Route::post('register', [AuthController::class, 'signup']);

// Simple API to get latest contact data with basic authentication
Route::get('latest-contact', function(Request $request) {
    // Check for basic auth credentials
    $credentials = [
        'email' => $request->getUser(),
        'password' => $request->getPassword()
    ];
    
    // If no credentials or invalid credentials, require authentication
    if (!$credentials['email'] || !$credentials['password'] || 
        !\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized access'
        ], 401)->header('WWW-Authenticate', 'Basic');
    }
    
    $latestContact = Contact::latest('created_at')->first();
    
    if (!$latestContact) {
        return response()->json([
            'status' => 'error',
            'message' => 'No contacts found'
        ], 404);
    }
    
    // Get related profilesfolio data
    $profileData = \App\Models\ProfileFolio::where('profileid', $latestContact->contactid)
        ->latest('created_at')
        ->first();
    
    // Get related transaction data if profilesfolio exists
    $transactionData = null;
    if ($profileData) {
        $transactionData = \App\Models\Transaction::where('resv_id', $profileData->folio)
            ->latest('created_at')
            ->first();
    }
    
    // Check if contact was created today
    $isUpdatedToday = false;
    if ($latestContact->created_at) {
        $isUpdatedToday = $latestContact->created_at->format('Y-m-d') === now()->format('Y-m-d');
    }
    
    return response()->json([
        'status' => 'success',
        'data' => [
            'contact' => [
                'contact_id' => $latestContact->contactid,
                'name' => $latestContact->fname . ' ' . $latestContact->lname,
                'email' => $latestContact->email,
                'created_at' => $latestContact->created_at,
                'updated_at' => $latestContact->updated_at,
                'last_update_days_ago' => $latestContact->created_at ? now()->diffInDays($latestContact->created_at) : null
            ],
            'profile' => $profileData ? [
                'folio' => $profileData->folio,
                'folio_status' => $profileData->foliostatus,
                'room' => $profileData->room,
                'room_type' => $profileData->roomtype,
                'pax' => $profileData->pax,
                'check_in' => $profileData->dateci,
                'check_out' => $profileData->dateco,
                'source' => $profileData->source,
                'created_at' => $profileData->created_at
            ] : null,
            'transaction' => $transactionData ? [
                'id' => $transactionData->id,
                'resv_id' => $transactionData->resv_id,
                'revenue' => $transactionData->revenue,
                'created_at' => $transactionData->created_at
            ] : null,
            'is_updated_today' => $isUpdatedToday,
            'has_profile_folio' => !is_null($profileData),
            'has_transaction' => !is_null($transactionData),
        ],
        'timestamp' => now()->toDateTimeString()
    ]);
});

Route::middleware('auth:sanctum')->group( function () {
    Route::resource('prestay', PrestayController::class);
    Route::post('updateprestay', [PrestayController::class,'update']);
    Route::post('deleteprestay', [PrestayController::class,'delete']);
});
