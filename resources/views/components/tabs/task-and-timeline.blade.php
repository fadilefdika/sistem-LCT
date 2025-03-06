<div class="w-full mx-auto bg-[#F3F4F6] h-fullpb-36 pt-3">
    @switch($budget->status_budget)
        @case('rejected')
            @include('partials.budget-reject', ['rejects' => $budget->rejects])
            @include('partials.budget-form', ['budget' => $budget]) 
            @break

        @case('pending')
            @include('partials.budget-notification')
            @break

        @case('approved')
            @include('partials.budget-task-timeline', ['tasks' => $tasks])
            @break

        @default
            @include('partials.budget-form', ['budget' => $budget])
    @endswitch
</div>
