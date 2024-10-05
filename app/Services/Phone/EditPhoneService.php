<?php

namespace App\Services\Phone;

use App\Models\PhoneNumber;
use App\Services\User\UserVerificationService;
use App\Traits\AssistentFunctions;
use Exception;

class EditPhoneService
{
    public function edit($request,$type,$phone_id)
    {
        $new_phone = $request->validated();

        if(!isset($new_phone))
            throw new Exception('You should send the new phone number');

        $phone = match($type)
        {
            'Venue' => PhoneNumber::with('relatedTo')->find($phone_id),
            'Store' => PhoneNumber::with('relatedTo')->find($phone_id),
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

        $phone['phone_number'] = $new_phone['phone_number'];

        $phone->save();

    }
}


