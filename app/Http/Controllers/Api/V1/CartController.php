<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product; 
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @tags Cart
 */
class CartController extends Controller
{
    /**
     * Display user's cart items.
     *
     * @summary مشاهده سبد خرید کاربر جاری
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = CartItem::with('product')
            ->where('user_id', $user->uuid)
            ->get();

        return CartItemResource::collection($cartItems);
    }

    /**
     * Add product to cart.
     *
     * @summary افزودن محصول به سبد خرید
     * @bodyParam product_id uuid required شناسه محصول. Example: 123e4567-e89b-12d3-a456-426614174000
     * @bodyParam quantity integer required تعداد. Example: 2
     */
    public function store(StoreCartItemRequest $request)
    {
       
        $user = Auth::user();

        $validated = $request->validated();

        $cartItem = DB::transaction(function () use ($user, $validated) {
            
            // قفل کردن رکورد محصول برای بررسی موجودی
            $product = Product::where('id', $validated['product_id'])->lockForUpdate()->first();
           
            if (!$product) {
                throw new \Exception('Product not found');
            }
            if ($product->stock < $validated['quantity']) {
                throw new \Exception('Insufficient stock');
            }

            // بررسی وجود آیتم قبلی در سبد خرید
            $existing = CartItem::where('user_id', $user->uuid)
                ->where('product_id', $validated['product_id'])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $newQuantity = $existing->quantity + $validated['quantity'];
                if ($product->stock < $newQuantity) {
                    throw new \Exception('Insufficient stock for total quantity');
                }
                $existing->quantity = $newQuantity;
                $existing->save();
                return $existing;
            } else {
                $cartItem = new CartItem();
                $cartItem->id = (string) \Illuminate\Support\Str::uuid();
                $cartItem->user_id = $user->uuid;
                $cartItem->product_id = $validated['product_id'];
                $cartItem->quantity = $validated['quantity'];
                $cartItem->save();
                return $cartItem;
            }
        });

        return new CartItemResource($cartItem);
    }

    /**
     * Update cart item quantity.
     *
     * @summary ویرایش تعداد یک آیتم در سبد خرید
     * @bodyParam quantity integer required تعداد جدید. Example: 3
     */
    public function update(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        // اطمینان از اینکه آیتم متعلق به کاربر جاری است
        if ($cartItem->user_id !== Auth::user()->uuid) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $newQuantity = $request->quantity;

        DB::transaction(function () use ($cartItem, $newQuantity) {
            // قفل محصول
            $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();
            if ($product->stock < $newQuantity) {
                throw new \Exception('Insufficient stock');
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        });

        return new CartItemResource($cartItem);
    }

    /**
     * Remove item from cart.
     *
     * @summary حذف آیتم از سبد خرید
     */
    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::user()->uuid) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($cartItem) {
            $cartItem->delete();
        });

        return response()->json(['message' => 'Item removed from cart']);
    }
}