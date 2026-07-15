<?php

namespace App\Scramble\Extensions;

use Dedoc\Scramble\Extensions\OperationExtension as BaseOperationExtension;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Types\ObjectType;
use Dedoc\Scramble\Support\Generator\Types\StringType;

class OperationExtension extends BaseOperationExtension
{
    public function handle(Operation $operation, \ReflectionClass $class, \ReflectionMethod $method)
    {
        // لیست متدهایی که می‌خواهیم فیلد profile را به file تغییر دهیم
        $targetMethods = ['store', 'update'];
        
        if (!in_array($method->getName(), $targetMethods)) {
            return;
        }

        // بررسی می‌کنیم که کنترلر UserController باشد
        if ($class->getName() !== 'App\Http\Controllers\UserController') {
            return;
        }

        // تغییر فیلد profile به type: string, format: binary
        $requestBody = $operation->requestBody;
        if ($requestBody) {
            $content = $requestBody->content;
            if (isset($content['multipart/form-data'])) {
                $schema = $content['multipart/form-data']->schema;
                
                if ($schema && $schema->type instanceof ObjectType) {
                    $properties = $schema->type->properties;
                    
                    // تغییر فیلد profile
                    if (isset($properties['profile'])) {
                        $properties['profile'] = (new StringType())
                            ->format('binary')
                            ->setDescription('تصویر پروفایل کاربر (حداکثر ۲ مگابایت)');
                    }
                }
            }
        }
    }
}