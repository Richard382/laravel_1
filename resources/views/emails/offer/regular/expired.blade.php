@component('mail::message')
# Sveiki,

Pranešame, kad Jūsų pasirinktam paslaugų teikėjui baigėsi terminas, skirtas susisiekti su Jumis.

Galite pratęsti bendravimą su kitais NT brokeriais pateikusiais savo atsakymus.

@component('mail::button', ['url' => route('inquiry.my')])
    Žiūrėti
@endcomponent

Jei mygtuko pagalba nuoroda neatsidaro, pasinaudokite šia nuoroda: <a href="{{ route('inquiry.my') }}">Žiūrėti</a>

Jeigu turite klausimų, susisiekiti galite <a href="mailto:info@cont.lt">info@cont.lt</a>

Pagarbiai,<br>
{{ config('app.name') }}
@endcomponent
