<?php

namespace App\Models;

use Google_Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gcalendar extends Model
{
    use HasFactory;

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

}
