{{-- resources/views/admin/report/exports/loyalty.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Program Loyalty</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 11px; color: #1f2933; }
        .header { border-bottom: 3px solid #111827; padding-bottom: 8px; margin-bottom: 6px; }
        .brand { font-size: 16px; font-weight: bold; color: #111827; }
        .sub { color: #6b7280; font-size: 10px; }
        .title { font-size: 14px; font-weight: bold; margin: 14px 0 2px; }
        .meta { color: #6b7280; font-size: 10px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 4px 6px; text-align: left; }
        th { background: #f3f4f6; font-size: 10px; text-transform: uppercase; }
        td { font-size: 10px; }
        .right { text-align: right; }
        .cards { width: 100%; margin-bottom: 12px; }
        .card { display: inline-block; width: 19%; border: 1px solid #d1d5db; padding: 6px; margin-right: 0.5%; vertical-align: top; }
        .card .label { font-size: 8.5px; color: #6b7280; }
        .card .value { font-size: 15px; font-weight: bold; }
        .footer { margin-top: 16px; font-size: 9px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Bakso Angkringan</div>
        <div class="sub">Laporan Program Loyalty Customer</div>
    </div>
    <div class="title">Laporan Program Loyalty</div>
    <div class="meta">
        Periode: {{ $labelPeriode }}<br>
        Dicetak: {{ now()->format('d M Y H:i') }}
    </div>

    <div class="cards">
        @foreach ($ringkasan as $r)
            <div class="card">
                <div class="value">{{ number_format($r['value']) }}</div>
                <div class="label">{{ $r['label'] }}</div>
            </div>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th>Program</th>
                <th>Keterangan</th>
                <th class="right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($detail as $d)
                <tr>
                    <td>{{ $d['program'] }}</td>
                    <td>{{ $d['keterangan'] }}</td>
                    <td class="right">{{ number_format($d['jumlah']) }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Belum ada data program loyalty.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dokumen ini dibuat otomatis oleh Sistem Loyalty Bakso Angkringan.</div>
</body>
</html>