<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Cashflow extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts   = [
        'upload' => 'array',
    ];



    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->created_by = Auth::check() ? Auth::id() : null;
        });
    }



    public function scopeFilter($query, $request)
    {
        $query->when($request->item_name ?? false, fn($query, $item_name) => $query
            ->where('item_name','like',"%$item_name%"));
    }


}
