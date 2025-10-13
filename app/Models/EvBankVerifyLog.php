<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvBankVerifyLog extends Model
{
    use HasFactory;

    protected $table = 'ev_bank_verify_log'; 

    protected $primaryKey = 'id'; 
    public $timestamps = true;

    protected $fillable = [
        'request_id',
        'account_status',
        'beneficiary_name',
        'beneficiary_account',
        'beneficiary_ifsc',
        'bank_name',
        'branch_name',
        'message',
        'dm_id' ,
        'res_created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
