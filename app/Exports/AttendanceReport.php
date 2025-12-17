<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

class AttendanceReport implements FromQuery, WithMapping, WithHeadings, WithChunkReading
{
    protected $from_date;
    protected $to_date;
    protected $date_filter;
    protected $city = [];
    protected $area = [];
    protected $user_type = [];
    protected $user_id = [];
    protected $emp_id = [];
    protected $selectedFields = [];

    public function __construct(
        $from_date,
        $to_date,
        $date_filter,
        $city = [],
        $area = [],
        $user_type = [],
        $user_id = [],
        $emp_id = [],
        $selectedFields = []
    ) {
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->date_filter = $date_filter;
        $this->city = (array) $city;
        $this->area = (array) $area;
        $this->user_type = (array) $user_type;
        $this->user_id = (array) $user_id;
        $this->emp_id = (array) $emp_id;
        $this->selectedFields = (array) $selectedFields;
    }

    public function query()
    {
        $from = $this->from_date;
        $to   = $this->to_date;

        switch ($this->date_filter) {
            case 'today':
                $from = $to = now()->toDateString();
                break;
            case 'yesterday':
                $from = $to = now()->subDay()->toDateString();
                break;
            case 'this_week':
                $from = now()->startOfWeek()->toDateString();
                $to   = now()->endOfWeek()->toDateString();
                break;
            case 'last_week':
                $from = now()->subWeek()->startOfWeek()->toDateString();
                $to   = now()->subWeek()->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $from = now()->startOfMonth()->toDateString();
                $to   = now()->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $from = now()->subMonth()->startOfMonth()->toDateString();
                $to   = now()->subMonth()->endOfMonth()->toDateString();
                break;
        }

        $query = DB::table('ev_delivery_man_logs as dml')
            ->join('ev_tbl_delivery_men as dm', 'dm.id', '=', 'dml.user_id')
            ->leftJoin('ev_tbl_city as c', 'c.id', '=', 'dm.current_city_id')
            ->leftJoin('ev_tbl_area as area', 'c.id', '=', 'dm.interested_city_id')
            ->select(
                'dm.emp_id',
                DB::raw("CONCAT(dm.first_name,' ',dm.last_name) as deliveryman_name"),
                'c.city_name',
                'area.Area_name',
                DB::raw('DATE(dml.punched_in) as punchin_date'),
                'dml.punched_in as punch_in',
                'dml.punched_out as punch_out',
                'dml.punchin_address as punch_in_location',
                'dml.punchout_address as punchout_location'
            );

        if (!empty($this->city) && !in_array('all', $this->city)) {
            $query->whereIn('dm.current_city_id', $this->city);
        }

        if (!empty($this->area) && !in_array('all', $this->area)) {
            $query->whereIn('dm.interested_city_id', $this->area);
        }

        if (!empty($this->user_type) && !in_array('all', $this->user_type)) {
            $query->whereIn('dm.work_type', $this->user_type);
        }

        if (!empty($this->user_id) && !in_array('all', $this->user_id)) {
            $query->whereIn('dm.id', $this->user_id);
        }
        
        if (!empty($this->emp_id) && !in_array('all', $this->emp_id)) {
            $query->whereIn('dm.id', $this->emp_id);
        }

        if ($from && $to) {
            $query->whereBetween(DB::raw('DATE(dml.punched_in)'), [$from, $to]);
        }
        $query->orderBy('dml.user_id','desc');
        return $query;
    }
    
    public function map($row): array
    {
        $mapped = [];
    
        foreach ($this->selectedFields as $key) {
            switch ($key) {
                case 'emp_id':
                    $mapped[] = $row->emp_id ?? '-';
                    break;
                 case 'deliveryman_name':
                    $mapped[] = $row->deliveryman_name ?? '-';
                    break;
    
                case 'city':
                    $mapped[] = $row->city_name ?? '-';
                    break;
                case 'area_name':
                    $mapped[] = $row->Area_name ?? '-';
                    break;
                case 'punchin_date':
                    $mapped[] = $row->punchin_date ? date('d-m-Y', strtotime($row->punchin_date)) : '-';
                    break;
                case 'punch_in':
                    $mapped[] = $row->punch_in ? date('h:i:s A', strtotime($row->punch_in)) : '-';
                    break;
                case 'punch_out':
                    $mapped[] = $row->punch_out ? date('h:i:s A', strtotime($row->punch_out)) : 'Not Punched Out';
                    break;
                case 'punch_in_location':
                    $mapped[] = $row->punch_in_location ?? '-';
                    break;
                case 'punchout_location':
                    $mapped[] = $row->punchout_location ?? '-';
                    break;
                case 'total_online_duration':
                     $mapped[] = $this->calculateDuration($row->punch_in, $row->punch_out);
                    break;
            default:
                $mapped[] = $row->$key ?? '-';
            }
        }
    
        return $mapped;
    }

