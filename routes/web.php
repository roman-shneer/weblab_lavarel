<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


//main page
Route::get('/', function () {
    $uid = \Auth::id();
    if ($uid == null) {
        return redirect('/login');
    }
    $statuses = env('MIX_EXPERIMENT_STATUSES');
    $statuses = explode(",", $statuses);
    $statuses = array_map('trim', $statuses);
    $userEnc = Setting::factory()->is_user_encrypted($uid);

    return Inertia\Inertia::render('Welcome', [
        'statuses' => $statuses,
        'encrypted' => !empty($userEnc),
        'encrypted_example' => $userEnc

    ]);
})->middleware('auth');
//TODO
Route::get('/about', function () {
    return Inertia\Inertia::render('About');
});


//request - grouped tasks per exp_number
Route::get('/tasks_grouped', function (Request $request) {
    $uid= Auth::id();    
    $all= $request->all();
  
       return json_encode([
        "items"=> Task::factory()->get_tasks_grouped($uid, $all),
        "max_id"=>Task::factory()->next_exp_number($uid),              
    ]);
})->middleware('auth');

//get all tasks for a given exp_number
Route::get(
    '/tasks',
    function (Request $request) {
        $uid = \Auth::id();
        $exp_number=$request->input('exp_number');
      
        return json_encode([
            "items" => Task::factory()->get_tasks($uid, $exp_number),           
        ]);
    }
)->middleware('auth');


//save single task
Route::post('/task', function (Request $request) {
    $uid = \Auth::id();
    $all = $request->all();
    Task::factory()->save($uid,$all);
   
 
    return json_encode([
        'result' => 'ok',
    ]);
})->middleware('auth');

//delete single task
Route::delete(
    '/task',
    function (Request $request) {
        $uid = \Auth::id();
        $id = $request->input('id');
        $task = Task::where('id',$id)->where('user_id', $uid)->first();
        if ($task) {
            $task->delete();
        }
        return json_encode(['result' => 'ok']);
    }
)->middleware('auth');

Auth::routes();

Route::get('/home', function () {
    $uid = \Auth::id();
    if ($uid == null) {
        return redirect('/login');
    }
    $userEnc = Setting::factory()->is_user_encrypted($uid);
    return Inertia\Inertia::render('Dashboard', [
        'encrypted' => !empty($userEnc),
        'encrypted_example' => $userEnc
    ]);
})->middleware('auth');


Route::post("/encrypt_data", function (Request $request) {
    $uid = \Auth::id();

    $key = $request->input("key");
    $crypted_example = $request->input("crypted_example");
    Setting::factory()->saveEncrypt($uid, $crypted_example);
    Task::factory()->encrypt_data($uid, $key);
})->middleware('auth');

Route::post("/decrypt_data", function (Request $request) {
    $uid = \Auth::id();

    $key = $request->input("key");
    Setting::factory()->saveEncrypt($uid, '');
    Task::factory()->decrypt_data($uid, $key);
})->middleware('auth');

/*
Route::get("/test", function (Request $request) {


    $pass = 'somepassword';

    $string = 'CM';
    #$mysqlAES = new mysqlAES();
    $enc = mysqlAES::hex(mysqlAES::encrypt($string, $pass));
    $dec = mysqlAES::decrypt(mysqlAES::unhex($enc), $pass);

    #$dec_mysql = mysqlAES::decrypt(mysqlAES::unhex($from_mysql), $pass);
    #$encrypted = Task::factory()->aes_encrypt('My long string', $pass);
    echo $enc . "<hr>";
    $from_mysql = substr($from_mysql, 2);
    echo $from_mysql . "<hr>";

    $dec_mysql = mysqlAES::decrypt(mysqlAES::unhex($from_mysql), $pass);
    echo $dec . "<hr>";
    echo $dec_mysql . "<hr>";
    #echo Task::factory()->aes_encrypt($encrypted, $pass) . "<hr>";
    die();
})->middleware('auth');*/