<?php
namespace App\Services\Store;

use App\Http\Requests\DeleteSectionRequest;
use App\Models\Section;
use App\Models\Store;
use App\Models\User;
use App\Models\Venue;
use App\Notifications\UserNotification;
use App\Services\User\UserVerificationService;
use Illuminate\Support\Facades\Notification;
use Exception;
use Illuminate\Support\Facades\Auth;



class EditStoreService
{
    public function EditStore($store_id,$request)
    {

        $store = GetStoreService::find($store_id);

        UserVerificationService::verify($store->user_id);



    }

}
