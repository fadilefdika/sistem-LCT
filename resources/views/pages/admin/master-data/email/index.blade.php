<x-app-layout>
    <section class="p-3 sm:p-5">
        <div class="max-w-6xl mx-auto bg-white shadow-md rounded-lg p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">📧 Email Templates</h2>
                <a href="{{ route('ehs.master-data.email.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Tambah Template
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 text-green-600 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100 text-gray-700 text-sm">
                        <tr>
                            <th class="px-4 py-2 border-b">#</th>
                            <th class="px-4 py-2 border-b">Nama</th>
                            <th class="px-4 py-2 border-b">Slug</th>
                            <th class="px-4 py-2 border-b">Terakhir Diubah</th>
                            <th class="px-4 py-2 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templates as $i => $template)
                            <tr class="hover:bg-gray-50 text-sm text-gray-700">
                                <td class="px-4 py-2 border-b">{{ $i + 1 }}</td>
                                <td class="px-4 py-2 border-b font-medium">{{ $template->name }}</td>
                                <td class="px-4 py-2 border-b text-gray-500">{{ $template->slug }}</td>
                                <td class="px-4 py-2 border-b text-gray-500">
                                    {{ \Carbon\Carbon::parse($template->updated_at)->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-2 border-b text-center">
                                    <a href="{{ route('ehs.master-data.email.edit', $template->id) }}"
                                       class="text-blue-600 hover:underline mr-2">Edit</a>

                                    <form action="{{ route('ehs.master-data.email.destroy', $template->id) }}"
                                          method="POST" class="inline-block"
                                          onsubmit="return confirm('Yakin ingin menghapus template ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-500 py-6">
                                    Belum ada template email.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-app-layout>
