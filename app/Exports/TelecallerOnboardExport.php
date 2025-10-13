<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\User;
use Modules\Leads\Entities\leads;

class TelecallerOnboardExport implements FromCollection, WithHeadings, WithMapping
{
    protected $id;
    protected $role;

    public function __construct($id, $role)
    {
        $this->id = $id;
        $this->role = $role;
    }

    public function collection()
    {
        if($this->id == 0 && $this->role == "not-assigned"){
            return leads::whereNull('assigned')->get(); 
        }else{
            return leads::where('assigned', $this->id)->get(); 
        }
    }

    public function map($lead): array
    {
        $vehicle_name = '';
        if($lead->vehicle_type == 1){
            $vehicle_name = '2 wheeler';
        }
        else if($lead->vehicle_type == 2){
            $vehicle_name = '3 wheeler';
        }
        else if($lead->vehicle_type == 3){
            $vehicle_name = '4 wheeler';
        }
         else if($lead->vehicle_type == 4){
            $vehicle_name = 'Rental';
        }
        return [
            $lead->telecaller_status ?? '',
            $lead->get_source->source_name ?? '-',
            $lead->f_name ?? '-',
            $lead->l_name ?? '-',
            "'" . ($lead->phone_number ?? '-'),
            $lead->get_city->city_name ?? '-',
            $lead->get_area->Area_name ?? '-',
            $vehicle_name ?? '-',
            $lead->description ?? '',
            $lead->created_at ? date('d-m-Y h:i:s',strtotime($lead->created_at)) : '-',
            $lead->updated_at ? date('d-m-Y h:i:s',strtotime($lead->updated_at)) : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Telecaller Status',
            'Source',
            'First Name',
            'Last Name',
            'Mobile Number',
            'Current city',
            'Interested Area',
            'Vehicle Type',
            'Description',
            'Created at',
            'Updated at',
        ];
    }
}
