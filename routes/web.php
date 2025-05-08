<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
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
    return Inertia\Inertia::render('Welcome',['statuses'=>$statuses]);
});
//TODO
Route::get('/about', function () {
    return Inertia\Inertia::render('About');
});
$last_grouped_request = null;
//request - grouped tasks per exp_number
Route::get('/tasks_grouped', function (Request $request) {
    $uid= Auth::id();    
    $all= $request->all();
  
       return json_encode([
        "items"=> Task::factory()->get_tasks_grouped($uid, $all),
        "max_id"=>Task::factory()->next_exp_number($uid),              
    ]);
});

//get all tasks for a given exp_number
Route::get(
    '/tasks',
    function (Request $request) {
        $uid = \Auth::id();
        $exp_number=$request->input('exp_number');
      
        return json_encode([
            "items" => Task::factory()->get_tasks($uid, $exp_number),           
        ]);
    });


//save single task
Route::post('/task', function (Request $request) {  
    $uid= \Auth::id();

    $all = $request->all();
    $id = $request->input('id');
    Task::factory()->save($uid,$all);
   
 
    return json_encode([
        'result' => 'ok',
    ]);
});

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
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
