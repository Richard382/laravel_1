@component('mail::message')
# Sveiki,


Jūsų Užklausa Nr. {{ $inquiry->id }} sukurta:

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
    </tbdoy>
</table>

Kai susidomėję pateiks pasiūlymus į Jūsų užklausą, gausite automatinį pranešimą.

Ačiū, kad pasirinkote CONT sistemą.

Jeigu turite klausimų, susisiekiti galite <a href="mailto:info@cont.lt">info@cont.lt</a>

Pagarbiai,<br>
{{ config('app.name') }}
@endcomponent
