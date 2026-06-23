<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Criterion extends Model
{
    protected $table = 'criteria';

    protected $fillable = [
        'code',
        'name',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function ahpWeight(): HasOne
    {
        return $this->hasOne(AhpWeight::class);
    }
}
