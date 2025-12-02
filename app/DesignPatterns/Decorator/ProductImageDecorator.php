<?php

namespace App\DesignPatterns\Decorator;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductImageDecorator extends ProductDecorator
{
    public function store(Request $request): Product
    {
        $product = parent::store($request);
        $this->handleImages($request, $product);
        $product->save();
        return $product;
    }

    public function update(Request $request, Product $product): Product
    {
        $product = parent::update($request, $product);
        $this->handleImages($request, $product, true);
        $product->save();
        return $product;
    }

    public function delete(Product $product): bool
    {
        $this->deleteImages($product);
        return parent::delete($product);
    }

    private function handleImages(Request $request, Product $product, bool $updating = false)
    {
        $manager = new ImageManager(new Driver());
        $current_timestamp = Carbon::now()->timestamp;

        // Main images
        if ($request->hasFile('image')) {
            if ($updating) {
                $this->deleteSingleImage($product->image);
            }
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->generateProductThumbnailImage($manager, $image, $imageName);
            $product->image = $imageName;
        }

        // Gallery
        if ($request->hasFile('images')) {
            if ($updating) {
                foreach (explode(',', $product->gallery ?? '') as $ofile) {
                    $this->deleteSingleImage($ofile);
                }
            }

            $gallery_arr = [];
            foreach ($request->file('images') as $file) {
                $ext = $file->getClientOriginalExtension();
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $gfileName = Str::uuid() . '.' . $ext;
                    $this->generateProductThumbnailImage($manager, $file, $gfileName);
                    $gallery_arr[] = $gfileName;
                }
            }
            $product->gallery = implode(',', $gallery_arr);
        }
    }

    private function generateProductThumbnailImage(ImageManager $manager, $image, $imageName)
    {
        $destinationPath = public_path('uploads/products');
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');

        $img = $manager->read($image->getPathname());

        // Save main
        $img->scaleDown(540, 689)->save($destinationPath . '/' . $imageName);

        // Save thumbnail
        $img->scaleDown(104, 104)->save($destinationPathThumbnail . '/' . $imageName);
    }

    private function deleteSingleImage(?string $fileName)
    {
        if (!$fileName) return;
        if (File::exists(public_path('uploads/products/' . $fileName))) {
            File::delete(public_path('uploads/products/' . $fileName));
        }
        if (File::exists(public_path('uploads/products/thumbnails/' . $fileName))) {
            File::delete(public_path('uploads/products/thumbnails/' . $fileName));
        }
    }

    private function deleteImages(Product $product)
    {
        $this->deleteSingleImage($product->image);
        foreach (explode(',', $product->gallery ?? '') as $file) {
            $this->deleteSingleImage($file);
        }
    }
}
