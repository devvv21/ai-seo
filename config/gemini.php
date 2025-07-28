<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | The API key for the Gemini API.
    |
    */
    'api_key' => env('GEMINI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    |
    | The default model to use for the `generateContent` method.
    | Find the list of available models here:
    | https://ai.google.dev/models/gemini
    |
    */
    'default_model' => 'gemini-2.5-flash', // <-- CHANGE THIS VALUE

    // ... other settings
];