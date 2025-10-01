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

        // Set headers with sub-headers for For Contractor and For Storing
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('C1', 'Staff Name');
        $sheet->setCellValue('D1', 'ABM Centre');
        $sheet->setCellValue('E1', 'Assessment Location');
        $sheet->setCellValue('F1', 'For Contractor');
        $sheet->setCellValue('H1', 'For ABM Storing');
        $sheet->setCellValue('J1', 'Total');
        $sheet->setCellValue('K1', 'Remarks');
        $sheet->setCellValue('L1', 'Created At');

        // Merge cells for main headers
        $sheet->mergeCells('F1:G1'); // For Contractor
        $sheet->mergeCells('H1:I1'); // For ABM Storing

        // Set sub-headers in row 2
        $sheet->setCellValue('A2', '');
        $sheet->setCellValue('B2', '');
        $sheet->setCellValue('C2', '');
        $sheet->setCellValue('D2', '');
        $sheet->setCellValue('E2', '');
        $sheet->setCellValue('F2', 'Helmet');
        $sheet->setCellValue('G2', 'Shirt');
        $sheet->setCellValue('H2', 'Helmet');
        $sheet->setCellValue('I2', 'Shirt');
        $sheet->setCellValue('J2', '');
        $sheet->setCellValue('K2', '');
        $sheet->setCellValue('L2', '');

        // Merge first row cells that span both rows
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:D2');
        $sheet->mergeCells('E1:E2');
        $sheet->mergeCells('J1:J2');
        $sheet->mergeCells('K1:K2');
        $sheet->mergeCells('L1:L2');

        // Style the header rows
        $sheet->getStyle('A1:L2')->applyFromArray([
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
        $row = 3; // Start from row 3 since rows 1-2 are headers
        $counter = 1;
        foreach ($distributions as $distribution) {
            $regionName = $this->getRegionName($distribution->region);

            $sheet->setCellValue('A' . $row, $counter);
            $sheet->setCellValue('B' . $row, Carbon::parse($distribution->distribution_date)->format('d M Y'));
            $sheet->setCellValue('C' . $row, $distribution->staff_name);
            $sheet->setCellValue('D' . $row, $regionName);
            $sheet->setCellValue('E' . $row, $distribution->warehouse);

            // For Contractor columns (Helmet and Shirt)
            $sheet->setCellValue('F' . $row, $distribution->for_use_helmets ?? 0);
            $sheet->setCellValue('G' . $row, $distribution->for_use_tshirts ?? 0);

            // For Storing columns (Helmet and Shirt)
            $sheet->setCellValue('H' . $row, $distribution->for_storing_helmets ?? 0);
            $sheet->setCellValue('I' . $row, $distribution->for_storing_tshirts ?? 0);

            // Total
            $sheet->setCellValue('J' . $row, $distribution->quantity);
            $sheet->setCellValue('K' . $row, $distribution->remarks ?: '-');
            $sheet->setCellValue('L' . $row, $distribution->created_at->format('d M Y H:i'));

            $row++;
            $counter++;
        }

        // Add totals row if there's data
        if ($distributions->count() > 0) {
            // Calculate totals
            $totalForUseHelmets = $distributions->sum('for_use_helmets');
            $totalForUseShirts = $distributions->sum('for_use_tshirts');
            $totalForStoringHelmets = $distributions->sum('for_storing_helmets');
            $totalForStoringShirts = $distributions->sum('for_storing_tshirts');
            $totalQuantity = $distributions->sum('quantity');

            // Add totals row
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->mergeCells('A' . $row . ':E' . $row);
            $sheet->setCellValue('F' . $row, $totalForUseHelmets);
            $sheet->setCellValue('G' . $row, $totalForUseShirts);
            $sheet->setCellValue('H' . $row, $totalForStoringHelmets);
            $sheet->setCellValue('I' . $row, $totalForStoringShirts);
            $sheet->setCellValue('J' . $row, $totalQuantity);
            $sheet->setCellValue('K' . $row, '');
            $sheet->setCellValue('L' . $row, '');

            // Style the totals row
            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
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
            $sheet->getStyle('F' . $row . ':J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Auto-size columns
        foreach (range('A', 'L') as $column) {
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
        $sheet->getStyle('A1:L' . $lastDataRow)->applyFromArray($styleArray);

        // Center align numeric columns (No, For Contractor, For ABM Storing, Total) - excluding totals row
        $lastRegularRow = $distributions->count() > 0 ? $row - 1 : $row - 1;
        $sheet->getStyle('A3:A' . $lastRegularRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F3:J' . $lastRegularRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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