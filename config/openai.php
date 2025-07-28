<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key and Organization
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for the OpenAI API. You
    | can find your API key and organization ID on your OpenAI dashboard.
    |
    */

    /**
     * The API key for the OpenAI API.
     * It uses the value from the OPENAI_API_KEY variable in your .env file.
     */
    'api_key' => env('OPENAI_API_KEY'),

    /**
     * The Organization ID for the OpenAI API.
     * You can leave this empty if you are not part of an organization.
     */
    'organization' => env('OPENAI_ORGANIZATION'),
];