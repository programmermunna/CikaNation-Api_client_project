<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class UserPermission extends Permission
{
    use HasFactory;

    protected $guarded = ['id'];


    public function children()
    {
        return $this->belongsTo(UserPermission::class,'parent_id','id');
    }
}
