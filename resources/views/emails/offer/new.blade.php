@component('mail::message')
# Sveiki,

Jūs gavote naują pasiūlymą į Jūsų užklausą  Nr. {{ $inquiry->id }}!

<b>Atsakymas:</b><br>
{!! nl2br($offer->text) !!}

<!--Norėdami patvirtinti pasiūlymą spauskite šią nuorodą ir prisijunkite: <a href="{{ $inquiry->getLink() }}">PATVIRTINKITE</a>-->
Norėdami patvirtinti pasiūlymą spauskite šią nuorodą: <a href="{{ $inquiry->getLink() }}">PERŽIŪRĖTI</a>

Jeigu turite klausimų, susisiekiti galite <a href="mailto:info@cont.lt">info@cont.lt</a>

Pagarbiai,<br>
{{ config('app.name') }}
@endcomponent
