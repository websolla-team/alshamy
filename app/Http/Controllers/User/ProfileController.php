<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->get();
        $user = User::with('address')->where('id', auth()->id())->first();
        $deliveries = Delivery::get();
        $categories  = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);
        return view('website.profile', compact('deliveries', 'user', 'orders','categories'));
    }
    public function info(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
        ]);
        $user = auth()->user();
        $user->update($data);
        return back()->with('success', 'Profile Info Updated Successfully');
    }
    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['incorrect password' => 'Incorrect Password']);
        }
        $request->user()->update([
            'password' => $request->password,
        ]);
        return back()->with('success', 'Password Updated Successfully');
    }
    public function address(Request $request)
    {
        $data = $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
        ]);
        $user = auth()->user();
        if ($user->address) {
            $user->address()->update($data);
        } else {
            $user->address()->create($data);
        }
        return back()->with('success', 'Address Updated Successfully');
    }
    public function order($id)
    {
        $order = Order::where('user_id', auth()->id())->with('products')->findOrFail($id);
        return view('website.order_details', compact('order'));
    }
}
