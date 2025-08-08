<x-app-layout>
    <section class="p-4 sm:p-6">
        <div class="max-w-5xl mx-auto bg-white shadow-sm border border-gray-200 rounded-2xl p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 tracking-tight">
                {{ isset($template) ? 'Edit Email Template' : 'Buat Email Template' }}
            </h2>

            <form action="{{ isset($template) 
                            ? route('ehs.master-data.email.update', $template->id) 
                            : route('ehs.master-data.email.store') }}" 
                  method="POST" class="space-y-6">
                @csrf
                @if(isset($template))
                    @method('PUT')
                @endif

                {{-- Nama Template --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Template</label>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name', $template->name ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition">
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" id="slug" required
                           value="{{ old('slug', $template->slug ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition">
                    <small class="text-gray-500">Contoh: <code>budget-approval</code></small>
                </div>

                {{-- Konten Email --}}
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Konten Email</label>
                    <textarea name="content" id="myeditorinstance" rows="10" class="hidden">
                        {{ old('content', $template->content ?? '') }}
                    </textarea>
                </div>


                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('ehs.master-data.email.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        Batal
                    </a>

                    <button type="submit"
                            class="px-5 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                        {{ isset($template) ? 'Simpan Perubahan' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
