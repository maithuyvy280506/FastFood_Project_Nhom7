<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CustomerOrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->withCount('details')
            ->where('id_user', session('user_id'))
            ->latest('created_at')
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        abort_unless($order->id_user === (int) session('user_id'), 404);

        $order->load(['details.product']);

        return view('customer.orders.show', compact('order'));
    }

    public function status(Order $order): JsonResponse
    {
        abort_unless($order->id_user === (int) session('user_id'), 404);

        return response()->json([
            'status' => $order->status,
            'statusLabel' => $order->statusLabel(),
            'canBeCancelled' => $order->canBeCancelled(),
            'updatedAt' => $order->updated_at?->toIso8601String(),
        ]);
    }

    public function cancel(Order $order): RedirectResponse
    {
        abort_unless($order->id_user === (int) session('user_id'), 404);

        if (! $order->canBeCancelled()) {
            return back()->with('error', 'Đơn hàng không còn ở trạng thái chờ xác nhận');
        }

        $order->update(['status' => Order::STATUS_CANCELLED]);

        return back()->with('success', 'Đơn hàng đã được hủy');
    }
}
