<?php
namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function applayCoupon(Request $request)
    {
        $promoCode = PromoCode::where('code', $request->coupon)->first();
        if ($promoCode && $promoCode->isValid()) {
            $discount = $promoCode->calculateDiscount(Cart::getSubTotal());
            return response()->json([
                'status' => true,
                'discount' => $discount,
                'coupon' => $promoCode,
            ]);
        } else {
            return response()->json([
                'status' => false,
            ]);
        }
    }

}