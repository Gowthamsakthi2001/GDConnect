<?php

namespace Modules\HRStatus\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Deliveryman\Entities\Deliveryman;

class CandidateProgressLog extends Model
{
    use HasFactory;

    protected $table = 'candidate_progress_logs';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'dm_id',
        'remarks',
        'application_status',
        'department',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function delivery_man()
    {
        return $this->belongsTo(Deliveryman::class, 'dm_id');
    }
    
    
    
}
