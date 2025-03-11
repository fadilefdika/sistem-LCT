<div class="w-full mx-auto bg-[#F3F4F6] h-full pb-36 pt-3 overflow-y-auto max-w-full px-3 max-h-[calc(100vh)] [&::-webkit-scrollbar]:w-1
            [&::-webkit-scrollbar-track]:rounded-full
            [&::-webkit-scrollbar-track]:bg-gray-100
            [&::-webkit-scrollbar-thumb]:rounded-full
            [&::-webkit-scrollbar-thumb]:bg-gray-300
            dark:[&::-webkit-scrollbar-track]:bg-neutral-700
            dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
    @switch($budget->status_budget ?? null)
        @case('revision')
                @include('partials.budget-reject', ['rejects' => $budget->rejects ?? []])
                @include('partials.budget-task-timeline', ['tasks' => $tasks ?? []])
                @include('partials.budget-form', ['budget' => $budget ?? null])
            @break

        @case('pending')
                @include('partials.budget-notification')
            @break

        @default
                @include('partials.budget-task-timeline', ['tasks' => $tasks ?? []])
                {{-- @include('partials.budget-form', ['budget' => $budget ?? null]) --}}
    @endswitch
</div>
