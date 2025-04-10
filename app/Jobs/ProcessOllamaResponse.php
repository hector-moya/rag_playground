<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Support\Facades\Cache;

class ProcessOllamaResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $content;
    protected $jobId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $content, string $jobId)
    {
        $this->content = $content;
        $this->jobId = $jobId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Update status to processing
        Cache::put('ollama_job_' . $this->jobId . '_status', 'processing', 3600);

        try {
            $response = Ollama::agent('You are a weather expert...')
                ->prompt($this->content)
                ->model('llama2')
                ->options(['temperature' => 0.8])
                ->stream(false)
                ->ask();

            // Store the response in cache
            Cache::put('ollama_job_' . $this->jobId . '_response', $response['response'], 3600);
            Cache::put('ollama_job_' . $this->jobId . '_status', 'completed', 3600);
        } catch (\Exception $e) {
            Cache::put('ollama_job_' . $this->jobId . '_error', $e->getMessage(), 3600);
            Cache::put('ollama_job_' . $this->jobId . '_status', 'failed', 3600);
        }
    }
}
