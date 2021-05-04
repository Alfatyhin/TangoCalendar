<?php


namespace App\Http\Controllers;


use App\Models\Gcalendar;
use App\Models\User;
use Google_Service_Calendar;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{



    public function index(Request $request)
    {

        $pageDescription = "Танго календарь,  Танго фестивали в Украине,
        милонги в Киеве и других городах Украины, танго семинары, расписания танго школ.";

        // обьявление массива событий
        $listEvents = [];
        // кеш событий
        $DataEvents = session('DataEvents');
        $calendarList = session('DataCalendars');

        // обновление раз в час
        $dateTime = \Date('Y-m-d H:i');
        $setDate = new \DateTime($dateTime);

        $TimeDataEvents = session('SetTimeDataEvents');
        $setTimeEvents = new \DateTime($TimeDataEvents);
        $setTimeEvents->modify('+1 hour');

        $messagesLog[] = "текущее время $dateTime время установки событий в сессии $TimeDataEvents";

        if ($setTimeEvents <= $setDate) {
            $messagesLog[] = 'удаляем данные о событиях из сессии';
            unset($DataEvents);
        }



        if (empty($calendarList)) {
            $messagesLog[] = "нет данных календарей в сессии";
        } else {
            $messagesLog[] = "берем данные календарей из сессии";
        }

        $addCalendarId = session('addCalendarId');
        $selectCalendars = session('selectCalendars');

        // модификация даты старта календаря
        if (session()->has('setDate')) {
            $operator = session('setDate');
            $calendarDateStart = new \DateTime(session('calendarStart'));

            if ($operator == 'plus') {
                $newDate = $calendarDateStart->modify('-1 month');
                $date = $newDate->format('Y-m');
            } else {
                $newDate = $calendarDateStart->modify('+1 month');
                $date = $newDate->format('Y-m');
            }

            session(['calendarStart' => $date]);
        }

        if (session()->has('calendarStart')) {
            $calendarDateStart = new \DateTime(session('calendarStart'));
        } else {
            $calendarDateStart = new \DateTime();
        }
        $timeMin = $calendarDateStart->format('Y-m') . '-01T00:00:00-00:00';
        $timeMax = $calendarDateStart->format('Y-m-t') . 'T23:59:00-00:00';
        $yearCalendar = $calendarDateStart->format('Y');
        $monthCalendar = $calendarDateStart->format('n') - 1;
        session(['calendarStart' => $timeMin]);


        $client = new \Google_Client();
        
        if ($credentials_file = Gcalendar::checkServiceAccountCredentialsFile()) {
            // set the location manually
            $client->setAuthConfig($credentials_file);
        } elseif (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
            // use the application default credentials
            $client->useApplicationDefaultCredentials();
        } else {
            echo Gcalendar::missingServiceAccountDetailsWarning();
            return;
        }


        ////////////////////////
        // инициализация сервиса
        $client->setApplicationName("laravelTangoCalendar");
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        $service = new Google_Service_Calendar($client);

        //  получаем список календарей
        $calendars = Gcalendar::all();

        foreach ($calendars as $item) {
            $id          = $item->id;
            $gcalendarId = $item->gcalendarId;
            $type_events = $item->type_events;
            $country     = $item->country;
            $city        = $item->city;
            $source      = $item->source;

            if (empty($calendarList[$type_events][$city][$id])) {
                // получаем метаданные календаря
                $calendar = $service->calendars->get($gcalendarId);
                $calendarName = $calendar->getSummary();
                $calendarDescription = $calendar->getDescription();

                // делаем массив для списка
                $calendarList[$type_events][$city][$id] = [
                    'calendarName' => $calendarName,
                    'id'           => $id,
                    'gcalendarId'  => $gcalendarId,
                    'source'       => $source,
                    'description'  => $calendarDescription
                ];
            } else {
                $calendarName = $calendarList[$type_events][$city][$id]['calendarName'];
            }



            // добавляем календарь если есть в сессии вспышка
            if ($addCalendarId == $id) {

                $selectCalendars[$id] = $gcalendarId;
                session(['selectCalendars' => $selectCalendars]);
            }

            if (empty($selectCalendars)) {

                // календарь по по умолчанию
                if ($type_events == 'festivals' && $country != 'All' ) {
                    $data = [$id => $gcalendarId];
                    session(['selectCalendars' => $data]);
                    $selectCalendars = session('selectCalendars');
                }
            }




            // заполняем событиями
            if (isset($selectCalendars[$id])) {

                if (empty($DataEvents[$yearCalendar][$monthCalendar][$id])) {
                    $messagesLog[] = "обновляем события календаря $calendarName";
                    // add selected
                    $calendarList[$type_events][$city][$id]['select'] = 'checked';
                    $calendarList[$type_events][$city][$id]['class'] = 'active';

                    $events = $service->events->listEvents($gcalendarId, [
                        'timeMin'      => $timeMin,
                        'timeMax'      => $timeMax,
                        'singleEvents' => true,
                    ]);


                    foreach ($events->getItems() as $event) {
                        $eventId          = $event->getId();
                        $eventName        = $event->getSummary();
                        $eventDescription = $event->getDescription();
                        $eventLocation    = $event->getLocation();

                        $eventDescLite = mb_substr($eventDescription, 0, 200);

                        if (empty($eventDescription)) {
                            $eventDescription = '';
                        }

                        if ($country != 'All') {
                            $eventLocation = $country . '<br>' . $eventLocation;
                        }


                        $dateStartObj = $event->getStart();
                        $dateStart    = $dateStartObj->getDateTime();
                        $date         = new \DateTime($dateStart);
                        $dateStart    = $date->format('Y-n-j');
                        $timeStart    = $date->format('H-i');


                        $dateEndtObj = $event->getEnd();
                        $dateEnd     = $dateEndtObj->getDateTime();
                        $date        = new \DateTime($dateEnd);
                        $dateEnd     = $date->format('Y-n-j');
                        $timeEnd     = $date->format('H-i');

                        $lastModifed = $event->getUpdated();
                        $date        = new \DateTime($lastModifed);
                        $dateMod     = $date->format('Y-m-d H:i');


                        $listEvents[$dateStart]["$id-$eventId"] = [
                            'calendarId'  => $id,
                            'eventId'     => $eventId,
                            'name'        => $eventName,
                            'description' => $eventDescLite,
                            'location'    => $eventLocation,
                            'dateStart'   => $dateStart,
                            'timeStart'   => $timeStart,
                            'dateEnd'     => $dateEnd,
                            'timeEnd'     => $timeEnd,
                            'update'      => $dateMod,
                        ];

                    }

                    // кеш событий
                    $DataEvents[$yearCalendar][$monthCalendar][$id] = $listEvents;
                    session(['DataEvents' => $DataEvents]);
                    // записываем время установки данных в сессию
                    session(['SetTimeDataEvents' => Date('Y-m-d H:i')]);

                } else {
                    // add selected
                    $calendarList[$type_events][$city][$id]['select'] = 'checked';
                    $calendarList[$type_events][$city][$id]['class'] = 'active';

                    $listEvents = $DataEvents[$yearCalendar][$monthCalendar][$id];
                    $messagesLog[] = "берем события календаря $calendarName из сессии";
                }

            } else {
                // add selected
                $calendarList[$type_events][$city][$id]['select'] = null;
                $calendarList[$type_events][$city][$id]['class'] = 'no_active';
            }

        }

        // кеш информации календарей
        session(['DataCalendars' => $calendarList]);







        $test = session('selectCalendars');
//        var_dump($calendarEvents);

        $messagesLog[] = 'backend finished';
        $messagesLog[] = '----------------------------';

        return view('index', [
            'pageDescription' => $pageDescription,
            'calendarList'    => $calendarList,
            'selectCalendars' => $selectCalendars,
            'listEvents'      => $listEvents,
            'yearCalendar'    => $yearCalendar,
            'monthCalendar'   => $monthCalendar,
            'messagesLog'     => $messagesLog,
            'DataEvents'      => $DataEvents,
        ]);


    }

    // для записи в сессию
    public function getEvents(Request $request)
    {

        if (!empty($request->get('calendar_id'))) {

            $selectCalendars = session('selectCalendars');
            $id = $request->get('calendar_id');

            if(isset($selectCalendars[$id])) {
                unset($selectCalendars[$id]);
                session(['selectCalendars' => $selectCalendars]);
            } else {
                session()->flash('addCalendarId', $request->post('calendar_id'));
            }

        }

        if (!empty($request->get('set_date'))) {
            session()->flash('setDate', $request->get('set_date'));
        }


        return redirect(route('index'));
    }

    public function gappautorise()
    {

        $client = Gcalendar::getGapClient();
        // Ваш URI перенаправления может быть любым зарегистрированным URI, но в этом примере
        // мы перенаправляем обратно на эту же страницу
        $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'];

        $client->setRedirectUri($redirect_uri);
        $client->addScope("https://www.googleapis.com/auth/calendar");
        $service = new Google_Service_Calendar($client);


        /************************************************
         * If we have a code back from the OAuth 2.0 flow,
         * we need to exchange that with the
         * Google\Client::fetchAccessTokenWithAuthCode()
         * function. We store the resultant access token
         * bundle in the session, and redirect to ourself.
         ************************************************/
        if (isset($_GET['code'])) {

            $token = $_GET['code'];
            var_dump($token);
            $token = $client->fetchAccessTokenWithAuthCode($token);
            $client->setAccessToken($token);

            // store in the session also
            $_SESSION['upload_token'] = $token;

            var_dump($_SESSION);

            // redirect back to the example
//            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));

            file_put_contents('../../gap/test-get.json', json_encode($_GET));
        }

        // set the access token as part of the client
        if (!empty($_SESSION['upload_token'])) {
            $client->setAccessToken($_SESSION['upload_token']);
            if ($client->isAccessTokenExpired()) {
                unset($_SESSION['upload_token']);
            }
        } else {
            $authUrl = $client->createAuthUrl();
        }

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
