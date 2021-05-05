$(function () {

    if (messagesLog) {
        for (key in messagesLog) {
            var message = messagesLog[key];
            console.log(message);
        }
    }

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
    }

    function appendToCalendar(year, month) {

        $('ul.event_list').remove();
        $('.date_has_event div.event').removeClass('event');
        $('.date_has_event').removeClass('date_has_event');


        $("input.calendar_id:checkbox:checked").each(function () {
            var id = $(this).val();

            if (!!DataEvents[id] && !!DataEvents[id][year] && !!DataEvents[id][year][month]) {

                if (!!DataEvents[id][year][month]) {

                    var listEvents = DataEvents[id][year][month];

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
                console.log('нет данных по календарю id ' + id);

                var selectedId = [];
                $("input.calendar_id:checkbox:checked").each(function () {
                    var id = $(this).val();
                    selectedId.push(id);
                });
                selectedId = selectedId.join('|');

                $('.calendar_id_send').val(selectedId);
                $("#calendar_set").submit();
            }

        });

        $('.date_has_event').each(function () {
            // options
            var distance = 10;
            var time = 250;
            var hideDelay = 500;

            var hideDelayTimer = null;

            // tracker
            var beingShown = false;
            var shown = false;

            var trigger = $(this);
            var popup = $('.events ul.event_list', this).css('opacity', 0);

            // set the mouseover and mouseout on both element
            $([trigger.get(0), popup.get(0)]).mouseover(function () {
                // stops the hide event if we move from the trigger to the popup element
                if (hideDelayTimer) clearTimeout(hideDelayTimer);

                // don't trigger the animation again if we're being shown, or already visible
                if (beingShown || shown) {
                    return;
                } else {
                    beingShown = true;

                    // reset position of popup box
                    popup.css({
                        bottom: 20,
                        left: -150,
                        display: 'block' // brings the popup back in to view
                    })

                        // (we're using chaining on the popup) now animate it's opacity and position
                        .animate({
                            bottom: '+=' + distance + 'px',
                            opacity: 1
                        }, time, 'swing', function() {
                            // once the animation is complete, set the tracker variables
                            beingShown = false;
                            shown = true;
                        });
                }
            }).mouseout(function () {
                // reset the timer if we get fired again - avoids double animations
                if (hideDelayTimer) clearTimeout(hideDelayTimer);

                // store the timer so that it can be cleared in the mouseover if required
                hideDelayTimer = setTimeout(function () {
                    hideDelayTimer = null;
                    popup.animate({
                        bottom: '-=' + distance + 'px',
                        opacity: 0
                    }, time, 'swing', function () {
                        // once the animate is complete, set the tracker variables
                        shown = false;
                        // hide the popup entirely after the effect (opacity alone doesn't do the job)
                        popup.css('display', 'none');
                    });
                }, hideDelay);
            });
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
        $('.date_' + date + ' div').append('<ul class="event_list"></ul>');
    }

    function appendEvent(date, event) {
        var eventDate = `<li>
<span class="title">${event['name']}</span>
    <ul class="sub_menu">
        <li> Date: <br>
            <span>${event['dateStart']} : ${event['timeStart']}
                  <br> ${event['dateEnd']} : ${event['timeEnd']}
            </span>
        </li>
        <li> Location: <br>
            <span>${event['location']}</span>
        </li>
        <li> Description: <br>
            <span>${event['description']}</span>
        </li>
    </ul>
</li>`;

        $('.date_' + date + ' .events ul.event_list').append(eventDate);
    }



    $('.calendar_list .menu-close').click(function () {
        $('.calendar_list ul.calendars').hide();
        $('.calendar_list .menu-close').hide();
    });
    $('.calendar_list .caption').click(function () {
        $('.calendar_list ul.calendars').show();
        $('.calendar_list .menu-close').show();
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
});
