<?php
namespace App\Services\Section;

use App\Models\Section;
use App\Services\Category\GetCategoryService;
use App\Services\Image\ImageService;
use App\Services\Rating\GetRatingService;
use App\Services\User\GetUserService;
use App\Services\User\UserVerificationService;
use App\Services\Venue\GetVenueService;
use App\Traits\AssistentFunctions;
use Exception;

class GetSectionService
{

    public static function all($venue_id,$with = null,$requests = null,$auth = null,$paginate=10)
    {
        $venue = GetVenueService::find($venue_id);

        if(isset($auth) && $auth && !GetUserService::find()->isAdmin())
            UserVerificationService::verify($venue->owner->id);

        if(isset($with))
        {

            if(isset($requests))
            {
                if($requests=='requests')
                    $sections = $venue->sections()->whereHas('requests')->with($with)->paginate($paginate);
                else if($requests=='rejects')
                    $sections = $venue->sections()->whereHas('rejects')->with($with)->paginate($paginate);
                else
                    $sections = $venue->sections()->whereHas('events')->with($with)->paginate($paginate);
            }
            else
                $sections = $venue->sections()->with($with)->paginate($paginate);

        }
        else
            $sections = $venue->sections;


        if(!isset($sections[0]))
            throw new Exception('There are no sections');

        return $sections;
    }

    public static function all_for_category($category_id)
    {
        $category = GetCategoryService::find($category_id);

        return $category;
    }

    public static function search($category_id,$capacity,$paginate=10,$venue_name=null)
    {

        $sections = Section::where('capacity','>=',$capacity)->with('name','photos','venue.photos','venue.phones','venue.times','venue.ratings','categories_pivot.levels')
        ->whereHas('categories_pivot',function($query) use ($category_id)
        {
            $query->where('category_id',$category_id)->where('available',1);
        });
        if(isset($venue_name))
        {
            $sections = $sections->whereHas('venue',function($query) use ($venue_name)
            {
                $query->where('name','like',$venue_name.'%');
            })->get()->makeHidden('name_id');
        }
        else
            $sections = $sections->get()->makeHidden('name_id');


        // Group the sections by their venue ID
        $groupedSections = $sections->mapToGroups(function ($section) {
        return [$section->venue->id => $section];
        });
        // Get the page number from the request (assuming you are using Laravel's request)
        $pageNumber = request()->query('page', 1);

        // Set the number of items per page
        $itemsPerPage = $paginate;

        // Calculate the offset based on the current page
        // $offset = ($pageNumber - 1) * $itemsPerPage;

        // Get the items for the current page
        $currentPageSections = $groupedSections->forPage($pageNumber, $itemsPerPage);

        // Get the total count of grouped items
        $totalItems = $groupedSections->count();

        // Create a LengthAwarePaginator instance to handle pagination
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentPageSections,
        $totalItems,
        $itemsPerPage,
        $pageNumber
        );

        // Set the path for the paginator (current URL)
        $paginator->setPath(request()->url());

        // Now you can use $paginator in your view for pagination links and data display

        $sections = $paginator;

        foreach($sections as $venue_id => $sub_sections)
        {
            unset($sub_sections[0]->venue->user_id);

            $times = $sub_sections[0]->venue->times->groupBy('day');

            $t = [];
            foreach($times as $time)
                $t[] = $time;

            unset ($sub_sections[0]->venue->times);

            $sections[$venue_id]->prepend($sub_sections[0]->venue);
            $sections[$venue_id][0]['times'] = $t;
            $sections[$venue_id][0]['rate'] = (new GetRatingService)->get($sections[$venue_id][0]);
            unset( $sections[$venue_id][0]['ratings']);

            foreach($sub_sections as $key => $section)
            {
                unset($section->venue);

                if(isset($section->categories_pivot))
                {
                    $categories = $section->categories_pivot;
                    $result = [];
                    unset($section->categories_pivot);
                    foreach($categories as $k => $cat)
                    {
                        if($cat->category_id == $category_id)
                        {
                            $result[] = $cat;
                        }
                    }
                    $section->categories_pivot = $result;
                }
            }
        }

        return $sections;
    }

    public static function find($section_id,$with = null,$select = null,$ignoreDeletion=null)
    {
        if(isset($select))
        {
            if(isset($ignoreDeletion) && $ignoreDeletion)
                $section = Section::withoutGlobalScope('available')->select($select)->find($section_id);
            else
                $section = Section::select($select)->find($section_id);
        }

        if(isset($with))
        {
            if(isset($ignoreDeletion) && $ignoreDeletion)
                $section = Section::withoutGlobalScope('available')->with($with)->find($section_id);
            else
                $section = Section::with($with)->find($section_id);
        }
        else
            $section = Section::find($section_id);

        if(!isset($section))
            throw new Exception('Section not found');

        return $section;
    }

}
