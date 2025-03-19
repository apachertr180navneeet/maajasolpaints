<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gift extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'points',
        'image',
        'status','worth_price'
    ];
    protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('images/gifts/' . $this->image) : null;
    }
}
