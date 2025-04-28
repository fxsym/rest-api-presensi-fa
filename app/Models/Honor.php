<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Honor extends Model
{
    use HasFactory;

    protected $fillable = ['category', 'amount', 'info'];

    /**
     * Get all of the honors for the Honor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'honors_id', 'id');
    }
}
