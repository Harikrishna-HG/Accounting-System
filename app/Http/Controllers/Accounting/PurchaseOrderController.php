<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $orders = $query->latest()->paginate(10);

        return view('accounting.purchase_orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();

        return view('accounting.purchase_orders.create', compact('suppliers', 'products'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items', 'supplier');

        return view('accounting.purchase_orders.show', compact('purchaseOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_name' => 'required_without:supplier_id|string|max:255',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $orderNumber = PurchaseOrder::generateOrderNumber();
        $subtotal = 0;
        $itemsData = [];
        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['price'];
            $subtotal += $total;
            $itemsData[] = $item + ['total' => $total];
        }

        DB::transaction(function () use ($validated, $subtotal, $itemsData, $orderNumber) {
            $order = PurchaseOrder::create([
                'order_number' => $orderNumber,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'supplier_name' => $validated['supplier_name'],
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }
        });

        return redirect()->route('accounting.purchase-orders.index')
            ->with('success', "Purchase Order {$orderNumber} created successfully.");
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        $purchaseOrder->load('items');

        return view('accounting.purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_name' => 'required|string|max:255',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'status' => 'nullable|in:pending,approved,received,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        $itemsData = [];
        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['price'];
            $subtotal += $total;
            $itemsData[] = $item + ['total' => $total];
        }

        DB::transaction(function () use ($validated, $subtotal, $itemsData, $purchaseOrder) {
            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'] ?? null,
                'supplier_name' => $validated['supplier_name'],
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'status' => $validated['status'] ?? $purchaseOrder->status,
                'notes' => $validated['notes'] ?? null,
            ]);

            $purchaseOrder->items()->delete();
            foreach ($itemsData as $item) {
                $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }
        });

        return redirect()->route('accounting.purchase-orders.index')
            ->with('success', 'Purchase Order updated successfully.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
        });

        return redirect()->route('accounting.purchase-orders.index')
            ->with('success', 'Purchase Order deleted successfully.');
    }
}
