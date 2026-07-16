<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * @tags Products
 */
class ProductController extends Controller
{
    /**
     * Display a listing of products with filters.
     *
     * @summary لیست محصولات
     * @queryParam search string جستجو در نام، slug و توضیحات. Example: phone
     * @queryParam category_id uuid فیلتر بر اساس دسته‌بندی. Example: 123e4567-e89b-12d3-a456-426614174000
     * @queryParam min_price number حداقل قیمت. Example: 100
     * @queryParam max_price number حداکثر قیمت. Example: 5000
     * @queryParam in_stock boolean فقط موجودی بیشتر از صفر. Example: true
     * @queryParam sort string فیلد مرتب‌سازی (name, price, stock, created_at, average_rating). Example: price
     * @queryParam order string جهت مرتب‌سازی (asc, desc). Example: asc
     * @queryParam per_page int تعداد در صفحه. Example: 15
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // جستجوی متنی
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // فیلتر بر اساس دسته
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // فیلتر قیمت
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // فیلتر موجودی
        if ($request->filled('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // مرتب‌سازی
        $sortField = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $perPage = $request->input('per_page', 15);
        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created product.
     *
     * @summary ایجاد محصول جدید (فقط ادمین/سوپرادمین)
     * @bodyParam name string required نام محصول. Example: iPhone 15
     * @bodyParam slug string required اسلاگ یکتا. Example: iphone-15
     * @bodyParam description string توضیحات. Example: New iPhone
     * @bodyParam price number required قیمت. Example: 999.99
     * @bodyParam stock integer required موجودی. Example: 10
     * @bodyParam category_id uuid required شناسه دسته‌بندی. Example: 123e4567-e89b-12d3-a456-426614174000
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $validated['id'] = (string) Str::uuid();

        $product = DB::transaction(function () use ($validated) {
            return Product::create($validated);
        });

        return new ProductResource($product);
    }

    /**
     * Display the specified product.
     *
     * @summary نمایش جزئیات محصول
     */
    public function show(Product $product)
    {
        $product->load('category', 'comments.user');
        return new ProductResource($product);
    }

    /**
     * Update the specified product.
     *
     * @summary ویرایش محصول (فقط ادمین/سوپرادمین)
     * @bodyParam name string نام محصول. Example: iPhone 15 Pro
     * @bodyParam slug string اسلاگ یکتا. Example: iphone-15-pro
     * @bodyParam description string توضیحات. Example: New iPhone Pro
     * @bodyParam price number قیمت. Example: 1099.99
     * @bodyParam stock integer موجودی. Example: 5
     * @bodyParam category_id uuid شناسه دسته‌بندی. Example: 123e4567-e89b-12d3-a456-426614174000
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($product, $validated) {
            $product->update($validated);
        });

        return new ProductResource($product);
    }

    /**
     * Remove the specified product.
     *
     * @summary حذف محصول (فقط ادمین/سوپرادمین)
     */
    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            $product->delete();
        });

        return response()->json(['message' => 'Product deleted successfully']);
    }
}