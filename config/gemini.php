<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Gemini API Key
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for the Gemini API. You can
    | generate your API key from the Google AI Studio dashboard.
    |
    */

    /**
     * The API key for the Gemini API.
     * It uses the value from the GEMINI_API_KEY variable in your .env file.
     */
    'api_key' => env('GEMINI_API_KEY'),

];