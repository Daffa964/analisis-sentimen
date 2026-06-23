<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ward extends Model
{
    protected $fillable = [
        'name',
        'category',
        'description',
        'qr_token',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate a unique token for QR survey on creation
        static::creating(function ($ward) {
            if (empty($ward->qr_token)) {
                $ward->qr_token = Str::random(16);
            }
        });
    }

    public function respondents(): HasMany
    {
        return $this->hasMany(Respondent::class);
    }

    public function sawResults(): HasMany
    {
        return $this->hasMany(SawResult::class);
    }
}
