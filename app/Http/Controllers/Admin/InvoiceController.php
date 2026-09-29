<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Danh sách hoá đơn.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['booking.room.roomType', 'booking'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('issued_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('issued_at', '<=', $request->to);
        }

        $invoices = $query->paginate(15);

        // Thống kê nhanh
        $stats = [
            'totalInvoices' => Invoice::count(),
            'totalRevenue'  => Invoice::where('status', 'paid')->sum('total'),
            'paidCount'     => Invoice::where('status', 'paid')->count(),
            'draftCount'    => Invoice::where('status', 'draft')->count(),
        ];

        return view('admin.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Chi tiết hoá đơn (có thể in).
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['booking.room.roomType', 'booking.services', 'items']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Đánh dấu đã thanh toán.
     */
    public function markPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);
        $invoice->booking->update(['payment_status' => 'paid']);

        return back()->with('success', "Hoá đơn {$invoice->invoice_number} đã được thanh toán.");
    }

    /**
     * Huỷ hoá đơn.
     */
    public function markCancelled(Invoice $invoice)
    {
        $invoice->update(['status' => 'cancelled']);
        return back()->with('success', "Hoá đơn {$invoice->invoice_number} đã bị huỷ.");
    }
}
