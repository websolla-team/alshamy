<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contact = Contact::first();
        return view('admin.contacts.index', compact('contact'));
    }
    public function update(Request $request)
    {
        $data = $request->validate([
            'phone1' => 'required|string',
            'phone2' => 'nullable|string',
            'address.*' => 'required|string',
            'time' => 'required|string',
            'email' => 'required|email',
        ]);
        Contact::first()->update($data);
        return back();
    }
    public function social()
    {
        $contact = Contact::first();
        return view('admin.contacts.social', compact('contact'));
    }
    public function updateSocial(Request $request)
    {
        $data = $request->validate([
            'facebook' => 'nullable|string',
            'twitter' => 'nullable|string',
            'linkedin' => 'nullable|string',
            'youtupe' => 'nullable|string',
            'instagram' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'tiktok' => 'nullable|string',
        ]);
        Contact::first()->update($data);
        return back();
    }
}
