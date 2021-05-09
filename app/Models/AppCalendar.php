<?php


namespace App\Models;


class AppCalendar
{
    private static $_appCalendar;
    private $calendarsCollection;
    private $calendarsTypeList;
    private $selectedCalendarsList;
    private $calendarDateStart;
    private $DataEvents;


    private function __construct()
    {
    }

    static function setAppCalendar()
    {
        if(!self::$_appCalendar) {
            self::$_appCalendar = new self();
        }
        return self::$_appCalendar;
    }


    public function setCalendarCollection($calendars)
    {
        foreach ($calendars as $item) {
            $id = $item->id;
            $gcalendarId = $item->gcalendarId;
            $type_events = $item->type_events;
            $city        = $item->city;

            $gCalendarService = GcalendarService::setService();
            $calendarInfo = $gCalendarService->getCalendarInfo($gcalendarId);

            $item->setName($calendarInfo['name']);
            $item->setDescription($calendarInfo['description']);

            if (empty($this->selectedCalendarsList)) {
                // календарь по по умолчанию
                if ($type_events == 'festivals' && $item->country != 'All' ) {
                    $data = [$id => $id];
                    $this->selectedCalendarsList = $data;
                }
            }

            if (isset($this->selectedCalendarsList[$id])) {
               $item->setSelect('checked');
               $item->setClass('active');
            }

            $calendarList[$type_events][$city][] = $id;
            $calendarCollection[$id] = $item;
        }
        $this->calendarsTypeList = $calendarList;
        $this->calendarsCollection = $calendarCollection;

        return $this->calendarsCollection;
    }

    public function getCalendarCollection()
    {
        return $this->calendarsCollection;
    }

    public function installCalendarCollection($collection)
    {
        return $this->calendarsCollection = $collection;
    }


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

    /**
     * @return mixed
     */
    public function getSelectedCalendarsList()
    {
        return $this->selectedCalendarsList;
    }

    /**
     * @param mixed $selectedCalendarsList
     */
    public function setSelectedCalendarsList($selectedCalendarsList)
    {

        $dateStart = $this->calendarDateStart;
        $dateStart = new \DateTime($dateStart);

        $timeMin = $dateStart->format('Y-m') . '-01T00:00:00-00:00';
        $timeMax = $dateStart->format('Y-m-t') . 'T23:59:00-00:00';
        $year = $dateStart->format('Y');
        $month = $dateStart->format('n') - 1;

        $calendarsCollection = $this->calendarsCollection;
        foreach ($calendarsCollection as $id => $calendar) {
            if (isset($selectedCalendarsList[$id])) {
                $calendar->setSelect('checked');
                $calendar->setClass('active');

            } else {
                if (!empty($selectedCalendarsList)) {
                    $calendar->setSelect(null);
                    $calendar->setClass(null);
                }

            }

            $gcalendarId = $calendar->gcalendarId;
            $select = $calendar->getSelect();

            if ($select == 'checked') {
                // добавляем события в календарь
                $events = $this->getCalendarEvents($gcalendarId, $timeMin, $timeMax);
                $DataEvents = $this->DataEvents;
                $DataEvents[$id][$year][$month] = $events;
                $this->DataEvents = $DataEvents;

            }

        }

        $this->selectedCalendarsList = $selectedCalendarsList;

        return $this->calendarsCollection;
    }

    // получаем события календаря и формируем данные
    public function getCalendarEvents($gcalendarId, $timeMin, $timeMax){

        $gCalendarService = GcalendarService::setService();
        $events = $gCalendarService->getCalendarEvents($gcalendarId, $timeMin, $timeMax);

        $listEvents = [];
        foreach ($events->getItems() as $event) {
            $eventId          = $event->getId();
            $eventName        = $event->getSummary();
            $eventDescription = $event->getDescription();
            $eventLocation    = $event->getLocation();

            if (empty($eventDescription)) {
                $eventDescription = '';
            }

            if (empty($eventLocation)) {
                $eventLocation = '';
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


            $listEvents[$dateStart] = [
                'eventId'     => $eventId,
                'name'        => $eventName,
                'description' => $eventDescription,
                'location'    => $eventLocation,
                'dateStart'   => $dateStart,
                'timeStart'   => $timeStart,
                'dateEnd'     => $dateEnd,
                'timeEnd'     => $timeEnd,
                'update'      => $dateMod,
            ];

        }

        return $listEvents;

    }

    /**
     * @return mixed
     */
    public function getCalendarDateStart()
    {
        return $this->calendarDateStart;
    }

    /**
     * @param mixed $calendarDateStart
     */
    public function setCalendarDateStart($calendarDateStart)
    {
        $this->calendarDateStart = $calendarDateStart;
        return $this->calendarDateStart;
    }

    /**
     * @return mixed
     */
    public function getCalendarsTypeList()
    {
        return $this->calendarsTypeList;
    }

    /**
     * @return mixed
     */
    public function getDataEvents()
    {
        return $this->DataEvents;
    }

    /**
     * @param mixed $DataEvents
     */
    public function setDataEvents($DataEvents)
    {
        $this->DataEvents = $DataEvents;

        return $this->DataEvents;
    }
}