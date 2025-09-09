<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use App\DesignPatterns\CategoryComposite;
use App\DesignPatterns\CategoryLeaf;

class CategoryController extends Controller
{
    public function categories()
    {
        // Get paginated categories
        $categories = Category::orderBy('id', 'ASC')->paginate(10);

        // Build composite
        $categoryComposite = new CategoryComposite();
        foreach ($categories as $category) {
            $categoryComposite->add(new CategoryLeaf($category));
        }

        // Replace paginator's collection
        $categoryList = new \Illuminate\Pagination\LengthAwarePaginator(
            collect($categoryComposite->display()),
            $categories->total(),
            $categories->perPage(),
            $categories->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.categories', compact('categoryList'));
    }

    public function category_add()
    {
        return view('admin.category-add');
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'required|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        $image = $request->file('image');
        $fileName = time() . '.' . $image->getClientOriginalExtension();
        $uploadPath = public_path('uploads/categories');

        $img = Image::read($image->getPathname());
        $img->cover(124, 124, 'top')->save($uploadPath . '/' . $fileName);

        Category::create([
            'name'  => $request->name,
            'slug'  => $request->slug,
            'image' => $fileName
        ]);

        return redirect()->route('admin.categories')
            ->with('status', 'Category has been added successfully!');
    }

    public function category_edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => 'nullable|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        $category = Category::findOrFail($request->id);
        $category->name = $request->name;
        $category->slug = $request->slug;

        if ($request->hasFile('image')) {
            if ($category->image && File::exists(public_path('uploads/categories/' . $category->image))) {
                File::delete(public_path('uploads/categories/' . $category->image));
            }

            $image = $request->file('image');
            $fileName = time() . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/categories');

            $img = Image::read($image->getPathname());
            $img->cover(124, 124, 'top')->save($uploadPath . '/' . $fileName);

            $category->image = $fileName;
        }

        $category->save();

        return redirect()->route('admin.categories')
            ->with('status', 'Category has been updated successfully!');
    }

    public function category_delete($id)
    {
        $category = Category::findOrFail($id);

        // Check if category has associated products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories')
                ->with('error', 'Cannot delete this category because it has ' . $category->products()->count() . ' associated product(s). Please remove or reassign the products first.');
        }

        if ($category->image && File::exists(public_path('uploads/categories/' . $category->image))) {
            File::delete(public_path('uploads/categories/' . $category->image));
        }

        $category->delete();

        return redirect()->route('admin.categories')
            ->with('status', 'Category has been deleted successfully!');
    }
}
