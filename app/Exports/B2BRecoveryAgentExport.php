<?php

namespace App\Exports;

use Modules\Deliveryman\Entities\Deliveryman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class B2BRecoveryAgentExport implements FromCollection, WithHeadings, WithMapping
{
    protected $from_date;
    protected $to_date;
    protected $city;
    protected $zone;
    protected $status;
    protected $selectedIds;
    protected $selectedFields;

    public function __construct($from_date, $to_date, $selectedIds = [], $selectedFields = [], $city = null, $zone = null,$status = null)
    {
        $this->from_date      = $from_date;
        $this->to_date        = $to_date;
        $this->selectedIds    = $selectedIds;
        $this->selectedFields = $selectedFields;
        $this->city           = $city;
        $this->zone           = $zone;
        $this->status           = $status;
    }

    public function collection()
    {
        $user = User::find(Auth::id());
        
        $query = Deliveryman::where('work_type','in-house')->where('team_type',22)->where('delete_status', 0)->with(['current_city', 'zone'])->withCount([
                        'openedRequest as opened_request_count',
                        'closedRequest as closed_request_count'
                    ]); // if relations exist

        if (!empty($this->selectedIds)) {
            $query->whereIn('id', $this->selectedIds);
        } else {
            $query->when(!in_array($user->role, [1, 13]), function ($q) use ($user) {
                        $q->where('current_city_id', $user->city_id);
                    });
                    
            if ($this->city) {
                $query->where('current_city_id', $this->city);
            }

            if ($this->zone) {
                $query->where('zone_id', $this->zone);
            }

            if ($this->from_date) {
                $query->whereDate('created_at', '>=', $this->from_date);
            }

            if ($this->to_date) {
                $query->whereDate('created_at', '<=', $this->to_date);
            }
            
            if ($this->status == "active") {
                $query->where('rider_status', 1);
            }
            if ($this->status == "inactive") {
                $query->where('rider_status', 0);
            }
            
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function map($row): array
    {
        $mapped = [];

        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'profile_photo_path':
                    $mapped[] = $row->photo
                        ? asset('EV/images/photos/' . $row->photo)
                        : '-';
                    break;

                case 'created_at':
                    $mapped[] = $row->$key
                        ? Carbon::parse($row->$key)->format('d M Y h:i A')
                        : '-';
                    break;
                
                case 'phone':
                    $mapped[] = $row->mobile_number ?? '-';
                    break;
                    
                case 'city_id':
                    $mapped[] = $row->current_city->city_name ?? '-';
                    break;
                
                case 'status':
                    $mapped[] = ($row->rider_status == 1) ? 'Active' :'Inactive';
                    break;
                    
                case 'zone_id':
                    $mapped[] = $row->zone->name ?? '-';
                    break;
                
                case 'emp_id':
                    $mapped[] = $row->emp_id ?? '-';
                    break;
                    
                case 'reg_id':
                    $mapped[] = $row->reg_application_id ?? '-';
                    break;
                    
                case 'recovery_opened':
                    $mapped[] = ($row->opened_request_count < 1) ? 0 :$row->opened_request_count;
                    break;
                    
                case 'recovery_closed':
                    $mapped[] = $row->closed_request_count ?? 0;
                    break;
                
                case 'name':
                    $mapped[] = $row->first_name .' '. $row->last_name ?? '';
                    break; 
                
                case 'address':
                    $mapped[] = $row->present_address ?? '';
                    break;
                    
                default:
                    $mapped[] = $row->$key ?? '-';
            }
        }

        return $mapped;
    }

    public function headings(): array
    {
        $headers = [];

        $customHeadings = [
            'emp_id'                => 'Employee ID',
            'reg_id'                => 'Registration ID',
            'name'                  => 'Name',
            'email'                 => 'Email',
            'phone'                 => 'Phone',
            'gender'                => 'Gender',
            'age'                   => 'Age',
            'address'               => 'Address',
            'status'                => 'Status',
            'profile_photo_path'    => 'Profile Photo',
            'recovery_opened'       => 'Opened Requests',
            'recovery_closed'       => 'Closed Requests',
            'city_id'               => 'City',
            'zone_id'               => 'Zone',
            'created_at'            => 'Created At',
            
        ];

        foreach ($this->selectedFields as $key) {
            $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
        }

        return $headers;
    }
}
