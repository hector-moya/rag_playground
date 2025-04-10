<?php

namespace App\Livewire\Component;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Jobs\ProcessDocument;
use App\Models\Document;
use Illuminate\Support\Str;

class DocumentUploaderComponent extends Component
{
    use WithFileUploads;

    public $file;
    public function mount()
    {
        // dd('hello');
    }
    

    public function uploadDocument()
    {
        $this->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048', // 2MB Max
        ]);

        $storePath = $this->file->store('documents');
        $document = Document::create([
            'name' => Str::slug(pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME)),
            'original_name' => $this->file->getClientOriginalName(),
            'path' => $storePath,
            'mime_type' => $this->file->getClientMimeType(),
        ]);

        ProcessDocument::dispatch($document->id);

        session()->flash('message', 'Document uploaded and queued for processing.');


    }
    public function render()
    {
        return view('livewire.component.document-uploader-component');
    }
}
