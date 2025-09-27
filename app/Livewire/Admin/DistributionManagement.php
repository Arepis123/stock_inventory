<?php

namespace App\Livewire\Admin;

use App\Models\StockInventory;
use App\Models\Region;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Flux\Flux;

class DistributionManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedStaff = '';
    public $selectedRegion = '';
    public $selectedWarehouse = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedStaff()
    {
        $this->resetPage();
    }

    public function updatedSelectedRegion()
    {
        $this->resetPage();
    }

    public function updatedSelectedWarehouse()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedStaff = '';
        $this->selectedRegion = '';
        $this->selectedWarehouse = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function getDistributionsProperty()
    {
        $query = StockInventory::query()
            ->orderBy($this->sortBy, $this->sortDirection);

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('warehouse', 'like', '%' . $this->search . '%')
                  ->orWhere('remarks', 'like', '%' . $this->search . '%');
            });
        }

        // Staff filter
        if ($this->selectedStaff) {
            $query->where('staff_name', $this->selectedStaff);
        }

        // Region filter
        if ($this->selectedRegion) {
            $query->where('region', $this->selectedRegion);
        }

        // Warehouse filter
        if ($this->selectedWarehouse) {
            $query->where('warehouse', $this->selectedWarehouse);
        }

        // Date filters
        if ($this->dateFrom) {
            $query->whereDate('distribution_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('distribution_date', '<=', $this->dateTo);
        }

        return $query->paginate(15);
    }

    public function getRegionsProperty()
    {
        return Region::where('is_active', true)->get();
    }

    public function getRegionName($regionCode)
    {
        $region = Region::where('code', $regionCode)->first();
        return $region ? $region->name : ucfirst($regionCode) . ' Coast';
    }

    public function getStaffNamesProperty()
    {
        return StockInventory::distinct()
            ->pluck('staff_name')
            ->filter()
            ->sort()
            ->values();
    }

    public function getWarehousesProperty()
    {
        return StockInventory::distinct()
            ->pluck('warehouse')
            ->filter()
            ->sort()
            ->values();
    }

    public function getTotalDistributedProperty()
    {
        return StockInventory::sum('quantity');
    }

    public function getTotalRecordsProperty()
    {
        return StockInventory::count();
    }

    public function exportData($format)
    {
        try {
            // Add debug toast to confirm method is called
            Flux::toast([
                'heading' => 'Export Started',
                'text' => 'Preparing ' . ucfirst($format) . ' export...',
                'variant' => 'info'
            ]);

            // Get filtered data for export (without pagination)
            $query = StockInventory::query()
                ->orderBy('created_at', 'desc');

            // Apply the same filters as the main query
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('staff_name', 'like', '%' . $this->search . '%')
                      ->orWhere('warehouse', 'like', '%' . $this->search . '%')
                      ->orWhere('remarks', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->selectedRegion) {
                $query->where('region', $this->selectedRegion);
            }

            if ($this->selectedWarehouse) {
                $query->where('warehouse', $this->selectedWarehouse);
            }

            if ($this->dateFrom) {
                $query->whereDate('distribution_date', '>=', $this->dateFrom);
            }

            if ($this->dateTo) {
                $query->whereDate('distribution_date', '<=', $this->dateTo);
            }

            $distributions = $query->get();

            if ($distributions->isEmpty()) {
                Flux::toast([
                    'heading' => 'No Data to Export',
                    'text' => 'No distribution records found with current filters.',
                    'variant' => 'warning'
                ]);
                return;
            }

            if ($format === 'excel') {
                return $this->exportExcel($distributions);
            } elseif ($format === 'pdf') {
                return $this->exportPdf($distributions);
            }

        } catch (\Exception $e) {
            Flux::toast([
                'heading' => 'Export Failed',
                'text' => 'Error: ' . $e->getMessage(),
                'variant' => 'danger'
            ]);
        }
    }

    private function exportExcel($distributions)
    {
        $filename = 'distribution_records_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Create Excel content using SpreadsheetML format
        $xmlContent = '<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
<Title>Distribution Records</Title>
<Created>' . date('Y-m-d\TH:i:s\Z') . '</Created>
</DocumentProperties>
<Worksheet ss:Name="Distribution Records">
<Table>
<Row>
<Cell><Data ss:Type="String">Date</Data></Cell>
<Cell><Data ss:Type="String">Staff Name</Data></Cell>
<Cell><Data ss:Type="String">ABM Centre</Data></Cell>
<Cell><Data ss:Type="String">Assessment Location</Data></Cell>
<Cell><Data ss:Type="String">For Use</Data></Cell>
<Cell><Data ss:Type="String">For Storing</Data></Cell>
<Cell><Data ss:Type="String">Total</Data></Cell>
<Cell><Data ss:Type="String">Remarks</Data></Cell>
<Cell><Data ss:Type="String">Created At</Data></Cell>
</Row>';

        foreach ($distributions as $distribution) {
            $xmlContent .= '<Row>
<Cell><Data ss:Type="String">' . htmlspecialchars(Carbon::parse($distribution->distribution_date)->format('d M Y')) . '</Data></Cell>
<Cell><Data ss:Type="String">' . htmlspecialchars($distribution->staff_name) . '</Data></Cell>
<Cell><Data ss:Type="String">' . htmlspecialchars($this->getRegionName($distribution->region)) . '</Data></Cell>
<Cell><Data ss:Type="String">' . htmlspecialchars($distribution->warehouse) . '</Data></Cell>
<Cell><Data ss:Type="Number">' . $distribution->for_use_stock . '</Data></Cell>
<Cell><Data ss:Type="Number">' . $distribution->for_storing . '</Data></Cell>
<Cell><Data ss:Type="Number">' . $distribution->quantity . '</Data></Cell>
<Cell><Data ss:Type="String">' . htmlspecialchars($distribution->remarks ?: '-') . '</Data></Cell>
<Cell><Data ss:Type="String">' . htmlspecialchars($distribution->created_at->format('d M Y H:i')) . '</Data></Cell>
</Row>';
        }

        $xmlContent .= '</Table>
</Worksheet>
</Workbook>';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        return response($xmlContent, 200, $headers);
    }

    public function exportPdf()
    {
        // Show info toast that PDF export is coming soon
        Flux::toast('PDF export feature coming soon. Please use Excel export for now.');
    }

    public function render()
    {
        return view('livewire.admin.distribution-management')
            ->layout('components.layouts.app.sidebar');
    }
}
