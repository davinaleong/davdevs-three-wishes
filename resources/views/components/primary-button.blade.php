<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150', 'style' => 'background-color: #2b7fff; hover:background-color: #1447e6; focus:background-color: #1447e6; active:background-color: #1447e6; focus:ring-color: #2b7fff;']) }}>
    {{ $slot }}
</button>
