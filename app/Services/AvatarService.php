<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use RuntimeException;

class AvatarService
{
    public function store(UploadedFile $upload): string
    {
        // Decode and re-encode the image rather than copying the uploaded bytes.
        $manager = new ImageManager(new Driver);
        $prepared = $manager->read($upload->getRealPath())->cover(256, 256)->toPng();
        $path = 'avatars/'.Str::uuid().'.png';

        if (! Storage::disk('public')->put($path, (string) $prepared)) {
            Storage::disk('public')->delete($path);
            throw new RuntimeException('The prepared avatar could not be stored.');
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && preg_match('/\Aavatars\/[a-f0-9-]{36}\.png\z/', $path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
