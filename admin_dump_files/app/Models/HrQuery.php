<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrQuery extends Model
{
    use HasFactory;
    protected $table = 'ev_tbl_hr_queries';
    protected $fillable = [
        'dm_id',
        'auth_id',
        'remarks',
        'query_type'
    ];
}
