@php
    $vendorNav = [
        'marketplace.vendor.dashboard' => ['label' => 'Dashboard', 'icon' => '📊'],
        'marketplace.vendor.products' => ['label' => 'Products', 'icon' => '📦'],
        'marketplace.vendor.inventory' => ['label' => 'Inventory', 'icon' => '📋'],
        'marketplace.vendor.orders' => ['label' => 'Orders', 'icon' => '🧾'],
        'marketplace.vendor.packing-slips' => ['label' => 'Packing Slips', 'icon' => '📄'],
        'marketplace.vendor.reports' => ['label' => 'Reports', 'icon' => '📈'],
    ];
@endphp
<div class="bg-emerald-800 rounded-2xl shadow-sm p-5 mb-6 flex flex-wrap items-center gap-4">
    <div class="flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-white font-extrabold text-xl">{{ strtoupper(substr($vendor->name, 0, 1)) }}</div>
        <div>
            <p class="text-white font-bold">{{ $vendor->business_name ?: $vendor->name }}</p>
            <p class="text-emerald-200 text-xs">Vendor ID: #{{ $vendor->id }} · {{ ucfirst($vendor->status) }}</p>
        </div>
    </div>
    <nav class="flex flex-wrap gap-1 ml-auto">
        @foreach ($vendorNav as $route => $item)
            <a href="{{ route($route) }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs($route) || request()->routeIs($route.'.*') ? 'bg-white text-emerald-800' : 'text-emerald-100 hover:bg-white/10' }}">
                <span class="mr-1">{{ $item['icon'] }}</span>{{ $item['label'] }}
            </a>
        @endforeach
    </nav>
</div>
