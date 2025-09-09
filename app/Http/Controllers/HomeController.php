<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{

    public function index()
    {
        // Fetch featured products or latest products for the homepage
        $products = Product::orderBy('created_at', 'DESC')->take(8)->get();
        return view('index', compact('products'));
    }
}
