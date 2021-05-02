<?php

namespace App\Http\Controllers;

use App\Models\Gcalendar;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    function saveCalendar (Request $request)
    {
        $gcalendar = new Gcalendar();

        $gcalendar->gcalendarId = $request->gcalendarId;
        $gcalendar->type_events = $request->type_events;
        $gcalendar->country     = $request->country;
        $gcalendar->city        = $request->city;
        $gcalendar->source      = $request->source;
        $res = $gcalendar->save();

        if ($res) {
            $message = "calendar $request->country $request->city $request->type_events added";
            session()->flash('message', $message);
        }
        session()->flash('type_events', $request->type_events);
        session()->flash('source', $request->source);

        return redirect(route('add-calendar'));
    }

    public function addCalendar()
    {
        $message = session('message');
        $type_events = session('type_events');
        $source = session('source');

        $category = Gcalendar::getCategory();
        $sources = Gcalendar::getUserSource();

        return view('add_calendar', [
            'message'         => $message,
            'category'        => $category,
            'sources'         => $sources,
            'select_source'   => $source,
            'select_type'     => $type_events,
        ]);
    }


}
