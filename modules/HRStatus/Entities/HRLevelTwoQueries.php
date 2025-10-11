<?php

namespace Modules\HRStatus\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Deliveryman\Entities\Deliveryman;
use App\Models\User;

class HRLevelTwoQueries extends Model
{
    use HasFactory;

    protected $table = 'ev_tbl_hrleveltwo_queries';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'dm_id',
        'remarks',
        'comment_type',
        'comment_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function delivery_man()
    {
        return $this->belongsTo(Deliveryman::class, 'dm_id');
    }
    
        public function comment_by()
    {
        return $this->belongsTo(User::class, 'comment_by', 'id');
    }
    
    
    
}