    public function headings(): array
    {
        $custom = [
            'emp_id' => 'Emp ID',
            'deliveryman_name' => 'Name',
            'city' => 'City',
            'area_name' => 'Area',
            'punchin_date' => 'Date',
            'punch_in' => 'Punch IN',
            'punch_out' => 'Punch Out',
            'punch_in_location' => 'Punch In Location',
            'punchout_location' => 'Punch Out Location',
            'total_online_duration' => 'Total Online Duration',
        ];

        return array_map(fn($f) => $custom[$f] ?? ucfirst(str_replace('_', ' ', $f)), $this->selectedFields);
    }
    
    public function calculateDuration($in, $out)
    {
        if (!$in || !$out) {
            return '00:00:00';
        }
    
        $start = \Carbon\Carbon::parse($in);
        $end   = \Carbon\Carbon::parse($out);
    
        $seconds = $start->diffInSeconds($end);
    
        $hours   = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs    = $seconds % 60;
    
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}


// class AttendanceReport implements FromQuery,WithMapping,WithHeadings, WithChunkReading
// {
//     protected $from_date;
//     protected $to_date;
//     protected $date_filter;
//     protected $city=[];
//     protected $area=[];
//     protected $user_type;
//     protected $user_id;
    
//      public function __construct($from_date, $to_date, $date_filter, $city = [], $area = [], $user_type = [],$user_id = [])
//     {
//         $this->from_date      = $from_date;
//         $this->to_date        = $to_date;
//         $this->date_filter = $date_filter; 
//         $this->city           = (array)$city;
//         $this->area           = (array)$area;
//         $this->user_type           = (array)$user_type;
//         $this->user_id           = (array)$user_id;
        
//     }
    
    
//     public function query()
//     {
//                 $city_ids   = $this->city ?? [];
//                 $zone_ids   = $this->area ?? [];
//                 $user_types = $this->user_type ?? [];
//                 $user_ids   = $this->user_id ?? [];
    
//                 $dateRange = $this->date_filter ?? 'today';
                
//                 $from = $this->from_date;
//                 $to   = $this->to_date;

//                 switch ($dateRange) {
//                     case 'today':
//                         $from = $to = now()->toDateString();
//                         break;
//                     case 'yesterday':
//                         $from = $to = now()->subDay()->toDateString();
//                         break;
//                     case 'this_week':
//                         $from = now()->startOfWeek()->toDateString();
//                         $to   = now()->endOfWeek()->toDateString();
//                         break;
//                     case 'last_week':
//                         $from = now()->subWeek()->startOfWeek()->toDateString();
//                         $to   = now()->subWeek()->endOfWeek()->toDateString();
//                         break;
//                     case 'this_month':
//                         $from = now()->startOfMonth()->toDateString();
//                         $to   = now()->endOfMonth()->toDateString();
//                         break;
//                     case 'last_month':
//                         $from = now()->subMonthNoOverflow()->startOfMonth()->toDateString();
//                         $to   = now()->subMonthNoOverflow()->endOfMonth()->toDateString();
//                         break;
//                     case 'custom':
//                         // keep passed from/to
//                         break;
//                     default:
//                         $from = $to = now()->toDateString();
//                 }
//                 $baseQuery = DB::table('ev_delivery_man_logs as dml')
//                     ->join('ev_tbl_delivery_men as dm', 'dm.id', '=', 'dml.user_id')
//                     ->leftJoin('ev_tbl_city as c', 'c.id', '=', 'dm.current_city_id')
//                     ->select(
//                         'dml.id as log_id',
//                         'dm.emp_id',
//                         DB::raw("CONCAT(dm.first_name,' ',dm.last_name) as deliveryman_name"),
//                         'dm.mobile_number',
//                         'c.city_name',
//                         'dml.punched_in',
//                         'dml.punched_out',
//                         'dml.punchin_latitude',
//                         'dml.punchin_longitude',
//                         'dml.punchout_latitude',
//                         'dml.punchedout_longitude',
//                         'dml.punchin_address',
//                         'dml.punchout_address'
//                     );

//                     if (!empty($city_ids) && !in_array('all', $city_ids)) {
//                         $baseQuery->whereIn('dm.current_city_id', $city_ids);
//                     }
//                     if (!empty($zone_ids) && !in_array('all', $zone_ids)) {
//                         $baseQuery->whereIn('dm.interested_city_id', $zone_ids);
//                     }
//                     if (!empty($user_types) && !in_array('all', $user_types)) {
//                         $baseQuery->whereIn('dm.work_type', $user_types);
//                     }
//                     if (!empty($user_ids) && !in_array('all', $user_ids)) {
//                         $baseQuery->whereIn('dm.emp_id', $user_ids);
//                     }

//                     if ($from && $to) {
//                         $baseQuery->whereDate('dml.punched_in', '>=', $from)
//                                   ->whereDate('dml.punched_in', '<=', $to);
//                     }
//                 return    $baseQuery->get();
//     }
    //  public function map($row): array
    // {
    //     $mapped = [];
    
