
<div x-data="taskData()">

    @if($laporan->status_lct === 'taskbudget_revision')
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-2" role="alert">
            <p class="font-bold text-sm">Revision Required</p>
            <p class="text-xs">Please scroll down near the submit button to view revision details.</p>
        </div>
    @elseif($laporan->status_lct === 'waiting_approval_taskbudget')
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 mb-2" role="alert">
            <p class="font-bold text-sm">Awaiting Manager Approval</p>
            <p class="text-xs">Your revision has been submitted. Please wait for approval.</p>
        </div>
    @elseif($laporan->status_lct === 'approved_permanent')
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-3 mb-2" role="alert">
            <p class="font-bold text-sm">Permanent Action Completed & Approved</p>
            <p class="text-xs">All tasks related to the permanent action have been successfully completed and officially approved.</p>
        </div>
    @elseif(in_array($laporan->status_lct, ['waiting_approval_temporary', 'approved_temporary']))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-2" role="alert">
            <p class="font-bold text-sm">Action Required: Complete the Task</p>
            <p class="text-xs">Complete this task within 2 days after temporary approval by providing the permanent action and timeline.</p>
        </div>
    @elseif($laporan->status_lct === 'closed')
        <div class="bg-gray-200 border-l-4 border-gray-500 text-gray-700 p-3 mb-2" role="alert">
            <p class="font-bold text-sm">🔒 Case Closed</p>
            <p class="text-xs">This case has been closed. No further actions required.</p>
        </div>
    @endif

    <!-- FORM TASK (DITAMPILKAN SAAT BELUM DI APPROVE) -->
    @if(!in_array($laporan->status_lct, ['approved_permanent','waiting_approval_permanent', 'closed']))
        <div x-show="!isApproved">
            <form action="{{ route('admin.manajemen-lct.submitTaskBudget', ['id_laporan_lct' => $laporan->id_laporan_lct]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="deletedTasks" id="deletedTasksInput">

                <div class="bg-white px-6 pt-6 pb-6 rounded-lg shadow-lg mb-4 overflow-x-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Permanent Action</h3>
                        @if(in_array($laporan->approved_temporary_by_ehs,['pending', 'revise', 'not yet']))
                            <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-medium px-3 py-1 rounded-full">
                                Temporary not yet approved
                            </span>
                        @else
                            <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full">
                                Temporary approved
                            </span>
                        @endif
                    </div>
                
                    <!-- Permanent Action -->
                        <div x-data="{ permanentAction: '{{ $laporan->action_permanent ?? '' }}' }" class="flex flex-col w-full max-w-lg">
                            <label for="permanent-action" class="mb-1 font-medium text-gray-700 text-sm">Title Permanent Action:</label>
                            <textarea 
                                id="permanent-action"
                                x-model="permanentAction" 
                                name="permanentAction" 
                                class="border border-gray-300 p-2 rounded-lg mb-2 text-xs resize-y w-full" 
                                placeholder="Specify the permanent corrective action..." 
                                required
                                rows="4"
                            ></textarea>
                            <p x-show="showErrorAction" class="text-red-500 text-sm mt-1">
                                Permanent Action is required!
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-300 my-3 sm:table-fixed">
                              <thead class="bg-gray-100">
                                <tr>
                                  <th class="border px-3 py-2 text-xs text-center w-[5%]">No</th>
                                  <th class="border px-3 py-2 text-xs text-left w-[53%]">Task Name</th>
                                  <th class="border px-3 py-2 text-xs text-left w-[22%]">PIC</th>
                                  <th class="border px-3 py-2 text-xs text-left w-[15%]">Due Date</th>
                                  <th class="border px-3 py-2 text-xs text-center w-[5%]">Action</th>
                                </tr>
                              </thead>
                              <tbody>
                                <template x-for="(task, index) in tasks" :key="index">
                                  <tr>
                                    <td class="border px-3 py-1 text-center" x-text="index + 1"></td>
                                    <input type="hidden" x-model="task.id" :name="'tasks['+index+'][id]'">
                                    <td class="border">
                                      <input type="text" x-model="task.taskName"
                                        @click="addRow(index)"
                                        class="w-full border-gray-100 text-xs min-w-[160px] truncate"
                                        :name="'tasks['+index+'][taskName]'"
                                        placeholder="Enter the task name...">
                                    </td>
                                    <td class="border">
                                      <select
                                        class="w-full min-w-[130px] text-xs border-gray-100"
                                        x-model="task.picId"
                                        :name="'tasks['+index+'][picId]'"
                                        @change="console.log('Task ID:', task.id, 'PIC Terpilih:', task.picId)">
                                        <option value="" class="text-sm">Select PIC</option>
                                        @foreach($picList as $pic)
                                          <option value="{{ $pic['pic_id'] }}" x-bind:selected="task.picId == {{ $pic['pic_id'] }}" class="text-xs">
                                            {{ $pic['fullname'] }}
                                          </option>
                                        @endforeach
                                      </select>
                                    </td>
                                    <td class="border">
                                      <input type="date" x-model="task.dueDate" class="w-full border-gray-100 text-xs" :name="'tasks['+index+'][dueDate]'" @click="$event.target.showPicker && $event.target.showPicker()">
                                    </td>
                                    <td class="border text-center">
                                      <button type="button" @click="removeTask(index)" class="text-red-600">×</button>
                                    </td>
                                  </tr>
                                </template>
                              </tbody>
                            </table>
                        </div>
                          

                    <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                        <h3 class="text-sm font-semibold mb-2">Estimated Budget</h3>
                        <div class="flex items-center">
                            <label for="estimatedBudget" class="font-medium text-xs mr-3">Estimated Budget (Rp):</label>
                            <input 
                                type="text" 
                                id="estimatedBudget" 
                                name="estimatedBudget"
                                @if(old('estimatedBudget') !== null)
                                    value="{{ old('estimatedBudget') }}"
                                @elseif(!empty($laporan->estimated_budget) && $laporan->estimated_budget > 0)
                                    value="{{ number_format($laporan->estimated_budget, 0, ',', '.') }}"
                                @endif
                                class="border border-gray-300 p-2 w-1/2 sm:w-1/4 rounded-lg text-xs" 
                                placeholder="Example: 1.000.000" 
                                required
                            >
                        </div>
                    </div>


                    <div x-data="fileUpload" class="mt-6 p-4 border rounded-lg shadow-md bg-white">
                        <h3 class="text-sm font-semibold mb-4 text-gray-800">Attachments</h3>
                        
                        <!-- Existing Attachments -->
                        @php
                            $existingAttachments = json_decode($laporan->attachments ?? '[]', true);
                        @endphp
                        
                        @if (!empty($existingAttachments))
                            <div class="mb-6">
                                <p class="text-xs font-medium text-gray-700 mb-2">Submitted Documents</p>
                                <ul class="list-disc pl-5 text-sm text-gray-600 space-y-2">
                                    @foreach ($existingAttachments as $index => $attachment)
                                        <li class="flex items-center justify-between">
                                            <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="text-blue-600 text-xs underline hover:text-blue-800">
                                                {{ $attachment['original_name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>                    
                        @else
                        <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                    </svg>
                                    <h4 class="mt-3 text-sm font-medium text-gray-900">No documents submitted</h4>
                                    <p class="mt-1 text-sm text-gray-500">There are no documents associated with these tasks.</p>
                                </div>
                        @endif

                        @if(in_array($laporan->status_lct, ['waiting_approval_temporary', 'temporary_revision','approved_temporary', 'taskbudget_revision']))
                            <!-- Label -->
                            <label for="file-upload" class="block mb-2 text-sm font-medium text-gray-700">Upload New Files</label>

                            <!-- Upload Input -->
                            <div x-data="fileUpload()" class="w-full">
                                <input 
                                    type="file" 
                                    name="attachments[]"
                                    id="file-upload"
                                    multiple
                                    accept="application/pdf, image/*"
                                    class="hidden"
                                    @change="handleFileChange"
                                    @if(empty($existingAttachments))
                                        required
                                    @endif
                                />

                                <label for="file-upload" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg cursor-pointer hover:bg-blue-700 transition duration-300 inline-block">
                                    Choose Files
                                </label>
                                
                                <!-- Error message -->
                                <p x-show="error" class="text-red-600 text-sm mt-2" x-text="error"></p>

                                <!-- Display selected file names (list vertical) -->
                                <div x-show="selectedFiles.length > 0" class="mt-4 w-full">
                                    <ul class="space-y-2">
                                        <template x-for="(file, index) in selectedFiles" :key="index">
                                            <li @click.self="openInNewTab(file)" :title="file.name" class="flex items-center justify-between p-3 bg-gray-100 rounded-lg shadow-sm hover:bg-gray-200 transition duration-200 cursor-default">
                                                <div class="flex items-center space-x-3">
                                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                            d="M7 16V4a2 2 0 012-2h6a2 2 0 012 2v12M7 16h10m-5 4h.01" />
                                                    </svg>
                                                    <span class="text-sm text-gray-800 truncate max-w-xs" x-text="file.name"></span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500" x-text="(file.size / 1024).toFixed(1) + ' KB'"></span>
                                                    <!-- Tombol hapus -->
                                                    <button 
                                                        type="button" 
                                                        @click="removeFile(index)" 
                                                        class="text-red-500 hover:text-red-700 text-sm"
                                                        title="Hapus file ini"
                                                    >
                                                        🗑️
                                                    </button>
                                                </div>
                                            </li>
                                        </template>                                        
                                    </ul>
                                </div>

                                <!-- Menambahkan attachment yang sudah ada jika tidak ada file yang diupload -->
                                <input type="hidden" name="existing_attachments" value="{{ $laporan->attachments ? json_encode($laporan->attachments) : '' }}">
                            </div>
                            <p class="text-sm text-gray-500 mt-2">You can upload multiple files (PDF, Images).</p>
                            @endif

                    </div>

                    @if($laporan->status_lct == 'taskbudget_revision')
                        <div class="mt-4">
                            <label for="tindakan_perbaikan" class="block text-xs font-semibold text-gray-700 mb-1">
                                PIC Response / Corrective Action Message <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="tindakan_perbaikan" 
                                id="tindakan_perbaikan" 
                                rows="3"
                                placeholder="Enter your corrective action message here..."
                                class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                required
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-1">This field is required during revision.</p>
                        </div>
                    @endif

                        <div class="flex flex-col w-full">
                            {{-- Revision history --}}
                            
                            @if ($combined->isNotEmpty())
                                <div x-data="{ openIndex: null }" class="bg-white p-6 rounded-xl border border-gray-200 mt-6 shadow-lg space-y-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-info-circle text-red-500 text-xl"></i>
                                        <p class="text-red-500 font-semibold text-sm">Revision Details</p>
                                    </div>
                                
                                    <table class="w-full text-sm text-left table-fixed border border-gray-200 rounded-lg overflow-hidden">
                                        <thead class="bg-gray-100 text-gray-700 uppercase text-[11px] tracking-wider">
                                            <tr>
                                                <th class="py-3 px-3 w-40">Date</th>
                                                <th class="py-3 px-3">Revision Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($combined as $i => $item)
                                                @php
                                                    $hasPicResponse = !empty($item['pic_message']);
                                                @endphp
                                
                                                <tr 
                                                    @if($hasPicResponse) 
                                                        @click="openIndex === {{ $i }} ? openIndex = null : openIndex = {{ $i }}" 
                                                        class="cursor-pointer hover:bg-gray-50 transition"
                                                    @else
                                                        class="cursor-not-allowed"
                                                    @endif
                                                >
                                                    <td class="py-2 px-3 text-gray-500 text-xs whitespace-nowrap align-top">
                                                        {{ $item['rev']->updated_at->format('d M Y H:i') }}
                                                    </td>
                                                    <td class="py-2 px-3 align-top">
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-gray-800 text-xs leading-snug break-words w-full">
                                                                {{ $item['rev']->alasan_reject }}
                                                            </span>
                                                            @if($hasPicResponse)
                                                                <svg :class="{ 'rotate-180': openIndex === {{ $i }} }"
                                                                    class="w-4 h-4 text-gray-400 transition-transform"
                                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                </svg>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                
                                                @if($hasPicResponse)
                                                <tr x-show="openIndex === {{ $i }}" x-transition>
                                                    <td colspan="2" class="bg-gray-50 px-6 py-4">
                                                        <p class="text-gray-700 text-xs font-semibold mb-1">PIC Response</p>
                                                        <p class="text-gray-800 text-[11px] leading-snug break-words text-justify">
                                                            {{ $item['pic_message'] }}
                                                        </p>
                                                    </td>
                                                </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        
                            {{-- Submit button --}}
                            @if(in_array($laporan->status_lct, ['waiting_approval_temporary', 'temporary_revision', 'approved_temporary', 'taskbudget_revision']))
                                <button 
                                    type="submit"  
                                    class="text-white bg-blue-700 px-4 py-2 rounded-lg text-sm cursor-pointer ml-auto mt-4"
                                >
                                    Submit
                                </button>
                            @endif

                        </div>
                </div>
            </form>
            </div>
        </div>
    @endif

        @if(in_array($laporan->status_lct, ['approved_taskbudget', 'waiting_approval_permanent', 'approved_permanent', 'closed']))
            <div class="mt-1 bg-white rounded-xl shadow-lg overflow-x-auto">
                <!-- Section Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-100 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h2 class="text-xl font-bold text-gray-800">Approved Tasks List</h2>
                            <p class="text-sm text-green-600">All approved tasks and related documents</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Budget Card -->
                    @if($laporan->estimated_budget)
                    <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-5 shadow-inner">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-semibold text-blue-700 uppercase tracking-wider mb-1">Approved Budget</h3>
                                <p class="text-2xl font-bold text-blue-900">
                                    Rp {{ number_format($laporan->estimated_budget, 0, ',', '.') }}
                                </p>
                            </div>
                            <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    @endif

                    <!-- Tasks Table -->
                    <div class="mb-8 overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[5%]">No</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[50%]">Task Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[22%]">PIC</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">Due Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[8%]">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($tasks as $index => $task)
                                    @if(!empty($task['taskName']) && !empty($task['picId']) && !empty($task['dueDate']))
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">
                                            {{ $index + 1 }}
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $task['taskName'] }}</div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $pic = $picList->firstWhere('pic_id', $task['picId']);
                                            @endphp
                                            <div class="flex items-center">
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $pic ? $pic['fullname'] : 'No PIC Assigned' }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $pic ? $pic['position'] ?? '' : '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($task['dueDate'])->translatedFormat('F j, Y') }}
                                            </div>
                                        </td>

                                        
                                        <td class="px-4 py-2">
                                            <label class="flex items-center space-x-2 {{ $laporan->is_task_only ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                                <input type="checkbox"
                                                    data-task-id="{{ $task['id'] }}"
                                                    class="status-checkbox form-checkbox h-5 w-5 text-green-600"
                                                    {{ $task['status'] == 'completed' ? 'checked' : '' }}
                                                    {{ $laporan->is_task_only ? 'disabled' : '' }}>
                                                <span class="text-sm text-gray-700">
                                                    {{ $task['status'] == 'completed' ? 'Completed' : 'Pending' }}
                                                </span>
                                            </label>
                                        </td>                                        
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Documents Section -->
                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="text-sm font-semibold text-gray-800">Submitted Documents</h3>
                            </div>
                        </div>

                        @php
                            $existingAttachments = json_decode($laporan->attachments ?? '[]', true);
                        @endphp

                        @if (!empty($existingAttachments))
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($existingAttachments as $index => $attachment)
                                    <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 mr-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-900 truncate">
                                                {{ $attachment['original_name'] }}
                                            </p>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                </svg>
                                <h4 class="mt-3 text-sm font-medium text-gray-900">No documents submitted</h4>
                                <p class="mt-1 text-sm text-gray-500">There are no documents associated with these tasks.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- History Card -->
        <div class="bg-white rounded-lg shadow-md p-4 mt-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Corrective Action History</h2>
                    <p class="text-sm text-gray-500">View the detailed progress and corrective actions taken for this case.</p>
                </div>
                <a href="{{ route('admin.manajemen-lct.history', $laporan->id_laporan_lct) }}">
                    <button class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                        History
                    </button>
                </a>
            </div>
        </div>
    </div>
