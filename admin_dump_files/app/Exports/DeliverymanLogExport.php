<?php

namespace App\Exports;

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DeliverymanLogExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $city_id;
    protected $zone_id;
    protected $client_id;
    protected $summary_type;
    protected $from_date;
    protected $to_date;
    protected $data;

    public function __construct($city_id = null, $zone_id = null, $client_id = null,$summary_type = 'all', $from_date = null, $to_date = null)
    {
        $this->city_id = $city_id;
        $this->zone_id = $zone_id;
        $this->client_id = $client_id;
        $this->summary_type = $summary_type;
        $this->from_date = $from_date;
        $this->to_date = $to_date;

    }

    // public function collection()
    // {
    //     $query = "
    //         SELECT 
    //             ev_delivery_man_logs.user_id,
    //             ev_tbl_delivery_men.first_name,
    //             ev_tbl_delivery_men.last_name,
    //             ev_tbl_delivery_men.rider_status,
    //             ev_tbl_delivery_men.current_city_id,
    //             ev_tbl_city.city_name,
    //             ev_tbl_delivery_men.zone_id,
    //             zones.name AS zone_name,
    //             ev_tbl_delivery_men.client_id,
    //             ev_tbl_clients.client_name AS client_name,
    //             SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)) AS total_minutes,
    //             CONCAT(
    //                 FLOOR(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)) / 60), ' hours ', 
    //                 MOD(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)), 60), ' minutes'
    //             ) AS total_time
    //         FROM ev_delivery_man_logs
    //         LEFT JOIN ev_tbl_delivery_men 
    //             ON ev_delivery_man_logs.user_id = ev_tbl_delivery_men.id
    //         LEFT JOIN ev_tbl_city 
    //             ON ev_tbl_city.id = ev_tbl_delivery_men.current_city_id
    //         LEFT JOIN zones 
    //             ON zones.id = ev_tbl_delivery_men.zone_id
    //         LEFT JOIN ev_tbl_clients 
    //             ON ev_tbl_clients.id = ev_tbl_delivery_men.client_id
    //         WHERE ev_tbl_delivery_men.work_type = 'deliveryman'
    //     ";

    //     $bindings = [];

    //     if ($this->city_id) {
    //         $query .= " AND ev_tbl_delivery_men.current_city_id = ?";
    //         $bindings[] = $this->city_id;
    //     }

    //     if ($this->zone_id) {
    //         $query .= " AND ev_tbl_delivery_men.zone_id = ?";
    //         $bindings[] = $this->zone_id;
    //     }

    //     if ($this->client_id) {
    //         $query .= " AND ev_tbl_delivery_men.client_id = ?";
    //         $bindings[] = $this->client_id;
    //     }

    //     $query .= "
    //         GROUP BY 
    //             ev_delivery_man_logs.user_id, 
    //             ev_tbl_delivery_men.first_name, 
    //             ev_tbl_delivery_men.last_name, 
    //             ev_tbl_delivery_men.rider_status,
    //             ev_tbl_delivery_men.current_city_id,
    //             ev_tbl_city.city_name,
    //             ev_tbl_delivery_men.zone_id,
    //             zones.name,
    //             ev_tbl_delivery_men.client_id,
    //             ev_tbl_clients.client_name
    //         ORDER BY ev_tbl_delivery_men.first_name ASC
    //     ";

    //     $result = DB::select($query, $bindings);
    //     $this->data = collect($result);
    //     return $this->data;
    // }
    
    public function collection()
    {
        $timeFilters = [
            'all'         => '', // No date condition
            'daily'       => "DATE(ev_delivery_man_logs.punched_in) = CURDATE()",
            'yesterday'   => "DATE(ev_delivery_man_logs.punched_in) = CURDATE() - INTERVAL 1 DAY",
            'this_week'   => "YEARWEEK(ev_delivery_man_logs.punched_in, 1) = YEARWEEK(CURDATE(), 1)",
            'last_week'   => "YEARWEEK(ev_delivery_man_logs.punched_in, 1) = YEARWEEK(CURDATE(), 1) - 1",
            'this_month'  => "MONTH(ev_delivery_man_logs.punched_in) = MONTH(CURDATE()) AND YEAR(ev_delivery_man_logs.punched_in) = YEAR(CURDATE())",
            'last_month'  => "MONTH(ev_delivery_man_logs.punched_in) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(ev_delivery_man_logs.punched_in) = YEAR(CURDATE() - INTERVAL 1 MONTH)",
        ];
    
        $bindings = [];
    
        // Base query
        $query = "
            SELECT 
                ev_tbl_delivery_men.id AS user_id,
                ev_tbl_delivery_men.first_name,
                ev_tbl_delivery_men.last_name,
                ev_tbl_delivery_men.rider_status,
                ev_tbl_delivery_men.current_city_id,
                ev_tbl_city.city_name,
                ev_tbl_delivery_men.zone_id,
                zones.name AS zone_name,
                ev_tbl_delivery_men.client_id,
                ev_tbl_clients.client_name AS client_name,
                IFNULL(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)), 0) AS total_minutes,
                CONCAT(
                    FLOOR(IFNULL(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)), 0) / 60), ' hours ', 
                    MOD(IFNULL(SUM(TIMESTAMPDIFF(MINUTE, ev_delivery_man_logs.punched_in, ev_delivery_man_logs.punched_out)), 0), 60), ' minutes'
                ) AS total_time
            FROM ev_tbl_delivery_men
            LEFT JOIN ev_delivery_man_logs 
                ON ev_delivery_man_logs.user_id = ev_tbl_delivery_men.id
        ";
    
        // Apply time filter if needed
        if ($this->summary_type === 'period' && $this->from_date && $this->to_date) {
            $query .= " AND DATE(ev_delivery_man_logs.punched_in) BETWEEN ? AND ?";
            $bindings[] = $this->from_date;
            $bindings[] = $this->to_date;
        } elseif (!empty($timeFilters[$this->summary_type])) {
            $query .= " AND " . $timeFilters[$this->summary_type];
        }
    
        $query .= "
            LEFT JOIN ev_tbl_city 
                ON ev_tbl_city.id = ev_tbl_delivery_men.current_city_id
            LEFT JOIN zones 
                ON zones.id = ev_tbl_delivery_men.zone_id
            LEFT JOIN ev_tbl_clients 
                ON ev_tbl_clients.id = ev_tbl_delivery_men.client_id
            WHERE ev_tbl_delivery_men.work_type = 'deliveryman'
        ";
    
        if ($this->city_id) {
            $query .= " AND ev_tbl_delivery_men.current_city_id = ?";
            $bindings[] = $this->city_id;
        }
    
        if ($this->zone_id) {
            $query .= " AND ev_tbl_delivery_men.zone_id = ?";
            $bindings[] = $this->zone_id;
        }
    
        if ($this->client_id) {
            $query .= " AND ev_tbl_delivery_men.client_id = ?";
            $bindings[] = $this->client_id;
        }
    
        $query .= "
            GROUP BY 
                ev_tbl_delivery_men.id, 
                ev_tbl_delivery_men.first_name, 
                ev_tbl_delivery_men.last_name, 
                ev_tbl_delivery_men.rider_status,
                ev_tbl_delivery_men.current_city_id,
                ev_tbl_city.city_name,
                ev_tbl_delivery_men.zone_id,
                zones.name,
                ev_tbl_delivery_men.client_id,
                ev_tbl_clients.client_name
            ORDER BY ev_tbl_delivery_men.first_name ASC
        ";
    
        $result = DB::select($query, $bindings);
        $this->data = collect($result);
        return $this->data;
    }


    public function map($emp): array
    {
        $status = match ((int)$emp->rider_status) {
            0 => 'Offline',
            1 => 'Online',
            default => 'Pending',
        };
        
        
        $row = [
            trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')),
            $emp->city_name ?? '-',
            $emp->zone_name ?? '-', 
            $emp->client_name ?? '-', 
        ];
        
        $type = strtolower(trim($this->summary_type));
        
        if ($type=="daily") {
            $today_log = \Illuminate\Support\Facades\DB::table('ev_delivery_man_logs')
                ->where('user_id', $emp->user_id)
                ->whereDate('punched_in', \Carbon\Carbon::today())
                ->orderBy('id', 'desc')
                ->first();
    
            $row[] = $today_log && $today_log->punched_in ? date('d-m-Y', strtotime($today_log->punched_in)) : '-';
            $row[] = $today_log && $today_log->punched_in ? date('H:i:s', strtotime($today_log->punched_in)) : '-';
            $row[] = $today_log && $today_log->punched_out ? date('H:i:s', strtotime($today_log->punched_out)) : '-';
        }
        
        if ($type == "yesterday") {
            $today_log = \Illuminate\Support\Facades\DB::table('ev_delivery_man_logs')
                ->where('user_id', $emp->user_id)
                ->whereDate('punched_in', \Carbon\Carbon::yesterday())
                ->orderBy('id', 'desc')
                ->first();
    
            $row[] = $today_log && $today_log->punched_in ? date('d-m-Y', strtotime($today_log->punched_in)) : '-';
            $row[] = $today_log && $today_log->punched_in ? date('H:i:s', strtotime($today_log->punched_in)) : '-';
            $row[] = $today_log && $today_log->punched_out ? date('H:i:s', strtotime($today_log->punched_out)) : '-';
        }
        
        $row[] = $emp->total_time ?? '-';
        $row[] = $status;
    
        return $row;
    }

    public function headings(): array
    {
        
        $headers = [
            'Deliveryman Name',
            'City',
            'Zone',
            'Client',
        ];
         if ($this->summary_type =="daily" || $this->summary_type =="yesterday") {
            $headers[] = 'Date';
            $headers[] = 'In Time';
            $headers[] = 'Out Time';
        }
        
        $headers[] = 'Total Online Hours';
        $headers[] = 'Status';
    
        return $headers;
    }
}

