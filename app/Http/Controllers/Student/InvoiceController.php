<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the user's invoices.
     */
    public function index()
    {
        $invoices = Invoice::where('user_id', Auth::id())
            ->with(['order'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('student.invoices.index', compact('invoices'));
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        // Ensure user can only view their own invoices
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $invoice->load(['order.items', 'user']);
        $package = $invoice->order->items()->first()?->item;

        return view('invoices.package-invoice', compact('invoice', 'package', 'user'));
    }

    /**
     * Download invoice as PDF.
     */
    public function download(Invoice $invoice)
    {
        // Ensure user can only download their own invoices
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $invoice->load(['order.items', 'user']);
        $order = $invoice->order;
        $package = $order->items()->first()?->item;
        $user = $invoice->user;

        // Generate PDF
        $pdf = Pdf::loadView('invoices.package-invoice', compact('invoice', 'order', 'package', 'user'));

        // Download PDF
        return $pdf->download("invoice-{$invoice->number}.pdf");
    }
}
