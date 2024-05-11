<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $limit = request('limit') ?? 10;
        $query = request('query') ?? '';
        $products = Product::where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('name', 'like', "%".$query."%")
                                 ->orWhere('descriptions', 'like', '%'.$query.'%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        return response()->json($products);
    }

    public function store(ProductRequest $productRequest)
    {
        $product = Product::create([
            'name' => $productRequest->name,
            'descriptions' => $productRequest->descriptions,
            'category' => $productRequest->category,
            'date_added' => Carbon::parse($productRequest->date_added)->toDateTimeString(),
            'user_id' => auth()->user()->id,
            'img' => $productRequest->img,
        ]);

        // Handle file uploads
        $imagePaths = [];
        if ($productRequest->hasFile('images')) {
            foreach ($productRequest->file('images') as $image) {
                // Store each image and get its path
                $path = $image->store('products', 'public');
                $imagePaths[] = Storage::url($path); // Get the full URL path
            }
        }

        // Store image paths as an array in the images field
        $product->img = $imagePaths;
        $product->save();

        return $product;
    }

    public function update(Product $product, ProductRequest $productRequest)
    {
        $product->update([
            'name' => $productRequest->name,
            'description' => $productRequest->description,
            'category' => $productRequest->category,
        ]);

        return $product;
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

    public function show(Product $product)
    {
        return response()->json($product);
    }
}
