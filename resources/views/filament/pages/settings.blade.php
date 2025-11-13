<x-filament-panels::page>
    <form wire:submit.prevent="testCredentials">
        {{ $this->form }}
        
        <div class="mt-6">
            {{ $this->getFormActions() }}
        </div>
    </form>
</x-filament-panels::page>
