<?php

namespace App\Http\Controllers\Notifications;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OneSignalController extends \App\Http\Controllers\Controller
{
    public function playerId(Request $request, $player_id)
    {
        if (! $player_id || $player_id === 'null' || $player_id === NULL) {
            return response()
                ->json([
                    'error' => 'invalid_player_id'
                ]);
        }

        $exists = Auth::user()
            ->oneSignalPlayers()
            ->where('player_id', '=', $player_id)
            ->exists();

        if ($exists) {
            return;
        }

        Auth::user()
            ->oneSignalPlayers()
            ->create([
               'player_id' => $player_id
            ]);
    }
}
