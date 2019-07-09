<?php

namespace Unscode\Galleries\Facades;

class Html
{
    public function form($model = '', $name = 'images')
    {
        return view('unscode/galleries::admin.galleries.form', [
            'gallery' => $model->galleries($name)->first()
            , 'name' => $name
        ])->render();
    }
}