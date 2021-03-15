@component('mail::message')
# Sveiki,

Pranešam, kad jūsų pasiulymas buvo priimtas,

@component('mail::button', ['url' => route('inquiry.index')])
    Pažiūrėti
@endcomponent

Jei mygtuko pagalba nuoroda neatsidaro, pasinaudokite šia nuoroda: <a href="{{ route('inquiry.index') }}">PAŽIŪRĖTI</a>

Pagarbiai,<br>
{{ config('app.name') }}
@endcomponent
