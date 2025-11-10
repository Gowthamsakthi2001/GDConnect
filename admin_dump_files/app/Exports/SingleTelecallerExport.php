<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\User;

class SingleTelecallerExport implements FromCollection, WithHeadings, WithMapping
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
        return User::where('id', $this->id)->get(); 
    }

    public function map($staff): array
    {
        return [
            $staff->name ?? '-',
            $staff->email ?? '-',
            "'" . ($staff->phone ?? '-'),
            $staff->gender ?? '-',
            $staff->age ?? '-',
            $staff->address ?? '-',
            $this->role ?? '-',
            $staff->status ?? '-',
            $staff->created_at ? date('d-m-Y h:i:s',strtotime($staff->created_at)) : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Mobile Number',
            'Gender',
            'Age',
            'Address',
            'Role',
            'Account status',
            'Created at',
        ];
    }
}
