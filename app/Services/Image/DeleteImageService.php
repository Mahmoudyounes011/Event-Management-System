<?php

namespace App\Services\Image;

use App\Models\Image;
use App\Services\User\UserVerificationService;
use App\Traits\AssistentFunctions;
use Exception;

class DeleteImageService
{
    use AssistentFunctions;

    public function delete($type,$image_id)
    {

        $image = match($type)
        {
            'Venue' => Image::with('relatedTo')->find($image_id),
            'Section' => Image::with('relatedTo.venue')->find($image_id),
            'Store' => Image::with('relatedTo')->find($image_id),
            'Event' => Image::with('relatedTo')->find($image_id),
            'PublicEvent' => Image::with('relatedTo')->find($image_id),
            'Product' => Image::with('relatedTo')->find($image_id),
            default => throw new Exception('Invalid type')
        };

        if(!isset($image))
            throw new Exception('Image not found');

        if('App\Models\\'.$type !== $image->imagable_type)
            throw new Exception('Image not related to this type');

        $owner_id = match($type)
        {
            'Venue' => $image['imagable']['user_id'],
            'Section' => $image['imagable']['venue']['user_id'],
            'Store' => $image['imagable']['user_id'],
            'Event' => $image['imagable']['user_id'],
            'PublicEvent' => $image['imagable']['user_id'],
            default => null
        };

        if(isset($owner_id))
            UserVerificationService::verify($owner_id);

        $image->delete();

    }
}


