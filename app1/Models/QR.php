<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QR extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'qr_records';

    protected $fillable = [
        'qr_id',
        'qr_image',
        'points',
        'remark',
        'is_used',
        'used_by',
    ];

    // Define the relationship with the User model (if applicable)
    public function user()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
