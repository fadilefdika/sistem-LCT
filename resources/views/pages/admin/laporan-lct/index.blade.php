<x-app-layout>
    <div class="container mx-auto p-6">
        <div class="overflow-hidden bg-white shadow-lg rounded-xl">
            <table id="userTable" class="w-full border-collapse">
                <thead class="bg-gray-50">
                    <tr class="text-gray-600 uppercase text-sm tracking-wider">
                        <th class="py-3 px-4 text-left border-b">No</th>
                        <th class="py-3 px-4 text-left border-b">Nama</th>
                        <th class="py-3 px-4 text-left border-b">Email</th>
                        <th class="py-3 px-4 text-left border-b">Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function () {
                console.log("Memulai DataTables...");

                $('#userTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.laporan-lct') }}",
                        type: "GET",
                        error: function(xhr, status, error) {
                            console.log("Error DataTables:", error);
                            alert("Gagal mengambil data dari server.");
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'fullname', name: 'fullname' },
                        { data: 'email', name: 'email' },
                        { data: 'created_at', name: 'created_at' }
                    ]
                });
            });
        </script>
    @endpush

    <style>
        /* Gaya mirip Notion */
        table {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            font-weight: 600;
            background-color: #f8f9fa;
        }

        tbody tr {
            border-bottom: 1px solid #e0e0e0;
            transition: background 0.2s ease-in-out;
        }

        tbody tr:hover {
            background-color: #f4f4f4;
        }

        th, td {
            padding: 12px 16px;
        }

        /* Styling untuk search bar */
.dataTables_filter {
    text-align: left !important;
    margin-bottom: 10px;
}

.dataTables_filter input {
    width: 250px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 8px;
    outline: none;
}

/* Styling untuk pagination */
.dataTables_paginate {
    text-align: center;
    margin-top: 15px;
}

.dataTables_paginate .paginate_button {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    padding: 6px 12px;
    border-radius: 6px;
    margin: 0 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.dataTables_paginate .paginate_button:hover {
    background-color: #e0e0e0;
}

.dataTables_paginate .paginate_button.current {
    background-color: #4A90E2;
    color: white !important;
    border: 1px solid #4A90E2;
}


    </style>
</x-app-layout>
