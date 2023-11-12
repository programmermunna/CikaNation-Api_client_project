<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserIp extends Model
{
    use HasFactory,
        SoftDeletes;

    protected $table = 'user_ips';

    public $timestamps = false;

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

    protected $appends = [
        'ip1',
        'ip2',
        'ip3',
        'ip4'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')
            ->withDefault([
                'name' => 'N/A',
                ]);
    }

    /**
     * Get the Ip1.
     */
    protected function ip1(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $ipAddress = explode('.', $attributes['ip_address']);

                return $ipAddress[0] === '*' ? null : $ipAddress[0];
            },
        );
    }

    /**
     * Get the Ip2.
     */
    protected function ip2(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $ipAddress = explode('.', $attributes['ip_address']);

                return $ipAddress[1] === '*' ? null : $ipAddress[1];
            },
        );
    }

    /**
     * Get the Ip3.
     */
    protected function ip3(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $ipAddress = explode('.', $attributes['ip_address']);

                return $ipAddress[2] === '*' ? null : $ipAddress[2];
            },
        );
    }


    /**
     * Get the Ip4.
     */
    protected function ip4(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                    $ipAddress = explode('.', $attributes['ip_address']);

                    return $ipAddress[3] === '*' ? null : $ipAddress[3];
                },
        );
    }

    public function scopeFilter(Builder $query, $request): void
    {
        $query->when($request->ip_address ?? false, fn($query, $ip_address) => $query
            ->where('ip_address','like',"%$ip_address%"));
    }
}
