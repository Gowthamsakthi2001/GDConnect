<?php

namespace Modules\Inventory\Entities;

use App\Traits\FormatTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryExpenseType extends Model
{
    use FormatTimestamps, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category',
    ];

    public static function categories(): array
    {
        return [
            'Fuel',
            'Maintenance',
            'Other',
        ];
    }
}
