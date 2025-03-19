<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'gift_id',
        'message',
        'points',
        'transaction_id',
        'type',
        'approved_at',
        'remark',
        'transaction_type', // Enum type (gift, cash, upi, account, qr)
        'status', // Enum status (pending, approved, completed, rejected)
        'product_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function gift()
    {
        return $this->belongsTo(Gift::class);
    }
        public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
