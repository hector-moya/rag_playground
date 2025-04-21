<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Chunk;
use Cloudstudio\Ollama\Facades\Ollama;

class GenerateEmbedding implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $chunkId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $chunk = Chunk::findOrFail($this->chunkId);

        $response = Ollama::model('Llama2')->embeddings($chunk->content);
        $rawEmbedding = $response['embedding'] ?? throw new \Exception("No embedding found in response");
        $embeddingArray = array_values($rawEmbedding);
        \Log::info('Saving embedding', ['chunk_id' => $chunk->id, 'vector' => $embeddingArray]);
        $chunk->update([
            'embedding' => $embeddingArray,
        ]);
    }
}
