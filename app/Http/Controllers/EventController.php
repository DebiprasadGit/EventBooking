<?php

namespace App\Http\Controllers;

use App\Models\EventBookingModel;
use App\Models\EventModel;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Nette\Utils\Json;
use Psy\Readline\Hoa\Console;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class EventController extends Controller
{
    //

    public function addEvent(Request $getreq)
    {

        // Log::info('addeventcalled');
        // Log::info($getreq->datetime);
        $validator = Validator::make($getreq->all(), [
            'name' => 'required|string',
            'datetime' => 'required|date',
            'location' => 'required|string',
            'seat' => 'required|integer',
            'price' => 'required|integer',
            'image' => 'required|image',
            // Use the unique rule with an array to specify the combination of fields

        ]);


        if ($validator->fails()) {
            // Log::info($validator->errors());
            return response()->json(['failed' => true, 'errors' => $validator->errors()], 422);
        }

        $result = EventModel::where('name', $getreq->name)
            ->where('eventtime', $getreq->datetime)
            ->where('location', $getreq->location)
            ->first();


        if (!is_null($result)) {
            Log::info($result);

            return response()->json(['duplicate' => true], 199);
        }
        try {
            $event = new EventModel();
            $event->name = $getreq->input('name');
            $event->eventtime = $getreq->input('datetime');
            $event->location = $getreq->input('location');
            $event->seat = $getreq->input('seat');
            $event->price = $getreq->input('price');
            if ($getreq->hasFile('image')) {
                $imagePath = $getreq->file('image')->store('event_images');
                $event->image = $imagePath;
            }

            $event->save();
            return response()->json(["success" => true], 200);
        } catch (\Exception $e) {
            return response()->json(["errors" => true, "message" => $e]);
        }
    }


    public function getEvent()
    {
        $events = EventModel::all();

        return response()->json(['events' => $events], 200);
    }

    public function getallEventDetails()
    {
        $events = EventModel::all();

        return response()->json(['eventdetails' => $events], 200);
    }

    public function getallBookingDetails()
    {
        $bookingdetails = EventBookingModel::all();
        return response()->json(['allbookingdetails' => $bookingdetails], 200);
    }

    public function getSearchedEvent(Request $getreq)
    {
        $query = $getreq->input('query');
        $events = EventModel::where('name', 'like', '%' . $query . '%')->get();


        return response()->json(['events' => $events], 200);
    }


    public function getImage($filename)
    {
        // Log::info("connected");
        $path = 'event_images/' . $filename;
        if (Storage::exists($path)) {
            return response()->file(storage_path('app/' . $path));
        } else {
            abort(404);
        }
    }


    public function bookTicket(Request $getreq)
    {
        $name = $getreq->name;
        $eventtime = $getreq->eventtime;
        $location = $getreq->location;
        $seat = $getreq->seat;
        $quantity = $getreq->quantity;
        $totalprice = $getreq->totalprice;
        $username = $getreq->username;



        $event = EventModel::where('name', $name)->where('eventtime', $eventtime)->where('location', $location)->first();

        $event->seat = $seat - $quantity;
        $event->save();
        $bookingDetails = new EventBookingModel();
        try {
            $bookingDetails->eventid = $event->id;
            $bookingDetails->useremail = $username;
            $bookingDetails->totalprice = $totalprice;
            $bookingDetails->members = $quantity;

            $bookingDetails->save();

            return response()->json(['success' => true, 200]);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 199]);
        }
    }


    public function viewBookingDetails(Request $getreq)
    {
        $useremail = $getreq['username'];

        try {
            $bookingdata = EventBookingModel::where('useremail', $useremail)->get(['eventid', 'members', 'totalprice']);
            $bookingDetails = [];
            foreach ($bookingdata as $bookingItem) {
                $eventdata = EventModel::where('id', $bookingItem['eventid'])->first();
                if ($eventdata) {
                    $combinedData = [
                        'members' => $bookingItem['members'],
                        'totalprice' => $bookingItem['totalprice'],
                        'name' => $eventdata->name,
                        'eventtime' => $eventdata->eventtime,
                        'location' => $eventdata->location,
                        'image' => $eventdata->image,
                    ];

                    $bookingDetails[] = $combinedData;
                }
            }
            return response()->json(['bookingdetails' => $bookingDetails], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 199);
        }
    }
}
