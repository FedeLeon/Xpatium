<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageService;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class ContentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Asset::where('user_id', $request->user()->id);

        $type = $request->get('type', 'all');
        if (in_array($type, ['image', 'video', 'file'], true)) {
            $query->where('type', $type);
        }

        $q = $request->get('q');
        if ($q) {
            $query->where('original_name', 'like', '%'.$q.'%');
        }

        $from = $request->get('from');
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        $to = $request->get('to');
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $sort = $request->get('sort', 'date_desc');
        $sortMap = [
            'date_desc' => ['created_at', 'desc'],
            'date_asc' => ['created_at', 'asc'],
            'name_asc' => ['original_name', 'asc'],
            'name_desc' => ['original_name', 'desc'],
        ];
        $order = $sortMap[$sort] ?? $sortMap['date_desc'];
        $query->orderBy($order[0], $order[1]);

        $assets = $query->paginate(24)->appends($request->all());

        $user = $request->user();
        $storageUsedBytes = $user->storageUsedBytes();
        $storageLimitBytes = $user->storageLimitBytes();
        $storagePercent = $storageLimitBytes > 0
            ? min(100, round(($storageUsedBytes / $storageLimitBytes) * 100, 2))
            : 0;
        $assetCount = (int) Asset::where('user_id', $user->id)->count();

        return view(
            'teacher.assets.index',
            compact(
                'assets',
                'type',
                'sort',
                'from',
                'to',
                'q',
                'storageUsedBytes',
                'storageLimitBytes',
                'storagePercent',
                'assetCount'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('teacher.assets.index');
    }

    public function destroy(Request $request, Asset $asset): RedirectResponse
    {
        if ($asset->user_id !== $request->user()->id) {
            abort(404);
        }

        if ($asset->path) {
            Storage::disk('public')->delete($asset->path);
        }
        if ($asset->thumb_path) {
            Storage::disk('public')->delete($asset->thumb_path);
        }

        $asset->delete();

        return redirect()->route('teacher.assets.index');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'assets' => ['required', 'array'],
            'assets.*' => ['integer'],
        ]);

        $assets = Asset::where('user_id', $request->user()->id)
            ->whereIn('id', $data['assets'])
            ->get();

        foreach ($assets as $asset) {
            if ($asset->path) {
                Storage::disk('public')->delete($asset->path);
            }
            if ($asset->thumb_path) {
                Storage::disk('public')->delete($asset->thumb_path);
            }
            $asset->delete();
        }

        return redirect()->route('teacher.assets.index');
    }

    public function uploadChunk(Request $request): JsonResponse
    {
        $handlerClass = HandlerFactory::classFromRequest($request);
        $receiver = new FileReceiver('file', $request, $handlerClass);
        if (! $receiver->isUploaded()) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $save = $receiver->receive();
        if ($save->isFinished()) {
            $file = $save->getFile();
            $user = $request->user();
            $type = $this->detectType($file);

            $storageLimitBytes = $user->storageLimitBytes();
            if ($storageLimitBytes > 0) {
                $storageUsedBytes = $user->storageUsedBytes();
                $incomingSize = (int) $file->getSize();
                if ($storageUsedBytes + $incomingSize > $storageLimitBytes) {
                    @unlink($file->getPathname());
                    return response()->json(['error' => 'Limite de almacenamiento alcanzado.'], 422);
                }
            }

            $path = $file->store('assets/'.$user->id.'/'.$type, 'public');
            $mime = $file->getClientMimeType();
            $size = $file->getSize();

            if ($type === 'image') {
                $converted = ImageService::convertToWebp('public', $path, 'assets/'.$user->id.'/image');
                $path = $converted['path'];
                $mime = $converted['mime'];
                $size = $converted['size'];
            }

            $asset = Asset::create([
                'user_id' => $user->id,
                'type' => $type,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $mime,
                'size' => $size,
            ]);

            $thumbPath = $this->generateThumbnail($asset);
            if ($thumbPath) {
                $asset->thumb_path = $thumbPath;
                $asset->save();
            }

            return response()->json(['done' => true, 'asset_id' => $asset->id]);
        }

        $handler = $save->handler();

        return response()->json([
            'done' => false,
            'progress' => $handler->getPercentageDone(),
        ]);
    }

    private function detectType($file): string
    {
        $mime = $file?->getClientMimeType();
        if ($mime && str_starts_with($mime, 'image/')) {
            return 'image';
        }

        if ($mime && str_starts_with($mime, 'video/')) {
            return 'video';
        }

        $ext = strtolower((string) $file?->getClientOriginalExtension());
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff', 'tif', 'heic'];
        $videoExts = ['mp4', 'mov', 'm4v', 'avi', 'mkv', 'webm'];
        if (in_array($ext, $imageExts, true)) {
            return 'image';
        }
        if (in_array($ext, $videoExts, true)) {
            return 'video';
        }

        return 'file';
    }

    private function generateThumbnail(Asset $asset): ?string
    {
        $thumbDir = 'assets/'.$asset->user_id.'/thumbs';

        if ($asset->type === 'image') {
            return ImageService::createImageThumbWebp('public', $asset->path, $thumbDir);
        }

        if ($asset->type === 'video') {
            return ImageService::createVideoThumbWebp('public', $asset->path, $thumbDir);
        }

        return null;
    }
}
