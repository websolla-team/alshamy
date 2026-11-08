<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\Variant;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;

class ShoppingCartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::getContent();
        foreach ($cartItems as $item) {
            $product = Product::whereHas('variants', function ($query) use ($item) {
                $query->where('id', $item->attributes->variant_id);
            })->with(['oneVariant' => function ($query) use ($item) {
                $query->where('id', $item->attributes->variant_id)
                    ->select('size', 'product_id', 'price');
            }])->find($item->attributes->product_id);
            if ($product) {
                if ($item->price != $product->oneVariant->price) {
                    Cart::update($item->id, [
                        'price' => $product->oneVariant->price,
                    ]);
                }
            } else {
                // return $product;
                Cart::remove($item->id);
            }
        }
         $categories  = Category::withCount(['products'])->latest()->get(['id', 'title', 'image']);
        return view('website.cart',compact('categories'));
    }
    public function addToCart(Request $request)
    {
        $productId = $request->input('id');
        $color = $request->input('color');
        $size = $request->input('size');
        $variantId = $request->input('variant');
        $quantity = $request->input('qty', 1);

        $productVariant = Variant::where('id', $variantId)
            ->where('product_id', $productId)
            ->with(['product', 'color'])
            ->first();

        if ($productVariant) {
            $id = $productId . '_' . $variantId;
            $cartItem = Cart::get($id);
            $totalQuantity = $quantity;
            if ($cartItem) {
                $totalQuantity += $cartItem->quantity;
            }
            if ($totalQuantity > $productVariant->quantity) {
                return response()->json(['error' => __('main.Requested quantity exceeds available stock')], 400);
            }

            $name = $productVariant->product->getTranslations('name');
            $category = $productVariant->product?->category?->getTranslations('title');


            Cart::add([
                'id' => $id,
                'name' => $name,
                'price' => $productVariant->price,
                'quantity' => $quantity,
                'attributes' => [
                    'variant_id' => $productVariant->id,
                    'product_id' => $productVariant->product_id,
                    'color' => $color,
                    'size' => $size,
                    'category' => $category,
                    'image' => $productVariant->product?->image,
                ],
            ]);

            return response()->json([
                'success' => __('main.Product added to cart successfully'),
            ]);
        } else {
            return response()->json(['error' => __('main.no products')], 404);
        }
    }
    public function update(Request $request)
    {
        $productId = $request->input('id');
        $quantity = $request->input('quantity', 1);
        if ($quantity < 1) {
            return response()->json(['error' => __('main.Quantity must be at least 1')], 400);
        }
        $cartItem = Cart::get($productId);
        $product = Product::whereHas('variants', function ($query) use ($cartItem) {
            $query->where('id', $cartItem->attributes->variant_id);
        })->with(['oneVariant' => function ($query) use ($cartItem) {
            $query->where('id', $cartItem->attributes->variant_id)
                ->select('size', 'product_id', 'price', 'quantity');
        }])->find($cartItem->attributes->product_id);

        if ($cartItem) {
            if ($quantity > $product->oneVariant->quantity) {
                return response()->json(['error' => __('main.Requested quantity exceeds available stock')], 400);
            }
            Cart::update($productId, [
                'quantity' => [
                    'relative' => false,
                    'value' => $quantity,
                ],
            ]);
            return response()->json([
                'success' => 'Quantity updated successfully!',
            ]);
        } else {
            return response()->json(['error' => __('main.Item not found in cart')], 404);
        }
    }
    public function remove(Request $request)
    {
        $productId = $request->input('id');
        $cartItem = Cart::get($productId);
        if ($cartItem) {
            Cart::remove($productId);
            return response()->json([
                'success' => 'Item removed successfully!',
                'cartCount' => Cart::getContent()->count(),
                'cartSubtotal' => Cart::getSubTotal(),
            ]);
        } else {
            return response()->json(['error' => 'Item not found in cart'], 404);
        }
    }
}
