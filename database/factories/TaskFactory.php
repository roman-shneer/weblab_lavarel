<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Libraries\mysqlAES\mysqlAES;
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
            DB::raw('CAST(created_at AS DATETIME) as datetime')
            )
            ->where('user_id', $uid);
        if  (!empty($exp_number)) {
            $query->where('exp_number', $exp_number);
        }

        $tasks = $query->orderBy('created_at', 'DESC')->get()->toArray();
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
            DB::raw('CAST(max(created_at) AS DATETIME) as datetime'),
                DB::raw('count(id) as amount'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT title ORDER BY created_at DESC SEPARATOR  "|"),"|",1) as title'),
            //DB::raw('GROUP_CONCAT(title2) as title2'),
            //DB::binary("GROUP_CONCAT(title2) as title2"),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT status ORDER BY created_at DESC SEPARATOR "|"),"|",1) as status'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT source ORDER BY created_at DESC SEPARATOR  "|"),"|",1) as source'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT description ORDER BY created_at DESC SEPARATOR  "|"),"|",1) as description'),
            DB::raw('GROUP_CONCAT(DISTINCT DATE(created_at) ORDER BY created_at DESC) as dates'),
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
        $tasks = $query->groupBy('exp_number')->orderBy('created_at', 'DESC')->get()->toArray();
        return $tasks;
    }



    public function save($uid, $args)
    {
        if ($args['id'] == 0) {

            $expNumbers = explode(",", $args['exp_number']);
            $expNumbers = array_map('trim', $expNumbers);
            foreach ($expNumbers as $expNumber) {
                $task = Task::create([
                    'exp_number' => $expNumber,
                    'title' => $args['title'],
                    'description' => $args['description'] ?? '',
                    'status' => $args['status'],
                    'source' => $args['source'],
                    'user_id' => $uid,
                    'created_at' => $args['datetime'],
                    'updated_at' => $args['datetime']
                ]);
            }
        } else {
            $task = Task::where('id',$args['id'])->where('user_id', $uid)->first();            
            $task->title = $args['title'] ?? '';
            $task->description = $args['description'] ?? '';
            $task->exp_number = $args['exp_number'];
            $task->status = $args['status'] ?? 0;
            $task->source = $args['source'] ?? '';
            $task->created_at = $args['datetime'] ?? now();
            $task->save();
        }
    }

    private function mysql_aes_key($key)
    {
        $new_key = str_repeat(chr(0), 16);
        for ($i = 0, $len = strlen($key); $i < $len; $i++) {
            $new_key[$i % 16] = $new_key[$i % 16] ^ $key[$i];
        }
        return $new_key;
    }
    /*
    public function aes_encrypt($val, $password)
    {
        $key = $this->mysql_aes_key($password);
        $pad_value = 16 - (strlen($val) % 16);
        $val = str_pad($val, (16 * (floor(strlen($val) / 16) + 1)), chr($pad_value));
        # return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $val, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM));

        $openssl_cipher_iv_length = openssl_cipher_iv_length('aes-256-cbc');
        #$openssl_cipher_iv_length = openssl_cipher_iv_length('aes-256-ecb');
        foreach (openssl_get_cipher_methods() as $alg) {
            if (strpos($alg, "ecb") > -1) {
                echo $alg . "<br>";
                echo openssl_cipher_iv_length($alg) . "<hr>";
            }
        }
        echo $openssl_cipher_iv_length . "<hr>";
        $iv = openssl_random_pseudo_bytes($openssl_cipher_iv_length);
        print_r($iv);
        return openssl_encrypt($val, 'aes-128-ecb', $key, OPENSSL_RAW_DATA, $iv);
        #return openssl_encrypt($val, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }

    public function aes_decrypt($val, $password)
    {
        $key = $this->mysql_aes_key($password);
        #$val = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $val, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM));
        #$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-ecb'));
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-128-cbc'));
        #$val = openssl_decrypt($val, 'aes-128-ecb', $key, OPENSSL_RAW_DATA, $iv);
        $val = openssl_decrypt($val, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return rtrim($val, "..16");
    }
*/
    private function encrypt($plaintext, $password)
    {
        #$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        #$key = hash('sha256', $password, true);
        #$ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        #return base64_encode(bin2hex($iv) . base64_encode($ciphertext));
        $enc = mysqlAES::hex(mysqlAES::encrypt($plaintext, $password));
        return $enc;
    }

    private function decrypt($ciphertext, $password)
    {
        #$ciphertext = base64_decode($ciphertext);
        #$iv = hex2bin(substr($ciphertext, 0, 32));
        #$ciphertext = base64_decode(substr($ciphertext, 32));
        #$key = hash('sha256', $password, true);
        #return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $dec = mysqlAES::decrypt(mysqlAES::unhex($ciphertext), $password);
        return $dec;
    }

    public function encrypt_data(int $uid, string $key)
    {
        $tasks = Task::where('user_id', $uid)->get();

        foreach ($tasks as $task) {
            if (!empty($task->title)) {
                $task->title = $this->encrypt($task->title, $key);
            }
            if (!empty($task->description)) {
                $task->description = $this->encrypt($task->description, $key);
            }
            if (!empty($task->source)) {
                $task->source = $this->encrypt($task->source, $key);
            }

            $task->save();            
        }
    }

    public function decrypt_data(int $uid, string $key)
    {
        $tasks = Task::where('user_id', $uid)->get();
        foreach ($tasks as $task) {

            if (!empty($task->title)) {
                $task->title = $this->decrypt($task->title, $key);
            }
            if (!empty($task->description)) {
                $task->description = $this->decrypt($task->description, $key);
            }
            if (!empty($task->source)) {
                $task->source = $this->decrypt($task->source, $key);
            }
            $task->save();
        }
    }
}
