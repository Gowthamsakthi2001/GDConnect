<?php

namespace Modules\AssetMaster\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AssetMaster\Entities\QualityCheck;
use App\Models\User;
use Modules\Deliveryman\Entities\Deliveryman;
class QualityCheckReinitiate extends Model
{
    use HasFactory;
    
    protected $table = 'vehicle_qc_reinitiates';

    protected $fillable = [
        'qc_id',
        'status',
        'initiated_by',
        'dm_id' ,
        'role',
        'remarks',
        'created_at',
        'updated_at'
    ];
    
        public function quality_check()
    {
        return $this->belongsTo(QualityCheck::class, 'qc_id', 'id');
    }
    

    public function technician_reinitiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
    
        public function deliveryman_relation()
    {
        return $this->belongsTo(Deliveryman::class, 'dm_id','id');
    }

}