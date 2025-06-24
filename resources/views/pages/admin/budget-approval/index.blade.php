<x-app-layout>
    <section class="p-5">
        <div class="max-w-screen-xl mx-auto">
            <div id="budget-table-container" class="bg-white p-5 shadow-sm rounded-lg border border-gray-100">
                
                @include('partials.tabel-budget-approval-wrapper', ['taskBudget' => $taskBudget])
            </div>
        </div>
    </section>
    <script>
        function fetchData(url = "{{ route('admin.budget-approval.index') }}", params = {}) {
            $.ajax({
                url: url,
                type: 'GET',
                data: params,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(res) {
                    $('#budget-table-container').html(res);
                    $('html, body').animate({ scrollTop: $('#budget-table-container').offset().top - 100 }, 300);
                },
                error: function(e) {
                    console.error('Gagal mengambil data.',e);
                    alert('Gagal mengambil data.');
                }
            });
        }
    
        $(document).ready(function() {
            // Pagination link click
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                fetchData(url);
            });
    
            // PerPage select change
            $(document).on('change', '#perPageSelect', function() {
                var perPage = $(this).val();
                fetchData("{{ route('admin.budget-approval.index') }}", { perPage: perPage });
            });
        });
    </script>
            
</x-app-layout>
