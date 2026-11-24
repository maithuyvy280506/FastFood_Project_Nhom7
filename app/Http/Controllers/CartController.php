<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(): View
    {
        $cartItems = Cart::query()
            ->with('product')
            ->where('id_user', session('user_id'))
            ->get();

        $subtotal = $cartItems->sum(fn (Cart $item): int => (int) $item->product->price * $item->quantity);

        return view('customer.cart.index', compact('cartItems', 'subtotal'));
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'food_id' => ['required', 'integer', 'exists:products,id_product'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
            'variants' => ['sometimes', 'array'],
            'variants.*' => ['string', 'max:255'],
        ]);

        $product = Product::query()
            ->whereKey($validated['food_id'])
            ->where('is_active', 1)
            ->first();

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'Món ăn không còn được bán'], 422);
        }

        Cart::create([
            'id_user' => session('user_id'),
            'id_product' => $product->id_product,
            'quantity' => $validated['quantity'] ?? 1,
            'variants_selected' => $validated['variants'] ?? [],
        ]);

        $totalCount = Cart::query()
            ->where('id_user', session('user_id'))
            ->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm món vào giỏ hàng',
            'totalCount' => (int) $totalCount,
        ]);
    }

    public function count(): JsonResponse
    {
        $totalCount = Cart::query()
            ->where('id_user', session('user_id'))
            ->sum('quantity');

        return response()->json(['success' => true, 'totalCount' => (int) $totalCount]);
    }

    public function update(Request $request, Cart $cart): RedirectResponse
    {
        abort_unless($cart->id_user === (int) session('user_id'), 404);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart->update(['quantity' => $validated['quantity']]);

        return back()->with('success', 'Đã cập nhật số lượng');
    }

    public function destroy(Cart $cart): RedirectResponse
    {
        abort_unless($cart->id_user === (int) session('user_id'), 404);
        $cart->delete();

        return back()->with('success', 'Đã xóa món khỏi giỏ hàng');
    }

    public function checkout(): View|RedirectResponse
    {
        $cartItems = Cart::query()
            ->with('product')
            ->where('id_user', session('user_id'))
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống');
        }

        $subtotal = $cartItems->sum(fn (Cart $item): int => (int) $item->product->price * $item->quantity);

        return view('customer.cart.checkout', compact('cartItems', 'subtotal'));
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_address' => ['required', 'string', 'max:1000'],
            'method' => ['required', 'in:1,2'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $cartItems = Cart::query()
            ->with('product')
            ->where('id_user', session('user_id'))
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống');
        }

        $shippingFee = 30000;
        $subtotal = $cartItems->sum(fn (Cart $item): int => (int) $item->product->price * $item->quantity);

        $order = DB::transaction(function () use ($validated, $cartItems, $subtotal, $shippingFee): Order {
            $order = Order::create([
                'id_user' => session('user_id'),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'discount_amount' => 0,
                'total_price' => $subtotal + $shippingFee,
                'note' => $validated['note'] ?? null,
                'shipping_fee' => $shippingFee,
                'status' => Order::STATUS_PENDING,
                'method' => (int) $validated['method'],
            ]);

            foreach ($cartItems as $cartItem) {
                if (! $cartItem->product || ! $cartItem->product->is_active) {
                    throw new \RuntimeException('Một món trong giỏ không còn được bán');
                }

                OrderDetail::create([
                    'id_order' => $order->id_order,
                    'id_product' => $cartItem->id_product,
                    'quantity' => $cartItem->quantity,
                    'variants_selected' => $cartItem->variants_selected ?? [],
                    'price' => (int) $cartItem->product->price,
                ]);
            }

            Cart::query()->where('id_user', session('user_id'))->delete();

            return $order;
        });

        return redirect()->route('customer.orders.show', $order)
            ->with('order_success', [
                'code' => 'DH'.str_pad((string) $order->id_order, 3, '0', STR_PAD_LEFT),
                'total' => number_format($order->total_price, 0, ',', '.').'đ',
            ]);
    }
}
