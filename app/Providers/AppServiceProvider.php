<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $s3Client = new S3Client([
                'version' => 'latest',
                'endpoint' => env('MINIO_ENDPOINT'),
                'credentials' => [
                    'key' => env('MINIO_ROOT_USER'),
                    'secret' => env('MINIO_ROOT_PASSWORD'),
                ],
                'region' => env('MINIO_REGION', 'us-east-1'),
                'use_path_style_endpoint' => true,
            ]);

            $bucket = env('MINIO_BUCKET');

            // بررسی استاندارد وجود باکت در MinIO/S3
            if (!$s3Client->doesBucketExist($bucket)) {
                $s3Client->createBucket([
                    'Bucket' => $bucket,
                ]);
                Log::info("MinIO Bucket '{$bucket}' created successfully.");
            }
        } catch (\Exception $e) {
            // اگر مینیو در دسترس نباشد یا خطایی رخ دهد، برنامه متوقف نمی‌شود و فقط لاگ می‌گیرد
            Log::warning('MinIO Bucket check/create failed: ' . $e->getMessage());
        }
    }
}