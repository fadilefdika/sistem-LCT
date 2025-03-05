<div class="mt-6 bg-white p-6 rounded-lg shadow-lg">
    <h3 class="text-lg font-semibold mb-4">Tasks and Timeline</h3>

    <table class="min-w-full table-auto">
        <thead>
            <tr>
                <th class="px-4 py-2 border-b">Task Name</th>
                <th class="px-4 py-2 border-b">Status</th>
                <th class="px-4 py-2 border-b">Due Date</th>
                <th class="px-4 py-2 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td class="px-4 py-2 border-b">{{ $task->task_name }}</td>
                <td class="px-4 py-2 border-b">
                    <span class="{{ 
                        $task->status === 'completed' ? 'text-green-500' :
                        ($task->status === 'in_progress' ? 'text-yellow-500' : 'text-red-500')
                    }}">
                        {{ ucfirst($task->status) }}
                    </span>
                </td>
                <td class="px-4 py-2 border-b">{{ $task->due_date }}</td>
                <td class="px-4 py-2 border-b">
                    <form action="{{ route('update-task-status', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="status" onchange="this.form.submit()" class="px-2 py-1 border border-gray-300 rounded-md">
                            <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
