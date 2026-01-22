<div class="task-card bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg p-3 cursor-move hover:shadow-md transition-shadow"
    data-task-id="{{ $task->id }}" onclick="openTaskModal(null, {{ $task->id }})">

    <div class="flex items-start justify-between mb-2">
        <h4 class="font-medium text-gray-900 dark:text-white text-sm flex-1">{{ $task->title }}</h4>
        <span class="text-xs px-2 py-1 rounded border ml-2 {{ $task->priorityColor() }}">
            {{ ucfirst($task->priority) }}
        </span>
    </div>

    @if ($task->description)
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">{{ $task->description }}</p>
    @endif

    @if ($task->tags->count() > 0)
        <div class="flex flex-wrap gap-1 mb-2">
            @foreach ($task->tags as $tag)
                <span class="text-xs px-2 py-1 rounded"
                    style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                    {{ $tag->name }}
                </span>
            @endforeach
        </div>
    @endif

    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mt-2">
        <div class="flex items-center space-x-2">
            @if ($task->due_date)
                <span class="flex items-center {{ $task->isOverdue() ? 'text-red-600' : '' }}">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $task->due_date->format('d/m/Y') }}
                </span>
            @endif

            @if ($task->comments->count() > 0)
                <span class="flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    {{ $task->comments->count() }}
                </span>
            @endif
        </div>

        <div class="flex items-center">
            @if ($task->user->avatar)
                <img src="{{ Storage::url($task->user->avatar) }}" alt="{{ $task->user->name }}"
                    title="{{ $task->user->name }}" class="h-6 w-6 rounded-full">
            @else
                <div class="h-6 w-6 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-semibold"
                    title="{{ $task->user->name }}">
                    {{ substr($task->user->name, 0, 1) }}
                </div>
            @endif
        </div>
    </div>
</div>
