<?php
namespace App\Services\Section;

use App\Models\Level;
use App\Services\User\UserVerificationService;
use App\Traits\AssistentFunctions;
use Exception;

class EditSectionService
{
    use AssistentFunctions;

    public function edit($section_id,$request)
    {
        $section = GetSectionService::find($section_id,['venue'],null,true);

        UserVerificationService::verify($section['venue']['user_id']);

        $data = $request->validated();

        if(isset($data['description']))
        $section->description = $data['description'];

        if(isset($data['price']) && $data['price'] != $section->price && $section->price > 0)
        $section->price = $data['price'];

        if(isset($data['capacity']) && $data['capacity'] != $section->capacity)
        $section->capacity  = $data['capacity'];

        $section->save();


        // foreach($levels as $level)
        //     if($level['category']['section']['id'] != $section['id'])
        //         throw new Exception('This level can not be accessed within this section');

        $levels1 = null;
        $levels2 = null;
        $levelsIds = null;

        if(isset($data['new_levels_prices']))
            $levels1 = array_keys($data['new_levels_prices']);
        if(isset($data['new_levels_names']))
            $levels2 = array_keys($data['new_levels_names']);

        if(isset($levels1) && isset($levels2))
            $levelsIds = array_unique(array_merge($levels1,$levels2));

        if(isset($levels1) && !isset($levels2))
            $levelsIds = $levels1;

        if(!isset($levels1) && isset($levels2))
            $levelsIds = $levels2;

        if(isset($levelsIds))
        {
            $levels = Level::whereIn('id',$levelsIds)->with('category.section')->get();

            foreach($levels as $level)
            {
                if(isset($data['new_levels_prices']) && isset($data['new_levels_prices'][$level->id]))
                    $level->price = $data['new_levels_prices'][$level->id];

                if(isset($data['new_levels_names']) && isset($data['new_levels_names'][$level->id]))
                    $level->level = $data['new_levels_names'][$level->id];

                $level->save();
            }
        }


        if(isset($data['levels_prices']) && count($data['levels_prices'])>0)
        {
            $this->opposite($data['levels_names'],$data['levels_prices'],false,true,'Every name must have a price');
            $this->opposite($data['levels_prices'],$data['levels_names'],false,true,'Every price must have a name');

            foreach($data['levels_names'] as $category_id => $levelss)
            {
                $exist_category = $section->categories_pivot()->where('category_id',$category_id)->get();

                if($exist_category == null)
                    $exist_category = $section->categories_pivot()->create(['category_id' => $category_id]);

                foreach($levelss as $key => $level)
                    $exist_category[0]->levels()->create(['level' => $level,'price' => $data['levels_prices'][$category_id][$key]]);
            }
        }
        else
            if(isset($data['levels_names']))
                throw new Exception('You must send the prices and names for new levels');


        if(isset($data['delete_levels']) && count($data['delete_levels']) > 0)
        {
            $levels = Level::whereIn('id',$data['delete_levels'])->with('category.section')->get();

            foreach($levels as $level)
                if($level['category']['section']['id'] != $section['id'])
                    throw new Exception('This level can not be accessed within this section');

            foreach($levels as $level)
                $level->available = 0;

            $levels->each->delete();
        }

        if(isset($data['delete_categories']) && count($data['delete_categories']) > 0)
        {
            $exist_categories = $section->categories_pivot()->whereIn('category_id',$data['delete_categories'])->with('levels')->get();

            if(!isset($exist_categories[0]))
                throw new Exception('Categories not found');

            foreach($exist_categories as $key => $category)
            {
                foreach($category['levels'] as $level)
                {
                    $level->available = 0;
                    $level->save();
                }
                $category->available = 0;
                $category->save();
            }
        }

    }
}
