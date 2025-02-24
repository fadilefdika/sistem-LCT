<script>
    document.addEventListener('DOMContentLoaded', function () {
        Livewire.on('showNotification', message => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                showConfirmButton: false,
                timer: 2000
            });
        });
    });
</script>
