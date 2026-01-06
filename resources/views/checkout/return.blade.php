@extends('layout.app')

@section('content')
<div class="container" style="padding:3rem 0;">
    @if($ok)
        <h1 style="color:#3E4E20; margin-bottom:1rem;">Dziękujemy!</h1>
        <p>Płatność została potwierdzona. Zamówienie przyjęte do realizacji.</p>
    @else
        <h1 style="color:#3E4E20; margin-bottom:1rem;">Płatność w trakcie</h1>
        <p>
            Jeśli właśnie wróciłaś z płatności, system może jeszcze ją potwierdzać.
            Odśwież stronę za chwilę lub sprawdź e-mail.
        </p>
    @endif

    <a href="{{ route('categories.show', 1) }}" class="btn-main" style="display:inline-block; margin-top:1.5rem;">
        Wróć do sklepu
    </a>
</div>
@endsection
