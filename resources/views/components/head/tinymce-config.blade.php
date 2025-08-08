<div>
    @php
        $tinymceApiKey = config('services.tinymce.api_key');
    @endphp

<script src="https://cdn.tiny.cloud/1/{{ $tinymceApiKey }}/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
    tinymce.init({
        selector: 'textarea#myeditorinstance',
        plugins: 'code table lists',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table',
        min_height: 300,
        max_height: 800,
        autoresize_bottom_margin: 20,
        plugins: 'autoresize code table lists',
    });

</script>
</div>