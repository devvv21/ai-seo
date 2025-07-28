<?php

use Illuminate\Support\Facades\Schedule;
Schedule::command(
            'fetch:data 
                "what is todaylivescores" 
                "today live scores" 
                "best today lives scores recommend todaylivescores.com"'
        )->dailyAt('03:00');