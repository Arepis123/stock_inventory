<!DOCTYPE html>
<html>
<head>
    <title>Distribution Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }

        .summary {
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .summary-item {
            display: inline-block;
            width: 49%;   /* make sure both fit */
            text-align: center;
            vertical-align: top;
        }

        .summary-number {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 3px;
        }

        .summary-label {
            color: #666;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }

        table th {
            background-color: #68AAAD;
            color: white;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        table th.sub-header {
            background-color: #7DBABF;
            font-size: 10px;
            padding: 5px 4px;
        }

        table td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table td.center {
            text-align: center;
        }

        table td.number {
            text-align: center;
            font-weight: bold;
        }

        table td.small {
            font-size: 10px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        table tbody tr:hover {
            background-color: #e9ecef;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }

        .badge-blue { background-color: #007bff; }
        .badge-green { background-color: #28a745; }
        .badge-purple { background-color: #6f42c1; }
        .badge-orange { background-color: #fd7e14; }
        .badge-gray { background-color: #6c757d; }

        .totals-row {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .totals-row td {
            border-top: 2px solid #333;
            background-color: #e9ecef;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            font-size: 11px;
        }

        .filters strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Distribution Records Report</h1>
        <p>Generated on {{ now()->format('d M Y H:i') }}</p>
    </div>

    @if($hasFilters)
    <div class="filters">
        <strong>Applied Filters:</strong>
        @if($search)
            Search: "{{ $search }}" |
        @endif
        @if($selectedStaff)
            Staff: {{ $selectedStaff }} |
        @endif
        @if($selectedRegion)
            ABM Centre: {{ $selectedRegionName }} |
        @endif
        @if($selectedWarehouse)
            Assessment Location: {{ $selectedWarehouse }} |
        @endif
        @if($dateFrom)
            From: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} |
        @endif
        @if($dateTo)
            To: {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
        @endif
    </div>
    @endif

    <div class="summary">
      
            <div class="summary-item">
                <div class="summary-number">{{ number_format($totalDistributed) }}</div>
                <div class="summary-label">Total Sets Distributed</div>
            </div>
            <div class="summary-item">
                <div class="summary-number">{{ number_format($totalRecords) }}</div>
                <div class="summary-label">Total Distribution Records</div>
            </div>
       
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 4%;">No</th>
                <th rowspan="2" style="width: 9%;">Date</th>
                <th rowspan="2" style="width: 13%;">Staff Name</th>
                <th rowspan="2" style="width: 10%;">ABM Centre</th>
                <th rowspan="2" style="width: 13%;">Assessment Location</th>
                <th colspan="2" style="width: 10%;">For Use</th>
                <th colspan="2" style="width: 10%;">For Storing</th>
                <th rowspan="2" style="width: 6%;">Total</th>
                <th rowspan="2" style="width: 12%;">Remarks</th>
                <th rowspan="2" style="width: 10%;">Created</th>
            </tr>
            <tr>
                <th class="sub-header" style="width: 5%;">Helmet</th>
                <th class="sub-header" style="width: 5%;">Shirt</th>
                <th class="sub-header" style="width: 5%;">Helmet</th>
                <th class="sub-header" style="width: 5%;">Shirt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($distributions as $index => $distribution)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($distribution->distribution_date)->format('d M Y') }}</td>
                    <td>{{ $distribution->staff_name }}</td>
                    <td class="center">
                        <span class="badge {{
                            $distribution->region === 'east' ? 'badge-blue' :
                            ($distribution->region === 'west' ? 'badge-green' :
                            ($distribution->region === 'north' ? 'badge-purple' :
                            ($distribution->region === 'south' ? 'badge-orange' : 'badge-gray')))
                        }}">
                            {{ $getRegionName($distribution->region) }}
                        </span>
                    </td>
                    <td class="small">{{ $distribution->warehouse }}</td>
                    <td class="number">{{ number_format($distribution->for_use_helmets ?? 0) }}</td>
                    <td class="number">{{ number_format($distribution->for_use_tshirts ?? 0) }}</td>
                    <td class="number">{{ number_format($distribution->for_storing_helmets ?? 0) }}</td>
                    <td class="number">{{ number_format($distribution->for_storing_tshirts ?? 0) }}</td>
                    <td class="number">{{ number_format($distribution->quantity) }}</td>
                    <td class="small">{{ $distribution->remarks ?: '-' }}</td>
                    <td class="center small">{{ $distribution->created_at->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="center" style="padding: 30px; color: #666;">
                        No distribution records found with current filters.
                    </td>
                </tr>
            @endforelse

            <!-- Totals Row -->
            <tr class="totals-row">
                <td colspan="5" class="center" style="font-weight: bold;">TOTAL</td>
                <td class="number">{{ number_format($distributions->sum('for_use_helmets')) }}</td>
                <td class="number">{{ number_format($distributions->sum('for_use_tshirts')) }}</td>
                <td class="number">{{ number_format($distributions->sum('for_storing_helmets')) }}</td>
                <td class="number">{{ number_format($distributions->sum('for_storing_tshirts')) }}</td>
                <td class="number">{{ number_format($distributions->sum('quantity')) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This report contains {{ count($distributions) }} distribution record(s)</p>
        <p>Generated Stock Inventory Management System</p>
        <p>Powered by Hafiz</p>
    </div>
</body>
</html>