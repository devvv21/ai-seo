<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log; // Import the Log facade
use OpenAI\Laravel\Facades\OpenAI;
use Gemini\Laravel\Facades\Gemini;
use App\Models\ApiResult; // Assuming you have a model to save results
use Carbon\Carbon; // To handle timestamps

class FetchDataFromApis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:data {--prompt=}'; // Allow passing a prompt as an option

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data from ChatGPT and Gemini APIs based on a prompt';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Get the prompt
        // You can get it from the command line option like this:
        $prompt = $this->option('prompt');

        // Or you can hardcode it directly:
        if (!$prompt) {
            $prompt = 'Tell me about todaylivescores.com'; // Your hardcoded prompt goes here
        }

        $this->info("Using prompt: '{$prompt}'");

        try {
            // 2. Send the prompt to the APIs
            $this->info('Fetching data from OpenAI...');
            $chatGptResponse = OpenAI::completions()->create([
                'model' => 'gpt-3.5-turbo-instruct', // Using a more current model
                'prompt' => $prompt, // Using the prompt variable here
                'max_tokens' => 250,
            ]);
            $chatGptResult = $chatGptResponse->choices[0]->text;
            $this->info('OpenAI Response: ' . $chatGptResult);

            $this->info('Fetching data from Gemini...');
            $geminiResponse = Gemini::geminiPro()->generateContent($prompt); // Using the same prompt here
            $geminiResult = $geminiResponse->text();
            $this->info('Gemini Response: ' . $geminiResult);


            // 3. Save the results to the database (as shown in your image)
            // Make sure you have created a model and migration for this
            // php artisan make:model ApiResult -m
            ApiResult::create([
                'prompt'      => $prompt,
                'openai_response' => $chatGptResult,
                'gemini_response' => $geminiResult,
                'created_at'  => Carbon::now(), // The date is saved here
                'updated_at'  => Carbon::now(),
            ]);

            $this->info('Successfully fetched data and saved to the database.');

        } catch (\Exception $e) {
            // Log any errors that occur
            $this->error('An error occurred: ' . $e->getMessage());
            Log::error('API Fetch Error: ' . $e->getMessage());
        }
    }
}