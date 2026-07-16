<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @tags Comments
 */
class CommentController extends Controller
{
    /**
     * Display comments for a product with optional filters.
     *
     * @summary لیست کامنت‌های یک محصول
     * @queryParam product_id uuid required شناسه محصول. Example: 123e4567-e89b-12d3-a456-426614174000
     * @queryParam approved boolean فیلتر بر اساس تایید شده. Example: true
     * @queryParam has_rate boolean فقط کامنت‌هایی که امتیاز دارند. Example: true
     */
     public function index(Request $request)
{
    $request->validate([
        'product_id' => 'required|uuid|exists:products,id',
    ]);

    $query = Comment::with('user', 'replies.user')
        ->where('product_id', $request->product_id)
        ->whereNull('parent_id'); // فقط کامنت‌های اصلی

    // بررسی نقش کاربر برای نمایش کامنت‌ها
    $user = Auth::user();
    $isAdmin = $user && in_array($user->role, ['admin', 'superAdmin']);

    if ($isAdmin) {
        // ادمین می‌تواند بر اساس وضعیت تایید فیلتر کند
        if ($request->filled('approved')) {
            $query->where('is_approved', $request->boolean('approved'));
        }
        // در غیر این صورت همه کامنت‌ها (اعم از تایید/رد) نمایش داده می‌شوند
    } else {
        // کاربر عادی فقط کامنت‌های تایید شده را می‌بیند
        $query->where('is_approved', true);
    }

    // فیلتر بر اساس وجود امتیاز
    if ($request->filled('has_rate')) {
        if ($request->boolean('has_rate')) {
            $query->whereNotNull('rate');
        } else {
            $query->whereNull('rate');
        }
    }

    $comments = $query->latest()->paginate(15);
    return CommentResource::collection($comments);
}

    /**
     * Store a new comment or reply.
     *
     * @summary ثبت کامنت جدید یا پاسخ به کامنت
     * @bodyParam product_id uuid required شناسه محصول. Example: 123e4567-e89b-12d3-a456-426614174000
     * @bodyParam parent_id uuid شناسه کامنت والد (برای پاسخ). Example: null
     * @bodyParam body string required متن کامنت. Example: Great product!
     * @bodyParam rate integer امتیاز (۱ تا ۵). Example: 5
     */
    public function store(StoreCommentRequest $request)
    {
        $validated = $request->validated();
        $validated['id'] = (string) \Illuminate\Support\Str::uuid();
        $validated['user_id'] = Auth::user()->uuid;
        $validated['is_approved'] = false; // نیاز به تایید ادمین

        // اگر امتیاز داده شده، حتماً باید عدد بین ۱ تا ۵ باشد (قبلاً در request بررسی شده)

        $comment = DB::transaction(function () use ($validated) {
            return Comment::create($validated);
        });

        return new CommentResource($comment);
    }

    /**
     * Update comment (only for admin to approve/reject).
     *
     * @summary ویرایش کامنت (فقط برای تایید/رد توسط ادمین)
     * @bodyParam is_approved boolean required وضعیت تایید. Example: true
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        // فقط ادمین یا سوپرادمین می‌تواند تایید/رد کند
        if (!in_array(Auth::user()->role, ['admin', 'superAdmin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validated();

        DB::transaction(function () use ($comment, $validated) {
            $comment->update($validated);
        });

        return new CommentResource($comment);
    }

    /**
     * Delete comment (admin can delete any, user can delete own).
     *
     * @summary حذف کامنت
     */
    public function destroy(Comment $comment)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'superAdmin']) && $comment->user_id !== $user->uuid) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($comment) {
            $comment->delete();
        });

        return response()->json(['message' => 'Comment deleted']);
    }
}