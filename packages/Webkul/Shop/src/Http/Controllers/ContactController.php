<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|max:150',
            'phone'    => 'nullable|digits_between:7,15',
            'location' => 'nullable|string|max:150',
            'message'  => 'required|string|max:200',
        ]);

        // Admin email (fallback to default)
        $adminEmail = config('app.admin_email', 'deepak.sharma5@dotsquares.com');

        // Send email using blade template (cleaner & structured)
        Mail::send('emails.contact', $validated, function ($mail) use ($adminEmail, $validated) {
            $mail->to($adminEmail)
                ->subject("New Contact Message - {$validated['name']}");
        });

        return back()->with('success', 'Your message has been sent successfully!');
    }
}
