<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Village extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Using $guarded = is simple for this project, but in a larger application,
     * it's safer to use $fillable to explicitly list allowed attributes.
     */
    protected $guarded =;

    /**
     * The attributes that should be cast.
     * This ensures that when we retrieve data, these fields are of the correct type.
     */
    protected $casts = [
        'last_updated' => 'datetime',
    ];

    /**
     * Define the inverse of the one-to-one relationship.
     * A Village "belongs to" a User.
     * This defines the relationship from the child (village) to the parent (user).
     * The foreign key 'user_id' is on this model's table.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}