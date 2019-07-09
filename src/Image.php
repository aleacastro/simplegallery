<?php

namespace Unscode\Galleries;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    /**
     *
     * @var string
     */
    protected $table = 'galleries_images';

    /**
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'order'];

    /**
     *
     * @return mixed
     */
    public function gallery()
    {
        return $this->belongsTo(\Unscode\Galleries\Gallery::class);
    }
}
