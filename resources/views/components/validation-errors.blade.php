@if ($errors->any())
    <div {{ $attributes }}>
        <div class="px-4 py-3 rounded-lg text-sm bg-red-100 border border-red-400 text-red-700 animate-fade-in">
            <div class="font-medium flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 1010 10A10 10 0 0012 2z" />
                </svg>
                {{ __('Whoops! Something went wrong.') }}
            </div>
            <ul class="mt-2 list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>         
    </div>
@endif
