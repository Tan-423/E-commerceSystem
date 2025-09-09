<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $o_column = "";
        $o_sorting = "";
        $sorting = $request->query('sorting') ? $request->query('sorting') : -1;
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
        $min_price = $request->query('min') ? $request->query('min') : 1;
        $max_price = $request->query('max') ? $request->query('max') : 7000;
        switch ($sorting) {
            case 1:
                $o_column = 'created_at';
                $o_sorting = 'DESC';
                break;
            case 2:
                $o_column = 'created_at';
                $o_sorting = 'ASC';
                break;
            case 3:
                $o_column = 'regular_price';
                $o_sorting = 'ASC';
                break;
            case 4:
                $o_column = 'regular_price';
                $o_sorting = 'DESC';
                break;
            default:
                $o_column = 'id';
                $o_sorting = 'DESC';
        }
        $brands = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();

        $products = Product::where(function ($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'" . $f_brands . "' = ''");
        })
        ->where(function ($query) use ($f_categories) {
            $query->whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'" . $f_categories . "' = ''");
        })
        ->where(function ($query) use ($min_price,$max_price){
            $query->whereBetween('regular_price',[$min_price,$max_price])
            ->orWhereBetween('sale_price',[$min_price,$max_price]);
        })
        ->orderBy($o_column, $o_sorting)->paginate(12);

        return view('shop', compact('products', 'sorting', 'brands', 'f_brands', 'categories', 'f_categories','min_price','max_price'));
    }

    public function product_details($product_slug)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $product = Product::where('slug', $product_slug)->first();
        return view('details', compact('product'));
    }
}
