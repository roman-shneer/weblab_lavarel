<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class Task extends Model
{
    use HasFactory, Notifiable;
    protected $primaryKey = "id";
    protected $table = 'tasks';
    public $incrementing = true;
    protected $connection = 'mysql';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'exp_number',
        'title',
        'description',
        'status',
        'source',
        'user_id',
        'created_at',
        'updated_at',
    ];

    
   
}
