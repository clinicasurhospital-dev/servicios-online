<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payments\Enums\BankTransferStatus;
use App\Domain\Payments\Services\BankTransferService;
use App\Http\Controllers\Controller;
use App\Models\BankTransfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminTransferController extends Controller
{
    public function __construct(
        protected BankTransferService $transferService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');

        $transfers = BankTransfer::query()
            ->with(['payment.appointment.patient.user', 'payment.appointment.doctor.user', 'payment.appointment.specialty', 'validator'])
            ->when($status, fn ($q) => $q->where('validation_status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending' => BankTransfer::where('validation_status', BankTransferStatus::PENDING_VALIDATION)->count(),
            'approved' => BankTransfer::where('validation_status', BankTransferStatus::APPROVED)->count(),
            'rejected' => BankTransfer::where('validation_status', BankTransferStatus::REJECTED)->count(),
        ];

        return view('admin.transfers', compact('transfers', 'stats', 'status'));
    }

    public function validateTransfer(Request $request, BankTransfer $transfer): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:APPROVED,REJECTED',
            'notes' => 'nullable|string|max:500',
        ]);

        $status = BankTransferStatus::from($request->string('status')->value());

        $this->transferService->validateTransfer(
            transfer: $transfer,
            status: $status,
            validator: $request->user(),
            rejectionReason: $status === BankTransferStatus::REJECTED ? $request->input('notes') : null
        );

        return back()->with('success', 'Transferencia procesada correctamente.');
    }

    public function downloadReceipt(BankTransfer $transfer): StreamedResponse|RedirectResponse
    {
        if (! $transfer->receipt_path || ! Storage::disk('receipts')->exists($transfer->receipt_path)) {
            return back()->with('error', 'El comprobante no fue encontrado en el almacenamiento seguro.');
        }

        return Storage::disk('receipts')->download(
            $transfer->receipt_path,
            'comprobante_' . ($transfer->reference_number ?: $transfer->id) . '.' . pathinfo($transfer->receipt_path, PATHINFO_EXTENSION)
        );
    }
}
