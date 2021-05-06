<?php


namespace App\Http\Controllers;


use App\Models\AppCalendar;
use App\Models\Calendar;
use App\Models\Gcalendar;
use App\Models\GcalendarService;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{

    public function index(Request $request)
    {

        $pageDescription = "Танго календарь,  Танго фестивали в Украине,
        милонги в Киеве и других городах Украины, танго семинары, расписания танго школ.";


        ////////////////////////////////////////////
        // обновление данных о событиях раз в час
        $dateTime = \Date('Y-m-d H:i');
        $setDate = new \DateTime($dateTime);

        $TimeDataEvents = session('SetTimeDataEvents');
        $setTimeEvents = new \DateTime($TimeDataEvents);
        $setTimeEvents->modify('+1 hour');

        $messagesLog[] = "текущее время $dateTime время установки событий в сессии $TimeDataEvents";

        if ($setTimeEvents <= $setDate) {
            $messagesLog[] = 'удаляем данные о событиях из сессии';
            $request->session()->forget('DataEvents');
            session(['SetTimeDataEvents' => $dateTime]);
        }
        ///////////////////////////////////////////////
        /// список календарей

        //  получаем список календарей
        $calendars = Gcalendar::all();

        // объект приложения
        $appCalendar = AppCalendar::setAppCalendar();

        ////////////////////////////////////////////////////////
        // модификация даты старта календаря
        if (session()->has('calendarStart')) {
            $calendarDateStart = $appCalendar->setCalendarDateStart(session('calendarStart'));

            $messagesLog[] = "берем  время старта календаря из сессии $calendarDateStart";
        } else {
            $calendarDateStart = $appCalendar->setCalendarDateStart(\Date('Y-n-1'));
            session(['SetTimeDataEvents' => $calendarDateStart]);
        }
        $calendarDateStart = new \DateTime($calendarDateStart);
        $yearCalendar = $calendarDateStart->format('Y');
        $monthCalendar = $calendarDateStart->format('n') - 1;
        /////////////////////////////////////////////////////

        if (session()->has('calendarCollection')) {
            $collection = session('calendarCollection');
            $calendarsCollection = $appCalendar->installCalendarCollection($collection);
        } else {
            $calendarsCollection = $appCalendar->setCalendarCollection($calendars);
            session(['calendarCollection' => $calendarsCollection]);
        }

//        var_dump($calendarsCollection);

        if (session()->has('calendarTypeList')) {
            $calendarTypeList = session('calendarTypeList');
        } else {
            $calendarTypeList = $appCalendar->getCalendarsTypeList();
            session(['calendarTypeList' => $calendarTypeList]);
        }

        // ставим коллекцию событий из сессии
        if (session()->has('DataEvents')) {
            $appCalendar->setDataEvents(session('DataEvents'));
        }

        // обработка для выбранных календарей
        // также добавляет события в DataEventCollection
        $appCalendar->setSelectedCalendarsList(session('selectCalendars'));

        // получаем коллекцию событий
        $DataEventsCollection = $appCalendar->getDataEvents();

        // кеш информации календарей
        session(['DataEvents' => $DataEventsCollection]);

        
        $messagesLog[] = 'backend finished';
        $messagesLog[] = '----------------------------';

        return view('index', [
            'pageDescription' => $pageDescription,
            'yearCalendar'    => $yearCalendar,
            'monthCalendar'   => $monthCalendar,
            'messagesLog'     => $messagesLog,
            'DataEvents'      => $DataEventsCollection,
            'calendarList'    => $calendarTypeList,
            'calendarsCollection' => $calendarsCollection
        ]);


    }

    // для записи в сессию
    public function setAppCalendar(Request $request)
    {

        if (!empty($request->get('calendar_id'))) {
            $selected = $request->get('calendar_id');
            $selectCalendars = explode('|', $selected);
            $selectCalendars = array_flip($selectCalendars);
            session(['selectCalendars' => $selectCalendars]);
        }

        if (!empty($request->get('set_date'))) {
            session()->flash('calendarStart', $request->get('set_date'));
        }


        return redirect(route('index'));
    }





    public function privacyPolicy()
    {
        return view('privacy-policy');
    }

    public function userAgreement()
    {
        return view('user-agreement');
    }

}
