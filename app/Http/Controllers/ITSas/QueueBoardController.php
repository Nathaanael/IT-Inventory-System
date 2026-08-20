<?php

namespace App\Http\Controllers\ITSas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QueueBoardController extends Controller
{
    public function index()
    {
        return view('helpdesk.queue.queueboard');
    }
}
