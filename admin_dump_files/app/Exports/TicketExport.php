<?php

namespace App\Exports;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketExport implements FromCollection, WithHeadings, WithMapping
{
    protected $type;
    protected $selectedFields;
    protected $data;

    public function __construct($type, $selectedFields = [], $data = [])
    {
        $this->type = $type;
        $this->selectedFields = $selectedFields;
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function map($row): array
    {
        $mapped = [];

        // Fields to format as dates
        $dateFields = ['started_at', 'ended_at', 'createdat', 'assigned_at' , 'updatedat'];
        
        foreach ($this->selectedFields as $field) {
            $key = $field['name'] ?? $field; // handle if plain string
    
            if ($key === 'ticket_id') {
                $mapped[] = $row['greendrive_ticketid'] ?? '-';
            } else {
                $value = $row[$key] ?? '-';
        
                // Try to detect date/datetime fields dynamically
                try {
                    if (!empty($value) && strtotime($value) !== false) {
                        $mapped[] = Carbon::parse($value)
                                        ->setTimezone('Asia/Kolkata')
                                        ->format('d M Y h:i A');
                    } else {
                        $mapped[] = $value;
                    }
                } catch (\Exception $e) {
                    // Fallback if parsing fails
                    $mapped[] = $value;
                }
            }
        }
        return $mapped;
    }

    public function headings(): array
    {
        $headers = [];
        foreach ($this->selectedFields as $field) {
            $key = $field['name'] ?? $field;
            $headers[] = ucfirst(str_replace('_', ' ', $key));
        }
        return $headers;
    }
}
