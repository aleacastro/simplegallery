<?php

namespace Unscode\Galleries;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use SoftDeletes;

    /**
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     *
     * @return mixed
     */
    public function galleriable()
    {
        return $this->morphTo();
    }

    /**
     *
     * @return mixed
     */
    public function images()
    {
        return $this->hasMany(\Unscode\Galleries\Image::class)->orderBy('order', 'asc');
    }

    /**
     *
     *
     * @return mixed
     */
    public function image()
    {
        return $this->images()->first();
    }
}
