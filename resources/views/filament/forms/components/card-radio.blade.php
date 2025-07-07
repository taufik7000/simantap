<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $statePath = $getStatePath();
    @endphp

    <div
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }},
        }"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
    >
        @foreach($options as $value => $label)
            <div
                @click="state = @js($value)"
                :class="{
                    'ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-500/10': state === @js($value),
                    'ring-1 ring-gray-300 dark:ring-gray-700': state !== @js($value),
                }"
                class="cursor-pointer rounded-lg p-4 shadow-sm transition-all duration-200 hover:ring-2 hover:ring-primary-400"
            >
                <div class="flex items-center gap-4">
                    {{-- Lingkaran Radio Button --}}
                    <div
                        class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full border"
                        :class="{
                            'border-primary-500': state === @js($value),
                            'border-gray-300 dark:border-gray-600': state !== @js($value)
                        }"
                    >
                        <div
                            x-show="state === @js($value)"
                            class="h-2 w-2 rounded-full bg-primary-500"
                        ></div>
                    </div>
                    
                    {{-- Konten Teks (Hanya Label) --}}
                    <div class="flex flex-col">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $label }}</span>
                        {{-- Bagian deskripsi sudah dihapus dari sini --}}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-dynamic-component>