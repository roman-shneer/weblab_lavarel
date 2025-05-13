<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Setting extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'settings';
    protected $connection = 'mysql';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'setting_key',
        'setting_value',        
    ];
}