    //     foreach ($this->selectedFields as $key) {
    
    //         switch ($key) {
    
    //             case 'emp_id':
    //                 $mapped[] = $row->emp_id ?? '-';
    //                 break;
    //              case 'deliveryman_name':
    //                 $mapped[] = $row->deliveryman_name ?? '-';
    //                 break;
    
    //             case 'city_name':
    //                 $mapped[] = $row->city_name ?? '-';
    //                 break;
    //             case 'punchin_date':
    //                 $mapped[] = $row->punchin_date ?? '-';
    //                 break;
    //             case 'punch_in':
    //                 $mapped[] = $row->punch_in ?? '-';
    //                 break;
    //             case 'punch_out':
    //                 $mapped[] = $row->punch_out ?? '-';
    //                 break;
    //             case 'punch_in_location':
    //                 $mapped[] = $row->punch_in_location ?? '-';
    //                 break;
    //             case 'punchout_location':
    //                 $mapped[] = $row->punchout_location ?? '-';
    //                 break;
    //             case 'total_online_duration':
    //                 $mapped[] = $this->calculateDuration($row->punch_in,$row->punch_out) ?? '-';
    //                 break;
                

    //         default:
    //             $mapped[] = $row->$key ?? '-';
    //         }
    //     }
    
    //     return $mapped;
    // }
    
//     public function calculateDuration($in, $out)
//     {
//         if (!$in || !$out) {
//             return '00:00:00';
//         }
    
//         $start = \Carbon\Carbon::parse($in);
//         $end   = \Carbon\Carbon::parse($out);
    
//         $seconds = $start->diffInSeconds($end);
    
//         $hours   = floor($seconds / 3600);
//         $minutes = floor(($seconds % 3600) / 60);
//         $secs    = $seconds % 60;
    
//         return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
//     }
    
//     public function headings(): array
//     {
//         $headers = [];
        
//         $customHeadings = [
//             'emp_id'              => 'Emp ID',
//             'deliveryman_name'                => 'Name',
//             'city_id'             => 'City Name',
//             'punchin_date'                => 'Date',
//             'punch_in'            => 'Punch IN',
//             'punch_out'           => 'Punch Out',
//             'punch_in_location'   => 'Punch In Location',
//             'punchout_location'   => 'Punch Out Location',
//             'total_online_duration' => 'Total Online Duration'
//         ];

//         foreach ($this->selectedFields as $key) {
//             $headers[] = $customHeadings[$key] ?? ucfirst(str_replace('_', ' ', $key));
//         }

//         return $headers;
//     }
    
//     public function chunkSize(): int
//     {
//         return 1000; 
//     }
// }