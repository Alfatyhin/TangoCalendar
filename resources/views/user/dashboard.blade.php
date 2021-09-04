@extends('layouts.master')

@section('title', 'dashboard')

@section('description', 'dashboard')

@section('head')
    @parent
@stop

@section('content')


    <h1>
        Dashboard
    </h1>

    <div>
        <img class=""
             src="https://graph.facebook.com/{{ $user->fb_id }}/picture?type=normal"/>
        <p>{{ $user->name }}</p>
        <p>{{ $user->email }}</p>
        <p>{{ $user->role }}</p>
    </div>

    @if ($calendars)

    @else

    @endif

    @if ($user_fb_calendar)

    @else
        <div class="form">
            <p>add faceboock calendar </p>
            <form method="post" action="{{ route('add_user_calendar') }}" >
                @csrf

                <input type="text" name="fb_cal_link" />
                <input type="submit">
            </form>
        </div>
    @endif

@stop
