<?php

namespace App\Http\Controllers\Front;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Crypt;
use App\Mail\OrderMail;
use App\Models\Variant;
use App\Models\Deliveryuse ;
use App\Models\Delivery ;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class OrderCartController extends Controller
{
    public function index(Request $request)
    {
        if (Cart::isEmpty()) {
            return redirect()->route('cart');
        }
        $user = false;
        $discount = 0;
        $subtotalAfterDiscount = Cart::getSubTotal();
        $deliveries = Delivery::get();
        if (auth()->check()) {
            $user = User::with('address')->where('id', auth()->id())->first();
        }

        // check coupon
        if (isset($request->coupon_code)) {
            $promoCode = PromoCode::where('code', $request->coupon_code)->first();
            if ($promoCode && $promoCode->isValid()) {
                $discount = $promoCode->calculateDiscount(Cart::getSubTotal());
                $subtotalAfterDiscount -= $discount;
            }
        }
 $categories  = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);
        return view('website.checkout', compact('user', 'subtotalAfterDiscount', 'discount', 'deliveries','categories'));
    }

    public function storeOrder(Request $request)
    {
        if (Cart::isEmpty()) {
            return redirect()->route('cart');
        }
        $data = $this->validateAndPrepareOrderData($request);

        DB::beginTransaction();
        try {
            $order = $this->createOrder($data, $request);
            $this->addProductsToOrder($order);
            DB::commit();
            Cart::clear();
            if($request->pay=='card'){
               return redirect()->route('payment', ['id' => Crypt::encrypt($order->id)]);
            }elseif($request->pay=='cash'){
            return redirect()->route('home')->with('success', __('main.We received your order successfully'));}
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }



    }

    protected function validateAndPrepareOrderData(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|max:255|string',
            'last_name' => 'required|max:255|string',
            'phone' => 'required|max:20|string',
            'delivery' => 'required|integer|exists:deliveries,id',
            'city' => 'required|max:255|string',
            'address' => 'required|max:500|string',
            'note' => 'nullable|max:500|string',
            'pay' => 'nullable|max:500|string',


        ]);

        $data['user_name'] = $data['first_name'] . ' ' . $data['last_name'];
        unset($data['first_name'], $data['last_name']);

        $data['code'] = rand(100000, 999999);
        $data['user_id'] = auth()->check() ? auth()->id() : null;

        $delivery = Delivery::find($request->delivery);
        $data['delivery'] = $delivery->getTranslation('name', 'ar');
        $data['shipping'] = $delivery->cost;

        return $data;
    }

    protected function createOrder(array $data, Request $request)
    {
        $discount = $this->applyCoupon($request, $data);
        $data['total'] = Cart::getSubTotal() - $discount + $data['shipping'];

        return Order::create(attributes: $data);
    }

    protected function applyCoupon(Request $request, array &$data)
    {
        if (!$request->has('coupon_code')) {
            return 0;
        }

        $promoCode = PromoCode::where('code', $request->coupon_code)->first();
        if (!$promoCode || !$promoCode->isValid()) {
            return 0;
        }

        $discount = $promoCode->calculateDiscount(Cart::getSubTotal());
        $data['coupon'] = $request->coupon_code;
        $type = $promoCode->discount_type == 'percentage' ? '% ' : 'LE ';
        $data['discount'] = $type . $promoCode->discount_value;
        $promoCode->increment('uses_count');

        return $discount;
    }

    protected function addProductsToOrder(Order $order)
    {
        foreach (Cart::getContent() as $item) {
            $variant = Variant::where('id', $item->attributes->variant_id)->lockForUpdate()->first();
            if (!$variant || $variant->quantity < $item->quantity) {
                throw new \Exception(__('main.Requested quantity exceeds available stock') . ': ' . $item->name[app()->getLocale()]);
            }

            $order->products()->create([
                'product_id' => $item->attributes->product_id,
                'name' => $item->name['en'],
                'quantity' => $item->quantity,
                'color' => $item->attributes?->color ?? '',
                'size' => $item->attributes?->size ?? '',
                'price' => $item->price,
                'total_price' => $item->price * $item->quantity,
            ]);

            $variant->decrement('quantity', $item->quantity);
        }
    }
}
