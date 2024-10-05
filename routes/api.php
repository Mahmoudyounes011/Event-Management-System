<?php

use App\Http\Controllers\_Event\EventController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Morph\TimeController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Store\StoreController;
use App\Http\Controllers\Venue\VenueController;
use App\Http\Controllers\Section\SectionController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\wallet\WalletController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Promotion\PromotionController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Rating\RatingController;
use App\Http\Controllers\FeedBack\FeedbackController;
use App\Http\Controllers\Morph\CommentController;
use App\Http\Controllers\Morph\ImageController;
use App\Http\Controllers\Morph\PhoneNumberController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Broadcast::routes(['middleware' => ['auth:api']]);


Route::post('/signup',[AuthenticateController::class,'signup']);

Route::post('/login',[AuthenticateController::class,'login']);

Route::post('/forgetPassword',[AuthenticateController::class,'forget_password']);

Route::post('/verifyCode/{resetCode}',[AuthenticateController::class,'verify_code']);

Route::post('/resetPassword',[UserController::class,'reset_password']);

Route::get('events/get/{type}/{event_id}',[EventController::class,'get']);//2

//just for front-end tests
Route::get('/roles/giveAdminRole/{user_id}',[RoleController::class,'make_admin']);

Route::group(['middleware'=> ['auth:api']],function()
{
    Route::post('/logout',[AuthenticateController::class,'logout']);

    Route::get('/get-users', [UserController::class,'get_all_users'])->middleware('admin');

    Route::get('/search-user', [UserController::class,'search']);

    Route::get('/delete_user/{user_id}', [UserController::class,'delete'])->middleware('admin');

    Route::post('/roles/buyOwnerAccount',[RoleController::class,'buy_owner_account']);

    Route::post('/categories/add',[CategoryController::class,'add'])->middleware('admin');

    Route::post('/AddTimeSchedule/{type}/{id}',[TimeController::class,'add_time_schedule'])->middleware('owner');

    Route::post('/editTimeSchedule/{type}/{time_id}',[TimeController::class,'edit'])->middleware('owner');

    Route::get('/deleteTimeSchedule/{time_id}',[TimeController::class,'delete'])->middleware('owner');

    Route::group(['prefix' => 'roles','middleware' => 'admin'],function()
    {
        Route::post('/add',[RoleController::class,'addRole']);

        Route::get('/getall',[RoleController::class,'index']);
    });

    Route::get('/get-wallet',[WalletController::class,'get']);

    Route::group(['prefix' => 'balance','middleware' => 'admin'],function()
    {
        Route::post('/add/{user_id}',[WalletController::class,'updateBalance']);
        Route::get('/payments',[PaymentController::class,'get_all']);

    });

    Route::group(['prefix' => 'images','middleware' => 'owner'],function()
    {
        Route::post('/add/{type}/{object_id}',[ImageController::class,'store']);
        Route::get('/delete/{type}/{image_id}',[ImageController::class,'delete']);
        Route::post('/edit/{type}/{image_id}',[ImageController::class,'edit']);

    });

    Route::group(['prefix' => 'phones','middleware' => 'owner'],function()
    {
        Route::post('/add/{type}/{object_id}',[PhoneNumberController::class,'store']);
        Route::get('/delete/{type}/{phone_id}',[PhoneNumberController::class,'delete']);
        Route::post('/edit/{type}/{phone_id}',[PhoneNumberController::class,'edit']);
    });



    Route::group(['prefix' => 'feedback','middleware' => 'admin'],function()
    {
        Route::get('/getall',  [FeedbackController::class,'get_all_feedbacks']);   //get all feedback

    });

    Route::get('/orders/user-orders',[OrderController::class,'user_orders']);

    Route::prefix('stores')->group(function ()
    {
        Route::get('/search/{name}',[StoreController::class,'search']);

        Route::get('/getAll',[StoreController::class,'get_all']);

        Route::post('/order/{store_id}',[OrderController::class,'add']);

        Route::get('/delete/{store_id}', [StoreController::class, 'deletestore'])->middleware('admin');  //mahmoud

        Route::get('/requests/{store_id}',[OrderController::class,'store_requests'])->middleware('notUser');

        Route::get('/orders/{store_id}',[OrderController::class,'store_orders'])->middleware('notUser');

        Route::get('/rejects/{store_id}',[OrderController::class,'store_rejects'])->middleware('notUser');

        Route::get('/products/getAll',[ProductController::class,'get_all']);

        Route::group(['middleware' => 'owner'],function()
        {
            Route::get('/getAllForOwner',[StoreController::class,'get_owner_stores']);

            Route::post('/add/{type}',[NotificationController::class,'send_if_add'])->middleware('isStore');

            Route::POST('/edit/{type}/{store_id}', [NotificationController::class, 'send_if_update'])->middleware('isStore');

            Route::post('/request/reply/{order_id}/{result}',[OrderController::class,'store_reply']);

            Route::prefix('/products')->group(function ()
            {
                Route::post('/add/{store_id}',[ProductController::class,'add_from_store']);

                Route::get('/edit/{product_id}/{new_price}', [ProductController::class, 'update']);

                Route::get('/delete/{product_id}', [ProductController::class, 'delete']);
            });
        });
    });

    Route::prefix('sections')->group(function ()
    {
        Route::get('/getAll/{venue_id}',[SectionController::class,'get_venue_sections']);

        Route::get('/getAllForCategory/{category_id}/{capacity}',[SectionController::class,'get_all_for_category']);

        Route::get('/Search/{category_id}/{capacity}/{venue_name}',[SectionController::class,'get_all_for_category']);

        Route::post('/FreeTimes/{section_id}',[SectionController::class,'get_free_times']);


        Route::group(['middleware' => 'owner'],function()
        {
            Route::post('/add/{venue_id}',[SectionController::class,'add']);
            Route::post('/edit/{section_id}', [SectionController::class, 'update']);  // this api without notification
            Route::get('/delete/{section_id}', [SectionController::class, 'delete']);
        });
    });

    Route::prefix('venues')->group(function ()
    {
        Route::get('/search/{name}',[VenueController::class,'search']);

        Route::get('/getAll',[VenueController::class,'get_all']);

        Route::get('/delete/{venue_id}', [VenueController::class, 'deletevenue'])->middleware('admin');        //mahmoud

        Route::group(['middleware' => 'owner'],function()
        {
            Route::get('/getAllForOwner',[VenueController::class,'get_owner_venues']);

            Route::post('/add/{type}',[NotificationController::class,'send_if_add'])->middleware('isVenue');

            Route::POST('/edit/{type}/{venue_id}', [NotificationController::class, 'send_if_update'])->middleware('isVenue');

        });
    });

    Route::prefix('notifications')->group(function ()
    {
        Route::get('/getAll',[NotificationController::class,'get_all']);

        Route::get('/markAsRead/{notification_id}',[NotificationController::class,'mark_as_read']);

        Route::post('/sendAll',[NotificationController::class,'notify_all']);

        Route::group(['middleware' => 'admin'],function()
        {
            Route::post('/reply/{notification_id}/{result}',[NotificationController::class,'reply'])->middleware('admin');

        });
    });

    Route::post('invitations/reply/{notification_id}/{result}',[NotificationController::class,'invitation_reply']);//1

    Route::post('invitations/linkReply/{type}/{event_id}/{result}',[EventController::class,'invitation_link_reply']);//2

    Route::prefix('categories')->group(function ()
    {
        Route::get('/getAll',[CategoryController::class,'get_all']);
    });


    Route::prefix('events')->group(function ()
    {
        Route::get('/Home',[EventController::class,'home']);
        Route::get('/suggestions',[EventController::class,'suggestions']);
        Route::get('/search',[EventController::class,'search']);
        Route::post('/createInVenue/{section_id}',[EventController::class,'craete_placed']);
        Route::post('/createCustom',[EventController::class,'craete_unplaced']);
        Route::get('/GetAllForUser',[EventController::class,'user_events']);
        Route::get('/GetAllRequestsForVenue/{venue_id}',[EventController::class,'venue_requests'])->middleware('notUser');
        Route::get('/GetAllEventsForVenue/{venue_id}',[EventController::class,'venue_events'])->middleware('notUser');
        Route::get('/GetAllRejectedEventsForVenue/{venue_id}',[EventController::class,'venue_rejects'])->middleware('notUser');
        Route::post('/reply/{event_id}/{result}',[EventController::class,'venue_reply'])->middleware('owner');
        Route::post('/delete/{event_id}',[EventController::class,'delete_unplaced'])->middleware('admin');
        Route::post('/register/{type}/{event_id}',[EventController::class,'register']);
        Route::get('/attenders/{type}/{event_id}',[EventController::class,'attenders']);
        Route::post('/deleteAttender/{type}/{event_id}/{user_id}',[EventController::class,'deleteAttender']);
        Route::get('/attender-events',[EventController::class,'attender_events']);

        Route::get('/invite/{type}/{event_id}/{user_id}',[EventController::class,'invite']);//1

        Route::get('verification/{type}/{event_id}',[EventController::class,'event_verification']);//2

        Route::prefix('comments')->group(function ()
        {
            Route::post('/add/{type}/{event_id}',[CommentController::class,'add']);
            Route::get('/getAll/{type}/{event_id}',[CommentController::class,'get_all']);
        });

    });

    Route::prefix('promotions')->group(function ()
    {
        Route::get('/getAll',[PromotionController::class,'get_all']);
        Route::post('/promote/{promote_id}/{event_id}/{event_type}',[PromotionController::class,'promote']);
    });

    Route::post('/rate/{type}/{id}', [RatingController::class,'store']);

    Route::prefix('feedbacks')->group(function ()
    {
        Route::post('/add', [FeedbackController::class,'store']);

        Route::get('/getAll', [FeedbackController::class,'get_all_feedbacks'])->middleware('admin');

        Route::get('/markAsRead/{feedback_id}', [FeedbackController::class,'mark_as_read'])->middleware('admin');
    });







});
