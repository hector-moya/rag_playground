<div>
    @if (session()->has('message'))
        <div class="bg-green-100 p-2 mb-2">{{ session('message') }}</div>
    @endif

    <!-- Hidden file input, or styled for better UX -->
    <input type="file" wire:model="file" class="hidden" id="fileInput">

    <!-- Custom button to trigger file input -->
    <flux:button onclick="document.getElementById('fileInput').click()" size="xs" icon:trailing="arrow-up-tray">
        Select File
    </flux:button>

    <!-- Upload button -->
    <flux:button wire:click="uploadDocument" size="xs" icon:trailing="paper-airplane">
        Upload
    </flux:button>

    <!-- Show validation errors -->
    @error('file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
</div>
