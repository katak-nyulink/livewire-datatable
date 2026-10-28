<?php

namespace KatakNyulink\LivewireDatatable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}