</div>


<script>    
document.addEventListener("DOMContentLoaded", function () {
    Alpine.start();
});

function formatCurrency(value) {
    value = value.replace(/\D/g, ""); // Hapus semua karakter non-digit
    return new Intl.NumberFormat("id-ID").format(value);
}

document.addEventListener('alpine:init', () => {
Alpine.data('taskData', () => ({
    isApproved: @json($laporan->status_lct === 'approved_taskbudget'),
    tasks: @json($tasks ?? []), 

    init() {
        if (this.tasks.length === 0) {
            this.tasks.push({ taskName: '', picId: '', dueDate: '' });
        }
    },

    addRow(index) {
        if (index === this.tasks.length - 1) {
            this.tasks.push({ taskName: '', picId: '', dueDate: '' });
        }
    },

    removeTask(index) {
        if (this.tasks.length > 1) {
            this.tasks.splice(index, 1);
        }
    },

    validatePicId(index) {
        console.log("Validasi PIC:", this.tasks[index].picId);
        if (!this.tasks[index].picId) {
            console.warn(`Task ${index + 1}: PIC belum dipilih!`);
        }
    }
}));
});

</script>

<script>
document.getElementById('estimatedBudget').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, ''); // Hanya angka
    value = new Intl.NumberFormat('id-ID').format(value); // Format angka dengan titik
    e.target.value = value;
});
</script>

