<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class UserPermission extends Permission
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $with = ['children'];


    public function children()
    {
        return $this->hasMany(UserPermission::class,'parent_id','id');
    }
}
