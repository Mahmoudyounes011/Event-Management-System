<?php
namespace App\Services\Section;

use App\Models\Name;
use App\Models\SectionCategory;
use App\Notifications\UserNotification;
use App\Services\Category\GetCategoryService;
use App\Services\Image\ImageService;
use App\Services\Notification\SendNotificationService;
use App\Services\Venue\GetVenueService;
use App\Traits\AssistentFunctions;
use Exception;

class AddSectionService
{

    use AssistentFunctions;

    public function add($request,$venue_id)
    {
        $venue = GetVenueService::find($venue_id,['times','owner','sections'],true);

        $data = $request->validated();


        $levels = $data['levels'];
        $level_prices = $data['level_prices'];

        $this->opposite($levels,$level_prices,true,false,'Each level must have a price');
        $this->opposite($level_prices,$levels,true,false,'Each price must related to a level');

        $prices = $data['prices'];
        $descriptions = $data['descriptions'];
        $capacities = $data['capacities'];

        $this->opposite($prices,$levels,false,false,'Each section must has at least one category');
        $this->opposite($descriptions,$prices,false,false,'Each section must has a price');
        $this->opposite($prices,$descriptions,false,false,'Each section must has a descriptions');
        $this->opposite($descriptions,$capacities,false,false,'Each section must has a capacity');

        $hasSections = count($venue['sections']);

        $names = Name::take(count($descriptions))->where('id','>',$hasSections)->get();

        $images = $request->file('images');

        foreach($descriptions as $key => $description)
        {
            $section = $venue->sections()->create(['description' => $description,'name_id' => $names[$key]['id'],'capacity' => $capacities[$key],'price' => $prices[$key]]);

            if(isset($images[$key]))
            {
                $paths = (new ImageService)->upload_image($images[$key],'section');
                foreach($paths as $path)
                    $section->photos()->create(['path' => $path]);
            }

            if(isset($levels[$key]))
            {
                foreach($levels[$key] as $category_id => $category_levels)
                {
                    GetCategoryService::find($category_id);

                    $pivot = $section->categories_pivot()->create(['category_id' => $category_id,'created_at' => now()]);

                    foreach($category_levels as $k => $level)
                    {
                        $pivot->levels()->create(['price' => $level_prices[$key][$category_id][$k],'level' => $level]);
                    }
                }
            }
        }

        if(isset($venue['times']) && isset($venue['times'][0]))
        {
            $venue->available = 1;
            $venue->save();

            $message = 'Hello '.$venue['owner']['name'].' your venue '.$venue['name'].' became visible to all users now';

            (new SendNotificationService)->sendNotify($venue['owner'],new UserNotification($venue['owner']['id'],$message,'Venue','Available'));

        }

    }

}
