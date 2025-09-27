<?php

namespace App\Http\Controllers;

use App\Models\StockInventory;
use App\Models\Region;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function exportDistributions(Request $request)
    {
        $query = StockInventory::query()->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('warehouse', 'like', '%' . $request->search . '%')
                  ->orWhere('remarks', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->selectedStaff) {
            $query->where('staff_name', $request->selectedStaff);
        }

        if ($request->selectedRegion) {
            $query->where('region', $request->selectedRegion);
        }

        if ($request->selectedWarehouse) {
            $query->where('warehouse', $request->selectedWarehouse);
        }

        if ($request->dateFrom) {
            $query->whereDate('distribution_date', '>=', $request->dateFrom);
        }

        if ($request->dateTo) {
            $query->whereDate('distribution_date', '<=', $request->dateTo);
        }

        $distributions = $query->get();

        if ($distributions->isEmpty()) {
            return response()->json(['error' => 'No data to export'], 400);
        }

        $format = $request->get('format', 'excel');

        if ($format === 'pdf') {
            return $this->generatePdfFile($distributions, $request);
        }

        return $this->generateExcelFile($distributions);
    }

    private function generateExcelFile($distributions)
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Distribution Records');

        // Set headers
        $headers = [
            'A1' => 'No',
            'B1' => 'Date',
            'C1' => 'Staff Name',
            'D1' => 'ABM Centre',
            'E1' => 'Assessment Location',
            'F1' => 'For Use',
            'G1' => 'For Storing',
            'H1' => 'Total',
            'I1' => 'Remarks',
            'J1' => 'Created At'
        ];

        // Apply headers and styling
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style the header row
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '68AAAD'] // Indigo color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Add data rows
        $row = 2;
        $counter = 1;
        foreach ($distributions as $distribution) {
            $regionName = $this->getRegionName($distribution->region);

            $sheet->setCellValue('A' . $row, $counter);
            $sheet->setCellValue('B' . $row, Carbon::parse($distribution->distribution_date)->format('d M Y'));
            $sheet->setCellValue('C' . $row, $distribution->staff_name);
            $sheet->setCellValue('D' . $row, $regionName);
            $sheet->setCellValue('E' . $row, $distribution->warehouse);
            $sheet->setCellValue('F' . $row, $distribution->for_use_stock);
            $sheet->setCellValue('G' . $row, $distribution->for_storing);
            $sheet->setCellValue('H' . $row, $distribution->quantity);
            $sheet->setCellValue('I' . $row, $distribution->remarks ?: '-');
            $sheet->setCellValue('J' . $row, $distribution->created_at->format('d M Y H:i'));

            $row++;
            $counter++;
        }

        // Add totals row if there's data
        if ($distributions->count() > 0) {
            // Calculate totals
            $totalForUse = $distributions->sum('for_use_stock');
            $totalForStoring = $distributions->sum('for_storing');
            $totalQuantity = $distributions->sum('quantity');

            // Add totals row
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->setCellValue('F' . $row, $totalForUse);
            $sheet->setCellValue('G' . $row, $totalForStoring);
            $sheet->setCellValue('H' . $row, $totalQuantity);
            $sheet->setCellValue('I' . $row, '');
            $sheet->setCellValue('J' . $row, '');

            // Style the totals row
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF']
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => ['rgb' => '333333']
                    ]
                ]
            ]);

            // Center align the TOTAL label and numbers
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row . ':H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Auto-size columns
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders to all data (including totals row if present)
        $lastDataRow = $distributions->count() > 0 ? $row : $row - 1;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ]
            ]
        ];
        $sheet->getStyle('A1:J' . $lastDataRow)->applyFromArray($styleArray);

        // Center align numeric columns (No, For Use, For Storing, Total) - excluding totals row
        $lastRegularRow = $distributions->count() > 0 ? $row - 1 : $row - 1;
        $sheet->getStyle('A2:A' . $lastRegularRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:H' . $lastRegularRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Generate filename
        $filename = 'distribution_records_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Create writer and save to temporary file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_export');
        $writer->save($tempFile);

        // Return download response
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function generatePdfFile($distributions, $request)
    {
        // Prepare data for PDF
        $totalDistributed = $distributions->sum('quantity');
        $totalRecords = $distributions->count();

        // Get filter values for display
        $search = $request->get('search');
        $selectedStaff = $request->get('selectedStaff');
        $selectedRegion = $request->get('selectedRegion');
        $selectedWarehouse = $request->get('selectedWarehouse');
        $dateFrom = $request->get('dateFrom');
        $dateTo = $request->get('dateTo');

        // Get region name for display
        $selectedRegionName = $selectedRegion ? $this->getRegionName($selectedRegion) : null;

        // Check if any filters are applied
        $hasFilters = $search || $selectedStaff || $selectedRegion || $selectedWarehouse || $dateFrom || $dateTo;

        // Create a closure for getting region names in the template
        $getRegionName = function($regionCode) {
            return $this->getRegionName($regionCode);
        };

        // Generate PDF
        $pdf = Pdf::loadView('exports.distributions-pdf', compact(
            'distributions',
            'totalDistributed',
            'totalRecords',
            'search',
            'selectedStaff',
            'selectedRegion',
            'selectedRegionName',
            'selectedWarehouse',
            'dateFrom',
            'dateTo',
            'hasFilters',
            'getRegionName'
        ));

        // Set PDF options
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        // Generate filename
        $filename = 'distribution_records_' . date('Y-m-d_H-i-s') . '.pdf';

        // Return PDF download
        return $pdf->download($filename);
    }

    private function getRegionName($regionCode)
    {
        $region = Region::where('code', $regionCode)->first();
        return $region ? $region->name : ucfirst($regionCode) . ' Coast';
    }
}