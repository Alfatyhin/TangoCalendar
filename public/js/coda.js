$(function () {

    if (messagesLog) {
        for (key in messagesLog) {
            var message = messagesLog[key];
            console.log(message);
        }
    }

    setCalendarsSelected();

    var jmon=['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];
    var jdn=['вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'];

    function CalendarOut(year, mon) {


        // текущая дата
        var adate=new Date();
        // текущий год
        var ayear=adate.getFullYear();
        // текущий месяц
        var amon=adate.getMonth();
        //текущий день
        var adey=adate.getDate();

        //следующий год и месяц
        var nextd = new Date(year, mon, 31);
        nextd.setDate(nextd.getDate() + 1);
        var nexty=nextd.getFullYear();
        var nextm=nextd.getMonth();


        //предыдущий год и месяц
        var ford = new Date(year, mon, 1);
        ford.setDate(ford.getDate() - 1);
        var fory=ford.getFullYear();
        var form=ford.getMonth();


        // получаем день недели начала месяца
        var ndate = new Date(year, mon, 1);

        // текущий год календаря
        var tyear=ndate.getFullYear();
        // текущий месяц календаря
        var tmon=ndate.getMonth();


        // получаем номер дня недели
        var fdn=ndate.getDay();
        // вычисляем с какой даты начинается неделя
        if (fdn==0)
            var xdey=6;
        else
            var xdey=fdn - 1;



        //получаем новую дату начала отсчета цикла календаря
        ndate.setDate(ndate.getDate() - xdey);
        // начальное значение месяца календаря
        var smon=ndate.getMonth();
        // начальный год календаря
        var syear=ndate.getFullYear();



        var txt=`<tr>`;

        for (x=0; x<=6; x++)
        {
            if (x==6)
                var xn=0;
            else
                xn=x + 1;
            var dn=jdn[xn];
            txt=`${txt} <th>${dn}</th>`;
        }
        txt=`${txt} </tr>`;

        var i=0;
        var nmon=ndate.getMonth();
        while (i<=6)
        {
            x=0;
            txt=`${txt} <tr>`;
            while (x<=6)
            {
                if (nmon == tmon)
                    cl='activmon';
                else
                    cl='unactivmon';

                var ndm=ndate.getDate();

                if (tyear==ayear && tmon==amon && ndm==adey && cl == 'activmon')
                    cl= cl + ' activdey';

                var xtmon = tmon + 1;

                txt=`${txt} <td class='${cl} date_${tyear}-${xtmon}-${ndm}' ><div>
			${ndm}
			</div></td>`;

                ndate.setDate(ndate.getDate() +1);
                var nmon=ndate.getMonth();
                x++;
            }
            if (nmon != tmon && nmon != smon)
                i=6;
            txt=`${txt} </tr>`;
            i++;
        }

        // получаем имя месяца
        $('#calendar .year').text(year);
        $('#calendar .month').text(jmon[tmon]);
        $('#calendar table tbody').html(txt);

        appendToCalendar(year, mon);


        $('.calendar .description-view').click(function () {
            $(this).parents('ul.sub_menu').toggleClass('content');
            $(this).parents('ul.event_list').toggleClass('pop-app');
            $(this).parents('ul.event_list').toggle('500');
            event.stopPropagation();
        });
        $('.pop-app-close').click(function () {
            $(this).parents('ul.sub_menu').toggleClass('content');
            $(this).parents('ul.event_list').toggleClass('pop-app');
            $(this).parents('ul.event_list').toggle('500');
            event.stopPropagation();
        });
        $('.event_list .menu-close').click(function () {
            $(this).parents('ul').first().hide('500');
            event.stopPropagation();
        });
        $('.event_list > li').click(function () {
            $(this).children('ul.sub_menu').show('500');
            event.stopPropagation();
        });

    }

    function appendToCalendar(year, month) {

        $('ul.event_list').remove();
        $('.date_has_event div.event').removeClass('event');
        $('.date_has_event').removeClass('date_has_event');

        var selectedId = [];

        $("input.calendar_id:checkbox:checked").each(function () {
            var id = $(this).val();
            selectedId.push(id);

            Setobj('calendars-selected', selectedId);
        });

        $("input.calendar_id:checkbox:checked").each(function () {
            var id = $(this).val();

            if (!!DataEvents[id] && !!DataEvents[id][year] && !!DataEvents[id][year][month]) {

                if (!!DataEvents[id][year][month]) {

                    var listEvents = DataEvents[id][year][month];

                    var count = Object.keys(listEvents).length;

                    $(this).parent().children('.count').html(count);

                    for (key in listEvents) {

                        if($('.date_' + key).hasClass('date_has_event')) {

                        } else {
                            appendDate(key);
                        }

                        var event = listEvents[key];

                        if (event['dateStart'] != event['dateEnd']) {

                            //предыдущий год и месяц
                            var eventDateStart = new Date(event['dateStart']);
                            var eventdateEnd = new Date(event['dateEnd']);

                            while (eventDateStart < eventdateEnd) {
                                eventDateStart.setDate(eventDateStart.getDate() + 1);
                                var eventYear = eventDateStart.getFullYear();
                                var eventMonth = eventDateStart.getMonth() + 1;
                                var eventDey = eventDateStart.getDate();
                                var date = eventYear + '-' + eventMonth + '-' + eventDey;

                                appendDate(date);
                                appendEvent(date, event);
                            }


                        }

                        appendEvent(event['dateStart'], event);

                    }

                }

            } else {
                $('.preloader_holder').addClass('holder');
                $('.preloader_holder .preloader_dis').addClass('preloader');

                console.log('нет данных по календарю id ' + id);

                selectedId = selectedId.join('|');
                $('.calendar_id_send').val(selectedId);
                $("#calendar_set").submit();
            }



        });


        $('.date_has_event').click(function () {
            $('.events ul.event_list', this).show('500');
            event.stopPropagation();
        });


        countFirst();
    }

    function countFirst () {

        $('.calendar_list .first').each(function () {

            var count = 0;
            $('input.calendar_id:checkbox:checked', this).each(function () {
                count = $(this).parent().children('.count').html() / 1 + count;
            });

            if (count > 0 ) {
                $(this).children('.count').html(count);
                $(this).children('.count').addClass('hash_events');
            } else {
                $(this).children('.count').removeClass('hash_events');
            }

        });
    }


    $('.calendar_list input.calendar_id').change(function () {
        appendToCalendar(yearCalendar, monthCalendar);
    })

    $('#calendar table .header_table .button_cal').click(function () {
        var data = $(this).attr('data');

        if (data == 'minus') {
            //предыдущий год и месяц
            var date = new Date(yearCalendar, monthCalendar, 1);
            date.setDate(date.getDate() - 1);
            yearCalendar = date.getFullYear();
            monthCalendar = date.getMonth();
        } else {
            //следующий год и месяц
            var date = new Date(yearCalendar, monthCalendar, 31);
            date.setDate(date.getDate() + 1);
            yearCalendar = date.getFullYear();
            monthCalendar = date.getMonth();
        }
        var next = monthCalendar + 1;
        var date = yearCalendar + '-' + next + '-1';

        $("#calendar_set .set_date").val(date);

        CalendarOut(yearCalendar, monthCalendar);
    })

    function appendDate(date) {
        $('.activmon.date_' + date).addClass('date_has_event');
        $('.date_' + date + ' div').addClass('events');
        if($('.date_' + date + ' ul').hasClass('event_list')) {

        } else {
            $('.date_' + date + ' div').append('<ul class="event_list"><li class="mobail"><span class="menu-close"></span></li></ul>');
        }


    }

    function appendEvent(date, event) {
        var description = event['description'];
        description = linkify(description);

        var eventDate = `<li>
<span class="title">${event['name']}</span>
    <ul class="sub_menu">
        <li class="mobail">
        <span class="menu-close"></span>
        </li>
        <li>
        <span class="title">${event['name']}</span>
        <span class="pop-app-close"></span>
        </li>
        <li> Date: <br>
            <span>${event['dateStart']} : ${event['timeStart']}
                  <br> ${event['dateEnd']} : ${event['timeEnd']}
            </span>
        </li>
        <li> Location: <br>
            <span>${event['location']}</span>
        </li>
        <li class="description"> Description: <br>
            <span>${description}</span>
            <span class="description-view"></span>
        </li>
    </ul>
</li>`;

        $('.date_' + date + ' .events ul.event_list').append(eventDate);
    }



    $('.calendar_list .menu-close').click(function () {
        $('.calendar_list ul.calendars').hide('500');
        $('.calendar_list .menu-close').hide('500');
        $('.calendar_list .menu-open').show('500');
    });
    $('.calendar_list .menu-open').click(function () {
        $('.calendar_list ul.calendars').show('500');
        $('.calendar_list .menu-close').show('500');
        $('.calendar_list .menu-open').hide('500');
        $('.info .description').hide('500');
    });

    $('.info').click(function () {
        $(this).children('.description').toggle('500');
    });

    $('.calendars .first > span').click(function () {
        var el = $(this).parent();
        $(this).parent().toggleClass('open');
    });




    // аякс обработка данных пока не работает
    function getCalendarEvents(calendarId) {

        var settings = {
            "url": "api/getevents",
            "method": "POST",
            "timeout": 0,
            "dataType": 'json',
            "data": {
                "calendarId" : calendarId,
            }
        };

        $.ajax({
            url: '/api/getevents',
            method: 'POST',
            dataType: 'json',
            "data": {
                "Items" : 'test',
            },
            success: function(data){
                console.log(data);
            }
        });

    }

    CalendarOut(yearCalendar, monthCalendar);

    $('.preloader_holder').removeClass('holder');
    $('.preloader_holder .preloader').addClass('preloader_dis');
    $('.preloader_holder .preloader').removeClass('preloader');


    $('.world_events .description').each(function () {
        var txt = $(this).html();
        $(this).html(linkify(txt));
    });

    $('.world_events .description-view').click(function () {
        $(this).parents('.event').toggleClass('content');
        $(this).parents('.world_events').toggleClass('pop-app');
    });

    $('.pop-app-close').click(function () {
        $(this).parents('.content').toggleClass('content');
        $(this).parents('.pop-app').toggleClass('pop-app');
    });


});

function linkify(text) {
    var urlRegex =/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return text.replace(urlRegex, function(url) {
        return '<a href="' + url + '">' + url + '</a>';
    });
}

////////////////////////////////////////////////////////
function Setobj(str, obj) {
    var serialObj = JSON.stringify(obj);
    localStorage.setItem(str, serialObj);
}
////////////////////////////////////////////////////////
function Getobj(str) {
    var res=JSON.parse(localStorage.getItem(str));
    return (res);
}

function setCalendarsSelected() {
    var calendarsId = Getobj('calendars-selected');

    if (!!calendarsId) {
        var selectedCalendars = {};
        calendarsId.forEach(function(id) {
            selectedCalendars[id] = id;
        });

        $("input.calendar_id").each(function () {
            var id = $(this).val();
            if (!!selectedCalendars[id]) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
            }
        });
    }
}
