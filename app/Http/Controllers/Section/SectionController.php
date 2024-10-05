<?php

namespace App\Http\Controllers\Section;

use App\Events\TestEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteSectionRequest;
use App\Http\Requests\EditRequest;
use App\Http\Requests\EditSectionRequest;
use App\Http\Requests\GetFreeTimesRequest;
use App\Http\Requests\SectionRequest;
use App\Models\Section;
use App\Services\Section\EditSectionService;
use Illuminate\Support\Facades\Auth;
use App\Services\Category\GetCategoryService;
use App\Services\Section\AddSectionService;
use App\Services\Section\DeleteSectionService;
use App\Services\Section\GetSectionService;
use App\Services\Time\GetFreeTimesService;
use Exception;
use Illuminate\Http\Request;

class SectionController extends Controller
{


    public function get_venue_sections(Request $request,$venue_id,GetSectionService $get)
    {

        try
        {
            $sections = $get->all($venue_id,['photos','categories_pivot.levels','categories_pivot.category'],null,null,$request->input('per_page')?$request->input('per_page'):10);
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
            'data' => $sections
        ]);
    }

    public function get_all_for_category(Request $request,$category_id,$capacity,$venue_name=null)
    {

        try
        {
            $sections = GetSectionService::search($category_id,$capacity,$request->input('per_page')?$request->input('per_page'):10,$venue_name);
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
            'data' => $sections
        ]);
    }

    public function add(SectionRequest $request,$venue_id, AddSectionService $add)
    {
        try
        {
            $add->add($request,$venue_id);
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
            'message' => 'the section is added successfully'
        ]);

    }

    public function get_free_times(GetFreeTimesRequest $request,$section_id,GetFreeTimesService $times)
    {
        try
        {
            $free_times = $times->get($request,$section_id);
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
            'data' => $free_times
        ]);

    }


    public function delete(DeleteSectionService $section, $section_id)
    {

        try
        {
            $section->delete($section_id);
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
            'message' => 'Section is deleted successfully'

        ]);
    }


    public function update(EditSectionRequest $request,EditSectionService $section,$section_id)
    {
        try
        {
            $section->edit($section_id, $request);

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
            'message' => 'Section is updated successfully'

        ]);
    }





}
