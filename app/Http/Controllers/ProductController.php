<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;


class ProductController extends Controller
{
    public function index()
{
    $response = Http::get('https://fakestoreapi.com/products');

    if ($response->successful()) {
        $products = $response->json();

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['id' => $productData['id']],
                [
                    'title' => $productData['title'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'image' => $productData['image'],
                ]
            );
        }

        $products = Product::orderBy('created_at', 'desc')->get();

        return Inertia::render('ProductList', [
            'products' => $products
        ]);
    } else {
        // Manejo de error en caso de que la API falle
        dd('Error en la API:', $response->status());
    }
}

    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|url',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'image' => 'sometimes|required|url',
        ]);

        $product->update($validated);

        return response()->json($product, 200);
    }

    public function destroy(Product $product)
{
    $product->delete();

    return response()->json(null, 204);
}
}
