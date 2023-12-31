<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'task';

    protected $fillable = [
        'id',
        'title',
        'description',
        'completed',
        'completed_date',
        'deleted_date',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'task_id');
    }
}
