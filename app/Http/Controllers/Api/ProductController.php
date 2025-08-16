<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /api/products
     * Geeft een paginatie-lijst van producten terug.
     */
    public function index()
    {
        $products = Product::query()->latest()->paginate(10);
        // Resource-collectie voegt automatisch 'data', 'links', 'meta' toe bij paginator
        return ProductResource::collection($products);
    }

    /**
     * POST /api/products
     * Maakt een nieuw product aan.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // default true als niet meegegeven
        if (! array_key_exists('is_active', $data)) {
            $data['is_active'] = true;
        }

        $product = Product::create($data);

        return new ProductResource($product);
    }

    /**
     * GET /api/products/{product}
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * PUT/PATCH /api/products/{product}
     * Werkt velden gedeeltelijk of volledig bij.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $product->update($data);

        return new ProductResource($product);
    }

    /**
     * DELETE /api/products/{product}
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent(); // 204
    }
}
