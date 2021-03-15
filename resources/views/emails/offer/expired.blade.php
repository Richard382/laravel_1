@component('mail::message')
# Sveiki,

Pranešame, kad jūs nesumokėjote laiku, todėl Jūsų pasiūlymas daugiau nebegalioja.

@component('mail::button', ['url' => route('inquiry.index')])
Žiūrėti
@endcomponent

Jei mygtuko pagalba nuoroda neatsidaro, pasinaudokite šia nuoroda: <a href="{{ route('inquiry.index') }}">Žiūrėti</a>

Jeigu turite klausimų, susisiekiti galite <a href="mailto:info@cont.lt">info@cont.lt</a>

Pagarbiai,<br>
{{ config('app.name') }}
@endcomponent
