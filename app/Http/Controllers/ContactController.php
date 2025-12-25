<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Get admin email from env
            $adminEmail = config('mail.from.address');

            // Send email
            Mail::send([], [], function ($message) use ($validated, $adminEmail) {
                $message->to($adminEmail)
                    ->subject('New Contact Form Submission - ' . $validated['name'])
                    ->html("
                        <h2>New Contact Form Submission</h2>
                        <p><strong>Name:</strong> {$validated['name']}</p>
                        <p><strong>Email:</strong> {$validated['email']}</p>
                        <p><strong>Phone:</strong> " . ($validated['phone'] ?? 'N/A') . "</p>
                        <p><strong>Message:</strong></p>
                        <p>" . nl2br(e($validated['message'])) . "</p>
                    ");
            });

            // Check if request is AJAX
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you! Your message has been sent successfully. We\'ll get back to you soon.'
                ]);
            }

            return back()->with('success', 'Thank you! Your message has been sent successfully. We\'ll get back to you soon.');
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());

            // Check if request is AJAX
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, there was an error sending your message. Please try again later.'
                ], 500);
            }

            return back()->with('error', 'Sorry, there was an error sending your message. Please try again later.');
        }
    }
}
