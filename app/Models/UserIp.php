<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserIp extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_ips';

    public $timestamps = false;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'ip_address',
        'description',
        'whitelisted',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault([
            'name' => 'N/A',
        ]);
    }
}
