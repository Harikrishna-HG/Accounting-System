<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->paginate(10);

        return view('accounting.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('accounting.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'pan_vat' => 'nullable|string|max:50',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($validated) {
            Supplier::create($validated);
        });

        return redirect()->route('accounting.suppliers.index')
            ->with('success', 'Supplier added successfully.');
    }

    public function show(Supplier $supplier)
    {
        $purchaseOrders = $supplier->purchaseOrders()->latest()->paginate(10, ['*'], 'po_page');
        $expenses = $supplier->expenses()->latest()->paginate(10, ['*'], 'exp_page');

        return view('accounting.suppliers.show', compact('supplier', 'purchaseOrders', 'expenses'));
    }

    public function edit(Supplier $supplier)
    {
        return view('accounting.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'pan_vat' => 'nullable|string|max:50',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($supplier, $validated) {
            $supplier->update($validated);
        });

        return redirect()->route('accounting.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        DB::transaction(function () use ($supplier) {
            $supplier->delete();
        });

        return redirect()->route('accounting.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
