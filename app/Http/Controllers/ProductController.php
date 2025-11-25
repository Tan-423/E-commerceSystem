<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\DesignPatterns\Decorator\BaseProductService;
use App\DesignPatterns\Decorator\ProductImageDecorator;

class ProductController extends Controller
{
    protected $service;

    public function __construct()
    {
        // Wrap base service with image decorator
        $this->service = new ProductImageDecorator(new BaseProductService());
    }

    public function products()
    {
        $products = Product::orderBy('created_at', 'ASC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function add_product()
    {
        // Add product
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories', 'brands'));
    }

    public function product_store(Request $request)
    {
        $this->service->store($request);
        return redirect()->route('admin.products')->with('status', 'Product has been added successfully');
    }

    public function edit_product($id)
    {
        $product = Product::find($id);
        $categories = Category::Select('id', 'name')->orderBy('name')->get();
        $brands = Brand::Select('id', 'name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product', 'categories', 'brands'));
    }

    public function update_product(Request $request)
    {
        $product = Product::find($request->id);
        $this->service->update($request, $product);
        return redirect()->route('admin.products')->with('status', 'Record has been updated successfully !');
    }

    public function delete_product($id)
    {
        $product = Product::find($id);
        $this->service->delete($product);
        return redirect()->route('admin.products')->with('status', 'Record has been deleted successfully !');
    }
}
