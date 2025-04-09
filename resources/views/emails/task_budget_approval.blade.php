@component('mail::message')
# Budget Approval Request

Dear Manager,

A new budget estimation has been submitted and is awaiting your approval.

---

**Submitted By:** {{ $laporan->picUser->fullname }}  
**Finding:** {{ $laporan->temuan_ketidaksesuaian }}  
**Estimated Budget:** Rp {{ number_format($laporan->estimated_budget, 0, ',', '.') }}

---

### 📋 Task List

@foreach ($tasks as $task)
- **Task:** {{ $task->task_name }}  
  **PIC:** {{ $task->pic->name ?? '-' }}  
  **Due Date:** {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}  
  **Notes:** {{ $task->notes ?? '-' }}
@endforeach

---

@component('mail::button', ['url' => url('/budget-approval/' . $laporan->id_laporan_lct)])
View LCT Report
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
