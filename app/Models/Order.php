<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'city',
        'country',
        'postcode',
        'mobile',
        'email',
        'order_notes',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'transaction_id'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}



