@extends('layouts.app')

@section('content')
@php $title = 'Giỏ hàng'; @endphp

<style>
    .cart-page {
        max-width: 1050px;
        margin: 24px auto;
        padding: 0 20px;
    }

    .cart-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        align-items: start;
        gap: 20px;
        margin-top: 18px;
    }

    .cart-item,
    .cart-summary {
        background: #fff;
        border: 1px solid #ffe8d6;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(231, 76, 60, .06);
    }

    .cart-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        margin-bottom: 12px;
    }

    .cart-item-image {
        width: 84px;
        height: 84px;
        flex: 0 0 84px;
        object-fit: cover;
        border-radius: 9px;
    }

    .cart-item-details {
        flex: 1;
        min-width: 0;
    }

    .cart-item-price {
        color: #e74c3c;
        font-weight: 700;
        margin-top: 5px;
    }

    .cart-quantity-form,
    .cart-quantity-control,
    .cart-item-actions {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .cart-quantity-form {
        margin-top: 9px;
        flex-wrap: wrap;
    }

    .cart-quantity-control button,
    .cart-quantity-control input,
    .cart-update-button,
    .cart-remove-button,
    .promotion-open-button,
    .checkout-button {
        border-radius: 7px;
        font: inherit;
    }

    .cart-quantity-control button {
        width: 32px;
        height: 32px;
        border: 1px solid #ddd;
        background: #fff;
        cursor: pointer;
    }

    .cart-quantity-control input {
        width: 54px;
        height: 32px;
        border: 1px solid #ddd;
        text-align: center;
    }

    .cart-update-button {
        padding: 7px 10px;
        border: 1px solid #ddd;
        background: #fff;
        cursor: pointer;
    }

    .cart-remove-button {
        padding: 8px 10px;
        border: 0;
        background: #fff0ed;
        color: #c0392b;
        cursor: pointer;
    }

    .cart-summary {
        padding: 18px;
    }

    .cart-summary h2 {
        margin: 0 0 14px;
        font-size: 19px;
    }

    .cart-summary-line {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 9px;
    }

    .cart-total {
        padding-top: 12px;
        margin-top: 12px;
        border-top: 1px dashed #ddd;
        color: #e74c3c;
        font-size: 18px;
        font-weight: 800;
    }

    .promotion-open-button,
    .checkout-button {
        display: block;
        width: 100%;
        padding: 11px 14px;
        font-weight: 700;
        text-align: center;
        cursor: pointer;
    }

    .promotion-open-button {
        margin-top: 14px;
        border: 1px solid #e74c3c;
        background: #fff;
        color: #e74c3c;
    }

    .checkout-button {
        margin-top: 12px;
        border: 0;
        background: #e74c3c;
        color: #fff;
        text-decoration: none;
    }

    .cart-message {
        padding: 11px 13px;
        margin: 12px 0;
        border-radius: 8px;
        background: #eafaf1;
        color: #196f3d;
    }

    .cart-message.error {
        background: #fdecea;
        color: #922b21;
    }

    .promotion-modal {
        position: fixed;
        z-index: 2000;
        inset: 0;
        display: grid;
        place-items: center;
        padding: 18px;
        background: rgba(30, 30, 30, .58);
    }

    .promotion-modal[hidden] {
        display: none;
    }

    .promotion-dialog {
        width: min(100%, 520px);
        max-height: min(80vh, 680px);
        overflow-y: auto;
        padding: 20px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 16px 50px rgba(0, 0, 0, .24);
    }

    .promotion-dialog-header,
    .promotion-option {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .promotion-dialog-header {
        margin-bottom: 14px;
    }

    .promotion-dialog-header h2 {
        margin: 0;
        color: #e74c3c;
        font-size: 20px;
    }

    .promotion-close-button {
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 50%;
        background: #f5f5f5;
        font-size: 20px;
        cursor: pointer;
    }

    .promotion-option {
        padding: 12px;
        margin-top: 9px;
        border: 1px solid #ffe0cc;
        border-radius: 9px;
    }

    .promotion-option button,
    .promotion-clear-button {
        flex: 0 0 auto;
        padding: 8px 10px;
        border: 0;
        border-radius: 7px;
        background: #e74c3c;
        color: #fff;
        font-weight: 700;
        cursor: pointer;
    }

    .promotion-option button:disabled,
    .cart-item button:disabled {
        opacity: .55;
        cursor: not-allowed;
    }

    .promotion-option small {
        display: block;
        margin-top: 4px;
        color: #666;
    }

    .promotion-clear-button {
        width: 100%;
        margin-top: 14px;
        border: 1px solid #ddd;
        background: #fff;
        color: #555;
    }

    @media (max-width: 760px) {
        .cart-layout {
            grid-template-columns: 1fr;
        }

        .cart-summary {
            order: -1;
        }
    }

    @media (max-width: 480px) {
        .cart-page {
            padding: 0 12px;
        }

        .cart-item {
            align-items: flex-start;
            gap: 10px;
            padding: 10px;
        }

        .cart-item-image {
            width: 64px;
            height: 64px;
            flex-basis: 64px;
        }

        .cart-item-actions {
            flex-direction: column;
        }
    }
</style>

<main
    class="cart-page"
    id="cartPage"
    data-csrf-token="{{ csrf_token() }}"
    data-apply-promotion-url="{{ route('cart.promotion.apply') }}"
>
    <h1 style="color:#e74c3c;">Giỏ hàng</h1>

    <div id="cartMessage" class="cart-message" role="status" aria-live="polite" hidden></div>
    @if (session('success'))
        <div class="cart-message">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="cart-message error">{{ session('error') }}</div>
    @endif

    <div id="cartEmptyState" style="text-align:center;padding:50px;" @if ($cartItems->isNotEmpty()) hidden @endif>
        Giỏ hàng trống
        <div style="margin-top:12px;"><a href="{{ url('/foods') }}">Tiếp tục chọn món</a></div>
    </div>

    <section id="cartContent" @if ($cartItems->isEmpty()) hidden @endif>
        <div class="cart-layout">
            <div id="cartItems">
                @foreach ($cartItems as $item)
                    <article
                        class="cart-item"
                        data-cart-item
                        data-cart-id="{{ $item->id_cart }}"
                    >
                        <img class="cart-item-image" src="{{ $item->product->image }}" alt="{{ $item->product->name }}">
                        <div class="cart-item-details">
                            <strong>{{ $item->product->name }}</strong>
                            @if (! empty($item->variants_selected))
                                <div style="margin-top:4px;color:#777;font-size:12px;">
                                    {{ implode(', ', $item->variants_selected) }}
                                </div>
                            @endif
                            @if ($item->note)
                                <div style="margin-top:4px;color:#777;font-size:12px;">Ghi chú: {{ $item->note }}</div>
                            @endif
                            <div class="cart-item-price">{{ number_format($item->product->price, 0, ',', '.') }}đ / món</div>

                            <form
                                method="POST"
                                action="{{ route('cart.update', $item) }}"
                                class="cart-quantity-form"
                                data-cart-update-form
                            >
                                @csrf
                                @method('PATCH')
                                <div class="cart-quantity-control">
                                    <button type="button" data-quantity-step="-1" aria-label="Giảm số lượng">−</button>
                                    <input
                                        type="number"
                                        name="quantity"
                                        value="{{ $item->quantity }}"
                                        min="1"
                                        max="99"
                                        required
                                        data-quantity-field
                                        aria-label="Số lượng {{ $item->product->name }}"
                                    >
                                    <button type="button" data-quantity-step="1" aria-label="Tăng số lượng">+</button>
                                </div>
                                <button class="cart-update-button" type="submit">Cập nhật</button>
                            </form>
                        </div>
                        <div class="cart-item-actions">
                            <strong data-line-total>{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}đ</strong>
                            <form method="POST" action="{{ route('cart.destroy', $item) }}" data-cart-remove-form>
                                @csrf
                                @method('DELETE')
                                <button class="cart-remove-button" type="submit" aria-label="Xóa {{ $item->product->name }} khỏi giỏ">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <aside class="cart-summary" aria-label="Tóm tắt giỏ hàng">
                <h2>Tóm tắt đơn hàng</h2>
                <div class="cart-summary-line">
                    <span>Tạm tính</span>
                    <strong id="cartSubtotal">{{ number_format($subtotal, 0, ',', '.') }}đ</strong>
                </div>
                <div class="cart-summary-line">
                    <span>Phí giao hàng dự kiến</span>
                    <strong id="cartShipping">{{ number_format($shippingFee, 0, ',', '.') }}đ</strong>
                </div>
                <div class="cart-summary-line" style="color:#196f3d;">
                    <span>Giảm giá</span>
                    <strong id="cartDiscount">-{{ number_format($discountAmount, 0, ',', '.') }}đ</strong>
                </div>
                <div class="cart-summary-line cart-total">
                    <span>Tổng dự kiến</span>
                    <strong id="cartTotal">{{ number_format($total, 0, ',', '.') }}đ</strong>
                </div>

                <div id="selectedPromotionSummary" style="margin-top:10px;color:#196f3d;" @if (! $selectedPromotion) hidden @endif>
                    @if ($selectedPromotion)
                        Đang chọn mã <strong id="selectedPromotionCode">{{ $selectedPromotion->code }}</strong>
                    @endif
                </div>
                <div id="cartPromotionMessage" style="margin-top:7px;color:#c0392b;font-size:13px;">
                    {{ $promotionError }}
                </div>
                <button class="promotion-open-button" id="openPromotionModal" type="button">Chọn mã khuyến mãi</button>
                <a
                    class="checkout-button"
                    id="checkoutButton"
                    href="{{ route('cart.checkout') }}"
                >
                    Tiến hành đặt hàng
                </a>
            </aside>
        </div>
    </section>
</main>

<div class="promotion-modal" id="promotionModal" role="presentation" aria-hidden="true" hidden>
    <section class="promotion-dialog" role="dialog" aria-modal="true" aria-labelledby="promotionModalTitle" tabindex="-1">
        <div class="promotion-dialog-header">
            <h2 id="promotionModalTitle">Chọn mã khuyến mãi</h2>
            <button type="button" class="promotion-close-button" data-close-promotion-modal aria-label="Đóng">×</button>
        </div>

        @forelse ($availablePromotions as $promotion)
            <div class="promotion-option">
                <div>
                    <strong>{{ $promotion->code }} - {{ $promotion->name }}</strong>
                    <small>Đơn tối thiểu {{ number_format($promotion->min_order_value, 0, ',', '.') }}đ</small>
                </div>
                <button
                    type="button"
                    data-apply-promotion="{{ $promotion->id_promotion }}"
                    data-promotion-minimum="{{ $promotion->min_order_value }}"
                    @disabled($subtotal < $promotion->min_order_value)
                >
                    @if ($selectedPromotion?->id_promotion === $promotion->id_promotion)
                        Đang chọn
                    @elseif ($subtotal < $promotion->min_order_value)
                        Chưa đủ đơn
                    @else
                        Áp dụng
                    @endif
                </button>
            </div>
        @empty
            <p style="color:#666;">Bạn chưa có mã khuyến mãi khả dụng.</p>
            <a href="{{ route('promotions.index') }}">Đến Kho Voucher</a>
        @endforelse

        @if ($selectedPromotion)
            <button class="promotion-clear-button" type="button" data-clear-promotion>Bỏ áp dụng mã</button>
        @endif
    </section>
</div>

<script>
    (() => {
        const page = document.getElementById('cartPage');
        const modal = document.getElementById('promotionModal');
        const message = document.getElementById('cartMessage');
        const currency = new Intl.NumberFormat('vi-VN');
        let currentSubtotal = {{ $subtotal }};

        function formatMoney(amount) {
            return `${currency.format(amount)}đ`;
        }

        function showMessage(text, isError = false) {
            message.textContent = text;
            message.classList.toggle('error', isError);
            message.hidden = !text;
        }

        async function sendCartRequest(url, method, data = null) {
            const options = {
                method,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': page.dataset.csrfToken
                }
            };

            if (data !== null) {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(url, options);
            let result;

            try {
                result = await response.json();
            } catch {
                throw new Error('Không thể đọc phản hồi từ máy chủ');
            }

            if (!response.ok || result.success === false) {
                const validationMessage = result.errors
                    ? Object.values(result.errors).flat()[0]
                    : null;
                throw new Error(result.message || validationMessage || 'Không thể cập nhật giỏ hàng');
            }

            return result;
        }

        function updateSummary(result) {
            currentSubtotal = result.subtotal;
            document.getElementById('cartSubtotal').textContent = formatMoney(result.subtotal);
            document.getElementById('cartShipping').textContent = formatMoney(result.shippingFee);
            document.getElementById('cartDiscount').textContent = `-${formatMoney(result.discountAmount)}`;
            document.getElementById('cartTotal').textContent = formatMoney(result.total);

            ['cart-count', 'cartCountBadge'].forEach(id => {
                const badge = document.getElementById(id);
                if (badge) {
                    badge.textContent = result.cartCount;
                }
            });

            const selectedSummary = document.getElementById('selectedPromotionSummary');
            const selectedCode = document.getElementById('selectedPromotionCode');
            selectedSummary.hidden = !result.selectedPromotionCode;
            if (result.selectedPromotionCode) {
                if (selectedCode) {
                    selectedCode.textContent = result.selectedPromotionCode;
                } else {
                    selectedSummary.innerHTML = 'Đang chọn mã <strong id="selectedPromotionCode"></strong>';
                    document.getElementById('selectedPromotionCode').textContent = result.selectedPromotionCode;
                }
            }

            document.getElementById('cartPromotionMessage').textContent = result.promotionError || '';
            modal.querySelectorAll('[data-apply-promotion]').forEach(button => {
                const isSelected = Number(button.dataset.applyPromotion) === Number(result.selectedPromotionId);
                button.disabled = result.subtotal < Number(button.dataset.promotionMinimum);
                button.textContent = isSelected
                    ? 'Đang chọn'
                    : (button.disabled ? 'Chưa đủ đơn' : 'Áp dụng');
            });
            document.getElementById('cartContent').hidden = result.isEmpty;
            document.getElementById('cartEmptyState').hidden = !result.isEmpty;
        }

        function openModal() {
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
            modal.querySelector('[data-close-promotion-modal]').focus();
        }

        function closeModal() {
            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
            document.getElementById('openPromotionModal').focus();
        }

        async function updateQuantity(form, quantity) {
            const buttonElements = form.querySelectorAll('button');
            buttonElements.forEach(button => {
                button.disabled = true;
            });

            try {
                const result = await sendCartRequest(form.action, 'PATCH', { quantity });
                form.querySelector('[data-quantity-field]').value = result.quantity;
                form.closest('[data-cart-item]').querySelector('[data-line-total]').textContent = formatMoney(result.itemSubtotal);
                updateSummary(result);
                showMessage(result.promotionError || 'Đã cập nhật số lượng');
            } catch (error) {
                showMessage(error.message, true);
            } finally {
                buttonElements.forEach(button => {
                    button.disabled = false;
                });
            }
        }

        async function removeItem(form) {
            const button = form.querySelector('button');
            button.disabled = true;

            try {
                const result = await sendCartRequest(form.action, 'DELETE');
                form.closest('[data-cart-item]').remove();
                updateSummary(result);
                showMessage('Đã xóa món khỏi giỏ hàng');
            } catch (error) {
                button.disabled = false;
                showMessage(error.message, true);
            }
        }

        async function applyPromotion(promotionId) {
            const buttons = modal.querySelectorAll('[data-apply-promotion], [data-clear-promotion]');
            buttons.forEach(button => {
                button.disabled = true;
            });

            try {
                const result = await sendCartRequest(page.dataset.applyPromotionUrl, 'POST', {
                    promotion_id: promotionId
                });
                updateSummary(result);
                closeModal();
                showMessage(result.selectedPromotionCode
                    ? `Đã áp dụng mã ${result.selectedPromotionCode}`
                    : 'Đã bỏ áp dụng voucher');
            } catch (error) {
                showMessage(error.message, true);
            } finally {
                buttons.forEach(button => {
                    button.disabled = button.dataset.promotionMinimum
                        ? currentSubtotal < Number(button.dataset.promotionMinimum)
                        : false;
                });
            }
        }

        document.getElementById('openPromotionModal').addEventListener('click', openModal);
        modal.addEventListener('click', event => {
            if (event.target === modal || event.target.closest('[data-close-promotion-modal]')) {
                closeModal();
            }

            const applyButton = event.target.closest('[data-apply-promotion]');
            if (applyButton) {
                applyPromotion(Number.parseInt(applyButton.dataset.applyPromotion, 10));
            }

            if (event.target.closest('[data-clear-promotion]')) {
                applyPromotion(null);
            }
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });

        document.querySelectorAll('[data-cart-update-form]').forEach(form => {
            form.addEventListener('submit', event => {
                event.preventDefault();
                updateQuantity(form, Number.parseInt(form.querySelector('[data-quantity-field]').value, 10));
            });
        });

        document.querySelectorAll('[data-cart-remove-form]').forEach(form => {
            form.addEventListener('submit', event => {
                event.preventDefault();
                removeItem(form);
            });
        });

        document.addEventListener('click', event => {
            const stepButton = event.target.closest('[data-quantity-step]');
            if (!stepButton) {
                return;
            }

            const form = stepButton.closest('[data-cart-update-form]');
            const input = form.querySelector('[data-quantity-field]');
            const currentQuantity = Number.parseInt(input.value, 10) || 1;
            const step = Number.parseInt(stepButton.dataset.quantityStep, 10);
            const nextQuantity = Math.min(99, Math.max(1, currentQuantity + step));

            if (nextQuantity !== currentQuantity) {
                input.value = nextQuantity;
                updateQuantity(form, nextQuantity);
            }
        });
    })();
</script>
@endsection
