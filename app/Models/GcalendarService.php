<?php

namespace App\Models;

use Google_Client;
use Google_Service_Calendar;

class GcalendarService
{
    private static $_service;
    public $service;

    private function __construct()
    {
        self::getService();
    }

    static function setService()
    {
        if(!self::$_service) {
            self::$_service = new self();
        }
        return self::$_service;
    }

    public function getCalendarInfo($gcalendarId)
    {
        // получаем метаданные календаря
        $calendar = $this->service->calendars->get($gcalendarId);
        $calendarName = $calendar->getSummary();
        $calendarDescription = $calendar->getDescription();

        return ['name' => $calendarName, 'description' => $calendarDescription];
    }

    public function getCalendarEvents($gcalendarId, $timeMin, $timeMax, $count)
    {
        $data = [
            'timeMin'      => $timeMin,
            'timeMax'      => $timeMax,
            'orderBy'      => 'startTime',
            'singleEvents' => true,
        ];
        if ($count) {
            $data['maxResults'] = $count;;
        }

        $events = $this->service->events->listEvents($gcalendarId, $data);
        return $events;
    }




    private static function missingServiceAccountDetailsWarning()
    {
        $ret = "
    <h3 class='warn'>
      Warning: You need download your Service Account Credentials JSON from the
      <a href='http://developers.google.com/console'>Google API console</a>.
    </h3>
    <p>
      Once downloaded, move them into the root directory of this repository and
      rename them 'service-account-credentials.json'.
    </p>
    <p>
      In your application, you should set the GOOGLE_APPLICATION_CREDENTIALS environment variable
      as the path to this file, but in the context of this example we will do this for you.
    </p>";

        return $ret;
    }

    private static function checkServiceAccountCredentialsFile()
    {
        // service account file
        $application_creds = '../../gap/laravelTangoCalendar-09fd9ec20b64.json';

        return file_exists($application_creds) ? $application_creds : false;
    }

    private function getService()
    {
        $client = new Google_Client();
        if ($credentials_file = self::checkServiceAccountCredentialsFile()) {
            // set the location manually
            $client->setAuthConfig($credentials_file);
        } elseif (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
            // use the application default credentials
            $client->useApplicationDefaultCredentials();
        } else {
            echo self::missingServiceAccountDetailsWarning();
            return;
        }
        ////////////////////////
        // инициализация сервиса
        $client->setApplicationName("laravelTangoCalendar");
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        $service = new Google_Service_Calendar($client);

        $this->service = $service;
    }





    ////////////////////////////////////
//  /// метод для авторизации
//    private static function getGapClient()
//    {
//        $file = '../../gap/client_secret_1046737382657-aj1ug2a88t7nb9pb9kv3ijqg28qbrt30.apps.googleusercontent.com.json';
//        if (file_exists($file)) {
//
//            $client = new Google_Client();
//            $client->setAuthConfig($file);
//            return $client;
//
//        } else {
//            $res = "not file exist $file";
//            return $res;
//        }
//
//    }
//    public function gappautorise()
//    {
//
//        $client = Gcalendar::getGapClient();
//        // Ваш URI перенаправления может быть любым зарегистрированным URI, но в этом примере
//        // мы перенаправляем обратно на эту же страницу
//        $redirect_uri = 'https://' . $_SERVER['HTTP_HOST'];
//
//        $client->setRedirectUri($redirect_uri);
//        $client->addScope("https://www.googleapis.com/auth/calendar");
//        $service = new Google_Service_Calendar($client);
//
//
//        /************************************************
//         * If we have a code back from the OAuth 2.0 flow,
//         * we need to exchange that with the
//         * Google\Client::fetchAccessTokenWithAuthCode()
//         * function. We store the resultant access token
//         * bundle in the session, and redirect to ourself.
//         ************************************************/
//        if (isset($_GET['code'])) {
//
//            $token = $_GET['code'];
//            var_dump($token);
//            $token = $client->fetchAccessTokenWithAuthCode($token);
//            $client->setAccessToken($token);
//
//            // store in the session also
//            $_SESSION['upload_token'] = $token;
//
//            var_dump($_SESSION);
//
//            // redirect back to the example
////            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
//
//            file_put_contents('../../gap/test-get.json', json_encode($_GET));
//        }
//
//        // set the access token as part of the client
//        if (!empty($_SESSION['upload_token'])) {
//            $client->setAccessToken($_SESSION['upload_token']);
//            if ($client->isAccessTokenExpired()) {
//                unset($_SESSION['upload_token']);
//            }
//        } else {
//            $authUrl = $client->createAuthUrl();
//        }
//
//    }

}
