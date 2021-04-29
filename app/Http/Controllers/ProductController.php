<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::getAndSort($request->sort);

        return response($products, 200);
    }

    /**
     * @param ProductRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response($product, 200);
    }

    /**
     * @param ProductRequest $request
     * @param Product $product
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response($product, 200);
    }

    public function destroy(ProductRequest $request, Product $product)
    {
        $product->delete();

        return response('', 200);
    }
}
