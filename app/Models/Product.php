<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'points',
        'description',
        'expiry_date','is_expire','worth_price'
    ];

    protected $casts = [
        'expiry_date' => 'date'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('images/products/' . $this->image) : null;
    }
}
