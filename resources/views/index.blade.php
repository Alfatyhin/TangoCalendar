<?php
/** @var \app\Models\AppCalendar $AppCalendar
 */
$verse = '1.3.5';
?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}" defer></script>
    <script src="{{ asset('js/coda.js') }}?{{$verse}}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/preloader.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?{{$verse}}" rel="stylesheet">
    <link href="{{ asset('css/master.css') }}?{{$verse}}" rel="stylesheet">

    <meta name="description" content="{{$pageDescription}}">
    <link type="image/x-icon" rel="shortcut icon" href="img/logo.ico">

    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ config('app.name', 'Laravel') }}">
    <meta property='og:description'   content='{{$pageDescription}}' >

    <meta property='og:image'   content='/img/logo-socal.png' />
    <meta property='og:image:secure_url'   content='/img/logo-socal.png' />
    <meta property="og:image:width" content="270">

    <meta property="og:url" content="/" >
    <meta property="og:site_name" content="{{ config('app.name', 'Laravel') }}" >
    <meta property="og:updated_time" content="">

    <script>
        var yearCalendar = {{$yearCalendar}};
        var monthCalendar = {{$monthCalendar}};
        var messagesLog = @json($messagesLog);
        var DataEvents = @json($DataEvents);
        var WorldFest = @json($worldFest);

    </script>
</head>

<body>

<div class="holder preloader_holder">
    <div class="preloader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
</div>

<div class="content">

        <h1>
            Tango Calendar
        </h1>

        <div class="calendar_list">
            <span class="caption">Calendars List</span>
            <span class="mobail menu-close"></span>
            <span class="mobail menu-open"></span>
            <ul class="calendars">
                <li class="first open festival_list">
                    <span>Festivals
                        <span class="count"></span>
                    </span>

                    <ul class="sub-menu">
                        @foreach($calendarList['festivals'] as $items)
                            @foreach($items as $id)
                                <li class="{{$calendarsCollection[$id]->getClass()}}">
                                    <label>
                                        <input class="calendar_id"
                                               type="checkbox" name="event_types"
                                               value="{{$id}}"
                                               {{$calendarsCollection[$id]->getSelect()}}
                                        />
                                        <span>{{$calendarsCollection[$id]->getName()}}
                                            <span class="count"></span>
                                        </span>

                                    </label>
                                    <div class="info">
                                        <div class="description">
                                            {{$calendarsCollection[$id]->getDescription()}}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        @endforeach
                        @foreach($calendarList['master_classes'] as $items)
                            @foreach($items as $id)
                                    <li class="{{$calendarsCollection[$id]->getClass()}}">
                                        <label>
                                            <input class="calendar_id"
                                                   type="checkbox" name="event_types"
                                                   value="{{$id}}"
                                                {{$calendarsCollection[$id]->getSelect()}}
                                            />
                                            <span>{{$calendarsCollection[$id]->getName()}}
                                                <span class="count"></span>
                                            </span>


                                        </label>

                                        <div class="info">
                                            <div class="description">
                                                {{$calendarsCollection[$id]->getDescription()}}
                                            </div>
                                        </div>
                                    </li>
                            @endforeach
                        @endforeach
                    </ul>

                </li>
                <li class="first milongas_list">
                    <span>Милонги в городе
                        <span class="count"></span>
                    </span>
                    <ul class="sub-menu">
                        @foreach($calendarList['milongas'] as $city=>$items)

                            @foreach($items as $id)
                                <li class="{{$calendarsCollection[$id]->getClass()}}">
                                    <label>
                                        <input class="calendar_id"
                                               type="checkbox" name="event_types"
                                               value="{{$id}}"
                                            {{$calendarsCollection[$id]->getSelect()}}
                                        />
                                        <span>{{$calendarsCollection[$id]->getName()}}
                                        <span class="count"></span>
                                        </span>


                                    </label>

                                    <div class="info">
                                        <div class="description">
                                            {{$calendarsCollection[$id]->getDescription()}}
                                        </div>
                                    </div>
                                </li>
                            @endforeach

                        @endforeach
                    </ul>
                </li>
                <li class="first school_list">
                    <span>Танго клубы
                        <span class="count"></span>
                    </span>

                    <ul class="sub-menu">
                        @foreach($calendarList['tango_school'] as $city=>$items)
                            <li>
                                <span>{{$city}}</span>
                                <ul>
                                    @foreach($items as $id)
                                        <li class="{{$calendarsCollection[$id]->getClass()}}">
                                            <label>
                                                <input class="calendar_id"
                                                       type="checkbox" name="event_types"
                                                       value="{{$id}}"
                                                    {{$calendarsCollection[$id]->getSelect()}}
                                                />
                                                <span>{{$calendarsCollection[$id]->getName()}}
                                                    <span class="count"></span>
                                                </span>


                                            </label>

                                            <div class="info">
                                                <div class="description">
                                                    {{$calendarsCollection[$id]->getDescription()}}
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>

        </div>

        <div class="calendar" id="calendar">
            <table  rules='none' style=' '>
                <thead>
                <tr>
                    <th class="caption" colspan="7">
                        танго календарь: <span class="year"></span> год
                        <span class="view_mode">вид <span class="icon"></span></span>
                    </th>
                </tr>
                <tr>
                    <th class="header_table" colspan="2">
                        <div class='button_cal right' data="minus"> < </div>
                    </th>
                    <th class="header_table" colspan="3">
                        <span class="month"></span>
                    </th>
                    <th class="header_table" colspan="2">
                        <div class='button_cal left' data="plus"> > </div>
                    </th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    <div class="world_events">
        @if ($worldFest)

            <h3> анонс ближайших фестивалей в мире </h3>

        @endif

    </div>

    <div class="clear"></div>





        <form id="calendar_set"  action="{{route('get.events')}}" method="get">
            <input class="calendar_id_send" type="hidden" name="calendar_id" value="" />
            <input class="set_date" type="hidden" name="set_date" value="{{$yearCalendar}}-{{$monthCalendar+1}}-1" />
        </form>


</div>
<footer>
    <span>calendar v{{$verse}}</span>
</footer>

</body>
</html>
