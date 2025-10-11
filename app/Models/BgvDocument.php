<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BgvDocument extends Model
{
    use HasFactory;
    protected $table = 'ev_tbl_bgv_documents';
    protected $fillable = [
        'dm_id',
        'bgv_id',
        'documents',
        'doc_type'
    ];
}
