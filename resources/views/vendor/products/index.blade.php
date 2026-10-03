@extends('vendor.layouts.app')

@section('title', 'Products')
@section('header', 'My Products')
@section('subheader', 'Manage your catalogue, pricing and stock.')

@section('content')

    @php
        $total = (int) ($counts->total ?? 0);
        $active = (int) ($counts->active ?? 0);
        $inactive = (int) ($counts->inactive ?? 0);
        $outOfStock = (int) ($counts->out_of_stock ?? 0);
        $lowStock = (int) ($counts->low_stock ?? 0);
        $pendingApproval = (int) ($counts->pending_approval ?? 0);
    @endphp

    {{-- Summary chips --}}
    <div class="mb-5 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-marketplace.stat-card label="Total Products" :value="number_format($total)" icon="products" tone="primary" />
        <x-marketplace.stat-card label="Active" :value="number_format($active)" icon="delivered" tone="info" :hint="$inactive.' inactive'" />
        <x-marketplace.stat-card label="Pending Approval" :value="number_format($pendingApproval)" icon="pending" tone="cta" hint="Awaiting admin review" />
        <x-marketplace.stat-card label="Out of Stock" :value="number_format($outOfStock)" icon="cancelled" tone="danger" hint="Hidden from buyers" />
    </div>

    {{-- Filters --}}
    <div class="mb-5 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('vendor.products.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-2">
                <label for="search" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Search</label>
                <input type="text" id="search" name="search" value="{{ $filters['search'] }}" placeholder="Product name or SKU..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>

            <div>
                <label for="category" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Category</label>
                <select id="category" name="category"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $filters['category'] === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Status</label>
                <select id="status" name="status"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none">
                    <option value="">All statuses</option>
                    <option value="active" @selected($filters['status'] === 'active')>Active</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                </select>
            </div>

            <div>
                <label for="approval" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Approval</label>
                <select id="approval" name="approval"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none">
                    <option value="">All approvals</option>
                    <option value="approved" @selected($filters['approval'] === 'approved')>Approved</option>
                    <option value="pending" @selected($filters['approval'] === 'pending')>Pending</option>
                    <option value="rejected" @selected($filters['approval'] === 'rejected')>Rejected</option>
                </select>
            </div>

            <div>
                <label for="stock" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Stock</label>
                <select id="stock" name="stock"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none">
                    <option value="">All stock</option>
                    <option value="in" @selected($filters['stock'] === 'in')>In stock</option>
                    <option value="low" @selected($filters['stock'] === 'low')>Low stock</option>
                    <option value="out" @selected($filters['stock'] === 'out')>Out of stock</option>
                </select>
            </div>

            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-6">
                <button type="submit" class="fc-btn-primary text-sm">Apply filters</button>
                <a href="{{ route('vendor.products.index') }}" class="fc-btn-ghost text-sm">Reset</a>
                <span class="ml-auto text-xs text-slate-500">{{ $products->total() }} result(s)</span>
            </div>
        </form>
    </div>

    <x-marketplace.card :flush="true">
        <x-slot:actions>
            <a href="{{ route('vendor.products.create') }}" class="fc-btn-primary text-xs">+ Add Product</a>
        </x-slot:actions>

        @if ($products->isEmpty())
            <x-marketplace.empty-state
                icon="products"
                :title="$filters['search'] || $filters['category'] || $filters['status'] || $filters['stock'] || $filters['approval'] ? 'No products match your filters' : 'No products yet'"
                :message="$filters['search'] || $filters['category'] || $filters['status'] || $filters['stock'] || $filters['approval']
                    ? 'Try widening your filters to see more of your catalogue.'
                    : 'Add your first product to start selling.'"
                :action-url="$filters['search'] || $filters['category'] || $filters['status'] || $filters['stock']
                    ? route('vendor.products.index')
                    : route('vendor.products.create')"
                :action-label="$filters['search'] || $filters['category'] || $filters['status'] || $filters['stock']
                    ? 'Clear filters'
                    : 'Add your first product'" />
        @else
            {{-- Desktop table --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3">SKU</th>
                            <th class="px-5 py-3">Price</th>
                            <th class="px-5 py-3">Stock</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Approval</th>
                            <th class="px-5 py-3">Sold</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($products as $product)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-emerald-50 text-xs font-bold text-emerald-700">
                                            @if ($product->image)
                                                <img src="{{ $product->image }}" alt="" class="h-full w-full object-cover">
                                            @else
                                                {{ strtoupper(substr($product->name, 0, 1)) }}
                                            @endif
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-slate-800">{{ $product->name }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $product->category->name ?? 'No category' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-mono text-xs text-slate-500">{{ $product->sku }}</td>
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-800">{{ format_currency($product->selling_price) }}</p>
                                    @if ($product->discount_percent)
                                        <p class="text-[11px] text-slate-400 line-through">{{ format_currency($product->price) }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if ($product->stock_quantity <= 0)
                                        <x-marketplace.status-badge status="out_of_stock" type="stock" />
                                    @elseif ($product->isLowStock())
                                        <span class="font-bold text-amber-600">{{ $product->stock_quantity }}</span>
                                        <x-marketplace.status-badge status="low_stock" type="stock" class="ml-1" />
                                    @else
                                        <span class="font-semibold text-slate-700">{{ $product->stock_quantity }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <x-marketplace.status-badge :status="$product->is_active ? 'active' : 'inactive'" />
                                </td>
                                <td class="px-5 py-4">
                                    <x-marketplace.status-badge :status="$product->approval_status" type="approval" />
                                    @if ($product->isRejected() && $product->rejection_reason)
                                        <span title="{{ $product->rejection_reason }}" class="block text-[10px] text-rose-500">Reason on file</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $product->sold_count }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('marketplace.product', $product->slug) }}" target="_blank" rel="noopener"
                                           class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-100" title="View on storefront">View</a>
                                        <a href="{{ route('vendor.products.edit', $product) }}" class="fc-btn-ghost text-xs">Edit</a>

                                        <form method="POST" action="{{ route('vendor.products.toggle', $product) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold transition hover:bg-slate-100 {{ $product->is_active ? 'text-amber-600' : 'text-emerald-600' }}">
                                                {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('vendor.products.delete', $product) }}" data-confirm="Deactivate this product? It will be hidden from the storefront.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">Remove</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="divide-y divide-slate-100 lg:hidden">
                @foreach ($products as $product)
                    <div class="p-4">
                        <div class="flex items-start gap-3">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-emerald-50 text-sm font-bold text-emerald-700">
                                @if ($product->image)
                                    <img src="{{ $product->image }}" alt="" class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr($product->name, 0, 1)) }}
                                @endif
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-800">{{ $product->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $product->sku }} · {{ $product->category->name ?? 'No category' }}</p>
                            </div>
                            <p class="shrink-0 text-sm font-bold text-slate-800">{{ format_currency($product->selling_price) }}</p>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <x-marketplace.status-badge :status="$product->is_active ? 'active' : 'inactive'" />
                            <x-marketplace.status-badge :status="$product->approval_status" type="approval" />
                            @if ($product->stock_quantity <= 0)
                                <x-marketplace.status-badge status="out_of_stock" type="stock" />
                            @elseif ($product->isLowStock())
                                <x-marketplace.status-badge status="low_stock" type="stock" />
                            @endif
                            <span class="text-[11px] text-slate-500">{{ $product->stock_quantity }} in stock · {{ $product->sold_count }} sold</span>
                        </div>

                        <div class="mt-3 grid grid-cols-3 gap-2">
                            <a href="{{ route('vendor.products.edit', $product) }}" class="fc-btn-ghost justify-center text-xs">Edit</a>
                            <form method="POST" action="{{ route('vendor.products.toggle', $product) }}">
                                @csrf
                                <button type="submit" class="fc-btn-ghost w-full justify-center text-xs">{{ $product->is_active ? 'Hide' : 'Show' }}</button>
                            </form>
                            <form method="POST" action="{{ route('vendor.products.delete', $product) }}" data-confirm="Deactivate this product?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full rounded-xl px-3 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-50">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <x-marketplace.pagination :paginator="$products" />
        @endif
    </x-marketplace.card>

@endsection