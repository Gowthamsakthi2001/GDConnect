<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BgvComment extends Model
{
    use HasFactory;
    protected $table = 'ev_tbl_bgv_comments';
    protected $fillable = [
        'dm_id',
        'bgv_id',
        'bgv_status',
        'remarks',
        'comment_type'
    ];
}
