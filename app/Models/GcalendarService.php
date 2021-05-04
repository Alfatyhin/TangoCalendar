<?php

namespace App\Models;

use Google_Client;
use Google_Service_Calendar;

class GcalendarService
{
    private $service;


    ///////////////////////////////////////////////
    ///  настройки полей для админки
    public static function getCategory()
    {
        $category = ['festivals', 'milongas', 'tango_school',
            'master_classes', 'festival_schedule', 'maestros_calendar'];
        return $category;
    }

    public static function getUserSource()
    {
        $source = ['admin', 'organizer', 'teacher', 'volunteer'];
        return $source;
    }
    ////////////////////////////////////

    public static function getGapClient()
    {
        $file = '../../gap/client_secret_1046737382657-aj1ug2a88t7nb9pb9kv3ijqg28qbrt30.apps.googleusercontent.com.json';
        if (file_exists($file)) {

            $client = new Google_Client();
            $client->setAuthConfig($file);
            return $client;

        } else {
            $res = "not file exist $file";
            return $res;
        }

    }

    public static function missingServiceAccountDetailsWarning()
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

    public static function checkServiceAccountCredentialsFile()
    {
        // service account file
        $application_creds = '../../gap/laravelTangoCalendar-09fd9ec20b64.json';

        return file_exists($application_creds) ? $application_creds : false;
    }

    public static function getService()
    {
        $client = new \Google_Client();

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

        return $service;
    }


    // получение информации о календарях



}