<?php

namespace Modules\B2B\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2BRidersNotification extends Model
{
    use HasFactory;

    protected $table = 'b2b_tbl_riders_notifications';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'description',
        'image',
        'status',
        'read_status',
        'rider_id',
    ];

    protected $casts = [
        'status' => 'integer',
        'read_status' => 'integer',
    ];
}
