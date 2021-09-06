<?php

namespace App\Http\Controllers;

use App\Models\AppCalendar;
use App\Models\UserCalendar;
use App\Services\ICal;
use App\Services\iCalReader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $fb_events = false;
        $fb_events_count = 0;
        $user_fb_calendar = false;

        ////////////////////////////////////////////
        // обновление данных о событиях раз в час
        $dateTime = \Date('Y-m-d H:i');
        $setDate = new \DateTime($dateTime);

        $TimeDataEvents = session('SetTimeUserDataEvents');
        $setTimeEvents = new \DateTime($TimeDataEvents);
        $setTimeEvents->modify('+1 hour');


        $messagesLog[] = "время установки событий в сессии $TimeDataEvents";
        $messagesLog[] = "текущее время $dateTime";

        if ($setTimeEvents <= $setDate || empty($TimeDataEvents)) {
            $messagesLog[] = 'удаляем данные о событиях из сессии';
            $request->session()->forget('UserDataEvents');
            $newDateTimeevents = Date('Y-m-d H:i');
            session(['SetTimeUserDataEvents' => $newDateTimeevents]);
            $messagesLog[] = "новое время $newDateTimeevents";

        }
        //////////////////////////////////////////////////
        // объект приложения
        $appCalendar = AppCalendar::setAppCalendar();

        if (session()->has('calendarTypeList')) {
            $calendarTypeList = session('calendarTypeList');
        } else {
            $calendarTypeList = $appCalendar->getCalendarsTypeList();
            session(['calendarTypeList' => $calendarTypeList]);
        }
        /////////////////////////////////////////////////////
        if (session()->has('calendarCollection')) {
            $collection = session('calendarCollection');
            if (!empty($collection)) {
                $calendarsCollection = $appCalendar->installCalendarCollection($collection);
            } else {
                $calendarsCollection = $appCalendar->setCalendarCollection($calendars);
                session(['calendarCollection' => $calendarsCollection]);
            }
        } else {
            $calendarsCollection = $appCalendar->setCalendarCollection($calendars);
            session(['calendarCollection' => $calendarsCollection]);
        }
        ///////////////////////////////////////////////////////////


        $user_calendars = UserCalendar::where('userId', $user_id)->get()->toArray();

        if ($user_calendars) {
           foreach ($user_calendars as $calendar) {
               $type_events = $calendar['type_events'];
               $calendars[$type_events][] = $calendar;
           }
        }

        if (!empty($calendars['facebook'])) {
            $user_fb_calendar = $calendars['facebook'][0];
        }


        if (session()->has('UserDataEvents')) {
            $userDataEvents = session('UserDataEvents');
        }

        if (!empty($userDataEvents['facebook'])) {
            $fb_events = $userDataEvents['facebook'];

        } else {
            if (!empty($calendars['facebook'])){
                $fb_calendar = new iCalReader($user_fb_calendar['source']);


                if ($fb_calendar->hasEvents()) {
                    $fb_events = $fb_calendar->events();

                    $userDataEvents['facebook'] = $fb_events;
                    session(['UserDataEvents' => $userDataEvents]);
                }
            }
        }

        if (!empty($fb_events)) {

            $dateNau = new Carbon();

            foreach ($fb_events as $k => &$event) {
                $date = $event['DTSTAMP'];
                $date = new Carbon($date);
                $event['DTSTAMP'] = $date->format('Y-m-d H:i');

                $date = $event['LAST-MODIFIED'];
                $date = new Carbon($date);
                $event['LAST-MODIFIED'] = $date->format('Y-m-d H:i');

                $date = $event['CREATED'];
                $date = new Carbon($date);
                $event['CREATED'] = $date->format('Y-m-d H:i');


                $date = $event['DTEND'];
                $date = new Carbon($date);
                $event['date_end'] = $date->format('Y-m-d');
                $event['time_end'] = $date->format('H:i');

                $description = $event['DESCRIPTION'];
                $description = str_replace("\\n ", '', $description);
                $description = str_replace("\\n", '<br>', $description);
                $event['DESCRIPTION'] = $description;

                $date = $event['DTSTART'];
                $date = new Carbon($date);
                $event['date_start'] = $date->format('Y-m-d');
                $event['time_start'] = $date->format('H:i');

                if ($date < $dateNau) {
                    unset($fb_events[$k]);
                }
            }
            $fb_events_count = sizeof($fb_events);
        }



//        echo "<div class='pre'>";
//        var_dump($fb_events[0]);
//        echo "</div>";


        return view('user.dashboard', [
            'user'             => $user,
            'calendars'        => $calendars,
            'user_fb_calendar' => $user_fb_calendar,
            'fb_events'        => $fb_events,
            'fb_events_count'  => $fb_events_count,
            'calendarList'     => $calendarTypeList,
            'calendarCollection' => $calendarsCollection
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
