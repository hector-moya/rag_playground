<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use App\Models\Chunk;
use App\Services\ChunkerService as Chunker;

class ProcessDocument implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $documentId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $document = Document::findOrFail($this->documentId);
        $filePath = Storage::path($document->path);

        $text = match($document->mime_type) {
            'application/pdf' => (new PdfParser)->parseFile($filePath)->getText(),
            'text/plain' => file_get_contents($filePath),
            default => throw new \Exception('Unsupported file type: ' . $document->mime_type),
        };

        $chunker = new Chunker();
        $chunks = $chunker->chunk($text);

        foreach ($chunks as $index => $chunkText) {
            Chunk::create([
                'document_id',
                'content' => $chunkText,
                'chunk_index' => $index,
            ]);
        }


        Storage::put('documents/{$document->id}_text.txt', $text);
    }
}
