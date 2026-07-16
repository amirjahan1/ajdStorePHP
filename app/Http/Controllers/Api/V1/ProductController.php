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
        // تغییر نام کلید برای تمایز از کش‌های قدیمی دیتابیس
        return 'products:list:elastic:' . md5(json_encode($params));
    }

        public function index(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 15), 100);
        $cacheKey = $this->getListCacheKey($request);

        $cachedData = Cache::tags(['products'])->remember($cacheKey, $this->cacheTTL * 60, function () use ($request, $perPage) {
            
            $searchTerm = $request->input('search', '');
            $builder = Product::search($searchTerm);

            // اعمال فیلترها
            if ($request->filled('category_id')) {
                $builder->where('category_id', $request->category_id);
            }
            if ($request->filled('min_price')) {
                $builder->where('price', '>=', (float) $request->min_price);
            }
            if ($request->filled('max_price')) {
                $builder->where('price', '<=', (float) $request->max_price);
            }
            if ($request->filled('in_stock')) {
                $builder->where('stock', '>', 0);
            }

            // --- بخش اصلاح‌شده مرتب‌سازی (Sorting) ---
            $sortField = $request->input('sort', 'created_at'); // پیش‌فرض روی created_at (که الان timestamp است)
            $sortOrder = $request->input('order', 'desc');
            
            // نگاشت فیلدهای متنی به معادل keyword آن‌ها برای الاستیک‌سرچ
            $elasticSortField = $sortField;
            if ($sortField === 'id') {
                $elasticSortField = 'id.keyword';
            } elseif ($sortField === 'name') {
                $elasticSortField = 'name.keyword';
            }

            // لیست فیلدهای مجاز برای مرتب‌سازی در الاستیک
            $allowedSortFields = ['created_at', 'price', 'name.keyword', 'id.keyword'];
            
            if (!in_array($elasticSortField, $allowedSortFields)) {
                $elasticSortField = 'created_at'; // بازگشت به حالت امن پیش‌فرض
            }
            
            $builder->orderBy($elasticSortField, $sortOrder);
            // -----------------------------------------

            return $builder->paginate($perPage)->toArray();
        });

        $models = Product::hydrate($cachedData['data']);

        $paginator = new LengthAwarePaginator(
            $models,
            $cachedData['total'],
            $cachedData['per_page'],
            $cachedData['current_page'],
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return ProductResource::collection($paginator);
    }

    
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $validated['id'] = (string) Str::uuid();

        $product = DB::transaction(function () use ($validated) {
            return Product::create($validated);
        });

        // پاک کردن کش لیست‌ها برای نمایش داده جدید
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

        // پاک کردن کش لیست‌ها و کش تکی این محصول
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