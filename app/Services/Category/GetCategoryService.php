<?php

namespace App\Services\Category;

use App\Models\Category;
use Exception;

class GetCategoryService
{
    public static function all()
    {
        $categories = Category::all();

        if(!isset($categories[0]))
            throw new Exception('There are no categories');

        return $categories;
    }

    public static function find($id,$with = null)
    {
        if(isset($with) && $with)
        {
            $category = Category::with('section_category')->find($id)->makeHidden('id','name');

            $category =  $category->section_category;


            if(isset($category))
            {
                $result = [];

                foreach($category as $key => $res)
                {
                    $section = $res->section;

                    $venue = $res->section->venue;

                    $levels = $res->levels;
                    $pivot_id = $res->id;

                    $section['pivot_id'] = $pivot_id;
                    $section['levels'] = $levels;

                    unset($venue->user_id);
                    $venueId = $venue->id;

                    if(!isset($result[$venueId]))
                    {
                        $result[$venueId] = [];

                        $result[$venueId][] = $venue;
                    }


                    $sectionId = $res->section->id;

                    unset($res->section->venue);
                    $result[$venueId][$sectionId] = $res->section;
                }

                $category = $result;
            }
        }
        else
        $category = Category::find($id)?->first();

        if(!isset($category))
            throw new Exception('Category not found');

            return $category;
    }
}
