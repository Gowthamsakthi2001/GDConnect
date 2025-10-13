<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use DB;

class TelecallerExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'LeadImportsheet' => new LeadImportSheetHeaders(),
            'ValidationSheet' => new ValidationSheet(),
        ];
    }
}

class LeadImportSheetHeaders implements FromArray
{
    public function array(): array
    {
        return [
            ['Telecaller_Status', 'Source_id', 'Telecaller_ID', 'First_Name', 'Last_Name', 'Mobile_Number', 'Current_city_id', 'Interested_Area_id', 'Vehicle_Type_id', 'Description'],
        ];
    }
}

class ValidationSheet implements FromArray
{
    public function array(): array
    {
        // Static dropdown values
        $telecallerStatus = [
            ['Telecaller Status'], // Heading
            ['Name', 'ID'],        // Sub-heading
            ['New', 'New'],
            ['Contacted', 'Contacted'],
            ['Call_Back', 'Call_Back'],
            ['Onboarded', 'Onboarded'],
            ['DeadLead', 'DeadLead'],
            [''], // Empty row for break
        ];

        $vehicleTypes = [
            ['Vehicle Types'],
            ['Name', 'ID'],
            ['2 wheeler', 1],
            ['3 wheeler', 2],
            ['4 wheeler', 3],
            ['Rental', 4],
            [''],
        ];

        // Fetching dynamic data from DB
        // $telecallers = DB::table('ev_tbl_telecallers')
        //     ->select('id', 'telecaller_name')
        //     ->get()
        //     ->map(function ($item) {
        //         return [$item->telecaller_name, $item->id];
        //     })
        //     ->toArray();
      $telecallers = \Illuminate\Support\Facades\DB::table('model_has_roles')
    ->join('users', 'model_has_roles.model_id', '=', 'users.id')
    ->select('users.id as id', 'users.name as telecaller_name')
    ->where('model_has_roles.role_id', 3)
    ->where('users.status', 'Active')
    ->get()
    ->map(fn($item) => ['telecaller_name' => $item->telecaller_name, 'id' => $item->id])
    ->toArray();




         $soure_names = DB::table('ev_tbl_lead_source')
        ->select('id', 'source_name')
        ->get()
        ->map(function ($item) {
            return [$item->source_name, $item->id];
        })
        ->toArray();

        $cities = DB::table('ev_tbl_city')
            ->where('status', 1)
            ->select('id', 'city_name')
            ->get()
            ->map(function ($item) {
                return [$item->city_name, $item->id];
            })
            ->toArray();
            
         $interested_cities = DB::table('ev_tbl_city')
            ->where('status', 1)
            ->select('id', 'city_name')
            ->get()
            ->map(function ($item) {
                return [$item->city_name, $item->id];
            })
            ->toArray();

        // Adding headings + breaks
        array_unshift($telecallers, ['Telecallers']);
        array_unshift($telecallers, ['Name', 'ID']);
        $telecallers[] = ['']; // Break
        
        // Adding headings + breaks
        array_unshift($soure_names, ['Source Names']);
        array_unshift($soure_names, ['Name', 'ID']);
        $soure_names[] = ['']; // Break

        array_unshift($cities, ['Current Cities']);
        array_unshift($cities, ['Name', 'ID']);
        $cities[] = ['']; // Break
        
        array_unshift($interested_cities, ['Interested Current Cities']);
        array_unshift($interested_cities, ['Name', 'ID']);
        $interested_cities[] = ['']; // Break

        // Final sheet data
        return array_merge($telecallerStatus,$soure_names,$telecallers, $vehicleTypes, $cities,$interested_cities);
    }
}
