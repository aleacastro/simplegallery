<?php

namespace Mixdinternet\Galleries\Http\Controllers;

use Illuminate\Http\Request;
use Mixdinternet\Galleries\Image;
use Mixdinternet\Galleries\Gallery;
use App\Http\Controllers\AdminController;
use Folklore\Image\Facades\Image as FolkloreImage;

class GalleriesAdminController extends AdminController
{
    /**
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

            FolkloreImage::make(storage_path('cache/tmp') . '/' . $fileName, [
                'width' => config('mgalleries.galleries.width', 800),
                'height' => config('mgalleries.galleries.height', 600),
                'crop' => true,
                'quality' => config('mgalleries.galleries.quality', 90)
            ])->save($imagesPath . '/' . $fileName);

            if (config('mgalleries.watermark')) {
                $imagine = new \Imagine\Imagick\Imagine();
                $image = $imagine->open($imagesPath . '/' . $fileName);
                $watermark = $imagine->open(config('mgalleries.watermark'));
                $imageSize = $image->getSize();
                $watermarkSize = $watermark->getSize();
                $height = ($imageSize->getWidth() / ($watermarkSize->getWidth() / $watermarkSize->getHeight()));
                $watermark->resize(new \Imagine\Image\Box($imageSize->getWidth(), $height));
                $watermarkSize = $watermark->getSize();
                $position = new \Imagine\Image\Point(0, $imageSize->getHeight() - $watermarkSize->getHeight());
                $image->paste($watermark, $position);
                $image->save($imagesPath . '/' . $fileName);
            }
            return [
                'status' => 'success',
                'name' => $fileName
            ];
        }
        return [
            'status' => 'error'
        ];
    }

    /**
     *
     * @param Request $request
     * @return void
     */
    public function sort(Request $request)
    {
        $images = $request->get('image');
        foreach ($images as $k => $v) {
            Image::find($v)->update(['order' => $k]);
        }
    }

    /**
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        $id = $request->get('id');
        $description = $request->get('description');
        Image::find($id)->update(['description' => $description]);
    }

    /**
     *
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request)
    {
        $id = $request->get('id');
        Image::destroy($id);
    }
}
