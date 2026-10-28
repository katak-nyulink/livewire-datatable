<?php

namespace KatakNyulink\LivewireDatatable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}