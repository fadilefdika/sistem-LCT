<div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Laporan</th>
                <th>PIC</th>
                <th>Status Budget</th>
                <th>Tanggal Update</th>
            </tr>
        </thead>
        <tbody>
            @foreach($budgets as $budget)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $budget->laporanLct->nama_laporan }}</td>
                    <td>{{ $budget->pic->user->name }}</td>
                    <td>{{ $budget->status_budget }}</td>
                    <td>{{ $budget->updated_at->format('d-m-Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $budgets->links() }}
    </div>
</div>
