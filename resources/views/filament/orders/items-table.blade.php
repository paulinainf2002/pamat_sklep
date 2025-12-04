<table class="filament-table w-full text-sm">
    <thead>
        <tr>
            <th class="px-3 py-2 text-left">Produkt</th>
            <th class="px-3 py-2 text-left">Ilość</th>
            <th class="px-3 py-2 text-left">Typ</th>
            <th class="px-3 py-2 text-left">Waga</th>
            <th class="px-3 py-2 text-left">Cena</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($record->items as $item)
            <tr>
                <td class="px-3 py-2">{{ $item->product->name ?? '—' }}</td>
                <td class="px-3 py-2">{{ $item->quantity }}</td>
                <td class="px-3 py-2">
                    @if($item->type === 'set') Zestaw
                    @elseif($item->type === 'weight') Na wagę
                    @else —
                    @endif
                </td>
                <td class="px-3 py-2">
                    {{ $item->weight ? $item->weight . ' g' : '—' }}
                </td>
                <td class="px-3 py-2">{{ number_format($item->price, 2) }} zł</td>
            </tr>
        @endforeach
    </tbody>
</table>
