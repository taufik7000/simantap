<x-filament-infolists::entry-wrapper
    :entry="$entry"
>
    <div class="fi-infolists-key-value-entry flex flex-col gap-y-3">
        @php
            $data = $getRecord()->data_pemohon;

            function renderData($data, $level = 0) {
                $html = '<ul class="list-disc ml-4">';
                foreach ($data as $key => $value) {
                    $html .= '<li><strong>' . ucwords(str_replace('_', ' ', $key)) . ':</strong> ';
                    if (is_array($value)) {
                        $html .= renderData($value, $level + 1);
                    } else {
                        $html .= e($value);
                    }
                    $html .= '</li>';
                }
                $html .= '</ul>';
                return $html;
            }
        @endphp

        {!! renderData($data) !!}
    </div>
</x-filament-infolists::entry-wrapper>