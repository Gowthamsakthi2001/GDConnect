<?php
namespace Modules\VehicleServiceTicket\Entities;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FieldProxyTicket extends Model
{
    use HasFactory;
    
    protected $table = 'fieldproxy_tickets';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'vehicle_type',
        'vehicle_number',
        'vehicle_name',
        'vehicle_id',
        'updatedAt',
        'ticket_status',
        'telematics',
        'technician_notes',
        'task_performed',
        'sync',
        'state',
        'started_location',
        'started_at',
        'customer_location',
        'service_type',
        'driver_name',
        'driver_number',
        'service_charges',
        'role',
        'repair_type',
        'customer_email',
        'priority',
        'point_of_contact_info',
        'odometer',
        'observation',
        'location',
        'lastsync',
        'labour_description',
        'job_type',
        'issue_description',
        'image',
        'greendrive_ticketid',
        'final_image',
        'ended_location',
        'ended_at',
        'deletedat',
        'delete',
        'customer_number',
        'customer_name',
        'current_status',
        'createdAt',
        'contact_details',
        'city',
        'chassis_number',
        'category',
        'battery',
        'audit_status',
        'assignment_info',
        'assigned_technician_id',
        'assigned_by',
        'assigned_at',
        'address',
        'final_technician_notes',
        'type',
        'created_by',
        'created_at',
        'updated_at'
    ];
    
    
    protected $casts = [
    'image' => 'array',
    'customer_location' => 'array',
];
}
