<?php

namespace App\Http\Controllers;

use App\Models\Gcalendar;
use Illuminate\Http\Request;

class Api
{

    public function c(Request $request)
    {
        $post = $request->post();

        return json_encode($post);
    }
}
