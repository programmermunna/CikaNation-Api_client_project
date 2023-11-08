<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'username',
        'clock',
        'date',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];


    public function scopeFilter($query, $request)
    {
        $query->when($request->item_name ?? false, fn($query, $item_name) => $query
            ->where('item_name','like',"%$item_name%"));
    }
}
