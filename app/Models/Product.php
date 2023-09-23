<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $appends = [
        'image_url',
    ];

    protected $fillable = [
        'name',
        'price',
        'code',
        'stock',
        'min_stock',
        'notes',
        'image',
        'brand_id',
        'unit_id',
        'status',
    ];

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? Storage::disk('s3')->temporaryUrl($this->image, now()->addMinutes(5))
            : 'https://ui-avatars.com/api/?name='.$this->name.'&color=7F9CF5&background=EBF4FF';
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->where('stock', '<=', $this->min_stock);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', 0);
    }

    /**
     * Get the price value in decimal format rounded to 2 decimal places.
     */
    public function getPriceAttribute($value)
    {
        return number_format($value / 100, 2);
    }

    /**
     * Set the formatted price.
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }
}
