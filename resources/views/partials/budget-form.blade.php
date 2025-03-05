<div class="bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-xl font-semibold mb-4">Budget Submission for LCT Repairs</h2>
    <form action="{{ route('admin.manajemen-lct.submitBudget', ['id_laporan_lct' => $laporan->id_laporan_lct]) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-6">
            <label for="budget_amount" class="block text-sm font-medium text-gray-700">Budget Amount <span class="text-red-500">*</span></label>
            <input type="text" name="budget_amount" id="budget_amount" value="{{ old('budget_amount', $budget->budget ?? '') }}" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
        </div>

        <div class="mb-6">
            <label for="budget_description" class="block text-sm font-medium text-gray-700">Budget Description <span class="text-red-500">*</span></label>
            <textarea name="budget_description" id="budget_description" rows="4" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">{{ old('budget_description', $budget->deskripsi ?? '') }}</textarea>
        </div>

        <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200 cursor-pointer">
            Submit Budget Request
        </button>
    </form>
</div>
