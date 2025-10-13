<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ValidationSheet implements WithEvents, WithTitle
{
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $spreadsheet = $event->sheet->getParent();
                $sheet = $event->sheet->getDelegate();

                // ✅ Ensure the referenced sheets exist
                $telecallerSheet = $spreadsheet->getSheetByName('Telecaller List');
                $sourceSheet = $spreadsheet->getSheetByName('Source List');
                $citySheet = $spreadsheet->getSheetByName('City List');
                $vehicleSheet = $spreadsheet->getSheetByName('Vehicle List');

                if (!$telecallerSheet || !$sourceSheet || !$citySheet || !$vehicleSheet) {
                    throw new \Exception("One or more required sheets are missing.");
                }

                // ✅ Correct Named Ranges
                $spreadsheet->addNamedRange(new NamedRange('TelecallerList', $telecallerSheet, '$A$2:$A$100'));
                $spreadsheet->addNamedRange(new NamedRange('SourceList', $sourceSheet, '$A$2:$A$100'));
                $spreadsheet->addNamedRange(new NamedRange('CityList', $citySheet, '$A$2:$A$100'));
                $spreadsheet->addNamedRange(new NamedRange('VehicleList', $vehicleSheet, '$A$2:$A$100'));

                // ✅ Apply Dropdowns
                $this->applyDropdown($sheet, 'A', 'TelecallerList', 100);
                $this->applyDropdown($sheet, 'C', '"New,Contacted,Call_Back,Onboarded,DeadLead"', 100);
                $this->applyDropdown($sheet, 'D', 'SourceList', 100);
                $this->applyDropdown($sheet, 'H', 'CityList', 100);
                $this->applyDropdown($sheet, 'J', 'CityList', 100);
                $this->applyDropdown($sheet, 'L', 'VehicleList', 100);
            }
        ];
    }

    // ✅ Function to Apply Dropdowns
    private function applyDropdown(Worksheet $sheet, string $column, string $formula, int $rowCount)
    {
        for ($row = 2; $row <= $rowCount; $row++) {
            $validation = $sheet->getCell("{$column}{$row}")->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1($formula);
        }
    }

    public function title(): string
    {
        return 'ValidationSheet';
    }
}
