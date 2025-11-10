<?php

namespace App\Exports;

use Modules\Zones\Entities\Zones;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class ZonesExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {

            $query = Zones::query()->select([
                'id',
                'name',
                'status',
                'lat',
                'long',
                'address',
                'state_id',
                'city_id',
                'created_at'
            ])
            ->where('delete_status', 0); // keep only non-deleted

            
           if (!empty($this->status) && $this->status !== 'all') {
                $query->where('status', intval($this->status));
            }

        if (!empty($this->filters['state_id'])) {
            $query->where('state_id', $this->filters['state_id']);
        }

        if (!empty($this->filters['city_id'])) {
            $query->where('city_id', $this->filters['city_id']);
        }

        if (!empty($this->filters['timeline'])) {
            $now = now();
            switch ($this->filters['timeline']) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('created_at', [$now->startOfMonth(), $now->endOfMonth()]);
                    break;
                case 'this_year':
                    $query->whereBetween('created_at', [$now->startOfYear(), $now->endOfYear()]);
                    break;
            }
        }

        if (!empty($this->filters['from_date'])) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($this->filters['from_date'])->startOfDay());
        }

        if (!empty($this->filters['to_date'])) {
            $query->where('created_at', '<=', \Carbon\Carbon::parse($this->filters['to_date'])->endOfDay());
        }

        $zones = $query->orderBy('id', 'desc')->get();

       // Map to export format
        return $zones->map(function($zone){
            return [
                'Zone Name'   => $zone->name,
                'State Name'  => $zone->state->state_name ?? 'N/A',
                'City Name'   => $zone->city->city_name ?? 'N/A',
                'Address'     => $zone->address ?? 'N/A',
                'Latitude'    => $zone->lat ?? 'N/A',
                'Longitude'   => $zone->long ?? 'N/A',
                'Status'      => $zone->status == 1 ? 'Active' : 'Inactive',
                'Created At'  => $zone->created_at->format('Y-m-d H:i'),
            ];
        });

    }

    public function headings(): array
    {
        return ['Zone Name', 'State Name', 'City Name', 'Address',' Latitude',
        'Longitude','Status', 'Created At'];
    }
}
