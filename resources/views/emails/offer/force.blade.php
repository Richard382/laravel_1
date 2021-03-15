@component('mail::message')
# Sveiki,

Pranešame, kad jūsų pasiūlymas buvo priimtas! Jums liko apmokėti, kad galėtumėte gauti kliento kontaktus.

@component('mail::button', ['url' => route('inquiry.index')])
Apmokėti
@endcomponent

Jei mygtuko pagalba nuoroda neatsidaro, pasinaudokite šia nuoroda: <a href="{{ route('inquiry.index') }}">Apmokėti</a>

Jeigu turite klausimų, susisiekiti galite <a href="mailto:info@cont.lt">info@cont.lt</a>

Pagarbiai,<br>
{{ config('app.name') }}
@endcomponent
