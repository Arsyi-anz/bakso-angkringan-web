{{-- resources/views/admin/report/exports/customer.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Customer</title>
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
        .footer { margin-top: 16px; font-size: 9px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Bakso Angkringan</div>
        <div class="sub">Laporan Program Loyalty Customer</div>
    </div>
    <div class="title">Laporan Customer</div>
    <div class="meta">
        Periode: {{ $dari || $sampai ? ($dari ?: 'Awal') . ' s/d ' . ($sampai ?: 'Akhir') : 'Semua waktu' }}<br>
        Dicetak: {{ now()->format('d M Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Customer</th>
                <th>No. HP</th>
                <th>Kode Referral</th>
                <th class="right">Total Transaksi</th>
                <th class="right">Total Belanja</th>
                <th>Terdaftar Sejak</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $l)
                <tr>
                    <td>{{ $l['nama'] }}</td>
                    <td>{{ $l['no_hp'] }}</td>
                    <td>{{ $l['kode_referral'] }}</td>
                    <td class="right">{{ $l['total_transaksi'] }}x</td>
                    <td class="right">Rp {{ number_format($l['total_belanja'], 0, ',', '.') }}</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($l['tanggal_daftar'])->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Tidak ada data pada rentang ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dokumen ini dibuat otomatis oleh Sistem Loyalty Bakso Angkringan.</div>
</body>
</html>