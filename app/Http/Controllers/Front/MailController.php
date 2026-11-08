<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function sendMail(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255|string',
            'phone' => 'required|max:255|string',
            'email' => 'required|email|max:255',
            'subject' => 'required|max:255',
            'message' => 'required|max:1000|string',
        ]);
        Message::create($data);
     return redirect()->back()->with('success', __('main.We received your message successfully'));

    }

    public function sendLetter(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
        ]);
        Newsletter::create($data);
        return __('main.We received your email successfully');
    }
}
