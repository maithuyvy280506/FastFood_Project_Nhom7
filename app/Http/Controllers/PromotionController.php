<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index(): View
    {
        $customer = User::query()->findOrFail((int) session('user_id'));
        $savedVoucherCodes = $customer->saved_vouchers ?? [];
        $usedPromotionIds = Order::query()
            ->where('id_user', $customer->id_user)
            ->whereNotNull('id_promotion')
            ->pluck('id_promotion')
            ->map(static fn ($id): int => (int) $id)
            ->all();
        $promotions = Promotion::query()
            ->orderByDesc('start_date')
            ->get();

        return view('customer.promotions.index', compact('promotions', 'savedVoucherCodes', 'usedPromotionIds'));
    }

    public function store(Promotion $promotion): RedirectResponse
    {
        if (! $promotion->isAvailable()) {
            return back()->with('error', 'Voucher này hiện không còn sử dụng được');
        }

        $customerId = (int) session('user_id');
        $canSaveVoucher = DB::transaction(function () use ($promotion, $customerId): bool {
            $customer = User::query()
                ->lockForUpdate()
                ->findOrFail($customerId);

            if (Order::query()
                ->where('id_user', $customer->id_user)
                ->where('id_promotion', $promotion->id_promotion)
                ->exists()) {
                return false;
            }

            $savedVoucherCodes = array_values(array_unique($customer->saved_vouchers ?? []));

            if (! in_array($promotion->code, $savedVoucherCodes, true)) {
                $savedVoucherCodes[] = $promotion->code;
            }

            if ($savedVoucherCodes !== ($customer->saved_vouchers ?? [])) {
                $customer->saved_vouchers = $savedVoucherCodes;
                $customer->save();
            }

            return true;
        });

        if (! $canSaveVoucher) {
            return back()->with('error', 'Bạn đã sử dụng voucher này trước đó');
        }

        $cartItems = Cart::query()
            ->with('product')
            ->where('id_user', $customerId)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect('/foods')->with('success', 'Đã lưu voucher. Hãy chọn món để sử dụng');
        }

        $subtotal = $cartItems->sum(
            fn (Cart $item): int => (int) $item->product->price * $item->quantity
        );

        if ($subtotal < $promotion->min_order_value) {
            return redirect()->route('cart.index')
                ->with('success', 'Đã lưu voucher. Hãy thêm món để đạt giá trị đơn tối thiểu');
        }

        return redirect()->route('cart.checkout', ['promotion_id' => $promotion->id_promotion])
            ->with('success', 'Đã lưu voucher. Kiểm tra mã tại trang thanh toán');
    }
}
