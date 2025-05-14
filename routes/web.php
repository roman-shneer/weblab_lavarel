<?php

use Illuminate\Support\Facades\Route;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;



//main page
Route::get('/', function () {
    $uid = Auth::id();

    if ($uid != null) {
        print_r($uid);
        return redirect()->to('/home');
    }
    return Inertia\Inertia::render('Welcome');
});
//lab
Route::get('/home', function () {
    $uid = Auth::id();
    $statuses = env('MIX_EXPERIMENT_STATUSES');
    $statuses = explode(",", $statuses);
    $statuses = array_map('trim', $statuses);
    $userEnc = Setting::factory()->is_user_encrypted($uid);

    return Inertia\Inertia::render('Home', [
        'statuses' => $statuses,
        'encrypted' => !empty($userEnc),
        'encrypted_example' => $userEnc

    ]);
})->middleware('auth');





Auth::routes();

Route::get('/setting', function () {
    $uid = Auth::id();
    $userEnc = Setting::factory()->is_user_encrypted($uid);
    return Inertia\Inertia::render('Settings', [
        'encrypted' => !empty($userEnc),
        'encrypted_example' => $userEnc
    ]);
})->middleware('auth');




require_once "api.php";