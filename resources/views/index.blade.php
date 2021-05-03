
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-1.3.min.js') }}" defer></script>
    <script src="{{ asset('js/coda.js?1.0') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css?v1.2.2') }}" rel="stylesheet">
    <link href="{{ asset('css/master.css?v1.2.2') }}" rel="stylesheet">

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
        var listEvents = @json($listEvents);
        var yearCalendar = {{$yearCalendar}};
        var monthCalendar = {{$monthCalendar}};
        var messagesLog = @json($messagesLog);

    </script>
</head>

<body>

<div class="content">

        <h1>
            Tango Calendar
        </h1>

        <div class="calendar_list">
            <span class="caption">Calendars List</span>
            <span class="mobail menu-close">X</span>
            <ul class="calendars">
                <li class="first festival_list">
                    <span>Festivals</span>
                    <ul class="sub-menu">
                        @foreach($calendarList['festivals'] as $items)
                            @foreach($items as $item)
                                <li class="{{$item['class']}}">
                                    <label>
                                        <input class="calendar_id"
                                               type="checkbox" name="event_types"
                                               value="{{$item['id']}}"
                                               {{$item['select']}}
                                        />
                                        <span>{{$item['calendarName']}} </span>
                                        <div class="description">
                                            {{$item['description']}}
                                        </div>
                                    </label>
                                </li>
                            @endforeach
                        @endforeach
                        @foreach($calendarList['master_classes'] as $items)
                            @foreach($items as $item)
                                    <li class="{{$item['class']}}">
                                        <label>
                                            <input class="calendar_id"
                                                   type="checkbox" name="event_types"
                                                   value="{{$item['id']}}"
                                                {{$item['select']}}
                                            />
                                            <span>{{$item['calendarName']}} </span>
                                            <div class="description">
                                                {{$item['description']}}
                                            </div>
                                        </label>
                                    </li>
                            @endforeach
                        @endforeach
                    </ul>

                </li>
                <li class="first milongas_list">
                    <span>Милонги в городе</span>
                    <ul class="sub-menu">
                        @foreach($calendarList['milongas'] as $city=>$items)
                            <li>
                                <span>{{$city}}</span>
                                <ul>
                                    @foreach($items as $item)
                                        <li class="{{$item['class']}}">
                                            <label>
                                                <input class="calendar_id"
                                                       type="checkbox" name="event_types"
                                                       value="{{$item['id']}}"
                                                    {{$item['select']}}
                                                />
                                                <span>{{$item['calendarName']}} </span>
                                                <div class="description">
                                                    {{$item['description']}}
                                                </div>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li class="first school_list">
                    <span>Танго клубы</span>
                    <ul class="sub-menu">
                        @foreach($calendarList['tango_school'] as $city=>$items)
                            <li>
                                <span>{{$city}}</span>
                                <ul>
                                    @foreach($items as $item)
                                        <li class="{{$item['class']}}">
                                            <label>
                                                <input class="calendar_id"
                                                       type="checkbox" name="event_types"
                                                       value="{{$item['id']}}"
                                                    {{$item['select']}}
                                                />
                                                <span>{{$item['calendarName']}} </span>
                                                <div class="description">
                                                    {{$item['description']}}
                                                </div>
                                            </label>
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
                <caption> танго календарь: <span class="year"></span> год </caption>
                <tr>
                    <th class="header_table" colspan="7">
                        <div class='button_cal left' data="plus"> < </div>
                        <span class="month"></span>
                        <div class='button_cal right' data="minus"> > </div>
                    </th>
                </tr>
            </table>
        </div>


    <div class="clear"></div>

        <form id="calendar_set"  action="{{route('get.events')}}" method="get">
            <input class="calendar_id_send" type="hidden" name="calendar_id" value="" />
            <input class="set_date" type="hidden" name="set_date" value="" />
        </form>


</div>
<footer>

</footer>

</body>
</html>
