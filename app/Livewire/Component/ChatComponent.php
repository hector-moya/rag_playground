<?php

namespace App\Livewire\Component;

use Livewire\Component;
use App\Jobs\ProcessOllamaResponse;
use App\Models\Chunk;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ChatComponent extends Component
{
    public string $content = '';
    public string $ollamaResponse = '';
    public ?string $currentJobId = null;
    public string $jobStatus = '';
    public string $errorMessage = '';

    public function response(string $content)
    {
        // Generate a unique job ID
        $jobId = Str::uuid()->toString();
        $this->currentJobId = $jobId;

        // Set initial status
        $this->jobStatus = 'queued';
        Cache::put('ollama_job_'.$jobId.'_status', 'queued', 3600);

        // Dispatch the job
        ProcessOllamaResponse::dispatch($content, $jobId);

        // Clear any previous response
        $this->ollamaResponse = '';
        $this->errorMessage = '';
    }

    public function checkJobStatus()
    {
        if ($this->currentJobId) {
            $status = Cache::get('ollama_job_'.$this->currentJobId.'_status', 'unknown');
            $this->jobStatus = $status;

            if ($status === 'completed') {
                $this->ollamaResponse = Cache::get('ollama_job_'.$this->currentJobId.'_response', '');
            } elseif ($status === 'failed') {
                $this->errorMessage = Cache::get('ollama_job_'.$this->currentJobId.'_error', 'An error occurred');
            }
        }
    }

    public function render()
    {
        return view('livewire.component.chat-component');
    }
}
