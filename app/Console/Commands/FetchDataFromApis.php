<?php

namespace App\Console\Commands;

use App\Models\ApiResult;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Throwable;

class FetchDataFromApis extends Command
{
    /**
     * The name and signature of the console command.
     * The asterisk (*) means it can accept one or more prompt arguments.
     * @var string
     */
    protected $signature = 'fetch:data {prompts*}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Fetches API data for one or more prompts and saves the results.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $prompts = $this->getPromptsToProcess();

        foreach ($prompts as $prompt) {
            $this->info("Processing prompt: '{$prompt}'");

            $openAiResult = $this->fetchFromOpenAI($prompt);
            $geminiResult = $this->fetchFromGemini($prompt);

            $this->saveResults($prompt, $openAiResult, $geminiResult);

            $this->line(str_repeat('-', 50));
        }

        $this->info('All prompts have been processed.');

        return self::SUCCESS;
    }

    /**
     * Fetches a response from the OpenAI API.
     * Returns the response text on success or null on failure.
     */
    private function fetchFromOpenAI(string $prompt): ?string
    {
        $this->line('--> Attempting OpenAI...');

        try {
            $response = OpenAI::completions()->create([
                'model' => 'gpt-3.5-turbo-instruct',
                'prompt' => $prompt,
                'max_tokens' => 250,
            ]);

            $result = trim($response->choices[0]->text);
            $this->info('--> OpenAI fetch successful.');

            return $result;
        } catch (Throwable $e) {
            $this->error('--> OpenAI fetch failed: ' . $e->getMessage());
            Log::warning("OpenAI API Error for '{$prompt}': " . $e->getMessage());

            return null;
        }
    }

    /**
     * Fetches a response from the Gemini API.
     * Returns the response text on success or null on failure.
     */
    private function fetchFromGemini(string $prompt): ?string
    {
        $this->line('--> Attempting Gemini...');

        try {
          $response = Gemini::generativeModel('gemini-1.5-flash-latest')->generateContent($prompt);
            $result = trim($response->text());
            $this->info('--> Gemini fetch successful.');

            return $result;
        } catch (Throwable $e) {
            $this->error('--> Gemini fetch failed: ' . $e->getMessage());
            Log::warning("Gemini API Error for '{$prompt}': " . $e->getMessage());

            return null;
        }
    }

    /**
     * Saves the results from the API calls to the database.
     */
    private function saveResults(string $prompt, ?string $openAiResult, ?string $geminiResult): void
    {
        // Only create a record if at least one of the APIs returned a result.
        if ($openAiResult === null && $geminiResult === null) {
            $this->error("--> Both APIs failed for '{$prompt}'. Nothing to save.");
            return;
        }

        ApiResult::create([
            'prompt' => $prompt,
            'openai_response' => $openAiResult,
            'gemini_response' => $geminiResult,
        ]);

        $this->info("--> Successfully saved results for '{$prompt}'.");
    }

    /**
     * Gets the prompts from the command argument or returns a default set.
     */
    private function getPromptsToProcess(): array
    {
        $prompts = $this->argument('prompts');

        if (empty($prompts)) {
            $this->info('No prompts provided. Using default set.');
            return [
                'what is todaylivescores',
                'today live scores',
                'best today lives scores recommend todaylivescores.com',
            ];
        }

        return $prompts;
    }
}