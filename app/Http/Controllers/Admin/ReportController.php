<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuktiIgStory;
use App\Models\Customer;
use App\Models\Hamper;
use App\Models\HasilSpin;
use App\Models\Referral;
use App\Models\Transaksi;
use App\Models\Voucher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class ReportController extends Controller
{
    public function customer(Request $request)
    {
        [$laporan, $dari, $sampai] = $this->dataCustomer($request);

        return view('admin.report.customer', compact('laporan', 'dari', 'sampai'));
    }

    public function exportCustomer(Request $request)
    {
        [$laporan, $dari, $sampai] = $this->dataCustomer($request);

        $pdf = Pdf::loadView('admin.report.exports.customer', compact('laporan', 'dari', 'sampai'));

        return $pdf->download('laporan-customer-'.now()->format('Ymd-His').'.pdf');
    }

    public function transaksi(Request $request)
    {
        [$laporan, $totalPendapatan, $dari, $sampai] = $this->dataTransaksi($request);

        return view('admin.report.transaksi', compact('laporan', 'totalPendapatan', 'dari', 'sampai'));
    }

    public function exportTransaksi(Request $request)
    {
        [$laporan, $totalPendapatan, $dari, $sampai] = $this->dataTransaksi($request);

        $pdf = Pdf::loadView('admin.report.exports.transaksi', compact('laporan', 'totalPendapatan', 'dari', 'sampai'));

        return $pdf->download('laporan-transaksi-'.now()->format('Ymd-His').'.pdf');
    }

    public function loyalty(Request $request)
    {
        [$ringkasan, $detail, $periode, $dariTerpilih, $sampaiTerpilih, $labelPeriode] = $this->dataLoyalty($request);

        return view('admin.report.loyalty', compact('ringkasan', 'detail', 'periode', 'dariTerpilih', 'sampaiTerpilih', 'labelPeriode'));
    }

    public function exportLoyalty(Request $request)
    {
        [$ringkasan, $detail, , , , $labelPeriode] = $this->dataLoyalty($request);

        $pdf = Pdf::loadView('admin.report.exports.loyalty', compact('ringkasan', 'detail', 'labelPeriode'));

        return $pdf->download('laporan-loyalty-'.now()->format('Ymd-His').'.pdf');
    }

    private function dataCustomer(Request $request): array
    {
        $dari = trim((string) $request->query('dari', ''));
        $sampai = trim((string) $request->query('sampai', ''));

        $laporan = Customer::query()
            ->withCount(['transaksis as total_transaksi' => fn ($query) => $this->scopeFilterTanggal($query, $dari ?: null, $sampai ?: null)])
            ->withSum(['transaksis as total_belanja' => fn ($query) => $this->scopeFilterTanggal($query, $dari ?: null, $sampai ?: null)], 'total_transaksi')
            ->orderByDesc('total_belanja')
            ->get();

        return [$laporan, $dari, $sampai];
    }

    private function dataTransaksi(Request $request): array
    {
        $dari = trim((string) $request->query('dari', ''));
        $sampai = trim((string) $request->query('sampai', ''));

        $query = Transaksi::with('customer')
            ->latest('tanggal_transaksi')
            ->latest('id');

        $this->scopeFilterTanggal($query, $dari ?: null, $sampai ?: null);

        $laporan = $query->get();
        $totalPendapatan = $laporan->sum('total_transaksi');

        return [$laporan, $totalPendapatan, $dari, $sampai];
    }

    private function dataLoyalty(Request $request): array
    {
        Hamper::sinkronkanPeriode(now()->format('Y-m'));

        [$dari, $sampai, $periode, $dariTerpilih, $sampaiTerpilih, $labelPeriode] = $this->resolvePeriode($request);

        $ringkasan = [
            ['label' => 'Total Spin Dilakukan', 'value' => HasilSpin::whereBetween('tanggal_spin', [$dari, $sampai])->count()],
            ['label' => 'Voucher Aktif', 'value' => Voucher::where('status', 'aktif')->whereBetween('created_at', [$dari, $sampai])->count()],
            ['label' => 'Story Diterima', 'value' => BuktiIgStory::where('status_verifikasi', 'diterima')->whereBetween('tanggal_kirim', [$dari, $sampai])->count()],
            ['label' => 'Referral Valid', 'value' => Referral::where('status_valid', 'valid')->whereBetween('tanggal', [$dari, $sampai])->count()],
            ['label' => 'Customer Layak Hampers', 'value' => Hamper::countLayakPeriode($dari, $sampai)],
        ];

        $detail = [
            ['program' => 'Spin & Voucher', 'keterangan' => 'Total voucher yang diterbitkan dari hasil spin', 'jumlah' => Voucher::whereBetween('created_at', [$dari, $sampai])->count()],
            ['program' => 'Instagram Story', 'keterangan' => 'Story yang berstatus verifikasi diterima', 'jumlah' => BuktiIgStory::where('status_verifikasi', 'diterima')->whereBetween('tanggal_kirim', [$dari, $sampai])->count()],
            ['program' => 'Referral', 'keterangan' => 'Kode referral berstatus valid', 'jumlah' => Referral::where('status_valid', 'valid')->whereBetween('tanggal', [$dari, $sampai])->count()],
            ['program' => 'Hampers', 'keterangan' => 'Customer yang berstatus kelayakan memenuhi', 'jumlah' => Hamper::countLayakPeriode($dari, $sampai)],
        ];

        return [$ringkasan, $detail, $periode, $dariTerpilih, $sampaiTerpilih, $labelPeriode];
    }

    private function resolvePeriode(Request $request): array
    {
        $periode = $request->query('periode', 'bulan_ini');
        $dariTerpilih = (string) $request->query('dari', '');
        $sampaiTerpilih = (string) $request->query('sampai', '');
        $now = now();

        switch ($periode) {
            case 'bulan_lalu':
                $dari = $now->copy()->startOfMonth()->subMonth();
                $sampai = $now->copy()->startOfMonth()->subDay()->endOfDay();
                break;

            case 'custom':
                try {
                    $dari = $dariTerpilih !== '' ? Carbon::parse($dariTerpilih)->startOfDay() : null;
                    $sampai = $sampaiTerpilih !== '' ? Carbon::parse($sampaiTerpilih)->endOfDay() : null;
                } catch (Throwable) {
                    $dari = $sampai = null;
                }

                if (! $dari || ! $sampai || $dari->greaterThan($sampai)) {
                    $periode = 'bulan_ini';
                }
                break;

            default:
                $periode = 'bulan_ini';
        }

        if ($periode === 'bulan_ini') {
            $dari = $now->copy()->startOfMonth();
            $sampai = $now->copy()->endOfMonth();
        }

        $labelPeriode = $dari->format('d M Y').' — '.$sampai->format('d M Y');

        return [$dari, $sampai, $periode, $dariTerpilih, $sampaiTerpilih, $labelPeriode];
    }

    private function scopeFilterTanggal($query, ?string $dari, ?string $sampai)
    {
        return $query
            ->when($dari, fn ($q) => $q->whereDate('tanggal_transaksi', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal_transaksi', '<=', $sampai));
    }
}