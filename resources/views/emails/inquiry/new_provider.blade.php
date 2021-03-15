@component('mail::message')
# Sveiki,


Nauja Užklausa Nr. {{ $inquiry->id }} atitinka jūsų kriterijus:

<table class="simple">
    <tbdoy>
        <tr>
            <th>Tikslas</th>
            <td>{{ $inquiry->service->type_user }}</td>
        </tr>
        <tr>
            <th>Reikalavimai</th>
            <td>{{ $inquiry->sum_up }}</td>
        </tr>
        <tr>
            <th>Išsamesnė informacija:</th>
            <td>{!! nl2br($inquiry->requirements) !!}</td>
        </tr>
        <tr>
            <td></td><td ><a class href="{{url('uzklausos')}}"><button class="btn primary">ŽIŪRĖTI</button></a></td>
        </tr>
    </tbdoy>
</table>

Nematomte mygtuko, spauskite <a href="{{url('uzklausos')}}">čia</a>

Ačiū, kad pasirinkote CONT sistemą.

Jeigu turite klausimų, susisiekiti galite <a href="mailto:info@cont.lt">info@cont.lt</a>

Pagarbiai,<br>
{{ config('app.name') }}
@endcomponent
