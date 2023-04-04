<?php

namespace App\Models;

use App\Enums\TransactionsStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'paidAmount',
        'Currency',
        'parentEmail',
        'statusCode',
        'paymentDate',
        'parentIdentification',
    ];

    protected $casts = [
        'statusCode' => TransactionsStatusEnum::class
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'parentEmail','email');
    }
}
