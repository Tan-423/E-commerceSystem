<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use App\DesignPatterns\BrandComposite;
use App\DesignPatterns\BrandLeaf;

class BrandController extends Controller
{
    public function brands()
{
    // Get paginated brands from DB
    $brands = Brand::orderBy('id', 'ASC')->paginate(10);

    // Build composite
    $brandComposite = new BrandComposite();
    foreach ($brands as $brand) {
        $brandComposite->add(new BrandLeaf($brand));
    }

    // Replace paginator's collection with composite's display result
    $brandList = new \Illuminate\Pagination\LengthAwarePaginator(
        collect($brandComposite->display()),
        $brands->total(),
        $brands->perPage(),
        $brands->currentPage(),
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return view('admin.brands', compact('brandList'));
}


    public function add_brand()
    {
        return view('admin.brand-add');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'required|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        $image = $request->file('image');
        $fileName = time() . '.' . $image->getClientOriginalExtension();
        $uploadPath = public_path('uploads/brands');

        $img = Image::read($image->getPathname());
        $img->cover(124, 124, 'top')->save($uploadPath . '/' . $fileName);

        Brand::create([
            'name'  => $request->name,
            'slug'  => $request->slug,
            'image' => $fileName
        ]);

        return redirect()->route('admin.brands')
            ->with('status', 'Brand has been added successfully!');
    }

    public function brand_edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => 'nullable|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        $brand = Brand::findOrFail($request->id);
        $brand->name = $request->name;
        $brand->slug = $request->slug;

        if ($request->hasFile('image')) {
            if ($brand->image && File::exists(public_path('uploads/brands/' . $brand->image))) {
                File::delete(public_path('uploads/brands/' . $brand->image));
            }

            $image = $request->file('image');
            $fileName = time() . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/brands');

            $img = Image::read($image->getPathname());
            $img->cover(124, 124, 'top')->save($uploadPath . '/' . $fileName);

            $brand->image = $fileName;
        }

        $brand->save();

        return redirect()->route('admin.brands')
            ->with('status', 'Brand has been updated successfully!');
    }

    public function brand_delete($id)
    {
        $brand = Brand::findOrFail($id);

        // Check if brand has associated products
        if ($brand->products()->count() > 0) {
            return redirect()->route('admin.brands')
                ->with('error', 'Cannot delete this brand because it has ' . $brand->products()->count() . ' associated product(s). Please remove or reassign the products first.');
        }

        if ($brand->image && File::exists(public_path('uploads/brands/' . $brand->image))) {
            File::delete(public_path('uploads/brands/' . $brand->image));
        }

        $brand->delete();

        return redirect()->route('admin.brands')
            ->with('status', 'Brand has been deleted successfully!');
    }
}
