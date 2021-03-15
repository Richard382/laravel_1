<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OneSignalPlayers extends Model
{
    protected $table = 'one_signal_players';

    protected $fillable = [
        'player_id'
    ];
}
