<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Support\Facades\Cache;
use App\Models\Chunk;
use Pgvector\Laravel\Distance;

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
        Cache::put('ollama_job_'.$this->jobId.'_status', 'processing', 3600);

        try {
            $embeddingResponse = Ollama::model('Llama2')->embeddings($this->content);
            $rawEmbedding = $embeddingResponse['embedding'] ?? throw new \Exception("No embedding found in response");
            $embeddingArray = array_values($rawEmbedding);

            $mostRelevantChunk = Chunk::query()
                ->nearestNeighbors('embedding', $embeddingArray, Distance::L2)
                ->take(5)
                ->pluck('content')
                ->implode("\n---\n");

                $rawAnswer = Ollama::agent("You are a helpful assistant. Use the information below to answer the user's question.")
                ->prompt("Context:\n$mostRelevantChunk\n\nQuestion: {$this->content}\nAnswer:")
                ->model('llama2')
                ->stream(false)
                ->options(['temperature' => 0.8])
                ->ask();

                $answer = $rawAnswer['response'] ?? 'No answer found in response';

            // Store the response in cache

            Cache::put('ollama_job_'.$this->jobId.'_response', $answer, 3600);
            Cache::put('ollama_job_'.$this->jobId.'_status', 'completed', 3600);
        } catch (\Exception $e) {
            Cache::put('ollama_job_'.$this->jobId.'_error', $e->getMessage(), 3600);
            Cache::put('ollama_job_'.$this->jobId.'_status', 'failed', 3600);
        }
    }
}
