<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class MainSheet implements WithEvents, WithTitle
{
    
    private function ensureSheetExists($spreadsheet, $sheetName)
{
    $sheet = $spreadsheet->getSheetByName($sheetName);
    
    if (!$sheet) {
        // If sheet does not exist, create a new one
        $newSheet = new Worksheet($spreadsheet, $sheetName);
        $spreadsheet->addSheet($newSheet);
        return $newSheet;
    }

    return $sheet;
}

   public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $spreadsheet = $event->sheet->getParent();
            $sheet = $event->sheet->getDelegate();

            // ✅ Ensure all sheets exist
            $telecallerSheet = $this->ensureSheetExists($spreadsheet, 'TelecallerListSheet');
            $sourceSheet = $this->ensureSheetExists($spreadsheet, 'SourceListSheet');
            $citySheet = $this->ensureSheetExists($spreadsheet, 'CityListSheet');
            $vehicleSheet = $this->ensureSheetExists($spreadsheet, 'VehicleListSheet');

            // ✅ Add Named Ranges
            $spreadsheet->addNamedRange(new NamedRange('TelecallerList', $telecallerSheet, '$B$2:$B$100'));
            $spreadsheet->addNamedRange(new NamedRange('SourceList', $sourceSheet, '$B$2:$B$100'));
            $spreadsheet->addNamedRange(new NamedRange('CityList', $citySheet, '$B$2:$B$100'));
            $spreadsheet->addNamedRange(new NamedRange('VehicleList', $vehicleSheet, '$B$2:$B$100'));

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


    private function applyDropdown($sheet, $column, $formula, $rowCount)
    {
        for ($row = 2; $row <= $rowCount; $row++) {
            $validation = $sheet->getCell("{$column}{$row}")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1($formula);
        }
    }

    public function title(): string
    {
        return 'MainSheet';
    }
}
