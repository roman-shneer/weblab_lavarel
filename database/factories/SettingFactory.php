<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    private $settingKey= 'dataencrypt_key';

    public function definition(): array
    {
        return [            
            'user_id' => fake()->uuid,
            'setting_key' => $this->settingKey,
            'setting_value' => Str::random(100)
        ];
    }
    

    public function is_user_encrypted(int $uid):string{
        $userEncrypted=Setting::where('user_id' , $uid)->where('setting_key' , $this->settingKey)->first();       
        if($userEncrypted==null){
            return '';
        }
        
        return $userEncrypted->setting_value;
    }

    public function saveEncrypt(int $uid , $value){
        $setting = Setting::where('user_id', $uid)->where('setting_key', $this->settingKey)->first();
        if($setting){
            $setting->setting_value=$value;
            $setting->save();            
        }else{
            Setting::create([
                'user_id' => $uid,
                'setting_key' => $this->settingKey,
                'setting_value' => $value,
            ]);
        }
    }

}
