@extends('layouts.app')

@section('content')
<div class="admin-page admin-dashboard">
    <section class="admin-panel dashboard-toolbar">
        <h1>TỔNG QUAN QUẢN LÝ</h1>
        <form class="dashboard-date-filter" id="dashboardDateFilter">
            <label for="date-from">Từ</label>
            <input id="date-from" name="from" type="date" value="{{ $dateFrom }}" required>
            <label for="date-to">đến</label>
            <input id="date-to" name="to" type="date" value="{{ $dateTo }}" required>
            <button type="submit" class="admin-button" id="dashboardFilterButton">Lọc</button>
        </form>
    </section>

    <section class="dashboard-stats" aria-label="Thống kê tổng quan">
        <article class="admin-panel stat-card">
            <span class="stat-icon stat-icon-orange"><i class="fa-solid fa-sack-dollar"></i></span>
            <div><p>Doanh thu đã giao</p><strong id="dashboardRevenue">{{ number_format($stats['revenue'], 0, ',', '.') }} đ</strong></div>
        </article>
        <article class="admin-panel stat-card">
            <span class="stat-icon stat-icon-orange"><i class="fa-solid fa-clock"></i></span>
            <div><p>Chờ xác nhận</p><strong id="dashboardPending">{{ $stats['pending'] }}</strong></div>
        </article>
        <article class="admin-panel stat-card">
            <span class="stat-icon stat-icon-blue"><i class="fa-solid fa-clipboard-list"></i></span>
            <div><p>Tổng đơn</p><strong id="dashboardTotal">{{ $stats['total'] }}</strong></div>
        </article>
        <article class="admin-panel stat-card">
            <span class="stat-icon stat-icon-red"><i class="fa-solid fa-ban"></i></span>
            <div><p>Tỉ lệ hủy đơn</p><strong id="dashboardCancelled">{{ number_format($stats['cancellationRate'], 1, ',', '.') }}%</strong></div>
        </article>
    </section>

    <section class="dashboard-main-grid">
        <article class="admin-panel chart-panel">
            <div class="admin-panel-heading">
                <h2>ĐƠN HÀNG TUẦN NÀY</h2>
                <span id="dashboardChartRange">{{ \Carbon\Carbon::parse($dateFrom)->format('d/m') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m') }}</span>
            </div>
            <div class="chart-wrap">
                <svg id="ordersChart" class="orders-chart" viewBox="0 0 760 250" role="img" aria-label="Biểu đồ số lượng đơn hàng trong tuần">
                    <line x1="46" y1="20" x2="46" y2="210" class="chart-axis"/>
                    <line x1="46" y1="210" x2="744" y2="210" class="chart-axis"/>
                </svg>
            </div>
        </article>

        <article class="admin-panel pending-panel">
            <div class="admin-panel-heading"><h2>CHỜ XÁC NHẬN</h2></div>
            <div class="pending-table-heading"><span>Mã đơn</span><span>Trạng thái</span></div>
            <div id="pendingOrdersList">
                @forelse($pendingOrders as $order)
                    <a class="pending-order-row" href="{{ route('admin.orders.show', $order['id']) }}">
                        <span><strong>{{ $order['code'] }}</strong><small>{{ $order['createdAt'] }}</small></span>
                        <span class="status-pill status-pending">Chờ xác nhận</span>
                    </a>
                @empty
                    <div class="top-row"><strong>Chưa có đơn chờ xác nhận</strong></div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="dashboard-tops">
        <article class="admin-panel top-panel">
            <div class="admin-panel-heading"><h2>TOP KHÁCH HÀNG</h2></div>
            <div id="topCustomersList">
                @forelse($topCustomers as $index => $customer)
                    <div class="top-row"><span class="top-rank">{{ $index + 1 }}</span><strong>{{ $customer['name'] }}</strong><span>{{ $customer['spent'] }}</span></div>
                @empty
                    <div class="top-row"><strong>Chưa có dữ liệu</strong></div>
                @endforelse
            </div>
        </article>
        <article class="admin-panel top-panel">
            <div class="admin-panel-heading"><h2>TOP MÓN ĂN</h2></div>
            <div id="topFoodsList">
                @forelse($topFoods as $index => $food)
                    <div class="top-row"><span class="top-rank">{{ $index + 1 }}</span><strong>{{ $food['name'] }}</strong><span>{{ $food['quantity'] }} phần</span></div>
                @empty
                    <div class="top-row"><strong>Chưa có dữ liệu</strong></div>
                @endforelse
            </div>
        </article>
    </section>
</div>

<script>
    function formatDashboardCurrency(value) {
        return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
    }

    function renderDashboardChart(dailyData) {
        const svg = document.getElementById('ordersChart');
        if (!svg) return;

        const width = 760;
        const height = 250;
        const left = 46;
        const right = 744;
        const top = 20;
        const bottom = 210;

        svg.innerHTML = '';
        const ns = 'http://www.w3.org/2000/svg';
        const create = (name, attributes = {}) => {
            const element = document.createElementNS(ns, name);
            Object.entries(attributes).forEach(([key, value]) => element.setAttribute(key, value));
            return element;
        };

        svg.appendChild(create('line', { x1: left, y1: top, x2: left, y2: bottom, class: 'chart-axis' }));
        svg.appendChild(create('line', { x1: left, y1: bottom, x2: right, y2: bottom, class: 'chart-axis' }));

        const counts = dailyData.map(item => Number(item.count));
        const maxCount = Math.max(...counts, 1);
        const roundedMax = Math.max(1, Math.ceil(maxCount / 5) * 5);

        for (let i = 0; i <= 5; i++) {
            const y = bottom - ((bottom - top) * i / 5);
            svg.appendChild(create('line', { x1: left, y1: y, x2: right, y2: y, class: i === 0 ? 'chart-axis' : 'chart-gridline' }));
            const label = create('text', { x: 20, y: y + 4, class: 'chart-label' });
            label.textContent = Math.round(roundedMax * i / 5);
            svg.appendChild(label);
        }

        if (dailyData.length === 0) return;

        const points = dailyData.map((item, index) => {
            const x = dailyData.length === 1
                ? (left + right) / 2
                : left + ((right - left) * index / (dailyData.length - 1));
            const y = bottom - ((bottom - top) * item.count / roundedMax);
            return { x, y, item };
        });

        svg.appendChild(create('polyline', {
            points: points.map(point => `${point.x},${point.y}`).join(' '),
            class: 'chart-line'
        }));

        points.forEach(point => {
            svg.appendChild(create('circle', { cx: point.x, cy: point.y, r: 4, class: 'chart-point' }));
        });

        const labelIndexes = dailyData.length <= 14
            ? dailyData.map((_, index) => index)
            : [...new Set([0, 1, 2, 3, 4, 5, 6].map(index => Math.round(index * (dailyData.length - 1) / 6)))];

        labelIndexes.forEach(index => {
            const point = points[index];
            if (!point) return;
            const label = create('text', { x: point.x - 12, y: 235, class: 'chart-label' });
            label.textContent = point.item.label;
            svg.appendChild(label);
        });
    }

    function renderTopList(elementId, rows, type) {
        const container = document.getElementById(elementId);
        if (!container) return;
        container.innerHTML = '';

        if (!rows.length) {
            const empty = document.createElement('div');
            empty.className = 'top-row';
            const name = document.createElement('strong');
            name.textContent = 'Chưa có dữ liệu';
            empty.appendChild(name);
            container.appendChild(empty);
            return;
        }

        rows.forEach((row, index) => {
            const item = document.createElement('div');
            item.className = 'top-row';

            const rank = document.createElement('span');
            rank.className = 'top-rank';
            rank.textContent = index + 1;

            const name = document.createElement('strong');
            name.textContent = row.name;

            const value = document.createElement('span');
            value.textContent = type === 'customer' ? row.spent : `${row.quantity} phần`;

            item.append(rank, name, value);
            container.appendChild(item);
        });
    }

    function renderPendingOrders(rows) {
        const container = document.getElementById('pendingOrdersList');
        if (!container) return;
        container.innerHTML = '';

        if (!rows.length) {
            const empty = document.createElement('div');
            empty.className = 'top-row';
            const name = document.createElement('strong');
            name.textContent = 'Chưa có đơn chờ xác nhận';
            empty.appendChild(name);
            container.appendChild(empty);
            return;
        }

        rows.forEach(order => {
            const link = document.createElement('a');
            link.className = 'pending-order-row';
            link.href = @json(url('/admin/orders')) + '/' + order.id;

            const details = document.createElement('span');
            const code = document.createElement('strong');
            code.textContent = order.code;
            const createdAt = document.createElement('small');
            createdAt.textContent = order.createdAt;
            details.append(code, createdAt);

            const status = document.createElement('span');
            status.className = 'status-pill status-pending';
            status.textContent = 'Chờ xác nhận';

            link.append(details, status);
            container.appendChild(link);
        });
    }

    const initialDashboardData = @json($stats);
    renderDashboardChart(initialDashboardData.daily);

    document.getElementById('dashboardDateFilter')?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const button = document.getElementById('dashboardFilterButton');
        const from = document.getElementById('date-from').value;
        const to = document.getElementById('date-to').value;
        if (!from || !to) return;

        button.disabled = true;
        button.textContent = 'Đang lọc...';

        try {
            const response = await fetch(@json(route('admin.dashboard.stats')) + `?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`, {
                headers: { Accept: 'application/json' }
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Không thể lọc dữ liệu');

            document.getElementById('dashboardRevenue').textContent = formatDashboardCurrency(data.stats.revenue);
            document.getElementById('dashboardPending').textContent = data.stats.pending;
            document.getElementById('dashboardTotal').textContent = data.stats.total;
            document.getElementById('dashboardCancelled').textContent = `${Number(data.stats.cancellationRate).toFixed(1).replace('.', ',')}%`;
            document.getElementById('dashboardChartRange').textContent =
                `${from.split('-').reverse().slice(0, 2).join('/')} - ${to.split('-').reverse().slice(0, 2).join('/')}`;

            renderDashboardChart(data.stats.daily);
            renderTopList('topCustomersList', data.stats.topCustomers, 'customer');
            renderTopList('topFoodsList', data.stats.topFoods, 'food');
            renderPendingOrders(data.stats.pendingOrders);
        } catch (error) {
            if (typeof showSiteToast === 'function') showSiteToast(error.message, 'error');
            else alert(error.message);
        } finally {
            button.disabled = false;
            button.textContent = 'Lọc';
        }
    });

    setInterval(async function () {
        const from = document.getElementById('date-from')?.value;
        const to = document.getElementById('date-to')?.value;
        if (!from || !to) return;

        try {
            const response = await fetch(@json(route('admin.dashboard.stats')) + `?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`, {
                headers: { Accept: 'application/json' },
                cache: 'no-store'
            });
            const data = await response.json();
            if (!response.ok) return;

            document.getElementById('dashboardRevenue').textContent = formatDashboardCurrency(data.stats.revenue);
            document.getElementById('dashboardPending').textContent = data.stats.pending;
            document.getElementById('dashboardTotal').textContent = data.stats.total;
            document.getElementById('dashboardCancelled').textContent = `${Number(data.stats.cancellationRate).toFixed(1).replace('.', ',')}%`;
            renderDashboardChart(data.stats.daily);
            renderTopList('topCustomersList', data.stats.topCustomers, 'customer');
            renderTopList('topFoodsList', data.stats.topFoods, 'food');
            renderPendingOrders(data.stats.pendingOrders);
        } catch (_) {
            // Giữ dữ liệu hiện tại nếu yêu cầu làm mới tạm thời thất bại.
        }
    }, 15000);
</script>
@endsection
