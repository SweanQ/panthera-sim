<?php

namespace App\Http\Controllers;

use App\Models\CustomInquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomInquiryController extends Controller
{
    public function index(): View
    {
        $inquiries = CustomInquiry::query()
            ->with('product')
            ->latest()
            ->paginate(15);

        return view('orders.inquiries', compact('inquiries'));
    }

    public function updateStatus(Request $request, CustomInquiry $inquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:baru,dihubungi,selesai'],
        ]);

        $inquiry->update($validated);

        return back()->with('success', 'Status permintaan diperbarui.');
    }
}
