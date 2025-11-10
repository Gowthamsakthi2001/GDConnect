<?php

namespace Modules\MasterManagement\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvTblAccountabilityType extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_accountability_types';

    protected $primaryKey = 'id'; 

    public $incrementing = true; 

    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'status',
    ];
}
