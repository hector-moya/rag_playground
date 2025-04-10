<div class="flex h-full flex-col justify-end" wire:poll.3s="checkJobStatus">
  @if ($jobStatus === 'queued' || $jobStatus === 'processing')
    <flux:card>
      <flux:icon.loading />
    </flux:card>
  @elseif ($errorMessage)
    <div class="rounded-lg bg-red-50 p-4 text-red-700">
      <p>Error: {{ $errorMessage }}</p>
    </div>
  @elseif ($ollamaResponse)
    <div class="m-6 p-6">
      <flux:text>{{ $ollamaResponse }}</flux:text>
    </div>
  @endif

  <div class="m-2 px-4 flex items-center justify-between gap-2 ">
    <flux:input wire:model="content" />

    <flux:button wire:click="response('{{ $content }}')" size="xs" icon:trailing="paper-airplane"></flux:button>
  </div>
</div>
