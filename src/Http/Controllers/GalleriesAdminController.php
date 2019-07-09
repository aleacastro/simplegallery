<?php

namespace Unscode\Galleries\Http\Controllers;

use Unscode\Galleries\Image;
use Illuminate\Http\Request;
use Unscode\Galleries\Gallery;
use Folklore\Image\Facades\Image as FolkloreImage;
use Mixdinternet\Admix\Http\Controllers\AdmixController;

class GalleriesAdminController extends AdmixController
{
    /**
     * Processa os arquivos enviados por upload
     *
     * @param Request $request
     * @return array
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $tmpPath = storage_path('cache/tmp');
            $imagesPath = storage_path('cache/images');

            @mkdir($tmpPath, 0775, true);
            @mkdir($imagesPath, 0775, true);

            $fileInfo = pathinfo($file->getClientOriginalName());
            $fileName = str_slug(str_limit($fileInfo['filename'], 50, '') . '-' . rand(1, 999)) . '.' . $file->getClientOriginalExtension();
            $file->move($tmpPath, $fileName);

            $config = config('mgalleries.galleries');
            $default = [
                'width' => 800
                , 'height' => 600
                , 'quality' => 90
            ];
            $mergeConfig = array_merge($default, $config);

            FolkloreImage::make(storage_path('cache/tmp') . '/' . $fileName, $mergeConfig)->save($imagesPath . '/' . $fileName);

            if (config('mgalleries.watermark')) {
                $imagine = new \Imagine\Imagick\Imagine();
                $watermark = $imagine->open(config('mgalleries.watermark'));
                $image = $imagine->open($imagesPath . '/' . $fileName);
                $size = $image->getSize();
                $watermark->resize(new \Imagine\Image\Box($size->getWidth(), $size->getHeight()));
                $wSize = $watermark->getSize();
                $position = new \Imagine\Image\Point($size->getWidth() - $wSize->getWidth(), $size->getHeight() - $wSize->getHeight());

                $image->paste($watermark, $position);
                $image->save($imagesPath . '/' . $fileName);
            }

            return [
                'status' => 'success'
                , 'name' => $fileName
            ];
        }

        return [
            'status' => 'error'
        ];
    }

    /**
     * Ordena as imagens no banco de dados
     *
     * @param Request $request
     */
    public function sort(Request $request)
    {
        $images = $request->get('image');

        foreach ($images as $k => $v) {
            $image = Image::find($v);
            if ($image) {
                $image->update(['order' => $k]);
            }
        }
    }

    /**
     * Atualiza informações das imagens no banco de dados
     *
     * @param Request $request
     */
    public function update(Request $request)
    {
        $id = $request->get('id');
        $description = $request->get('description');
        $image = Image::find($id);

        if ($image) {
            $image->update(['description' => $description]);
        }

    }

    /**
     * Remove uma image no banco de dados
     *
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        $id = $request->get('id');

        Image::destroy($id);
    }
}
