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

    public function scopeFilter($query, $request)
    {
        $query->when($request->ip_address ?? false, fn($query, $ip_address) => $query
            ->where('ip_address','like',"%$ip_address%"));
    }
}
