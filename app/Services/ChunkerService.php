<?php

namespace App\Services;

class ChunkerService
{
    public function chunk(string $text, int $chunkSize = 500, int $overlap = 50): array
    {
        $words = preg_split('/\s+/', $text);
        $chunks = [];

        for ($i = 0; $i < count($words); $i += ($chunkSize - $overlap)) {
            $chunkWords = array_slice($words, $i, $chunkSize);
            if (count($chunkWords) < 100) break; // skip tiny leftovers
            $chunks[] = implode(' ', $chunkWords);
        }

        return $chunks;
    }
}
