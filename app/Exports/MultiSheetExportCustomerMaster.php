<?php 

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetExportCustomerMaster implements WithMultipleSheets
{
    protected $status;
    protected $from_date;
    protected $to_date;
    protected $timeline;
    protected $selectedIds;
    protected $selectedFields;

    public function __construct($status, $from_date, $to_date, $timeline, $selectedIds = [], $selectedFields = [])
    {
        $this->status = $status;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->selectedIds = $selectedIds;
        $this->timeline = $timeline;
        $this->selectedFields = $selectedFields;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Always include CustomerMasterExport
        $sheets[] = new CustomerMasterExport(
            $this->status,
            $this->from_date,
            $this->to_date,
            $this->timeline,
            $this->selectedIds ,
            $this->selectedFields
        );

        
        // Convert fields into simple array of names
        $fields = collect($this->selectedFields)->pluck('name')->toArray();
    
        if (in_array('poc_details', $fields)) {
            $sheets[] = new CustomerPOCDetailExport(
                $this->status,
                $this->from_date,
                $this->to_date,
                $this->timeline,
                $this->selectedIds
            );
        }
    
        if (in_array('customer_hubs', $fields)) {
            $sheets[] = new OperationalHubExport(
                $this->status,
                $this->from_date,
                $this->to_date,
                $this->timeline,
                $this->selectedIds
            );
        }
        
    
        

        return $sheets;
    }
}



?>