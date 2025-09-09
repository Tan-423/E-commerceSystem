<?php

namespace App\DesignPatterns;

use App\Models\Category;

class CategoryLeaf extends CategoryComponent
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function display()
    {
        return [
            'id'    => $this->category->id,
            'name'  => $this->category->name,
            'slug'  => $this->category->slug,
            'image' => $this->category->image,
            'products_count' => $this->category->products()->count(),
        ];
    }
}
