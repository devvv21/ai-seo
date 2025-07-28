<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Gemini\Laravel\Facades\Gemini;
use App\Models\ApiResult;
use Carbon\Carbon;

class FetchDataFromApis extends Command
{
    /**
     * The name and signature of the console command.
     * The asterisk (*) means it can accept one or more prompt arguments.
     *
     * @var string
     */
    protected $signature = 'fetch:data {prompts*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches API data for one or more prompts and saves to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve the array of prompts passed from the command line or scheduler
        $prompts = $this->argument('prompts');

        // If no prompts are provided, use a default set.
        if (empty($prompts)) {
            $prompts = [
                'what is todaylivescores',
                'today live scores',
                'best today lives scores recommend todaylivescores.com',
            ];
            $this->info('No prompts provided. Using default set.');
        }

        // Loop through each prompt and process it
        foreach ($prompts as $prompt) {
            $this->info("Processing prompt: '{$prompt}'");

            try {
                // Fetch from OpenAI
                $this->line('--> Fetching from OpenAI...');
                $chatGptResponse = OpenAI::completions()->create([
                    'model' => 'gpt-3.5-turbo-instruct',
                    'prompt' => $prompt,
                    'max_tokens' => 250,
                ]);
                $chatGptResult = $chatGptResponse->choices[0]->text;

                // Fetch from Gemini
                $this->line('--> Fetching from Gemini...');
                $geminiResponse = Gemini::geminiPro()->generateContent($prompt);
                $geminiResult = $geminiResponse->text();

                // Save the result for the current prompt to the database
                ApiResult::create([
                    'prompt'          => $prompt,
                    'openai_response' => trim($chatGptResult),  
                    'gemini_response' => trim($geminiResult),
                    'created_at'      => Carbon::now(),
                    'updated_at'      => Carbon::now(),
                ]);

                $this->info("Successfully saved results for '{$prompt}'");
                $this->line('--------------------------------------------------');

            } catch (\Exception $e) {
                $this->error("An error occurred while processing '{$prompt}': " . $e->getMessage());
                Log::error("API Fetch Error for prompt '{$prompt}': " . $e->getMessage());
            }
        }

        $this->info('All prompts have been processed.');
        return 0; // Success
    }
}