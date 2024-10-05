<?php

namespace App\Http\Controllers\Morph;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddImageRequest;
use App\Http\Requests\AddPhoneRequest;
use App\Services\Image\DeleteImageService;
use App\Services\Image\EditImageService;
use App\Services\Image\EditPhoneService;
use App\Services\Image\ImageService;
use App\Services\Phone\DeletePhoneService;
use App\Services\Phone\StorePhoneService;
use Exception;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function store(AddImageRequest $request,$type,$object_id)
    {
        try
        {
            (new ImageService)->upload_image($request->file('images'),$type,$object_id);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }
        return response([
            'status' => 'success',
            'message' => 'Images are added successfully'
        ]);
    }

    public function edit(AddImageRequest $request,$type,$image_id)
    {
        try
        {
            (new EditImageService)->edit($request,$type,$image_id);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }
        return response([
            'status' => 'success',
            'message' => 'Image is edited successfully'
        ]);
    }
    public function delete($type,$image_id)
    {
        try
        {
            (new DeleteImageService)->delete($type,$image_id);
        }
        catch(Exception $e)
        {
            return response([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }
        return response([
            'status' => 'success',
            'message' => 'Image is deleted successfully'
        ]);
    }
}
