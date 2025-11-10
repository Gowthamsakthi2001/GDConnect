<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'type_id',
        'qty',
        'price',
        'total',
    ];

    protected static function newFactory()
    {
        return \Modules\Inventory\Database\factories\ExpenseDetailFactory::new();
    }

    public function type()
    {
        return $this->belongsTo(ExpenseType::class, 'type_id');
    }
}
