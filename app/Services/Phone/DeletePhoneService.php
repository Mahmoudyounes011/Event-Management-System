<?php

namespace App\Services\Phone;

use App\Models\PhoneNumber;
use App\Services\User\UserVerificationService;
use App\Traits\AssistentFunctions;
use Exception;

class DeletePhoneService
{
    use AssistentFunctions;

    public function delete($type,$phone_id)
    {

        $phone = match($type)
        {
            'Venue' => PhoneNumber::with('relatedTo.phones')->find($phone_id),
            'Store' => PhoneNumber::with('relatedTo.phones')->find($phone_id),
            default => throw new Exception('Invalid type')
        };

        if(!isset($phone))
            throw new Exception('Phone number not found');

        $owner_id = match($type)
        {
            'Venue' => $phone['phoneable']['user_id'],
            'Store' => $phone['phoneable']['user_id'],
        };

        UserVerificationService::verify($owner_id);

        $phones = match($type)
        {
            'Venue' => $phone['phoneable']['phones'],
            'Store' => $phone['phoneable']['phones'],
        };

        if(count($phones) == 1)
            throw new Exception('You can not delete this number , you must have at least one number for your '.$type);


        $phone->delete();

    }
}


