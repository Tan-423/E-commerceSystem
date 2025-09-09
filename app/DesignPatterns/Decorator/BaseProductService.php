<?php

namespace App\DesignPatterns\Decorator;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;

class BaseProductService implements ProductServiceInterface
{
    public function store(Request $request): Product
    {
        $product = new Product();
        $this->mapRequestToProduct($request, $product);
        $product->save();
        return $product;
    }

    public function update(Request $request, Product $product): Product
    {
        $this->mapRequestToProduct($request, $product);
        $product->save();
        return $product;
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    private function mapRequestToProduct(Request $request, Product $product)
    {   
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured ?? false;
        $product->quantity = $request->quantity;
    }
}
