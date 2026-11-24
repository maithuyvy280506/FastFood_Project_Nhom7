<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    private const SHIPPING_FEE = 30000;

    public function index(): View
    {
        $customer = User::query()->findOrFail((int) session('user_id'));

        return view('customer.cart.index', $this->cartPageData($customer));
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'food_id' => ['required', 'integer', 'exists:products,id_product'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
            'variants_selected' => ['sometimes', 'array'],
            'variants_selected.*' => ['string', 'max:255'],
            'variants' => ['sometimes', 'array'],
            'variants.*' => ['string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
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
            'variants_selected' => $validated['variants_selected'] ?? $validated['variants'] ?? [],
            'note' => $validated['note'] ?? null,
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

    public function update(Request $request, Cart $cart): JsonResponse|RedirectResponse
    {
        abort_unless($cart->id_user === (int) session('user_id'), 404);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart->update(['quantity' => $validated['quantity']]);

        if ($request->expectsJson()) {
            return response()->json($this->cartJsonState(
                User::query()->findOrFail((int) session('user_id')),
                $cart,
            ));
        }

        return back()->with('success', 'Đã cập nhật số lượng');
    }

    public function destroy(Request $request, Cart $cart): JsonResponse|RedirectResponse
    {
        abort_unless($cart->id_user === (int) session('user_id'), 404);
        $cart->delete();

        if ($request->expectsJson()) {
            return response()->json($this->cartJsonState(
                User::query()->findOrFail((int) session('user_id')),
            ));
        }

        return back()->with('success', 'Đã xóa món khỏi giỏ hàng');
    }

    public function applyPromotion(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'promotion_id' => ['nullable', 'integer', 'min:1'],
        ]);
        $customer = User::query()->findOrFail((int) session('user_id'));

        if (empty($validated['promotion_id'])) {
            session()->forget('cart_promotion_id');

            if ($request->expectsJson()) {
                return response()->json($this->cartJsonState($customer));
            }

            return back()->with('success', 'Đã bỏ áp dụng voucher');
        }

        $cartItems = Cart::query()
            ->with('product')
            ->where('id_user', $customer->id_user)
            ->get();

        if ($cartItems->isEmpty()) {
            return $this->promotionFailure($request, 'Giỏ hàng đang trống');
        }

        $cartSubtotal = $cartItems->sum(
            fn (Cart $item): int => (int) $item->product->price * $item->quantity
        );
        $promotion = Promotion::query()->find((int) $validated['promotion_id']);

        if (! $promotion) {
            return $this->promotionFailure($request, 'Voucher không còn khả dụng');
        }

        if (! in_array($promotion->code, $customer->saved_vouchers ?? [], true)) {
            return $this->promotionFailure($request, 'Voucher chưa được lưu vào Kho Voucher');
        }

        if (! $promotion->isAvailable()) {
            return $this->promotionFailure($request, 'Voucher không còn khả dụng');
        }

        if ($cartSubtotal < $promotion->min_order_value) {
            return $this->promotionFailure($request, 'Đơn hàng chưa đạt giá trị tối thiểu của voucher');
        }

        session(['cart_promotion_id' => $promotion->id_promotion]);

        if ($request->expectsJson()) {
            return response()->json($this->cartJsonState($customer));
        }

        return back()->with('success', 'Đã áp dụng voucher '.$promotion->code);
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
        $customer = User::query()->findOrFail((int) session('user_id'));
        $savedVoucherCodes = $customer->saved_vouchers ?? [];
        $availablePromotions = Promotion::query()
            ->whereIn('code', $savedVoucherCodes)
            ->orderBy('end_date')
            ->get()
            ->filter(fn (Promotion $promotion): bool => $promotion->isAvailable())
            ->values();
        $requestedPromotionId = request()->integer('promotion_id');

        if ($requestedPromotionId === 0) {
            $requestedPromotionId = (int) old('promotion_id', session('cart_promotion_id', 0));
        }

        $selectedPromotion = $availablePromotions->firstWhere('id_promotion', $requestedPromotionId);

        if ($selectedPromotion && $subtotal < $selectedPromotion->min_order_value) {
            $selectedPromotion = null;
        }

        $shippingFee = self::SHIPPING_FEE;
        $discountAmount = $selectedPromotion && $subtotal >= $selectedPromotion->min_order_value
            ? $selectedPromotion->discountAmount($subtotal, $shippingFee)
            : 0;
        $promotionError = match (true) {
            $requestedPromotionId === 0 => null,
            ! $selectedPromotion && $availablePromotions->contains('id_promotion', $requestedPromotionId) => 'Đơn hàng chưa đạt giá trị tối thiểu của voucher',
            ! $selectedPromotion => 'Voucher không còn khả dụng hoặc chưa được lưu vào Kho Voucher',
            default => null,
        };

        return view('customer.cart.checkout', compact(
            'cartItems',
            'customer',
            'subtotal',
            'availablePromotions',
            'selectedPromotion',
            'discountAmount',
            'promotionError',
            'shippingFee',
        ));
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'customer_address' => ['required', 'string', 'max:1000'],
            'method' => ['required', 'in:1,2'],
            'note' => ['nullable', 'string', 'max:1000'],
            'promotion_id' => ['nullable', 'integer', 'exists:promotions,id_promotion'],
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
            $customer = User::query()->findOrFail((int) session('user_id'));
            $promotion = null;

            if (! empty($validated['promotion_id'])) {
                $promotion = Promotion::query()
                    ->whereKey($validated['promotion_id'])
                    ->lockForUpdate()
                    ->first();

                if (
                    ! $promotion
                    || ! in_array($promotion->code, $customer->saved_vouchers ?? [], true)
                    || Order::query()
                        ->where('id_user', $customer->id_user)
                        ->where('id_promotion', $promotion->id_promotion)
                        ->exists()
                    || ! $promotion->isAvailable()
                    || $subtotal < $promotion->min_order_value
                ) {
                    throw ValidationException::withMessages([
                        'promotion_id' => 'Voucher không hợp lệ, đã hết hạn hoặc đơn hàng chưa đạt giá trị tối thiểu',
                    ]);
                }
            }

            $discountAmount = $promotion?->discountAmount($subtotal, $shippingFee) ?? 0;

            $order = Order::create([
                'id_user' => session('user_id'),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'id_promotion' => $promotion?->id_promotion,
                'discount_amount' => $discountAmount,
                'total_price' => max(0, $subtotal + $shippingFee - $discountAmount),
                'note' => $validated['note'] ?? null,
                'shipping_fee' => $shippingFee,
                'status' => Order::STATUS_PENDING,
                'method' => (int) $validated['method'],
            ]);

            foreach ($cartItems as $cartItem) {
                if (! $cartItem->product || ! $cartItem->product->is_active) {
                    throw new \RuntimeException('Một món trong giỏ không còn được bán');
                }

                $selectedVariants = $cartItem->variants_selected ?? [];

                if ($cartItem->note) {
                    $selectedVariants[] = 'Ghi chú: '.$cartItem->note;
                }

                OrderDetail::create([
                    'id_order' => $order->id_order,
                    'id_product' => $cartItem->id_product,
                    'quantity' => $cartItem->quantity,
                    'variants_selected' => $selectedVariants,
                    'price' => (int) $cartItem->product->price,
                ]);
            }

            if ($promotion) {
                $updatedPromotions = Promotion::query()
                    ->whereKey($promotion->getKey())
                    ->whereColumn('used', '<', 'quantity')
                    ->where('status', 1)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->increment('used');

                if ($updatedPromotions !== 1) {
                    throw ValidationException::withMessages([
                        'promotion_id' => 'Voucher vừa hết lượt sử dụng, vui lòng chọn mã khác',
                    ]);
                }

                $customer->saved_vouchers = array_values(array_filter(
                    $customer->saved_vouchers ?? [],
                    static fn (string $code): bool => $code !== $promotion->code,
                ));
                $customer->save();
            }

            Cart::query()->where('id_user', session('user_id'))->delete();

            return $order;
        });

        session()->forget('cart_promotion_id');

        return redirect()->route('customer.orders.show', $order)
            ->with('order_success', [
                'code' => 'DH'.str_pad((string) $order->id_order, 3, '0', STR_PAD_LEFT),
                'total' => number_format($order->total_price, 0, ',', '.').'đ',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function cartPageData(User $customer): array
    {
        $cartItems = Cart::query()
            ->with('product')
            ->where('id_user', $customer->id_user)
            ->get();
        $subtotal = $cartItems->sum(
            fn (Cart $item): int => (int) $item->product->price * $item->quantity
        );
        $savedVoucherCodes = $customer->saved_vouchers ?? [];
        $availablePromotions = empty($savedVoucherCodes)
            ? collect()
            : Promotion::query()
                ->whereIn('code', $savedVoucherCodes)
                ->orderBy('end_date')
                ->get()
                ->filter(fn (Promotion $promotion): bool => $promotion->isAvailable())
                ->values();
        $selectedPromotionId = (int) session('cart_promotion_id', 0);
        $selectedPromotion = $selectedPromotionId === 0
            ? null
            : $availablePromotions->firstWhere('id_promotion', $selectedPromotionId);
        $promotionError = null;

        if ($selectedPromotionId !== 0 && ! $selectedPromotion) {
            session()->forget('cart_promotion_id');
            $promotionError = 'Voucher không còn khả dụng';
        } elseif ($selectedPromotion && $cartItems->isEmpty()) {
            session()->forget('cart_promotion_id');
            $selectedPromotion = null;
        } elseif ($selectedPromotion && $subtotal < $selectedPromotion->min_order_value) {
            $promotionError = 'Đơn hàng chưa đạt giá trị tối thiểu của voucher';
        }

        $shippingFee = self::SHIPPING_FEE;
        $discountAmount = $selectedPromotion && ! $promotionError
            ? $selectedPromotion->discountAmount($subtotal, $shippingFee)
            : 0;

        return [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'cartCount' => $cartItems->sum('quantity'),
            'availablePromotions' => $availablePromotions,
            'selectedPromotion' => $selectedPromotion,
            'promotionError' => $promotionError,
            'shippingFee' => $shippingFee,
            'discountAmount' => $discountAmount,
            'total' => max(0, $subtotal + $shippingFee - $discountAmount),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cartJsonState(User $customer, ?Cart $cartItem = null): array
    {
        $cartData = $this->cartPageData($customer);
        $cartItem?->loadMissing('product');

        return [
            'success' => true,
            'cartCount' => $cartData['cartCount'],
            'subtotal' => $cartData['subtotal'],
            'discountAmount' => $cartData['discountAmount'],
            'shippingFee' => $cartData['shippingFee'],
            'total' => $cartData['total'],
            'selectedPromotionId' => $cartData['selectedPromotion']?->id_promotion,
            'selectedPromotionCode' => $cartData['selectedPromotion']?->code,
            'promotionError' => $cartData['promotionError'],
            'isEmpty' => $cartData['cartItems']->isEmpty(),
            'quantity' => $cartItem?->quantity,
            'itemSubtotal' => $cartItem ? (int) $cartItem->product->price * $cartItem->quantity : null,
        ];
    }

    private function promotionFailure(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        return back()->with('error', $message);
    }
}
