<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->withCount(['transaksis as total_transaksi'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->query('search');
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('kode_referral', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customer.index', compact('customers'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:20', Rule::unique('customers', 'no_hp')->ignore($customer->id)],
        ]);

        $customer->update($validated);

        return back()->with('success', 'Data customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();

            return back()->with('success', 'Customer berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException) {
            return back()->withErrors([
                'delete' => 'Customer tidak dapat dihapus karena memiliki data terkait (transaksi, voucher, referral, dll).',
            ]);
        }
    }
}