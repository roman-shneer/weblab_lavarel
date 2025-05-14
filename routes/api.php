<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Setting;

//request - grouped tasks per exp_number
Route::get('/tasks_grouped', function (Request $request) {
    $uid = Auth::id();
    $all = $request->all();
    return json_encode([
        "items" => Task::factory()->get_tasks_grouped($uid, $all),
        "max_id" => Task::factory()->next_exp_number($uid),
    ]);
})->middleware('auth');


//get all tasks for a given exp_number
Route::get(
    '/tasks',
    function (Request $request) {
        $uid = Auth::id();
        $exp_number = $request->input('exp_number');
        return json_encode([
            "items" => Task::factory()->get_tasks($uid, $exp_number),
        ]);
    }
)->middleware('auth');


//save single task
Route::post('/task', function (Request $request) {
    $uid = Auth::id();
    $all = $request->all();
    Task::factory()->save($uid, $all);

    return json_encode([
        'result' => 'ok',
    ]);
})->middleware('auth');

//delete single task
Route::delete(
    '/task',
    function (Request $request) {
        $uid = Auth::id();
        $id = $request->input('id');
        $task = Task::where('id', $id)->where('user_id', $uid)->first();
        if ($task) {
            $task->delete();
        }
        return json_encode(['result' => 'ok']);
    }
)->middleware('auth');


Route::post("/encrypt_data", function (Request $request) {
    $uid = Auth::id();

    $key = $request->input("key");
    $crypted_example = $request->input("crypted_example");
    Setting::factory()->saveEncrypt($uid, $crypted_example);
    Task::factory()->encrypt_data($uid, $key);
})->middleware('auth');

Route::post("/decrypt_data", function (Request $request) {
    $uid = Auth::id();

    $key = $request->input("key");
    Setting::factory()->saveEncrypt($uid, '');
    Task::factory()->decrypt_data($uid, $key);
})->middleware('auth');