<script>
document.querySelectorAll('.status-checkbox').forEach((checkbox) => {
    checkbox.addEventListener('change', async function () {
        const taskId = this.getAttribute('data-task-id');
        const isChecked = this.checked;
        const newStatus = isChecked ? 'completed' : 'pending';
        const updateUrl = `/manajemen-lct/${taskId}/updateStatus`;

        try {
            const response = await fetch(updateUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus }),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            console.log("Status updated:", data);

            // Update label teks sesuai status
            const label = this.closest('label').querySelector('span');
            label.textContent = isChecked ? '✅ Completed' : '⏳ Pending';

            // Efek sukses
            this.closest('td').classList.add('bg-green-100');
            setTimeout(() => this.closest('td').classList.remove('bg-green-100'), 2000);

        } catch (error) {
            console.error("Failed to update status:", error);
            alert("Gagal memperbarui status. Silakan coba lagi.");
        }
    });
});
</script>

<script>
    function fileUpload() {
        return {
            selectedFiles: [],
            error: '',

            async handleFileChange(event) {
                const files = Array.from(event.target.files);
                this.selectedFiles = [];

                if (files.length > 5) {
                    this.error = 'Maksimal upload 5 file saja.';
                    event.target.value = '';
                    return;
                } else {
                    this.error = '';
                }

                for (const file of files) {
                    if (file.type.startsWith('image/')) {
                        // Kompres gambar
                        const compressed = await this.compressImage(file);
                        this.selectedFiles.push(compressed);
                    } else if (file.type === 'application/pdf') {
                        // PDF langsung masukkan
                        this.selectedFiles.push(file);
                    } else {
                        this.error = 'Hanya gambar dan dokumen PDF yang diperbolehkan.';
                    }
                }
            },

            async compressImage(file) {
                return new Promise((resolve) => {
                    const img = new Image();
                    const reader = new FileReader();

                    reader.onload = (e) => {
                        img.src = e.target.result;
                    };

                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const maxWidth = 1024; // Atur maksimal ukuran gambar
                        const scale = Math.min(1, maxWidth / img.width);

                        canvas.width = img.width * scale;
                        canvas.height = img.height * scale;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                        canvas.toBlob((blob) => {
                            const newFile = new File([blob], file.name, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(newFile);
                        }, 'image/jpeg', 0.7); // kualitas 0.7
                    };

                    reader.readAsDataURL(file);
                });
            },

            removeFile(index) {
                this.selectedFiles.splice(index, 1);
            },

            openInNewTab(file) {
                const url = URL.createObjectURL(file);
                window.open(url, '_blank');
                setTimeout(() => URL.revokeObjectURL(url), 1000 * 60);
            }
        };
    }

    
    document.addEventListener('alpine:init', () => {
        Alpine.data('fileUpload', fileUpload);
    });
</script>
    

<script>
document.addEventListener("click", function(event) {
if (event.target.type === "date") {
    event.target.showPicker();
}
});
</script>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const status = '{{ $laporan->status_lct }}';
        if (status === 'taskbudget_revision') {
            const msg = document.getElementById('tindakan_perbaikan').value.trim();
            if (!msg) {
                e.preventDefault();
                alert('Please enter your corrective action message before submitting.');
            }
        }
    });
</script>
    