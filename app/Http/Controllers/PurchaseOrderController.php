<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): View
    {
        $purchaseOrders = PurchaseOrder::with([
            'requestedBy:id,name',
            'workOrder:id,code,title',
        ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('supplier_name', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->priority, fn ($q, $p) => $q->where('priority', $p))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => PurchaseOrder::count(),
            'draft' => PurchaseOrder::where('status', PurchaseOrderStatus::Draft)->count(),
            'pending_approval' => PurchaseOrder::where('status', PurchaseOrderStatus::PendingApproval)->count(),
            'ordered' => PurchaseOrder::where('status', PurchaseOrderStatus::Ordered)->count(),
            'received' => PurchaseOrder::where('status', PurchaseOrderStatus::Received)->count(),
        ];

        return view('purchase-orders.index', [
            'purchaseOrders' => $purchaseOrders,
            'filters' => $request->only(['search', 'status', 'priority']),
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        $workOrders = WorkOrder::select('id', 'code', 'title')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return view('purchase-orders.create', [
            'workOrders' => $workOrders,
        ]);
    }

    public function store(StorePurchaseOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $items = $data['items'];
        unset($data['items']);

        $data['tenant_id'] = Auth::user()->tenant_id;
        $data['requested_by'] = Auth::id();
        $data['status'] = PurchaseOrderStatus::Draft;
        $data['code'] = PurchaseOrder::generateCode();
        $data['currency'] = $data['currency'] ?? 'MXN';

        $purchaseOrder = DB::transaction(function () use ($data, $items) {
            $order = PurchaseOrder::create($data);

            $totalAmount = 0;

            foreach ($items as $item) {
                $totalPrice = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
                $totalAmount += $totalPrice;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'description' => $item['description'],
                    'part_number' => $item['part_number'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);

            return $order;
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Orden de compra creada correctamente.');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load([
            'items.part:id,name,unit',
            'workOrder:id,code,title',
            'requestedBy:id,name',
            'approvedBy:id,name',
        ]);

        return view('purchase-orders.show', [
            'purchaseOrder' => $purchaseOrder,
        ]);
    }

    public function edit(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load('items');

        $workOrders = WorkOrder::select('id', 'code', 'title')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return view('purchase-orders.edit', [
            'purchaseOrder' => $purchaseOrder,
            'workOrders' => $workOrders,
        ]);
    }

    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $data = $request->validated();
        $items = $data['items'];
        unset($data['items']);

        $data['currency'] = $data['currency'] ?? 'MXN';

        DB::transaction(function () use ($purchaseOrder, $data, $items) {
            $purchaseOrder->update($data);

            $purchaseOrder->items()->delete();

            $totalAmount = 0;

            foreach ($items as $item) {
                $totalPrice = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
                $totalAmount += $totalPrice;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'description' => $item['description'],
                    'part_number' => $item['part_number'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                ]);
            }

            $purchaseOrder->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Orden de compra actualizada correctamente.');
    }
}
