<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $slip->karyawan->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 3px double #333;
            padding-bottom: 10px;
            text-align: center;
        }
        .header h1 {
            font-size: 22px;
            margin: 0 0 5px 0;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .subtitle {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        .header .doc-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-table td {
            border: none;
            padding: 3px 0;
            vertical-align: top;
        }
        .meta-label {
            width: 15%;
            color: #666;
        }
        .meta-value {
            width: 35%;
            font-weight: bold;
        }
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .breakdown-table th {
            background-color: #1a1a1a;
            color: #fff;
            text-transform: uppercase;
            font-size: 9px;
            font-weight: bold;
            padding: 6px 10px;
            border: 1px solid #1a1a1a;
        }
        .breakdown-table td {
            padding: 6px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
        }
        .item-table td {
            border: none;
            padding: 4px 0;
        }
        .item-label {
            width: 60%;
            color: #333;
        }
        .item-val {
            width: 40%;
            text-align: right;
            font-weight: bold;
        }
        .item-total-row td {
            border-top: 1px solid #999;
            padding-top: 6px;
            font-weight: bold;
        }
        .net-salary-box {
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            padding: 10px 15px;
            margin-bottom: 25px;
            text-align: right;
        }
        .net-salary-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 4px;
        }
        .net-salary-val {
            font-size: 18px;
            font-weight: bold;
            color: #111;
        }
        .notes-section {
            margin-bottom: 30px;
            padding: 8px 12px;
            background-color: #fafafa;
            border-left: 3px solid #ccc;
            font-size: 10px;
            color: #555;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-table td {
            border: none;
            text-align: center;
            width: 50%;
        }
        .signature-space {
            height: 60px;
        }
        .footer {
            margin-top: 20px;
            font-size: 8px;
            color: #999;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PT SINAR ARTA DIGITAL</h1>
          
            <div class="doc-title">SLIP GAJI KARYAWAN</div>
        </div>

        @php
            $periodStartFormatted = '';
            $periodEndFormatted = '';
            try {
                $start = new DateTime($slip->periode_start . '-01');
                $periodStartFormatted = $start->format('F Y');
                $end = new DateTime($slip->periode_end . '-01');
                $periodEndFormatted = $end->format('F Y');
            } catch (Exception $e) {
                $periodStartFormatted = $slip->periode_start;
                $periodEndFormatted = $slip->periode_end;
            }
            $periodString = ($slip->periode_start === $slip->periode_end) 
                ? $periodStartFormatted 
                : $periodStartFormatted . ' - ' . $periodEndFormatted;
        @endphp

        <table class="meta-table">
            <tr>
                <td class="meta-label">ID Karyawan:</td>
                <td class="meta-value">{{ $slip->karyawan->staff_id }}</td>
                <td class="meta-label">Periode Gaji:</td>
                <td class="meta-value">{{ $periodString }}</td>
            </tr>
            <tr>
                <td class="meta-label">Nama:</td>
                <td class="meta-value">{{ $slip->karyawan->name }}</td>
                <td class="meta-label">Tanggal Bayar:</td>
                <td class="meta-value">{{ $slip->paid_at ? \Carbon\Carbon::parse($slip->paid_at)->format('d-m-Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td class="meta-label">Jabatan:</td>
                <td class="meta-value">{{ $slip->karyawan->position }}</td>
                <td class="meta-label">Rekening Bank:</td>
                <td class="meta-value">{{ $slip->karyawan->bank_name }} - {{ $slip->karyawan->bank_account }} (a.n. {{ $slip->karyawan->bank_account_name }})</td>
            </tr>
        </table>

        @php
            $totalEarnings = $slip->basic_salary + $slip->overtime_salary + $slip->meal_allowance + $slip->transportation_allowance + $slip->bonus_salary;
        @endphp

        <table class="breakdown-table">
            <thead>
                <tr>
                    <th width="50%">PENGHASILAN (EARNINGS)</th>
                    <th width="50%">POTONGAN (DEDUCTIONS)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table class="item-table">
                            <tr>
                                <td class="item-label">Gaji Pokok</td>
                                <td class="item-val">Rp {{ number_format($slip->basic_salary, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="item-label">Uang Lembur</td>
                                <td class="item-val">Rp {{ number_format($slip->overtime_salary, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="item-label">Tunjangan Makan</td>
                                <td class="item-val">Rp {{ number_format($slip->meal_allowance, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="item-label">Tunjangan Transport</td>
                                <td class="item-val">Rp {{ number_format($slip->transportation_allowance, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="item-label">Bonus</td>
                                <td class="item-val">Rp {{ number_format($slip->bonus_salary, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="item-total-row">
                                <td class="item-label">Total Penghasilan</td>
                                <td class="item-val">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table class="item-table">
                            <tr>
                                <td class="item-label">Potongan Keterlambatan</td>
                                <td class="item-val">Rp {{ number_format($slip->late_deduction, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="item-label">Potongan Absensi</td>
                                <td class="item-val">Rp {{ number_format($slip->absence_deduction, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="item-label">Ganti Rugi Kerusakan</td>
                                <td class="item-val">Rp {{ number_format($slip->damaged_cost, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="item-label">Potongan Lain-lain</td>
                                <td class="item-val">Rp {{ number_format($slip->other_deduction, 0, ',', '.') }}</td>
                            </tr>
                            <!-- Spacing row to align total layout -->
                            <tr>
                                <td class="item-label" style="height: 17px;">&nbsp;</td>
                                <td class="item-val">&nbsp;</td>
                            </tr>
                            <tr class="item-total-row">
                                <td class="item-label">Total Potongan</td>
                                <td class="item-val">Rp {{ number_format($slip->total_deduction, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="net-salary-box">
            <div class="net-salary-title">Penerimaan Bersih (Net Take Home Pay)</div>
            <div class="net-salary-val">Rp {{ number_format($slip->total_salary, 0, ',', '.') }}</div>
        </div>

        @if(!empty($slip->bonus_notes) || !empty($slip->other_deduction_notes))
            <div class="notes-section">
                <strong>Catatan / Keterangan:</strong><br>
                @if(!empty($slip->bonus_notes))
                    &bull; Catatan Bonus: {{ $slip->bonus_notes }}<br>
                @endif
                @if(!empty($slip->other_deduction_notes))
                    &bull; Catatan Potongan: {{ $slip->other_deduction_notes }}<br>
                @endif
            </div>
        @endif

        <table class="signature-table">
            <tr>
                <td>
                    <div>Penerima,</div>
                    <div class="signature-space"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $slip->karyawan->name }}</div>
                </td>
                <td>
                    <div>Kudus, {{ \Carbon\Carbon::parse($slip->paid_at ?? now())->format('d F Y') }}</div>
                    <div>PT Sinar Arta Digital,</div>
                    <div class="signature-space"></div>
                    <div style="font-weight: bold; text-decoration: underline;">HAIDAR WIBOWO RAHARDJO, S.M.</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Surat ini diterbitkan secara otomatis dan sah tanpa tanda tangan basah ketika status dinyatakan PAID. &bull; Sinar Arta Payroll System &bull; Dokumen Rahasia
        </div>
    </div>
</body>
</html>
