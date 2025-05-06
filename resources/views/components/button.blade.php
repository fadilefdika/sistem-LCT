<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn whitespace-nowrap']) }}>
    {{ $slot }}
</button>
