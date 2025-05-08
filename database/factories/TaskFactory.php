<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'id'=>fake()->uuid,
            'exp_number'=> fake()->uuid,
            'title'=> Str::random(10),
            'description'=> Str::random(100),
            'status'=>fake()->uuid,
            'source'=> fake()->uuid,
            'user_id'=> fake()->uuid,
            'updated_at'=>now(),
            'created_at' => now()
        ];
    }

    public function next_exp_number($uid)
    {        
      
        $max_id = Task::where('user_id', $uid)->max('exp_number');
        
        if ($max_id == null) {
            $max_id = 0;
        }
        $max_id = $max_id + 1;
        
        return $max_id;
    }


    public function get_tasks($uid, $exp_number)
    {
        $query=Task::select(
            'id', 'exp_number', 'title', 'description', 'status', 'source', 'user_id',
            DB::raw('CAST(updated_at AS DATETIME) as datetime')
            )
            ->where('user_id', $uid);
        if  (!empty($exp_number)) {
            $query->where('exp_number', $exp_number);
        }
            
        $tasks=$query->orderBy('updated_at', 'DESC')->get()->toArray();        
        return $tasks;
    }


    /**
     * Get tasks grouped by exp_number
     *
     * @param array $args
     * @return array
     */
    public function get_tasks_grouped($uid, $args)
    {
        #$tasks = Task::where('user_id', $args['user_id'])->get();
        $query=Task::select(
            #'id', 'exp_number', 'title', 'description', 'status', 'source', 'user_id', 'updated_at'
            'exp_number',
                DB::raw('CAST(max(updated_at) AS DATETIME) as datetime'),
                DB::raw('count(id) as amount'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(title ORDER BY updated_at DESC),",",1) as title'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(status ORDER BY updated_at DESC),",",1) as status'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(source ORDER BY updated_at DESC),",",1) as source'),
                DB::raw('GROUP_CONCAT(DISTINCT DATE(updated_at) ORDER BY updated_at DESC) as dates'),
            )
            ->where('user_id', $uid);

        if  (!empty($args['exp_number']??'')) {
            $query->where('exp_number', $args['exp_number']);
        }
        if (!empty($args['title']??'')) {
            $query->having('title','LIKE', "%" . $args['title'] . "%");
        }

        if  (!empty($args['status']??'')) {
            $query->having('status', $args['status']);
        }
        if  (!empty($args['source']??'')) {
            $query->having('source', $args['source']);
        }
        if (!empty($args['date'] ?? '')) {
            $query->having('dates', 'LIKE', "%" . $args['date'] . "%");           
        }    
        $tasks=$query->groupBy('exp_number')->orderBy('updated_at', 'DESC')->get()->toArray();
        return $tasks;
    }



    public function save($uid, $args)
    {
        if ($args['id'] == 0) {
            $task = Task::create([
                'exp_number' => $args['exp_number'],
                'title' => $args['title'],
                'description' => $args['description'] ?? '',
                'status' => $args['status'],
                'source' => $args['source'],
                'user_id' => $uid,
                'created_at' => $args['datetime'],
                'updated_at' => $args['datetime']
            ]);
        } else {
            $task = Task::where('id',$args['id'])->where('user_id', $uid)->first();            
            $task->title = $args['title'] ?? '';
            $task->description = $args['description'] ?? '';
            $task->exp_number = $args['exp_number'];
            $task->status = $args['status'] ?? 0;
            $task->source = $args['source'] ?? '';
            $task->updated_at = $args['datetime'] ?? now();            
            $task->save();            
        }
    }

   
}
