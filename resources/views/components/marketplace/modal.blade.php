{{--
    Modal dialog wrapper used for confirmations (cancel order, request return,
    write review). Hidden by default; opened from JS via `data-modal-open="<id>"`.

    Usage:
        <x-marketplace.modal id="cancel-modal" title="Cancel Order?">
            ...form...
        </x-marketplace.modal>
--}}
@props(['id', 'title' => '', 'maxWidth' => 'max-w-md'])

<div id="{{ $id }}"
    data-modal
    class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
    role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    <div class="w-full {{ $maxWidth }} overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 id="{{ $id }}-title" class="text-sm font-bold text-slate-800">{{ $title }}</h3>
            <button type="button" data-modal-close class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Close">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-5">
            {{ $slot }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Delegated so it works for every modal on the page without inline handlers.
    document.addEventListener('click', function(e) {
        const opener = e.target.closest('[data-modal-open]');
        if (opener) {
            const modal = document.getElementById(opener.getAttribute('data-modal-open'));
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
            return;
        }

        const closer = e.target.closest('[data-modal-close]');
        if (closer) {
            const modal = closer.closest('[data-modal]');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            return;
        }

        // Click on the backdrop closes the dialog.
        if (e.target.matches('[data-modal]')) {
            e.target.classList.add('hidden');
            e.target.classList.remove('flex');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Escape') return;
        document.querySelectorAll('[data-modal]:not(.hidden)').forEach(m => {
            m.classList.add('hidden');
            m.classList.remove('flex');
        });
    });
</script>
@endpush