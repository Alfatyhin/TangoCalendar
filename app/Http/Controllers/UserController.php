<?php

namespace App\Http\Controllers;

use App\Models\UserCalendar;
use App\Services\ICal;
use App\Services\iCalReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $user_id = $user->id;

        $user_calendars = UserCalendar::where('userId', $user_id)->get()->toArray();

        if ($user_calendars) {
           foreach ($user_calendars as $calendar) {
               $type_events = $calendar['type_events'];
               $calendars[$type_events][] = $calendar;
           }
        }

        if (!empty($calendars['facebook'])){
            $user_fb_calendar = $calendars['facebook'][0];

            $fb_calendar = new iCalReader($user_fb_calendar['source']);
            $fb_events = $fb_calendar->events();

            echo "<div style=''>";
            var_dump($fb_events);
            echo "</pre>";


        }


        return view('user.dashboard', [
            'user' => $user,
            'calendars' => $calendars,
            'user_fb_calendar' => $user_fb_calendar
        ]);
    }

    public function addUserCalendar(Request $recuest)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $userCalendarData['userId'] = $user_id;

        if (!empty($recuest->post('fb_cal_link'))) {
            $fb_cal_link = $recuest->post('fb_cal_link');

            $userCalendar = UserCalendar::firstOrCreate(
                ['calendarId' => $fb_cal_link]
            );
            $userCalendar->userId = $user_id;
            $userCalendar->source = $fb_cal_link;
            $userCalendar->type_events = 'facebook';
            $res = $userCalendar->save();


        }

        if ($res) {
            $message = 'calendar save';
        } else {
            $message = 'error save calendar';
        }

        return view('messages', [
            'message' => $message
        ]);
    }
}
