<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * @tags Categories
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of categories with filters.
     *
     * @summary لیست دسته‌بندی‌ها
     * @queryParam search string جستجو در نام و slug. Example: electronics
     * @queryParam parent_id uuid فیلتر بر اساس والد (برای گرفتن زیردسته‌ها). Example: 123e4567-e89b-12d3-a456-426614174000
     * @queryParam sort string فیلد مرتب‌سازی (name, slug, created_at). Example: name
     * @queryParam order string جهت مرتب‌سازی (asc, desc). Example: asc
     * @queryParam per_page int تعداد در صفحه. Example: 15
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // فیلتر بر اساس جستجوی متنی
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        // فیلتر بر اساس والد
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // مرتب‌سازی
        $sortField = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $perPage = $request->input('per_page', 15);
        $categories = $query->paginate($perPage);

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created category.
     *
     * @summary ایجاد دسته‌بندی جدید
     * @bodyParam name string required نام دسته‌بندی. Example: Electronics
     * @bodyParam slug string required اسلاگ یکتا. Example: electronics
     * @bodyParam parent_id uuid شناسه دسته والد (اختیاری). Example: 123e4567-e89b-12d3-a456-426614174000
     */
    public function store(StoreCategoryRequest $request)
    {
        
        $validated = $request->validated();
        $validated['id'] = (string) Str::uuid();
        
        $category = DB::transaction(function () use ($validated) {
            return Category::create($validated);
        });

        return new CategoryResource($category);
    }


    /**
 * Get categories as a nested tree structure up to specified depth.
 *
 * @summary دریافت درخت دسته‌بندی‌ها (همراه با زیرمجموعه‌ها تا عمق مشخص)
 * @queryParam depth integer عمق درخت (پیش‌فرض ۵). Example: 3
 * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
 */
public function tree(Request $request)
{
    $depth = (int) $request->input('depth', 5);
    
    // ساخت آرایه از روابط children برای بارگذاری تا عمق مورد نظر
    $relations = [];
    $current = 'children';
    for ($i = 0; $i < $depth; $i++) {
        $relations[] = $current;
        $current .= '.children';
    }
    
    // دریافت دسته‌بندی‌های ریشه (بدون والد) به همراه فرزندان تا عمق مشخص
    $categories = Category::with($relations)
        ->whereNull('parent_id')
        ->get();
    
    return CategoryResource::collection($categories);
}

    /**
     * Display the specified category.
     *
     * @summary نمایش جزئیات دسته‌بندی
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified category.
     *
     * @summary ویرایش دسته‌بندی
     * @bodyParam name string نام دسته‌بندی. Example: Electronics
     * @bodyParam slug string اسلاگ یکتا. Example: electronics
     * @bodyParam parent_id uuid شناسه دسته والد (اختیاری). Example: null
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($category, $validated) {
            $category->update($validated);
        });

        return new CategoryResource($category);
    }

    /**
     * Remove the specified category.
     *
     * @summary حذف دسته‌بندی
     */
    public function destroy(Category $category)
    {
        DB::transaction(function () use ($category) {
            $category->delete();
        });

        return response()->json(['message' => 'Category deleted successfully']);
    }
}