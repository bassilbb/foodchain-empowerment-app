@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'status-msg']) }}>
        {{ $status }}
    </div>
@endif
