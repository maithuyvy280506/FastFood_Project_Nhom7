<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPageController extends Controller
{
    public function dashboard(Request $request): View
    {
        $to = CarbonImmutable::today();
        $from = $to->subDays(6);

        $stats = $this->getDashboardStats($from, $to);

        $pendingOrders = Order::query()
            ->where('status', Order::STATUS_PENDING)
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(fn (Order $order): array => [
                'code' => 'DH'.str_pad((string) $order->id_order, 3, '0', STR_PAD_LEFT),
                'createdAt' => $order->created_at?->format('d/m H:i') ?? '',
            ])
            ->all();

        return view('admin.dashboard', [
            'pendingOrders' => $pendingOrders,
            'topCustomers' => $stats['topCustomers'],
            'topFoods' => $stats['topFoods'],
            'stats' => $stats,
            'dateFrom' => $from->toDateString(),
            'dateTo' => $to->toDateString(),
        ]);
    }

    public function dashboardStats(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $from = CarbonImmutable::parse($validated['from'])->startOfDay();
        $to = CarbonImmutable::parse($validated['to'])->endOfDay();

        if ($from->diffInDays($to) > 365) {
            return response()->json([
                'success' => false,
                'message' => 'Khoảng thời gian lọc tối đa là 365 ngày',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'stats' => $this->getDashboardStats($from, $to),
        ]);
    }

    /**
     * @return array{
     *   revenue: int,
     *   pending: int,
     *   total: int,
     *   delivered: int,
     *   cancelled: int,
     *   cancellationRate: float,
     *   daily: array<int, array{label: string, date: string, count: int}>,
     *   topCustomers: array<int, array{name: string, orders: int, spent: string}>,
     *   topFoods: array<int, array{name: string, quantity: int}>
     * }
     */
    private function getDashboardStats(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $baseQuery = Order::query()
            ->whereBetween('created_at', [$from, $to]);

        $revenue = (int) (clone $baseQuery)
            ->where('status', Order::STATUS_DELIVERED)
            ->sum('total_price');
        $pending = (clone $baseQuery)->where('status', Order::STATUS_PENDING)->count();
        $total = (clone $baseQuery)->count();
        $delivered = (clone $baseQuery)->where('status', Order::STATUS_DELIVERED)->count();
        $cancelled = (clone $baseQuery)->where('status', Order::STATUS_CANCELLED)->count();
        $cancellationRate = $total > 0 ? round(($cancelled * 100) / $total, 1) : 0.0;

        $groupedCounts = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as order_date, COUNT(*) as order_count')
            ->groupByRaw('DATE(created_at)')
            ->pluck('order_count', 'order_date')
            ->map(fn ($count): int => (int) $count)
            ->all();

        $daily = [];
        $cursor = $from->startOfDay();
        $lastDay = $to->startOfDay();
        while ($cursor->lte($lastDay)) {
            $date = $cursor->toDateString();
            $daily[] = [
                'label' => $cursor->format('d/m'),
                'date' => $date,
                'count' => $groupedCounts[$date] ?? 0,
            ];
            $cursor = $cursor->addDay();
        }

        $topCustomers = (clone $baseQuery)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->selectRaw('customer_name, COUNT(*) as order_count, SUM(total_price) as total_spent')
            ->groupBy('customer_name')
            ->orderByDesc('total_spent')
            ->limit(3)
            ->get()
            ->map(fn ($row): array => [
                'name' => $row->customer_name,
                'orders' => (int) $row->order_count,
                'spent' => number_format((int) $row->total_spent, 0, ',', '.').' đ',
            ])
            ->all();

        $topFoods = DB::table('order_details')
            ->join('orders', 'orders.id_order', '=', 'order_details.id_order')
            ->join('products', 'products.id_product', '=', 'order_details.id_product')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where('orders.status', '!=', Order::STATUS_CANCELLED)
            ->selectRaw('products.name, SUM(order_details.quantity) as total_quantity')
            ->groupBy('products.id_product', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(3)
            ->get()
            ->map(fn ($row): array => [
                'name' => $row->name,
                'quantity' => (int) $row->total_quantity,
            ])
            ->all();

        return [
            'revenue' => $revenue,
            'pending' => $pending,
            'total' => $total,
            'delivered' => $delivered,
            'cancelled' => $cancelled,
            'cancellationRate' => $cancellationRate,
            'daily' => $daily,
            'topCustomers' => $topCustomers,
            'topFoods' => $topFoods,
        ];
    }

    public function orders(): View
    {
        $orders = Order::query()
            ->with('user')
            ->latest('created_at')
            ->paginate(15)
            ->through(fn (Order $order): array => [
                'id' => $order->id_order,
                'code' => 'DH'.str_pad((string) $order->id_order, 3, '0', STR_PAD_LEFT),
                'date' => $order->created_at?->format('d/m/Y H:i') ?? '',
                'customer' => $order->customer_name,
                'payment' => $order->method === 2 ? 'Chuyển khoản' : 'Tiền mặt',
                'updated' => $order->updated_at?->format('d/m/Y H:i') ?? '',
                'total' => number_format($order->total_price, 0, ',', '.').' đ',
                'status' => $order->statusLabel(),
                'statusCode' => $order->status,
            ]);

        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder(Order $order): View
    {
        $order->load(['details.product', 'user']);

        $items = $order->details->map(fn ($detail): array => [
            'name' => $detail->product->name,
            'quantity' => $detail->quantity,
            'price' => number_format($detail->price * $detail->quantity, 0, ',', '.').' đ',
            'image' => $detail->product->image,
        ]);

        return view('admin.orders.show', [
            'order' => $order,
            'orderCode' => 'DH'.str_pad((string) $order->id_order, 3, '0', STR_PAD_LEFT),
            'items' => $items,
        ]);
    }

    public function updateOrderStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'integer', 'in:0,1,2,3,4'],
        ]);

        $order->update(['status' => (int) $validated['status']]);

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'statusLabel' => $order->statusLabel(),
        ]);
    }

    public function customers(): View
    {
        $customers = User::query()
            ->where('role', 0)
            ->orderBy('id_user')
            ->get();

        return view('admin.customers.index', compact('customers'));
    }
}
