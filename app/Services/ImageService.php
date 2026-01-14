<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Symfony\Component\Process\Process;

class ImageService
{
    public static function convertToWebp(string $diskName, string $sourcePath, string $destDir, int $quality = 80): array
    {
        $disk = Storage::disk($diskName);
        $sourceFull = $disk->path($sourcePath);
        $disk->makeDirectory($destDir);

        $destName = Str::uuid()->toString().'.webp';
        $destPath = trim($destDir, '/').'/'.$destName;
        $destFull = $disk->path($destPath);

        $manager = ImageManager::gd();
        $manager->read($sourceFull)->toWebp($quality)->save($destFull);

        $disk->delete($sourcePath);

        return [
            'path' => $destPath,
            'mime' => 'image/webp',
            'size' => filesize($destFull) ?: 0,
        ];
    }

    public static function createImageThumbWebp(string $diskName, string $sourcePath, string $destDir, int $width = 360, int $height = 240, int $quality = 80): ?string
    {
        $disk = Storage::disk($diskName);
        $sourceFull = $disk->path($sourcePath);
        $disk->makeDirectory($destDir);

        $thumbName = Str::uuid()->toString().'.webp';
        $thumbPath = trim($destDir, '/').'/'.$thumbName;
        $thumbFull = $disk->path($thumbPath);

        $manager = ImageManager::gd();
        $manager->read($sourceFull)->cover($width, $height)->toWebp($quality)->save($thumbFull);

        return $thumbPath;
    }

    public static function createVideoThumbWebp(string $diskName, string $sourcePath, string $destDir, int $width = 360): ?string
    {
        if (! self::ffmpegAvailable()) {
            return null;
        }

        $disk = Storage::disk($diskName);
        $sourceFull = $disk->path($sourcePath);
        $disk->makeDirectory($destDir);

        $thumbName = Str::uuid()->toString().'.webp';
        $thumbPath = trim($destDir, '/').'/'.$thumbName;
        $thumbFull = $disk->path($thumbPath);

        $process = Process::fromShellCommandline(sprintf(
            'ffmpeg -y -i %s -ss 00:00:01.000 -vframes 1 -vf "scale=%d:-1" -c:v libwebp %s',
            escapeshellarg($sourceFull),
            $width,
            escapeshellarg($thumbFull)
        ));
        $process->run();

        return $process->isSuccessful() ? $thumbPath : null;
    }

    public static function ffmpegAvailable(): bool
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $process = new Process(['which', 'ffmpeg']);
        $process->run();
        $cached = $process->isSuccessful();

        return $cached;
    }
}
