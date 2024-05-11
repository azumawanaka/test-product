<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        [
            'code' => '',
            'name' => 'Select Category'
        ],
        [
            'code' => 'electronics',
            'name' => 'Electronics'
        ],
        [
            'code' => 'clothing_and_apparel',
            'name' => 'Clothing & Apparel'
        ],
        [
            'code' => 'home_and_garden',
            'name' => 'Home & Garden'
        ],
        [
            'code' => 'health_and_beauty',
            'name' => 'Health & Beauty'
        ],
        [
            'code' => 'sports_and_outdoors',
            'name' => 'Sports & Outdoors'
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'descriptions',
        'img',
        'date_added',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_added' => 'datetime',
            'img' => 'array',
        ];
    }

    /**
     * Get the user that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // protected $appends = [
    //     'formatted_created_at',
    // ];

    // public function getFormattedCreatedAtAttribute()
    // {
    //     return $this->created_at->format('Y-m-d');
    // }
}
