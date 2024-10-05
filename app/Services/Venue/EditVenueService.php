<?php
namespace App\Services\Venue;


use App\Models\User;
use App\Models\Venue;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class EditVenueService
{
    public function EditVenue($venue_id,$request)
    {
        $venue = Venue::findOrFail($venue_id);

        $authenticatedUserId = Auth::id();

        if ($authenticatedUserId !== Venue::find($venue_id)->user_id)
        {

            return response(['error' => 'Unauthorized']);
        }

        $venue->name = $request->input('name');

        $venue->description = $request->input('description');

        $venue->save();

    }
}
