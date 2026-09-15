@props([
    'status', // AppointmentStatus or PaymentStatus instance or string
    'label' => null,
])

@php
    $statusValue = is_object($status) ? ($status->value ?? (string)$status) : $status;
    $displayLabel = $label ?? (is_object($status) && method_exists($status, 'label') ? $status->label() : $statusValue);

    $classes = match(strtoupper($statusValue)) {
        'CONFIRMED', 'PAID', 'APPROVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'PENDING', 'PENDING_PAYMENT', 'PENDING_VALIDATION' => 'bg-amber-50 text-amber-700 border-amber-200',
        'CANCELLED', 'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
        'COMPLETED' => 'bg-blue-50 text-blue-700 border-blue-200',
        'RESCHEDULED' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'NO_SHOW' => 'bg-slate-100 text-slate-700 border-slate-300',
        default => 'bg-slate-50 text-slate-700 border-slate-200',
    };

    $dotColor = match(strtoupper($statusValue)) {
        'CONFIRMED', 'PAID', 'APPROVED' => 'bg-emerald-500',
        'PENDING', 'PENDING_PAYMENT', 'PENDING_VALIDATION' => 'bg-amber-500',
        'CANCELLED', 'REJECTED' => 'bg-rose-500',
        'COMPLETED' => 'bg-blue-500',
        'RESCHEDULED' => 'bg-indigo-500',
        default => 'bg-slate-400',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {$classes}"]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $dotColor }}"></span>
    {{ $displayLabel }}
</span>
