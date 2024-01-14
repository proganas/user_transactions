<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'amount',
        'paid_on',
        'details',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
