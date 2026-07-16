<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    protected $cacheTTL = 60; // دقیقه

    private function getListCacheKey(Request $request): string
    {
        $params = $request->only(['search', 'category_id', 'min_price', 'max_price', 'in_stock', 'sort', 'order', 'per_page', 'page']);
        ksort($params);
        return 'products:list:' . md5(json_encode($params));
    }

    public function index(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 15), 100);
        $cacheKey = $this->getListCacheKey($request);

        // 1. دریافت آرایه از کش (یا اجرای کوئری و تبدیل به آرایه برای کش)
        $cachedData = Cache::tags(['products'])->remember($cacheKey, $this->cacheTTL * 60, function () use ($request, $perPage) {
            
            $query = Product::select('id', 'name', 'slug', 'price', 'stock', 'category_id', 'created_at')
                            ->with('category:id,name');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('slug', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }
            if ($request->filled('in_stock')) {
                $query->where('stock', '>', 0);
            }

            $sortField = $request->input('sort', 'created_at');
            $sortOrder = $request->input('order', 'desc');
            $allowedSortFields = ['created_at', 'price', 'name', 'id'];
            
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = 'created_at';
            }
            
            $query->orderBy($sortField, $sortOrder);

            // اجرا و تبدیل به آرایه ساده (برای جلوگیری از خطای unserialize)
            return $query->paginate($perPage)->toArray();
        });

        // 2. تبدیل آرایه‌های کش‌شده обратно به آبجکت‌های مدل Eloquent
        // این خط جادویی است که باعث می‌شود ProductResource بدون خطا کار کند
        $models = Product::hydrate($cachedData['data']);

        // 3. ساخت مجدد Paginator با استفاده از مدل‌های واقعی
        $paginator = new LengthAwarePaginator(
            $models,                      // Collection of Models (نه آرایه!)
            $cachedData['total'],
            $cachedData['per_page'],
            $cachedData['current_page'],
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // 4. ارسال به Resource (حالا بدون خطا کار می‌کند)
        return ProductResource::collection($paginator);
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $validated['id'] = (string) Str::uuid();

        $product = DB::transaction(function () use ($validated) {
            return Product::create($validated);
        });

        Cache::tags(['products'])->flush();

        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        $cacheKey = 'product:single:' . $product->id;

        // کش کردن به صورت آرایه ساده
        $cachedData = Cache::tags(['products'])->remember($cacheKey, $this->cacheTTL * 60, function () use ($product) {
            $product->load('category:id,name');
            return $product->toArray();
        });

        // تبدیل آرایه به مدل برای استفاده در Resource
        $productModel = Product::hydrate([$cachedData])->first();

        return new ProductResource($productModel);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($product, $validated) {
            $product->update($validated);
        });

        Cache::tags(['products'])->flush();

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            $product->delete();
        });

        Cache::tags(['products'])->flush();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}