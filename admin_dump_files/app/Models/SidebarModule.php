<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SidebarModule extends Model
{
    protected $table = 'sidebar_modules';

    protected $fillable = [
        'module_name',
        'image',
        'view_roles_id',
        'status',
        'route_name',
    ];

    protected $casts = [
        'view_roles_id' => 'array', 
    ];
}
