<?php

namespace App\DesignPatterns;

use App\Models\Brand;

class BrandLeaf implements BrandComponent
{
    private $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function add(BrandComponent $component)
    {
        throw new \Exception("Cannot add to a leaf");
    }

    public function remove(BrandComponent $component)
    {
        throw new \Exception("Cannot remove from a leaf");
    }

    public function display()
    {
        return [
            'id'    => $this->brand->id,
            'name'  => $this->brand->name,
            'slug'  => $this->brand->slug,
            'image' => $this->brand->image,
            'products_count' => $this->brand->products()->count(),
        ];
    }
}
