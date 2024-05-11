<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class VideoController extends Controller
{
    public function index()
    {
        $limit = request('limit') ?? 10;
        $query = request('query') ?? '';
        $videos = Video::where('original_url', 'like', "%".$query."%")
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

        return response()->json($videos);
    }

    public function download()
    {
        $videoUrl = request()->input('url');

        $response = Http::get($videoUrl);
        if ($response->successful()) {
            $videoContents = $response->body();
            $fileName = uniqid() . '.mp4';
            $tempFilePath = 'public/videos/' . $fileName;
            Storage::put($tempFilePath, $videoContents);

            Video::create([
                'user_id' => auth()->user()->id,
                'original_url' => $videoUrl,
                'saved_url' => 'storage/videos/' . $fileName,
            ]);

            return response()->json(['status' => 200, 'url' => asset($tempFilePath)]);
        }

        return response()->json(['error' => 'Failed to download video'], 500);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    public function bulkDelete()
    {
        Product::whereIn('id', request('ids'))->delete();
        return response()->json(['message' => 'Selected product was successfully deleted!']);
    }
}
