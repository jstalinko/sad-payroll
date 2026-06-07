<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0 0 5px 0;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header .meta {
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th {
            background-color: #f5f5f5;
            color: #333;
            text-align: left;
            padding: 8px 10px;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
            text-transform: uppercase;
            font-size: 9px;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .grand-total {
            font-weight: bold;
            background-color: #fdfdfd;
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
            font-size: 12px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            font-size: 9px;
            color: #777;
            text-align: center;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-draft {
            background-color: #e5f1ff;
            color: #0066cc;
        }
        .badge-paid {
            background-color: #e6f7ed;
            color: #13803b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sinar Arta Payroll</h1>
        <div class="meta">
            <strong>Document:</strong> {{ $title }} &middot; 
            <strong>Date Generated:</strong> {{ now()->format('d F Y, H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Staff ID</th>
                <th width="20%">Employee Name</th>
                <th width="15%">Bank Details</th>
                <th width="15%">Bank Account No</th>
                <th width="13%">NPWP</th>
                <th width="10%">Period</th>
                <th width="10%" class="text-right">Total Salary</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($slips as $index => $slip)
                @php $grandTotal += $slip->total_salary; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $slip->karyawan->staff_id }}</strong></td>
                    <td>
                        {{ $slip->karyawan->name }}<br>
                        <small style="color: #666;">{{ $slip->karyawan->position }}</small>
                    </td>
                    <td>
                        {{ $slip->karyawan->bank_name }}<br>
                        <small style="color: #666;">a.n. {{ $slip->karyawan->bank_account_name }}</small>
                    </td>
                    <td>{{ $slip->karyawan->bank_account }}</td>
                    <td>{{ $slip->karyawan->npwp ?? '-' }}</td>
                    <td>
                        {{ $slip->periode_start }}<br>
                        <span style="font-size: 8px; color: #888;">to {{ $slip->periode_end }}</span>
                    </td>
                    <td class="text-right"><strong>Rp {{ number_format($slip->total_salary, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">No salary slip data available.</td>
                </tr>
            @endforelse
            
            @if($slips->count() > 0)
                <tr class="grand-total">
                    <td colspan="7" class="text-right">GRAND TOTAL:</td>
                    <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Sinar Arta Payroll System &bull; Confidential Report &bull; Page 1 of 1
    </div>
</body>
</html>
