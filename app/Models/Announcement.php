<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'message',
        'created_by',
        'status',
    ];


    public function scopeFilter(Builder $query, $request)
    {
        $query->when($request->message ?? false, function($query, $message){
            $query->where('message','like',"%$message%");
        })

        ->when($request->status ?? false, function($query, $status){
            $query->where('status',$status);
        });
    }
}
