<?php

namespace App\Http\Controllers\Venue;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteVenueRequest;
use App\Http\Requests\EditRequest;
use App\Models\Venue;
use App\Services\Venue\DeleteVenueService;
use App\Services\Venue\EditVenueService;
use App\Services\Venue\GetVenueService;
use App\Services\Venue\SearchVenueService;
use Exception;
use Illuminate\Http\Request;

class VenueController extends Controller
{


    public function get_owner_venues(Request $request,GetVenueService $venues)
    {
        try
        {
            $venues = $venues->for_user($request->input('per_page')?$request->input('per_page'):10);
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
            'data' => $venues
        ]);
    }

    public function get_all(Request $request,GetVenueService $venues)
    {
        try
        {
            $venues = $venues->all($request->input('per_page')?$request->input('per_page'):10);
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
            'data' => $venues
        ]);
    }

    public function search($name,Request $request,GetVenueService $search)
    {
        try
        {
            $venues = $search->search($name,$request->input('per_page')?$request->input('per_page'):10);
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
            'data' => $venues
        ]);
    }


 public function deletevenue($venue_id, DeleteVenueService $venue)
{
        $venue->deleteVenueAndSections($venue_id);

        return response([
            'status' => 'success'
        ]);
    }

    public function update(EditRequest $request,EditVenueService $venue,$venue_id)
    {

        try
        {
            $venue->EditVenue($venue_id, $request);
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

        ]);
    }

}
