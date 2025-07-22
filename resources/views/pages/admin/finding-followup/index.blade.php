<x-app-layout>
    <section class="p-3 sm:p-5">
        <div x-data="{ openRow: null }">
            <div class="mx-auto max-w-screen-2xl">
                <div id="report-container-followup">
                    @include('partials.tabel-finding-followup-wrapper', ['laporans' => $laporans])
                </div>
            </div>
        </div>
        
        <script>
            function attachPaginationListeners() {
                const wrapper = document.getElementById('report-container-followup');
                const links = wrapper.querySelectorAll('#pagination-links a');

                links.forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();

                        const url = new URL(this.href);
                        const perPage = document.getElementById('perPageSelect').value;
                        url.searchParams.set('perPage', perPage);

                        fetch(url, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(response => response.text())
                        .then(html => {
                            wrapper.innerHTML = html;
                            attachPerPageListener();
                            attachPaginationListeners();
                        });
                    });
                });
            }

            function attachPerPageListener() {
                const perPageSelect = document.getElementById('perPageSelect');
                const wrapper = document.getElementById('report-container-followup');

                if (perPageSelect) {
                    perPageSelect.addEventListener('change', () => {
                        const perPage = perPageSelect.value;
                        fetch(`{{ route('admin.finding-followup.table') }}?perPage=${perPage}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(response => response.text())
                        .then(html => {
                            wrapper.innerHTML = html;
                            attachPerPageListener();
                            attachPaginationListeners(); // tambahkan ini
                        });
                    });
                }
            }
            
            document.addEventListener('DOMContentLoaded', () => {
                attachPerPageListener();
                attachPaginationListeners();
            });

        </script>
        
            
    </section>
</x-app-layout>
