<?php

namespace App\Http\Controllers;

use App\BrokerVisibility;
use App\BrokerVisibilityDurations;
use Illuminate\Http\Request;

class BrokerVisibilityController extends Controller
{
    public function index()
    {
        $positions = BrokerVisibility::all();
        $durations = BrokerVisibilityDurations::all();

        return view('pages.broker-visibility', compact('positions', 'durations'));
    }
}
