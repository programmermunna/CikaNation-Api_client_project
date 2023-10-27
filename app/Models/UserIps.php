<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserIps extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'user_ips';

    public $timestamps = false;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'ip_address',
        'description',
        'whitelisted', // default true
        'created_by',
        'updated_by',
        'deleted_by',
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    protected $appends = ['ip1', 'ip2', 'ip3', 'ip4'];

    public function getIp1Attribute()
    {
        return (int) explode('.', $this->ip_address)[0];
    }

    public function getIp2Attribute()
    {
        return (int) explode('.', $this->ip_address)[1];
    }

    public function getIp3Attribute()
    {
        return explode('.', $this->ip_address)[2] == '*' ? null : (int) explode('.', $this->ip_address)[2];
    }

    public function getIp4Attribute()
    {
        return explode('.', $this->ip_address)[3] == '*' ? null : (int) explode('.', $this->ip_address)[3];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault([
            'name' => 'N/A',
        ]);
    }
}
