@component('mail::message')
# Sveiki,

Pranešame, kad Jūsų matomumas buvo pakeistas į {{ $visibility->name }}.

Jeigu turite klausimų, susisiekiti galite <a href="mailto:info@cont.lt">info@cont.lt</a>

Pagarbiai,<br>
{{ config('app.name') }}
@endcomponent
